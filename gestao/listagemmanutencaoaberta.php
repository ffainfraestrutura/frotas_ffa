<?php
  date_default_timezone_set('America/Sao_Paulo');
  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  // Iniciar a sessão
  session_start();

  $hoje = date("Y-m-d");

  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];
  $tipo= $_SESSION['tipo'];

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
  } /*elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
  }*/

  //||$matricula == '500422'||$matricula == '501640'||$matricula == '500459'||$matricula == '410109'
  if($matricula == '160030'||$matricula == '501285'||$matricula == '410039'){
    $npode='nao pode';
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipo;

  include"../conecta2.php";
  //include"conecta.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $datainicial = $_POST['datai'];
  $datafinal = $_POST['dataf'];
  $tipofim = $_POST['tipo'];

  $sqla = "SELECT estado FROM bdaniel.tbfuncionario WHERE matricula='$matricula';";
  $resultadoa = mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
  $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
    $estadofunc = $rowa['estado'];

  $unidade = $_POST['unidade'];

  if(!empty($unidade)){
    $unidadef = $unidade;
  }else{
    if(!empty($estadofunc)){    
        $unidadef = $estadofunc;     
    }
  }

  if($matricula<>'410109'){
    $unidadef='TODOS';
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

    <title> Veículos em Manutenção </title>

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

        <div class="container-fluid px-4" style="width: 100%;">
          <h1 class="h1 pt-2 pb-2"> Veículos em Manutenção </h1>

          <!--div filtro-->
          <div class="border-bottom border-dark">
            <form action="./listagemmanutencaoaberta.php" method='POST'>
              <p>Filtre as manutenções concluídas por data de cadastro:</p>

              <div>
                <div class="col-4">
                  <label class='form-label'>Selecione unidade:</label>
                  <select class='form-select' name='unidade'>
                    <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
                    <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
                    <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
                    <option value="MG" <?=($unidadef == 'MG')?'selected':''?> >MG</option>
                    <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
                  </select>
                </div>
              </div>

              <div class="d-flex justify-content-start mb-2">
                <div class="col-4">
                  <label class="form-label" for='datai'>Data inicial:</label>
                  <input class="form-control" type="date" name="datai" id='datai' <?php print"value='$datainicial'";?>>
                </div>

                <div class="ms-2 col-4">
                  <label class="form-label" for='datai'>Data final:</label>
                  <input class="form-control" type="date" name="dataf" id='dataf' <?php print"value='$datafinal'";?>>
                </div>

                <div class="ms-2 col-4">
                  <label class="form-label" for='tipo'>Tipo de Manutenção:</label>
                  <select class="form-select" name="tipo" id='tipo'>
                    <option value=''>Selecione... </option>
                    <option value= 'MP' <?=($tipofim == 'MP')?'selected':''?>>Preventiva</option> 
                    <option value='MC' <?=($tipofim == 'MC')?'selected':''?>>Corretiva</option>
                    <option value='OS' <?=($tipofim == 'OS')?'selected':''?>>Ordem de Serviço</option> 
                    <option value='SS' <?=($tipofim == 'SS')?'selected':''?>>Sinistro</option>   
                  </select>
                </div>
              </div>

              <div>
                <button class='btn btn-success' type='submit'>Filtrar</button>
              </div>

              <div class="mt-2">
                <p><span style="color: red;">*</span> Campos obrigatórios</p>
              </div>
              
            </form>
            

          </div>

          <!-- div cadastrar nova manutenção-->
          <?php
          if($perfil=='4' && empty($npode)){
          print"
          <!-- div cadastrar nova manutenção-->
          <div class='mt-1 text-rigth d-flex justify-content-start'>
            <!--<a class='btn btn-primary text-rigth' href='https://ffa.painel-telecom.com:4545/frotas/gestao/importmanutencao.php' target='_blank'> Importar Manutenções em Lote </a>-->
            <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/importmanutencao.php' target='_blank'>
              <input type='hidden' name='perfil' value='$perfil'>
              <input type='hidden' name='mat_autor' value='$matricula'>
              
              <button class='btn btn-primary text-rigth' type='submit'> Importar Manutenções em Lote </button>
            </form>

            <!--<a class='btn btn-success text-rigth' href='./iniciocadastromanpreventiva.php'> Nova </a>-->
            <!--novo cadastro 0.5-->
            
            <form class='ms-2' method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/iniciocadastromanpreventiva.php' target='_blank'>
              <input type='hidden' name='perfil' value='$perfil'>
              <input type='hidden' name='mat_autor' value='$matricula'>
              <button class='btn btn-success text-rigth' type='submit'> Nova </button>
            </form>
          </div>";
          }
          
            //$sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND status<>'CONCLUIDO' AND placa<>'' ; ";
            //$sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND  tipo <> 'Desativada'  AND placa<>'' AND status<>'CONCLUIDO' ;";

          if($unidadef<>'TODOS'){//com alguma unidade definida
            if(!empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal' AND tipo='$tipofim' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";
            }elseif(!empty($datainicial) && !empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";
            }elseif(!empty($datainicial) && empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";

            }elseif(!empty($datainicial) && empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial'  AND tipo='$tipofim' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";
            }elseif(empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' AND tipo='$tipofim' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";

            }elseif(empty($datainicial) && !empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";

            }elseif(empty($datainicial) && empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND tipo='$tipofim' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";

            }else{
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND placa IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef');";

            }
          }else{//sem unidade definida
            if(!empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal' AND tipo='$tipofim';";
            }elseif(!empty($datainicial) && !empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal' ;";
            }elseif(!empty($datainicial) && empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial' ;";

            }elseif(!empty($datainicial) && empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND data>='$datainicial'  AND tipo='$tipofim' ;";
            }elseif(empty($datainicial) && !empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' AND tipo='$tipofim' ;";

            }elseif(empty($datainicial) && !empty($datafinal) && empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND  data<='$datafinal' ;";

            }elseif(empty($datainicial) && empty($datafinal) && !empty($tipofim)){
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' AND tipo='$tipofim' ;";

            }else{
              $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada'  AND placa<>'' AND status='ABERTO' ;";

            }
          }

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
                  <th>Prev de Saída </th>

                  <th>Tipo </th>
                  <!--<th>Entrada na oficina </th>-->
                  <th>Status </th>
                  <th>Oficina </th>
                  <th>Observação </th> 
                  <th>Editar manutenção</th>
                  <th>Ordem de Serviço</th>
                  <th>Apagar Registro</th>


                  <th>Últ. movimentação </th>
                  <th>Data Últ. movimentação </th>
                  <th>Feita por </th>
                </tr>
              </thead>

              <tbody>
              <?php
                include"./popupApagarOS.php";
                //query que puxa todos cadastrados na tabela manutencao
                //idtbmanprev, placa, data, tipo, hodometro

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $idtbmanprev= $row[0];
                  $placa=$row['placa'];
                  $data=$row['data'];
                  $prevsaida = $row['prevsaida'];

                  $tipo = $row['tipo'];
                  $dataentrada = $row['dataentrada'];
                  $dataagendamento = $row['dataagendamento'];
                  /*$status = utf8_encode($row['status']);
                  $oficina = utf8_encode($row['oficina']);
                  $observacao = utf8_encode($row['observacao']);*/
                  $status = $row['status'];
                  $oficina = $row['oficina'];
                  $observacao = $row['observacao'];                

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
                  if($data=='0000-00-00 00:00:00'){
                    $dataf = "00/00/0000";
                  }else{
                    $data1 = new DateTime($data);
                    $dataf = $data1-> format( 'd/m/Y' );
                  }      
                  
                  if($dataentrada == '0000-00-00 00:00:00'){
                    $dataentradaf = "00/00/0000";
                  }else{
                    $dataentrada1 = new DateTime($dataentrada);
                    $dataentradaf = $dataentrada1-> format( 'd/m/Y' );
                  }

                  if($dataagendamento=='0000-00-00 00:00:00'){
                    $dataagendamentof = "00/00/0000";
                  }else{
                    $dataagendamento1 = new DateTime($dataagendamento);
                    $dataagendamentof = $dataagendamento1-> format( 'd/m/Y' );
                  }

                  if(!empty($datamov)){                  
                    $datamov1 = new DateTime($datamov);
                    $datamovf = $datamov1-> format( 'd/m/Y' );
                  }else{
                    $datamovf = "";
                  }

                  if(!empty($prevsaida)){
                    /*$prevsaida1 = new DateTime($prevsaida);
                    $prevsaidaf = $prevsaida1-> format( 'd/m/Y' );*/
                    
                    $prevsaida1 = explode(" ", $prevsaida);
                    $prevsaida2 = explode("-", $prevsaida1[0]);
                    $prevsaidaf = $prevsaida2[2]."/".$prevsaida2[1]."/".$prevsaida2[0];
                  }else{
                    $prevsaidaf = "";
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

                  /*/historicoplacamanutencao.php*/
                  if($perfil == 4 && empty($npode)){
                    $info="
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/historicoplacamanutencao.php' target='_blank'> 
                      
                      <input type='hidden' name='placa' value='$placa'/> 
                      
                      <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Manutenções'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                    </form>";
                    
                    $editar="<!--editar manutenção https://ffa.painel-telecom.com:4545/frotas/gestao-->
                    <td style='padding-left: 55px'> 
                      <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/editarmanutencao.php' target='_blank'>
                        <input type='hidden' name='idtbmanprev' id='nome' value='$idtbmanprev'>
                        
                        <input type='hidden' name='perfil' value='$perfil'>
                        <input type='hidden' name='mat_autor' value='$matricula'>
                        <button style='border: none; background-color: transparent;' type='submit' title='Editar Manutenção'> <span class='material-symbols-outlined'> edit_note </span> </button>
                      </form> 
                    </td>";

                    $apagar = "<button type='button' data-bs-toggle='modal' data-bs-target='#apagaros' onclick = \"popupApagarOS($idtbmanprev,'$placa')\"  style='border: none; background-color: transparent;' title='Apagar manutenção'> <span class='material-symbols-outlined'>delete</span></button>";
                  }else{
                    $editar="<!--editar manutenção-->
                    <td style='padding-left: 55px'> </td>";

                    $apagar = " "; 
                  }

                  $date1 = new DateTime($hoje);
                  $date2 = new DateTime($prevsaida1[0]);
                  
                  if($date1 > $date2){

                    $style1 = "style= 'color:red';";
                  }else{
                    $style1 = "";
                  }

                  print"
                  <tr>                  
                    <td> $placa$info </td>
                    <td> $unidade </td>                  
                    <td> $dataagendamentof </td>
                    <td> $dataf </td>
                    <td $style1> $prevsaidaf </td>

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

                    <td>$apagar </td>                  

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
            <form  action="https://ffa.painel-telecom.com:4545/frotas/gestao/control/gerarexcelmanaberta.php" method="post">
              <?php               
              print "<input type='hidden' name='matricula' value='$matricula'>
              <input type='hidden' name='datafinal' value='$datafinal'>
              <input type='hidden' name='datainicial' value='$datainicial'>
              <input type='hidden' name='tipofim' value='$tipofim'>"; ?>
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

    <!-- jquery -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>

    <!-- dataTables -->
    <script type="text/javascript" src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>  

    <!-- Moment.js: -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>  
    <!-- Brazilian locale file for moment.js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/pt-br.js"></script>    
    <!-- Ultimate date sorting plug-in-->
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>


    <!--bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>
    
    <!-- script local -->
    <script type="text/javascript">
      var width = $(window).width();

      DataTable.datetime('DD/MM/YYYY');

      new DataTable('#tabela1', {
        order: [[3, 'desc']],     

        "columns": [
          null, //0
          null,
          { type: 'date-eu'},
          { type: 'date-eu'}, 
          { type: 'date-eu' }, 
          null, //4
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          { type: 'date-eu' },
          null,
                  
        ],

        // language: {
        //   url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
        // },

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

    <script type="text/javascript">
      function popupApagarOS(idtbmanprev, placa) {   

        $('#inputform').attr('value',idtbmanprev);
        $('#inputplaca').attr('value',placa);

        
      }
    </script>

  </body>
</html>