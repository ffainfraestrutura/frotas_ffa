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
    <title>GPS x Rota</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-gps-x-rota.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>KM OS</th>
                            <th>KM GPS</th>                            
                            <th>Diferença</th>
            </tr>";


        $sql = "SELECT * FROM bdfrota.tbofensoresgpstoa where data = '$diai' ORDER BY diferenca desc";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $id = $row[0];
        $mattec = $row[1];
        $placa = $row[2];
        $kmos = str_replace(".", ",", $row[3]);
        $kmgps = str_replace(".", ",", $row[4]);
        $diferenca = str_replace(".", ",", $row[5]);

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        $sql2 = "SELECT b.matricula FROM bdfrota.tbusuario a, bdfrota.tbsupervisor b where a.idtbsupervisor = b.idtbsupervisor and a.matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $matsup = $row2[0];

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$matsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];


       $html .= "<tr>
                <td>' $mattec</td>
                <td>$nometec</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$nomesup</td>
                <td>$kmos</td>
                <td>$kmgps</td>
                <td>$diferenca</td>
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