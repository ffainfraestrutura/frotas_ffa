<?php

include("../../conecta03.php");
include_once"../../func/log3.php";


header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matricula'];

$datainicial = $_POST['datain'];
$datafinal = $_POST['datafi'];
$tipo = $_POST['tipofim'];

?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Manuntenções Concluídas</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "relatorio-manutencoes-concluidas.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Num. OS </th>
            <th>Protocolo </th>

            <th>Placa </th>
            <th>Data de Agendamento </th>
            <th>Data de Cadastro </th>
            <th>Data Conclusão </th>

            <th>Tipo </th>
            <th>Hodometro cadastrado </th>
            <th>Entrada na oficina </th>
            <th>Status </th>
            
            <th>Oficina</th>
            <th>Centro Custo</th>
            <th>Descrição</th>
            <th>Previsão de saída</th>
            <th>Data de retirada</th>
            <th>Reembolso aprovado</th>
            <th>Valor oficina</th>
            <th>Valor mão de obra</th>
            <th>Valor material</th>
            <th>Observação</th>
            <th>Descontado do condutor</th>
        </tr>";

//$sql="SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND placa<>'ABC1234'  AND placa<>'' AND status<>'ABERTO';";

if(!empty($datainicial) && !empty($datafinal)){
    if(!empty($tipo)){
        $sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND placa<>'ABC1234' AND placa<>'' AND status<>'ABERTO' AND data>='$datainicial' AND data<='$datafinal' AND tipo='$tipo' ;";

    }else{
        $sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND placa<>'ABC1234' AND placa<>'' AND status<>'ABERTO' AND data>='$datainicial' AND data<='$datafinal' ;";

    }
}/*else{
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND placa<>'ABC1234'  AND placa<>'' AND status<>'ABERTO' ;";
}*/

//print $sql;
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
$idtbmanprev = $row['idtbmanprev'];
$placa = $row['placa'];
$data = $row['data'];//datacadastro
$tipo = $row['tipo'];
//$dataagendamento = utf8_encode($row['dataagendamento']);

$dataagendamento = $row['dataagendamento'];
$status = $row['status'];
/*$hodometro = utf8_encode($row['hodometro']);
$statusveic = utf8_encode($row['statusveic']);

$atualizadoem = utf8_encode($row['atualizadoem']);
$solicitante = utf8_encode($row['solicitante']);
$etapa = utf8_encode($row['etapa']);*/

$hodometro = $row['hodometro'];
$statusveic = $row['statusveic'];

$atualizadoem = $row['atualizadoem'];
$solicitante = $row['solicitante'];
$etapa = $row['etapa'];

$dataocorrencia = $row['dataocorrencia'];
$modelo = $row['modelo'];
$Km = $row['Km'];
$fornman = $row['fornman'];
//$descricao = utf8_encode($row['descricao']);

$descricao = $row['descricao'];
$ccusto = $row['ccusto'];
$condutor = $row['condutor'];

$oficina = $row['oficina'];

/*$dataagendamento = utf8_encode($row['dataagendamento']);
$prevsaida = utf8_encode($row['prevsaida']);
$dataentrada = utf8_encode($row['dataentrada']);*/

$dataagendamento = $row['dataagendamento'];
$prevsaida = $row['prevsaida'];
$dataentrada = $row['dataentrada'];


$dataretirada = $row['dataretirada'];
$tipopagamento = $row['tipopagamento'];
$reembolsoaprov = $row['reembolsoaprov'];
$valorreembolso = $row['valorreembolso'];
//$valoroficina = utf8_encode($row['valoroficina']);

$valoroficina = $row['valoroficina'];

$valordesconto = $row['valordesconto'];
$valormaoobra = $row['valormaoobra'];
$valormaterial = $row['valormaterial'];
//$valortransp = utf8_encode($row['valortransp']);

$valortransp = $row['valortransp'];

$outrosvalor = $row['outrosvalor'];
$descontarcond = $row['descontarcond'];
$datavencimento = $row['datavencimento'];
$datapagamento = $row['datapagamento'];
$formapagam = $row['formapagam'];
$condicaopag = $row['condicaopag'];
$numparc = $row['numparc'];
$valorparcela = $row['valorparcela'];
$dataprimparc = $row['dataprimparc'];
$protocolo = $row['protocolo'];
$dataconclusao = $row['dataconclusao'];
$placaanterior = $row['placaanterior'];
$observacao = $row['observacao'];

 $sql1="SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$statusveic' ";
     $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    //$statusveic= utf8_encode($row1['status']);
    $statusveic= $row1['status'];

    //nomecol
    $sql4="SELECT nome, cargo, codfilial, codempresa FROM bdaniel.tbfuncionario WHERE  matricula ='$amatcond' ";
    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
    $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        /*$enome=utf8_encode($row4['nome']);
        $ecargo= utf8_encode($row4['cargo']);*/
        $enome=$row4['nome'];
        $ecargo= $row4['cargo'];
        $codfilial = $row4['codfilial'];
        $codempresa  = $row4['codempresa'];


    //tratando tipo
    if($tipo=='MP'){
      $tipo='Manutenção Preventiva';
    } elseif($tipo=='OS'){
      $tipo='Ordem de Serviço';
    } elseif($tipo=='MC'){
      $tipo='Manutenção Corretiva';
    } elseif($tipo=='SS'){
      $tipo='Sinistro';
    }

    if($status == 1){
        $status =  'ABERTO';
    }elseif($status==2){
        $status =  'CONCLUIDO';
    }

    if($reembolsoaprov == 0){
        $reembolsoaprov =  'NÃO';
    }elseif($reembolsoaprov==1){
        $reembolsoaprov =  'SIM';
    }

    if($descontarcond == 0){
        $descontarcond =  'NÃO';
    }elseif($descontarcond==1){
        $reembolsoaprov =  'SIM';
    }

$html .="
    <tr>
        <td>$idtbmanprev </td>
        <td>'$protocolo </td>

        <td>$placa</td>
        <td>$dataagendamento</td>
        <td>$data</td>
        <td>$dataconclusao</td>

        <td>$tipo </td>
        <td>$hodometro </td>

        <td>$dataentrada</td>
        <td>$status </td>
        
        <td>$oficina</td>
        <td>$ccusto</td>
        <td>$descricao</td>
        <td>$prevsaida</td>
        <td>$dataretirada</td>
        <td>$reembolsoaprov</td>
        <td>$valoroficina</td>
        <td>$valormaoobra</td>
        <td>$valormaterial</td>
        <td>$observacao</td>
        <td>$descontarcond</td>

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

$log = new Log($con);
$datahora = $hoje;
$acao = 'extraiu relatorio';
//$flag = '0';
$logresult = $log->enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>