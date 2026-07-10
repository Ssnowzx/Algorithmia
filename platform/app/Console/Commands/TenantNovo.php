<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\ConteudoPorInstituicao;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Dominio\Tenancy\ProvisionamentoInvalido;
use Illuminate\Console\Command;

/**
 * Provisiona uma instituição. Ela nasce **desligada** — ver `RUNBOOK §10.6b`.
 */
final class TenantNovo extends Command
{
    protected $signature = 'algorithmia:tenant:novo
        {nome : O nome da instituição, como o aluno o verá}
        {--slug= : Identificador curto e estável. Padrão: derivado do nome.}
        {--host= : O domínio pelo qual ela é alcançada. Só o host: sem esquema, sem porta.}';

    protected $description = 'Cria uma instituição, desligada, com o seu domínio primário';

    public function handle(ProvisionamentoDeInstituicoes $provisionamento, ConteudoPorInstituicao $conteudo): int
    {
        $host = (string) $this->option('host');

        if ($host === '') {
            $this->error('Falta `--host`. Sem domínio, o `ResolverTenant` nunca alcança a instituição.');

            return self::FAILURE;
        }

        try {
            $tenant = $provisionamento->provisionar(
                (string) $this->argument('nome'),
                $this->option('slug') === null ? null : (string) $this->option('slug'),
                $host,
            );
        } catch (ProvisionamentoInvalido $erro) {
            // Erro de uso vira frase, não stack trace: quem lê isto está no meio de uma
            // operação, e o que ele precisa é da próxima ação.
            $this->error($erro->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Instituição "%s" criada (id %d), DESLIGADA.', $tenant->slug, $tenant->id));
        $this->newLine();

        // Os ids do conteúdo são globais. Se outra instituição já os tem, esta nunca poderá
        // ser semeada — e imprimir "rode o importador" seria mandar o operador contra uma
        // violação de chave primária. Ver `ConteudoPorInstituicao`.
        $dona = $conteudo->instituicaoComConteudo(exceto: $tenant->id);

        if ($dona !== null) {
            $this->warn('  Ela NÃO poderá receber conteúdo, e portanto não poderá ser ativada.');
            $this->newLine();
            $this->line($conteudo->porQueSoUma($dona));

            return self::SUCCESS;
        }

        // A ordem é a razão de este comando existir. Escrevê-la aqui, na saída, é mais
        // barato do que confiar em quem lembrou de abrir o runbook.
        $this->line('  Ela ainda não atende ninguém. Os próximos passos, nesta ordem:');
        $this->newLine();
        $this->line("    1. php artisan algorithmia:importar --tenant={$tenant->slug}");
        $this->line("    2. php artisan algorithmia:tenant:ativar {$tenant->slug}");
        $this->newLine();
        $this->line('  O passo 2 roda o smoke da instituição e recusa ligá-la se ela estiver injogável.');
        $this->line('  Uma escola ativa e vazia reprova o próximo deploy inteiro.');

        return self::SUCCESS;
    }
}
