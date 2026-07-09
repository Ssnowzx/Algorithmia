<?php

declare(strict_types=1);

/**
 * Balanceamento do jogo — porte de `config/config.php` do legado.
 *
 * Estes números são o contrato que os vetores-ouro protegem. Mudar qualquer um
 * deles quebra testes de propósito: a falha é o pedido de revisão, não um
 * incômodo a contornar.
 *
 * As classes trazem aqui só o que o motor consome (atributos e identidade
 * visual). Descrição e lore continuam no legado até a Fase 4, quando as views
 * chegarem — duplicá-las agora criaria dois cânones para manter.
 */
return [

    'classes' => [
        'mago' => ['nome' => 'Mago do Backend', 'especie' => 'Humano', 'hp' => 80, 'mp' => 60, 'ataque' => 12, 'defesa' => 4, 'svg' => 'heroi-mago', 'cor' => '#7c5cff'],
        'guerreiro' => ['nome' => 'Guerreiro do Frontend', 'especie' => 'Humano', 'hp' => 130, 'mp' => 25, 'ataque' => 10, 'defesa' => 9, 'svg' => 'heroi-guerreiro', 'cor' => '#ff7a59'],
        'ranger' => ['nome' => 'Ranger Fullstack', 'especie' => 'Humano', 'hp' => 100, 'mp' => 40, 'ataque' => 11, 'defesa' => 6, 'svg' => 'heroi-ranger', 'cor' => '#2ecc71'],
        'xeno' => ['nome' => 'Xeno do DevOps', 'especie' => 'Xenoíde Insectoide', 'hp' => 90, 'mp' => 50, 'ataque' => 10, 'defesa' => 5, 'svg' => 'heroi-xeno', 'cor' => '#00e5a0'],
        'elfo' => ['nome' => 'Elfo da UX', 'especie' => 'Elfo', 'hp' => 75, 'mp' => 55, 'ataque' => 9, 'defesa' => 4, 'svg' => 'heroi-elfo', 'cor' => '#a8d4ff'],
        'draconato' => ['nome' => 'Draconato do Kernel', 'especie' => 'Draconato', 'hp' => 115, 'mp' => 35, 'ataque' => 12, 'defesa' => 7, 'svg' => 'heroi-draconato', 'cor' => '#ff6b4a'],
    ],

    'combate' => [
        'dano_base_por_nivel' => 3,
        'combo_bonus' => 0.25,   // +25% de dano por acerto consecutivo
        'combo_max' => 4,
        'custo_mp_especial' => 15,
        'multiplicador_especial' => 2.0,
    ],

    /**
     * Duelo Final: a batalha nunca termina por acabarem os desafios — só quando
     * um HP zera. Cruzado o limite de ritmo, a fúria cresce a cada rodada até
     * alguém tombar, sem tirar a decisão das mãos de quem sabe a matéria.
     */
    'morte_subita' => [
        'rage_step' => 0.5,
        'rage_max' => 4.0,
    ],

    /** Limiares da "leitura do inimigo": sugerem a tática sem empurrar a loja. */
    'inimigo_hp_alto' => 150,
    'inimigo_ataque_alto' => 15,

    /**
     * Maestria por matéria. Cada faixa exige VOLUME de acertos e um PISO de
     * precisão sustentada — não a porcentagem bruta — para que "domínio" signifique
     * competência consistente, e não sorte com poucas respostas.
     *
     * [rótulo, ícone, classe de cor, mín. de acertos, piso de precisão 0..1]
     */
    'maestria_faixas' => [
        ['rotulo' => 'Não iniciado', 'icone' => '○', 'cor' => 'inerte', 'min_acertos' => 0, 'piso' => 0.00],
        ['rotulo' => 'Iniciante', 'icone' => '🌱', 'cor' => 'iniciante', 'min_acertos' => 0, 'piso' => 0.00],
        ['rotulo' => 'Aprendiz', 'icone' => '📘', 'cor' => 'aprendiz', 'min_acertos' => 3, 'piso' => 0.00],
        ['rotulo' => 'Praticante', 'icone' => '🔷', 'cor' => 'praticante', 'min_acertos' => 6, 'piso' => 0.60],
        ['rotulo' => 'Especialista', 'icone' => '🎖️', 'cor' => 'especialista', 'min_acertos' => 10, 'piso' => 0.75],
        ['rotulo' => 'Mestre', 'icone' => '👑', 'cor' => 'mestre', 'min_acertos' => 15, 'piso' => 0.85],
    ],
    /** A partir deste tier a matéria conta como "dominada". */
    'maestria_tier_dominada' => 4,

    /**
     * Missões da semana: objetivos de curto prazo, derivados da atividade da semana
     * ISO corrente. Sem cron, sem recompensa, sem persistência.
     *
     * metrica ∈ respostas | acertos | acertos_sem_ia | respostas_sem_ia | materias |
     *           fases | precisao (usa 'alvo' como % e 'min' como volume mínimo)
     */
    'missoes_semanais' => [
        ['codigo' => 'maratona', 'titulo' => 'Maratona de Código', 'icone' => '🏃', 'metrica' => 'respostas', 'alvo' => 20, 'desc' => 'Responda 20 desafios esta semana. O sofá que espere.'],
        ['codigo' => 'tiro_certo', 'titulo' => 'Tiro Certeiro', 'icone' => '🎯', 'metrica' => 'acertos', 'alvo' => 15, 'desc' => 'Acerte 15 desafios. Chutar não conta como talento.'],
        ['codigo' => 'mente_afiada', 'titulo' => 'Mente Afiada', 'icone' => '🧠', 'metrica' => 'precisao', 'alvo' => 80, 'min' => 10, 'desc' => 'Mantenha 80% de acerto em pelo menos 10 respostas. Sem desculpas.'],
        ['codigo' => 'avanco', 'titulo' => 'Avanço no Reino', 'icone' => '🗺️', 'metrica' => 'fases', 'alvo' => 3, 'desc' => 'Conclua 3 fases. O reino não se salva sozinho.'],
        ['codigo' => 'sem_muletas', 'titulo' => 'Sem Muletas', 'icone' => '💪', 'metrica' => 'acertos_sem_ia', 'alvo' => 10, 'desc' => 'Acerte 10 desafios sem implorar para a IA. Orgulho tem preço.'],
        ['codigo' => 'polimata', 'titulo' => 'Polímata', 'icone' => '📚', 'metrica' => 'materias', 'alvo' => 4, 'desc' => 'Pratique 4 matérias diferentes. Variedade é tempero.'],
        ['codigo' => 'aquecimento', 'titulo' => 'Aquecimento', 'icone' => '🔥', 'metrica' => 'respostas', 'alvo' => 8, 'desc' => 'Responda 8 desafios. O mínimo do mínimo, vai.'],
        ['codigo' => 'disciplina', 'titulo' => 'Disciplina de Ferro', 'icone' => '⚔️', 'metrica' => 'respostas_sem_ia', 'alvo' => 12, 'desc' => 'Responda 12 desafios sem tocar na IA. Prove que tem coluna.'],
    ],
    'missoes_por_semana' => 3,

    /**
     * Domínio das regiões (maestria horizontal). "Dominada" exige perfeição total:
     * todas as fases com 3 estrelas, o que significa sem erro e sem IA.
     */
    'regiao_faixas' => [
        'a_explorar' => ['rotulo' => 'A explorar', 'cor' => 'inerte'],
        'em_jornada' => ['rotulo' => 'Em jornada', 'cor' => 'jornada'],
        'conquistada' => ['rotulo' => 'Conquistada', 'cor' => 'conquistada'],
        'dominada' => ['rotulo' => 'Dominada', 'cor' => 'dominada'],
    ],
    'regiao_titulo_lenda' => 'Mestre dos Cinco',

    /** O painel de "Primeiros passos" some quando o jogador passa deste nível. */
    'onboarding_nivel_max' => 3,

    'progressao' => [
        'hp_por_nivel' => 15,
        'mp_por_nivel' => 8,
        'bonus_ouro_ranger' => 1.2,
        'reputacao_por_vitoria_limpa' => 5,
    ],

    'reputacao' => [
        'uso_ia' => -10,
        'min' => -100,
        'max' => 100,
    ],

    /**
     * Cada fase guarda um POOL maior do que o sorteado por batalha. O sorteio
     * prioriza perguntas inéditas; o resto abastece a reserva do Duelo Final.
     */
    'desafios_por_batalha' => [
        'licao' => 4,
        'secundaria' => 3,
        'chefe' => 5,
        'chefe_final' => 6,
        'historia' => 0,
    ],
    'desafios_por_batalha_padrao' => 4,

    /** Recompensa em ouro única, creditada na primeira vez. */
    'objetivos_ouro' => [
        'primeira_arma' => 30,
        'primeira_pocao' => 20,
        'arsenal_completo' => 60,
    ],

    /** Região governada por cada mestre, chaveada pelo svg_slug (estável). */
    'regioes_mestre' => [
        'mestre-willen' => ['fundo' => 'fundo-porto', 'conquista' => 'mestre_willen'],
        'mestre-clayton' => ['fundo' => 'fundo-cidadela', 'conquista' => 'mestre_clayton'],
        'mestre-marcelo' => ['fundo' => 'fundo-floresta', 'conquista' => 'mestre_marcelo'],
        'mestre-cesar' => ['fundo' => 'fundo-montanha', 'conquista' => 'mestre_cesar'],
        'mestre-cassandro' => ['fundo' => 'fundo-torre', 'conquista' => 'mestre_cassandro'],
    ],

    /** Slug do Fragmento da IA Ancestral — resolvido por svg_slug, não pelo nome. */
    'item_fragmento_ia' => 'item-fragmento-ia',

    'loja' => [
        // Revenda por metade do preço. Sem o desconto, comprar e vender de volta
        // seria uma forma gratuita de estocar ouro.
        'fator_venda' => 0.5,
    ],

    /** Um item de cada tipo por vez; poções e especiais não ocupam slot. */
    'tipos_equipaveis' => ['arma', 'escudo', 'acessorio'],

    /** Ordem de exibição do inventário (o `FIELD()` do MySQL não existe no PostgreSQL). */
    'ordem_tipos_item' => ['arma', 'escudo', 'acessorio', 'pocao', 'especial'],

    'limite_ranking' => 50,

    /** Escolhas válidas diante da IA Ancestral, e a conquista de cada desfecho. */
    'escolhas_finais' => ['destruir', 'fundir', 'reescrever'],
    'conquistas_de_final' => [
        'mestre' => 'final_mestre',
        'singularidade' => 'final_singularidade',
        'equilibrio' => 'final_equilibrio',
    ],

    /** Limiares de reputação que definem o desfecho quando não há escolha explícita. */
    'reputacao_final' => ['mestre' => 40, 'singularidade' => -40],

    /** Reputação a partir da qual os diálogos mudam para a variante da IA. */
    'reputacao_variante_ia' => -20,

    'desafio' => [
        'dificuldade_min' => 1,
        'dificuldade_max' => 5,
        'tipos' => ['multipla', 'vf', 'completar', 'erro', 'ordenar', 'arrastar'],
        'assuntos' => ['php', 'mvc', 'sql', 'poo', 'estruturas', 'redes', 'logica', 'calculo'],
    ],

    'assuntos_rotulos' => [
        'php' => 'PHP',
        'mvc' => 'Arquitetura MVC',
        'sql' => 'Banco de Dados / SQL',
        'poo' => 'Orientação a Objetos',
        'estruturas' => 'Estruturas de Dados',
        'redes' => 'Redes de Computadores',
        'logica' => 'Lógica e Algoritmos',
        'calculo' => 'Cálculo',
    ],

    /**
     * IDs das fases secundárias que concedem "arquivista_do_vazio".
     * O legado os traz fixos em ConquistaService.php:86. A importação da Fase 3
     * preserva os IDs originais justamente para que isto continue valendo.
     */
    'fases_secundarias' => [8, 14, 20, 32],
];
