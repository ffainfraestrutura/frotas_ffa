<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];


if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($tipo <> '0' || $tipo<>'1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$data = date('Y-m-d');

include"../conecta2.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

if(file_exists("../../assinaturas/docs/colass/$matricula.png") == false){
  
  print"<script> window.alert('Assinatura digital não cadastrada.');
          window.location = '../tecnico.php'; </script>";
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

  <title> Multas Pendentes </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles2.css" rel="stylesheet" />
  <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
</head>
<body class="sb-nav-fixed">

  <!--nav-->
  <?php include "menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">
      <div class='container-fluid px-4 col-md-10'>
        <h1 class='h1 pt-3 pb-2 col-12 text-center'> Multas Pendentes</h1>      
          
          <!--div dados-->
          <?php
          //print "hi";

          if(file_exists("../../assinaturas/docs/colass/$matricula1.png")){//se a assinatura digital existe

            $sql="SELECT * FROM bdfrota.tbmulta WHERE etapa LIKE 'AGUARDANDO ASSINATURA%' AND autoinfracao IN (SELECT autoinfra FROM bdfrota.tbmovidatramite WHERE matricula = '$matricula' AND (reciboass='' OR reciboass='nao' OR recibolocass=0)); ";
            $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
            $nummultas = mysqli_num_rows($resultado);

            if($nummultas <= '0'){
              
              print "
              <p class='text-center'> Não há recibo(s) de multa(s) pedente(s) de assinatura.</p>";

            } else {
                
              print "
              <p class='text-center'> Há recibo(s) de multa(s) pedente(s) de assinatura. Clique no botão abaixo, verique e assine o relatório.</p>

                <div class='mt-3' >
                  <table id='tabela1'>
                    <thead>
                      <tr>
                        <th>Auto de Infração</th>
                        <th>Data de Infração</th>
                        <th>Placa do Veículo</th>
                        <th>Visualizar recibos</th>                        
                      </tr>
                    </thead>
                    <tbody>";

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                  $autoinfra = $row['autoinfracao'];
                  $placa = $row['placa'];

                  $sql1 = "SELECT dtinfra, reciboass, recibolocass FROM bdfrota.tbmovidatramite WHERE autoinfra='$autoinfra' AND placa='$placa'; ";
                  
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                  $dtinfra = $row1['dtinfra'];
                  $reciboass = $row1['reciboass'];
                  $recibolocass = $row1['recibolocass'];

                  $date = new DateTime( $dtinfra );
                  $dtinfraf = $date-> format( 'd/m/Y H:i:s' );

                  $sql1 = "SELECT idlocador FROM bdfrota.tbveiculo WHERE placa='$placa'; ";
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                    $idlocador = $row1['idlocador'];

                  if($reciboass<>'sim' || $recibolocass=='0'){
                    if($idlocador == '2' || $idlocador == '9' || $idlocador == '16' || $idlocador == '17' || $idlocador == '19'){
                      $recibolocadora = 'recibomovida';

                      $acaolocadora="
                      <form method='post' action='./$recibolocadora.php'> 
                        
                        <input type='hidden' name='placa' value='$placa'/> 
                        <input type='hidden' name='autoinfra' value='$autoinfra'/> 
                        <button type='submitt' style='border: none; background-color: transparent;' title='Assinar recibo'> <span class='material-symbols-outlined'>edit_note</span></button> 
                      </form>";

                    } elseif($idlocador == '3' || $idlocador == '4' || $idlocador == '14' || $idlocador == '15'){
                      $recibolocadora = 'recibolocaliza';

                      $acaolocadora="
                      <form method='post' action='./$recibolocadora.php'> 
                        
                        <input type='hidden' name='placa' value='$placa'/> 
                        <input type='hidden' name='autoinfra' value='$autoinfra'/> 
                        <button type='submitt' style='border: none; background-color: transparent;' title='Assinar recibo'> <span class='material-symbols-outlined'>edit_note</span></button> 
                      </form>";

                    } elseif($idlocador == 8){
                      $recibolocadora = 'recibounidas';

                      $acaolocadora="
                      <form method='post' action='./$recibolocadora.php'> 
                        
                        <input type='hidden' name='placa' value='$placa'/> 
                        <input type='hidden' name='autoinfra' value='$autoinfra'/> 
                        <button type='submitt' style='border: none; background-color: transparent;' title='Assinar recibo'> <span class='material-symbols-outlined'>edit_note</span></button> 
                      </form>";

                    }else{
                     
                      $acaolocadora="
                      <form method='post' action='./recibomultaffa.php'> 
                        
                        <input type='hidden' name='placa' value='$placa'/> 
                        <input type='hidden' name='autoinfra' value='$autoinfra'/> 
                        <button type='submitt' style='border: none; background-color: transparent;' title='Assinar recibo'> <span class='material-symbols-outlined'>edit_note</span></button> 
                      </form>";
                    }

                    
                  } else{
                    $acaolocadora="Recibo assinado";
                    
                  }

                  print"
                  <tr>
                    <td>$autoinfra</td>
                    <td>$dtinfraf</td>
                    <td>$placa</td>
                    <td class='text-center'>$acaolocadora</td>
                  </tr>";
                }
                
                print "
                    </tbody>
                  </table>               
                </div>"; 
              }

            } else {//se a assinatura digital não existe
                if($perfil =='0'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../tecnico.php\"</script>";
                  //print $tipo;
                } elseif($perfil=='1'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../aprovacaocota.php\"</script>";
                  //print $tipo;
                } elseif($perfil=='2'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../coordenador.php\"</script>";
                }elseif($perfil=='3'){
                  print"
                  <script language='javascript' type='text/javascript'>alert('Sem assinatura, procurar o DP');window.location=\"../dashboard.php\"</script>";
                }
              
              
            }
          ?>            
      </div>
    </main>

    <!--<footer class="py-4 bg-light mt-auto" style="position: fixed;">
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
      $('#tabela1').DataTable({
        
        //"order": [1, 'desc'],
        responsive: true,
        "searching": true,
        info: false,
        /*ordering: false,
        paging: false,*/

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

    });

      
  </script>

</body>
</html>