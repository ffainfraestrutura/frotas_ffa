<?php

include("../../conecta03.php");

header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matricula'];
$datainicial = $_POST['datainicial'];
$datafinal = $_POST['datafinal'];
$tipofim = $_POST['tipofim'];
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
    $arquivo = "rel-manutencoes-abertas.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Num. OS </th>
            <th>Protocolo </th>

            <th>Placa </th>
            <th>Data de Agendamento </th>
            <th>Data de Cadastro </th>
            <th>Tipo </th>
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

            <th>Valor oficina - Valor de reembolso</th>
        </tr>";

/*$sql="SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status<>'CONCLUIDO';";*/
if(!empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal' AND tipo='$tipofim' ;";
  }elseif(!empty($datainicial) && !empty($datafinal) && empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal';";
  }elseif(!empty($datainicial) && empty($datafinal) && empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial';";

  }elseif(!empty($datainicial) && empty($datafinal) && !empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial'  AND tipo='$tipofim' ;";
  }elseif(empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' AND tipo='$tipofim' ;";

  }elseif(empty($datainicial) && !empty($datafinal) && empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' ;";

  }elseif(empty($datainicial) && empty($datafinal) && !empty($tipofim)){
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND tipo='$tipofim' ;";

  }else{
    $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO';";

  }

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
    //$observacao = utf8_encode($row['observacao']);
    $observacao = $row['observacao'];

    //valor oficina real
    $oficinareal = $valoroficina - $valorreembolso;

    $sql1="SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$statusveic' ";
    $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
        $statusveic= utf8_encode($row1['status']);

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
    switch ($tipo) {
        case 'MP':
            $tipo='Manutenção Preventiva';
            break;

        case 'OS':
            $tipo='Ordem de Serviço';
            break;
            
        case 'MC':
            $tipo='Manutenção Corretiva';
            break;

        case 'SS':
            $tipo='Sinistro';
            break;  
        
        default:
            // code...
            break;
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
        <td>$tipo </td>
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

        <td>$oficinareal</td>

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