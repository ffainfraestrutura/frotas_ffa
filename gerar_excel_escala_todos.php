<?php
date_default_timezone_set('America/Sao_Paulo');

// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

// ====================================
// CONFIGURAÇÕES DE VALORES MÍNIMOS
// ====================================
$VALOR_MINIMO_DIA = 45;        // Valor mínimo por dia
$VALOR_EXIBICAO_MINIMA = 45;   // Valor que será exibido se for menor

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];

include"conecta.php";
header("Content-type: text/html; charset=utf-8");

// Nome do arquivo
$arquivo = 'Escala_Todos_Tecnicos_'.date('d-m-Y').'.xls';

// Busca TODOS os técnicos ativos
$sql_tecnicos = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor, v.tipovel
                 FROM bdfrota.tbusuario u
                 INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                 LEFT JOIN bdfrota.tbveiculo v ON u.matricula = v.matcond
                 WHERE u.perfil = 0
                 AND f.status = 'Ativo'
                 ORDER BY f.nome";

$result_tecnicos = mysql_query($sql_tecnicos);

// Busca TODAS as escalas
$sql_escalas = "SELECT matricula, DATE(dia) as dia, status
                FROM bdfrota.tbescala
                WHERE matricula IN (
                    SELECT u.matricula
                    FROM bdfrota.tbusuario u
                    WHERE u.perfil = 0
                )
                ORDER BY dia DESC";

$result_escalas = mysql_query($sql_escalas);
$escalas = [];

if($result_escalas) {
  while($row_escala = mysql_fetch_array($result_escalas)) {
    $mat = trim($row_escala['matricula']);
    $dia = $row_escala['dia'];
    $status = $row_escala['status'];
    
    if(!isset($escalas[$mat])) {
      $escalas[$mat] = [];
    }
    $escalas[$mat][$dia] = $status;
  }
}

// Monta array de técnicos com todos os dados
$tecnicos_data = [];

if($result_tecnicos) {
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

    // Nome coordenador
    $nome_coordenador = 'Sem coordenador';
    if($id_supervisor) {
      $sql_coord_nome = "SELECT f.nome
                         FROM bdfrota.tbcoord c
                         INNER JOIN bdfrota.tbsupervisor s ON c.idtbcoordenador = s.idtbcoordenador
                         INNER JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula
                         WHERE s.idtbsupervisor = $id_supervisor";
      
      $result_coord_nome = mysql_query($sql_coord_nome);
      if($row_coord = mysql_fetch_array($result_coord_nome)) {
        $nome_coordenador = $row_coord[0];
      }
    }

    // Busca placa
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

    // Conta total de escalas
    $total_sabados = 0;
    $total_domingos = 0;
    $total_dias = 0;
    
    if(isset($escalas[$matricula_tec])) {
      foreach($escalas[$matricula_tec] as $dia => $status) {
        if($status > 0) {
          $dia_semana = date('w', strtotime($dia));
          if($dia_semana == 6) { // Sábado
            $total_sabados++;
          } else if($dia_semana == 0) { // Domingo
            $total_domingos++;
          }
          $total_dias++;
        }
      }
    }

    // Busca valor do saldo
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
    
    // Aplicar regra de valor para tipos de veículo específicos (1 ou 4)
    // Se tipovel é 1 ou 4: se valor <= 45, mantém 45; senão, mantém 75
    if ($tipovel == 1 || $tipovel == 4) {
      $valor_por_dia = ($valor_por_dia <= 45) ? 45 : 75;
    } elseif($valor_por_dia > 0 && $valor_por_dia < $VALOR_MINIMO_DIA) {
      // Para outros tipos, aplica o valor mínimo
      $valor_por_dia = $VALOR_EXIBICAO_MINIMA;
    }
    
    $valor_total = $valor_por_dia * $total_dias;

    $tecnicos_data[] = [
      'matricula' => $matricula_tec,
      'nome' => $nome_tecnico,
      'coordenador' => $nome_coordenador,
      'supervisor' => $nome_supervisor,
      'placa' => $placa,
      'total_sabados' => $total_sabados,
      'total_domingos' => $total_domingos,
      'total_dias' => $total_dias,
      'valor_por_dia' => $valor_por_dia,
      'valor_total' => $valor_total
    ];
  }
}

// Cria o cabeçalho do arquivo Excel
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename={$arquivo}");
header("Pragma: no-cache");
header("Expires: 0");

// Inicia o HTML da tabela
echo "\xEF\xBB\xBF"; // BOM para UTF-8
echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>';
echo 'table { border-collapse: collapse; font-family: Arial, sans-serif; width: 100%; }';
echo 'th { background-color: #0d6efd; color: white; font-weight: bold; border: 1px solid #000; padding: 10px; text-align: center; font-size: 12px; }';
echo 'td { border: 1px solid #000; padding: 8px; font-size: 11px; }';
echo '.centro { text-align: center; }';
echo '.direita { text-align: right; }';
echo '.destaque { background-color: #d4edda; font-weight: bold; }';
echo '.footer-info { margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6; font-size: 10px; color: #6c757d; }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>MATRÍCULA</th>';
echo '<th>TÉCNICO</th>';
echo '<th>COORDENADOR</th>';
echo '<th>SUPERVISOR</th>';
echo '<th>PLACA</th>';
echo '<th>TOTAL SÁBADOS</th>';
echo '<th>TOTAL DOMINGOS</th>';
echo '<th>TOTAL DIAS</th>';
echo '<th>VALOR/DIA</th>';
echo '<th>VALOR TOTAL</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach($tecnicos_data as $tecnico) {
  echo '<tr>';
  echo '<td class="centro">' . htmlspecialchars($tecnico['matricula']) . '</td>';
  echo '<td>' . htmlspecialchars($tecnico['nome']) . '</td>';
  echo '<td class="centro">' . htmlspecialchars($tecnico['coordenador']) . '</td>';
  echo '<td class="centro">' . htmlspecialchars($tecnico['supervisor']) . '</td>';
  echo '<td class="centro">' . htmlspecialchars($tecnico['placa']) . '</td>';
  echo '<td class="centro">' . $tecnico['total_sabados'] . '</td>';
  echo '<td class="centro">' . $tecnico['total_domingos'] . '</td>';
  echo '<td class="centro destaque">' . $tecnico['total_dias'] . '</td>';
  
  // Valor/dia e total já foram calculados com mínimo aplicado
  if($tecnico['valor_por_dia'] > 0) {
    echo '<td class="direita">R$ ' . number_format($tecnico['valor_por_dia'], 2, ',', '.') . '</td>';
  } else {
    echo '<td class="direita">-</td>';
  }
  
  // Valor total (já tem mínimo aplicado no cálculo)
  if($tecnico['valor_total'] > 0) {
    echo '<td class="direita destaque">R$ ' . number_format($tecnico['valor_total'], 2, ',', '.') . '</td>';
  } else {
    echo '<td class="direita destaque">-</td>';
  }
  
  echo '</tr>';
}

echo '</tbody>';
echo '</table>';

echo '<div class="footer-info">';
echo '<strong>Gerado em:</strong> ' . date('d/m/Y H:i:s') . ' | ';
echo '<strong>Total de técnicos:</strong> ' . count($tecnicos_data);
echo '</div>';

echo '</body>';
echo '</html>';

exit;
?>
