<?php

include("../../conecta03.php");
include_once "../../func/log3.php";

$hoje = date('Y-m-d H:i:s');

header("Content-type: text/html; charset=utf-8");

$etapaf = $_POST['etapaf'];
$data_inicial = $_POST['data_inicialf'];
$data_final = $_POST['data_finalf'];
?>
<!--*** gera excel*-->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Relatório Todas as Multas</title>

    <head>

    <body>
        <?php

        // Definimos o nome do arquivo que será exportado
        $arquivo = "rel-multas-$etapaf-$data_inicial-$data_final.xls";

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
                <th>Valor</th>
                <th>Valor do Desconto</th>
                <th>Taxa Administrativa</th>
                <th>Valor Total</th>
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

        if ($etapaf == 'TODAS') {
            $sql = "SELECT a.idtbmulta, a.etapa, a.datalimitecond, a.placa, a.autoinfracao, a.descricaoinfra, a.codigom, a.orgao, a.datalimiteloc, a.valor, a.valdesconto, a.taxaadm, a.valtotal, a.endereco, a.municipio, a.ufinfra, a.pontos, b.idtbmovidatramite, b.placa, b.autoinfra, b.dtinfra, b.dtvenc, b.tramite, b.gravidade, b.apCondDV, b.status, DATEDIFF(b.apCondDV , CURDATE()) as diferenca, b.idmulta, b.status, b.locadora, b.dtcons, b.nome, b.matricula, b.parecer, b.parecerpor, b.descontocol, b.dtdesconto FROM bdfrota.tbmulta a
                LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa AND a.autoinfracao = b.autoinfra
                WHERE  a.placa <> 'ABC1234' AND a.placa<>'ABC1245' AND a.idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464', '7269','7271','7273','7275', '6505') AND b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59';";
        } else {
            $sql = "SELECT a.idtbmulta, a.etapa, a.datalimitecond, a.placa, a.autoinfracao, a.descricaoinfra, a.codigom, a.orgao, a.datalimiteloc, a.valor, a.valdesconto, a.taxaadm, a.valtotal, a.endereco, a.municipio, a.ufinfra, a.pontos, b.idtbmovidatramite, b.placa, b.autoinfra, b.dtinfra, b.dtvenc, b.tramite, b.gravidade, b.apCondDV, b.status, DATEDIFF(b.apCondDV , CURDATE()) as diferenca, b.idmulta, b.locadora, b.dtcons, b.nome, b.matricula, b.parecer, b.parecerpor, b.descontocol, b.dtdesconto FROM bdfrota.tbmulta a
                LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa AND a.autoinfracao = b.autoinfra
                WHERE  a.placa <> 'ABC1234' AND a.placa<>'ABC1245' AND a.idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464', '7269','7271','7273','7275', '6505') AND b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59' AND etapa = '$etapaf';";
        }

        $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
        while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
            $idtbmulta = $row['idtbmulta'];
            $etapa1 = $row['etapa'];
            $datalimitecond = $row['datalimitecond'];
            $placa = $row['placa'];
            $autoinfracao = $row['autoinfracao'];
            $descricaoinfra = $row['descricaoinfra'];
            $codigom = $row['codigom'];
            $orgao = $row['orgao'];

            $valor = $row['valor'];
            $valdesconto = $row['valdesconto'];
            $taxaadm = $row['taxaadm'];
            $valor_total = $row['valtotal'];

            $endereco = $row['endereco'];
            $municipio = $row['municipio'];
            $ufinfra = $row['ufinfra'];

            $pontos = $row['pontos'];

            $idtbmovidatramite = $row['idtbmovidatramite'];
            $gravidade = $row['gravidade'];
            $apCondDV = $row['apCondDV'];
            $datainfra = $row['dtinfra'];
            $dtvenc = $row['dtvenc'];
            $tramite = $row['tramite'];
            $status = $row['status'];

            $idlocador = $row['locadora'];
            $dtcons = $row['dtcons'];
            $nomecond = $row['nome'];
            $matriculacond = $row['matricula'];

            $parecerf = $row['parecer'];
            $parecerpor = $row['parecerpor'];
            $dtdesconto = $row['dtdesconto'];
            $descontocol = $row['descontocol'];

            $datalimiteloc = $row['datalimiteloc'];

            $sql3 = "SELECT ccusto, cpf FROM bdaniel.tbfuncionario WHERE matricula='$matriculacond';";
            $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
            $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
            $ccustocond = $row3['ccusto'];
            $cpfcond = $row3['cpf'];

            if ($idlocador == "") {
                $sql4 = "SELECT idlocador FROM bdfrota.tbveiculo WHERE placa ='$placa';";
                $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
                $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                $idlocador = $row4['idlocador'];
            }

            $sql4 = "SELECT fantasia, razaosocial FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
            $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
            $fantasia = $row4['fantasia'];
            $razaosocial = $row4['razaosocial'];


            $datainfra_view = DateTime::createFromFormat('Y-m-d H:i:s', $datainfra)->format('d/m/Y H:i:s');

            $datalimiteloc_view = DateTime::createFromFormat('Y-m-d H:i:s', $datalimiteloc)->format('d/m/Y H:i:s');

            $datahoracadastro = DateTime::createFromFormat('Y-m-d H:i:s', $dtcons)->format('d/m/Y H:i:s');

            $inicio = new DateTime($hoje);
            $apCondDV1 = new DateTime($datalimitecond);

            $dateInterval = $inicio->diff($apCondDV1);
            $intervalo = $dateInterval->format('%a dia(s)');

            $apCondDV2 = explode(" ", $datalimitecond);
            $apCondDV2 = $apCondDV2[0];
            $apCondDV3 = explode("-", $apCondDV2);
            $apCondDV3 = $apCondDV3[2] . '/' . $apCondDV3[1] . '/' . $apCondDV3[0];

            if ($apCondDV3 == '00/00/0000') {
                $aprescond = "<td> Indeterminado" . ".</td>";
            } elseif ($hoje > $datalimitecond) {
                $aprescond = "<td class='p-2 m-2' style='color:white; background-color: #733030; font-size: 14px;'> Prazo apresentar condutor venceu há $intervalo.</td>";
            } else {
                $aprescond = "<td style='color:white; background-color: #255573;'> Prazo apresentar condutor vence em $intervalo.</td>";
            }

            $datalimitecond_view = DateTime::createFromFormat('Y-m-d H:i:s', $datalimitecond)->format('d/m/Y');

            $sql5 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$parecerpor';";
            $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
            $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
            $nomeparecer = $row5['nome'];


            if ($placa <> '') {
                $html .= "
                    <tr>
                        <td>$placa</td>
                        <td>$fantasia</td>
                        <td>$gravidade</td>
                        <td>$autoinfracao</td>
                        <td>$datainfra_view</td>
                        <td>$datalimitecond_view</td>
                        <td>$datalimiteloc_view</td>
                        $aprescond
                        <td>$valor</td>
                        <td>$valdesconto</td>
                        <td>$taxaadm</td>
                        <td>$valor_total</td>
                        <!--<td>$status</td>-->
                        <td>$tramite</td>
                        <td>$etapa1</td>
                        <td>$endereco</td>
                        <td>$municipio</td>
                        <td>$ufinfra</td>
                        <td>$orgao</td>
                        <td>$descricaoinfra</td>
                        <td>$nomecond</td>
                        <td>$matriculacond</td>
                        <td>$cpfcond</td>
                        <td>$ccustocond</td>
                        <td>$descontocol</td>
                        <td>$dtdesconto</td>
                        <td>$parecerf</td>
                        <td>$nomeparecer</td>
                        <td>$codigom</td>
                        <td>$datahoracadastro</td>
                        <td>$pontos</td>
                    </tr>";
            }
        }



        $html .= "</table>";

        // Configurações header para forçar o download
        header("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
        header("Content-Description: PHP Generated Data");
        // Envia o conteúdo do arquivo
        echo $html;
        ?>
    </body>

</html>