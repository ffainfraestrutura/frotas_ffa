<?php
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

$hoje=date('Y-m-d H:i:s');

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} /*elseif ($perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

$data = date('Y-m-d H:i:s');

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

  <title> Listagem de Multas </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="../src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

  <!-- Bootstrap core CSS 
  <link href="../src/index_files/bootstrap.css" rel="stylesheet">-->

  <!-- Custom styles for this template -->
  <!--<script src="../src/index_files/ie-emulation-modes-warning.js.download"></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <!--<script type="text/javascript" src="DataTables/datatables.min.js"></script> -->
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Multas </h1>

        <!-- div cadastrar nova multa-->
        <div class="mt-1 text-rigth d-flex justify-content-end">
          <a class="btn btn-success text-rigth" href="./cadastromulta.php"> Cadastro Unitário </a>
        </div>

        <!-- div import de multas excel-->
        <div class="mt-2 text-rigth d-flex justify-content-end">
          <a class="btn btn-primary text-rigth" href="./importmultas.php" style="width: 150px;"> Importar Excel </a>

          <a class="ms-2 btn btn-secondary text-rigth" style="width: 150px;" href="./control/docs/modelos/ImportMultaModelo.xls"> Modelo Excel </a>
        </div>

        <!--div listagem multas-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table"> <!--table-striped-->
            <thead>
              <tr>
                <th>Placa</th> 
                <th>Gravidade</th>
                <th>Nº do Auto</th>
                <th>Data Infração</th>
                <th>Vencimento</th>
                <th>Apresentar condutor em</th>
                <th>Situação</th>
                <th>Valor</th>
                <th>Editar</th>
              </tr>
            </thead>

            <tbody>
            <?php  
              $sql = "SELECT idtbmulta, placa, gravidade, autoinfracao,datalimiteloc, datainfracao, vencimento, valtotal FROM bdfrota.tbmulta WHERE idtbmulta<>'2112';";
              //$sql="SELECT idtbmovidatramite, placa, gravidade, autoinfra, apCondDV, dtinfra, dtvenc, valor FROM bdfrota.tbmovidatramite WHERE placa <> ''";
              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

              while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                $id = $row[0];
                $placa = $row[1];
                $gravidade= utf8_encode($row[2]);
                $autoinfracao= $row[3];
                $datalimiteloc = $row[4];
                $datainfracao= $row[5];
                $vencimento = $row[6];
                $valtotal= $row[7];

                $datainfracao1=explode(" ", $datainfracao);
                $datainfracao1= explode("-", $datainfracao1[0]);
                $datainfracao2= $datainfracao1[2]."/".$datainfracao1[1]."/".$datainfracao1[0];

                $vencimento1=explode(" ", $vencimento);
                $vencimento1= explode("-", $vencimento1[0]);
                $vencimento2= $vencimento1[2]."/".$vencimento1[1]."/".$vencimento1[0];

                $datalimiteloc1=explode(" ", $datalimiteloc);
                $datalimiteloc1= explode("-", $datalimiteloc1[0]);
                $datalimiteloc2= $datalimiteloc1[2]."/".$datalimiteloc1[1]."/".$datalimiteloc1[0];

                //valor
                $valtotal1= str_replace(".", ",", $valtotal);
                /*$valor1 = explode(",", $valtotal1);
                if(strlen($valor1[1])==1){
                  $valtotal1 = $valtotal1."0";
                } elseif(strlen($valor1[1])==0){
                  $valtotal1 = $valtotal1."00";
                }*/
                $valtotal2= "R$".$valtotal1;

                /*if($vencimento2 != "00/00/0000"){
                  //$vencimentoconv = $vencimento->format('d/m/Y');

                  $data_hoje = new DateTime($data);
                  $data_limite = new DateTime($vencimento);
                  $vencimentoconv = $data_limite->format('d/m/Y');

                  // Resgata diferença entre as datas
                  $dateInterval = $data_hoje->diff($data_limite);
                  $intervaloFinal = $dateInterval->format('%R%a');
                  $intervalo = $dateInterval->format('%a dias');
                  //$intervaloFinal;
                } else {
                  $intervalo = '-';
                }*/

                /*$data_hoje = new DateTime($data);
                $data_limite = new DateTime($datalimiteloc);
                //$datalimitelocconv = $data_limite->format('d/m/Y');

                // Resgata diferença entre as datas
                $dateInterval = $data_hoje->diff($data_limite);
                //$intervaloFinal = $dateInterval->format('%R%a');
                $intervalo = $dateInterval->format('%a dias');*/

                $inicio = new DateTime($hoje);
                $datalimiteloc3 = new DateTime($datalimiteloc);
                
                $dateInterval = $inicio->diff($datalimiteloc3);
                $intervalo = $dateInterval->format('%a dia(s)');

              print"
                <tr>
                  <td> $placa </td>
                  <td> $gravidade </td>
                  <td> $autoinfracao </td>
                  <td> $datainfracao2 </td>                  
                  <td> $vencimento2 </td>
                  <td class='text-center'> $datalimiteloc2 </td>";
                  
                  if($datalimiteloc2 == '00/00/0000'){
                    print "<td> Indeterminado".".</td>";
                  }elseif($inicio == $datalimiteloc3){
                    print "<td style='color:white; background-color: #733030; font-size: 14px;'> Prazo apresentar condutor vence hoje.</td>";
                  }elseif($inicio>$datalimiteloc3){
                    print "<td style='color:white; background-color: #733030; font-size: 14px;'> Prazo apresentar condutor venceu há $intervalo.</td>";
                  } else{
                    print "<td style='color:white; background-color: #255573;'> Prazo apresentar condutor vence em $intervalo.</td>";
                  }
                      
                  print"<td> $valtotal2 </td>
                  
                      <td> <form method='post' action='./editarmulta.php'><input type='hidden' name='idtbmulta' id='nome' value='$id'><button style='border: none; background-color: transparent;' type='submit' title='Editar Multa'> <span class='material-symbols-outlined'> edit_note </span> </button></form> </td>
                      </tr>";
                    }
              
            ?>

            </tbody>

            <tfoot>
              <tr>
                <th>Placa</th> 
                <th>Gravidade</th>
                <th>Nº do Auto</th>
                <th>Data Infração</th>
                <th>Vencimento</th>
                <th>Apresentar condutor em</th>
                <th>Situação</th>
                <th>Valor</th>
                <th>Editar </th>
              </tr>
            </tfoot>  
          </table>

          <div style="width: 100%;" class="mt-2 m-auto">
            <h6>Legenda tabela: </h6>
            <p class="mb-0" style='color: blue;'>Multas no prazo</p>
            <p style='color: red;'>Multas vencidas</p>
          </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('#tabela1').DataTable({
        "columns": [
          null,
          null,
          null,
          { "type": "date-eu" },
          { "type": "date-eu" },
          { "type": "date-eu" },
          null,
          null,
          null,
        ],
        "order": [4, 'asc'],

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
        }
        
      });
    });
  </script>

</body>
</html>