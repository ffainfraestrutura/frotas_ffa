<?php
session_start();
if (!isset($_SESSION['matricula']) || empty($_SESSION['matricula'])) {
    header('Location: /frotas/index.php');
    exit;
}

$perfil = $_SESSION['perfil']; 
$nome = $_SESSION['nome'];
$matricula = $_SESSION['matricula'];
$ccusto = $_SESSION['ccusto']; 
$tipo_usuario_sessao = $_SESSION['tipo'];

$menuConfig = [
    // Perfil 0
    'memorando_p0' => [
        'title' => 'Memorando',
        'icon' => 'fas fa-file-alt',
        'profiles' => ['0'],
        'sub_items' => [['title' => 'Memorando', 'href' => './memorando.php']]
    ],
    'solicitacoes_p0' => [
        'title' => 'Solicitações',
        'icon' => 'fas fa-tasks',
        'profiles' => ['0'],
        'sub_items' => [
            ['title' => 'Pedir Cota Extra', 'href' => './tecnico.php'],
            ['title' => 'Tô Aqui!', 'href' => 'https://ffainfraestrutura.com.br/frotas/toaqui.php'],
            ['title' => 'Assinar Desconto por Uso Indevido', 'href' => './relatoriodescontousoindevidotec.php'],
        ]
    ],
    'vistorias_p0' => [
        'title' => 'Vistorias',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['0'],
        'sub_items' => [
            ['title' => 'Realizar Vistoria de Auditoria', 'href' => './checklist/checklistp1-condutor.php'],
            ['title' => 'Visualizar Relatórios A Assinar', 'href' => './checklist/condutorinicial.php'],
            ['title' => 'Visualizar Todos Relatórios', 'href' => './checklist/condutorinicialtodos.php'],
        ]
    ],
    'chamados_p0' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['0'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => './chamados/abrirchamado.php'],
            ['title' => 'Meus chamados', 'href' => './chamados/listagemchamadoscliente.php'],
        ]
    ],
    'documentos_veiculo_p0' => [
        'title' => 'Documentos do veículo',
        'icon' => 'fas fa-id-card',
        'profiles' => ['0'],
        'sub_items' => [
            ['title' => 'CRVL', 'href' => './gestao/docsveiculocrvl.php'],
            ['title' => 'CRV', 'href' => './gestao/docsveiculocrv.php'],
            ['title' => 'Cert IPVA', 'href' => './gestao/docsveiculoipva.php'],
        ]
    ],

    // Perfil 1
    'memorando_p1' => [
        'title' => 'Memorando',
        'icon' => 'fas fa-file-alt',
        'profiles' => ['1'],
        'sub_items' => [['title' => 'Memorando', 'href' => './memorando.php']]
    ],
    'solicitacoes_p1' => [
        'title' => 'Solicitações',
        'icon' => 'fas fa-tasks',
        'profiles' => ['1'],
        'sub_items' => [
            ['title' => 'Aprovar Cotas', 'href' => './aprovacaocota.php'],
            ['title' => 'Visão Força', 'href' => './visaoforca.php'],
            ['title' => 'Pedir Orçamento Complementar', 'href' => './supervisor.php'],
            ['title' => 'Pedir Cota Extra', 'href' => './supervisorcota.php'],
            ['title' => 'Transferir', 'href' => './transferirtec.php'],
            ['title' => 'Fazer Tô Aqui!', 'href' => 'https://ffainfraestrutura.com.br/frotas/toaquisup.php'],
            ['title' => 'Aceitar Tô Aqui!', 'href' => './aceitetoaqui.php'],
            ['title' => 'Calculo de Rota', 'href' => './calculorotasup.php'],
            ['title' => 'Remanejar Cotas', 'href' => './remanejamento-final.php'],
        ],
        'conditional_sub_items' => function ($user_ccusto_param) {
            $items = [];
            if ($user_ccusto_param == 'tim') {
                $items[] = ['title' => 'Atividade Complementar TIM', 'href' => './atividadecomplementartimsup.php'];
            } elseif ($user_ccusto_param == 'claro') {
                $items[] = ['title' => 'Atividade Complementar Claro', 'href' => './atividadecomplementarclarosup.php'];
            } elseif ($user_ccusto_param == 'ligga') {
                $items[] = ['title' => 'Atividade Complementar Ligga', 'href' => './atividadecomplementarliggasup.php'];
            }
            return $items;
        }
    ],
    'relatorios_p1' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['1'],
        'sub_items' => [
            ['title' => 'Relatório Deslocamento', 'href' => './detalhes-kmoscotaind-rel.php'],
            ['title' => 'Rel. Saldo Semanal', 'href' => './relsaldoatual-sup.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => './gpsxfolga-sup.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => './gpsxhora-sup.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => './detalhes-cota-extra-aprovadas-sup.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => './detalhes-cota-extra-indevidas.php'],
            ['title' => 'Endereços Casa Aniel', 'href' => './detalhes-endaniel.php'],
            ['title' => 'Relatório Cotas Extra Indevidas Aceitas', 'href' => './detalhes-cota-extra-descontadaac.php'],
            ['title' => 'Rel Tô Aqui!', 'href' => './reltoaqui.php'],
            ['title' => 'Rel Tô Aqui! Sup', 'href' => './reltoaquisup.php'],
            ['title' => 'Relatório de Log', 'href' => './rel-log.php'],
            ['title' => 'Relatório Memorando Aceitas', 'href' => './detalhes-memorando.php'],
        ]
    ],
    'chamados_p1' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['1'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => './chamados/abrirchamado.php'],
            ['title' => 'Meus chamados', 'href' => './chamados/listagemchamadoscliente.php'],
        ]
    ],
    'vistorias_p1' => [
        'title' => 'Vistorias',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['1'],
        'sub_items' => [
            ['title' => 'Realizar Vistoria de Auditoria', 'href' => './checklist/checklistp1-condutor.php'],
            ['title' => 'Visualizar Relatórios A Assinar', 'href' => './checklist/condutorinicial.php'],
            ['title' => 'Visualizar Todos Relatórios', 'href' => './checklist/condutorinicialtodos.php'],
        ]
    ],
    'documentos_veiculo_p1' => [
        'title' => 'Documentos do veículo',
        'icon' => 'fas fa-id-card',
        'profiles' => ['1'],
        'sub_items' => [
            ['title' => 'CRVL', 'href' => './gestao/docsveiculocrvl.php'],
            ['title' => 'CRV', 'href' => './gestao/docsveiculocrv.php'],
            ['title' => 'Cert IPVA', 'href' => './gestao/docsveiculoipva.php'],
        ]
    ],

    // Perfil 2
    'memorando_p2' => [
        'title' => 'Memorando',
        'icon' => 'fas fa-file-alt',
        'profiles' => ['2'],
        'sub_items' => [['title' => 'Memorando', 'href' => './memorando.php']]
    ],
    'solicitacoes_p2' => [
        'title' => 'Solicitações',
        'icon' => 'fas fa-tasks',
        'profiles' => ['2'],
        'sub_items' => [
            ['title' => 'Aprovar Orçamento Complementar', 'href' => './coordenador.php'],
            ['title' => 'Remanejar Orçamentos', 'href' => './remanejamentocoord.php'],
            ['title' => 'Aprovar Cota Supervisor', 'href' => './aprovacaocotacoord.php'],
            ['title' => 'Aprovar Cota Escalonada', 'href' => './aprovacaocotaesc.php'],
            ['title' => 'Pedir Cota Extra', 'href' => './coordenadorcota.php'],
            ['title' => 'Distribuir Orçamento', 'href' => './distorccoord.php'],
            ['title' => 'Pedir Orçamento', 'href' => './coordenadororc.php'],
            ['title' => 'Calculo de Rota', 'href' => './aceitacotarotasup.php'],
            ['title' => 'Avaliação de Desconto por Uso Indevido', 'href' => './relatoriodescontousoindevido.php'],
        ]
    ],
    'relatorios_p2' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['2'],
        'sub_items' => [
            ['title' => 'Relatório Deslocamento', 'href' => './detalhes-kmoscotaind-rel.php'],
            ['title' => 'Rel. Rel. Saldo Semanal', 'href' => './relsaldoatual.php'],
            ['title' => 'Rel. Saldo Diário', 'href' => './relsaldodiario.php'],
            ['title' => 'Rel. Rel. Saldo do Sup por Período', 'href' => './detalhes-saldo-supxcoord.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => './gpsxfolga.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => './gpsxhora.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => './detalhes-cota-extra-aprovadas.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => './detalhes-cota-extra-indevidas.php'],
            ['title' => 'Endereços Casa Aniel', 'href' => './detalhes-endaniel.php'],
            ['title' => 'Relatório Cotas Extra Indevidas Aceitas', 'href' => './detalhes-cota-extra-descontadaac.php'],
            ['title' => 'Rel Tô Aqui!', 'href' => './reltoaqui.php'],
            ['title' => 'Rel Tô Aqui! Sup', 'href' => './reltoaquisup.php'],
            ['title' => 'Relatório de Log', 'href' => './rel-log.php'],
            ['title' => 'Relatório Memorando Aceitas', 'href' => './detalhes-memorando.php'],
        ]
    ],
    'importar_p2' => [
        'title' => 'Importar',
        'icon' => 'fas fa-file-import',
        'profiles' => ['2'],
        'sub_items' => [['title' => 'Atividades Complementar em Massa', 'href' => './importoperadora.php']]
    ],
    'chamados_p2' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['2'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => './chamados/abrirchamado.php'],
            ['title' => 'Meus chamados', 'href' => './chamados/listagemchamadoscliente.php'],
        ]
    ],
    'vistorias_p2' => [
        'title' => 'Vistorias',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['2'],
        'sub_items' => [
            ['title' => 'Realizar Vistoria de Auditoria', 'href' => './checklist/checklistp1-condutor.php'],
            ['title' => 'Visualizar Relatórios A Assinar', 'href' => './checklist/condutorinicial.php'],
            ['title' => 'Visualizar Todos Relatórios', 'href' => './checklist/condutorinicialtodos.php'],
        ]
    ],
    'documentos_veiculo_p2' => [
        'title' => 'Documentos do veículo',
        'icon' => 'fas fa-id-card',
        'profiles' => ['2'],
        'sub_items' => [
            ['title' => 'CRVL', 'href' => './gestao/docsveiculocrvl.php'],
            ['title' => 'CRV', 'href' => './gestao/docsveiculocrv.php'],
            ['title' => 'Cert IPVA', 'href' => './gestao/docsveiculoipva.php'],
        ]
    ],

    // Perfil 3
    'painel_p3' => [
        'title' => 'Painel',
        'icon' => 'fas fa-tachometer-alt',
        'profiles' => ['3'],
        'sub_items' => [] // Exemplo de item sem subitens
    ],
    'solicitacoes_p3' => [
        'title' => 'Solicitações',
        'icon' => 'fas fa-tasks',
        'profiles' => ['3'],
        'sub_items' => [
            ['title' => 'Remanejamento', 'href' => './remanejamentogerente.php'],
            ['title' => 'Aprovar Cota Escalada', 'href' => './aprovacaocotaescger.php'],
        ]
    ],
    'relatorios_p3' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['3'],
        'sub_items' => [
            ['title' => 'Relatório Deslocamento', 'href' => './detalhes-kmoscotaind-rel.php'],
            ['title' => 'Rel. Saldo Semanal', 'href' => './relsaldoatual.php'],
            ['title' => 'Saldo Diário', 'href' => './relsaldodiario.php'],
            ['title' => 'Rel. Saldo do Sup por Período', 'href' => './detalhes-saldo-supxcoord.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => './gpsxfolga.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => './gpsxhora.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => './detalhes-cota-extra-aprovadas.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => './detalhes-cota-extra-indevidas.php'],
            ['title' => 'Endereços Casa Aniel', 'href' => './detalhes-endaniel.php'],
            ['title' => 'Relatório Cotas Extra Indevidas Aceitas', 'href' => './detalhes-cota-extra-descontadaac.php'],
            ['title' => 'Rel Tô Aqui!', 'href' => './reltoaqui.php'],
            ['title' => 'Rel Tô Aqui! Sup', 'href' => './reltoaquisup.php'],
            ['title' => 'Relatório de Log', 'href' => './rel-log.php'],
            ['title' => 'Relatório Memorando Aceitas', 'href' => './detalhes-memorando.php'],
        ]
    ],
    'importar_p3' => [
        'title' => 'Importar',
        'icon' => 'fas fa-file-import',
        'profiles' => ['3'],
        'sub_items' => [['title' => 'Atividades Complementar em Massa', 'href' => './importoperadora.php']]
    ],
    'chamados_p3' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['3'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => './chamados/abrirchamado.php'],
            ['title' => 'Meus Chamados', 'href' => './chamados/listagemchamadoscliente.php'],
        ]
    ],
    'vistorias_p3' => [
        'title' => 'Vistorias',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['3'],
        'sub_items' => [
            ['title' => 'Realizar Vistoria de Auditoria', 'href' => '../checklist/checklistp1-condutor.php'],
            ['title' => 'Visualizar Relatórios A Assinar', 'href' => './checklist/condutorinicial.php'],
            ['title' => 'Visualizar Todos Relatórios', 'href' => './checklist/condutorinicialtodos.php'],
        ]
    ],
    'documentos_veiculo_p3' => [
        'title' => 'Documentos do veículo',
        'icon' => 'fas fa-id-card',
        'profiles' => ['3'],
        'sub_items' => [
            ['title' => 'CRVL', 'href' => './gestao/docsveiculocrvl.php'],
            ['title' => 'CRV', 'href' => './gestao/docsveiculocrv.php'],
            ['title' => 'Cert IPVA', 'href' => './gestao/docsveiculoipva.php'],
        ]
    ],

    // Perfil 4
    'sist_frotas_p4' => [
        'title' => 'Sist Frotas',
        'icon' => 'fas fa-car',
        'profiles' => ['4'],
        'sub_items' => [['title' => 'Sist Frotas', 'href' => 'http://ffasip.ddns.net:4242/frotas/telainicio.php']]
    ],
    'painel_p4' => [
        'title' => 'Painel',
        'icon' => 'fas fa-tachometer-alt',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Aprovar Cota Coordenador', 'href' => 'http://ffasip.ddns.net:4242/frotas/aprovacaocotafrotas.php'],
            ['title' => 'Aprovar Orçamento Coordenador', 'href' => 'http://ffasip.ddns.net:4242/frotas/frotaorccoord.php'],
            ['title' => 'Orçamento Coordenadores', 'href' => 'http://ffasip.ddns.net:4242/frotas/orcamento.php'],
            ['title' => 'Visão Força SUP', 'href' => 'http://ffasip.ddns.net:4242/frotas/visaoforcafrotas.php'],
            ['title' => 'Aprovar Cota Técnico', 'href' => 'http://ffasip.ddns.net:4242/frotas/aprovacaocotatecfrota.php'],
        ]
    ],
    'relatorios_p4' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Relatório Deslocamento por OS', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-kmoscotaind-rel.php'],
            ['title' => 'Relatório Saldo Veiculos', 'href' => 'http://ffasip.ddns.net:4242/frotas/relsaldoveiculos.php'],
            ['title' => 'Rel. Orc. Semanal Sup.', 'href' => 'http://ffasip.ddns.net:4242/frotas/relorcsemanalsup.php'],
            ['title' => 'Rel. Saldo Semanal', 'href' => 'http://ffasip.ddns.net:4242/frotas/relsaldoatual.php'],
            ['title' => 'Rel. Saldo do Sup por Período', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-saldo-supxcoord.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => 'http://ffasip.ddns.net:4242/frotas/gpsxfolga.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => 'http://ffasip.ddns.net:4242/frotas/gpsxhora.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-cota-extra-aprovadas.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-cota-extra-indevidas.php'],
            ['title' => 'Endereços Casa Aniel', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-endaniel.php'],
            ['title' => 'Relatório Cotas Extra Indevidas Aceitas', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-cota-extra-descontadaac.php'],
            ['title' => 'Rel Tô Aqui!', 'href' => 'http://ffasip.ddns.net:4242/frotas/reltoaqui.php'],
            ['title' => 'Rel Tô Aqui! Sup', 'href' => 'http://ffasip.ddns.net:4242/frotas/reltoaquisup.php'],
            ['title' => 'Relatório de Log', 'href' => 'http://ffasip.ddns.net:4242/frotas/rel-log.php'],
            ['title' => 'Relatório Memorando Aceitas', 'href' => 'http://ffasip.ddns.net:4242/frotas/detalhes-memorando.php'],
            ['title' => 'Relatório Desconto por Uso Indevido', 'href' => 'http://ffasip.ddns.net:4242/frotas/relatoriodescontousoindevidofrota.php', 'target' => '_blank'],
        ]
    ],
    'importar_p4' => [
        'title' => 'Importar',
        'icon' => 'fas fa-file-import',
        'profiles' => ['4'],
        'sub_items' => [['title' => 'Atividades Complementar em Massa', 'href' => 'http://ffasip.ddns.net:4242/frotas/importoperadora.php']]
    ],
    'chamados_p4' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => 'http://ffasip.ddns.net:4242/frotas/chamados/abrirchamado.php'],
            ['title' => 'Meus Chamados', 'href' => 'http://ffasip.ddns.net:4242/frotas/chamados/listagemchamadoscliente.php'],
            ['title' => 'Chamados', 'href' => 'http://ffasip.ddns.net:4242/frotas/chamados/listagemchamados.php'],
            ['title' => 'Relatório de Chamados', 'href' => 'http://ffasip.ddns.net:4242/frotas/chamados/relatoriochamados.php'],
        ]
    ],
    'vistoria_p4' => [
        'title' => 'Vistoria',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Iniciar vistoria', 'href' => 'http://ffasip.ddns.net:4242/frotas/checklist/checklistinicio.php'],
            ['title' => 'Iniciar vistoria frota', 'href' => 'http://ffasip.ddns.net:4242/frotas/checklist/checklistmc.php'],
            ['title' => 'Iniciar vistoria manual', 'href' => 'http://ffasip.ddns.net:4242/frotas/checklist/checklistinicio-manual.php'],
            ['title' => 'Relatório de Vistoria por Placa', 'href' => 'https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php', 'target' => '_blank'],
            ['title' => 'Relatório de Vistoria por Período', 'href' => 'https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php', 'target' => '_blank'],
            ['title' => 'Relatórios Pendentes de Assinatura', 'href' => 'http://ffasip.ddns.net:4242/frotas/checklist/vistoriadorpendentes.php'],
            ['title' => 'Relatórios Condutores Pendentes de Assinatura', 'href' => 'http://ffasip.ddns.net:4242/frotas/checklist/assinaturaspendentes.php'],
        ]
    ],
    'multas_p4' => [ 
        'title' => 'Multas',
        'icon' => 'fas fa-file-invoice-dollar',
        'profiles' => ['4'],
        'sub_items_logic' => function ($current_perfil_param, $user_matricula_param) { 
            return [
                ['title' => 'Listar Multas', 'href' => 'http://ffasip.ddns.net:4242/multas/multasfrota.php'],
                ['title' => 'Acompanhar Assinaturas', 'href' => 'http://ffasip.ddns.net:4242/frotas/multas/multasfrota.php'],
                ['title' => 'Cadastrar Multa', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/cadastromulta.php'],
                ['title' => 'Importar Multas Novas', 'href' => "https://ffa.painel-telecom.com:4545/frotas/gestao/importmultasnovas.php?mat={$user_matricula_param}&perfil={$current_perfil_param}", 'target' => '_blank'],
                ['title' => 'Códigos de Multa', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemcodmulta.php'],
                ['title' => 'Relatório Multa por Etapa', 'href' => 'http://ffasip.ddns.net:4242/multas/relatoriomultasetapa.php'],
            ];
        }
    ],
    'gestao_p4' => [ 
        'title' => 'Gestão',
        'icon' => 'fas fa-cogs',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Fornecedores', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemfornecedores.php'],
            ['title' => 'Filiais', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemfiliais.php'],
            ['title' => 'Condutores Ativos', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemcondutores.php'],
            ['title' => 'Listagem Geral de Condutores', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemgeralcondutores.php'],
            ['title' => 'Veículos', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemveiculos.php'],
            ['title' => 'Manutenções em Aberto', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemmanutencaoaberta.php'],
            ['title' => 'Manutenções Concluídas', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemmanutencaoconc.php'],
            ['title' => 'Todas Manutenções', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/listagemmanutencao.php'],
            ['title' => 'Farol Manutenção', 'href' => 'http://ffasip.ddns.net:4242/frotas/gestao/farolmanutencaopreventiva.php'],
        ]
    ],
    'config_comb' => [
        'title' => 'Config. Combustível',
        'icom' => 'fa-solid fa-gas-pump',
        'profiles' => ['4'],
        'sub_items' => [
            ['title' => 'Controle de Combustivel', 'href' => 'http://ffasip.ddns.net:4242/frotas/config_comb/controle_combustivel/index.php'],
            ['title' => 'Limite de Cota Indevida e Repasse', 'href' => 'http://ffasip.ddns.net:4242/frotas/config_comb/limite_cota_indevida/index.php'],
            ['title' => 'Controle de Cota Fixa', 'href' => 'http://ffasip.ddns.net:4242/frotas/config_comb/cota_fixa/index.php'],
        ]
    ],

    // Perfil 5
    'sist_frotas_p5' => [
        'title' => 'Sist Frotas',
        'icon' => 'fas fa-car',
        'profiles' => ['5'],
        'sub_items' => [['title' => 'Sist Frotas', 'href' => './telainicio.php']]
    ],
    'relatorios_p5' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['5'],
        'sub_items' => [
            ['title' => 'Relatório Deslocamento', 'href' => './detalhes-kmoscotaind-rel.php'],
            ['title' => 'Rel. Saldo Semanal', 'href' => './relsaldoatual.php'],
            ['title' => 'Saldo Diário', 'href' => './relsaldodiario.php'],
            ['title' => 'Rel. Saldo do Sup por Período', 'href' => './detalhes-saldo-supxcoord.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => './gpsxfolga.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => './gpsxhora.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => './detalhes-cota-extra-aprovadas.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => './detalhes-cota-extra-indevidas.php'],
            ['title' => 'Endereços Casa Aniel', 'href' => './detalhes-endaniel.php'],
            ['title' => 'Relatório Cotas Extra Indevidas Aceitas', 'href' => './detalhes-cota-extra-descontadaac.php'],
            ['title' => 'Rel Tô Aqui!', 'href' => './reltoaqui.php'],
            ['title' => 'Rel Tô Aqui! Sup', 'href' => './reltoaquisup.php'],
            ['title' => 'Relatório de Log', 'href' => './rel-log.php'],
            ['title' => 'Relatório Memorando Aceitas', 'href' => './detalhes-memorando.php'],
        ]
    ],
    'importar_p5' => [
        'title' => 'Importar',
        'icon' => 'fas fa-file-import',
        'profiles' => ['5'],
        'sub_items' => [['title' => 'Atividades Complementar em Massa', 'href' => './importoperadora.php']]
    ],
    'chamados_p5' => [
        'title' => 'Chamados',
        'icon' => 'fas fa-headset',
        'profiles' => ['5'],
        'sub_items' => [
            ['title' => 'Abrir Chamado', 'href' => './chamados/abrirchamado.php'],
            ['title' => 'Meus Chamados', 'href' => './chamados/listagemchamadoscliente.php'],
            ['title' => 'Relatório de Chamados', 'href' => './chamados/relatoriochamados.php'],
        ]
    ],
    'vistoria_p5' => [
        'title' => 'Vistoria',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['5'],
        'sub_items' => [
            ['title' => 'Relatório de Vistoria por Placa', 'href' => 'https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php', 'target' => '_blank'],
            ['title' => 'Relatório de Vistoria por Período', 'href' => 'https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php', 'target' => '_blank'],
        ]
    ],
    'multas_p5' => [ 
        'title' => 'Multas',
        'icon' => 'fas fa-file-invoice-dollar',
        'profiles' => ['5'],
        'sub_items_logic' => function ($current_perfil_func, $user_matricula_func, $user_tipo_sessao_func) {
            if ($user_tipo_sessao_func == 5) {
                return [
                    ['title' => 'Multas a pagar', 'href' => '../../multas/multasfinanceiro.php'],
                    ['title' => 'Multas Controladoria', 'href' => '../../multas/multascontroladoria.php'],
                ];
            }
            return [];
        }
    ],
    'gestao_p5' => [
        'title' => 'Gestão',
        'icon' => 'fas fa-cogs',
        'profiles' => ['5'],
        'sub_items' => [
            ['title' => 'Fornecedores', 'href' => './gestao/listagemfornecedores.php'],
            ['title' => 'Filiais', 'href' => './gestao/listagemfiliais.php'],
            ['title' => 'Condutores Ativos', 'href' => './gestao/listagemcondutores.php'],
            ['title' => 'Listagem Geral de Condutores', 'href' => './gestao/listagemgeralcondutores.php'],
            ['title' => 'Veículos', 'href' => './gestao/listagemveiculos.php'],
            ['title' => 'Inventário de Veículos', 'href' => './gestao/relinventario.php'],
            ['title' => 'Manutenções em Aberto', 'href' => './gestao/listagemmanutencaoaberta.php'],
            ['title' => 'Manutenções Concluídas', 'href' => './gestao/listagemmanutencaoconc.php'],
            ['title' => 'Todas Manutenções', 'href' => './gestao/listagemmanutencao.php'],
        ]
    ],

    // Perfil 6
    'rotas_p6' => [
        'title' => 'Rotas',
        'icon' => 'fas fa-route',
        'profiles' => ['6'],
        'sub_items' => [['title' => 'Atividade Complementar', 'href' => './atividadecomplementar.php']]
    ],
    'importar_p6' => [
        'title' => 'Importar',
        'icon' => 'fas fa-file-import',
        'profiles' => ['6'],
        'sub_items' => [['title' => 'Atividades Complementar em Massa', 'href' => './importoperadora.php']]
    ],

    // Perfil 7
    'vistorias_p7' => [
        'title' => 'Vistorias',
        'icon' => 'fas fa-clipboard-check',
        'profiles' => ['7'],
        'sub_items' => [
            ['title' => 'Realizar Vistoria de Auditoria', 'href' => './checklist/checklistp1-condutor.php'],
            ['title' => 'Visualizar Relatórios A Assinar', 'href' => './checklist/condutorinicial.php'],
            ['title' => 'Visualizar Todos Relatórios', 'href' => './checklist/condutorinicialtodos.php'],
        ]
    ],

    // Perfil 11
    'relatorios_p11' => [
        'title' => 'Relatórios',
        'icon' => 'fas fa-chart-line',
        'profiles' => ['11'],
        'sub_items' => [
            ['title' => 'KMs', 'href' => './detalhes-kmsem.php'],
            ['title' => 'Rel. Saldo Semanal', 'href' => './relsaldoatual.php'],
            ['title' => 'Rel. Saldo do Sup por Período', 'href' => './detalhes-saldo-supxcoord.php'],
            ['title' => 'Rel. Deslocamento Fora de Escala', 'href' => './gpsxfolga.php'],
            ['title' => 'Rel. Deslocamento Fora do Horário', 'href' => './gpsxhora.php'],
            ['title' => 'Rel. Cotas Extras Aprovadas', 'href' => './detalhes-cota-extra-aprovadas.php'],
            ['title' => 'Rel. Cotas Extras Indevidas', 'href' => './detalhes-cota-extra-indevidas.php'],
        ]
    ],
    'gestao_p11' => [
        'title' => 'Gestão',
        'icon' => 'fas fa-cogs',
        'profiles' => ['11'],
        'sub_items' => [
            ['title' => 'Fornecedores', 'href' => './gestao/listagemfornecedores.php'],
            ['title' => 'Filiais', 'href' => './gestao/listagemfiliais.php'],
            ['title' => 'Condutores Ativos', 'href' => './gestao/listagemcondutores.php'],
            ['title' => 'Listagem Geral de Condutores', 'href' => './listagemgeralcondutores.php'],
            ['title' => 'Veículos', 'href' => './gestao/listagemveiculos.php'],
            ['title' => 'Manutenções em Aberto', 'href' => './listagemmanutencaoaberta.php'],
            ['title' => 'Manutenções Concluídas', 'href' => './gestao/listagemmanutencaoconc.php'],
            ['title' => 'Todas Manutenções', 'href' => './gestao/listagemmanutencao.php'],
            ['title' => 'Farol Manutenção', 'href' => './gestao/farolmanutencaopreventiva.php'],
        ]
    ],
];

$LARGURA_SIDEBAR = "280px"; // Defina a largura da sua sidebar aqui
$ALTURA_TOPBAR = "56px"; // Altura do seu topbar
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FFA Frotas - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden; /* Previne scroll horizontal durante a transição */
            background-color: #f8f9fa; /* bg-light */
            margin-top: 75px;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030; /* Acima da sidebar */
            height: <?php echo $ALTURA_TOPBAR; ?>;
        }

        .sidebar {
            position: fixed;
            top: <?php echo $ALTURA_TOPBAR; ?>; /* Abaixo do topbar */
            left: 0;
            width: <?php echo $LARGURA_SIDEBAR; ?>;
            height: calc(100vh - <?php echo $ALTURA_TOPBAR; ?>); /* Altura total menos topbar */
            z-index: 1020; /* Abaixo do topbar, mas acima do conteúdo */
            background-color: #212529; /* bg-dark */
            color: white;
            transition: margin-left 0.3s ease-in-out;
            margin-left: 0; /* Começa visível */
        }

        .sidebar-collapsed .sidebar {
            margin-left: -<?php echo $LARGURA_SIDEBAR; ?>; /* Esconde a sidebar para a esquerda */
        }

        .main-content {
            margin-top: <?php echo $ALTURA_TOPBAR; ?>; /* Espaço para o topbar fixo */
            margin-left: <?php echo $LARGURA_SIDEBAR; ?>; /* Espaço para a sidebar visível */
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
            min-height: calc(100vh - <?php echo $ALTURA_TOPBAR; ?>); /* Garante que o conteúdo possa preencher a tela */
        }

        .sidebar-collapsed .main-content {
            margin-left: 0; /* Conteúdo ocupa toda a largura quando sidebar está escondida */
        }

        /* Estilos para a scrollbar da sidebar (opcional, mas melhora a aparência) */
        .sidebar .overflow-auto::-webkit-scrollbar {
            width: 8px;
        }
        .sidebar .overflow-auto::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        .sidebar .overflow-auto::-webkit-scrollbar-track {
            background-color: rgba(0, 0, 0, 0.1);
        }

        /* Ajustes para o dropdown de usuário na sidebar */
        .sidebar .dropdown-menu {
            position: static !important; /* Para o dropdown funcionar dentro do flex container */
            width: 100%;
        }
        .sidebar hr {
            border-top: 1px solid rgba(255,255,255,.15);
        }

        
        
    </style>    
</head>

<div class="<?php echo 'sidebar-collapsed';?>" id="body">

    <!-- Barra de Navegação Superior (Topbar) -->
    <nav class="navbar navbar-expand navbar-dark bg-dark topbar">
        <div class="container-fluid">
            <!-- Botão para mostrar/esconder sidebar -->
            <button class="btn btn-dark me-3" id="sidebarToggle" type="button">
                <i class="fas fa-bars"></i><img src="/frotas/src/images/logoredonda.png" alt="Logo FFA" width="40" height="40" class="ms-3">
            </button>
            <!-- Espaçador para empurrar o dropdown de usuário para a direita -->
            <div class="ms-auto"></div>

            <!-- Dropdown do Usuário no Topbar -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarUserDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-1"></i>
                        <?php echo htmlspecialchars($nome); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarUserDropdown">
                        <li><a class="dropdown-item" href="./altsenha.php"><i class="fas fa-key me-2"></i>Alterar senha</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/frotas/index.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3 block layoutSidenav" onclick="puxadinha()">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <!-- <img src="/frotas/src/images/logoredonda.png" alt="Logo FFA" width="40" height="40" class="me-2"> -->
            <span class="fs-4">FFA Frotas</span>
        </a>
        <hr>
        <div class="flex-grow-1 overflow-auto pe-2 sb-sidenav-menu">
            <ul class="nav nav-pills flex-column mb-auto sb-sidenav-menu accordion" id="sidenavAccordion">
                <?php
                if (empty($perfil) && $perfil !== '0') { // Checa se perfil é vazio ou null, mas permite '0'
                    echo "<li class='nav-item'><span class='nav-link text-danger'>Perfil não definido.</span></li>";
                } else {
                    $idCounter = 0;
                    $foundItemsForProfile = false;
                    foreach ($menuConfig as $menuKey => $menuGroup) {
                        if (isset($menuGroup['profiles']) && in_array($perfil, $menuGroup['profiles'])) {
                            $foundItemsForProfile = true;
                            $collapseId = "collapse_" . preg_replace("/[^a-zA-Z0-9_]/", "", $menuKey) . "_" . $idCounter++;

                            $subItemsToRender = [];
                            if (isset($menuGroup['sub_items']) && is_array($menuGroup['sub_items'])) {
                                $subItemsToRender = array_merge($subItemsToRender, $menuGroup['sub_items']);
                            }
                            if (isset($menuGroup['conditional_sub_items']) && is_callable($menuGroup['conditional_sub_items'])) {
                                $subItemsToRender = array_merge($subItemsToRender, $menuGroup['conditional_sub_items']($ccusto));
                            }
                            if (isset($menuGroup['sub_items_logic']) && is_callable($menuGroup['sub_items_logic'])) {
                                // Adapte as chamadas conforme a necessidade dos parâmetros das suas funções de lógica
                                try {
                                    $reflection = new ReflectionFunction($menuGroup['sub_items_logic']);
                                    $numParams = $reflection->getNumberOfParameters();
                                    if ($numParams == 0) {
                                        $subItemsToRender = array_merge($subItemsToRender, $menuGroup['sub_items_logic']());
                                    } elseif ($numParams == 1) { // Ex: espera $perfil
                                        $subItemsToRender = array_merge($subItemsToRender, $menuGroup['sub_items_logic']($perfil));
                                    } elseif ($numParams == 2) { // Ex: espera $perfil, $matricula
                                        $subItemsToRender = array_merge($subItemsToRender, $menuGroup['sub_items_logic']($perfil, $matricula));
                                    } elseif ($numParams == 3) { // Ex: espera $perfil, $matricula, $tipo_usuario_sessao
                                        $subItemsToRender = array_merge($subItemsToRender, $menuGroup['sub_items_logic']($perfil, $matricula, $tipo_usuario_sessao));
                                    }
                                } catch (ReflectionException $e) {
                                    echo "<li class='nav-item'><span class='nav-link text-warning small'>Erro na lógica do item: " . htmlspecialchars($menuGroup['title']) . "</span></li>";
                                }
                            }
                            ?>
                            <li class="nav-item mb-1">
                                <a href="#" class="nav-link text-white collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo htmlspecialchars($collapseId); ?>" aria-expanded="false">
                                    <i class="<?php echo htmlspecialchars($menuGroup['icon'] ?? 'fas fa-circle'); ?> me-2"></i>
                                    <?php echo htmlspecialchars($menuGroup['title'] ?? 'Menu Item'); ?>
                                    <i class="fas fa-angle-down float-end ms-auto arrow-icon"></i>
                                </a>
                                <div class="collapse ps-3" id="<?php echo htmlspecialchars($collapseId); ?>" data-bs-parent="#sidenavAccordion">
                                    <ul class="nav flex-column">
                                        <?php if (empty($subItemsToRender)): ?>
                                            <!-- Não mostrar "Vazio" se não houver subitens, a menos que explicitamente desejado -->
                                            <!-- <li class="nav-item"><span class="nav-link text-muted small disabled">Vazio</span></li> -->
                                        <?php else: ?>
                                            <?php foreach ($subItemsToRender as $sub): ?>
                                                <?php if (is_array($sub) && isset($sub['href']) && isset($sub['title'])): ?>
                                                    <li class="nav-item">
                                                        <a href="<?php echo htmlspecialchars($sub['href']); ?>"
                                                            class="nav-link text-white small py-1 <?php echo (isset($sub['target']) && $sub['target'] === '_blank' ? '' : 'internal-link'); ?>"
                                                            <?php echo (isset($sub['target']) ? 'target="' . htmlspecialchars($sub['target']) . '"' : ''); ?>>
                                                            <?php echo htmlspecialchars($sub['title']); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    if (!$foundItemsForProfile) {
                         echo "<li class='nav-item'><span class='nav-link text-warning'>Nenhum item de menu disponível para este perfil.</span></li>";
                    }
                }
                ?>
            </ul>
        </div>
        <hr>
        <!-- Dropdown do usuário na sidebar (movido do seu código original) -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUserSidebar"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg me-2"></i>
                <strong><?php echo htmlspecialchars($nome); ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUserSidebar">
                <li><a class="dropdown-item" href="./altsenha.php">Alterar senha</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="./index.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const bodyElement = document.getElementById('body');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    bodyElement.classList.toggle('sidebar-collapsed');
                });
            }

            document.querySelectorAll('#sidenavAccordion .collapse a.nav-link.internal-link').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    // Não previne o default, apenas fecha o collapse
                    let collapseElement = link.closest('.collapse');
                    if (collapseElement && collapseElement.classList.contains('show')) {
                        // Pequeno delay para garantir que a navegação ocorra antes do hide
                        setTimeout(() => {
                            let bsCollapse = bootstrap.Collapse.getInstance(collapseElement);
                            if (bsCollapse) {
                                bsCollapse.hide();
                            } 
                        }, 50);
                    }
                });
            });

            // Script para mudar a seta do ícone de colapso (adaptado)
            let collapseButtons = document.querySelectorAll('#sidenavAccordion > .nav-item > a[data-bs-toggle="collapse"]');
            collapseButtons.forEach(function (button) {
                let targetCollapseId = button.getAttribute('data-bs-target');
                if (targetCollapseId) {
                    let targetCollapse = document.querySelector(targetCollapseId);
                    let arrowIcon = button.querySelector('.arrow-icon'); // Usando a classe .arrow-icon

                    if (targetCollapse && arrowIcon) {
                        // Função para atualizar a seta
                        const updateArrow = () => {
                            if (targetCollapse.classList.contains('show')) {
                                arrowIcon.classList.remove('fa-angle-down');
                                arrowIcon.classList.add('fa-angle-right'); // Ou fa-angle-up
                            } else {
                                arrowIcon.classList.remove('fa-angle-right'); // Ou fa-angle-up
                                arrowIcon.classList.add('fa-angle-down');
                            }
                        };

                        targetCollapse.addEventListener('show.bs.collapse', updateArrow);
                        targetCollapse.addEventListener('hide.bs.collapse', updateArrow);
                        
                        // Estado inicial da seta
                        updateArrow();
                    }
                }
            });
        });
    </script>
</div>
</html>