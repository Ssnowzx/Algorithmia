-- Dedupe da tabela `itens`: o catálogo (18 itens) foi semeado mais de uma vez
-- em alguns bancos (seeds.sql aplicado 2x), deixando cada item duplicado e a
-- LOJA mostrando tudo em dobro. Esta migração mantém o MENOR id de cada item
-- (nome+tipo+raridade) e remove os demais. É idempotente: num banco já limpo,
-- nenhum duplicado casa e nada é apagado.
--
-- Repointa as referências para o id mantido ANTES de apagar (defensivo, caso
-- algum banco aponte inventario/fases para a cópia de id maior).

UPDATE inventario inv
JOIN itens d ON d.id = inv.item_id
JOIN (SELECT nome, tipo, raridade, MIN(id) AS keep_id FROM itens GROUP BY nome, tipo, raridade) g
  ON g.nome = d.nome AND g.tipo = d.tipo AND g.raridade = d.raridade
SET inv.item_id = g.keep_id
WHERE inv.item_id <> g.keep_id;

UPDATE fases f
JOIN itens d ON d.id = f.item_drop_id
JOIN (SELECT nome, tipo, raridade, MIN(id) AS keep_id FROM itens GROUP BY nome, tipo, raridade) g
  ON g.nome = d.nome AND g.tipo = d.tipo AND g.raridade = d.raridade
SET f.item_drop_id = g.keep_id
WHERE f.item_drop_id <> g.keep_id;

DELETE d FROM itens d
JOIN itens k ON k.nome = d.nome AND k.tipo = d.tipo AND k.raridade = d.raridade AND k.id < d.id;
