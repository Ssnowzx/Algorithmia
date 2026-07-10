<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Models\Tenant;
use Illuminate\Console\Command;

/**
 * Tira uma instituição do ar, sem tocar nas outras — o requisito de rollback do piloto.
 *
 * Desligar nunca roda o smoke: exigir que a escola esteja jogável para poder desligá-la
 * trancaria por dentro exatamente a escola quebrada que se quer tirar do ar.
 */
final class TenantDesativar extends Command
{
    protected $signature = 'algorithmia:tenant:desativar
        {slug : A instituição a desligar}
        {--sim : Não perguntar. Para scripts.}';

    protected $description = 'Desliga uma instituição: o domínio dela passa a devolver 404';

    public function handle(ProvisionamentoDeInstituicoes $provisionamento): int
    {
        $tenant = Tenant::query()->where('slug', $this->argument('slug'))->first();

        if ($tenant === null) {
            $this->error(sprintf('Não existe instituição com slug "%s".', $this->argument('slug')));

            return self::FAILURE;
        }

        if (! $tenant->ativo) {
            $this->info(sprintf('A instituição "%s" já está desligada.', $tenant->slug));

            return self::SUCCESS;
        }

        // Desligar é reversível, mas não é inofensivo: os alunos dela passam a receber 404
        // no meio de uma partida. Quem faz isso tem de estar dizendo o nome em voz alta.
        if (! $this->option('sim') && ! $this->confirm(
            sprintf('Desligar "%s"? Os alunos dela receberão 404 imediatamente.', $tenant->slug),
            default: false
        )) {
            $this->line('Nada foi feito.');

            return self::SUCCESS;
        }

        $provisionamento->desativar($tenant);

        $this->info(sprintf('Instituição "%s" desligada. As demais seguem no ar.', $tenant->slug));

        return self::SUCCESS;
    }
}
