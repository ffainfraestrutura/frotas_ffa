<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '4' && $perfil<>'5' && $perfil<>'6'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

include_once"../func/log.php";
include"conecta.php";
//include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

$hoje = date("Y-m-d");//H:i:s
$autor=$_POST['autor'];
$ccusto1 = $_POST['ccusto1'];
$status = $_POST['status'];
$dataf = $_POST['dataf'];
$datai = $_POST['datai'];

$ccusto2 =str_replace($comAcentos, $semAcentos, $ccusto1);;

if($datai == '' or $dataf==''){
    $datai = date('Y-m-d');
    $dataf = date('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="../src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous">
        </script>

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <!--data tables - css e js-->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        
        <!-- jquery e masks -->
        <!--<script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>-->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"./menu-teste.php"; ?>

<div id="layoutSidenav_content">
    <main>

        <div class="container-fluid px-4">
    		<h1 class="h1 pt-1"> Relatório de Chamados </h1>

            <!--div form-->
            <div class="mt-2">
                <form action='./relatoriochamados.php' method='post'> <!-- /control/teste.php -->
                <div class="d-flex justify-content-start">
                    <div class="col-4">
                        <label class="form-label">Data inicial:<span style="color: red;">*<span></label>
                        <input class="form-control" type="date" name="datai" style="width: 75%" required>
                    </div>

                    <div class="ms-3 col-4">
                        <label class="form-label">Data final:<span style="color: red;">*<span></label>
                        <input class="form-control" type="date" name="dataf" style="width: 75%" required>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-start">
                    <div class="col-4">
                        <label class="form-label">Status:<span style="color: red;">*<span></label>
                        <select name="status" id='status' class='form-select' required> <!-- -->
                        <option value=' '>Selecione... </option>
                        <!--<option value='0'>Todos </option>-->                    
                <?php //idtbstatuscham, status
                $sql2 = "SELECT * FROM bdfrota.tbastatuscham;";
                $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                while ($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)) {
                    $idtbstatuscham=$row2['idtbstatuscham'];
                    $status1=$row2['status'];

                    $selected = ($status == $idtbstatuscham) ? "selected=\"selected\"" : null;
                    print"<option value='$idtbstatuscham' $selected> $status1 </option>";
                }
                ?>
                        </select>                 
                    </div>

                    <div class="ms-3 col-4">
                        <label class="form-label">Centro de custo:</label>
                        <select class="form-select" name="ccusto1" id='ccusto1'>
                            <option value=' '> Selecione... </option>
                        <?php
                        $sql3="SELECT DISTINCT(ccusto) FROM bdfrota.tbchamados WHERE matricula<>'003535';";
                        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                        while($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
                            $ccusto=$row3['ccusto'];

                            print"<option value='$ccusto'> $ccusto </option>";
                        }
                        ?>
                        </select>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-start">
                    <div class="col-4">
                        <label class="form-label">Autor do chamado:</label>
                        <select class="form-select" name="autor" id="autor">
                            <option value=' '> Selecione... </option>
                    <?php
                    $sql4="SELECT DISTINCT(nome) FROM bdfrota.tbchamados;";
                    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

                    while($row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC)){
                        $nome=$row4['nome'];

                        print"<option value='$nome'> $nome </option>";
                    }
                    ?>
                        </select>                  
                    </div>
                </div>

                    <div class="mt-3">
                        <button type='submit' class='btn btn-secondary'> Filtrar </button>
                    </div>

                    <div class='mt-4'>
                        <p style="color: red;"> * Campos obrigatórios. </p>
                    </div>
                </form>  
            </div>
            
            <!--div tabela-->
            <div class="mt-4">
                <table id='tabela1'>
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Data de abertura</th>
                            <th>Data de fechamento</th>
                            <th>Categoria</th>
                            <th>Problema</th>
                            <th>Centro de custo</th>
                            <th>Status</th>
                            <!--<th> </th>-->
                        </tr>
                    </thead>

                    <tbody>
                        
                    <?php

                    //print $autor."<br>".$ccusto1."<br>".$status."<br>".$dataf."<br>".$datai;
                    if($ccusto1 !=' ' && $autor !=' '){
                        //print $autor."<br>".$ccusto1."<br>".$status."<br>".$dataf."<br>".$datai;
                        //print 'opção 1';
                        $sql="SELECT * FROM bdfrota.tbchamados WHERE data_abert >= '".$datai."' AND data_abert<= '".$dataf."' AND status = '".$status."' AND ccusto='".$ccusto2."' AND nome='".$autor."'; ";
                    } elseif($ccusto1 != ' ' && $autor==' '){
                        //print $autor."<br>".$ccusto2."<br>".$status."<br>".$dataf."<br>".$datai."<br>";
                        //print 'opção 2';
                        $sql="SELECT * FROM bdfrota.tbchamados WHERE data_abert >= '".$datai."' AND data_abert<= '".$dataf."' AND status = '$status' AND ccusto='".$ccusto2."' ;";

                        //print "<br>".$sql;
                    } elseif($autor != ' ' && $ccusto1==' '){
                        //print $autor."<br>".$ccusto1."<br>".$status."<br>".$dataf."<br>".$datai;
                        //print 'opção 3';
                        $sql="SELECT * FROM bdfrota.tbchamados WHERE data_abert >= '".$datai."' AND data_abert<= '".$dataf."' AND status = '".$status."' AND nome='".$autor."';";
                    } else {
                        //print $autor."<br>".$ccusto1."<br>".$status."<br>".$dataf."<br>".$datai;
                        //print 'opção 4';
                        $sql="SELECT * FROM bdfrota.tbchamados WHERE data_abert >= '".$datai."' AND data_abert<= '".$dataf."' AND status = '".$status."'; "; 
                    }
                    
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                    //idtbchamados, matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, data_fech
                    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                        $numero = $row['idtbchamados'];
                        $matricula = $row[' matricula'];
                        $nome = $row['nome'];
                        $telefone = $row['telefone'];
                        $ccusto = $row['ccusto'];
                        $placa=$row['placa'];
                        $hodometro=$row['hodometro'];
                        $tipodechamado= $row['tipodechamado'];
                        $tiposinistro=$row['tiposinistro'];
                        $tipomanuprev=$row['tipomanuprev'];
                        $prevcord=$row['prevcord'];
                        $tipomanucor=$row['tipomanucor'];
                        $data_abert=$row['data_abert'];
                        $data_fech=$row['data_fech'];
                        $status=$row['status'];

                        //tratando datas
                        $data_abert = explode(" ", $data_abert);
                        $data_abert = explode("-", $data_abert[0]);
                        $data_abert1 = "$data_abert[2]".'/'."$data_abert[1]".'/'."$data_abert[0]";

                        $data_fech = explode(" ", $data_fech);
                        $data_fech = explode("-", $data_fech[0]);
                        $data_fech1 = "$data_fech[2]".'/'."$data_fech[1]".'/'."$data_fech[0]";

                        
                        print"<tr><td> $numero </td>
                              <td> $data_abert1 </td>
                              <td> $data_fech1 </td>
                              <td> $tipodechamado </td>";

                        if($tipodechamado == 'Comunicado de Sinistro'){
                            print "<td> $tiposinistro </td>";
                        } elseif($tipodechamado == 'Solicitação de Manutenção Preventiva'){
                            print "<td> $tipomanuprev </td>";   
                        } elseif($tipodechamado == 'Solicitação de Manutenção Corretiva'){
                            print "<td> $tipomanucor </td>";
                        }

                        print"<td> $ccusto </td>";

                        if($status == 1){
                            print "<td> Aberto </td>";
                        } elseif($status == 2){
                            print "<td> Fechado </td>";
                        } elseif($status == 3){
                            print "<td> Agendado </td>";
                        } elseif($status == 4){
                            print "<td> Cancelado </td>";
                        }

                        print"
                              <!--<td> <form method='post' action='./visualizarchamado.php'><input type='hidden' name='idtbchamados' id='idtbchamados' value='$numero'><button style='border: none; background-color: transparent;' type='submit' title='Visualizar chamado'> <span class='material-symbols-outlined'> visibility </span></button></form> </td></tr>-->";
                    }              

                    ?>
                        
                    </tbody>
                </table>
            </div>

            <!-- botão gerar excel -->
            <div class='mt-4 mb-4'>
                <form action="./control/gerarexcel.php" method="post">
                <?php 
                print "<input type='hidden' name='datai1' value='$datai'/>
                 <input type='hidden' name='dataf1' value='$dataf'/>
                 <input type='hidden' name='autor1' value='$autor'/>
                 <input type='hidden' name='ccusto2' value='$ccusto1'/>
                 <input type='hidden' name='status2' value='$status'/>"; ?>
                    <button type='submit' class='btn btn-success'> Gerar Excel </button>
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
                </div>!-->
            </div>
        </div>
    </footer>
</div>
            

<script type="text/javascript">
    $(document).ready(function() { 
        $('#tabela1').DataTable({
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
    } );
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="../src/js/datatables-simple-demo.js"></script>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->
</body>

</html>