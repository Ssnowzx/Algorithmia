<?php
/**
 * Histórico de respostas, base das estatísticas por matéria no perfil.
 */
class RespostaLog extends Model
{
    protected string $table = 'respostas_log';

    public function registrar(int $personagemId, int $desafioId, bool $correta, bool $usouIa): void
    {
        $this->create([
            'personagem_id' => $personagemId,
            'desafio_id'    => $desafioId,
            'correta'       => $correta ? 1 : 0,
            'usou_ia'       => $usouIa ? 1 : 0,
        ]);
    }

    /**
     * Estatísticas agregadas por assunto: total e acertos.
     */
    public function estatisticasPorAssunto(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.assunto,
                    COUNT(*) AS total,
                    SUM(r.correta) AS acertos
             FROM respostas_log r
             JOIN desafios d ON d.id = r.desafio_id
             WHERE r.personagem_id = :p
             GROUP BY d.assunto"
        );
        $stmt->execute(['p' => $personagemId]);
        $resultado = [];
        foreach ($stmt->fetchAll() as $linha) {
            $resultado[$linha['assunto']] = [
                'total'   => (int) $linha['total'],
                'acertos' => (int) $linha['acertos'],
            ];
        }
        return $resultado;
    }

    public function totalRespostas(int $personagemId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM respostas_log WHERE personagem_id = :p");
        $stmt->execute(['p' => $personagemId]);
        return (int) $stmt->fetchColumn();
    }

    public function totalUsosIa(int $personagemId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM respostas_log WHERE personagem_id = :p AND usou_ia = 1");
        $stmt->execute(['p' => $personagemId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Resumo da última semana (7 dias) para o recap do perfil: desafios
     * respondidos, acertos e usos de IA. Read-only; não cria nem altera dado.
     *
     * @return array{respostas:int,acertos:int,usos_ia:int}
     */
    public function resumoSemana(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS respostas,
                    COALESCE(SUM(correta), 0) AS acertos,
                    COALESCE(SUM(usou_ia), 0) AS usos_ia
             FROM respostas_log
             WHERE personagem_id = :p AND respondido_em >= (NOW() - INTERVAL 7 DAY)"
        );
        $stmt->execute(['p' => $personagemId]);
        $r = $stmt->fetch() ?: [];
        return [
            'respostas' => (int) ($r['respostas'] ?? 0),
            'acertos'   => (int) ($r['acertos'] ?? 0),
            'usos_ia'   => (int) ($r['usos_ia'] ?? 0),
        ];
    }

    /**
     * Métricas da SEMANA ISO corrente (segunda→domingo) para as missões da
     * semana — read-only, derivado, numa única query agregada.
     *
     * @return array{respostas:int,acertos:int,respostas_sem_ia:int,acertos_sem_ia:int,materias:int}
     */
    public function metricasSemana(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS respostas,
                    COALESCE(SUM(r.correta), 0) AS acertos,
                    COALESCE(SUM(CASE WHEN r.usou_ia = 0 THEN 1 ELSE 0 END), 0) AS respostas_sem_ia,
                    COALESCE(SUM(CASE WHEN r.usou_ia = 0 AND r.correta = 1 THEN 1 ELSE 0 END), 0) AS acertos_sem_ia,
                    COUNT(DISTINCT d.assunto) AS materias
             FROM respostas_log r
             JOIN desafios d ON d.id = r.desafio_id
             WHERE r.personagem_id = :p AND YEARWEEK(r.respondido_em, 3) = YEARWEEK(NOW(), 3)"
        );
        $stmt->execute(['p' => $personagemId]);
        $r = $stmt->fetch() ?: [];
        return [
            'respostas'        => (int) ($r['respostas'] ?? 0),
            'acertos'          => (int) ($r['acertos'] ?? 0),
            'respostas_sem_ia' => (int) ($r['respostas_sem_ia'] ?? 0),
            'acertos_sem_ia'   => (int) ($r['acertos_sem_ia'] ?? 0),
            'materias'         => (int) ($r['materias'] ?? 0),
        ];
    }
}
