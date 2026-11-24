<?php
    setlocale(LC_ALL, 'pt_BR.utf8');
    date_default_timezone_set('America/Sao_Paulo');
    session_start();

    // Função para retornar nome do mês em português
    function nomeMesPortugues($numeroMes) {
        $nomesDosMeses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];
        return $nomesDosMeses[(int) $numeroMes];
    }
    
    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];

    /*if ($perfil == null){
        echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
    } elseif ($perfil <> '1' AND $perfil <> '2' AND $perfil <> '3' and $perfil <> '4' and $perfil <> '5'){
        echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    }*/

    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;

    $empresaf = $_POST['empresa'];
    if ($empresaf <> '') {
        $sqlfiltro = "and a.matricula in (SELECT matricula FROM bdaniel.tbfuncionario where status <> 'demitido' and ccusto like '%$empresaf%')";
    }
    $coordenadorf = $_POST['coordenador'];
    if ($coordenadorf <> '') {
        $sqlfiltro = "and a.matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor in (SELECT idtbsupervisor FROM bdfrota.tbsupervisor where idtbcoordenador = $coordenadorf))";
    }
    $supervisorf = $_POST['supervisor'];
    if ($supervisorf <> '') {
        $sqlfiltro = "and a.matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = $supervisorf)";
    }

    include"conecta.php";
    include_once"./func/log.php";
    header("Content-type: text/html; charset=utf-8");

    $data = date('Y-m-d');
    //$data = date('2024-04-12');
    //$nomemes = strftime('%B',strtotime("$data"));
    $anoatual = date('Y',strtotime("$data"));
    $mesatual = date('m',strtotime("$data"));
    $diasemana = strftime('%A',strtotime("$data"));

    //variaveis POST
    $dataesc = $_POST['mesesc'];

    if(empty($dataesc)){
    
        $mesesc = $mesatual;
        $anoesc = $anoatual;
        $nomemes = strftime('%B',strtotime("$data"));

        if ($diasemana == "segunda") {
            $ultseg = date('Y-m-d');
            $proxdom = date('Y-m-d', strtotime($data . 'next ssunday'));
            //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
        } elseif ($diasemana == "domingo") {
                $ultseg = date('Y-m-d', strtotime($data . 'last monday'));
                $proxdom = date('Y-m-d', strtotime($data));
                //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
        } else {
        $ultseg = date('Y-m-d', strtotime($data . 'last monday'));
        $proxdom = date('Y-m-d', strtotime($data . 'next sunday'));
        //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
        }

    }else{
    
        $escolha = explode("-",$dataesc);
        $mesesc = $escolha[1];
        $anoesc = $escolha[0];
        $nomemes = strftime('%B',strtotime("$anoesc-$mesesc-01"));
        $nomemes = strtoupper($nomemes);

        if($mesesc == $mesatual){
            if ($diasemana == "segunda") {
                $ultseg = date('Y-m-d');
                $proxdom = date('Y-m-d', strtotime($data . 'next sunday'));
                //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
            } elseif ($diasemana == "domingo") {
                $ultseg = date('Y-m-d', strtotime($data . 'last monday'));
                $proxdom = date('Y-m-d', strtotime($data));
                //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
            } else {
                $ultseg = date('Y-m-d', strtotime($data . 'last monday'));
                $proxdom = date('Y-m-d', strtotime($data . 'next sunday'));
                //$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
            }
        }
        

    }

    

    if($perfil == 2){
        $sql = "SELECT * FROM bdfrota.tbcoord WHERE matricula='$matricula'; ";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $idtbcoord = $row[0];
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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <style>
        /* Tela de carregamento */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente */
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

        <div id="layoutSidenav_content">
            <main>

                <div class="container-fluid px-4">

                    <div class="callout callout-secondary ms-4">
                        <h1 class="h1 mt-2"></h1>
                    </div>

                    <!--filtros-->
                    <div>
                        <div class="row col-12">
                            <div class="col-lg-12">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Filtros<?php print "";?>
                                    </div>
                                    <div class="card-body">
                                        <form action="./dashboard-ger.php" method="post">
                                            <div class='d-flex justify-content-start'>
                                                <div class='d-flex align-items-center'>
                                                    <label for="mes" class='form-label col-4'>Mês e Ano:</label>
                                                    <input type="month" name="mesesc" id="mes" class='form-control' min="2024-01" <?php print"value='$dataesc'";?>>
                                                </div>
                                                <!-- Filtro de Empresa -->
                                                <div class='d-flex align-items-center me-3'>
                                                    <label for="empresa" class='form-label col-6'>Empresa:</label>
                                                    <select name="empresa" id="empresa" class='form-control'>
                                                        <option value="">TODAS</option>
                                                        <option value="claro">Claro</option>
                                                        <option value="ligga">Ligga</option>
                                                        <option value="tim">Tim</option>
                                                        <!-- Adicione mais opções conforme necessário -->
                                                    </select>
                                                </div>
                                                <!-- Filtro de Coordenador -->
                                                <div class='d-flex align-items-center me-3'>
                                                    <label for="coordenador" class='form-label col-6'>Coordenador:</label>
                                                    <select name="coordenador" id="coordenador" class='form-control'>
                                                        <option value="">TODOS</option>
                                                        <?php
                                                        $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                                        $resultado = mysql_query($sql) or die(mysql_error());
                                                        while($row = mysql_fetch_array($resultado)){
                                                            $idcoord = $row[0];
                                                            $nomecoord = $row[1];

                                                            print"<option value='$idcoord'>$nomecoord</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <!-- Filtro de Supervisor -->
                                                <div class='d-flex align-items-center me-3'>
                                                    <label for="supervisor" class='form-label col-6'>Supervisor:</label>
                                                    <select name="supervisor" id="supervisor" class='form-control'>
                                                        <option value="">TODOS</option>
                                                        <?php
                                                        $sql = "SELECT DISTINCT (a.idtbsupervisor), b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                                        $resultado = mysql_query($sql) or die(mysql_error());
                                                        while($row = mysql_fetch_array($resultado)){
                                                            $idsup = $row[0];
                                                            $nomesup = $row[1];

                                                            print"<option value='$idsup'>$nomesup</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                

                                                
                                                <div>
                                                    <button class='btn btn-success ms-2' type="submit">Filtrar</button>
                                                </div>
                                            </div>  
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div id="loadingScreen" class="loading-screen">
                        <div>
                            <h2>Aguarde, carregando...</h2>
                        </div>
                    </div>

                    <!--div graficos-->
                    <div id='conteudo' class="container-fluid px-4 mt-4 d-none">
                        <div class="row col-12">
                            <!--consumo de combustível-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <!--<i class="fas fa-chart-bar me-1"></i>
                                    Dash Combustível <?php print "$anoesc"; ?>-->

                                    <i class="fas fa-chart-bar me-1"></i>
                                    Dash Combustível até <?php print "$nomemes/$anoesc";?>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="dashcombustivel" width="600" height="310"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!--farol de consumo-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    <?php
                                    if($mesatual == $mesesc){
                                        print "Orçamento Semanal";
                                    }else{
                                        print "Orçamento Mensal $nomemes/$anoesc";
                                    }
                                    ?>
                                    
                                    </div>
                                    <div class="card-body">
                                        <canvas id="orcsemanal" width="600" height="200"></canvas>
                                        <?php 
                                            if($mesatual == $mesesc){
                                                $sql="SELECT  ROUND(sum(a.orcsemanal),0) AS orcsemanal, ROUND(sum(a.valoraplicado),0) AS saldo FROM bdfrota.tbsaldo a, bdfrota.tbusuario b  where data='$ultseg' AND a.matricula = b.matricula $sqlfiltro";
                                            }else{
                                                $sql="SELECT  ROUND(sum(a.orcsemanal),0) AS orcsemanal, ROUND(sum(a.valoraplicado),0) AS saldo FROM bdfrota.tbsaldo a, bdfrota.tbusuario b  where YEAR(data)='$anoesc' AND MONTH(data)='$mesesc' AND a.matricula = b.matricula $sqlfiltro";
                                            }                                       
                                                                                        
                                            $resultado = mysql_query($sql) or die (mysql_error());
                                            $row = mysql_fetch_array($resultado);
                                                $orcsemanal = $row[0];
                                                $consumo = $row[1];
                                                
                                            if($diasemana == "segunda" || $diasemana=="terça"){
                                                $bloqueado = round((0.6*$orcsemanal),2);     
                                            }elseif($diasemana == "quarta" || $diasemana=="quinta"){
                                                $bloqueado = round((0.6*$orcsemanal),2);
                                            }else{
                                                $bloqueado = 0;
                                            }

                                            $disponivel = $orcsemanal-$bloqueado;

                                            $sql2="SELECT COALESCE(ROUND(SUM(a.valor),0),0) FROM bdfrota.tbsupervisor a where valor <> 0 $sqlfiltro";                                        
                                            $resultado2 = mysql_query($sql2) or die (mysql_error());
                                            $row2 = mysql_fetch_array($resultado2);
                                                $saldoatual = $row2[0];

                                            if($mesatual == $mesesc){
                                                $ultseg = new DateTime($ultseg);
                                                $datalim = new DateTime($data);
                                                $proxdom = new DateTime($proxdom);

                                                $dateInterval1 = $ultseg->diff($datalim);
                                                $intervalo = $dateInterval1->format('%a');

                                                $intervalo = $intervalo + 1;
                                                
                                                $dateInterval2 = $ultseg->diff($proxdom);
                                                $intervalo2 = $dateInterval2->format('%a');
                                                
                                                if($intervalo!=0){
                                                    $consmedio = $consumo/$intervalo;
                                                    $projecao = round(($consmedio * $intervalo2),0);
                                                }else{
                                                    $projecao = $orcsemanal;
                                                }
                                            }else{
                                                
                                            }                                         
                                            if($mesatual == $mesesc){
                                                print"
                                                <table class='m-auto col-7' style='font-size: 11px;'>
                                                    <tr>
                                                        <td style='text-align:left;'>Orçamento Semanal dos Técnicos:</td>
                                                        <td style='text-align:right;' id='valorc'>R$ $orcsemanal</td>
                                                    </tr>

                                                    <!--<tr>
                                                        <td style='text-align:left;'>Bloqueado</td>
                                                        <td style='text-align:right;'>R$ $bloqueado</td>
                                                    </tr>

                                                    <tr>
                                                        <td style='text-align:left;'>Em uso</td>
                                                        <td style='text-align:right;'>R$ $disponivel</td>
                                                    </tr>-->

                                                    <tr>
                                                        <td style='text-align:left;'>Saldo Distribuível Atual:</td>
                                                        <td style='text-align:right;'>R$ $saldoatual</td>
                                                    </tr>

                                                    <tr>
                                                        <td style='text-align:left;'>Valor Aplicado:</td>
                                                        <td style='text-align:right;'  id='valcon'>R$ $consumo</td>
                                                    </tr>

                                                    <tr>
                                                        <td style='text-align:left;'>Projeção de consumo:</td>
                                                        <td style='text-align:right;' id='valproj'>R$ $projecao</td>
                                                    </tr>
                                                </table>";
                                            }else{
                                                print"
                                                <table class='m-auto col-7' style='font-size: 11px;'>
                                                    <tr>
                                                        <td style='text-align:left;'>Orçamento Recibido:</td>
                                                        <td style='text-align:right;' id='valorc'>R$ $orcsemanal</td>
                                                    </tr>

                                                    <tr>
                                                        <td style='text-align:left;'>Valor Aplicado:</td>
                                                        <td style='text-align:right;'  id='valcon'>R$ $consumo</td>
                                                    </tr>
                                                </table>";
                                            }

                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row col-12">
                            <!--grafico 2 num pedidos-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Consumo Total <?php print "$nomemes/$anoesc";?>
                                    
                                    </div>
                                    <div class="card-body m-auto">
                                        <canvas id="consumototal" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!--grafico 3-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Cota Extra Indevida <?php print "$nomemes/$anoesc";?>
                                    </div>
                                    <div class="card-body m-auto">
                                        <canvas id="cei" width="300" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row col-12">
                            <!--performance-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Performance (R$/l) <?php print "$nomemes/$anoesc";?>
                                    </div>
                                    <div class="card-body m-auto">
                                        <canvas id="performancers" width="300px" height="300px"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!--grafico 3-->
                            <div class="col-lg-6">
                                <div class="card mb-4 text-center">
                                    <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Performance (Km/l) <?php print "$nomemes/$anoesc";?>
                                    </div>
                                    <div class="card-body m-auto">
                                        <canvas id="performancekm" width="300px" height="300px"></canvas>
                                    </div>
                                </div>
                            </div>
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
                        </div>!-->
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/chart.js' crossorigin="anonymous"></script>
        
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

        <!--script graficos-->
        <script type="text/javascript">
            //estilos do chart
            // Set new default font family and font color to mimic Bootstrap's default styling
            /*Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';*/

            //inserindo chart dash combustivel
            var ctx = document.getElementById("dashcombustivel");
            var dashcombustivel = new Chart(ctx, {
                data: {
                    datasets: [{
                        type: 'bar',
                        label: 'Orçamento mensal',
                        data: [
                        <?php
                            $totalanual = 0;
                            //for($i=1; $i <= $mesatual ; $i++) {
                            for($i=1; $i <= $mesesc ; $i++) {
                                $sql="SELECT ROUND(sum(a.valoraplicado),2)  FROM bdfrota.tbsaldo a, bdfrota.tbusuario b  where YEAR(a.data)=$anoatual and MONTH(a.data)=$i AND a.matricula = b.matricula $sqlfiltro";
                                $resultado = mysql_query($sql) or die (mysql_error());
                                $row = mysql_fetch_array($resultado);
                                    $totalmensal = $row[0];
                                
                                $totalanual = $totalanual + $totalmensal;
                                if($i != $mesatual){
                                    print "$totalmensal,";
                                }else{
                                    print "$totalmensal";
                                }                        
                            }
                            /*if($mesesc == 1 || $mesesc == 2 || $mesesc == 3){


                            }else{

                            }*/
                        ?>],
                        backgroundColor: ['#1F4E79',],
                    }, /*{
                        type: 'line',
                        label: 'Line Dataset',
                        data: [
                            <?php
                                
                                for($i=1; $i <= $mesatual ; $i++) { 
                                    $sql="SELECT ROUND(sum(orcsemanal),2) FROM bdfrota.tbsaldo where YEAR(data)=$anoatual and MONTH(data)=$i;"; 
                                    $resultado = mysql_query($sql) or die (mysql_error());
                                    $row = mysql_fetch_array($resultado);
                                        $totalmensal = $row[0];

                                    $sql1="SELECT COUNT(matricula) FROM bdfrota.tbsaldo where YEAR(data)=$anoatual and MONTH(data)=$i;"; 
                                    $resultado1 = mysql_query($sql1) or die (mysql_error());
                                    $row1 = mysql_fetch_array($resultado1);
                                        $nummatr = $row1[0];
                                    
                                    $percp = $totalmensal/$nummatr;

                                    print "$percp,";
                                }
                            ?>
                        ],
                    }*/],
                    labels: [<?php
                        
                        $mes = date('m',strtotime("$data"));
                        $nomemes = strftime('%B',strtotime("$data"));
                        //print $mes;
                        for ($i=1; $i <= $mesesc ; $i++) {                         
                            $dateObj = DateTime::createFromFormat('!m', $i);
                            $nomemes = nomeMesPortugues($i);
                            
                            print " '$nomemes',";
                        }
                    ?>]
                },
                //options: options
            });
            
            //grafico consumo total
            var consumototal = document.getElementById("consumototal");
            const data2 = {
                labels: [
                    'Uso Normal',
                    'Cota Extra Devida', 
                    'Cota Extra Indevida',
                    'Deslocamento',
                ],
                datasets: [{
                    label: ' Consumo Total R$',
                    
                    backgroundColor: [
                    '#1F4E79',
                    '#2E75B6',
                    '#9DC3E6',
                    '#DEEBF7',
                    ],
                    hoverOffset: 4,

                    data: [<?php
                        //orc semanal
                        //$sql1 = "SELECT SUM(orcsemanal) FROM bdfrota.tbsaldo WHERE YEAR(data)=$anoesc AND MONTH(data)=$mesesc;";
                        $sql="SELECT  ROUND(sum(a.orcsemanal),2)  FROM bdfrota.tbsaldo a, bdfrota.tbusuario b  where YEAR(a.data)=$anoesc and MONTH(a.data)=$mesesc AND a.matricula = b.matricula $sqlfiltro";

                        $resultado = mysql_query($sql) or die (mysql_error());
                        $row = mysql_fetch_array($resultado);
                            $usonormal = $row[0];
                            $usonormal = str_replace(",",".", $usonormal);
                            
                        //cota extra devida
                        //$sql2 = "SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac WHERE YEAR(data)=$anoesc AND MONTH(data)=$mesesc AND cota_validade=0 AND matsup = '$matricula';";                        
                        $sql2="SELECT ROUND(sum(a.valorinserido),2)  FROM bdfrota.tbcotaextraac a, bdfrota.tbusuario b WHERE YEAR(a.data)=$anoesc and MONTH(a.data)=$mesesc AND a.cota_validade=0 AND a.matricula=b.matricula $sqlfiltro";                        
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                            $cotaextradev = $row2[0];
                            $cotaextradev = round($cotaextradev, 2);
                            $cotaextradev = str_replace(",",".", $cotaextradev);
                    
                        //cota extra indevida
                        //$sql3 = "SELECT SUM(valorinserido) FROM bdfrota.tbcotaextraac WHERE YEAR(data)=$anoesc AND MONTH(data)=$mesesc AND cota_validade=1;";
                        
                        //$sql3="SELECT ROUND(SUM(valorinserido),2) FROM bdfrota.tbcotaextraac WHERE YEAR(data)=$anoesc AND MONTH(data)=$mesesc AND cota_validade=1 AND matsup = '$matricula';";
                        $sql3 = "SELECT ROUND(sum(a.valorinserido),2)  FROM bdfrota.tbcotaextraac a, bdfrota.tbusuario b WHERE YEAR(a.data)=$anoesc and MONTH(a.data)=$mesesc AND a.cota_validade=1 AND a.matricula=b.matricula $sqlfiltro;";
                        $resultado3 = mysql_query($sql3) or die (mysql_error());
                        $row3 = mysql_fetch_array($resultado3);
                            $cotaextraindev = $row3[0];
                            $cotaextraindev = round($cotaextraindev, 2);
                            $cotaextraindev = str_replace(",",".", $cotaextraindev);

                        //cota de deslocamento
                        $sql4 = "SELECT ROUND(SUM(a.valor),2) FROM bdfrota.tbcotarotasup a WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.decisao=1 AND a.matsup='$matricula' $sqlfiltro;";
                        $resultado4 = mysql_query($sql4) or die (mysql_error());
                        $row4 = mysql_fetch_array($resultado4);
                            $cotadesloc = $row4[0];
                            $cotadesloc = round($cotadesloc, 2);
                            $cotadesloc = str_replace(",",".", $cotadesloc);
                    
                        print "'$usonormal', '$cotaextradev', '$cotaextraindev', '$cotadesloc'";?>],
                }]

            };
            var myLineChart = new Chart(consumototal, {
                type: 'doughnut',
                data: data2,
                options: {
                    responsive: false
                }
            });

            //grafico cota extra indevida
            var cei = document.getElementById("cei");
            const data3 = {
                labels: [
                    'Saldo no Tanque',
                    'Fora Horário',
                    'Fora Escala'
                ],
                datasets: [{
                    label: 'Cota Extra Indevida R$',
                    data: [<?php
                            $valorfe = 0;
                            $valorfh = 0;
                            $sldtanque = 0;

                            $sql = "SELECT 
                                        CASE
                                            WHEN a.foraescala = 1 THEN 'Fora Escala'
                                            WHEN a.forahorario = 1 THEN 'Fora Horario'
                                            ELSE 'Tanque'
                                        END AS grupo,
                                        SUM(a.valorinserido) AS soma_valor
                                    FROM 
                                        bdfrota.tbcotaextraac a, bdfrota.tbusuario b
                                    WHERE 
                                        YEAR(a.data)=$anoesc and MONTH(a.data)=$mesesc AND a.cota_validade=1 AND a.matricula=b.matricula $sqlfiltro 
                                    GROUP BY 
                                        grupo
                                    ";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            while($row = mysql_fetch_array($resultado)){
                                

                                if ($row['grupo'] == 'Fora Escala') {
                                    $valorfe = $row['soma_valor'];
                                }
                                if ($row['grupo'] == 'Fora Horario') {
                                    $valorfh = $row['soma_valor'];
                                }
                                if ($row['grupo'] == 'Tanque') {
                                    $sldtanque = $row['soma_valor'];
                                }

                            }
                        
                            print " '$sldtanque', '$valorfh', '$valorfe'";
                        ?>],
                    
                    backgroundColor: [
                    '#1F4E79',
                    '#2E75B6',
                    '#9DC3E6',
                    ],
                    hoverOffset: 4
                }]
            };
            var myLineChart = new Chart(cei, {
                type: 'pie',
                data: data3,
                options: {
                    responsive: false,
                }                
            });

            //grafico performance por litro
            const grafperformancers = document.getElementById("performancers");            
            const data4 = {
                labels: ['Leve','Utilitário'],
                
                datasets: [
                    {
                        label: 'Péssimo',
                        data: [<?php
                                $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)>7 $sqlfiltro;";
                                
                                $resultado = mysql_query($sql) or die (mysql_error());
                                $row = mysql_fetch_array($resultado);
                                $pesl = $row[0]; 
                                
                                $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)>7 $sqlfiltro;";
                                $resultado = mysql_query($sql) or die (mysql_error());
                                $row = mysql_fetch_array($resultado);
                                $pesu = $row[0];
                                
                                print "$pesl, $pesu"?>],

                        backgroundColor: '#F70604',
                    },
                    {
                        label: 'Muito Abaixo',
                        data: [<?php
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<7 AND (a.valor/a.litros)>=6.6 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $mtabxl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<7 AND (a.valor/a.litros)>=6.6 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $mtabxu = $row[0];
                            
                            print "$mtabxl, $mtabxu"?>],

                        backgroundColor: '#FFBE05',
                    },
                    {
                        label: 'Abaixo',
                        data: [<?php
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<6.6 AND (a.valor/a.litros)>=6 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $abxl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<6.6 AND (a.valor/a.litros)>=6.2 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $abxu = $row[0];
                            
                            print "$abxl, $abxu"?>],

                        backgroundColor: '#F7DC6D',
                    },

                    {
                        label: 'Esperado',
                        data: [<?php
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<6.2 AND (a.valor/a.litros)>=5.8 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $espl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<6.2 AND (a.valor/a.litros)>=5.8 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $espu = $row[0];
                            
                            print "$espl, $espu"?>],

                        backgroundColor: '#3074B6',
                    },

                    {
                        label: 'Acima',
                        data: [<?php 
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<5.8 AND (a.valor/a.litros)>=5.4 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $acl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<5.8 AND (a.valor/a.litros)>=5.4 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $acu = $row[0];
                        
                            print "$acl, $acu"?>],
                        backgroundColor: '#92D14F',
                    },

                    {
                        label: 'Muito Acima',
                        data: [<?php
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<5.4 AND (a.valor/a.litros)>=5 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $mtacl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<5.4 AND (a.valor/a.litros)>=5 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $mtacu = $row[0];
                        
                            print "$mtacl, $mtacu"?>],
                        backgroundColor: '#538134',
                    },

                    {
                        label: 'Excelente',
                        data: [<?php
                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=1 and a.placa=c.placa AND (a.valor/a.litros)<=5 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $excl = $row[0];

                            $sql="SELECT count(a.placa) from bdfrota.tbabastecimento a, bdfrota.tbusuario b, bdfrota.tbveiculo c WHERE YEAR(a.data)=$anoesc AND MONTH(a.data)=$mesesc AND a.matricula=b.matricula  AND c.tipovel=2 and a.placa=c.placa AND (a.valor/a.litros)<=5 $sqlfiltro;";
                            $resultado = mysql_query($sql) or die (mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $excu = $row[0];
                        
                            print "$excl, $excu"?>],
                        backgroundColor: '#3D4F1F',
                    },
                ]
                };
            var performancers = new Chart(grafperformancers, {
                type: 'bar',
                data: data4,
                options: {
                    plugins: {
                        title: {
                            display: true,
                            //text: 'Chart.js Bar Chart - Stacked'
                        },
                    },
                    //responsive: true,                    
                    responsive: false,
                
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });

            const grafperformancekm = document.getElementById("performancekm");            
            const data5 = {
                labels: ['Leve','Utilitário'],
                
                datasets: [
                <?php
                    $pesl = 0;
                    $pesu = 0;

                    $mtabxl = 0;
                    $mtabxu = 0;

                    $abxl = 0;
                    $abxu = 0;

                    $espl = 0;
                    $espu = 0;

                    $acl = 0;
                    $acu = 0;

                    $mtacl = 0;
                    $mtacu = 0;

                    $excl = 0;
                    $excu = 0;

                    //$sql="SELECT distinct(a.placa) AS placa, b.tipovel AS tipo from bdfrota.tbabastecimento a, bdfrota.tbveiculo b where year(a.data)=$anoatual and month(a.data)=$mesatual AND a.placa=b.placa order by a.placa;";
                    $sql ="SELECT distinct(a.placa) AS placa, b.tipovel AS tipo from bdfrota.tbabastecimento a, bdfrota.tbveiculo b, bdfrota.tbusuario c where year(a.data)=$anoatual and month(a.data)=$mesatual AND a.matricula=c.matricula AND a.placa=b.placa $sqlfiltro order by a.placa;";
                    $resultado = mysql_query($sql) or die (mysql_error());
                    
                    while($row = mysql_fetch_array($resultado)){
                        $placa = $row[0];
                        $tipovel = $row[1];

                        $sql1 = "SELECT sum(litros), max(hodometro), min(hodometro), placa from bdfrota.tbabastecimento where year(data)=$anoesc and month(data)=$mesesc AND placa='$placa';";
                        $resultado1 = mysql_query($sql1) or die (mysql_error());
                        $row1 = mysql_fetch_array($resultado1);
                            $somalitros = $row1[0];
                            $maxhod = $row1[1];
                            $minhod = $row1[2];

                        $rend = ($maxhod-$minhod)/$somalitros;
                        
                        if($rend<=6.05){
                            if($tipovel == 1){
                                $pesl = $pesl+1;
                            }elseif ($tipovel == 2) {
                                $pesu = $pesu+1;
                            }                           
                        }elseif($rend>6.05 && $rend<=7.69){
                            if($tipovel == 1){
                                $mtabxl = $mtabxl+1;
                            }elseif ($tipovel == 2) {
                                $mtabxu = $mtabxu+1;
                            }                           
                        }elseif($rend>7.69 && $rend<=9.34){
                            if($tipovel == 1){
                                $abxl = $abxl+1;
                            }elseif ($tipovel == 2) {
                                $abxu = $abxu+1;
                            }                           
                        }elseif($rend>9.34 && $rend<=12.65){
                            if($tipovel == 1){
                                $espl = $espl+1;
                            }elseif ($tipovel == 2) {
                                $espu = $espu+1;
                            }                          
                        }elseif($rend>12.65 && $rend<=14.30){
                            if($tipovel == 1){
                                $acl = $acl+1;
                            }elseif ($tipovel == 2) {
                                $acu = $acu+1;
                            }
                        }elseif($rend>14.30 && $rend<=15.95){
                            if($tipovel == 1){
                                $mtacl = $mtacl+1;
                            }elseif ($tipovel == 2) {
                                $mtacu = $mtacu+1;
                            }
                        }elseif($rend>15.95){
                            if($tipovel == 1){
                                $excl = $excl+1;
                            }elseif ($tipovel == 2) {
                                $excu = $excu+1;
                            }
                        }
                    }          
                    
                    print"{
                        label: 'Péssimo',
                        data: [$pesl, $pesu],
                        backgroundColor: '#F70604',
                    },

                    {
                        label: 'Muito Abaixo',
                        data: [$mtabxl, $mtabxu],
                        backgroundColor: '#FFBE05',
                    },
                    {
                        label: 'Abaixo',
                        data: [$abxl, $abxu],
                        backgroundColor: '#F7DC6D',
                    },

                    {
                        label: 'Esperado',
                        data: [$espl, $espu],
                        backgroundColor: '#3074B6',
                    },

                    {
                        label: 'Acima',
                        data: [$acl, $acu],
                        backgroundColor: '#92D14F',
                    },

                    {
                        label: 'Muito Acima',
                        data: [$mtacl, $mtacu],
                        backgroundColor: '#538134',
                    },
                    {
                        label: 'Excelente',
                        data: [$excl, $excu],
                        backgroundColor: '#3D4F1F',
                    },"?>
                ]
            };
            var performancers = new Chart(grafperformancekm, {
                type: 'bar',
                data: data5,
                options: {
                    //indexAxis: 'y',
                    plugins: {
                        title: {
                            display: true,
                            //text: 'Chart.js Bar Chart - Stacked'
                        },
                    },
                    //responsive: true,
                    responsive: false,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });

            var orcsemanal = document.getElementById("orcsemanal");
            var valorc = $("#valorc").text();
            if(valorc.includes("R$ ")){
                valorc = valorc.replace("R$ ", '');                
            }
            valorc = parseFloat(valorc);

            var valcons = $("#valcon").text();//valor consumo
            if(valcons.includes("R$ ")){
                valcons = valcons.replace("R$ ", '');                
            }
            valcons = parseInt(valcons);

            var valproj = $("#valproj").text();//valor consumo
            if(valproj.includes("R$ ")){
                valproj = valproj.replace("R$ ", '');                
            }
            valproj = parseInt(valproj);

            /*console.log(typeof(valproj));
            console.log(valproj)*/

            const labels1 = ['Orç. Sem.', 'Cons.','Proj.'];
            const data6 = {                
                datasets: [{                    
                    label: 'Orç. Sem.',
                    //data: [1736.8, valorc,valorc],
                    data: [valorc, valcons,valproj],
                    fill: false,
                    /*backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',                   
                    ], */
                    backgroundColor: [
                        '#2E75B6',
                        '#9DC3E6',
                        '#DEEBF7',
                    ],                  
                    
                    borderWidth: 1
                }],
                labels: labels1
            };            
            var orcsemanal2 = new Chart(orcsemanal, {
                type: 'bar',
                data: data6,
                options: {
                    indexAxis: 'y',

                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });
        </script>
    <script>
        // Oculta a tela de carregamento após a página carregar
        window.addEventListener('load', function () {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('conteudo').classList.remove('d-none');
        });
    </script>
        
        
        
    </body>

</html>