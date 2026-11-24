<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
header("Content-Type: text/html; charset=utf-8");
session_start();
include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";

$filtroCoodenador_matricula = $_GET["filtroCoordenador"] ?? null;

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve                                            <form action='control/aprovarcota.php' method='post'>
                                                <button class='btn btn-outline-danger' 
                                                        style='margin-bottom: -100px !important;'
                                                        title='Negar pedido de cota'
                                                        data-bs-toggle='tooltip'
                                                        data-bs-placement='top'>
                                                    <span class='material-symbols-outlined'>close</span>
                                                </button>ar para ter acesso');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

if ($filtroCoodenador_matricula) {
    $filtroCoodenador_matricula = mysqli_real_escape_string($conn, $filtroCoodenador_matricula);
    $whereCoordenador = "WHERE tc.matricula = '$filtroCoodenador_matricula'";
} else {
    $whereCoordenador = "";
}

$matricula = mysqli_real_escape_string($conn, $matricula);

$sql_coordenadores = "
                    SELECT 
                        tc.matricula, tf.nome
                    FROM
                        bdfrota.tbcoord tc
                            JOIN
                        bdaniel.tbfuncionario tf ON tc.matricula = tf.matricula
                    ORDER BY tf.nome
                    ";

$result_coordenadores = mysqli_query($conn, $sql_coordenadores);

$coordenadores = mysqli_fetch_all($result_coordenadores, MYSQLI_ASSOC);


$sql_registros = "SELECT 
    mh.placa,
    tf.matricula,
    tf.nome,
    tf.ccusto,
    mh.hodometro,
    mh.previsao_manutencao,
    mh.km_medio_diario,
    am.data_agendada_gerencia,
    am.data_agendada_frota,
    mh.faixa_km_atual
FROM
    bdfrota.metricas_hodometro mh
        LEFT JOIN agendamento_manutencao am 
            ON am.faixa_km = mh.faixa_km_atual
            AND am.placa = mh.placa
        JOIN bdfrota.tbveiculo tv 
            ON mh.placa = tv.placa
        LEFT JOIN bdaniel.tbfuncionario tf 
            ON tf.matricula = tv.matcond
        LEFT JOIN bdfrota.tbusuario tu 
            ON tu.matricula = tf.matricula
        LEFT JOIN bdfrota.tbsupervisor ts 
            ON tu.idtbsupervisor = ts.idtbsupervisor
        LEFT JOIN bdfrota.tbcoord tc 
            ON ts.idtbcoordenador = tc.idtbcoordenador
        $whereCoordenador
ORDER BY mh.previsao_manutencao ASC";

$result_registros = mysqli_query($conn, $sql_registros);
$registros = mysqli_fetch_all($result_registros, MYSQLI_ASSOC);
$hojeObj = new DateTime();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            background-color: #f8f9fc;
        }

        /* Card mais bonito */
        .card {
            border-radius: 12px;
        }

        .modern-card {
            border-radius: 16px;
            border: 1px solid #eaeef5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .custom-table {
            border-collapse: separate;
            border-spacing: 0 8px;
            /* Espaço entre linhas, se quiser efeito de "cards" */
            border: none !important;
        }

        .custom-table thead th {
            border: none !important;
            background-color: transparent !important;
            padding-bottom: 15px;
        }

        .custom-table tbody tr {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .custom-table tbody td {
            border: none !important;
            padding: 12px 15px !important;
        }

        .datatable-input {
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
            padding: 7px 12px !important;
        }

        .datatable-selector {
            border-radius: 8px !important;
            padding: 5px 10px !important;
            border: 1px solid #ced4da !important;
        }

        /* Ajuste do dropdown de registros por página */
        .datatable-dropdown label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 13px;
        }

        /* Hover suave */
        .table-hover tbody tr:hover {
            background-color: #f1f5ff;
            transition: 0.2s;
        }

        /* Badge mais elegante */
        .badge {
            font-size: 12px;
            padding: 6px 10px;
        }

        /* Botão */
        .btn-success {
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <?php include "../menu.php"; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid p-4">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                    <div>
                        <h2 class="fw-bold mb-0">
                            <i class="fa-solid fa-truck-moving me-2 text-primary"></i>
                            Gestão de Manutenção de Frota
                        </h2>
                        <small class="text-muted">
                            Acompanhe previsões e agendamentos de manutenção dos veículos
                        </small>
                    </div>

                    <button class="btn btn-success shadow-sm" onclick="exportarExcel()">
                        <i class="fa-solid fa-file-excel me-2"></i>
                        Exportar Excel
                    </button>
                </div>

                <!-- CARD -->
                <div class="card modern-card">
                    <div class="card-body">
                        <!-- Filtro de Coordenador com margem inferior para separar da busca do Datatable -->
                        <div
                            class="d-flex flex-wrap gap-3 align-items-end mb-4 justify-content-between border-bottom pb-4">
                            <div class="d-flex flex-wrap gap-3 align-items-end flex-grow-1">
                                <div style="min-width: 250px;">
                                    <label class="form-label mb-1 fw-bold text-muted small">Coordenador</label>
                                    <select class="form-select shadow-sm" name='filtroCoordenador'
                                        id="filtroCoordenador">
                                        <option value="">Todos os Coordenadores</option>
                                        <?php foreach ($coordenadores as $coordenador): ?>
                                            <option value="<?= $coordenador['matricula'] ?>"
                                                <?= $filtroCoodenador_matricula == $coordenador['matricula'] ? "selected" : "" ?>> <?= $coordenador['nome'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class=" btn btn-primary shadow-sm" onclick="aplicarFiltro()">
                                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                                </button>
                                <button class="btn btn-outline-secondary" onclick="limparFiltro()">
                                    Limpar
                                </button>

                            </div>

                            <div class="text-end">
                                <span class="text-muted small d-block mb-1">Total de registros</span>
                                <span class="badge bg-primary px-3 py-2 fs-6 shadow-sm">
                                    <i class="fa-solid fa-database me-1"></i> <?= count($registros) ?>
                                </span>
                            </div>
                        </div>
                        <!-- TABELA -->
                        <div class="table-responsive">
                            <table class="table align-middle datatable custom-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Placa</th>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>C. Custo</th>
                                        <th>Hodômetro Atual</th>
                                        <th>Km médio diário</th>
                                        <th>Previsão Manutenção</th>
                                        <th>Data Sugerida (Coordenador)</th>
                                        <th>Data Agendada (Frota)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $registro): ?>
                                        <tr>
                                            <td><span class="badge bg-dark"><?= $registro['placa'] ?></span></td>
                                            <td><?= $registro['matricula'] ?></td>
                                            <td><?= $registro['nome'] ?></td>
                                            <td><?= $registro['ccusto'] ?></td>
                                            <td data-order="<?= $registro['hodometro'] ?>">
                                                <strong><?= $registro['hodometro'] ?> km</strong>
                                            </td>
                                            <td data-order="<?= $registro['km_medio_diario'] ?>">
                                                <strong><?= $registro['km_medio_diario'] ?> km</strong>
                                            </td>
                                            <?php
                                            if (!empty($registro['previsao_manutencao'])) {

                                                $dataPrevista = new DateTime($registro['previsao_manutencao']);
                                                $intervalo = $hojeObj->diff($dataPrevista);
                                                $diasRestantes = (int) $intervalo->format("%r%a");

                                                $dataFormatada = $dataPrevista->format('d/m/Y');

                                                if ($diasRestantes <= 0) {
                                                    $classeBadge = "bg-danger text-white";
                                                    $textoDias = " Urgência ";
                                                } elseif ($diasRestantes <= 15) {
                                                    $classeBadge = "bg-warning text-dark";
                                                    $textoDias = $diasRestantes . " dias restantes";
                                                } else {
                                                    $classeBadge = "bg-success text-white";
                                                    $textoDias = $diasRestantes . " dias restantes";
                                                }

                                            } else {
                                                // fallback seguro
                                                $dataFormatada = '-';
                                                $textoDias = 'Sem previsão';
                                                $classeBadge = 'bg-secondary';
                                            }
                                            ?>

                                            <td
                                                data-order="<?= !empty($registro['previsao_manutencao']) ? (new DateTime($registro['previsao_manutencao']))->format('Y-m-d') : '' ?>">
                                                <span class="badge <?= $classeBadge ?>">
                                                    <?= $dataFormatada ?>
                                                    <small>(<?= $textoDias ?>)</small>
                                                </span>
                                            </td>
                                            <?php
                                            $data_agendada_coordenador = !empty($registro['data_agendada_gerencia'])
                                                ? (new DateTime($registro['data_agendada_gerencia']))->format('d/m/Y')
                                                : '-';
                                            ?>
                                            <td data-placa="<?= $registro['placa'] ?>"
                                                data-faixa="<?= $registro['faixa_km_atual'] ?>"
                                                data-order="<?= !empty($registro['data_agendada_gerencia']) ? (new DateTime($registro['data_agendada_gerencia']))->format('Y-m-d') : '' ?>">
                                                <div class="d-flex align-items-center gap-2">

                                                    <!-- DATA VISUAL -->
                                                    <span class="data-text">
                                                        <?= $data_agendada_coordenador ?>
                                                    </span>

                                                    <!-- INPUT ESCONDIDO -->
                                                    <input type="date"
                                                        class="form-control form-control-sm d-none input-data"
                                                        value="<?= !empty($registro['data_agendada_gerencia']) ? (new DateTime($registro['data_agendada_gerencia']))->format('Y-m-d') : '' ?>">

                                                    <!-- BOTÃO EDITAR -->
                                                    <button class="btn btn-sm btn-outline-primary btn-editar">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>

                                                    <!-- BOTÃO SALVAR -->
                                                    <button class="btn btn-sm btn-success d-none btn-salvar">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>

                                                </div>
                                            </td>
                                            <?php
                                            $data_agendada_frota = !empty($registro['data_agendada_frota'])
                                                ? (new DateTime($registro['data_agendada_frota']))->format('d/m/Y')
                                                : '-';
                                            ?>
                                            <td
                                                data-order="<?= !empty($registro['data_agendada_frota']) ? (new DateTime($registro['data_agendada_frota']))->format('Y-m-d') : '' ?>">
                                                <?= $data_agendada_frota ?>
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
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>!-->
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>
        function aplicarFiltro() {
            let coord = document.getElementById('filtroCoordenador').value;
            window.location.href = "?filtroCoordenador=" + coord;
        }

        function limparFiltro() {
            window.location.href = window.location.pathname;
        }

        function exportarExcel() {
            let tabela = document.querySelector("table");
            let wb = XLSX.utils.table_to_book(tabela, { sheet: "Manutenção" });

            XLSX.writeFile(wb, "manutencao_frota.xlsx");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabela = document.querySelector('.datatable');

            if (tabela) {
                new simpleDatatables.DataTable(tabela, {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 10,
                    perPageSelect: [10, 25, 50, 100],
                    labels: {
                        placeholder: "Buscar...",
                        noRows: "Nenhum registro encontrado",
                        info: "Mostrando {start} até {end} de {rows} registros"
                    }
                });
            }
        });
        document.addEventListener('click', function (e) {

            // CLICOU EM EDITAR
            if (e.target.closest('.btn-editar')) {
                let td = e.target.closest('td');

                td.querySelector('.data-text').classList.add('d-none');
                td.querySelector('.input-data').classList.remove('d-none');
                td.querySelector('.btn-editar').classList.add('d-none');
                td.querySelector('.btn-salvar').classList.remove('d-none');
            }

            // CLICOU EM SALVAR
            if (e.target.closest('.btn-salvar')) {
                let td = e.target.closest('td');
                let input = td.querySelector('.input-data');
                let novaData = input.value;

                if (!novaData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        text: 'Selecione uma data antes de salvar'
                    });
                    return;
                }

                let placa = td.dataset.placa;
                let faixa_km = td.dataset.faixa;

                // 🔄 Loading
                Swal.fire({
                    title: 'Salvando...',
                    text: 'Aguarde um momento',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
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

                            // Atualiza visual só se deu certo
                            let dataFormatada = new Date(novaData).toLocaleDateString('pt-BR');
                            td.querySelector('.data-text').innerText = dataFormatada;
                            td.setAttribute('data-order', novaData);

                            td.querySelector('.data-text').classList.remove('d-none');
                            input.classList.add('d-none');
                            td.querySelector('.btn-editar').classList.remove('d-none');
                            td.querySelector('.btn-salvar').classList.add('d-none');

                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso',
                                text: 'Data salva com sucesso!',
                                timer: 1500,
                                showConfirmButton: false
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message || 'Erro ao salvar'
                            });
                        }

                    })
                    .catch(err => {
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Falha na comunicação com o servidor'
                        });

                        console.error(err);
                    });
            }

        });
    </script>

</body>

</html>