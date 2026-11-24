<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

if (empty($_SESSION['checklist_csrf_token'])) {
  $_SESSION['checklist_csrf_token'] = bin2hex(random_bytes(32));
}

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

/*$placaveic = $_POST['placa'];
$placacorreta = str_replace(" ", "", $placaveic);*/

$data = date('Y-m-d');

include"conecta.php";

//include_once"./func/log.php";

//$link = "$_SERVER[REQUEST_URI]";
//$idaux = explode("=", $link);
//$placa = $idaux[1];

$placa = $_GET['placa'];
$datavistoria = $_GET['datavistoria'];
$matriculac = $_GET['matricula'];
$idinserido = $_GET['idinserido'];

$id = $idinserido;

// Busca os dados do Passo 1 (checklist) para exibir no modal
$sqlDados = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria = '$id'";
$resultadoDados = mysqli_query($con, $sqlDados) or die(mysqli_error($con));
$dadosChecklist = mysqli_fetch_assoc($resultadoDados);

// Formata os dados para exibição
$tipoVistoria = '';
if ($dadosChecklist['tipo']) {
    $sqlTipo = "SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist = '" . $dadosChecklist['tipo'] . "'";
    $resTipo = mysqli_query($con, $sqlTipo);
    if ($rowTipo = mysqli_fetch_assoc($resTipo)) {
        $tipoVistoria = $rowTipo['tipo'];
    }
}

$dataVistoriaFormatada = date('d/m/Y', strtotime($dadosChecklist['datavistoria']));

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta http-equiv="ScreenOrientation" content="autoRotate:disabled"/>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Checklist - Passo 2 </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

  <!-- Bootstrap core CSS 
  <link href="src/index_files/bootstrap.css" rel="stylesheet">-->

  <!-- Custom styles for this template -->
  <!--<script src="src/index_files/ie-emulation-modes-warning.js.download"></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  
  <!--data tables - css e js
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    @media only screen and (min-width: 320px) and (max-width: 767px) and (orientation:landscape) {
      body {
        height: 100vw;
        transform: rotate(90deg);
      }
    }

    /* Garantir que o botão fixo fique acima do footer */
    #inputSubmit {
      animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translate(-50%, 20px); }
      to { opacity: 1; transform: translate(-50%, 0); }
    }

    /* Responsividade para SMARTPHONES (max-width: 576px) */
    @media (max-width: 576px) {
      /* Ajustes no cabeçalho */
      .header-title {
        font-size: 1.5rem !important;
      }
      .header-placa {
        font-size: 1rem !important;
        display: block !important;
        margin-top: 0.5rem !important;
        margin-left: 0 !important;
      }
      
      /* Botão Próximo */
      #inputSubmit {
        bottom: 70px !important;
        padding: 10px 20px !important;
      }
      #inputSubmit .btn {
        padding: 0.5rem 2rem !important;
        font-size: 0.9rem !important;
      }
      
      /* Container do carro */
      .car-container {
        height: 50vh !important;
        margin-bottom: 80px !important;
      }
      
      /* Imagem do carro menor em mobile */
      .car-image {
        width: 160px !important;
        height: 300px !important;
      }
      
      /* Botões de foto menores */
      .btn-foto {
        font-size: 0.65rem !important;
        padding: 0.25rem 0.4rem !important;
      }
      
      /* Main padding */
      main {
        padding-bottom: 100px !important;
      }
    }
    
    /* Responsividade para TABLETS (577px - 991px) */
    @media (min-width: 577px) and (max-width: 991px) {
      /* Ajustes no cabeçalho */
      .header-title {
        font-size: 1.8rem !important;
      }
      .header-placa {
        font-size: 1.2rem !important;
      }
      
      /* Container do carro */
      .car-container {
        height: 58vh !important;
      }
      
      /* Imagem do carro ajustada para tablet */
      .car-image {
        width: 180px !important;
        height: 340px !important;
      }
      
      /* Botões de foto */
      .btn-foto {
        font-size: 0.7rem !important;
        padding: 0.3rem 0.5rem !important;
      }
    }
    
    /* Desktop (acima de 992px) - valores padrão */
    @media (min-width: 992px) {
      .car-image {
        width: 200px;
        height: 380px;
      }
    }
    
  </style>
</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100vw; padding-bottom: 120px;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <div class="text-center pt-3 pb-2">
          <h1 class="h1 d-inline-block mb-0 header-title"> Checklist do Veículo </h1>
          <h5 class="d-inline-block mb-0 ms-3 header-placa">(
            <i class="fas fa-car me-1"></i>
            <strong><?php echo strtoupper($placa); ?></strong>
          )</h5>
          
          <!-- Botão para abrir modal com dados do Passo 1 -->
          <div class="mt-3">
            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDadosChecklist">
              <i class="fas fa-info-circle me-2"></i>Visualizar Dados do Checklist
            </button>
          </div>
        </div>

        <!-- div form-->
        <div>
          <?php

                $sql = "SELECT 
                    CASE WHEN frontal <> '' THEN 1 ELSE 0 END AS frontal,
                    CASE WHEN traseira <> '' THEN 1 ELSE 0 END AS traseira,
                    CASE WHEN direita <> '' THEN 1 ELSE 0 END AS direita,
                    CASE WHEN esquerda <> '' THEN 1 ELSE 0 END AS esquerda,
                    CASE WHEN painel <> '' THEN 1 ELSE 0 END AS painel,
                    CASE WHEN selfie <> '' THEN 1 ELSE 0 END AS selfie,
                    CASE WHEN cnh <> '' THEN 1 ELSE 0 END AS cnh,
                    CASE WHEN extra1 <> '' THEN 1 ELSE 0 END AS extra1,
                    CASE WHEN extra2 <> '' THEN 1 ELSE 0 END AS extra2,
                    CASE WHEN extra3 <> '' THEN 1 ELSE 0 END AS extra3
                FROM 
                    bdfrota.tbvistoriafotos
                WHERE 
                    idtbvistoria = $id";

              // Executar a consulta
              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

              // Obter a primeira linha do resultado
              $linha = mysqli_fetch_assoc($resultado);
              
              // Criar elementos HTML com base nos valores da linha
              foreach ($linha as $chave => $valor) {
                  echo "<div class='d-none' id='resp$chave'>$valor</div>";
              }

                ?>
            <div class="position-relative car-container" style="height: 55vh; max-width: 800px; margin: 0 auto; margin-bottom: 100px; margin-top: 10px;">
              <div style="position: relative; height: 100%; display: flex; justify-content: center; align-items: center;">

                <img src="./src/images/istockphoto-carro1.jpg" class="car-image" style="position: absolute; width: 200px; height: 380px; object-fit: contain;">

                <!-- div foto frontal -->
                <div style="position: absolute; left: 50%; top: 10%; transform: translateX(-50%);">
                  <label for="frontal-foto" class="btn btn-primary btn-sm btn-foto" id='frontal' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Frontal <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='frontal-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='frontal' style='display: none;'>
                </div>

                <!-- div foto Traseira --> 
                <div style="position: absolute; left: 50%; bottom: 10%; transform: translateX(-50%);">
                  <label for="traseira-foto" class="btn btn-primary btn-sm btn-foto" id='traseira' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Traseira <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='traseira-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='traseira' style='display: none;' required>
                </div>

                <!-- div foto Painel -->
                <div style="position: absolute; left: 5%; top: 28%;">
                  <label for="painel-foto" class="btn btn-primary btn-sm btn-foto" id='painel' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Painel <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='painel-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='painel' style='display: none;' required>
                </div>

                <!-- div foto Lateral Esquerda -->
                <div style="position: absolute; left: 5%; top: 43%;">
                  <label for="esquerda-foto" class="btn btn-primary btn-sm btn-foto" id='esquerda' style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                    <i class="fas fa-camera"></i> Lat. Esq. <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='esquerda-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='esquerda' style='display: none;' required>
                </div>

                <!-- div foto Lateral Direita -->
                <div style="position: absolute; right: 5%; top: 33%;">
                  <label for="direita-foto" class="btn btn-primary btn-sm btn-foto" id='direita' style="font-size: 0.7rem; padding: 0.3rem 0.5rem;">
                    <i class="fas fa-camera"></i> Lat. Dir. <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='direita-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='direita' style='display: none;' required>
                </div>                

                <!-- div foto Selfie -->
                <div style="position: absolute; right: 5%; top: 18%;">
                  <label for="selfie-foto" class="btn btn-primary btn-sm btn-foto" id='selfie' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Selfie <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='selfie-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' accept='user' name='selfie' style='display: none;' required>
                </div> 

                <!-- div foto CNH -->
                <div style="position: absolute; right: 5%; top: 48%;">
                  <label for="cnh-foto" class="btn btn-primary btn-sm btn-foto" id='cnh' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> CNH <span style="color: red;">*</span>
                  </label>
                  <input type='file' id='cnh-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='cnh' style='display: none;' required>
                </div> 

                <!-- div foto Extra -->
                <div style="position: absolute; right: 5%; top: 60%;">
                  <label for="extra1-foto" class="btn btn-primary btn-sm btn-foto" id='extra1' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Extra 1
                  </label>
                  <input type='file' id='extra1-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra1' style='display: none;'>
                </div> 

                <!-- div foto Extra -->
                <div style="position: absolute; right: 5%; top: 71%;">
                  <label for="extra2-foto" class="btn btn-primary btn-sm btn-foto" id='extra2' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Extra 2
                  </label>
                  <input type='file' id='extra2-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra2' style='display: none;'>
                </div>

                <!-- div foto Extra -->
                <div style="position: absolute; right: 5%; top: 82%;">
                  <label for="extra3-foto" class="btn btn-primary btn-sm btn-foto" id='extra3' style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                    <i class="fas fa-camera"></i> Extra 3
                  </label>
                  <input type='file' id='extra3-foto' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='extra3' style='display: none;'>
                </div>

              <!-- div campos obrigatórios-->
              <div class="text-center" style="position: absolute; left: 50%; bottom: -40px; transform: translateX(-50%); width: 100%;">
                <p style='color: red; font-size: 0.85rem; margin-bottom: 0;'> * Campos obrigatórios </p>
              </div>
            </div>

            <!-- div botão FIXO (sempre visível, independente do zoom) -->
            <form method="post" action="./control/checklistp2.php" enctype='multipart/form-data'>
              <?php
                print "<input type='hidden' id='placa' name='placa' value='$placa' />
                <input type='hidden' id='vistoriador' name='vistoriador' value='$matricula1' />
                <input type='hidden' id='matriculac' name='matriculac' value='$matriculac' />
                <input type='hidden' name='datavistoriaf' id='datavistoriaf' value='$datavistoria' />
                <input type='hidden' name='idinserido' id='idinserido' value='$idinserido' />
                ";
                
              ?>
            <div class="text-center d-none" id='inputSubmit' style="position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); z-index: 1000; background: white; padding: 15px 30px; border-radius: 10px; box-shadow: 0 -2px 10px rgba(0,0,0,0.2);">
              <button class="btn btn-success btn-lg px-5" type='submit' id='proximo'> 
                <i class="fas fa-arrow-right me-2"></i>Próximo 
              </button>
            </div>
          </form>
        </div>   
      </div>
    </main>

    <footer class="py-4 bg-light mt-auto fixed-bottom">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
            <div>
                <a href="./checklistinicio.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
      </div>
    </footer>
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<!-- Modal com os dados do Passo 1 -->
<div class="modal fade" id="modalDadosChecklist" tabindex="-1" aria-labelledby="modalDadosChecklistLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalDadosChecklistLabel">
          <i class="fas fa-clipboard-check me-2"></i>Dados do Checklist - Passo 1
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <!-- Informações do Condutor -->
        <div class="mb-4">
          <h6 class="border-bottom pb-2"><i class="fas fa-user me-2"></i>Informações do Condutor</h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Nome:</strong> <?php echo $dadosChecklist['nome'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Matrícula:</strong> <?php echo $dadosChecklist['matricula'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>CPF:</strong> <?php echo $dadosChecklist['cpf'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>CNH:</strong> <?php echo $dadosChecklist['cnh'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Categoria CNH:</strong> <?php echo $dadosChecklist['categoriacnh'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Validade CNH:</strong> <?php echo $dadosChecklist['validadecnh'] ? date('d/m/Y', strtotime($dadosChecklist['validadecnh'])) : 'N/A'; ?>
            </div>
          </div>
        </div>

        <!-- Informações do Veículo -->
        <div class="mb-4">
          <h6 class="border-bottom pb-2"><i class="fas fa-car me-2"></i>Informações do Veículo</h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Placa:</strong> <?php echo strtoupper($dadosChecklist['placa']) ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Modelo:</strong> <?php echo $dadosChecklist['modelo'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Ano Fabricação:</strong> <?php echo $dadosChecklist['anofabricacao'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Unidade:</strong> <?php echo $dadosChecklist['unidade'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Centro de Custo:</strong> <?php echo $dadosChecklist['centrocusto'] ?? 'N/A'; ?>
            </div>
          </div>
        </div>

        <!-- Informações da Vistoria -->
        <div class="mb-4">
          <h6 class="border-bottom pb-2"><i class="fas fa-clipboard-list me-2"></i>Informações da Vistoria</h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>Tipo:</strong> <?php echo $tipoVistoria ?: 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Vistoriador:</strong> <?php echo $dadosChecklist['vistoriador'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Data da Vistoria:</strong> <?php echo $dataVistoriaFormatada ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Hodômetro:</strong> <?php echo $dadosChecklist['hodometro'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Nível do Tanque:</strong> <?php echo $dadosChecklist['niveltanque'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Estado Geral:</strong> <?php echo $dadosChecklist['estado'] ?? 'N/A'; ?>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Possui Avaria:</strong> <?php echo ($dadosChecklist['avaria'] == '1') ? 'SIM' : 'NÃO'; ?>
            </div>
          </div>
        </div>

        <!-- Itens da Vistoria -->
        <div class="mb-4">
          <h6 class="border-bottom pb-2"><i class="fas fa-tasks me-2"></i>Itens da Vistoria</h6>
          <div class="row">
            <?php
            $itens = [
              'teto' => 'Teto',
              'frente' => 'Frente',
              'latesq' => 'Lateral Esquerda',
              'latdir' => 'Lateral Direita',
              'traseira' => 'Traseira',
              'itinterno' => 'Itens Internos',
              'pneus' => 'Pneus',
              'bateria' => 'Bateria',
              'safecar' => 'SafeCar',
              'limpint' => 'Limpeza Interna',
              'limpext' => 'Limpeza Externa'
            ];
            
            foreach ($itens as $campo => $label) {
              $valor = $dadosChecklist[$campo] ?? '';
              $classe = '';
              $texto = 'N/A';
              
              if ($valor == 'ok') {
                $classe = 'text-success';
                $texto = 'OK';
              } elseif ($valor == 'naook') {
                $classe = 'text-warning';
                $texto = 'NÃO OK';
              }
              
              echo "<div class='col-md-4 mb-2'><strong>$label:</strong> <span class='$classe'>$texto</span></div>";
            }
            ?>
          </div>
        </div>

        <!-- Observações -->
        <?php if (!empty($dadosChecklist['observacao'])): ?>
        <div class="mb-3">
          <h6 class="border-bottom pb-2"><i class="fas fa-comment me-2"></i>Observações</h6>
          <p class="text-muted"><?php echo nl2br(htmlspecialchars($dadosChecklist['observacao'])); ?></p>
        </div>
        <?php endif; ?>

      </div>
      <div class="modal-footer d-flex justify-content-between">
        <form method="post" action="./control/excluir-checklist.php" onsubmit="return confirm('Deseja excluir este checklist pendente? Os dados e vínculos de fotos serão removidos.');">
          <input type="hidden" name="idinserido" value="<?php echo (int) $idinserido; ?>">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['checklist_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
          <button class="btn btn-danger" type="submit">
            <i class="fas fa-trash me-1"></i>Excluir e iniciar novo
          </button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Fechar
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  
<script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

<script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

<script>
  

  document.querySelectorAll('input[type=file]').forEach((e)=>{
    e.addEventListener('change', function() {
      var file = this.files[0];
      var formData = new FormData();
      formData.append('file', file);

      // Adiciona os elementos hidden ao FormData
      formData.append('placa', document.getElementById('placa').value);
      formData.append('vistoriador', document.getElementById('vistoriador').value);
      formData.append('matriculac', document.getElementById('matriculac').value);
      formData.append('datavistoriaf', document.getElementById('datavistoriaf').value);
      formData.append('idinserido', document.getElementById('idinserido').value);
      formData.append('localcarro',  e.id);

      var localcarro = e.id.replace("-foto", "");
      

      var xhr = new XMLHttpRequest();
      xhr.open('POST', './control/checklistp2-foto.php', true);

      xhr.onload = function() {
          if (xhr.status === 200) {
              // Aqui você pode tratar a resposta do servidor
              console.log(xhr.responseText);
              document.getElementById('resp'+localcarro).innerHTML = '1';
          } else {
              // Se houver um erro no envio
              alert('Erro ao enviar o arquivo.');
          }
      };

      xhr.onerror = function() {
          alert('Erro de rede ao enviar o arquivo.');
      };

      xhr.send(formData);
  });
  });
</script>

<script type="text/javascript">

  /*function mudar(){
    var btnfrontal = document.getElementById('frontal');
    var frontal = document.getElementById("frontal-foto");

    if(frontal.value!=""){
      btnfrontal.classList.remove('btn-primary');
      btnfrontal.classList.add('btn-success');
      console.log(frontal.value);
    }
  }*/

  $(document).ready(function () {
    setInterval(function(){
        var frontal = document.getElementById('respfrontal').innerHTML;

        var traseira = document.getElementById('resptraseira').innerHTML;
        var painel = document.getElementById('resppainel').innerHTML;
        var esquerda = document.getElementById('respesquerda').innerHTML;
        var direita = document.getElementById('respdireita').innerHTML;
        var selfie = document.getElementById('respselfie').innerHTML;
        var cnh = document.getElementById('respcnh').innerHTML;
        var extra1 = document.getElementById('respextra1').innerHTML;
        var extra2 = document.getElementById('respextra2').innerHTML;
        var extra3 = document.getElementById('respextra3').innerHTML;

        
        var btnfrontal = document.getElementById('frontal');
        var btntraseira = document.getElementById("traseira");
        var btnpainel = document.getElementById("painel");
        var btnesquerda = document.getElementById("esquerda");
        var btndireita = document.getElementById("direita");        
        var btnselfie = document.getElementById("selfie");
        var btncnh = document.getElementById("cnh");
        var btnextra1 = document.getElementById("extra1");
        var btnextra2 = document.getElementById("extra2");
        var btnextra3 = document.getElementById("extra3");
        

        if(frontal=="1"){
          btnfrontal.classList.remove('btn-primary');
          btnfrontal.classList.add('btn-secondary');
        }
        
        if(traseira=="1"){
          btntraseira.classList.remove('btn-primary');
          btntraseira.classList.add('btn-secondary');
        }

        if(painel=="1"){
          btnpainel.classList.remove('btn-primary');
          btnpainel.classList.add('btn-secondary');
        }

        if(esquerda=="1"){
          btnesquerda.classList.remove('btn-primary');
          btnesquerda.classList.add('btn-secondary');
        }

        if(direita=="1"){
          btndireita.classList.remove('btn-primary');
          btndireita.classList.add('btn-secondary');
        }

        

        if(selfie=="1"){
          btnselfie.classList.remove('btn-primary');
          btnselfie.classList.add('btn-secondary');
        }

        if(cnh=="1"){
          btncnh.classList.remove('btn-primary');
          btncnh.classList.add('btn-secondary');
        }

        if(extra1=="1"){
          btnextra1.classList.remove('btn-primary');
          btnextra1.classList.add('btn-secondary');
        }

        if(extra2=="1"){
          btnextra2.classList.remove('btn-primary');
          btnextra2.classList.add('btn-secondary');
        }

        if(extra3=="1"){
          btnextra3.classList.remove('btn-primary');
          btnextra3.classList.add('btn-secondary');
        }        

        if(frontal=="1" && traseira=="1" && painel=="1" && esquerda=="1" && direita=="1" && selfie=="1" && cnh=="1") {
          $("#inputSubmit").removeClass('d-none');
        }else{
          $("#inputSubmit").addClass('d-none');
        }

        /*if(frontal.value=="" || traseira.value=="" ) {
          $("#inputGroupFileAddon04").addClass('d-none');
        }else{
          $("#inputGroupFileAddon04").removeClass('d-none');
        }*/
    });


  });

  $( "form" ).on( "submit", function( event ) {
    //var btn = document.getElementById("proximo");
    $("#proximo").addClass('d-none');
  });

  

  </script>

</body>
</html>