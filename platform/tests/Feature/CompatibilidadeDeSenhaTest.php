<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Prova que os hashes de senha do legado sobrevivem à migração.
 *
 * O legado grava `password_hash($senha, PASSWORD_DEFAULT)`. Se isso não casar
 * com o `Hash::check` do Laravel, a Fase 3 teria de forçar a redefinição de
 * senha de todo mundo — o pior jeito de estrear um sistema novo. Este teste
 * existe para que a descoberta aconteça aqui, e não no dia do corte.
 */
final class CompatibilidadeDeSenhaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_hash_do_legado_e_verificavel_pelo_laravel(): void
    {
        // ARRANGE: exatamente o que AuthController do legado escreve no banco.
        $hashLegado = password_hash('qwe123', PASSWORD_DEFAULT);

        // ACT + ASSERT
        $this->assertTrue(Hash::check('qwe123', $hashLegado));
        $this->assertFalse(Hash::check('senha-errada', $hashLegado));
    }

    #[Test]
    public function o_password_default_do_php_continua_sendo_bcrypt(): void
    {
        // Se um PHP futuro trocar o PASSWORD_DEFAULT para argon2, o driver de
        // hash do Laravel precisa acompanhar — e este teste avisa antes.
        $info = password_get_info(password_hash('x', PASSWORD_DEFAULT));

        $this->assertSame('bcrypt', $info['algoName']);
        $this->assertSame('bcrypt', config('hashing.driver'));
    }

    #[Test]
    public function um_usuario_importado_do_legado_consegue_autenticar(): void
    {
        // ARRANGE
        $usuario = Usuario::create([
            'nome' => 'Mestre Boss',
            'email' => 'masterboss@boss.com',
            'senha_hash' => password_hash('qwe123', PASSWORD_DEFAULT),
            'papel' => 'mestre',
        ]);

        // ACT: o guard lê a senha por getAuthPassword() → coluna senha_hash.
        $autenticou = Auth::attempt(['email' => 'masterboss@boss.com', 'password' => 'qwe123']);

        // ASSERT
        $this->assertTrue($autenticou);
        $this->assertSame($usuario->id, Auth::id());
        $this->assertTrue($usuario->ehMestre());
    }

    #[Test]
    public function o_hash_da_senha_nunca_e_serializado(): void
    {
        // ARRANGE
        $usuario = Usuario::create([
            'nome' => 'Ana', 'email' => 'ana@algorithmia.test',
            'senha_hash' => password_hash('x', PASSWORD_DEFAULT),
        ]);

        // ACT + ASSERT
        $this->assertArrayNotHasKey('senha_hash', $usuario->toArray());
    }
}
