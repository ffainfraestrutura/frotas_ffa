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

$diaf = $_POST["diaf"];
$diai = $_POST["diai"];


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
        
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!--datatable-->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">

        <style>
            #tabela1 {
                table-layout: fixed;
                width: 100% !important;
            }
            #tabela1 td,
            #tabela1 th{
                width: auto !important;
                white-space: normal;
                text-overflow: ellipsis;
                overflow: hidden;
            }
        </style>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Cotas Extra Aprovadas </h1>

<form action="./detalhes-cota-extra-aprovadas-sup.php" method="post">

<div class="row" style="width: 100%;">     
<h5><b>Selecione o periodo:</b></h5>
     <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Início:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diai">
    </div>

    <div class="col-md-3">
      <label for="exampleInputEmail1"><h5>Fim:</h5></label>
      <input min="2023-03-17" type="date" class="form-control form-control-md" name="diaf">
    </div></div>   

<div class='mt-2' style="width: 50%;">
   

        <div class="mt-4">
            <input type="submit" class="btn btn-success"  value="Filtrar">
        </div>


        <!--</form>
    </div>-->
</div>
      </form>  



<div class="mt-4"><h5 class="text-danger"><b><?php if ($diaf <> '') {
    $dataf = explode("-", $diaf);
    $datat = $dataf[2]."/".$dataf[1]."/".$dataf[0];
    $datai = explode("-", $diai);
    $datati = $datai[2]."/".$datai[1]."/".$datai[0];
    print"Dados dos dias $datati até $datat";

} ?></b></h5></div>

        <div class="card mt-4 mb-4">
            <div class="card-header">
                <a href="./dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard
                </a href="./dashboard.php">
            </div>
             <div class="card-body" style="width:100%">
                <!--tabela para entrada de dados-->
                <table id='tabela1' class='table table-bordered' style="width:100%">
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Placa</th>
                            <th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>KM GPS</th>
                            <th>Orc. Semanal</th>
                            <th>Sld. Remanejamento</th>
                            <th>Fundo Frota</th>
                            <th>Sld. Cartão</th>
                            <th>Tot. Extra</th>
                            <th>Data Pedido</th>
                            <th>Justificativa</th>
                            <th>Valor Pedido</th>
                            <th>Valor Inserido</th>
                            <th>Data Aceito</th>
                            <th>Tipo Cota</th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php     
                        if ($diaf == '' or $diai == '') {
    
                            print "
                            <tr>
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
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                                <td>Sem Dados</td>
                            </tr>";
                        } else {
                            $sql = "SELECT matricula, orcsemanal, kmproj, kmos, gps, sldcartao, ROUND(totalextra,2), data, justificativa, valor, valorinserido, dataaceite, tipo_cota FROM bdfrota.tbcotaextraac where matsup = '$matricula' and dataaceite > '$diai 00:00:00' and dataaceite < '$diaf 23:39:59' order by data";
                            $resultado = mysql_query($sql) or die (mysql_error());
                           
                            while ($row = mysql_fetch_array($resultado)){
                                $mattec = $row[0];
                                $orcsemanal = $row[1];
                                $kmproj = $row[2];
                                $kmos = $row[3];
                                $gps = $row[4];
                                $sldcartao = $row[5];
                                $totalextra = $row[6];
                                $data = $row[7];
                                $justificativa = $row[8];
                                $valor = $row[9];
                                $valorinserido = $row[10];
                                $dataaceite = $row[11];
                                $tipo_cota = $row[12];
                                
                                if ($tipo_cota == 1) {
                                $tipo_cota = 'Emergencial';
                                } else {
                                    $tipo_cota = "Extra";
                                }

                                $data1 = new DateTime("$data");
                                $datav = $data1-> format('d/m/Y H:i:s');

                                $dataaceite1 = new DateTime("$dataaceite");
                                $dataaceitev = $dataaceite1-> format('d/m/Y H:i:s');

                                $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$mattec'";
                                $resultado2 = mysql_query($sql2) or die (mysql_error());
                                $row2 = mysql_fetch_array($resultado2);
                                $placa = $row2[0];

                                $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
                                $resultado2 = mysql_query($sql2) or die (mysql_error());
                                $row2 = mysql_fetch_array($resultado2);
                                $nometec = $row2[0];

                                $ultseg = date('Y-m-d', strtotime($diaf . 'last monday'));
                                //remanejamento positivo
                                $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$diaf 23:59:59' AND tipo=1;";                          
                                $resultado4 = mysql_query($sql4) or die (mysql_error());
                                $row4 = mysql_fetch_array($resultado4);
                                $remanejpositivo = $row4[0];
                                
                                //remanejamento negativo
                                $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$diaf 23:59:59' AND tipo=0;";                          
                                $resultado5 = mysql_query($sql5) or die (mysql_error());
                                $row5 = mysql_fetch_array($resultado5);
                                $remanejnegativo = $row5[0];
                                
                                $sldremanej = $remanejpositivo-$remanejnegativo;

                                //saldo fundo frota
                                $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$diaf 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                                $resultado6 = mysql_query($sql6) or die (mysql_error());
                                $row6 = mysql_fetch_array($resultado6);
                                $ffrota = $row6[0];
                                $ffrota = str_replace(".", ",", $ffrota);

                                print "
                                <tr>
                                    <td>$mattec</td>
                                    <td>$nometec</td>
                                    <td>$placa</td>
                                    <td>$kmproj km</td>
                                    <td>$kmos km</td>
                                    <td>$gps km</td>
                                    <td>R$ $orcsemanal</td>
                                    <td>R$ $sldremanej</td>
                                    <td>R$ $ffrota</td>
                                    <td>R$ $sldcartao</td>
                                    <td>R$ $totalextra</td>
                                    <td>$datav</td>
                                    <td>$justificativa</td>
                                    <td>R$ $valor</td>
                                    <td>R$ $valorinserido</td>
                                    <td>$dataaceitev</td>
                                    <td>$tipo_cota</td>
                                    <td> <form action='./detalhes-deslocamento-sup.php' method='post'>
                                        <input type='hidden' name='diai2' value='$diai'/>
                                        <input type='hidden' name='diaf2' value='$diaf'/>
                                        <input type='hidden' name='mattec' value='$mattec' />
                                        <input type='hidden' name='matsup' value='$matricula' />
                                        <input type='submit' class='btn btn-success'  value='Detalhes'> 
                                    </form> 
                                    </td>
                                </tr>";
                            }

                            
                        }
                        ?>
                        
                    </tbody>
                </table>
                
                <!--<form action="./control/gerarexcelcota-extra-aprovada.php" method="post">
                    <?php 
                    print "<input type='hidden' name='diai' value='$diai'>";
                    print "<input type='hidden' name='diaf' value='$diaf'>";
                    print "<input type='hidden' name='matsupf' value='$matricula'>"; ?>
                    <div class="form-group col-md-2">
                        <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                        <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                    </div>
                </form>-->
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
<!--<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->

<!--script para criar data table-->
<script>
    new DataTable('#tabela1', {
        "scrollX": true,
        order: [[10, 'desc']],
        layout: {
            topStart: {
            //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            buttons: ['excel']
            }
        }
    });

    //$(".table").addClass("compact w-100");

</script>
</html>