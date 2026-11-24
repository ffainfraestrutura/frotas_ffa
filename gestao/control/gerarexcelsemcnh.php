<?php
include_once"../../func/log3.php";

include("../../conecta03.php");

header("Content-type: text/html; charset=utf-8");

//$unidadef = $_POST['unidadef'];

?>

<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Todos Condutores</title>
  <head>
  <body>
  <?php
    // Definimos o nome do arquivo que será exportado
    $arquivo = "rel-todos-condutores-$unidadef.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
          <th>Matrícula </th>
          <th>Nome </th>
          <th>Situação do Funcionário </th>
          <th>Centro de Custo </th>             
        </tr>";

    //$sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcnh WHERE matricula<>'003535' AND doc1='' AND doc2='';";
    $sql="SELECT DISTINCT(matricula) AS matricula FROM bdfrota.tbcnh WHERE matricula<>'003535' AND (doc1='' OR doc1 IS NULL);";

    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
      $matcond = $row['matricula'];

      $sql1="SELECT nome, ccusto, status FROM bdaniel.tbfuncionario WHERE matricula='$matcond'; ";
      $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
      $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
        $nomecond = $row1['nome'];
        $ccusto = $row1['ccusto'];
        $statusfunc = $row1['status'];
      

      if($matcond !=''){
        $html .="
          <tr>
            <td> '$matcond </td>
            <td> $nomecond </td>
            <td> $statusfunc </td>
            <td> $ccusto </td>
          </tr>";
      }

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
    $logresult = $log->enviarlog($datahora, $acao, '', '',$matricula, '', '');*/
    
  ?>
  </body>
</html>