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
  $matricula = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];
  $tipo= $_SESSION['tipo'];

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
  } /*elseif ($perfil <>'4' && $perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index-teste.php\"</script>";
  }*/

  if($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula == '501640'||$matricula == '500459'){
    $npode='nao pode';
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

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

    <title> Códigos de Multa </title>
    
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />

  </head>

  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Códigos de Multa </h1>

          <!--div listagem-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>                
                  <th>Código </th>
                  <th>Descrição </th>
                  <th>Valor </th>
                  <th>Editar</th>
                </tr>
              </thead>

              <tbody>
              <?php
                $sql = "SELECT * FROM bdfrota.tbacodmulta";
                $resultado1 = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado1, MYSQL_BOTH)) {
                  $idtbacodmulta = $row['idtbacodmulta'];
                  $codigo = $row['codigomulta'];
                  $descricao = $row['descricao'];
                  $valor = $row['valor'];

                  //tratando valores
                  $valor= str_replace(".", ",", $valor);
                  $valor = "R$ ".$valor;

                  print"
                  <tr>                  
                    <td> $codigo </td>
                    <td> $descricao </td>
                    <td> $valor </td>                  
                    <td style='padding-left: 35px;'> 
                      <form method='post' action='./editarcodmulta.php'>
                        <input type='hidden' name='idtbacodmulta' id='idtbacodmulta' value='$idtbacodmulta'>
                        <button style='border: none; background-color: transparent;' type='submit' title='Editar Fornecedor'>
                          <span class='material-symbols-outlined'> edit_note </span> 
                        </button>
                      </form> 
                    </td>
                  </tr>";
                }
              ?>                
              </tbody>              
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
    </div><!--div para fechar a div que fica aberta no menu.php-->

    <!-- jquery 3.7 -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>

    <!-- datatbles 1.13 -->
    <script type="text/javascript" src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>
    
    <!-- script local -->
    <script type="text/javascript">
      var width = $(window).width();

      new DataTable('#tabela1',{
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
          },
        }
      }); 
        
    </script>

  </body>
</html>