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
if($perfil == 0 || $perfil == ''){
  echo "<script>alert('Você não tem permissão para acessar esta página'); window.location='../index.php';</script>";
  exit;
}

include"../conecta.php";

include"../conecta.php";
header("Content-type: text/html; charset=utf-8");

$diaEncerramento = 5; // padrão de segurança: sexta-feira
$horarioEncerramento = 15; // padrão de segurança: 15h
$sqlHorario = "SELECT dia_encerramento, hora_encerramento FROM tbconfiguracao_escala_tecnicos ORDER BY id DESC LIMIT 1";
$resultadoHorario = mysqli_query($conn, $sqlHorario);

if ($resultadoHorario && mysqli_num_rows($resultadoHorario) > 0) {
  $linhaHorario = mysqli_fetch_assoc($resultadoHorario);
  $diaTabela = (int)$linhaHorario['dia_encerramento'];
  $horaTabela = (int)$linhaHorario['hora_encerramento'];

  if ($diaTabela >= 0 && $diaTabela <= 6) {
    $diaEncerramento = $diaTabela;
  }

  if ($horaTabela >= 0 && $horaTabela <= 23) {
    $horarioEncerramento = $horaTabela;
  }
}

$diasSemana = array('domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado');
$diaEncerramentoTexto = $diasSemana[$diaEncerramento];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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
              <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Escala de Técnicos</h5>
            </div>
            <div class="card-body p-4 text-center">
              
              <!-- Coordenador (readonly) -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-user-tie me-2"></i>Coordenador
                </label>
                <input type="text" class="form-control form-control-lg text-center" 
                       value="<?php echo htmlspecialchars($nome1); ?>" 
                       readonly style="background-color: #e9ecef;">
                <small class="text-muted">Matrícula: <?php echo $matricula1; ?></small>
              </div>

              <!-- Informação adicional -->
              <div class="alert alert-info mb-4">
                <i class="fas fa-calendar-week me-2"></i>
                <strong>Semana:</strong> A escala sempre exibe o próximo fim de semana (sábado e domingo)
              </div>

              <!-- Aviso de Horário de Encerramento -->
              <div class="alert alert-warning mb-4" id="avisoHorario" style="display: none;">
                <h6 class="mb-1"><i class="fas fa-clock me-2"></i><strong>Atenção!</strong></h6>
                <p class="mb-0 small">As edições da semana são encerradas na <strong><?php echo $diaEncerramentoTexto; ?></strong>, às <strong><?php echo $horarioEncerramento; ?>h</strong>.
              </div>

              <!-- Botão -->
              <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg" onclick="abrirEscala()" id="btnAbrir">
                  <i class="fas fa-calendar-check me-2"></i> Abrir Gerenciamento de Escala
                </button>
              </div>

              <!-- Aviso de Encerramento -->
              <div class="alert alert-danger mt-4" id="avisoEncerrado" style="display: none;">
                <h5 class="mb-2"><i class="fas fa-lock me-2"></i>Edições Encerradas</h5>
                <p class="mb-0">⏰ Agendamentos de escala encerrados. Aguarde até segunda-feira para realizar novos agendamentos</p>
              </div>

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
    const DIA_ENCERRAMENTO = <?php echo (int)$diaEncerramento; ?>;
    const HORA_ENCERRAMENTO = <?php echo (int)$horarioEncerramento; ?>;
    const DIAS_SEMANA = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];

    function passouDoDiaEncerramento(diaAtual, diaLimite) {
      if (diaLimite === 0) {
        return diaAtual >= 1 && diaAtual <= 6;
      }
      return diaAtual > diaLimite || diaAtual === 0;
    }

    // Função para verificar horário e controlar visibilidade do botão
    function verificarHorario() {
      const agora = new Date();
      const diaSemana = agora.getDay(); // 0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado
      const horas = agora.getHours();
      
      const btnAbrir = document.getElementById('btnAbrir');
      const avisoEncerrado = document.getElementById('avisoEncerrado');
      const avisoHorario = document.getElementById('avisoHorario');
      
      // Período de bloqueio: dia/hora configurados até a virada para segunda-feira
      const bloqueado = (diaSemana === DIA_ENCERRAMENTO && horas >= HORA_ENCERRAMENTO) ||
                        passouDoDiaEncerramento(diaSemana, DIA_ENCERRAMENTO);
      
      if (bloqueado) {
        // Oculta botão e mostra aviso de encerrado
        btnAbrir.style.display = 'none';
        avisoEncerrado.style.display = 'block';
        avisoHorario.style.display = 'none';
        console.log(`Período bloqueado - ${DIAS_SEMANA[DIA_ENCERRAMENTO]} após ${HORA_ENCERRAMENTO}h até segunda-feira 00:00`);
      } else {
        // Exibe botão e oculta aviso de encerrado
        btnAbrir.style.display = 'block';
        avisoEncerrado.style.display = 'none';
        avisoHorario.style.display = 'block';
        
        // Calcula tempo restante até o próximo dia/hora de encerramento configurados
        if (diaSemana >= 1 && diaSemana <= 5) {
          const diasAteVencimento = diaSemana === DIA_ENCERRAMENTO ? 0 : (DIA_ENCERRAMENTO - diaSemana);
          const horasRestantes = diaSemana === DIA_ENCERRAMENTO ? (HORA_ENCERRAMENTO - horas) : ((diasAteVencimento * 24) + (HORA_ENCERRAMENTO - horas));
          console.log(`Botão visível - ${horasRestantes}h até ${DIAS_SEMANA[DIA_ENCERRAMENTO]} ${HORA_ENCERRAMENTO}:00`);
        }
      }
    }

    // Função para abrir a escala sem filtro de supervisor
    function abrirEscala() {
      // Monta URL sem parâmetro de supervisor
      const url = '../escala_fim_semana_tecnico.php';
      
      // Define URL do iframe
      document.getElementById('iframeEscala').src = url;
      
      // Atualiza título do modal
      document.getElementById('importModalLabel').textContent = 'Escala Fim de Semana - Técnicos';
      
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

    // Executa verificação ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
      verificarHorario();
      // Verifica a cada minuto
      setInterval(verificarHorario, 60000);
    });
  </script>

</body>
</html>