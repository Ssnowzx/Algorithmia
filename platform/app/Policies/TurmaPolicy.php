<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Turma;
use App\Models\Usuario;

/**
 * Tenancy e autorização são coisas diferentes, e as duas são necessárias.
 *
 * O RLS garante que a Escola A não vê a turma da Escola B — ele nem chega a devolver a
 * linha. Ele **não** diz nada sobre um professor da Escola A que abre a turma de outro
 * professor da Escola A: para o banco, as duas linhas pertencem ao mesmo tenant.
 *
 * Trancar o prédio e deixar as salas abertas seria o pior dos dois mundos: a sensação de
 * isolamento, sem o isolamento.
 */
final class TurmaPolicy
{
    /** O mestre administra a instituição inteira; o professor, o que leciona. */
    public function verQualquer(Usuario $usuario): bool
    {
        return $usuario->podeVerRelatorios();
    }

    public function ver(Usuario $usuario, Turma $turma): bool
    {
        if ($usuario->ehMestre()) {
            return true;
        }

        if (! $usuario->ehProfessor()) {
            return false;
        }

        // `exists()` e não `contains()`: carregar a coleção de turmas para responder a
        // uma pergunta booleana é caro, e num professor com trinta turmas é caro sempre.
        return $usuario->turmasQueLeciona()->whereKey($turma->getKey())->exists();
    }

    /** Criar, editar e matricular é administração da escola. */
    public function administrar(Usuario $usuario): bool
    {
        return $usuario->ehMestre();
    }
}
