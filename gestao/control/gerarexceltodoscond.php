<?php
include_once"../../func/log3.php";

include("../../conecta03.php");

header("Content-type: text/html; charset=utf-8");

$unidadef = $_POST['unidadef'];

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
          <th>Status como condutor </th>
          <th>Placa</th>
          <th>Data de associação</th>
          <th>Data dissociação</th>             
        </tr>";

    if($unidadef ==='TODOS'){
      //$sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE placaassoc<>'ABC1234' AND matricula<>'003535' /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/;";
     
      $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE placaassoc<>'ABC1234' AND matricula<>'003535' AND placaassoc<>'ABC1245' /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/;";

    }else{
      //$sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE matricula<>'003535' AND placaassoc<>'ABC1234' AND placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef') /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/; ";

      $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE matricula<>'003535' AND placaassoc<>'ABC1234' AND placaassoc<>'ABC1245'  AND placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef')  /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/; ";
    }

    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
      $matcond = $row['matricula'];

      $sql2="SELECT MAX(idtbcondutor) AS id FROM bdfrota.tbcondutor WHERE matricula='$matcond' /*AND ativo=1*/; ";
      $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
      $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $id = $row2['id'];
                
      //pegando os dados do ultimo id ativo
      $sql3 = "SELECT * FROM bdfrota.tbcondutor WHERE  idtbcondutor = '$id' ";
      $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
      $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
        $idtbcondutor = $row3['idtbcondutor'];
        //$nomecond = utf8_encode($row3['nome']);
        $datadissoc = $row3['datadissoc'];
        $dataassoc = $row3['dataassoc'];

        $placaassoc = $row3['placaassoc'];
        $ativo = $row3['ativo'];

      $sql4 = "SELECT nome, status, ccusto FROM bdaniel.tbfuncionario WHERE  matricula = '$matcond' ";
      $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
      $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        /*$nomecond = utf8_encode($row4['nome']);
        $statusfunc = utf8_encode($row4['status']);
        $ccusto= utf8_encode($row4['ccusto']);*/
        $nomecond = $row4['nome'];
        $statusfunc = $row4['status'];
        $ccusto= $row4['ccusto'];

      //tratando ativo
      if($ativo == 1){
        $ativo1='Ativo';
      } elseif($ativo==0){
        $ativo1= 'Não ativo';
      }

      if($matcond !='' && $nomecond !=''){
        $html .="
          <tr>
            <td> '$matcond </td>
            <td> $nomecond </td>
            <td> $statusfunc </td>
            <td> $ccusto </td>
            <td> $ativo1 </td>
            <td> $placaassoc </td>
            <td> $dataassoc </td>
            <td> $datadissoc </td>
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