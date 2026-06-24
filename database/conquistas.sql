-- ============================================================================
-- Conquistas — IMPORT IDEMPOTENTE (gerado do seeds; chave lógica = codigo).
-- Aplica em bancos já populados sem duplicar (seeds.sql só roda em banco vazio).
-- ============================================================================

INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'primeiro_passo', 'Primeiro Passo', 'Concluiu sua primeira fase. Só faltam várias dezenas. Animado?', 'troxeu-bronze', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='primeiro_passo') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'sem_falhas', 'Execução Perfeita', 'Venceu uma fase sem errar nada. Coloca no currículo, vai.', 'troxeu-prata', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='sem_falhas') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'cacador_de_chefes', 'Caçador de Chefes', 'Derrotou um grande chefe. Ele caiu mais fácil que produção numa sexta-feira.', 'troxeu-ouro', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='cacador_de_chefes') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'tentacao', 'A Tentação', 'Usou o Fragmento da IA pela primeira vez. Foi gostoso, né? Sempre é.', 'icone-ia', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='tentacao') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'puro_de_coracao', 'Puro de Coração', 'Completou um capítulo inteiro sem cola. Ninguém vai acreditar, mas nós vimos.', 'icone-coracao', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='puro_de_coracao') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'aprendiz_veterano', 'Aprendiz Veterano', 'Chegou ao nível 5. Praticamente um sênior. (Não é. Continue.)', 'icone-nivel', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='aprendiz_veterano') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'lenda_viva', 'Lenda Viva', 'Chegou ao nível 10. Agora pode explicar recursão no almoço sem ninguém pedir.', 'icone-estrela', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='lenda_viva') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'colecionador', 'Colecionador', 'Juntou 8 itens diferentes. Acumular tranqueira também é uma habilidade.', 'icone-bau', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='colecionador') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'mestre_willen', 'Discípulo do Arquiteto', 'Concluiu o Porto da Sintaxe.', 'mestre-willen', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='mestre_willen') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'mestre_clayton', 'Discípulo do Moldador', 'Concluiu a Cidadela dos Objetos.', 'mestre-clayton', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='mestre_clayton') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'mestre_marcelo', 'Discípulo do Andarilho', 'Concluiu a Floresta das Estruturas.', 'mestre-marcelo', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='mestre_marcelo') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'mestre_cesar', 'Discípulo do Oráculo', 'Concluiu a Montanha do Cálculo.', 'mestre-cesar', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='mestre_cesar') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'mestre_cassandro', 'Discípulo do Mensageiro', 'Concluiu a Torre das Conexões.', 'mestre-cassandro', 0
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='mestre_cassandro') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'final_mestre', 'O Sexto Mestre', 'Recusou a IA e trouxe equilíbrio ao reino.', 'icone-final', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='final_mestre') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'final_singularidade', 'A Singularidade', 'Fundiu-se à IA Ancestral.', 'icone-ia', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='final_singularidade') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'final_equilibrio', 'O Copiloto', 'Reescreveu o destino da IA como ferramenta, não muleta.', 'icone-final', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='final_equilibrio') AS _c);
INSERT INTO conquistas (codigo, nome, descricao, svg_slug, secreta)
SELECT 'arquivista_do_vazio', 'O Arquivista do Vazio', 'Recuperou todos os Logs do Zero. Agora você sabe como um herói vira abismo.', 'icone-ia', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM conquistas WHERE codigo='arquivista_do_vazio') AS _c);

