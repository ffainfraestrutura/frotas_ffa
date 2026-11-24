<?php
session_start();
include("../conecta.php");


$diai = $_POST["diai"];
$diaf = $_POST["diaf"];
$matriculaf = $_POST["matriculaf"];
$nometec = $_POST["nometec"];
$placatec = $_POST["placatec"];
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
    $arquivo = "Relatorio-GPS-x-Folga-Detalhado.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Placa</th>
                            <th>Endereço</th>
                            <th>KMs</th>
                            <th>Data e Hora</th>
            </tr>";


     
    $sql = "SELECT endereco, kmtotal, data FROM bdfrota.tbrelatoriogpsfalta where data > '$diai 00:00:00' and data < '$diaf 23:39:59' and matricula = '$matriculaf' order by data";
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $endereco = $row[0];
        $kmtotal = $row[1];
        $datagps = $row[2];

                  $html .="<tr>
                <td>$matriculaf</td>
                <td>$nometec</td>
                <td>$placatec</td>
                <td>$endereco</td>
                <td>$kmtotal</td>
                <td>$datagps</td>
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