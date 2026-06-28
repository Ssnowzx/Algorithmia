<?php
/**
 * Professores-NPC. Cada um governa uma região/capítulo do mapa.
 */
class Mestre extends Model
{
    protected string $table = 'mestres';

    public function todosOrdenados(): array
    {
        return $this->findAll('ordem ASC');
    }

    /**
     * Progresso de domínio por região (maestria horizontal). Parte das FASES
     * (não de `mestres`), de modo que só entram os mestres realmente referenciados
     * por fases — robusto à duplicação do catálogo de mestres. Considera apenas
     * fases jogáveis (não-história, que são as que dão estrelas). Read-only.
     *
     * @return array<int,array{id:int,ordem:int,regiao:string,titulo:string,
     *   cor_tema:string,svg_slug:string,total:int,concluidas:int,estrelas:int,perfeitas:int}>
     */
    public function progressoPorRegiao(int $personagemId): array
    {
        $stmt = $this->db->prepare(
            "SELECT m.id, m.ordem, m.regiao, m.titulo, m.cor_tema, m.svg_slug,
                    COUNT(f.id) AS total,
                    COUNT(pf.fase_id) AS concluidas,
                    COALESCE(SUM(pf.estrelas), 0) AS estrelas,
                    COALESCE(SUM(pf.estrelas = 3), 0) AS perfeitas
             FROM fases f
             JOIN mestres m ON m.id = f.mestre_id
             LEFT JOIN progresso_fases pf ON pf.fase_id = f.id AND pf.personagem_id = :p
             WHERE f.mestre_id IS NOT NULL AND f.tipo <> 'historia'
             GROUP BY m.id, m.ordem, m.regiao, m.titulo, m.cor_tema, m.svg_slug
             ORDER BY m.ordem"
        );
        $stmt->execute(['p' => $personagemId]);
        return $stmt->fetchAll();
    }
}
