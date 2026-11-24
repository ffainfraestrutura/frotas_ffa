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
    <title>Cota Extra Aprovada</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Cota-Extra-Aprovada.xls";


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
                            <th>KM GPS</th>
                            <th>Sld. Cartão</th>
                            <th>Tot. Extra</th>
                            <th>Data e Hora</th>
                            <th>Justificativa</th>
                            <th>Valor Pedido</th>
                            <th>Valor Inserido</th>
                            <th>Data Aceito</th>
                            <th>Tipo Cota</th>
            </tr>";

if ($matsup == 'ss') {

   $sql = "SELECT matricula, orcsemanal, kmproj, kmos, gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, tipo_cota FROM bdfrota.tbcotaextraac where dataaceite >= '$diai 00:00:00' and dataaceite <= '$diaf 23:39:59' order by data";
 } else {   


 $sql = "SELECT matricula, orcsemanal, kmproj, kmos, gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, tipo_cota FROM bdfrota.tbcotaextraac where matsup = '$matsup' and dataaceite >= '$diai 00:00:00' and dataaceite <= '$diaf 23:39:59' order by data";
}

      $resultado = mysql_query($sql) or die (mysql_error());
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];
        $orcsemanal = $row[1];
        $kmproj = $row[2];
        $kmos = $row[3];
        $gps = $row[4];
        $sldcartao = $row[5];
        $totalextra = $row[6];
        $data = $row[7];
        $justificativa = $row[8];
        $valor = $row[9];
        $valorinserido = $row[10];
        $dataaceite = $row[11];
        $tipo_cota = $row[12];
        if ($tipo_cota == 1) {
           $tipo_cota = 'Emergencial';
        } else {
            $tipo_cota = "Extra";
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

                  $html .="<tr>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$orcsemanal</td>
                <td>$kmproj</td>
                <td>$kmos</td>
                <td>$gps</td>
                <td>$sldcartao</td>
                <td>$totalextra</td>
                <td>$data</td>
                <td>$justificativa</td>
                <td>$valor</td>
                <td>$valorinserido</td>
                <td>$dataaceite</td>
                <td>$tipo_cota</td>
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