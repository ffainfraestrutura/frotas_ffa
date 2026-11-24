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
} elseif ($perfil <> '1'){
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

        <!-- vendor jquery js -->
        <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

        <script language="javascript">   

  function popmodalagt(idtbprocesso){

    $('#idprocesso')[0].value = idtbprocesso;

}

  function valida_formulariodir(){

      document.getElementById("form_dir").submit();

  }

            
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


    <body class="sb-nav-fixed vsc-initialized">

        <!--nav-->
        <?php include"menu.php"; 
include"popupjustind.php";
        ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

        
        <h1 class="h1 pt-2 pb-2"> Aprovação de Cota </h1>



            <!--div tabela - entrar dados do sistema-->
            <div id="containertab">
                <table id="datatablesSimple" class="display" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th style="white-space: nowrap;">Nome</th>
                            <th>Placa</th>
                            <th>Val. Desc.</th>
                            <th>Orc. Semanal</th>
                            <th>Km Proj.</th>
                            <th>Km OS</th>
                            <th>KM GPS</th>
                            <th>Sld. Cartão</th>
                            <th>Tot. Extra</th>
                            <th>Data</th>
                            <th>Justificativa</th>
                            <th>Solicitação</th>
                            <th>Novo Valor</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php

                    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = $matricula";

                    $resultado = mysql_query($sql) or die (mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $idtbsupervisor = $row[0];
                      
                    $sql = "SELECT a.matricula, b.nome, b.codempresa FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsupervisor";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, kmproj, kmosatual, (valoraplicado - orcsemanal) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];
                        $totalextra = $row2[5];
                        $valdesc = $row2[6];

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];



                        $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidostec, data FROM bdfrota.tbpedidostec where matricula = '$matriculatec' and flag = 0";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());

                        if (mysql_num_rows($resultado2) > 0) {

                            $row2 = mysql_fetch_array($resultado2);
                            $justificativa = $row2[0];
                            $novoorccomp = $row2[1];
                            $novoorccomp = str_replace(".", ",", $novoorccomp);
                            $id = $row2[2];
                            $dataehora = $row2[3];

if ($codempresa == '2') {
    
} else{

$curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_POST, TRUE);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'Accept-Charset: utf-8',
                                'Accept-Encoding: gzip',
                                'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                            ));
                            curl_setopt($curl, CURLOPT_HEADER, 0);

                            $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "numeroCartao": null,
  "especieCartao": null,
  "numeroMatricula": null,
  "nomeCompleto": null,
  "placaVeiculo": "'.$placa.'",
  "numeroFrota": null,
  "codigoTipoFrota": null,
  "codigoFamiliaVeiculo": null,
  "codigoVeiculoModelo": null,
  "codigoCidade": null,
  "codigoClienteTCVeiculoResponsavel": null,
  "nomeResponsavel": null,
  "codigoGrupo": 4,
  "codigoTipoCombustivel": null,
  "informacoesAdicionais": [
    {
      "labelIA": null,
      "codigoIA": null,
      "descricaoIA": null
    }
  ],
  "situacaoCartao": "T",
  "ordem": "C",
  "quebra": null,
  "tipoMotorista": null,
  "ativacao": null,
  "tipoCartao": null,
  "controlaHodometro": null,
  "controlaHorimetro": null,
  "situacaoVeiculo": null,
  "trackOnLine": null
}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                            $result = json_decode(curl_exec($curl));

                            if (count($result->itens)) {

                                foreach ($result->itens as $itens) {
                                    
                                    $situacao = $itens->situacao;

                                    if ($situacao == 'A') {
                                        $saldocartao = $itens->saldo;
                                        $saldocartao = str_replace(".", ",", $saldocartao);
                                    }

                                }
                            }

}
                            

/*$servidor5="10.17.0.5";
$usuario5="blvp";
$senha5="@F3r7n6c2";
$db5 = "bdfrota";
$conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

$sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

$resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$kmsem = $row5[0];
if ($kmsem > 2500) {
    $kmsem = 0;
}*/


                            print"                        
                            <tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$placa</td>
                            <td>$valdesc</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>$kmsem</td>
                            <td>R$ $saldocartao</td>
                            <td>R$ $totalextra</td>
                            <td>$dataehora</td>
                            <td style='font-size: 9px;'>$justificativa</td>
                            <td>$novoorccomp</td>";

if ($kmproj > $kmosatual) {
    
print"<td><div class=\"btn-group\">

 <input class='form-control' type='text' name='valor' id='valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>

<button class='btn btn-outline-success' data-bs-toggle='modal' data-bs-target='#cadastromodaljustind' onclick=\"popmodalagt('$codprocesso')\"><span class='material-symbols-outlined'>done</span></button>


<form action='./control/aprovacaocota.php' method='post'>
                                        <button class='btn btn-outline-danger'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button> 
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='1'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            </form>

                                </div></td></tr>"; 


} else {

print"<td><div class=\"btn-group\">
                            <form action='./control/aprovacaocota.php' method='post' class='d-flex'>
                                
                                    <input class='form-control' type='text' name='valor' id='valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button class='btn btn-outline-success'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldocartao'>
                            <input type='hidden' name='valor_desc' value='$valdesc'>
                            <input type='hidden' name='orcsem' value='$orcsemanal'>
                            <input type='hidden' name='kmproj' value='$kmproj'>
                            <input type='hidden' name='kmos' value='$kmosatual'>
                            <input type='hidden' name='kmsem' value='$kmsem'>
                            <input type='hidden' name='totextra' value='$totalextra'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='./control/aprovacaocota.php' method='post'>
                                        <button class='btn btn-outline-danger'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button> 
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='1'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            </form>

                                </div>

                            </td></tr>";

}



                            


                        }


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
            //função data table para criar barra de rolagem
            $(document).ready(function () {
                $('#datatablesSimple').DataTable({
                scrollX: true,
                });
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    </body>

</html>