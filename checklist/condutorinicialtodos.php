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
} /*elseif ($tipo <> '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$data = date('Y-m-d');

include"../conecta.php";
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

  <title> Condutor - Início </title>

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

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <!--<script type="text/javascript" src="DataTables/datatables.min.js"></script> -->
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4 col-md-10">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Condutor  </h1>        
          
          <!--div dados-->
          <?php
          //print $perfil."<br>". $tipo;
          if(file_exists("../../assinaturas/docs/colass/$matricula1.png")){
            $sql = "SELECT COUNT(*) as numlinhas FROM bdfrota.tbvistoria WHERE matricula = '$matricula1'; ";

            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

            while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
              $numlinhas = $row['numlinhas'];
            }

            if($numlinhas <= '0'){
                print "
                <p class='text-center'> Não há relatórios de vistoria disponíveis. </p>";
              } else {

            print "
            <!--<p class='text-center'> Para visualizar o relatório, clique em visualizar relatório.</p>-->

            <div class='mt-3' style='width: 100%; margin: auto;'>
              <table id='tabela1' class='text-center table'>
                <thead>
                  <tr>
                    <th class='text-center'>Relatório placa nº </th>
                    <th class='text-center'>Data</th>
                    <th class='text-center'>Assinado</th>
                    <!--<th class='text-center'>Visualizar </th>-->
                  </tr>
                </thead>
                <tbody>";

                $sql1 = "SELECT DISTINCT idtbvistoria, datavistoria, placa, assinadocond, idnextfleet FROM bdfrota.tbvistoria WHERE matricula = '$matricula1' AND (idtbvistoria<2995 or  idtbvistoria>3015) AND idtbvistoria NOT IN (3028, 3031, 3032, 4719, 4718) ORDER BY datavistoria DESC; ";

                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

                while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                  $idtbvistoria = $row1['idtbvistoria'];
                  $placa = $row1['placa'];
                  $datavistoria = $row1['datavistoria'];
                  $assinadocond=$row1['assinadocond'];
                  $idnextfleet = $row1['idnextfleet'];
                  //$idnextfleet = 'idnextfleet';

                  $datavistoria1 = explode(" ",$datavistoria);
                  $datavistoria2 = explode("-",$datavistoria1[0]);
                  $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];

                  $sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria='$idtbvistoria'";
                  $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
                  $linhas = mysqli_num_rows($resultado5);

                  if($linhas <= 0){
                    //$link = '././fpdf/relatoriocond.php';
                    $link = '././fpdf/relatorio.php';
                  } else {
                    //$link = '././fpdf/relatoriocondcomfotos.php';
                    $link = '././fpdf/relatoriocomfotos.php';
                  }

                  $href="checklist_ID_".$idnextfleet."_".$placa.".pdf";

                  if($idnextfleet == ''){
                    $pdf = "<td><form method='post' action=$link target='_blank'>
                            <input type='hidden' name='idtbvistoria' value='$idtbvistoria'/>                      
                            <input type='hidden' name='datavistoria' value='$datavistoria'/>
                            <input type='hidden' name='placa' value='$placacorreta'/>
                            <button style='border: none; background-color: white;' type='submit'> <span class='material-symbols-outlined'> info </span></button>
                          </form></td>";
                  } else{
                    $pdf = "<td><a href='$href' target='_blank'><button style='border: none; background-color: white;'> <span class='material-symbols-outlined'> info </span></button></a></td>";
                  }

                  if($idtbvistoria<3026){
                    $assinadotab = "<td> Sim </td>";
                  } else {
                    if($assinadocond==1){
                      $assinadotab="<td> Sim </td>";
                    } else {
                      $assinadotab= "<td> Não </td>";
                    }
                  }                  
                  
                  print "
                    <tr>
                      <td>$placa </td>
                      <td>$datavistoriareal</td>
                      $assinadotab
                      <!--$pdf-->
                    </tr>";
                  
                      
                }
                print "
                    </tbody>
                  </table>               
                </div>"; 
              }} else {
                if($perfil=='0'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../tecnico.php\"</script>";
                } elseif($perfil=='1'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../aprovacaocota.php\"</script>";
                } elseif($perfil=='2'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../coordenador.php\"</script>";
                }elseif($perfil=='3'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../dashboard.php\"</script>";
                }
              print"
              
              <!--
              <p> Você não possui assinatura cadastrada.</p>
              <p> Por favor procure o Departamento Pessoal para cadastrar a sua assinatura digital. </p>
              <p>Clique no botão abaixo e volte a página inicial.</p>
              
              <div class='mt-3 col-12 text-center'>
                <a class='btn btn-secondary' href='index.php' > Voltar a página inicial </a>
              </div>
              -->";
            }
          ?>            
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
  

<script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

<script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function (){
      $('#tabela1').DataTable({
        "searching": true,
        info: false,
        /*ordering: false,*/
        /*paging: false,*/
        "columns": [          
          null,
          { "type": "date-eu" },
          null,
          //null,
        ],
        "order": [1, 'desc'],

        responsive: true,

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
          },
          "aria": {
              "sortAscending":  ": activate to sort column ascending",
              "sortDescending": ": activate to sort column descending"
          }
        }
      });

      //mascara de placa
      /*$('#placa').mask('AAA0U00', {
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
      });*/
    });
  </script>

</body>
</html>