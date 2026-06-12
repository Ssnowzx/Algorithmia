-- ============================================================
--  Algorithmia — A Lenda dos Cinco Mestres
--  Esquema do banco de dados (MySQL / InnoDB / utf8mb4)
-- ============================================================

CREATE DATABASE IF NOT EXISTS algorithmia
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE algorithmia;

-- Schema NÃO-DESTRUTIVO: usa CREATE TABLE IF NOT EXISTS para nunca apagar
-- dados existentes (contas, personagens, progresso). Para zerar tudo de
-- propósito, use: php database/migrate.php --reset

-- ------------------------------------------------------------
-- 1. Usuários (contas de acesso)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(80)  NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    papel      ENUM('jogador','mestre') NOT NULL DEFAULT 'jogador',
    criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Mestres / Professores (NPCs principais, uma cidade cada)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mestres (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120) NOT NULL,
    titulo        VARCHAR(120) NOT NULL,
    disciplina    VARCHAR(120) NOT NULL,
    regiao        VARCHAR(120) NOT NULL,
    historia      TEXT,
    personalidade TEXT,
    bordao        VARCHAR(255),
    svg_slug      VARCHAR(80)  NOT NULL,
    cor_tema      VARCHAR(7)   NOT NULL DEFAULT '#7c5cff',
    ordem         INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Itens (armas, escudos, acessórios, poções, especiais)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS itens (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(120) NOT NULL,
    descricao TEXT,
    tipo      ENUM('arma','escudo','acessorio','pocao','especial') NOT NULL,
    efeito    JSON,
    preco     INT NOT NULL DEFAULT 0,
    svg_slug  VARCHAR(80) NOT NULL DEFAULT 'item-generico',
    raridade  ENUM('comum','raro','epico','lendario') NOT NULL DEFAULT 'comum',
    compravel TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Personagens (avatar do jogador; 1 por usuário)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS personagens (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT NOT NULL UNIQUE,
    nome         VARCHAR(80) NOT NULL,
    classe       ENUM('mago','guerreiro','ranger') NOT NULL,
    nivel        INT NOT NULL DEFAULT 1,
    xp           INT NOT NULL DEFAULT 0,
    hp_max       INT NOT NULL DEFAULT 100,
    hp_atual     INT NOT NULL DEFAULT 100,
    mp_max       INT NOT NULL DEFAULT 40,
    mp_atual     INT NOT NULL DEFAULT 40,
    ouro         INT NOT NULL DEFAULT 50,
    reputacao    INT NOT NULL DEFAULT 0,
    capitulo     INT NOT NULL DEFAULT 0,
    criado_em    DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Fases (nós do mapa: história, lição, chefe, final, secundária)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fases (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    mestre_id        INT NULL,
    ordem_global     INT NOT NULL,
    nome             VARCHAR(150) NOT NULL,
    tipo             ENUM('historia','licao','chefe','chefe_final','secundaria') NOT NULL DEFAULT 'licao',
    descricao        TEXT,
    inimigo_nome     VARCHAR(120),
    inimigo_svg      VARCHAR(80),
    inimigo_hp       INT NOT NULL DEFAULT 60,
    inimigo_ataque   INT NOT NULL DEFAULT 10,
    xp_recompensa    INT NOT NULL DEFAULT 50,
    ouro_recompensa  INT NOT NULL DEFAULT 20,
    item_drop_id     INT NULL,
    requisito_fase_id INT NULL,
    FOREIGN KEY (mestre_id) REFERENCES mestres(id) ON DELETE SET NULL,
    FOREIGN KEY (item_drop_id) REFERENCES itens(id) ON DELETE SET NULL,
    FOREIGN KEY (requisito_fase_id) REFERENCES fases(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. Desafios (perguntas de uma fase; 6 tipos)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS desafios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    fase_id     INT NOT NULL,
    ordem       INT NOT NULL DEFAULT 0,
    tipo        ENUM('multipla','vf','completar','erro','ordenar','arrastar') NOT NULL,
    assunto     ENUM('php','mvc','sql','poo','estruturas','redes','logica') NOT NULL,
    pergunta    TEXT NOT NULL,
    codigo      TEXT,
    opcoes      JSON,
    resposta    JSON NOT NULL,
    explicacao  TEXT NOT NULL,
    dificuldade TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY (fase_id) REFERENCES fases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. Inventário (itens que cada personagem possui)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inventario (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    personagem_id INT NOT NULL,
    item_id       INT NOT NULL,
    quantidade    INT NOT NULL DEFAULT 1,
    equipado      TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uq_inv (personagem_id, item_id),
    FOREIGN KEY (personagem_id) REFERENCES personagens(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES itens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. Progresso por fase (estrelas, acertos, uso de IA)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS progresso_fases (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    personagem_id INT NOT NULL,
    fase_id       INT NOT NULL,
    estrelas      TINYINT NOT NULL DEFAULT 0,
    acertos       INT NOT NULL DEFAULT 0,
    erros         INT NOT NULL DEFAULT 0,
    usou_ia       TINYINT(1) NOT NULL DEFAULT 0,
    concluida_em  DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_prog (personagem_id, fase_id),
    FOREIGN KEY (personagem_id) REFERENCES personagens(id) ON DELETE CASCADE,
    FOREIGN KEY (fase_id) REFERENCES fases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. Conquistas (catálogo)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS conquistas (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    codigo    VARCHAR(60) NOT NULL UNIQUE,
    nome      VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    svg_slug  VARCHAR(80) NOT NULL DEFAULT 'conquista-generica',
    secreta   TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. Conquistas obtidas por personagem
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS conquistas_personagem (
    personagem_id INT NOT NULL,
    conquista_id  INT NOT NULL,
    obtida_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (personagem_id, conquista_id),
    FOREIGN KEY (personagem_id) REFERENCES personagens(id) ON DELETE CASCADE,
    FOREIGN KEY (conquista_id) REFERENCES conquistas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. Diálogos (falas das fases, com variante padrão/IA)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dialogos (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    fase_id  INT NOT NULL,
    momento  ENUM('antes','vitoria','derrota') NOT NULL DEFAULT 'antes',
    variante ENUM('padrao','ia') NOT NULL DEFAULT 'padrao',
    ordem    INT NOT NULL DEFAULT 0,
    falante  VARCHAR(80) NOT NULL,
    svg_slug VARCHAR(80),
    texto    TEXT NOT NULL,
    FOREIGN KEY (fase_id) REFERENCES fases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. Escolhas narrativas (decisões que afetam os finais)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS escolhas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    personagem_id INT NOT NULL,
    codigo        VARCHAR(60) NOT NULL,
    valor         VARCHAR(120) NOT NULL,
    criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personagem_id) REFERENCES personagens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 13. Log de respostas (estatísticas por matéria)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS respostas_log (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    personagem_id INT NOT NULL,
    desafio_id    INT NOT NULL,
    correta       TINYINT(1) NOT NULL,
    usou_ia       TINYINT(1) NOT NULL DEFAULT 0,
    respondido_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personagem_id) REFERENCES personagens(id) ON DELETE CASCADE,
    FOREIGN KEY (desafio_id) REFERENCES desafios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
