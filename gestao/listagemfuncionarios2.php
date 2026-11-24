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

  if($perfil==''){
    $perfil = $_POST['perfil_autor'];
    $matricula1 = $_POST['matr_autor'];
  }

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }elseif ($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula == '501640'||$matricula == '500459' || $matricula=='411425' || $matricula == '003931'){

    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";

    //*window.location=\"../index.php\"*
  } * elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  include"../conecta03.php";
  //include"../conecta.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $sqla = "SELECT estado FROM bdaniel.tbfuncionario WHERE matricula='$matricula';";
  $resultadoa = mysqli_query($con, $sqla) or die(mysqli_error($con));
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

    <title> Listagem Funcionários </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
    <!--data tables - css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

    <link href="../src/css/styles.css" rel="stylesheet" />
  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="mt-2">Listagem de Funcionários</h1>
          
          <h6 class="pt-5 h6"> Selecione, entre os colaboradores abaixo, aquele que se deseja cadastrar a CNH. </h6>

          <p style="font-style: italic; font-size:14px;">Aviso: Para otimizar o carregamento, nesta página estarão listados apenas os funcionários de seu estado. Para visualizar todos os funcionários, filtre por 'Todos'.</p>        

          <!--div filtro-->
          <div class="col-6">

            <form action="./listagemfuncionarios2.php" method="POST">
              <div class='d-flex justify-content-start align-items-end'>
                <div>              
                  <label class='form-label'>Selecione unidade:</label>
                  <select class='form-select' name='unidade'>
                    <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
                    <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
                    <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
                    <option value="ES" <?=($unidadef == 'ES')?'selected':''?> >ES</option>
                    <option value="MG" <?=($unidadef == 'MG')?'selected':''?> >MG</option>
                    <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
                  </select>
                </div>

                <div>
                  <button type='submit' class="mt-1 ms-3 btn btn-success">Filtrar</button>
                </div>
              </div>

            </form>
            
          </div>

          <!--div listagem funcionários-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Matrícula </th>
                  <th>Nome </th>
                  <th>Centro de Custo </th>                
                  <th>Cadastrar CNH </th>
                </tr>
              </thead>

              <tbody>
                
              <?php
                //duvidas, conferir leticia/frotas/control/fetch_records4.php

                //query que puxa o nome de todos os funcionarios não demitidos do banco de dados/da empresa e que não foram cadastrados como condutores ainda
                if($unidadef == 'TODOS'){
                  $sql = "SELECT idtbfuncionario, matricula, nome, ccusto FROM bdaniel.tbfuncionario where UPPER(TRIM(status)) <> 'DEMITIDO' AND matricula NOT IN (SELECT matricula FROM bdfrota.tbcnh where matricula is not null) AND matricula<>'999999' ORDER BY nome;";

                }else{
                  $sql = "SELECT idtbfuncionario, matricula, nome, ccusto FROM bdaniel.tbfuncionario where UPPER(TRIM(status)) <> 'DEMITIDO' AND matricula NOT IN (SELECT matricula FROM bdfrota.tbcnh where matricula is not null) AND matricula<>'999999' AND estado='$unidadef' ORDER BY nome;";
                  
                }

                $resultado = mysqli_query($con, $sql) OR die(mysqli_error($con));

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $idtbfuncionario = $row['idtbfuncionario'];
                  $matricula = $row['matricula'];              
                  $nome = $row['nome'];
                  $ccusto = $row['ccusto'];

                  print "
                  <tr>
                    <td>$matricula</td>
                    <td>$nome</td>
                    <td>$ccusto</td>

                    <td style='padding-left: 65px;'> <form method='post' action='./cadastrocnh.php' target='_blank'>

                    <input type='hidden' name='matricula' id='matricula' value='$matricula'>
                    <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>

                    <button style='border: none; background-color: transparent;' type='submit' title='Cadastrar CNH'> <span class='material-symbols-outlined'> problem </span> </button></form> </td>
                  </tr>";
                }
              ?>
              </tbody>

              <tfoot>
                <tr>
                  <th>Matrícula </th>
                  <th>Nome </th>
                  <th>Centro de Custo </th>
                  <th>Cadastrar CNH </th>
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

    <!-- jquery -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- datatables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>
    
    <!-- script local -->
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