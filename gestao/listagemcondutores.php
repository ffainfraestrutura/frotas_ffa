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

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão.');window.location=\"../index.php\"</script>";  
  } /*/elseif ($perfil <> '5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']= $tipo;

  include"../conecta2.php";
  //include"../conecta.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $sqla = "SELECT estado FROM bdaniel.tbfuncionario WHERE matricula='$matricula1' and status <> 'Demitido';";
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
    }else{
      $unidadef = 'TODOS';
    }
  }

  if($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1 =='411425' || $matricula1 == '003931'){
    $npode = "não pode";
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

    <title> Condutores </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- estilo próprio -->
    <link href="../src/css/styles.css" rel="stylesheet" /> 

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

      <main class="mb-2" style="width: 100%;">

        <div class="container-fluid px-4" style="width: 100%;">
          <h1 class="h1 pt-2 pb-2"> Condutores Ativos </h1>

          <p style="font-style: italic; font-size:14px;">Aviso: Para otimizar o carregamento, nesta página estarão listados apenas os condutores com veículos com unidade em seu estado. Para visualizar todos os condutores ativos, filtre por 'Todos'.</p>
          <p style="font-style: italic; font-size:14px;">Visando a unificação dos processos, a associação/dessassociação do veículo a/de condutor deve ser realizada através um checklist (vistoria).</p>

          <!--div filtro-->
          <div class="d-flex justify-content-start mb-4 pb-4 border-bottom border-dark">
            <form action="./listagemcondutores.php" method="POST">
              <div class="d-flex align-items-end">
                <div>
                  <label class="form-label">Selecione unidade:</label>
                  <select class="form-select" name='unidade'>
                    <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
                    <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
                    <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
                    <option value="MG" <?=($unidadef == 'MG')?'selected':''?> >MG</option>
                    <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
                  </select>
                </div>
                
                <div class='ms-2'>
                  <button type='submit' class="mt-1 btn btn-success">Filtrar</button>
                </div>
              </div>
            </form>            
          </div>

          <!-- div cadastrar novo condutor-->
          <?php
          if($perfil==4 && empty($npode)){
          print"
          <div class='mt-1 text-rigth'>
            <div class='mt-1 text-rigth d-flex justify-content-start'>
              <!--<a class='btn btn-primary text-rigth' href='./importcondutor.php'> Importar Condutores em Lote </a>-->

              <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/importcnh.php' method='post' target='_blank'>              
                <input type='hidden' name='matr_autor' value='$matricula1'>
                <input type='hidden' name='perfil_autor' value='$perfil'>             
                <button class='btn btn-primary text-rigth'> Importar CNHs em Lote </button>
              </form>

              <!--<a class='btn btn-success text-rigth' href='./listagemfuncionarios2.php'> Cadastrar Nova CNH</a>-->
              <!--<a class='btn btn-success text-rigth' target='_blank' href='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemfuncionarios2.php'> Cadastrar Nova CNH</a>-->

              <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemfuncionarios2.php' method='post' target='_blank'>              
                <input type='hidden' name='matr_autor' value='$matricula1'>
                <input type='hidden' name='perfil_autor' value='$perfil'>             
                <button class='btn btn-success text-rigth ms-3'> Cadastrar Nova CNH </button>
              </form>

              <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemcnh.php' method='post' target='_blank'>              
                <input type='hidden' name='matr_autor' value='$matricula1'>
                <input type='hidden' name='perfil_autor' value='$perfil'>             
                <button class='btn btn-secondary text-rigth ms-3'> Listagens de CNHs Cadastradas </button>
              </form>
            </div>

            <!-- div cadastrar novo-->
            <!--<div class='mt-1 text-rigth d-flex justify-content-end'>
              <!--<a class='btn btn-primary text-rigth' href='./listagemcnh.php'> Cadastrar Novo Condutor</a>
              <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemcnh.php' method='post' target='_blank'>
                
                  <input type='hidden' name='matr_autor' value='$matricula1'>
                  <input type='hidden' name='perfil_autor' value='$perfil'>              
                <button class='btn btn-primary text-rigth'> Cadastrar Novo Condutor </button>
              </form>
            </div>-->
          </div>";
        }
        ?>

          <!--div listagem condutores-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Matrícula</th>
                  <th>Nome</th>
                  <th>Placa</th>
                  <th>Status Aniel</th>
                  <th>Associado em</th>
                  <th>Venc. CNH</th>
                  <th>Status CNH</th>              
                  <!--<th>Editar condutor</th>-->
                  <th>Centro de Custo</th>
                  <!--<th>Desvincular</th>-->

                  <th>Atualizado em </th>
                  <th>Última atualização </th>
                  <th>Realizada por </th>               
                </tr>
              </thead>

              <tbody>
              <?php              

                if($unidadef ==='TODOS'){
                  $sql="SELECT * FROM bdfrota.tbcondutor WHERE ativo=1 AND matricula<>'003535';";
                }else{
                  $sql="SELECT * FROM bdfrota.tbcondutor WHERE ativo=1 AND matricula<>'003535' AND placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef'); ";
                }
                
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                  $idtbcondutor = $row['idtbcondutor'];
                  //$nomecond = utf8_encode($row['nome']); 
                  $nomecond = $row['nome'];                
                  $matricula = $row['matricula'];
                  $dataassoc = $row['dataassoc'];
                  //$datadissoc = $row['datadissoc'];
                  $placaassoc = $row['placaassoc'];

                  $sql2="SELECT validade FROM bdfrota.tbcnh WHERE matricula='$matricula' ";
                  $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $validadeCNH = $row2['validade'];

                  $sql5="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE matricula='$matricula' AND (tipo='checklist' OR tipo='cadastro' OR tipo='edição'); ";
                  
                  //$sql5="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE (matricula='$matricula' OR mat_autor='$matricula') AND (tipo='checklist' OR tipo='cadastro' OR tipo='edição'); ";
                  $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
                  $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                    $idtblog1=$row5['idtblog'];

                  $sql6="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog1' ";
                  $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
                  $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                    //$acao1= utf8_encode($row6['acao']);
                    $acao1= $row6['acao'];
                    $autor1=$row6['mat_autor'];
                    $datamov=$row6['data_e_hora'];

                  if(strpos($acao1, "-1") !== false){
                    $acao1 = str_replace("-1", "", $acao1);
                  }

                  if(strpos($acao1, "-2") !== false){
                    $acao1 = str_replace("-2", "", $acao1);
                  }

                  if(strpos($acao1, "-3") !== false){
                    $acao1 = str_replace("-3", "", $acao1);
                  }

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
                    $datamov1 = new DateTime($datamov);
                    $datamovf = $datamov1-> format('d/m/Y');
                  }else{
                    $datamovf = "";
                  }

                  //tratando data
                  $dataassoc1 = new DateTime($dataassoc);
                  $datafim = $dataassoc1-> format('d/m/Y');

                  //tratando validadeCNH
                  $validadeCNH1 = new DateTime($validadeCNH);
                  $validadefim = $validadeCNH1-> format('d/m/Y');

                  $inicio = new DateTime($hoje);
                  $validadeCNH2 = new DateTime($validadeCNH);
                  
                  $dateInterval = $inicio->diff($validadeCNH2);
                  $intervalo = $dateInterval->format('%a');

                  $periodo = date("Y-m-d H:i:s", strtotime($validadeCNH . "-30 days"));

                  if($hoje>=$validadeCNH){
                    $title = "title='Carteira vencida.' ";
                    $style = "style='color: red;'";
                    $statuscnh = 'Carteira vencida.';

                    $style2 = "style='background-color: #d98a85' ";

                  } elseif($validadeCNH>$hoje && $hoje>=$periodo) {
                    $style = "style='color: #000000;'";
                    $statuscnh ='Data de vencimento em menos de 30 dias.';

                    $style2 = "style='background-color: #f6ffc7' ";
                  }elseif($periodo>$hoje){
                    $style = "style='color: blue;'";
                    $statuscnh ='Carteira dentro do período de validade.';

                    $style2 = "style='background-color: #ffffff;' ";
                  }                

                  $sql3="SELECT status, matcond FROM bdfrota.tbveiculo WHERE placa='$placaassoc' ";
                  $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));

                  $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                    $status = $row3['status'];
                    $matcond = $row3['matcond'];

                  $sql4="SELECT ccusto, nome, status FROM bdaniel.tbfuncionario WHERE matricula='$matricula'";
                  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
                  $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                    /*$ccusto = utf8_encode($row4['ccusto']);
                    $nomefunc = utf8_encode($row4['nome']);
                    $statusani = utf8_encode($row4['status']);*/
                    $ccusto = $row4['ccusto'];
                    $nomefunc = $row4['nome'];
                    $statusani = $row4['status'];

                  if($nomecond==''){
                    $nomefinal = $nomefunc;
                  }else{
                    $nomefinal = $nomecond;
                  }

                  if($perfil==4 ){
                    /*./historicocondchecklist.php ./dadoscondutor*/
                    $infocon = "
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/dadoscondutor.php' target='_blank'> 
                      
                      <input type='hidden' name='matcond' value='$matricula'/>
                      <input type='hidden' name='perfil' value='$perfil'/> 
                      <input type='hidden' name='matr_autor' value='$matricula1'/> 
                      
                      <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Condutor'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                    </form>";

                    /*./historicoplacachecklist.php ./dadosplaca.php*/
                    $infoplaca = "
                    <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/dadosplaca.php' target='_blank'> 

                      <input type='hidden' name='placa' value='$placaassoc'/>
                      <input type='hidden' name='perfil' value='$perfil'/> 
                      <input type='hidden' name='matr_autor' value='$matricula1'/>
                      
                      <button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
                    </form>";

                    if(empty($npode)){
                      $editarcnh="
                      <td class='text-center'> 
                        <form method='post' action='./editarcnh.php' target='_blank'>
                          <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Editar condutor'> <span class='material-symbols-outlined'> edit_note </span> </button>
                        </form>  
                      </td>";
                      //style='padding-left: 65px;'
                      
                      $desvincular="<td class='text-center'> <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/desvincularveiculo.php' target='_blank'>
                          <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Desvincular Veículo'> <span class='material-symbols-outlined'> link_off </span> </button></form>  
                      </td>";
                    }else{
                      $desvincular="<td> </td>";
                    }
                  }else{
                    $desvincular = "<td style='padding-left: 65px;'> </td>";
                  }
                    

                  //if($status=='1' $matcond == $matricula && ){
                  if($matricula !=''){
                  print "
                  <tr $style2>
                    <td>$matricula</td>                  
                    <td>$nomefinal$infocon</td>
                    <td>$placaassoc$infoplaca</td>
                    <td>$statusani</td>
                    <td>$datafim</td>
                    <td $title $style>$validadefim</td>
                    <td> $statuscnh</td>
                    <td> $ccusto</td>

                    <!--<td style='padding-left: 65px;'> <form method='post' action='./editarcondutor.php' target='_blank'>
                        <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                        <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>
                        <button style='border: none; background-color: transparent;' type='submit' title='Editar condutor'> <span class='material-symbols-outlined'> edit_note </span> </button></form>  
                    </td>-->                 

                    <td>$datamovf </td>
                    <td>$acao1 </td>
                    <td>$autor1 </td>                
                  </tr>";
                  }//$desvincular
                  //}
                }
              ?>
              </tbody>
              
              <tfoot>
                <tr>
                <th>Matrícula</th>
                  <th>Nome</th>
                  <th>Placa</th>
                  <th>Status Aniel</th>
                  <th>Associado em</th>
                  <th>Venc. CNH</th>
                  <th>Status CNH</th>              
                  <!--<th>Editar condutor</th>-->
                  <th>Centro de Custo</th>
                  <!--<th>Desvincular</th>-->

                  <th>Atualizado em </th>
                  <th>Última atualização </th>
                  <th>Realizada por </th> 
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- div listar todos condutores-->
          <div class="mt-5 text-rigth d-flex justify-content-end">
            <a class="btn btn-secondary text-rigth" href="./listagemgeralcondutores.php"> Listar Todos Condutores </a>

            <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/control/gerarexcelcondativos.php' method="POST">
            <?php print"
              <input type='hidden' name='unidadef' value='$unidadef'>
            ";
            ?>
              <button type='submit'class="ms-2 btn btn-success text-rigth">
                Excel Condutores Ativos
              </button>
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

    <!-- fontawesome - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- datatable - js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <script type="text/javascript" src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js'></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script type="text/javascript">
      var width = $(window).width();

      DataTable.datetime('DD/MM/YYYY');
      new DataTable('#tabela1', {
        order: [[4, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
        },
      });

      /*if(width<=768){
        $(document).ready(function(){
          $('#tabela1').DataTable({
            responsive: true,
            order: [[3, 'asc']],

          });
        });
        //console.log(width);
      }else{
        $(document).ready(function(){
          $('#tabela1').DataTable({
            "columns": [
              null,
              null,
              null,
              { "type": "date-eu" },
              { "type": "date-eu" },
              null,
              null,
              null,
              { "type": "date-eu" },
              null,
              null,
            ],

            order: [[4, 'asc']],

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
      }*/
          
    </script>

  </body>
</html>