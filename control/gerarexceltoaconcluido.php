<?php
session_start();
include("../conecta.php");
$usuariof = $_SESSION['usuario'];
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Detalhes Km Proj - KM Os</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Detalhes-kms.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                  <th>Nome</th>
                  <th>Supervisor</th>
                  <th>Orc. Semanal</th>
                  <th>Cota Extra</th>
                  <th>Valor Total</th>
                  <th>KM Proj.</th>
                  <th>KM Proj. Total</th>
                  <th>KM OS</th>
                  <th>Diferença</th>
                  <th>KM GPS</th>
            </tr>";
 <?php

                            
if (strftime('t') >= 21) {

    $mesini = date('m');
    $anoini = date('y');
    if (date('m') == 12) {
        $mesfim = 1;
        $anofim = date('y', strtotime('next year'));
    } else {
        $mesfim = date('m', strtotime('next month'));
        $anofim = date('y');
    }

} else {

    $mesfim = date('m');
    $anofim = date('y');
    if (date('m') == 1) {
        $mesini = 12;
        $anoini = date('y', strtotime('last year'));
    } else {
        $mesini = date('m', strtotime('last month'));
        $anoini = date('y');
    }

}

$diah = strftime('t');
$ontem = $diah - 1;

$dataini = "$anoini-$mesini-21";
$datafim = "$anofim-$mesfim-$ontem";

$datam1 = date("Y-m-d", strtotime("-1 day"));
$useg = date("Y-m-d", strtotime($datam1 . "last monday"));

if (strftime('%A') == "segunda") {

    $sql = "SELECT matricula, kmproj, kmosatual, kmproj-kmosatual as diferenca, valoraplicado, kmorcsem FROM bdfrota.tbsaldo where data >= '$useg' and data <= '$datam1' group by matricula order by diferenca desc";
    $resultado = mysql_query($sql) or die (mysql_error());

while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];
        $kmproj = round($row[1],2);
        $kmos = round($row[2],2);
        $diff = round($row[3],2);
        $valortotal = round($row[4],2);
        $kmorcsem = $row[5];
        
        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

        $servidor5="10.17.0.5";
        $usuario5="blvp";
        $senha5="@F3r7n6c2";
        $db5 = "bdfrota";
        $conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

        $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$useg 00:00:00' and datagps <= '$datam1 23:59:59' group by placa";
        $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
        $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $kmgps = $row5[0];

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
                  $html .="<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$placa</td>
              <td style='white-space:  nowrap;'>$kmproj</td>
              <td style='white-space:  nowrap;'>$kmos</td>
              <td style='white-space:  nowrap;'>$diff</td>
              <td style='white-space:  nowrap;'>$kmgps</td>
              <td style='white-space:  nowrap;'>R$ $valtot</td>
              </tr> ";
                }
} else {

    $domingo = date('Y-m-d', strtotime('last sunday'));
    $hoje = date('Y-m-d');

    $datetime1 = new DateTime($domingo);
    $datetime2 = new DateTime($hoje);
    $interval = $datetime1->diff($datetime2);
    $differenceInDays = $interval->format('%a');

    $useg = date('Y-m-d', strtotime('last sunday'));
    $datam1 = date("Y-m-d", strtotime("-1 day"));

    $sql = "SELECT matricula, kmproj, kmosatual, orcsemanal, totalextra, kmproj-kmosatual as diferenca, valoraplicado, kmorcsem FROM bdfrota.tbsaldo where data >= '$useg' and data <= '$datam1' group by matricula order by diferenca desc";
    $resultado = mysql_query($sql) or die (mysql_error());

    while ($row = mysql_fetch_array($resultado)) {
        
        $mattec = $row[0];
        $kmproj = $row[1];
        $kmos = $row[2];
        $diff = $kmproj - $kmos;
        $orcsem = $row[3];
        $totcot = $row[4];
        $valortotal = $row[6];
        $kmorcsem = $row[7];


        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

        $servidor5="10.17.0.5";
        $usuario5="blvp";
        $senha5="@F3r7n6c2";
        $db5 = "bdfrota";
        $conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

        $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$useg' and datagps <= '$datam1' group by placa";
        $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
        $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $kmgps = $row5[0];

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $idsup = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        $html .="<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td style='white-space:  nowrap;'>R$ $orcsem</td>
              <td style='white-space:  nowrap;'>R$ $totcot</td>
              <td style='white-space:  nowrap;'>R$ $valortotal</td>
              <td style='white-space:  nowrap;'>$kmorcsem</td>
              <td style='white-space:  nowrap;'>$kmproj</td>
              <td style='white-space:  nowrap;'>$kmos</td>
              <td style='white-space:  nowrap;'>$diff</td>
              <td style='white-space:  nowrap;'>$kmgps</td>
              </tr> ";
    }

}

    $html .= "</table>";

    
    // Configurações header para forçar o download
    header ("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
    header ("Content-Description: PHP Generated Data" );
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>