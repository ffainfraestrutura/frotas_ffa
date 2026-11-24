<?php
header("Content-Type: text/html; charset=utf-8");
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
    FUNCAO REMOVER ACENTOS
============================== */
function remover_acentos($text) {
    $text = str_replace(
        array('á', 'à', 'ã', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'õ', 'ô', 'ö', 'ú', 'ù', 'û', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Í', 'Ì', 'Î', 'Ï', 'Ó', 'Ò', 'Õ', 'Ô', 'Ö', 'Ú', 'Ù', 'Û', 'Ü', 'Ç'),
        array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'C'),
        $text
    );
    return $text;
}

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
                    u.idtbsupervisor,
                    v.tipovel
                 FROM bdfrota.tbusuario u
                 INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                 LEFT JOIN bdfrota.tbveiculo v ON u.matricula = v.matcond
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
echo "<tr style='background-color: #343a40; color: white; font-weight: bold;'>";
echo "<th style='width: 15%; padding: 10px;'>MATRICULA</th>";
echo "<th style='width: 30%; padding: 10px;'>TECNICO</th>";
echo "<th style='width: 20%; padding: 10px;'>SUPERVISOR</th>";
echo "<th style='width: 15%; padding: 10px; text-align: center;'>SABADO ($sabado_br)</th>";
echo "<th style='width: 15%; padding: 10px; text-align: center;'>DOMINGO ($domingo_br)</th>";
echo "<th style='width: 20%; padding: 10px; text-align: center;'>VALOR</th>";
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
    $tipovel = $tecnico[3];

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

    // Status do sábado e domingo
    $sabado_status  = isset($escalas[$matricula_tec][$sabado])  ? $escalas[$matricula_tec][$sabado]  : 0;
    $domingo_status = isset($escalas[$matricula_tec][$domingo]) ? $escalas[$matricula_tec][$domingo] : 0;

    // Texto do status
    $sabado_texto = 'NAO';
    $sabado_cor = '#ffffff';
    if($sabado_status == 1) {
        $sabado_texto = 'ESCALADO';
        $sabado_cor = '#d4edda';
        $total_escalados_sab++;
    } elseif($sabado_status == 2) {
        $sabado_texto = 'RESERVA';
        $sabado_cor = '#fff3cd';
    }

    $domingo_texto = 'NAO';
    $domingo_cor = '#ffffff';
    if($domingo_status == 1) {
        $domingo_texto = 'ESCALADO';
        $domingo_cor = '#d4edda';
        $total_escalados_dom++;
    } elseif($domingo_status == 2) {
        $domingo_texto = 'RESERVA';
        $domingo_cor = '#fff3cd';
    }

    // CÁLCULO DO VALOR
    $valor_exibir = '-';
    $dias_escalados = 0;
    
    if($sabado_status > 0) $dias_escalados++;
    if($domingo_status > 0) $dias_escalados++;
    
    if($dias_escalados > 0) {
        $sql_saldo = "SELECT sldprjtd 
                      FROM bdfrota.tbsaldo 
                      WHERE matricula = '$matricula_tec' 
                      ORDER BY data DESC 
                      LIMIT 1";
        
        $result_saldo = mysql_query($sql_saldo);
        
        if($result_saldo && mysql_num_rows($result_saldo) > 0) {
            $row_saldo = mysql_fetch_array($result_saldo);
            $saldo_projetado = floatval($row_saldo[0]);
            
            // DEBUG: Verifica se o saldo existe
            if($saldo_projetado > 0) {
                // Cálculo base: saldo / 5 * dias escalados (mesmo que inserir_seg_ter em insercaofimsemanaDEV)
                $valor_calculado = ($saldo_projetado / 5) * $dias_escalados;
                
                // Aplicar regra de valor para tipos de veículo específicos (1 ou 4)
                // Se tipovel é 1 ou 4: se valor <= 45, mantém 45; senão, mantém o valor
                if ($tipovel == 1 || $tipovel == 4) {
                    $valor_calculado = ($valor_calculado <= 45) ? 45 : $valor_calculado;
                } else {
                    // Para outros tipos: se valor <= 75, mantém 75; senão, mantém o valor
                    $valor_calculado = ($valor_calculado <= 75) ? 75 : $valor_calculado;
                }
                
                $valor_exibir = 'R$ ' . number_format($valor_calculado, 2, ',', '.');
            } else {
                $valor_exibir = 'Sem saldo';
            }
        } else {
            $valor_exibir = 'Sem registro';
        }
    }

    // IMPRIME LINHA DIRETAMENTE (streaming)
    echo "<tr>";
    echo "<td style='padding: 8px;'>'". htmlspecialchars($matricula_tec) . "</td>";
    echo "<td style='padding: 8px;'>" . htmlspecialchars(remover_acentos($nome_tecnico)) . "</td>";
    echo "<td style='padding: 8px; text-align: center;'>" . htmlspecialchars(remover_acentos($nome_supervisor)) . "</td>";
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
echo "<td colspan='3' style='padding: 10px; text-align: right;'>TOTAL ESCALADOS:</td>";
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