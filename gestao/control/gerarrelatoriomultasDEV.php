<?php
// Aumenta limites de execução
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '512M');

include("../../conecta03.php");
include_once "../../func/log3.php";

// Define charset UTF-8 para a conexão
mysqli_set_charset($con, "utf8mb4");

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

// Inicia tabela Excel - IMPRIME DIRETO (modelo antigo com 31 colunas)
echo "<table border='1'>";
echo "<tr>
    <th>Placa</th>
    <th>Locadora</th>
    <th>Unidade</th>
    <th>Gravidade</th>
    <th>Numero Auto</th>
    <th>Data Infracao</th>
    <th>Apresentar condutor em</th>
    <th>Apresentar condutor a locadora em</th>
    <th>Situacao</th>
    <th>Valor</th>
    <th>Valor do Desconto</th>
    <th>Taxa Administrativa</th>
    <th>Valor Total</th>
    <th>Tramite</th>
    <th>Etapa</th>
    <th>Endereco da Infracao</th>
    <th>Cidade da Infracao</th>
    <th>Estado da Infracao</th>
    <th>Orgao Emissor</th>
    <th>Descricao da Infracao</th>
    <th>Condutor Indicado</th>
    <th>Matricula Indicado</th>
    <th>CPF Indicado</th>
    <th>Centro de Custo do Indicado</th>
    <th>Descontado do condutor</th>
    <th>Desconto a partir</th>
    <th>Parecer</th>
    <th>Parecer por</th>
    <th>Codigo da Infracao</th>
    <th>Data Hora de Cadastro</th>
    <th>Pontos</th>
</tr>";

// Força envio do cabeçalho
flush();

// Query modelo antigo - com todas as colunas necessárias
if ($etapaf == 'TODAS') {
    $sql = "SELECT a.idtbmulta, a.etapa, a.datalimitecond, a.placa, a.autoinfracao, a.descricaoinfra, a.codigom, a.orgao, a.datalimiteloc, a.valor, a.valdesconto, a.taxaadm, a.valtotal, a.endereco, a.municipio, a.ufinfra, a.pontos, b.idtbmovidatramite, b.placa, b.autoinfra, b.dtinfra, b.dtvenc, b.tramite, b.gravidade, b.apCondDV, b.status, DATEDIFF(b.apCondDV, CURDATE()) as diferenca, b.idmulta, b.locadora, b.dtcons, b.nome, b.matricula, b.parecer, b.parecerpor, b.descontocol, b.dtdesconto 
        FROM bdfrota.tbmulta a
        LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa AND a.autoinfracao = b.autoinfra
        WHERE a.placa <> 'ABC1234' AND a.placa <> 'ABC1245' 
        AND b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'
        GROUP BY a.autoinfracao;";
} else {
    $sql = "SELECT a.idtbmulta, a.etapa, a.datalimitecond, a.placa, a.autoinfracao, a.descricaoinfra, a.codigom, a.orgao, a.datalimiteloc, a.valor, a.valdesconto, a.taxaadm, a.valtotal, a.endereco, a.municipio, a.ufinfra, a.pontos, b.idtbmovidatramite, b.placa, b.autoinfra, b.dtinfra, b.dtvenc, b.tramite, b.gravidade, b.apCondDV, b.status, DATEDIFF(b.apCondDV, CURDATE()) as diferenca, b.idmulta, b.locadora, b.dtcons, b.nome, b.matricula, b.parecer, b.parecerpor, b.descontocol, b.dtdesconto 
        FROM bdfrota.tbmulta a
        LEFT JOIN bdfrota.tbmovidatramite b ON a.placa = b.placa AND a.autoinfracao = b.autoinfra
        WHERE a.placa <> 'ABC1234' AND a.placa <> 'ABC1245' 
        AND a.etapa = '$etapaf'
        AND b.dtcons BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59'
        GROUP BY a.autoinfracao;";
}

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

$contador = 0;

// Loop - IMPRIME CADA LINHA DIRETAMENTE (streaming)
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
    $idtbmulta = $row['idtbmulta'];
    $etapa1 = utf8_decode($row['etapa']);
    $datalimitecond = $row['datalimitecond'];
    $placa = $row['placa'];
    $autoinfracao = $row['autoinfracao'];
    $descricaoinfra = utf8_decode($row['descricaoinfra']);
    $codigom = $row['codigom'];
    $orgao = utf8_decode($row['orgao']);
    $datalimiteloc = $row['datalimiteloc'];

    $valor = $row['valor'];
    $valdesconto = $row['valdesconto'];
    $taxaadm = $row['taxaadm'];
    $valor_total = $row['valtotal'];

    $endereco = utf8_decode($row['endereco']);
    $municipio = utf8_decode($row['municipio']);
    $ufinfra = $row['ufinfra'];
    $pontos = $row['pontos'];

    $idtbmovidatramite = $row['idtbmovidatramite'];
    $gravidade = utf8_decode($row['gravidade']);
    $apCondDV = $row['apCondDV'];
    $datainfra = $row['dtinfra'];
    $dtvenc = $row['dtvenc'];
    $tramite = utf8_decode($row['tramite']);
    $status = $row['status'];

    $idlocador = $row['locadora'];
    $dtcons = $row['dtcons'];
    $nomecond = utf8_decode($row['nome']);
    $matriculacond = $row['matricula'];

    $parecerf = utf8_decode($row['parecer']);
    $parecerpor = $row['parecerpor'];
    $dtdesconto = $row['dtdesconto'];
    $descontocol = utf8_decode($row['descontocol']);

    // Buscar CPF e Centro de Custo do condutor
    $sql_func = "SELECT ccusto, cpf FROM bdaniel.tbfuncionario WHERE matricula='$matriculacond';";
    $resultado_func = mysqli_query($con, $sql_func);
    if ($resultado_func) {
        $row_func = mysqli_fetch_array($resultado_func, MYSQLI_BOTH);
        $ccustocond = utf8_decode($row_func['ccusto'] ?? '');
        // Preserva CPF como string para evitar perder zeros
        $cpfcond = utf8_decode($row_func['cpf'] ?? '');
    } else {
        $ccustocond = '';
        $cpfcond = '';
    }

    // Buscar Locadora (fantasia) se não encontrada
    if ($idlocador == "") {
        $sql_veiculo = "SELECT idlocador, unidade FROM bdfrota.tbveiculo WHERE placa ='$placa';";
        $resultado_veiculo = mysqli_query($con, $sql_veiculo);
        if ($resultado_veiculo) {
            $row_veiculo = mysqli_fetch_array($resultado_veiculo, MYSQLI_BOTH);
            $idlocador = $row_veiculo['idlocador'] ?? '';
            $unidade = utf8_decode($row_veiculo['unidade'] ?? '');
        } else {
            $idlocador = '';
            $unidade = '';
        }
    } else {
        $sql_unidade = "SELECT unidade FROM bdfrota.tbveiculo WHERE placa ='$placa';";
        $resultado_unidade = mysqli_query($con, $sql_unidade);
        if ($resultado_unidade) {
            $row_unidade = mysqli_fetch_array($resultado_unidade, MYSQLI_BOTH);
            $unidade = utf8_decode($row_unidade['unidade'] ?? '');
        } else {
            $unidade = '';
        }
    }

    // Buscar fantasia da locadora
    $sql_fornecedor = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
    $resultado_fornecedor = mysqli_query($con, $sql_fornecedor);
    if ($resultado_fornecedor) {
        $row_fornecedor = mysqli_fetch_array($resultado_fornecedor, MYSQLI_BOTH);
        $fantasia = utf8_decode($row_fornecedor['fantasia'] ?? 'PRÓPRIO');
    } else {
        $fantasia = 'PRÓPRIO';
    }

    // Buscar nome de quem fez o parecer
    $sql_parecer = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$parecerpor';";
    $resultado_parecer = mysqli_query($con, $sql_parecer);
    if ($resultado_parecer) {
        $row_parecer = mysqli_fetch_array($resultado_parecer, MYSQLI_BOTH);
        $nomeparecer = utf8_encode($row_parecer['nome'] ?? '');
    } else {
        $nomeparecer = '';
    }

    // Formatar datas
    if (!empty($datainfra)) {
        try {
            $datainfra_view = DateTime::createFromFormat('Y-m-d H:i:s', $datainfra)->format('d/m/Y H:i:s');
        } catch (Exception $e) {
            $datainfra_view = '';
        }
    } else {
        $datainfra_view = '';
    }

    if (!empty($datalimitecond)) {
        try {
            $datalimitecond_view = DateTime::createFromFormat('Y-m-d H:i:s', $datalimitecond)->format('d/m/Y');
        } catch (Exception $e) {
            $datalimitecond_view = '00/00/0000';
        }
    } else {
        $datalimitecond_view = '00/00/0000';
    }

    if (!empty($datalimiteloc)) {
        try {
            $datalimiteloc_view = DateTime::createFromFormat('Y-m-d H:i:s', $datalimiteloc)->format('d/m/Y H:i:s');
        } catch (Exception $e) {
            $datalimiteloc_view = '';
        }
    } else {
        $datalimiteloc_view = '';
    }

    if (!empty($dtcons)) {
        try {
            $datahoracadastro = DateTime::createFromFormat('Y-m-d H:i:s', $dtcons)->format('d/m/Y H:i:s');
        } catch (Exception $e) {
            $datahoracadastro = '';
        }
    } else {
        $datahoracadastro = '';
    }

    // Calcular situação do prazo
    try {
        $inicio = new DateTime($hoje);
        if (!empty($datalimitecond)) {
            $apCondDV1 = new DateTime($datalimitecond);
        } else {
            $apCondDV1 = new DateTime('0001-01-01');
        }

        $dateInterval = $inicio->diff($apCondDV1);
        $intervalo = $dateInterval->format('%a dia(s)');
    } catch (Exception $e) {
        $intervalo = '0 dia(s)';
    }

    if ($datalimitecond_view == '00/00/0000' || empty($datalimitecond)) {
        $situacao = "Indeterminado";
    } elseif ($hoje > $datalimitecond) {
        $situacao = utf8_decode("Prazo vencido ha " . $intervalo);
    } else {
        $situacao = utf8_decode("Prazo vence em " . $intervalo);
    }

    // Cores alternadas: branco e azul
    $cor_linha = ($contador % 2 == 0) ? " bgcolor='#FFFFFF'" : " bgcolor='#4472C4'";
    $cor_texto = ($contador % 2 == 0) ? "" : " style='color:#FFFFFF'"; // Texto branco no azul

    // Montando a linha do Excel - EXATAMENTE AS 31 COLUNAS DO MODELO ANTIGO
    if ($placa <> '') {
        // IMPRIME LINHA DIRETAMENTE (streaming)
        echo "<tr$cor_linha>
            <td$cor_texto>$placa</td>
            <td$cor_texto>$fantasia</td>
            <td$cor_texto>$unidade</td>
            <td$cor_texto>$gravidade</td>
            <td$cor_texto>$autoinfracao</td>
            <td$cor_texto>$datainfra_view</td>
            <td$cor_texto>$datalimitecond_view</td>
            <td$cor_texto>$datalimiteloc_view</td>
            <td$cor_texto>$situacao</td>
            <td$cor_texto>$valor</td>
            <td$cor_texto>$valdesconto</td>
            <td$cor_texto>$taxaadm</td>
            <td$cor_texto>$valor_total</td>
            <td$cor_texto>$tramite</td>
            <td$cor_texto>$etapa1</td>
            <td$cor_texto>$endereco</td>
            <td$cor_texto>$municipio</td>
            <td$cor_texto>$ufinfra</td>
            <td$cor_texto>$orgao</td>
            <td$cor_texto>$descricaoinfra</td>
            <td$cor_texto>$nomecond</td>
            <td$cor_texto>'$matriculacond</td>
            <td$cor_texto>'$cpfcond</td>
            <td$cor_texto>$ccustocond</td>
            <td$cor_texto>$descontocol</td>
            <td$cor_texto>$dtdesconto</td>
            <td$cor_texto>$parecerf</td>
            <td$cor_texto>$nomeparecer</td>
            <td$cor_texto>$codigom</td>
            <td$cor_texto>$datahoracadastro</td>
            <td$cor_texto>$pontos</td>
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
