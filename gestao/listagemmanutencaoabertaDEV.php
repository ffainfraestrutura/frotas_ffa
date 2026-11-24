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
  } elseif ($perfil <> '1' && $perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
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

  // Buscar o ID do supervisor logado (SOMENTE para perfil 1)
  $idtbsupervisor = null;
  if($perfil == '1'){
    $sql_supervisor = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE matricula = '$matricula'";
    $resultado_supervisor = mysqli_query($conexao, $sql_supervisor) or die(mysqli_error($conexao));
    if($row_supervisor = mysqli_fetch_array($resultado_supervisor, MYSQLI_BOTH)){
      $idtbsupervisor = $row_supervisor['idtbsupervisor'];
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

          <?php if($perfil == '1' && !empty($idtbsupervisor)): ?>
          <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle"></i> <strong>Supervisor:</strong> Exibindo apenas manutenções de veículos com condutores da sua equipe.
          </div>
          <?php endif; ?>

          <!--div filtro-->
          <div class="border-bottom border-dark pb-3">
            <form action="./listagemmanutencaoabertaDEV.php" method='POST'>
              <p class="mb-3"><i class="fas fa-filter"></i> Filtre as manutenções em aberto por data de cadastro:</p>

              <div class="row g-2 mb-3">
                <div class="col-md-4">
                  <label class="form-label" for='datai'><i class="far fa-calendar"></i> Data inicial:</label>
                  <input class="form-control" type="date" name="datai" id='datai' <?php print"value='$datainicial'";?>>
                </div>

                <div class="col-md-4">
                  <label class="form-label" for='dataf'><i class="far fa-calendar"></i> Data final:</label>
                  <input class="form-control" type="date" name="dataf" id='dataf' <?php print"value='$datafinal'";?>>
                </div>
              </div>

              <div>
                <button class='btn btn-success' type='submit'><i class="fas fa-search"></i> Filtrar</button>
                <?php if(!empty($datainicial) || !empty($datafinal)): ?>
                <a href="./listagemmanutencaoabertaDEV.php" class="btn btn-secondary ms-2"><i class="fas fa-times"></i> Limpar Filtros</a>
                <?php endif; ?>
              </div>
              
            </form>
          </div>

          <?php
            //$sql = "SELECT * FROM bdfrota.tbmanprev WHERE tipo <> 'Desativada' AND status<>'CONCLUIDO' AND placa<>'' ; ";
            //$sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND  tipo <> 'Desativada'  AND placa<>'' AND status<>'CONCLUIDO' ;";

            // Query diferenciada SOMENTE para perfil 1 (supervisor) - apenas técnicos da equipe
            // Filtro: Veículos cujo condutor ATUAL (tbveiculo.matcond) pertence à equipe do supervisor
            if($perfil == '1' && !empty($idtbsupervisor)){
              $sql_base = "SELECT DISTINCT m.* 
                          FROM bdfrota.tbmanprev m
                          INNER JOIN bdfrota.tbveiculo v ON m.placa = v.placa
                          INNER JOIN bdfrota.tbusuario u ON v.matcond = u.matricula
                          WHERE m.placa <> 'ABC1234' 
                          AND m.tipo <> 'Desativada' 
                          AND m.placa <> '' 
                          AND m.status = 'ABERTO'
                          AND u.idtbsupervisor = '$idtbsupervisor'";

              // Adicionar filtros de data
              if(!empty($datainicial) && !empty($datafinal)){
                $sql = $sql_base . " AND m.data >= '$datainicial' AND m.data <= '$datafinal'";
              }elseif(!empty($datainicial) && empty($datafinal)){
                $sql = $sql_base . " AND m.data >= '$datainicial'";
              }elseif(empty($datainicial) && !empty($datafinal)){
                $sql = $sql_base . " AND m.data <= '$datafinal'";
              }else{
                $sql = $sql_base;
              }

              $sql .= " ORDER BY m.data DESC";
            }else{
              // Query para outros perfis (4, 5, 11) - todas as manutenções
              if(!empty($datainicial) && !empty($datafinal)){
                $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada' AND placa<>'' AND status='ABERTO' AND data>='$datainicial' AND data<='$datafinal';";
              }elseif(!empty($datainicial) && empty($datafinal)){
                $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada' AND placa<>'' AND status='ABERTO' AND data>='$datainicial';";
              }elseif(empty($datainicial) && !empty($datafinal)){
                $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada' AND placa<>'' AND status='ABERTO' AND data<='$datafinal';";
              }else{
                $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa<>'ABC1234' AND tipo <> 'Desativada' AND placa<>'' AND status='ABERTO';";
              }
            }

            $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
            $linhas =mysqli_num_rows($resultado);
          ?>

          <!-- div total de manutenções-->
          <div class="mt-3 text-rigth">
            <p> Total de manutenções em aberto<?php if($perfil == '1' && !empty($idtbsupervisor)) echo ' da sua equipe'; ?>: <?php print $linhas?>. </p>
          </div>

          <!--div listagem veículos  manutenção-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>                
                  <th>Placa </th>
                  <th>Unidade </th>
                  <th>Condutor </th>
                  <th>Data de Agendamento </th>
                  <th>Data de Cadastro </th>
                  <th>Prev de Saída </th>

                  <th>Tipo </th>
                  <!--<th>Entrada na oficina </th>-->
                  <th>Status </th>
                  <th>Oficina </th>
                  <th>Observação </th>
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

                  $sql1="SELECT unidade, matcond FROM bdfrota.tbveiculo WHERE placa='$placa' ";
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                    $unidade=$row1['unidade'];
                    $matcond=$row1['matcond']; // Matrícula do condutor ATUAL do veículo

                  // Buscar nome do condutor
                  $condutor = "";
                  if(!empty($matcond)){
                    $sql_condutor = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matcond'";
                    $resultado_condutor = mysqli_query($conexao, $sql_condutor);
                    if($row_condutor = mysqli_fetch_array($resultado_condutor, MYSQLI_BOTH)){
                      $condutor = $row_condutor['nome'];
                    }
                  }

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
                  if($perfil == 4){
                    $info="
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/historicoplacamanutencao.php' target='_blank'> 
                      
                      <input type='hidden' name='placa' value='$placa'/> 
                      
                      <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Manutenções'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                    </form>";
                  }else{
                    $info = "";
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
                    <td> $condutor </td>
                    <td> $dataagendamentof </td>
                    <td> $dataf </td>
                    <td $style1> $prevsaidaf </td>

                    <td> $tipo </td>
                    <!--<td> $dataentradaf </td>-->
                    <td> $status</td>
                    <td> $oficina </td>
                    <td> $observacao </td>
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
              <input type='hidden' name='datainicial' value='$datainicial'>"; ?>
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
        order: [[4, 'desc']],     

        "columns": [
          null,                   // 0 - Placa
          null,                   // 1 - Unidade
          null,                   // 2 - Condutor
          { type: 'date-eu'},     // 3 - Data de Agendamento
          { type: 'date-eu'},     // 4 - Data de Cadastro
          { type: 'date-eu'},     // 5 - Prev de Saída
          null,                   // 6 - Tipo
          null,                   // 7 - Status
          null,                   // 8 - Oficina
          null,                   // 9 - Observação
          null,                   // 10 - Últ. movimentação
          { type: 'date-eu'},     // 11 - Data Últ. movimentação
          null                    // 12 - Feita por
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

  </body>
</html>