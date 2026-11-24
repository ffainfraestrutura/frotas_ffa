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

// Controle de acesso padronizado: bloqueia perfis 0, 1, 2 e matrícula 500422
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  exit();
}

// Bloqueia perfis 0, 1 e 2
if ($perfil == '0' || $perfil == '1' || $perfil == '2'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta página. (Perfil: $perfil)');window.location=\"../index.php\"</script>";
  exit();
}

// Bloqueia matrícula 500422
if ($matricula1 == '500422'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta página. (Matrícula bloqueada)');window.location=\"../index.php\"</script>";
  exit();
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

// Pegar matrícula do condutor a ser editado
$matricula_editar = isset($_GET['matricula']) ? $_GET['matricula'] : '';

if (empty($matricula_editar)) {
  echo"<script language='javascript' type='text/javascript'>alert('Matrícula não informada');window.location=\"./listar_condutorespj.php\"</script>";
  exit();
}

include"../conecta2.php";
header("Content-type: text/html; charset=utf-8");

// Buscar dados do condutor
$sql = "SELECT f.*, fil.nome as nome_empresa
        FROM bdaniel.tbfuncionario f
        LEFT JOIN bdaniel.tbfilial fil ON f.codempresa = fil.idtbfilial
        WHERE f.matricula = '$matricula_editar'";

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

if (mysqli_num_rows($resultado) == 0) {
  echo"<script language='javascript' type='text/javascript'>alert('Condutor não encontrado');window.location=\"./listar_condutorespj.php\"</script>";
  exit();
}

$dados = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Editar Condutor PJ </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

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
      bottom: 70px;
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

    .form-container {
      margin-bottom: 100px;
      padding-bottom: 20px;
    }
  </style>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2"> Editar Condutor PJ </h1>

        <!-- div do formulario -->
        <div class="form-container" style="width: 100%;">
          <form id="formCondutor" method="post" action="./atualizar_condutorespj.php" class="mb-3">
            <input type='hidden' name='matr_autor' value='<?php echo $matricula1; ?>'>
            <input type='hidden' name='matricula_original' value='<?php echo $matricula_editar; ?>'>

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
                      <label for='nome' class='form-label'>Nome Completo:</label>
                      <input type='text' class='form-control form-control-sm' id='nome' name='nome' value='<?php echo utf8_encode($dados['nome']); ?>'>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='datanascimento' class='form-label'>Data de Nascimento:</label>
                      <input type='date' class='form-control form-control-sm data-validation' id='datanascimento' name='datanascimento' value='<?php echo $dados['dtnasc']; ?>'>
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
                      <label for='cpf' class='form-label'>CPF (somente números):</label>
                      <input type='text' class='form-control form-control-sm' id='cpf' name='cpf' value='<?php echo $dados['cpf']; ?>' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='14'>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='rg' class='form-label'>RG (somente números):</label>
                      <input type='text' class='form-control form-control-sm' id='rg' name='rg' value='<?php echo $dados['rg']; ?>' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='11'>
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
                      <label for='cep' class='form-label'>CEP:</label>
                      <input type='text' class='form-control form-control-sm' id='cep' name='cep' value='<?php echo $dados['cep']; ?>' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='9'>
                    </div>

                    <div class='ms-3 col-4'>
                      <label for='endereco' class='form-label'>Endereço:</label>
                      <input type='text' class='form-control form-control-sm' id='endereco' name='endereco' value='<?php echo utf8_encode($dados['endereco']); ?>'>
                    </div>

                    <div class='ms-3 col-1'>
                      <label for='numres' class='form-label'>Número:</label>
                      <input type='text' class='form-control form-control-sm' id='numres' name='numres' value='<?php echo $dados['numres']; ?>' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='10'>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='bairro' class='form-label'>Bairro:</label>
                      <input type='text' class='form-control form-control-sm' id='bairro' name='bairro' value='<?php echo utf8_encode($dados['bairro']); ?>'>
                    </div>
                  </div>

                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-3'>
                      <label for='cidade' class='form-label'>Cidade:</label>
                      <input type='text' class='form-control form-control-sm' id='cidade' name='cidade' value='<?php echo utf8_encode($dados['cidade']); ?>'>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='estado' class='form-label'>Estado (UF):</label>
                      <input type='text' class='form-control form-control-sm' id='estado' name='estado' value='<?php echo $dados['estado']; ?>' maxlength='2'>
                    </div>

                    <div class='ms-3 col-4'>
                      <label for='email' class='form-label'>E-mail:</label>
                      <input type='email' class='form-control form-control-sm' id='email' name='email' value='<?php echo $dados['email']; ?>'>
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
                      <label for='matfunc' class='form-label'>Matrícula:</label>
                      <input type='text' class='form-control form-control-sm' id='matfunc' name='matfunc' value='<?php echo $dados['matricula']; ?>'>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='mataux' class='form-label'>Mat. Auxiliar:</label>
                      <input type='text' class='form-control form-control-sm' id='mataux' name='mataux' value='<?php echo $dados['mataux']; ?>' pattern='[0-9]*' inputmode='numeric' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='6'>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='status' class='form-label'>Situação:</label>
                      <select class='form-select form-select-sm' name='status' id='status'>
                        <option value='ATIVO' <?php echo ($dados['status'] == 'ATIVO' || $dados['status'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                        <option value='INATIVO' <?php echo ($dados['status'] == 'INATIVO' || $dados['status'] == 'Inativo') ? 'selected' : ''; ?>>Inativo</option>
                        <option value='AFASTADO' <?php echo ($dados['status'] == 'AFASTADO' || $dados['status'] == 'Afastado') ? 'selected' : ''; ?>>Afastado</option>
                        <option value='FERIAS' <?php echo ($dados['status'] == 'FERIAS' || $dados['status'] == 'Férias') ? 'selected' : ''; ?>>Férias</option>
                      </select>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='dtadmissao' class='form-label'>Data Admissão:</label>
                      <input type='date' class='form-control form-control-sm data-validation' id='dtadmissao' name='dtadmissao' value='<?php echo $dados['dtadmissao']; ?>'>
                    </div>

                    <div class='ms-3 col-3'>
                      <!-- EMPRESA -->
                      <label for='codempresa' class='form-label'>Base de Gestão:</label>
                      <select class='form-select form-select-sm select-searchable' name='codempresa' id='codempresa'>
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
                                $selected = ($dados['codempresa'] == $id_filial) ? 'selected' : '';
                                echo "<option value='$id_filial' $selected>$nome_filial</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class='mb-3 d-flex justify-content-start'>
                    <div class='col-3'>
                      <label for='ccusto' class='form-label'>Departamento/Centro de Custo:</label>
                      <select class='form-select form-select-sm select-searchable' name='ccusto' id='ccusto'>
                        <option value=''>Selecione</option>
                        <?php
                          $sql_depto = "SELECT DISTINCT UPPER(ccusto) as ccusto FROM bdaniel.tbfuncionario WHERE ccusto IS NOT NULL AND ccusto != '' ORDER BY ccusto";
                          $resultado_depto = mysqli_query($conexao, $sql_depto);
                          
                          if ($resultado_depto) {
                            while($row_depto = mysqli_fetch_array($resultado_depto, MYSQLI_ASSOC)) {
                              $ccusto_item = $row_depto['ccusto'];
                              if (!is_numeric($ccusto_item) && !preg_match('/^\d+$/', $ccusto_item)) {
                                $selected = (strtoupper($dados['ccusto']) == $ccusto_item) ? 'selected' : '';
                                echo "<option value='$ccusto_item' $selected>$ccusto_item</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>

                    <div class='ms-3 col-3'>
                      <label for='cargo' class='form-label'>Cargo:</label>
                      <select class='form-select form-select-sm select-searchable' name='cargo' id='cargo'>
                        <option value=''>Selecione</option>
                        <?php
                          $sql_cargo = "SELECT DISTINCT UPPER(cargo) as cargo FROM bdaniel.tbfuncionario WHERE cargo IS NOT NULL AND cargo != '' ORDER BY cargo";
                          $resultado_cargo = mysqli_query($conexao, $sql_cargo);
                          
                          if ($resultado_cargo) {
                            while($row_cargo = mysqli_fetch_array($resultado_cargo, MYSQLI_ASSOC)) {
                              $cargo_item = $row_cargo['cargo'];
                              if (!is_numeric($cargo_item) && !preg_match('/^\d+$/', $cargo_item)) {
                                $selected = (strtoupper($dados['cargo']) == $cargo_item) ? 'selected' : '';
                                echo "<option value='$cargo_item' $selected>$cargo_item</option>";
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
                          $sql_projeto = "SELECT DISTINCT UPPER(TRIM(projeto)) as projeto FROM bdaniel.tbfuncionario WHERE projeto IS NOT NULL AND TRIM(projeto) != '' ORDER BY projeto";
                          $resultado_projeto = mysqli_query($conexao, $sql_projeto);
                          
                          if ($resultado_projeto) {
                            while($row_projeto = mysqli_fetch_array($resultado_projeto, MYSQLI_ASSOC)) {
                              $projeto_item = $row_projeto['projeto'];
                              if (!is_numeric($projeto_item) && !preg_match('/^\d+$/', $projeto_item)) {
                                $selected = (strtoupper($dados['projeto']) == $projeto_item) ? 'selected' : '';
                                echo "<option value='$projeto_item' $selected>$projeto_item</option>";
                              }
                            }
                          }
                        ?>
                      </select>
                    </div>

                    <div class='ms-3 col-2'>
                      <label for='uf_trabalho' class='form-label'>UF de Trabalho:</label>
                      <select class='form-select form-select-sm' name='uf_trabalho' id='uf_trabalho'>
                        <option value=''>Selecione</option>
                        <?php
                          $ufs = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'];
                          foreach ($ufs as $uf) {
                            $selected = ($dados['uf_trabalho'] == $uf) ? 'selected' : '';
                            echo "<option value='$uf' $selected>$uf</option>";
                          }
                        ?>
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
            <button class="btn-geral btn btn-success rounded-circle" type='submit' form='formCondutor' title='Salvar Alterações'> 
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
        </div>
      </div>
    </footer>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

  <script>
    //mascaras de input
    $(document).ready(function(){
      $('#cpf').mask('000.000.000-00');
      $('#rg').mask('00000000000');
      $('#cep').mask('00000-000');
      $('#matfunc').mask('000000');
      $('#mataux').mask('000000');

      //formatação do texto no input para maiúsculas
      $("input[type='text']").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

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

      // Validação de CPF antes do envio
      $('#formCondutor').on('submit', function(e) {
        var cpf = $('#cpf').val().replace(/\D/g, '');
        var rg = $('#rg').val().replace(/\D/g, '');

        // Valida CPF somente se estiver preenchido
        if (cpf.length > 0) {
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
            alert('RG inválido! Não é permitido RG com todos os dígitos iguais (ex: 000000).');
            $('#rg').focus();
            return false;
          }
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
  </script>

</body>
</html>
<!--  -->
