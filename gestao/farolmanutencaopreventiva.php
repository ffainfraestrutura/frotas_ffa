<?php
  date_default_timezone_set('America/Sao_Paulo');
  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  // Iniciar a sessão
  session_start();

  $hoje = date('Y-m-d');

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

  if ($matricula == '160030'||$matricula == '410109'||$matricula == '501285'||$matricula == '410039' || $matricula=='411425' || $matricula == '003931'){
    $npode = 'não pode';
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
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

    <title> Farol Manut. Prev.
 | Portal FFA </title>
    
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - js-->
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
          <h1 class="h1 pt-2 pb-2"> Farol Manutenção Preventiva </h1>

          <!-- div cadastrar nova manutenção-->
          <!--<div class="mt-1 text-rigth d-flex justify-content-end">
            <a class="btn btn-success text-rigth" href="./iniciocadastromanpreventiva.php"> Nova </a>
          </div>-->

          <!--div filtros farol manutenção preventiva-->
          <div class="card mb-3">
            <div class="card-body py-2">
              <div class="row g-2 align-items-end">
                <div class="col-md-4">
                  <label class="form-label mb-1" for="filtroUf">UF</label>
                  <select class="form-select form-select-sm filtro-farol" id="filtroUf" data-column="13">
                    <option value="">Todas</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label mb-1" for="filtroStatusFarol">Status</label>
                  <select class="form-select form-select-sm filtro-farol" id="filtroStatusFarol" data-column="9">
                    <option value="">Todos</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label mb-1" for="filtroStatusVeiculo">Status do veículo</label>
                  <select class="form-select form-select-sm filtro-farol" id="filtroStatusVeiculo" data-column="15">
                    <option value="">Todos</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!--div listagem veículos  manutenção-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>                
                  <th>Placa </th>
                  <th>Condutor </th>
                  <th>Centro de Custo </th>                
                  <th>Última Manutenção </th>
                  <th>Hodômetro Atual </th>
                  <th>Últ. Man. Hodometro </th>
                  <th>Diferença </th>
                  <th>Locadora </th>
                  <th>Dev. para locadora </th>
                  <th>Status </th>
                  <th>Cadastrar manutenção</th>
                  <th>Data últ. agend. em aberto</th>
                  <th>Obs. últ. manutenção</th>
                  <th>UF</th>
                  <th>Base de gestão</th>
                  <th>Status do veículo</th>
                </tr>
              </thead>

              <tbody>
              <?php
                //query que puxa todos cadastrados na tabela veiculo
                //idtbmanprev, placa, data, tipo, hodometro
                $sql = "SELECT placa, matcond, hodometro, idlocador, dtdisponivelloc, dtdevolucaoloc, uf, basegestao, statusvel FROM bdfrota.tbveiculo WHERE placa NOT IN ('ABC112','ABC1122','ABC1245') AND placa<>'' /*AND statusvel NOT IN (5,13,18)*/ AND status='1' AND tipoposse<>'AGREGADO' AND tipoposse<>'TERCEIRO' AND tipoposse<>'PARTICULAR' /*AND dtdevolucaoloc>'$hoje'*/ ;";
                
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $placa=$row['placa'];
                  $hodometro=$row['hodometro'];
                  $matcond = $row['matcond'];
                  $idlocador = $row['idlocador'];
                  $dtdevolucaoloc = $row['dtdevolucaoloc'];
                  $uf = $row['uf'];
                  //$basegestao = utf8_encode($row['basegestao']);
                  $basegestao = $row['basegestao'];
                  $statusvel = $row['statusvel'];

                  $sql1="SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond';";
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                    /*$nomecond = utf8_encode($row1['nome']);
                    $ccusto = utf8_encode($row1['ccusto']);*/
                    $nomecond = $row1['nome'];
                    $ccusto = $row1['ccusto'];

                  /*$sql1a = "SELECT max(idtbpedidostec) as idtbpedidostec FROM bdfrota.tbpedidostec WHERE matricula='$matcond';";
                  $resultado1a = mysqli_query($conexao, $sql1a) or die(mysqli_error($conexao));
                  $row1a = mysqli_fetch_array($resultado1a, MYSQLI_BOTH);
                    $idtbpedidostec = utf8_encode($row1a['idtbpedidostec']);

                  $sql1b = "SELECT kmhodometro FROM bdfrota.tbpedidostec WHERE idtbpedidostec='$idtbpedidostec';";
                  $resultado1b = mysqli_query($conexao, $sql1b) or die(mysqli_error($conexao));
                  $row1b = mysqli_fetch_array($resultado1b, MYSQLI_BOTH);
                    $hodometro = utf8_encode($row1b['kmhodometro']);*/


                  $sql2="SELECT max(idtbmanprev) as idtbmanprev FROM bdfrota.tbmanprev WHERE placa='$placa' AND tipo='MP' ";
                  $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $idtbmanprev = $row2['idtbmanprev'];

                  $sql3 = "SELECT dataocorrencia,hodometro,observacao, status FROM bdfrota.tbmanprev WHERE idtbmanprev='$idtbmanprev' ";
                  $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
                  $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                    $dataocorrencia = $row3['dataocorrencia'];
                    $hodometroman = $row3['hodometro'];
                    //$obsultman = utf8_encode($row3['observacao']);
                    $obsultman = $row3['observacao'];
                    $statusman = $row3['status'];
                    

                  $sql4="SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
                  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
                  $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                    //$fantasia = utf8_encode($row4['fantasia']);
                    $fantasia = $row4['fantasia'];

                  $sql5 = "SELECT * FROM bdfrota.tbmanprev WHERE placa='$placa' and tipo='MP' AND status='ABERTO' ORDER BY dataagendamento DESC LIMIT 1;";
                  $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
                  $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                    $dataagendamento = $row5['dataagendamento'];

                  $sql6="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusvel';";
                  $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
                  $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                    //$statusvel = utf8_encode($row6['status']);
                    $statusvel = $row6['status'];

                  $dif = $hodometro - $hodometroman;

                  if($statusman <> 'ABERTO'){

                    if($dif>=11000){
                      $status="PERDEU A MANUTENÇÃO";
                      $style = "style='background-color: red;' ";
                    }elseif($dif<11000 && $dif>=9000){
                      $status="ENVIAR PARA MANUTENÇÃO PREVENTIVA";
                      $style = "style='background-color: yellow;' ";
                    }elseif($dif<9000){
                      $status="EM INTERVALO";
                      $style = "style='background-color: green;' ";
                    }

                  }else{
                    
                    $status="AGENDADA";
                    $style = "style='background-color: gray;' ";
                  
                  }

                  if(!empty($dtdevolucaoloc)){
                    $dtdevloc1 = explode(" ", $dtdevolucaoloc);
                    $dtdevloc2 = $dtdevloc1[0];
                    $dtdevloc3 = explode("-", $dtdevloc2);
                    $dtdevlocf = $dtdevloc3[2]."/".$dtdevloc3[1]."/".$dtdevloc3[0];
                  }else{
                    $dtdevlocf = "";
                  }
                  
                  if(!empty($dataocorrencia)){
                    $dtocor1 = explode(" ", $dataocorrencia);
                    $dtocor2 = $dtocor1[0];
                    $dtocor3 = explode("-", $dtocor2);
                    $dtocorf = $dtocor3[2]."/".$dtocor3[1]."/".$dtocor3[0];
                  }else{
                    $dtocorf = "";
                  }

                  if(!empty($dataagendamento)){
                    $dtagend1 = explode(" ", $dataagendamento);
                    $dtagend2 = $dtagend1[0];
                    $dtagend3 = explode("-", $dtagend2);
                    $dtagendf = $dtagend3[2]."/".$dtagend3[1]."/".$dtagend3[0];
                  }else{
                    $dtagendf = "";
                  }
                  

                  if($perfil==4 && empty($npode)){
                    $infoplaca="
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/dadosplaca.php' target='_blank'> 
                      
                      <input type='hidden' name='placa' value='$placa'/> 
                      <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                    </form>";

                    $acao= "
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/cadastromanpreventiva.php' target='_blank'>

                      <input type='hidden' name='placa' id='nome' value='$placa'>
                      <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>

                      <button style='border: none; background-color: transparent;' type='submit' title='Cadastrar Manutenção'> <span class='material-symbols-outlined'> edit_note </span> </button>
                    </form>";

                  }else{
                    $infoplaca=" ";

                    $acao= "";

                  }                

                  print"
                  <tr $style>                  
                    <td> $placa$infoplaca </td>                  
                    <td> $nomecond </td>
                    <td> $ccusto </td>
                    <td> $dtocorf </td>
                    <td> $hodometro km</td>
                    <td> $hodometroman km</td>
                    <td> $dif km</td>
                    <td> $fantasia </td>
                    <td> $dtdevlocf </td>
                    <td> $status </td>                             

                    <td style='padding-left: 55px'>$acao</td>
                    <td>$dtagendf</td>
                    <td>$obsultman</td>

                    <td>$uf</td>
                    <td>$basegestao</td>
                    <td>$statusvel</td>
                  </tr>
                  ";
                }
              ?>
              </tbody>             
            </table>
          </div>

          <!-- div listar veículos cadastrados -->
          <!-- div listar veículos cadastrados -->
          <div class="mt-5 text-rigth d-flex justify-content-end">
            <!--<a class="btn btn-secondary text-rigth" href="./listagemveiculos.php"> Veículos Cadastrados </a>-->
            <form method="post" action='https://ffa.painel-telecom.com:4545/frotas/gestao/control/gerarexcelfarol.php'>
              <?php print"<input type='hidden' name='matricula' value='$matricula'>";?>
              <button class='btn btn-success' type='submit'>Gerar Excel</button>
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

    <!-- jquery - js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>

    <!-- datatables - js -->
    <script type="text/javascript" src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>

    <!-- fontawesome - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap 5.1.3 - js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script type="text/javascript">
      var width = $(window).width();

      var tabelaFarol = new DataTable('#tabela1', {
      //dom: 'Bfrtip',
      language: {
        decimal:        "",
        
        emptyTable:     "Não há dados disponíveis na tabela",
        info:           "Mostrando _START_ a _END_ de _TOTAL_ entradas",
        infoEmpty:      "Mostrando 0 a 0 de 0 entradas",
        infoFiltered:   "(filtrado de _MAX_ entradas totais)",
        infoPostFix:    "",
        thousands:      ".",
        lengthMenu:     "Mostrar _MENU_ entradas",
        loadingRecords: "Carregando...",
        processing:     "",
        search:         "Buscar:",
        zeroRecords:    "Nenhum registro correspondente encontrado",
        paginate: {
          first:      "Primeiro",
          last:       "Último",
          next:       "Próximo",
          previous:   "Anterior"
        }
      },    
    });

      function popularFiltroFarol($select) {
        var coluna = tabelaFarol.column(Number($select.data('column')));
        var valores = [];
        coluna.data().each(function(valor) {
          var texto = $('<div>').html(valor).text().trim();
          if (texto !== '' && valores.indexOf(texto) === -1) {
            valores.push(texto);
          }
        });
        valores.sort(function(a, b) { return a.localeCompare(b, 'pt-BR'); });
        valores.forEach(function(valor) {
          $select.append($('<option>', { value: valor, text: valor }));
        });
      }

      $('.filtro-farol').each(function() {
        var $select = $(this);
        popularFiltroFarol($select);
        $select.on('change', function() {
          var valor = $.fn.dataTable.util.escapeRegex($(this).val());
          tabelaFarol.column(Number($(this).data('column'))).search(valor ? '^' + valor + '$' : '', true, false).draw();
        });
      });

      //new DataTable('#tabela1');

      /**if(width<=768){
        $(document).ready(function(){
          $('#tabela1').DataTable({
            responsive: true,
          });
        });
        //console.log(width);
      }else{
        $(document).ready(function(){
          $('#tabela1').DataTable({
          order: [[8, 'asc']],

            "columns": [
              null,
              null,
              null,
              { type: 'date-eu', targets: 0 },
              null,            
              null,
              null,
              null,
              { type: 'date-eu', targets: 0 },
              null,
              null,
              { type: 'date-eu', targets: 0 },//colocar data
              null, 
              null,
              null,
              null
            ],
          
          });
        });

        console.log(width);
        console.log('2');
      }*/
        
    </script>

  </body>
</html>