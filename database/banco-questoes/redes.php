<?php
/**
 * Banco de questões — Cassandro, o Mensageiro (Torre das Conexões).
 * Redes de Computadores: modelo OSI, IP/DNS, TCP/UDP/HTTP e segurança.
 * Pool ampliado das fases 29, 30, 31, 32 (secundária) e 33 (chefe DDoS).
 */

return [
    // ---- Fase 29: As Sete Camadas (OSI) ----
    ['fase' => 29, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual camada do modelo OSI cuida do endereçamento IP e do roteamento?', 'codigo' => null,
     'opcoes' => ['Física', 'Enlace', 'Rede', 'Aplicação'], 'resposta' => 2,
     'explicacao' => 'A camada de Rede (3) endereça (IP) e escolhe rotas entre redes.', 'dif' => 3],

    ['fase' => 29, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual camada cuida da entrega fim-a-fim e abriga TCP e UDP?', 'codigo' => null,
     'opcoes' => ['Rede', 'Transporte', 'Sessão', 'Física'], 'resposta' => 1,
     'explicacao' => 'A camada de Transporte (4) controla a entrega entre os processos: TCP e UDP vivem aqui.', 'dif' => 3],

    ['fase' => 29, 'tipo' => 'vf', 'assunto' => 'redes',
     'pergunta' => 'O modelo TCP/IP é mais enxuto que o OSI, com menos camadas.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'O TCP/IP agrupa as 7 camadas do OSI em 4 (ou 5).', 'dif' => 3],

    ['fase' => 29, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O switch opera principalmente em qual camada do OSI?', 'codigo' => null,
     'opcoes' => ['Física', 'Enlace', 'Rede', 'Transporte'], 'resposta' => 1,
     'explicacao' => 'O switch comuta quadros por endereço MAC: camada de Enlace (2).', 'dif' => 4],

    ['fase' => 29, 'tipo' => 'completar', 'assunto' => 'redes',
     'pergunta' => 'A camada que entrega serviços direto ao usuário (HTTP, DNS, FTP) é a camada de ___.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['Aplicação', 'aplicacao', 'aplicação'],
     'explicacao' => 'A camada de Aplicação (7) é onde vivem os protocolos que o usuário usa.', 'dif' => 2],

    ['fase' => 29, 'tipo' => 'ordenar', 'assunto' => 'redes',
     'pergunta' => 'Ordene as camadas do OSI da 5 para a 7:', 'codigo' => null,
     'opcoes' => ['Aplicação', 'Sessão', 'Apresentação'], 'resposta' => [1, 2, 0],
     'explicacao' => 'Camada 5 Sessão, 6 Apresentação, 7 Aplicação.', 'dif' => 4],

    // ---- Fase 30: IP, DNS e Rotas ----
    ['fase' => 30, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Quantos bits tem um endereço IPv4?', 'codigo' => null,
     'opcoes' => ['16', '32', '64', '128'], 'resposta' => 1,
     'explicacao' => 'IPv4 usa 32 bits, divididos em quatro octetos (ex.: 192.168.0.1).', 'dif' => 3],

    ['fase' => 30, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Quantos bits tem um endereço IPv6?', 'codigo' => null,
     'opcoes' => ['32', '64', '128', '256'], 'resposta' => 2,
     'explicacao' => 'IPv6 usa 128 bits — espaço gigantesco, criado porque o IPv4 acabou.', 'dif' => 3],

    ['fase' => 30, 'tipo' => 'vf', 'assunto' => 'redes',
     'pergunta' => 'A máscara de sub-rede separa a parte de rede da parte de host de um endereço IP.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'A máscara (ex.: 255.255.255.0) diz quais bits identificam a rede e quais o host.', 'dif' => 4],

    ['fase' => 30, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual protocolo distribui endereços IP automaticamente aos dispositivos da rede?', 'codigo' => null,
     'opcoes' => ['DNS', 'DHCP', 'HTTP', 'ARP'], 'resposta' => 1,
     'explicacao' => 'O DHCP atribui IP, máscara e gateway sem configuração manual.', 'dif' => 3],

    ['fase' => 30, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O "gateway padrão" de uma rede local é, normalmente:', 'codigo' => null,
     'opcoes' => ['O servidor DNS público', 'O roteador que encaminha o tráfego para fora da rede local', 'O switch principal', 'O cabo de internet'],
     'resposta' => 1,
     'explicacao' => 'Pacotes destinados a outras redes saem pelo gateway (o roteador da borda).', 'dif' => 4],

    ['fase' => 30, 'tipo' => 'completar', 'assunto' => 'redes',
     'pergunta' => 'O serviço que traduz www.exemplo.com em um endereço IP é o ___.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['DNS', 'dns'],
     'explicacao' => 'O DNS é a "agenda de contatos" da internet: nome → IP.', 'dif' => 2],

    // ---- Fase 31: TCP, UDP e HTTP ----
    ['fase' => 31, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O "aperto de mão" de três vias (SYN, SYN-ACK, ACK) pertence a qual protocolo?', 'codigo' => null,
     'opcoes' => ['UDP', 'TCP', 'HTTP', 'DNS'], 'resposta' => 1,
     'explicacao' => 'O TCP estabelece a conexão com o three-way handshake antes de enviar dados.', 'dif' => 4],

    ['fase' => 31, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual protocolo é preferido para chamadas de vídeo ao vivo e jogos online?', 'codigo' => null,
     'opcoes' => ['TCP', 'UDP', 'FTP', 'SMTP'], 'resposta' => 1,
     'explicacao' => 'O UDP é veloz e sem overhead de confirmação — melhor perder um quadro do que travar.', 'dif' => 3],

    ['fase' => 31, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O código de status HTTP 500 significa:', 'codigo' => null,
     'opcoes' => ['Sucesso', 'Não encontrado', 'Erro interno do servidor', 'Redirecionamento'],
     'resposta' => 2,
     'explicacao' => '5xx são erros do servidor; 500 é a falha interna genérica.', 'dif' => 3],

    ['fase' => 31, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'O código de status HTTP 403 significa:', 'codigo' => null,
     'opcoes' => ['OK', 'Proibido (acesso negado)', 'Não encontrado', 'Criado'], 'resposta' => 1,
     'explicacao' => '403 Forbidden: o servidor entendeu o pedido, mas se recusa a atendê-lo.', 'dif' => 3],

    ['fase' => 31, 'tipo' => 'vf', 'assunto' => 'redes',
     'pergunta' => 'HTTPS é o HTTP com uma camada de criptografia (TLS/SSL).', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'O TLS cifra a comunicação, protegendo os dados em trânsito.', 'dif' => 2],

    ['fase' => 31, 'tipo' => 'completar', 'assunto' => 'redes',
     'pergunta' => 'O método HTTP usado para ENVIAR os dados de um formulário ao servidor é o ___.', 'codigo' => null,
     'opcoes' => null, 'resposta' => ['POST', 'post'],
     'explicacao' => 'POST envia dados no corpo da requisição; GET apenas busca recursos.', 'dif' => 3],

    // ---- Fase 32: O Pacote Perdido (secundária) ----
    ['fase' => 32, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Para que serve o número de sequência do TCP?', 'codigo' => null,
     'opcoes' => ['Criptografar o pacote', 'Remontar os pacotes na ordem correta no destino', 'Escolher a rota', 'Acelerar o DNS'],
     'resposta' => 1,
     'explicacao' => 'Os números de sequência permitem reordenar pacotes que chegam fora de ordem.', 'dif' => 3],

    ['fase' => 32, 'tipo' => 'vf', 'assunto' => 'redes',
     'pergunta' => 'O UDP não retransmite automaticamente um pacote perdido.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'O UDP é "dispare e esqueça": sem confirmação nem retransmissão.', 'dif' => 3],

    ['fase' => 32, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual comando testa conectividade enviando pacotes ICMP echo?', 'codigo' => null,
     'opcoes' => ['ping', 'grep', 'echo', 'cat'], 'resposta' => 0,
     'explicacao' => 'ping mede se o destino responde e em quanto tempo (latência).', 'dif' => 3],

    ['fase' => 32, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Latência alta numa rede significa:', 'codigo' => null,
     'opcoes' => ['Mais banda disponível', 'Maior demora para um pacote ir e voltar', 'Menos perda de pacotes', 'IP inválido'],
     'resposta' => 1,
     'explicacao' => 'Latência é o atraso de ida e volta (RTT); alta = resposta lenta.', 'dif' => 3],

    // ---- Fase 33: DDoS, o Enxame (chefe) ----
    ['fase' => 33, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Num ataque DDoS, o tráfego malicioso costuma vir de:', 'codigo' => null,
     'opcoes' => ['Uma única máquina', 'Muitas máquinas distribuídas (uma botnet)', 'O próprio servidor', 'O cabo de rede'],
     'resposta' => 1,
     'explicacao' => 'O "D" extra é de Distributed: milhares de fontes inundam o alvo ao mesmo tempo.', 'dif' => 4],

    ['fase' => 33, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual é a porta padrão do HTTP (sem o S)?', 'codigo' => null,
     'opcoes' => ['21', '80', '443', '8080'], 'resposta' => 1,
     'explicacao' => 'HTTP usa a porta 80; HTTPS usa a 443.', 'dif' => 3],

    ['fase' => 33, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'Qual é a porta padrão do SSH?', 'codigo' => null,
     'opcoes' => ['21', '22', '23', '25'], 'resposta' => 1,
     'explicacao' => 'SSH usa a porta 22 (21 é FTP, 23 é Telnet, 25 é SMTP).', 'dif' => 4],

    ['fase' => 33, 'tipo' => 'vf', 'assunto' => 'redes',
     'pergunta' => 'Um firewall pode bloquear tráfego com base em portas, IPs e protocolos.', 'codigo' => null,
     'opcoes' => null, 'resposta' => true,
     'explicacao' => 'O firewall aplica regras de filtragem para permitir ou barrar o tráfego.', 'dif' => 3],

    ['fase' => 33, 'tipo' => 'erro', 'assunto' => 'redes',
     'pergunta' => 'Qual destas NÃO é uma defesa contra DDoS?', 'codigo' => null,
     'opcoes' => ['Rate limiting (limitar requisições por IP)', 'Filtros e firewall na borda', 'Publicar a senha de admin do servidor', 'Usar uma CDN para absorver o tráfego'],
     'resposta' => 2,
     'explicacao' => 'Publicar credenciais é entregar o reino ao inimigo — nada a ver com mitigar DDoS.', 'dif' => 4],

    ['fase' => 33, 'tipo' => 'multipla', 'assunto' => 'redes',
     'pergunta' => 'A criptografia ponta-a-ponta garante que:', 'codigo' => null,
     'opcoes' => ['A rede fica mais rápida', 'Só o remetente e o destinatário conseguem ler o conteúdo', 'Os pacotes nunca se perdem', 'O IP fica oculto para sempre'],
     'resposta' => 1,
     'explicacao' => 'Apenas as pontas têm as chaves; intermediários veem só dados cifrados.', 'dif' => 4],
];
