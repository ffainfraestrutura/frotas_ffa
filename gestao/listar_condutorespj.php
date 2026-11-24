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
}elseif ($matricula1 == '500422'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}
// matrícula que estavam como restritas $matricula1 == '160030'||$matricula1 == '410109'||||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

include"../conecta2.php";
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Lista de Funcionários PJ </title>

  <!-- CSS -->
  <link href="../src/css/styles.css" rel="stylesheet" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" />

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
  <?php include "../menu.php";; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-5">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <div class="d-flex justify-content-between align-items-center pt-2 pb-2">
          <h1 class="h1"> Funcionários e condutores PJ </h1>
          <div class="d-flex gap-2">
            <a href="https://ffa.painel-telecom.com:4242/frotas/gestao/listagemgeralcondutores.php" class="btn btn-secondary" target="_blank">
              <i class="fas fa-users me-2"></i>Listagem Geral
            </a>
            <a href="https://ffa.painel-telecom.com:4545/frotas/gestao/listagemcnh.php" class="btn btn-info" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Aqui você pode consultar os dados da CNH dos colaboradores e a operadora do cartão de combustível.">
              <i class="fas fa-list me-2"></i>Listagem CNH
            </a>
            <a href="https://ffa.painel-telecom.com:4545/frotas/gestao/listagemfuncionarios2.php" class="btn btn-primary" target="_blank">
              <i class="fas fa-id-card me-2"></i>Cadastrar CNH
            </a>
            <a href="./control/gerarexcelcondutorespj.php" class="btn btn-outline-success" target="_blank">
              <i class="fas fa-file-excel me-2"></i>Exportar Excel
            </a>
            <a href="./cadastrar_condutorespj.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Novo Condutor
            </a>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-2">
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
              <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-fill" onclick="filtrarTabela()">
                  <i class="fas fa-filter me-2"></i>Filtrar
                </button>
                <button class="btn btn-secondary flex-fill" onclick="limparFiltros()">
                  <i class="fas fa-eraser me-2"></i>Limpar
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
                    <th>Centro de Custo</th>
                    <th>Cargo</th>
                    <th>UF</th>
                    <th>Data Admissão</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Busca condutores PJ (matrículas que começam com 62)
                    $sql = "SELECT f.matricula, f.nome, f.ccusto, f.cargo, 
                                   f.uf_trabalho, f.dtadmissao, f.status
                            FROM bdaniel.tbfuncionario f
                            WHERE f.matricula LIKE '62%'
                            ORDER BY f.nome";
                    
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $matricula = htmlspecialchars((string)$row['matricula'], ENT_QUOTES, 'UTF-8');
                      $nome = htmlspecialchars(normalizarTexto($row['nome']), ENT_QUOTES, 'UTF-8');
                      $ccusto = htmlspecialchars(normalizarTexto($row['ccusto']), ENT_QUOTES, 'UTF-8');
                      $cargo = htmlspecialchars(normalizarTexto($row['cargo']), ENT_QUOTES, 'UTF-8');
                      $uf = htmlspecialchars((string)$row['uf_trabalho'], ENT_QUOTES, 'UTF-8');
                      $dtadmissao = $row['dtadmissao'];
                      $status = htmlspecialchars(normalizarTexto($row['status']), ENT_QUOTES, 'UTF-8');
                      
                      // Formatar data
                      $data_formatada = '';
                      if ($dtadmissao && $dtadmissao != '0000-00-00') {
                        $data_formatada = date('d/m/Y', strtotime($dtadmissao));
                      }

                      $status_raw = strtoupper(trim((string)$row['status']));
                      
                      // Badge do status
                      $badge_class = 'badge-ativo';
                      switch($status_raw) {
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
                      
                      echo "<tr data-status='" . htmlspecialchars($status_raw, ENT_QUOTES, 'UTF-8') . "' data-ccusto='$ccusto' data-cargo='$cargo'>
                        <td>$matricula</td>
                        <td>$nome</td>
                        <td>$ccusto</td>
                        <td>$cargo</td>
                        <td>$uf</td>
                        <td>$data_formatada</td>
                        <td><span class='badge $badge_class'>$status</span></td>
                        <td class='text-center text-nowrap'>
                          <a href='editar_condutorespj.php?matricula=$matricula' class='btn btn-sm btn-warning btn-action' title='Editar'>
                            <i class='fas fa-edit'></i>
                          </a>
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

  <!-- Scripts devem ser carregados na ordem correta -->
  
  <!-- 1. jQuery (PRIMEIRO) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  
  <!-- 2. Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  
  <!-- 3. Scripts do projeto -->
  <script src="../src/js/scripts.js"></script>
  
  <!-- 4. DataTables (depende do jQuery) -->
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  
  <!-- Fallback: Se o DataTables não carregar, tenta outra CDN -->
  <script>
    if (typeof $.fn.DataTable === 'undefined') {
      console.warn('DataTables não carregou da CDN principal. Tentando CDN alternativa...');
      document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"><\/script>');
    }
  </script>

  <script>
    var table; // Variável global para a tabela
    var tabelaInicializada = false; // Flag para controlar inicialização
    var tentativasInicializacao = 0; // Contador de tentativas
    
    // Aguarda o carregamento completo da página E dos scripts
    $(document).ready(function() {
      // Inicializa tooltips do Bootstrap
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });

      // Verifica se o elemento da tabela existe
      if ($('#tabelaCondutores').length === 0) {
        alert('ERRO: Tabela não encontrada! Verifique o código HTML.');
        return;
      }
      
      // Verifica se DataTables está carregado
      if (typeof $.fn.DataTable === 'undefined' && typeof $.fn.dataTable === 'undefined') {
        // Tenta recarregar DataTables
        var script = document.createElement('script');
        script.src = 'https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js';
        script.onload = function() {
          setTimeout(function() {
            inicializarTabela();
          }, 500);
        };
        script.onerror = function() {
          alert('ERRO: Não foi possível carregar a biblioteca DataTables.\n\nVerifique sua conexão com a internet.');
        };
        document.head.appendChild(script);
        return;
      }
      
      // Aguarda um pouco para garantir que tudo foi carregado
      setTimeout(function() {
        inicializarTabela();
      }, 200);
    });
    
    function inicializarTabela() {
      tentativasInicializacao++;
      
      if (tentativasInicializacao > 5) {
        alert('Erro ao inicializar a tabela. Por favor, recarregue a página.');
        return;
      }
      
      try {
        // Destroi a tabela se já existir
        if ($.fn.DataTable.isDataTable('#tabelaCondutores')) {
          $('#tabelaCondutores').DataTable().destroy();
        }
        
        // Inicializa DataTable
        table = $('#tabelaCondutores').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json"
          },
          "pageLength": 25,
          "order": [[1, 'asc']], // Ordena por nome
          "columnDefs": [
            { "orderable": false, "targets": 7 } // Desabilita ordenação na coluna de ações
          ],
          "initComplete": function(settings, json) {
            tabelaInicializada = true;
            
            // Adicionar evento de mudança nos filtros APÓS inicialização
            $('#filtroStatus, #filtroCcusto, #filtroCargo').off('change').on('change', function() {
              filtrarTabela();
            });
            
            // Aplicar filtro inicial após a inicialização completa
            setTimeout(function() {
              filtrarTabela();
            }, 300);
          }
        });
        
      } catch(error) {
        // Tenta novamente após um delay
        if (tentativasInicializacao < 5) {
          setTimeout(inicializarTabela, 1000);
        } else {
          alert('Erro ao inicializar a tabela: ' + error.message);
        }
      }
    }
    
    var tentativasFiltro = 0; // Contador para tentativas de filtro
    
    function filtrarTabela() {
      // Aguarda a inicialização se ainda não ocorreu
      if (!tabelaInicializada || typeof table === 'undefined' || !table) {
        tentativasFiltro++;
        
        if (tentativasFiltro > 10) {
          alert('A tabela não foi inicializada corretamente. Por favor, recarregue a página.');
          return;
        }
        
        setTimeout(function() {
          filtrarTabela();
        }, 500);
        return;
      }
      
      // Reset contador quando conseguir filtrar
      tentativasFiltro = 0;
      
      try {
        var statusFiltro = $('#filtroStatus').val().toUpperCase().trim();
        var ccustoFiltro = $('#filtroCcusto').val().trim();
        var cargoFiltro = $('#filtroCargo').val().trim();
        
        // Limpa todos os filtros anteriores
        $.fn.dataTable.ext.search = [];
        
        // Adiciona função de filtro customizada
        $.fn.dataTable.ext.search.push(
          function(settings, data, dataIndex) {
            // Índices das colunas: 0: Matrícula, 1: Nome, 2: Centro de Custo, 3: Cargo, 4: UF, 5: Data Admissão, 6: Status, 7: Ações
            var statusTabela = data[6].replace(/<[^>]*>/g, '').trim().toUpperCase(); // Remove HTML tags
            var ccustoTabela = data[2].trim();
            var cargoTabela = data[3].trim();
            
            // Verifica cada filtro
            var statusMatch = !statusFiltro || statusTabela === statusFiltro;
            var ccustoMatch = !ccustoFiltro || ccustoTabela === ccustoFiltro;
            var cargoMatch = !cargoFiltro || cargoTabela === cargoFiltro;
            
            return statusMatch && ccustoMatch && cargoMatch;
          }
        );
        
        // Redesenha a tabela com os filtros aplicados
        table.draw();
        
      } catch(error) {
        alert('Erro ao aplicar filtro: ' + error.message);
      }
    }
    
    function limparFiltros() {
      if (!tabelaInicializada) {
        alert('Aguarde a tabela ser carregada antes de limpar os filtros.');
        return;
      }
      
      $('#filtroStatus').val('');
      $('#filtroCcusto').val('');
      $('#filtroCargo').val('');
      $.fn.dataTable.ext.search = [];
      
      if (table) {
        table.draw();
      } else {
        alert('Erro ao limpar filtros. Por favor, recarregue a página.');
      }
    }
  </script>

</body>
</html>
<!--  -->