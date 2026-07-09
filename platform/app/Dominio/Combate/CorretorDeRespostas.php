<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

/**
 * Decide se uma resposta está certa, por tipo de desafio.
 *
 * Roda sempre no servidor: o gabarito nunca chega ao cliente, então esta é a
 * única autoridade sobre acerto e erro.
 */
final class CorretorDeRespostas
{
    /** @param  array<string,mixed>  $desafio */
    public function acertou(array $desafio, mixed $resposta): bool
    {
        $gabarito = $desafio['resposta'];

        return match ($desafio['tipo']) {
            // O gabarito é o índice da opção correta.
            'multipla', 'erro' => is_scalar($resposta) && (int) $resposta === (int) $gabarito,

            'vf' => $this->paraBool($resposta) === $this->paraBool($gabarito),

            // O gabarito é texto, e aceita uma lista de alternativas válidas.
            'completar' => $this->textoConfere($gabarito, $resposta),

            // O gabarito é a sequência correta de índices ou tokens.
            'ordenar', 'arrastar' => is_array($resposta) && is_array($gabarito)
                && array_map(strval(...), $resposta) === array_map(strval(...), $gabarito),

            default => false,
        };
    }

    private function textoConfere(mixed $gabarito, mixed $resposta): bool
    {
        if (! is_scalar($resposta)) {
            return false;
        }

        $aceitas = is_array($gabarito) ? $gabarito : [$gabarito];
        $normalizada = $this->normalizar((string) $resposta);

        foreach ($aceitas as $valida) {
            if (is_scalar($valida) && $this->normalizar((string) $valida) === $normalizada) {
                return true;
            }
        }

        return false;
    }

    private function paraBool(mixed $valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        if (is_string($valor)) {
            return in_array(mb_strtolower($valor), ['1', 'true', 'v', 'verdadeiro'], true);
        }

        return (bool) $valor;
    }

    /** Compara código ignorando espaços extras e o ponto-e-vírgula final. */
    private function normalizar(string $texto): string
    {
        $texto = trim($texto);
        $texto = (string) preg_replace('/\s+/', ' ', $texto);

        return rtrim($texto, '; ');
    }
}
