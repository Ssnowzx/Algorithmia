@extends('layouts.app')
@section('titulo', 'Inventário')

@section('conteudo')
@php use App\Support\Arte; @endphp

<section class="hud-heroi">
    <strong>{{ $heroi->nome }}</strong>
    <span>❤ {{ $heroi->hp_atual }}/{{ $heroi->hp_max }}</span>
    <span>✦ {{ $heroi->mp_atual }}/{{ $heroi->mp_max }}</span>
    <span>🪙 {{ $heroi->ouro }}</span>
</section>

@foreach (['sucesso', 'erro', 'info'] as $tipo)
    @if (session($tipo))
        <div class="flash flash-{{ $tipo }}" role="status">{{ session($tipo) }}</div>
    @endif
@endforeach

@if ($itens->isEmpty())
    <p class="sutil">A mochila está vazia. Até o vento passa por ela sem esbarrar em nada.</p>
@endif

<ul class="grade-itens">
    @foreach ($itens as $linha)
        @php $item = $linha->item; @endphp

        <li class="carta-item raridade-{{ $item->raridade }} {{ $linha->equipado ? 'equipado' : '' }}">
            <img src="{{ Arte::srcOu("itens/{$item->svg_slug}", 'itens/item-generico') }}" alt="">
            <strong>{{ $item->nome }}</strong>
            <span class="sutil">{{ $item->tipo }} · {{ $linha->quantidade }}x</span>

            @if ($linha->equipado)
                <span class="selo">Equipado</span>
            @endif

            <div class="acoes">
                @if ($item->ehEquipavel())
                    @if ($linha->equipado)
                        <form method="POST" action="{{ route('inventario.desequipar', $item) }}">
                            @csrf
                            <button type="submit" class="botao botao-sm botao-fantasma">Desequipar</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('inventario.equipar', $item) }}">
                            @csrf
                            <button type="submit" class="botao botao-sm">Equipar</button>
                        </form>
                    @endif
                @endif

                @if ($item->ehPocao())
                    <form method="POST" action="{{ route('inventario.usar', $item) }}">
                        @csrf
                        <button type="submit" class="botao botao-sm">Usar</button>
                    </form>
                @endif

                {{-- Descartar é destrutivo e irreversível: confirma antes. --}}
                <form method="POST" action="{{ route('inventario.descartar', $item) }}"
                      data-confirmar="Descartar {{ $item->nome }}? Não há volta.">
                    @csrf
                    <button type="submit" class="botao botao-sm botao-fantasma">Descartar</button>
                </form>
            </div>
        </li>
    @endforeach
</ul>
@endsection
