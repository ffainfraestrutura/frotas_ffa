<?php
session_start();
include("../conecta.php");
$usuariof = $_SESSION['usuario'];
$mesi = $_POST["mesi"];
$mesf = $_POST["mesf"];
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>TOA x Aniel Claro</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-TOAxAniel-Claro.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                <th>Nome</th>
                <th>Data Aniel</th>
                <th>Inicio Aniel</th>
                <th>Fim Aniel</th>
                <th>Total Aniel</th>
                <th>Data TOA</th>
                <th>Inicio TOA</th>
                <th>Fim TOA</th>
                <th>Total TOA</th>
                <th>Dif TOA x Aniel</th>
            </tr>";
                $sql = "SELECT * FROM bdaniel.tbtoaxanielclaro where dataaniel >= '2022-".$mesi."-20' and dataaniel <= '2022-".$mesf."-21'";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                  $row = array_map("utf8_decode", $row);
                  $nomeaniel = $row[1];
                  $dataaniel = $row[2];
                  $dataaniel = explode(" ", $dataaniel);
                  $cdtaniel = $dataaniel[0];
                  $inicioaniel = $row[3];
                  $inicioaniel = explode(" ", $inicioaniel);
                  $cinianiel = $inicioaniel[1];
                  $fimaniel = $row[4];
                  $fimaniel = explode(" ", $fimaniel);
                  $cfimaniel = $fimaniel[1];
                  $diffaniel = $row[5];
                  $diffaniel = explode("-", $diffaniel);
                  $difaniel = $diffaniel[1];
                  $nometoa = $row[6];
                  $datatoa = $row[7];
                  $datatoa = explode(" ", $datatoa);
                  $cdttoa = $datatoa[0];
                  $iniciotoa = $row[8];
                  $fimtoa = $row[9];
                  $difftoa = $row[10];
                  $difftoa = explode("-", $difftoa);
                  $diftoa = $difftoa[1];
                  $diftoaaniel = $row[11];
                  $html .="<tr>
                    <td>$nomeaniel</td>
                    <td>$cdtaniel</td>
                    <td>$cinianiel</td> 
                    <td>$cfimaniel</td>
                    <td>$difaniel</td>
                    <td>$cdttoa</td>
                    <td>$iniciotoa</td>
                    <td>$fimtoa</td>
                    <td>$diftoa</td>
                    <td>$diftoaaniel</td>
                    </tr>";
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