<?php
session_start();
include("../conecta.php");
$dia = $_POST['dia'];
if ($dia == '') {
  $dia = date('Y-m-d');
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
    $arquivo = "Relatorio-agendamento-$dia.xls";
    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
    <tr>
    <td colspan='4'>Relatorio Agendamento</td>
    </tr>
    <tr>
    <td colspan='4'>$dia</td>
    </tr>";

    
      $html .= "<tr  class='bg-primary'>
                  <th>OS</th>
                  <th>OPERADOR</th>
                  <th>RAMAL</th>
                  <th>CLIENTE</th>
                  <th>TELEFONE</th>
                  <th>STATUS</th>
                  <th>DATA AGENDAMENTO</th>
                  <th>PERIODO</th>
                  <th>PONTO REFERENCIA</th>
                  <th>OBS</th>
                  <th>CONFERIU PLANO?</th>
    </tr>";
    
                $sql = "SELECT a.numordem, a.contato, a.telefone1, a.status, a.dataagendamento, a.periodo, a.ptref, a.obs, a.confplan, a.ramal, b.id_agente FROM bluecall.tbagendamento a, bluecall.agente b where a.id_agente = b.id_agente and a.data = '$dia'";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                  $dataa = $row[4];
                  $dataa = explode(" ", $dataa);
                  $dias = explode("-", $dataa[0]);
                  $DATA = "$dias[2]".'/'."$dias[1]".'/'."$dias[0]";
                  $CLIENTE = $row[1];
                  $TELEFONE = $row[2];
                  $STATUS = $row[3];
                  $OS = $row[0];
                  $PERIODO = $row[5];
                  $PONTO = $row[6];
                  $OBS = $row[7];
                  $CONFERIUp = $row[8];
                  if ($CONFERIUp == '1') {
                    $CONFERIU = 'Sim';
                  } elseif ($CONFERIUp == '2')  {
                    $CONFERIU = 'Não';
                  } else {
                    $CONFERIU = 'Não Informado';
                  }
                  $RAMAL = $row[9];
                  $OPERADOR = $row[10];
               $html .= "<tr>
                  <td>$OS</td>
                  <td>$OPERADOR</td>
                  <td>$RAMAL</td>
                  <td>$CLIENTE</td>
                  <td>$TELEFONE</td>
                  <td>$STATUS</td>
                  <td>$DATA</td>
                  <td>$PERIODO</td>
                  <td>$PONTO</td>
                  <td>$OBS</td>
                  <td>$CONFERIU</td>
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