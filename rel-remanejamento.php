<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

// $dataInicialfi = isset($_POST["datai"]) ? $_POST["datai"] : '';
// $dataFinalfi = isset($_POST["dataf"]) ? $_POST["dataf"] : '';
// $mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';

$datai = $_POST['datai'];
$dataf = $_POST['dataf'];
$matr_autor = $_POST['matr_autor'];

if (strftime('%A') == "domingo" ){
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}

include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png"/>
    
    <title>Portal FFA</title>    
  
    <link href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" rel="stylesheet" />
    
    <link href="src/css/styles.css" rel="stylesheet" />
    
  </head>

  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include"menu.php"; ?>

    <div id="layoutSidenav_content">

      <main>
        <div class="container-fluid px-4">
          <h1 class="h1 mt-2"> Relatório Remanejamento</h1>
          <form action="./rel-remanejamento.php" method="post">
            <h5>Selecione o Periodo:</h5>

            <div class="row">
              <div class="col-md-3">
                <label for="datai"><h5>Data Inicial:<span style='color: red;'>*</span></h5></label>
                <input type="date" class="form-control" id="datai" name="datai" <?php print"value='$datai'"; ?> required>
              </div>     
              
              <div class="col-md-3">
                <label for="dataf"><h5>Data Final:<span style='color: red;'>*</span></h5></label>
                <input type="date" class="form-control" id="dataf" name="dataf" <?php print"value='$dataf'"; ?> required>
              </div>

              <div class="col-md-3">
                <label for="matr_autor"><h5>Matrícula:<span style='color: red;'>*</span></h5></label>
                <input type="text" class="form-control mb-3" name="matr_autor" placeholder="Digite a matrícula" <?php print"value='$matr_autor'"; ?>>
              </div> 
            </div>

            <p><span style='color: red;'>*</span>Campo obrigatório</p>

            <div class='mt-2'> 
              <div class="mt-4">
                  <input type="submit" class="btn btn-success"  value="Filtrar">
              </div>
            </div>
          </form> 
          
          <?php
            if($matr_autor != ""){
              $sqla = "SELECT nome, cargo FROM bdaniel.tbfuncionario where matricula = '$matr_autor'";
              $resultadoa = mysql_query($sqla) or die (mysql_error());
              $rowa = mysql_fetch_array($resultadoa);
                $nome_autor = $rowa[0];
                $cargo_autor = $rowa[1];

              print"
              <div class='d-flex mt-3'>
                <h5>Nome: $nome_autor</h5>
                <h5 class='ms-3'>Cargo: $cargo_autor</h5>
              </div>";
            }

          ?>

          <div class="card mt-4 mb-4">
            <div class="card-header">
              <a href="./dashboard.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Voltar para Dashboard
              </a>
            </div>
            
            <div class="card-body">
              <!--tabela para entrada de dados-->
              <table id='datatablesSimple' class='table table-bordered'>
                <thead>
                  <tr>
                    <th>Data</th>
                    <th>Saída</th>
                    <th>Entrada</th>                            
                  </tr>
                </thead>
                
                <tbody>
                  <?php
                    $sql = "SELECT acao, data_e_hora, matricula, mat_autor, valor_novo FROM bdfrota.tblog where data_e_hora BETWEEN '$datai 00:00:00' AND '$dataf 23:59:59' AND mat_autor = '$matr_autor' AND acao LIKE '%remanejou%'";
                    $resultado = mysql_query($sql) or die (mysql_error());
              
                    while($row = mysql_fetch_array($resultado)){
                      $acao = $row['acao'];
                      $data_e_hora = $row['data_e_hora'];                            
                      $valor = $row['valor_novo'];

                      $data_e_horav = DateTime::createFromFormat('Y-m-d H:i:s', $data_e_hora)->format('d/m/Y H:i:s');

                      if(strpos($valor,".") != false){
                        $valor = str_replace(".",",",$valor);
                      }

                      if(strpos($acao,"adicionou") != false){
                        $saida = $valor;
                        $entrada = "";
                      }elseif (strpos($acao,"retirou") != false) {
                        $entrada = $valor;
                        $saida = "";
                      }


                  
                      print "
                      <tr>
                        <td>$data_e_horav</td>
                        <td>R$ $entrada</td>
                        <td>R$ $saida</td>
                      </tr>";
                    }
                  ?>
                  
                </tbody>
              </table>                
            </div>
          </div>
        </div>
      </main>

      <footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
          <div class="container-fluid px-4">
              <div class="d-flex align-items-center justify-content-between small">
                  <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                  <!--<div>
                      <a href="#">Privacy Policy</a>
                                      &middot;
                      <a href="#">Terms &amp; Conditions</a>
                  </div>!-->
              </div>
          </div>
      </footer>

    </div>


  </body>

  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>


  <script src="src/js/scripts.js"></script>

  <script>
    $(document).ready( function () {
      new DataTable('#datatablesSimple', {
        layout: {
          topStart: {
            //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            buttons: [ { extend: 'excelHtml5', title: 'Relatório Remanejamento'}]
          }
        },

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
            [25, 50, 100, -1],
            [25, 50, 100, 'All']
          ]
      });
    });
        
    /*$("#selectcoordenador").change(function() {
      var selectcoordenador = document.getElementById('selectcoordenador');
      var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
      var selectsupervisor = document.getElementById('selectsupervisor');
      var selecttecnico = document.getElementById('selecttecnico');
          
      for (var i = 0; i < selectsupervisor.length; i++) {
        var idsupervisor = selectsupervisor.options[i].id;
        if (!idsupervisor.startsWith(idcoordenador)) {
          $(selectsupervisor.options[i]).attr('disabled', 'disabled').hide();
        } else {
          $(selectsupervisor.options[i]).removeAttr('disabled').show();
        }
      }
          
      for (var i = 0; i < selecttecnico.length; i++) {
        var idtecnico = selecttecnico.options[i].id;
        if (!idtecnico.startsWith(idcoordenador)) {
          $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
        } else {
          $(selecttecnico.options[i]).removeAttr('disabled').show();
        }
      }
    });*/

    /*$("#selectsupervisor").change(function() {
    var selectcoordenador = document.getElementById('selectcoordenador');
    var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
    var selectsupervisor = document.getElementById('selectsupervisor');
    var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
    var selecttecnico = document.getElementById('selecttecnico');
    for (var i = 0; i < selecttecnico.length; i++) {
      var idtecnico = selecttecnico.options[i].id;
      if (!idtecnico.startsWith(idsupervisor)) {
        $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
      } else {
        $(selecttecnico.options[i]).removeAttr('disabled').show();
      }
    }
    const id = idsupervisor.split('-');
    const [coordenador, supervisor] = id;
    for (var i = 0; i < selectcoordenador.length; i++) {
      if (selectcoordenador[i].id == coordenador) {
        selectcoordenador[i].selected = true;
      }else{
        selectcoordenador[i].selected = false;
      }
    }
    });*/

    /*$("#selecttecnico").change(function() {
      var selectcoordenador = document.getElementById('selectcoordenador');
      var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
      var selectsupervisor = document.getElementById('selectsupervisor');
      var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
      var selecttecnico = document.getElementById('selecttecnico');
      var idtecnico = selecttecnico[selecttecnico.selectedIndex].id;
      const id = idtecnico.split('-');
      const [coordenador, supervisor, tecnico] = id;
      for (var i = 0; i < selectcoordenador.length; i++) {
        if (selectcoordenador[i].id == coordenador) {
          selectcoordenador[i].selected = true;
        }else{
          selectcoordenador[i].selected = false;
        }
      }
      for (var i = 0; i < selectsupervisor.length; i++) {
        if (selectsupervisor[i].id == coordenador+'-'+supervisor) {
          selectsupervisor[i].selected = true;
        }else{
          selectsupervisor[i].selected = false;
        }
      }
    });*/
  </script>

</html>