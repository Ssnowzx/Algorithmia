<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Suporte;

/**
 * BatalhaService com o sorteio de desafios substituído por uma sequência fixa.
 *
 * O sorteio real usa shuffle(), que é semeável (mt_srand) mas cuja permutação é
 * um detalhe do Mt19937 do PHP. Amarrar as asserções a ela travaria a
 * IMPLEMENTAÇÃO do sorteio, não as REGRAS de combate — e o port em Laravel não
 * teria como reproduzi-la sem copiar o algoritmo. Fixando a sequência, os
 * vetores-ouro passam a medir só o que precisa sobreviver à migração: dano,
 * combo, especial, fúria, XP, ouro, reputação, estrelas e conquistas.
 *
 * As invariantes do sorteio em si (priorizar inéditos, ordenar por dificuldade)
 * são cobertas à parte, sem depender do RNG, em tests/Motor/SorteioTest.php.
 */
final class MotorComSorteioFixo extends \BatalhaService
{
    /** @var list<array<string,mixed>> */
    private array $sequencia = [];
    private int $limite = 0;

    /**
     * @param list<array<string,mixed>> $desafios sequência inteira jogável
     * @param int|null $limite  gatilho da morte súbita; padrão = tamanho da sequência
     */
    public function comSequencia(array $desafios, ?int $limite = null): self
    {
        $this->sequencia = $desafios;
        $this->limite = $limite ?? count($desafios);
        return $this;
    }

    protected function sortearDesafios(array $personagem, array $fase): array
    {
        return ['lista' => $this->sequencia, 'limite' => $this->limite];
    }
}
