@extends('layouts.app')
@section('titulo', 'O Destino de Algorithmia')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/cena.css') }}">
@endpush

@section('conteudo')
@php use App\Support\{Arte, Reputacao}; @endphp

<div class="tela-final">
    <div class="selo-final">🌌</div>
    <h1>O Destino de Algorithmia</h1>

    <p class="subtitulo">
        Lorde Segfault está derrotado. A IA Ancestral palpita, exposta, à sua frente.
        Sua reputação atual: <strong>{{ Reputacao::rotulo($heroi->reputacao) }}</strong> ({{ $heroi->reputacao }}).
    </p>

    <div class="palco" style="min-height:200px;border-radius:18px">
        <div class="ator" style="width:160px;height:160px">
            <img src="{{ Arte::srcOu('inimigos/inimigo-ia-ancestral', 'inimigos/inimigo-bug') }}" alt="A IA Ancestral">
        </div>
    </div>

    @error('escolha')
        <div class="flash flash-erro" role="alert">{{ $message }}</div>
    @enderror

    {{-- POST: a escolha decide o final e concede a conquista secreta. --}}
    <form method="POST" action="{{ route('historia.escolher') }}" style="margin-top:1.5rem;display:grid;gap:.9rem">
        @csrf

        @foreach (config('finais.escolhas') as $opcao)
            <button name="escolha" value="{{ $opcao['valor'] }}" class="painel escolha-final">
                <strong>{{ $opcao['icone'] }} {{ $opcao['titulo'] }}</strong>
                <p class="subtitulo">{{ $opcao['descricao'] }}</p>
            </button>
        @endforeach
    </form>
</div>
@endsection
