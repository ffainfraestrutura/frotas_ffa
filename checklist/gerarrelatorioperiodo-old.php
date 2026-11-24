<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil<>'1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;


$data = date('Y-m-d');

$inicio=$_POST['inicio'];
$fim = $_POST['fim'];

//include"../conecta.php";
include"../conecta03.php";

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

  <title> Visualizar Relatório </title>

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

  <!--link Material Symbols -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />  

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php //include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Relatórios por Período </h1>

        <p> Insira o período para visualizar os relatórios das vistorias realizadas.</p>

        <!-- div form-->
        <div>
          <form method="post" action="./gerarrelatorioperiodo.php" class="row">
            <!--div inicio-->
            <div class="row m-auto justify-content-around">
              <div class="col-6">
                <label class="col-form-label" for="inicio"> Data Inicial:<span style='color:red;'>*</span> </label>
                <div class="col-9">
                  <input type="date" name="inicio" id="inicio" class="form-control"  required <?php print "value = '$inicio' max='$data'; " ?>>
                </div>
              </div>
            

            <!--div fim-->
            
              <div class="col-6">
                <label class="col-3 col-form-label" for="fim">Data Final: <span style='color:red;'>*</span> </label>
                <div class="col-9">
                  <input type="date" name="fim" id="fim" class="form-control"  required <?php print "value = '$fim' max='$data'; " ?>>
                </div>
              </div>
            </div>

            <div class="mt-3 col-12 text-center">
              <button class="col-4 btn btn-success" type='submit'> Exibir relatórios </button>
            </div>

            <!-- div campos obrigatórios-->
            <div class="mt-4 col-12 border-bottom border-dark">
              <p style='color: red;'> * Campos obrigatórios. </p>
            </div>
          </form>
        </div>

        <!--div tabela-->        
        <div class='mt-3 col-12'>
          <table id='tabela1' class="table" style='width: 100%;'>
            <thead>
              <tr>
                <th>Relatório</th>
                <th>Data</th>
                <th>Tipo de vistoria</th>
                <th>Condutor</th>
                <th class="text-center">Assinado pelo condutor?</th>
                <th> </th>
                <th> </th>
              </tr>
            </thead>
            <tbody>
          <?php
            if(!empty($fim)){
              $sql1 = "SELECT DISTINCT * FROM bdfrota.tbvistoria WHERE datavistoria>='$inicio 00:00:00' AND datavistoria<='$fim 23:59:59' AND nome<>'' AND (idtbvistoria<2995 or  idtbvistoria>3015) AND idtbvistoria NOT IN (3028, 3031, 3032, 4719, 4718) AND placa NOT IN ('ABC1234', 'ABC1245', 'ABC1122') AND statusreg=1; ";
            }

            //$sql1 = "SELECT DISTINCT * FROM bdfrota.tbvistoria WHERE datavistoria>='$inicio' AND datavistoria<='$fim' AND idtbvistoria>3016; ";

            $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

            while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
              $idtbvistoria = $row1['idtbvistoria'];
              $placacorreta = $row1['placa'];
              $datavistoria = $row1['datavistoria'];
              $assinado = $row1['assinadocond'];
              $idnextfleet = $row1['idnextfleet'];
              $tipo = $row1['tipo'];
              $nome = $row1['nome'];


              /*$datavistoria1 = explode(" ",$datavistoria);
              $datavistoria2 = explode("-", $datavistoria1[0]);
              $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];*/

              $datavistoriareal= DateTime::createFromFormat('Y-m-d H:i:s', $datavistoria)->format('d/m/Y H:i:s');

              //$sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE placa = '$placacorreta' AND datavistoriaf='$datavistoria1[0]';";
              $sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria ='$idtbvistoria' and idtbvistfotos not in (40,41,42,43,44,45,47) ;";
              $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
              $linhas = mysqli_num_rows($resultado5);

              if($linhas <= 0 && $idnextfleet==''){//vistoria sem fotos
                //$link = '././fpdf/relatorio.php';
                $link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatorio.php';
                    
                $link2 = 'http://ffasip.ddns.net:4242/frotas/checklist'.$extra1;

                //$verarq = "<a href='$link2' target='_blank'>Ver anexos</a>";
                $pdf = "
                <td>
                  <form method='post' action='$link' target='_blank'>
                    <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                    <input type='hidden' name='datavistoria' value='$datavistoria'/>
                    <input type='hidden' name='placa' value='$placacorreta'/>
                    <button class='btn btn-success' type='submit'> Visualizar PDF</button>
                  </form>
                </td>";                

                $verfotos = " ";

                $a = 'entrou na opção 1';

              } elseif($linhas > 0 && $idnextfleet=='') {//vistoria com fotos
                $row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
                  $extra1 = $row5['extra1'];
                  $frontal = $row5['frontal'];

                //if else define o link
                if(strpos($extra1, "pdf") !== false){//vistoria manual
                  $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatorio.php';
                  

                  $pdf = "
                  <td>
                    <form class='mt-2'  action='./veranexos2.php' method='post' target='_blank'>
                      <input type='hidden' name='idtbvistoria' value='$idtbvistoria'>
                      <button type='submit' class='btn btn-secondary'>Vistoria Manual</button>
                    </form>
                  </td>";

                  $verfotos = "";

                  $a = 'entrou na opção 2';
                }else{
                  $achou = 'f';
                  // Nome do diretório inicial
                  $dirin = 'docs_old/';

                  $diretorio = dir($dirin);
                  $buscar = substr($frontal, 10);

                  while(($arquivo = $diretorio -> read()) && ($achou=='f')){
                  //while($arquivo = $diretorio -> read()){
                    if ($arquivo != '.' && $arquivo != '..'){

                      if(strpos($arquivo, $buscar) !== false){
                        $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatoriocomfotos.php';
                        $action='./verfotos05.php';
                        
                        //break;
                        $achou = 'true';
                      
                      }else{
                        $link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatoriocomfotos.php';
                        $action='./verfotos.php';
                        
                        //break;
                      }
                      
                    }
                  }                 

                  $diretorio -> close();

                  $pdf = "
                  <td>
                    <form method='post' action='$link' target='_blank'>
                      <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                      <input type='hidden' name='datavistoria' value='$datavistoria'/>
                      <input type='hidden' name='placa' value='$placacorreta'/>
                      <button class='btn btn-success' type='submit'> Visualizar PDF</button>
                    </form>
                  </td>";

                  $verfotos = "                    
                  <form method='post' action='$action' target='_blank'>
                    <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                    <input type='hidden' name='datavistoria' value='$datavistoria'/>
                    <input type='hidden' name='placa' value='$placacorreta'/>
                    <button class='btn btn-primary' type='submit'> Ver fotos</button>
                  </form>";
                  
                }

                
              }elseif($idnextfleet!=''){
                // Nome do diretório inicial
                $dirin = 'nextfleet/';

                $diretorio = dir($dirin);

                while($arquivo = $diretorio -> read()){
                  if ($arquivo != '.' && $arquivo != '..'){

                    if(strpos($arquivo, $idnextfleet) !== false){
                      $href = $arquivo;
                    }
                    
                  }
                }                 

                $diretorio -> close();
                

                $pdf = "<td><a href='nextfleet/$href' target='_blank'><button class='btn btn-success'> Visualizar PDF</button></a> </td>";

                $verfotos = "                    
                <form method='post' action='./verfotosnextfleet.php' target='_blank'>
                  <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                  <input type='hidden' name='datavistoria' value='$datavistoria'/>
                  <input type='hidden' name='placa' value='$placacorreta'/>
                  <input type='hidden' name='idnextfleet' value='$idnextfleet'/>

                  <button class='btn btn-primary' type='submit'> Ver fotos</button>
                </form>";
              }

              $sql2 ="SELECT  tipo FROM bdfrota.tbatipovist WHERE idtbatipovist = '$tipo' ";
              $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
              $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
                $tipof = $row2['tipo'];                       

                

              if($assinado==1){
                $assinadotab="<td class='text-center'>Sim</td>";
              } else {
              $assinadotab= "<td class='text-center'>Não</td>";
              }

              print "
                  <tr>
                    <td>Placa $placacorreta</td>
                    <td>$datavistoriareal</td>
                    <td>$tipof</td>
                    <td>$nome</td>
                    $assinadotab
                    $pdf
                    <td>$verfotos</td>             
                  </tr>";
            }
            ?>            
            </tbody>
          </table>               
        </div>

        <!--div gerar excel -->
        <div class="mt-2">
          <form action="./control/gerarexcelperiodo.php" method="post">
              <?php print "<input type='hidden' name='inicio' value='$inicio'/>
              <input type='hidden' name='fim' value='$fim'/>";?>
              <button type='submit' class='btn btn-success'>Gerar Relatório Excel</button>
          </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

<script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

<script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

<script type="text/javascript">
  $(document).ready(function () {
    $('#tabela1').DataTable({
      order: [[1, 'asc']],

      "language": {
        "decimal":        "",
        "emptyTable":     "Nada para exibir",
        "info":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        "infoEmpty":      "Exibindo página 0 de 0 de 0 registros",
        "infoFiltered":   "(filtrado do total de _MAX_ registros)",
        "infoPostFix":    "",
        "thousands":      ",",
        "lengthMenu":     "Exibir _MENU_ registros",
        "loadingRecords": "Carregando...",
        "processing":     "Processando...",
        "search":         "Buscar:",
        "zeroRecords":    "Nenhum resultado encontrado",
        "paginate": {
            "first":      "Primeira",
            "last":       "Última",
            "next":       "Próxima",
            "previous":   "Anterior"
        }
      },

      lengthMenu: [
          [10, 5, 1, -1],
          [10, 5, 1, 'Todos'],
      ],
    });

    
  });
</script>

</body>
</html>