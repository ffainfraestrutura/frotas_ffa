<?php
session_start();
include("../conecta.php");
$inicio = $_POST['inicio'];
$fim = $_POST['fim'];
if ($inicio == '' or $fim == '') {
  $inicio = date('Y-m-d');
  $fim = date('Y-m-d');
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
    <title>Contato</title>
  <head>
  <body>
    <?php
    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Teste-Final-$dia.xls";
    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
    <tr>
    <td colspan='4'>Relatorio Teste Final</td>
    </tr>
    <tr>
    <td colspan='4'>$dia</td>
    </tr>";

    
      $html .= "<tr  class='bg-primary'>
                  <th>DATA</th>
                  <th>REGIONAL</th>
                  <th>NUMERO_ORDEM</th>
                  <th>OPERADOR</th>
                  <th>SERVICO</th>
                  <th>PARÂMETROS</th>
                  <th>EVIDENCIA_MODEM</th>
                  <th>CONTATO_CLIENTE</th>
                  <th>TECNOLOGIA</th>
                  <th>TECNICO</th>
    </tr>";
    

                $sql = "SELECT a.datarg, a.regional, a.ordem, b.codagente, a.tipoatv, a.status, a.nummodem, a.numcli, a.tecnologia, a.nome_tec FROM bluecall.tbrtestefinal a, bluecall.agente b where a.id_agente = b.id_agente and a.datarg >= '$inicio' and a.datarg <= '$fim'";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                  $dataa = $row[0];
                  $dataa = explode(" ", $dataa);
                  $dias = explode("-", $dataa[0]);
                  $DATA = "$dias[2]".'/'."$dias[1]".'/'."$dias[0]";
                  $REGIONAL = $row[1];
                  $NUMERO_ORDEM = $row[2];
                  $OPERADOR = $row[3];
                  $nSERVICO = $row[4];
                  if ($nSERVICO == '0') {
                    $SERVICO = 'Instalação';
                  } else {
                    $SERVICO = 'Reparo';
                  }
                  $nSTATUS = $row[5];
                  if ($nSTATUS == '0') {
                    $STATUS = 'Parâmetros Dentro';
                  } else {
                    $STATUS = 'Parâmetros Fora';
                  }
                  $EVIDENCIA_MODEM = $row[6];
                  $CONTATO_CLIENTE = $row[7];
                  $nTECNOLOGIA = $row[8];
                  if ($nTECNOLOGIA == '1') {
                    $TECNOLOGIA = 'FTTC';
                  } elseif ($nTECNOLOGIA == '2')  {
                    $TECNOLOGIA = 'FTTH';
                  }
                  $TECNICO = $row[9];
                  
               $html .= "<tr>
<td>$DATA</td>
<td>$REGIONAL</td>
<td>$NUMERO_ORDEM</td>
<td>$OPERADOR</td>
<td>$SERVICO</td>
<td>$STATUS</td>
<td>$EVIDENCIA_MODEM</td>
<td>$CONTATO_CLIENTE</td>
<td>$TECNOLOGIA</td>
<td>$TECNICO</td>
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