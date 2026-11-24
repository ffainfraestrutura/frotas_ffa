<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$hoje = $_GET["diai"];
//$hoje = '2023-11-12';
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
include"./conecta.php";
include_once"../func/log.php";

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$mattec = $_GET["matriculatec"];
$dataac = $hoje;


// Data de referência (pode ser qualquer data)
$data_referencia = new DateTime($dataac); // Substitua essa data pela sua data de referência

// Verifique se hoje é uma segunda-feira (1 é segunda-feira no PHP)
if ($data_referencia->format('N') == 1) {
    // Hoje é uma segunda-feira
    $usegunda = $data_referencia->format('Y-m-d');
} else {
    // Hoje não é uma segunda-feira
    $data_referencia->modify('last monday');
    $usegunda = $data_referencia->format('Y-m-d');
}


$nomesDiasDaSemana = array(
    1 => 'Segunda-feira',
    2 => 'Terça-feira',
    3 => 'Quarta-feira',
    4 => 'Quinta-feira',
    5 => 'Sexta-feira',
    6 => 'Sábado',
    7 => 'Domingo'
);

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
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>
<script type="text/javascript">
  function popmodalagt(dataDia, mattec){

    $('#mattec')[0].value = mattec;
    $('#datadia')[0].value = dataDia;

}

</script>
  <style>
    /* Estilo para ocultar o elemento inicialmente */
    .numeric-input {
      display: none;
    }
  </style>
    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php";
        include"popupgercol.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Deslocamento OS</h1>


    <?php   
if ($mattec <> '') {


    // Data inicial (segunda-feira)
    $dataInicial = new DateTime($usegunda); // Substitua com a data da segunda-feira desejada
    $dataInicial->modify('next sunday');
    $pdomingo = $dataInicial->format('Y-m-d');
    // Data final (domingo)
    $dataFinal = new DateTime($pdomingo); // Substitua com a data do domingo desejado
    $dataInicial = new DateTime($usegunda);

while ($dataInicial <= $dataFinal) {
        $dataDia = $dataInicial->format('Y-m-d');
        $timestamp = strtotime($dataDia);
        $diaSemanaNumero = date('N', $timestamp);
        $nomeDiaSemana = $nomesDiasDaSemana[$diaSemanaNumero];
        print"<div class='card mt-4 mb-4'>
            <div class='card-header'>
                 $dataDia - $nomeDiaSemana 
            </div>
             <div class='card-body'>
                <!--tabela para entrada de dados-->
                <table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th>OS</th>
                            <th>latitude</th>
                            <th>longitude</th>
                            <th>Origem</th>
                            <th>KM</th>
                        </tr>
                    </thead>
                    <tbody>";
                          
        $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];

      // Verifica se o valor de $ccusto contém "TIM"
        if (strpos($ccusto, 'TIM') !== false) {
            $tabela = "bdfrota.tbosrota";
            $id = 'idtbosrota';
        }
        // Verifica se o valor de $ccusto contém "CLARO"
        elseif (strpos($ccusto, 'CLARO') !== false) {
            $tabela = "bdfrota.tbosrotalatlong";
            $id = 'idtbosrotalatlong';
        }elseif(strpos($ccusto, 'LIGGA') !== false) {
            $tabela = "bdfrota.tbosrota";
            $id = 'idtbosrota';
        }


        $sql = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa, latitude, longitude, $id FROM $tabela where matricula = '$mattec' and data = '$dataDia' group by eta order by data, eta";
      $resultado = mysql_query($sql) or die (mysql_error());
      
              
      while ($row = mysql_fetch_array($resultado)){

        $data = $row[3];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endereco = $row[0];
        $bairro = $row[1];
        $cidade = $row[2];
        $eta = $row[4];
        $os = $row[5];
        if ($os == '') {
            $os = $row[7];
        }
        $kmrota = $row[6];
        if ($kmrota == '') {
            $kmrota = 0;
        }

        $foratoa = $row[8];
        if ($foratoa == 1) {
            $origem = 'MANUAL';
        } elseif ($os == 'CASA') {
            $origem = 'ANIEL';
        } else {
            $origem = 'TOA';
        }
        $latitude = $row[9];
        $longitude = $row[10];
        $idtbos = $row[11];

        $endcomp = "$endereco $bairro $cidade";

if (mb_detect_encoding($endcomp, 'UTF-8', true)) {
    $endcomp = "$endereco $bairro $cidade";
} else {
    $endcomp = utf8_encode("$endereco $bairro $cidade");
}
        


        $kmsrodados = $row2[0];

        print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                 <td class='text-nowrap'>$datat</td>
                <td>$eta</td>
                <td class='text-nowrap'>$os</td>
                <td>$latitude</td>
                <td>$longitude</td>
                <td>$origem</td>
                <td>$kmrota</td>
               </tr>";

        


      }
      
            

            $sql = "SELECT ROUND(kmsrodados,1) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$dataDia'";
        
            $resultado = mysql_query($sql) or die (mysql_error()); 
            $row = mysql_fetch_array($resultado);

            $kmsrodados = $row[0];

            if ($kmsrodados > 0) {
                print "<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>-</td>
                 <td class='text-nowrap'>$datat</td>
                <td>-</td>
                <td>ALMOÇO</td>
                <td>-</td>
                <td>-</td>
                <td>ALMOÇO</td>
                <td>5.0</td>
               </tr>";
            }
            

                

            $sql = "SELECT kmsedit, posneg, matop, motivo FROM bdfrota.tbkmdiarioedit where matricula = '$mattec' and dataedit = '$dataDia'";
        
            $resultado = mysql_query($sql) or die (mysql_error()); 
            if (mysql_num_rows($resultado)>=1)
      {
            $row = mysql_fetch_array($resultado);

            $kmsedit = $row[0];
            $posneg = $row[1];
            if ($posneg == 1) {
                $sinal = '-';
                $kmsrodados = $kmsrodados - $kmsedit;
            } else {
                $kmsrodados = $kmsrodados + $kmsedit;
                $sinal = '+';
            }
            $matup = $row[2];
            $motivo = $row[3];
                print "<tr>
                <td colspan = '1'><b>Mat. Operador</b></td>
                <td colspan = '8'><b>Motivo</b></td>
                <td colspan = '1'><b>KMs</b></td>
               </tr>";
            
                print "<tr>
                <td colspan = '1'>$matup</td>
                <td colspan = '8'>$motivo</td>
                <td colspan = '1' class='text-nowrap'>$sinal $kmsedit</td>
               </tr>";

            }

            print"<tr>
            <td colspan = '8'></td>
                <td colspan = '1'><b>Total</b></td>
                <td colspan = '1'><b>$kmsrodados</b></td>
            </tr>"; 

                   print"</tbody>
                </table>
            
            </div>
        </div>";
        $dataInicial->add(new DateInterval('P1D'));
    }}
        ?>


    </div>

</main>

<footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script>
    // Seleciona os radios
    const radioAdicionar = document.getElementById("decisaoscol");
    const radioDiminuir = document.getElementById("decisaoncol");

    // Seleciona a div do campo de entrada numérica
    const numericInputDiv = document.querySelector(".numeric-input");

    // Adiciona um ouvinte de evento para os radios
    radioAdicionar.addEventListener("change", function() {
      // Mostra a div quando o radio "Adicionar" é selecionado
      numericInputDiv.style.display = "block";
    });

    radioDiminuir.addEventListener("change", function() {
      // Mostra a div quando o radio "Diminuir" é selecionado
      numericInputDiv.style.display = "block";
    });
  </script>
<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>
<script>
        
       $("#selectcoordenador").change(function() {

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
        });

         $("#selectsupervisor").change(function() {
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
        });

        $("#selecttecnico").change(function() {
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
        });
        </script>
</html>