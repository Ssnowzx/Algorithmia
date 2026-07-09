@extends('layouts.app')
@section('titulo', 'Criar herói')

@section('conteudo')
@php use App\Support\Arte; @endphp

<div class="cartao">
    <h1>Escolha sua classe</h1>
    <p class="sutil">Seis caminhos. Nenhum deles fácil.</p>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('personagem.salvar') }}">
        @csrf

        <label for="nome">Nome do herói</label>
        <input id="nome" name="nome" type="text" value="{{ old('nome') }}" required maxlength="80" autofocus>

        <fieldset class="grade-classes">
            <legend class="sr-only">Classe</legend>

            @foreach ($classes as $chave => $classe)
                <label class="carta-classe" style="--cor-classe: {{ $classe['cor'] }}">
                    <input type="radio" name="classe" value="{{ $chave }}"
                           @checked(old('classe') === $chave || (! old('classe') && $loop->first)) required>

                    <img src="{{ Arte::srcOu("herois/{$classe['svg']}", 'herois/heroi-ranger') }}" alt="">
                    <strong>{{ $classe['nome'] }}</strong>
                    <span class="sutil">{{ $classe['especie'] }}</span>

                    <dl class="atributos">
                        <dt>HP</dt><dd>{{ $classe['hp'] }}</dd>
                        <dt>MP</dt><dd>{{ $classe['mp'] }}</dd>
                        <dt>Ataque</dt><dd>{{ $classe['ataque'] }}</dd>
                        <dt>Defesa</dt><dd>{{ $classe['defesa'] }}</dd>
                    </dl>
                </label>
            @endforeach
        </fieldset>

        <button type="submit" class="botao">Começar a jornada</button>
    </form>
</div>
@endsection
