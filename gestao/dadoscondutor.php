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

  //echo"<script language='javascript' type='text/javascript'>alert('PERFIL $perfil'); </script>";

  if($perfil == ''){
    
    $perfil=$_POST['perfil'];
    $matricula = $_POST['matr_autor'];

  }

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso'); window.close();</script>";
  } elseif ($perfil <> '4' && $perfil <>'5'){
    
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.close();</script>";
      
  }*/

  /*window.location=\"index.php\"*/
  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  include"../conecta03.php";
  include_once"./func/log3.php";
  header("Content-type: text/html; charset=utf-8");

  $matriculacond=$_POST['matcond'];

?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

    <title> Portal FFA </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

    <!-- estilo próprio -->
    <link href="../src/css/styles.css" rel="stylesheet" />
    
    <!-- estilo local -->
    <style type="text/css">
      body, .form-control, .btn{
        font-size: 12px;
      }
    </style>

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h2>Dados do Condutor</h2>
          
          <!-- div do formulario -->
          <div>
            <?php
              $sql = "SELECT nome, matricula, cargo, status,ccusto, endereco, bairro, cidade, cep, cpf, rg, codempresa, codfilial, dtnasc, dtadmissao, dtdemissao FROM bdaniel.tbfuncionario WHERE matricula = '$matriculacond';";

              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
              $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                $nomecond = $row['nome'];
                //$matricula = $row['matricula'];
                $cargo = $row['cargo'];               
                $status = $row['status'];
                $ccusto = $row['ccusto'];
                $endereco = $row['endereco'];
                $bairro = $row['bairro'];
                $cidade = $row['cidade'];
                $cep = $row['cep'];               
                $cpf = $row['cpf'];
                $rg = $row['rg'];
                $codempresa= $row['codempresa'];
                $codfilial = $row['codfilial'];
                $dtnasc = $row['dtnasc'];
                $dtadmissao = $row['dtadmissao'];
                $dtdemissao = $row['dtdemissao'];

              $sql2="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial';";
              $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
              $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
                $filial = strtoupper($row2['nome']);

              $sql3 = "SELECT numcnh, validade, categoria, doc1, doc2 FROM bdfrota.tbcnh WHERE matricula = '$matriculacond'; ";
              $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
              $row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC);
                $numcnh = $row3['numcnh'];
                $validade = $row3['validade'];
                $categoria = $row3['categoria'];
                $doc1 = $row3['doc1'];
                $doc2 = $row3['doc2'];

                $validade1 = explode(" ", $validade);

              $sql4 = "SELECT email, telefone FROM bdfrota.tbusuario WHERE matricula = '$matriculacond'; ";
              $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
              $row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC);
                $emailcond = $row4['email'];
                $telcond = $row4['telefone'];

              $sql5 = "SELECT placa FROM bdfrota.tbveiculo WHERE matcond = '$matriculacond'; ";
              $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
              $row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
                $placaassoc = $row5['placa'];

              print "              
                <input type='hidden' name='matr_autor' value='$matricula1'>
                <!--div nome e matricula-->
                <div class='col-sm-12 d-flex justify-content-start'>
                  <!-- div nome -->
                  <div class='d-flex flex-column col-sm-5'>
                    <label class='form-label mt-2 me-4' for='nome'>Nome:</label>
                    <input class='form-control'  type='text' name='nome' id='nome' value='$nomecond' readonly>
                  </div>

                  <!-- div dtnasc -->
                  <div class='ms-1 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2 me-4' for='dtnasc'>Data de Nasc.:</label>
                    <input class='form-control'  type='date' name='dtnasc' id='dtnasc' value='$dtnasc' readonly>
                  </div>

                  <!-- div matricula -->
                  <div class='ms-3 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2' for='matricula'>Matrícula: </label>
                    <input class='form-control'  type='text' name='matricula' id='matricula'  value='$matriculacond' readonly>
                  </div>

                  <!-- div status -->
                  <div class='ms-3 d-flex flex-column col-sm-2'>
                    <label class='form-label mt-2' for='status'>Status: </label>
                    <input class='form-control'  type='text' name='status' id='status'  value='$status' readonly>
                  </div>
                </div>

                <!-- div data admissao/dta demissao -->
                <div class='col-sm-12 d-flex justify-content-start'>
                  <div class='d-flex flex-column col-sm-3'>
                    <label class='form-label mt-2' for='dtadmissao'>Data de admissão: </label>
                    <input class='form-control'  type='date' name='dtadmissao' id='dtadmissao'  value='$dtadmissao' readonly>
                  </div>";

            
                if($status == 'Demitido'){
                  print"
                    <div class='ms-3 d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='dtdemissao'>Data de demissão: </label>
                      <input class='form-control'  type='date' name='dtdemissao' id='dtdemissao'  value='$dtdemissao' readonly>
                    </div>";
                }

                print"
                  </div>

                  <!-- div cargo filial status centro custo -->
                  <div class='col-sm-12 d-flex justify-content-start'>
                    
                    <!-- cargo -->
                    <div class='d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='cargo'>Cargo:</label>
                      <input class='form-control' type='text' name='cargo' id='cargo' readonly value='$cargo'>
                    </div>

                    <!-- filial -->
                    <div class='ms-3 d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='filial'>Filial: </label>
                      <input class='form-control' type='text' name='filial' id='filial' style='width: 94%;' readonly value='$filial'>
                    </div>

                    <!-- centro custo --> 
                    <div class='ms-1 d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='ccusto'>Centro de custo:</label>
                      <input class='form-control' type='text' name='ccusto' id='ccusto' value = '$ccusto' readonly>
                    </div>

                  </div>            

                  <!-- div cpf rg -->
                  <div class='col-sm-12 d-flex justify-content-start'>
                    
                    <!-- cpf -->
                    <div class='d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='cpf'>CPF:</label>
                      <input class='form-control' type='text' name='cpf' id='cpf' readonly value='$cpf'>
                    </div>

                    <!-- rg -->
                    <div class='ms-3 d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='rg'>RG: </label>
                      <input class='form-control' type='text' name='rg' id='rg' style='width: 94%;' readonly value='$rg'>
                    </div>

                  </div>

                  <div class='col-sm-12 d-flex justify-content-start'>

                    <!-- telefone -->
                    <div class='d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='telefone'>Telefone: </label>
                      <input class='form-control' type='text' name='telefone' id='telefone' readonly value='$telcond'>
                    </div>

                    <!-- email -->
                    <div class='ms-3 d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='email'>Email: </label>
                      <input class='form-control' type='text' name='email' id='email' readonly value='$emailcond'>
                    </div>
                  </div>

                  <!-- div cnh validadecnh categoriacnh -->
                  <div class='col-sm-12 d-flex justify-content-start align-items-end'>
                    
                    <!-- cnh -->
                    <div class='d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='cnh'>CNH:</label>
                      <input class='form-control' type='text' name='cnh' id='cnh' readonly value='$numcnh'>
                    </div>

                    <!-- validadecnh -->
                    <div class='ms-3 d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='validadecnh'>Validade CNH: </label>
                      <input class='form-control' type='date' name='validadecnh' id='validadecnh' style='width: 94%;' readonly value='$validade1[0]'>
                    </div>

                    <!-- categoriacnh --> 
                    <div class='ms-1 d-flex flex-column col-sm-2'>
                      <label class='form-label mt-2' for='categoriacnh'>Categoria CNH:</label>
                      <input class='form-control' type='text' name='categoriacnh' id='categoriacnh' value = '$categoria' readonly>
                    </div>";

                
                if(!empty($doc2)){
                  //$href = $doc2;
                  $href = 'https://ffa.painel-telecom.com:4545/frotas/gestao'.$doc2;
                }else{
                  if(!empty($doc1)){
                    //$href = $doc1;
                    $href = 'https://ffa.painel-telecom.com:4545/frotas/gestao'.$doc1;
                    
                  }
                }

                if(!empty($href)){
                  print"
                    <div class='ms-2 d-flex flex-column col-sm-1'>
                      <a href='$href' target='_blank' class='btn  btn-secondary' title='Baixar Último Arquivo'><span class='material-symbols-outlined'>visibility</span></a>                      
                    </div>";
                }else{
                  print"
                    <div class='ms-2 d-flex flex-column col-sm-2'>
                      
                      <form action='./editarcnh.php' method='post' target='_blank'>
                        <input type='hidden' name='matricula' value='$matriculacond'>
                        <input type='hidden' name='perfil' value='$perfil'>
                        <input type='hidden' name='matr_autor' value='$matricula1'>
                        <button type='submit' class='btn  btn-secondary'>Editar CNH/Enviar Arquivo</button>
                      </form>
                                            
                    </div>";
                }
            
                print"
                  </div>
                  
                  <!--div endereço-->
                  <div class='col-sm-12 d-flex justify-content-start'>
                    <!-- div endereço -->
                    <div class='d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2 me-4' for='endereco'>Endereço:</label>
                      <input class='form-control'  type='text' name='endereco' id='endereco' value='$endereco' readonly>
                    </div>

                    <!-- div bairro -->
                    <div class='ms-3 d-flex flex-column col-sm-3'>
                      <label class='form-label mt-2' for='bairro'>Bairro: </label>
                      <input class='form-control'  type='text' name='bairro' id='bairro'  value='$bairro' readonly>
                    </div>

                    <!-- div cidade -->
                    <div class='ms-3 d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2' for='cidade'>Cidade: </label>
                      <input class='form-control'  type='text' name='cidade' id='cidade'  value='$cidade' readonly>
                    </div>
                  </div>

                  <div class='mb-2 col-sm-12 d-flex justify-content-start'>
                    <!-- div CEP -->
                    <div class='d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2 me-4' for='cep'>CEP</label>
                      <input class='form-control'  type='text' name='cep' id='cep' value='$cep' readonly>
                    </div>

                    <!-- div placa associada -->
                    <div class='ms-2 d-flex flex-column col-sm-4'>
                      <label class='form-label mt-2 me-4' for='placaassoc'>Placa associada:</label>
                      <input class='form-control'  type='text' name='placaassoc' id='placaassoc' value='$placaassoc' readonly>
                    </div>
                  </div>"; 
            ?>

          <div class="mt-3 mb-2 col-md-12 d-flex justify-content-around">
            <div class="col-6">
              <form action='./historicocondchecklist.php' method='post' target='_blank'>
                <?php print"
                <input type='hidden' name='matcond' value='$matriculacond'>
                <input type='hidden' name='perfil' value='$perfil'>
                <input type='hidden' name='matr_autor' value='$matricula'>
                "; ?>
                <button type="submit" class="btn btn-success">Histórico de Vistorias</button>
              </form>
            </div>

            <div class="col-6">
              <form action='./historicocondutor.php' method='post' target='_blank'>
                <?php print"<input type='hidden' name='matcond' value='$matriculacond'>"; ?>
                <button type="submit" class="btn btn-secondary">Histórico de Placas</button>
              </form>
              
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

    <!-- jquery-3.5.1 - js  -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap - 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- datatables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <!--<script type="text/javascript" src="DataTables/datatables.min.js"></script> -->
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
    
    <!-- script próprio -->
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

    <!-- script local -->
    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {
        

      });
    </script>
  </body>
</html>