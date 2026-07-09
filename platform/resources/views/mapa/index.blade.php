@extends('layouts.app')
@section('titulo', 'Mapa')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/mapa.css') }}">
@endpush

@section('conteudo')
@php use App\Support\{Arte, LeituraDoInimigo}; @endphp

<section class="hud-heroi">
    <strong>{{ $heroi->nome }}</strong>
    <span class="sutil">Nível {{ $heroi->nivel }} · {{ $heroi->xp }} XP</span>
    <span>❤ {{ $heroi->hp_atual }}/{{ $heroi->hp_max }}</span>
    <span>✦ {{ $heroi->mp_atual }}/{{ $heroi->mp_max }}</span>
    <span>🪙 {{ $heroi->ouro }}</span>
    <span>⭐ {{ $estrelas }}</span>
</section>

<ol class="mapa">
    @foreach ($fases as $no)
        @php
            $fase = $no['fase'];
            $progresso = $no['progresso'];
        @endphp

        <li class="no-fase {{ $no['liberada'] ? 'liberada' : 'trancada' }} tipo-{{ $fase->tipo }}">
            @if ($no['liberada'])
                {{-- Fases de história não têm combate: levam à cena, não à arena. --}}
                <a href="{{ $fase->tipo === 'historia'
                    ? route('historia.ver', $fase)
                    : route('batalha.iniciar', $fase) }}">
            @else
                {{-- Trancada não vira link: a guarda de verdade está no controller,
                     mas oferecer o caminho e depois recusá-lo é UI que mente. --}}
                <span aria-disabled="true">
            @endif

                <img src="{{ Arte::srcOu("mapas/fase-{$fase->ordem_global}", 'mapas/fase-1') }}" alt="">
                <strong>{{ $fase->nome }}</strong>

                @if ($fase->mestre)
                    <span class="sutil">{{ $fase->mestre->regiao }}</span>
                @endif

                @if ($progresso)
                    <span class="estrelas" title="{{ $progresso->estrelas }} de 3 estrelas">
                        {{ str_repeat('★', $progresso->estrelas) }}{{ str_repeat('☆', 3 - $progresso->estrelas) }}
                    </span>
                @elseif (! $no['liberada'])
                    <span class="cadeado" aria-label="Trancada">🔒</span>
                @endif

                {{-- Chip de tática: só aparece para inimigos que ameaçam de verdade.
                     A frase inteira fica no tooltip. --}}
                @php $tag = LeituraDoInimigo::tag($fase->inimigo_hp, $fase->inimigo_ataque); @endphp
                @if ($tag !== '' && ! $progresso)
                    <div class="no-tatica" title="{{ LeituraDoInimigo::frase($fase->inimigo_hp, $fase->inimigo_ataque) }}">
                        {{ $tag }}
                    </div>
                @endif

            @if ($no['liberada'])
                </a>
            @else
                </span>
            @endif
        </li>
    @endforeach
</ol>
@endsection
