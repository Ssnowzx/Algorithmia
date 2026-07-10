<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Uma instituição nasce **desligada**.
 *
 * Provisionar uma escola e semeá-la são dois atos, e entre eles ela existe sem conteúdo:
 * nenhuma fase, nenhum desafio, nenhum mestre. Nesse intervalo ela é injogável.
 *
 * Com `ativo` nascendo `true`, duas coisas ruins aconteciam ao mesmo tempo:
 *
 * 1. O `ResolverTenant` atenderia o domínio dela, e o aluno veria um mapa vazio.
 * 2. O `algorithmia:smoke` — que varre todas as instituições **ativas** e é o portão do
 *    `bin/deploy.sh` — reprovaria o deploy. **Todo deploy voltaria sozinho**, até alguém
 *    perceber que o culpado era uma escola pela metade.
 *
 * Foi assim que o ensaio do corte (C.6) reprovou um build correto. O rollback automático
 * funcionou, o site seguiu no ar, e o defeito era este.
 *
 * A ordem passa a ser: provisionar → importar/semear → `UPDATE tenants SET ativo = true`.
 *
 * Aditiva: só muda o DEFAULT. As linhas existentes não são tocadas — inclusive o tenant
 * padrão, que a migration `add_tenant_id_ao_jogo` criou com `ativo = true` explícito.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tenants ALTER COLUMN ativo SET DEFAULT false');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tenants ALTER COLUMN ativo SET DEFAULT true');
    }
};
