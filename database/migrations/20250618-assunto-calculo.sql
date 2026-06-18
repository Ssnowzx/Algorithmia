-- Banco de questões: matéria própria de Cálculo (Cesar, o Oráculo do Ritmo).
-- Antes, os desafios de Cálculo eram marcados como 'logica'. A partir do banco
-- ampliado, Cálculo vira um assunto independente — sem perder os já existentes.
ALTER TABLE desafios
    MODIFY assunto ENUM('php','mvc','sql','poo','estruturas','redes','logica','calculo') NOT NULL;
