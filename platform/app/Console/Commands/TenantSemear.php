<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Dominio\Tenancy\ConteudoPorInstituicao;
use App\Dominio\Tenancy\SemeadorDeConteudo;
use App\Models\Tenant;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Dá a uma instituição nova o conteúdo do jogo, copiado de outra.
 *
 * O `algorithmia:importar` é a ferramenta do **corte**: ele traz o legado inteiro — contas,
 * heróis, progresso — e preserva os ids, porque o progresso importado aponta para eles. Isso
 * só funciona uma vez: `fases.id` é chave primária global.
 *
 * Uma escola nova não quer os alunos da primeira. Quer o conteúdo. É o que este comando copia,
 * com ids novos.
 */
final class TenantSemear extends Command
{
    protected $signature = 'algorithmia:tenant:semear
        {para : A instituição que receberá o conteúdo}
        {--de= : De qual instituição copiar. Padrão: a única que tem conteúdo.}';

    protected $description = 'Copia o conteúdo do jogo (mestres, fases, desafios, itens) para uma instituição nova';

    public function handle(SemeadorDeConteudo $semeador, ConteudoPorInstituicao $conteudo): int
    {
        $para = Tenant::query()->where('slug', $this->argument('para'))->first();

        if ($para === null) {
            $this->error(sprintf('Não existe instituição com slug "%s".', $this->argument('para')));

            return self::FAILURE;
        }

        $de = $this->origem($conteudo, $para);

        if ($de === null) {
            return self::FAILURE;
        }

        $this->line(sprintf('Copiando o conteúdo de "%s" para "%s"…', $de->slug, $para->slug));

        try {
            $copiadas = $semeador->semear($de, $para);
        } catch (RuntimeException $erro) {
            // Erro de uso vira frase. E nada ficou pela metade: a cópia inteira roda numa
            // transação, e a reconciliação a desfaz se as contagens divergirem.
            $this->error($erro->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->table(
            ['tabela', 'linhas'],
            array_map(fn (string $t, int $n): array => [$t, $n], array_keys($copiadas), $copiadas),
        );

        $this->newLine();
        $this->info(sprintf('Conteúdo copiado. Os ids são novos — "%s" não compartilha nenhuma linha com "%s".', $para->slug, $de->slug));
        $this->newLine();
        $this->line("  Agora ligue a instituição:  php artisan algorithmia:tenant:ativar {$para->slug}");
        $this->line('  Ele roda o smoke dela antes, e recusa ligá-la se algo faltar.');

        return self::SUCCESS;
    }

    private function origem(ConteudoPorInstituicao $conteudo, Tenant $para): ?Tenant
    {
        if ($this->option('de') !== null) {
            $de = Tenant::query()->where('slug', (string) $this->option('de'))->first();

            if ($de === null) {
                $this->error(sprintf('Não existe instituição com slug "%s".', $this->option('de')));

                return null;
            }

            return $de;
        }

        // Sem `--de`, a origem é a instituição que tem conteúdo. Com duas, não há resposta
        // óbvia, e adivinhar seria escolher em nome do operador de qual escola copiar.
        $candidatas = $conteudo->todasComConteudo(exceto: $para->id);

        return match (count($candidatas)) {
            0 => $this->recusar('Nenhuma instituição tem conteúdo para copiar. Rode `algorithmia:importar` primeiro.'),
            1 => $candidatas[0],
            default => $this->recusar(sprintf(
                'Há %d instituições com conteúdo (%s). Diga de qual copiar, com `--de`.',
                count($candidatas),
                implode(', ', array_map(fn (Tenant $t): string => $t->slug, $candidatas)),
            )),
        };
    }

    private function recusar(string $mensagem): null
    {
        $this->error($mensagem);

        return null;
    }
}
