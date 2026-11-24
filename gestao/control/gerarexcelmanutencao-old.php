<?php

include("../../conecta2.php");

header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matricula'];
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Todas Manuntenções</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "rel-todas-manutencoes.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Placa </th>
            <th>Data de Agendamento </th>
            <th>Data de Cadastro </th>
            <th>Tipo </th>
            <th>Entrada na oficina </th>
            <th>Status </th>
            <th>Num. OS </th>                

        </tr>";

$sql="SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234';";

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
$idtbmanprev = $row['idtbmanprev'];
$placa = $row['placa'];
$data = $row['data'];//datacadastro
$tipo = $row['tipo'];
$dataagendamento = utf8_encode($row['dataagendamento']);
$status = $row['status'];
$hodometro = utf8_encode($row['hodometro']);
$statusveic = utf8_encode($row['statusveic']);

$atualizadoem = utf8_encode($row['atualizadoem']);
$solicitante = utf8_encode($row['solicitante']);
$etapa = utf8_encode($row['etapa']);
$dataocorrencia = $row['dataocorrencia'];
$modelo = $row['modelo'];
$Km = $row['Km'];
$fornman = $row['fornman'];
$descricao = utf8_encode($row['descricao']);
$ccusto = $row['ccusto'];
$condutor = $row['condutor'];

$oficina = $row['oficina'];
$dataagendamento = utf8_encode($row['dataagendamento']);
$prevsaida = utf8_encode($row['prevsaida']);
$dataentrada = utf8_encode($row['dataentrada']);
$dataretirada = $row['dataretirada'];
$tipopagamento = $row['tipopagamento'];
$reembolsoaprov = $row['reembolsoaprov'];
$valorreembolso = $row['valorreembolso'];
$valoroficina = utf8_encode($row['valoroficina']);
$valordesconto = $row['valordesconto'];
$valormaoobra = $row['valormaoobra'];
$valormaterial = $row['valormaterial'];
$valortransp = utf8_encode($row['valortransp']);
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
     $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
        $statusveic= utf8_encode($row1['status']);

    //nomecol
    $sql4="SELECT nome, cargo, codfilial, codempresa FROM bdaniel.tbfuncionario WHERE  matricula ='$amatcond' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
    $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        $enome=utf8_encode($row4['nome']);
        $ecargo= utf8_encode($row4['cargo']);
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

$html .="
    <tr>
        <th>$placa</th>
        <th>$dataagendamento</th>
        <th>$data</th>
        <th>$tipo </th>
        <th>$dataentrada</th>
        <th>$status </th>
        <th>$idtbmanprev </th>

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