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

// Validação de acesso - apenas perfil 2 (coordenador)
if($perfil != 2){
  echo "<script>alert('Apenas coordenadores têm acesso a esta página'); window.location='../index.php';</script>";
  exit;
}

include"../conecta.php";
header("Content-type: text/html; charset=utf-8");

// Inicializa variáveis padrão
$coordenadores = [];
$erro_conexao = false;

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

  <title> Gerenciar Escala Fim de Semana </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>

  <style type="text/css">
    body{
      font-size: 12px;
    }

    .btn{
      font-size: 12px;
    }

    /* Estilo para o modal ocupar quase toda a tela */
    .modal-fullscreen-custom {
      max-width: 95%;
      max-height: 95%;
      margin: 2.5%;
    }

    .modal-fullscreen-custom .modal-content {
      height: 90vh;
    }

    .modal-fullscreen-custom .modal-body {
      overflow-y: auto;
      max-height: calc(90vh - 120px);
    }

    .modal-fullscreen-custom iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Botão X do modal grande e em evidência */
    .modal-header .btn-close {
      font-size: 0 !important;
      width: 50px !important;
      height: 50px !important;
      padding: 0 !important;
      opacity: 1 !important;
      background: transparent !important;
      position: relative;
      margin: 0;
    }

    .modal-header .btn-close::before {
      content: "×";
      font-size: 60px !important;
      font-weight: bold;
      color: #000;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      line-height: 1;
    }

    .modal-header .btn-close:hover {
      opacity: 0.7 !important;
      transform: scale(1.1);
      background: transparent !important;
    }

    .modal-header .btn-close:hover::before {
      color: #dc3545;
    }
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <!--nav-->
  <?php include "../menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid" style="width: 100%;">
        <h1 class="h1 pt-2 pb-2"> Gerenciar Escala Fim de Semana </h1>
        
        <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
          <div class="card mb-4 shadow" style="width: 600px;">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Escala</h5>
            </div>
            <div class="card-body p-4">
              
              <?php if($erro_conexao): ?>
              <!-- Mensagem de erro de conexão -->
              <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erro de Conexão</h5>
                <p>Não foi possível conectar ao banco de dados. Verifique:</p>
                <ul>
                  <li>Se o arquivo <code>conecta.php</code> existe e está configurado corretamente</li>
                  <li>Se as credenciais do banco estão corretas</li>
                  <li>Se o servidor MySQL está rodando e acessível</li>
                  <li>Se a rede permite conexão ao servidor <strong>10.17.0.5</strong></li>
                </ul>
                <hr>
                <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Entre em contato com o suporte técnico se o problema persistir.</p>
              </div>
              <?php else: ?>
              
              <!-- Coordenador (select) -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-user-tie me-2"></i>Coordenador <span class="text-danger">*</span>
                </label>
                <select id="coordenadorSelect" class="form-select form-select-lg" required>
                  <option value="">-- Selecione um Coordenador --</option>
                  <?php foreach($coordenadores as $coord): ?>
                    <option value="<?php echo $coord['id']; ?>" data-nome="<?php echo htmlspecialchars($coord['nome']); ?>">
                      <?php echo htmlspecialchars($coord['nome']); ?> (<?php echo $coord['matricula']; ?>)
                    </option>
                  <?php endforeach; ?>
                  
                  <?php if(empty($coordenadores)): ?>
                    <option value="" disabled>Nenhum coordenador cadastrado</option>
                  <?php endif; ?>
                </select>
                <small class="text-muted">
                  <i class="fas fa-info-circle me-1"></i>
                  Selecione o coordenador para carregar seus supervisores
                </small>
              </div>

              <!-- Supervisor (select) -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-user-cog me-2"></i>Supervisor <span class="text-danger">*</span>
                </label>
                <select id="supervisorSelect" class="form-select form-select-lg" required disabled>
                  <option value="">-- Selecione um supervisor --</option>
                </select>
                <small class="text-muted">
                  <i class="fas fa-info-circle me-1"></i>
                  Selecione o supervisor para visualizar a escala de seus técnicos
                </small>
              </div>

              <!-- Informação adicional -->
              <div class="alert alert-info mb-4">
                <i class="fas fa-calendar-week me-2"></i>
                <strong>Semana:</strong> A escala sempre exibe o próximo fim de semana (sábado e domingo)
              </div>

              <!-- Botão -->
              <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg" onclick="abrirEscala()" id="btnAbrir" disabled>
                  <i class="fas fa-calendar-check me-2"></i> Abrir Gerenciamento de Escala
                </button>
              </div>

              <?php endif; ?>

            </div>
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

  <!-- Modal -->
  <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-custom modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Gerenciar Escala Fim de Semana</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <iframe id="iframeEscala" src="" title="Escala Fim de Semana" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script>
    // Carrega supervisores quando coordenador é selecionado
    document.getElementById('coordenadorSelect').addEventListener('change', function() {
      const idCoordenador = this.value;
      const supervisorSelect = document.getElementById('supervisorSelect');
      const btnAbrir = document.getElementById('btnAbrir');
      
      // Limpa e desabilita supervisor
      supervisorSelect.innerHTML = '<option value="">-- Carregando... --</option>';
      supervisorSelect.disabled = true;
      btnAbrir.disabled = true;
      
      if(!idCoordenador) {
        supervisorSelect.innerHTML = '<option value="">-- Selecione um supervisor --</option>';
        return;
      }
      
      // Busca supervisores via AJAX
      $.ajax({
        url: '../buscar_supervisores.php',
        method: 'GET',
        data: { coordenador: idCoordenador },
        dataType: 'json',
        success: function(supervisores) {
          supervisorSelect.innerHTML = '<option value="">-- Selecione um Supervisor --</option>';
          
          if(supervisores.length > 0) {
            supervisores.forEach(function(sup) {
              const option = document.createElement('option');
              option.value = sup.id;
              option.setAttribute('data-nome', sup.nome);
              option.textContent = sup.nome + ' (' + sup.matricula + ')';
              supervisorSelect.appendChild(option);
            });
            supervisorSelect.disabled = false;
          } else {
            supervisorSelect.innerHTML = '<option value="" disabled>Nenhum supervisor vinculado</option>';
          }
        },
        error: function() {
          supervisorSelect.innerHTML = '<option value="" disabled>Erro ao carregar supervisores</option>';
        }
      });
    });

    // Habilita/desabilita botão quando supervisor é selecionado
    document.getElementById('supervisorSelect').addEventListener('change', function() {
      const btnAbrir = document.getElementById('btnAbrir');
      if(this.value) {
        btnAbrir.disabled = false;
        btnAbrir.classList.add('pulse');
      } else {
        btnAbrir.disabled = true;
        btnAbrir.classList.remove('pulse');
      }
    });

    // Função para abrir a escala com filtro de supervisor e coordenador
    function abrirEscala() {
      const coordenadorSelect = document.getElementById('coordenadorSelect');
      const supervisorSelect = document.getElementById('supervisorSelect');
      const idCoordenador = coordenadorSelect.value;
      const idSupervisor = supervisorSelect.value;
      const nomeSupervisor = supervisorSelect.options[supervisorSelect.selectedIndex].dataset.nome;

      if(!idCoordenador) {
        alert('Por favor, selecione um coordenador');
        return;
      }

      if(!idSupervisor) {
        alert('Por favor, selecione um supervisor');
        return;
      }

      // Monta URL com parâmetros do coordenador e supervisor
      const url = '../escala_fim_semana.php?coordenador=' + idCoordenador + '&supervisor=' + idSupervisor;
      
      // Define URL do iframe
      document.getElementById('iframeEscala').src = url;
      
      // Atualiza título do modal com nome do supervisor
      document.getElementById('importModalLabel').textContent = 'Escala - ' + nomeSupervisor;
      
      // Abre o modal
      const modal = new bootstrap.Modal(document.getElementById('importModal'));
      modal.show();
    }

    // Limpa iframe ao fechar modal
    document.getElementById('importModal').addEventListener('hidden.bs.modal', function () {
      document.getElementById('iframeEscala').src = '';
    });

    // Listener para fechar modal quando iframe enviar mensagem
    window.addEventListener('message', function(event) {
      if (event.data === 'fecharModal') {
        const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
        if (modal) {
          modal.hide();
        }
      }
    });
  </script>
<!-- telas e consultas relacionadas 
 SELECT * FROM bdfrota.tbescala;
para liberar a tela SELECT * FROM bdfrota.tbconfiguracao_escala_tecnicos;-->
 <!-- SELECT *  FROM bdfrota.tbconfiguracao_escala_tecnicos; -->
</body>
</html>
