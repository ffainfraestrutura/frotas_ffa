<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_POST["diai"];
$datetime = new DateTime($dia);

// Define o dia da semana para domingo (0 é domingo, 1 é segunda-feira, e assim por diante)
$datetime->setISODate($datetime->format('Y'), $datetime->format('W'), 7);

// Formate a data para 'Y-m-d' (ou o formato desejado)
$diaf = $datetime->format('Y-m-d');

$matsup = $_POST["matsupf"];

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
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Cotas Extra Indevidas </h1>


<div id="collapseExample4" style="
    width: 100%;
">
<form action="./detalhes-cota-extra-indevidas.php" method="post">
 <h5><b>Selecione a Data:</b></h5>

     <div class="row">

     <div class="col-md-6">
      <label for="exampleInputEmail1"><h5>Semana:</h5></label>
      <select class="form-select mb-3" name="diai">
                <option value=" "> Selecione a data </option>

            <?php
            
            $hoje = time(); // Obtém a data e hora atual
            $dia_da_semana = date('w', $hoje); // Obtém o dia da semana (0 é domingo, 1 é segunda-feira, 2 é terça-feira, etc.)

            if ($dia_da_semana == 1) {
                $segunda_atual = $hoje; // Hoje é uma segunda-feira
            } else {
                $segunda_atual = strtotime('last Monday', $hoje); // Caso contrário, obtenha a última segunda-feira
            }

            $contador = 0;

            while ($contador < 4) {
                $dataval = date('Y-m-d', $segunda_atual);
                
                $dataFormatada = date('d/m/Y', strtotime($dataval));
                print"<option value='$dataval'>$dataFormatada</option>";

                $segunda_atual = strtotime('-7 days', $segunda_atual); // Subtrai 7 dias para a próxima segunda-feira
                $contador++;
            }
 
            ?>

            </select>
    </div>


        <div class="col-md-3">
      <label for="exampleInputEmail1" style=" width: 100%; color: white;"><h5>.</h5></label>
      <input type="submit" class="btn btn-success"  value="Filtrar">
    </div>
</div>



</form>

</div>
<div class="btn-group d-flex justify-content-center mb-4 mt-4" role="group" aria-label="Grupo de Botões">
            <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
            <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
            <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço Aniel</button>
            <button type="button" class="btn btn-danger mx-2" onclick="foraEscala()">Fora Escala</button>
            <button type="button" class="btn btn-warning mx-2" onclick="foraHorario()">Fora Horário</button>
            <button type="button" class="btn btn-info mx-2" onclick="extratoKmProj()">Extrato KM Proj</button>
            <button type="button" class="btn btn-light mx-2" onclick="enderecoForaTOA()">Outras Atividades</button>
        </div>
        <div class="card mt-4 mb-4">
            <div class="card-header">
                <a href="./dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard
                </a href="./dashboard.php">
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orc. Inicial</th>
                            <th>Orc. Atual</th>
                            <th>Km Proj. Inicial</th>
                            <th>Km Proj. Atual</th>
                            <th>KM OS</th>
                            <th>Sld. Cartão</th>
                            <!--<th>Valor Retirado</th>
                            <th>Tot. Extra</th>-->
                            <th style='width: 30%;'>Data Pedido</th>
                            <th>Justificativa Técnico</th>
                            <th>Valor Pedido</th>
                            <th>Valor Inserido</th>
                            <th>Just. Supervisor</th>
                            <th>Data Aceito</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
if ($diaf == '' or $diai == '') {
    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <!--<td>Sem Dados</td>
                <td>Sem Dados</td>-->
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";


} else {

        

        $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmproj,0), ROUND(kmos,0), gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, matsup, justsup, qtdgasta, qtdretirada, ROUND(kmprojatual,0), ROUND(orcsematual,2), id FROM bdfrota.tbcotaextraac where dataaceite > '$diai 00:00:00' and dataaceite < '$diaf 23:39:59' and kmproj > 0 and cota_validade = '1' order by data";
        
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row[0];
        $orcsemanal = $row[1];
        $kmproj = $row[2];
        $kmos = $row[3];
        $sldcartao = $row[5];
        $totalextra = $row[6];
        $data = $row[7];
        $justificativa = $row[8];
        $valor = $row[9];
        $valorinserido = $row[10];
        $dataaceite = $row[11];
        $matsup = $row[12];
        $justsup = $row[13];
        $qtdgasta = $row[14];
        $qtdretirada = $row[15];
        $kmprojatual = $row[16];
        $orcsematual = $row[17];
        $id = $row[18];

        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $placa = $row2[0];

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$matsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];


        print "<tr>
        <td>
            <input type='radio' name='idtbpedido' id='$id' value='$id'>
        </label></td>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$nomesup</td>
                <td>$orcsemanal</td>
                <td>$orcsematual</td>
                <td>$kmproj</td>
                <td>$kmprojatual</td>
                <td>$kmos</td>
                <td>$sldcartao</td>
                <!--<td>$qtdretirada</td>
                <td>$totalextra</td>-->
                <td>$data</td>
                <td>$justificativa</td>
                <td>$valor</td>
                <td>$valorinserido</td>
                <td>$justsup</td>
                <td>$dataaceite</td>
               </tr>";

        


      }
}
      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcelcota-extra-indevida.php" method="post">
              <?php 
              print "<input type='hidden' name='diai' value='$diai'>";
              print "<input type='hidden' name='diaf' value='$diaf'>";?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
            </div>
        </div>
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
        function endOS() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-kmoscotaind.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function endGPS() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = 'http://ffasip.ddns.net:4545/gerarelgpsrodado.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
                alert('Relatório está sendo gerado, por favor, aguarde');
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function endAniel() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-endcasa.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function foraEscala() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-foraescalacotaind.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function foraHorario() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-forahorariocotaind.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function extratoKmProj() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-extratokmproj.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }
        function enderecoForaTOA() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-enderecoforatoa.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }
    </script>
<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable({
            "columnDefs": [
                        { "title": "Data e Hora", "width": "50%"}
        });
    });
</script>
</html>