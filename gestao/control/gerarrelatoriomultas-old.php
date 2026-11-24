<?php

include("../../conecta03.php");
include_once"../../func/log3.php";

$hoje=date('Y-m-d H:i:s');

header("Content-type: text/html; charset=utf-8");

$etapaf = $_POST['etapaf'];
/*$etapaf = $etapaf;

if(strpos($etapaf, 'CONCLU') !== false){
    $etapaf = 'CONCLUÍDO';
}*/
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Todas as Multas</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "rel-multas-$etapaf.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Placa</th>
            <th>Locadora</th>
            <th>Gravidade</th>
            <th>Nº Auto</th>
            <th>Data infração</th>
            <th>Apresentar condutor em</th>
            <th>Apresentar condutor à locadora em</th>
            <th>Situação</th>
            <th>Valor Total</th>
            <th>Valor do Desconto</th>
            <th>Taxa Administrativa</th>
            <th>Valor</th> 
            <!--<th>Status</th>-->
            <th>Trâmite</th>
            <th>Etapa</th>
            <th>Endereço da Infração</th>
            <th>Cidade da Infração</th>
            <th>Estado da Infração</th>
            <th>Órgão Emissor</th>
            <th>Descrição da Infração</th>
            <th>Condutor Indicado</th>
            <th>Matrícula Indicado</th>
            <th>CPF Indicado</th>
            <th>Centro de Custo do Indicado</th>
            <th>Descontado do condutor?</th>
            <th>Desconto a partir</th>

            <th>Parecer</th>
            <th>Parecer por</th>

            <th>Código da Infração</th>
            <th>Data Hora de Cadastro</th>
            <th>Pontos</th>

        </tr>";

    /*if($etapaf=='TODAS'){
        $sql="SELECT * FROM bdfrota.tbmulta;";
    }else{
        $sql="SELECT * FROM bdfrota.tbmulta WHERE etapa = '$etapaf';";      
    }  */
    if($etapaf=='TODAS'){
        $sql="SELECT * FROM bdfrota.tbmulta WHERE placa <> 'ABC1234' AND placa <> 'ABC1245' AND idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464',  '7269','7271','7273','7275', '6505');";
    }else{
        $sql="SELECT * FROM bdfrota.tbmulta WHERE etapa = '$etapaf' AND placa <> 'ABC1234' AND placa <> 'ABC1245' AND idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464',  '7269','7271','7273','7275', '6505');";        
    }
    //print $sql;
    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
     $idtbmulta= $row['idtbmulta'];
     $placa = $row['placa'];
     $idlocador = $row['fornecedor'];
     $gravidade = $row['gravidade'];
     $autoinfra = $row['autoinfracao'];
     $datainfracao = $row['datainfracao'];
     $datalimitecond = $row['datalimitecond'];
     $datalimiteloc = $row['datalimiteloc'];
     //situaçao é uma conta
     $etapa = $row['etapa'];
     $valortotal = $row['valor'];
     $valdesconto = $row['valdesconto'];
     $taxaadm = $row['taxaadm'];
     $valor = $row['valtotal'];
     $endereco = $row['endereco'];
     $municipio = $row['municipio'];
     $ufinfra = $row['ufinfra'];
     $orgao = $row['orgao'];
     $descricaoinfra = $row['descricaoinfra'];
     $ccusto =  $row['ccusto'];
     $pontos = $row['pontos'];
     $codigom = $row['codigom'];
     $datahoracadastro = $row['datahoracadastro'];

     /*$sql2="SELECT * FROM bdfrota.tbmovidatramite WHERE idmulta = '$idtbmulta' ";
     $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
     $linhas = mysqli_num_rows($resultado2);

     if($linhas<=0){*/
        $sql2="SELECT * FROM bdfrota.tbmovidatramite WHERE placa = '$placa' AND autoinfra='$autoinfra' ";
        $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
     //}

     $linhas2 = mysqli_num_rows($resultado2);

if($linhas2>0){

     $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $nome = $row2['nome'];
        $matricula = $row2['matricula'];
        $cpf = $row2['cpf'];
        $descontocol = $row2['descontocol'];
        $dtdesconto = $row2['dtdesconto'];
        $status = $row2['status'];
        $tramite = $row2['tramite'];
        $parecer = $row2['parecer'];
        $parecerpor = $row2['parecerpor'];
        $parecerdp = $row2['parecer'];
        $parecerpordp = $row2['parecerpor'];

        if(!empty($parecerdp)){
            $parecerf = $parecerdp;
            $parecerporf = $parecerpordp;
        }else{
            $parecerf = $parecer;
            $parecerporf = $parecerpor;
        }
    
    /*if(empty($ccusto)){*/
        $sql3 = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
        $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
            $ccusto = $row3['ccusto'];
    //}
    

    $sql4 = "SELECT fantasia, razaosocial FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
     $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        $fantasia = $row4['fantasia'];
        $razaosocial = $row4['razaosocial'];

    if($fantasia<>'' && $razaosocial==''){
        $locadora = $fantasia;
    } elseif($razaosocial<>'' && $fantasia==''){
        $locadora = $razaosocial;
    } elseif($fantasia<>'' && $razaosocial<>''){
        $locadora = $fantasia;
    } elseif($fantasia=='' && $razaosocial==''){
        $locadora = $fantasia;
    }

    $inicio = new DateTime($hoje);
    $apCondDV1 = new DateTime($datalimitecond);
  
    $dateInterval = $inicio->diff($apCondDV1);
    $intervalo = $dateInterval->format('%a dia(s)');

    $apCondDV2 = explode(" ", $datalimitecond);
    $apCondDV2 = $apCondDV2[0];             
    $apCondDV3 = explode("-", $apCondDV2);
    $apCondDV3 = $apCondDV3[2].'/'.$apCondDV3[1].'/'.$apCondDV3[0];

    if($apCondDV3 == '00/00/0000'){
        $aprescond= "<td> Indeterminado".".</td>";
    }elseif($hoje>$datalimitecond){
        $aprescond= "<td class='p-2 m-2' style='color:white; background-color: #733030; font-size: 14px;'> Prazo apresentar condutor venceu há $intervalo.</td>";
    } else{
        $aprescond= "<td style='color:white; background-color: #255573;'> Prazo apresentar condutor vence em $intervalo.</td>";
    }

    $sql5="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$parecerporf';";
    $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
    $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $nomecond = $row5['nome'];

    /*if($gravidade=='' && $pontos<>''){
        $sql6="SELECT gravidade FROM bdfrota.tbacodmulta where pontos='$pontos'; ";
        $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($conexao));
        $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
            $gravidade = $row6['gravidade'];
    }*/
     
    if($placa<>''){
        $html .="
        <tr>
            <td>$placa</td>
            <td>$locadora</td>
            <td>$gravidade</td>
            <td>'$autoinfra</td>
            <td>$datainfracao</td>
            <td>$datalimitecond</td>
            <td>$datalimiteloc</td>
            $aprescond
            <td>$valortotal</td>
            <td>$valdesconto</td>
            <td>$taxaadm</td>
            <td>$valor</td>
            <!--<td>$status</td>-->
            <td>$tramite</td>
            <td>$etapa</td>
            <td>$endereco</td>
            <td>$municipio</td>
            <td>$ufinfra</td>
            <td>'$orgao</td>
            <td>$descricaoinfra</td>
            <td>$nome</td>
            <td>'$matricula</td>
            <td>'$cpf</td>
            <td>'$ccusto</td>
            <td>$descontocol</td>
            <td>$dtdesconto</td>

            <td>$parecerf</td>
            <td>$nomecond</td>

            <td>$codigom</td>
            <td>$datahoracadastro</td>
            <td>$pontos</td>
        </tr>";
    }
    
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
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>