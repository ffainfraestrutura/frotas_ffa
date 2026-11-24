<?php
  date_default_timezone_set('America/Sao_Paulo');

  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  // Iniciar a sessão
  session_start();

  $nome1 = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula1 = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];
  $tipo=$_SESSION['tipo'];

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } /*elseif ($perfil <> '4' || $perfil<>'5' || $perfil<>'6'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']= $tipo;

  $data = date('Y-m-d');

  include"../conecta.php";
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

    <title> Vistoria </title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!-- jquery e masks -->
    <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <!--data tables - css e js-->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
  </head>
  <body class="sb-nav-fixed">

    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">        
            
            <!--div dados-->
            

                  <div class='container-fluid px-4 col-md-10'>
                    <h1 class='h1 pt-3 pb-2 col-12 text-center'> Relatórios Pendentes de Assinatura </h1>
                    <div class="form-group col-md-2">
    <label for="exampleInputEmail1"><b style="color: white">.</b></label>
    <button class="form-control form-control-md btn btn-success" onclick="baixarExcel()">Baixar Excel</button>
  </div> 

                    <div class='mt-3' style='width: 100%;'>
                      <table id='tabela1' class='text-center table'>
                        <thead>
                          <tr>
                            <th>Relatório placa nº</th>
                            <th>Data</th>
                            <th>Condutor</th>
                            <th>Vistoriador</th>
                            <th>Unidade</th>
                          </tr>
                        </thead>
                        <tbody>
                  <?php
                  $sql1 = "SELECT DISTINCT idtbvistoria, datavistoria, placa, nome, vistoriador, unidade FROM bdfrota.tbvistoria WHERE placa<>'ABC1234' and assinadocond = '0' and nome <> '';  ";

                  $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

                  while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
                    $idtbvistoria = $row1['idtbvistoria'];
                    $placa = $row1['placa'];
                    $datavistoria = $row1['datavistoria'];
                    $idnextfleet = $row1['idnextfleet'];
                    $nometec = $row1['nome'];
                    $vistoriador = $row1['vistoriador'];
                    $unidade = $row1['unidade'];

                    $datavistoria1 = new DateTime($datavistoria);
                    $datavistoriareal = $datavistoria1-> format('d/m/Y');


                    print "
                        <tr>
                          <td>$placa</td>
                          <td>$datavistoriareal</td>
                          <td>$nometec</td>
                          <td>$vistoriador</td>
                          <td>$unidade</td>
                        </tr>";
                  }
                  ?> 
                  
                      </tbody>
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
    </div><!--div para fechar a div que fica aberta no menu.php-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>

    <!--
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->


    <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('#tabela1').DataTable({
          "columns": [
            null,
            { "type": "date-eu" },
            null,
            null,
            null
          ],
          "order": [1, 'desc'],
          responsive: true,
          "searching": true,
          info: false,
          /*ordering: false,
          paging: false,*/

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
            },
            "aria": {
                "sortAscending":  ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
          },
          // Customização do lengthMenu para incluir "Todos"
        "lengthMenu": [
            [10, 50, 100, 200, -1], // Valores internos para a configuração
            [10, 50, 100, 200, "Todos"] // Valores exibidos para o usuário
        ],
        "pageLength": 100 // Quantidade padrão de registros por página
        });

      });
    </script>

<script>
    function baixarExcel() {
        // Obter a tabela
        var tabela = document.getElementById('tabela1');

        // Converter a tabela para um objeto SheetJS
        var worksheet = XLSX.utils.table_to_sheet(tabela);

        // Criar uma nova planilha
        var workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Planilha1');

        // Criar um arquivo XLSX a partir do workbook
        var xlsxFile = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });

        // Criar um link para download
        var blob = new Blob([xlsxFile], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        var url = URL.createObjectURL(blob);

        // Criar um elemento para download
        var a = document.createElement('a');
        a.href = url;
        a.download = 'assinaturaspendentes.xlsx'; // Nome do arquivo
        a.click();

        // Limpar a URL para evitar vazamentos de memória
        URL.revokeObjectURL(url);
    }
    </script>

  </body>
</html>