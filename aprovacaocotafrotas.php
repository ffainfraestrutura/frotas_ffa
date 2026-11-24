<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

// Lista de matrículas autorizadas
$matriculas_autorizadas = array('601000','601004', '004607', '086272');

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"telainicio.php\"</script>";
} elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"telainicio.php\"</script>";
} elseif ($perfil == '4' and !in_array($matricula, $matriculas_autorizadas)) {
    echo "<script language='javascript' type='text/javascript'>alert('Sua matrícula não tem permissão para acessar esta tela');window.location=\"telainicio.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "conecta.php";
include_once "./func/log.php";
include_once "./func/consultacartao.php";
header("Content-type: text/html; charset=utf-8");

$data = date('Y-m-d');
$useg = date('Y-m-d', strtotime($data . 'last monday'));

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
    <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

    <!--data tables - css e js-->
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
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2"> Aprovar Cota Coordenador </h1>

                <!--div tabela - entrar dados do sistema-->
                <div id="containertab">
                    <table id="datatablesSimple" class="display" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th style="white-space: nowrap;">Nome</th>
                                <th>Placa</th>
                                <th>Sld. Cartão</th>
                                <th>Sld. Remanejamento</th>
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
                                        p.matricula,
                                        p.justificativa,
                                        ROUND(p.valor,2) AS novoorccomp,
                                        p.idtbpedidoscoordcota,
                                        p.data,

                                        c.valor              AS saldocoord,
                                        c.totcotaextra,

                                        f.nome,
                                        f.codempresa,

                                        v.placa,

                                        COALESCE(rp.remanejpositivo,0)  AS remanejpositivo,
                                        COALESCE(rn.remanejnegativo,0)  AS remanejnegativo,
                                        COALESCE(ff.ffrota,0)           AS ffrota

                                    FROM bdfrota.tbpedidoscoordcota p

                                    JOIN bdfrota.tbcoord c 
                                        ON p.matricula = c.matricula

                                    JOIN bdaniel.tbfuncionario f 
                                        ON p.matricula = f.matricula

                                    LEFT JOIN bdfrota.tbveiculo v 
                                        ON v.matcond = p.matricula

                                    -- Remanejamento positivo
                                    LEFT JOIN (
                                        SELECT matr_autor, SUM(valor) AS remanejpositivo
                                        FROM bdfrota.tbremanejamento
                                        WHERE tipo = 1
                                        AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59'
                                        GROUP BY matr_autor
                                    ) rp 
                                        ON rp.matr_autor = p.matricula

                                    -- Remanejamento negativo
                                    LEFT JOIN (
                                        SELECT matr_autor, SUM(valor) AS remanejnegativo
                                        FROM bdfrota.tbremanejamento
                                        WHERE tipo = 0
                                        AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59'
                                        GROUP BY matr_autor
                                    ) rn 
                                        ON rn.matr_autor = p.matricula

                                    -- FFrota
                                    LEFT JOIN (
                                        SELECT matricula, SUM(valor) AS ffrota
                                        FROM bdfrota.tbremanejamento
                                        WHERE tipo = 1 
                                        AND perfil_autor = 4
                                        AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59'
                                        GROUP BY matricula
                                    ) ff 
                                        ON ff.matricula = p.matricula

                                    WHERE p.flag = 0
                                    ";
                            $resultado = mysql_query($sql) or die(mysql_error());
                            while ($row = mysql_fetch_assoc($resultado)) {

                                $matriculacoord = $row['matricula'];
                                $saldocoord = str_replace(".", ",", $row['saldo_coord']);
                                $nome = $row['nome'];
                                $codempresa = $row['codempresa'];
                                $filial = ($codempresa == 2) ? 'SP' : 'RJ';

                                $orccomp = $row['totcotaextra'] ?? 0;
                                $justificativa = $row['justificativa'];
                                $novoorccomp = str_replace(".", ",", $row['novoorccomp']);
                                $placa = $row['placa'];

                                $remanejpositivo = $row['remanejpositivo'];
                                $remanejnegativo = $row['remanejnegativo'];

                                $saldoremanej = $remanejpositivo - $remanejnegativo;

                                $ffrota = str_replace(".", ",", $row['ffrota']);

                                $saldocartao = consultacartao($codempresa, $placa);

                                print "                        
                                            <tr>
                                                <td>$matriculacoord</td>
                                                <td style=\"white-space: nowrap;\">$nome</td>
                                                <td>$placa</td>
                                                <td>$saldocartao</td>
                                                <td>R$ $sldremanejamento</td>
                                                <td>R$ $fdfrota</td>
                                                <td>R$ $orccomp</td>
                                                <td>$dataehora</td>
                                                <td style='font-size: 9px;'>$justificativa</td>
                                                <td>R$ $novoorccomp</td>
                                                <td class='d-flex align-items-start'>
                                                    <div class=\"btn-group\">
                                                        <form action='./control/aprovacaocotafrotas.php' method='post'>
                                                            <div class='input-group mb-3'>             
                                                                <input type='text' class='form-control' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor' onkeyup='formatarMoeda(this);'>
                                                                <div class='input-group-append'>
                                                                    <button class='btn  btn-outline-success' type='submit' id='button-addon2'><span class='material-symbols-outlined'>done</span></button>
                                                                </div>
                                                            </div>
                                                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                                                            <input type='hidden' name='matriculasup' value='$matriculacoord'>
                                                            <input type='hidden' name='decisao' value='2'>
                                                            <input type='hidden' name='id' value='$id'>
                                                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                            <input type='hidden' name='valor_ant' value='$saldocoord'>
                                                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                                            <input class='dataplantao' type='hidden' name='dataplantao' value ='extra'>
                                                        </form>

                                                        <form action='./control/aprovacaocotafrotas.php' method='post'>
                                                            <button class='btn btn-outline-danger'>
                                                                <span class='material-symbols-outlined'>close</span>
                                                            </button> 
                                                            <input type='hidden' name='matriculasup' value='$matriculacoord'>
                                                            <input type='hidden' name='decisao' value='1'>
                                                            <input type='hidden' name='id' value='$id'>
                                                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>";


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