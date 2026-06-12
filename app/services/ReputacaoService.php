<?php
/**
 * Cuida do eixo de reputação (Disciplina vs. IA) e escolhe variantes narrativas.
 *
 * A reputação cai sempre que o jogador usa o Fragmento da IA e sobe quando ele
 * vence sem recorrer a ele. Determina diálogos e o final da história.
 */
class ReputacaoService
{
    private Personagem $personagens;

    public function __construct()
    {
        $this->personagens = new Personagem();
    }

    /**
     * Aplica uma variação de reputação, limitando ao intervalo permitido.
     */
    public function ajustar(int $personagemId, int $delta): int
    {
        $p = $this->personagens->findById($personagemId);
        $novo = max(REPUTACAO_MIN, min(REPUTACAO_MAX, (int) $p['reputacao'] + $delta));
        $this->personagens->update($personagemId, ['reputacao' => $novo]);
        return $novo;
    }

    /**
     * Variante de diálogo conforme o alinhamento atual do personagem.
     * Reputação negativa o aproxima do caminho da IA.
     */
    public function variante(array $personagem): string
    {
        return ((int) $personagem['reputacao'] <= -20) ? 'ia' : 'padrao';
    }

    /**
     * Decide o final com base na reputação e na escolha feita na fase final.
     * Retorna 'mestre', 'singularidade' ou 'equilibrio'.
     */
    public function finalDeterminado(array $personagem): string
    {
        $rep = (int) $personagem['reputacao'];
        $escolha = (new Escolha())->valor((int) $personagem['id'], 'final');

        if ($escolha === 'fundir') {
            return 'singularidade';
        }
        if ($escolha === 'destruir') {
            return $rep >= 40 ? 'mestre' : 'equilibrio';
        }
        if ($escolha === 'reescrever') {
            return 'equilibrio';
        }
        // Sem escolha explícita: deduz pelo alinhamento acumulado.
        if ($rep >= 40) return 'mestre';
        if ($rep <= -40) return 'singularidade';
        return 'equilibrio';
    }
}
