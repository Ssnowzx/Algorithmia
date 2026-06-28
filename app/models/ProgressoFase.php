<?php
/**
 * Registro de conclusão de fase por personagem (estrelas, acertos, uso de IA).
 */
class ProgressoFase extends Model
{
    protected string $table = 'progresso_fases';

    /**
     * Mapa fase_id => linha de progresso, para um personagem.
     */
    public function mapaDoPersonagem(int $personagemId): array
    {
        $linhas = $this->where('personagem_id', $personagemId);
        $mapa = [];
        foreach ($linhas as $linha) {
            $mapa[(int) $linha['fase_id']] = $linha;
        }
        return $mapa;
    }

    public function concluiu(int $personagemId, int $faseId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM progresso_fases WHERE personagem_id = :p AND fase_id = :f"
        );
        $stmt->execute(['p' => $personagemId, 'f' => $faseId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Registra (ou melhora) o resultado de uma fase. Mantém o maior nº de estrelas.
     */
    public function registrar(int $personagemId, int $faseId, int $estrelas, int $acertos, int $erros, bool $usouIa): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO progresso_fases (personagem_id, fase_id, estrelas, acertos, erros, usou_ia)
             VALUES (:p, :f, :e, :a, :er, :ia)
             ON DUPLICATE KEY UPDATE
                estrelas = GREATEST(estrelas, VALUES(estrelas)),
                acertos  = VALUES(acertos),
                erros    = VALUES(erros),
                usou_ia  = VALUES(usou_ia),
                concluida_em = NOW()"
        );
        $stmt->execute([
            'p' => $personagemId, 'f' => $faseId, 'e' => $estrelas,
            'a' => $acertos, 'er' => $erros, 'ia' => $usouIa ? 1 : 0,
        ]);
    }

    public function totalEstrelas(int $personagemId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(estrelas),0) FROM progresso_fases WHERE personagem_id = :p"
        );
        $stmt->execute(['p' => $personagemId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Resumo da última semana (7 dias) para o recap do perfil: fases
     * concluídas/melhoradas e estrelas dessas fases. Read-only.
     *
     * @return array{fases:int,estrelas:int}
     */
    public function resumoSemana(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS fases, COALESCE(SUM(estrelas), 0) AS estrelas
             FROM progresso_fases
             WHERE personagem_id = :p AND concluida_em >= (NOW() - INTERVAL 7 DAY)"
        );
        $stmt->execute(['p' => $personagemId]);
        $r = $stmt->fetch() ?: [];
        return ['fases' => (int) ($r['fases'] ?? 0), 'estrelas' => (int) ($r['estrelas'] ?? 0)];
    }

    /**
     * Fases concluídas/melhoradas na SEMANA ISO corrente — para as missões da
     * semana. Read-only.
     */
    public function fasesSemana(int $personagemId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM progresso_fases
             WHERE personagem_id = :p AND YEARWEEK(concluida_em, 3) = YEARWEEK(NOW(), 3)"
        );
        $stmt->execute(['p' => $personagemId]);
        return (int) $stmt->fetchColumn();
    }
}
