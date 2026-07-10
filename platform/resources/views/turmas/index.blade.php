@extends('layouts.app')
@section('titulo', 'Turmas')

@section('conteudo')
<div class="cartao">
    <h1>Turmas</h1>
    <p class="sutil">
        {{ $ehMestre ? 'Todas as turmas desta instituição.' : 'As turmas que você leciona.' }}
    </p>

    @if ($turmas->isEmpty())
        <p class="sutil">Nenhuma turma ainda.</p>
    @else
        <table class="tabela">
            <thead><tr><th>Turma</th><th>Código</th><th>Alunos</th><th></th></tr></thead>
            <tbody>
                @foreach ($turmas as $turma)
                    <tr>
                        <td>{{ $turma->nome }}</td>
                        <td><code>{{ $turma->codigo }}</code></td>
                        <td>{{ $turma->alunos_count }}</td>
                        <td><a href="{{ route('turmas.ver', $turma) }}">Relatório</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($ehMestre)
        <form method="POST" action="{{ route('turmas.criar') }}" class="formulario">
            @csrf
            <label>Nome <input type="text" name="nome" maxlength="120" required></label>
            <label>Código <input type="text" name="codigo" maxlength="20" required></label>
            <button type="submit">Criar turma</button>
        </form>
    @endif
</div>
@endsection
