<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo= $_SESSION['tipo'];

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

  <title> Listagem Geral de Condutores </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js 
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>

  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Listagem Geral de Condutores </h1>

        <!-- div cadastrar novo fornecedor-->
        <div class="mt-1 text-rigth d-flex justify-content-end">
          <a class="btn btn-success text-rigth" href="./listagemfuncionarios2.php"> Cadastrar Novo Condutor</a>
        </div>

        <!--div listagem geral de condutores-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>
                <th>Matrícula </th>
                <th>Nome </th>
                <th>Centro de Custo </th>                
                <th>Status </th>
                <th>Placa</th>
                <th>Data de associação</th>
                <th>Data dissociação</th>
                <th>Associar/Desvincular veículo </th>
                <th>Editar CNH </th>
              </tr>
            </thead>
            <tbody>
            <?php
              
              //$sql="SELECT p.nome, p.placaassoc, p.idtbcondutor, p.matricula, p.ativo, p.cartaoassoc, p.dataassoc, p.datadissoc FROM bdfrota.tbcondutor p INNER JOIN ( SELECT matricula, MAX(dataassoc) AS dataassoc FROM  ON  groupedp.matricula = p.matricula AND groupedp.dataassoc = p.dataassoc AND p.matricula<>'003535' ";

              $sql="SELECT DISTINCT(matricula) FROM `bdfrota-teste`.tbcondutor;";
              
              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($con));

              while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                $matricula = $row['matricula'];

                $sql2="SELECT MAX(dataassoc) AS dataassoc FROM bdfrota.tbcondutor WHERE matricula='$matricula' ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $dataassoc = $row2['dataassoc'];

                $sql3 = "SELECT * FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND dataassoc = '$dataassoc' ";
                $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                  $idtbcondutor = $row3['idtbcondutor'];
                  $nomecond = $row3['nome'];
                  $datadissoc = $row3['datadissoc'];
                  $placaassoc = $row3['placaassoc'];
                  $ativo = $row3['ativo'];

                //tratando data
                $dataassoc1 = explode(" ", $dataassoc);
                $data = explode("-", $dataassoc1[0]);
                $datafim = $data[2]."/".$data[1]."/".$data[0];

                $datadissoc1 = explode(" ", $datadissoc);
                $datad = explode("-", $datadissoc1[0]);
                $datadfim = $datad[2]."/".$datad[1]."/".$datad[0];

                $sql2="SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $ccusto= utf8_encode($row2['ccusto']);

                //tratando ativo
                if($ativo == 1){
                  $ativo1='Ativo';
                } elseif($ativo==0){
                  $ativo1= 'Não ativo';
                }

                $editarcnh="
                <td class='text-center'> 
                  <form method='post' action='./editarcnh1.php'>
                    <input type='hidden' name='matricula' id='id' value='$matricula'>
                    <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                    <button style='border: none; background-color: transparent;' type='submit' title='Editar condutor'> <span class='material-symbols-outlined'> edit_note </span> </button>
                  </form>  
                </td>";

                if($matricula != ''){

                  print "<tr>
                    <td> $matricula </td>
                    <td> $nomecond </td>
                    <td> $ccusto </td>
                    <td> $ativo1 </td>
                    <td> $placaassoc </td>
                    <td> $datafim </td>
                    <td> $datadfim </td>";

                  if($ativo == 1){
                    print"<td style='padding-left: 78px;'> <form method='post' action='./desvincularveiculo.php'><input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'><button style='border: none; background-color: transparent;' type='submit' title='Desvincular veículo'> <span class='material-symbols-outlined'> link_off </span> </button></form>  </td>";
                  } elseif($ativo == 0){
                    print"<td style='padding-left: 78px;'> <form method='post' action='./editarcondutor.php'><input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'><button style='border: none; background-color: transparent;' type='submit' title='Associar veículo'> <span class='material-symbols-outlined'> add_link </span> </button></form> </td>";
                  }

                  print"
                   $editarcnh
                  </tr>";

                }
                                  
              }
            ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Matrícula </th>
                <th>Nome </th>
                <th>Centro de Custo </th>
                <th>Status </th>
                <th>Placa</th>
                <th>Data de associação</th>
                <th>Data dissociação</th>
                <th>Associar/Desvincular veículo </th>
                <th>Editar CNH </th>                
              </tr>
            </tfoot>  
          </table>
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

  <!--<script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>-->

  <!--<script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script> 
        
  <!--<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script type="text/javascript">
    var width = $(window).width();

    if(width<=768){
      $(document).ready(function(){
        $('#tabela1').DataTable({
          responsive: true,

          "order": [5, 'desc'],

          "columns": [
            null,
            null,
            null,
            null,
            null,
            { "type": "date-eu" },
            { "type": "date-eu" },
            null,
            null     
          ],

        });
      });
      //console.log(width);
    }else{
    
    $(document).ready( function () {
      //$.fn.dataTable.moment('DD/MM/AAAA');

      $('#tabela1').DataTable({
        "columns": [
            null,
            null,
            null,
            null,
            null,
            { "type": "date-eu" },
            { "type": "date-eu" },
            null,
            null     
          ],

        "order": [5, 'desc'],

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
}
  </script>

</body>
</html>