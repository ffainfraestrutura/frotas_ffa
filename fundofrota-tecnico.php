<?php
    setlocale(LC_ALL, 'pt_BR.utf8');
    date_default_timezone_set('America/Sao_Paulo');
    session_start();
    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];
    
    if ($perfil == null){
        echo"<script language='javascript' type='text/javascript'>alert('$perfil')</script>";
        //;window.location=\"index.php\"
    } elseif ($perfil <> '4' && $perfil<>'5'){
        echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    }
    
    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;

    $data = date('Y-m-d');
    $ultseg = date('Y-m-d', strtotime($data . 'last monday')); //última segunda
    $ultdom = date('Y-m-d', strtotime($data . 'last sunday')); //última domingo

    include"conecta.php";
    include_once"./func/log.php";
    include_once"./func/consultacartao.php";

    header("Content-type: text/html; charset=utf-8");

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

    $idtbsup = $_POST['supervisor'];
    $tecnico = $_POST['tecnico'];
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
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

        
        
    </head>


    <!--<body class="sb-nav-fixed vsc-initialized">-->
    <body class='sb-nav-fixed vsc-initialized sb-sidenav-toggled'>

        <!--nav-->
        <?php include"menu.php"; ?>

        <div id="layoutSidenav_content">

            <main style="width: 100%;">

                <div class="container-fluid px-4" style="width: 100%;">

        
                    <h1 class="h1 pt-2 pb-2"> Fundo Frota </h1>
                    
                    <div class='d-flex align-items-center'>
                        <?php
                            //$sql1 = "SELECT ((round(sum(orcsemanal),2)*5)/100) AS saldofrota FROM bdfrota.tbsaldo where data > '$ultdom'";
                            $sql1 = "SELECT saldoatual, idtbsaldofrota FROM bdfrota.tbsaldofrota WHERE data_e_hora='$ultseg';";
                            $resultado1 = mysql_query($sql1) or die (mysql_error());
                            $row1 = mysql_fetch_array($resultado1);
                            $saldofrota = $row1[0];

                            $saldofrota = round($saldofrota, 2);
                            $saldofrotav = round($saldofrota, 2);

                            print"<h3 class='h3 pt-2 pb-2'> R$ $saldofrotav</h3>";
                        ?>
                        <a href='./fundofrota-extrato.php'><span class="material-symbols-outlined ms-2">search</span></a>
                    </div>

                    <div>
                        <form action='./fundofrota-tecnico.php' method='post' class='mb-2'>
                            <div class='d-flex'>
                                <div class="col-4">
                                    <label>Coordenador:</label>
                                    <select class="form-select" name="coordenador" id="selectcoordenador">
                                        <option value=" "> Selecione o coordenador </option>
                                        <?php
                                        $i=0;
                                        $sql2 = "SELECT DISTINCT (a.idtbcoordenador), b.nome,b.matricula FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        while($row2 = mysql_fetch_array($resultado2)){
                                            $idcoord = $row2[0];
                                            $nomecoord = $row2[1];
                                            $matcoord = $row2[2];

                                            $addmatriz = array($idcoord => $i );
                                            
                                            foreach($addmatriz as $key => $value){
                                                $idselect[$key] = $value;
                                            }
                                            $i++;
                                            
                                                print"<option id='$idselect[$idcoord]' value='$matcoord'>$nomecoord</option>";
                                            
                                        }
                                        ?>
                                    </select>
                                </div>
                            
                                <div class="ms-1 col-4">
                                    <label>Supervisor:</label>
                                    <select class="form-select" name="supervisor" id="selectsupervisor">
                                        <option value=""> Selecione o supervisor </option>
                                        <?php
                                            $i=0;
                                            $idselect;

                                            $sql3 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome, a.idtbsupervisor FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador ORDER BY b.nome;";                        
                                            $resultado3 = mysql_query($sql3) or die(mysql_error());
                                            while($row3 = mysql_fetch_array($resultado3)){
                                                $matsupf = $row3[0];
                                                $idcoord = $row3[1];
                                                $nomesup = $row3[2];
                                                $idtbsupervisor =  $row3[3];

                                                $selected = ($idtbsup == $idtbsupervisor) ? "selected=\"selected\"" : null;

                                                $addmatriz = array($matsup => $i );

                                                foreach($addmatriz as $key => $value){
                                                    $idselect[$key] = $value;
                                                }
                                                $i++;
                                                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]' value='$idtbsupervisor' $selected>$nomesup</option>";
                                            }

                                        ?>
                                    </select>
                                </div>

                                <div class="ms-1 col-4">
                                    <label>Técnico:<span style='color:red;'>*</span></label>
                                    <select class="form-select mb-3" name="tecnico" id="selecttecnico" required>
                                        <option value=" "> Selecione o técnico </option>
                                        <?php
                                        $i=0;

                                        $sql3 ="SELECT DISTINCT (a.matricula), b.nome, c.matricula, d.idtbcoordenador FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbsupervisor c, bdfrota.tbcoord d where a.matricula = b.matricula AND a.idtbsupervisor = c.idtbsupervisor AND c.idtbcoordenador = d.idtbcoordenador ORDER BY b.nome;";                        
                                        $resultado3 = mysql_query($sql3) or die(mysql_error());
                                        while($row3 = mysql_fetch_array($resultado3)){
                                            $mattecf = $row3[0];
                                            $nometec = $row3[1];
                                            $matsupf = $row3[2];
                                            $idcoord = $row3[3];

                                            $selected = ($mattecf == $tecnico) ? "selected=\"selected\"" : null;

                                            print"<option id='$idselect[$idcoord]-$idselect[$matsupf]-$i' value='$mattecf' $selected>$nometec </option>";
                                        }
                                        $i++;

                                        ?>
                                    </select>
                                    
                                </div> 
                            </div>

                            <div class='mt-2' style="width: 50%;">
                                <input type="submit" class="btn btn-success"  value="Pesquisar">
                            </div>
                        </form>                        
                    </div>
                    

                    <!--div tabela - entrar dados do sistema-->
                    <div id="containertab">
                        <!--<div class="btn-group d-flex justify-content-center mb-4" role="group" aria-label="Grupo de Botões">
                            <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
                            <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
                            <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço Aniel</button>
                            <button type="button" class="btn btn-danger mx-2" onclick="foraEscala()">Fora Escala</button>
                            <button type="button" class="btn btn-warning mx-2" onclick="foraHorario()">Fora Horário</button>
                            <button type="button" class="btn btn-info mx-2" onclick="extratoKmProj()">Extrato KM Proj</button>
                        </div> datatablesSimple-->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Técnicos
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple" class="display" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <!--<th></th>-->
                                            <th>Matrícula</th>
                                            <th style="white-space: nowrap;">Nome</th>
                                            <th>Placa</th>
                                            <th>Orc. Semanal</th>
                                            <th>Sld. Remanej.</th>
                                            <th>Tot. Extra</th>
                                            <th>Sld. Cartão</th>
                                            
                                            <th>Km Proj.</th>
                                            <th>Km Trab.</th>
                                            
                                            <th>Fora Escala?</th>
                                            <th>Fora Horário?</th>
                                            <th>Valor R$</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php 
                                        //AND a.idtbsupervisor='$idtbsup'                          
                                            $sql = "SELECT a.matricula, b.nome, b.codempresa, b.status, c.matricula FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbsupervisor c where a.matricula = b.matricula and a.perfil = 0 AND a.idtbsupervisor = c.idtbsupervisor and a.matricula='$tecnico';";
                                    
                                            $resultado = mysql_query($sql) or die (mysql_error());
                                            while($row = mysql_fetch_array($resultado)){
                                                $matriculatec = $row[0];
                                                $nometec = $row[1];
                                                $codempresa = $row[2];
                                                $statustec = $row[3];
                                                $matsuperv = $row[4];

                                                $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, kmproj, kmosatual, (valoraplicado - orcsemanal) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                                                $resultado2 = mysql_query($sql2) or die (mysql_error());
                                                $row2 = mysql_fetch_array($resultado2);
                                                
                                                    $saldotec = $row2[0];
                                                    $saldotec = str_replace(".", ",", $saldotec);
                                                    $idtbsaldo = $row2[1];
                                                    
                                                    $orcsemanal = $row2[2];
                                                    $orcsemanalv =  round($orcsemanal, 2);
                                                    $orcsemanalv = str_replace(".", ",", $orcsemanalv);
                                                    
                                                    $kmproj = $row2[3];
                                                    $kmproj = round($kmproj, 2);
                                                    $kmosatual = $row2[4];
                                                    $totalextra = $row2[5];
                                                    $totalextra =  round($totalextra,2);
                                                    $valdesc = $row2[6];
                                                
                                                if ($totalextra < 0) {
                                                    $totalextra = 0;
                                                }                                    

                                                $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                                                $resultado2 = mysql_query($sql2) or die (mysql_error());
                                                $row2 = mysql_fetch_array($resultado2);
                                                $placa = $row2[0];

                                                if ($placa <> '' AND $statustec=='Ativo') {
                                                    

                                                    if (mysql_num_rows($resultado2) > 0) {
                                                        
                                                        
                                                        /*$sql3 = "SELECT saldoatual FROM bdfrota.tbcartaocomb where placa = '$placa'";
                                                        $resultado3 = mysql_query($sql3) or die (mysql_error()); 

                                                        $row3 = mysql_fetch_array($resultado3);
                                                        $saldocartao = $row3[0];
                                                        $saldoct = $saldocartao;*/

                                                        $saldocartao = consultacartao($codempresa, $placa);
                                                        //$saldoct = $saldocartao;
                                                        if(strpos("$saldocartao","R$ ") !== false){
                                                            $sldcartao = str_replace("R$ ","","$saldocartao");
                                                        }

                                                        if(strpos("$sldcartao",",") !== false){
                                                            $sldcartao = str_replace(",",".","$sldcartao");
                                                        }
                                                        

                                                        $useg = date('Y-m-d', strtotime('last monday'));
                                                        $hoje = date('Y-m-d');
                                                        
                                                        //remanejamento positivo
                                                        $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$hoje 23:59:59' AND tipo=1; ";                          
                                                        $resultado4 = mysql_query($sql4) or die (mysql_error());
                                                        $row4 = mysql_fetch_array($resultado4);
                                                        $remanejpositivo = $row4[0];
                                                        
                                                        //remanejamento negativo
                                                        $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$hoje 23:59:59' AND tipo=0; ";                          
                                                        $resultado5 = mysql_query($sql5) or die (mysql_error());
                                                        $row5 = mysql_fetch_array($resultado5);
                                                        $remanejnegativo = $row5[0];
                                                        
                                                        $saldoremanej = $remanejpositivo-$remanejnegativo;

                                                        //saldo fundo frota
                                                        $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$hoje 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                                                        $resultado6 = mysql_query($sql6) or die (mysql_error());
                                                        $row6 = mysql_fetch_array($resultado6);
                                                        $ffrota = $row6[0];
                                                        $ffrota = str_replace(".", ",", $ffrota);
                                                        
                                                        $sqlind = "SELECT * FROM bdfrota.tbrelatoriogpsfalta where matricula = '$matriculatec' and data >= '$useg' and data <= '$hoje'";
                                                        $resultadoind = mysql_query($sqlind) or die (mysql_error());
                                                        
                                                        if (mysql_num_rows($resultadoind)>=1){
                                                            $foraescala = 'Sim';
                                                        } else {
                                                            $foraescala = 'Não';   
                                                        }
                                                        
                                                        $sqlind = "SELECT * FROM bdfrota.tbrelatoriogpsmovind where matricula = '$matriculatec' and data >= '$useg 20:00:00' and data <= '$hoje 06:00:00'";
                                                        $resultadoind = mysql_query($sqlind) or die (mysql_error());
                                                        
                                                        if (mysql_num_rows($resultadoind)>=1){
                                                            $forahorario = 'Sim';
                                                        } else {
                                                            $forahorario = 'Não';   
                                                        }

                                                        print"                        
                                                        <tr>
                                                            <!--<td><input type='radio' name='idtbpedido' id='$id' value='$id'></label></td>-->
                                                            <td>$matriculatec</td>
                                                            <td style=\"white-space: nowrap;\">$nometec</td>
                                                            <td>$placa</td>";
                                                        print"
                                                        <td>R$ $orcsemanalv</td>
                                                        <td>R$  $saldoremanej</td>
                                                        <td>R$ $totalextra</td>
                                                        <td>$saldocartao</td>";

                                                        if ($kmproj > $kmosatual) {
                                                            print"<td class='bg-danger text-white'>$kmproj</td>
                                                            <td class='bg-danger text-white'>$kmosatual</td>";
                                                        } else {
                                                            print"<td class='bg-success text-white'>$kmproj</td>
                                                            <td class='bg-success text-white'>$kmosatual</td>";
                                                        }

                                                        

                                                        if ($foraescala == 'Sim'){

                                                            print"
                                                            <td class='btn btn-sm btn-danger'> SIM
                                                             </td> ";

                                                        } else {
                                                            print"<td class='bg-success text-white'>NÃO</td>";
                                                        }

                                                        if ($forahorario == 'Sim'){
                                                            print"
                                                            <td class='btn btn-sm btn-danger'>SIM </td>";

                                                        } else {
                                                            print"
                                                            <td class='bg-success text-white'>NÃO</td>";
                                                        }

                                                        print"
                                                            <td style='white-space:  nowrap;'>
                                                                <form action='./control/fundofrota-tecnico.php' method='post' class='d-flex'>
                                                                    
                                                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                                    <input type='hidden' name='saldofrota' value='$saldofrota'>
                                                                    <input type='hidden' name='mattec' value='$matriculatec'>
                                                                    <input type='hidden' name='matsuperv' value='$matsuperv'>
                                                                    <input type='hidden' name='placa' value='$placa'>
                                                                    <input type='hidden' name='codempresa' value='$codempresa'>
                                                                    <input type='hidden' name='sldcartao' value='$sldcartao'>

                                                                    <input class='form-control' type='text' name='valor' id='valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                                                    <button class='btn btn-outline-success'><span class='material-symbols-outlined'>done</span></button>
                                                                    
                                                                </form>
                                                            </td>
                                                        </tr>";
                                                    }


                                                }
                                            }
                                    

                                        ?>

                                    </tbody>
                                </table>
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
            
        </div>      

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

        <script>
            /*function endOS() {
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
            }*/
    
            //função data table para criar barra de rolagem
            /*$(document).ready(function () {
                $('#datatablesSimple').DataTable({
                scrollX: true,
                });
            });*/

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

            //função para formatar valor do input - moeda
            function formatarMoeda(i) {
                var v = i.value.replace(/\D/g,'');
                v = (v/100).toFixed(2) + '';
                v = v.replace(".", ",");
                v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }
        </script>
    </body>

</html>