<?php
  date_default_timezone_set('America/Sao_Paulo');
  session_start();

  $nome1 = $_GET['nome'] ?? ($_SESSION['nome'] ?? '');
  $usuariof = $_GET['usuario'] ?? ($_SESSION['usuario'] ?? '');
  $matricula1 = $_GET['matricula'] ?? ($_SESSION['matricula'] ?? '');
  $perfil = $_GET['perfil'] ?? ($_SESSION['perfil'] ?? '');
  $tipoUsuario= $_GET['tipo'] ?? ($_SESSION['tipo'] ?? '');

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } /*elseif ($perfil<>'1'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";}*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']=$tipoUsuario;


  $data = date('Y-m-d');

    $placaveic = $_POST['placa'] ?? '';
  if(strpos($placaveic, "-") !== false){
    $placacorreta = str_replace("-", "", $placaveic);
  } else {
    $placacorreta = str_replace(" ", "", $placaveic);
  }

  include"../conecta03.php";

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

    <title> Visualizar Relatório </title>

    <!--link Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />   

    <!--data tables - css -->
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">
    
    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
        <?php include "./menu.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-3 pb-2 col-12 text-center"> Relatórios por Placa </h1>

          <p> Insira a placa do veículo para visualizar seus relatórios de vistoria.</p>

          <!-- div form action="./relatoriosadm.php"-->
          <div>
            <form method="post" action="./gerarrelatorio.php" class="row"> <!---->
              <!--div dados-->
              <div class="d-flex justify-content-start col-12">
                <label class="col-1 col-form-label" for="placa"> Placa:<span style="color: red;">*</span> </label>
                <div class="col-5">
                  <input type="text" name="placa" id="placa" class="form-control" placeholder="Nº da Placa"  <?php print "value='$placaveic'"; ?> required>
                </div>

                <div class="ms-1 col-7">
                  <button class="btn btn-success" type='submit'> Visualizar relatório(s) </button>
                </div>
              </div>

              <!-- div campos obrigatórios-->
              <div class="mt-4 col-12 border-bottom border-dark">
                <p style='color: red;'> * Campos obrigatórios. </p>
              </div>
            </form>
          </div>

          <!--div tabela-->        
          <div class='mt-3 col-12'>
            <table id='tabela1' class="table" style='width: 100%;'>
              <thead>
                <tr>
                  <th>Relatório</th>
                  <th>Data</th>
                  <th>Tipo de vistoria</th>
                  <th>Condutor</th>
                  <th class="text-center">Assinado pelo condutor?</th>
                  <th> </th>
                  <th> </th>
                </tr>
              </thead>
              <tbody>
            <?php           

              $sql1 = "SELECT DISTINCT idtbvistoria, datavistoria, placa, assinadocond, idnextfleet, tipo, nome, matricula FROM bdfrota.tbvistoria WHERE placa='$placacorreta' AND placa<>'' AND placa NOT IN ('ABC1234', 'ABC1245', 'ABC1122') AND (idtbvistoria<2995 or  idtbvistoria>3015) AND idtbvistoria NOT IN (3028, 3031, 3032, 4719, 4718) AND statusreg=1";
              //print $sql1;
              $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

              while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                $idtbvistoria = $row1['idtbvistoria'];
                $placacorreta = $row1['placa'];
                $datavistoria = $row1['datavistoria'];
                $assinado = $row1['assinadocond'];
                $idnextfleet = $row1['idnextfleet'];
                $tipo = $row1['tipo'];
                $nome = $row1['nome'];
                $matriculaCondutor = $row1['matricula'] ?? '';
                $baseAssinaturaCondutor = '/var/www/html/assinaturas/docs/colass/';
                $assinaturaCondutorArquivo = '';
                $assinaturaCondutorResposta = 'nao_encontrou';

                if ($matriculaCondutor !== '') {
                  $candidatosAssinatura = [
                    $matriculaCondutor . '.png',
                    $matriculaCondutor . '.jpg',
                    $matriculaCondutor . '.jpeg',
                    $matriculaCondutor,
                  ];

                  foreach ($candidatosAssinatura as $candidatoAssinatura) {
                    $caminhoAssinatura = rtrim($baseAssinaturaCondutor, '/\\') . '/' . ltrim($candidatoAssinatura, '/\\');
                    if (file_exists($caminhoAssinatura) && is_readable($caminhoAssinatura)) {
                      $assinaturaCondutorArquivo = basename($candidatoAssinatura);
                      $extensaoAssinatura = pathinfo($assinaturaCondutorArquivo, PATHINFO_EXTENSION);
                      $assinaturaCondutorResposta = 'encontrou_' . ($extensaoAssinatura !== '' ? $extensaoAssinatura : 'arquivo');
                      break;
                    }
                  }
                }

                $paramsCompartilhaveis = [
                  'vistoria' => $idtbvistoria,
                  'datavistoria' => $datavistoria,
                  'placa' => $placacorreta,
                  'matricula' => $matriculaCondutor,
                  'matriculacondutor' => $matriculaCondutor,
                  'nome' => $nome1,
                  'usuario' => $usuariof,
                  'matriculausuario' => $matricula1,
                  'perfil' => $perfil,
                  'tipo' => $tipoUsuario,
                ];

                /*$datavistoria1 = explode(" ",$datavistoria);
                $datavistoria2 = explode("-", $datavistoria1[0]);
                $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];*/

                $datavistoriareal= DateTime::createFromFormat('Y-m-d H:i:s', $datavistoria)->format('d/m/Y H:i:s');

                $sql4 = "SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist = '$tipo';";
                $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
                $row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC);
                  $tipof = $row4['tipo'];              
                  
                $sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria = '$idtbvistoria' and idtbvistfotos not in (40,41,42,43,44,45,47) ;";              
                $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));                
                  $linhas = mysqli_num_rows($resultado5);

                if($linhas <= 0 && $idnextfleet==''){//vistoria sem fotos
                  //$link = '././fpdf/relatorio.php';
                  $link = 'https://ffa.painel-telecom.com:4242/frotas/checklist/fpdf/relatorio.php';
                  $linkPdf = $link . '?' . http_build_query($paramsCompartilhaveis);
                      
                  $link2 = 'https://ffa.painel-telecom.com:4242/frotas/checklist'.$extra1;

                  //$verarq = "<a href='$link2' target='_blank'>Ver anexos</a>";
                  $pdf = "
                  <td>
                    <a class='btn btn-success' href='$linkPdf' target='_blank' rel='noopener'>Visualizar PDF</a>
                  </td>";                

                  $verfotos = " ";

                  $a = 'entrou na opção 1';

                } elseif($linhas > 0 && $idnextfleet=='') {//vistoria com fotos
                  $row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
                    $extra1 = $row5['extra1'];
                    $frontal = $row5['frontal'];

                  //if else define o link
                  if(strpos($extra1, "pdf") !== false){//vistoria manual
                    $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatorio.php';
                    $linkManual = './veranexos2.php?' . http_build_query([
                      'idtbvistoria' => $idtbvistoria,
                      'matricula' => $matriculaCondutor,
                    ]);
                    

                    $pdf = "
                    <td>
                      <a class='btn btn-secondary mt-2' href='$linkManual' target='_blank' rel='noopener'>Vistoria Manual</a>
                    </td>";

                    $verfotos = "";

                    $a = 'entrou na opção 2';
                  }else{
                    $achou = 'f';
                    // Nome do diretório inicial
                    $dirin = 'docs_old/';

                    $diretorio = dir($dirin);
                    $buscar = substr($frontal, 10);

                    while(($arquivo = $diretorio -> read()) && ($achou=='f')){
                    //while($arquivo = $diretorio -> read()){
                      if ($arquivo != '.' && $arquivo != '..'){

                        if(strpos($arquivo, $buscar) !== false){
                          $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatoriocomfotos.php';
                          
                          $action='./verfotos05.php';
                          
                          //break;
                          $achou = 'true';
                        
                        }else{
                          $link = 'https://ffa.painel-telecom.com:4242/frotas/checklist/fpdf/relatoriocomfotos.php';
                          $action='https://ffa.painel-telecom.com:4242/frotas/checklist/verfotos.php';
                          
                          //break;
                        }
                        
                      }
                    }                 

                    $diretorio -> close();
                    $linkPdf = $link . '?' . http_build_query($paramsCompartilhaveis);
                    $linkFotos = $action . '?' . http_build_query($paramsCompartilhaveis);

                    $pdf = "
                    <td>
                      <a class='btn btn-success' href='$linkPdf' target='_blank' rel='noopener'>Visualizar PDF</a>
                    </td>";

                    $verfotos = "                    
                    <a class='btn btn-primary' href='$linkFotos' target='_blank' rel='noopener'>Ver fotos</a>";
                    
                  }

                  
                }elseif($idnextfleet!=''){
                  // Nome do diretório inicial
                  $dirin = 'nextfleet/';

                  $diretorio = dir($dirin);

                  while($arquivo = $diretorio -> read()){
                    if ($arquivo != '.' && $arquivo != '..'){

                      if(strpos($arquivo, $idnextfleet) !== false){
                        $href = $arquivo;
                      }
                      
                    }
                  }                 

                  $diretorio -> close();
                  $linkPdf = 'https://ffa.painel-telecom.com:4545/frotas/checklist/nextfleet/' . $href;
                  $linkFotos = './verfotosnextfleet.php?' . http_build_query($paramsCompartilhaveis + ['idnextfleet' => $idnextfleet]);
                  

                  $pdf = "<td><a class='btn btn-success' href='$linkPdf' target='_blank' rel='noopener'>Visualizar PDF</a></td>";

                  $verfotos = "                    
                  <a class='btn btn-primary' href='$linkFotos' target='_blank' rel='noopener'>Ver fotos</a>";
                }

                if($assinado==1){
                  $assinadotab="<td class='text-center'>Sim</td>";
                } else {
                  $assinadotab= "<td class='text-center'>Não</td>";
                }

                print "
                  <tr>
                    <td>Placa $placacorreta</td>
                    <td>$datavistoriareal</td>
                    <td>$tipof</td>
                    <td>$nome</td>
                    $assinadotab
                    $pdf
                    <td>$verfotos</td>             
                  </tr>";
              }
            ?>            
              </tbody>
            </table>               
          </div>

          <!--div gerar excel -->
          <div class="mt-2">
            <form action="./control/gerarexcelplaca.php" method="post">
                <?php print "<input type='hidden' name='placa' value='$placacorreta'/>";?>
                <button type='submit' class='btn btn-success'>Gerar Relatório Excel</button>
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
    <!-- jquery --> 
    <!-- <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script> -->
    <script src='https://code.jquery.com/jquery-3.7.1.js'  crossorigin='anonymous'></script>
    
    <!-- masks -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <!-- fontawesome - v6.1.0 - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- datatables.net - 1.13.1 - js -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>

    <!-- bootstrap 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>    

    <script type="text/javascript">
      $(document).ready(function () {
        DataTable.datetime('DD/MM/YYYY HH:mm:ss');

        new DataTable('#tabela1', {
        //$('#tabela1').DataTable({
          // "columns": [
          //   null,
          //   { "type": "date-eu" },
          //   null,
          //   null,
          //   null,
          //   null,
          //   null
          // ],

          order: [[1, 'desc']],

          language: {
            decimal:        "",
            emptyTable:     "Nada para exibir",
            info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
            infoFiltered:   "(filtrado do total de _MAX_ registros)",
            infoPostFix:    "",
            thousands:      ",",
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
          },

          lengthMenu: [
              [10, 5, 1, -1],
              [10, 5, 1, 'Todos'],
          ],
        });

        //mascara de placa
        $('#placa').mask('AAA0U00', {
          translation: {
              'A': {
                  pattern: /[A-Za-z]/
              },
              'U': {
                  pattern: /[A-Za-z0-9]/
              },
          },
          onKeyPress: function (value, e, field, options) {
              // Convert to uppercase
              e.currentTarget.value = value.toUpperCase();

              // Get only valid characters
              let val = value.replace(/[^\w]/g, '');

              // Detect plate format
              let isNumeric = !isNaN(parseFloat(val[4])) && isFinite(val[4]);
              let mask = 'AAA 0U00';
              if(val.length > 4 && isNumeric) {
                  mask = 'AAA-0000';
              }
              $(field).mask(mask, options);
          }
        });
      });
    </script>

  </body>
</html>