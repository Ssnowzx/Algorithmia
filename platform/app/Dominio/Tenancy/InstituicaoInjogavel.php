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
 */
final class InstituicaoInjogavel extends RuntimeException
{
    public function __construct(
        public readonly Tenant $tenant,
        public readonly string $saidaDoSmoke,
    ) {
        parent::__construct(sprintf(
            'A instituição "%s" não passou no smoke e NÃO foi ativada. '
            .'Uma escola ativa e injogável reprova o próximo deploy inteiro. '
            .'Semeie o conteúdo dela: php artisan algorithmia:importar --tenant=%s',
            $tenant->slug,
            $tenant->slug,
        ));
    }
}
