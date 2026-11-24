<?php
session_start();
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
include("../conecta.php");


$matsup = $_POST["matsupf"];
$tiporelat = $_POST["tiporelat"];
$mattec = $_POST["mattecf"];

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

header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Saldo Atual</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Saldo-Atual.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Orc. Semanal</th>
                            <th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>Tot. Extra</th>
            </tr>";

if ($tiporelat == 'sup') {
   
 $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsupervisor";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmorcsem,0), ROUND(kmosatual,0), ROUND((valoraplicado - orcsemanal),2) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                        $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";

                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
if (mysql_num_rows($resultado2)>=1){
/*$servidor5="10.17.0.5";
$usuario5="blvp";
$senha5="@F3r7n6c2";
$db5 = "bdfrota";
$conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

$sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

$resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$kmsem = $row5[0];
if ($kmsem > 2500) {
    $kmsem = 0;
}*/

        $html .= "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        }
}

      }

} elseif ($tiporelat == 'todos') {
   
 $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor <> '0' and a.idtbsupervisor is not null";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmorcsem,0), ROUND(kmosatual,0), ROUND((valoraplicado - orcsemanal),2) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                         $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";
                         
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }


                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
if (mysql_num_rows($resultado2)>=1){
/*$servidor5="10.17.0.5";
$usuario5="blvp";
$senha5="@F3r7n6c2";
$db5 = "bdfrota";
$conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

$sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

$resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$kmsem = $row5[0];
if ($kmsem > 2500) {
    $kmsem = 0;
}*/

        $html .= "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        }}


      }

} elseif ($tiporelat == 'tec') {

   


    $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.matricula = '$mattec'";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, ROUND(kmorcsem,0), ROUND(kmosatual,0), totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$mattec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        if (mysql_num_rows($resultado2)>=1){
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $valdesc = $row2[6];

                         $sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac where data >= '$segunda 00:00:00'  and matricula = '$matriculatec'";

                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        if ($totalextra == '') {
                            $totalextra = 0;
                        }


                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];



        $html .= "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $totalextra</td>
               </tr>";

        
}

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