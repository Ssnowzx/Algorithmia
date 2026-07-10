<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Operador;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Cria um operador da plataforma — quem entra no console.
 *
 * **A senha nunca vem de argumento.** Um `--senha=` fica no histórico do shell, no `ps` de
 * quem estiver na mesma máquina, e no log de quem gravar a sessão. Ela é digitada, oculta,
 * e confirmada.
 *
 * Não há seed com senha padrão. Uma conta de administrador com senha conhecida é a primeira
 * coisa que um scanner encontra, e a última que alguém lembra de trocar.
 */
final class OperadorNovo extends Command
{
    protected $signature = 'algorithmia:operador:novo
        {nome : O nome de quem opera}
        {email : O e-mail, que é o login}';

    protected $description = 'Cria um operador da plataforma (quem entra no console)';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error(sprintf('"%s" não é um e-mail.', $email));

            return self::FAILURE;
        }

        // A unicidade do banco é sobre `lower(email)`; a consulta precisa concordar com ela.
        if (Operador::query()->whereRaw('lower(email) = ?', [$email])->exists()) {
            $this->error(sprintf('Já existe um operador com o e-mail "%s".', $email));

            return self::FAILURE;
        }

        $senha = (string) $this->secret('Senha (mínimo 12 caracteres)');

        // Doze, e não os seis do jogo. Esta conta liga e desliga escolas.
        if (mb_strlen($senha) < 12) {
            $this->error('A senha precisa de pelo menos 12 caracteres.');

            return self::FAILURE;
        }

        if ($senha !== (string) $this->secret('Repita a senha')) {
            $this->error('As senhas não conferem.');

            return self::FAILURE;
        }

        $operador = Operador::create([
            'nome' => trim((string) $this->argument('nome')),
            'email' => $email,
            'senha_hash' => Hash::make($senha),
            'ativo' => true,
        ]);

        $this->info(sprintf('Operador "%s" criado (id %d).', $operador->email, $operador->id));

        if (config('tenancy.host_do_console') === '') {
            // Criar a conta e não conseguir usá-la é o próximo tropeço, e ele é silencioso:
            // as rotas do console nem são registradas sem esta variável.
            $this->newLine();
            $this->warn('`CONSOLE_HOST` está vazio: o console NÃO está publicado, e `/console` responde 404.');
            $this->line('  Defina-o no `.env` e recarregue o container. Ver RUNBOOK §11.');
        }

        return self::SUCCESS;
    }
}
