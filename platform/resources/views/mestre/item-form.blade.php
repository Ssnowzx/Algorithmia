@extends('layouts.app')
@section('titulo', $item ? 'Editar Item' : 'Novo Item')

@section('conteudo')
<div class="cartao">
    <h1>{{ $item ? "Editar item #{$item->id}" : 'Novo item' }}</h1>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">
            <ul>@foreach ($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $item ? route('mestre.item.atualizar', $item) : route('mestre.item.criar') }}">
        @csrf

        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" maxlength="120" required value="{{ old('nome', $item?->nome) }}">

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="2">{{ old('descricao', $item?->descricao) }}</textarea>

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (['arma', 'escudo', 'acessorio', 'pocao', 'especial'] as $tipo)
                <option value="{{ $tipo }}" @selected(old('tipo', $item?->tipo) === $tipo)>{{ $tipo }}</option>
            @endforeach
        </select>

        <label for="raridade">Raridade</label>
        <select id="raridade" name="raridade" required>
            @foreach (['comum', 'raro', 'epico', 'lendario'] as $raridade)
                <option value="{{ $raridade }}" @selected(old('raridade', $item?->raridade) === $raridade)>{{ $raridade }}</option>
            @endforeach
        </select>

        <label for="preco">Preço</label>
        <input id="preco" name="preco" type="number" min="0" required value="{{ old('preco', $item?->preco ?? 0) }}">

        <label for="svg_slug">Slug da arte</label>
        <input id="svg_slug" name="svg_slug" type="text" maxlength="80" required
               value="{{ old('svg_slug', $item?->svg_slug ?? 'item-generico') }}">

        <fieldset>
            <legend>Efeito (deixe 0 para omitir)</legend>
            @foreach (['ataque' => 'Ataque', 'defesa' => 'Defesa', 'cura_hp' => 'Cura HP', 'cura_mp' => 'Cura MP'] as $chave => $rotulo)
                <label for="ef_{{ $chave }}">{{ $rotulo }}</label>
                <input id="ef_{{ $chave }}" name="ef_{{ $chave }}" type="number"
                       value="{{ old("ef_{$chave}", $item?->efeito($chave) ?? 0) }}">
            @endforeach
        </fieldset>

        <label>
            <input type="checkbox" name="compravel" value="1" @checked(old('compravel', $item?->compravel ?? true))>
            Aparece na loja
        </label>

        <button type="submit" class="botao">Salvar</button>
        <a class="botao botao-fantasma" href="{{ route('mestre.itens') }}">Cancelar</a>
    </form>
</div>
@endsection
