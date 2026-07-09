@extends('layouts.app')
@section('titulo', 'Epílogo')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/cena.css') }}">
@endpush

@section('conteudo')
@php
    use App\Support\Reputacao;

    // O equilíbrio é o desfecho de reserva: nenhum jogador fica sem epílogo por
    // causa de um valor inesperado.
    $desfecho = config("finais.{$final}") ?? config('finais.equilibrio');
@endphp

<div class="tela-final {{ $desfecho['classe'] }}">
    <div class="selo-final">{{ $desfecho['selo'] }}</div>
    <h1>{{ $desfecho['titulo'] }}</h1>

    <div class="epilogo">
        @foreach ($desfecho['epilogo'] as $paragrafo)
            <p>{{ $paragrafo }}</p>
        @endforeach
    </div>

    <div class="painel" style="text-align:center">
        <h3>🏆 Parabéns, {{ $heroi->nome }}!</h3>
        <p class="subtitulo">Você concluiu Algorithmia — A Lenda dos Cinco Mestres.</p>
        <p>Nível {{ $heroi->nivel }} · Reputação {{ Reputacao::rotulo($heroi->reputacao) }}</p>

        <div style="margin-top:1rem;display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap">
            <a class="botao" href="{{ route('mapa') }}">Voltar ao Mapa</a>
            <a class="botao botao-fantasma" href="{{ route('perfil') }}">Ver Estatísticas</a>
        </div>
    </div>
</div>
@endsection
