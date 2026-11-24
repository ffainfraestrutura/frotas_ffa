<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : 'td';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : 'td';
$cargofi = isset($_POST["cargo"]) ? $_POST["cargo"] : 'td';
$mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';
$placatecfi = isset($_POST["placatec"]) ? $_POST["placatec"] : '';
$cartaotecfi = isset($_POST["cartaotec"]) ? $_POST["cartaotec"] : '';

if (strftime('%A') == "domingo") {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}


include "conecta.php";
include_once "./func/log.php";
include_once "./func/consultacartao.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Relatório de Cartões</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4 mt-4">
                <h1 class="h1 mt-2 mb-2"> Relatório de Cartões </h1>
                <form action="" method="post">
                    <div class="row col-span-12 align-items-end">
                        <div class="col-md-2">
                            <label for="exampleInputDate1">
                                <h5>Centro de Custo:</h5>
                            </label>
                            <select class="form-select" name="ccusto" id="ccusto">
                                <option value="td" <?= ($ccustofi == 'td') ? 'selected' : '' ?>> Todos </option>
                                <?php
                                $sql = "SELECT DISTINCT COALESCE(ccusto, 'Sem centro de custo') AS ccusto
                                            FROM bdaniel.tbfuncionario 
                                            where status = 'Ativo' 
                                            and (ccusto like '%IHS%' or ccusto like '%CLARO%' or ccusto is null)
                                            ORDER BY ccusto;";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $ccusto = $row[0];
                                    $selected = ($ccusto == $ccustofi) ? 'selected' : '';
                                    print "<option value='$ccusto' $selected>$ccusto</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="exampleInputDate2">
                                <h5>Estado:</h5>
                            </label>
                            <select class="form-select" name="filial" id="filial">
                                <option value="td" <?= ($filialfi == 'td') ? 'selected' : '' ?>> Todos </option>
                                <?php
                                $sql = "SELECT DISTINCT unidade FROM bdfrota.tbveiculo where unidade <> '' order by unidade";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $uf_trabalho = $row[0];
                                    $selected = ($uf_trabalho == $filialfi) ? 'selected' : '';
                                    print "<option value='$uf_trabalho' $selected>$uf_trabalho</option>";
                                }
                                ?>
                            </select>
                        </div>


                        <div class="col-md-2">
                            <label for="matricula">
                                <h5>Matrícula:</h5>
                            </label>
                            <input type="text" class="form-control" name="mattec" placeholder="Digite a matrícula"
                                value="<?= htmlspecialchars($mattecfi) ?>">
                        </div>

                        <div class="col-md-2">
                            <label for="placatec">
                                <h5>Placa:</h5>
                            </label>
                            <input type="text" class="form-control" name="placatec" placeholder="Digite a placa"
                                value="<?= htmlspecialchars($placatecfi) ?>">
                        </div>

                        <div class='col-md-1'>
                            <input type="submit" class="btn w-100 btn-success" value="Filtrar">
                        </div>
                    </div>
                </form>

                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Placa</th>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>Supervisor</th>
                                    <th>Cargo</th>
                                    <th>Centro de Custo</th>
                                    <th>Número Cartão</th>
                                    <th>Empresa</th>
                                    <th>Saldo Cartão</th>
                                    <th>Limite Próximo Período</th>
                                    <th>Cota Inicial</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $executarConsulta = ($_SERVER['REQUEST_METHOD'] === 'POST');

                                // Não executa nada ao entrar na página
                                if ($executarConsulta) {


                                    $filtros = [];

                                    if ($ccustofi !== 'td') {
                                        $filtros[] = "f.ccusto = '$ccustofi'";
                                    }

                                    if ($filialfi !== 'td') {
                                        $filtros[] = "f.uf_trabalho = '$filialfi'";
                                    }

                                    if ($cargofi !== 'td') {
                                        $filtros[] = "f.cargo = '$cargofi'";
                                    }

                                    if ($mattecfi !== '') {
                                        $filtros[] = "f.matricula = '$mattecfi'";
                                    }

                                    if ($placatecfi !== '') {
                                        $filtros[] = "v.placa = '$placatecfi'";
                                    }

                                    $whereExtra = !empty($filtros)
                                        ? " AND " . implode(" AND ", $filtros)
                                        : "";

                                    $sql = "
                                        SELECT 
                                            v.placa,
                                            COALESCE(f.matricula, '') AS matricula,
                                            COALESCE(f.nome, '') AS nome,
                                            COALESCE(sf.nome, '') AS supervisor_nome,
                                            COALESCE(f.ccusto, '') AS ccusto,
                                            COALESCE(f.cargo, '') AS cargo,
                                            v.unidade,
                                            COALESCE(sa.totalextra, 0) AS totalextra,
                                            COALESCE(sa.saldo, 0) AS saldo,
                                            f.codempresa,
                                            COALESCE(sa.orcsemanal, 0) as orcinicial
                                        FROM bdfrota.tbveiculo v
                                        JOIN bdfrota.tbastatusvel a 
                                            ON v.statusvel = a.idtbastatusvel
                                        LEFT JOIN bdfrota.tbsaldo sa
                                            ON sa.matricula = v.matcond
                                            AND sa.data = (
                                                            SELECT MAX(data) 
                                                            FROM bdfrota.tbsaldo
                                                            )
                                        LEFT JOIN bdaniel.tbfuncionario f 
                                            ON v.matcond = f.matricula
                                        LEFT JOIN bdfrota.tbusuario u
                                            ON f.matricula = u.matricula 
                                            AND (u.idtbsupervisor IS NOT NULL AND u.idtbsupervisor <> 0)
                                        LEFT JOIN bdfrota.tbsupervisor s
                                            ON u.idtbsupervisor = s.idtbsupervisor
                                        LEFT JOIN bdaniel.tbfuncionario sf
                                            ON s.matricula = sf.matricula
                                        WHERE a.statusgeral = 1 
                                        AND f.status = 'Ativo' 
                                        AND a.status NOT IN (
                                            'INDISPONÍVEL',
                                            'SINISTRO/MANUTENÇÃO',
                                            'EM DESMOBILIZAÇÃO',
                                            'RESERVADO: PROJETO'
                                        )
                                        $whereExtra
                                    ";


                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    $totalRegistros = mysql_num_rows($resultado);
                                    $cartoes = [];

                                    if ($totalRegistros > 3) {
                                        // Busca geral
                                        $cartoes = consultarCartoes();
                                    }

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $placa = $row[0];
                                        $matriculatec = $row[1];
                                        $nometec = $row[2];
                                        $supervisor_nome = $row[3];
                                        $ccusto = $row[4];
                                        $cargo = $row[5];
                                        $unidade = $row[6];
                                        $totalextra = $row[7];
                                        $saldobd = number_format($row[8], 2, ',', '.');
                                        $codempresa = $row[9];
                                        $orcinicial = number_format($row[10], 2, ',', '.');

                                        $numeroCartao = "Sem cartão Vinculado";
                                        $empresa = "";
                                        $saldo = 0;
                                        $limiteProximoPeriodo = "";

                                        // 🔹 SE FOR ATÉ 3 → busca individual
                                        if ($totalRegistros <= 3) {
                                            $cartaoIndividual = consultarCartoes($placa, null, $codempresa);
                                        } else {
                                            // 🔹 SE FOR MAIS DE 3 → usa resultado geral
                                            $cartaoIndividual = $cartoes;
                                        }

                                        // =========================
                                        // VALCARD
                                        // =========================
                                        if (!empty($cartaoIndividual[$codempresa])) {
                                            if (!empty($cartaoIndividual[$codempresa][$placa])) {
                                                $cartao = $cartaoIndividual[$codempresa][$placa];
                                                $saldo = number_format($cartao['saldo'], 2, ',', '.');
                                                $empresa = $cartao['operadora'];
                                                $numeroCartao = $cartao['numeroCartao'];
                                                $limiteProximoPeriodo = $cartao['limiteProximoPeriodo'];
                                            }
                                        }
                                        $limiteProximoPeriodo = str_replace(',', '.', $limiteProximoPeriodo);
                                        $orcinicial = str_replace(',', '.', $orcinicial);
                                        print "<tr>
                                                <td>$placa</td>
                                                <td>$matriculatec</td>
                                                <td style=\"white-space: nowrap;\">$nometec</td>
                                                <td>$supervisor_nome</td>
                                                <td>$cargo</td>
                                                <td>$ccusto</td>
                                                <td>$numeroCartao</td>
                                                <td>$empresa</td>
                                                <td>R$ $saldo</td>
                                                <td>R$ $limiteProximoPeriodo</td>
                                                <td>R$ $orcinicial</td>
                                                <td>
                                                    <span class='badge " . (($limiteProximoPeriodo != $orcinicial) ? 'bg-danger' : 'bg-success') . "'>
                                                        " . (($limiteProximoPeriodo != $orcinicial) ? 'Erro' : 'Sucesso') . "
                                                    </span>
                                                </td>
                                            </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <button id="btnExportExcel" class="btn btn-success mt-3">Gerar Excel</button>
                    </div>
                </div>
            </div>

        </main>

        <footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
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

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<script>
    function exportTableToExcel(tableID, filename = '') {
        var dataType = 'application/vnd.ms-excel';

        // Clona a tabela original
        var tableClone = document.getElementById(tableID).cloneNode(true);

        // Remove a última coluna de todas as linhas (tanto do cabeçalho quanto do corpo)
        var rows = tableClone.querySelectorAll('tr');
        rows.forEach(function (row) {
            var cells = row.querySelectorAll('th, td');
            if (cells.length > 0) {
                // Remove o último elemento de cada linha
                cells[cells.length - 1].remove();
            }
        });

        // Cria o HTML da tabela sem a coluna de ação
        var tableHTML = '<table border="1">' + tableClone.innerHTML + '</table>';

        // Cria um Blob com o conteúdo da tabela
        var blob = new Blob([tableHTML], { type: dataType });
        var downloadLink = document.createElement("a");

        // Cria uma URL para o Blob
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = filename ? filename + '.xls' : 'excel_data.xls';

        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Dispara a função ao clicar no botão
    document.getElementById('btnExportExcel').addEventListener('click', function () {
        exportTableToExcel('datatablesSimple', 'excel_saldo_veiculo');
    });
</script>


<!--script para criar data table-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {

            perPageSelect: [10, 25, 50, 1000]
        });
    });

    // Função para limpar filtros
    function limparFiltros() {
        document.getElementById('filtroMatricula').value = '';
        document.getElementById('filtroPlaca').value = '';
        document.getElementById('filtroTipoAcao').value = '';
        document.getElementById('filtroDataInicio').value = '';
        document.getElementById('filtroDataFim').value = '';
        document.getElementById('corpoTabelaJustificativas').innerHTML =
            '<tr><td colspan="8" class="text-center text-muted"><i class="fa-solid fa-info-circle"></i> Utilize os filtros acima para buscar justificativas</td></tr>';
    }

</script>

</html>