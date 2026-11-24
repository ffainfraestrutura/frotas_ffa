<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Verificar autenticação
$nome = $_SESSION['nome'] ?? '';
$usuariof = $_SESSION['usuario'] ?? '';
$matricula = $_SESSION['matricula'] ?? '';
$perfil = $_SESSION['perfil'] ?? '';

if (empty($perfil) || empty($matricula)) {
    die('Acesso não autorizado. Por favor, faça login.');
}

include "../conecta.php";

// Receber período
$periodo = $_GET['periodo'] ?? '';

if (empty($periodo)) {
    die('Período não informado. Por favor, selecione uma semana.');
}

// Calcular segunda-feira da semana
$data_referencia = new DateTime($periodo);
if ($data_referencia->format('N') == 1) {
    $usegunda = $data_referencia->format('Y-m-d');
} else {
    $data_referencia->modify('last monday');
    $usegunda = $data_referencia->format('Y-m-d');
}

// Calcular domingo da semana
$dataInicial = new DateTime($usegunda);
$dataInicial->modify('next sunday');
$pdomingo = $dataInicial->format('Y-m-d');

// Nome do arquivo
$nomeArquivo = 'Relatorio_Rotas_Semana_' . date('d-m-Y', strtotime($usegunda)) . '_a_' . date('d-m-Y', strtotime($pdomingo)) . '.xls';

// Headers para download do Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$nomeArquivo\"");
header("Pragma: no-cache");
header("Expires: 0");

// Array com nomes dos dias da semana
$nomesDiasDaSemana = array(
    1 => 'Segunda-feira',
    2 => 'Terça-feira',
    3 => 'Quarta-feira',
    4 => 'Quinta-feira',
    5 => 'Sexta-feira',
    6 => 'Sábado',
    7 => 'Domingo'
);

// Iniciar documento HTML (Excel aceita HTML)
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        th { background-color: #4472C4; color: white; font-weight: bold; padding: 8px; border: 1px solid #000; text-align: center; }
        td { padding: 6px; border: 1px solid #000; }
        .header-tecnico { background-color: #70AD47; color: white; font-weight: bold; font-size: 14px; }
        .header-dia { background-color: #5B9BD5; color: white; font-weight: bold; }
        .total-dia { background-color: #FFC000; font-weight: bold; }
        .total-semana { background-color: #FF6600; color: white; font-weight: bold; font-size: 12px; }
        .centro { text-align: center; }
        .direita { text-align: right; }
        .titulo { background-color: #203864; color: white; font-size: 16px; font-weight: bold; padding: 12px; }
    </style>
</head>
<body>';

echo '<h1 style="text-align: center; color: #203864;">RELATÓRIO DE DESLOCAMENTO - TODOS OS TÉCNICOS</h1>';
echo '<h3 style="text-align: center;">Período: ' . date('d/m/Y', strtotime($usegunda)) . ' a ' . date('d/m/Y', strtotime($pdomingo)) . '</h3>';
echo '<p style="text-align: center;">Gerado em: ' . date('d/m/Y H:i:s') . ' por ' . $nome . ' (' . $matricula . ')</p>';
echo '<br>';

// Buscar todos os técnicos ativos
$sqlTecnicos = "SELECT DISTINCT u.matricula, u.nome 
                FROM bdfrota.tbusuario u
                INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                WHERE u.matricula IS NOT NULL 
                AND u.matricula != '' 
                AND u.nome IS NOT NULL 
                AND u.nome != ''
                AND f.status = 'Ativo'
                AND u.perfil IN (0, 1)
                ORDER BY u.nome";

$resultTecnicos = mysql_query($sqlTecnicos) or die('Erro ao buscar técnicos: ' . mysql_error());

$totalGeralKm = 0;
$totalTecnicos = 0;

// Para cada técnico
while ($rowTecnico = mysql_fetch_array($resultTecnicos)) {
    $mattec = $rowTecnico['matricula'];
    $nomeTecnico = $rowTecnico['nome'];
    
    // Buscar informações do funcionário
    $sqlFunc = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$mattec'";
    $resultFunc = mysql_query($sqlFunc);
    $rowFunc = mysql_fetch_array($resultFunc);
    $ccusto = $rowFunc['ccusto'] ?? '';
    
    // Determinar tabela baseado no centro de custo
    if (strpos($ccusto, 'TIM') !== false) {
        $tabela = "bdfrota.tbosrota";
        $id = 'idtbosrota';
    } elseif (strpos($ccusto, 'CLARO') !== false) {
        $tabela = "bdfrota.tbosrotalatlong";
        $id = 'idtbosrotalatlong';
    } elseif (strpos($ccusto, 'LIGGA') !== false) {
        $tabela = "bdfrota.tbosrota";
        $id = 'idtbosrota';
    } elseif (strpos($ccusto, 'IHS') !== false) {
        $tabela = "bdfrota.tbosrota";
        $id = 'idtbosrota';
    } else {
        $tabela = "bdfrota.tbosrota";
        $id = 'idtbosrota';
    }
    
    // Verificar se o técnico tem rotas no período
    $sqlVerifica = "SELECT COUNT(*) as total FROM $tabela 
                    WHERE matricula = '$mattec' 
                    AND data >= '$usegunda 00:00:00' 
                    AND data <= '$pdomingo 23:59:59'";
    $resultVerifica = mysql_query($sqlVerifica);
    $rowVerifica = mysql_fetch_array($resultVerifica);
    
    if ($rowVerifica['total'] == 0) {
        continue; // Pular técnicos sem rotas no período
    }
    
    $totalTecnicos++;
    
    echo '<table style="margin-bottom: 30px;">';
    echo '<tr><td colspan="11" class="header-tecnico">TÉCNICO: ' . $nomeTecnico . ' (Mat. ' . $mattec . ') - Centro de Custo: ' . $ccusto . '</td></tr>';
    
    $totaisDiarios = array();
    $dataInicial = new DateTime($usegunda);
    $dataFinal = new DateTime($pdomingo);
    
    // Para cada dia da semana
    while ($dataInicial <= $dataFinal) {
        $dataDia = $dataInicial->format('Y-m-d');
        $timestamp = strtotime($dataDia);
        $diaSemanaNumero = date('N', $timestamp);
        $nomeDiaSemana = $nomesDiasDaSemana[$diaSemanaNumero];
        
        echo '<tr><td colspan="11" class="header-dia">' . $dataDia . ' - ' . $nomeDiaSemana . '</td></tr>';
        echo '<tr>
                <th>Matrícula</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Dia</th>
                <th>Hora</th>
                <th>OS</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Origem</th>
                <th>KM</th>
              </tr>';
        
        // Buscar rotas do dia
        $sqlRotas = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa, latitude, longitude, $id 
                     FROM $tabela 
                     WHERE matricula = '$mattec' 
                     AND data = '$dataDia' 
                     GROUP BY eta 
                     ORDER BY data, eta";
        $resultRotas = mysql_query($sqlRotas);
        
        $temRotas = false;
        while ($rowRota = mysql_fetch_array($resultRotas)) {
            $temRotas = true;
            $data = $rowRota[3];
            $datac = explode(" ", $data);
            $datat = $datac[0];
            $endereco = $rowRota[0];
            $bairro = $rowRota[1];
            $cidade = $rowRota[2];
            $eta = $rowRota[4];
            $os = $rowRota[5];
            if ($os == '') $os = $rowRota[7];
            $kmrota = $rowRota[6];
            if ($kmrota == '') $kmrota = 0;
            $foratoa = $rowRota[8];
            
            if ($foratoa == 1) {
                $origem = 'MANUAL';
            } elseif ($os == 'CASA') {
                $origem = 'ANIEL';
            } else {
                $origem = 'TOA';
            }
            
            $latitude = $rowRota[9];
            $longitude = $rowRota[10];
            
            $endcomp = "$endereco $bairro $cidade";
            if (!mb_detect_encoding($endcomp, 'UTF-8', true)) {
                $endcomp = utf8_encode("$endereco $bairro $cidade");
            }
            
            echo '<tr>
                    <td class="centro">' . $mattec . '</td>
                    <td>' . $nomeTecnico . '</td>
                    <td>' . $endcomp . '</td>
                    <td class="centro">' . $datat . '</td>
                    <td class="centro">' . $eta . '</td>
                    <td class="centro">' . $os . '</td>
                    <td class="centro">' . $latitude . '</td>
                    <td class="centro">' . $longitude . '</td>
                    <td class="centro">' . $origem . '</td>
                    <td class="direita">' . $kmrota . '</td>
                  </tr>';
        }
        
        if (!$temRotas) {
            echo '<tr><td colspan="10" class="centro" style="color: #999;">Sem rotas registradas neste dia</td></tr>';
        }
        
        // Buscar KM rodados do dia
        $sqlKm = "SELECT ROUND(kmsrodados,1) FROM bdfrota.tbkmrodadosdia WHERE matricula = '$mattec' AND dia = '$dataDia'";
        $resultKm = mysql_query($sqlKm);
        $rowKm = mysql_fetch_array($resultKm);
        $kmsrodados = $rowKm[0] ?? 0;
        
        // Almoço (se tiver KM rodados)
        if ($kmsrodados > 0) {
            echo '<tr>
                    <td class="centro">' . $mattec . '</td>
                    <td>' . $nomeTecnico . '</td>
                    <td>-</td>
                    <td class="centro">' . $datat . '</td>
                    <td class="centro">-</td>
                    <td class="centro">ALMOÇO</td>
                    <td class="centro">-</td>
                    <td class="centro">-</td>
                    <td class="centro">ALMOÇO</td>
                    <td class="direita">5.0</td>
                  </tr>';
        }
        
        // Edições de KM
        $sqlEdit = "SELECT kmsedit, posneg, matop, motivo FROM bdfrota.tbkmdiarioedit WHERE matricula = '$mattec' AND dataedit = '$dataDia'";
        $resultEdit = mysql_query($sqlEdit);
        if (mysql_num_rows($resultEdit) >= 1) {
            $rowEdit = mysql_fetch_array($resultEdit);
            $kmsedit = $rowEdit[0];
            $posneg = $rowEdit[1];
            if ($posneg == 1) {
                $sinal = '-';
                $kmsrodados = $kmsrodados - $kmsedit;
            } else {
                $kmsrodados = $kmsrodados + $kmsedit;
                $sinal = '+';
            }
            $matup = $rowEdit[2];
            $motivo = $rowEdit[3];
            
            echo '<tr style="background-color: #FFE699;">
                    <td colspan="9"><b>Edição KM - Operador: ' . $matup . ' | Motivo: ' . $motivo . '</b></td>
                    <td class="direita"><b>' . $sinal . ' ' . $kmsedit . '</b></td>
                  </tr>';
        }
        
        // Total do dia
        $totaisDiarios[] = floatval($kmsrodados);
        echo '<tr class="total-dia">
                <td colspan="9" class="direita">TOTAL DO DIA:</td>
                <td class="direita">' . number_format($kmsrodados, 1, ',', '.') . '</td>
              </tr>';
        
        $dataInicial->add(new DateInterval('P1D'));
    }
    
    // Total semanal do técnico
    $totalSemanal = array_sum($totaisDiarios);
    $mediaDiaria = count($totaisDiarios) > 0 ? $totalSemanal / 6 : 0;
    
    echo '<tr class="total-semana">
            <td colspan="9" class="direita">TOTAL SEMANAL:</td>
            <td class="direita">' . number_format($totalSemanal, 1, ',', '.') . ' km</td>
          </tr>';
    echo '<tr class="total-semana">
            <td colspan="9" class="direita">MÉDIA DIÁRIA (6 dias úteis):</td>
            <td class="direita">' . number_format($mediaDiaria, 1, ',', '.') . ' km</td>
          </tr>';
    echo '</table>';
    
    $totalGeralKm += $totalSemanal;
}

// Resumo geral
echo '<br><br>';
echo '<table style="background-color: #203864; color: white;">';
echo '<tr><td colspan="2" class="titulo">RESUMO GERAL</td></tr>';
echo '<tr style="background-color: white; color: black;"><td><b>Total de Técnicos com Rotas:</b></td><td class="direita"><b>' . $totalTecnicos . '</b></td></tr>';
echo '<tr style="background-color: white; color: black;"><td><b>Total Geral de KM (Todos os Técnicos):</b></td><td class="direita"><b>' . number_format($totalGeralKm, 1, ',', '.') . ' km</b></td></tr>';
if ($totalTecnicos > 0) {
    $mediaGeralPorTecnico = $totalGeralKm / $totalTecnicos;
    echo '<tr style="background-color: white; color: black;"><td><b>Média de KM por Técnico na Semana:</b></td><td class="direita"><b>' . number_format($mediaGeralPorTecnico, 1, ',', '.') . ' km</b></td></tr>';
}
echo '</table>';

echo '</body></html>';

mysql_close();
?>
