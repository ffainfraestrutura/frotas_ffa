<?php
include_once"../../func/log3.php";

include("../../conecta03.php");

header("Content-type: text/html; charset=utf-8");

?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Condutores Descontados</title>
  <head>
  <body>
    <?php

      // Definimos o nome do arquivo que será exportado
      $arquivo = "rel-condutores-descontados.xls";

      
      // Criamos uma tabela HTML com o formato da planilha
      $html = "
      <table border='1' class='table table-bordered'>
          <tr>
            <th>Nome</th>
            <th>Matricula</th>
            <th>Status do funcionário</th>
            <th>Placa do Veiculo</th> 
            <th>Gravidade</th>
            <th>Nº do Auto</th>
            <th>Data da Infração</th>
            <th>Vencimento</th>
            <th>Valor</th>             
          </tr>";
          
      $sql="SELECT dtcons, placa, gravidade, autoinfra, dtinfra, dtvenc, valor, nome, matricula FROM bdfrota.tbmovidatramite WHERE descontocol = 'sim' ;";
      
      $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
      
      while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
            
        $datac=$row['dtcons'];
        $placa=$row['placa'];
        $gravidade=utf8_encode($row['gravidade']);
        $autoinfra=$row['autoinfra'];
        $datainfra=$row['dtinfra'];
        $dtvenc=$row['dtvenc'];
        $valor=$row['valor'];
        $nome = utf8_encode($row['nome']);
        $matricula = $row['matricula'];

        $sql1 = "SELECT status FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
        $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
        $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
          $statuscond = $row1['status'];

        //tratando valor
        $valor=str_replace('.', ',', $valor);

        //if($matricula !=''){
          $html .="
          <tr>      
            <td>$nome</td>
            <td>$matricula</td>
            <td>$statuscond</td>
            <td>$placa</td>
            <td>$gravidade</td>
            <td>$autoinfra</td>
            <td>$datainfra</td>
            <td>$dtvenc</td>
            <td>R$ $valor</td>      
          </tr>";
        //}

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
      // Envia o conteúdo do arquivo*/
      echo $html;
      
      /*$log = new Log($con);
      $datahora = $hoje;
      $acao = 'extraiu relatorio';
      //$flag = '0';
      //$logresult = $log->enviarlog($datahora, $acao, '', '',$matricula, '', '');*/
    
    ?>
  </body>
</html>