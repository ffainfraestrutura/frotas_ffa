<?php
ob_start();
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

include("../conecta.php");
include_once("../func/rel-log-query.php");

function valorPostLog($nome)
{
    return isset($_POST[$nome]) ? trim($_POST[$nome]) : '';
}

function escaparCelulaLog($valor)
{
    $valor = (string) $valor;
    if (!preg_match('//u', $valor)) {
        $valor = utf8_encode($valor);
    }
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

$filtros = array(
    'datai' => valorPostLog('datai'),
    'dataf' => valorPostLog('dataf'),
    'acao' => valorPostLog('acao'),
    'matricula' => valorPostLog('matricula'),
    'mat_autor' => valorPostLog('mat_autor'),
    'placa' => valorPostLog('placa'),
    'tipo' => valorPostLog('tipo')
);

$sql = montarConsultaRelatorioLog($filtros);
$resultado = mysql_query($sql);

if ($resultado === false) {
    ob_end_clean();
    http_response_code(500);
    exit('Não foi possível gerar o relatório de LOG.');
}

$html = '<html><head><meta charset="UTF-8"></head><body>';
$html .= '<table border="1"><thead><tr>';
$colunas = array(
    'Matrícula Atuante',
    'Nome Atuante',
    'Ação',
    'Valor',
    'Matrícula Funcionário',
    'Nome Funcionário',
    'Data e hora'
);
foreach ($colunas as $coluna) {
    $html .= '<th>' . escaparCelulaLog($coluna) . '</th>';
}
$html .= '</tr></thead><tbody>';

while ($row = mysql_fetch_array($resultado)) {
    $html .= '<tr>';
    $html .= '<td style="mso-number-format:\'\\@\';">' . escaparCelulaLog($row[3]) . '</td>';
    $html .= '<td>' . escaparCelulaLog($row[6]) . '</td>';
    $html .= '<td>' . escaparCelulaLog($row[0]) . '</td>';
    $html .= '<td>' . escaparCelulaLog($row[4]) . '</td>';
    $html .= '<td style="mso-number-format:\'\\@\';">' . escaparCelulaLog($row[2]) . '</td>';
    $html .= '<td>' . escaparCelulaLog($row[5]) . '</td>';
    $html .= '<td>' . escaparCelulaLog($row[1]) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table></body></html>';

$arquivo = 'Relatorio-LOG-' . date('d-m-Y') . '.xls';
ob_end_clean();
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $arquivo . '"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
echo "\xEF\xBB\xBF" . $html;
