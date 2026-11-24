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

    $cargoesc = $_POST['cargo'];
    $supervesc = $_POST['supervisor'];
    $coordesc = $_POST['coordenador'];
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


    <body class="sb-nav-fixed vsc-initialized">
        <!--nav-->
        <?php include"menu.php"; ?>

        <div id="layoutSidenav_content">
            <main style="width: 100%;">
                <div class="container-fluid px-4" style="width: 100%;">        
                    <h1 class="h1 pt-2 pb-2"> Fundo Frota </h1>

                    <!--div saldo-->
                    <div class='d-flex align-items-center'>
                        <?php
                            //$sql1 = "SELECT ((round(sum(orcsemanal),2)*5)/100) AS saldofrota FROM bdfrota.tbsaldo where data > '$ultdom'";
                            $sql1 = "SELECT saldoatual, idtbsaldofrota FROM bdfrota.tbsaldofrota WHERE data_e_hora<='$ultseg';";
                            
                            $resultado1 = mysql_query($sql1) or die (mysql_error());
                            $row1 = mysql_fetch_array($resultado1);
                            $saldofrota = $row1[0];

                            $saldofrota = round($saldofrota, 2);

                            print"<h3 class='h3 pt-2 pb-2'> R$ $saldofrota </h3>";
                        ?>
                        <a href='./fundofrota-extrato.php'><span class="material-symbols-outlined ms-2">search</span></a>

                    </div>

                    <!--div form filtro-->
                    <div class='mb-3'>
                        <form action='./fundofrota.php' method='post'>
                            <div class='d-flex'> 
                                <div class="form-check form-check-inline col-5">
                                    <input class="form-check-input" type="radio" id="coord" name="cargo" value="coord" required <?php if($cargoesc == 'coord') print "checked";?>>
                                    <label class="form-check-label" for="coord">Coordenador</label>
                                </div>

                                <div class="form-check form-check-inline col-5">
                                    <input class="form-check-input" type="radio" id="superv" name="cargo" value="superv" <?php if($cargoesc == 'superv') print "checked";?>>
                                    <label class="form-check-label" for="superv">Supervisor</label>
                                </div>                                                            
                            </div>

                            <div class='d-flex '>
                                <div class="col-5 me-2">
                                    <label>Coordenador:</label>
                                    <select class="form-select" name="coordenador" id="selectcoordenador" disabled>
                                        <option value=" "> Selecione o coordenador </option>
                                        <?php
                                        $i=0;
                                        $sql2 = "SELECT DISTINCT (a.idtbcoordenador), b.nome,b.matricula FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        while($row2 = mysql_fetch_array($resultado2)){
                                            $idcoord = $row2[0];
                                            $nomecoord = $row2[1];
                                            $matcoord = $row2[2];

                                            $selected = ($matcoord == $coordesc) ? "selected=\"selected\"" : null;

                                            $addmatriz = array($idcoord => $i );
                                            
                                            foreach($addmatriz as $key => $value){
                                                $idselect[$key] = $value;
                                            }
                                            $i++;
                                            print"<option id='$idselect[$idcoord]' value='$matcoord' $selected>$nomecoord</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            
                                <div class="col-5">
                                    <label>Supervisor:</label>
                                    <select class="form-select" name="supervisor" id="selectsupervisor" disabled>
                                        <option value=" "> Selecione o supervisor </option>
                                        <?php
                                            $i=0;
                                            $idselect;

                                            $sql3 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador order by b.nome;";                        
                                            $resultado3 = mysql_query($sql3) or die(mysql_error());
                                            while($row3 = mysql_fetch_array($resultado3)){
                                                $matsupf = $row3[0];
                                                $idcoord = $row3[1];
                                                $nomesup = $row3[2];

                                                $selected = ($matsupf == $supervesc) ? "selected=\"selected\"" : null;

                                                $addmatriz = array($matsup => $i );

                                                foreach($addmatriz as $key => $value){
                                                    $idselect[$key] = $value;
                                                }
                                                $i++;
                                                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]' value='$matsupf' $selected>$nomesup</option>";
                                            }

                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='mt-2' style="width: 50%;">
                                <input type="submit" class="btn btn-success"  value="Pesquisar">
                            </div>
                        </form>                        
                    </div>

                    <!--div info -->
                    <div>
                        <table class='mt-1 table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <!--<th>Data</th>-->
                                    <th>Aporte</th> <!--recebido na semana -->
                                    <th>Saldo Atual</th>
                                    <th>Valor (R$)</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    //print "$cargoesc = $supervesc = $coordesc ";
                                    
                                    if($cargoesc == 'coord'){
                                        $sql4= "SELECT a.matricula, b.nome, a.valor, a.orcrecebido from bdfrota.tbcoord  a, bdaniel.tbfuncionario b where a.matricula='$coordesc' AND a.matricula=b.matricula";
                                        $resultado4 = mysql_query($sql4) or die(mysql_error());
                                        
                                        //totorcomp - tudo recebido na semana
                                    }elseif($cargoesc == 'superv'){
                                        $sql4= "SELECT a.matricula, b.nome, a.valor, a.totorccomp from bdfrota.tbsupervisor  a, bdaniel.tbfuncionario b where a.matricula='$supervesc' AND a.matricula=b.matricula";
                                        $resultado4 = mysql_query($sql4) or die(mysql_error());
                                    }else{
                                        print "
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <!--<td>Data</td>-->
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>";
                                    }                                   
                                    
                                    
                                    while($row4 = mysql_fetch_array($resultado4)){
                                        $matpesq = $row4[0];
                                        $nomepesq = $row4[1];
                                        $saldoatual = $row4[2];
                                        $aporte = $row4[3];

                                        $saldoatualv = str_replace(".",",", $saldoatual);
                                        $aportev = str_replace(".",",",$aporte);

                                        print "
                                        <tr>
                                            <td>$matpesq</td>
                                            <td>$nomepesq</td>
                                            <!--<td>Data</td>-->
                                            <td>R$ $aportev</td>
                                            <td>R$ $saldoatualv</td>
                                            <td style='white-space:  nowrap;'>
                                                <form action='./control/fundofrota.php' method='post'>
                                                    <div class='input-group mb-3'>
                                                        <input type='text' class='form-control'   name='valor' onkeyup='formatarMoeda(this);'>
                                                        <div class='input-group-append'>

                                                        <input type='hidden' name='cargof' value='$cargoesc'>
                                                        <input type='hidden' name='matpesq' value='$matpesq'>
                                                        <input type='hidden' name='saldofrota' value='$saldofrota'>
                                                        
                                                        <button class='btn  btn-outline-success' type='submit'><span class='material-symbols-outlined'>done</span></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            
                                            </td>
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
                        </div>!-->
                    </div>
                </div>
            </footer>
        </div>
        
        </div>

        <script>
            
        </script>

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

            $('input:radio[name="cargo"]').change(function() {
                if ($(this).val() == 'superv') {   
                    $("#selectsupervisor").attr("disabled", false);       
                    $("#selectcoordenador").attr("disabled", false);

                    $("#selectsupervisor").attr("required", true);
                    
                    $(this).attr("checked", true);
                
                } else if ($(this).val() == 'coord') {
                    $("#selectsupervisor").attr("disabled", true);
                    $("#selectcoordenador").attr("disabled", false);

                    $("#selectcoordenador").attr("required", true);

                    $(this).attr("checked", true);  
                }
            });

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