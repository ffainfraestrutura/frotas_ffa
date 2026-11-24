<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../control/conecta.php';
require_once __DIR__ . '/../includes/portal_helpers.php';
exigirLogin();

$perfilLogado = (string) ($_SESSION['perfil'] ?? '');
$matriculaLogada = (string) ($_SESSION['matricula'] ?? '');

// Perfis permitidos: 2 (Coordenador) e 4 (Gestor/Admin)
if ($perfilLogado !== '2' &&$perfilLogado !== '4') {
    echo "<script type='text/javascript'>alert('Você não tem permissão para acessar esta página.'); window.location='../index.php';</script>";
    exit;
}

$nome      = (string) ($_SESSION['nome'] ?? '');
$usuariof  = (string) ($_SESSION['usuario'] ?? '');
$filial    = (string) ($_SESSION['filial'] ?? '');

function esc(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Se o perfil for coordenador (2), ele vê por padrão a sua própria matrícula, a menos que venha um filtro GET explícito (se permitido)
$filtroCoordenador_matricula = trim((string) ($_GET['filtroCoordenador'] ?? ''));

if ($perfilLogado === '2' &&$filtroCoordenador_matricula === '') {
    $filtroCoordenador_matricula =$matriculaLogada;
}

$whereCoordenador = '';
if ($filtroCoordenador_matricula !== '') {
    $filtroCoordenador_matriculaEsc = mysqli_real_escape_string($conn, $filtroCoordenador_matricula);$whereCoordenador = "AND tc.matricula = '$filtroCoordenador_matriculaEsc'";
}

// Busca lista de coordenadores para o select (se perfil 4 ele vê todos, se perfil 2 foca ou restringe)
$sql_coordenadores = "
    SELECT tc.matricula, tf.nome
    FROM bdcorp.tbcoord tc
    JOIN bdcorp.tbfuncionario tf ON tc.matricula = tf.matricula
    ORDER BY tf.nome
";
$result_coordenadores = mysqli_query($conn, $sql_coordenadores);$coordenadores = $result_coordenadores ? mysqli_fetch_all($result_coordenadores, MYSQLI_ASSOC) : [];

$sql_registros = "SELECT 
    mh.placa,
    MAX(tf.matricula) AS matricula,
    MAX(tf.nome) AS nome,
    MAX(tf.ccusto) AS ccusto,
    MAX(mh.hodometro) AS hodometro,
    MAX(mh.previsao_manutencao) AS previsao_manutencao,
    MAX(mh.km_medio_diario) AS km_medio_diario,
    MAX(am.data_agendada_gerencia) AS data_agendada_gerencia,
    MAX(am.data_agendada_frota) AS data_agendada_frota,
    MAX(mh.faixa_km_atual) AS faixa_km_atual
FROM
    bdautofrotas.tb_metricas_hodometro mh
    LEFT JOIN bdautofrotas.tb_agendamento_manutencao am 
        ON am.faixa_km = mh.faixa_km_atual
        AND am.placa = mh.placa
    JOIN bdautofrotas.tbveiculo tv 
        ON mh.placa = tv.placa
    LEFT JOIN bdcorp.tbfuncionario tf 
        ON tf.matricula = tv.matcond
    LEFT JOIN bdcorp.tbusuario tu 
        ON tu.matricula = tf.matricula
    LEFT JOIN bdcorp.tbsupervisor ts 
        ON tu.idtbsupervisor = ts.idtbsupervisor
    LEFT JOIN bdcorp.tbcoord tc 
        ON ts.idtbcoordenador = tc.idtbcoordenador
    WHERE 1=1
    " . $whereCoordenador . "
    GROUP BY mh.placa
    ORDER BY mh.previsao_manutencao ASC";

$result_registros = mysqli_query($conn,$sql_registros);
$registros =$result_registros ? mysqli_fetch_all($result_registros, MYSQLI_ASSOC) : [];$hojeObj = new DateTime();

$totalVeiculos = count($registros);$urgentes = 0;
$pendentesAgendamento = 0;
$encerrados = 0;

foreach ($registros as $r) {$isEnc = (!empty($r['data_fech']) || ($r['status_manutencao'] ?? '') === 'Encerrado');
    if ($isEnc) {$encerrados++;
    } else {
        if (!empty($r['previsao_manutencao'])) {$dtPrev = new DateTime($r['previsao_manutencao']);$diff = (int) $hojeObj->diff($dtPrev)->format("%r%a");
            if ($diff <= 0) {$urgentes++;
            }
        }
        if (empty($r['data_agendada_frota'])) {$pendentesAgendamento++;
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../src/images/favicon.png">
    <title>Portal FFA - Gestão de Manutenção de Frota (Coordenador)</title>
    
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../src/css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13.5px;
            background-color: #f4f6f9;
            color: #334155;
        }
        .kpi-card {
            border: none;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .modern-card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }
        .custom-table thead th {
            background-color: #f8fafc !important;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 12px 14px !important;
        }
        .custom-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }
        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .custom-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle;
        }
        .badge-placa {
            font-family: monospace;
            font-size: 13px;
            letter-spacing: 1px;
            background-color: #0f172a;
            color: #fff;
            padding: 4px 8px;
            border-radius: 6px;
        }
        .status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .badge-urgente { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-atencao { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-ok { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-neutro { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    </style>
</head>
<body class="sb-nav-fixed">
<?php renderCabecalhoAutofrota('Gestão de Manutenção - Coordenador'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-3">

                <!-- CABEÇALHO -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">
                            <i class="fa-solid fa-screwdriver-wrench me-2 text-primary"></i>Gestão de Manutenção - Coordenador
                        </h3>
                        <p class="text-muted mb-0 small">
                            Acompanhe prazos de revisão e sugira datas operacionais para a frota.
                        </p>
                    </div>
                    <button class="btn btn-success shadow-sm rounded-3 fw-medium" onclick="exportarExcel()">
                        <i class="fa-solid fa-file-excel me-2"></i>Exportar Relatório
                    </button>
                </div>

                <!-- CARDS INDICADORES (KPIs) -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold d-block mb-1">FROTA MONITORADA</span>
                                    <h3 class="fw-bold mb-0 text-dark"><?= $totalVeiculos ?></h3>
                                </div>
                                <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="fa-solid fa-truck"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold d-block mb-1">MANUTENÇÃO URGENTE</span>
                                    <h3 class="fw-bold mb-0 text-danger"><?= $urgentes ?></h3>
                                </div>
                                <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold d-block mb-1">PENDENTE AGENDAMENTO</span>
                                    <h3 class="fw-bold mb-0 text-warning"><?= $pendentesAgendamento ?></h3>
                                </div>
                                <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="fa-solid fa-calendar-minus"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small fw-semibold d-block mb-1">MANUTENÇÕES CONCLUÍDAS</span>
                                    <h3 class="fw-bold mb-0 text-success"><?= $encerrados ?></h3>
                                </div>
                                <div class="kpi-icon bg-success bg-opacity-10 text-success">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PAINEL PRINCIPAL COM FILTROS E TABELA -->
                <div class="card modern-card">
                    <div class="card-body p-4">
                        
                        <!-- BARRA DE FILTROS -->
                        <div class="d-flex flex-wrap gap-3 align-items-end justify-content-between pb-3 mb-3 border-bottom">
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <div style="min-width: 280px;">
                                    <label class="form-label fw-bold text-secondary small mb-1">Filtrar por Coordenador</label>
                                    <select class="form-select shadow-sm rounded-3" name="filtroCoordenador" id="filtroCoordenador">
                                        <option value="">Todos os Coordenadores</option>
                                        <?php foreach ($coordenadores as$coordenador): ?>
                                            <option value="<?= esc($coordenador['matricula']) ?>" <?= $filtroCoordenador_matricula ===$coordenador['matricula'] ? "selected" : "" ?>>
                                                <?= esc($coordenador['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class="btn btn-primary shadow-sm rounded-3 px-3" onclick="aplicarFiltro()">
                                    <i class="fa-solid fa-filter me-1"></i>Filtrar
                                </button>
                                <button class="btn btn-outline-secondary rounded-3 px-3" onclick="limparFiltro()">
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- TABELA DE DADOS -->
                        <div class="table-responsive">
                            <table class="table align-middle datatable custom-table">
                                <thead>
                                    <tr>
                                        <th>Placa</th>
                                        <th>Condutor</th>
                                        <th>C. Custo</th>
                                        <th>Hodômetro</th>
                                        <th>Média Diária</th>
                                        <th>Previsão Manutenção</th>
                                        <th>Data Sugerida (Coordenador)</th>
                                        <th>Data Agendada (Frota)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php foreach ($registros as$registro): ?>
                                    <?php
                                    $isEncerrado = (!empty($registro['data_fech']) || ($registro['status_manutencao'] ?? '') === 'Encerrado');
                                    
                                    if ($isEncerrado) {$dataFormatada = '-';
                                        $badgeClass = 'badge-neutro';$textoDias = 'Concluído';
                                        $dataOrderValue = '9999-12-31';
                                    } elseif (!empty($registro['previsao_manutencao'])) {$dataPrevista = new DateTime($registro['previsao_manutencao']);$intervalo = $hojeObj->diff($dataPrevista);
                                        $diasRestantes = (int)$intervalo->format("%r%a");

                                        $dataFormatada =$dataPrevista->format('d/m/Y');
                                        $dataOrderValue =$dataPrevista->format('Y-m-d');

                                        if ($diasRestantes <= 0) {$badgeClass = "badge-urgente";
                                            $textoDias = "Vencida / Urgente";
                                        } elseif ($diasRestantes <= 15) {$badgeClass = "badge-atencao";
                                            $textoDias =$diasRestantes . " dias rest.";
                                        } else {
                                            $badgeClass = "badge-ok";
                                            $textoDias =$diasRestantes . " dias rest.";
                                        }
                                    } else {
                                        $dataFormatada = '-';$textoDias = 'Sem previsão';
                                        $badgeClass = 'badge-neutro';$dataOrderValue = '';
                                    }
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="badge-placa"><?= esc($registro['placa']) ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= esc($registro['nome'] ?? '-') ?></div>
                                                <small class="text-muted">Matrícula: <?= esc($registro['matricula'] ?? '-') ?></small>
                                            </td>
                                            <td><span class="text-secondary fw-medium"><?= esc($registro['ccusto'] ?? '-') ?></span></td>
                                            <td data-order="<?= esc((string) ($registro['hodometro'] ?? 0)) ?>">
                                                <span class="fw-bold text-dark"><?= number_format((float)($registro['hodometro'] ?? 0), 0, ',', '.') ?> km</span>
                                            </td>
                                            <td data-order="<?= esc((string) ($registro['km_medio_diario'] ?? 0)) ?>">
                                                <span class="text-secondary"><?= number_format((float)($registro['km_medio_diario'] ?? 0), 1, ',', '.') ?> km/dia</span>
                                            </td>
                                            <td data-order="<?= esc($dataOrderValue) ?>">
                                                <span class="status-badge <?= $badgeClass ?>">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <span><?= $dataFormatada ?></span>
                                                    <?php if ($textoDias !== 'Concluído' && $dataFormatada !== '-'): ?>
                                                        <small class="fw-normal">(<?= esc($textoDias) ?>)</small>
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <?php 
                                            $dtGerencia = !empty($registro['data_agendada_gerencia']) 
                                                ? (new DateTime($registro['data_agendada_gerencia']))->format('d/m/Y') 
                                                : '-';
                                            $dtGerenciaOrder = !empty($registro['data_agendada_gerencia']) ? (new DateTime($registro['data_agendada_gerencia']))->format('Y-m-d') : '';
                                            ?>
                                            <td data-placa="<?= esc($registro['placa']) ?>"
                                                data-faixa="<?= esc($registro['faixa_km_atual'] ?? '') ?>"
                                                data-order="<?= esc($dtGerenciaOrder) ?>">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="data-text fw-bold text-primary">
                                                        <?= $dtGerencia ?>
                                                    </span>

                                                    <input type="date"
                                                        class="form-control form-control-sm d-none input-data"
                                                        style="max-width: 130px;"
                                                        value="<?= !empty($registro['data_agendada_gerencia']) ? (new DateTime($registro['data_agendada_gerencia']))->format('Y-m-d') : '' ?>">

                                                    <button class="btn btn-sm btn-light border btn-editar text-secondary" title="Editar Data Sugerida">
                                                        <i class="fa-solid fa-pen fa-xs"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-success d-none btn-salvar" title="Salvar">
                                                        <i class="fa-solid fa-check fa-xs"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <?php 
                                            $dtFrota = !empty($registro['data_agendada_frota']) 
                                                ? (new DateTime($registro['data_agendada_frota']))->format('d/m/Y') 
                                                : '-';
                                            $dtFrotaOrder = !empty($registro['data_agendada_frota']) ? (new DateTime($registro['data_agendada_frota']))->format('Y-m-d') : '';
                                            ?>
                                            <td data-order="<?= esc($dtFrotaOrder) ?>">
                                                <span class="text-dark fw-medium"><?= $dtFrota ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </main>

        <footer class="py-3 bg-white border-top mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <div>Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        function aplicarFiltro() {
            let coord = document.getElementById('filtroCoordenador').value;
            window.location.href = "?filtroCoordenador=" + encodeURIComponent(coord);
        }

        function limparFiltro() {
            window.location.href = window.location.pathname;
        }

        function exportarExcel() {
            let tabela = document.querySelector("table");
            let wb = XLSX.utils.table_to_book(tabela, { sheet: "Manutenção Coordenador" });
            XLSX.writeFile(wb, "manutencao_coordenador.xlsx");
        }

        document.addEventListener('DOMContentLoaded', function () {
            let dataTable;
            const tabela = document.querySelector('.datatable');

            if (tabela) {
                dataTable = new simpleDatatables.DataTable(tabela, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    perPageSelect: [10, 25, 50, 100],
                    labels: {
                        placeholder: "Buscar por placa, condutor...",
                        noRows: "Nenhum registro encontrado",
                        info: "Exibindo {start} até {end} de {rows} registros"
                    }
                });
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('.btn-editar')) {
                    let td = e.target.closest('td');
                    td.querySelector('.data-text').classList.add('d-none');
                    td.querySelector('.input-data').classList.remove('d-none');
                    td.querySelector('.btn-editar').classList.add('d-none');
                    td.querySelector('.btn-salvar').classList.remove('d-none');
                }

                if (e.target.closest('.btn-salvar')) {
                    let td = e.target.closest('td');
                    let input = td.querySelector('.input-data');
                    let novaData = input.value;

                    if (!novaData) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Selecione uma data válida antes de salvar'
                        });
                        return;
                    }

                    let placa = td.dataset.placa;
                    let faixa_km = td.dataset.faixa;

                    Swal.fire({
                        title: 'Salvando...',
                        text: 'Aguarde um momento',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('./control/salvar_data_manutencao.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            placa: placa,
                            faixa_km: faixa_km,
                            data: novaData
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            let [ano, mes, dia] = novaData.split('-');
                            let dataFormatada = `${dia}/${mes}/${ano}`;

                            td.querySelector('.data-text').innerText = dataFormatada;
                            td.setAttribute('data-order', novaData);

                            td.querySelector('.data-text').classList.remove('d-none');
                            input.classList.add('d-none');
                            td.querySelector('.btn-editar').classList.remove('d-none');
                            td.querySelector('.btn-salvar').classList.add('d-none');

                            if (dataTable) {
                                dataTable.update();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: 'Data sugerida salva com sucesso!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message || 'Erro ao salvar alteração'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Falha de conexão com o servidor'
                        });
                        console.error(err);
                    });
                }
            });
        });
    </script>
<?php renderRodapeAutofrota(); ?>
</body>
</html>