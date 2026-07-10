<?php

declare(strict_types=1);

namespace App\Dominio\Tenancy;

use RuntimeException;

/**
 * O operador pediu algo que não dá para fazer — slug repetido, host tomado, nome vazio.
 *
 * É erro de uso, e não defeito: vira mensagem no terminal ou na tela, nunca stack trace.
 */
final class ProvisionamentoInvalido extends RuntimeException {}
