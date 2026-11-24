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

    $sql = "SELECT matricula, placa, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal, DATE(data) as dia
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$diai 00:00:00' AND data < '$diaf 23:59:59' and kmtotal <> 0
  GROUP BY matricula, dia
  HAVING kmtotal > 5
  order by dia";
    

   

} else {

    $sqls = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matsup'";
    $resultados = mysql_query($sqls) or die (mysql_error());
    $rows = mysql_fetch_array($resultados);
    $idtbsupervisor = $rows[0];

    $sql = "
  SELECT a.matricula, a.placa, ROUND(MAX(a.kmtotal) - MIN(a.kmtotal), 2) as kmtotal, DATE(a.data) as dia
  FROM bdfrota.tbrelatoriogpsfalta a, bdfrota.tbusuario b
  WHERE a.matricula = b.matricula
    AND b.idtbsupervisor = '$idtbsupervisor'
    AND a.data > '$diai 00:00:00'
    AND a.data < '$diaf 23:39:59' and kmtotal <> 0
  GROUP BY matricula, dia
  HAVING kmtotal > 5
  order by dia";
    


}

      $resultado = mysql_query($sql) or die (mysql_error());
      while ($row = mysql_fetch_array($resultado)){

        $matriculaf = $row[0];
        $placa = $row[1];
        $kmtotal = $row[2];
        $dia = $row[3];

        if ($kmtotal <> '0.00' and $kmtotal < ($diferencaDias * 500.00)) {
        

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
                <td>'$matriculaf</td>
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