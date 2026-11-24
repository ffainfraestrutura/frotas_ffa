<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;



include"conecta02.php";
include_once"./func/log.php";




// Inicializando o $where com a condição inicial obrigatória
$where = "WHERE f.visivel = 1";

if (!empty($_GET['matricula'])) {
    $matricula = mysqli_real_escape_string($conexao, $_GET['matricula']);
    $where .= " AND p.matricula = '$matricula'";
} elseif (empty($_GET)) { // Nenhum filtro aplicado, usa a matrícula padrão
    $defaultMatricula = '0'; // Defina aqui sua matrícula padrão
    $where .= " AND p.matricula = '$defaultMatricula'";
}

if (!empty($_GET['nome'])) {
    $matricula = mysqli_real_escape_string($conexao, $_GET['nome']);
    $where .= " AND f.matricula = '$matricula'";
}

if (!empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
    $data_inicio = mysqli_real_escape_string($conexao, $_GET['data_inicio']);
    $data_fim = mysqli_real_escape_string($conexao, $_GET['data_fim']);
    $where .= " AND p.dia BETWEEN '$data_inicio' AND '$data_fim'";
} elseif (!empty($_GET['data_ponto'])) {
    $data_ponto = mysqli_real_escape_string($conexao, $_GET['data_ponto']);
    $where .= " AND DATE(p.data_ponto) = '$data_ponto'";
}

if (!empty($_GET['gestor'])) {
    $matriculaGestor = mysqli_real_escape_string($conexao, $_GET['gestor']);
    $where .= " AND g.matriculagestor = '$matriculaGestor'";
}


if (!empty($_GET['centro_custo'])) {
    $fkccusto = mysqli_real_escape_string($conexao, $_GET['centro_custo']);
    $where .= " AND cc.idtbccusto = '$fkccusto'";
}


// Montando a consulta
$query = "SELECT 
            p.matricula,
            f.nome AS funcionario,
            e.descricao AS empresa,
            f.fkccusto,
            cc.descricao AS centro_custo,
            fl.descricao AS filial,
            f.fkgestor,
            g.nomegestor AS gestor,
            f.visivel,
            j.descricao AS jornada,
            p.dia,
            p.numero_ordem,
            p.eta,
            p.fim,
            p.endereco_gps_eta,
            p.endereco_gps_fim,
            p.distancia_eta,
            p.distancia_fim
        FROM BdPonto.relatorio_os AS p
        LEFT JOIN BdPonto.tbfuncionario AS f ON p.matricula = f.matricula
        LEFT JOIN BdPonto.tbempresa AS e ON f.fkempresa = e.idtbempresa
        LEFT JOIN BdPonto.tbfilial AS fl ON f.fkfilial = fl.idtbfilial
        LEFT JOIN BdPonto.tbgestor AS g ON f.fkgestor = g.idtbgestor
        LEFT JOIN BdPonto.tbccusto AS cc ON f.fkccusto = cc.idtbccusto
        LEFT JOIN BdPonto.tbjornada AS j ON f.fkjornada = j.idtbjornada
        $where";

$resultado = mysqli_query($conexao, $query);

function DiaSemana($numero)
{
    $dias_semana = [
        1 => 'Domingo',
        2 => 'Segunda-feira',
        3 => 'Terça-feira',
        4 => 'Quarta-feira',
        5 => 'Quinta-feira',
        6 => 'Sexta-feira',
        7 => 'Sábado'
    ];

    return $dias_semana[$numero];
}

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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório OS x GPS</h1>
<div class="col-md-3 mt-4">
                    <button type="button" id="toggleFilters" class="btn btn-secondary">Filtros</button>
                </div>
                <div id="filterContainer" style="display: none;">
                    <form method="GET" action="">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="matricula" class="form-label">Matrícula:</label>
                                <input type="text" name="matricula" id="matricula" class="form-control"
                                    value="<?= $_GET['matricula'] ?? $defaultMatricula ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="nome" class="form-label">Colaborador:</label>
                                <select name="nome" id="nome" class="form-select form-select-sm">
                                    <option value="">Selecione um colaborador</option>
                                    <?php
                                    // Recuperar os colaboradores
                                    $sqlColaborador = "SELECT matricula, nome FROM BdPonto.tbfuncionario ORDER BY nome";
                                    $resultadoColaborador = mysqli_query($conexao, $sqlColaborador);
                                    while ($rowColaborador = mysqli_fetch_array($resultadoColaborador)) {
                                        echo "<option value='" . htmlspecialchars($rowColaborador['matricula']) . "'" .
                                            ($rowColaborador['matricula'] == ($colaboradorFiltro ?? '') ? ' selected' : '') .
                                            ">" . htmlspecialchars($rowColaborador['nome']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="data_inicio" class="form-label">Data Inicial:</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= $_GET['data_inicio'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="data_fim" class="form-label">Data Final:</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= $_GET['data_fim'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="gestor" class="form-label">Gestor:</label>
                                <select name="gestor" id="gestor" class="form-select form-select-sm">
                                    <option value="">Selecione um gestor</option>
                                    <?php
                                    // Recuperar os gestores
                                    $sqlGestor = "SELECT matriculagestor, nomegestor FROM BdPonto.tbgestor WHERE visivel = 1 ORDER BY nomegestor";
                                    $resultadoGestor = mysqli_query($conexao, $sqlGestor);
                                    while ($rowGestor = mysqli_fetch_array($resultadoGestor)) {
                                        echo "<option value='" . htmlspecialchars($rowGestor['matriculagestor']) . "'" .
                                            ($rowGestor['matriculagestor'] == ($_GET['gestor'] ?? '') ? ' selected' : '') .
                                            ">" . htmlspecialchars($rowGestor['nomegestor']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="centro_custo" class="form-label">Centro de Custo:</label>
                                <select name="centro_custo" id="centro_custo" class="form-select form-select-sm">
                                    <option value="">Selecione um centro de custo</option>
                                    <?php
                                    // Recuperar os centros de custo
                                    $sqlCentroCusto = "SELECT idtbccusto, descricao FROM BdPonto.tbccusto WHERE visivel = 1 ORDER BY descricao";
                                    $resultadoCentroCusto = mysqli_query($conexao, $sqlCentroCusto);
                                    while ($rowCentroCusto = mysqli_fetch_array($resultadoCentroCusto)) {
                                        $selected = ($rowCentroCusto['idtbccusto'] == ($_GET['centro_custo'] ?? '')) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($rowCentroCusto['idtbccusto']) . "' $selected>" . htmlspecialchars($rowCentroCusto['descricao']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3 mt-4">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <button type="button" id="clearFilters" class="btn btn-secondary">Limpar Filtros</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div style="text-align: right; margin: 20px;">
                    <button id="exportExcel" style="background-color: #28a745; border: none; color: white; padding: 10px 20px; font-size: 16px; border-radius: 5px; cursor: pointer;">Exportar para Excel</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

 <script>
        $(document).ready(function() {
            let table = $('#tabela1').DataTable({
                "lengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "Todos"]
                ],
            });

            $('#toggleFilters').click(function() {
                $('#filterContainer').toggle();
            });
            $('#clearFilters').click(function() {
                $('#filterContainer input').val(''); // Limpa todos os campos de input dentro do container de filtros
            });
        });
        document.getElementById("exportExcel").addEventListener("click", function() {
            let table = document.getElementById("tabela1");
            let workbook = XLSX.utils.table_to_book(table, {
                sheet: "Relatório"
            });
            XLSX.writeFile(workbook, "RelatorioPontosColaboradores.xlsx");
        });
    </script>
</html>