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
    <title>Relatório Memorando</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-memorando.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Km</th>
            </tr>";

  $sql = "SELECT t1.matricula, t1.data, t1.dir, t1.kmhodometro, t1.valor, t1.placa
FROM bdfrota.tbpedidostec t1
JOIN (
    SELECT matricula, MAX(idtbpedidostec) as max_id
    FROM bdfrota.tbpedidostec where matricula <> ''
    GROUP BY matricula
) t2 ON t1.matricula = t2.matricula AND t1.idtbpedidostec = t2.max_id
ORDER BY t1.data desc;";
   
  
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
       $dataaceite = $row[1];
       $doc = $row[2];
       $kmh = $row[3];
       $valor = $row[4];
       $placassoc = $row[5];

       $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
       $resultado2 = mysql_query($sql2) or die (mysql_error());
       $row2 = mysql_fetch_array($resultado2);
        $nometec = utf8_encode($row2[0]);
        $ccusto = utf8_encode($row2[1]);

        if(empty($placassoc)){
          $sql3 = "SELECT placaassoc FROM bdfrota.tbcondutor where dataassoc <= '$dataaceite' AND datadissoc >= '$dataaceite' AND matricula = '$mattec'; ";
          $resultado3 = mysql_query($sql3) or die (mysql_error());
          $row3 = mysql_fetch_array($resultado3);
            $placassoc = $row3[0];      
        }

        $html .="<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$ccusto</td>
              <td>$placassoc</td>
              <td>$dataaceite</td>
              <td>R$ $valor</td>
              <td>$kmh</td>
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