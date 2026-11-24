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

include"../conecta03.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Importar Multas </title>

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
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <!--nav-->
  <?php include "../menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid" style="width: 100%;">
        <h1 class="h1 pt-2 pb-2"> Importar Novas Multas </h1>
        
        <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
          <div class="card mb-4 shadow" style="width: 500px;">
            <div class="card-body text-center p-5">
              <i class="fas fa-file-upload fa-3x mb-3 text-primary"></i>
              <h5 class="card-title mb-3">Importação de Multas</h5>
              <p class="card-text text-muted mb-4">Clique no botão abaixo para abrir o formulário de importação.</p>
              <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-upload"></i> Abrir Formulário de Importação
              </button>
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
          <!-- <h5 class="modal-title" id="importModalLabel">Importar Novas Multas</h5> -->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <iframe id="iframeImport" title="Importar Novas Multas" style="width: 100%; height: 75vh; border: none;"></iframe>
          <script>
            // Construir a URL do iframe com parâmetros de sessão
            const iframeUrl = "https://ffa.painel-telecom.com:4545/frotas/gestao/importarmultasnovas.php?" +
              "nome=" + encodeURIComponent("<?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : ''; ?>") +
              "&usuario=" + encodeURIComponent("<?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : ''; ?>") +
              "&mat=" + encodeURIComponent("<?php echo isset($_SESSION['matricula']) ? $_SESSION['matricula'] : ''; ?>") +
              "&perfil=" + encodeURIComponent("<?php echo isset($_SESSION['perfil']) ? $_SESSION['perfil'] : ''; ?>") +
              "&tipo=" + encodeURIComponent("<?php echo isset($_SESSION['tipo']) ? $_SESSION['tipo'] : ''; ?>") +
              "&from_modal=1";
            document.getElementById('iframeImport').src = iframeUrl;
          </script>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

</body>
</html>
