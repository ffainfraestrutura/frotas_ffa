<?php
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
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        
    </head>

    <body class="sb-nav-fixed">

    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

    	
    	<h1 class="h1 pt-2 pb-2"> Aprovação de Cota </h1>

    	<form action='./control/aprovacaocota.php' method='post'>

            <!-- div botões superiores-->
            <div class="d-flex flex-row mb-3 pb-3 justify-content-around border-bottom">
                <!--div botão extra-->
    			<div class="col-auto d-inline text-center col-2">
    				<button value="0" id="btnExtra" class="btn btn-primary" type="button" style="border-radius: 0.4rem; width: 90%;" onclick="ativarBotao(this.value)"> Extra </button> 
    			</div>

                <!--div botão plantão-->
    			<div class="text-center col-2" id="plantao">
                    <button id="btPlantao" value="1" class="btn btn-warning mb-2" type="button" style="border-radius: 0.4rem; color: white; background-color: #fd7e14; border-color: #fd7e14; width: 90%;" onclick="btnPlantao()"> Plantão </button>
                    <p class="d-none" id="data">
                        <input class="inputdata" type="date" name="data">
                    </p>
                </div>

                <!--div botão emergencia-->
                <div class="text-center col-2">
                    <button class="btn btn-danger" id="btnEmerg" value="2" type="button" style="border-radius: 0.4rem; width: 90%;" onclick="ativarBotao(this.value)"> Emergência </button>
                </div>

                <!--div botão zerar-->
                <div class="text-center col-2">
                    <button class="btn btn-secondary" id="btnZerar" value="3" type="button" style="border-radius: 0.4rem; width: 90%;" onclick="ativarBotao(this.value)"> Zerar </button>
                </div>

                <!--div botão redução-->
                <div class="text-center col-2">
                    <button class="btn btn-dark" id="btnRed" value="4" type="button" style="border-radius: 0.4rem; width: 90%;" onclick="ativarBotao(this.value)"> Redução </button>
                </div>
                
            </div>

            <!--div tabela - entrar dados do sistema-->
    		<div id="containertab">
                <table id="datatablesSimple" class="display nowrap" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">Matrícula</th>
                            <th style="white-space: nowrap;">Nome</th>
                            <th style="white-space: nowrap;">Placa</th>
                            <th style="white-space: nowrap;">Orçamento Semanal</th>
                            <th style="white-space: nowrap;">Km Projetado</th>
                            <th style="white-space: nowrap;">Km OS</th>
                            <th style="white-space: nowrap;">Saldo Cartão</th>
                            <th style="white-space: nowrap;">Justificativa</th>
                            <th style="white-space: nowrap;">Solicitação</th>
                            <th style="white-space: nowrap;">Inserir</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php

                    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = $matricula";
                    $resultado = mysql_query($sql) or die (mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $idtbsupervisor = $row[0];
                      
                    $sql = "SELECT a.matricula, b.nome FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = $idtbsupervisor";
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];

                        $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, kmproj, kmosatual FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $saldotec = $row2[0];
                        $saldotec = str_replace(".", ",", $saldotec);
                        $idtbsaldo = $row2[1];
                        $orcsemanal = $row2[2];
                        $orcsemanal = str_replace(".", ",", $orcsemanal);
                        $kmproj = $row2[3];
                        $kmosatual = $row2[4];

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];

                        $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidostec FROM bdfrota.tbpedidostec where matricula = '$matriculatec' and flag = 0";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());

                        if (mysql_num_rows($resultado2) > 0) {

                            $row2 = mysql_fetch_array($resultado2);
                            $justificativa = $row2[0];
                            $novoorccomp = $row2[1];
                            $novoorccomp = str_replace(".", ",", $novoorccomp);
                            $id = $row2[2];

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
                                    
                                    $saldocartao = $itens->saldo;
                                    $saldocartao = str_replace(".", ",", $saldocartao);

                                }
                            }

                            print"                        
                            <tr>
                            <td>$matriculatec</td>
                            <td>$nometec</td>
                            <td>$placa</td>
                            <td>R$ $orcsemanal</td>
                            <td>$kmproj</td>
                            <td>$kmosatual</td>
                            <td>R$ $saldocartao</td>
                            <td>$justificativa</td>
                            <td>$novoorccomp</td>
                            <td>
                            <form action='./control/aprovacaocota.php' method='post'>
                                <p class='input-group' style='flex-wrap: nowrap;'>
                                    <input class='form-control' type='text' name='solicitacao' id='valor' style='width: 200px;' onkeyup='formatarMoeda()'>
                                    <button class='btn btn-outline-success'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>

                                        <button class='btn btn-outline-danger'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button>
                                </p>
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldotec'>
                            <input class='tipocota' type='hidden' name='tipocota' value =''>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>
                            </td>";


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