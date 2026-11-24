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
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
  } /*elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  include"../conecta2.php";
  //include"../conecta.php";
  include_once"./func/log.php";
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

    <title> Listagem Geral de Fornecedores </title>

    <link href="../src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
    <!--data tables - css e js-->    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Listagem Geral de Fornecedores </h1>

          <!-- div cadastrar novo fornecedor-->
          <?php
            if($perfil=='4'){
              print"
              <div class='mt-1 text-rigth d-flex justify-content-end'>
                <a class='btn btn-success text-rigth' href='./cadastrofornecedor.php'> Novo </a>
              </div>";
            }
          ?>

          <!--div listagem fornecedores-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Número </th>
                  <th>Nome Fantasia </th>
                  <th>Telefone 1 </th>
                  <th>Telefone 2 </th>
                  <th>Ativo: </th>
                  <th>Editar Fornecedor </th>
                  <th>Ativar/Desativar Fornecedor </th>
                </tr>
              </thead>

              <tbody>
              <?php
                $sql = "SELECT idtbfornecedor, fantasia, telefone, telefone2, status FROM bdfrota.tbfornecedor";
                $resultado1 = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));


                while($row = mysqli_fetch_array($resultado1, MYSQL_BOTH)) {
                  $idtbfornecedor = $row['idtbfornecedor'];
                  $fantasia = utf8_encode($row['fantasia']);
                  $telefone = $row['telefone'];
                  $telefone2 = $row['telefone2'];
                  $contato = utf8_encode($row['contato']);
                  $status = $row['status'];

                  //tratando status
                  if($status == 0){
                    $status='Não';
                  } elseif($status == 1){
                    $status = 'Sim';
                  }

                  if($perfil == '4'){
                    $editar="<td style='padding-left: 35px;'> <form method='post' action='./editarfornecedor.php'><input type='hidden' name='idtbfornecedor' id='idtbfornecedor' value='$idtbfornecedor'><button style='border: none; background-color: transparent;' type='submit' title='Editar Fornecedor'> <span class='material-symbols-outlined'> edit_note </span> </button></form> </td>";
                  }else{
                    $editar="<td style='padding-left: 35px;'>  </td>";
                  }

                  if($perfil == 4){
                    $ativar = "<td style='padding-left: 40px;'> <form method='post' action='./control/cancelarfornecedor.php'><input type='hidden' name='idtbfornecedor' id='nome' value='$idtbfornecedor'>
                      <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                      <button style='border: none; background-color: transparent;' type='submit' title='Ativar/Desativar Fornecedor'> <span class='material-symbols-outlined'> delete </span> </button></form> </td>";
                  }else{
                    $ativar = "<td style='padding-left: 40px;'>  </td>";
                  }

                  print"
                  <tr>
                    <td> $idtbfornecedor </td>
                    <td> $fantasia </td>
                    <td style= 'white-space: nowrap;'> $telefone </td>
                    <td style= 'white-space: nowrap;'> $telefone2 </td>
                    <td> $status </td>
                    $editar
                    $ativar
                  </tr>
                  ";
                }
              ?>
                
              </tbody>

              <tfoot>
                <tr>
                  <th>Número </th>
                  <th>Nome Fantasia </th>
                  <th>Telefone 1 </th>
                  <th>Telefone 2 </th>
                  <th>Ativo </th>
                  <th>Editar Fornecedor </th>
                  <th>Ativar/Desativar Fornecedor </th>
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

    <!-- jquery - 3.5.1 -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
    
    <!-- fontawesome - v6.1.0 -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- datatables 1.13.1 - js -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <!-- bootstrap 5.1.3 - js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>
    
    <!-- script local -->
    <script type="text/javascript">
      $(document).ready(function(){
        $('#tabela1').DataTable({
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
      });
        
    </script>
  </body>
</html>