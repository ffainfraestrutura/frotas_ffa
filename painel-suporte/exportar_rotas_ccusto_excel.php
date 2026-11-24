<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

// Validação de sessão
if ($perfil == null) {
  echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
  exit();
}

// Validação de perfil
if (!in_array($perfil, ['3', '4', '5', 3, 4, 5])) {
  echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta página');window.location=\"index.php\"</script>";
  exit();
}

include "../conecta.php";

// Receber parâmetros
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '';
$ccusto = isset($_GET['ccusto']) ? $_GET['ccusto'] : '';

if (empty($periodo) || empty($ccusto)) {
  echo "<script language='javascript' type='text/javascript'>alert('Período e Centro de Custo são obrigatórios');window.history.back();</script>";
  exit();
}

// Calcular início e fim da semana
$dataac = $periodo;
$data_referencia = new DateTime($dataac);
if ($data_referencia->format('N') == 1) {
  $usegunda = $data_referencia->format('Y-m-d');
} else {
  $data_referencia->modify('last monday');
  $usegunda = $data_referencia->format('Y-m-d');
}

$dataInicial = new DateTime($usegunda);
$dataInicial->modify('next sunday');
$pdomingo = $dataInicial->format('Y-m-d');

// Buscar todos os funcionários do centro de custo
$sqlFuncs = "SELECT DISTINCT f.matricula, u.nome 
             FROM bdaniel.tbfuncionario f
             INNER JOIN bdfrota.tbusuario u ON f.matricula = u.matricula
             WHERE TRIM(f.ccusto) = '$ccusto'
             AND f.matricula IS NOT NULL 
             AND f.matricula != ''
             AND u.nome IS NOT NULL 
             AND u.nome != ''
             AND u.nome NOT REGEXP '[0-9]'
             AND u.nome NOT REGEXP '^adm'
             AND LOWER(u.nome) NOT LIKE '%administrador%'
             AND LOWER(u.nome) NOT LIKE '%admin%'
             AND f.matricula NOT IN ('000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999')
             AND f.matricula NOT REGEXP '^(.)\\1{5}$'
             AND f.status = 'Ativo'
             AND f.cargo NOT LIKE 'GEREN%'
             AND f.cargo NOT LIKE 'SUPERVI%'
             AND f.cargo NOT LIKE 'COORDE%'
             ORDER BY u.nome";


$resultFuncs = mysql_query($sqlFuncs) or die(mysql_error());

$dados = array();
$totalGeralSemanal = 0;

// Para cada funcionário, calcular o total semanal
while ($rowFunc = mysql_fetch_array($resultFuncs)) {
  $mat = $rowFunc[0];
  $nomeFunc = $rowFunc[1];
  
  // Calcular total semanal do funcionário
  $dataInicial = new DateTime($usegunda);
  $dataFinal = new DateTime($pdomingo);
  $totaisDiarios = array();
  
  while ($dataInicial <= $dataFinal) {
    $dataDia = $dataInicial->format('Y-m-d');
    
    // Buscar KMs rodados do dia
    $sql = "SELECT ROUND(kmsrodados,1) FROM bdfrota.tbkmrodadosdia WHERE matricula = '$mat' AND dia = '$dataDia'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $kmsrodados = floatval($row[0]);
    
    // Verificar edições de KM
    $sql = "SELECT kmsedit, posneg FROM bdfrota.tbkmdiarioedit WHERE matricula = '$mat' AND dataedit = '$dataDia'";
    $resultado = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($resultado) >= 1) {
      $row = mysql_fetch_array($resultado);
      $kmsedit = $row[0];
      $posneg = $row[1];
      if ($posneg == 1) {
        $kmsrodados = $kmsrodados - $kmsedit;
      } else {
        $kmsrodados = $kmsrodados + $kmsedit;
      }
    }
    
    $totaisDiarios[] = $kmsrodados;
    $dataInicial->add(new DateInterval('P1D'));
  }
  
  $totalSemanal = array_sum($totaisDiarios);
  $mediaDiaria = $totalSemanal / 6;
  
  $dados[] = array(
    'matricula' => $mat,
    'nome' => $nomeFunc,
    'total' => $totalSemanal,
    'media' => $mediaDiaria
  );
  
  $totalGeralSemanal += $totalSemanal;
}

// Ordenar do maior para o menor
usort($dados, function($a, $b) {
  return $b['total'] <=> $a['total'];
});

$mediaGeralDiaria = $totalGeralSemanal / 6;

// Gerar arquivo Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Relatorio_" . $ccusto . "_" . date('Ymd', strtotime($periodo)) . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // BOM para UTF-8

echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
echo "<style>";
echo "table { border-collapse: collapse; width: 100%; }";
echo "th, td { border: 1px solid black; padding: 8px; text-align: left; }";
echo "th { background-color: #4CAF50; color: white; font-weight: bold; text-align: center; }";
echo ".total { background-color: #333; color: white; font-weight: bold; }";
echo ".numero { text-align: center; }";
echo ".header-titulo { background-color: #2196F3; color: white; font-size: 16px; font-weight: bold; text-align: center; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<table>";

// Cabeçalho do relatório
echo "<tr><td colspan='5' class='header-titulo'>RELATÓRIO DE DESLOCAMENTO - " . htmlspecialchars($ccusto) . "</td></tr>";
echo "<tr><td colspan='5' class='header-titulo'>Período: " . date('d/m/Y', strtotime($usegunda)) . " a " . date('d/m/Y', strtotime($pdomingo)) . "</td></tr>";
echo "<tr><td colspan='5'>&nbsp;</td></tr>";

// Cabeçalho da tabela
echo "<tr>";
echo "<th class='numero'>#</th>";
echo "<th>Matrícula</th>";
echo "<th>Nome do Funcionário</th>";
echo "<th class='numero'>Total Semanal (km)</th>";
echo "<th class='numero'>Média Diária (km)</th>";
echo "</tr>";

// Dados dos funcionários
$posicao = 1;
foreach ($dados as $func) {
  echo "<tr>";
  echo "<td class='numero'>" . $posicao . "</td>";
  echo "<td>" . htmlspecialchars($func['matricula']) . "</td>";
  echo "<td>" . htmlspecialchars($func['nome']) . "</td>";
  echo "<td class='numero'>" . number_format($func['total'], 1, ',', '.') . "</td>";
  echo "<td class='numero'>" . number_format($func['media'], 1, ',', '.') . "</td>";
  echo "</tr>";
  $posicao++;
}

// Totais
echo "<tr class='total'>";
echo "<td colspan='3' style='text-align: right;'>TOTAL GERAL:</td>";
echo "<td class='numero'>" . number_format($totalGeralSemanal, 1, ',', '.') . " km</td>";
echo "<td class='numero'>" . number_format($mediaGeralDiaria, 1, ',', '.') . " km</td>";
echo "</tr>";

echo "<tr class='total'>";
echo "<td colspan='5' class='numero'>Total de Funcionários: " . count($dados) . "</td>";
echo "</tr>";

// Fórmula de cálculo
echo "<tr><td colspan='5'>&nbsp;</td></tr>";
echo "<tr><td colspan='5' style='background-color: #E3F2FD; padding: 10px;'>";
echo "<strong>Fórmula de Cálculo:</strong><br>";
echo "• Média Diária Individual: Total Semanal do Funcionário ÷ 6 dias (1 dia de descanso)<br>";
echo "• Total Geral: Soma de todos os totais semanais individuais<br>";
echo "• Média Geral Diária: Total Geral ÷ 6 dias";
echo "</td></tr>";

echo "</table>";
echo "</body>";
echo "</html>";

exit();
?>
