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

$matricula = $_POST['matricula'];

include"../conecta2.php";
//include"conecta.php";
//include_once"./func/log.php";
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

  <title> Cadastro de Condutor </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!-- Select2 para select com busca -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Estilos para cards colapsáveis -->
  <style>
    .btn-collapse {
      background: none;
      border: none;
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    
    .btn-collapse.rotated {
      transform: rotate(180deg);
    }
    
    .card-header {
      background-color: #f8f9fa;
      font-weight: 600;
    }

    /* Botões de ação fixos na tela */
    .action-buttons-fixed {
      position: fixed;
      bottom: 70px; /* Acima do footer fixo */
      right: 30px;
      z-index: 1050;
      display: flex;
      gap: 15px;
      background: rgba(255, 255, 255, 0.98);
      padding: 12px 18px;
      border-radius: 50px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
      backdrop-filter: blur(10px);
      border: 2px solid rgba(0, 0, 0, 0.05);
    }

    .action-buttons-fixed .btn {
      transition: all 0.3s ease;
    }

    .action-buttons-fixed .btn:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Espaçamento inferior para evitar sobreposição com botões fixos */
    .form-container {
      margin-bottom: 100px;
      padding-bottom: 20px;
    }

    /* Estilo para contadores de caracteres */
    .char-counter {
      font-size: 11px;
      font-weight: 500;
      margin-top: 2px;
      transition: color 0.3s ease;
    }
    
    .char-counter.complete {
      color: #28a745;
    }
    
    .char-counter.incomplete {
      color: #dc3545;
    }
    
    .char-counter.neutral {
      color: #6c757d;
    }
  </style>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2"> Cadastro de Condutor </h1>

        <!-- div do formulario -->
        <div class="form-container" style="width: 100%;">
<form id="formCondutor" method="post" action="./control/processarcondutor.php" class="mb-3">
              <p style='font-size: 10px;'><span style='color: red;'>*</span> Campos obrigatórios.</p>
            
            <?php 
              $sql = "SELECT nome, matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matricula';";

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $nome = utf8_encode($row['nome']);
                  $matricula = $row['matricula'];
                }

                print "<input type='hidden' name='matr_autor' value='$matricula1'>";
                
                // Gera a próxima matrícula automaticamente (inicia em 620000)
                $sql_mat = "SELECT MAX(CAST(matricula AS UNSIGNED)) as max_mat FROM bdaniel.tbfuncionario WHERE matricula REGEXP '^62[0-9]{4}$'";
                $resultado_mat = mysqli_query($conexao, $sql_mat);
                $proxima_matricula = '620000'; // Valor inicial
                
                if ($resultado_mat && $row_mat = mysqli_fetch_array($resultado_mat, MYSQLI_ASSOC)) {
                  if ($row_mat['max_mat']) {
                    $proxima_matricula = str_pad($row_mat['max_mat'] + 1, 6, '0', STR_PAD_LEFT);
                  }
                }
            ?>

            <div id='dadoscadastrais'>
              <!-- CARD 1: Informações Pessoais -->
              <div class='card'>
                <div class='card-header d-flex justify-content-between'>
                  <h6><i class="fas fa-user me-2"></i>Informações Pessoais</h6>
                  <button type="button" data-bs-toggle="collapse" href="#collapseExample1" id='botao1' onclick="girarBotao('botao1')" class='btn-collapse'>
                    <i class="fa-solid fa-chevron-up"></i>
                  </button>
                </div>
                <div class='card-body collapse show' id="collapseExample1">
                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-6'>
                      <label for='nome' class='form-label'>Nome Completo:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='nome' name='nome' value='<?php echo $nome; ?>' required>
                      <input type='hidden' name='matricula' value='<?php echo $matricula; ?>'>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='datanascimento' class='form-label'>Data de Nascimento:<span style='color: red;'>*</span></label>
                      <input type='date' class='form-control form-control-sm data-validation' id='datanascimento' name='datanascimento' required>
                    </div>
                  </div>
                </div>
              </div>

              <!-- CARD 2: Documentos -->
              <div class='card mt-3'>
                <div class='card-header d-flex justify-content-between'>
                  <h6><i class="fas fa-file-alt me-2"></i>Documentos</h6>
                  <button type="button" data-bs-toggle="collapse" href="#collapseExample2" id='botao2' onclick="girarBotao('botao2')" class='btn-collapse'>
                    <i class="fa-solid fa-chevron-up"></i>
                  </button>
                </div>
                <div class='card-body collapse show' id="collapseExample2">
                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-3'>
                      <label for='cpf' class='form-label'>CPF (somente números):<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='cpf' name='cpf' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='11' required>
                      <div class='char-counter incomplete' id='cpf-counter'>0/11 dígitos</div>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='rg' class='form-label'>RG (somente números):</label>
                      <input type='text' class='form-control form-control-sm' id='rg' name='rg' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='11' minlength='6'>
                      <div class='char-counter neutral' id='rg-counter'>0 dígitos (6-11)</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- CARD 3: Informações de Endereço -->
              <div class='card mt-3'>
                <div class='card-header d-flex justify-content-between'>
                  <h6><i class="fas fa-address-book me-2"></i>Informações de Endereço</h6>
                  <button type="button" data-bs-toggle="collapse" href="#collapseExample3" id='botao3' onclick="girarBotao('botao3')" class='btn-collapse'>
                    <i class="fa-solid fa-chevron-up"></i>
                  </button>
                </div>
                <div class='card-body collapse show' id="collapseExample3">
                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-2'>
                      <label for='cep' class='form-label'>CEP:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='cep' name='cep' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='8' required>
                      <div class='char-counter incomplete' id='cep-counter'>0/8 dígitos</div>
                    </div>

                    <div class='ms-3 col-4'>
                      <label for='endereco' class='form-label'>Endereço:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='endereco' name='endereco' required>
                    </div>

                    <div class='ms-3 col-1'>
                      <label for='numres' class='form-label'>Número:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='numres' name='numres' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='10' required>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='bairro' class='form-label'>Bairro:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='bairro' name='bairro' required>
                    </div>
                  </div>

                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-3'>
                      <label for='cidade' class='form-label'>Cidade:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='cidade' name='cidade' required>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='estado' class='form-label'>Estado (UF):<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='estado' name='estado' maxlength='2' required>
                    </div>

                    <div class='ms-3 col-4'>
                      <label for='email' class='form-label'>E-mail:</label>
                      <input type='email' class='form-control form-control-sm' id='email' name='email'>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CARD 4: Dados Contratuais -->
            <div id='dadoscontratuais' class='mt-2'>
              <div class='card'>
                <div class='card-header d-flex justify-content-between'>
                  <h6><i class="fas fa-briefcase me-2"></i>Dados Contratuais</h6>
                  <button type="button" data-bs-toggle="collapse" href="#collapseExample4" id='botao4' onclick="girarBotao('botao4')" class='btn-collapse'>
                    <i class="fa-solid fa-chevron-up"></i>
                  </button>
                </div>
                <div class='card-body collapse show' id="collapseExample4">
                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-2'>
                      <label for='matfunc' class='form-label'>Matrícula:<span style='color: red;'>*</span></label>
                      <input type='text' class='form-control form-control-sm' id='matfunc' name='matfunc' value='<?php echo $proxima_matricula; ?>' readonly style='background-color: #e9ecef;'>
                      <small class='text-muted'>Auto-gerada (6 dígitos)</small>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='mataux' class='form-label'>Mat. Auxiliar:</label>
                      <input type='text' class='form-control form-control-sm' id='mataux' name='mataux' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='6'>
                      <div class='char-counter neutral' id='mataux-counter'>0/6 dígitos</div>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='status' class='form-label'>Situação:</label>
                      <select class='form-select form-select-sm' name='status' id='status' disabled>
                        <option value='ATIVO' selected>Ativo</option>
                        <option value='INATIVO'>Inativo</option>
                        <option value='AFASTADO'>Afastado</option>
                        <option value='FERIAS'>Férias</option>
                      </select>
                      <input type='hidden' name='status' value='ATIVO'>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='dtadmissao' class='form-label'>Data Admissão:<span style='color: red;'>*</span></label>
                      <input type='date' class='form-control form-control-sm data-validation' id='dtadmissao' name='dtadmissao' required>
                    </div>

                   \                    <div class='ms-3 col-3'>
                      <!-- EMPRESA -->
                      <label for='codempresa' class='form-label'>Base de Gestão:<span style='color: red;'>*</span></label>
                      <select class='form-select form-select-sm select-searchable' name='codempresa' id='codempresa' required>
                        <option value=''>Selecione</option>
                        <option value='999999'>Hallen</option>
                        <?php
                          // Busca filiais com DISTINCT e TRIM para evitar duplicatas
                          $sql_empresa = "SELECT DISTINCT idtbfilial, TRIM(nome) as nome FROM bdaniel.tbfilial WHERE TRIM(nome) != '' ORDER BY nome";
                          $resultado_empresa = mysqli_query($conexao, $sql_empresa);
                          
                          // Array para controlar nomes já exibidos (remover duplicatas de nomes iguais)
                          $nomes_exibidos = array();
                          
                          if ($resultado_empresa) {
                            while($row_empresa = mysqli_fetch_array($resultado_empresa, MYSQLI_ASSOC)) {
                              $id_filial = $row_empresa['idtbfilial'];
                              $nome_filial = utf8_encode(trim($row_empresa['nome']));
                              
                              // Verifica se o nome já foi exibido (evita duplicatas)
                              if (!in_array(strtoupper($nome_filial), $nomes_exibidos)) {
                                $nomes_exibidos[] = strtoupper($nome_filial);
                                echo "<option value='$id_filial'>$nome_filial</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-3'>
                      <label for='ccusto' class='form-label'>Departamento/Centro de Custo:<span style='color: red;'>*</span></label>
                      <select class='form-select form-select-sm select-searchable' name='ccusto' id='ccusto' required>
                        <option value=''>Selecione</option>
                        <?php
                          // Busca departamentos/centros de custo distintos da tabela
                          $sql_depto = "SELECT DISTINCT UPPER(ccusto) as ccusto FROM bdaniel.tbfuncionario WHERE ccusto IS NOT NULL AND ccusto != '' ORDER BY ccusto";
                          $resultado_depto = mysqli_query($conexao, $sql_depto);
                          
                          if ($resultado_depto) {
                            while($row_depto = mysqli_fetch_array($resultado_depto, MYSQLI_ASSOC)) {
                              $ccusto_item = $row_depto['ccusto'];
                              // Ignora valores que são apenas números
                              if (!is_numeric($ccusto_item) && !preg_match('/^\d+$/', $ccusto_item)) {
                                echo "<option value='$ccusto_item'>$ccusto_item</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='cargo' class='form-label'>Cargo:<span style='color: red;'>*</span></label>
                      <select class='form-select form-select-sm select-searchable' name='cargo' id='cargo' required>
                        <option value=''>Selecione</option>
                        <?php
                          // Busca cargos distintos da tabela
                          $sql_cargo = "SELECT DISTINCT UPPER(cargo) as cargo FROM bdaniel.tbfuncionario WHERE cargo IS NOT NULL AND cargo != '' ORDER BY cargo";
                          $resultado_cargo = mysqli_query($conexao, $sql_cargo);
                          
                          if ($resultado_cargo) {
                            while($row_cargo = mysqli_fetch_array($resultado_cargo, MYSQLI_ASSOC)) {
                              $cargo_item = $row_cargo['cargo'];
                              // Ignora valores que são apenas números
                              if (!is_numeric($cargo_item) && !preg_match('/^\d+$/', $cargo_item)) {
                                echo "<option value='$cargo_item'>$cargo_item</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='projeto' class='form-label'>Projeto:</label>
                      <select class='form-select form-select-sm select-searchable' name='projeto' id='projeto'>
                        <option value=''>Selecione</option>
                        <?php
                          // Busca projetos distintos da tabela
                          $sql_projeto = "SELECT DISTINCT UPPER(TRIM(projeto)) as projeto FROM bdaniel.tbfuncionario WHERE projeto IS NOT NULL AND TRIM(projeto) != '' ORDER BY projeto";
                          $resultado_projeto = mysqli_query($conexao, $sql_projeto);
                          
                          if ($resultado_projeto) {
                            while($row_projeto = mysqli_fetch_array($resultado_projeto, MYSQLI_ASSOC)) {
                              $projeto_item = $row_projeto['projeto'];
                              // Ignora valores que são apenas números
                              if (!is_numeric($projeto_item) && !preg_match('/^\d+$/', $projeto_item)) {
                                echo "<option value='$projeto_item'>$projeto_item</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='uf_trabalho' class='form-label'>UF de Trabalho:<span style='color: red;'>*</span></label>
                      <select class='form-select form-select-sm' name='uf_trabalho' id='uf_trabalho' required>
                        <option value=''>Selecione</option>
                        <option value='AC'>AC</option>
                        <option value='AL'>AL</option>
                        <option value='AM'>AM</option>
                        <option value='AP'>AP</option>
                        <option value='BA'>BA</option>
                        <option value='CE'>CE</option>
                        <option value='DF'>DF</option>
                        <option value='ES'>ES</option>
                        <option value='GO'>GO</option>
                        <option value='MA'>MA</option>
                        <option value='MG'>MG</option>
                        <option value='MS'>MS</option>
                        <option value='MT'>MT</option>
                        <option value='PA'>PA</option>
                        <option value='PB'>PB</option>
                        <option value='PE'>PE</option>
                        <option value='PI'>PI</option>
                        <option value='PR'>PR</option>
                        <option value='RJ'>RJ</option>
                        <option value='RN'>RN</option>
                        <option value='RO'>RO</option>
                        <option value='RR'>RR</option>
                        <option value='RS'>RS</option>
                        <option value='SC'>SC</option>
                        <option value='SE'>SE</option>
                        <option value='SP'>SP</option>
                        <option value='TO'>TO</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </form>
          
          <!-- Botões de ação fixos -->
          <div class='action-buttons-fixed'>
            <a class="btn-geral btn btn-danger rounded-circle" href='./listar_condutorespj.php' title='Cancelar'> 
              <i class="fa-solid fa-arrow-left fa-xl"></i> 
            </a>
            <button class="btn-geral btn btn-success rounded-circle" type='submit' form='formCondutor' title='Salvar'> 
              <i class="fa-solid fa-floppy-disk icon fa-xl"></i> 
            </button>
          </div>
        </div>
      </div>
    </main>

    <footer class="mt-4 py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
      <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
              <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
              <!--<div>
                  <a href="#">Privacy Policy</a>
                                  &middot;
                  <a href="#">Terms &amp; Conditions</a>
              </div>!-->
          </div>
      </div>
    </footer>
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>-->
  <!--<script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    //mascaras de input - REMOVIDAS para CPF, RG e CEP permitirem apenas números puros
    $(document).ready(function(){
      // Máscaras removidas - campos aceitam apenas dígitos conforme validação
      // CPF: 11 dígitos, RG: 9 dígitos, CEP: 8 dígitos, Matrícula: 6 dígitos

      //formatação do texto no input para maiúsculas
      $("input[type='text']").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      // Função para atualizar contadores de caracteres
      function updateCharCounter(inputId, counterId, maxLength, isRequired) {
        const input = $('#' + inputId);
        const counter = $('#' + counterId);
        
        input.on('input', function() {
          const currentLength = $(this).val().length;
          
          // Formatação especial para RG (6-11 dígitos)
          if (inputId === 'rg') {
            if (currentLength >= 6 && currentLength <= 11) {
              counter.text(currentLength + ' dígitos (✓ válido)');
              counter.removeClass('incomplete neutral').addClass('complete');
            } else if (currentLength > 0) {
              counter.text(currentLength + ' dígitos (6-11 necessários)');
              counter.removeClass('complete neutral').addClass('incomplete');
            } else {
              counter.text('0 dígitos (6-11)');
              counter.removeClass('complete incomplete').addClass('neutral');
            }
          } else {
            // Formatação padrão para outros campos
            counter.text(currentLength + '/' + maxLength + ' dígitos');
            
            if (currentLength === maxLength) {
              counter.removeClass('incomplete neutral').addClass('complete');
            } else if (currentLength > 0 && isRequired) {
              counter.removeClass('complete neutral').addClass('incomplete');
            } else if (currentLength > 0 && !isRequired) {
              counter.removeClass('complete incomplete').addClass('neutral');
            } else if (currentLength === 0 && isRequired) {
              counter.removeClass('complete neutral').addClass('incomplete');
            } else {
              counter.removeClass('complete incomplete').addClass('neutral');
            }
          }
        });
      }

      // Inicializa contadores para campos com limite
      updateCharCounter('cpf', 'cpf-counter', 11, true);
      updateCharCounter('rg', 'rg-counter', 9, false);
      updateCharCounter('cep', 'cep-counter', 8, true);
      updateCharCounter('mataux', 'mataux-counter', 6, false);

      // Inicializa Select2 para selects com busca
      $('.select-searchable').select2({
        placeholder: 'Selecione ou digite para buscar',
        allowClear: true,
        language: {
          noResults: function() {
            return "Nenhum resultado encontrado";
          },
          searching: function() {
            return "Buscando...";
          }
        }
      });

      // Validação de CPF e RG antes do envio
      $('#formCondutor').on('submit', function(e) {
        var cpf = $('#cpf').val().replace(/\D/g, '');
        var rg = $('#rg').val().replace(/\D/g, '');

        // Valida CPF - exatamente 11 dígitos
        if (cpf.length !== 11) {
          e.preventDefault();
          alert('CPF deve conter exatamente 11 dígitos!');
          $('#cpf').focus();
          return false;
        }

        // Verifica se CPF é inválido (todos os dígitos iguais)
        if (/^(.)\1+$/.test(cpf)) {
          e.preventDefault();
          alert('CPF inválido! Não é permitido CPF com todos os dígitos iguais (ex: 00000000000).');
          $('#cpf').focus();
          return false;
        }

        // Valida RG se preenchido - entre 6 e 11 dígitos
        if (rg.length > 0) {
          if (rg.length < 6 || rg.length > 11) {
            e.preventDefault();
            alert('RG deve conter entre 6 e 11 dígitos!');
            $('#rg').focus();
            return false;
          }

          // Verifica se RG é inválido (todos os dígitos iguais)
          if (/^(.)\1+$/.test(rg)) {
            e.preventDefault();
            alert('RG inválido! Não é permitido RG com todos os dígitos iguais (ex: 000000000).');
            $('#rg').focus();
            return false;
          }
        }

        // Valida CEP - exatamente 8 dígitos
        var cep = $('#cep').val().replace(/\D/g, '');
        if (cep.length !== 8) {
          e.preventDefault();
          alert('CEP deve conter exatamente 8 dígitos!');
          $('#cep').focus();
          return false;
        }
      });

      // Busca CEP
      function limpa_formulario_cep() {
        $("#endereco").val("");
        $("#numres").val("");
        $("#bairro").val("");
        $("#cidade").val("");
        $("#estado").val("");
      }

      $("#cep").blur(function() {
        var cep = $(this).val().replace(/\D/g, '');

        if (cep != "") {
          var validacep = /^[0-9]{8}$/;

          if (validacep.test(cep)) {
            $("#endereco").val("...");
            $("#bairro").val("...");
            $("#cidade").val("...");
            $("#estado").val("...");

            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {
              if (!("erro" in dados)) {
                // Monta o endereço completo
                var enderecoCompleto = dados.logradouro;
                $("#endereco").val(enderecoCompleto);
                $("#bairro").val(dados.bairro);
                $("#cidade").val(dados.localidade);
                $("#estado").val(dados.uf);
              } else {
                limpa_formulario_cep();
                alert("CEP não encontrado.");
              }
            });
          } else {
            limpa_formulario_cep();
            alert("Formato de CEP inválido.");
          }
        } else {
          limpa_formulario_cep();
        }
      });
    });
    
    // Função para girar o botão de collapse
    function girarBotao(btnId) {
      const btn = document.getElementById(btnId);
      btn.classList.toggle('rotated');
    }

    // Validação de ano nas datas
    function validateYear(input) {
      const inputDate = input.value;

      if (inputDate) {
        const dateParts = inputDate.split('-');
        const year = parseInt(dateParts[0], 10);

        if (isNaN(year) || year < 1000 || year > 9999) {
          alert("Por favor, insira um ano válido.");
          input.value = "";
        }
      }
    }

    document.querySelectorAll('.data-validation').forEach(field => {
      field.addEventListener('blur', function() {
        validateYear(this);
      });
    });
    // 
  </script>

</body>
</html>