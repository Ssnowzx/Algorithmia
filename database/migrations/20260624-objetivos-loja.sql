-- Objetivos de loja: conquistas de "primeira vez" que dão um empurrão em ouro
-- (valores em config OBJETIVOS_OURO). Reaproveita a infra de conquistas — aqui
-- só registramos as linhas no catálogo. INSERT IGNORE para conviver com a seed
-- (instalações novas já trazem estas linhas via seeds.sql).
INSERT IGNORE INTO conquistas (codigo, nome, descricao, svg_slug, secreta) VALUES
('primeira_arma',    'Primeira Lâmina',   'Equipou sua primeira arma. Agora o bug tem motivo pra ter medo — pouco, mas tem.', 'troxeu-bronze', 0),
('primeira_pocao',   'Goró de Cura',      'Usou sua primeira poção. Beber no meio da luta: a única ocasião em que isso é estratégia.', 'icone-coracao', 0),
('arsenal_completo', 'Equipado dos Pés à Cabeça', 'Arma, escudo e acessório equipados ao mesmo tempo. Visual de quem leu o tutorial.', 'icone-bau', 0);
