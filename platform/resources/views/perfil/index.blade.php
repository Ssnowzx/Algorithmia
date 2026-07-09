@extends('layouts.app')
@section('titulo', 'Perfil')

@section('conteudo')
@php use App\Support\Arte; @endphp

<div class="cartao">
    <img src="{{ Arte::srcOu('herois/'.config("jogo.classes.{$heroi->classe}.svg"), 'herois/heroi-ranger') }}" alt="">

    <h1>{{ $heroi->nome }}</h1>
    <p class="sutil">{{ config("jogo.classes.{$heroi->classe}.nome") }} · Nível {{ $heroi->nivel }}</p>

    <dl class="atributos">
        <dt>XP</dt><dd>{{ $heroi->xp }} / {{ $xpProximoNivel }}</dd>
        <dt>Vida</dt><dd>{{ $heroi->hp_atual }} / {{ $heroi->hp_max }}</dd>
        <dt>Mana</dt><dd>{{ $heroi->mp_atual }} / {{ $heroi->mp_max }}</dd>
        <dt>Ouro</dt><dd>{{ $heroi->ouro }}</dd>
        <dt>Fases concluídas</dt><dd>{{ $fasesConcluidas }}</dd>
        <dt>Estrelas</dt><dd>{{ $estrelas }}</dd>
        <dt>Reputação</dt>
        <dd title="Cai a cada uso do Fragmento da IA; sobe ao vencer sem ele">
            {{ $heroi->reputacao }}
            @if ($heroi->reputacao <= -20) <span class="sutil">— o Fragmento sussurra</span> @endif
        </dd>
    </dl>
</div>

<div class="cartao">
    <h2>Desempenho por matéria</h2>
    <p class="sutil">Acertos comprados com o Fragmento aparecem à parte: eles não medem o que você sabe.</p>

    <table class="tabela">
        <thead>
            <tr><th>Matéria</th><th>Respostas</th><th>Acertos</th><th>Precisão</th><th>Com IA</th></tr>
        </thead>
        <tbody>
            @foreach ($porAssunto as $linha)
                <tr class="{{ $linha['respostas'] === 0 ? 'inerte' : '' }}">
                    <td>{{ $linha['rotulo'] }}</td>
                    <td>{{ $linha['respostas'] }}</td>
                    <td>{{ $linha['acertos'] }}</td>
                    <td>{{ $linha['respostas'] > 0 ? $linha['precisao'].'%' : '—' }}</td>
                    <td>{{ $linha['com_ia'] ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
