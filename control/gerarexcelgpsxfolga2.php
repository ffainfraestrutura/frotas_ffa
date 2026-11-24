<?php
session_start();
include("../conecta.php");


$diai = $_POST["diai"];
$diaf = $_POST["diaf"];

$matsup = $_POST["matsupf"];

header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>GPS x Folga</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-GPS-x-Folga.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Filial</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Dia</th>
                            <th>KM Período</th>
            </tr>";

$timestampInicio = strtotime($diai);
$timestampFim = strtotime($diaf);
$diferencaSegundos = $timestampFim - $timestampInicio;
$diferencaDias = $diferencaSegundos / 86400; 

if ($matsup == 'ss') {

    $sql = "SELECT matricula, placa, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT matricula, placa, DATE(data) as dia, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$diai 00:00:00' AND data < '$diaf 23:59:59'
  GROUP BY matricula, dia
) as subconsulta
GROUP BY matricula
HAVING soma_kmtotal > 5";
    

   

} else {

    $sqls = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultados = mysql_query($sqls) or die (mysql_error());
    $rows = mysql_fetch_array($resultados);
    $idtbsupervisor = $rows[0];

    $sql = "SELECT subconsulta.matricula, subconsulta.placa, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT a.matricula, a.placa, DATE(a.data) as dia, ROUND(MAX(a.kmtotal) - MIN(a.kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsfalta a, bdfrota.tbusuario b
  WHERE a.matricula = b.matricula
    AND b.idtbsupervisor = '$idtbsupervisor'
    AND a.data > '$diai 00:00:00'
    AND a.data < '$diaf 23:39:59'
  GROUP BY a.matricula, a.placa, dia
) as subconsulta
GROUP BY subconsulta.matricula, subconsulta.placa
HAVING soma_kmtotal > 5";
    


}

      $resultado = mysql_query($sql) or die (mysql_error());
      while ($row = mysql_fetch_array($resultado)){

        $matriculaf = $row[0];
        $placa = $row[1];
        $kmtotal = $row[2];


        if ($kmtotal <> '0.00' and $kmtotal < ($diferencaDias * 500.00)) {
        
        $sql2 = "SELECT DATE(data) as dia
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$diai 00:00:00' AND data < '$diaf 23:39:59' and matricula = '$matriculaf'
  GROUP BY matricula, dia";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $dia = "";
        while ($row2 = mysql_fetch_array($resultado2)) {
            $dia .= $row2[0]." - ";
        }
        

        $sql2 = "SELECT nome, codempresa, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nome = $row2[0];
        if ($row2[1] == '2') {
            $filial = 'SP';
        } else {
            $filial = 'RJ';
        }
        $ccusto = utf8_encode($row2[2]);
  
                  $html .="<tr>
                <td>$matriculaf</td>
                <td>$filial</td>
                <td>$nome</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$dia</td>
                <td>$kmtotal</td>
               </tr>";
                }}

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