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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Veículos em Manutenção </title>

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
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>



</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Veículos com Manutenção Concluída </h1>

        <!-- div cadastrar nova manutenção-->
        <?php
        if($perfil == 4){
          print"       
          <!-- div cadastrar nova manutenção-->
          <div class='mt-1 text-rigth d-flex justify-content-between'>
            <a class='btn btn-primary text-rigth' href='http://ffasip.ddns.net:4545/frotas/gestao/importmanutencao.php' target='_blank'> Importar Manutenções em Lote </a>

            <a class='btn btn-success text-rigth' href='./iniciocadastromanpreventiva.php'> Nova </a>
          </div>";
        }
        ?>

        <?php
          //$sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND status<>'CONCLUIDO' AND placa<>'' ;";
        $sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND placa<>'ABC1234'  AND placa<>'' AND status<>'ABERTO' ;";
          $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
          $linhas =mysqli_num_rows($resultado);
        ?>

        <!-- div total de manutenções-->
        <div class="mt-3 text-rigth">
          <p> Total de manutenções em aberto: <?php print $linhas?>. </p>
        </div>

        <!--div listagem veículos  manutenção-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>                
                <th>Placa </th>
                <th>Unidade </th>
                <th>Data de Agendamento </th>
                <th>Data de Cadastro </th>
                <th>Data de Conclusão </th>

                <th>Tipo </th>
                <!--<th>Entrada na oficina </th>-->
                <th>Status </th>
                <th>Oficina </th>
                <th>Observação </th> 
                <th>Editar manutenção</th>
                <th>Ordem de Serviço</th>

                <th>Últ. movimentação </th>
                <th>Data Últ. movimentação </th>
                <th>Feita por </th>
              </tr>
            </thead>

            <tbody>
            <?php
              //query que puxa todos cadastrados na tabela manutencao
              //idtbmanprev, placa, data, tipo, hodometro

              while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                $idtbmanprev= $row[0];
                $placa=$row['placa'];
                $data=$row['data'];
                $tipo = $row['tipo'];
                $dataentrada = $row['dataentrada'];
                $dataconclusao = $row['dataconclusao'];

                $dataagendamento = $row['dataagendamento'];
                $status = utf8_encode($row['status']);
                $oficina = utf8_encode($row['oficina']);
                $observacao = utf8_encode($row['observacao']);                

                $sql1="SELECT unidade FROM bdfrota.tbveiculo WHERE placa='$placa' ";
                $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                  $unidade=$row1['unidade'];

                $sql2="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE placa='$placa' AND (tipo='cadastro' OR tipo='edição'); ";
                $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                  $idtblog=$row2['idtblog'];

                $sql3="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog' ";
                $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                  $acao=$row3['acao'];
                  $autor=$row3['mat_autor'];
                  $datamov=$row3['data_e_hora'];

                //seleciona nome autor da ult modificação
                if($autor=='003535' || $autor=='003427'){
                  $autor = 'EQUIPE TI';
                }else{
                  $sql4="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor'; ";
                  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
                  $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                    $autor=$row4['nome'];
                }

                //tratando data
                $data1= explode(" ", $data);
                $data1 = explode("-", $data1[0]);
                $dataf = $data1[2]."/".$data1[1]."/".$data1[0];

                $dataentrada1= explode(" ", $dataentrada);
                $dataentrada1 = explode("-", $dataentrada1[0]);
                $dataentradaf = $dataentrada1[2]."/".$dataentrada1[1]."/".$dataentrada1[0];

                $dataagendamento1= explode(" ", $dataagendamento);
                $dataagendamento1 = explode("-", $dataagendamento1[0]);
                $dataagendamentof = $dataagendamento1[2]."/".$dataagendamento1[1]."/".$dataagendamento1[0];

                $dataconclusao1= explode(" ", $dataconclusao);
                $dataconclusao1 = explode("-", $dataconclusao1[0]);
                $dataconclusaof = $dataconclusao1[2]."/".$dataconclusao1[1]."/".$dataconclusao1[0];

                if(!empty($datamov)){
                  $datamov1 = explode(" ", $datamov);
                  $datamov2 = explode("-", $datamov1[0]);
                  $datamovf = $datamov2[2]."/".$datamov2[1]."/".$datamov2[0];
                }else{
                  $datamovf = "";
                }

                //tratando tipo
                if($tipo=='MP'){
                  $tipo='Manutenção Preventiva';
                } elseif($tipo=='OS'){
                  $tipo='Ordem de Serviço';
                } elseif($tipo=='MC'){
                  $tipo='Manutenção Corretiva';
                } elseif($tipo=='SS'){
                  $tipo='Sinistro';
                }

                if($status == 1){
                  $status =  'ABERTO';
                }elseif($status==2){
                  $status =  'CONCLUIDO';
                }

                $info="<form method='post' action='./historicoplacamanutencao.php' target='_blank'> 
                  <input type='hidden' name='placa' value='$placa'/> 
                  <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Manutenções'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                </form>";

                if($perfil == 4){
                  $editar="<!--editar manutenção-->
                  <td style='padding-left: 55px'> <form method='post' action='./editarmanutencao.php'><input type='hidden' name='idtbmanprev' id='nome' value='$idtbmanprev'><button style='border: none; background-color: transparent;' type='submit' title='Editar Manutenção'> <span class='material-symbols-outlined'> edit_note </span> </button></form> 
                  </td>";
                }else{
                  $editar="
                  <td style='padding-left: 55px'> </td>";
                }

                print"
                <tr>                  
                  <td> $placa$info </td>
                  <td> $unidade </td>                  
                  <td> $dataagendamentof </td>
                  <td> $dataf </td>
                  <td> $dataconclusaof </td>

                  <td> $tipo </td>
                  <!--<td> $dataentradaf </td>-->
                  <td> $status</td>
                  <td> $oficina </td>
                  <td> $observacao </td>

                  $editar                 

                  <!--ordem de serviço-->
                  <td class='text-center'> <form method='post' action='../pdf/fpdf/ordemdeservico.php' target='_blank'>
                    <input type='hidden' name='idtbmanprev' id='nome' value='$idtbmanprev'><button style='border: none; background-color: transparent;' type='submit' title='Editar Manutenção'> <span class='material-symbols-outlined'>visibility</span> </button></form>  
                  </td>

                  <td>$acao </td>
                  <td>$datamovf </td>
                  <td>$autor </td>
                </tr>
                ";
              }
            ?>
            </tbody>             
          </table>
        </div>

        <!-- div listar veículos cadastrados control/gerarexcelmanutencao-->
        <div class="mt-5 text-rigth d-flex justify-content-end">
          <!--<a class="btn btn-secondary text-rigth" href="./control/gerarexcelmanutencao.php"> Gerar Excel Todas Manuntenções </a>-->
          <form  action="./control/gerarexcelmanconc.php" method="post">
            <?php               
            print "<input type='hidden' name='matricula' value='$matricula'>"; ?>
            <input type="submit" class="form-control form-control-md btn btn-success" value="Gerar Excel">
          </form>

          <a class="ms-3 btn btn-secondary text-rigth" href="./listagemveiculos.php"> Veículos Cadastrados </a>
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
        order: [[3, 'desc']],

          "columns": [
            null, //0
            null,
            { type: 'date-eu', targets: 0 },
            { type: 'date-eu', targets: 0 }, 

            { type: 'date-eu', targets: 0 }, 
            null, //4
            { type: 'date-eu', targets: 0 },
            null,
            null,
            null,
            null,
            null,
            null,
            null
          ],
        
        });
      });

      /*console.log(width);
      console.log('2');*/
    }
      
  </script>

</body>
</html>