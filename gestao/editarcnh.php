<?php date_default_timezone_set('America/Sao_Paulo'); // Defina o tempo de vida da sessão para 1 hora 
  $lifetime=3600;
  // 1 hora em segundos // Configurar os parâmetros do cookie da sessão 
  session_set_cookie_params($lifetime); // Iniciar a sessão 
  session_start(); $nome1=$_SESSION['nome']; $usuariof=$_SESSION['usuario'];
  $matricula1=$_SESSION['matricula']; $perfil=$_SESSION['perfil']; $tipo=$_SESSION['tipo']; if(empty($perfil)){
  $perfil=$_POST['perfil']; $matricula1=$_POST['matr_autor']; } if(empty($matricula1)){
  $matricula1=$_POST['matr_autor']; } /*if ($perfil==null){ echo"<script language='javascript' type='text/javascript'>
  alert('Você deve logar para ter acesso');window.close();</script>";

  }elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"
  <script language='javascript'
    type='text/javascript'>alert('Você não tem permissão'); window.location =\"../index.php\"</script>";
  }elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 ==
  '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' ||
  $matricula1 == '003931'){

  echo"
  <script language='javascript' type='text/javascript'>alert('Você não tem permissão'); window.close();</script>";

  //*window.location=\"../index.php\"
  }*/


  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']= $tipo;

  $matcond = $_POST['matricula'];


  include"../conecta03.php";
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
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />

    <title> Editar CNH </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />

    <!-- estilo local -->
    <style type="text/css">
      @media screen and (min-width: 992px) {
        .row {
          --bs-gutter-x: 0rem;
          flex-wrap: nowrap;
        }
      }
    </style>

  </head>

  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 100%;">
          <h1 class="h1 pt-2 pb-2 m-auto text-center"> Editar CNH </h1>

          <!-- div do formulario action="./control/editarcnh.php" -->
          <div>
            <form method="post" action="https://ffa.painel-telecom.com:4545/frotas/gestao/control/editarcnh.php"
              style="width: 70%;" class="pb-5 mb-5 m-auto" enctype="multipart/form-data">

              <!-- div identificação -->
              <!--mt-2 col-sm-12 d-flex justify-content-start-->
              <div class="row">

                <?php
                //$sql = "SELECT a.nome, a.matricula, b.numcnh, b.validade, b.uf, b.categoria, b.matricula, b.pontos, b.consulta, b.suspensa  FROM bdfrota.tbcondutor a, bdfrota.tbcnh b WHERE a.matricula = '$matricula' AND a.matricula = b.matricula;";
                
                //$sql = "SELECT a.nome AS nome, a.matricula AS matricula, b.numcnh AS numcnh, b.validade AS validade, b.uf AS uf, b.categoria AS categoria, b.matricula, b.pontos AS pontos, b.consulta AS consulta, b.suspensa AS suspensa, b.doc AS doc  FROM bdaniel.tbfuncionario a, bdfrota.tbcnh b WHERE a.matricula = '$matricula' AND a.matricula = b.matricula;";
                $sql = "SELECT tc.*, tf.codempresa FROM bdfrota.tbcnh tc join bdaniel.tbfuncionario tf on tc.matricula = tf.matricula WHERE tc.matricula='".$matcond."' ; ";

                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                $id = $row['idtbcnh'];

                $matricula = $row['matricula'];
                $numcnh = $row['numcnh'];
                $validade = $row['validade']; //date('Y-m-d');
                $uf = $row['uf'];
                $categoria = $row['categoria'];
                $pontos = $row['pontos'];
                $consulta = $row['consulta']; //date('Y-m-d');
                $suspensa = $row['suspensa'];
                $doc1 = $row['doc1'];
                $doc2 = $row['doc2'];
                $codempresa = $row['codempresa'];

                $consulta1 = explode(" ", $consulta);
                $consulta1 = $consulta1[0];

                $validade1 = explode(" ", $validade);
                $validade1 = $validade1[0];

                $sql2 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matcond'; ";
                $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                $nome = utf8_encode($row2['nome']);

                if (!empty($doc2)) {
                  $docfind = substr($doc2, 10);
                } else {
                  if (!empty($doc1)) {
                    $docfind = substr($doc1, 10);
                  }
                }

                $dirin = 'docs/cnh/';

                $diretorio = dir($dirin);

                while ($arquivo = $diretorio->read()) {
                  if ($arquivo != '.' && $arquivo != '..') {
                    //print "$arquivo</br>";
                
                    //if(strpos($arquivo, $docfind) !== false){
                    if ($arquivo == $docfind) {
                      $href = "./docs/cnh/$docfind";
                      //print"$arquivo";
                      break;
                    } else {
                      $href = "http://ffasip.ddns.net:4242/frotas/gestao/docs/cnh/$docfind";

                      //print"$arquivo, $docfind";
                    }

                  }
                }

                $diretorio->close();

                print "
              <input type='hidden' name='matr_autor' value='$matricula1'>
              <input type='hidden' name='id' value='$id'>


              <!-- div nome -->
              <div class='d-flex flex-column col-sm-7'>
                <label class='form-label mt-2' for='nome'>Nome: <span style='color: red;'>*</span></label>
                <input class='form-control' style='width: 100%;' type='text' name='nome' id='nome' placeholder='Nome' value='$nome' required>
              </div>

              <!-- div matricula -->
              <div class='ms-3 d-flex flex-column col-sm-3'>
                <label class='form-label mt-2' for='matricula'>Matricula: <span style='color: red;'>*</span> </label>
                <input class='form-control' style='width: 100%;' type='text' name='matricula' id='matricula'  value='$matcond'  placeholder='Matrícula' required>
              </div>
            </div>

            <!-- div infos cnh -->
            <!--mt-2 col-sm-12 d-flex justify-content-start-->
            <div class='row'>
              <!-- div cnh-->
              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label mt-2' for='cnh'>Número da CNH: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text' name='cnh' id='cnh' required placeholder='000000000000'  value='$numcnh'>
              </div>

              <!-- div validade-->
              <div class='ms-3 col-sm-3 d-flex flex-column'>
                <label class='form-label mt-2' for='validade'>Validade: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='date' name='validade' id='validade' required value='$validade1'>
              </div>";
                ?>


                <!-- div UF-->
                <div class='ms-3 col-sm-1 d-flex flex-column'>
                  <p>U.F.: <span style='color: red;'>*</span></p>
                  <select class='form-select' name='uf' value='$uf' required>
                    <option value='AC' <?= ($uf == 'AC') ? 'selected' : '' ?>>AC</option>
                    <option value='AL' <?= ($uf == 'AL') ? 'selected' : '' ?>>AL</option>
                    <option value='AP' <?= ($uf == 'AP') ? 'selected' : '' ?>>AP</option>
                    <option value='AM' <?= ($uf == 'AM') ? 'selected' : '' ?>>AM</option>
                    <option value='BA' <?= ($uf == 'BA') ? 'selected' : '' ?>>BA</option>
                    <option value='CE' <?= ($uf == 'CE') ? 'selected' : '' ?>>CE</option>
                    <option value='DF' <?= ($uf == 'DF') ? 'selected' : '' ?>>DF</option>
                    <option value='ES' <?= ($uf == 'ES') ? 'selected' : '' ?>>ES</option>
                    <option value='GO' <?= ($uf == 'GO') ? 'selected' : '' ?>>GO</option>
                    <option value='MA' <?= ($uf == 'MA') ? 'selected' : '' ?>>MA</option>
                    <option value='MT' <?= ($uf == 'MT') ? 'selected' : '' ?>>MT</option>
                    <option value='MS' <?= ($uf == 'MS') ? 'selected' : '' ?>>MS</option>
                    <option value='MG' <?= ($uf == 'MG') ? 'selected' : '' ?>>MG</option>
                    <option value='PA' <?= ($uf == 'PA') ? 'selected' : '' ?>>PA</option>
                    <option value='PB' <?= ($uf == 'PB') ? 'selected' : '' ?>>PB</option>
                    <option value='PR' <?= ($uf == 'PR') ? 'selected' : '' ?>>PR</option>
                    <option value='PE' <?= ($uf == 'PE') ? 'selected' : '' ?>>PE</option>
                    <option value='PI' <?= ($uf == 'PI') ? 'selected' : '' ?>>PI</option>
                    <option value='RJ' <?= ($uf == 'RJ') ? 'selected' : '' ?>>RJ</option>
                    <option value='RN' <?= ($uf == 'RN') ? 'selected' : '' ?>>RN</option>
                    <option value='RS' <?= ($uf == 'RS') ? 'selected' : '' ?>>RS</option>
                    <option value='RR' <?= ($uf == 'RO') ? 'selected' : '' ?>>RO</option>
                    <option value='RR' <?= ($uf == 'RR') ? 'selected' : '' ?>>RR</option>
                    <option value='SC' <?= ($uf == 'SC') ? 'selected' : '' ?>>SC</option>
                    <option value='SP' <?= ($uf == 'SP') ? 'selected' : '' ?>>SP</option>
                    <option value='SE' <?= ($uf == 'SE') ? 'selected' : '' ?>>SE</option>
                    <option value='TO' <?= ($uf == 'TO') ? 'selected' : '' ?>>TO</option>
                  </select>
                </div>


                <?php
                print "
              <!-- div categoria-->
              <div class='ms-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mt-2' for='categoria'>Categoria: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='text'  name='categoria' id='categoria' required value='$categoria'>
              </div>
            </div>

            <!-- div infos cnh -->
            <!--pb-2 mt-2 col-sm-12 d-flex justify-content-start-->
            <div class='row'>
              <!-- div pontos-->
              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label mt-2' for='pontos'>Pontos: </label>
                <input class='form-control' type='text' onKeyPress='if(this.value.length==3)'  name='pontos' id='pontos' value='$pontos'>
              </div>

              <!-- div consulta-->
              <div class='ms-3 col-sm-3 d-flex flex-column'>
                <label class='form-label mt-2' for='consulta'>Consulta ao DETRAN: <span style='color: red;'>*</span> </label>
                <input class='form-control' type='date'  name='consulta' id='consulta' required value='$consulta1'>
              </div>
              ";
                ?>
                <!-- div suspensa -->
                <div class='ms-3 mt-2 col-sm-3 d-flex'>
                  <!-- div select suspensa -->
                  <div class='altcli pe-0 col-sm-12 mt-2'>
                    Suspensa: <span style='color: red;'>*</span>
                    <select name='suspensa' class='mb-3 form-select' aria-label='Default select example' required>
                      <option value='0' <?= ($suspensa == '0') ? 'selected' : '' ?>> Não </option>
                      <option value='1' <?= ($suspensa == '1') ? 'selected' : '' ?>> Sim </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-start mb-4 align-items-end">
                <div class='mt-2 col-sm-12 d-flex'>
                  <div class='altcli pe-0 col-sm-10 mt-2'>
                    Empresa Emissora do Cartão de Combustível: <span style='color: red;'>*</span>
                    <select name='codempresa' class='mb-3 form-select' aria-label='Default select example' required>
                      <option value='1' <?= ($codempresa == '1') ? 'selected' : '' ?>> Ticketlog </option>
                      <option value='2' <?= ($codempresa == '2') ? 'selected' : '' ?>> Valecard </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-start mb-4 align-items-end">
                <!-- div anexararquivo-->
                <div class='flex-colum col-sm-6'>
                  <label class='form-label' for='arquivo'>Anexar habilitação: </label>
                  <input class='form-control' type='file' name='arquivo' id='arquivo'
                    accept=".jpg, .png, .gif, .pdf, .jpeg">
                </div>

                <?php
                /*if(!empty($doc2)){
                  $href = $doc2;
                  $href = 'https://ffa.painel-telecom.com:4545/frotas/gestao'.$doc2;

                }else{
                  if(!empty($doc1)){
                    //$href = $doc1;
                    $href = 'https://ffa.painel-telecom.com:4545/frotas/gestao'.$doc1;

                  }
                }*/

                if (!empty($docfind)) {
                  print "
                <div class='ms-3 flex-colum col-sm-1'>
                  <a href='$href' target='_blank' class='btn  btn-secondary' title='Baixar Último Arquivo' download><span class='material-symbols-outlined'>download</span></a>

                  <!--Último Documento Anexado-->
                  <!--<p>O documento anexado anterioremente não será mais listado.</p>-->
                </div>";
                }

                //excluir último arquivo de cnh
                if (!empty($docfind)) {//target='_blank'
                  print "
                <div class='flex-colum col-sm-4'>
                  <a href='./apagararqcnh.php?id=$id&mat_autor=$matricula1'  class='btn  btn-secondary' title='Apagar Último Arquivo'><span class='material-symbols-outlined'>delete_forever</span></a>

                  <!--Último Documento Anexado-->
                  <!--<p>O documento anexado anterioremente não será mais listado.</p>-->
                </div>";
                }


                ?>
              </div>


              <!-- div texto campos obrigatório -->
              <div class="border-top border-dark" style="color: red;" class="pt-2">
                * Campos obrigatórios.
              </div>

              <!-- div botão -->
              <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
                <div>
                  <button class="btn btn-success" type='submit'> Confirmar edição </button>
                </div>

                <div class="ms-5">
                  <!--<a class="btn btn-danger" href='./listagemgeralcondutores.php'> Cancelar edição </a>-->

                  <button class="btn btn-danger" onclick="window.close();"> Cancelar edição </button>
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

    <!-- jquery e masks -->
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'
      integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

    <script type="text/javascript"
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <!-- fontawesome - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
      crossorigin="anonymous"></script>

    <!-- script próprio -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script>
      //mascaras de input
      $(document).ready(function () {
        $('#cnh').mask('00000000000', { reverse: true });
        $('#pontos').mask('000', { reverse: true });
      });
    </script>

  </body>

  </html>