-- O chefe final ganha nome e rosto: Márcio, o Lorde Segfault.
-- Substitui o /dev/null genérico pela arte do Márcio (public/img/inimigos/inimigo-segfault.png).
UPDATE fases SET inimigo_nome = 'Márcio, o Lorde Segfault' WHERE tipo = 'chefe_final';

-- Revela, no confronto final, o nome por trás do Zero/Segfault — fecha o arco da história.
INSERT INTO dialogos (fase_id, momento, variante, ordem, falante, svg_slug, texto)
SELECT f.id, 'antes', 'padrao', 7, 'Márcio, o Lorde Segfault', 'inimigo-segfault',
       'Quer um nome para o vazio? Tenha: eu fui Márcio. Fui Zero, o primeiro aluno, o melhor de todos — até terceirizar cada pensamento à IA e descobrir que não restava ninguém aqui dentro. Há espaço de sobra no /dev/null. Venha provar que aprendeu o que eu nunca aprendi.'
FROM fases f
WHERE f.tipo = 'chefe_final'
  AND NOT EXISTS (
      SELECT 1 FROM dialogos d
      WHERE d.fase_id = f.id AND d.momento = 'antes' AND d.ordem = 7
  );
