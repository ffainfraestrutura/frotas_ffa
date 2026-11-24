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
$tipo= $_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

include"../conecta2.php";
//include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");


$idtbcondutor = $_POST['idtbcondutor'];
$matcondutor = $_POST['matcondutor'];
$matr_autor = $_POST['matr_autor'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Enviar Documentos </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  
  <link href="../src/css/styles.css" rel="stylesheet" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> 

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js 
  <script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>

  <-- jquery e masks 
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <script src="../src/js/scripts.js"></script>-->

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 ms-3"> Enviar Documentos </h1>

        <!-- div do formulario https://ffa.painel-telecom.com:4545/frotas/gestao-->
        <div style="width: 80%;">
          <form method="post" action="./control/enviardocscond.php" enctype="multipart/form-data" style="width: 90%;" class="pb-5 mb-5 m-auto">

            <!-- div identificação -->
            <div class="mt-2 col-sm-12 d-flex justify-content-start">

              <!-- div nome -->
              <?php 
              $sql = "SELECT nome, matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matcondutor';";
              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
              $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
                $nome = utf8_encode($row['nome']);
                $matricula = $row['matricula'];

              $sql1="SELECT * FROM bdfrota.tbcnh WHERE matricula = '$matcondutor';";
              $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
              $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                $numcnh = utf8_encode($row1['numcnh']);
                $validade = $row1['validade'];
                $uf = $row1['uf'];
                $categoria = $row1['categoria'];
                $pontos = $row1['pontos'];
                $consulta = $row1['consulta'];
                $suspensa = $row1['suspensa'];
                $politicauso = $row1['politicauso'];
                $termocombust = $row1['termocombust'];
                $contratoagregamento = $row1['contratoagregamento'];
                $termorescisao = $row1['termorescisao'];

                $validade1 = explode(" ", $validade);
                $consulta1 = explode(" ", $consulta);               


                print "<input type='hidden' name='matr_autor' value='$matricula1'>
                <input type='hidden' name='matcond' value='$matcondutor'>
                <input type='hidden' name='mat_autor' value='$matricula1'>


                <div class='d-flex flex-column col-sm-7'>
                  <label class='form-label mt-2' for='nome'>Nome:</label>
                  <input class='form-control' style='width: 100%;' type='text' name='nome' id='nome' placeholder='Nome' value='$nome' readonly>
                </div>

                <!-- div matricula -->
                <div class='ms-3 d-flex flex-column col-sm-3'>
                  <label class='form-label mt-2' for='matricula'>Matricula:</label>
                  <input class='form-control' style='width: 100%;' type='text' name='matricula' id='matricula'  value='$matricula'  placeholder='Matrícula' maxlength='6' readonly>
                </div>";
              ?>            
            </div>

            <!-- div infos cnh -->
            <div class="mt-2 col-sm-12 d-flex justify-content-start">
              <!-- div cnh-->
              <div class="col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="cnh">Número da CNH: </label>
                <input class="form-control" type="text" name="cnh" id='cnh' <?php print "value='$numcnh'"?> readonly>
              </div>

              <!-- div validade-->
              <div class="ms-3 col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="validade">Validade:</label>
                <input class="form-control" type="date" name="validade" id="validade" <?php print "value='$validade1[0]'"?> readonly>
              </div>

              <!-- div UF-->
              <div class="ms-3 col-sm-1 d-flex flex-column">
               
                <p>U.F.: <span style="color: red;">*</span></p>
                <select class="form-select" name="uf" disabled>
                  <option value="AC" <?=($uf == 'AC')?'selected':''?>>AC</option> 
                  <option value="AL" <?=($uf == 'AL')?'selected':''?>>AL</option>
                  <option value="AP" <?=($uf == 'AP')?'selected':''?>>AP</option> 
                  <option value="AM" <?=($uf == 'AM')?'selected':''?>>AM</option>  
                  <option value="BA" <?=($uf == 'BA')?'selected':''?>>BA</option> 
                  <option value="CE" <?=($uf == 'CE')?'selected':''?>>CE</option> 
                  <option value="DF" <?=($uf == 'DF')?'selected':''?>>DF</option> 
                  <option value="ES" <?=($uf == 'ES')?'selected':''?>>ES</option> 
                  <option value="GO" <?=($uf == 'GO')?'selected':''?>>GO</option> 
                  <option value="MA" <?=($uf == 'MA')?'selected':''?>>MA</option> 
                  <option value="MT" <?=($uf == 'MT')?'selected':''?>>MT</option> 
                  <option value="MS" <?=($uf == 'MS')?'selected':''?>>MS</option> 
                  <option value="MG" <?=($uf == 'MG')?'selected':''?>>MG</option> 
                  <option value="PA" <?=($uf == 'PA')?'selected':''?>>PA</option> 
                  <option value="PB" <?=($uf == 'PB')?'selected':''?>>PB</option> 
                  <option value="PR" <?=($uf == 'PR')?'selected':''?>>PA</option> 
                  <option value="PE" <?=($uf == 'PE')?'selected':''?>>PE</option> 
                  <option value="PI" <?=($uf == 'PI')?'selected':''?>>PI</option> 
                  <option value="RJ" <?=($uf == 'RJ')?'selected':''?>>RJ</option> 
                  <option value="RN" <?=($uf == 'RN')?'selected':''?>>RN</option>
                  <option value="RS" <?=($uf == 'RS')?'selected':''?>>RS</option> 
                  <option value="RO" <?=($uf == 'RO')?'selected':''?>>RO</option> 
                  <option value="RR" <?=($uf == 'RR')?'selected':''?>>RR</option> 
                  <option value="SC" <?=($uf == 'SC')?'selected':''?>>SC</option> 
                  <option value="SP" <?=($uf == 'SP')?'selected':''?>>SP</option>
                  <option value="SE" <?=($uf == 'SE')?'selected':''?>>SE</option>  
                  <option value="TO" <?=($uf == 'TO')?'selected':''?>>TO</option> 
                </select>
              </div>

              <!-- div categoria-->
              <div class="ms-3 col-sm-2 d-flex flex-column">
                <label class="form-label mt-2" for="categoria">Categoria: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text"  name="categoria" id="categoria" <?php print "value='$categoria'"; ?> readonly>
              </div>

            </div>

            <!-- div infos cnh -->
            <div class="pb-2 mt-2 col-sm-12 d-flex justify-content-start">
              <!-- div pontos-->
              <div class="col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="pontos">Pontos: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text" onKeyPress="if(this.value.length==3)"  name="pontos" id="pontos" <?php print "value='$pontos'"; ?> readonly>
              </div>

              <!-- div consulta-->
              <div class="ms-3 col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="consulta">Consulta ao DETRAN: <span style="color: red;">*</span> </label>
                <input class="form-control" type="date"  name="consulta" id="consulta" <?php print "value='$consulta1[0]'"; ?> readonly>
              </div>

              <!-- div suspensa -->
              <div class="ms-3 mt-2 col-sm-3 d-flex">
                <!-- div select suspensa -->
                <div class="altcli pe-0 col-sm-5 mt-2">
                  Suspensa: <span style="color: red;">*</span>
                  <select name="suspensa" class="mb-3 form-select" aria-label="Default select example" disabled>
                    <option value='0' <?=($suspensa == '0')?'selected':''?>> Não </option>
                    <option value='1' <?=($suspensa == '1')?'selected':''?>> Sim </option>
                  </select>  
                </div>
              </div>
            </div>

            <!-- docs politica de uso -->
            <div class="pb-2 mt-2 col-sm-12 d-flex justify-content-start align-items-end">
              
              <div class="mt-2 col-sm-3">
                <a class="btn btn-primary" target="_blank" <?php print "href='./fpdf/politicauso.php?matcond=$matcondutor' " ?> >Política de Uso Imprimir</a>
              </div>

              
              <div class="ms-3 col-sm-7">
                <label class="form-label" for='politicauso'>Enviar Política de Uso:<span style="color: red;">*</span></label>
                <input class="form-control" type="file" name="politicauso" id='politicauso' required accept=".pdf, .doc, .docx">
              </div>

              <?php if(!empty($politicauso)){/* print"
              <div class='ms-3 col-sm-3'>
                
                <a href='https://ffa.painel-telecom.com:4545/frotas/gestao$politicauso' class='btn btn-secondary' target='_blank' download>Último Documento</a>
              </div>";*/}
              ?>
            </div>

            <!-- docs responsibilidade por infração de transito -->
            <div class="pb-2 mt-2 col-sm-12 d-flex justify-content-start align-items-center">
              <!-- div  
              <div class="ms-3 mt-2 col-sm-3">
                <a class="btn btn-primary" target="_blank" <?php print "href='./fpdf/respinfracaomovida.php?matcond=$matcondutor' " ?> >Termo de Responsabilidade Infração - Imprimir</a>
              </div> -->

              <!-- div enviar
              <div class="ms-3 col-sm-7">
                <label class="form-label" for='termoinfra'>Enviar Termo de Responsabilidade Infração:</label>
                <input class="form-control" type="file" name="termoinfra" id='termoinfra'>
              </div> -->
            </div>

            <!-- docs responsibilidade combustivel -->
            <div class="pb-2 mt-2 col-sm-12 d-flex justify-content-start align-items-end">
              
              <div class="mt-2 col-sm-3">
                <a class="btn btn-primary" target="_blank" <?php print "href='./fpdf/termocombustivel.php?matcond=$matcondutor' " ?> >Termo de Responsabilidade Combustível - Imprimir</a>
              </div>

              
              <div class="ms-3 col-sm-7">
                <label class="form-label" for='termocombustivel'>Enviar Termo de Responsabilidade Combustível:<span style="color: red;">*</span></label>
                <input class="form-control" type="file" name="termocombustivel" id='termocombustivel' required accept=".pdf, .doc, .docx">
              </div>

              <?php if(!empty($termocombust)){ /*print"
              <div class='ms-3 col-sm-3'>                
                <a href='https://ffa.painel-telecom.com:4545/frotas/gestao$termocombust' class='btn btn-secondary' target='_blank' download>Último Documento</a>
              </div>";*/}
              ?>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2">
              * Campos obrigatórios.
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-start">
              <div>
                <input class="btn btn-success" type='submit' value='Confirmar cadastro'>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemveiculos.php'> Cancelar cadastro </a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  
  <!--<script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    //mascaras de input
    $(document).ready(function(){ 
    }); 
  </script>

</body>
</html>