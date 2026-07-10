@extends('layouts.app')
@section('titulo', $fase->nome)

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/batalha.css') }}">
@endpush

@section('conteudo')
@php
    use App\Support\Arte;

    $heroiSvg = config("jogo.classes.{$heroi->classe}.svg", 'heroi-ranger');
    $fundoRegiao = $fase->mestre
        ? config("jogo.regioes_mestre.{$fase->mestre->svg_slug}.fundo", 'fundo-batalha')
        : 'fundo-batalha';
    $fundo = Arte::srcOu("fundos/{$fundoRegiao}", 'fundos/fundo-batalha');
@endphp

<div class="arena">
    @if ($intel !== '')
        <div class="intel-inimigo" role="note">🧠 <span>{{ $intel }}</span></div>
    @endif

    <div class="campo-batalha" id="campo" style="--fundo-bioma: url('{{ $fundo }}')">
        <div class="cena-ambiente" aria-hidden="true"></div>
        <div class="combo-indicador" id="comboInd"></div>
        <div class="banner-duelo" id="bannerDuelo" hidden>⚔ DUELO FINAL<br><small>Chega de aquecimento.</small></div>

        <div class="combatente inimigo" id="ladoInimigo">
            <div class="sprite" id="spriteInimigo">
                <img src="{{ Arte::srcOu('inimigos/'.$estado['inimigo_svg'], 'inimigos/inimigo-bug') }}"
                     alt="{{ $estado['inimigo_nome'] }}">
            </div>
            <div class="nome-combatente">{{ $estado['inimigo_nome'] }}</div>

            {{-- Lore do bestiário: o epíteto na tela, a história no tooltip. --}}
            @if ($bestiario)
                <div class="bestiario-titulo" title="{{ $bestiario['lore'] }}">“{{ $bestiario['titulo'] }}”</div>
            @endif
            <div class="barra barra-hp">
                <div class="barra-fill" id="hpInimigoFill" style="width:100%"></div>
                <span class="barra-label" id="hpInimigoLabel">{{ $estado['inimigo_hp'] }} / {{ $estado['inimigo_hp_max'] }}</span>
            </div>
        </div>

        <div class="combatente heroi" id="ladoHeroi">
            <div class="sprite" id="spriteHeroi">
                <img src="{{ Arte::srcOu("herois/{$heroiSvg}", 'herois/heroi-ranger') }}" alt="{{ $heroi->nome }}">
            </div>
            <div class="nome-combatente">{{ $heroi->nome }}</div>
            <div class="barra barra-hp">
                <div class="barra-fill" id="hpHeroiFill" style="width:100%"></div>
                <span class="barra-label" id="hpHeroiLabel">{{ $estado['heroi_hp'] }} / {{ $estado['heroi_hp_max'] }}</span>
            </div>
            <div class="barra barra-mp" style="margin-top:3px">
                <div class="barra-fill" id="mpHeroiFill" style="width:100%"></div>
                <span class="barra-label" id="mpHeroiLabel">{{ $estado['heroi_mp'] }} / {{ $estado['heroi_mp_max'] }}</span>
            </div>
        </div>
    </div>

    {{-- Preenchido pelo JS a cada turno, a partir do que paraCliente() autoriza. --}}
    <div class="painel-desafio" id="painelDesafio"></div>

    <div class="barra-acoes-secundarias" id="acoesSecundarias">
        <button class="botao botao-sm" id="btnEspecial" title="Gasta mana e dobra o dano do próximo acerto">✦ Especial</button>

        @foreach ($itensUsaveis as $item)
            @php $ehFragmento = $item['svg_slug'] === config('jogo.item_fragmento_ia'); @endphp
            <button class="botao botao-sm {{ $ehFragmento ? 'btn-ia' : 'botao-fantasma' }}"
                    data-acao="{{ $ehFragmento ? 'fragmento' : 'pocao' }}"
                    data-item="{{ $item['item_id'] }}">
                {{ $ehFragmento ? '🤖' : '🧪' }} {{ $item['nome'] }} ({{ $item['quantidade'] }})
            </button>
        @endforeach

        <button class="botao botao-sm botao-fantasma" id="btnFugir" style="margin-left:auto">🏃 Fugir</button>
    </div>
</div>

<div id="telaResultado" style="display:none"></div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
    // Mesmo contrato do jogo em PHP puro, para que public/js/batalha.js seja
    // reaproveitado sem uma linha de alteração. Só as URLs mudaram de forma.
    window.BATALHA = {
        csrf: @json(csrf_token()),
        estado: @json($estado),
        urls: {
            responder:  @json(route('batalha.responder')),
            especial:   @json(route('batalha.especial')),
            pocao:      @json(route('batalha.pocao')),
            fragmento:  @json(route('batalha.fragmento')),
            fugir:      @json(route('batalha.fugir')),
            mapa:       @json(route('mapa')),
            reiniciar:  @json(route('batalha.iniciar', $fase))
        }
    };
</script>
<script src="{{ asset('js/juice.js') }}"></script>
<script src="{{ asset('js/batalha.js') }}"></script>
@endpush
