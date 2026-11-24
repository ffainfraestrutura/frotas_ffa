<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Aumenta limites de execução
set_time_limit(300);
ini_set('memory_limit', '512M');

$matricula = trim($_SESSION['matricula']);
$perfil = $_SESSION['perfil'];

// Validação de acesso
if ($perfil == null || $perfil != 2) {
    die("Apenas coordenadores têm acesso");
}

include 'conecta.php';

/* ==============================
    BUSCA COORDENADOR
============================== */
$sql_coord = "SELECT c.idtbcoordenador 
              FROM bdfrota.tbcoord c
              INNER JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula
              WHERE c.matricula = '$matricula'
              AND f.status = 'Ativo'";

$result_coord = mysql_query($sql_coord) or die(mysql_error());
$dados_coord = mysql_fetch_array($result_coord);

if(!$dados_coord){
    die("Coordenador não encontrado.");
}

$idtbcoordenadorlogado = $dados_coord[0];

/* ==============================
    CALCULA SEMANA
============================== */
$segunda = strtotime('monday this week');
$sabado  = date('Y-m-d', strtotime('+5 days', $segunda));
$domingo = date('Y-m-d', strtotime('+6 days', $segunda));

$sabado_br = date('d/m/Y', strtotime($sabado));
$domingo_br = date('d/m/Y', strtotime($domingo));

/* ==============================
    BUSCA SUPERVISORES
============================== */
$sql_sup_ids = "SELECT idtbsupervisor 
                FROM bdfrota.tbsupervisor 
                WHERE idtbcoordenador = $idtbcoordenadorlogado";

$result_sup_ids = mysql_query($sql_sup_ids);
$lista_ids_sups = [];

while($row = mysql_fetch_array($result_sup_ids)) {
    $lista_ids_sups[] = $row[0];
}

if (empty($lista_ids_sups)) {
    die("Nenhum supervisor vinculado ao coordenador.");
}

$lista_ids_sups_str = implode(',', $lista_ids_sups);

/* ==============================
    BUSCA TÉCNICOS E ESCALAS
============================== */
$sql_tecnicos = "SELECT DISTINCT 
                    u.matricula, 
                    f.nome, 
                    u.idtbsupervisor 
                 FROM bdfrota.tbusuario u
                 INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                 WHERE u.perfil = 0
                 AND f.status = 'Ativo'
                 AND u.idtbsupervisor IN ($lista_ids_sups_str)
                 ORDER BY f.nome";

$result_tecnicos = mysql_query($sql_tecnicos) or die(mysql_error());

/* ==============================
    BUSCA ESCALAS DA SEMANA
============================== */
$sql_escalas = "SELECT matricula, DATE(dia) as dia, status
                FROM bdfrota.tbescala
                WHERE DATE(dia) IN ('$sabado','$domingo')
                AND matricula IN (
                    SELECT u.matricula
                    FROM bdfrota.tbusuario u
                    WHERE u.idtbsupervisor IN ($lista_ids_sups_str)
                )";

$result_escalas = mysql_query($sql_escalas) or die(mysql_error());

$escalas = [];

while($row_escala = mysql_fetch_array($result_escalas)) {
    $mat = trim($row_escala['matricula']);
    $dia = $row_escala['dia'];
    $status = $row_escala['status'];
    $escalas[$mat][$dia] = $status;
}

// Define nome do arquivo
$arquivo = "escala-fim-semana-" . date('d-m-Y') . ".xls";

// Limpa qualquer saída anterior
if (ob_get_level()) {
    ob_end_clean();
}

// Envia headers Excel ANTES de qualquer HTML
header("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header("Content-Description: PHP Generated Data");

// Inicia tabela Excel - IMPRIME DIRETO
echo "<table border='1'>";
echo "<thead>";
echo "<tr style='background-color: #0d6efd; color: white; font-weight: bold;'>";
echo "<th colspan='7' style='text-align: center; font-size: 16px; padding: 10px;'>";
echo "ESCALA DE FIM DE SEMANA - TÉCNICOS";
echo "</th>";
echo "</tr>";
echo "<tr style='background-color: #6c757d; color: white;'>";
echo "<th colspan='7' style='text-align: center; padding: 8px;'>";
echo "Sábado: $sabado_br | Domingo: $domingo_br";
echo "</th>";
echo "</tr>";
echo "<tr style='background-color: #343a40; color: white; font-weight: bold;'>";
echo "<th style='width: 25%; padding: 10px;'>TÉCNICO</th>";
echo "<th style='width: 12%; padding: 10px;'>MATRÍCULA</th>";
echo "<th style='width: 18%; padding: 10px;'>SUPERVISOR</th>";
echo "<th style='width: 10%; padding: 10px; text-align: center;'>PLACA</th>";
echo "<th style='width: 11%; padding: 10px; text-align: center;'>SÁBADO ($sabado_br)</th>";
echo "<th style='width: 11%; padding: 10px; text-align: center;'>DOMINGO ($domingo_br)</th>";
echo "<th style='width: 13%; padding: 10px; text-align: center;'>VALOR</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

// Força envio do cabeçalho
flush();

$contador = 0;
$total_escalados_sab = 0;
$total_escalados_dom = 0;

// Loop - IMPRIME CADA LINHA DIRETAMENTE (streaming)
while($tecnico = mysql_fetch_array($result_tecnicos)) {
    
    $matricula_tec = trim($tecnico[0]);
    $nome_tecnico  = $tecnico[1];
    $id_supervisor = $tecnico[2];

    // Nome supervisor
    $nome_supervisor = 'Sem supervisor';
    if($id_supervisor) {
        $sql_sup_nome = "SELECT f.nome
                         FROM bdfrota.tbsupervisor s
                         INNER JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                         WHERE s.idtbsupervisor = $id_supervisor";
        
        $result_sup_nome = mysql_query($sql_sup_nome);
        if($row_sup = mysql_fetch_array($result_sup_nome)) {
            $nome_supervisor = $row_sup[0];
        }
    }

    // Busca placa vinculada
    $placa = 'Sem veículo';
    $sql_placa = "SELECT placaassoc 
                  FROM bdfrota.tbcondutor 
                  WHERE matricula = '$matricula_tec' 
                  AND ativo = 1 
                  AND statuscond = 'COM VEICULO VINCULADO'";
    
    $result_placa = mysql_query($sql_placa);
    if($row_placa = mysql_fetch_array($result_placa)) {
        if($row_placa[0]) {
            $placa = $row_placa[0];
        }
    }

    // Status do sábado e domingo
    $sabado_status  = isset($escalas[$matricula_tec][$sabado])  ? $escalas[$matricula_tec][$sabado]  : 0;
    $domingo_status = isset($escalas[$matricula_tec][$domingo]) ? $escalas[$matricula_tec][$domingo] : 0;

    // Busca valor do saldo (sldprjtd / 5 por dia)
    $valor_total = 0;
    $dias_escalados = 0;
    
    // Busca o saldo mais recente da matrícula
    $sql_saldo = "SELECT (sldprjtd / 5) as sld 
                  FROM bdfrota.tbsaldo 
                  WHERE matricula = '$matricula_tec' 
                  ORDER BY data DESC
                  LIMIT 1";
    
    $result_saldo = mysql_query($sql_saldo);
    $valor_por_dia = 0;
    
    if($row_saldo = mysql_fetch_array($result_saldo)) {
        $valor_por_dia = floatval($row_saldo['sld']);
    }
    
    // Calcula valor baseado nos dias escalados
    if($sabado_status > 0) {
        $dias_escalados++;
    }
    if($domingo_status > 0) {
        $dias_escalados++;
    }
    
    $valor_total = $valor_por_dia * $dias_escalados;

    // Texto do status
    $sabado_texto = 'NÃO';
    $sabado_cor = '#ffffff';
    if($sabado_status == 1) {
        $sabado_texto = 'ESCALADO';
        $sabado_cor = '#d4edda';
        $total_escalados_sab++;
    } elseif($sabado_status == 2) {
        $sabado_texto = 'RESERVA';
        $sabado_cor = '#fff3cd';
    }

    $domingo_texto = 'NÃO';
    $domingo_cor = '#ffffff';
    if($domingo_status == 1) {
        $domingo_texto = 'ESCALADO';
        $domingo_cor = '#d4edda';
        $total_escalados_dom++;
    } elseif($domingo_status == 2) {
        $domingo_texto = 'RESERVA';
        $domingo_cor = '#fff3cd';
    }

    // Formata valor para exibição
    $valor_exibir = '-';
    if($valor_total > 0) {
        $valor_exibir = 'R$ ' . number_format($valor_total, 2, ',', '.');
    }

    // IMPRIME LINHA DIRETAMENTE (streaming)
    echo "<tr>";
    echo "<td style='padding: 8px;'>" . htmlspecialchars($nome_tecnico) . "</td>";
    echo "<td style='padding: 8px; text-align: center;'>" . htmlspecialchars($matricula_tec) . "</td>";
    echo "<td style='padding: 8px; text-align: center;'>" . htmlspecialchars($nome_supervisor) . "</td>";
    echo "<td style='padding: 8px; text-align: center; background-color: #e9ecef; font-weight: bold;'>" . htmlspecialchars($placa) . "</td>";
    echo "<td style='padding: 8px; text-align: center; background-color: $sabado_cor; font-weight: bold;'>$sabado_texto</td>";
    echo "<td style='padding: 8px; text-align: center; background-color: $domingo_cor; font-weight: bold;'>$domingo_texto</td>";
    echo "<td style='padding: 8px; text-align: center; font-weight: bold;'>$valor_exibir</td>";
    echo "</tr>";

    // Flush a cada 50 registros para evitar timeout
    $contador++;
    if ($contador % 50 == 0) {
        flush();
    }
}

// Linha de totais
echo "<tr style='background-color: #e9ecef; font-weight: bold;'>";
echo "<td colspan='4' style='padding: 10px; text-align: right;'>TOTAL ESCALADOS:</td>";
echo "<td style='padding: 10px; text-align: center; background-color: #d4edda;'>$total_escalados_sab</td>";
echo "<td style='padding: 10px; text-align: center; background-color: #d4edda;'>$total_escalados_dom</td>";
echo "<td style='padding: 10px; text-align: center;'>-</td>";
echo "</tr>";

echo "</tbody>";
echo "</table>";

// Fecha conexão e libera recursos
mysql_close($conexao);
exit();
?>