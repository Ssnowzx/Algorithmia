<?php
/**
 * Model base: CRUD genérico sobre uma tabela via PDO com prepared statements.
 * Todos os models do jogo herdam daqui (composição de queries comuns).
 */
class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = getConnection();
    }

    /**
     * Valida um identificador (coluna) antes de interpolá-lo na SQL.
     * Nomes de coluna não podem ser parametrizados via PDO, então restringimos
     * ao formato seguro para evitar SQL injection caso algum dia o nome passe a
     * vir de entrada do usuário.
     */
    protected function colunaSegura(string $nome): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $nome)) {
            throw new InvalidArgumentException("Coluna inválida: {$nome}");
        }
        return $nome;
    }

    /**
     * Valida uma cláusula ORDER BY do tipo "coluna [ASC|DESC]".
     */
    protected function ordenacaoSegura(string $orderBy): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\s+(ASC|DESC))?$/i', trim($orderBy))) {
            throw new InvalidArgumentException("Ordenação inválida: {$orderBy}");
        }
        return $orderBy;
    }

    public function findAll(string $orderBy = 'id ASC'): array
    {
        $orderBy = $this->ordenacaoSegura($orderBy);
        return $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}")->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Retorna a primeira linha que casa com uma coluna = valor.
     */
    public function findBy(string $coluna, $valor): ?array
    {
        $coluna = $this->colunaSegura($coluna);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$coluna} = :v LIMIT 1");
        $stmt->execute(['v' => $valor]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Retorna todas as linhas que casam com uma coluna = valor.
     */
    public function where(string $coluna, $valor, string $orderBy = 'id ASC'): array
    {
        $coluna = $this->colunaSegura($coluna);
        $orderBy = $this->ordenacaoSegura($orderBy);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$coluna} = :v ORDER BY {$orderBy}");
        $stmt->execute(['v' => $valor]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        array_map([$this, 'colunaSegura'], array_keys($data));
        $colunas = implode(', ', array_keys($data));
        $marcadores = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$colunas}) VALUES ({$marcadores})");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach (array_keys($data) as $coluna) {
            $this->colunaSegura($coluna);
            $sets[] = "{$coluna} = :{$coluna}";
        }
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id");
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
