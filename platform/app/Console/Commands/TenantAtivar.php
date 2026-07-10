<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\InstituicaoInjogavel;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Models\Tenant;
use Illuminate\Console\Command;

/**
 * Liga uma instituição — e só se ela estiver jogável.
 *
 * Não há `--forcar`. O escape, se um dia for mesmo necessário, é um
 * `UPDATE tenants SET ativo = true` escrito à mão por quem sabe o que está fazendo, e
 * não uma opção que a próxima pessoa copia do histórico do shell.
 */
final class TenantAtivar extends Command
{
    protected $signature = 'algorithmia:tenant:ativar {slug : A instituição a ligar}';

    protected $description = 'Liga uma instituição, depois de o smoke provar que ela é jogável';

    public function handle(ProvisionamentoDeInstituicoes $provisionamento): int
    {
        $tenant = Tenant::query()->where('slug', $this->argument('slug'))->first();

        if ($tenant === null) {
            $this->error(sprintf('Não existe instituição com slug "%s".', $this->argument('slug')));

            return self::FAILURE;
        }

        if ($tenant->ativo) {
            $this->info(sprintf('A instituição "%s" já está ligada.', $tenant->slug));

            return self::SUCCESS;
        }

        try {
            $provisionamento->ativar($tenant);
        } catch (InstituicaoInjogavel $erro) {
            // A saída do smoke diz QUAL verificação caiu. Sem ela, o operador rodaria o
            // smoke à mão para descobrir o que este comando já sabe.
            $this->newLine();
            $this->line($erro->saidaDoSmoke);
            $this->error($erro->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Instituição "%s" ligada. Ela já atende no seu domínio.', $tenant->slug));

        return self::SUCCESS;
    }
}
