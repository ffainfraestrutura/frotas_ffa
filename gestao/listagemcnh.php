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

  if($perfil ==''){
    $perfil = $_POST['perfil_autor'];
    $matricula = $_POST['matr_autor'];
  }

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil<>'5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }elseif ($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula == '501640'||$matricula == '500459' || $matricula =='411425' || $matricula == '003931'){

    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão.');window.close();</script>";

    //*window.location=\"../index.php\"
  } /elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  include"../conecta03.php";
  include(__DIR__ . "/conecta02_frota.php");
  //include"conecta.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $sqla = "SELECT estado FROM bdaniel.tbfuncionario WHERE matricula='$matricula';";
  $resultadoa = mysqli_query($con, $sqla) or die(mysqli_error($con));
  $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
    $estadofunc = $rowa['estado'];

  $unidade = $_POST['unidade'] ?? null;

  if(!empty($unidade)){
    $unidadef = $unidade;
  }else{
    if(!empty($estadofunc)){
      $unidadef = $estadofunc;
    }else{
      $unidadef = 'RJ'; // valor padrão caso nenhum seja definido
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

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css e js-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">   

    <link href="../src/css/styles.css" rel="stylesheet" />

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
          <div class='mt-2 d-flex justify-content-start'>
            <form action="./listagemcnh.php" method="POST">
              <div class='d-flex justify-content-start align-items-end'>
                <div>
                  <label class="form-label">Selecione unidade:</label>
                  <select class="form-select" name='unidade'>
                    <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
                    <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
                    <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
                    <option value="ES" <?=($unidadef == 'ES')?'selected':''?> >ES</option>
                    <option value="MG" <?=($unidadef == 'MG')?'selected':''?> >MG</option>
                    <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
                  </select>
                </div>

                <div class='ms-3'>
                  <button type='submit' class="btn btn-success">Filtrar</button>
                </div>
              </div>
              
            </form>          
          </div>

          <!-- Aviso de filtro aplicado -->
          <div class="mt-3 alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> <strong>Filtro Aplicado:</strong> Exibindo funcionários do estado: <strong><?php echo ($unidadef == 'TODOS') ? 'TODOS OS ESTADOS' : $unidadef; ?></strong>
          </div>

          <h6 class="pt-5 h6"> Selecione, entre os colaboradores com CNH cadastrada abaixo, o colaborador desejado para ativá-lo como condutor, associando-o a um veículo. </h6>

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
                  <th>Anexar documentos </th>
                </tr>
              </thead>

              <tbody>
                <?php
                  //query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela cnh, lista apenas os que tem cnh cadastrada
                  //$sql = "SELECT a.matricula, a.nome from bdaniel.tbfuncionario a, bdfrota.tbcnh b where a.matricula = b.matricula; ";

                  //query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela cnh, lista apenas os que tem cnh cadastrada e não estão cadastrados na tabela condutor
                  //$sql="SELECT a.matricula AS matricula, a.nome AS nome from bdaniel.tbfuncionario a, bdfrota.tbcnh b where a.matricula = b.matricula AND a.matricula NOT IN (SELECT matricula FROM bdfrota.tbcondutor WHERE ativo=1) AND a.status<>'Demitido'";

                  if($unidadef == 'TODOS'){
                    $sql="SELECT * FROM bdfrota.tbcnh WHERE matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE UPPER(TRIM(status))='ATIVO'); ";
                  }else{
                    $sql="SELECT * FROM bdfrota.tbcnh WHERE matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE estado='$unidadef' AND UPPER(TRIM(status))='ATIVO'); ";
                  }

                  $resultado = mysqli_query($con, $sql) OR die(mysqli_error($con));

                  while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                    $matcond = $row['matricula'];

                    $sql1="SELECT nome, status FROM bdaniel.tbfuncionario WHERE matricula = '$matcond';";
                    $resultado1 = mysqli_query($con, $sql1) OR die(mysqli_error($con));
                    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                    $nome=$row1['nome'];
                    $statusaniel = $row1['status'];

                    $sql2="SELECT MAX(idtbcondutor) AS idtbcondutor FROM bdfrota.tbcondutor WHERE matricula='$matcond'; ";
                    $resultado2 = mysqli_query($con, $sql2) OR die(mysqli_error($con));
                    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $idtbcondutor=$row2['idtbcondutor'];

                    $sql3="SELECT ativo FROM bdfrota.tbcondutor WHERE idtbcondutor='$idtbcondutor'; ";
                    $resultado3 = mysqli_query($con, $sql3) OR die(mysqli_error($con));
                    $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                    $ativo=$row3['ativo'];

                    if($ativo == 0){
                      $acaoveiculo = "<form method='post' action='./associarveiculo.php' target='_blank'><input type='hidden' name='matcondutor' id='matricula' value='$matcond'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar como condutor/Associar veículo'> <span class='material-symbols-outlined'>  add_link  </span> </button></form>";
                    }

                    if($ativo == 1){
                      $acaoveiculo = "<form method='post' action='./desvincularveiculo.php' target='_blank'><input type='hidden' name='idtbcondutor' id='matricula' value='$idtbcondutor'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar como condutor/Associar veículo'> <span class='material-symbols-outlined'> link_off  </span> </button></form>";
                    }



                    if(strtoupper(trim($statusaniel)) == 'ATIVO'){
                    print "
                    <tr>
                      <td>$matcond</td>
                      <td>$nome </td>
                      <!--<td> $acaoveiculo </td>-->
                      
                      <td> 
                      <form method='post' action='./editarcnh.php' target='_blank'>
                        <input type='hidden' name='matricula' id='matricula' value='$matcond'>
                        <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>

                        <button style='border: none; background-color: transparent;' type='submit' title='Editar CNH'> <span class='material-symbols-outlined'> edit_note </span> </button>
                      </form> 
                      </td>
                      <td class='text-center'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/enviardocscond.php' target='_blank'>
                          <input type='hidden' name='matcondutor' value='$matcond'>
                          <input type='hidden' name='matr_autor' value='$matricula'>
                          <input type='hidden' name='perf_autor' value='$perfil'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Anexar documentos'> <span class='material-symbols-outlined'>attach_file </span> </button>                  
                        </form>  
                  </td>
                    </tr>";
                    }
                  }
                ?>
              </tbody>

              <tfoot>
                <tr>
                  <th>Matrícula </th>
                  <th>Nome </th>
                <!-- <th>Vincular/Desvincular veículo </th>-->
                  <th>Editar CNH </th>
                  <th>Anexar documentos </th>
                </tr>
              </tfoot>  
            </table>
          </div>

          <!--div botões -->
          <div class="mt-5 text-rigth d-flex justify-content-between">
            <!-- botão voltar -->
            <div>
              <!--<a class="btn btn-danger" href='./listagemcondutores.php'> Voltar para página anterior </a>-->

              <button class="btn btn-danger" onclick="window.close();"> Voltar para página anterior </button>
            </div>

            <!-- div listar todos condutores-->
            <div class="">
              <a class="btn btn-secondary text-rigth" href="https://ffa.painel-telecom.com:4242/frotas/gestao/listagemcondutores.php"> Listar Todos Condutores </a>
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

    <!-- jquery 3.5 -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- datatables js -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>

    <script type="text/javascript">
      var width = $(window).width();

      if(width<=768){
        $(document).ready(function(){
          $('#tabela1').DataTable({
            responsive: true,
            order: [[1, 'asc']],

          });
        });
        //console.log(width);
      }else{
        $(document).ready(function(){
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
            }

          });
        });
      }
        
    </script>

  </body>
</html>