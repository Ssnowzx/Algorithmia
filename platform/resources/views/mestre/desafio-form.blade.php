@extends('layouts.app')
@section('titulo', $desafio ? 'Editar Desafio' : 'Novo Desafio')

@section('conteudo')
@php
    // O formulário edita opções e resposta como texto; SalvarDesafioRequest as
    // reinterpreta conforme o tipo. Aqui apenas desfazemos a serialização.
    $opcoesTexto = old('opcoes') ?? ($desafio && is_array($desafio->opcoes) && array_is_list($desafio->opcoes)
        ? implode("\n", $desafio->opcoes) : '');

    $respostaTexto = old('resposta') ?? match (true) {
        $desafio === null => '',
        is_bool($desafio->resposta) => $desafio->resposta ? 'true' : 'false',
        is_array($desafio->resposta) => implode(', ', $desafio->resposta),
        default => (string) $desafio->resposta,
    };
@endphp

<div class="cartao">
    <h1>{{ $desafio ? "Editar desafio #{$desafio->id}" : 'Novo desafio' }}</h1>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">
            <ul>@foreach ($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $desafio
        ? route('mestre.desafio.atualizar', $desafio)
        : route('mestre.desafio.criar') }}">
        @csrf

        <label for="fase_id">Fase</label>
        <select id="fase_id" name="fase_id" required>
            @foreach ($fases as $fase)
                <option value="{{ $fase->id }}" @selected(old('fase_id', $desafio?->fase_id) == $fase->id)>
                    {{ $fase->ordem_global }}. {{ $fase->nome }}
                </option>
            @endforeach
        </select>

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (config('jogo.desafio.tipos') as $tipo)
                <option value="{{ $tipo }}" @selected(old('tipo', $desafio?->tipo) === $tipo)>{{ $tipo }}</option>
            @endforeach
        </select>

        <label for="assunto">Assunto</label>
        <select id="assunto" name="assunto" required>
            @foreach (config('jogo.assuntos_rotulos') as $chave => $rotulo)
                <option value="{{ $chave }}" @selected(old('assunto', $desafio?->assunto) === $chave)>{{ $rotulo }}</option>
            @endforeach
        </select>

        <label for="pergunta">Pergunta</label>
        <textarea id="pergunta" name="pergunta" required rows="3">{{ old('pergunta', $desafio?->pergunta) }}</textarea>

        <label for="codigo">Código (opcional)</label>
        <textarea id="codigo" name="codigo" rows="4">{{ old('codigo', $desafio?->codigo) }}</textarea>

        <label for="opcoes">Opções — uma por linha</label>
        <textarea id="opcoes" name="opcoes" rows="4">{{ $opcoesTexto }}</textarea>

        <label for="resposta">Resposta</label>
        <input id="resposta" name="resposta" type="text" value="{{ $respostaTexto }}">
        <small class="sutil">
            Múltipla escolha / Encontrar o erro: o índice da opção certa (0, 1, 2…).
            Verdadeiro/falso: <code>true</code> ou <code>false</code>.
            Completar: alternativas aceitas, separadas por vírgula.
            Ordenar: todos os índices na ordem correta (ex.: <code>2, 0, 1</code>).
        </small>

        <label for="explicacao">Explicação (mostrada ao errar)</label>
        <textarea id="explicacao" name="explicacao" required rows="3">{{ old('explicacao', $desafio?->explicacao) }}</textarea>

        <label for="ordem">Ordem</label>
        <input id="ordem" name="ordem" type="number" min="0" value="{{ old('ordem', $desafio?->ordem ?? 0) }}" required>

        <label for="dificuldade">Dificuldade ({{ config('jogo.desafio.dificuldade_min') }}–{{ config('jogo.desafio.dificuldade_max') }})</label>
        <input id="dificuldade" name="dificuldade" type="number"
               min="{{ config('jogo.desafio.dificuldade_min') }}"
               max="{{ config('jogo.desafio.dificuldade_max') }}"
               value="{{ old('dificuldade', $desafio?->dificuldade ?? 1) }}" required>

        <button type="submit" class="botao">Salvar</button>
        <a class="botao botao-fantasma" href="{{ route('mestre.desafios') }}">Cancelar</a>
    </form>
</div>
@endsection
