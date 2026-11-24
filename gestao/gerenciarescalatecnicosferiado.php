<?php
date_default_timezone_set('America/Sao_Paulo');

$lifetime = 3600;
session_set_cookie_params($lifetime);
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

if($perfil == 0 || $perfil == ''){
  echo "<script>alert('Você não tem permissão para acessar esta página'); window.location='../index.php';</script>";
  exit;
}

include"../conecta.php";
header("Content-type: text/html; charset=utf-8");

$diaEncerramento = 1; // padrão: segunda-feira
$horarioEncerramento = 15; // padrão: 15h
$sqlHorario = "SELECT dia_encerramento, hora_encerramento FROM tbconfiguracao_escala_tecnicos_feriado ORDER BY id DESC LIMIT 1";
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
$tercaFeriado = date('d/m/Y', strtotime('tuesday next week'));
$quintaFeriado = date('d/m/Y', strtotime('thursday next week'));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Gerenciar Escala de Feriado </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <?php include "../menu.php"; ?>

  <div id="layoutSidenav_content">
    <main style="width: 100%;" class="mb-2">
      <div class="container-fluid" style="width: 100%;">
        <h1 class="h1 pt-2 mb-1 text-center">Gerenciar Escala de Feriado</h1>
        <p class="text-center text-muted pb-2 mb-0">Utilizar somente quando aplicável</p>

        <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
          <div class="card mb-4 shadow" style="width: 600px;">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Escala de Técnicos - Feriado</h5>
            </div>
            <div class="card-body p-4 text-center">
              <div class="mb-4">
                <label class="form-label fw-bold">
                  <i class="fas fa-user-tie me-2"></i>Coordenador
                </label>
                <input type="text" class="form-control form-control-lg text-center"
                       value="<?php echo htmlspecialchars($nome1); ?>"
                       readonly style="background-color: #e9ecef;">
                <small class="text-muted">Matrícula: <?php echo $matricula1; ?></small>
              </div>

              <!-- <div class="alert alert-info mb-4">
                <i class="fas fa-calendar-week me-2"></i>
                <strong>Feriado da próxima semana:</strong> terça (<?php echo $tercaFeriado; ?>) e quinta (<?php echo $quintaFeriado; ?>)
              </div> -->

              <!-- <div class="alert alert-warning mb-4" id="avisoHorario" style="display: none;">
                <h6 class="mb-1"><i class="fas fa-clock me-2"></i><strong>Atenção!</strong></h6>
                <p class="mb-0 small">As edições da escala de feriado são encerradas na <strong><?php echo $diaEncerramentoTexto; ?></strong>, às <strong><?php echo $horarioEncerramento; ?>h</strong>.</p>
              </div> -->

              <div class="d-grid">
                <button type="button" class="btn btn-primary btn-lg" onclick="abrirEscala()" id="btnAbrir">
                  <i class="fas fa-calendar-check me-2"></i> Abrir Gerenciamento de Escala de Feriado
                </button>
              </div>
              <div class="d-grid mt-3">
                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="abrirCadastroFeriados()">
                  <i class="fas fa-calendar-plus me-2"></i> Cadastrar/Visualizar Feriados (UF / BR)
                </button>
              </div>

              <div class="alert alert-danger mt-4" id="avisoEncerrado" style="display: none;">
                <h5 class="mb-2"><i class="fas fa-lock me-2"></i>Edições Encerradas</h5>
                <p class="mb-0">⏰ Agendamentos de feriado encerrados. Aguarde a próxima janela para novos agendamentos.</p>
              </div>

            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Gerenciar Escala de Feriado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <iframe id="iframeEscala" src="" title="Escala Feriado" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    const DIA_ENCERRAMENTO = <?php echo (int)$diaEncerramento; ?>;
    const HORA_ENCERRAMENTO = <?php echo (int)$horarioEncerramento; ?>;

    function passouDoDiaEncerramento(diaAtual, diaLimite) {
      if (diaLimite === 0) {
        return diaAtual >= 1 && diaAtual <= 6;
      }
      return diaAtual > diaLimite || diaAtual === 0;
    }

    function verificarHorario() {
      const agora = new Date();
      const diaSemana = agora.getDay();
      const horas = agora.getHours();

      const btnAbrir = document.getElementById('btnAbrir');
      const avisoEncerrado = document.getElementById('avisoEncerrado');
      const avisoHorario = document.getElementById('avisoHorario');

      const bloqueado = (diaSemana === DIA_ENCERRAMENTO && horas >= HORA_ENCERRAMENTO) ||
                        passouDoDiaEncerramento(diaSemana, DIA_ENCERRAMENTO);

      if (bloqueado) {
        btnAbrir.style.display = 'none';
        avisoEncerrado.style.display = 'block';
        avisoHorario.style.display = 'none';
      } else {
        btnAbrir.style.display = 'block';
        avisoEncerrado.style.display = 'none';
        avisoHorario.style.display = 'block';
      }
    }

    function abrirEscala() {
      document.getElementById('iframeEscala').src = '../escala_feriado_tecnico.php';
      const modal = new bootstrap.Modal(document.getElementById('importModal'));
      modal.show();
    }

    function abrirCadastroFeriados() {
      document.getElementById('iframeEscala').src = 'cadastroferiadostecnicos.php';
      const modal = new bootstrap.Modal(document.getElementById('importModal'));
      modal.show();
    }

    document.getElementById('importModal').addEventListener('hidden.bs.modal', function () {
      document.getElementById('iframeEscala').src = '';
    });

    document.addEventListener('DOMContentLoaded', function() {
      verificarHorario();
      setInterval(verificarHorario, 60000);
    });
    // https://ffa.painel-telecom.com:4242/frotas/gestao/cadastroferiadostecnicos.php
    // bdfrota.tbferiados_escala_tecnico 
  </script>
</body>
</html>