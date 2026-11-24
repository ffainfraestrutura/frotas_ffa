<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if(empty($perfil)){
  $perfil = $_POST['perfil_autor'];
}

if(empty($matricula1)){
  $matricula1 = $_POST['mat_autor'];
}

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($perfil<>'4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$placaveic = $_POST['placa'];
//$placaveic = 'PZL5I97';

if(!empty($placaveic)){
  if(strpos($placaveic, "-") !== false){
    $placacorreta = str_replace("-", "", $placaveic);
  } else {
    $placacorreta = str_replace(" ", "", $placaveic);
  }
}

$cpf1 = $_POST['cpf1'];

$data = date('Y-m-d');

include"../conecta03.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

/*if(file_exists("../../assinaturas/docs/colass/$matricula1.png") == false){
  print "
  <script language='javascript' type='text/javascript'> 
    window.alert('Você não possui assinatura cadastrada. Por favor, procure o DP.');
    window.location=\"./checklistinicio.php\";    
  </script>";
}*/

//$sqla="SELECT ds_placa FROM `bdfrota-teste`.tbcarrosxcolaborador WHERE ds_placa ='".$placacorreta."'; ";

$sqla= "SELECT placa FROM `bdfrota-teste`.tbveiculo WHERE placa ='".$placacorreta."'; ";
      $resultadoa= mysqli_query($con, $sqla);
      $linha = mysqli_num_rows($resultadoa);
      if($linha == 0){
        echo"<script language='javascript' type='text/javascript'>alert('Placa não encontrada no sistema.');window.location=\"./checklistinicio-manual.php\"</script>";
      }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Checklist Manual </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  
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

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  

</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "./menu.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-sm-12">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Vistoria do Veículo </h1>

        <!-- div form-->
        <div>
          <?php
            //pegando informações da última vistoria
            $sql1="SELECT max(idtbvistoria) FROM `bdfrota-teste`.tbvistoria where placa='$placacorreta'";
            $resultado1=mysqli_query($con, $sql1) or die(mysqli_error($con));
            $row1=mysqli_fetch_array($resultado1, MYSQLI_BOTH);
              $idtbvistoria=$row1[0];

            $sql2="SELECT * FROM `bdfrota-teste`.tbvistoriafotos where idtbvistoria='$idtbvistoria'"; 
            $resultado2=mysqli_query($con, $sql2) or die(mysqli_error($con));
            
              $linhas = mysqli_num_rows($resultado2);

              if($linhas <= 0){
                print"
                <form method='post' action='./fpdf/relatorio.php' target='_blank'>
                <input type='hidden' name='idtbvistoria' value='$idtbvistoria'>
                <button type='submit' class='btn btn-success'> Última Vistoria </button>

                </form>
                ";
                
                //$link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatorio.php';                
              } else {

                $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
                  $extra1 = $row2['extra1'];
                  $extra2 = $row2['extra2'];
                  $extra3 = $row2['extra3'];
                  $extra4 = $row2['extra4'];


                if(strpos($extra1,"pdf") !==false){
                  print"
                  <a href='.$extra1' target='_blank' class='btn btn-success'> Última Vistoria </a>";

                  if(!empty($extra2)){
                    print"
                  <a href='.$extra2' target='_blank' class='btn btn-success'> Imagem 1 </a>";
                  }

                  if(!empty($extra3)){
                    print"
                  <a href='.$extra3' target='_blank' class='btn btn-success'> Imagem 2 </a>";
                  }

                  if(!empty($extra4)){
                    print"
                  <a href='.$extra4' target='_blank' class='btn btn-success'> Imagem 3 </a>";
                  }
                }else{
                  print"
                <form method='post' action='./fpdf/relatoriocomfotos.php' target='_blank'>
                <input type='hidden' name='idtbvistoria' value='$idtbvistoria'>
                <button type='submit' class='btn btn-success'> $extra1 Última Vistoria </button>
                </form>";
                }               
                
                //$link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatoriocomfotos.php';
              } 
              
          ?>
        </div>

         
        <?php
          //selecionando se tem condutor        
          $sql="SELECT matcond, anofabr, modelo FROM `bdfrota-teste`.tbveiculo WHERE placa='$placacorreta'; ";        
          $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
          $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
            $matrcond = $row['matcond'];
            $anofabr = $row['anofabr'];
            $idmodelo = $row['modelo'];

          $sql1="SELECT nome, cpf, ccusto, codfilial, codempresa FROM bdaniel.tbfuncionario WHERE matricula='$matrcond'; ";
          $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
          $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
            $condutor = utf8_encode($row1['nome']);          
            $cpf = $row1['cpf'];
            $ccusto = utf8_encode($row1['ccusto']);
            $codfilial= $row1['codfilial'];
            $codempresa=$row1['codempresa'];

          $sql2="SELECT numcnh, validade, categoria FROM `bdfrota-teste`.tbcnh WHERE matricula ='$matrcond'; ";
          $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
          $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
            $cnh =$row2['numcnh'];
            $validade = $row2['vencimento'];
            $categoria = $row2['categoria'];

          $sql3 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='$idmodelo'; ";
          $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
          $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
            $modelo =$row3['modelo'];

          $validade1=explode(" ", $validade);
          

          //print $nome, $matricula, $id_colaborador, $cpf, $numcnh, $categoria, $validade;
          if($cpf != ''){
            $cpf2 = $cpf;
          }

          if($cpf1=='' && $cpf==''){
            $d = 'd-none';
          }elseif($cpf2<>''){
            $d = 'd-none';
          }else{
            $dd = 'd-none';
            
          }
          //print $idtbvistoria;

          if(!empty($matrcond)){
            
            //print"com matricula";
            include "checlistcommatriculamanual.php";          
            
          }else{
            
            //print"sem matricula";
            include "checlistsemmatriculamanual.php";
            
          }
        
        ?>

        <?php print "

        <!--div informações do veiculo-->
        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Informações do veículo </h6>

          <!--div placa --> 
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='placa'> Placa:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='text' name='placa' id='placa' class='form-control' placeholder='Placa do Veículo' required value='$placacorreta'>
            </div>
          </div>

          <!--div modelo -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='modelo'> Modelo:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='text' name='modelo' id='modelo' class='form-control' placeholder='Modelo do Veículo' required value='$modelo'>
            </div>
          </div>

          <!--div anofabricacao -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='anofabricacao'> Ano - fabricação:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='text' name='anofabricacao' id='anofabricacao' class='form-control' placeholder='Ano de fabricação' required value='$anofabr'>
            </div>
          </div>
        </div>";?>

        <!--div arquivos da vistoria-->
        <div class='mt-4 border-top border-dark pt-2'>
            <h6> Arquivos da vistoria </h6>

            <!--div pdf-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='pdfvist'> Vistoria Manual: <span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <input type='file' name='pdfvist' id='pdfvist' class='form-control' accept='.pdf' required>
              </div>
            </div>

            <!--div imagem1-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='imagem1'> Imagem 1: </label>
              <div class='col-6'>
                <input type='file' name='imagem1' id='imagem1' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
              </div>
            </div>

            <!--div imagem2-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='imagem2'> Imagem 2: </label>
              <div class='col-6'>
                <input type='file' name='imagem2' id='imagem2' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
              </div>
            </div>

            <!--div imagem3-->
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='imagem3'> Imagem 3: </label>
              <div class='col-6'>
                <input type='file' name='imagem3' id='imagem3' class='form-control' accept='.jpg, .jpeg, .png, .pdf'>
              </div>
            </div>              
          </div>

        <!-- div informações checklist-->
        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Informações da vistoria </h6>
          
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='tipo'> Tipo:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <select name="tipo" id='tipo' class="form-select" required>
                  <option value=''> Selecione a opção </option>
              <?php
                $sql="SELECT * FROM bdfrota.tbatipovist /*WHERE idtbatipovist<>'1'*/;";
                $resultado=mysqli_query($con, $sql) or die(mysqli_error($con));
                while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                  $idtbatipovist=$row['idtbatipovist'];
                  $tipo=utf8_encode($row['tipo']);

                  print"
                  <option value='$idtbatipovist'> $tipo </option>";
                }
              ?>
              </select>
            </div>
          </div>          

          <!--div vistoriador -->
          <?php
            $sql = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matricula1';";

            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $nomevist = utf8_encode($row['nome']);                
            }

            print "
            <div class='row mt-2'>
            <label class='col-5 col-form-label' for='vistoriador'> Vistoriador:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='hidden' name='matrvistoriador' value='$matricula1'>
              <input type='text' name='vistoriador' id='vistoriador' class='form-control' placeholder='Vistoriador' value='$nomevist' required>
            </div>
          </div>
            ";
          ?>
          
          <!--div data da vistoria -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='datavistoria'> Data da Vistoria:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='date' name='datavistoria' id='datavistoria' class='form-control' required>
              <!--<input type='text' name='datavistoria' id='datavistoria' class='form-control' required>-->
            </div>
          </div>

          <!--div hora da vistoria -->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='datavistoria'> Hora da Vistoria:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <input type='time' name='horavistoria' id='horavistoria' class='form-control' required>
            </div>
          </div>

          <!--div tipo-->
          <div class='row mt-2'>
            <label class='col-5 col-form-label' for='tipo'>Status:<span style='color: red;'>*</span> </label>
            <div class='col-6'>
              <select name="statusveic" id='statusveic' class="form-select" >
                  <option value=''> Selecione a opção </option>
                <?php
                  $sql3="SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel in ('1','2','5','18','36', '32')"; //idtbastatusvel, status
                  $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                  while($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
                    $idtbastatusvel = $row3['idtbastatusvel'];
                    $statusveic = utf8_encode($row3['status']);

                    $selected = ($statusvel == $idtbastatusvel) ? "selected=\"selected\"" : null;
                    
                    print"<option value='$idtbastatusvel' $selected> $statusveic </option>";
                  }

                ?>

              
              </select>
            </div>
          </div>

          <!--div estado geral -->
          <div class='column mt-2 align-items-center'>
            <label class='col-form-label' for='estado'> Estado Geral do Veículo:<span style='color: red;'>*</span> </label>
            
            <div class='col-12' style= 'flex-wrap: wrap;'>
              <input type="radio" id="pessimo" name="estado" value="pessimo" required>
              <label for="pessimo">Péssimo</label>
              <input class="ms-1" type="radio" id="ruim" name="estado" value="ruim">
              <label for="ruim">Ruim</label>
              <input class="ms-1" type="radio" id="regular" name="estado" value="regular">
              <label for="regular">Regular</label>
              <input class="ms-1" type="radio" id="bom" name="estado" value="bom">
              <label for="bom">Bom</label>
              <input class="ms-1" type="radio" id="otimo" name="estado" value="otimo">
              <label for="otimo">Ótimo</label>
            </div>
          </div>

          <!--div avaria -->
          <div class='row mt-2 align-items-center'>
            <label class='col-form-label col-6' for='avaria'> Possui avaria? <span style='color: red;'>*</span> </label>                
            <div class='col-6'>
              <input type="radio" id="sim" name="avaria" value="1" required>
              <label for="sim">Sim</label>
              <input class="ms-1" type="radio" id="nao" name="avaria" value="0">
              <label for="nao">Não</label>
            </div>

            <!--div observaçao -->
            <div class='mt-2 align-items-center'>
              <label class='col-form-label' for='observacao'>Observação:</label> 
              <textarea class="form-control" id="observacao" name="observacao" placeholder="Observação" rows="5"></textarea>        
            </div>                        
          </div>

          <!-- div campos obrigatórios-->
          <div class='mt-4 col-12'>
            <p style='color: red;'> * Campos obrigatórios. </p>
          </div>          

          <!-- div botão-->
          <div class='mt-5 mb-5 col-12 text-center'>
            <button class='col-4 btn btn-success' type='submit'> Enviar </button>
          </div>
          
      </form>      

      </div>

      </div>
    </main>

    <!--<footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
            <--<div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>--
        </div>
      </div>
    </footer>-->
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      /*$('#datavistoria').mask("00/00/0000");
      $('#validadecnh').mask("00/00/0000");
      $('#validadecnh2').mask("00/00/0000");*/

      //$(".data").mask("99/99/9999",{placeholder:" "});      

      $('#cnh').mask('00000000000', {reverse: true});
      //$('#cpf').mask('000.000.000-00', {reverse: true});

      $('#hodometro').mask('000000000');
      $('#niveltanque').mask('0/0');

      //$('#validadecnh').mask('0000-00-00');
      

      //formatação do texto no input
      $("#categoriacnh").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      /*$("#unidade").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });*/


      //mascara de placa
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


      // quando for alterado
      /*$("#cpf").change(function() {      
        $("#nome").val()="";
        $("#cnh").val()="";
        $("#matricula").val()="";
        $("#categoriacnh").val()="";
        $("#validadecnh").val()="";
        $("#centrocusto").val()="";        
      });*/

      $('#tipo').change(function() {        
        if($("#tipo").val() == '2'){

          $("#statusveic").val('1');
          $("#statusveic option[value='1']").show();
          
          $("#statusveic option[value!='1']").hide();

            
        } else if($("#tipo").val() == '3'){
          
          $("#statusveic").val('2');
          $("#statusveic option[value!='1']").show();
          $("#statusveic option[value='1']").hide();
        }       
      });


  });
  </script>

</body>
</html>