<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
$ccusto = $_SESSION['ccusto'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '4'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
$_SESSION['ccusto']=$ccusto;

$data = date('Y-m-d');
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
      <script>
    function disableButton(btn) {
        btn.disabled = true;
    }

    // Função a ser chamada quando o formulário é enviado
    function handleSubmit(event) {
        // Desabilita o botão de submit para evitar envios duplicados
        var submitBtn = event.target.querySelector('.enviarpedido');
        disableButton(submitBtn);
    }
</script>  
    </head>


    <body class="sb-nav-fixed vsc-initialized">

        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

        
        <h1 class="h1 pt-2 pb-2"> Aprovação de Cota Técnico</h1>



            <!--div tabela - entrar dados do sistema-->
            <div id="containertab">
                <div class="btn-group d-flex justify-content-center mb-4 mt-4" role="group" aria-label="Grupo de Botões">
            <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
            <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
            <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço Aniel</button>
            <button type="button" class="btn btn-danger mx-2" onclick="foraEscala()">Fora Escala</button>
            <button type="button" class="btn btn-warning mx-2" onclick="foraHorario()">Fora Horário</button>
            <button type="button" class="btn btn-info mx-2" onclick="extratoKmProj()">Extrato KM Proj</button>
            <button type="button" class="btn btn-light mx-2" onclick="enderecoForaTOA()">Outras Atividades</button>
        </div>
                <table id="datatablesSimple" class="display" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Matrícula</th>
                            <th style="white-space: nowrap;">Nome</th>
                            <th>Placa</th>
                            <th>Status</th>
                            <th>Hodometro</th>
                            <th>Foto hodometro</th>                            
                            <th>Km Proj. Inicial</th>
                            <th>Km Proj. Atual</th>
                            <th>Km Trab.</th>
                            <th>Orc. Inicial</th>
                            <th>Orc. Atual</th>
                            <th>Sld. Remanej.</th>
                            <th>Fd. Frota</th>
                            <th>Sld. Cartão</th>
                            <th>Tot. Extra</th>
                            <th>Fora Escala?</th>
                            <th>Fora Horário?</th>
                            <th>Data</th>
                            <th>Justificativa</th>
                            <th>Solicitação</th>
                            <th>Novo Valor</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php                   
                      
                    $sql = "SELECT a.matricula, b.nome, b.codempresa FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.matricula in (SELECT matricula FROM bdfrota.tbpedidostec where frota = 1 and data >= '$segunda 00:00:00')";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmproj,0), ROUND(kmosatual,0), ROUND(valoraplicado - orcsemanal,2) as totalextra, slddscnt, ROUND(kmorcsem,0), ROUND(valoraplicado,2) FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc LIMIT 1";
                        
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
                        
                        if ($totalextra < 0) {
                           $totalextra = 0;
                        }
                        
                        $valdesc = $row2[6];
                        $kmorcsem = $row2[7];
                        $valoraplicado = $row2[8];
                        $valoraplicado = str_replace(".", ",", $valoraplicado);

                        $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        
                        $placa = $row2[0];

                        if ($placa <> '') {
                            //and data >= '$hoje 00:00:00' and data <= '$hoje 23:59:59'
                            $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidostec, data, dir, kmhodometro FROM bdfrota.tbpedidostec where matricula = '$matriculatec' and frota = 1 and data >= '$segunda 00:00:00' and data <= '$hoje 23:59:59' and desctec is null ";
                            
                            $resultado2 = mysql_query($sql2) or die (mysql_error());

                            if (mysql_num_rows($resultado2) > 0) {

                                $row2 = mysql_fetch_array($resultado2);
                                $justificativa = $row2[0];
                                $novoorccomp = $row2[1];
                                $novoorccomp = str_replace(".", ",", $novoorccomp);
                                $id = $row2[2];
                                $dataehora = $row2[3];
                                $dirhod = $row2[4];
                                $kmhodometro = $row2[5];
                                
                                /*$saldocartaov = consultacartao($codempresa, $placa);
                                if(strpos("$saldocartaov","R$") !== false){
                                    $saldoct = str_replace("R$","","$saldocartaov");
                                } */
                                
                                if ($filial <> '2') {

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
                                                $saldoct = $saldocartao;
                                                $saldocartao = str_replace(".", ",", $saldocartao);
                                            }
        
                                        }
                                    }
                                }  

                                $hoje = date('N'); // Obtém o dia da semana (1 para segunda-feira, 7 para domingo)

                                if ($hoje == 1) {
                                    $useg = date('Y-m-d'); // Se hoje for segunda-feira, $useg é igual a hoje
                                } else {
                                    $useg = date('Y-m-d', strtotime('last monday')); // Caso contrário, obtém a última segunda-feira
                                }
                                $hoje = date('Y-m-d');

                                $sqlind = "SELECT matricula, ROUND(max(kmtotal) - min(kmtotal),2) AS diferenca_kms FROM bdfrota.tbrelatoriogpsfalta where matricula = '$matriculatec' and data >= '$useg 00:00:00' and data <= '$hoje 23:59:59' HAVING diferenca_kms > 5.0";

                                $resultadoind = mysql_query($sqlind) or die (mysql_error());
                                if (mysql_num_rows($resultadoind)>=1){
                                    $foraescala = 'Sim';
                                } else {
                                    $foraescala = 'Não';   
                                }

                                $sqlind = "SELECT matricula, ROUND(max(kmtotal) - min(kmtotal),2) AS diferenca_kms FROM bdfrota.tbrelatoriogpsmovind where matricula = '$matriculatec' and data >= '$useg 00:00:00' and data <= '$hoje 23:59:59' HAVING diferenca_kms > 3.0";
                                $resultadoind = mysql_query($sqlind) or die (mysql_error());
                                if (mysql_num_rows($resultadoind)>=1){
                                    $forahorario = 'Sim';
                                } else {
                                    $forahorario = 'Não';   
                                }

                                if ($saldoct >= 40) {
                                    $status = 'Proibido';
                                } elseif (($kmproj * 0.75) > $kmosatual or $forahorario == 'Sim' or $foraescala == 'Sim') {
                                    $status = 'Indevido';
                                } else{
                                    $status = 'Devido';
                                }
                                
                                if ($placa == 'SQY0I35' or $placa == 'SQV8G36' or $placa == 'SQY0I37' or $placa == 'RKF8H29' or $placa == 'SQW8G79' or $placa == 'SQW8G73') {
                                    $status = 'Devido';
                                }

                                //remanejamento positivo
                                $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1;";                          
                                $resultado4 = mysql_query($sql4) or die (mysql_error());
                                $row4 = mysql_fetch_array($resultado4);
                                $remanejpositivo = $row4[0];
                                
                                //remanejamento negativo
                                $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=0;";                          
                                $resultado5 = mysql_query($sql5) or die (mysql_error());
                                $row5 = mysql_fetch_array($resultado5);
                                $remanejnegativo = $row5[0];
                                
                                $saldoremanej = $remanejpositivo-$remanejnegativo;

                                $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                                $resultado6 = mysql_query($sql6) or die (mysql_error());
                                $row6 = mysql_fetch_array($resultado6);
                                $ffrota = $row6[0];
                                $ffrota = str_replace(".", ",", $ffrota);
                            
                            
                            print"                        
                            <tr>
                                <td><input type='radio' name='idtbpedido' id='$id' value='$id'>
                                    </label></td>
                                <td>$matriculatec</td>
                                <td style=\"white-space: nowrap;\">$nometec</td>
                                <td>$placa</td>";
                                
                            if ($status == 'Devido') {
                                print"<td class='text-success'>$status</td>";
                            } elseif ($status == 'Indevido') {
                                print"<td class='text-warning'>$status</td>";
                            } elseif ($status == 'Proibido') {
                                print"<td class='text-danger'>$status</td>";
                            } else {
                                print"<td>$status</td>";
                            }
                        
                            print"<td style=\"white-space: nowrap;\">$kmhodometro</td>";
                            print"<td><a href='.$dirhod' target='_blank'><button class='btn btn-link' type='submit'><i class='fa-solid fa-eye'></i></button></a></td>
                            <td>$kmorcsem</td>";

                            if (($kmproj * 0.75) > $kmosatual) {
                                print"<td class='bg-danger text-white'>$kmproj</td>
                                <td class='bg-danger text-white'>$kmosatual</td>";
                            } else {
                                print"<td class='bg-success text-white'>$kmproj</td>
                                <td class='bg-success text-white'>$kmosatual</td>";
                            }
                            
                            print"<td style=\"white-space: nowrap;\">R$ $orcsemanal</td>
                            <td style=\"white-space: nowrap;\">R$ $valoraplicado</td>
                            <td>R$ $saldoremanej</td>
                            <td>R$ $ffrota</td>";                            

                            if ($saldoct >= 40) {
                                print"<td class='bg-danger text-white'>R$ $saldocartao</td>";
                            } else {
                                print"<td class='bg-success text-white'>R$ $saldocartao</td>";
                            }                            

                            print"<td>R$ $totalextra</td>";

                            if ($foraescala == 'Sim'){

                            print"
                                <td><form action='./gpsxfolga-sup.php' method='post' target = '_blank'>                                        
                                    <input type='hidden' name='matriculatec' value='$matriculatec'>
                                    <input type='hidden' name='diaf' value='$hoje'>
                                    <input type='hidden' name='diai' value='$useg'>
                                    <input type='hidden' name='detcota' value='sim'>
                            
                                    <input type='submit' class='btn btn-sm btn-danger' value='SIM'> </form> </td>";
                            } else {print"<td class='bg-success text-white'>NÃO</td>";}

                            if ($forahorario == 'Sim'){

                            print"
                                <td><form action='./gpsxhora-sup.php' method='post' target = '_blank'>
                                                                    
                                    <input type='hidden' name='matriculatec' value='$matriculatec'>
                                    <input type='hidden' name='diaf' value='$hoje'>
                                    <input type='hidden' name='diai' value='$useg'>
                                    <input type='hidden' name='detcota' value='sim'>
                                    <input type='submit' class='btn btn-sm btn-danger' value='SIM'></form> 
                                </td>";

                            } else {print"<td class='bg-success text-white'>NÃO</td>";}

                            print"<td>$dataehora</td>
                            <td style='font-size: 9px;'>$justificativa</td>
                            <td>$novoorccomp</td>";

print"<td><div class=\"btn-group\">
                            <form action='./control/aprovacaocotatecfrota.php' method='post' class='d-flex' ".'onsubmit="handleSubmit(event)"'.">
                                
                                    <input class='form-control' type='text' name='valor' id='valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button class='btn btn-outline-success enviarpedido'>
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
                            <input type='hidden' name='kmprojini' value='$kmorcsem'>
                            <input type='hidden' name='orcsematu' value='$valoraplicado'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='./control/aprovacaocotatecfrota.php' method='post'>
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
        function endOS() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;
                    
                }
            }

            if (corSelecionada) {
                var url = '../frotas/detalhes-kmoscotaind-sup.php'; // URL de destino

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
                var url = 'http://ffasip.ddns.net:4545/gerarelgpsrodado-sup.php'; // URL de destino

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
                var url = '../frotas/detalhes-endcasa-sup.php'; // URL de destino

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
                var url = '../frotas/detalhes-foraescalacotaind-sup.php'; // URL de destino

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
                var url = '../frotas/detalhes-forahorariocotaind-sup.php'; // URL de destino

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
                var url = '../frotas/detalhes-extratokmproj-sup.php'; // URL de destino

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
                var url = '../frotas/detalhes-enderecoforatoa-sup.php'; // URL de destino

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