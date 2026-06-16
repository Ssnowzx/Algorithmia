-- Novas classes jogáveis: xeno, elfo, draconato
ALTER TABLE personagens
    MODIFY classe ENUM('mago','guerreiro','ranger','xeno','elfo','draconato') NOT NULL;
