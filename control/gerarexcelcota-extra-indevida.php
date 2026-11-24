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
    <title>Cota Extra Indevida</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Cota-Extra-Indevida.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orc. Inicial</th>
                            <th>Orc. Atual</th>
                            <th>Km Proj. Inicial</th>
                            <th>Km Proj. Atual</th>
                            <th>KM OS</th>
                            <th>Sld. Cartão</th>
                            <!--<th>Valor Retirado</th>
                            <th>Tot. Extra</th>-->
                            <th style='width: 30%;'>Data Pedido</th>
                            <th>Justificativa Técnico</th>
                            <th>Valor Pedido</th>
                            <th>Valor Inserido</th>
                            <th>Just. Supervisor</th>
                            <th>Data Aceito</th>
            </tr>";


        $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmproj,0), ROUND(kmos,0), gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, matsup, justsup, qtdgasta, qtdretirada, ROUND(kmprojatual,0), ROUND(orcsematual,2), id FROM bdfrota.tbcotaextraac where dataaceite > '$diai 00:00:00' and dataaceite < '$diaf 23:39:59' and kmproj > 0 and cota_validade = '1' order by data";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];
        $orcsemanal = $row[1];
        $kmproj = $row[2];
        $kmos = $row[3];
        $sldcartao = $row[5];
        $totalextra = $row[6];
        $data = $row[7];
        $justificativa = $row[8];
        $valor = $row[9];
        $valorinserido = $row[10];
        $dataaceite = $row[11];
        $matsup = $row[12];
        $justsup = $row[13];
        $qtdgasta = $row[14];
        $qtdretirada = $row[15];
        $kmprojatual = $row[16];
        $orcsematual = $row[17];
        $id = $row[18];

        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

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
                <td>$orcsemanal</td>
                <td>$orcsematual</td>
                <td>$kmproj</td>
                <td>$kmprojatual</td>
                <td>$kmos</td>
                <td>$sldcartao</td>
                <!--<td>$qtdretirada</td>
                <td>$totalextra</td>-->
                <td>$data</td>
                <td>$justificativa</td>
                <td>$valor</td>
                <td>$valorinserido</td>
                <td>$justsup</td>
                <td>$dataaceite</td>
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