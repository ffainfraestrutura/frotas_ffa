<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$hoje=('Y-m-d H:i:s');

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

include"../conecta2.php";
//include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$unidadef = $_POST['unidade'];
$basef = $_POST['base'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Veículos </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="../src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="../src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

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
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

  <style type="text/css">
    body{
      font-size: 12px;
    }

    .btn{
      font-size: 12px;
    }
  </style>

</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 90%;">
        <h1 class="h1 pt-2 pb-2"> Veículos Cadastrados Ativos </h1>

        <p style="font-style: italic; font-size:14px;">Aviso: Nesta página estarão listados apenas os veículos ativos. Para visualizar tanto os veículos ativos quanto os inativos, clique no botão 'Todos os Veículos'.</p>

        <!-- div cadastrar novo veículo manualmente-->
        <!---->
        <div class="mt-1 text-rigth d-flex justify-content-start">
          <a class="btn btn-success text-rigth" href="./cadastroveiculo.php"> Cadastro Unitário </a>
        </div>

        <div class="mt-1 text-rigth d-flex justify-content-star">
          <a class="btn btn-secondary text-rigth col-2" href="./relinventario.php" target="_blank"> Todos os Veículos </a>
          <a class="ms-2 btn btn-primary text-rigth" style="width: 200px;" href="./importhodometro.php"> Atualizar Hodômetro </a>
            
        </div>

        <!-- div import de veículos excel 
       mt-2 text-rigth d-flex justify-content-between row
        <div class="mt-2 d-flex justify-content-between">
        <div class="d-flex justify-content-start">
          <div>
            <a class="btn btn-primary text-rigth" style="width: 150px;" href="./importveiculo.php"> Importar Excel </a>
          </div>

          <div>
            <a class="ms-2 btn btn-secondary text-rigth" style="width: 150px;" href="./control/docs/modelos/ModeloImportVeiculosNovo.xls"> Modelo Excel </a>
          </div>
        </div>

          <div>
            <div>
              <a class="ms-2 btn btn-primary text-rigth" style="width: 200px;" href="./importhodometro.php"> Atualizar Hodômetro </a>
            </div>            
          </div>
        </div>-->

        <!--div filtros-->
        <div class="mt-3">
          <form action='./listagemveiculos.php' method='POST'>
            <div class="d-flex">
              <div class="col-4">
                <label class='form-label' for='unidade'>Selecione a unidade:</label>
                <select class='form-select' name='unidade' id='unidade'>
                  <option value=''>Selecione...</option>
                  <?php
                    $sql="SELECT DISTINCT(unidade) FROM bdfrota.tbveiculo ORDER BY unidade;";
                    $resultado = $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                    while($row = mysqli_fetch_array($resultado, MYSQL_BOTH)) {
                      $unidade1= utf8_encode($row['unidade']);
                      $selected = ($unidadef == $unidade1) ? "selected=\"selected\"" : null;

                      if($unidade1 !=''){
                        print"<option value='$unidade1' $selected>$unidade1</option>";
                      }
                      
                    }
                  ?>
                </select>
              </div>

              <div class="ms-1 col-4">
                <label class='form-label' for='base'>Selecione a base de gestão:</label>
                <select class='form-select' name='base' id='base'>
                  <option value=''>Selecione...</option>
                  <?php
                    $sql="SELECT DISTINCT(basegestao) FROM bdfrota.tbveiculo ORDER BY basegestao; ";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                    while($row = mysqli_fetch_array($resultado, MYSQL_BOTH)) {
                      $basegestao= utf8_encode($row['basegestao']);
                      $selected = ($basef == $basegestao) ? "selected=\"selected\"" : null;

                      if($basegestao!=''){
                        print"<option value='$basegestao' $selected>$basegestao</option>";
                      }
                      
                    }
                  ?>
                </select>
              </div>


            </div>

            <div class="mt-1">
              <button class='btn btn-success' type='submit'>Filtrar</button>
            </div>            
          </form>          
        </div>

        <!--div listagem veículos-->
        <div style="width: 100%;" class="mt-4 m-auto">
          
          <table id="tabela1" class="table"><!---->
            <thead>
              <tr>
                <!--<th>Número </th>-->
                <th>Placa </th>
                <th>Condutor </th>
                <th>Matrícula </th>

                <th>Situação </th>
                <th>Aplicação </th>
                <th>Em manutenção? </th>
                <!--<th>Custo </th>--> <!--preciso definir uma coluna-->
                <th>Tipo de posse </th>
                <th>Vencimento da locação </th>                
                <th>Modelo </th>
                <th>Data últ. atualização </th>
                <th>Cadastrar/Editar Manutenção </th>
                <th>Editar Veículo </th>
                <th>Enviar Documentos </th>
              </tr>
            </thead>

            <tbody>
            <?php
              //$sql = "SELECT a.statusvel AS status, a.aplicacao AS aplicacao, a.tipoposse AS tipoposse, a.dtdevolucaoloc AS venclocacao, a.tipoposse AS tipoposse, a.idtbveiculo AS idtbveiculo, a.placa AS placa, c.modelo AS modelo, a.dtdevolucaoloc FROM bdfrota.tbveiculo a, bdfrota.tbmodeloveic c WHERE a.modelo = c.idtbmodeloveic AND idtbveiculo<>'1241'"; (statusvel = '1' or statusvel='33')

              //$sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' ";
              if(empty($unidadef)){
                if(empty($basef)){
                  $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' ";
                }else{
                  $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND basegestao='$basef'; ";
                }
                
              }else{
                if(empty($basef)){
                  $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND unidade='$unidadef'";
                }else{
                  $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND unidade='$unidadef' AND basegestao='$basef' ";
                }
                
              }

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

              while($row = mysqli_fetch_array($resultado, MYSQL_BOTH)) {
                $statusvel = $row['statusvel'];
                $aplicacao = $row['aplicacao'];
                $tipoposse = utf8_encode($row['tipoposse']);
                $venclocacao = $row['dtdevolucaoloc'];                
                $idtbveiculo=$row['idtbveiculo'];
                $placa=$row['placa'];
                $modelo = $row['modelo'];
                $marca = $row['marca'];

                $datamovimentacao = $row['datamovimentacao'];
                $matcond = $row['matcond'];

                $sql1 = "SELECT * FROM bdfrota.tbmanprev WHERE placa='$placa'; ";
                $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                $linhas = mysqli_num_rows($resultado1);

                $sql2="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusvel'; ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $status1=utf8_encode($row2['status']);

                $sql3="SELECT aplicacao FROM bdfrota.tbaplicacaoveic WHERE idtbaplicacaoveic='$aplicacao'; ";
                $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                  $aplicacao1= utf8_encode($row3['aplicacao']);

                if($linhas > 0){
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                  $dataentrada = $row1['dataentrada'];
                  $dataretirada = $row1['dataretirada'];
                  $status = utf8_decode($row1['status']);
                  $oficina= utf8_decode($row1['oficina']);
                }
                
                if($dataentrada != ''){
                  if($dataentrada != '0000-00-00 00:00:00' && $hoje>=$dataentrada && $dataretirada=='0000-00-00 00:00:00'){
                    $manutencao='SIM';
                  } else{
                  $manutencao='NÃO';
                  }
                } else{
                  $manutencao='NÃO';
                }

                $sql4="SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='$modelo';";
                $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
                $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                  $modelof=$row4['modelo'];

                $sql5="SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$Tipo de posse'; ";
                $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                  $fantasia=$row5['fantasia'];

                $sql6="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matcond' ";
                $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
                $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                  $nomecond=$row6['nome'];

                //tratando data
                $venclocacao1 = explode(" ", $venclocacao);
                $venclocacao2 = explode("-", $venclocacao1[0]);
                $venclocacaof = $venclocacao2[2]."/".$venclocacao2[1]."/".$venclocacao2[0];

                $datamovimentacao1 = explode(" ", $datamovimentacao);
                $datamovimentacao2 = explode("-", $datamovimentacao1[0]);
                $datamovimentacaof = $datamovimentacao2[2]."/".$datamovimentacao2[1]."/".$datamovimentacao2[0];

                if($tipoposse == 'AGREGADO'){
                  $letra= "color: green;";
                } 
                if($tipoposse == 'LOCADO'){
                  $letra= "color: blue;";
                } 
                if($tipoposse == 'PRÓPRIO'){
                 $letra= "color: purple;";
                }
                if($tipoposse == 'TERCEIRO'){
                  $letra= "color: red;";
                }

                $inicio = new DateTime($hoje);
                $venclocacao3 = new DateTime($venclocacao);
                
                $dateInterval = $inicio->diff($venclocacao3);
                $intervalo = $dateInterval->format('%a');

                if($venclocacaof != '00/00/0000'){
                  if($hoje>$venclocacao){
                    $title = "title='Locação vencida.' ";
                    $style = "style=\"background-color: #F0F0F0; $letra\" "; 
                  } 
                } else {
                    $style = "style= \"background-color: white; $letra\"";
                }

                $infoplaca = "<form method='post' action='./historicoplacachecklist.php' target='_blank'> 
                  <input type='hidden' name='placa' value='$placa'/> 
                  <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                </form>";

              if($matcond!=''){
                $infocon = "<form method='post' action='./historicocondchecklist.php' target='_blank'> 
                  <input type='hidden' name='matcond' value='$matcond'/> 
                  <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Condutor'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                </form>";
              }else{
                $infocon = "";
              }
//$status1
                print"<tr $style>
                        <!--<td> $idtbveiculo </td>-->
                        <td> $placa$infoplaca </td>
                        <td>$nomecond$infocon</td> 
                        <td>$matcond</td>                        

                        <td> $status1 </td>
                        <td> $aplicacao1 </td>
                        <td title='Oficina: $tit'> $manutencao </td>
                        <!--<td> custo </td>-->
                        <td> $tipoposse </td>
                        <td> $venclocacaof </td>
                        <td> $modelof </td>                        
                        <td>$datamovimentacaof</td>";

                if($linhas<=0){
                  print"
                        <td class='text-center'> <form method='post' action='./cadastromanpreventiva.php'><input type='hidden' name='placa' id='placa' value='$placa'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar/Editar Manutenção'> <span class='material-symbols-outlined'> problem </span> </button></form> </td>";
                } else {
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQL_BOTH);
                    $idtbmanprev = $row1['idtbmanprev'];
                  print"
                        <td class='text-center'> <form method='post' action='./editarmanutencao.php'><input type='hidden' name='idtbmanprev' id='nome' value='$idtbmanprev'><button style='border: none; background-color: transparent;' type='submit' title='Cadastrar/Editar Manutenção'> <span class='material-symbols-outlined'> problem </span> </button></form> </td>";
                }
                  print"
                        <td style='padding-left: 45px;'> <form method='post' action='./editarcadveiculo.php'><input type='hidden' name='idtbveiculo' id='nome' value='$idtbveiculo'><button style='border: none; background-color: transparent;' type='submit' title='Editar Veículo'> <span class='material-symbols-outlined'> edit_note </span> </button></form> </td>

                        <td style='padding-left: 45px;'> <form method='post' action='./enviardocumento.php'>
                        <input type='hidden' name='idtbveiculo' id='nome' value='$idtbveiculo'>
                        <input type='hidden' name='placa' id='placa' value='$placa'>
                        <button style='border: none; background-color: transparent;' type='submit' title='Enviar Documentos'> <span class='material-symbols-outlined'> attach_file</span></button></form> </td>
                      </tr>";
              }
            ?>
            </tbody>

            <!--<tfoot>
              <tr>
                <--<th>Número </th>
                <th>Placa </th>
                <th>Modelo </th>
                <th>Situação </th>
                <th>Aplicação </th>
                <th>Em manutenção?</th>
                <--<th>Custo </th>
                <th>Tipo de posse </th>
                <th>Vencimento da locação </th>
                <th>Condutor </th>
                <th>Data últ. atualização </th>

                <th>Cadastrar/Editar Manutenção </th>
                <th>Editar Veículo </th>
                <th>Enviar Documentos </th>
              </tr>
            </tfoot>-->  
          </table>
        </div>

        <!-- div listar veículos em manutenção -->
        <div class="mt-5 text-rigth d-flex justify-content-end">
          <a class="btn btn-secondary text-rigth" href="./listagemmanutencao.php"> Veículos em Manutenção </a>

          <form class="ms-1" action='./control/gerarexcelveicativos.php' method='post'>            
            <?php
            print "<input type='hidden' name='matr_autor' value='$matricula' />
            <input type='hidden' name='unidade' value='$unidadef' />
            <input type='hidden' name='base' value='$basef' />";
            ?>
            <button class="btn btn-success">Gerar Excel - Veículos Ativos</button>
          </form>
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
    var width = $(window).width();

    if(width<=768){
      $(document).ready(function(){
        $('#tabela1').DataTable({
          responsive: true,
        });
      });
      //console.log(width);
    }else{
      $(document).ready(function(){
        $('#tabela1').DataTable({
          "columns": [
            null,//placa
            null,
            null,//modelo
            null,//situação
            null,//aplicaçaão
            null,//manutenção
            null, //tipo de posse
            { "type": "date-eu" },         
            null,
            { "type": "date-eu" },
            null,
            null,
            null,
          ],

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

      /*console.log(width);
      console.log('2');*/
    }

    /*$(document).ready(function(){
      $('#tabela1').DataTable({
        

        
      });
    });*/
      
  </script>

</body>
</html>