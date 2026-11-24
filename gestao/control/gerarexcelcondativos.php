<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date("Y-m-d");

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

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
     $idtbcondutor = $row['idtbcondutor'];
                //$nomecond = utf8_encode($row['nome']); 
                $nomecond = $row['nome'];                
                $matricula = $row['matricula'];
                $dataassoc = $row['dataassoc'];
                //$datadissoc = $row['datadissoc'];
                $placaassoc = $row['placaassoc'];

                $sql2="SELECT validade FROM bdfrota.tbcnh WHERE matricula='$matricula' ";
                $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $validadeCNH = $row2['validade'];

                $sql5="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE matricula='$matricula' AND (tipo='checklist' OR tipo='cadastro' OR tipo='edição'); ";
                
                //$sql5="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE (matricula='$matricula' OR mat_autor='$matricula') AND (tipo='checklist' OR tipo='cadastro' OR tipo='edição'); ";
                $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                  $idtblog1=$row5['idtblog'];

                $sql6="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog1' ";
                $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
                $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                  //$acao1= utf8_encode($row6['acao']);
                  $acao1= $row6['acao'];
                  $autor1=$row6['mat_autor'];
                  $datamov=$row6['data_e_hora'];

                if(strpos($acao1, "-1") !== false){
                  $acao1 = str_replace("-1", "", $acao1);
                }

                if(strpos($acao1, "-2") !== false){
                  $acao1 = str_replace("-2", "", $acao1);
                }

                if(strpos($acao1, "-3") !== false){
                  $acao1 = str_replace("-3", "", $acao1);
                }

                //seleciona nome autor da ult modificação
                if($autor1=='003535' || $autor1=='003427'){
                  $autor1 = 'EQUIPE TI';
                }else{
                  $sql7="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor1'; ";
                  $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
                  $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
                    $autor1=$row7['nome'];
                }

                if(!empty($datamov)){
                  $datamov1 = new DateTime($datamov);
                  $datamovf = $datamov1-> format('d/m/Y');
                }else{
                  $datamovf = "";
                }

                //tratando data
                $dataassoc1 = new DateTime($dataassoc);
                $datafim = $dataassoc1-> format('d/m/Y');

                //tratando validadeCNH
                $validadeCNH1 = new DateTime($validadeCNH);
                $validadefim = $validadeCNH1-> format('d/m/Y');

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

                $sql3="SELECT status, matcond FROM bdfrota.tbveiculo WHERE placa='$placaassoc' ";
                $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                  $status = $row3['status'];
                  $matcond = $row3['matcond'];

                $sql4="SELECT ccusto, nome, status FROM bdaniel.tbfuncionario WHERE matricula='$matricula'";
                $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
                $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                  /*$ccusto = utf8_encode($row4['ccusto']);
                  $nomefunc = utf8_encode($row4['nome']);
                  $statusani = utf8_encode($row4['status']);*/
                  $ccusto = $row4['ccusto'];
                  $nomefunc = $row4['nome'];
                  $statusani = $row4['status'];

                if($nomecond==''){
                  $nomefinal = $nomefunc;
                }else{
                  $nomefinal = $nomecond;
                }

if($matricula !=''){
$html .="
<tr>
  <td>'$matricula</td>                  
  <td>$nomefinal</td>
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
$log = new Log($con);
$datahora = $hoje;
$acao = 'extraiu relatorio';
//$flag = '0';
$logresult = $log->enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>