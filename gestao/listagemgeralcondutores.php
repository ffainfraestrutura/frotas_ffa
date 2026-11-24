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
  } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '11'){
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
    $unidadef = '';
  }

  if($matricula == '160030'||$matricula == '501285'||$matricula == '410039' || $matricula =='411425' || $matricula == '003931'){
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

    <title> Listagem Geral de Condutores </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
    <!--data tables - css e js-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />
    
    <!-- estilo local -->
    <style type="text/css">
      body, .btn, .form-select{
        font-size: 12px;
      }
    </style>

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "../menu.php";  ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 100%;">
          <h2 class="h2 pt-2 pb-2"> Listagem Geral de Condutores </h2>

          <p style="font-style: italic">Aviso: Esta tela inicia sem resultados. Para visualizar os condutores, selecione uma unidade e clique em Filtrar.</p>
          <p style="font-style: italic;">Visando a unificação dos processos, a associação/dessassociação do veículo a/de condutor deve ser realizada através um checklist (vistoria).</p>

          <!--div filtro-->
          <div class="d-flex justify-content-start mb-4 pb-4 border-bottom border-dark">
            <form action="./listagemgeralcondutores.php" method="POST">
              <div class='d-flex align-items-end'>
                <div class='col-12'>
                  <label class="form-label">Selecione unidade:</label>
                  <select class="form-select" name='unidade'>
                    <option value="" <?=($unidadef == '')?'selected':''?> >Selecione</option>
                    <option value="RJ" <?=($unidadef == 'RJ')?'selected':''?> >RJ</option>                
                    <option value="PR" <?=($unidadef == 'PR')?'selected':''?> >PR</option>
                    <option value="SP" <?=($unidadef == 'SP')?'selected':''?> >SP</option>
                    <option value="MG" <?=($unidadef == 'MG')?'selected':''?> >MG</option>
                    <option value="ES" <?=($unidadef == 'ES')?'selected':''?> >ES</option>
                    <option value="TODOS" <?=($unidadef == 'TODOS')?'selected':''?> >TODOS</option>
                  </select>
                </div>

                <div class='ms-2'>
                  <button type='submit' class="mt-1 btn btn-success">Filtrar</button>
                </div>                
              </div>
            </form>          
          </div>

          <!-- div cadastrar novo fornecedor-->
          <?php 
          /*if($perfil==4){
            print"
            <div class='mt-1 text-rigth '>         
            
            <div class='mt-1 text-rigth d-flex justify-content-between'>
              <!--<a class='btn btn-primary text-rigth' href='./importcondutor.php'> Importar Condutores em Lote </a>-->

              <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/importcnh.php' method='post' target='_blank'>
                <input type='hidden' name='matr_autor' value='$matricula'>
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
          }*/
          if($perfil==4 && empty($npode)){
            print"
            <div class='mt-1 text-rigth'>
              <div class='mt-1 text-rigth d-flex justify-content-start'>
                <!--<a class='btn btn-primary text-rigth' href='./importcondutor.php'> Importar Condutores em Lote </a>-->

                <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/importcnh.php' method='post' target='_blank'>              
                  <input type='hidden' name='matr_autor' value='$matricula'>
                  <input type='hidden' name='perfil_autor' value='$perfil'>             
                  <button class='btn btn-primary text-rigth'> Importar CNHs em Lote </button>
                </form>

                <!--<a class='btn btn-success text-rigth' href='./listagemfuncionarios2.php'> Cadastrar Nova CNH</a>-->
                <a class='btn btn-success text-rigth ms-3' target='_blank' href='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemfuncionarios2.php'> Cadastrar Nova CNH</a>

                <a class='btn btn-secondary text-rigth ms-3' target='_blank' href='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemcnh.php'>Listagens de CNHs Cadastradas</a>

                <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/control/gerarexceltodoscond.php' target='_blank' method='POST' class='ms-3'>
                  <input type='hidden' name='unidadef' value='$unidadef'>
                  <button type='submit' class='btn btn-success text-rigth'>
                    Excel Todos Condutores
                  </button>
                </form>
              </div>

              <!-- div cadastrar novo-->
              <!--<div class='mt-1 text-rigth d-flex justify-content-end'>
                <!--<a class='btn btn-primary text-rigth' href='./listagemcnh.php'> Cadastrar Novo Condutor</a>
                <form action='https://ffa.painel-telecom.com:4545/frotas/gestao/listagemcnh.php' method='post' target='_blank'>
                  
                    <input type='hidden' name='matr_autor' value='$matricula'>
                    <input type='hidden' name='perfil_autor' value='$perfil'>              
                  <button class='btn btn-primary text-rigth'> Cadastrar Novo Condutor </button>
                </form>
              </div>-->
            </div>";
          }
          ?>

          <!--div listagem geral de condutores-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <?php if (empty($unidadef)): ?>
              <div class="alert alert-info">Selecione uma unidade e clique em Filtrar para carregar os dados.</div>
            <?php endif; ?>

            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Matrícula </th>
                  <th>Nome </th>
                  <th>Situação do Funcionário </th>
                  <th>Centro de Custo </th>                
                  <th>Status como condutor</th>
                  <th>Placa</th>
                  <th>Data de associação</th>
                  <th>Data dissociação</th>
                  <!--<th>Associar/Desvincular veículo </th>-->
                  <th>Cadastrar/Editar CNH </th>
                  <th>Anexar documentos </th>
                  <th>Atualizado em </th>
                  <th>Atualizado por </th>

                </tr>
              </thead>
              <tbody>
              <?php
                //$sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE matricula<>'003535';";WHERE matricula<>'003535';            

                if (!empty($unidadef)) {
                  if($unidadef ==='TODOS'){
                    $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE placaassoc<>'ABC1234' AND matricula<>'003535' AND matricula<>'004115' AND placaassoc<>'ABC1245' /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/;";

                  }else{
                    $sql="SELECT DISTINCT(matricula) FROM bdfrota.tbcondutor WHERE matricula<>'003535' AND matricula<>'004115' AND placaassoc<>'ABC1234' AND placaassoc<>'ABC1245'  AND placaassoc IN (SELECT placa FROM bdfrota.tbveiculo WHERE unidade='$unidadef')  /*AND matricula IN (SELECT matricula FROM bdaniel.tbfuncionario WHERE status<>'Demitido')*/; ";
                  }

                  //print $sql;
                  
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

                      $nomecond = $row4['nome'];
                      $statusfunc = $row4['status'];
                      $statusfunc = ($statusfunc == 4) ? 'Afastado' : $statusfunc;
                      $ccusto = $row4['ccusto'];
                  //}

                    $sql3 = "SELECT matricula FROM bdfrota.tbcnh WHERE matricula='$matcond';";
                    $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
                    $lin3 = mysqli_num_rows($resultado3);

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
                  if($dataassoc == '0000-00-00 00:00:00'){
                    $datafim = "00/00/0000";
                  }else{
                    $dataassoc1 = new DateTime($dataassoc);
                    $datafim = $dataassoc1-> format('d/m/Y');
                  }           
                  
                  if($datadissoc == '0000-00-00 00:00:00'){
                    $datadfim = "00/00/0000";
                  }else{
                    $datadissoc1 = new DateTime($datadissoc);
                    $datadfim = $datadissoc1-> format('d/m/Y');
                  }

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
                      $autor1 = $row7['nome'];
                  }

                  if(!empty($datamov)){                  
                    $datamov1 = new DateTime($datamov);
                    $datamovf = $datamov1-> format('d/m/Y');
                  }else{
                    $datamovf = "";
                  }

                  //tratando ativo
                  if($ativo == 1){
                    $ativo1='Ativo';
                  } elseif($ativo==0){
                    $ativo1= 'Não ativo';
                  } else {
                    $ativo1= 'Não ativo';
                  }

                  if($perfil==4 && empty($npode)){
                    if($lin3>0){
                      $editarcnh="
                      <td class='text-center'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/editarcnh.php' target='_blank'>
                          <input type='hidden' name='matricula' id='id' value='$matcond'>
                          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>
                          <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Editar CNH'> <span class='material-symbols-outlined'> edit_note </span> </button>
                        </form>  
                      </td>";
                    }else{
                      $editarcnh="
                        <td class='text-center'> 
                          <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/cadastrocnh.php' target='_blank'>
                            <input type='hidden' name='matricula' id='id' value='$matcond'>
                            <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>
                            <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                            <button style='border: none; background-color: transparent;' type='submit' title='Cadastrar CNH'> <span class='material-symbols-outlined'> edit_note </span> </button>
                          </form>  
                        </td>";
                    }
                    
                    if($lin3>0){
                      $anexardocs="
                      <td class='text-center'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/enviardocscond.php' target='_blank'>
                          <input type='hidden' name='matcondutor' value='$matcond'>
                          <input type='hidden' name='matr_autor' value='$matricula'>
                          <input type='hidden' name='perf_autor' value='$perfil'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Anexar documentos'> <span class='material-symbols-outlined'>attach_file </span> </button>                  
                        </form>  
                      </td>";
                    }else{
                      $anexardocs="
                      <td class='text-center'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/enviardocscond.php' target='_blank'>
                          <input type='hidden' name='matcondutor' value='$matcond'>
                          <input type='hidden' name='matr_autor' value='$matricula'>
                          <input type='hidden' name='perf_autor' value='$perfil'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Anexar documentos'> <span class='material-symbols-outlined'>attach_file </span> </button>                  
                        </form>  
                      </td>";
                    }

                    

                    if($ativo == 1){
                      $acao="
                      <td style='padding-left: 78px;'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/desvincularveiculo.php' target='_blank'>
                          <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>
                          <input type='hidden' name='perfil' id='matr_autor' value='$perfil'>
                          <button style='border: none; background-color: transparent;' type='submit' title='Desvincular veículo'> <span class='material-symbols-outlined'> link_off </span> 
                          </button>
                        </form>  
                      </td>";
                    } elseif($ativo == 0){
                      $acao="
                      <td style='padding-left: 78px;'> 
                        <form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/associarveiculo.php' target='_blank'>
                          <input type='hidden' name='idtbcondutor' id='id' value='$idtbcondutor'>
                          <input type='hidden' name='matcondutor' id='id' value='$matcond'>
                          <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula'>
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

                  if($matcond != '' && $nomecond !=''){

                    print "
                    <tr>
                      <td> $matcond </td>
                      <td> $nomecond $infocond</td>
                      <td> $statusfunc </td>
                      <td> $ccusto </td>
                      <td> $ativo1 </td>
                      <td> $placaassoc$infoplaca </td>
                      <td> $datafim </td>
                      <td> $datadfim </td>
      
                      $editarcnh
                      $anexardocs
                      <td title='$title'>$datamovf</td>
                      <td> $autor1 </td>
                    </tr>";
                  }//$acao
                                    
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

    <!-- fontawesome js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- jquery - js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.0.js'></script>

    <!-- datatables -->
    <script type="text/javascript" src='https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js'></script>  

    <!-- Moment.js: -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>  
    <!-- Brazilian locale file for moment.js-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/pt-br.js"></script>    
    <!-- Ultimate date sorting plug-in-->
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>

    <!-- datatatble sorting -->
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>

    <script src="../src/js/scripts.js"></script>

    <script type="text/javascript">
      var width = $(window).width(); 
      
      DataTable.datetime('DD/MM/YYYY');
      DataTable.datetime('DD/MM/YYYY');

      new DataTable('#tabela1', {
        order: [[1, 'asc']],
        
        "columns": [
          null,//matcond
          null,//nomecond
          null,//situação funci
          null, //ccusto      
          null,//statuscond
          null,//placa
          { "type": "date-eu" },//dtassoc
          { "type": "date-eu" },//dt dissoc
          //null,//ação
          null,//cad cnh
          null,// anexar doc
          { "type": "date-eu" },//atualizado em 
          null   //atualizado por
        ],

        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
        },
      });
      
    </script>

  </body>
</html>