<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Traduz o formulário do Painel do Mestre num desafio válido.
 *
 * A validação por tipo não é burocracia: sem ela o painel salvava perguntas
 * impossíveis de responder — um "ordenar" sem opções deixa o jogador com nada
 * para mover, e um índice de resposta fora da faixa faz `CorretorDeRespostas`
 * recusar a resposta certa para sempre. As regras aqui espelham exatamente o que
 * o corretor espera.
 */
final class SalvarDesafioRequest extends FormRequest
{
    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'fase_id' => ['required', 'integer', Rule::exists('fases', 'id')],
            'ordem' => ['required', 'integer', 'min:0'],
            'tipo' => ['required', Rule::in(config('jogo.desafio.tipos'))],
            'assunto' => ['required', Rule::in(config('jogo.desafio.assuntos'))],
            'pergunta' => ['required', 'string'],
            'codigo' => ['nullable', 'string'],
            'explicacao' => ['required', 'string'],
            'dificuldade' => [
                'required', 'integer',
                'min:'.config('jogo.desafio.dificuldade_min'),
                'max:'.config('jogo.desafio.dificuldade_max'),
            ],
        ];
    }

    /** As opções vêm uma por linha; a resposta é interpretada conforme o tipo. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'opcoes' => $this->opcoesDoFormulario(),
            'resposta' => $this->respostaDoFormulario(),
        ]);
    }

    public function withValidator(Validator $validador): void
    {
        $validador->after(function (Validator $v): void {
            if ($v->errors()->isNotEmpty()) {
                return; // sem tipo válido, as regras abaixo não fazem sentido
            }

            $erro = $this->coerenciaDoTipo();
            if ($erro !== null) {
                $v->errors()->add('resposta', $erro);
            }
        });
    }

    /**
     * A linha pronta para o banco.
     *
     * @return array<string,mixed>
     */
    public function paraBanco(): array
    {
        /** @var list<string> $opcoes */
        $opcoes = $this->input('opcoes');

        return [
            'fase_id' => (int) $this->input('fase_id'),
            'ordem' => (int) $this->input('ordem'),
            'tipo' => (string) $this->input('tipo'),
            'assunto' => (string) $this->input('assunto'),
            'pergunta' => trim((string) $this->input('pergunta')),
            'codigo' => trim((string) $this->input('codigo')) ?: null,
            'opcoes' => $opcoes === [] ? null : $opcoes,
            'resposta' => $this->input('resposta'),
            'explicacao' => trim((string) $this->input('explicacao')),
            'dificuldade' => (int) $this->input('dificuldade'),
        ];
    }

    /** @return list<string> */
    private function opcoesDoFormulario(): array
    {
        $texto = trim((string) $this->input('opcoes', ''));
        if ($texto === '') {
            return [];
        }

        $linhas = array_map(trim(...), explode("\n", $texto));

        return array_values(array_filter($linhas, static fn (string $l): bool => $l !== ''));
    }

    private function respostaDoFormulario(): mixed
    {
        $bruta = trim((string) $this->input('resposta', ''));

        return match ($this->input('tipo')) {
            'vf' => in_array(mb_strtolower($bruta), ['true', 'v', '1', 'verdadeiro'], true),
            'multipla', 'erro' => (int) $bruta,
            'completar' => $this->listaDeTextos($bruta),
            'ordenar', 'arrastar' => array_map(intval(...), $this->listaDeTextos($bruta)),
            default => $bruta,
        };
    }

    /** @return list<string> */
    private function listaDeTextos(string $bruta): array
    {
        $partes = array_map(trim(...), explode(',', $bruta));

        return array_values(array_filter($partes, static fn (string $p): bool => $p !== ''));
    }

    /** Devolve a mensagem de erro, ou null se o desafio é jogável. */
    private function coerenciaDoTipo(): ?string
    {
        /** @var list<string> $opcoes */
        $opcoes = $this->input('opcoes');
        $resposta = $this->input('resposta');
        $n = count($opcoes);

        return match ($this->input('tipo')) {
            'multipla', 'erro' => match (true) {
                $n < 2 => 'Múltipla escolha e Encontrar o erro precisam de ao menos 2 opções (uma por linha).',
                ! is_int($resposta) || $resposta < 0 || $resposta >= $n => 'A resposta deve ser o índice de uma opção válida (0 a '.($n - 1).').',
                default => null,
            },

            'ordenar' => $this->erroDeOrdenar($opcoes, $resposta),

            // "Relacionar" usa opcoes = {itens, alvos}. O textarea não gera esse
            // formato, então criar por aqui produziria um desafio insolúvel.
            'arrastar' => 'Relacionar (arrastar) usa opções no formato {itens, alvos} e ainda não pode ser criado por este formulário.',

            'completar' => is_array($resposta) && $resposta !== []
                ? null
                : 'Completar precisa de ao menos uma resposta aceita (separe alternativas por vírgula).',

            default => null, // 'vf' aceita qualquer booleano
        };
    }

    /**
     * @param  list<string>  $opcoes
     */
    private function erroDeOrdenar(array $opcoes, mixed $resposta): ?string
    {
        $n = count($opcoes);
        if ($n < 2) {
            return 'Ordenar precisa de ao menos 2 opções — são os itens que o jogador vai mover.';
        }

        $ordenada = is_array($resposta) ? array_map(intval(...), $resposta) : [];
        sort($ordenada);

        return $ordenada === range(0, $n - 1)
            ? null
            : 'Em Ordenar, a resposta deve conter TODOS os índices das opções, cada um uma vez, na ordem correta (ex.: 3 opções → 2,0,1).';
    }
}
