<?php
/**
 * Banco de questões — Clayton, o Moldador (Cidadela dos Objetos).
 * Programação Orientada a Objetos. Conceitos OO com sintaxe PHP, como no jogo.
 * Pool ampliado das fases 11, 12, 13, 14 (interfaces) e 15 (chefe God-Class).
 */

return [
    // ---- Fase 11: Classes e Objetos ----
    ['fase' => 11, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Qual método especial é chamado automaticamente ao criar um objeto?', 'codigo' => null,
     'opcoes' => ['O destrutor', 'O construtor', 'O getter', 'O main'], 'resposta' => 1,
     'explicacao' => 'O construtor (__construct em PHP) inicializa o objeto no momento da criação.', 'dif' => 2],

    ['fase' => 11, 'tipo' => 'completar', 'assunto' => 'poo',
     'pergunta' => 'Complete o nome do método construtor em PHP:', 'codigo' => 'public function ___________() { }',
     'opcoes' => null, 'resposta' => ['__construct'],
     'explicacao' => 'Em PHP o construtor se chama __construct (dois underscores).', 'dif' => 3],

    ['fase' => 11, 'tipo' => 'vf', 'assunto' => 'poo',
     'pergunta' => 'Vários objetos diferentes podem ser criados a partir de uma mesma classe.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'A classe é o molde; cada new gera uma instância independente com seu próprio estado.', 'dif' => 2],

    ['fase' => 11, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Numa classe, os atributos guardam o ___ e os métodos definem o ___.', 'codigo' => null,
     'opcoes' => ['comportamento / estado', 'estado / comportamento', 'nome / tipo', 'banco / tela'],
     'resposta' => 1,
     'explicacao' => 'Atributos = estado (dados); métodos = comportamento (ações).', 'dif' => 2],

    ['fase' => 11, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Qual a saída?', 'codigo' => "class Gato {\n  public \$nome = 'Bigode';\n}\n\$g = new Gato();\necho \$g->nome;",
     'opcoes' => ['nome', 'Bigode', 'Gato', 'Erro'], 'resposta' => 1,
     'explicacao' => 'O objeto $g acessa seu atributo nome com ->, imprimindo "Bigode".', 'dif' => 3],

    ['fase' => 11, 'tipo' => 'ordenar', 'assunto' => 'poo',
     'pergunta' => 'Ordene os passos para usar um objeto:', 'codigo' => null,
     'opcoes' => ['Chamar um método do objeto', 'Declarar a classe', 'Instanciar o objeto com new'],
     'resposta' => [1, 2, 0],
     'explicacao' => 'Primeiro existe o molde (classe), depois cria-se a instância e então usam-se seus métodos.', 'dif' => 3],

    // ---- Fase 12: Encapsulamento ----
    ['fase' => 12, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Qual modificador permite acesso ao atributo de QUALQUER lugar?', 'codigo' => null,
     'opcoes' => ['private', 'protected', 'public', 'final'], 'resposta' => 2,
     'explicacao' => 'public deixa o membro acessível de dentro e de fora da classe.', 'dif' => 2],

    ['fase' => 12, 'tipo' => 'vf', 'assunto' => 'poo',
     'pergunta' => 'Getters e setters dão acesso controlado a atributos privados.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Eles são a porta oficial: leem/alteram o estado interno com validação, sem expor o atributo.', 'dif' => 2],

    ['fase' => 12, 'tipo' => 'completar', 'assunto' => 'poo',
     'pergunta' => 'Torne o atributo $saldo inacessível de fora da classe:', 'codigo' => 'class Conta { _______ $saldo; }',
     'opcoes' => null, 'resposta' => ['private'],
     'explicacao' => 'private restringe o acesso ao interior da própria classe.', 'dif' => 3],

    ['fase' => 12, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Por que manter atributos private com getters/setters?', 'codigo' => null,
     'opcoes' => ['Para digitar mais', 'Para controlar e validar o acesso ao estado interno', 'Para deixar tudo público', 'Para remover métodos'],
     'resposta' => 1,
     'explicacao' => 'Encapsular permite validar mudanças e proteger invariantes do objeto.', 'dif' => 3],

    ['fase' => 12, 'tipo' => 'erro', 'assunto' => 'poo',
     'pergunta' => 'O que fere o encapsulamento?', 'codigo' => null,
     'opcoes' => ['Usar getters e setters', 'Deixar todos os atributos public e alterá-los direto de fora', 'Marcar atributos como private', 'Validar dados no setter'],
     'resposta' => 1,
     'explicacao' => 'Expor tudo como public deixa o estado interno à mercê de qualquer um — o oposto de encapsular.', 'dif' => 3],

    ['fase' => 12, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'O modificador protected permite acesso:', 'codigo' => null,
     'opcoes' => ['Só de fora da classe', 'Na própria classe e nas suas subclasses', 'De qualquer lugar', 'Em nenhum lugar'],
     'resposta' => 1,
     'explicacao' => 'protected libera o acesso à classe e às que a estendem, mas não ao mundo externo.', 'dif' => 3],

    // ---- Fase 13: Herança vs Composição ----
    ['fase' => 13, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Herança expressa qual relação entre as classes?', 'codigo' => null,
     'opcoes' => ['tem-um (has-a)', 'é-um (is-a)', 'usa-um', 'faz-um'], 'resposta' => 1,
     'explicacao' => 'Cachorro é-um Animal: herança modela especialização (is-a).', 'dif' => 2],

    ['fase' => 13, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Composição expressa qual relação?', 'codigo' => null,
     'opcoes' => ['é-um (is-a)', 'tem-um (has-a)', 'igual-a', 'maior-que'], 'resposta' => 1,
     'explicacao' => 'Carro tem-um Motor: composição monta um objeto a partir de outros (has-a).', 'dif' => 3],

    ['fase' => 13, 'tipo' => 'vf', 'assunto' => 'poo',
     'pergunta' => 'Em PHP, uma classe pode herdar de apenas uma classe pai (herança simples).', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'PHP não tem herança múltipla de classes; para múltiplos contratos, usam-se interfaces ou traits.', 'dif' => 3],

    ['fase' => 13, 'tipo' => 'completar', 'assunto' => 'poo',
     'pergunta' => 'Complete para chamar o construtor da classe pai:', 'codigo' => 'parent::___________();',
     'opcoes' => null, 'resposta' => ['__construct'],
     'explicacao' => 'parent::__construct() reaproveita a inicialização definida na superclasse.', 'dif' => 4],

    ['fase' => 13, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Sobrescrever (override) um método significa:', 'codigo' => null,
     'opcoes' => ['Apagar o método do pai', 'Redefinir, na subclasse, um método herdado', 'Criar um atributo novo', 'Tornar o método privado'],
     'resposta' => 1,
     'explicacao' => 'A subclasse fornece sua própria versão de um método já existente na superclasse.', 'dif' => 3],

    ['fase' => 13, 'tipo' => 'ordenar', 'assunto' => 'poo',
     'pergunta' => 'Ordene da classe mais genérica para a mais específica:', 'codigo' => null,
     'opcoes' => ['Cachorro', 'SerVivo', 'Animal'], 'resposta' => [1, 2, 0],
     'explicacao' => 'SerVivo (geral) → Animal → Cachorro (específico).', 'dif' => 3],

    // ---- Fase 14: Interfaces Secretas (secundária) ----
    ['fase' => 14, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'O que uma interface contém?', 'codigo' => null,
     'opcoes' => ['A implementação completa dos métodos', 'Assinaturas de métodos sem implementação (um contrato)', 'Apenas atributos privados', 'Um laço de repetição'],
     'resposta' => 1,
     'explicacao' => 'A interface lista o QUE deve existir; cada classe decide o COMO ao implementá-la.', 'dif' => 3],

    ['fase' => 14, 'tipo' => 'vf', 'assunto' => 'poo',
     'pergunta' => 'Uma mesma classe pode implementar várias interfaces ao mesmo tempo.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Diferente da herança de classe, assinar vários contratos (interfaces) é permitido.', 'dif' => 3],

    ['fase' => 14, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Polimorfismo permite:', 'codigo' => null,
     'opcoes' => ['Tratar objetos de tipos diferentes pela mesma interface', 'Criar atributos privados', 'Eliminar classes', 'Acelerar o banco de dados'],
     'resposta' => 0,
     'explicacao' => 'Vários tipos respondem à mesma chamada à sua maneira — código que fala com a interface, não com a classe concreta.', 'dif' => 4],

    ['fase' => 14, 'tipo' => 'completar', 'assunto' => 'poo',
     'pergunta' => 'Faça a classe Pato assinar o contrato Nadador:', 'codigo' => 'class Pato __________ Nadador { }',
     'opcoes' => null, 'resposta' => ['implements'],
     'explicacao' => 'implements obriga a classe a fornecer os métodos declarados na interface.', 'dif' => 3],

    // ---- Fase 15: A Gárgula God-Class (chefe) ----
    ['fase' => 15, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'No SOLID, o que o "S" (SRP) defende?', 'codigo' => null,
     'opcoes' => ['Single Responsibility: uma classe, uma razão para mudar', 'Simple Rule', 'Static Reference', 'Sorted Records'],
     'resposta' => 0,
     'explicacao' => 'Princípio da Responsabilidade Única: cada classe deve ter um único motivo para mudar.', 'dif' => 4],

    ['fase' => 15, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'Uma classe com ALTA coesão:', 'codigo' => null,
     'opcoes' => ['Faz muitas coisas sem relação', 'Concentra-se em uma responsabilidade bem definida', 'Não tem métodos', 'Depende de todas as outras classes'],
     'resposta' => 1,
     'explicacao' => 'Coesão alta = a classe trata de um único assunto, o oposto da God Class.', 'dif' => 3],

    ['fase' => 15, 'tipo' => 'vf', 'assunto' => 'poo',
     'pergunta' => 'Baixo acoplamento entre classes facilita mudanças e testes.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'Quanto menos uma classe depende das outras, mais fácil trocá-la ou testá-la isoladamente.', 'dif' => 3],

    ['fase' => 15, 'tipo' => 'erro', 'assunto' => 'poo',
     'pergunta' => 'Qual problema esta classe evidencia?', 'codigo' => "class Pedido {\n  function calcularFrete() {}\n  function gerarPdf() {}\n  function enviarEmail() {}\n  function salvarNoBanco() {}\n}",
     'opcoes' => ['Está coesa e correta', 'Responsabilidades demais (viola o SRP)', 'Falta herança', 'Falta um construtor'],
     'resposta' => 1,
     'explicacao' => 'Frete, PDF, e-mail e persistência são quatro responsabilidades — separe em classes próprias.', 'dif' => 3],

    ['fase' => 15, 'tipo' => 'multipla', 'assunto' => 'poo',
     'pergunta' => 'A refatoração típica de uma God Class é:', 'codigo' => null,
     'opcoes' => ['Adicionar ainda mais métodos', 'Dividi-la em classes menores e coesas', 'Tornar tudo public', 'Apagar todos os testes'],
     'resposta' => 1,
     'explicacao' => 'Quebrar a classe-deus em partes coesas melhora manutenção, testes e leitura.', 'dif' => 3],

    ['fase' => 15, 'tipo' => 'completar', 'assunto' => 'poo',
     'pergunta' => 'Complete a sigla do princípio que evita repetir código: Don\'t Repeat Yourself = ___', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['DRY', 'dry'],
     'explicacao' => 'DRY: cada conhecimento deve ter uma única representação no sistema.', 'dif' => 3],
];
