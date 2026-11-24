<?php
setlocale(LC_ALL, 'pt_BR.utf8');
setlocale(LC_TIME, 'pt_BR');
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

$diai = isset($_POST["diai"]) ? $_POST["diai"] : '';
$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : '';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : '';
$empresafi = isset($_POST["empresa"]) ? $_POST["empresa"] : '';
$coordenadorfi = isset($_POST["coordenador"]) ? $_POST["coordenador"] : '';
$matsupfi = isset($_POST["matsupf"]) ? $_POST["matsupf"] : '';
$mattecfi = isset($_POST["mattecf"]) ? $_POST["mattecf"] : '';


$datetime = new DateTime($diai);

// Define o dia da semana para domingo (0 é domingo, 1 é segunda-feira, e assim por diante)
$datetime->setISODate($datetime->format('Y'), $datetime->format('W'), 7);

// Formate a data para 'Y-m-d' (ou o formato desejado)
$diaf = $datetime->format('Y-m-d');
$datasem = new DateTime($diai);
if (strftime('%A') == "domingo" ){
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}

include"conecta.php";
include_once"./func/log.php";
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
        <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Apuração Semanal </h1>
<form action="./relsaldoatualnovo2.php" method="post">


     <div class="row">
<div class="col-md-1">
 <h5><b>Selecione o Periodo:</b></h5>
 </div>
<div class="col-md-6">
      <select class="form-select mb-3" name="diai">
                <option value=" "> Selecione a data </option>

            <?php
            
            $hoje = time(); // Obtém a data e hora atual
            $dia_da_semana = date('w', $hoje); // Obtém o dia da semana (0 é domingo, 1 é segunda-feira, 2 é terça-feira, etc.)

            if ($dia_da_semana == 1) {
                $segunda_atual = $hoje; // Hoje é uma segunda-feira
            } else {
                $segunda_atual = strtotime('last Monday', $hoje); // Caso contrário, obtenha a última segunda-feira
            }

            $contador = 0;

            while ($contador < 4) {
                $dataval = date('Y-m-d', $segunda_atual);
                
                $dataFormatada = date('d/m/Y', strtotime($dataval));
                print"<option value='$dataval'>$dataFormatada</option>";

                $segunda_atual = strtotime('-7 days', $segunda_atual); // Subtrai 7 dias para a próxima segunda-feira
                $contador++;
            }
 
            ?>

            </select>
    </div>

   

        <div class="col-md-3">
            <input type="submit" class="btn btn-success form-control mb-3"  value="Filtrar">
        </div>


        <!--</form>
    </div>-->
</div>
      </form>      

        <div class="card mt-2 mb-2">
            <div class="card-header">
                <a href="./dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard
                </a href="./dashboard.php">
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='example' class="display table table-bordered" width="100%">
                    <thead>
<?php

                        
                        $datetimesem = new DateTime($diai);
                        
                        $nomesDiasSemana = array(
                            'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'
                        );

                        $coresDia = array(
                            '#ED8E89', '#F7B685', '#F3EBA5', '#94C691', '#98D694', '#b6c8fc', '#c9bfe8'
                        );

                        echo '<tr style="font-size: 20px;">';
                        echo '<th class="text-center" colspan="8"></th>';

                        for ($i = 0; $i < 7; $i++) {
                            $diaFormatado = $datetimesem->format('l, d/m/Y');
                            $nomeDia = $nomesDiasSemana[$datetimesem->format('w')];
                            $corDia = $coresDia[$i];
                            echo '<th class="text-center" colspan="7" style="background-color: ' . $corDia . ';">' . $nomeDia . ', ' . $datetimesem->format('d/m/Y') . '</th>';
                            $datetimesem->modify('+1 day');
                        }

                        echo '<th class="text-center" colspan="8" style="background-color: #dbdbdb;">Acumulado Semana</th>';
                        echo '<th class="text-center" colspan="3"></th>';
                        echo '</tr>';

?>
                        <tr style="font-size: 11px;">
                            <th>UF</th>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orçamento Semanal</th>
                            <th>KM Projetado</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor a Descontar</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Projeção Semanal</th>
                            <th>Diferença</th>
                            <th>Variação</th>

                        </tr>
                        <tr style="font-size: 11px;">
                            <th>UF</th>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orçamento Semanal</th>
                            <th>KM Projetado</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Desconto</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Projeção Semanal</th>
                            <th>Diferença</th>
                            <th>Variação</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php     

if ($diai == '' or $diaf == '') {    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
              
               </tr>";
} else {

$sql = "SELECT matricula FROM bdfrota.tbsaldosemanal WHERE dia BETWEEN '$diai' AND '$diaf' GROUP BY matricula order by Matricula";

$resultado = mysql_query($sql) or die(mysql_error());

while ($row = mysql_fetch_array($resultado)) {
    $mattec = $row[0];

    // Consulta para tabela b
    $sql_b = "SELECT estado, nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$mattec'";
    $resultado_b = mysql_query($sql_b) or die(mysql_error());
    $row_b = mysql_fetch_array($resultado_b);

    // Consulta para tabela c
    $sql_c = "SELECT placa FROM bdfrota.tbveiculo WHERE matcond = '$mattec'";
    $resultado_c = mysql_query($sql_c) or die(mysql_error());
    $row_c = mysql_fetch_array($resultado_c);

    // Consulta para tabela d
    $sql_d = "SELECT idtbsupervisor FROM bdfrota.tbusuario WHERE matricula = '$mattec'";
    $resultado_d = mysql_query($sql_d) or die(mysql_error());
    $row_d = mysql_fetch_array($resultado_d);

    // Consulta para tabela e
    $sql_e = "SELECT ROUND(orcsemanal,0) as orcsemanal, ROUND(kmorcsem,0) as kmorcsem FROM bdfrota.tbsaldo WHERE matricula = '$mattec' and data = '$diai'";
    //echo "$sql_e <br>";
    $resultado_e = mysql_query($sql_e) or die(mysql_error());
    $row_e = mysql_fetch_array($resultado_e);

    // Criar variáveis para cada tabela
    $estado = $row_b['estado'];
    $nome = utf8_encode($row_b['nome']);
    $ccusto = utf8_encode($row_b['ccusto']);
    $placa = $row_c['placa'];
    $idtbsupervisor = $row_d['idtbsupervisor'];
    $orcsemanal = $row_e['orcsemanal'];
    $kmorcsem = $row_e['kmorcsem'];

    $sql_f = "SELECT a.nome FROM bdaniel.tbfuncionario a, bdfrota.tbsupervisor b WHERE a.matricula = b.matricula and b.idtbsupervisor = '$idtbsupervisor'";
    $resultado_f = mysql_query($sql_f) or die(mysql_error());
    $row_f = mysql_fetch_array($resultado_f);
    $nomesup = $row_f[0];

    if ($placa <> '') {
        print "<tr style='font-size: 10px;'>
                            <td>$estado</td>
                            <td>$mattec</td>
                            <td>$nome</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>$nomesup</td>
                            <td>$orcsemanal</td>
                            <td>$kmorcsem</td>";

        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diai' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diai . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT ROUND(sldinicial,2), kmproj, kmos, ctxdevida, ctxindevida, valremanejado, ROUND(valgasto,2), ROUND(sldfinal,2) FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

        $sql2 = "SELECT ROUND(SUM(kmos),2), ROUND(SUM(ctxdevida),2), ROUND(SUM(ctxindevida),2), ROUND(SUM(valremanejado),2), ROUND(SUM(valgasto),2) FROM bdfrota.tbsaldosemanal WHERE dia BETWEEN '$diai' AND '$diaf' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $saldofinalsemanal = round($orcsemanal + $row2[1] + $row2[2] - $row2[3] - $row2[4],2);

        $sql3 = "SELECT valorlitro FROM bdfrota.tbabastecimento WHERE data < '$diaf' and matricula = '$mattec' and valorlitro > 5";
        $resultado3 = mysql_query($sql3) or die(mysql_error());
        if (mysql_num_rows($resultado3) < 1) {
            $vallitro = 5.60;
        } else {
            $row3 = mysql_fetch_array($resultado3);
            $vallitro = $row3[0];
        }

        $sql3 = "SELECT * FROM bdfrota.tbveiculo where matcond = '$mattec' and tipovel in ('2','5')";
        $resultado3 = mysql_query($sql3) or die(mysql_error());
        if (mysql_num_rows($resultado3) < 1) {
            $kmlitro = 11;
        } else {
            $kmlitro = 8;
        }
        

        $valkmos =  round((($row2[0] / $vallitro ) * $kmlitro),2);

        $valdesc = $row2[4] - $valkmos;

        if ($valdesc <= 15) {
            $valdesc = 0;
        }

        print "             <td>$orcsemanal</td>
                            <td>$kmorcsem</td>
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$valdesc</td>
                            <td>$row2[4]</td>
                            <td>$saldofinalsemanal</td>";

               $variacao = ROUND($row2[4] / $orcsemanal * 100,2);
        print "             <td>$orcsemanal</td>
                            <td>$row2[4]</td>
                            <td>$variacao%</td>
                            
               </tr>";
        
    }
            

}
}

      ?>
                        
                    </tbody>
                </table>
                <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <button class="form-control form-control-md btn btn-success" onclick="exportarParaXLSX()">Exportar para Excel</button>
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


</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>



<!--script para criar data table-->
<script>
    //função iniciar data table 
new DataTable('#example', {
    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
    "pageLength": 50,
    initComplete: function () {
        this.api()
            .columns()
            .every(function () {
                let column = this;
 
                // Create select element
                let select = document.createElement('select');
                select.add(new Option(''));
                column.header().replaceChildren(select);
 
                // Apply listener for user change in value
                select.addEventListener('change', function () {
                    var val = DataTable.util.escapeRegex(select.value);
 
                    column
                        .search(val ? '^' + val + '$' : '', true, false)
                        .draw();
                });
 
                // Add list of options
                column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d, j) {
                        select.add(new Option(d));
                    });
            });
    }
});
</script>

  <script>
    function exportarParaXLSX() {
      /* Obtenha os dados da tabela */
      var tabela = document.getElementById('example');
      var dados = XLSX.utils.table_to_sheet(tabela);

      /* Crie um objeto de pasta de trabalho e adicione a folha de dados a ela */
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, dados, 'Planilha1');

      /* Salve o arquivo XLSX */
      XLSX.writeFile(wb, 'rel-apuracao-sem.xlsx');
    }
  </script>

</html>