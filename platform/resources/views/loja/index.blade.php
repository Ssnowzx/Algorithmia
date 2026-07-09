@extends('layouts.app')
@section('titulo', 'Loja do Reino')

@section('conteudo')
@php
    use App\Support\{Arte, LeituraDoInimigo};

    $poderAtual = LeituraDoInimigo::poderTotal($atributos['ataque'], $atributos['defesa']);
@endphp

<section class="hud-heroi">
    <strong>Loja do Reino</strong>
    <span>🪙 {{ $heroi->ouro }} de ouro</span>
    <span>⚔ Ataque {{ $atributos['ataque'] }}</span>
    <span>🛡 Defesa {{ $atributos['defesa'] }}</span>
    <span title="Ataque + Defesa totais. Veja-o subir ao equipar.">💪 Poder {{ $poderAtual }}</span>
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

            {{-- Comparação ciente de slot: o número absoluto do item não diz se vale
                 trocar. O que importa é o Poder que o herói terá depois. --}}
            @if ($item->ehEquipavel())
                @php
                    $equipadoAtaque = $equipado?->efeito('ataque') ?? 0;
                    $equipadoDefesa = $equipado?->efeito('defesa') ?? 0;

                    $poderNovo = LeituraDoInimigo::poderTotal(
                        $atributos['ataque'] - $equipadoAtaque + $item->efeito('ataque'),
                        $atributos['defesa'] - $equipadoDefesa + $item->efeito('defesa'),
                    );
                    $delta = $poderNovo - $poderAtual;
                @endphp

                @if (! $equipado || $equipado->id !== $item->id)
                    <div class="cmp-poder">
                        💪 Poder {{ $poderAtual }} → <strong>{{ $poderNovo }}</strong>
                        <span class="cmp-delta {{ $delta >= 0 ? 'pos' : 'neg' }}">
                            ({{ $delta > 0 ? '+' : '' }}{{ $delta }})
                        </span>
                    </div>

                    @if ($equipado)
                        <p class="comparacao {{ $delta >= 0 ? 'ganho' : 'perda' }}">
                            {{ $delta >= 0 ? '+' : '' }}{{ $delta }} em relação a {{ $equipado->nome }}
                        </p>
                    @endif
                @endif
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
