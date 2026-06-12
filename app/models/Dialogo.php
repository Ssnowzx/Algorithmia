<?php
/**
 * Falas de uma fase, em momentos (antes/vitoria/derrota) e variantes (padrao/ia).
 */
class Dialogo extends Model
{
    protected string $table = 'dialogos';

    /**
     * Diálogos de um momento. Tenta a variante pedida; se não houver, usa 'padrao'.
     */
    public function paraMomento(int $faseId, string $momento, string $variante = 'padrao'): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM dialogos
             WHERE fase_id = :f AND momento = :m AND variante = :v
             ORDER BY ordem ASC"
        );
        $stmt->execute(['f' => $faseId, 'm' => $momento, 'v' => $variante]);
        $linhas = $stmt->fetchAll();

        if (!$linhas && $variante !== 'padrao') {
            return $this->paraMomento($faseId, $momento, 'padrao');
        }
        return $linhas;
    }
}
