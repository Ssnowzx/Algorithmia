@extends('layouts.console')

@section('titulo', 'Entrar')

@section('conteudo')
    <h1>Console da plataforma</h1>

    <p>Esta não é a porta do jogo. Contas de aluno, professor e mestre não entram aqui.</p>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('console.entrar') }}" class="formulario">
        @csrf

        <label for="email">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

        <label for="password">Senha</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">

        <button type="submit" class="botao">Entrar</button>
    </form>
@endsection
