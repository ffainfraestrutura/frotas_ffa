<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$perfil = $_SESSION['perfil'] ?? null;

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location='../../index.php'</script>";
    exit;
}

include "../../conecta2.php";
header("Content-type: text/html; charset=utf-8");

function normalizarTexto($valor) {
    if ($valor === null) {
        return '';
    }

    $texto = (string)$valor;

    if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
        if (!mb_detect_encoding($texto, 'UTF-8', true)) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1,Windows-1252,UTF-8');
        }
    }

    $dicionario = [
        'Ã¡' => 'á', 'Ã¢' => 'â', 'Ã£' => 'ã', 'Ã¤' => 'ä', 'Ãª' => 'ê', 'Ã©' => 'é', 'Ã¨' => 'è',
        'Ã­' => 'í', 'Ã¬' => 'ì', 'Ã³' => 'ó', 'Ã´' => 'ô', 'Ãµ' => 'õ', 'Ã¶' => 'ö',
        'Ãº' => 'ú', 'Ã¼' => 'ü', 'Ã§' => 'ç', 'Ã' => 'Á', 'Ã‚' => 'Â', 'Ãƒ' => 'Ã',
        'Ã„' => 'Ä', 'Ã‰' => 'É', 'ÃŠ' => 'Ê', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ã”' => 'Ô',
        'Ã•' => 'Õ', 'Ãš' => 'Ú', 'Ãœ' => 'Ü', 'Ã‡' => 'Ç', 'Ã±' => 'ñ', 'Ã‘' => 'Ñ',
        'ÃƒÂ‡' => 'Ç', 'ÃƒÂƒ' => 'Ã', 'ÃƒÂ“' => 'Ó', 'ÃƒÂ‰' => 'É',
        'Âº' => 'º', 'Âª' => 'ª', 'Â°' => '°', 'Â' => ''
    ];

    return strtr($texto, $dicionario);
}

$arquivo = "rel-condutores-pj-" . date('Ymd_His') . ".xls";

$html = "
<html>
<head><meta charset='UTF-8'></head>
<body>
<table border='1'>
    <tr>
        <th>Matrícula</th>
        <th>Nome</th>
        <th>Centro de Custo</th>
        <th>Cargo</th>
        <th>UF</th>
        <th>Data Admissão</th>
        <th>Status</th>
    </tr>";

$sql = "SELECT matricula, nome, ccusto, cargo, uf_trabalho, dtadmissao, status
        FROM bdaniel.tbfuncionario
        WHERE matricula LIKE '62%'
        ORDER BY nome";

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    $matricula = htmlspecialchars((string)$row['matricula'], ENT_QUOTES, 'UTF-8');
    $nome = htmlspecialchars(normalizarTexto($row['nome']), ENT_QUOTES, 'UTF-8');
    $ccusto = htmlspecialchars(normalizarTexto($row['ccusto']), ENT_QUOTES, 'UTF-8');
    $cargo = htmlspecialchars(normalizarTexto($row['cargo']), ENT_QUOTES, 'UTF-8');
    $uf = htmlspecialchars((string)$row['uf_trabalho'], ENT_QUOTES, 'UTF-8');
    $dtadmissao = $row['dtadmissao'];
    $status = htmlspecialchars(normalizarTexto($row['status']), ENT_QUOTES, 'UTF-8');

    $data_formatada = '';
    if (!empty($dtadmissao) && $dtadmissao !== '0000-00-00') {
        $data_formatada = date('d/m/Y', strtotime($dtadmissao));
    }

    $html .= "
    <tr>
        <td>'$matricula</td>
        <td>$nome</td>
        <td>$ccusto</td>
        <td>$cargo</td>
        <td>$uf</td>
        <td>$data_formatada</td>
        <td>$status</td>
    </tr>";
}

$html .= "</table></body></html>";

header("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header("Content-Description: PHP Generated Data");

echo "\xEF\xBB\xBF";
echo $html;

mysqli_close($conexao);
?>
