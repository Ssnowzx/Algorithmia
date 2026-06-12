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
}
