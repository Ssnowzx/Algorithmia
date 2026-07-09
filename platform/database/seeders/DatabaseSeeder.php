<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Ainda vazio, de propósito.
 *
 * O conteúdo do jogo — 5 mestres, 35 fases, o banco de desafios, itens, diálogos
 * e conquistas — é importado do legado na Fase 3 do docs/migracao/PLANO.md,
 * preservando os IDs originais (ConquistaService referencia fases por ID fixo).
 * Semear dados falsos aqui só criaria um segundo cânone para manter.
 */
final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //
    }
}
