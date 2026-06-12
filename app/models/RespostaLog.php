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
}
