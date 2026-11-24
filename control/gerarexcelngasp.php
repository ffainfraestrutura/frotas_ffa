<?php
session_start();
include("../conecta.php");
header("Content-type: text/html; charset=utf-8");

?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Contato</title>
  <head>
  <body>
    <?php
    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-NGASP.xls";
    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
    <tr>
    <td colspan='4'>Relatorio NGASP</td>
    </tr>";

    
      $html .= "<tr  class='bg-primary'>
                  <th>Numero Ordem</th>
                  <th>Ordem de Serviço</th>
                  <th>Status</th>
                  <th>Data</th>
    </tr>";
    

                $sql = "SELECT numero_ordem, ordem_servico, status, dtatu FROM bdimport.bdngaspm";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                $numeroordem = $row['numero_ordem'];
                $ordemservico = $row['ordem_servico'];
                $tipoatv = $row['status'];
                if ($tipoatv == '0') {
                  $tipoatvs = "Sem Resposta";
                } elseif ($tipoatv == 'OK') {
                  $tipoatvs = "OK";
                } else {
                  $tipoatvs = "Não OK";
                }
                $data = $row['dtatu'];
                  
               $html .= "<tr>
<td>$numeroordem</td>
<td>$ordemservico</td>
<td>$tipoatvs</td>
<td>$data</td>
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