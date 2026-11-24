<?php
// Aumenta limites de execução
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '512M');

include("../../conecta03.php");
include_once "../../func/log3.php";

$hoje = date('Y-m-d H:i:s');

$etapaf = $_POST['etapaf'];
$data_inicial = $_POST['data_inicialf'];
$data_final = $_POST['data_finalf'];

// Define nome do arquivo
$arquivo = "rel-multas-$etapaf-$data_inicial-$data_final.xls";

// Limpa qualquer saída anterior
if (ob_get_level()) {
    ob_end_clean();
}

// Envia headers ANTES de qualquer HTML
header("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header("Content-Description: PHP Generated Data");

// Inicia tabela Excel - IMPRIME DIRETO (modelo antigo)
echo "<table border='1'>";
echo "<tr>
    <th>Placa</th>
    <th>Locadora</th>
    <th>Unidade</th>
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

// Força envio do cabeçalho
flush();

        // Query exatamente igual à da tela multasfrota.php
        if ($etapaf == 'TODAS') {
            $sql = "SELECT a.idtbmulta, 
                         a.etapa, 
                         a.datalimitecond, 
                         a.placa, 
                         a.autoinfracao, 
                         a.descricaoinfra, 
                         a.codigom,
                         a.valtotal,
                         a.orgao, 
                         b.idtbmovidatramite, 
                         b.placa, 
                         b.autoinfra, 
                         b.dtinfra, 
                         b.dtvenc, 
                         b.valor, 
                         b.tramite, 
                         b.gravidade, 
                         b.apCondDV, 
                         b.status, 
                DATEDIFF(b.apCondDV , CURDATE()) as diferenca, 
                         b.idmulta, 
                         b.status, 
                         b.locadora, 
                         b.dtcons, 
                         b.nome, 
                         b.matricula, 
                         b.parecer, 
                         b.parecerpor 
                    FROM bdfrota.tbmulta a
                   INNER JOIN (
                          SELECT autoinfracao, 
                                 MIN(CASE 
                                     WHEN etapa = 'AGUARDANDO ASSINATURA CONDUTOR' THEN 999999999
                                     ELSE idtbmulta 
                                 END) as id_prioritario
                          FROM bdfrota.tbmulta 
                          WHERE placa <> 'ABC1234' 
                            AND placa <> 'ABC1245'
                            AND idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464', '7269','7271','7273','7275', '6505')
                          GROUP BY autoinfracao
                      ) primeiro ON a.autoinfracao = primeiro.autoinfracao 
                                  AND (CASE 
                                       WHEN a.etapa = 'AGUARDANDO ASSINATURA CONDUTOR' THEN 999999999
                                       ELSE a.idtbmulta 
                                   END) = primeiro.id_prioritario
               LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa 
                     AND a.autoinfracao = b.autoinfra
                  WHERE b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'
                  GROUP BY a.autoinfracao;";
        } else {
            $sql = "SELECT a.idtbmulta, 
                         a.etapa, 
                         a.datalimitecond, 
                         a.placa, 
                         a.autoinfracao, 
                         a.descricaoinfra, 
                         a.codigom,
                         a.valtotal, 
                         a.orgao, 
                         b.idtbmovidatramite, 
                         b.placa, 
                         b.autoinfra, 
                         b.dtinfra, 
                         b.dtvenc, 
                         b.valor, 
                         b.tramite, 
                         b.gravidade, 
                         b.apCondDV, 
                         b.status, 
                DATEDIFF(b.apCondDV , CURDATE()) as diferenca, 
                         b.idmulta, 
                         b.status, 
                         b.locadora, 
                         b.dtcons, 
                         b.nome, 
                         b.matricula, 
                         b.parecer, 
                         b.parecerpor 
                    FROM bdfrota.tbmulta a
                   INNER JOIN (
                          SELECT autoinfracao, 
                                 MIN(CASE 
                                     WHEN etapa = 'AGUARDANDO ASSINATURA CONDUTOR' THEN 999999999
                                     ELSE idtbmulta 
                                 END) as id_prioritario
                          FROM bdfrota.tbmulta 
                          WHERE placa <> 'ABC1234' 
                            AND placa <> 'ABC1245'
                            AND etapa = '$etapaf'
                            AND idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464', '7269','7271','7273','7275', '6505')
                          GROUP BY autoinfracao
                      ) primeiro ON a.autoinfracao = primeiro.autoinfracao 
                                  AND (CASE 
                                       WHEN a.etapa = 'AGUARDANDO ASSINATURA CONDUTOR' THEN 999999999
                                       ELSE a.idtbmulta 
                                   END) = primeiro.id_prioritario
               LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa 
                     AND a.autoinfracao = b.autoinfra
                  WHERE b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'
                  GROUP BY a.autoinfracao;";
        }

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

$contador = 0;

// Loop - IMPRIME CADA LINHA DIRETAMENTE (streaming)
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
            // Variáveis exatamente como na tela multasfrota.php
            $idtbmulta = $row['idtbmulta'];
            $etapa1 = $row['etapa'];
            $datalimitecond = $row['datalimitecond'];
            $placa = $row['placa'];
            $autoinfracao = $row['autoinfracao'];
            $descricao = $row['descricaoinfra'];
            $codigom = $row['codigom'];
            $orgao = $row['orgao'];

            $idtbmovidatramite = $row['idtbmovidatramite'];
            $gravidade = $row['gravidade'];
            $apCondDV = $row['apCondDV'];
            $datainfra = $row['dtinfra'];
            $dtvenc = $row['dtvenc'];
            $valor = $row['valor'];
            $valtotal = $row['valtotal'];
            $tramite = $row['tramite'];
            $status = $row['status'];

            $locador = $row['locadora'];
            $dtcons = $row['dtcons'];
            $nomecond = $row['nome'];
            $matriculacond = $row['matricula'];

            $parecerbd = $row['parecer'];
            $parecerpor = $row['parecerpor'];
            $dtdesconto = $row['dtdesconto'];

            // Condutor na época da multa - LÓGICA EXATA DA TELA
            if ($nomecond == '') {
                $sqla1 = "SELECT matricula, nome, dataassoc, datadissoc FROM bdfrota.tbcondutor WHERE placaassoc = '$placa' AND dataassoc<='$datainfra' AND datadissoc>='$datainfra' AND datadissoc<>'0000-00-00 00:00:00' ";
                $resultadoa = mysqli_query($con, $sqla1) or die(mysqli_error($con));
                $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
                $nomecond = $rowa['nome'];
                $matriculacond = $rowa['matricula'];
            }

            // Buscar informações do funcionário
            $sqla2 = "SELECT MAX(dtadmissao) AS max_dtadmissao FROM bdaniel.tbfuncionario WHERE nome='$nomecond' ";
            $resultadoa2 = mysqli_query($con, $sqla2) or die(mysqli_error($con));
            $rowa2 = mysqli_fetch_array($resultadoa2, MYSQLI_BOTH);
            $max_dtadmissao = $rowa2['max_dtadmissao'];

            $sqla3 = "SELECT ccusto, status FROM bdaniel.tbfuncionario WHERE dtadmissao='$max_dtadmissao' AND nome='$nomecond'; ";
            $resultadoa3 = mysqli_query($con, $sqla3) or die(mysqli_error($con));
            $rowa3 = mysqli_fetch_array($resultadoa3, MYSQLI_BOTH);
            $ccusto = $rowa3['ccusto'];
            $statuscond = $rowa3['status'];

            // Se não encontrar locadora no resultado, buscar do veículo
            if ($locador == '') {
                $sql5 = "SELECT idlocador FROM bdfrota.tbveiculo WHERE placa='$placa'; ";
                $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                $locador = $row5['idlocador'];
            }

            // Pegando locador
            if ($locador == '') {
                $fantasia = 'PRÓPRIO';
            } else {
                $sql3 = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor = '$locador'";
                $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                $fantasia = $row3['fantasia'];
            }

            // Selecionando unidade
            $sql4 = "SELECT unidade, aplicacao FROM bdfrota.tbveiculo WHERE placa='$placa';";
            $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
            $unidade = $row4['unidade'];
            $aplicacao = $row4['aplicacao'];

            // Selecionando autor do parecer1
            $sql5 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$parecerpor';";
            $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
            $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
            $parecerpor1 = $row5['nome'];

            $sql10 = "SELECT aplicacao FROM bdfrota.tbaplicacaoveic WHERE idtbaplicacaoveic='$aplicacao'; ";
            $resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
            $row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
            $aplicacao = $row10['aplicacao'];

            // Formatar data de consulta - EXATAMENTE COMO NA TELA
            if (!empty($dtcons)) {
                $dtcons3 = DateTime::createFromFormat('Y-m-d H:i:s', $dtcons)->format('d/m/Y');
                $dtcons1 = explode(" ", $dtcons);
            } else {
                $dtcons3 = '';
                $dtcons1 = array('', '');
            }

            // Formatar data de infração
            if (!empty($datainfra)) {
                $datainfra2 = DateTime::createFromFormat('Y-m-d H:i:s', $datainfra)->format('d/m/Y');
            } else {
                $datainfra2 = '';
            }

            // Formatar data limite condutor
            if (!empty($apCondDV)) {
                $apCondDV3 = DateTime::createFromFormat('Y-m-d H:i:s', $apCondDV)->format('d/m/Y');
            } else {
                $apCondDV3 = '00/00/0000';
            }

            // Calcular situação do prazo - LÓGICA EXATA DA TELA
            $inicio = new DateTime($hoje);
            if (!empty($apCondDV)) {
                $apCondDV1 = new DateTime($apCondDV);
            } else {
                $apCondDV1 = new DateTime('0001-01-01');
            }

            $dateInterval = $inicio->diff($apCondDV1);
            $intervalo = $dateInterval->format('%a dia(s)');

            if ($apCondDV3 == '01/01/0001' || $apCondDV3 == '00/00/0000') {
                $apCondDV3 = '00/00/0000';
                $aprescond = "Prazo indeterminado";
            } else {
                if ($tramite != 'Finalizado Frota') {
                    if ($hoje > $apCondDV) {
                        $aprescond = "Prazo vencido";
                    } else {
                        $aprescond = "Prazo em aberto: $intervalo";
                    }
                } else {
                    $aprescond = "CONDUTOR INDICADO";
                }
            }

            // Montando a linha do Excel - EXATAMENTE AS 24 COLUNAS DA TELA
            if ($placa <> '') {
                // IMPRIME LINHA DIRETAMENTE (streaming)
                echo "<tr>
                    <td>$placa</td>
                    <td>$aplicacao</td>
                    <td>$nomecond</td>
                    <td>$statuscond</td>
                    <td>$ccusto</td>
                    <td>$unidade</td>
                    <td>$fantasia</td>
                    <td>$dtcons3 $dtcons1[1]</td>
                    <td>$gravidade</td>
                    <td>$autoinfracao</td>
                    <td>$codigom</td>
                    <td>$descricao</td>
                    <td>$datainfra2</td>
                    <td>$orgao</td>
                    <td>$apCondDV3</td>
                    <td>$aprescond</td>
                    <td>$etapa1</td>
                    <td>R$ $valtotal</td>
                    <td>$tramite</td>
                    <td>$parecerbd</td>
                    <td>$parecerpor1</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>";
                
                // Flush a cada 50 registros para evitar timeout
                $contador++;
                if ($contador % 50 == 0) {
                    flush();
                }
            }
        }

// Fecha tabela
echo "</table>";

// Libera recursos
mysqli_free_result($resultado);
mysqli_close($con);
exit();
?>