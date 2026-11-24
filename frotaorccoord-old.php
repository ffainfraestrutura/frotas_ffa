<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$data = date('Y-m-d');
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
        

<script language="javascript">   
function moeda(a, e, r, t) {
    let n = ""
      , h = j = 0
      , u = tamanho2 = 0
      , l = ajd2 = ""
      , o = window.Event ? t.which : t.keyCode;
    if (13 == o || 8 == o)
        return !0;
    if (n = String.fromCharCode(o),
    -1 == "0123456789".indexOf(n))
        return !1;
    for (u = a.value.length,
    h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
        ;
    for (l = ""; h < u; h++)
        -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
    if (l += n,
    0 == (u = l.length) && (a.value = ""),
    1 == u && (a.value = "0" + r + "0" + l),
    2 == u && (a.value = "0" + r + l),
    u > 2) {
        for (ajd2 = "",
        j = 0,
        h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
            j = 0),
            ajd2 += l.charAt(h),
            j++;
        for (a.value = "",
        tamanho2 = ajd2.length,
        h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    return !1
}
 </script>  
    </head>
 <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">
                <main>

<div class="container-fluid px-4">
                    <h1 class="mt-4">Orçamentos Coordenador</h1>
                      <div class="callout callout-secondary">


     <!--<div class="col-md-4">
      <label for="exampleInputEmail1">Cota Diária:</label>
      <input type="input" class="form-control form-control-md" name="cotadiaria" onKeyPress="return(moeda(this,'.',',',event))">
    </div>

    <div class="form-group col-md-2">
      <label for="exampleInputEmail1"><b style="color: white">.</b></label>
    <input type="submit" class="form-control form-control-md btn btn-success" value="Inserir">
  </div>-->

</div></div>



  <div class="container-fluid px-4">


    <h3 class="mt-4"></h3>
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Orçamento Coordenador
      </div>
      <div class="card-body">
        <table id="datatablesSimple" style="font-size: 12px;">
          <thead>
            <tr>
              <th>Matricula</th>
              <th>Coordenador</th>
              <th>Saldo</th>
              <th>Orc. Complementar</th>
              <th>Filial</th>
              <th>Data e Hora</th>
              <th>Justificativa</th>
              <th>Novo Orc.</th>
              <th>Decisão</th>
            </tr>
          </thead>
          
          
          <tbody>
            <?php
              
              $sql = "SELECT a.matricula, a.valor, b.nome, b.codempresa FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula";

              $resultado = mysql_query($sql) or die (mysql_error());
              while($row = mysql_fetch_array($resultado)){
                $matriculasup = $row[0];
                $saldosup = $row[1];
                $saldosup = str_replace(".", ",", $saldosup);
                $nome = $row[2];
                $codempresa = $row[3];
                if ($codempresa == 2) {
                    $filial = 'SP';
                } else {
                    $filial = 'RJ';
                }
                $sql2 = "SELECT orcrecebido FROM bdfrota.tbcoord where matricula = '$matriculasup'";

                $resultado2 = mysql_query($sql2) or die (mysql_error());
                $row2 = mysql_fetch_array($resultado2);
                $orccomp = $row2[0];
                if ($row2[0] == '' or $row2[0] == null) {
                    $orccomp == '0,00';
                }

                $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidoscoord, data FROM bdfrota.tbpedidoscoord where matricula = '$matriculasup' and flag = 0";
                
                $resultado2 = mysql_query($sql2) or die (mysql_error());
                while ($row2 = mysql_fetch_array($resultado2)) {
                    $justificativa = $row2[0];
                $novoorccomp = $row2[1];
                $novoorccomp = str_replace(".", ",", $novoorccomp);
                $id = $row2[2];
                $dataehora = $row2[3];

                if (mysql_num_rows($resultado2) > 0) {
                    print"<tr role='row'>
                    <td>$matriculasup</td>
                    <td>$nome</td>
                    <td>R$ $saldosup</td>
                    <td>R$ $orccomp</td>
                    <td>$filial</td>
                    <td>$dataehora</td>
                    <td style='font-size: 9px;'>$justificativa</td>
                    <td>R$ $novoorccomp</td>
                    <td style=\"white-space: nowrap;\"><div class=\"btn-group\"><form method='post' action=./control/frotaorccoord.php\'><input type='hidden' name='matriculasup' value='$matriculasup'><input type='hidden' name='decisao' value='2'><input type='hidden' name='id' value='$id'><input type='hidden' name='valor' value='$novoorccomp'><input type='hidden' name='valor_ant' value='$saldosup'>
      <button type=\"submit\" class='btn btn-outline-success'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>
      </form><form method='post' action=./control/frotaorccoord.php\'><input type='hidden' name='matriculasup' value='$matriculasup'><input type='hidden' name='decisao' value='1'><input type='hidden' name='id' value='$id'>
<button class='btn btn-outline-danger'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button> 
    </form></div></td>
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
    </body>
</html>
