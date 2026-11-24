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
  } elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  } elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  $idtbfornecedor = $_POST['idtbfornecedor'];

  include"../conecta2.php";
  //include"../conecta.php";
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

  <title> Editar Fornecedor </title>
  
  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <link href="../src/css/styles.css" rel="stylesheet" />

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Editar Fornecedor </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/editarfornecedor.php" style="width: 70%;" class="pb-5 mb-5 m-auto">
            <?php 
              $sql = "SELECT * FROM bdfrota.tbfornecedor WHERE idtbfornecedor= '$idtbfornecedor';";
              //$sql = "SELECT fantasia, razaosocial FROM bdfrota.tbfornecedor WHERE idtbfornecedor= 1;";

            //$sql = "SELECT * FROM bdfrota.tbfornecedor WHERE idtbfornecedor= 1;";

            $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
           
            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $idtbfornecedor = $row['idtbfornecedor'];
              $status = $row['status'];
              $tipo = $row['tipo'];
              $fantasia = $row['fantasia'];
              $razaosocial = $row['razaosocial'];
              $iestadual = $row['inscestadual'];
              $imunicipal = $row['inscmunicipal'];
              $cnpj = $row['CNPJ'];               
            }

            print "
              <div class='d-flex flex-column input-group col-sm-8'>
                <input type='hidden' name='idtbfornecedor' id='nome' value='$idtbfornecedor'>
                <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>

                <label class='form-label mt-2' for='nomefant'>Nome Fantasia: <span style='color: red;'>*</span></label>
                <input class='form-control' style='width: 100%;' type='text' name='nomefant' id='nomefant' value='$fantasia' placeholder='Nome Fantasia' required>
              </div>

              <div class='mt-2 d-flex flex-column input-group col-sm-8'>
                <label class='form-label mt-2' for='razsocial'>Razão Social: <span style='color: red;'>*</span> </label>
                <input class='form-control' style='width: 100%;' type='text' name='razsocial' id='razsocial' value='$razaosocial' placeholder='Razão Social' required>
              </div>

              <!--mt-2 col-sm-12 d-flex justify-content-between m-auto-->
              <div class='row'>
                <div class='col-sm-4 d-flex flex-column input-group-sm'>
                  <label class='form-label mt-2' for='cnpj'>CNPJ: <span style='color: red;'>*</span> </label>
                  <input onblur=\"if(!validarCNPJ(this.value)){alert('CNPJ Informado é inválido'); this.value=''};\" class='form-control' type='text' name='cnpj' id='cnpj' value='$cnpj' placeholder='00.000.000/0000-00' required >
                </div>

                <div class='col-sm-4 d-flex flex-column input-group-sm'>
                  <label class='form-label mt-2' for='iestadual'>Inscrição Estadual: <span style='color: red;'>*</span> </label>
                  <input class='form-control' type='text' name='iestadual' id='iestadual' value='$iestadual' required>
                </div>

                <div class='col-sm-3 d-flex flex-column input-group-sm'>
                  <label class='form-label mt-2' for='imunicipal'>Inscrição Municipal: <span style='color: red;'>*</span> </label>
                  <input class='form-control' type='text'  name='imunicipal' id='imunicipal' value='$imunicipal' required>
                </div>
              </div>";

              //mysqli_close($conexao);
             ?>              

            <!-- div tipo e status -->
            <!-- mt-2 col-sm-12 d-flex justify-content-between m-auto -->
            <div class="row">

              <!-- div select tipo -->
              <div class="altcli pe-0 col-sm-5 mt-2">
                Tipo de Fornecedor: <span style="color: red;">*</span>
                <select name="tipo" class="mb-3 form-select" aria-label="Default select example" required>
                <?php 
                  $sql = "SELECT * FROM bdfrota.tbtipofornec ORDER BY descricao";
                  $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                  $sql1 = "SELECT tipo FROM bdfrota.tbfornecedor where idtbfornecedor = '$idtbfornecedor'";
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

                   while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                    $tipo = $row1['tipo'];//retorna o tipo 

                    //print "<option> $tipo </option>";
                   }
                  
                  while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                    $idtbtipofornec = $row['idtbtipofornec'];
                    $descricao = $row['descricao'];
                    $selected = ($tipo == $idtbtipofornec) ? "selected=\"selected\"" : null;

                    print "<option value='$idtbtipofornec' $selected> $descricao </option>
                    ";
                  }
                ?>
                </select>  
              </div>
            </div>

            <!-- mt-2 d-flex justify-content-between m-auto-->
            <div class="row">

              <?php 
                $sql = "SELECT * FROM bdfrota.tbfornecedor WHERE idtbfornecedor= '$idtbfornecedor';";
                //$sql = "SELECT fantasia, razaosocial FROM bdfrota.tbfornecedor WHERE idtbfornecedor= 1;";

                //$sql = "SELECT * FROM bdfrota.tbfornecedor WHERE idtbfornecedor= 1;";

                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
               
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $telefone = $row['telefone'];
                  $telefone2 = $row['telefone2'];
                  $email = $row['email'];
                  $contato = $row['contato'];
                  $endereco = $row['endereco'];
                  $complemento = $row['complemento'];
                  $bairro = $row['bairro'];
                  $cidade = $row['cidade'];
                  $estado = $row['estado'];
                  $cep = $row['cep'];                
                }

                print "
                <div class='col-sm-6 d-flex flex-column input-group-sm'>
                  <label class='form-label mt-2' for='telefone1'>Telefone 1: <span style='color: red;'>*</span> </label>
                  <input class='form-control' type='text' name='telefone1' id='telefone1' value='$telefone' placeholder='(00)00000-0000' required>
                </div>

                <div class='col-sm-5 d-flex flex-column input-group-sm'>
                  <label class='form-label mt-2' for='telefone2'>Telefone 2: </label>
                  <input class='form-control' type='text' name='telefone2' id='telefone2' placeholder='(00)00000-0000' value='$telefone2'>
                </div>
              </div>

            <!-- mt-2 d-flex justify-content-between m-auto -->
            <div class='row'>
              <!--div email-->
              <div class='d-flex col-sm-6 flex-column input-group-sm'>
                <label class='form-label mt-2'  for='email'>E-mail: </label>
                <input class='form-control' type='email' name='email' id='email' placeholder='usuario@email.com' value='$email'>
              </div>

              <!--div contato-->
              <div class='d-flex col-sm-5 flex-column input-group-sm'>
                <label class='form-label mt-2'  for='contato'>Contato: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='contato' id='contato' placeholder='Contato' value='$contato' required>
              </div>
            </div>

            <!-- div endereço parte 1 -->
            <!--mt-2 d-flex justify-content-between m-auto-->
            <div class='row'>
              <div class='d-flex col-sm-2 flex-column input-group-sm'>
                <label class='form-label mt-2' for='cep'>CEP: </label>
                <input class='form-control' type='text' name='cep' id='cep' placeholder='00000-000' value='$cep'>
              </div>

              <div class='d-flex col-sm-7 flex-column input-group-sm'>
                <label class='form-label mt-2' for='endereco'>Endereço: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='endereco' id='endereco' value='$endereco' placeholder='Rua A, 000' required>
              </div>

              <div class='d-flex col-sm-2 flex-column input-group-sm'>
                <label class='form-label mt-2' for='complemento'>Complemento: </label>
                <input class='form-control' type='text' name='complemento' id='complemento' placeholder='Complemento' value='$complemento'>
              </div>
            </div>

            <!--mt-2 pb-4 d-flex justify-content-between align-items-center m-auto-->
            <!-- div endereço parte 2 -->
            <div class='row border-bottom border-dark'>
              <div class='d-flex col-sm-4 flex-column input-group-sm'>
                <label class='form-label mt-2' for='bairro'>Bairro: <span style='color: red;'>*</span></label>
                <input class='form-control' type='text' name='bairro' id='bairro' placeholder='Bairro' value='$bairro' required>
              </div>

              <div class='d-flex col-sm-5 flex-column input-group-sm'>
                <label class='form-label mt-2' for='cidade'>Cidade: <span style='color: red;'>*</span> </label> 
                <input class='form-control' type='text' name='cidade' id='cidade' placeholder='Cidade' value='$cidade'  required>
              </div>

              <div class='mt-2 d-flex col-sm-2 flex-column input-group-sm form-label'>

              <label class='form-label mt-2' for='estado'>UF: <span style='color: red;'>*</span> </label> 
                <input class='form-control' type='text' name='estado' id='estado' required placeholder='UF' value='$estado'>
              </div>
            ";
              ?>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar edição </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemfornecedores.php'> Descartar edição </a>
              </div>
            </div>
          </form>
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

  <!-- jquery -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <!-- masks -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!-- fontawesome v6.1.0 - js -->
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- bootstrap 5.1.3 - js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->

  <script type="text/javascript">
    //mascaras de input
    $(document).ready(function(){
      $('#cnpj').mask('00.000.000/0000-00', {reverse: true});
      $('#iestadual').mask('00000000000000');
      $('#imunicipal').mask('00000000000000');
      $('#cep').mask('00000-000');
      $('#telefone1').mask('(00) 00000-0000');
      $('#telefone2').mask('(00) 00000-0000');

      //formatação do texto no input
      $("#nomefant").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#razsocial").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#contato").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#endereco").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#complemento").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#bairro").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#cidade").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      }); 

      $("#estado").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#endereco").val("");
        $("#bairro").val("");
        $("#cidade").val("");
        $("#estado").val("");
      }
        
      //Quando o campo cep perde o foco.
      $("#cep").blur(function() {
        //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

          //Expressão regular para validar o CEP.
          var validacep = /^[0-9]{8}$/;

          //Valida o formato do CEP.
          if(validacep.test(cep)) {

            //Preenche os campos com "..." enquanto consulta webservice.
            $("#endereco").val("...");
            $("#bairro").val("...");
            $("#cidade").val("...");
            $("#estado").val("...");

            //Consulta o webservice viacep.com.br/
            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                if (!("erro" in dados)) {
                  //Atualiza os campos com os valores da consulta.
                  $("#endereco").val(dados.logradouro);
                  $("#bairro").val(dados.bairro);
                  $("#cidade").val(dados.localidade);
                  $("#estado").val(dados.uf);
                } //end if.
                else {
                  //CEP pesquisado não foi encontrado.
                  limpa_formulário_cep();
                  alert("CEP não encontrado.");
                }
            });
          } //end if.
          else {
              //cep é inválido.
              limpa_formulário_cep();
              alert("Formato de CEP inválido.");
          }
        } //end if.
        else {
          //cep sem valor, limpa formulário.
          limpa_formulário_cep();
        }
      });
    });

    //validação do cnpj
    function validarCNPJ(cnpj) {
 
      cnpj = cnpj.replace(/[^\d]+/g,'');
   
      if(cnpj == '') return false;
       
      if (cnpj.length != 14)
          return false;
   
      // Elimina CNPJs invalidos conhecidos
      if (cnpj == "00000000000000" || 
          cnpj == "11111111111111" || 
          cnpj == "22222222222222" || 
          cnpj == "33333333333333" || 
          cnpj == "44444444444444" || 
          cnpj == "55555555555555" || 
          cnpj == "66666666666666" || 
          cnpj == "77777777777777" || 
          cnpj == "88888888888888" || 
          cnpj == "99999999999999")
          return false;
           
      // Valida DVs
      tamanho = cnpj.length - 2
      numeros = cnpj.substring(0,tamanho);
      digitos = cnpj.substring(tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
              pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(0))
          return false;
           
      tamanho = tamanho + 1;
      numeros = cnpj.substring(0,tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
              pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      if (resultado != digitos.charAt(1))
            return false;
             
      return true;
    }
  
  </script>

</body>
</html>