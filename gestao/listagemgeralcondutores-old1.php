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
} elseif ($perfil <> '4' && $perfil <> '11'){
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

  <style type="text/css">
    body, .btn, .form-select{
      font-size: 12px;
    }


  </style>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php";  ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h2 class="h2 pt-2 pb-2"> Listagem Geral de Condutores </h2>

        <p style="font-style: italic">Aviso: Para otimizar o carregamento, nesta página estarão listados apenas os condutores cujo último veículo possui unidade em seu estado. Para visualizar todos os veículos, filtre por 'Todos'.</p>

        <!--div filtro-->
        <div class="d-flex justify-content-start mb-4 pb-4 border-bottom border-dark">
          <form action="./listagemgeralcondutores.php" method="POST">
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

        <!-- div cadastrar novo fornecedor-->
        <?php 
        if($perfil==4){
          print"
          <div class='mt-1 text-rigth '>         
          
          <div class='mt-1 text-rigth d-flex justify-content-between'>
            <!--<a class='btn btn-primary text-rigth' href='./importcondutor.php'> Importar Condutores em Lote </a>-->

            <form action='http://ffasip.ddns.net:4545/frotas/gestao/importcnh.php' method='post' target='_blank'>
              <input type='hidden' name='matr_autor' value='$matricula1'>
              <input type='hidden' name='perfil_autor' value='$perfil'>
              <button class='btn btn-primary text-rigth'> Importar CNHs em Lote </button>
            </form>

            <a class='btn btn-success text-rigt' href='./listagemfuncionarios2.php'> Cadastrar Nova CNH</a>
          </div>

          <!-- div cadastrar novo-->
          <div class='mt-1 text-rigth d-flex justify-content-end'>
            <a class='btn btn-primary text-rigth' href='./listagemcnh.php'> Cadastrar Novo Condutor</a>
          </div>
        </div>";
        }
        ?>

        <!--div listagem geral de condutores-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>
                <th>Matrícula </th>
                <th>Nome </th>
                <th>Situação do Funcionário </th>
                <th>Centro de Custo </th>                
                <th>Status </th>
                <th>Placa</th>
                <th>Data de associação</th>
                <th>Data dissociação</th>
                <th>Associar/Desvincular veículo </th>
                <th>Editar CNH </th>
                <th>Anexar documentos </th>
                <th>Atualizado em </th>

              </tr>
            </thead>
            <tbody>
            <?php
              //$sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE matricula<>'003535';";WHERE matricula<>'003535';            

              if($unidadef ==='TODOS'){
                $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE placaassoc<>'ABC1234' /*AND matricula<>'003535' AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/;";

              }else{
                $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE /*matricula<>'003535' AND*/ placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef') /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/; ";
              }
              
              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($con));

              while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                $matcond = $row['matricula'];                

                $sql2="SELECT MAX(idtbcondutor) AS id FROM bdfrota.tbcondutor WHERE matricula='$matcond' /*AND ativo=1*/; ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $id = $row2['id'];
                
                //pegando os dados do ultimo id ativo
                $sql3 = "SELECT * FROM bdfrota.tbcondutor WHERE  idtbcondutor = '$id' ";
                $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                  $idtbcondutor = $row3['idtbcondutor'];
                  //$nomecond = utf8_encode($row3['nome']);
                  $datadissoc = $row3['datadissoc'];
                  $dataassoc = $row3['dataassoc'];

                  $placaassoc = $row3['placaassoc'];
                  $ativo = $row3['ativo'];

                //if($nomecond == '' && $matcond != ''){
                  $sql4 = "SELECT nome, status, ccusto FROM bdaniel.tbfuncionario WHERE  matricula = '$matcond' ";
                  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($con));
                  $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                    $nomecond = utf8_encode($row4['nome']);
                    $statusfunc = utf8_encode($row4['status']);
                    $ccusto= utf8_encode($row4['ccusto']);
                //}

                /*if($datadissoc !== '0000-00-00 00:00:00'){
                  if($datadissoc<$hoje){
                    $sql2="SELECT MAX(idtbcondutor) AS id FROM bdfrota.tbcondutor WHERE matricula='$matcond' ";
                  $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
                  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $id = $row2['id'];


                  $sql3 = "SELECT * FROM bdfrota.tbcondutor WHERE  idtbcondutor = '$id' ";
                  $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
                  $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                    $idtbcondutor = $row3['idtbcondutor'];
                    $nomecond = utf8_encode($row3['nome']);
                    $datadissoc = $row3['datadissoc'];
                    $dataassoc = $row3['dataassoc'];

                    $placaassoc = $row3['placaassoc'];
                    $ativo = $row3['ativo'];

                  }
                  
                }*/

                //tratando data
                $dataassoc1 = explode(" ", $dataassoc);
                $data = explode("-", $dataassoc1[0]);
                $datafim = $data[2]."/".$data[1]."/".$data[0];

                $datadissoc1 = explode(" ", $datadissoc);
                $datad = explode("-", $datadissoc1[0]);
                $datadfim = $datad[2]."/".$datad[1]."/".$datad[0];

                /*$sql2="SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond'; ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $ccusto= utf8_encode($row2['ccusto']);*/

                $sql5="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE matricula='$matcond' AND (tipo='cadastro' OR tipo='edição'); ";
                $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                  $idtblog1=$row5['idtblog'];

                $sql6="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog1' ";
                $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
                $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                  $acao1=$row6['acao'];
                  $autor1=$row6['mat_autor'];
                  $datamov=$row6['data_e_hora'];

                //seleciona nome autor da ult modificação
                if($autor1=='003535' || $autor1=='003427'){
                  $autor1 = 'EQUIPE TI';
                }else{
                  $sql7="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor1'; ";
                  $resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));
                  $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
                    $autor1=$row7['nome'];
                }

                if(!empty($datamov)){
                  $datamov1 = explode(" ", $datamov);
                  $datamov2 = explode("-", $datamov1[0]);
                  $datamovf = $datamov2[2]."/".$datamov2[1]."/".$datamov2[0];
                }else{
                  $datamovf = "";
                }

                //tratando ativo
                if($ativo == 1){
                  $ativo1='Ativo';
                } elseif($ativo==0){
                  $ativo1= 'Não ativo';
                }

                if($perfil==4){
                  $editarcnh="
                  <td class='text-center'> 
                    <form method='post' action='http://ffasip.ddns.net:4545/frotas/gestao/editarcnh.php' target='_blank'>
                      <input type='hidden' name='matricula' id='id' value='$matcond'>
                      <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                      <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                      <button style='border: none; background-color: transparent;' type='submit' title='Editar condutor'> <span class='material-symbols-outlined'> edit_note </span> </button>
                    </form>  
                  </td>";

                  $anexardocs="
                <td class='text-center'> 
                  <form method='post' action='http://ffasip.ddns.net:4545/frotas/gestao/enviardocscond.php' target='_blank'>
                    <input type='hidden' name='matcondutor' value='$matcond'>
                    <input type='hidden' name='matr_autor' value='$matricula'>
                    <input type='hidden' name='perf_autor' value='$perfil'>
                    <button style='border: none; background-color: transparent;' type='submit' title='Anexar documentos'> <span class='material-symbols-outlined'>attach_file </span> </button>                  
                  </form>  
                </td>";

                  if($ativo == 1){
                    $acao="
                    <td style='padding-left: 78px;'> 
                      <form method='post' action='http://ffasip.ddns.net:4545/frotas/gestao/desvincularveiculo.php' target='_blank'>
                        <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                        <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                        <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                        <button style='border: none; background-color: transparent;' type='submit' title='Desvincular veículo'> <span class='material-symbols-outlined'> link_off </span> 
                        </button>
                      </form>  
                    </td>";
                  } elseif($ativo == 0){
                    $acao="
                    <td style='padding-left: 78px;'> 
                      <form method='post' action='http://ffasip.ddns.net:4545/frotas/gestao/associarveiculo.php' target='_blank'>
                        <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                        <input type='hidden' name='matcondutor' id='id' value='$matcond'>
                        <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                        <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                        
                        <button style='border: none; background-color: transparent;' type='submit' title='Associar veículo'> <span class='material-symbols-outlined'> add_link </span> </button>
                      </form> 
                    </td>";
                  }
                }else{
                  $editarcnh="<td class='text-center'> </td>";

                  $anexardocs="<td class='text-center'> </td>";

                  $acao= "<td class='text-center'> </td>";
                }

                if(!empty($acao1)){
                  $title = "Descrição: $acao1; Realizado por: $autor1";
                }

                /*./historicocondchecklist.php*/
                $infocond = "<form method='post' action='./dadoscondutor.php' target='_blank'> 
                  <input type='hidden' name='matcond' value='$matcond'/> 
                  <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Condutor'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                </form>";

                /*./historicoplacachecklist.php*/
                $infoplaca = "<form method='post' action='./dadosplaca.php' target='_blank'> 
                  <input type='hidden' name='placa' value='$placaassoc'/> 
                  <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                </form>";

                if($matcond != ''){

                  print "
                  <tr>
                    <td> $matcond </td>
                    <td> $nomecond$infocond </td>
                    <td> $statusfunc </td>
                    <td> $ccusto </td>
                    <td> $ativo1 </td>
                    <td> $placaassoc$infoplaca </td>
                    <td> $datafim </td>
                    <td> $datadfim </td>
                    $acao
                   $editarcnh
                   $anexardocs
                   <td title='$title'>$datamovf</td>
                  </tr>";
                }
                                  
              }
            ?>
            </tbody>
            <!--<tfoot>
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
            </tfoot> --> 
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
            null,
            { "type": "date-eu" },
            { "type": "date-eu" },
            null,
            null,
            null,
          { "type": "date-eu" }

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
            null,
            { "type": "date-eu" },
            { "type": "date-eu" },
            null,
            null,
            null,
            { "type": "date-eu" }    
          ],

        "order": [1, 'asc'],

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