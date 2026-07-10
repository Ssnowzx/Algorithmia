<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use App\Models\Tenant;
use RuntimeException;

/**
 * Tentou-se ligar uma instituição que o `algorithmia:smoke` reprovou.
 *
 * Quase sempre significa a mesma coisa: provisionaram a escola e esqueceram de semeá-la.
 * Por isso a exceção carrega a saída do smoke — a lista de verificações e qual delas caiu.
 * Uma mensagem "não foi possível ativar" mandaria o operador rodar o smoke à mão para
 * descobrir o que ele já sabe.
 *
 * **A sugestão de conserto vem de fora, e não é sempre a mesma.** Mandar rodar
 * `algorithmia:importar --tenant=X` só faz sentido enquanto NENHUMA outra instituição tem
 * conteúdo: os ids do jogo são globais, e o importador recusa a segunda escola. Uma exceção
 * que instruísse o operador a rodar um comando que não pode funcionar seria pior do que uma
 * que não instruísse nada. Ver `ConteudoPorInstituicao`.
 */
final class InstituicaoInjogavel extends RuntimeException
{
    public function __construct(
        public readonly Tenant $tenant,
        public readonly string $saidaDoSmoke,
        string $sugestao,
    ) {
        parent::__construct(sprintf(
            'A instituição "%s" não passou no smoke e NÃO foi ativada. '
            .'Uma escola ativa e injogável reprova o próximo deploy inteiro. %s',
            $tenant->slug,
            $sugestao,
        ));
    }
}
