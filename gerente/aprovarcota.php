<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
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
} elseif ($perfil <> '3') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "../conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");
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

$sql2 = "SELECT valor, orcrecebido FROM bdfrota.tbgerente where matricula = '$matricula'";
$resultado2 = mysql_query($sql2) or die(mysql_error());
$row2 = mysql_fetch_array($resultado2);
$saldo = $row2[0];
$orccomp = $row2[1];
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
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <script language="javascript">
        function moeda(a, e, r, t) {
            let n = ""
                , h = j = 0
                , u = tamanho2 = 0
                , l = ajd2 = ""
                , o = window.Event ? t.which : t.keyCode;
            if (13 == o || 8 == o)
                return !0;
            if (n = String.fromCharCode(o),
                -1 == "0123456789".indexOf(n))
                return !1;
            for (u = a.value.length,
                h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
                ;
            for (l = ""; h < u; h++)
                -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
            if (l += n,
                0 == (u = l.length) && (a.value = ""),
                1 == u && (a.value = "0" + r + "0" + l),
                2 == u && (a.value = "0" + r + l),
                u > 2) {
                for (ajd2 = "",
                    j = 0,
                    h = u - 3; h >= 0; h--)
                    3 == j && (ajd2 += e,
                        j = 0),
                        ajd2 += l.charAt(h),
                        j++;
                for (a.value = "",
                    tamanho2 = ajd2.length,
                    h = tamanho2 - 1; h >= 0; h--)
                    a.value += ajd2.charAt(h);
                a.value += r + l.substr(u - 2, u)
            }
            return !1
        }
    </script>

</head>


<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">


                <h1 class="h1 pt-2 pb-2"> Aprovação de Cota </h1>
                <h6>Saldo Atual: <?= $saldo ?></h6>
                <!--div tabela - entrar dados do sistema-->
                <div id="containertab">
                    <table id="datatablesSimple" class="display" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th style="white-space: nowrap;">Nome</th>
                                <th>Placa</th>
                                <th>Sld. Cartão</th>
                                <th>Fd. Frota</th>
                                <th>Tot. Extra</th>
                                <th>Data e Hora</th>
                                <th>Justificativa</th>
                                <th>Solicitação</th>
                                <th>Novo Valor</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $sql = "SELECT 
                                        idtbgerente
                                    FROM
                                        bdfrota.tbgerente 
                                    WHERE
                                        matricula = $matricula";
                            $resultado = mysql_query($sql) or die(mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $idtbcoordenador = $row[0];

                            $sql = "SELECT a.matricula, a.valor, b.nome, b.codempresa FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbgerente = $idtbcoordenador";
                            $resultado = mysql_query($sql) or die(mysql_error());
                            while ($row = mysql_fetch_array($resultado)) {
                                $matriculasup = $row[0];
                                $saldosup = $row[1];
                                $saldosup = str_replace(".", ",", $saldosup);
                                $nome = $row[2];
                                $codempresa = $row[3];
                                if ($codempresa == 2) {
                                    $filial = 'SP';
                                } else {
                                    $filial = 'RJ';
                                }
                                $sql2 = "SELECT totcotaextra FROM bdfrota.tbcoord where matricula = '$matriculasup'";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());
                                $row2 = mysql_fetch_array($resultado2);
                                $orccomp = $row2[0];
                                if ($row2[0] == '' or $row2[0] == null) {
                                    $orccomp == '0,00';
                                }

                                $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidoscoordcota, data FROM bdfrota.tbpedidoscoordcota where matricula = '$matriculasup' and flag = 0";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());

                                if (mysql_num_rows($resultado2) > 0) {
                                    while ($row2 = mysql_fetch_array($resultado2)) {
                                        $justificativa = $row2[0];
                                        $novoorccomp = $row2[1];
                                        $novoorccomp = str_replace(".", ",", $novoorccomp);
                                        $id = $row2[2];
                                        $dataehora = $row2[3];

                                        $sqlPlaca = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculasup'";
                                        $resultadoPlaca = mysql_query($sqlPlaca) or die(mysql_error());
                                        $rowPlaca = mysql_fetch_array($resultadoPlaca);
                                        $placa = $rowPlaca[0];

                                        $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculasup' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=3; ";
                                        $resultado6 = mysql_query($sql6) or die(mysql_error());
                                        $row6 = mysql_fetch_array($resultado6);
                                        $ffrota = $row6[0];
                                        $ffrota = str_replace(".", ",", $ffrota);

                                        if ($codempresa == '2') {
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

                                                if ($statusCartao == 'A') {
                                                    $saldocartao = $itens->saldo;
                                                    $saldocartao = str_replace(".", ",", $saldocartao);
                                                    $limiteAtual = $itens->limite;
                                                    $limiteAtual = str_replace(".", ",", $limiteAtual);

                                                }

                                            }
                                            if ($saldocartao == '') {
                                                $saldocartao = 'Não encontrado';
                                                $limiteAtual = 'Não encontrado';
                                            }
                                        } else {
                                            $curl = curl_init();
                                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_POST, TRUE);
                                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                                'Content-Type: application/json',
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
                                        print "                        
                                    <tr>
                                        <td>$matriculasup</td>
                                        <td style=\"white-space: nowrap;\">$nome</td>
                                        <td>$placa</td>
                                        <td>R$ $saldocartao</td>
                                        <td>R$ $ffrota</td>
                                        <td>R$ $orccomp</td>
                                        <td>$dataehora</td>
                                        <td style='font-size: 9px;'>$justificativa</td>
                                        <td>R$ $novoorccomp</td>
                                        <td style='min-width: 250px;'>
                                            <div style='display: flex; align-items: center; gap: 5px;'>
                                            
                                            <form action='control/aprovarcota.php' method='post' style='flex: 1; margin: 0;'>
                                    
                                                <div class='input-group' style='margin-bottom: 0;'>             
                                                    <input type='text' 
                                                           class='form-control' 
                                                           style='min-width: 150px;'
                                                           placeholder='Vazio aprova R$ $novoorccomp ou insira novo valor' 
                                                           aria-label='Valor a aprovar' 
                                                           aria-describedby='button-addon2' 
                                                           name='valor' 
                                                           onkeyup='formatarMoeda(this);'>
                                                    <button class='btn btn-outline-success' 
                                                            type='submit' 
                                                            id='button-addon2'
                                                            title='Aprovar pedido (com ou sem alteração de valor)'
                                                            data-bs-toggle='tooltip'
                                                            data-bs-placement='top'
                                                            style='border-left: 0;'>
                                                        <span class='material-symbols-outlined'>done</span>
                                                    </button>
                                                </div>
                                                <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                                                <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                <input type='hidden' name='decisao' value='2'>
                                                <input type='hidden' name='id' value='$id'>
                                                <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                <input type='hidden' name='valor_ant' value='$saldocartao'>
                                                <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                                <input class='dataplantao' type='hidden' name='dataplantao' value ='extra'>
                                            </form>

                                            <form action='control/aprovarcota.php' method='post' style='margin: 0;'>
                                                <button class='btn btn-outline-danger' 
                                                        type='submit'
                                                        title='Negar pedido de cota'
                                                        data-bs-toggle='tooltip'
                                                        data-bs-placement='top'
                                                        style='height: 38px; display: flex; align-items: center; justify-content: center;'>
                                                    <span class='material-symbols-outlined' style='font-size: 20px;'>close</span>
                                                </button> 
                                                <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                <input type='hidden' name='decisao' value='1'>
                                                <input type='hidden' name='id' value='$id'>
                                                <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                            </form>

                                            </div>
                                        </td>";


                                    }


                                }
                            }

                            ?>

                        </tbody>
                    </table>
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

    </div>

    <script>
        //função data table para criar barra de rolagem
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
                scrollX: true,
            });
            
            // Inicializar tooltips do Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

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
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</body>

</html>