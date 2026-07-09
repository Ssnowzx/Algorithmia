@extends('layouts.app')
@section('titulo', 'Ranking')

@section('conteudo')
<div class="cartao">
    <h1>Ranking de Algorithmia</h1>
    <p class="sutil">Por nível e, no empate, por XP. Ninguém chega aqui por sorte.</p>

    <table class="tabela">
        <thead>
            <tr><th>#</th><th>Herói</th><th>Classe</th><th>Nível</th><th>XP</th></tr>
        </thead>
        <tbody>
            @foreach ($ranking as $posicao => $heroi)
                <tr class="{{ $heroi->id === $heroiId ? 'destaque' : '' }}">
                    <td>{{ $posicao + 1 }}</td>
                    <td>{{ $heroi->nome }} <span class="sutil">({{ $heroi->usuario?->nome }})</span></td>
                    <td>{{ config("jogo.classes.{$heroi->classe}.nome") }}</td>
                    <td>{{ $heroi->nivel }}</td>
                    <td>{{ $heroi->xp }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
