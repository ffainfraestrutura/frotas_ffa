<?php

include("../../conecta2.php");

header("Content-type: text/html; charset=utf-8");

$hoje = date('Y-m-d');

$matr_autor = $_POST['matricula'];

?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Farol Manutenção</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "relatorio-farol-manutencao.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
          <th>Placa </th>
          <th>Condutor </th>
          <th>Centro de Custo </th>                
          <th>Última Manutenção </th>
          <th>Últ. Man. Hodometro </th>
          <th>Hodômetro Atual </th>
          <th>Locadora </th>
          <th>Dev. para locadora </th>
          <th>Status </th>
          <th>Obs. últ. Manutenção </th>             
        </tr>";

$sql = "SELECT placa, matcond, hodometro, idlocador, dtdisponivelloc, dtdevolucaoloc FROM bdfrota.tbveiculo WHERE placa <> 'ABC1234' AND placa<>'' AND statusvel NOT IN (5,13,18) AND status='1' AND tipoposse<>'AGREGADO' AND dtdevolucaoloc>'$hoje' ;";

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
  $placa=$row['placa'];
  //$hodometro=$row['hodometro'];
  $matcond = $row['matcond'];
  $idlocador = $row['idlocador'];
  $dtdevolucaoloc = $row['dtdevolucaoloc'];

  $sql1="SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond';";
  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    $nomecond = utf8_encode($row1['nome']);
    $ccusto = utf8_encode($row1['ccusto']);

  $sql1a = "SELECT max(idtbpedidostec) as idtbpedidostec FROM bdfrota.tbpedidostec WHERE matricula='$matcond';";
  $resultado1a = mysqli_query($conexao, $sql1a) or die(mysqli_error($conexao));
  $row1a = mysqli_fetch_array($resultado1a, MYSQLI_BOTH);
    $idtbpedidostec = utf8_encode($row1a['idtbpedidostec']);

  $sql1b = "SELECT kmhodometro FROM bdfrota.tbpedidostec WHERE idtbpedidostec='$idtbpedidostec';";
  $resultado1b = mysqli_query($conexao, $sql1b) or die(mysqli_error($conexao));
  $row1b = mysqli_fetch_array($resultado1b, MYSQLI_BOTH);
    $hodometro = utf8_encode($row1b['kmhodometro']);


  $sql2="SELECT max(idtbmanprev) as idtbmanprev FROM bdfrota.tbmanprev WHERE placa='$placa' AND status='CONCLUIDO' ";
  $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
    $idtbmanprev = $row2['idtbmanprev'];

  $sql3 = "SELECT * FROM bdfrota.tbmanprev WHERE idtbmanprev='$idtbmanprev' ";
  $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
  $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
    $dataocorrencia = $row3['dataocorrencia'];
    $hodometroman = $row3['hodometro'];
    $obsultman = utf8_encode($row3['observacao']); 

  $sql4="SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
  $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
    $fantasia = utf8_encode($row4['fantasia']);

  $dif = $hodometro - $hodometroman;

  if($dif>=11000){
    $status="3-PERDEU A MANUTENÇÃO";
    $style = "style='background-color: red;' ";
  }elseif($dif<11000 && $dif>=9000){
    $status="1-MANUTENÇÃO";
    $style = "style='background-color: yellow;' ";
  }elseif($dif<9000){
    $status="2-EM INTERVALO";
    $style = "style='background-color: green;' ";
  }

  $html .="
  <tr $style>
    <td> $placa </td>                  
    <td> $nomecond </td>
    <td> $ccusto </td>
    <td> $dataocorrencia </td>
    <td> $hodometroman km</td>
    <td> $hodometro km</td>
    <td> $fantasia </td>
    <td> $dtdevolucaoloc </td>
    <td> $status </td>
    <td> $obsultman </td>
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
    // Envia o conteúdo do arquivo*/
    echo $html;

$datahora = $hoje;
$acao = 'extraiu relatorio';
//$flag = '0';
$logresult = enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>