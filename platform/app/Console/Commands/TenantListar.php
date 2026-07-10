<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\PainelDeInstituicoes;
use Illuminate\Console\Command;

/**
 * As instituições, o estado de cada uma, e as métricas de ativação e uso do roteiro v1 §9.
 *
 * Inclui as desligadas: são justamente as que o operador acabou de provisionar e precisa
 * enxergar. Um painel que só mostra o que está no ar não ajuda quem está pondo algo no ar.
 */
final class TenantListar extends Command
{
    protected $signature = 'algorithmia:tenant:listar';

    protected $description = 'Lista as instituições, o seu estado, as flags e as métricas de uso';

    public function handle(PainelDeInstituicoes $painel): int
    {
        $linhas = [];

        foreach ($painel->instituicoes() as $instituicao) {
            $tenant = $instituicao['tenant'];
            $m = $instituicao['metricas'];

            $ligadas = array_keys(array_filter($instituicao['flags'], fn (array $f): bool => $f['ativa']));

            $linhas[] = [
                $tenant->slug,
                $tenant->ativo ? '<fg=green>no ar</>' : '<fg=yellow>desligada</>',
                $instituicao['host'] ?? '<fg=red>SEM DOMÍNIO</>',
                $m['contas'],
                // Ativação: de cada cem contas, quantas criaram herói. Contar contas
                // sozinho esconderia uma escola que não passa da tela de criação.
                $m['ativacao'] === null ? '—' : $m['herois'].' ('.$m['ativacao'].'%)',
                $m['fases_concluidas'],
                $m['precisao'] === null ? '—' : $m['precisao'].'%',
                $m['ultima_atividade'] ?? '—',
                $ligadas === [] ? '—' : implode(', ', $ligadas),
            ];
        }

        if ($linhas === []) {
            $this->error('Nenhuma instituição. Rode as migrations.');

            return self::FAILURE;
        }

        $this->table(
            ['slug', 'estado', 'host', 'contas', 'heróis (ativação)', 'fases', 'precisão', 'última atividade', 'flags'],
            $linhas,
        );

        return self::SUCCESS;
    }
}
