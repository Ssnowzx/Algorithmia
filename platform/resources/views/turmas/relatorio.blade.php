@extends('layouts.app')
@section('titulo', 'Turma ' . $turma->nome)

@section('conteudo')
<div class="cartao">
    <h1>{{ $relatorio['turma']['nome'] }}</h1>
    <p class="sutil">
        Código <code>{{ $relatorio['turma']['codigo'] }}</code> ·
        {{ $relatorio['turma']['alunos'] }} aluno(s)
    </p>

    <h2>Alunos</h2>
    <table class="tabela">
        <thead>
            <tr>
                <th>Aluno</th><th>Nível</th><th>Fases</th><th>Estrelas</th>
                <th>Tentativas</th><th>Precisão</th><th>Com o Fragmento</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($relatorio['alunos'] as $aluno)
                <tr>
                    <td>{{ $aluno['nome'] }}</td>
                    <td>{{ $aluno['nivel'] }}</td>
                    <td>{{ $aluno['fases_concluidas'] }}</td>
                    <td>{{ $aluno['estrelas'] }}</td>
                    <td>{{ $aluno['tentativas'] }}</td>
                    {{-- Sem tentativas não há precisão: 0% mentiria sobre quem nunca jogou. --}}
                    <td>{{ $aluno['precisao'] === null ? '—' : $aluno['precisao'] . '%' }}</td>
                    <td>{{ $aluno['respostas_com_ia'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Por assunto</h2>
    <p class="sutil">Onde a turma acerta, e onde ela pediu ajuda ao Fragmento da IA.</p>
    @if ($relatorio['por_assunto'] === [])
        <p class="sutil">Ninguém respondeu nada ainda.</p>
    @else
        <table class="tabela">
            <thead><tr><th>Assunto</th><th>Respostas</th><th>Precisão</th><th>Com o Fragmento</th></tr></thead>
            <tbody>
                @foreach ($relatorio['por_assunto'] as $linha)
                    <tr>
                        <td>{{ $linha['assunto'] }}</td>
                        <td>{{ $linha['total'] }}</td>
                        <td>{{ $linha['precisao'] }}%</td>
                        <td>{{ $linha['com_ia'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
