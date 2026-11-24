<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$datai = $_POST['datai'];
$dataf = $_POST['dataf'];

include "conecta2.php";
function infoFuncionario($conexao, $matricula)
{
    $sql = "SELECT * FROM bdaniel.tbfuncionario WHERE matricula='$matricula'";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
    return $row;
}

if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
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
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Relatório Remanejamento</h1>
                <form action="./relatorioremanejamento.php" method="post">
                    <h5>Selecione o Periodo:</h5>

                    <div class="row">
                        <div class="col-md-3">
                            <label for="datai">
                                <h5>Data Inicial:<span style='color: red;'>*</span></h5>
                            </label>
                            <input type="date" class="form-control" id="datai" name="datai" <?php print "value='$datai'"; ?> required>
                        </div>

                        <div class="col-md-3">
                            <label for="dataf">
                                <h5>Data Final:<span style='color: red;'>*</span></h5>
                            </label>
                            <input type="date" class="form-control" id="dataf" name="dataf" <?php print "value='$dataf'"; ?> required>
                        </div>
                    </div>
                    <div class='mt-2'>
                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>
                    </div>
                </form>
                <div class="card mt-4 mb-4">
                    <div class="card-header">
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
                                    <th>Funcionário de Origem</th>
                                    <th>Saldo Inicial</th>
                                    <th>Saldo Final</th>
                                    <th>Funcionário de Destino</th>
                                    <th>Saldo Inciial</th>
                                    <th>Saldo Final</th>
                                    <th>Quantidade Transferida</th>
                                    <th>Horário da Movimentação</th>
                                    <th>Autor</th>
                                    <th>Centro de Custo</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $sql = "SELECT * FROM bdfrota.tbremanejamentosaldo where data_hora_transferencia BETWEEN '$datai 00:00:00' AND '$dataf 23:59:59'";
                                $stmt = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                                $resultado = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
                                foreach ($resultado as $row) {
                                    $funcionario_origem = infoFuncionario($conexao, $row['matricula_origem']);
                                    $funcionario_origem = $funcionario_origem['matricula'] . ' - ' . $funcionario_origem['nome'];
                                    $funcionario_destino = infoFuncionario($conexao, $row['matricula_destino']);
                                    $funcionario_destino = $funcionario_destino['matricula'] . ' - ' . $funcionario_destino['nome'];
                                    $autor = infoFuncionario($conexao, $row['autor']);
                                    $centroCusto = $autor['ccusto'];
                                    $autor = $autor['matricula'] . ' - ' . $autor['nome'];
                                    $horario_movimentacao = $row['data_hora_transferencia'];
                                    $campos = [
                                        'valor_transferido',
                                        'saldo_inicial_origem',
                                        'saldo_inicial_destino',
                                        'saldo_final_origem',
                                        'saldo_final_destino'
                                    ];

                                    foreach ($campos as $campo) {
                                        $$campo = $row[$campo];
                                        if (strpos($$campo, ".") !== false) {
                                            $$campo = str_replace(".", ",", $$campo);
                                        }
                                    }

                                    print "
                                            <tr>
                                                <th>$funcionario_origem</th>
                                                <th>$saldo_inicial_origem</th>
                                                <th>$saldo_final_origem</th>
                                                <th>$funcionario_destino</th>
                                                <th>$saldo_inicial_destino</th>
                                                <th>$saldo_final_destino</th>
                                                <th>$valor_transferido</th>
                                                <th>$horario_movimentacao</th>
                                                <th>$autor</th>
                                                <th>$centroCusto</th>
                                            </tr>";
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>

    </div>


</body>

<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>


<script src="src/js/scripts.js"></script>

<script>
    $(document).ready(function () {
        new DataTable('#datatablesSimple', {
            layout: {
                topStart: {
                    //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    buttons: [{ extend: 'excelHtml5', title: 'Relatório Remanejamento' }]
                }
            },

            language: {
                decimal: "",
                emptyTable: "Nada para exibir",
                info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                infoEmpty: "Exibindo página 0 de 0 de 0 registros",
                infoFiltered: "(filtrado do total de _MAX_ registros)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Exibir _MENU_ registros",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Buscar:",
                zeroRecords: "Nenhum resultado encontrado",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Próxima",
                    previous: "Anterior"
                }
            },
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, 'All']
            ]
        });
    });
</script>

</html>