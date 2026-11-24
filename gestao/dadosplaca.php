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

  if($perfil==''){
    $perfil=$_POST['perfil'];
    $matricula = $_POST['matr_autor'];
  }

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
  } elseif ($perfil <> '4' && $perfil <>'5'){
    
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";    
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  include"../conecta03.php";
  include_once"../func/log3.php";
  header("Content-type: text/html; charset=utf-8");

  $placa=$_POST['placa'];
  $autoinfra = $_POST['autoinfra'];

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

    <link href="../src/css/styles.css" rel="stylesheet" />
    
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1>Dados do Veículo</h1>

          <!-- div do formulario -->
          <div >

            <?php
              $sql = "SELECT * FROM bdfrota.tbveiculo WHERE placa= '$placa';";

              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
            
              $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
                $idtbveiculo = $row['idtbveiculo'];
                $placa = $row['placa'];
                $marca = $row['marca'];
                $modelo = $row['modelo'];
                $versao = $row['versao'];
                $categoria = $row['categoria'];
                $anofabr = $row['anofabr'];
                $anomodelo = $row['anomodelo'];
                $tipo = $row['tipo'];
                $aplicacao = $row['aplicacao'];
                $zerokm = $row['zerokm'];
                $renavam = $row['renavam'];
                $chassi = $row['chassi'];
                $nummotor = $row['nummotor'];
                $uf = $row['uf'];
                $combustivel = $row['combustivel'];
                $tanque = $row['tanque'];
                $motorizacao = $row['motorizacao'];
                $cor = $row['cor'];
                $nportas = $row['nportas'];
                $npassageiros = $row['npassageiros'];
                $qtdeixos = $row['qtdeixos'];
                $qtdpneus = $row['qtdpneus'];
                $qtdestepe = $row['qtdestepe'];
                $aro = $row['aro'];
                $calibragem = $row['calibragem'];
                $velocmax = $row['velocmax'];
                $gnv = $row['gnv'];
                $gps = $row['gps'];
                $tagpedagio = $row['tagpedagio'];
                $status = $row['status'];
                $hodometro = $row['hodometro'];
                $tipoposse = $row['tipoposse'];
                $idlocador = $row['idlocador'];
                $hodometroinicial = $row['hodometroinicial'];
                $obsveiculo = $row['obsveiculo'];
                $basegestaof = $row['basegestao'];
                $ccusto = $row['ccusto'];

                $statusvel = $row['statusvel'];
                $datamovimentacao = $row['datamovimentacao'];
                  $datamovimentacao = substr($datamov, 0, 10);
                  $horamovimentacao = substr($datamov, 11, 8);
                
                $matcond = $row['matcond'];
                $oficina = $row['oficina'];
                $dtentregaa = $row['dtentrega'];
                  $dtentrega = substr($dtentregaa, 0, 10);
                $dtdevolucaoa = $row['dtdevolucao'];
                $dtdevolucao = substr($dtdevolucaoa, 0, 10);
                $tipovel = $row['tipovel'];
                $situacao = $row['situacao'];
                //$doccrlv = $row['doccrlv'];
                $airbag = $row['airbag'];
                $gpsemp = $row['gpsemp'];
                $rack = $row['rack'];
                $ncontloc = $row['ncontloc'];
                $dtdisponivelloca = $row['dtdisponivelloc'];
                $dtdisponivelloc = substr($dtdisponivelloca, 0, 10);
                $dtdevolucaoloca = $row['dtdevolucaoloc'];
                $dtdevolucaoloc = substr($dtdevolucaoloca, 0, 10);
                $valaquisicao = $row['valaquisicao'];
                $totkmmensal = $row['totkmmensal'];
                $blindagem = $row['blindagem'];
                $bo = $row['bo'];

                //valor
                $valaquisicao= str_replace(".", ",", $valaquisicao);
                $valaquisicao= str_replace(" ", ".", $valaquisicao);
                $valaquisicao = "R$ ".$valaquisicao;

                if($status == '0'){
                  $status = 'Inativo';
                }elseif($status == '1'){
                  $status = 'Ativo';
                }

              $sql1 = "SELECT marca FROM bdfrota.tbmarcaveic WHERE idtbmarcaveic='$marca'; ";
              $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));           
              $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                $marca = $row1['marca'];

              $sql2 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic='$modelo'; ";
              $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));           
              $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                $modelo = $row2['modelo'];

              $sql3 = "SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusvel'; ";
              $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));           
              $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                $statusvel = $row3['status'];

              $sql4="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE placa='$placa' AND (tipo='cadastro' OR tipo='edição' OR tipo='checklist') ";
              $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
              $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                $idtblog = $row4['idtblog'];

              $sql5="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog' ";
              $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
              $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                $acao=$row5['acao'];
                $autor=$row5['mat_autor'];
                $datamov=$row5['data_e_hora'];

                $datamov1= explode(" ", $datamov);

              if($autor=='003535' || $autor=='003427'){
                $autor = 'EQUIPE TI';
              }else{
                $sql8="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor'; ";
                $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
                $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
                  $autor = $row8['nome'];
              }

              $sql6 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matcond';";
              $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
              $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                $nomecond = $row6['nome'];

              $sql7 = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador';";
              $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
              $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
                $locadora = $row7['fantasia'];            
              
              $sql8 = "SELECT * FROM bdfrota.tbveicdocs WHERE placa='$placa';";
              $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
              $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
                $crlv = $row8['crlv'];
                $crv = $row8['crv'];
                $cert_ipva = $row8['cert_ipva'];
              
              $numrow8 = mysqli_num_rows($resultado8);

              //pegando a imagem do hodometro
              $dir = '../docs/';
              //$dir = 'http://ffasip.ddns.net:4242/frotas/docs/';

              $diretorio = dir($dir);
              $memorando = "memorando";
              $todoshod =  array();

              while($arquivo = $diretorio -> read()){
                if ($arquivo != '.' && $arquivo != '..'){
                  if(strpos($arquivo, $matcond) !== false){

                    if(strpos($arquivo,$memorando) === false){

                      $todoshod[] = $arquivo;

                    }
                  }               
                  
                }
                
              }

              $diretorio -> close();

              if(!empty($todoshod[0])){
                $href = "http://ffasip.ddns.net:4242/frotas/docs/$todoshod[0]";
              }else{
                $href = "";
              }


              
            ?>        

            <div class='row'>
            <?php print"
              <!--div status-->
              <div class='d-flex flex-column col-sm-3'>
                <label class='form-label mb-0' for='status'>Status:</label>
                <input class='form-control' type='text' name='status' id='status' value='$status' readonly>
              </div>

              <!-- div placa-->
              <div class='d-flex flex-column col-sm-3'>
                <label class='form-label mb-0' for='placa'>Placa:</label>
                <input class='form-control' type='text' name='placa' id='placa' value='$placa' readonly>
              </div>

              <!-- div uf-->
              <div class='d-flex flex-column col-sm-2'>
                <label class='form-label mb-0' for='uf'>UF Placa:</label>
                <input class='form-control' type='text' name='uf' id='placa' value='$uf' readonly>
              </div>
            
              <!-- div cor-->
              <div class='ms-2 mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='cor'>Cor:</label>
                <input class='form-control' type='text' name='cor' id='cor' placeholder='Cor do Veículo' value='$cor' readonly>
              </div>
            </div>

            <!-- div marca,modelo, versão, cor, ano fabricação, ano modelo-->
            <div class='row mb-3'>
              <!-- div marca-->
              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label mb-0' for='marca'>Marca:</label>
                <input class='form-control' type='text' name='marca' id='marca' value='$marca' readonly>
              </div>

              <!-- div modelo-->
              <div class='col-sm-3 d-flex flex-column'>
                <label class='form-label mb-0' for='modelo'>Modelo:</label>
                <input class='form-control' type='text' name='modelo' id='modelo' value='$modelo' readonly>
              </div>

              <!-- div versão-->
              <div class='d-flex flex-column col-sm-3'>
                <label class='form-label mb-0' for='versao'>Versão:</label>
                <input class='form-control' type='text' name='versao' id='versao' value='$versao' readonly>
              </div>                        
            </div>

            <!-- div Status Veículo,Tipo Veículo, Situação, Hodômetro Inicial (em Total de Km): *-->
            <!--col-md-12 d-flex justify-content-start-->
            <div class='row'>
              <!-- div Status Veículo-->
              <div class='d-flex flex-column col-sm-3'>
                <label class='form-label mb-0' for='statusvel'>Status do veículo: </label>
                <input class='form-control' type='text' name='statusvel' id='statusvel' value='$statusvel' readonly>
              </div>

              <!-- div condutor -->
              <div class='d-flex flex-column col-sm-6'>
                <label class='form-label mb-0' for='nomecond'>Nome condutor: </label>
                <input class='form-control' type='text' name='nomecond' id='nomecond' value='$nomecond' readonly>
              </div>
            </div>

            <div class='row mt-1'>
              
              <!-- div ano fabricação-->
              <div class='mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='anofabric'>Ano de fabricação:</label>
                <input class='form-control' type='text' name='anofabric' id='anofabric' value='$anofabr' readonly>
              </div>

              <!-- div ano do modelo-->
              <div class='ms-2 mb-3 col-sm-2 d-flex flex-column'>
                <label class='form-label mb-0' for='anomodelo'>Ano do modelo: </label>
                <input class='form-control' type='text' name='anomodelo' id='anomodelo' value='$anomodelo' readonly>
              </div>
            </div> ";

            

            print"
            <!-- div hodometro -->
          <div class='row mt-1 align-items-end'>
            <div class='d-flex col-sm-4 flex-column'>
              <label class='form-label mb-0' for='hodometroinicial'>Hodômetro Inicial(em Km):</label>
              <input class='form-control' type='text' name='hodometroinicial' id='hodometroinicial'  value='$hodometroinicial' readonly>
            </div>

            <!-- div hodometro -->
            <div class='d-flex col-sm-4 flex-column'>
              <label class='form-label mb-0' for='hodometro'>Hodômetro (em Km):</label>
              <input class='form-control' type='text' name='hodometro' id='hodometro' placeholder='Hodômetro em Total de Km' value='$hodometro' readonly>
            </div>

            <!-- div verhodometro 
            <div class='d-flex col-sm-4 flex-column'>
              
              <a  href='$href' target='_blank' class='col-2 btn btn-secondary'><span class='material-symbols-outlined'>visibility</span></a>
            </div> --> ";

            if(!empty($todoshod[0])){
            print"
            <!-- div verhodometro -->
            <div class='d-flex col-sm-4 flex-column'>

              <p style='font-size: 10px;' class='m-0'><span style='color:red'>*</span>Última foto de hodômetro enviada para solicitação de combustível.</p>
              
              <a  href='$href' target='_blank' class='btn btn-secondary'>Ver Último Hodômetro<span style='color:red'>*</span></a>            
            </div> ";
            } ?>
          </div>

          <div class='row mt-1'>
            <!-- div data -->
            <div class="col-sm-3 d-flex flex-column justify-content-start">
              <label class="mb-0" for='datainfracao'>Data Ult. Movimentação: </label> 
              <input type="date" class="form-control" name="datamovimentacao" id='datamovimentacao' value="<?php echo $datamov1[0]; ?>" readonly/>
            </div>

            <!-- div acao -->
            <div class="ms-1 col-sm-4 d-flex flex-column justify-content-start"> 
              <label class="mb-0" for='acao'>Ação: </label>
              <input type="text" class="form-control" name="acao" id='acao' value="<?php echo $acao; ?>" readonly/>
            </div>

            <?php
            print "
            <!-- div autor movimentação -->
            <div class='d-flex col-sm-4 flex-column'>
              <label class='form-label mb-0' for='autorm'>Autor:</label>
              <input class='form-control' type='text' name='autorm' id='autorm' value='$autor' readonly>
            </div>  
          </div>";

        if ($numrow8> 0) {
              print"

          <div class='row mt-1'>";

          if(!empty($crlv)){print"
            <!-- div qtd doccrlv -->
            <div class='d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0' for='doccrlv'>Doc. CRLV:</label>
              <a class='btn btn-secondary' href='$crlv' download>Download</a>
            </div>";
          }

          if(!empty($crv)){ print"
            <div class='d-flex flex-column col-sm-3'>            
              <p class='mb-0'> Doc. CRV: </p>
              <a class='btn btn-secondary' href='$crv' download>Download</a>             
            </div>";
          }

          if(!empty($cert_ipva)){print"
            <div class='d-flex flex-column col-sm-3'>            
              <p class='mb-0'> Doc. IPVA: </p>
              <a class='btn btn-secondary' href='$cert_ipva' download>Download</a>             
            </div>";
          }
          print"
          </div>";
            }       

          print"
          <!-- div renavam, numero do chassi, num motor -->            
          <div class='mt-2 row'>
            <!--div renavam-->
            <div class='d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0'  for='renavam'>RENAVAM:</label>
              <input class='form-control' type='renavam' name='renavam' id='renavam' value='$renavam' readonly>
            </div>

            <!--div num chassi -->
            <div class='ms-2 d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0'  for='chassi'>Número do Chassi:</label>
              <input class='form-control' type='text' name='chassi' id='chassi' value='$chassi'readonly>
            </div>

            <!--div num motor -->
            <div class='ms-2 d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0'  for='nummotor'>Número do Motor:</label>
              <input class='form-control' type='text' name='nummotor' id='nummotor' value='$nummotor' readonly>
            </div>
        </div> ";
        ?> 

        <!--col-md-12 d-flex justify-content-start-->
        <div class="row mt-2">
          <!-- div marca-->
          <?php
          print"<!-- div qtd oficina -->
          <div class='d-flex col-sm-6 flex-column'>
            <label class='form-label mb-0' for='oficina'>Oficina:</label>
            <input class='form-control' type='text' name='oficina' id='oficina' placeholder='Oficina' value='$oficina' readonly>
          </div>

          <!-- div qtd ncontloc -->
            <div class='ms-2 d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0' for='ncontloc'>N° Contrato Locação:</label>
              <input class='form-control' type='text' name='ncontloc' id='ncontloc' placeholder='Número Contrato Locação' value='$ncontloc' readonly>
            </div>";
          ?>
            
          </div>
              
          <!--mt-3 col-md-12 d-flex justify-content-start-->
          <div class="mt-3 row">
          <?php  
            print "
            <div class='d-flex col-sm-3 flex-column'>
              <label class='form-label mb-0' for='propriedade'>Tipo de posse do veículo:</label>
              <input class='form-control' type='text' name='propriedade' id='propriedade'  value='$tipoposse' readonly>
            </div>

            <div class='d-flex col-sm-6 flex-column'>
              <label class='form-label mb-0' for='locador'>Locador (se o veículo for locado): </label>
              <input class='form-control' type='text' name='locador' id='locador'  value='$locadora' readonly>
            </div>      
          </div>

          <!-- div base gestão e centro de custo -->
          <div class='mt-3 mb-2 col-md-12 d-flex justify-content-start'>
            <div class='col-sm-4'>                
              <label for='basegestao'>Base Gestão:</label>
              <input class='form-control' type='text' name='basegestao' id='basegestao' placeholder='Base Gestão' value = '$basegestaof' readonly>
            </div>

            <div class='ms-2 col-sm-4'>                
              <label for='centrocusto'>Centro de Custo:</label>
              <input class='form-control' type='text' name='centrocusto' id='centrocusto' value = '$ccusto' readonly>
            </div>
          </div>";
          ?>

          <div class="mt-3 mb-2 col-md-12 d-flex justify-content-around">
            <div class="col-4">
              <form action='./historicoplacachecklist.php' method='post' target='_blank'>
                <?php print"<input type='hidden' name='placa' value='$placa'>"; ?>
                <button type="submit" class="btn btn-success">Histórico de Vistorias</button>
              </form>
            </div>

            <div class="col-4">
              <form action='./historicoplaca.php' method='post' target='_blank'>
                <?php print"<input type='hidden' name='placa' value='$placa'>"; ?>

                <button type="submit" class="btn btn-secondary">Histórico de Condutores</button>
              </form>
            </div>

              <div class="col-4">
              <form action='./historicoplacamanutencao.php' method='post' target='_blank'>
                <?php print"<input type='hidden' name='placa' value='$placa'>"; ?>

                <button type="submit" class="btn btn-primary">Histórico de Manutenções</button>
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
    
    <!-- jquery - 3.5.1 - js  -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap - 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- datatables - js -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    
    <!-- script próprio -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {

      });
    </script>
    
  </body>
</html>