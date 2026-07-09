@extends('layouts.app')
@section('titulo', 'Criar conta')

@section('conteudo')
<div class="cartao cartao-estreito">
    <h1>Nova conta</h1>

    @if ($errors->any())
        <div class="flash flash-erro" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('registro') }}">
        @csrf

        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" value="{{ old('nome') }}" required maxlength="80" autofocus>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="150" autocomplete="username">

        <label for="password">Senha</label>
        <input id="password" name="password" type="password" required minlength="6" autocomplete="new-password">

        <label for="password_confirmation">Repita a senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

        <button type="submit" class="botao">Criar conta</button>
    </form>

    <p>Já tem conta? <a href="{{ route('login') }}">Entre</a>.</p>
</div>
@endsection
