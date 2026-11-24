<?php
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} elseif($perfil == '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} elseif($perfil == '2'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} elseif($perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

include"../conecta2.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Listagem de Condutores PJ </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  
  <!-- jQuery -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>
  
  <!-- DataTables JS -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <style>
    .badge-ativo {
      background-color: #28a745;
      color: white;
    }
    .badge-inativo {
      background-color: #dc3545;
      color: white;
    }
    .badge-afastado {
      background-color: #ffc107;
      color: black;
    }
    .badge-ferias {
      background-color: #17a2b8;
      color: white;
    }
    
    .btn-action {
      margin: 0 2px;
    }
    
    table.dataTable thead th {
      background-color: #2E2851;
      color: white;
    }
    
    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid #ced4da;
      border-radius: 4px;
      padding: 5px 10px;
    }
    
    .table-responsive {
      overflow-x: auto;
    }
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-5">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <div class="d-flex justify-content-between align-items-center pt-2 pb-2">
          <h1 class="h1"> Condutores PJ </h1>
          <a href="./cadastrar_condutores.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Novo Condutor
          </a>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label for="filtroStatus" class="form-label">Status:</label>
                <select class="form-select" id="filtroStatus">
                  <option value="">Todos</option>
                  <option value="ATIVO" selected>Ativo</option>
                  <option value="INATIVO">Inativo</option>
                  <option value="AFASTADO">Afastado</option>
                  <option value="FERIAS">Férias</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="filtroCcusto" class="form-label">Centro de Custo:</label>
                <select class="form-select" id="filtroCcusto">
                  <option value="">Todos</option>
                  <?php
                    $sql_ccusto = "SELECT DISTINCT UPPER(ccusto) as ccusto 
                                   FROM bdaniel.tbfuncionario 
                                   WHERE matricula LIKE '62%' 
                                   AND ccusto IS NOT NULL 
                                   AND ccusto != '' 
                                   ORDER BY ccusto";
                    $resultado_ccusto = mysqli_query($conexao, $sql_ccusto);
                    
                    if ($resultado_ccusto) {
                      while($row_ccusto = mysqli_fetch_array($resultado_ccusto, MYSQLI_ASSOC)) {
                        $ccusto_item = $row_ccusto['ccusto'];
                        if (!is_numeric($ccusto_item) && !preg_match('/^\d+$/', $ccusto_item)) {
                          echo "<option value='$ccusto_item'>$ccusto_item</option>";
                        }
                      }
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-3">
                <label for="filtroCargo" class="form-label">Cargo:</label>
                <select class="form-select" id="filtroCargo">
                  <option value="">Todos</option>
                  <?php
                    $sql_cargo = "SELECT DISTINCT UPPER(cargo) as cargo 
                                  FROM bdaniel.tbfuncionario 
                                  WHERE matricula LIKE '62%' 
                                  AND cargo IS NOT NULL 
                                  AND cargo != '' 
                                  ORDER BY cargo";
                    $resultado_cargo = mysqli_query($conexao, $sql_cargo);
                    
                    if ($resultado_cargo) {
                      while($row_cargo = mysqli_fetch_array($resultado_cargo, MYSQLI_ASSOC)) {
                        $cargo_item = $row_cargo['cargo'];
                        if (!is_numeric($cargo_item) && !preg_match('/^\d+$/', $cargo_item)) {
                          echo "<option value='$cargo_item'>$cargo_item</option>";
                        }
                      }
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="filtrarTabela()">
                  <i class="fas fa-filter me-2"></i>Filtrar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabela de condutores -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover" id="tabelaCondutores">
                <thead>
                  <tr>
                    <th>Matrícula</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Centro de Custo</th>
                    <th>Cargo</th>
                    <th>Projeto</th>
                    <th>UF</th>
                    <th>Data Admissão</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Busca condutores PJ (matrículas que começam com 62)
                    $sql = "SELECT f.matricula, f.nome, f.cpf, f.ccusto, f.cargo, f.projeto, 
                                   f.uf_trabalho, f.dtadmissao, f.status
                            FROM bdaniel.tbfuncionario f
                            WHERE f.matricula LIKE '62%'
                            ORDER BY f.nome";
                    
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $matricula = $row['matricula'];
                      $nome = utf8_encode($row['nome']);
                      $cpf = $row['cpf'];
                      $ccusto = utf8_encode($row['ccusto']);
                      $cargo = utf8_encode($row['cargo']);
                      $projeto = utf8_encode($row['projeto']);
                      $uf = $row['uf_trabalho'];
                      $dtadmissao = $row['dtadmissao'];
                      $status = $row['status'];
                      
                      // Formatar CPF
                      if ($cpf && strlen($cpf) == 11) {
                        $cpf_formatado = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
                      } else {
                        $cpf_formatado = $cpf;
                      }
                      
                      // Formatar data
                      $data_formatada = '';
                      if ($dtadmissao && $dtadmissao != '0000-00-00') {
                        $data_formatada = date('d/m/Y', strtotime($dtadmissao));
                      }
                      
                      // Badge do status
                      $badge_class = 'badge-ativo';
                      switch(strtoupper($status)) {
                        case 'INATIVO':
                          $badge_class = 'badge-inativo';
                          break;
                        case 'AFASTADO':
                          $badge_class = 'badge-afastado';
                          break;
                        case 'FERIAS':
                          $badge_class = 'badge-ferias';
                          break;
                      }
                      
                      echo "<tr data-status='$status' data-ccusto='$ccusto' data-cargo='$cargo'>
                        <td>$matricula</td>
                        <td>$nome</td>
                        <td>$cpf_formatado</td>
                        <td>$ccusto</td>
                        <td>$cargo</td>
                        <td>$projeto</td>
                        <td>$uf</td>
                        <td>$data_formatada</td>
                        <td><span class='badge $badge_class'>$status</span></td>
                        <td class='text-center text-nowrap'>
                          <a href='editar_condutor.php?matricula=$matricula' class='btn btn-sm btn-warning btn-action' title='Editar'>
                            <i class='fas fa-edit'></i>
                          </a>
                          <button class='btn btn-sm btn-danger btn-action' onclick='confirmarExclusao(\"$matricula\", \"$nome\")' title='Excluir'>
                            <i class='fas fa-trash'></i>
                          </button>
                        </td>
                      </tr>";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </main>

    <footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
          <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
        </div>
      </div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>

  <script>
    $(document).ready(function() {
      // Inicializa DataTable
      var table = $('#tabelaCondutores').DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json"
        },
        "pageLength": 25,
        "order": [[1, 'asc']], // Ordena por nome
        "columnDefs": [
          { "orderable": false, "targets": 9 } // Desabilita ordenação na coluna de ações
        ]
      });
      
      // Filtrar apenas ativos ao carregar
      filtrarTabela();
    });
    
    function filtrarTabela() {
      var status = $('#filtroStatus').val();
      var ccusto = $('#filtroCcusto').val();
      var cargo = $('#filtroCargo').val();
      
      var table = $('#tabelaCondutores').DataTable();
      
      // Limpa filtros anteriores
      table.columns().search('');
      
      // Aplica filtros
      if (status) {
        table.column(8).search(status);
      }
      if (ccusto) {
        table.column(3).search('^' + ccusto + '$', true, false);
      }
      if (cargo) {
        table.column(4).search('^' + cargo + '$', true, false);
      }
      
      table.draw();
    }
    
    function confirmarExclusao(matricula, nome) {
      if (confirm('Tem certeza que deseja excluir o condutor:\n\n' + nome + ' (Matrícula: ' + matricula + ')?\n\nEsta ação não poderá ser desfeita!')) {
        window.location.href = './control/excluir_condutor.php?matricula=' + matricula;
      }
    }
  </script>

</body>
</html>
