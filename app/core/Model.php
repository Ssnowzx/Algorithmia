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

    public function findAll(string $orderBy = 'id ASC'): array
    {
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
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$coluna} = :v LIMIT 1");
        $stmt->execute(['v' => $valor]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Retorna todas as linhas que casam com uma coluna = valor.
     */
    public function where(string $coluna, $valor, string $orderBy = 'id ASC'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$coluna} = :v ORDER BY {$orderBy}");
        $stmt->execute(['v' => $valor]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
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
