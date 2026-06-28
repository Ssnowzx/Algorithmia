-- Dedupe da tabela `mestres`: o catálogo (5 mestres) foi semeado mais de uma vez
-- em alguns bancos (seeds.sql aplicado 2x), deixando 10 registros (5 reais + 5
-- cópias órfãs sem fases). Esta migração mantém o MENOR id de cada mestre (por
-- `svg_slug`, identificador estável) e remove as cópias. É idempotente: num banco
-- já limpo, nenhum duplicado casa e nada é apagado.
--
-- Repointa as fases para o id mantido ANTES de apagar (defensivo, caso algum
-- banco tenha fases apontando para a cópia de id maior).

UPDATE fases f
JOIN mestres d ON d.id = f.mestre_id
JOIN (SELECT svg_slug, MIN(id) AS keep_id FROM mestres GROUP BY svg_slug) g
  ON g.svg_slug = d.svg_slug
SET f.mestre_id = g.keep_id
WHERE f.mestre_id <> g.keep_id;

DELETE d FROM mestres d
JOIN mestres k ON k.svg_slug = d.svg_slug AND k.id < d.id;
