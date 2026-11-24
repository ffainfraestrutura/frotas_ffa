<?php
// Adicionado em 17/12 para resolver erro de memória
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');

require './fpdf.php';
require('../conecta.php');

// Limpa qualquer saída anterior
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header("Content-type: text/html; charset=utf-8");

$placacorreta = $_POST['placa'] ?? '';
$datavistoria = $_POST['datavistoria'] ?? '';
$idtbvistoria = filter_input(INPUT_POST, 'idtbvistoria', FILTER_VALIDATE_INT);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Validação inicial
if (empty($idtbvistoria)) {
    die("ID da vistoria não informado");
}

// Usando prepared statement para prevenir SQL injection
$sql = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $idtbvistoria);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) == 0) {
    die("Vistoria não encontrada");
}

// Buscar status do veículo em uma única consulta
$sql_status = "SELECT idtbastatusvel, status FROM bdfrota.tbastatusvel";
$result_status = mysqli_query($con, $sql_status);
$status_map = [];
while ($row = mysqli_fetch_assoc($result_status)) {
    $status_map[$row['idtbastatusvel']] = $row['status'];
}

// Buscar tipo de vistoria em uma única consulta
$sql_tipo = "SELECT idtbatipovist, tipo FROM bdfrota.tbatipovist";
$result_tipo = mysqli_query($con, $sql_tipo);
$tipo_map = [];
while ($row = mysqli_fetch_assoc($result_tipo)) {
    $tipo_map[$row['idtbatipovist']] = $row['tipo'];
}

while ($row = mysqli_fetch_assoc($resultado)) {
    $nome = $row['nome'];
    $matricula = $row['matricula'];
    $cpf = $row['cpf'];
    $cnh = $row['cnh'];
    $categoriacnh = $row['categoriacnh'];
    $validadecnh = $row['validadecnh'];
    $assinado = $row['assinadocond'];
    $statusveic_id = $row['statusveic'];
    $statusveic = $status_map[$statusveic_id] ?? $statusveic_id;
    $matrvistoriador = $row['matrvistoriador'];
    $placa = $row['placa'];
    $modelo = $row['modelo'];
    $anofabricacao = $row['anofabricacao'];
    $unidade = $row['unidade'];
    $centrocusto = $row['centrocusto'];
    $tipo_id = $row['tipo'];
    $tipo = $tipo_map[$tipo_id] ?? $tipo_id;
    $vistoriador = $row['vistoriador'];
    $datavistoria = $row['datavistoria'];
    $estado = strtoupper($row['estado']);
    $avaria = $row['avaria'];
    $hodometro = $row['hodometro'];
    $niveltanque = $row['niveltanque'];
    $observacao = str_replace("\n", " ", $row['observacao'] ?? '');

    $datavistoriareal = DateTime::createFromFormat('Y-m-d H:i:s', $datavistoria)->format('d/m/Y H:i:s');

    // Mapeamento dos campos de vistoria
    $campos_vistoria = [
        'teto', 'tetoesp', 'tetoesq', 'tetodir', 'frente', 'capo', 'parabrisa',
        'farolesq', 'faroldir', 'parachoque', 'grade', 'latesq', 'paralamaesq',
        'retrovesq', 'cxaresq', 'ptdiantesq', 'pttrasesq', 'latdir', 'paralamadir',
        'retrovdir', 'cxardir', 'ptdiantdir', 'pttrasdir', 'traseira', 'lantesq',
        'lantdir', 'tmpmala', 'parachoquet', 'itinterno', 'painel', 'som', 'bancos',
        'ilumint', 'tmpbag', 'retrovint', 'tapetes', 'pneus', 'step', 'marcapneus',
        'kitstep', 'calotas', 'bateria', 'safecar', 'limpint', 'limpext'
    ];
    
    foreach ($campos_vistoria as $campo) {
        $$campo = strtoupper($row[$campo] ?? '');
    }

    $imgassinaturavist = $row['dirassvist'] ?? '';
    $imgassinaturacond = $row['dir'] ?? '';
    $assinadovist = $row['assinadovist'] ?? 0;

    if ($validadecnh != "") {
        $validadecnhreal = DateTime::createFromFormat('Y-m-d', $validadecnh)->format('d/m/Y');
    }
}

$avaria = ($avaria == 0) ? 'NÃO' : (($avaria == 1) ? 'SIM' : $avaria);

// Função para tratar NAOOK
function tratarNaoOk(&$var) {
    if ($var == 'NAOOK') {
        $var = 'NÃO OK';
    }
}

$itensParaTratar = ['frente', 'teto', 'latesq', 'latdir', 'traseira', 'itinterno', 'painel', 'som', 'bancos', 'ilumint', 'pneus', 'step', 'kitstep', 'calotas'];
foreach ($itensParaTratar as $item) {
    if (isset($$item)) {
        tratarNaoOk($$item);
    }
}

// Buscar fotos com prepared statement
$sql_fotos = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria = ? AND idtbvistfotos NOT IN (40,41,42,43,44,45,47,2639,2640,2641,2642,2643,2644,2645,2646,2647,3198,3199,3201)";
$stmt_fotos = mysqli_prepare($con, $sql_fotos);
mysqli_stmt_bind_param($stmt_fotos, "i", $idtbvistoria);
mysqli_stmt_execute($stmt_fotos);
$resultado5 = mysqli_stmt_get_result($stmt_fotos);

$frontal = $fototraseira = $direita = $esquerda = $bateriaf = $selfie = $fotocnh = $extra1 = $extra2 = $extra3 = $extra4 = $ftpainel = '';

// Definir caminhos base
$base_path = '/var/www/html/frotas/checklist/';
$pasta_atual = $base_path . 'docs/';
$pasta_antiga = $base_path . 'docs_old/';

while ($row = mysqli_fetch_assoc($resultado5)) {
    $placa = $row['placa'];
    $frontal = getFilePath($row['frontal'] ?? '', $pasta_atual, $pasta_antiga);
    $fototraseira = getFilePath($row['traseira'] ?? '', $pasta_atual, $pasta_antiga);
    $direita = getFilePath($row['direita'] ?? '', $pasta_atual, $pasta_antiga);
    $esquerda = getFilePath($row['esquerda'] ?? '', $pasta_atual, $pasta_antiga);
    $bateriaf = $row['bateria'] ?? '';
    $selfie = getFilePath($row['selfie'] ?? '', $pasta_atual, $pasta_antiga);
    $fotocnh = getFilePath($row['cnh'] ?? '', $pasta_atual, $pasta_antiga);
    $extra1 = getFilePath($row['extra1'] ?? '', $pasta_atual, $pasta_antiga);
    $extra2 = getFilePath($row['extra2'] ?? '', $pasta_atual, $pasta_antiga);
    $extra3 = getFilePath($row['extra3'] ?? '', $pasta_atual, $pasta_antiga);
    $extra4 = getFilePath($row['extra4'] ?? '', $pasta_atual, $pasta_antiga);
    $ftpainel = getFilePath($row['painel'] ?? '', $pasta_atual, $pasta_antiga);
}

// Função para buscar arquivos
function getFilePath($file, $pasta_atual, $pasta_antiga) {
    if (empty($file)) {
        return '';
    }
    
    $file = basename($file);
    
    $possiveis_caminhos = [
        $pasta_atual . $file,
        $pasta_antiga . $file,
        $pasta_atual . 'uploads/' . $file,
        $pasta_antiga . 'uploads/' . $file
    ];
    
    foreach ($possiveis_caminhos as $caminho) {
        if (file_exists($caminho) && is_readable($caminho)) {
            return $caminho;
        }
    }
    
    return '';
}

// Função para corrigir caminho de assinatura
function corrigirCaminhoAssinatura($caminho) {
    if (empty($caminho) || trim($caminho) == '') {
        return '';
    }
    
    $nome_arquivo = basename($caminho);
    
    $possiveis_caminhos = [
        "../../../assinaturas/docs/colass/" . $nome_arquivo,
        "/var/www/html/assinaturas/docs/colass/" . $nome_arquivo,
        $caminho
    ];
    
    foreach ($possiveis_caminhos as $path) {
        if (file_exists($path) && is_readable($path)) {
            return $path;
        }
    }
    
    return '';
}

$imgassinaturavist = corrigirCaminhoAssinatura($imgassinaturavist ?? '');
$imgassinaturacond = corrigirCaminhoAssinatura($imgassinaturacond ?? '');

class PDF extends FPDF
{
    function Header()
    {
        // Header vazio para evitar problemas
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Image('../../src/images/logoredonda.png', 10, 8, 10);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(255);
$pdf->Cell(12);
$pdf->Cell(170, 6, utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'), 1, 1, 'C', 'true');
$pdf->Ln(1);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(12);
$pdf->MultiCell(170, 4, utf8_decode("INFORMAÇÕES DO CONDUTOR"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(20, 4, utf8_decode("Nome"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68, 4, utf8_decode("$nome"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(20, 4, utf8_decode("CNH"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(120);
$pdf->MultiCell(62, 4, utf8_decode("$cnh"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(20, 4, utf8_decode("Matrícula"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68, 4, utf8_decode("$matricula"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(40, 4, utf8_decode("CNH categoria"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(140);
$pdf->MultiCell(42, 4, utf8_decode("$categoriacnh"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(20, 4, utf8_decode("CPF"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68, 4, utf8_decode("$cpf"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(40, 4, utf8_decode("CNH vencimento"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(140);
$pdf->MultiCell(42, 4, utf8_decode("$validadecnhreal"), 1, 1);

$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170, 4, utf8_decode("INFORMAÇÕES DO VEÍCULO"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(15, 4, utf8_decode("Placa"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(60, 4, utf8_decode("$placa"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(15, 4, utf8_decode("Unidade"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(102);
$pdf->MultiCell(80, 4, utf8_decode("$unidade"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(15, 4, utf8_decode("Modelo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(45, 4, utf8_decode("$modelo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(72);
$pdf->MultiCell(35, 4, utf8_decode("Centro de Custo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(107);
$pdf->MultiCell(75, 4, utf8_decode("$centrocusto"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(15, 4, utf8_decode("Ano"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(45, 4, utf8_decode("$anofabricacao"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(72);
$pdf->MultiCell(30, 4, utf8_decode("Status do Veículo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(102);
$pdf->MultiCell(80, 4, utf8_decode("$statusveic"), 1, 1);

$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170, 4, utf8_decode("INFORMAÇÕES DA VISTORIA"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(15, 4, utf8_decode("Tipo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(155, 4, utf8_decode("$tipo"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(25, 4, utf8_decode("Realizada por"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(78, 4, utf8_decode("$vistoriador"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(115);
$pdf->MultiCell(15, 4, utf8_decode("Data"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(130);
$pdf->MultiCell(52, 4, utf8_decode("$datavistoriareal"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(25, 4, utf8_decode("Estado geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(50, 4, utf8_decode("$estado"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(30, 4, utf8_decode("Possui avaria?"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(117);
$pdf->MultiCell(65, 4, utf8_decode("$avaria"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(25, 4, utf8_decode("Hodômetro"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(50, 4, utf8_decode("$hodometro"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(30, 4, utf8_decode("Nível do tanque"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(117);
$pdf->MultiCell(65, 4, utf8_decode("$niveltanque"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(25, 4, utf8_decode("Observação"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(145, 4, utf8_decode("$observacao"), 1, 1);

$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170, 4, utf8_decode("ITENS PERSONALIZADOS DA VISTORIA"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Teto - Est Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$teto"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Teto Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$tetoesp"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Coluna do teto - esquerda"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$tetoesq"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Coluna do teto - lado direito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$tetodir"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Frente Estado Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$frente"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Capô"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$capo"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Parabrisa"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$parabrisa"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Farol esquerdo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$farolesq"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Farol direito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$faroldir"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Para-choque dianteiro"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$parachoque"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Grade frontal"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$grade"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Lateral esquerda - Estado Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$latesq"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Para-lama esquerdo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$paralamaesq"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Retrovisor Lado Esquerdo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$retrovesq"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Caixa de Ar Lado Esquerdo"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$cxaresq"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Porta Dianteira Esquerda"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$ptdiantesq"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Porta Traseira Esquerda"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$pttrasesq"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Lateral direita - Estado Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$latdir"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Para-lama direito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$paralamadir"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Retrovisor Lado Direito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$retrovdir"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Caixa de Ar Lado Direito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$cxardir"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Porta Dianteira Direita"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$ptdiantdir"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Porta Traseira Direita"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$pttrasdir"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Traseira - Estado Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$traseira"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Lanterna Esquerda"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$lantesq"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Lanterna Direita"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$lantdir"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Tampa da Mala"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$tmpmala"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Para-choque Traseiro"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$parachoquet"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Itens Internos Estado Geral"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$itinterno"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Painel"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$painel"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Som"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$som"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Bancos"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$bancos"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Iluminação Interna"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$ilumint"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Tampa do Bagagito"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$tmpbag"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Retrovisor interno"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$retrovint"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Tapetes"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$tapetes"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Pneus"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$pneus"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Step"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$step"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Kit Step"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$kitstep"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Calotas"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$calotas"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Marca bateria"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$bateria"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("SafeCar"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$safecar"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Limpeza interna"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27, 4, utf8_decode("$limpint"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55, 4, utf8_decode("Limpeza externa"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40, 4, utf8_decode("$limpext"), 1, 1);

$pdf->Cell(12);
$pdf->MultiCell(48, 4, utf8_decode("Marca dos Pneus"), 1, 1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(122, 4, utf8_decode("$marcapneus"), 1, 1);

$pdf->Ln(1);
$pdf->Cell(2);
$pdf->MultiCell(181, 3, utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas.\n"), 0, 'J');

// FUNÇÃO PARA ADICIONAR IMAGEM
function adicionarImagemSegura($pdf, $caminho, $x, $y, $largura, $altura) {
    if (empty($caminho)) {
        return false;
    }
    
    if (!file_exists($caminho)) {
        return false;
    }
    
    if (!is_readable($caminho)) {
        return false;
    }
    
    $info = @getimagesize($caminho);
    if ($info === false) {
        return false;
    }
    
    $formatos_suportados = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
    if (!in_array($info[2], $formatos_suportados)) {
        return false;
    }
    
    try {
        $pdf->Image($caminho, $x, $y, $largura, $altura);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// ADICIONAR IMAGENS PRINCIPAIS
adicionarImagemSegura($pdf, $frontal, 12, 203, 32, 32);
adicionarImagemSegura($pdf, $fototraseira, 45, 203, 32, 32);
adicionarImagemSegura($pdf, $direita, 78, 203, 32, 32);
adicionarImagemSegura($pdf, $esquerda, 111, 203, 32, 32);
adicionarImagemSegura($pdf, $selfie, 144, 203, 32, 32);
adicionarImagemSegura($pdf, $fotocnh, 177, 203, 32, 32);

// Posicionamento das assinaturas
if ($ftpainel == '') {
    $yassvist = 242;
    $yimgvist = 265;
    $yasscond = 242;
    $yimgcond = 265;
    $ln = 40;
} else {
    $yassvist = 10;
    $yimgvist = 32;
    $yasscond = 10;
    $yimgcond = 32;
    $ln = 90;
}

$pdf->Ln($ln);

// ============================================
// ASSINATURA DO VISTORIADOR
// ============================================
$pdf->Cell(12);
$pdf->MultiCell(80, 5, utf8_decode("\n_________________________________________________\nAssinatura do Vistoriador \n $vistoriador"), 0, 'L', false);

// Só adiciona a imagem da assinatura do vistoriador se assinadovist for igual a 1
if ($assinadovist == 1) {
    // Tenta carregar a imagem da assinatura do vistoriador
    if (!empty($imgassinaturavist) && file_exists($imgassinaturavist) && is_readable($imgassinaturavist)) {
        adicionarImagemSegura($pdf, $imgassinaturavist, 25, $yimgvist, 25, 25);
    } elseif (!empty($matrvistoriador)) {
        // Tenta encontrar a assinatura pela matrícula
        $caminho_ass_vist = "../../../assinaturas/docs/colass/" . $matrvistoriador . ".png";
        if (file_exists($caminho_ass_vist) && is_readable($caminho_ass_vist)) {
            adicionarImagemSegura($pdf, $caminho_ass_vist, 25, $yimgvist, 25, 25);
        }
    }
}

// ============================================
// ASSINATURA DO CONDUTOR
// ============================================
$pdf->Ln(-20);
$pdf->Cell(95);
$pdf->MultiCell(95, 5, utf8_decode("\n_________________________________________________\nAssinatura do Condutor \n $nome"), 0, 'R', false);

// Só adiciona a imagem da assinatura do condutor se assinado for igual a 1
if ($assinado == 1) {
    // Tenta carregar a imagem da assinatura do condutor
    if (!empty($imgassinaturacond) && file_exists($imgassinaturacond) && is_readable($imgassinaturacond)) {
        adicionarImagemSegura($pdf, $imgassinaturacond, 175, $yimgcond, 25, 25);
    } elseif (!empty($matricula)) {
        // Tenta encontrar a assinatura pela matrícula
        $caminho_ass_cond = "../../../assinaturas/docs/colass/" . $matricula . ".png";
        if (file_exists($caminho_ass_cond) && is_readable($caminho_ass_cond)) {
            adicionarImagemSegura($pdf, $caminho_ass_cond, 175, $yimgcond, 25, 25);
        }
    }
}

// Adiciona fotos extras
adicionarImagemSegura($pdf, $ftpainel, 12, 236, 32, 32);
adicionarImagemSegura($pdf, $extra1, 45, 236, 32, 32);
adicionarImagemSegura($pdf, $extra2, 78, 236, 32, 32);
adicionarImagemSegura($pdf, $extra3, 111, 236, 32, 32);
adicionarImagemSegura($pdf, $extra4, 176, 236, 32, 32);

// Fechar statements
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
if (isset($stmt_fotos)) {
    mysqli_stmt_close($stmt_fotos);
}
mysqli_close($con);

// Limpa buffer e gera PDF
ob_end_clean();
$pdf->Output();
?>