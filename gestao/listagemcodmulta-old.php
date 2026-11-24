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
} /*elseif ($perfil <>'4' && $perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index-teste.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
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

  <title> Listagem de Códigos de Multa </title>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <!--data tables - css e js-->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Códigos de Multa </h1>

        <!-- div cadastrar novo codigo multa-->
        <div class="mt-1 text-rigth d-flex justify-content-end">
          <a class="btn btn-success text-rigth" href="./cadastrocodmulta.php"> Novo </a>
        </div>

        <!--div listagem fornecedores-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id='datatablesSimple' class='mt-4 table table-striped'>
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
                $codigo = utf8_encode($row['codigomulta']);
                $descricao = utf8_encode($row['descricao']);
                $valor = $row['valor'];

                //tratando valores
                $valor= str_replace(".", ",", $valor);
                $valor = "R$ ".$valor;

                print"
                <tr>                  
                  <td> $codigo </td>
                  <td> $descricao </td>
                  <td> $valor </td>                  
                  <td style='padding-left: 35px;'> <form method='post' action='./editarcodmulta.php'><input type='hidden' name='idtbacodmulta' id='idtbacodmulta' value='$idtbacodmulta'><button style='border: none; background-color: transparent;' type='submit' title='Editar Fornecedor'><span class='material-symbols-outlined'> edit_note </span> </button></form> </td>
                </tr>
                ";
              }
            ?>
              
            </tbody>

            <tfoot>
              <tr>
                <th>Código</th>
                <th>Descrição </th>
                <th>Valor </th>
                <th>Editar</th>               
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
  </div><!--div para fechar a div que fica aberta no menu.php-->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $('#datatablesSimple').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
        },               

        /*lengthMenu: [
            [50, 100, 150, -1], [50, 100, 150, 'All']
        ]*/

        columnDefs: [{ 
            type: 'date-eu',
            targets: [14,17,18,26,27,29,31],
        }],
      });
      console.log('oi')
     });
      
  </script>

</body>
</html>