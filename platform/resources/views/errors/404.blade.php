@extends('layouts.app')
@section('titulo', 'Página não encontrada')

@section('conteudo')
<div class="cartao cartao-estreito" style="text-align:center">
    <h1>404</h1>
    <p>Esta página caiu no Abismo do <code>/dev/null</code>.</p>
    <p class="sutil">Nem o Lorde Segfault sabe onde ela foi parar.</p>

    <a class="botao" href="{{ auth()->check() ? route('mapa') : route('home') }}">Voltar ao reino</a>
</div>
@endsection
