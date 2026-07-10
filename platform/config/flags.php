<?php

declare(strict_types=1);

/**
 * O catálogo de funcionalidades liberáveis por instituição. Etapa E.1.
 *
 * **Esta lista é a fonte da verdade.** O banco (`tenants.flags`) guarda apenas as
 * exceções: o que uma escola decidiu diferente do padrão daqui. Uma chave que não está
 * neste arquivo não existe — `Flags::ativa()` levanta exceção em vez de devolver `false`.
 * Um erro de digitação numa flag tem de quebrar o teste, e não desligar a funcionalidade
 * em silêncio, para sempre, em produção.
 *
 * **Regra de admissão: uma flag só entra aqui se o estado "desligada" for seguro.**
 * `registro_aberto` foi considerada e recusada — sem um fluxo de convite implementado
 * (a tabela `convites` existe, as rotas não), fechar o registro deixaria a escola sem
 * nenhuma forma de matricular o primeiro aluno. Uma flag que trava a instituição não é
 * liberação progressiva; é um tijolo.
 *
 * Toda flag é aplicada em DOIS lugares, e os dois são obrigatórios:
 *
 *  1. na rota, com o middleware `flag:<chave>` — que devolve 404, não 403;
 *  2. na navegação, com `@flag('<chave>')`.
 *
 * Esconder o link e deixar a rota aberta é a mesma classe de erro de ligar o RLS com um
 * papel que o ignora: a sensação de uma barreira que não existe. O `FlagsTest` bate na
 * URL direto, sem passar pelo menu.
 */
return [

    /**
     * Turmas, matrículas e relatórios pedagógicos — a Etapa D inteira.
     *
     * Nasce **desligada**, e é o caso de uso que motivou a Etapa E: a funcionalidade foi
     * construída, testada e entregue, mas nenhuma escola em produção jamais a usou. Ela
     * se abre na instituição piloto, e só nela, até que o piloto diga que ela presta.
     */
    'turmas' => [
        'padrao' => false,
        'descricao' => 'Turmas, matrículas e relatórios pedagógicos.',
    ],

    /**
     * O placar entre os alunos da instituição.
     *
     * Nasce ligada — é assim que o jogo está em produção hoje, e a Etapa E não muda
     * comportamento nenhum sem que alguém peça. Desligá-la é um pedido pedagógico real:
     * há escola que não quer os alunos comparando notas em público.
     */
    'ranking' => [
        'padrao' => true,
        'descricao' => 'Placar de heróis entre os alunos da instituição.',
    ],

];
