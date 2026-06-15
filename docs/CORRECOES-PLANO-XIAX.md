# Correções jurídicas e de consistência — Plano de Produto Xiax

> Documento de apoio. O PDF `Xiax-Plano-de-Produto-EdTech.pdf` não tem fonte editável
> versionada no repositório, então aqui está o **texto exato a aplicar** em cada seção.
> Cole no gerador do documento e regere o PDF.
>
> **Natureza das correções:** nenhuma é bloqueador legal. São ajustes de *conformidade*
> (LGPD / leis de dados de menores) e de *consistência interna* do documento.
> Para fechar contratos e a política de privacidade definitiva, validar com advogado de
> LGPD/EdTech.

---

## 🔴 Prioridade ALTA — adicionar arquitetura jurídica de dados (Controladora/Operadora)

**Onde:** Seção 04 (Modelo de Negócio) ou Seção 14 (Riscos), como bloco novo.

**Por que me preocupa:** o documento trata LGPD como "risco a mitigar", mas nunca define
*quem é responsável legal pelos dados do aluno*. Sem isso, o ônus de consentimento dos pais
parece cair sobre a Xiax — o que assustaria qualquer sócio ou investidor. A definição correta
inverte isso e é o ponto que mais te protege.

**Texto a adicionar:**

> **ARQUITETURA DE DADOS (LGPD)**
> A **escola é a Controladora** dos dados dos alunos; a **Xiax é a Operadora**, que trata
> os dados sob instrução da escola. Quem coleta o consentimento dos responsáveis é a escola,
> no ato da matrícula. Isso é formalizado em contrato com cláusulas de proteção de dados (DPA).
> Resultado: a Xiax opera com base legal sólida e o ônus de consentimento parental fica com
> quem já tem a relação com a família.

---

## 🔴 Prioridade ALTA — incluir conformidade no ESCOPO do MVP

**Onde:** Seção 05 (MVP Inicial), tabela "Escopo do MVP".

**Por que me preocupa:** a Seção 14 afirma "conformidade desde o MVP", mas a tabela de escopo
do MVP **não lista** privacidade/consentimento como módulo. É uma contradição interna — e na
prática privacy by design precisa nascer no código, não ser remendado depois.

**Linha a adicionar na tabela "Escopo do MVP (o que entra)":**

> | Privacidade & consentimento | Multi-tenant com isolamento de dados, política de privacidade, registro de consentimento da escola, criptografia em repouso/trânsito, exclusão/retenção de dados |

---

## 🟠 Prioridade MÉDIA — nota de compliance na expansão internacional

**Onde:** Seção 09 (Estratégia de Expansão), eixo "GEOGRÁFICO" e/ou Seção 15 (LatAm).

**Por que me preocupa:** o documento projeta "Brasil → América Latina" e presença em LatAm,
mas não registra que **cada jurisdição tem sua própria lei de dados de menores**. Para um plano
que se vende como global, é uma lacuna que um investidor estrangeiro notaria.

**Texto a adicionar ao eixo GEOGRÁFICO:**

> Cada país exige sua própria camada de conformidade — GDPR/GDPR-K e UK Children's Code (Europa),
> COPPA e FERPA (EUA), leis inspiradas na LGPD (LatAm). Construindo o produto no padrão LGPD/GDPR
> desde o MVP, a internacionalização vira ajuste, não reconstrução.

---

## 🟠 Prioridade MÉDIA — ancorar legalmente o "cuidado ético" da monetização

**Onde:** Seção 10 (Monetização), bloco "Cuidado ético".

**Por que me preocupa:** o cuidado já está certo (não vender itens com dinheiro real a menores).
Mas está como escolha ética, quando na verdade tem **base legal** — o que o torna inegociável e,
de novo, te protege. Vale citar a fonte.

**Substituir o bloco "Cuidado ético" por:**

> **Cuidado ético e legal:** cosméticos e colecionáveis são comprados apenas com moeda *ganha
> jogando/estudando* — nunca incentivamos gasto real de menores. Isso não é só ética: a CONANDA
> (Res. 163/2014) restringe comunicação mercadológica dirigida a crianças, e a LGPD (art. 14)
> exige tratamento no melhor interesse do menor. Monetização de itens fica no nível da instituição,
> não do aluno. Dados de menores **nunca** alimentam publicidade ou perfilamento.

---

## 🟡 Prioridade BAIXA — ancorar o "Design Responsável" de engajamento

**Onde:** Seção 12 (Engajamento), bloco "Design Responsável".

**Por que me preocupa:** o conteúdo é bom. Só reforço que, para menores, evitar dark patterns
deixou de ser boa prática e virou tendência regulatória (UK Children's Code já exige). Citar isso
mostra maturidade ao sócio.

**Acrescentar uma frase ao final do bloco "Design Responsável":**

> Para menores, isso acompanha a tendência regulatória global (ex.: UK Age Appropriate Design Code),
> que já trata a proteção contra mecânicas manipulativas como exigência legal, não opcional.

---

## 🟡 Prioridade BAIXA — consistência do número de mercado

**Onde:** Seção 00 (Sumário) e Seção 03 (Mercado).

**Observação:** "R$ 25 Bi" aparece como tamanho do mercado EdTech BR (Sumário) **e** como TAM
(Seção 03). Se forem a mesma coisa, ok; se forem conceitos diferentes, está ambíguo. O documento
já avisa que os números são estimativas a validar com pesquisa primária (Inep/Censo Escolar,
Sebrae, Abramat), então é só conferir a consistência — não é erro factual, é clareza.

---

## ✅ O que NÃO precisa mudar (e está certo)

- Tratar **LGPD/dados de menores como risco "Alta"** — correto.
- Adiar rede pública para a "fase 3 via editais" — sábio; é o que evita entrar em direito
  administrativo/licitação e te mantém em terreno de adoção voluntária (escola privada).
- Modelo B2B2C (contrato com a escola, não com o aluno) — reduz exposição ao CDC.
- Alinhamento à BNCC tratado como diferencial de venda — correto; não é restrição legal.

---

## Resumo

| # | Prioridade | Seção | Correção |
|---|------------|-------|----------|
| 1 | 🔴 Alta | 04/14 | Definir escola = Controladora, Xiax = Operadora (DPA) |
| 2 | 🔴 Alta | 05 | Incluir privacidade/consentimento no escopo do MVP |
| 3 | 🟠 Média | 09/15 | Nota de compliance por jurisdição (GDPR/COPPA/FERPA) |
| 4 | 🟠 Média | 10 | Ancorar monetização ética em CONANDA 163/2014 + LGPD art. 14 |
| 5 | 🟡 Baixa | 12 | Citar UK Children's Code no design responsável |
| 6 | 🟡 Baixa | 00/03 | Conferir consistência do número de mercado (R$ 25 Bi) |
