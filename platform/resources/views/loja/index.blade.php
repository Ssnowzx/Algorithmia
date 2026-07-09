@extends('layouts.app')
@section('titulo', 'Loja do Reino')

@section('conteudo')
@php use App\Support\Arte; @endphp

<section class="hud-heroi">
    <strong>Loja do Reino</strong>
    <span>🪙 {{ $heroi->ouro }} de ouro</span>
    <span>⚔ Ataque {{ $atributos['ataque'] }}</span>
    <span>🛡 Defesa {{ $atributos['defesa'] }}</span>
</section>

@foreach (['sucesso', 'erro', 'info'] as $tipo)
    @if (session($tipo))
        <div class="flash flash-{{ $tipo }}" role="status">{{ session($tipo) }}</div>
    @endif
@endforeach

<ul class="grade-itens">
    @foreach ($itens as $item)
        @php
            $equipado = $equipadoPorTipo[$item->tipo] ?? null;
            $quantidade = $possui[$item->id] ?? 0;
        @endphp

        <li class="carta-item raridade-{{ $item->raridade }}">
            <img src="{{ Arte::srcOu("itens/{$item->svg_slug}", 'itens/item-generico') }}" alt="">
            <strong>{{ $item->nome }}</strong>
            <p class="sutil">{{ $item->descricao }}</p>

            @if ($item->efeito)
                <ul class="efeitos">
                    @foreach ($item->efeito as $chave => $valor)
                        <li>{{ str_replace('_', ' ', $chave) }} +{{ $valor }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- Comparação ciente de slot: o número absoluto não diz se vale trocar. --}}
            @if ($equipado && $equipado->id !== $item->id && $item->ehEquipavel())
                @php
                    $ganho = ($item->efeito('ataque') + $item->efeito('defesa'))
                           - ($equipado->efeito('ataque') + $equipado->efeito('defesa'));
                @endphp
                <p class="comparacao {{ $ganho >= 0 ? 'ganho' : 'perda' }}">
                    {{ $ganho >= 0 ? '+' : '' }}{{ $ganho }} em relação a {{ $equipado->nome }}
                </p>
            @endif

            <p class="preco">🪙 {{ $item->preco }}</p>

            @if ($quantidade > 0)
                <span class="sutil">Você tem {{ $quantidade }}</span>
            @endif

            <div class="acoes">
                <form method="POST" action="{{ route('loja.comprar', $item) }}">
                    @csrf
                    <button type="submit" class="botao botao-sm" @disabled($heroi->ouro < $item->preco)>
                        Comprar
                    </button>
                </form>

                @if ($quantidade > 0 && $item->podeSerVendido())
                    <form method="POST" action="{{ route('loja.vender', $item) }}">
                        @csrf
                        <button type="submit" class="botao botao-sm botao-fantasma">
                            Vender por {{ $item->valorDeVenda() }}
                        </button>
                    </form>
                @endif
            </div>
        </li>
    @endforeach
</ul>
@endsection
