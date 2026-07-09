<?php

declare(strict_types=1);

namespace App\Dominio\Combate;

/**
 * Máquina de estados de uma batalha por turnos.
 *
 * Princípio anti-cola: as respostas corretas NUNCA são enviadas ao cliente. O
 * estado guarda os desafios completos — com gabarito — e `paraCliente()` é a
 * única porta de saída, que os remove.
 *
 * `batalhaId` nasce aqui e nunca vem do navegador. É a chave de idempotência da
 * recompensa: sem ela, a única proteção contra duplo-crédito seria um flag de
 * sessão, que é o que o legado tem.
 *
 * @phpstan-type LinhaDeDesafio array{id:int,tipo:string,assunto:string,pergunta:string,codigo:?string,opcoes:mixed,resposta:mixed,explicacao:string,dificuldade:int}
 */
final class EstadoDeBatalha
{
    /**
     * @param  list<LinhaDeDesafio>  $desafios  sequência inteira jogável: os N principais
     *                                          (curva didática crescente) seguidos da reserva que abastece o Duelo Final
     * @param  int  $total  limite de RITMO que abre a morte súbita — não é condição de derrota
     */
    public function __construct(
        public readonly string $batalhaId,
        public readonly int $faseId,
        public readonly int $personagemId,
        public array $desafios,
        public int $total,
        public readonly string $inimigoNome,
        public readonly string $inimigoSvg,
        public readonly int $inimigoHpMax,
        public int $inimigoHp,
        public readonly int $inimigoAtaque,
        public int $heroiHp,
        public readonly int $heroiHpMax,
        public int $heroiMp,
        public readonly int $heroiMpMax,
        public readonly int $heroiAtaque,
        public readonly int $heroiDefesa,
        public readonly int $heroiDefesaBase,
        public readonly int $bonusEquipAtaque,
        public readonly int $bonusEquipDefesa,
        public readonly int $heroiNivel,
        public int $indice = 0,
        public int $combo = 0,
        public bool $especialArmado = false,
        public int $acertos = 0,
        public int $erros = 0,
        public bool $usouIa = false,
        public bool $finalizada = false,
        public ?string $resultado = null,
        public bool $morteSubita = false,
        public int $rodadaSubita = 0,
        public int $danoTotal = 0,
        public int $danoEquipTotal = 0,
        public int $bloqueadoTotal = 0,
        public int $hpCuradoTotal = 0,
        public int $mpCuradoTotal = 0,
    ) {}

    /** @return LinhaDeDesafio|null */
    public function desafioAtual(): ?array
    {
        return $this->desafios[$this->indice] ?? null;
    }

    /**
     * Estado seguro para a tela: sem a sequência de desafios, sem gabarito e sem
     * explicação (que entregaria a resposta antes da hora).
     *
     * @return array<string,mixed>
     */
    public function paraCliente(): array
    {
        $atual = $this->finalizada ? null : $this->desafioAtual();

        return [
            'inimigo_nome' => $this->inimigoNome,
            'inimigo_svg' => $this->inimigoSvg,
            'inimigo_hp' => $this->inimigoHp,
            'inimigo_hp_max' => $this->inimigoHpMax,
            'heroi_hp' => $this->heroiHp,
            'heroi_hp_max' => $this->heroiHpMax,
            'heroi_mp' => $this->heroiMp,
            'heroi_mp_max' => $this->heroiMpMax,
            'combo' => $this->combo,
            'especial_armado' => $this->especialArmado,
            'indice' => $this->indice,
            'total' => $this->total,
            'finalizada' => $this->finalizada,
            'morte_subita' => $this->morteSubita,
            'rodada_subita' => $this->rodadaSubita,
            'desafio' => $atual === null ? null : [
                'id' => $atual['id'],
                'tipo' => $atual['tipo'],
                'assunto' => $atual['assunto'],
                'pergunta' => $atual['pergunta'],
                'codigo' => $atual['codigo'],
                'opcoes' => $atual['opcoes'],
                'dificuldade' => $atual['dificuldade'],
            ],
        ];
    }

    /** @return array<string,mixed> */
    public function paraArray(): array
    {
        return get_object_vars($this);
    }

    /** @param  array<string,mixed>  $dados */
    public static function deArray(array $dados): self
    {
        /** @var array{batalhaId:string,faseId:int,personagemId:int,desafios:list<LinhaDeDesafio>,total:int,inimigoNome:string,inimigoSvg:string,inimigoHpMax:int,inimigoHp:int,inimigoAtaque:int,heroiHp:int,heroiHpMax:int,heroiMp:int,heroiMpMax:int,heroiAtaque:int,heroiDefesa:int,heroiDefesaBase:int,bonusEquipAtaque:int,bonusEquipDefesa:int,heroiNivel:int,indice:int,combo:int,especialArmado:bool,acertos:int,erros:int,usouIa:bool,finalizada:bool,resultado:?string,morteSubita:bool,rodadaSubita:int,danoTotal:int,danoEquipTotal:int,bloqueadoTotal:int,hpCuradoTotal:int,mpCuradoTotal:int} $dados */
        return new self(...$dados);
    }
}
