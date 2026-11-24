<?php
date_default_timezone_set('America/Sao_Paulo');

// Data de referência: segunda-feira atual ou próxima segunda-feira.
$hoje = new DateTimeImmutable('today');
$dataReferencia = $hoje->format('N') == 1
    ? $hoje
    : $hoje->modify('next monday');

$dataReferencia = $dataReferencia->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestão de Saldo de Combustível</title>

    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #15803d;
            --danger: #dc2626;
            --warning: #d97706;
            --background: #f4f6f9;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #1f2937;
            --muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: var(--background);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            max-width: 1600px;
            margin: auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 25px;
        }

        .page-header p {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 18px;
        }

        .filters {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 210px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
        }

        input,
        select {
            height: 40px;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: white;
            color: var(--text);
            font-size: 14px;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--primary);
        }

        button {
            border: none;
            border-radius: 6px;
            padding: 11px 17px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--primary-hover);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover:not(:disabled) {
            background: #166534;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover:not(:disabled) {
            background: #d1d5db;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-outline {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .metric {
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
        }

        .metric-label {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .metric-value {
            font-size: 25px;
            font-weight: 700;
        }

        .metric-description {
            margin-top: 7px;
            font-size: 12px;
            color: var(--muted);
        }

        .text-success {
            color: var(--success);
        }

        .text-danger {
            color: var(--danger);
        }

        .text-warning {
            color: var(--warning);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 18px;
        }

        .table-header h2 {
            font-size: 17px;
            margin: 0;
        }

        .table-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
            font-size: 13px;
        }

        th {
            background: #f9fafb;
            color: #4b5563;
            font-weight: 700;
            text-align: left;
            padding: 13px 12px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 13px 12px;
            border-bottom: 1px solid var(--border);
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            border-radius: 20px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-secondary {
            background: #f3f4f6;
            color: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 45px 20px;
            color: var(--muted);
        }

        .empty-state strong {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--text);
        }

        .alert {
            padding: 14px 17px;
            border-radius: 7px;
            margin-bottom: 18px;
            font-size: 13px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .loading {
            display: none;
            margin-left: 5px;
            animation: pulse 1s infinite;
        }

        .loading.active {
            display: inline;
        }

        @keyframes pulse {
            50% {
                opacity: .3;
            }
        }

        .section-description {
            color: var(--muted);
            font-size: 13px;
            margin: -10px 0 18px;
        }

        .error-message {
            max-width: 400px;
            white-space: normal;
            line-height: 1.5;
        }

        .footer-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            margin-top: 15px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 10px;
            padding: 25px;
            width: 100%;
            max-width: 480px;
        }

        .modal h3 {
            margin: 0 0 12px;
        }

        .modal p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
        }

        @media (max-width: 900px) {
            .metrics {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .metrics {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .metric {
                padding: 14px;
            }

            .metric-value {
                font-size: 20px;
            }

            .filters,
            .form-group {
                width: 100%;
            }

            .filters button {
                width: 100%;
            }

            .table-actions {
                width: 100%;
            }

            .table-actions button {
                flex: 1;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Cabeçalho -->
        <header class="page-header">
            <div>
                <h1>Gestão de Saldo de Combustível</h1>
                <p>Controle de cálculo e inserção dos saldos semanais dos colaboradores.</p>
            </div>
        </header>

        <!-- Mensagens -->
        <div id="alertMessage" class="alert"></div>

        <!-- Filtros e ações -->
        <section class="card">

            <h2 class="card-title">Parâmetros da operação</h2>

            <div class="filters">

                <div class="form-group">
                    <label for="dataReferencia">Data de referência</label>

                    <input
                        type="date"
                        id="dataReferencia"
                        value="<?= htmlspecialchars($dataReferencia, ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <button
                    type="button"
                    class="btn-primary"
                    id="btnCalcular"
                    onclick="calcularSaldos()">
                    <span>⟳</span>
                    Calcular / Recalcular saldos
                </button>

                <button
                    type="button"
                    class="btn-success"
                    id="btnInserir"
                    onclick="abrirConfirmacaoInsercao()"
                    disabled>
                    <span>↗</span>
                    Inserir saldos nas APIs
                </button>

                <button
                    type="button"
                    class="btn-outline"
                    id="btnAtualizar"
                    onclick="carregarSaldos()">
                    <span>⟳</span>
                    Atualizar tabela
                </button>

            </div>

            <p class="section-description" style="margin-top: 15px; margin-bottom: 0;">
                O cálculo não realiza alterações nas APIs externas. A inserção somente
                acontece após a confirmação do administrador.
            </p>

        </section>

        <!-- Indicadores -->
        <section class="metrics">

            <div class="metric">
                <div class="metric-label">Colaboradores calculados</div>
                <div class="metric-value" id="totalColaboradores">0</div>
                <div class="metric-description">Registros na semana selecionada</div>
            </div>

            <div class="metric">
                <div class="metric-label">Saldo total calculado</div>
                <div class="metric-value" id="saldoTotal">R$ 0,00</div>
                <div class="metric-description">Valores projetados para a semana</div>
            </div>

            <div class="metric">
                <div class="metric-label">Inserções com sucesso</div>
                <div class="metric-value text-success" id="totalSucesso">0</div>
                <div class="metric-description">Confirmações das integrações</div>
            </div>

            <div class="metric">
                <div class="metric-label">Inserções com erro</div>
                <div class="metric-value text-danger" id="totalErros">0</div>
                <div class="metric-description">Falhas registradas nas integrações</div>
            </div>

        </section>

        <!-- Tabela de saldos -->
        <section class="card">

            <div class="table-header">

                <h2>Saldos calculados</h2>

                <div class="table-actions">

                    <button
                        type="button"
                        class="btn-outline"
                        onclick="limparFiltros()">
                        Limpar filtros
                    </button>

                    <button
                        type="button"
                        class="btn-outline"
                        onclick="carregarSaldos()">
                        Atualizar
                    </button>

                </div>

            </div>

            <!-- Filtros da tabela -->
            <div class="filters" style="margin-bottom: 18px;">

                <div class="form-group">
                    <label for="filtroMatricula">Matrícula</label>
                    <input
                        type="text"
                        id="filtroMatricula"
                        placeholder="Pesquisar matrícula..."
                        oninput="renderizarTabela()">
                </div>

                <div class="form-group">
                    <label for="filtroPlaca">Placa</label>
                    <input
                        type="text"
                        id="filtroPlaca"
                        placeholder="Pesquisar placa..."
                        oninput="renderizarTabela()">
                </div>

                <div class="form-group">
                    <label for="filtroUF">UF</label>
                    <select id="filtroUF" onchange="renderizarTabela()">
                        <option value="">Todas</option>
                        <option value="RJ">RJ</option>
                        <option value="ES">ES</option>
                        <option value="MG">MG</option>
                        <option value="SP">SP</option>
                        <option value="PR">PR</option>
                    </select>
                </div>

            </div>

            <div class="table-responsive">

                <table>

                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Placa</th>
                            <th>UF</th>
                            <th>Classificação</th>
                            <th>KM diário</th>
                            <th>KM/L</th>
                            <th>Valor litro</th>
                            <th>Saldo real</th>
                            <th>Saldo semanal</th>
                            <th>Saldo a inserir</th>
                            <th>Situação</th>
                        </tr>
                    </thead>

                    <tbody id="tabelaSaldos">

                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <strong>Nenhum saldo carregado</strong>
                                    Selecione uma data e clique em calcular saldos.
                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="footer-info">
                <span id="quantidadeExibida">Exibindo 0 registros</span>
                <span id="ultimaAtualizacao">Aguardando consulta</span>
            </div>

        </section>

        <!-- Relatório de erros -->
        <section class="card">

            <div class="table-header">

                <div>
                    <h2>Relatório de execução</h2>
                    <p class="section-description" style="margin: 7px 0 0;">
                        Histórico de erros e resultados das integrações externas.
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-outline"
                    onclick="carregarErros()">
                    ⟳ Atualizar relatório
                </button>

            </div>

            <div class="table-responsive">

                <table>

                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Matrícula</th>
                            <th>Placa</th>
                            <th>Etapa</th>
                            <th>Integração</th>
                            <th>Valor</th>
                            <th>HTTP</th>
                            <th>Status</th>
                            <th>Mensagem</th>
                        </tr>
                    </thead>

                    <tbody id="tabelaErros">

                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <strong>Nenhum registro carregado</strong>
                                    O histórico aparecerá após a execução dos serviços.
                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="footer-info">
                <span id="quantidadeErros">0 registros no relatório</span>
            </div>

        </section>

    </div>

    <!-- Modal de confirmação -->
    <div class="modal-overlay" id="modalConfirmacao">

        <div class="modal">

            <h3>Confirmar inserção de saldos?</h3>

            <p>
                Você está prestes a enviar os saldos calculados para as APIs
                de combustível.
            </p>

            <p>
                <strong>Data de referência:</strong>
                <span id="modalDataReferencia"></span>
            </p>

            <p>
                <strong>Colaboradores:</strong>
                <span id="modalQuantidade"></span>
            </p>

            <p>
                <strong>Valor total:</strong>
                <span id="modalValor"></span>
            </p>

            <p>
                Confirme somente se os valores apresentados na tabela estiverem corretos.
            </p>

            <div class="modal-actions">

                <button
                    type="button"
                    class="btn-secondary"
                    onclick="fecharConfirmacaoInsercao()">
                    Cancelar
                </button>

                <button
                    type="button"
                    class="btn-success"
                    id="btnConfirmarInsercao"
                    onclick="inserirSaldos()">
                    Confirmar inserção
                </button>

            </div>

        </div>

    </div>

    <script>
        /*
         * CONFIGURAÇÃO DOS ENDPOINTS
         *
         * Estrutura esperada:
         *
         * saldo.php
         * api/
         *   calcular.php
         *   inserir.php
         *   listar.php
         *   erros.php
         */

        const API_BASE = './api/';

        const ENDPOINTS = {
            calcular: API_BASE + 'calcular.php',
            inserir: API_BASE + 'inserir.php',
            listar: API_BASE + 'listar.php',
            erros: API_BASE + 'erros.php'
        };

        let saldos = [];
        let erros = [];

        let calculando = false;
        let inserindo = false;

        let saldoDisponivel = false;

        /*
         * FUNÇÕES AUXILIARES
         */

        function obterDataReferencia() {
            return document.getElementById('dataReferencia').value;
        }

        function formatarMoeda(valor) {
            return Number(valor || 0).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function formatarNumero(valor, casas = 2) {
            return Number(valor || 0).toLocaleString('pt-BR', {
                minimumFractionDigits: casas,
                maximumFractionDigits: casas
            });
        }

        function escaparHTML(valor) {
            return String(valor ?? '').replace(/[&<>"']/g, caractere => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[caractere]);
        }

        function mostrarMensagem(mensagem, tipo = 'info') {
            const elemento = document.getElementById('alertMessage');

            elemento.className = 'alert show alert-' + tipo;
            elemento.textContent = mensagem;

            elemento.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function limparMensagem() {
            const elemento = document.getElementById('alertMessage');

            elemento.className = 'alert';
            elemento.textContent = '';
        }

        function definirCarregamento(botaoId, carregando, texto) {
            const botao = document.getElementById(botaoId);

            botao.disabled = carregando;
            botao.innerHTML = carregando ?
                '⏳ ' + texto :
                botao.dataset.originalText;
        }

        async function requisicao(url, opcoes = {}) {
            const resposta = await fetch(url, {
                ...opcoes,
                headers: {
                    'Accept': 'application/json',
                    ...(opcoes.body ? {
                        'Content-Type': 'application/json'
                    } : {}),
                    ...opcoes.headers
                }
            });

            const texto = await resposta.text();

            let dados;

            try {
                dados = texto ? JSON.parse(texto) : {};
            } catch (erro) {
                throw new Error(
                    'O servidor retornou uma resposta inválida. Verifique os logs do PHP.'
                );
            }

            if (!resposta.ok) {
                throw new Error(
                    dados.message || dados.mensagem ||
                    'Erro HTTP ' + resposta.status
                );
            }

            if (dados.success === false || dados.sucesso === false) {
                throw new Error(
                    dados.message || dados.mensagem || 'A operação não foi concluída.'
                );
            }

            return dados;
        }

        function extrairLista(dados) {
            if (Array.isArray(dados)) {
                return dados;
            }

            if (Array.isArray(dados.data)) {
                return dados.data;
            }

            if (Array.isArray(dados.saldos)) {
                return dados.saldos;
            }

            if (Array.isArray(dados.resultados)) {
                return dados.resultados;
            }

            return [];
        }

        function dataValida() {
            const data = obterDataReferencia();

            if (!data) {
                mostrarMensagem(
                    'Selecione uma data de referência.',
                    'error'
                );

                return false;
            }

            if (!/^\d{4}-\d{2}-\d{2}$/.test(data)) {
                mostrarMensagem(
                    'A data de referência é inválida.',
                    'error'
                );

                return false;
            }

            return true;
        }

        function atualizarEstadoBotoes() {
            document.getElementById('btnInserir').disabled = !saldoDisponivel || saldos.length === 0 || inserindo || calculando;

            document.getElementById('btnCalcular').disabled =
                calculando || inserindo;

            document.getElementById('btnAtualizar').disabled =
                calculando || inserindo;
        }

        /*
         * CARREGAMENTO DOS SALDOS
         */

        async function carregarSaldos() {
            if (!dataValida()) {
                return;
            }

            limparMensagem();

            const tbody = document.getElementById('tabelaSaldos');

            tbody.innerHTML = `
            <tr>
                <td colspan="11">
                    <div class="empty-state">
                        <strong>Carregando saldos...</strong>
                        Aguarde enquanto consultamos os registros.
                    </div>
                </td>
            </tr>
        `;

            try {
                const params = new URLSearchParams({
                    data_referencia: obterDataReferencia()
                });

                const dados = await requisicao(
                    ENDPOINTS.listar + '?' + params.toString()
                );

                saldos = extrairLista(dados);

                saldoDisponivel = saldos.length > 0;

                renderizarTabela();
                atualizarMetricas();

                document.getElementById('ultimaAtualizacao').textContent =
                    'Atualizado em ' + new Date().toLocaleString('pt-BR');

                atualizarEstadoBotoes();

            } catch (erro) {
                saldos = [];
                saldoDisponivel = false;

                renderizarTabela();
                atualizarMetricas();
                atualizarEstadoBotoes();

                mostrarMensagem(
                    'Erro ao carregar saldos: ' + erro.message,
                    'error'
                );
            }
        }

        /*
         * CÁLCULO DOS SALDOS
         */

        async function calcularSaldos() {
            if (!dataValida() || calculando || inserindo) {
                return;
            }

            const confirmar = confirm(
                'Deseja calcular ou recalcular os saldos da referência ' +
                obterDataReferencia() + '?\n\n' +
                'Os registros existentes serão atualizados, sem gerar duplicidades.'
            );

            if (!confirmar) {
                return;
            }

            calculando = true;
            atualizarEstadoBotoes();

            limparMensagem();

            definirCarregamento(
                'btnCalcular',
                true,
                'Calculando...'
            );

            try {
                const dados = await requisicao(ENDPOINTS.calcular, {
                    method: 'POST',
                    body: JSON.stringify({
                        data_referencia: obterDataReferencia()
                    })
                });

                mostrarMensagem(
                    dados.message || dados.mensagem ||
                    'Cálculo realizado com sucesso.',
                    'success'
                );

                // Atualiza os dados persistidos.
                await carregarSaldos();

                // Atualiza o relatório.
                await carregarErros();

            } catch (erro) {
                mostrarMensagem(
                    'Erro durante o cálculo: ' + erro.message,
                    'error'
                );

            } finally {
                calculando = false;

                definirCarregamento(
                    'btnCalcular',
                    false,
                    'Calcular / Recalcular saldos'
                );

                atualizarEstadoBotoes();
            }
        }

        /*
         * MÉTRICAS
         */

        function atualizarMetricas() {
            const total = saldos.length;

            const saldoTotal = saldos.reduce((soma, item) => {
                return soma + Number(
                    item.saldo ?? item.orcsemanal ?? 0
                );
            }, 0);

            const sucesso = saldos.filter(item =>
                String(item.status_insercao).toUpperCase() === 'SUCESSO'
            ).length;

            const falhas = saldos.filter(item =>
                String(item.status_insercao).toUpperCase() === 'ERRO'
            ).length;

            document.getElementById('totalColaboradores').textContent = total;

            document.getElementById('saldoTotal').textContent =
                formatarMoeda(saldoTotal);

            document.getElementById('totalSucesso').textContent = sucesso;

            document.getElementById('totalErros').textContent = falhas;
        }

        /*
         * RENDERIZAÇÃO DA TABELA
         */

        function renderizarTabela() {
            const tbody = document.getElementById('tabelaSaldos');

            const matricula = document
                .getElementById('filtroMatricula')
                .value.toLowerCase()
                .trim();

            const placa = document
                .getElementById('filtroPlaca')
                .value.toLowerCase()
                .trim();

            const uf = document
                .getElementById('filtroUF')
                .value.toUpperCase();

            const filtrados = saldos.filter(item => {

                const correspondeMatricula = String(
                    item.matricula ?? ''
                ).toLowerCase().includes(matricula);

                const correspondePlaca = String(
                    item.placa ?? ''
                ).toLowerCase().includes(placa);

                const correspondeUF = !uf ||
                    String(item.uf ?? item.unidade ?? '').toUpperCase() === uf;

                return correspondeMatricula &&
                    correspondePlaca &&
                    correspondeUF;
            });

            document.getElementById('quantidadeExibida').textContent =
                `Exibindo ${filtrados.length} de ${saldos.length} registros`;

            if (filtrados.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="11">
                        <div class="empty-state">
                            <strong>Nenhum registro encontrado</strong>
                            Não existem saldos para os filtros selecionados.
                        </div>
                    </td>
                </tr>
            `;

                return;
            }

            tbody.innerHTML = filtrados.map(item => {

                const matricula = escaparHTML(item.matricula);
                const placa = escaparHTML(item.placa);
                const uf = escaparHTML(item.uf ?? item.unidade);
                const classificacao = escaparHTML(item.classificacao ?? 'Técnico');

                const kmDiario = formatarNumero(
                    item.media_km_diario ?? item.km_diario
                );

                const kmLitro = formatarNumero(
                    item.km_litro ?? item.km_litro_previsto
                );

                const valorLitro = formatarMoeda(item.valorcomb);

                const saldoReal = formatarMoeda(
                    item.saldo_real_calculado
                );

                const saldoSemanal = formatarMoeda(
                    item.sldprjtd ?? item.saldo_semanal
                );

                const saldoInserir = formatarMoeda(
                    item.saldo ?? item.orcsemanal
                );

                const status = String(
                    item.status_insercao ?? 'PENDENTE'
                ).toUpperCase();

                let badge;

                if (status === 'SUCESSO' || status === 'INSERIDO') {
                    badge = '<span class="badge badge-success">Inserido</span>';
                } else if (status === 'ERRO') {
                    badge = '<span class="badge badge-danger">Erro</span>';
                } else {
                    badge = '<span class="badge badge-warning">Pendente</span>';
                }

                return `
                <tr>
                    <td><strong>${matricula}</strong></td>
                    <td>${placa}</td>
                    <td>${uf}</td>
                    <td>${classificacao}</td>
                    <td>${kmDiario}</td>
                    <td>${kmLitro}</td>
                    <td>${valorLitro}</td>
                    <td>${saldoReal}</td>
                    <td>${saldoSemanal}</td>
                    <td><strong>${saldoInserir}</strong></td>
                    <td>${badge}</td>
                </tr>
            `;

            }).join('');
        }

        function limparFiltros() {
            document.getElementById('filtroMatricula').value = '';
            document.getElementById('filtroPlaca').value = '';
            document.getElementById('filtroUF').value = '';

            renderizarTabela();
        }

        /*
         * RELATÓRIO DE ERROS
         */

        async function carregarErros() {
            if (!dataValida()) {
                return;
            }

            const tbody = document.getElementById('tabelaErros');

            try {
                const params = new URLSearchParams({
                    data_referencia: obterDataReferencia()
                });

                const dados = await requisicao(
                    ENDPOINTS.erros + '?' + params.toString()
                );

                erros = dados.logs ?? [];

                renderizarErros();
                atualizarMetricas();

            } catch (erro) {
                erros = [];

                renderizarErros();

                console.error('Erro ao carregar relatório:', erro);
            }
        }

        function renderizarErros() {
    const tbody = document.getElementById('tabelaErros');

    document.getElementById('quantidadeErros').textContent =
        `${erros.length} registros no relatório`;

    if (erros.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <strong>Nenhum erro ou registro encontrado</strong>
                        Não existem registros no relatório para esta referência.
                    </div>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = erros.map(item => {

        const status = String(item.status ?? '').toUpperCase();

        const badge = status === 'SUCESSO'
            ? '<span class="badge badge-success">Sucesso</span>'
            : '<span class="badge badge-danger">Erro</span>';

        return `
            <tr>
                <td>${escaparHTML(item.created_at ?? '')}</td>
                <td>${escaparHTML(item.matricula ?? '')}</td>
                <td>${escaparHTML(item.placa ?? '')}</td>
                <td>${escaparHTML(item.etapa ?? '')}</td>
                <td>${escaparHTML(item.integracao ?? '')}</td>
                <td>${formatarMoeda(item.valor)}</td>
                <td>${escaparHTML(item.http_code ?? '-')}</td>
                <td>${badge}</td>
                <td>
                    <div class="error-message">
                        ${escaparHTML(item.texto ?? '')}
                    </div>
                </td>
            </tr>
        `;

    }).join('');
}

        /*
         * INSERÇÃO DOS SALDOS
         */

        function abrirConfirmacaoInsercao() {
            if (!dataValida()) {
                return;
            }

            if (!saldoDisponivel || saldos.length === 0) {
                mostrarMensagem(
                    'Não existem saldos calculados para inserir.',
                    'error'
                );

                return;
            }

            const total = saldos.reduce((soma, item) => {
                return soma + Number(item.saldo ?? item.orcsemanal ?? 0);
            }, 0);

            document.getElementById('modalDataReferencia').textContent =
                obterDataReferencia();

            document.getElementById('modalQuantidade').textContent =
                saldos.length;

            document.getElementById('modalValor').textContent =
                formatarMoeda(total);

            document.getElementById('modalConfirmacao').classList.add('active');
        }

        function fecharConfirmacaoInsercao() {
            document.getElementById('modalConfirmacao').classList.remove('active');
        }

        async function inserirSaldos() {
            if (inserindo || calculando) {
                return;
            }

            if (!dataValida()) {
                return;
            }

            if (!confirm(
                    'ATENÇÃO: esta operação enviará os saldos para os cartões dos colaboradores.\n\n' +
                    'Deseja realmente continuar?'
                )) {
                return;
            }

            inserindo = true;

            fecharConfirmacaoInsercao();
            atualizarEstadoBotoes();

            definirCarregamento(
                'btnConfirmarInsercao',
                true,
                'Enviando...'
            );

            // Desabilita também o botão principal durante a operação.
            document.getElementById('btnInserir').disabled = true;

            limparMensagem();

            try {
                const dados = await requisicao(ENDPOINTS.inserir, {
                    method: 'POST',
                    body: JSON.stringify({
                        data_referencia: obterDataReferencia()
                    })
                });

                const sucesso = Number(
                    dados.sucesso_count ?? dados.sucessos ?? 0
                );

                const falhas = Number(
                    dados.erro_count ?? dados.erros ?? 0
                );

                let mensagem = dados.message || dados.mensagem ||
                    'Processamento de inserção concluído.';

                if (dados.success !== false) {
                    mensagem += ` Sucessos: ${sucesso}. Erros: ${falhas}.`;
                }

                mostrarMensagem(
                    mensagem,
                    falhas > 0 ? 'error' : 'success'
                );

                await carregarSaldos();
                await carregarErros();

            } catch (erro) {
                mostrarMensagem(
                    'Erro na inserção dos saldos: ' + erro.message,
                    'error'
                );

                // Mesmo em caso de erro, consulta o histórico.
                await carregarErros();

            } finally {
                inserindo = false;

                document.getElementById('btnConfirmarInsercao').disabled = false;
                document.getElementById('btnConfirmarInsercao').textContent =
                    'Confirmar inserção';

                atualizarEstadoBotoes();
            }
        }

        /*
         * INICIALIZAÇÃO
         */

        document.addEventListener('DOMContentLoaded', async () => {

            // Salva os textos originais dos botões.
            document.querySelectorAll('[id^="btn"]').forEach(botao => {
                botao.dataset.originalText = botao.innerHTML;
            });

            await carregarSaldos();
            await carregarErros();

        });

        // Ao alterar a referência, carrega novamente os dados.
        document.getElementById('dataReferencia').addEventListener(
            'change',
            async () => {
                saldoDisponivel = false;
                atualizarEstadoBotoes();

                saldos = [];
                erros = [];

                atualizarMetricas();

                await carregarSaldos();
                await carregarErros();
            }
        );
    </script>

</body>

</html>