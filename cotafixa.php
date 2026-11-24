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

$action = isset($_POST["action"]) ? $_POST["action"] : 'td';
$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : 'td';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : 'td';
$cargofi = isset($_POST["cargo"]) ? $_POST["cargo"] : 'td';
$mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';
$placatecfi = isset($_POST["placatec"]) ? $_POST["placatec"] : '';

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
    <title>Cota fixa | FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>
<!-- <style>
    .input-group{
        max-width: 40%;
    }
</style> -->

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2">Cota fixa</h1>

                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a href="./dashboard.php">
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="cotafixaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="lista-tab" data-bs-toggle="tab"
                                    data-bs-target="#lista" type="button" role="tab">
                                    Lista de Cotas
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="adicionar-tab" data-bs-toggle="tab"
                                    data-bs-target="#adicionar" type="button" role="tab">
                                    Adicionar Cota
                                </button>
                            </li>
                        </ul>
                        <!--tabela para entrada de dados-->
                        <div class="tab-content" id="cotafixaTabsContent">
                            <div class="tab-pane fade show active" id="lista" role="tabpanel">
                                <table id='datatablesSimple' class='table table-bordered'>
                                    <thead>
                                        <tr>
                                            <th>Placa</th>
                                            <th>Matricula</th>
                                            <th>Nome</th>
                                            <th>Valor</th>
                                            <th>Editar Cota:</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        include 'conecta05.php';

                                        $stmt = "SELECT * FROM bdcombustivel.tbcotafixa";
                                        $stmt = mysqli_query($conexao05, $stmt);

                                        while ($row = mysqli_fetch_array($stmt)) {
                                            $placa = $row['placa'];
                                            $nome = $row['nome'];
                                            $valor = $row['valor'];
                                            $matricula = $row['matricula'];

                                            echo "<tr>
                                    <td>$placa</td>
                                    <td>$matricula</td>
                                    <td>$nome</td>
                                    <td>$valor</td> 
                                    <td class='col-md-3'>
                                        <form method='POST' class='input-group mb-3'>
                                            <input type='hidden' name='action' value='editar'>
                                            <input type='hidden' name='placa' value='$placa'>
                            
                                            <input name='valor' type='text' class='form-control'
                                                   placeholder='Nova Cota'
                                                   oninput='formatCurrency(this)'>
                            
                                            <button class='btn btn-outline-secondary' type='submit'>
                                                Enviar
                                            </button>
                                        </form>
                                    </td>
                                    <td class='col-md-1'>
                                        <form method='POST' onsubmit=\"return confirm('Tem certeza que deseja excluir?')\">
                                            <input type='hidden' name='action' value='remover'>
                                            <input type='hidden' name='placa' value='$placa'>

                                            <button type='submit' class='btn btn-outline-danger'>
                                                Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                                        }
                                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['placa'])) {

                                            $action = $_POST['action'];
                                            $placa = $_POST['placa'];

                                            // ===== EDITAR =====
                                            if ($action === 'editar' && isset($_POST['valor'])) {

                                                $valor = $_POST['valor'];

                                                $stmt = $conexao05->prepare(
                                                    "UPDATE bdcombustivel.tbcotafixa SET valor = ? WHERE placa = ?"
                                                );
                                                $stmt->bind_param("ds", $valor, $placa);

                                                if ($stmt->execute()) {
                                                    echo "<script>
                                                    alert('Cota atualizada com sucesso!');
                                                    window.location.href='cotafixa.php';
                                                  </script>";
                                                } else {
                                                    echo "<script>alert('Erro ao atualizar');</script>";
                                                }

                                                $stmt->close();
                                            }

                                            if ($action === 'adicionar') {

                                                $placa = strtoupper($_POST['placa']);
                                                $matricula = $_POST['matricula'];
                                                $nome = $_POST['nome'];
                                                $valor = $_POST['valor'];

                                                $stmt = $conexao05->prepare(
                                                    "INSERT INTO bdcombustivel.tbcotafixa (placa, matricula, nome, valor)
                                                     VALUES (?, ?, ?, ?)"
                                                );
                                                $stmt->bind_param("sssd", $placa, $matricula, $nome, $valor);

                                                if ($stmt->execute()) {
                                                    echo "<script>
                                                            alert('Cota adicionada com sucesso!');
                                                            window.location.href='cotafixa.php';
                                                          </script>";
                                                } else {
                                                    echo "<script>alert('Erro ao adicionar cota');</script>";
                                                }

                                                $stmt->close();
                                            }

                                            // ===== EXCLUIR =====
                                            if ($action === 'remover') {

                                                $stmt = $conexao05->prepare(
                                                    "DELETE FROM bdcombustivel.tbcotafixa WHERE placa = ?"
                                                );
                                                $stmt->bind_param("s", $placa);

                                                if ($stmt->execute()) {
                                                    echo "<script>
                                                    alert('Registro excluído com sucesso!');
                                                    window.location.href='cotafixa.php';
                                                  </script>";
                                                } else {
                                                    echo "<script>alert('Erro ao excluir');</script>";
                                                }

                                                $stmt->close();
                                            }

                                            $conexao05->close();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <button id="btnExportExcel" class="btn btn-success mt-3">Gerar Excel</button>
                            </div>
                            <div class="tab-pane fade" id="adicionar" role="tabpanel">

                                <form method="POST" class="row g-3 mt-3">

                                    <input type="hidden" name="action" value="adicionar">

                                    <div class="col-md-3">
                                        <label class="form-label">Placa</label>
                                        <input type="text" name="placa" class="form-control" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Matrícula</label>
                                        <input type="text" name="matricula" class="form-control" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Nome</label>
                                        <input type="text" name="nome" class="form-control" required>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Valor</label>
                                        <input type="text" name="valor" class="form-control"
                                            oninput="formatCurrency(this)" required>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success">
                                            Adicionar Cota
                                        </button>
                                    </div>

                                </form>

                            </div>

                        </div>
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
    <?php

    ?>
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
            perPageSelect: [10, 50, 100, 500]
        });
    });
</script>
<script>
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, ''); // Remove qualquer caractere não numérico

        // Limita o número de caracteres a 3 dígitos
        if (value.length > 3) {
            value = value.substring(0, 3);
        }

        // Atualiza o valor no input
        input.value = value;
    }
</script>

</html>