@extends('layouts.app')
@section('titulo', 'Painel do Mestre')

@section('conteudo')
<div class="cartao">
    <h1>Painel do Mestre</h1>
    <p class="sutil">O que você muda aqui vale para todos os heróis do reino.</p>

    <dl class="atributos">
        <dt>Fases</dt><dd>{{ $totais['fases'] }}</dd>
        <dt>Desafios</dt><dd>{{ $totais['desafios'] }}</dd>
        <dt>Itens</dt><dd>{{ $totais['itens'] }}</dd>
        <dt>Mestres</dt><dd>{{ $totais['mestres'] }}</dd>
        <dt>Jogadores</dt><dd>{{ $totais['jogadores'] }}</dd>
    </dl>

    <nav class="acoes">
        <a class="botao" href="{{ route('mestre.fases') }}">Gerir Fases</a>
        <a class="botao" href="{{ route('mestre.desafios') }}">Gerir Desafios</a>
        <a class="botao" href="{{ route('mestre.itens') }}">Gerir Itens</a>
    </nav>
</div>
@endsection
