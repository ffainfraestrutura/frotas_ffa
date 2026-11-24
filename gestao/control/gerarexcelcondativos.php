<?php

include("../../conecta2.php");

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
    <title>Relatório Condutores Ativos</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "rel-cond-ativos.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Placa</th>
            <th>Associado em</th>
            <th>Venc. CNH</th>
            <th>Status CNH</th> 
            <th>Centro de Custo</th>             
        </tr>";

if($unidadef ==='TODOS'){
  $sql="SELECT * FROM bdfrota.tbcondutor WHERE ativo=1 AND matricula<>'003535';";
}else{
  $sql="SELECT * FROM bdfrota.tbcondutor WHERE ativo=1 AND matricula<>'003535' AND placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef'); ";
}

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
        
    $idtbcondutor = $row['idtbcondutor'];
    $nome = $row['nome'];                
    $matricula = $row['matricula'];
    $dataassoc = $row['dataassoc'];
    //$datadissoc = $row['datadissoc'];
    $placaassoc = $row['placaassoc'];

    $sql2="SELECT validade FROM bdfrota.tbcnh WHERE matricula='$matricula' ";
    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $validadeCNH = $row2['validade'];

    $inicio = new DateTime($hoje);
    $validadeCNH2 = new DateTime($validadeCNH);
    
    $dateInterval = $inicio->diff($validadeCNH2);
    $intervalo = $dateInterval->format('%a');

    $periodo = date("Y-m-d H:i:s", strtotime($validadeCNH . "-30 days"));

    if($hoje>=$validadeCNH){
      $title = "title='Carteira vencida.' ";
      $style = "style='color: red;'";
      $statuscnh = 'Carteira vencida.';

      $style2 = "style='background-color: #d98a85' ";

    } elseif($validadeCNH>$hoje && $hoje>=$periodo) {
      $style = "style='color: #000000;'";
      $statuscnh ='Data de vencimento em menos de 30 dias.';

      $style2 = "style='background-color: #f6ffc7' ";
    }elseif($periodo>$hoje){
      $style = "style='color: blue;'";
      $statuscnh ='Carteira dentro do período de validade.';

      $style2 = "style='background-color: #ffffff;' ";
    }

    $sql4="SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matricula' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
    $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
      $ccusto = utf8_encode($row4['ccusto']);

if($matricula !=''){
$html .="
<tr>
  <td>'$matricula</td>                  
  <td>$nome</td>
  <td>$placaassoc</td>
  <td>$dataassoc</td>
  <td>$validadeCNH</td>
  <td>$statuscnh</td>
  <td>$ccusto</td>
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

$datahora = $hoje;
$acao = 'extraiu relatorio';
//$flag = '0';
$logresult = enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>