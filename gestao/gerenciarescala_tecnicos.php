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

if($perfil==''){
  $perfil = $_GET['perfil'];
}

if($matricula1==''){
  $matricula1 = $_GET['mat'];
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

// Validação de acesso - qualquer perfil exceto 0 (técnicos)
if($perfil == 0 || $perfil == ''){
  echo "<script>alert('Você não tem permissão para acessar esta página'); window.location='../index.php';</script>";
  exit;
}

include"../conecta.php";
header("Content-type: text/html; charset=utf-8");

// Inicializa variáveis padrão
$coordenadores = [];
$supervisores = [];
$tecnicos_data = [];
$erro_conexao = false;
$mostrar_dados = false;

// Recebe filtros via GET
$periodo_filtro = isset($_GET['periodo']) ? $_GET['periodo'] : 'TODOS';
$coordenador_filtro = isset($_GET['coordenador']) ? $_GET['coordenador'] : 'TODOS';
$supervisor_filtro = isset($_GET['supervisor']) ? $_GET['supervisor'] : 'TODOS';

// Define se deve mostrar dados (apenas se o botão filtrar foi clicado)
$mostrar_dados = isset($_GET['periodo']) || isset($_GET['coordenador']) || isset($_GET['supervisor']);

// Verifica se a conexão existe (conecta.php usa $conexao)
if(!isset($conexao) || !$conexao) {
  $erro_conexao = true;
} else {
  /* ==============================
      BUSCA TODOS OS COORDENADORES ATIVOS
  ============================== */
  $sql_coords = "SELECT c.idtbcoordenador, f.nome, f.matricula
                FROM bdfrota.tbcoord c
                INNER JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula
                WHERE f.status = 'Ativo'
                ORDER BY f.nome";

  $result_coords = mysql_query($sql_coords);
  
  if($result_coords) {
    while($row_coord = mysql_fetch_array($result_coords)) {
      $coordenadores[] = [
        'id' => $row_coord[0],
        'nome' => $row_coord[1],
        'matricula' => $row_coord[2]
      ];
    }
  }

  /* ==============================
      SE COORDENADOR FOI SELECIONADO, BUSCA SUPERVISORES
  ============================== */
  if($coordenador_filtro && $coordenador_filtro != 'TODOS') {
    $sql_sups = "SELECT s.idtbsupervisor, f.nome, f.matricula
                 FROM bdfrota.tbsupervisor s
                 INNER JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                 WHERE s.idtbcoordenador = '$coordenador_filtro'
                 AND f.status = 'Ativo'
                 ORDER BY f.nome";
    
    $result_sups = mysql_query($sql_sups);
    
    if($result_sups) {
      while($row_sup = mysql_fetch_array($result_sups)) {
        $supervisores[] = [
          'id' => $row_sup[0],
          'nome' => $row_sup[1],
          'matricula' => $row_sup[2]
        ];
      }
    }
  }

  /* ==============================
      SE MOSTRAR_DADOS = TRUE, BUSCA TÉCNICOS E ESCALAS
  ============================== */
  if($mostrar_dados) {
    
    // Calcula datas do fim de semana baseado no período selecionado
    // Se TODOS, pega o último período disponível apenas para referência
    if($periodo_filtro == 'TODOS') {
      $sql_ultimo_periodo = "SELECT DATE(data) FROM bdfrota.tbsaldo WHERE DAYOFWEEK(data) = 2 ORDER BY data DESC LIMIT 1";
      $result_ultimo = mysql_query($sql_ultimo_periodo);
      if($row_ultimo = mysql_fetch_array($result_ultimo)) {
        $periodo_ref = $row_ultimo[0];
      } else {
        $periodo_ref = date('Y-m-d', strtotime('last monday'));
      }
      $sabado  = date('Y-m-d', strtotime($periodo_ref . ' +5 days'));
      $domingo = date('Y-m-d', strtotime($periodo_ref . ' +6 days'));
    } else {
      $sabado  = date('Y-m-d', strtotime($periodo_filtro . ' +5 days'));
      $domingo = date('Y-m-d', strtotime($periodo_filtro . ' +6 days'));
    }

    // Monta filtro de supervisor
    $filtro_supervisor = '';
    if($supervisor_filtro != 'TODOS' && !empty($supervisor_filtro)) {
      $filtro_supervisor = " AND u.idtbsupervisor = '$supervisor_filtro'";
    } else if($coordenador_filtro != 'TODOS' && !empty($coordenador_filtro)) {
      // Se tem coordenador mas não supervisor, busca todos supervisores do coordenador
      $filtro_supervisor = " AND u.idtbsupervisor IN (
        SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE idtbcoordenador = '$coordenador_filtro'
      )";
    }

    // Busca técnicos
    $sql_tecnicos = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor, v.tipovel
                     FROM bdfrota.tbusuario u
                     INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                     LEFT JOIN bdfrota.tbveiculo v ON u.matricula = v.matcond
                     WHERE u.perfil = 0
                     AND f.status = 'Ativo'
                     $filtro_supervisor
                     ORDER BY f.nome";

    $result_tecnicos = mysql_query($sql_tecnicos);

    // Busca escalas
    $filtro_supervisor_escala = '';
    if($supervisor_filtro != 'TODOS' && !empty($supervisor_filtro)) {
      $filtro_supervisor_escala = " AND u.idtbsupervisor = '$supervisor_filtro'";
    } else if($coordenador_filtro != 'TODOS' && !empty($coordenador_filtro)) {
      $filtro_supervisor_escala = " AND u.idtbsupervisor IN (
        SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE idtbcoordenador = '$coordenador_filtro'
      )";
    }
    
    // Filtro de período para escalas
    $filtro_periodo = '';
    if($periodo_filtro != 'TODOS') {
      $filtro_periodo = " AND DATE(dia) IN ('$sabado','$domingo')";
    }
    
    $sql_escalas = "SELECT matricula, DATE(dia) as dia, status
                    FROM bdfrota.tbescala
                    WHERE 1=1
                    $filtro_periodo
                    AND matricula IN (
                        SELECT u.matricula
                        FROM bdfrota.tbusuario u
                        WHERE u.perfil = 0
                        $filtro_supervisor_escala
                    )";

    $result_escalas = mysql_query($sql_escalas);
    $escalas = [];

    if($result_escalas) {
      while($row_escala = mysql_fetch_array($result_escalas)) {
        $mat = trim($row_escala['matricula']);
        $dia = $row_escala['dia'];
        $status = $row_escala['status'];
        
        if($periodo_filtro == 'TODOS') {
          // Para TODOS, acumula todos os dias
          if(!isset($escalas[$mat])) {
            $escalas[$mat] = [];
          }
          $escalas[$mat][$dia] = $status;
        } else {
          // Para período específico, apenas sábado e domingo
          $escalas[$mat][$dia] = $status;
        }
      }
    }

    // Monta array de técnicos
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

        $sabado_status  = 0;
        $domingo_status = 0;
        $sabado_data = '';
        $domingo_data = '';
        
        if($periodo_filtro == 'TODOS') {
          // Para TODOS, mostra status do último fim de semana encontrado
          $ultimo_sabado = null;
          $ultimo_domingo = null;
          
          if(isset($escalas[$matricula_tec])) {
            foreach($escalas[$matricula_tec] as $dia => $status) {
              $dia_semana = date('w', strtotime($dia));
              if($dia_semana == 6) { // Sábado
                if($ultimo_sabado === null || $dia > $ultimo_sabado) {
                  $ultimo_sabado = $dia;
                  $sabado_status = $status;
                  $sabado_data = $dia;
                }
              } else if($dia_semana == 0) { // Domingo
                if($ultimo_domingo === null || $dia > $ultimo_domingo) {
                  $ultimo_domingo = $dia;
                  $domingo_status = $status;
                  $domingo_data = $dia;
                }
              }
            }
          }
        } else {
          $sabado_status  = isset($escalas[$matricula_tec][$sabado])  ? $escalas[$matricula_tec][$sabado]  : 0;
          $domingo_status = isset($escalas[$matricula_tec][$domingo]) ? $escalas[$matricula_tec][$domingo] : 0;
          $sabado_data = $sabado_status > 0 ? $sabado : '';
          $domingo_data = $domingo_status > 0 ? $domingo : '';

          // Em período específico, exibe apenas técnicos com escala no fim de semana.
          if($sabado_status <= 0 && $domingo_status <= 0) {
            continue;
          }
        }

        // Busca valor do saldo
        $valor_total = 0;
        $dias_escalados = 0;
        
        // Se período é TODOS, conta o total de dias escalados
        if($periodo_filtro == 'TODOS' && isset($escalas[$matricula_tec])) {
          foreach($escalas[$matricula_tec] as $dia => $status) {
            if($status > 0) {
              $dias_escalados++;
            }
          }
        } else {
          if($sabado_status > 0) $dias_escalados++;
          if($domingo_status > 0) $dias_escalados++;
        }
        
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
        
        // Aplica regra de valor para tipos de veículo específicos (1 ou 4)
        // Se tipovel é 1 ou 4: se valor <= 45, mantém 45; senão, mantém o valor
        // Para outros tipos: se valor <= 75, mantém 75; senão, mantém o valor
        if ($tipovel == 1 || $tipovel == 4) {
          $valor_por_dia = ($valor_por_dia <= 45) ? 45 : $valor_por_dia;
        } else {
          // Para outros tipos, se valor <= 75, mantém 75
          $valor_por_dia = ($valor_por_dia <= 75) ? 75 : $valor_por_dia;
        }
        
        // Calcula valor total (mínimo já foi aplicado ao valor_por_dia)
        $valor_total = $valor_por_dia * $dias_escalados;

        $tecnicos_data[] = [
          'matricula' => $matricula_tec,
          'nome' => $nome_tecnico,
          'coordenador' => $nome_coordenador,
          'supervisor' => $nome_supervisor,
          'placa' => $placa,
          'sabado_status' => $sabado_status,
          'domingo_status' => $domingo_status,
          'sabado_data' => $sabado_data,
          'domingo_data' => $domingo_data,
          'valor' => $valor_total,
          'dias_escalados' => $dias_escalados
        ];
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Relatório  Escalas de Fins de Semana </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

  <style type="text/css">
    body{
      font-size: 12px;
    }

    .btn{
      font-size: 12px;
    }

    /* Estilos da escala */
    .card-escala { 
      border-left: 5px solid #28a745; 
      box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
    }
    .table tbody td {
      font-size: 14px !important;
    }
    .sabado { background-color: #e3f2fd !important; }
    .domingo { background-color: #f5edf1 !important; }
    .status-ativo { 
      background: linear-gradient(45deg, #d4edda, #c3e6cb) !important; 
      border: 2px solid #d4edda !important;
    }
    .status-reserva { 
      background: linear-gradient(45deg, #fff3cd, #ffeaa7) !important; 
      border: 2px solid #ffc107 !important;
    }
    .supervisor-tag { 
      color: #212529 !important;
      font-weight: 600;
      font-size: 13px;
    }
    .card-header-escala {
      background: linear-gradient(45deg, #0d6efd, #083b65) !important;
      color: white !important;
    }
    .table-dark th {
      background: linear-gradient(45deg, #0d6efd, #0a58ca) !important;
      color: white !important;
      font-size: 14px;
    }
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <!--nav-->
  <?php include "../menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">
      <div class="container-fluid px-4">
        <h1 class="h1 mt-3 mb-3">Relatório  Escalas de Fins de Semana</h1>
        
        <!-- FILTROS -->
        <div class="mb-4">
          <form action="" method="GET">
            <div class="row g-3">
              <!-- Período -->
              <div class="col-md-3">
                <label for="periodoSelect" class="form-label">Período:</label>
                <select class="form-select" id="periodoSelect" name="periodo">
                  <option value="TODOS" <?php echo ($periodo_filtro == 'TODOS') ? 'selected' : ''; ?>>Todos os Períodos</option>
                  <?php
                  // Calcula a segunda-feira da semana atual
                  $hoje = date('Y-m-d');
                  $dia_semana_hoje = date('w', strtotime($hoje)); // 0=domingo, 1=segunda, ..., 6=sábado
                  
                  // Se hoje é domingo (0), segunda é amanhã. Caso contrário, calcula para trás
                  if ($dia_semana_hoje == 0) {
                    $segunda_atual = date('Y-m-d', strtotime('tomorrow'));
                  } else {
                    $segunda_atual = date('Y-m-d', strtotime('last monday'));
                  }
                  
                  $domingo_atual = date('Y-m-d', strtotime($segunda_atual . ' +6 days'));
                  $segunda_atual_fmt = date('d/m/Y', strtotime($segunda_atual));
                  $domingo_atual_fmt = date('d/m/Y', strtotime($domingo_atual));
                  
                  // Opção da semana atual (destacada)
                  $selected_atual = ($periodo_filtro == $segunda_atual) ? 'selected' : '';
                  echo "<option value='$segunda_atual' $selected_atual style='font-weight: bold; background-color: #d4edda;'>SEMANA ATUAL ($segunda_atual_fmt até $domingo_atual_fmt)</option>";
                  echo "<option disabled>─────────────────────────────────────</option>";
                  
                  // Busca períodos disponíveis (segundas-feiras da tabela tbsaldo)
                  $sql_periodos = "SELECT DISTINCT DATE(data) as data_periodo 
                                  FROM bdfrota.tbsaldo 
                                  WHERE DAYOFWEEK(data) = 2
                                  ORDER BY data DESC";
                  $resultado_periodos = mysql_query($sql_periodos);
                  
                  if($resultado_periodos) {
                    while ($row_periodo = mysql_fetch_array($resultado_periodos)) {
                      $data_seg = $row_periodo[0];
                      
                      // Pula a semana atual se já foi listada
                      if($data_seg == $segunda_atual) continue;
                      
                      $data_dom = date('Y-m-d', strtotime($data_seg . ' +6 days'));
                      
                      $data_seg_fmt = date('d/m/Y', strtotime($data_seg));
                      $data_dom_fmt = date('d/m/Y', strtotime($data_dom));
                      
                      $selected = ($periodo_filtro == $data_seg) ? 'selected' : '';
                      echo "<option value='$data_seg' $selected>$data_seg_fmt até $data_dom_fmt</option>";
                    }
                  }
                  ?>
                </select>
              </div>
              
              <!-- Coordenador -->
              <div class="col-md-2">
                <label for="coordenadorSelect" class="form-label">Coordenador:</label>
                <select class="form-select" id="coordenadorSelect" name="coordenador">
                  <option value="TODOS" <?php echo ($coordenador_filtro == 'TODOS' || $coordenador_filtro == '') ? 'selected' : ''; ?>>Todos</option>
                  <?php foreach($coordenadores as $coord): ?>
                    <option value="<?php echo $coord['id']; ?>" <?php echo ($coordenador_filtro == $coord['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($coord['nome']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <!-- Supervisor -->
              <div class="col-md-2">
                <label for="supervisorSelect" class="form-label">Supervisor:</label>
                <select class="form-select" id="supervisorSelect" name="supervisor">
                  <option value="TODOS" <?php echo ($supervisor_filtro == 'TODOS' || $supervisor_filtro == '') ? 'selected' : ''; ?>>Todos</option>
                  <?php foreach($supervisores as $sup): ?>
                    <option value="<?php echo $sup['id']; ?>" <?php echo ($supervisor_filtro == $sup['id']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($sup['nome']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <!-- Busca -->
              <div class="col-md-3">
                <label for="searchInput" class="form-label">Buscar:</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Digite para filtrar...">
              </div>
              
              <!-- Botões -->
              <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success">Filtrar</button>
                <a href="?" class="btn btn-outline-secondary">Limpar</a>
              </div>
            </div>
          </form>
        </div>

        <!-- TABELA -->
        <div class="card mb-4 mt-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <i class="fas fa-table me-1"></i>
              Escala de Técnicos
              <?php if($mostrar_dados && isset($sabado) && $periodo_filtro != 'TODOS'): ?>
                <span class="text-muted ms-2">
                  (<?php echo date('d/m/Y', strtotime($sabado)); ?> - <?php echo date('d/m/Y', strtotime($domingo)); ?>)
                </span>
              <?php elseif($mostrar_dados && $periodo_filtro == 'TODOS'): ?>
                <span class="text-muted ms-2">(Todos os Períodos)</span>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
              <?php if($mostrar_dados): ?>
              <button type="button" onclick="exportarFiltrados()" class="btn btn-success" 
                      data-bs-toggle="tooltip" data-bs-placement="top" 
                      title="Exporta os dados exibidos na tela. Aplique filtros para exportar grupos específicos ou use a busca para encontrar registros individuais. Com filtro 'Todos' selecionado, exporta todos os técnicos.">
                <i class="fas fa-file-excel"></i>
                Exportar Filtrados
              </button>
              <a href="../gerar_excel_escala_todos.php" class="btn btn-primary"
                 data-bs-toggle="tooltip" data-bs-placement="top"
                 title="Exporta o resumo completo de todos os técnicos com totais de escalas, independente dos filtros aplicados na tela.">
                <i class="fas fa-file-excel"></i>
                Exportar Totais
              </a>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-body">
            <table id="tabelaEscala" class="table table-bordered table-hover" style="font-size: 12px !important; width: 100%;">
              <thead class="table-dark">
                <tr>
                  <th><i class="fas fa-id-card me-1"></i>Matrícula</th>
                  <th><i class="fas fa-user me-1"></i>Técnico</th>
                  <th><i class="fas fa-user-tie me-1"></i>Coordenador</th>
                  <th><i class="fas fa-users-cog me-1"></i>Supervisor</th>
                  <th class="text-center"><i class="fas fa-car me-1"></i>Placa</th>
                  <th class="text-center"><i class="fas fa-calendar-day me-1"></i>Sábado</th>
                  <th class="text-center"><i class="fas fa-calendar-day me-1"></i>Domingo</th>
                  <th class="text-center"><i class="fas fa-dollar-sign me-1"></i>Valor</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!$mostrar_dados): ?>
                <tr>
                  <td colspan="7" class="text-center">
                    <div class="alert alert-info m-3">
                      <i class="fas fa-info-circle me-2"></i>
                      Selecione os filtros desejados e clique em <strong>Filtrar</strong> para exibir os dados da escala.
                    </div>
                  </td>
                </tr>
                <?php else: ?>
                  <?php if(empty($tecnicos_data)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                      <i class="fas fa-users fa-4x mb-4 opacity-50"></i>
                      <h4>Nenhum técnico encontrado</h4>
                      <p>Tente ajustar os filtros e buscar novamente.</p>
                    </td>
                  </tr>
                  <?php else: ?>
                    <?php foreach($tecnicos_data as $tecnico): ?>
                    <tr>
                      <td class="fw-bold align-middle">
                        <?php echo htmlspecialchars($tecnico['matricula']); ?>
                      </td>
                      <td class="fw-bold ps-3 align-middle">
                        <div><?php echo htmlspecialchars($tecnico['nome']); ?></div>
                      </td>
                      <td class="text-center align-middle">
                        <span class="supervisor-tag"><?php echo htmlspecialchars($tecnico['coordenador']); ?></span>
                      </td>
                      <td class="text-center align-middle">
                        <span class="supervisor-tag"><?php echo htmlspecialchars($tecnico['supervisor']); ?></span>
                      </td>
                      <td class="text-center align-middle">
                        <span class="fw-bold text-dark">
                          <?php echo htmlspecialchars($tecnico['placa']); ?>
                        </span>
                      </td>
                      <td class="text-center sabado align-middle <?php echo $tecnico['sabado_status'] ? ($tecnico['sabado_status']==1 ? 'status-ativo' : 'status-reserva') : ''; ?>" data-sort="<?php echo $tecnico['sabado_status'] == 1 ? 'A_ESCALADO' : ($tecnico['sabado_status'] == 2 ? 'B_RESERVA' : 'C_NAO'); ?>">
                        <span class="fw-bold" style="font-size: 13px !important;">
                          <?php 
                          if($tecnico['sabado_status']==1) {
                            echo 'ESCALADO';
                            if(!empty($tecnico['sabado_data'])) {
                              echo '<br><small style="font-size: 11px;">' . date('d/m/Y', strtotime($tecnico['sabado_data'])) . '</small>';
                            }
                          } else if($tecnico['sabado_status']==2) {
                            echo 'RESERVA';
                            if(!empty($tecnico['sabado_data'])) {
                              echo '<br><small style="font-size: 11px;">' . date('d/m/Y', strtotime($tecnico['sabado_data'])) . '</small>';
                            }
                          } else {
                            echo 'NAO';
                          }
                          ?>
                        </span>
                      </td>
                      <td class="text-center domingo align-middle <?php echo $tecnico['domingo_status'] ? ($tecnico['domingo_status']==1 ? 'status-ativo' : 'status-reserva') : ''; ?>" data-sort="<?php echo $tecnico['domingo_status'] == 1 ? 'A_ESCALADO' : ($tecnico['domingo_status'] == 2 ? 'B_RESERVA' : 'C_NAO'); ?>">
                        <span class="fw-bold" style="font-size: 13px !important;">
                          <?php 
                          if($tecnico['domingo_status']==1) {
                            echo 'ESCALADO';
                            if(!empty($tecnico['domingo_data'])) {
                              echo '<br><small style="font-size: 11px;">' . date('d/m/Y', strtotime($tecnico['domingo_data'])) . '</small>';
                            }
                          } else if($tecnico['domingo_status']==2) {
                            echo 'RESERVA';
                            if(!empty($tecnico['domingo_data'])) {
                              echo '<br><small style="font-size: 11px;">' . date('d/m/Y', strtotime($tecnico['domingo_data'])) . '</small>';
                            }
                          } else {
                            echo 'NAO';
                          }
                          ?>
                        </span>
                      </td>
                      <td class="text-center align-middle" data-sort="<?php echo $tecnico['valor']; ?>">
                        <span class="fw-bold text-success">
                          <?php 
                          if($tecnico['valor'] > 0) {
                            // Valor já foi aplicado com mínimo no cálculo
                            echo 'R$ ' . number_format($tecnico['valor'], 2, ',', '.');
                          } else {
                            echo '<span class="text-muted">-</span>';
                          }
                          ?>
                        </span>
                        <?php if($tecnico['dias_escalados'] > 0): ?>
                        <br><small class="text-muted" style="font-size: 11px;">
                          <?php echo $tecnico['dias_escalados']; ?> dia(s)
                        </small>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                <?php endif; ?>
              </tbody>
            </table>
            
            <?php if($mostrar_dados && !empty($tecnicos_data)): ?>
            <!-- Paginação será gerenciada pelo DataTable -->
            <?php endif; ?>
          </div>
        </div>

      </div>
    </main>

    <footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
          <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
          <!--<div>
              <a href="#">Privacy Policy</a>
              &middot;
              <a href="#">Terms &amp; Conditions</a>
          </div>-->
        </div>
      </div>
    </footer>
  </div>

  </div><!--div para fechar a div que fica aberta no menu.php-->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script>
    // Atualiza supervisores quando coordenador muda
    document.addEventListener('DOMContentLoaded', function() {
      // Inicializa tooltips do Bootstrap
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      const coordSelect = document.getElementById('coordenadorSelect');
      if(coordSelect) {
        coordSelect.addEventListener('change', function() {
          const supervisorSelect = document.getElementById('supervisorSelect');
          if(supervisorSelect) {
            // Reseta supervisor para TODOS
            supervisorSelect.value = 'TODOS';
          }
        });
      }
    });

    <?php if($mostrar_dados && !empty($tecnicos_data)): ?>
    // Inicializa DataTable com sorting
    let dataTable;
    let allRows = [];
    let filteredRows = [];

    // Aguarda o DOM estar pronto
    document.addEventListener('DOMContentLoaded', function() {
      initDataTable();
    });

    function initDataTable() {
      const table = document.getElementById('tabelaEscala');
      if(!table) return;
      
      // Inicializa DataTable com sorting e paginação
      dataTable = $(table).DataTable({
        paging: true,
        pageLength: 10,
        searching: false, // Desabilita busca do DataTable, usaremos a nossa
        ordering: true, // Habilita sorting
        order: [[0, 'asc']], // Ordem padrão: Técnico (coluna 0) ascendente
        columnDefs: [
          { 
            orderable: true,
            targets: 0 // Matrícula - ordena alfabeticamente
          },
          { 
            orderable: true,
            targets: 1 // Técnico - ordena alfabeticamente
          },
          { 
            orderable: true,
            targets: 2 // Coordenador - ordena alfabeticamente
          },
          { 
            orderable: true,
            targets: 3 // Supervisor - ordena alfabeticamente
          },
          { 
            orderable: true,
            targets: 4 // Placa - ordena alfabeticamente
          },
          { 
            orderable: false,
            targets: 5 // Sábado - sem ordenação
          },
          { 
            orderable: false,
            targets: 6 // Domingo - sem ordenação
          },
          { 
            orderable: false,
            targets: 7 // Valor - sem ordenação
          }
        ],
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
        }
      });
      
      // Event listener para busca customizada
      const searchInput = document.getElementById('searchInput');
      if(searchInput) {
        searchInput.addEventListener('keyup', function() {
          dataTable.search(this.value).draw();
        });
      }
    }

    // Função para exportar dados filtrados
    function exportarFiltrados() {
      const table = document.getElementById('tabelaEscala');
      let rowsToExport = [];

      // Usa a API do DataTables para pegar todas as linhas filtradas (todas as páginas).
      if(dataTable && typeof dataTable.rows === 'function') {
        rowsToExport = dataTable
          .rows({ search: 'applied' })
          .nodes()
          .toArray()
          .filter(row => row.cells.length >= 8);
      } else {
        // Fallback sem DataTables: usa as linhas do tbody atual.
        const tbody = table.getElementsByTagName('tbody')[0];
        rowsToExport = Array.from(tbody.getElementsByTagName('tr')).filter(row => row.cells.length >= 8);
      }
      
      // Prepara dados para enviar
      const dados = [];
      const chavesExportadas = new Set();
      rowsToExport.forEach(row => {
        const cells = row.getElementsByTagName('td');

        // Exporta apenas quem tem escala no sábado ou domingo.
        const statusSabado = cells[5].getAttribute('data-sort') || '';
        const statusDomingo = cells[6].getAttribute('data-sort') || '';
        const tecnicoTemEscala = statusSabado !== 'C_NAO' || statusDomingo !== 'C_NAO';
        if(!tecnicoTemEscala) {
          return;
        }
        
        // Extrai o texto sem as tags HTML
        const getTecnicoNome = (cell) => {
          const div = cell.querySelector('div');
          return div ? div.textContent.trim() : cell.textContent.trim();
        };
        
        const getMatricula = (cell) => {
          const small = cell.querySelector('small');
          return small ? small.textContent.trim() : '';
        };
        
        const getCoordenador = (cell) => {
          const span = cell.querySelector('.supervisor-tag');
          return span ? span.textContent.trim() : cell.textContent.trim();
        };
        
        const getSupervisor = (cell) => {
          const span = cell.querySelector('.supervisor-tag');
          return span ? span.textContent.trim() : cell.textContent.trim();
        };
        
        const getPlaca = (cell) => {
          const span = cell.querySelector('span');
          return span ? span.textContent.trim() : cell.textContent.trim();
        };
        
        const getStatusComData = (cell) => {
          const spans = cell.querySelectorAll('span');
          let texto = '';
          spans.forEach(span => {
            const content = span.innerHTML;
            // Substitui <br> por quebra de linha
            texto += content.replace(/<br>/gi, '\n').replace(/<[^>]*>/g, '');
          });
          return texto.trim() || cell.textContent.trim();
        };
        
        const getValor = (cell) => {
          const spans = cell.querySelectorAll('span');
          let texto = '';
          spans.forEach(span => {
            const content = span.innerHTML;
            texto += content.replace(/<br>/gi, '\n').replace(/<[^>]*>/g, '') + ' ';
          });
          return texto.trim() || cell.textContent.trim();
        };
        
        const getValorPorDia = (sabado, domingo, valorTotal) => {
          // Extrai o texto dos status para contar dias escalados
          let diasEscalados = 0;
          
          if(sabado && sabado.toLowerCase().includes('escalado')) diasEscalados++;
          if(domingo && domingo.toLowerCase().includes('escalado')) diasEscalados++;
          
          if(diasEscalados === 0) return '-';
          
          // Extrai o valor total removendo R$, pontos e substituindo vírgula por ponto
          const valorTexto = valorTotal.replace(/R\$/g, '').replace(/\./g, '').replace(',', '.').trim();
          const valorNum = parseFloat(valorTexto.split('\n')[0]); // Pega só a primeira linha (o valor)
          
          if(isNaN(valorNum) || valorNum === 0) return '-';
          
          // Valor já foi aplicado com mínimo no cálculo, apenas divide pelos dias
          const valorPorDia = valorNum / diasEscalados;
          return 'R$ ' + valorPorDia.toFixed(2).replace('.', ',');
        };
        
        const sabadoStatus = getStatusComData(cells[5]);
        const domingoStatus = getStatusComData(cells[6]);
        const valorTotal = getValor(cells[7]);

        // Evita duplicidade de linhas no Excel quando o DataTables retornar nós repetidos.
        const chaveLinha = [
          cells[0].textContent.trim(),
          getTecnicoNome(cells[1]),
          getCoordenador(cells[2]),
          getSupervisor(cells[3]),
          getPlaca(cells[4]),
          sabadoStatus,
          domingoStatus,
          valorTotal
        ].join('||');

        if(chavesExportadas.has(chaveLinha)) {
          return;
        }
        chavesExportadas.add(chaveLinha);
        
        dados.push({
          matricula: cells[0].textContent.trim(),
          nome_tecnico: getTecnicoNome(cells[1]),
          coordenador: getCoordenador(cells[2]),
          supervisor: getSupervisor(cells[3]),
          placa: getPlaca(cells[4]),
          sabado: sabadoStatus,
          domingo: domingoStatus,
          valor_por_dia: getValorPorDia(sabadoStatus, domingoStatus, valorTotal),
          valor: valorTotal
        });
      });
      
      // Cria formulário e envia
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '../gerar_excel_escala_filtrado.php';
      form.target = '_blank';
      
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'dados';
      input.value = JSON.stringify(dados);
      
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    }
    <?php endif; ?>
  </script>

</body>
</html>
