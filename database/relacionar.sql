-- ============================================================================
-- Questoes de relacionar (tipo arrastar) - IMPORT IDEMPOTENTE
-- Gerado a partir do banco do host (fonte de verdade em producao).
-- Nao editar a mao. Rodar de novo nao duplica: identidade logica = fase_id + pergunta.
-- ============================================================================

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada artefato ao guardião correto do MVC.', NULL, '{\"itens\": [\"Tela do usuário\", \"Regra de negócio\", \"Decisão de fluxo\", \"Mapeamento objeto-relacional\"], \"alvos\": [\"View\", \"Model\", \"Controller\", \"ORM\"]}', '[0, 1, 2, 3]', 'Cada item pertence a uma responsabilidade específica.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada artefato ao guardião correto do MVC.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada sintoma para a camada mais provável.', NULL, '{\"itens\": [\"Botão não dispara ação\", \"Dado calculado errado\", \"Layout quebrado\", \"Objeto não mapeia tabela\"], \"alvos\": [\"Controller\", \"Model\", \"View\", \"ORM\"]}', '[0, 1, 2, 3]', 'O sintoma indica onde começar a investigação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada sintoma para a camada mais provável.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada missão do Porto da Sintaxe para sua camada.', NULL, '{\"itens\": [\"Mostrar formulário\", \"Receber comando\", \"Aplicar regra\", \"Mapear objeto para tabela\"], \"alvos\": [\"View\", \"Controller\", \"Model\", \"ORM\"]}', '[0, 1, 2, 3]', 'A arquitetura fica limpa quando cada missão tem dono.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada missão do Porto da Sintaxe para sua camada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada conceito ao significado correto.', NULL, '{\"itens\": [\"Model\", \"View\", \"Controller\", \"Roteamento\"], \"alvos\": [\"Dados e regras\", \"Apresentação\", \"Coordenação\", \"URL para ação\"]}', '[0, 1, 2, 3]', 'Esses conceitos compõem a organização da fase O Padrão MVC.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada conceito ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 7,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=7) AS _m),
       'arrastar', 'mvc', 'Arraste cada etapa ao caminho correto da requisição.', NULL, '{\"itens\": [\"Navegador do jogador\", \"Ação do Controller\", \"Consulta ao Model\", \"Resposta visual\"], \"alvos\": [\"Início da interação\", \"Coordenação\", \"Processamento de dados\", \"View final\"]}', '[0, 1, 2, 3]', 'O fluxo evita mistura de responsabilidades.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=7 AND pergunta='Arraste cada etapa ao caminho correto da requisição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada palavra do Baú para seu poder SQL.', NULL, '{\"itens\": [\"SELECT\", \"FROM\", \"WHERE\", \"ORDER BY\"], \"alvos\": [\"Escolher colunas\", \"Indicar tabela\", \"Filtrar registros\", \"Ordenar resultado\"]}', '[0, 1, 2, 3]', 'Essas cláusulas formam a base de uma consulta SQL.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada palavra do Baú para seu poder SQL.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada comando de manipulação ao efeito correto.', NULL, '{\"itens\": [\"INSERT\", \"UPDATE\", \"DELETE\", \"SELECT\"], \"alvos\": [\"Inserir registro\", \"Alterar registro\", \"Remover registro\", \"Consultar dados\"]}', '[0, 1, 2, 3]', 'Cada comando tem uma função específica no banco.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada comando de manipulação ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada fragmento ao lugar correto da consulta.', NULL, '{\"itens\": [\"SELECT nome\", \"FROM herois\", \"WHERE nivel > 3\", \"ORDER BY nome\"], \"alvos\": [\"Projeção\", \"Origem\", \"Filtro\", \"Ordenação\"]}', '[0, 1, 2, 3]', 'A consulta fica legível quando cada parte cumpre seu papel.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada fragmento ao lugar correto da consulta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada situação ao recurso SQL mais adequado.', NULL, '{\"itens\": [\"Contar registros\", \"Unir tabelas\", \"Filtrar por cidade\", \"Ordenar por data\"], \"alvos\": [\"COUNT\", \"JOIN\", \"WHERE\", \"ORDER BY\"]}', '[0, 1, 2, 3]', 'COUNT, JOIN, WHERE e ORDER BY resolvem necessidades diferentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada situação ao recurso SQL mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 8,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=8) AS _m),
       'arrastar', 'sql', 'Arraste cada perigo à proteção correspondente.', NULL, '{\"itens\": [\"DELETE sem filtro\", \"UPDATE geral sem alvo\", \"Consulta confusa sem origem\", \"Resultado fora de ordem\"], \"alvos\": [\"Usar WHERE\", \"Usar WHERE com chave\", \"Usar FROM\", \"Usar ORDER BY\"]}', '[0, 1, 2, 3]', 'Filtros, origem e ordenação tornam o feitiço SQL mais controlado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=8 AND pergunta='Arraste cada perigo à proteção correspondente.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada peça da forja ao conceito correto.', NULL, '{\"itens\": [\"class Heroi\", \"new Heroi()\", \"nome\", \"atacar()\"], \"alvos\": [\"Classe\", \"Objeto/instância\", \"Atributo\", \"Método\"]}', '[0, 1, 2, 3]', 'Classe, objeto, atributo e método são os fundamentos da fase.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada peça da forja ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra Java ao seu papel.', NULL, '{\"itens\": [\"new\", \"this\", \"static\", \"constructor\"], \"alvos\": [\"Cria objeto\", \"Objeto atual\", \"Membro da classe\", \"Inicializa objeto\"]}', '[0, 1, 2, 3]', 'Essas palavras aparecem no vocabulário central de classes e objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada palavra Java ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada exemplo ao tipo correto.', NULL, '{\"itens\": [\"int vida\", \"void curar()\", \"Heroi()\", \"Heroi h\"], \"alvos\": [\"Atributo\", \"Método\", \"Construtor\", \"Referência de objeto\"]}', '[0, 1, 2, 3]', 'Cada trecho mostra uma parte da construção orientada a objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada exemplo ao tipo correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada descrição ao conceito.', NULL, '{\"itens\": [\"Molde\", \"Coisa criada do molde\", \"Estado guardado\", \"Comportamento executável\"], \"alvos\": [\"Classe\", \"Objeto\", \"Atributo\", \"Método\"]}', '[0, 1, 2, 3]', 'A metáfora do molde ajuda a entender a Cidadela dos Objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada descrição ao conceito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada runa ao momento da criação.', NULL, '{\"itens\": [\"Definir classe\", \"Chamar construtor\", \"Usar new\", \"Acessar método\"], \"alvos\": [\"Antes da instância\", \"Durante inicialização\", \"Criação do objeto\", \"Comportamento do objeto\"]}', '[0, 1, 2, 3]', 'A sequência mostra como um objeto nasce e age.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada runa ao momento da criação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada artefato da Cidadela ao seu papel.', NULL, '{\"itens\":[\"Classe\",\"Objeto\",\"Atributo\",\"Método\"],\"alvos\":[\"Molde do ser\",\"Instância criada\",\"Estado guardado\",\"Comportamento executado\"]}', '[0,1,2,3]', 'Esses são os elementos centrais de classes e objetos.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada artefato da Cidadela ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra-chave ao efeito correto.', NULL, '{\"itens\":[\"new\",\"this\",\"static\",\"null\"],\"alvos\":[\"Cria instância\",\"Refere-se ao objeto atual\",\"Pertence à classe\",\"Ausência de referência\"]}', '[0,1,2,3]', 'As palavras-chave controlam criação, referência e escopo de membros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada palavra-chave ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao tipo de membro.', NULL, '{\"itens\":[\"String nome\",\"void atacar()\",\"Heroi(String nome)\",\"static int total\"],\"alvos\":[\"Atributo\",\"Método\",\"Construtor\",\"Membro de classe\"]}', '[0,1,2,3]', 'Cada fragmento cumpre papel específico dentro da classe.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada fragmento ao tipo de membro.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada situação à decisão correta.', NULL, '{\"itens\":[\"Vida do herói\",\"Total global de heróis\",\"Inicialização do nome\",\"Ação pular\"],\"alvos\":[\"Atributo de instância\",\"static\",\"Construtor\",\"Método\"]}', '[0,1,2,3]', 'Estado individual, estado compartilhado, inicialização e comportamento têm papéis distintos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada situação à decisão correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 11,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=11) AS _m),
       'arrastar', 'poo', 'Arraste cada criatura ao conceito que ela testa.', NULL, '{\"itens\":[\"Golem de Classe\",\"Espelho this\",\"Selo static\",\"Forja new\"],\"alvos\":[\"Molde\",\"Objeto atual\",\"Membro compartilhado\",\"Instanciação\"]}', '[0,1,2,3]', 'A narrativa reforça os conceitos básicos de POO.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=11 AND pergunta='Arraste cada criatura ao conceito que ela testa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada modificador ao nível de abertura.', NULL, '{\"itens\": [\"private\", \"protected\", \"public\"], \"alvos\": [\"Mais restrito\", \"Intermediário/subclasses e pacote\", \"Mais aberto\"]}', '[0, 1, 2]', 'Os modificadores controlam visibilidade.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada modificador ao nível de abertura.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada método ao uso correto.', NULL, '{\"itens\": [\"getNome()\", \"setNome(String nome)\", \"getNivel()\", \"setNivel(int nivel)\"], \"alvos\": [\"Ler nome\", \"Alterar nome\", \"Ler nível\", \"Alterar nível\"]}', '[0, 1, 2, 3]', 'Getters leem; setters alteram.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada método ao uso correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça do Espião para a defesa.', NULL, '{\"itens\": [\"Atributo public\", \"Alteração sem validação\", \"Classe perdida no projeto\", \"Acesso direto ao campo\"], \"alvos\": [\"Usar private\", \"Usar setter controlado\", \"Usar package organizado\", \"Usar getter/setter\"]}', '[0, 1, 2, 3]', 'Encapsulamento combina proteção de estado e organização.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada ameaça do Espião para a defesa.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada termo ao conceito correto.', NULL, '{\"itens\": [\"Estado interno\", \"Getter\", \"Setter\", \"Package\"], \"alvos\": [\"Dados do objeto\", \"Método de leitura\", \"Método de alteração\", \"Organização de classes\"]}', '[0, 1, 2, 3]', 'São termos centrais da fase Encapsulamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada termo ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada linha ao papel no castelo.', NULL, '{\"itens\": [\"private int vida;\", \"public int getVida()\", \"public void setVida(int vida)\", \"package br.com.jogo;\"], \"alvos\": [\"Campo protegido\", \"Leitura controlada\", \"Alteração controlada\", \"Organização\"]}', '[0, 1, 2, 3]', 'Cada linha contribui para o encapsulamento ou organização.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada linha ao papel no castelo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada modificador ao alcance mais adequado.', NULL, '{\"itens\":[\"private\",\"public\",\"protected\",\"sem modificador\"],\"alvos\":[\"Somente a própria classe\",\"Acesso geral\",\"Subclasses e pacote\",\"Acesso de pacote\"]}', '[0,1,2,3]', 'Cada modificador define um nível de visibilidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada modificador ao alcance mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada membro ao papel no encapsulamento.', NULL, '{\"itens\":[\"atributo private\",\"getter\",\"setter\",\"pacote\"],\"alvos\":[\"Estado protegido\",\"Leitura controlada\",\"Alteração controlada\",\"Organização de classes\"]}', '[0,1,2,3]', 'Encapsulamento combina estado oculto, acesso controlado e organização.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada membro ao papel no encapsulamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça do Espião à defesa correta.', NULL, '{\"itens\":[\"Alteração direta\",\"Valor inválido\",\"Classe perdida no reino\",\"Exposição de lista interna\"],\"alvos\":[\"private\",\"validação no setter\",\"package organizado\",\"cópia ou lista imutável\"]}', '[0,1,2,3]', 'Cada risco pede uma barreira específica.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada ameaça do Espião à defesa correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada método ao uso correto.', NULL, '{\"itens\":[\"getVida()\",\"setVida(int)\",\"getNome()\",\"setNome(String)\"],\"alvos\":[\"Consultar vida\",\"Alterar vida\",\"Consultar nome\",\"Alterar nome\"]}', '[0,1,2,3]', 'Prefixos get e set indicam leitura e alteração.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada método ao uso correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 12,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=12) AS _m),
       'arrastar', 'poo', 'Arraste cada decisão à boa prática.', NULL, '{\"itens\":[\"Dado interno sensível\",\"Operação pública necessária\",\"Acesso só por herança\",\"Classes relacionadas\"],\"alvos\":[\"private\",\"método public\",\"protected\",\"mesmo pacote\"]}', '[0,1,2,3]', 'A visibilidade deve refletir a necessidade real de acesso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=12 AND pergunta='Arraste cada decisão à boa prática.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada símbolo da Quimera ao significado.', NULL, '{\"itens\":[\"extends\",\"super\",\"@Override\",\"composição\"],\"alvos\":[\"Herda de uma classe\",\"Acessa a superclasse\",\"Sobrescreve método\",\"Relação tem-um\"]}', '[0,1,2,3]', 'Esses elementos modelam herança, sobrescrita e composição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada símbolo da Quimera ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada relação ao melhor modelo.', NULL, '{\"itens\":[\"Dragao é Monstro\",\"Carro tem Motor\",\"Pedido tem Itens\",\"Cachorro é Animal\"],\"alvos\":[\"herança\",\"composição\",\"composição\",\"herança\"]}', '[0,1,2,3]', 'é-um favorece herança; tem-um favorece composição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada relação ao melhor modelo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito ao efeito no reúso.', NULL, '{\"itens\":[\"Herança\",\"Composição\",\"Polimorfismo\",\"Sobrescrita\"],\"alvos\":[\"Reúso por especialização\",\"Reúso por partes\",\"Uso por tipo comum\",\"Novo comportamento no filho\"]}', '[0,1,2,3]', 'Cada mecanismo reutiliza ou especializa comportamento de forma diferente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada conceito ao efeito no reúso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada criatura ao alerta de projeto.', NULL, '{\"itens\":[\"Quimera da Herança\",\"Armadura Composta\",\"Eco Polimórfico\",\"Selo Override\"],\"alvos\":[\"Evitar hierarquia falsa\",\"Preferir partes quando há tem-um\",\"Mesmo chamado, formas diferentes\",\"Verificar sobrescrita\"]}', '[0,1,2,3]', 'A fase ensina a escolher herança ou composição com cuidado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada criatura ao alerta de projeto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 13,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=13) AS _m),
       'arrastar', 'poo', 'Arraste cada trecho ao diagnóstico.', NULL, '{\"itens\":[\"class A extends B\",\"super()\",\"new Motor() em Carro\",\"List<Personagem> com Mago e Guerreiro\"],\"alvos\":[\"Herança\",\"Construtor da superclasse\",\"Composição\",\"Polimorfismo\"]}', '[0,1,2,3]', 'Os trechos representam recursos clássicos de POO em Java.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=13 AND pergunta='Arraste cada trecho ao diagnóstico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada elemento das Interfaces Secretas ao significado.', NULL, '{\"itens\":[\"interface\",\"implements\",\"método abstrato\",\"contrato\"],\"alvos\":[\"Define comportamentos esperados\",\"Classe assume cumprir\",\"Assinatura sem corpo\",\"Promessa de uso\"]}', '[0,1,2,3]', 'Interfaces funcionam como contratos de comportamento.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada elemento das Interfaces Secretas ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada trecho ao conceito.', NULL, '{\"itens\":[\"interface Curavel\",\"class Pocao implements Curavel\",\"void curar();\",\"Curavel c = new Pocao()\"],\"alvos\":[\"Contrato\",\"Implementação\",\"Método exigido\",\"Polimorfismo\"]}', '[0,1,2,3]', 'A interface permite usar objetos por um contrato comum.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada trecho ao conceito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada contrato ao guardião compatível.', NULL, '{\"itens\":[\"Voador\",\"Nadador\",\"Atacante\",\"Curavel\"],\"alvos\":[\"DragaoAlado\",\"Sereia\",\"Guerreiro\",\"Pocao\"]}', '[0,1,2,3]', 'Classes diferentes podem implementar contratos diferentes.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada contrato ao guardião compatível.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada vantagem da interface ao efeito.', NULL, '{\"itens\":[\"Baixo acoplamento\",\"Troca de implementação\",\"Múltiplos contratos\",\"Polimorfismo\"],\"alvos\":[\"Depender menos da classe concreta\",\"Substituir classe sem mudar cliente\",\"Cumprir vários papéis\",\"Usar pelo tipo do contrato\"]}', '[0,1,2,3]', 'Interfaces ajudam a organizar e flexibilizar o código.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada vantagem da interface ao efeito.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 14,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=14) AS _m),
       'arrastar', 'poo', 'Arraste cada erro do Contrato Fantasma à correção.', NULL, '{\"itens\":[\"Método ausente\",\"Nome diferente\",\"Dependência concreta\",\"Tentativa de construtor\"],\"alvos\":[\"Implementar assinatura\",\"Usar mesmo nome e parâmetros\",\"Usar tipo da interface\",\"Remover construtor da interface\"]}', '[0,1,2,3]', 'As correções preservam o contrato e o polimorfismo.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=14 AND pergunta='Arraste cada erro do Contrato Fantasma à correção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito ao golpe correto contra a Gárgula.', NULL, '{\"itens\": [\"SRP\", \"Coesão\", \"God Class\", \"Refatoração\"], \"alvos\": [\"Responsabilidade única\", \"Responsabilidades relacionadas\", \"Classe que faz demais\", \"Dividir e melhorar o código\"]}', '[0, 1, 2, 3]', 'Esses conceitos formam o núcleo da fase chefe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada conceito ao golpe correto contra a Gárgula.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada recurso Java ao significado.', NULL, '{\"itens\": [\"Generics\", \"Lambda\", \"Coleção\", \"Exceção\"], \"alvos\": [\"Tipo parametrizado\", \"Função/expressão compacta\", \"Grupo de elementos\", \"Falha tratável\"]}', '[0, 1, 2, 3]', 'Generics, lambdas, coleções e exceções aparecem no conjunto de tópicos da fase.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada recurso Java ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada cheiro de código à cura.', NULL, '{\"itens\": [\"Classe faz tudo\", \"List sem tipo\", \"catch vazio\", \"Método sem relação com a classe\"], \"alvos\": [\"Aplicar SRP\", \"Usar generics\", \"Tratar exceção\", \"Aumentar coesão\"]}', '[0, 1, 2, 3]', 'Cada cura reduz um problema comum da Gárgula.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada cheiro de código à cura.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao tópico da fase.', NULL, '{\"itens\": [\"List<String>\", \"x -> x + 1\", \"try/catch\", \"classe com 20 funções distintas\"], \"alvos\": [\"Generics\", \"Lambda\", \"Exceções\", \"God Class\"]}', '[0, 1, 2, 3]', 'Os fragmentos representam diretamente os tópicos listados para a chefe.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada fragmento ao tópico da fase.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada missão ao artefato mais adequado.', NULL, '{\"itens\": [\"Guardar vários nomes\", \"Processar cada item com expressão curta\", \"Responder a falha de arquivo\", \"Dividir classe gigante\"], \"alvos\": [\"Coleção\", \"Lambda\", \"Exceção\", \"SRP/refatoração\"]}', '[0, 1, 2, 3]', 'A escolha correta enfraquece a Gárgula God-Class.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada missão ao artefato mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada conceito da Gárgula ao significado.', NULL, '{\"itens\":[\"SRP\",\"God Class\",\"Coesão\",\"Generics\"],\"alvos\":[\"Responsabilidade única\",\"Classe com tarefas demais\",\"Unidade de propósito\",\"Tipo parametrizado\"]}', '[0,1,2,3]', 'Esses conceitos ajudam a organizar código orientado a objetos.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada conceito da Gárgula ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada coleção ao uso típico.', NULL, '{\"itens\":[\"List\",\"Set\",\"Map\",\"Stream\"],\"alvos\":[\"Sequência de elementos\",\"Conjunto sem duplicados\",\"Chave associada a valor\",\"Fluxo de operações\"]}', '[0,1,2,3]', 'Coleções possuem papéis diferentes.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada coleção ao uso típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada palavra de exceção ao papel.', NULL, '{\"itens\":[\"try\",\"catch\",\"throw\",\"Exception\"],\"alvos\":[\"Bloco arriscado\",\"Tratamento da falha\",\"Lança falha\",\"Tipo de falha\"]}', '[0,1,2,3]', 'Essas palavras aparecem no tratamento de erros.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada palavra de exceção ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada fragmento ao recurso.', NULL, '{\"itens\":[\"List<String>\",\"x -> x > 0\",\"new ArrayList<>()\",\"catch (IOException e)\"],\"alvos\":[\"Generics\",\"Lambda\",\"Coleção\",\"Captura de exceção\"]}', '[0,1,2,3]', 'A fase combina organização, coleções, lambdas e erros.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada fragmento ao recurso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 15,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=15) AS _m),
       'arrastar', 'poo', 'Arraste cada ameaça à refatoração indicada.', NULL, '{\"itens\":[\"Classe faz tudo\",\"Lista sem tipo\",\"Erro ignorado\",\"Código repetido em filtros\"],\"alvos\":[\"Separar responsabilidades\",\"Usar generics\",\"Tratar ou registrar exceção\",\"Usar lambda/reuso de critério\"]}', '[0,1,2,3]', 'Cada problema pede uma correção de projeto ou linguagem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=15 AND pergunta='Arraste cada ameaça à refatoração indicada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada criatura para sua regra de remoção.', NULL, '{\"itens\":[\"Pilha Viva\",\"Fila dos Goblins\",\"Pilha estática\",\"Fila circular\"],\"alvos\":[\"Remove o último inserido\",\"Remove o primeiro inserido\",\"Tem capacidade fixa\",\"Reaproveita posições do vetor\"]}', '[0,1,2,3]', 'Cada estrutura tem uma regra de acesso e uma estratégia de armazenamento.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada criatura para sua regra de remoção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada operação ao efeito correto.', NULL, '{\"itens\":[\"push ou adicionar\",\"pop ou remover\",\"peek ou consultar\",\"limpar\"],\"alvos\":[\"Insere elemento\",\"Remove elemento\",\"Olha sem remover\",\"Esvazia a estrutura\"]}', '[0,1,2,3]', 'Essas são operações comuns em pilhas e filas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada operação ao efeito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada armadilha ao significado correto.', NULL, '{\"itens\":[\"Overflow\",\"Underflow\",\"LIFO\",\"FIFO\"],\"alvos\":[\"Inserir quando está cheia\",\"Remover quando está vazia\",\"Último entra, primeiro sai\",\"Primeiro entra, primeiro sai\"]}', '[0,1,2,3]', 'Overflow e underflow são erros de estado; LIFO e FIFO são políticas de acesso.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada armadilha ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada referência da fila encadeada ao papel correto.', NULL, '{\"itens\":[\"base\",\"topo\",\"proximo\",\"null\"],\"alvos\":[\"Primeiro elemento a sair\",\"Último elemento inserido\",\"Ligação ao nó seguinte\",\"Fim da cadeia\"]}', '[0,1,2,3]', 'A fila encadeada depende dessas referências para manter a ordem FIFO.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada referência da fila encadeada ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 17,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=17) AS _m),
       'arrastar', 'estruturas', 'Arraste cada implementação à característica dominante.', NULL, '{\"itens\":[\"Pilha estática\",\"Pilha encadeada\",\"Fila estática\",\"Fila encadeada\"],\"alvos\":[\"Usa vetor fixo e topo\",\"Usa nós ligados ao topo\",\"Pode usar vetor circular\",\"Usa nós com base e topo\"]}', '[0,1,2,3]', 'As estruturas podem ser sequenciais ou encadeadas.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=17 AND pergunta='Arraste cada implementação à característica dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada peça da lista ao papel correto.', NULL, '{\"itens\":[\"dado\",\"proximo\",\"anterior\",\"tamanho\"],\"alvos\":[\"Valor armazenado\",\"Referência ao nó seguinte\",\"Referência ao nó anterior\",\"Quantidade de elementos\"]}', '[0,1,2,3]', 'Esses campos organizam a lista e seu percurso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada peça da lista ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada tipo de lista à característica.', NULL, '{\"itens\":[\"Lista estática\",\"Lista encadeada\",\"Lista duplamente encadeada\",\"Lista circular\"],\"alvos\":[\"Usa vetor fixo\",\"Usa nós e referências\",\"Permite voltar ao nó anterior\",\"Último nó aponta para o início\"]}', '[0,1,2,3]', 'Cada variação organiza seus nós de forma diferente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada tipo de lista à característica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada operação ao custo típico em lista encadeada.', NULL, '{\"itens\":[\"Acessar posição k\",\"Inserir no início\",\"Remover nó já localizado\",\"Percorrer todos os nós\"],\"alvos\":[\"O(n)\",\"O(1)\",\"O(1)\",\"O(n)\"]}', '[0,1,2,3]', 'O custo depende de já ter ou não a referência ao nó.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada operação ao custo típico em lista encadeada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada sentinela da Serpente ao valor que costuma indicar vazio ou fim.', NULL, '{\"itens\":[\"base vazia\",\"topo vazio\",\"proximo do último\",\"anterior do primeiro\"],\"alvos\":[\"null\",\"null\",\"null\",\"null\"]}', '[0,1,2,3]', 'O valor null indica ausência de nó em vários pontos da lista.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada sentinela da Serpente ao valor que costuma indicar vazio ou fim.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 18,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=18) AS _m),
       'arrastar', 'estruturas', 'Arraste cada frase ao tipo correto.', NULL, '{\"itens\":[\"Dados lado a lado\",\"Nós dispersos\",\"Acesso direto por índice\",\"Percurso por referências\"],\"alvos\":[\"Sequencial\",\"Encadeada\",\"Sequencial\",\"Encadeada\"]}', '[0,1,2,3]', 'Sequencial favorece índice; encadeada favorece inserções e remoções por ligação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=18 AND pergunta='Arraste cada frase ao tipo correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada termo da árvore ao significado.', NULL, '{\"itens\":[\"raiz\",\"folha\",\"filho esquerdo\",\"filho direito\"],\"alvos\":[\"Nó principal\",\"Nó sem filhos\",\"Caminho dos menores na BST\",\"Caminho dos maiores na BST\"]}', '[0,1,2,3]', 'A árvore organiza dados por relações hierárquicas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada termo da árvore ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada custo à descrição.', NULL, '{\"itens\":[\"O(1)\",\"O(log n)\",\"O(n)\",\"O(n²)\"],\"alvos\":[\"Constante\",\"Logarítmico\",\"Linear\",\"Quadrático\"]}', '[0,1,2,3]', 'Essas classes expressam crescimento de custo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada custo à descrição.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada algoritmo à ideia dominante.', NULL, '{\"itens\":[\"Busca binária\",\"Bubble sort\",\"BST\",\"AVL\"],\"alvos\":[\"Dividir intervalo ordenado\",\"Comparar vizinhos\",\"Árvore com regra de busca\",\"Árvore balanceada\"]}', '[0,1,2,3]', 'Cada técnica organiza ou procura dados de forma distinta.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada algoritmo à ideia dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada percurso ao início típico.', NULL, '{\"itens\":[\"Pré-ordem\",\"Em ordem\",\"Pós-ordem\",\"Busca na BST\"],\"alvos\":[\"Visita o nó antes dos filhos\",\"Visita esquerda antes do nó\",\"Visita filhos antes do nó\",\"Compara e escolhe lado\"]}', '[0,1,2,3]', 'Percursos definem a ordem de visita dos nós.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada percurso ao início típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 19,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=19) AS _m),
       'arrastar', 'estruturas', 'Arraste cada criatura do Ent ao risco correto.', NULL, '{\"itens\":[\"Árvore desbalanceada\",\"Bubble sort em vetor grande\",\"BST balanceada\",\"Big-O ignorado\"],\"alvos\":[\"Pode degradar a busca\",\"Pode ser lento por muitas comparações\",\"Busca tende a log n\",\"Escolha ruim de estrutura\"]}', '[0,1,2,3]', 'Análise e balanceamento evitam escolhas ineficientes.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=19 AND pergunta='Arraste cada criatura do Ent ao risco correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada busca à condição principal.', NULL, '{\"itens\":[\"Busca linear\",\"Busca binária\",\"Vetor ordenado\",\"Lista encadeada\"],\"alvos\":[\"Percorre item por item\",\"Divide intervalo ao meio\",\"Permite descartar metades\",\"Não oferece acesso direto ao meio\"]}', '[0,1,2,3]', 'O tipo de estrutura define a estratégia eficiente.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada busca à condição principal.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada custo à busca típica.', NULL, '{\"itens\":[\"O(n)\",\"O(log n)\",\"O(1)\",\"O(n²)\"],\"alvos\":[\"Busca linear no pior caso\",\"Busca binária\",\"Acesso direto a índice de vetor\",\"Alguns algoritmos com laços aninhados\"]}', '[0,1,2,3]', 'Cada padrão de acesso tem um crescimento típico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada custo à busca típica.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada situação à escolha adequada.', NULL, '{\"itens\":[\"Dados desordenados e poucos\",\"Vetor ordenado grande\",\"Lista encadeada sem índice direto\",\"Precisa manter ordem após inserir\"],\"alvos\":[\"Busca linear aceitável\",\"Busca binária indicada\",\"Evitar busca binária direta\",\"Inserir na posição correta\"]}', '[0,1,2,3]', 'O atalho depende de ordem e acesso eficiente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada situação à escolha adequada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada comparação da busca binária à ação.', NULL, '{\"itens\":[\"alvo menor que meio\",\"alvo maior que meio\",\"alvo igual ao meio\",\"intervalo vazio\"],\"alvos\":[\"Pesquisar esquerda\",\"Pesquisar direita\",\"Encontrou\",\"Não encontrado\"]}', '[0,1,2,3]', 'A comparação central decide o próximo intervalo.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada comparação da busca binária à ação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 20,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=20) AS _m),
       'arrastar', 'estruturas', 'Arraste cada placa do caminho ao significado.', NULL, '{\"itens\":[\"meio\",\"inicio\",\"fim\",\"intervalo\"],\"alvos\":[\"Elemento comparado\",\"Limite inferior\",\"Limite superior\",\"Trecho ainda pesquisado\"]}', '[0,1,2,3]', 'Busca binária controla os limites do intervalo pesquisado.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=20 AND pergunta='Arraste cada placa do caminho ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada cabeça da Hidra ao conceito correto.', NULL, '{\"itens\":[\"recursão\",\"caso base\",\"pilha de execução\",\"retorno\"],\"alvos\":[\"Função chama a si mesma\",\"Condição de parada\",\"Armazena chamadas\",\"Desempilha a chamada concluída\"]}', '[0,1,2,3]', 'Recursão depende de chamadas empilhadas e de uma parada clara.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada cabeça da Hidra ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada termo de hash ao significado.', NULL, '{\"itens\":[\"função hash\",\"colisão\",\"balde\",\"rehash\"],\"alvos\":[\"Calcula índice\",\"Conflito de índice\",\"Lista ou espaço de armazenamento\",\"Recalcula após ampliar\"]}', '[0,1,2,3]', 'Tabelas hash dependem de cálculo de índice e tratamento de colisões.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada termo de hash ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada algoritmo ao comportamento.', NULL, '{\"itens\":[\"bubble sort\",\"quick sort\",\"insertion sort\",\"busca binária\"],\"alvos\":[\"Compara vizinhos\",\"Divide e ordena recursivamente\",\"Insere no ponto correto\",\"Descarta metade do intervalo\"]}', '[0,1,2,3]', 'Cada algoritmo usa uma estratégia própria.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada algoritmo ao comportamento.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada risco ao antídoto.', NULL, '{\"itens\":[\"recursão infinita\",\"muitas colisões\",\"vetor quase ordenado\",\"tabela muito cheia\"],\"alvos\":[\"caso base\",\"melhor função hash ou encadeamento\",\"insertion sort pode ser útil\",\"rehash\"]}', '[0,1,2,3]', 'Cada problema pede uma técnica de controle adequada.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada risco ao antídoto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 21,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=21) AS _m),
       'arrastar', 'estruturas', 'Arraste cada elemento da chamada recursiva ao papel.', NULL, '{\"itens\":[\"parâmetro menor\",\"condição de parada\",\"chamada pendente\",\"resultado retornado\"],\"alvos\":[\"Aproxima do caso base\",\"Interrompe a expansão\",\"Fica na pilha\",\"Volta para quem chamou\"]}', '[0,1,2,3]', 'Uma recursão saudável avança para a parada e retorna resultados.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=21 AND pergunta='Arraste cada elemento da chamada recursiva ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada símbolo da Montanha do Cálculo ao seu significado.', NULL, '{\"itens\":[\"Limite\",\"Continuidade\",\"Limite lateral\",\"Limite no infinito\"],\"alvos\":[\"Tendência ao aproximar\",\"Sem ruptura no ponto\",\"Aproximação por um lado\",\"Comportamento para x muito grande\"]}', '[0,1,2,3]', 'Cada conceito descreve uma forma específica de analisar funções.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada símbolo da Montanha do Cálculo ao seu significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada situação ao veredito correto.', NULL, '{\"itens\":[\"laterais iguais\",\"laterais diferentes\",\"limite igual a f(a)\",\"função sem f(a)\"],\"alvos\":[\"limite bilateral pode existir\",\"limite bilateral não existe\",\"continuidade no ponto\",\"pode haver limite sem valor definido\"]}', '[0,1,2,3]', 'A fase exige distinguir limite, laterais e continuidade.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada situação ao veredito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada guardião à pergunta que ele faz.', NULL, '{\"itens\":[\"Eco da Esquerda\",\"Eco da Direita\",\"Guardião do Infinito\",\"Sentinela da Continuidade\"],\"alvos\":[\"x se aproxima por valores menores\",\"x se aproxima por valores maiores\",\"x cresce sem limite\",\"limite e valor coincidem\"]}', '[0,1,2,3]', 'A narrativa reforça os lados e o comportamento da função.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada guardião à pergunta que ele faz.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada expressão ao limite correto.', NULL, '{\"itens\":[\"1/x com x -> +infinito\",\"x + 2 com x -> 2\",\"3x com x -> 1\",\"x^2 com x -> 3\"],\"alvos\":[\"0\",\"4\",\"3\",\"9\"]}', '[0,1,2,3]', 'São avaliações diretas de tendências simples.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada expressão ao limite correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 23,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=23) AS _m),
       'arrastar', 'logica', 'Arraste cada obstáculo ao cuidado necessário.', NULL, '{\"itens\":[\"furo no ponto\",\"salto no gráfico\",\"x indo ao infinito\",\"denominador zerando\"],\"alvos\":[\"verificar se o limite ainda existe\",\"comparar limites laterais\",\"observar termos dominantes\",\"tentar simplificar antes de concluir\"]}', '[0,1,2,3]', 'Cada obstáculo pede uma leitura matemática adequada.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=23 AND pergunta='Arraste cada obstáculo ao cuidado necessário.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada artefato da Espiral ao conceito correto.', NULL, '{\"itens\":[\"Derivada\",\"Reta tangente\",\"Regra da potência\",\"Regra da cadeia\"],\"alvos\":[\"Taxa instantânea\",\"Inclinação no ponto\",\"Deriva x^n\",\"Deriva função composta\"]}', '[0,1,2,3]', 'Os principais instrumentos da fase são taxa, tangente e regras de derivação.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada artefato da Espiral ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada função à sua derivada.', NULL, '{\"itens\":[\"x^2\",\"x^3\",\"5\",\"2x\"],\"alvos\":[\"2x\",\"3x^2\",\"0\",\"2\"]}', '[0,1,2,3]', 'São derivadas básicas pela regra da potência e pela regra da constante.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada função à sua derivada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada missão à regra mais adequada.', NULL, '{\"itens\":[\"x^5\",\"f(x)g(x)\",\"f(g(x))\",\"constante 9\"],\"alvos\":[\"potência\",\"produto\",\"cadeia\",\"constante\"]}', '[0,1,2,3]', 'Cada forma algébrica aciona uma regra de derivação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada missão à regra mais adequada.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada interpretação ao nome correto.', NULL, '{\"itens\":[\"velocidade instantânea\",\"inclinação da tangente\",\"variação média em intervalo\",\"intervalo tendendo a zero\"],\"alvos\":[\"derivada aplicada\",\"leitura geométrica\",\"taxa média\",\"passo do limite\"]}', '[0,1,2,3]', 'A derivada conecta taxas médias, limite e interpretação geométrica.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada interpretação ao nome correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 24,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=24) AS _m),
       'arrastar', 'logica', 'Arraste cada erro ao conserto.', NULL, '{\"itens\":[\"d(x^4)=x^3\",\"d(8)=8\",\"esquecer g\'(x)\",\"usar área para derivada\"],\"alvos\":[\"4x^3\",\"0\",\"aplicar cadeia completa\",\"usar taxa de variação\"]}', '[0,1,2,3]', 'Os consertos preservam as regras básicas de derivação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=24 AND pergunta='Arraste cada erro ao conserto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada relíquia do Colosso Menor ao conceito correto.', NULL, '{\"itens\":[\"Primitiva\",\"Integral indefinida\",\"Integral definida\",\"Teorema Fundamental\"],\"alvos\":[\"Função cuja derivada retorna f\",\"Família com +C\",\"Acumulação em intervalo\",\"Conecta derivada e integral\"]}', '[0,1,2,3]', 'A fase trata de primitivas, acumulação e relação com derivadas.', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada relíquia do Colosso Menor ao conceito correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada expressão à integral indefinida correta.', NULL, '{\"itens\":[\"2x\",\"x\",\"x^2\",\"0\"],\"alvos\":[\"x^2 + C\",\"x^2/2 + C\",\"x^3/3 + C\",\"C\"]}', '[0,1,2,3]', 'As correspondências seguem a regra da potência e a constante de integração.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada expressão à integral indefinida correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada pergunta ao tipo de integral.', NULL, '{\"itens\":[\"Qual família de funções deriva para f?\",\"Quanto acumula de a até b?\",\"Qual área sob a curva no intervalo?\",\"Qual primitiva geral?\"],\"alvos\":[\"indefinida\",\"definida\",\"definida\",\"indefinida\"]}', '[0,1,2,3]', 'Integrais definidas têm intervalo; indefinidas geram primitivas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada pergunta ao tipo de integral.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada erro ao conserto.', NULL, '{\"itens\":[\"esquecer +C\",\"usar F(a)-F(b)\",\"integrar x^2 como 2x\",\"confundir área com derivada\"],\"alvos\":[\"adicionar constante\",\"usar F(b)-F(a)\",\"usar x^3/3\",\"usar integral definida\"]}', '[0,1,2,3]', 'Cada conserto recupera a lógica correta da integração.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada erro ao conserto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 25,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=25) AS _m),
       'arrastar', 'logica', 'Arraste cada símbolo de batalha ao papel matemático.', NULL, '{\"itens\":[\"a\",\"b\",\"F\",\"C\"],\"alvos\":[\"limite inferior\",\"limite superior\",\"primitiva\",\"constante de integração\"]}', '[0,1,2,3]', 'Na notação de integrais, extremos, primitiva e constante têm papéis próprios.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=25 AND pergunta='Arraste cada símbolo de batalha ao papel matemático.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada camada OSI ao poder correto da torre.', NULL, '{\"itens\":[\"Física\",\"Enlace de dados\",\"Rede\",\"Transporte\"],\"alvos\":[\"Sinais, cabos e bits\",\"Quadros entre pontos físicos\",\"Roteamento e IP\",\"Comunicação fim a fim\"]}', '[0,1,2,3]', 'Cada camada possui uma responsabilidade própria no fluxo da rede.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada camada OSI ao poder correto da torre.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada sigla ao território correto do mapa de Cassandro.', NULL, '{\"itens\":[\"LAN\",\"MAN\",\"WAN\",\"WLAN\"],\"alvos\":[\"Área local\",\"Área metropolitana\",\"Área geográfica ampla\",\"Rede local sem fio\"]}', '[0,1,2,3]', 'As siglas classificam redes pelo alcance e forma de acesso.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada sigla ao território correto do mapa de Cassandro.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada camada TCP/IP ao equivalente mais próximo.', NULL, '{\"itens\":[\"Aplicação\",\"Transporte\",\"Inter-redes\",\"Host/Rede\"],\"alvos\":[\"Aplicação do OSI\",\"Transporte do OSI\",\"Rede do OSI\",\"Física + Enlace do OSI\"]}', '[0,1,2,3]', 'O TCP/IP simplifica e agrupa algumas funções do OSI.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada camada TCP/IP ao equivalente mais próximo.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada item ao tipo correto: payload ou overhead.', NULL, '{\"itens\":[\"Cabeçalho IP\",\"Mensagem HTTP\",\"Cabeçalho TCP\",\"Dados do usuário\"],\"alvos\":[\"Overhead\",\"Payload\",\"Overhead\",\"Payload\"]}', '[0,1,2,3]', 'Cabeçalhos são controle adicional; dados da aplicação são conteúdo transportado.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada item ao tipo correto: payload ou overhead.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 29,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=29) AS _m),
       'arrastar', 'redes', 'Arraste cada criatura da torre para a camada onde costuma agir.', NULL, '{\"itens\":[\"Roteador\",\"Switch\",\"Navegador web\",\"Cabo UTP\"],\"alvos\":[\"Rede\",\"Enlace de dados\",\"Aplicação\",\"Física\"]}', '[0,1,2,3]', 'Roteadores lidam com rotas, switches com enlace, navegadores com aplicação e cabos com a camada física.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=29 AND pergunta='Arraste cada criatura da torre para a camada onde costuma agir.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada campo ou conceito IPv4 ao seu papel.', NULL, '{\"itens\":[\"TTL\",\"Endereço de origem\",\"Endereço de destino\",\"Checksum do cabeçalho\"],\"alvos\":[\"Limita saltos\",\"Identifica remetente\",\"Identifica alvo\",\"Verifica erro no cabeçalho\"]}', '[0,1,2,3]', 'Esses campos ajudam o datagrama a circular corretamente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada campo ou conceito IPv4 ao seu papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada item ao significado correto.', NULL, '{\"itens\":[\"DNS\",\"Gateway\",\"Loopback\",\"Broadcast\"],\"alvos\":[\"Nome para IP\",\"Saída para outra rede\",\"Teste local\",\"Mensagem para todos da rede\"]}', '[0,1,2,3]', 'São conceitos centrais de IP, nomes e rotas.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada item ao significado correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada exemplo à categoria correta.', NULL, '{\"itens\":[\"192.168.1.0/24\",\"192.168.1.255\",\"192.168.1.50\",\"127.0.0.1\"],\"alvos\":[\"Endereço de rede\",\"Broadcast\",\"Host\",\"Loopback\"]}', '[0,1,2,3]', 'Cada endereço cumpre um papel específico.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada exemplo à categoria correta.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada topologia ao traço dominante.', NULL, '{\"itens\":[\"Estrela\",\"Anel\",\"Barramento\",\"Rede roteada\"],\"alvos\":[\"Elemento central\",\"Nós ligados a dois vizinhos\",\"Meio compartilhado\",\"Caminho entre redes\"]}', '[0,1,2,3]', 'Topologias e roteamento organizam caminhos de comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada topologia ao traço dominante.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 30,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=30) AS _m),
       'arrastar', 'redes', 'Arraste cada notação de máscara ao equivalente aproximado.', NULL, '{\"itens\":[\"/8\",\"/16\",\"/24\",\"255.255.254.0\"],\"alvos\":[\"255.0.0.0\",\"255.255.0.0\",\"255.255.255.0\",\"/23\"]}', '[0,1,2,3]', 'A notação CIDR representa a quantidade de bits 1 na máscara.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=30 AND pergunta='Arraste cada notação de máscara ao equivalente aproximado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada protocolo ao papel correto.', NULL, '{\"itens\":[\"TCP\",\"UDP\",\"HTTP\",\"HTTPS\"],\"alvos\":[\"Transporte confiável\",\"Transporte sem conexão\",\"Web em hipertexto\",\"Web com segurança\"]}', '[0,1,2,3]', 'Cada protocolo atua em um papel específico na comunicação.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada protocolo ao papel correto.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada porta ao serviço esperado no mapa da fase.', NULL, '{\"itens\":[\"80\",\"443\",\"53\",\"25\"],\"alvos\":[\"HTTP\",\"HTTPS\",\"DNS\",\"SMTP\"]}', '[0,1,2,3]', 'Essas portas são referências usuais para serviços de rede.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada porta ao serviço esperado no mapa da fase.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada status HTTP ao significado.', NULL, '{\"itens\":[\"200\",\"404\",\"500\",\"301\"],\"alvos\":[\"Sucesso\",\"Não encontrado\",\"Erro do servidor\",\"Redirecionamento\"]}', '[0,1,2,3]', 'Os códigos HTTP indicam o resultado da requisição.', 2
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada status HTTP ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada campo TCP ao seu uso.', NULL, '{\"itens\":[\"SEQ\",\"ACK\",\"Janela de recepção\",\"FIN\"],\"alvos\":[\"Sequência\",\"Reconhecimento\",\"Controle de fluxo\",\"Encerramento\"]}', '[0,1,2,3]', 'O TCP usa campos e flags para controlar a comunicação.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada campo TCP ao seu uso.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 31,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=31) AS _m),
       'arrastar', 'redes', 'Arraste cada situação ao protocolo mais adequado.', NULL, '{\"itens\":[\"Página web comum\",\"Videoconferência em tempo real\",\"Acesso web seguro\",\"Serviço que exige entrega ordenada\"],\"alvos\":[\"HTTP\",\"UDP\",\"HTTPS\",\"TCP\"]}', '[0,1,2,3]', 'A escolha depende de aplicação, segurança, latência e confiabilidade.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=31 AND pergunta='Arraste cada situação ao protocolo mais adequado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada sinal TCP à função no resgate do pacote.', NULL, '{\"itens\":[\"ACK\",\"SEQ\",\"Janela de recepção\",\"Timeout\"],\"alvos\":[\"Reconhecer recebimento\",\"Ordenar fragmentos\",\"Controlar fluxo\",\"Detectar espera excessiva\"]}', '[0,1,2,3]', 'Esses recursos ajudam o TCP a manter comunicação confiável.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada sinal TCP à função no resgate do pacote.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada evento ao efeito esperado.', NULL, '{\"itens\":[\"ACK não chega\",\"Pacote chega fora de ordem\",\"Janela pequena\",\"ACK válido\"],\"alvos\":[\"Retransmitir após timeout\",\"Reordenar por SEQ\",\"Enviar menos dados\",\"Continuar transmissão\"]}', '[0,1,2,3]', 'O TCP adapta o envio conforme confirmações e capacidade do receptor.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada evento ao efeito esperado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada termo ao monstro que ele derrota.', NULL, '{\"itens\":[\"ACK\",\"Retransmissão\",\"Controle de fluxo\",\"Número de sequência\"],\"alvos\":[\"Dúvida de recebimento\",\"Perda de pacote\",\"Excesso no receptor\",\"Ordem embaralhada\"]}', '[0,1,2,3]', 'Cada mecanismo reduz um tipo de problema de transporte.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada termo ao monstro que ele derrota.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada protocolo ao comportamento diante da perda.', NULL, '{\"itens\":[\"TCP\",\"UDP\",\"HTTP sobre TCP\",\"Aplicação em tempo real sobre UDP\"],\"alvos\":[\"Pode retransmitir\",\"Não garante retransmissão\",\"Herda confiabilidade do TCP\",\"Pode tolerar perdas pequenas\"]}', '[0,1,2,3]', 'A confiabilidade depende do protocolo usado por baixo.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada protocolo ao comportamento diante da perda.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 32,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=32) AS _m),
       'arrastar', 'redes', 'Arraste cada pista do Eco do Timeout à interpretação.', NULL, '{\"itens\":[\"Sem ACK\",\"SEQ fora de ordem\",\"Janela zerada\",\"FIN recebido\"],\"alvos\":[\"Possível perda\",\"Reordenar dados\",\"Pausar envio\",\"Encerrar conexão\"]}', '[0,1,2,3]', 'Essas pistas orientam o diagnóstico da sessão TCP.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=32 AND pergunta='Arraste cada pista do Eco do Timeout à interpretação.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada ameaça ao tipo correto de proteção.', NULL, '{\"itens\":[\"Vírus em terminal\",\"Porta exposta\",\"Mensagem interceptada\",\"Usuário enganado por e-mail falso\"],\"alvos\":[\"Antivírus/atualizações\",\"Firewall\",\"Criptografia\",\"Treinamento contra engenharia social\"]}', '[0,1,2,3]', 'Segurança combina ferramentas técnicas e políticas de usuário.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada ameaça ao tipo correto de proteção.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada ataque ao efeito típico.', NULL, '{\"itens\":[\"DDoS\",\"Man-in-the-middle\",\"Sequestro de DNS\",\"Engenharia social\"],\"alvos\":[\"Indisponibilidade por excesso de tráfego\",\"Interceptação da comunicação\",\"Redirecionamento para página falsa\",\"Manipulação do usuário\"]}', '[0,1,2,3]', 'Cada ataque explora uma fraqueza diferente.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada ataque ao efeito típico.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada conceito de criptografia ao significado.', NULL, '{\"itens\":[\"Texto simples\",\"Mensagem cifrada\",\"Chave pública\",\"Chave privada\"],\"alvos\":[\"Conteúdo original\",\"Conteúdo criptografado\",\"Pode ser compartilhada\",\"Deve ficar protegida\"]}', '[0,1,2,3]', 'A segurança depende de proteger chaves e transformar a mensagem.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada conceito de criptografia ao significado.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada zona da fortaleza ao papel.', NULL, '{\"itens\":[\"Internet\",\"DMZ\",\"Intranet\",\"Firewall\"],\"alvos\":[\"Rede externa\",\"Zona intermediária\",\"Rede interna protegida\",\"Filtro de tráfego\"]}', '[0,1,2,3]', 'A DMZ isola serviços expostos da rede interna.', 4
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada zona da fortaleza ao papel.') AS _chk);

INSERT INTO desafios (fase_id, ordem, tipo, assunto, pergunta, codigo, opcoes, resposta, explicacao, dificuldade)
SELECT 33,
       (SELECT COALESCE(MAX(ordem),0)+1 FROM (SELECT ordem FROM desafios WHERE fase_id=33) AS _m),
       'arrastar', 'redes', 'Arraste cada etapa ao plano de segurança.', NULL, '{\"itens\":[\"Avaliar ameaças\",\"Identificar bens críticos\",\"Escolher controles\",\"Treinar usuários\"],\"alvos\":[\"Entender riscos\",\"Saber o que proteger\",\"Aplicar firewall/cripto/antivírus\",\"Reduzir engenharia social\"]}', '[0,1,2,3]', 'Um plano de segurança começa pelo risco e combina controles técnicos e humanos.', 3
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM desafios WHERE fase_id=33 AND pergunta='Arraste cada etapa ao plano de segurança.') AS _chk);
