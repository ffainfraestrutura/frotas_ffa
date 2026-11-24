<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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

include "conecta2.php";
$datai = "";
if (isset($_POST['datai'])) {
    $datai = $_POST['datai'];
    
    $diaSemana = date('N', strtotime($_POST['datai'])); // 1 = segunda, 7 = domingo
    if ($diaSemana != 1) {
        $diaSemana = date('Y-m-d', strtotime('monday this week', strtotime($datai)));
    } else {
        $diaSemana = date('Y-m-d', strtotime($datai));
    }
    // Consulta funcionários para obter nomes e ccusto
    $sqlFuncionarios = "SELECT nome, matricula, ccusto, status FROM bdaniel.tbfuncionario";
    $stmt = mysqli_query($conexao, $sqlFuncionarios) or die(mysqli_error($conexao));

    // Criar array associativo matricula => dados do funcionário
    $funcionarios = [];
    while ($rowFunc = mysqli_fetch_assoc($stmt)) {
        $funcionarios[$rowFunc['matricula']] = [
            'nome' => $rowFunc['nome'],
            'ccusto' => $rowFunc['ccusto'],
            'status' => $rowFunc['status']
        ];
    }

    $queryGerente = "";
    if(isset($_POST['gerente']) && !empty(trim($_POST['gerente']))){
        $gerente = trim($_POST['gerente']);
        $queryGerente = "AND tg.matricula = '$gerente'";
    }

    // Consulta principal
    $sqlSaldo = "
                SELECT 
                    sldprjtd AS cota_inicial_tecnico,
                    ts.saldo_real_calculado,
                    tu.matricula AS matricula_tecnico,
                    tsup.matricula AS matricula_supervisor,
                    tc.matricula AS matricula_coordenador,
                    tg.matricula AS matricula_gerente,
                    tg.valor AS orcamento_atual_gerente,
                    tg.saldoinicial AS saldo_inicial_gerente,



                    hcrc.valor AS orcamento_atual_coordenador,
                    hcrc.valor AS saldo_inicial_coordenador
                FROM
                    bdfrota.tbsaldo ts
                        JOIN
                    bdfrota.tbusuario tu ON ts.matricula = tu.matricula
                        JOIN
                    bdfrota.tbsupervisor tsup ON tu.idtbsupervisor = tsup.idtbsupervisor
                        JOIN
                    bdfrota.tbcoord tc ON tsup.idtbcoordenador = tc.idtbcoordenador
                        JOIN
                    bdfrota.tbgerente tg ON tg.idtbgerente = tc.idtbgerente
                        JOIN
                    bdfrota.historico_cota_recebida_coordenador hcrc ON tc.matricula = hcrc.matricula
                        AND hcrc.data_referencia = '$diaSemana'
                        JOIN
                    bdfrota.historico_cota_recebida_gerente hcrg ON tg.matricula = hcrg.matricula
                        AND hcrg.data_referencia = '$diaSemana'
                WHERE
                    ts.data = '$diaSemana'
                $queryGerente
                ";

    $stmt = mysqli_query($conexao, $sqlSaldo) or die(mysqli_error($conexao));

    $arrayFinal = [];

    while ($row = mysqli_fetch_assoc($stmt)) {
        $matGerente = $row['matricula_gerente'];
        $matCoord = $row['matricula_coordenador'];
        $matSup = $row['matricula_supervisor'];
        $matTecnico = $row['matricula_tecnico'];

        // Buscar dados dos funcionários
        $nomeGerente = $funcionarios[$matGerente]['nome'] ?? 'N/A';
        $ccustoGerente = $funcionarios[$matGerente]['ccusto'] ?? 'N/A';
        $nomeCoordenador = $funcionarios[$matCoord]['nome'] ?? 'N/A';
        $ccustoCoordenador = $funcionarios[$matCoord]['ccusto'] ?? 'N/A';
        $nomeSupervisor = $funcionarios[$matSup]['nome'] ?? 'N/A';
        $ccustoSupervisor = $funcionarios[$matSup]['ccusto'] ?? 'N/A';
        $nomeTecnico = $funcionarios[$matTecnico]['nome'] ?? 'N/A';
        $ccustoTecnico = $funcionarios[$matTecnico]['ccusto'] ?? 'N/A';
        $statusTecnico = $funcionarios[$matTecnico]['status'] ?? 'N/A';

        // Cria o gerente se ainda não existir
        if (!isset($arrayFinal[$matGerente])) {
            $arrayFinal[$matGerente] = [
                "nome_gerente" => $nomeGerente,
                "ccusto_gerente" => $ccustoGerente,
                "orcamento_atual_gerente" => $row['orcamento_atual_gerente'] ?? 0,
                "orcamento_inicial_gerente" => $row['saldo_inicial_gerente'] ?? 0,
                "coordenadores" => []
            ];
        }

        // Cria o coordenador se ainda não existir
        if (!isset($arrayFinal[$matGerente]['coordenadores'][$matCoord])) {
            $arrayFinal[$matGerente]['coordenadores'][$matCoord] = [
                "nome_coordenador" => $nomeCoordenador,
                "ccusto_coordenador" => $ccustoCoordenador,
                "saldo_atual_coordenador" => $row['orcamento_atual_coordenador'] ?? 0,
                "saldo_inicial_coordenador" => $row['saldo_inicial_coordenador'] ?? 0,
                "supervisores" => []
            ];
        }

        // Cria o supervisor se ainda não existir
        if (!isset($arrayFinal[$matGerente]['coordenadores'][$matCoord]['supervisores'][$matSup])) {
            $arrayFinal[$matGerente]['coordenadores'][$matCoord]['supervisores'][$matSup] = [
                "nome_supervisor" => $nomeSupervisor,
                "ccusto_supervisor" => $ccustoSupervisor,
                "tecnicos" => []
            ];
        }

        // Calcula cotas
        $cota_inicial = (float) ($row['cota_inicial_tecnico'] ?? 0);
        $saldo_real_calculado = (float) ($row['saldo_real_calculado'] ?? 0);
        $cota_repasse_gerente = $cota_inicial * 0.10;
        $cota_repasse_coordenador = $cota_inicial * 0.10;
        // Adiciona técnico
        $arrayFinal[$matGerente]['coordenadores'][$matCoord]['supervisores'][$matSup]['tecnicos'][$matTecnico] = [
            "nome_tecnico" => $nomeTecnico,
            "ccusto_tecnico" => $ccustoTecnico,
            "cota_inicial" => $cota_inicial,
            "saldo_real_calculado" => $saldo_real_calculado,
            "status" => $statusTecnico,
            "cota_repasse_gerente" => $cota_repasse_gerente,
            "cota_repasse_coordenador" => $cota_repasse_coordenador
        ];
    }
}

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
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
                <h1 class="h1 mt-2"> Relatório Cálculo Saldo</h1>
                <form action="#" method="post">
                    <div class="row">
                    <div class="col">
                        <h5>Selecione a semana:</h5>
                        <label for="datai">
                            <h5>Semana (Segunda-feira):<span style='color: red;'>*</span></h5>
                        </label>
                        <input type="date" class="form-control" id="datai" name="datai" <?php print "value='$datai'"; ?>
                            required>
                    </div>
                    <div class="col">
                        <h5>Selecione um gerente:</h5>
                        <label for="gerente">
                            <h5>Gerente:</h5>
                        </label>
                        <select class="form-control" id="gerente" name="gerente">
                            <option value="">-- Todos --</option>
                            <?php
                            // Buscar gerentes ativos
                            $sqlG = "SELECT g.matricula, f.nome 
                                     FROM bdfrota.tbgerente g 
                                     JOIN bdaniel.tbfuncionario f ON g.matricula = f.matricula 
                                     WHERE f.status = 'Ativo' 
                                     ORDER BY f.nome";
                            $resG = mysqli_query($conexao, $sqlG);

                            while ($g = mysqli_fetch_assoc($resG)) {
                                $selected = (isset($_POST['gerente']) && $_POST['gerente'] == $g['matricula']) ? 'selected' : '';
                                echo "<option value='{$g['matricula']}' $selected>{$g['nome']} ({$g['matricula']})</option>";
                            }
                            ?>
                        </select>
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

                    <?php if (!empty($arrayFinal)) { ?>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nível Hierárquico</th>
                                        <th>Matrícula</th>
                                        <th>CCusto</th>
                                        <th>Cota Inicial (Técnico)</th>
                                        <th>Saldo Real Calculado</th>
                                        <th>Repasse Gerente (10%)</th>
                                        <th>Repasse Coordenador (10%)</th>
                                        <th>Orçamento Inicial (G/C)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Gerente
                                    foreach ($arrayFinal as $matGerente => $dadosGerente) {
                                        ?>
                                        <tr class="table-primary">
                                            <td><i class="fas fa-user-tie"></i> Gerente:
                                                <?php echo $dadosGerente['nome_gerente']; ?>
                                            </td>
                                            <td><?php echo $matGerente; ?></td>
                                            <td><?php echo $dadosGerente['ccusto_gerente']; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>R$
                                                <?php echo number_format($dadosGerente['orcamento_inicial_gerente'], 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php
                                        // Coordenador
                                        foreach ($dadosGerente['coordenadores'] as $matCoord => $dadosCoord) {
                                            ?>
                                            <tr class="table-info">
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp; <i class="fas fa-sitemap"></i> Coordenador:
                                                    <?php echo $dadosCoord['nome_coordenador']; ?>
                                                </td>
                                                <td><?php echo $matCoord; ?></td>
                                                <td><?php echo $dadosCoord['ccusto_coordenador']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>R$
                                                    <?php echo number_format($dadosCoord['saldo_inicial_coordenador'], 2, ',', '.'); ?>
                                                </td>
                                            </tr>
                                            <?php
                                            // Supervisor
                                            foreach ($dadosCoord['supervisores'] as $matSup => $dadosSup) {
                                                ?>
                                                <tr class="table-light">
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <i
                                                            class="fas fa-user-shield"></i> Supervisor:
                                                        <?php echo $dadosSup['nome_supervisor']; ?>
                                                    </td>
                                                    <td><?php echo $matSup; ?></td>
                                                    <td><?php echo $dadosSup['ccusto_supervisor']; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                                // Técnico
                                                foreach ($dadosSup['tecnicos'] as $matTecnico => $dadosTecnico) {
                                                    if ($dadosTecnico['status'] != "Demitido") {
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <i
                                                                    class="fas fa-tools"></i> Técnico:
                                                                <?php echo $dadosTecnico['nome_tecnico']; ?>
                                                            </td>
                                                            <td><?php echo $matTecnico; ?></td>
                                                            <td><?php echo $dadosTecnico['ccusto_tecnico']; ?></td>
                                                            <td>R$ <?php echo number_format($dadosTecnico['cota_inicial'], 2, ',', '.'); ?></td>
                                                            <td>R$ <?php echo number_format($dadosTecnico['saldo_real_calculado'], 2, ',', '.'); ?></td>
                                                            <td>R$
                                                                <?php echo number_format($dadosTecnico['cota_repasse_gerente'], 2, ',', '.'); ?>
                                                            </td>
                                                            <td>R$
                                                                <?php echo number_format($dadosTecnico['cota_repasse_coordenador'], 2, ',', '.'); ?>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="card-body">
                            <p>Selecione uma semana ou nenhum dado foi encontrado para a semana selecionada
                                (<?php echo $datai; ?>).</p>
                        </div>
                    <?php } ?>
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
            ordering: false,
            layout: {
                topStart: {
                    //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    buttons: [{ extend: 'excelHtml5', text: 'Exportar para Excel', title: 'Relatório Remanejamento' }]
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

<style>
    /* Estilo para o botão Excel - cor verde */
    .dt-button.buttons-excel {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    
    .dt-button.buttons-excel:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
        color: white !important;
    }
    
    .dt-button.buttons-excel:focus,
    .dt-button.buttons-excel:active {
        background-color: #1e7e34 !important;
        border-color: #1c7430 !important;
        color: white !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
    }
</style>

</html>