<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dominio\Operacao\ServicoDeAuditoria;
use App\Dominio\Tenancy\InstituicaoInjogavel;
use App\Dominio\Tenancy\PainelDeInstituicoes;
use App\Dominio\Tenancy\ProvisionamentoDeInstituicoes;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * O console do operador da plataforma. Etapa E, Fase 9 do roteiro v1.
 *
 * **Ele não fura o RLS, e é importante entender por quê.** As tabelas que ele lê e escreve
 * diretamente — `tenants`, `tenant_dominios`, `operadores` — são catálogo global, sem RLS,
 * pelas mesmas razões de sempre: o `ResolverTenant` precisa lê-las antes de existir
 * contexto. As métricas de cada escola vêm do `PainelDeInstituicoes`, que entra numa
 * instituição de cada vez pelo `ContextoDoTenant::usar()` — o mesmo caminho de uma
 * requisição HTTP, sujeito às mesmas policies. Não há conexão do dono nem `BYPASSRLS`.
 *
 * O que a Etapa D.2 recusou continua não existindo: um papel de banco que lê **através**
 * das instituições, sem contexto.
 *
 * O console **não provisiona** instituições. Criar uma escola é criar uma escola vazia, e
 * só o `algorithmia:importar` sabe enchê-la — um comando que roda no servidor, lê um dump e
 * demora minutos. Um botão "criar escola" na web produziria uma instituição desligada e
 * inútil, e alguém acabaria ligando-a à força. Provisionar mora no terminal, junto do
 * comando que semeia. Ver `RUNBOOK §11`.
 */
final class ConsoleController extends Controller
{
    public function __construct(private readonly ServicoDeAuditoria $auditoria) {}

    // ------------------------------------------------------------------ autenticação

    public function mostrarLogin(): View
    {
        return view('console.entrar');
    }

    public function entrar(Request $requisicao): RedirectResponse
    {
        $credenciais = $requisicao->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // `ativo` entra nas credenciais: revogar o acesso de quem saiu da equipe não pode
        // exigir apagar as linhas de auditoria que ele assinou. E um operador desligado tem
        // de receber a mesma mensagem de quem errou a senha — dizer "sua conta foi
        // desativada" confirma que o e-mail existe.
        if (! Auth::guard('operador')->attempt([...$credenciais, 'ativo' => true], remember: false)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        // Sem isto, um id de sessão capturado antes do login continua valendo depois.
        $requisicao->session()->regenerate();

        Auth::guard('operador')->user()?->forceFill(['ultimo_acesso_em' => now()])->save();

        return redirect()->intended(route('console.painel'));
    }

    public function sair(Request $requisicao): RedirectResponse
    {
        Auth::guard('operador')->logout();

        $requisicao->session()->invalidate();
        $requisicao->session()->regenerateToken();

        return redirect()->route('console.entrar');
    }

    // ------------------------------------------------------------------ o painel

    public function painel(PainelDeInstituicoes $painel): View
    {
        return view('console.painel', ['instituicoes' => $painel->instituicoes()]);
    }

    // ------------------------------------------------------------------ as ações

    public function ativar(Tenant $tenant, ProvisionamentoDeInstituicoes $provisionamento): RedirectResponse
    {
        try {
            $provisionamento->ativar($tenant);
        } catch (InstituicaoInjogavel $erro) {
            // A tela não mostra a saída do smoke: ela tem centenas de linhas e nasce para um
            // terminal. A mensagem diz o comando que a produz.
            return back()->with('erro', $erro->getMessage());
        }

        $this->auditoria->registrar('tenant.ativar', 'tenant', $tenant->id, ['slug' => $tenant->slug]);

        return back()->with('sucesso', sprintf('"%s" está no ar.', $tenant->slug));
    }

    public function desativar(Tenant $tenant, ProvisionamentoDeInstituicoes $provisionamento): RedirectResponse
    {
        $provisionamento->desativar($tenant);

        $this->auditoria->registrar('tenant.desativar', 'tenant', $tenant->id, ['slug' => $tenant->slug]);

        return back()->with('sucesso', sprintf('"%s" saiu do ar. As demais seguem.', $tenant->slug));
    }

    public function flag(Request $requisicao, Tenant $tenant, ProvisionamentoDeInstituicoes $provisionamento): RedirectResponse
    {
        $dados = $requisicao->validate([
            'chave' => ['required', 'string', 'max:40'],
            // `padrao` grava `null`, que REMOVE a chave: a escola volta a acompanhar o
            // código. Gravar o valor do padrão a congelaria no valor de hoje.
            'estado' => ['required', 'in:ligar,desligar,padrao'],
        ]);

        $valor = match ($dados['estado']) {
            'ligar' => true,
            'desligar' => false,
            default => null,
        };

        try {
            $provisionamento->definirFlag($tenant, $dados['chave'], $valor);
        } catch (InvalidArgumentException $erro) {
            // A chave veio de um `<button value>` que este mesmo controller desenhou. Se ela
            // não está no catálogo, alguém a forjou — ou o catálogo mudou sob a página aberta.
            throw ValidationException::withMessages(['chave' => $erro->getMessage()]);
        }

        $this->auditoria->registrar('tenant.flag', 'tenant', $tenant->id, [
            'slug' => $tenant->slug, 'flag' => $dados['chave'], 'estado' => $dados['estado'],
        ]);

        return back()->with('sucesso', sprintf('"%s" em "%s": %s.', $dados['chave'], $tenant->slug, $dados['estado']));
    }
}
