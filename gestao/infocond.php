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

$matriculacond = $_POST['matcond'];

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

  <!-- vendor jquery js 
  <script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Informações do Condutor </h1>

        <!-- div do formulario -->
        <div>
          <div style="width: 70%;" class="mb-3 m-auto">
            
            <!-- div identificação -->
            <div>             
            <?php 
              $sql = "SELECT nome, matricula, cargo, status,ccusto, endereco, bairro, cidade, cep, cpf, rg, codempresa, codfilial, dtnasc FROM bdaniel.tbfuncionario WHERE matricula = '$matriculacond';";

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
              $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                $nomecond = utf8_encode($row['nome']);
                //$matricula = $row['matricula'];
                $cargo = utf8_encode($row['cargo']);                
                $status = utf8_encode($row['status']);
                $ccusto = utf8_encode($row['ccusto']);
                $endereco = utf8_encode($row['endereco']);
                $bairro = utf8_encode($row['bairro']);
                $cidade = utf8_encode($row['cidade']);
                $cep = utf8_encode($row['cep']);                
                $cpf = $row['cpf'];
                $rg = $row['rg'];
                $codempresa= $row['codempresa'];
                $codfilial = $row['codfilial'];
                $dtnasc = $row['dtnasc'];



              $sql2="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial';";
              $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
              $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
                $filial = strtoupper(utf8_encode($row2['nome']));

              $sql3 = "SELECT numcnh, validade, categoria, doc1, doc2 FROM bdfrota.tbcnh WHERE matricula = '$matriculacond'; ";
              $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
              $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
                $numcnh = $row3['numcnh'];
                $validade = $row3['validade'];
                $categoria = $row3['categoria'];
                $doc1 = $row3['doc1'];
                $doc2 = $row3['doc2'];

                $validade1 = explode(" ", $validade);

              $sql4 = "SELECT email FROM bdfrota.tbusuario WHERE matricula = '$matriculacond'; ";
              $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
              $row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC);
                $emailcond = $row4['email'];

              print 
              "<input type='hidden' name='matr_autor' value='$matricula1'>
                <!--div nome e matricula-->
                <div class='col-sm-12 d-flex justify-content-start'>
                  <!-- div nome -->
                  <div class='d-flex flex-column col-sm-5'>
                    <label class='form-label mt-2 me-4' for='nome'>Nome:</label>
                    <input class='form-control'  type='text' name='nome' id='nome' value='$nomecond' readonly>
                  </div>

                  <!-- div dtnasc -->
                  <div class='ms-1 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2 me-4' for='dtnasc'>Data de Nasc.:</label>
                    <input class='form-control'  type='date' name='dtnasc' id='dtnasc' value='$dtnasc' readonly>
                  </div>

                  <!-- div matricula -->
                  <div class='ms-3 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2' for='matricula'>Matrícula: </label>
                    <input class='form-control'  type='text' name='matricula' id='matricula'  value='$matriculacond' readonly>
                  </div>

                  <!-- div status -->
                  <div class='ms-3 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2' for='status'>Status: </label>
                    <input class='form-control'  type='text' name='status' id='status'  value='$status' readonly>
                  </div>
                </div>

                <!-- div cargo filial status centro custo -->
                <div class='col-sm-12 d-flex justify-content-start'>
                  
                  <!-- cargo -->
                  <div class='d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='cargo'>Cargo:</label>
                    <input class='form-control' type='text' name='cargo' id='cargo' readonly value='$cargo'>
                  </div>

                  <!-- filial -->
                  <div class='ms-3 d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='filial'>Filial: </label>
                    <input class='form-control' type='text' name='filial' id='filial' style='width: 94%;' readonly value='$filial'>
                  </div>

                  <!-- centro custo --> 
                  <div class='ms-1 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='ccusto'>Centro de custo:</label>
                    <input class='form-control' type='text' name='ccusto' id='ccusto' value = '$ccusto' readonly>
                  </div>

                </div>
              

                <!-- div cpf rg -->
                <div class='col-sm-12 d-flex justify-content-start'>
                  
                  <!-- cpf -->
                  <div class='d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='cpf'>CPF:</label>
                    <input class='form-control' type='text' name='cpf' id='cpf' readonly value='$cpf'>
                  </div>

                  <!-- rg -->
                  <div class='ms-3 d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='rg'>RG: </label>
                    <input class='form-control' type='text' name='rg' id='rg' style='width: 94%;' readonly value='$rg'>
                  </div>

                  <!-- email -->
                  <div class='ms-3 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='email'>Email: </label>
                    <input class='form-control' type='text' name='email' id='email' readonly value='$emailcond'>
                  </div>

                </div>

                <!-- div cnh validadecnh categoriacnh -->
                <div class='col-sm-12 d-flex justify-content-start align-items-end'>
                  
                  <!-- cnh -->
                  <div class='d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='cnh'>CNH:</label>
                    <input class='form-control' type='text' name='cnh' id='cnh' readonly value='$numcnh'>
                  </div>

                  <!-- validadecnh -->
                  <div class='ms-3 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='validadecnh'>Validade CNH: </label>
                    <input class='form-control' type='date' name='validadecnh' id='validadecnh' style='width: 94%;' readonly value='$validade1[0]'>
                  </div>

                  <!-- categoriacnh --> 
                  <div class='ms-1 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2' for='categoriacnh'>Categoria CNH:</label>
                    <input class='form-control' type='text' name='categoriacnh' id='categoriacnh' value = '$categoria' readonly>
                  </div>";

                  if(!empty($doc2)){
                    $href = $doc2;
                  }else{
                    if(!empty($doc1)){
                      $href = $doc1;
                    }
                  }

                  if(!empty($href)){
                  print"
                    <div class='ms-2 d-flex flex-column col-sm-1'>
                      <a href='.$href' target='_blank' class='btn  btn-secondary' title='Baixar Último Arquivo'><span class='material-symbols-outlined'>visibility</span></a>                      
                    </div>";
                  }else{
                    print"
                    <div class='ms-2 d-flex flex-column col-sm-3'>
                      
                      <form action='./editarcnh1.php' method='post' target='_blank'>
                        <input type='hidden' name='matricula' value='$matriculacond'>
                        <button type='submit' class='btn  btn-secondary'>Editar CNH/Enviar Arquivo</button>
                      </form>                      
                    </div>";
                  }
              
              print"
                </div>

                <!--div endereço-->
                <div class='col-sm-12 d-flex justify-content-start'>
                  <!-- div endereço -->
                  <div class='d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2 me-4' for='endereco'>Endereço:</label>
                    <input class='form-control'  type='text' name='endereco' id='endereco' value='$endereco' readonly>
                  </div>

                  <!-- div bairro -->
                  <div class='ms-3 d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='bairro'>Bairro: </label>
                    <input class='form-control'  type='text' name='bairro' id='bairro'  value='$bairro' readonly>
                  </div>

                  <!-- div cidade -->
                  <div class='ms-3 d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2' for='cidade'>Cidade: </label>
                    <input class='form-control'  type='text' name='cidade' id='cidade'  value='$cidade' readonly>
                  </div>
                </div>

                <div class='mb-2 col-sm-12 d-flex justify-content-start'>
                  <!-- div CEP -->
                  <div class='d-flex flex-column col-sm-4'>
                    <label class='form-label mt-2 me-4' for='cep'>CEP</label>
                    <input class='form-control'  type='text' name='cep' id='cep' value='$cep' readonly>
                  </div>
                </div>";

            ?> 
            </div>
          </div>
        </div>
      </div>
    </main>

    <!--<footer class="mt-4 py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
      <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
              <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
              <!-<div>
                  <a href="#">Privacy Policy</a>
                                  &middot;
                  <a href="#">Terms &amp; Conditions</a>
              </div>->
          </div>
      </div>
    </footer>-->
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
    //mascaras de input
    $(document).ready(function(){
      $('#cnh').mask('00000000000', {reverse: true});
      $('#pontos').mask('000', {reverse: true});
      $('#cartao').mask('0000 0000 0000 0000');

      //formatação de placa
      $('#placa').mask('AAA0U00', {
        translation: {
          'A': {
            pattern: /[A-Za-z]/
          },
          'U': {
            pattern: /[A-Za-z0-9]/
          },
        },
        onKeyPress: function (value, e, field, options) {
          // Convert to uppercase
          e.currentTarget.value = value.toUpperCase();

          // Get only valid characters
          let val = value.replace(/[^\w]/g, '');

          // Detect plate format
          let isNumeric = !isNaN(parseFloat(val[4])) && isFinite(val[4]);
          let mask = 'AAA 0U00';
          if(val.length > 4 && isNumeric) {
            mask = 'AAA-0000';
          }
          $(field).mask(mask, options);
        }
      });

      //formatação do texto no input
      $("#nome").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });
    });
    
  </script>

</body>
</html>