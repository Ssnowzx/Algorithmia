@extends('layouts.app')
@section('titulo', $fase ? 'Editar Fase' : 'Nova Fase')

@section('conteudo')
<div class="cartao">
    <h1>{{ $fase ? "Editar fase #{$fase->id}" : 'Nova fase' }}</h1>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">
            <ul>@foreach ($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $fase ? route('mestre.fase.atualizar', $fase) : route('mestre.fase.criar') }}">
        @csrf

        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" maxlength="150" required value="{{ old('nome', $fase?->nome) }}">

        <label for="ordem_global">Ordem no mapa</label>
        <input id="ordem_global" name="ordem_global" type="number" min="1" required
               value="{{ old('ordem_global', $fase?->ordem_global ?? 1) }}">

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (['historia', 'licao', 'chefe', 'chefe_final', 'secundaria'] as $tipo)
                <option value="{{ $tipo }}" @selected(old('tipo', $fase?->tipo) === $tipo)>{{ $tipo }}</option>
            @endforeach
        </select>

        <label for="mestre_id">Mestre da região</label>
        <select id="mestre_id" name="mestre_id">
            <option value="">— nenhum —</option>
            @foreach ($mestres as $mestre)
                <option value="{{ $mestre->id }}" @selected(old('mestre_id', $fase?->mestre_id) == $mestre->id)>
                    {{ $mestre->nome }}
                </option>
            @endforeach
        </select>

        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="3">{{ old('descricao', $fase?->descricao) }}</textarea>

        <label for="inimigo_nome">Inimigo</label>
        <input id="inimigo_nome" name="inimigo_nome" type="text" maxlength="120"
               value="{{ old('inimigo_nome', $fase?->inimigo_nome) }}">

        <label for="inimigo_svg">Slug da arte do inimigo</label>
        <input id="inimigo_svg" name="inimigo_svg" type="text" maxlength="80"
               value="{{ old('inimigo_svg', $fase?->inimigo_svg) }}">

        <label for="inimigo_hp">HP do inimigo</label>
        <input id="inimigo_hp" name="inimigo_hp" type="number" min="1" required
               value="{{ old('inimigo_hp', $fase?->inimigo_hp ?? 60) }}">

        <label for="inimigo_ataque">Ataque do inimigo</label>
        <input id="inimigo_ataque" name="inimigo_ataque" type="number" min="0" required
               value="{{ old('inimigo_ataque', $fase?->inimigo_ataque ?? 10) }}">

        <label for="xp_recompensa">XP de recompensa</label>
        <input id="xp_recompensa" name="xp_recompensa" type="number" min="0" required
               value="{{ old('xp_recompensa', $fase?->xp_recompensa ?? 50) }}">

        <label for="ouro_recompensa">Ouro de recompensa</label>
        <input id="ouro_recompensa" name="ouro_recompensa" type="number" min="0" required
               value="{{ old('ouro_recompensa', $fase?->ouro_recompensa ?? 20) }}">

        <label for="item_drop_id">Item que cai</label>
        <select id="item_drop_id" name="item_drop_id">
            <option value="">— nenhum —</option>
            @foreach ($itens as $item)
                <option value="{{ $item->id }}" @selected(old('item_drop_id', $fase?->item_drop_id) == $item->id)>
                    {{ $item->nome }}
                </option>
            @endforeach
        </select>

        <label for="requisito_fase_id">Fase exigida antes desta</label>
        <select id="requisito_fase_id" name="requisito_fase_id">
            <option value="">— nenhuma —</option>
            @foreach ($fases as $outra)
                {{-- Uma fase não pode exigir a si mesma: ficaria trancada para sempre. --}}
                @continue($fase && $outra->id === $fase->id)
                <option value="{{ $outra->id }}" @selected(old('requisito_fase_id', $fase?->requisito_fase_id) == $outra->id)>
                    {{ $outra->ordem_global }}. {{ $outra->nome }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="botao">Salvar</button>
        <a class="botao botao-fantasma" href="{{ route('mestre.fases') }}">Cancelar</a>
    </form>
</div>
@endsection
