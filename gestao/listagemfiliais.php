<?php
  date_default_timezone_set('America/Sao_Paulo');

  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  // Iniciar a sessão
  session_start();

  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula1 = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];
  $tipo=$_SESSION['tipo'];

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
  } /*elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
  }*/

  if($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459'){
    $npode='nao pode';
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  //include"conecta.php";
  include"../conecta2.php";
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

    <title> Listagem de Filiais </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css --> 
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css">

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />
  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Filiais Ativas </h1>

          <!-- div cadastrar nova filial-->
          <?php
            if($perfil=='4' && empty($npode)){
              print"
              <div class='mt-1 text-rigth d-flex justify-content-end'>
                <a class='btn btn-success text-rigth' href='./cadastrofilial.php'> Nova </a>
              </div>";
            }
          ?>

          <!--div listagem filiais-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  
                  <th>Código </th>
                  <th>Descrição </th>
                  <th>Editar Filial </th>
                  <th>Desativar Filial </th>
                </tr>
              </thead>

              <tbody>
              <?php
                $sql = "SELECT idtbfilial, codigo, descricao FROM bdfrota.tbfilial WHERE ativo=1";
                //$sql="SELECT * FROM bdaniel.tbfilial;";
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $idtbfilial = $row['idtbfilial'];
                  $codigo = $row['codigo'];
                  $descricao = $row['descricao'];

                  /*$idtbfilial= $row['idtbfilial'];
                  $codigo= $row['codfilial'];
                  $descricao=$row['nome'];*/

                  if($perfil == 4  && empty($npode)){
                    $editar="<td style='padding-left: 45px;'> <form method='post' action='./editarfilial.php'><input type='hidden' name='idtbfilial' id='idtbfornecedor' value='$idtbfilial'><button style='border: none; background-color: transparent;' type='submit' title='Editar Filial'> <span class='material-symbols-outlined'> edit_note </span> </button></form> 
                          </td>";

                    $desativar="<td style='padding-left: 60px;'> <form method='post' action='./control/cancelarfilial.php' ><input type='hidden' name='idtbfilial' id='idtbfilial' value='$idtbfilial'>
                            <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                            <button style='border: none; background-color: transparent;' type='submit' title='Desativar Filial'> <span class='material-symbols-outlined'> delete </span> </button></form> 
                          </td>";
                  }else{
                    $editar="<td style='padding-left: 45px;'> </td>";
                    $desativar = "<td style='padding-left: 60px;'> </td>";
                  }

                  print"
                  <tr>                        
                    <td> $codigo </td>
                    <td> $descricao </td>
                    $editar
                    $desativar
                  </tr>";  
                }
              ?>     
              </tbody>

              <tfoot>
                <tr>
                  
                  <th>Código </th>
                  <th>Descrição </th>
                  <th>Editar Filial </th>
                  <th>Desativar Filial </th>
                </tr>
              </tfoot>  
            </table>
          </div>

          <!-- div listar todas filiais-->
          <div class="mt-5 text-rigth d-flex justify-content-end">
            <a class="btn btn-secondary text-rigth" href="./listagemgeralfiliais.php"> Listar Todas Filiais </a>
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

    <!-- jquery - js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.1.js'></script>

    <!-- fontawesome - v6.1.0 - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- datatables.net - 1.13.7 - js -->
    <script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>

    <!-- bootstrap - 5.1.3 - js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>

    <script type="text/javascript">
      var width = $(window).width();

      new DataTable('#tabela1', {
        responsive: true,

        language: {
          decimal:        "",
          emptyTable:     "Nada para exibir",
          info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
          infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
          infoFiltered:   "(filtrado do total de _MAX_ registros)",
          infoPostFix:    "",
          thousands:      ".",
          lengthMenu:     "Exibir _MENU_ registros",
          loadingRecords: "Carregando...",
          processing:     "Processando...",
          search:         "Buscar:",
          zeroRecords:    "Nenhum resultado encontrado",
          paginate: {
            first:      "Primeira",
            last:       "Última",
            next:       "Próxima",
            previous:   "Anterior"
          }
        }
      });

    </script>

  </body>
</html>