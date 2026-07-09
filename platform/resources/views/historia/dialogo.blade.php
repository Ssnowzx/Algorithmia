@extends('layouts.app')
@section('titulo', $fase->nome)

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/cena.css') }}">
@endpush

@section('conteudo')
@php use App\Support\Arte; @endphp

<article class="cena">
    <h1>{{ $fase->nome }}</h1>

    @if ($fase->descricao)
        <p class="sutil">{{ $fase->descricao }}</p>
    @endif

    <ol class="dialogos">
        @forelse ($dialogos as $fala)
            <li class="fala">
                @if ($fala->svg_slug && $retrato = Arte::src("atores/{$fala->svg_slug}"))
                    <img src="{{ $retrato }}" alt="" class="retrato">
                @endif
                <strong>{{ $fala->falante }}</strong>
                <p>{{ $fala->texto }}</p>
            </li>
        @empty
            <li class="sutil">O silêncio também é uma fala.</li>
        @endforelse
    </ol>

    <div class="acoes">
        @if ($ehCombate)
            <a class="botao" href="{{ route('batalha.iniciar', $fase) }}">Encarar o inimigo</a>
        @else
            {{-- POST: no legado esta conclusão era um GET, e gravava XP. --}}
            <form method="POST" action="{{ route('historia.concluir', $fase) }}">
                @csrf
                <button type="submit" class="botao">Seguir em frente</button>
            </form>
        @endif

        <a class="botao botao-fantasma" href="{{ route('mapa') }}">Voltar ao mapa</a>
    </div>
</article>
@endsection
