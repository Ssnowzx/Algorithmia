@extends('layouts.app')
@section('titulo', 'Entrar')

@section('conteudo')
<div class="cartao cartao-estreito">
    <h1>Entrar no reino</h1>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">

        <label for="password">Senha</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">

        <button type="submit" class="botao">Entrar</button>
    </form>

    <p>Sem conta? <a href="{{ route('registro') }}">Crie a sua</a>.</p>
</div>
@endsection
