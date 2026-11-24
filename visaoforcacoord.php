<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];


if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '2') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

$sql = "SELECT codempresa FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);
$codempresa = $row[0];

/*if($codempresa == 2){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

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
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- vendor jquery js -->
    <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
    <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

    <!--data tables - css e js-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">

                <h1 class="h1 pt-2 pb-2"> Visão Força </h1>

                <?php
                // Buscar saldo do coordenador
                $sql_saldo = "SELECT valor FROM bdfrota.tbcoord WHERE matricula = '$matricula'";
                $resultado_saldo = mysql_query($sql_saldo) or die(mysql_error());
                $row_saldo = mysql_fetch_array($resultado_saldo);
                $saldo_coordenador = $row_saldo[0];
                ?>
                
                <h6 class="mb-3">Saldo Coordenador: R$ <?php echo number_format($saldo_coordenador, 2, ',', '.'); ?></h6>

                <?php
                // Buscar dados do coordenador logado da tabela bdfrota.tbcoord
                $sql = "SELECT c.idtbcoordenador, c.matricula, c.idtbgerente, f.nome 
                        FROM bdfrota.tbcoord c
                        JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula
                        WHERE c.matricula = '$matricula'";
                $resultado = mysql_query($sql) or die(mysql_error());
                $row = mysql_fetch_array($resultado);
                $idtbcoordenador = $row[0];
                $matricula_coordenador = $row[1];
                $idtbgerente = $row[2];
                $nome_coordenador = $row[3];
                
                // Pega o supervisor selecionado via GET (se houver)
                $supervisor_selecionado = isset($_GET['supervisor']) ? $_GET['supervisor'] : '';
                ?>

                <!--div tabela coordenador-->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user-tie"></i>
                        Informações do Coordenador
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome do Coordenador</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style='white-space: nowrap;'><?php echo $matricula; ?></td>
                                    <td style='white-space: nowrap;'><?php echo $nome; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Seletor de Supervisor -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-filter me-1"></i>
                        Selecionar Supervisor
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="supervisorSelect" class="form-label">Supervisor:</label>
                                    <select class="form-select" id="supervisorSelect" name="supervisor">
                                        <option value="">Selecione um supervisor...</option>
                                        <?php                        // Buscar supervisores vinculados ao coordenador com status ativo
                        $sql_sup = "SELECT DISTINCT s.idtbsupervisor, s.matricula, f.nome, s.saldo
                                    FROM bdfrota.tbsupervisor s
                                    JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                                    WHERE s.idtbcoordenador = '$idtbcoordenador'
                                    AND UPPER(TRIM(f.status)) = 'ATIVO'
                                    ORDER BY f.nome";
                                        $resultado_sup = mysql_query($sql_sup) or die(mysql_error());
                                        
                                        while($row_sup = mysql_fetch_array($resultado_sup)) {
                                            $id_sup = $row_sup['idtbsupervisor'];
                                            $mat_sup = $row_sup['matricula'];
                                            $nome_sup = $row_sup['nome'];
                                            $saldo_sup = $row_sup['valor'];
                                            
                                            $selected = ($supervisor_selecionado == $id_sup) ? 'selected' : '';
                                            echo "<option value='$id_sup' data-matricula='$mat_sup' data-saldo='$saldo_sup' $selected>$nome_sup (Mat: $mat_sup)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Visualizar Técnicos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($supervisor_selecionado != ''): 
                    // Buscar informações do supervisor selecionado com status ativo e placa
                    $sql_sup_info = "SELECT s.matricula, f.nome, s.saldo, v.placa
                                     FROM bdfrota.tbsupervisor s
                                     JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                                     LEFT JOIN bdfrota.tbveiculo v ON f.matricula = v.matcond
                                     WHERE s.idtbsupervisor = '$supervisor_selecionado'
                                     AND UPPER(TRIM(f.status)) = 'ATIVO'";
                    $resultado_sup_info = mysql_query($sql_sup_info) or die(mysql_error());
                    $row_sup_info = mysql_fetch_array($resultado_sup_info);
                    $matricula_supervisor = $row_sup_info['matricula'];
                    $nome_supervisor = $row_sup_info['nome'];
                    $saldosup = $row_sup_info['saldo'];
                    $placa_supervisor = $row_sup_info['placa'] ? $row_sup_info['placa'] : 'Sem placa';
                ?>

                <!--div tabela supervisor selecionado-->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user-check"></i>
                        Informações do Supervisor Selecionado
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome do Supervisor</th>
                                    <th>Placa</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style='white-space: nowrap;'><?php echo $matricula_supervisor; ?></td>
                                    <td style='white-space: nowrap;'><?php echo $nome_supervisor; ?></td>
                                    <td style='white-space: nowrap;'><?php echo $placa_supervisor; ?></td>
                                    <td style='white-space: nowrap;'><?php echo "R$ " . number_format($saldosup, 2, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!--div tabela técnicos-->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-users"></i>
                        Técnicos Vinculados ao Supervisor: <?php echo $nome_supervisor; ?>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome do Técnico</th>
                                    <th>Cargo</th>
                                    <th>Placa</th>
                                    <th>Saldo Cartão</th>
                                    <th>Total Orçamento</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php

                                $sql = "SELECT DISTINCT
                                            a.matricula, 
                                            b.nome, 
                                            b.codempresa,
                                            a.perfil
                                        FROM 
                                            bdfrota.tbusuario a
                                        JOIN 
                                            bdaniel.tbfuncionario b ON a.matricula = b.matricula
                                        LEFT JOIN 
                                            bdfrota.tbcondutor c ON a.matricula = c.matricula
                                        WHERE 
                                            a.idtbsupervisor = '$supervisor_selecionado' 
                                            AND a.perfil = 0 
                                            AND UPPER(TRIM(b.status)) = 'ATIVO'
                                        ORDER BY b.nome
                                        ";

                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $matriculatec = $row[0];
                                    $nometec = $row[1];
                                    $codemptec = $row[2];
                                    $perfiltec = $row[3];
                                    
                                    // Define o cargo baseado no perfil
                                    if ($perfiltec == 0) {
                                        $cargotec = "Técnico";
                                    } elseif ($perfiltec == 1) {
                                        $cargotec = "Supervisor";
                                    } else {
                                        $cargotec = "Outro";
                                    }

                                    $saldocartao = 0;

                                    $sql2 = "SELECT totalextra, idtbsaldo FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $totalextra = $row2[0];
                                    $idtbsaldo = $row2[1];

                                    $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $placa = $row2[0];

                                    if (mysql_num_rows($resultado2) >= 1) {
                                        if ($codemptec == 2) {//pesquisar na valecard
                                            $curl = curl_init();
                                            curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_POST, TRUE);
                                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                                'Content-Type: application/json',
                                                'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
                                            ));
                                            curl_setopt($curl, CURLOPT_HEADER, 0);
                                            $body = '{
                                                "contratoCliente" : "524073",
                                                "numeroCartao" : null,
                                                "placa" : "' . $placa . '"
                                            }';

                                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                                            $result = json_decode(curl_exec($curl));

                                            foreach ($result as $itens) {

                                                $statusCartao = $itens->statusCartao;
                                                $saldocartao = $itens->saldo;

                                                if ($statusCartao == 'A') {
                                                    $saldocartao = $itens->saldo;
                                                    $saldocartao = str_replace(".", ",", $saldocartao);


                                                } else {
                                                    $saldocartaov = $statusCartao;
                                                    //$saldocartaov = "Sem cartão vinculado";
                                
                                                }

                                            }

                                        } else {

                                            $curl = curl_init();
                                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_POST, TRUE);
                                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                                'Content-Type: application/json',
                                                'Accept: application/json',
                                                'Accept-Charset: utf-8',
                                                'Accept-Encoding: gzip',
                                                'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                                            ));
                                            curl_setopt($curl, CURLOPT_HEADER, 0);
                                            $body = '{
                                            "codigoCliente": 183062,
                                            "codigoProduto": 4,
                                            "numeroCartao": null,
                                            "especieCartao": null,
                                            "numeroMatricula": null,
                                            "nomeCompleto": null,
                                            "placaVeiculo": "' . $placa . '",
                                            "numeroFrota": null,
                                            "codigoTipoFrota": null,
                                            "codigoFamiliaVeiculo": null,
                                            "codigoVeiculoModelo": null,
                                            "codigoCidade": null,
                                            "codigoClienteTCVeiculoResponsavel": null,
                                            "nomeResponsavel": null,
                                            "codigoGrupo": 4,
                                            "codigoTipoCombustivel": null,
                                            "informacoesAdicionais": [
                                                {
                                                "labelIA": null,
                                                "codigoIA": null,
                                                "descricaoIA": null
                                                }
                                            ],
                                            "situacaoCartao": "T",
                                            "ordem": "C",
                                            "quebra": null,
                                            "tipoMotorista": null,
                                            "ativacao": null,
                                            "tipoCartao": null,
                                            "controlaHodometro": null,
                                            "controlaHorimetro": null,
                                            "situacaoVeiculo": null,
                                            "trackOnLine": null
                                            }';

                                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                                            $result = json_decode(curl_exec($curl));

                                            if (count($result->itens)) {

                                                foreach ($result->itens as $itens) {

                                                    $situacao = $itens->situacao;

                                                    if ($situacao == 'A') {
                                                        $saldocartao = $itens->saldo;
                                                        $saldocartao = str_replace(".", ",", $saldocartao);
                                                    }

                                                }
                                            }
                                        }
                                    } else {
                                        $placa = 'Sem placa';
                                        $saldocartao = 0;
                                    }   




                                    print "                        
                            <tr>
                            <td style='white-space: nowrap;'>$matriculatec</td>
                            <td style='white-space: nowrap;'>$nometec</td>
                            <td style='white-space: nowrap;'>$cargotec</td>
                            <td style='white-space: nowrap;'>$placa</td> 
                            <td style='white-space: nowrap;'>R$ $saldocartao</td>
                            <td style='white-space: nowrap;'>$totalextra</td>    
                        </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php endif; ?>

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

    <script language="javascript">
        //função para formatar valor do input - moeda
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            i.value = v;
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</body>

</html>