<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\Flags;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Models\Tenant;
use Illuminate\Console\Command;
use InvalidArgumentException;

/**
 * Lê e move as funcionalidades liberadas para uma instituição. Etapa E.1/E.2.
 */
final class TenantFlag extends Command
{
    protected $signature = 'algorithmia:tenant:flag
        {slug : A instituição}
        {chave? : A funcionalidade. Sem ela, lista todas.}
        {--ligar}
        {--desligar}
        {--padrao : Remove a opinião da escola e a devolve ao padrão do código}';

    protected $description = 'Lista ou muda as funcionalidades liberadas para uma instituição';

    public function handle(ProvisionamentoDeInstituicoes $provisionamento, Flags $flags): int
    {
        $tenant = Tenant::query()->where('slug', $this->argument('slug'))->first();

        if ($tenant === null) {
            $this->error(sprintf('Não existe instituição com slug "%s".', $this->argument('slug')));

            return self::FAILURE;
        }

        $chave = $this->argument('chave');

        if ($chave === null) {
            return $this->listar($flags, $tenant);
        }

        $escolhidas = array_filter([
            'ligar' => (bool) $this->option('ligar'),
            'desligar' => (bool) $this->option('desligar'),
            'padrao' => (bool) $this->option('padrao'),
        ]);

        if (count($escolhidas) !== 1) {
            $this->error('Escolha exatamente uma: --ligar, --desligar ou --padrao.');

            return self::FAILURE;
        }

        // `--padrao` grava `null`, que REMOVE a chave. Gravar o valor do padrão seria
        // diferente: no dia em que o padrão mudasse, a escola não o acompanharia.
        $valor = match (array_key_first($escolhidas)) {
            'ligar' => true,
            'desligar' => false,
            default => null,
        };

        try {
            $provisionamento->definirFlag($tenant, (string) $chave, $valor);
        } catch (InvalidArgumentException $erro) {
            $this->error($erro->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            '"%s" em "%s": %s.',
            $chave,
            $tenant->slug,
            $valor === null ? 'de volta ao padrão do código' : ($valor ? 'ligada' : 'desligada'),
        ));

        return self::SUCCESS;
    }

    private function listar(Flags $flags, Tenant $tenant): int
    {
        $linhas = [];

        foreach ($flags->todas($tenant) as $chave => $estado) {
            $linhas[] = [
                $chave,
                $estado['ativa'] ? '<fg=green>ligada</>' : '<fg=gray>desligada</>',
                // A distinção que importa numa auditoria: a escola pediu isto, ou apenas
                // herdou o padrão de quem escreveu o código?
                $estado['sobrescrita'] ? 'a escola escolheu' : 'padrão do código',
                $estado['descricao'],
            ];
        }

        $this->table(['flag', 'estado', 'origem', 'o que é'], $linhas);

        return self::SUCCESS;
    }
}
