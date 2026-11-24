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
    <title>Relatório Cotas Extra Descontadas Aceitas</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Cotas-Extra-Descontadas-Aceitas.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Valor Pedido</th>
                            <th>Valor Descontado</th>
                            <th>Data</th>
            </tr>";


                  $sql = "SELECT matricula, valdesc, idpedidotec, actec, dataac FROM bdfrota.tbcotaextraindevida WHERE actec = 1 and dataac >= '$diai 00:00:00' and dataac <= '$diaf 23:59:59' ORDER BY dataac DESC";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];
        $valdesc = $row[1];
        $idpedidotec = $row[2];
        $actec = $row[3];
        $dataac = $row[4];
        if ($actec == '1') {
          $actec = 'Sim';
        } else {
          $actec = 'Não';
        }

        $sqla = "SELECT valor FROM bdfrota.tbpedidostec where idtbpedidostec = '$idpedidotec'";
        $resultadoa = mysql_query($sqla) or die (mysql_error());
        $rowa = mysql_fetch_array($resultadoa);
        $valorpedido  = $rowa[0];

        $dataaceitef = '';
        if (!empty($dataac)) {
          $dataaceiteObj = date_create($dataac);
          if ($dataaceiteObj) {
            $dataaceitef = date_format($dataaceiteObj, 'd/m/Y H:i:s');
          }
        }

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
          <td>$mattec</td>
                <td>$nometec</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$nomesup</td>
          <td>$valorpedido</td>
          <td>$valdesc</td>
          <td>$dataaceitef</td>
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