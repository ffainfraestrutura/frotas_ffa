<?php
date_default_timezone_set('America/Sao_Paulo');

session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once"../func/log.php";

$hoje = date('Y-m-d H:i:s');

$matriculaf = $_POST['matriculatec'];
$idtbsaldo = $_POST['idtbsaldo'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$tipocota = $_POST["tipocota"];
$saldocartao = $_POST["saldocartao"];
$saldocartao = str_replace(",", ".", $saldocartao);
$justsup = $_POST["justificativa"];

if ($saldocartao >= 40) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../emergencialcoord.php\"</script>";

            }
    

    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldocoord = $row[0];

    

            if ($valor > $saldocoord) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../emergencialcoord.php\"</script>";

            } else {


            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
            $resultado = mysql_query($sql) or die (mysql_error());

            $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());
          $sql = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];

 $sql = "SELECT codempresa FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $codempresa = $row[0];

$sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, orcsemanal, kmproj, kmosatual, (valoraplicado - orcsemanal) as totalextra, slddscnt FROM bdfrota.tbsaldo where matricula = '$matriculaf' order by data desc";
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

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculaf'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];

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


$servidor5="10.17.0.5";
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
}

if ($codempresa == '2') {
    
$curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_POST, TRUE);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept-Encoding: gzip, deflate, br',
                                'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
                            ));
                            curl_setopt($curl, CURLOPT_HEADER, 0);


                            $body = '{
"contratoCliente" : "524073",
"veiculoId" : null,
"placa" : "'.$placa.'",
"numeroCartao" : null,
"valorAlteracao" : "'.$valor.'",
"tipoAlteracao" : "adicionar",
"periodo" : "atual",
"motivo" : "Sistema Combustivel",
"modulo" : "abastecimento"
}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                            $result = json_decode(curl_exec($curl));

} else {

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
                                        $numeroCartao = $itens->numeroCartao;
                                        $saldocartao = $itens->saldo;
                                        if ($saldocartao >= 40) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../emergencialcoord.php\"</script>";

            }
                                    }

                                }
                            }

             $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');
                            
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'Accept-Charset: utf-8',
                                'Accept-Encoding: gzip',
                                'Connection: keep-alive',
                                'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                            ));
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_HEADER, 0);

                            $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "tipoAlteracao": "AR",
  "tipoLimite": "AS",
  "tipoOperacao": "SP",
  "cartoes": [
    {
      "numeroCartao": "'.$numeroCartao.'",
      "valorLimite": '.$valor.',
      "valorLimiteProxPeriodo": null,
      "mensagem": "Controle Combustivel"
    }
  ]
}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                            $result = json_decode(curl_exec($curl));
}

$sql3 = "INSERT INTO bdfrota.tbcotaextraac (matricula, valor, justificativa, data, orcsemanal, kmproj, kmos, gps, valordescontado, sldcartao, totalextra, tipo_cota, matsup, dataaceite, valorinserido, justsup) values ('$matriculaf', '0', '$justificativa', '$hoje', '$orcsemanal', '$kmproj', '$kmosatual', '$kmsem', '$valdesc', '$saldocartao', '$totalextra', '1', '$matricula', '$hoje', '$valor', '$justsup')";

    $resultado3 = mysql_query($sql3) or die (mysql_error());

    
            $acao = 'Adicionou cota emergencial';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', $valor);

                echo"<script language='javascript' type='text/javascript'>alert('Cota Emergencial inserida');window.location=\"../emergencialcoord.php\"</script>";
        } 

        
?>
