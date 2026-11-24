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

$sqla = "SELECT estado FROM bdaniel.tbfuncionario WHERE matricula='$matricula';";
$resultadoa = mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
  $estadofunc = $rowa['estado'];

$unidade = $_POST['unidade'];

if(!empty($unidade)){
  $unidadef = $unidade;
}else{
  if(!empty($estadofunc)){
    /*if($estadofunc == 'RJ'){
      $unidadef = 'TODOS';
    }else{*/
      $unidadef = $estadofunc;
    //}    
  }
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

  <title> Listagem CNH </title>

  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="../src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="../src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

  <!-- Bootstrap core CSS 
  <link href="../src/index_files/bootstrap.css" rel="stylesheet">-->

  <!-- Custom styles for this template -->
  <!--<script src="../src/index_files/ie-emulation-modes-warning.js.download"></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!--data tables - css e js-->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">

        <div class='mt-4'>
          <p style="font-style: italic; font-size:14px;">Aviso: Para otimizar o carregamento, nesta página estarão listados apenas os funcionários com CNH cadstrada de seu estado. Para visualizar todos os funcionários com CNH cadstrada, filtre por 'Todos'.</p>
        </div>

        <!--div filtro-->
        <div class="mt-2 d-flex justify-content-start">
          <form action="./listagemcnh.php" method="POST">
            <label class="form-label">Selecione unidade:</label>
            <select class="form-select" name='unidade'>
              <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
              <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
              <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
              <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
            </select>

            <button type='submit' class="mt-1 btn btn-success">Filtrar</button>
          </form>          
        </div>

        <!--<h6 class="pt-5 h6"> Selecione, entre os colaboradores com CNH cadastrada abaixo, o colaborador desejado para ativá-lo como condutor, associando-o a um veículo. </h6>-->

        <h6 class="pt-1 h6">Caso o colaborador desejado não tenha CNH cadastrada no sistema, utilize o botão "Novo Cadastro de CNH" abaixo e retorne a esta tela.</h6>

        <!-- div cadastrar nova cnh -->
        <div class="mt-3 d-flex justify-content-left">
          <a class="btn btn-success" href="./listagemfuncionarios2.php"> Novo Cadastro de CNH </a>
        </div>

        <!--div listagem fornecedores-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>
                <th>Matrícula </th>
                <th>Nome </th>
                <!--<th>Vincular/Desvincular veículo </th>-->
                <th>Editar CNH </th>
              </tr>
            </thead>

            <tbody>
              <?php
                //query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela cnh, lista apenas os que tem cnh cadastrada
                //$sql = "SELECT a.matricula, a.nome from bdaniel.tbfuncionario a, bdfrota.tbcnh b where a.matricula = b.matricula; ";

                //query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela cnh, lista apenas os que tem cnh cadastrada e não estão cadastrados na tabela condutor
                //$sql="SELECT a.matricula AS matricula, a.nome AS nome from bdaniel.tbfuncionario a, bdfrota.tbcnh b where a.matricula = b.matricula AND a.matricula NOT IN (SELECT matricula FROM bdfrota.tbcondutor WHERE ativo=1) AND a.status<>'Demitido'";

                if($unidadef == 'TODOS'){
                  $sql="SELECT * FROM bdfrota.tbcnh WHERE matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE  status='Ativo'); ";
                }else{
                  $sql="SELECT * FROM bdfrota.tbcnh WHERE matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE estado='$unidadef' AND status='Ativo'); ";
                }

                $resultado = mysqli_query($conexao, $sql) OR die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $matcond = $row['matricula'];

                  $sql1="SELECT nome, status FROM bdaniel.tbfuncionario WHERE matricula = '$matcond';";
                  $resultado1 = mysqli_query($conexao, $sql1) OR die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                   $nome=$row1['nome'];
                   $statusaniel = $row1['status'];

                  $sql2="SELECT MAX(idtbcondutor) AS idtbcondutor FROM bdfrota.tbcondutor WHERE matricula='$matcond'; ";
                  $resultado2 = mysqli_query($conexao, $sql2) OR die(mysqli_error($conexao));
                  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                   $idtbcondutor=$row2['idtbcondutor'];

                  $sql3="SELECT ativo FROM bdfrota.tbcondutor WHERE idtbcondutor='$idtbcondutor'; ";
                  $resultado3 = mysqli_query($conexao, $sql3) OR die(mysqli_error($conexao));
                  $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                   $ativo=$row3['ativo'];

                  if($ativo == 0){
                    $acaoveiculo = "<form method='post' action='./associarveiculo.php' target='_blank'><input type='hidden' name='matcondutor' id='matricula' value='$matcond'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar como condutor/Associar veículo'> <span class='material-symbols-outlined'>  add_link  </span> </button></form>";
                  }

                  if($ativo == 1){
                    $acaoveiculo = "<form method='post' action='./desvincularveiculo.php' target='_blank'><input type='hidden' name='idtbcondutor' id='matricula' value='$idtbcondutor'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar como condutor/Associar veículo'> <span class='material-symbols-outlined'> link_off  </span> </button></form>";
                  }



                  if($statusaniel=='Ativo'){
                  print "
                  <tr>
                    <td>$matcond</td>
                    <td>$nome </td>
                    <!--<td> $acaoveiculo </td>-->
                    
                    <td> <form method='post' action='./editarcnh.php' target='_blank'><input type='hidden' name='matricula' id='matricula' value='$matcond'><button style='border: none; background-color: transparent;' type='submit' title='Editar CNH'> <span class='material-symbols-outlined'> edit_note </span> </button></form> </td>
                  </tr>";
                  }
                }
              ?>
            </tbody>

            <tfoot>
              <tr>
                <th>Matrícula </th>
                <th>Nome </th>
                <!--<th>Vincular </th>-->
                <th>Editar CNH </th>
              </tr>
            </tfoot>  
          </table>
        </div>

        <!--div botões -->
        <div class="mt-5 text-rigth d-flex justify-content-between">
          <!-- botão voltar -->
          <div>
            <a class="btn btn-danger" href='./listagemcondutores.php'> Voltar para página anterior </a>
          </div>

          <!-- div listar todos condutores-->
          <div class="">
            <a class="btn btn-secondary text-rigth" href="./listagemcondutores.php"> Listar Todos Condutores </a>
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

  <script>window.jQuery || document.write('<script src="../src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    var width = $(window).width();
    new DataTable('#tabela1', {
        order: [[3, 'desc']]
    });   
      
  </script>

</body>
</html>