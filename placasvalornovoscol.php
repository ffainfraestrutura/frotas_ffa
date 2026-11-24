<?php
date_default_timezone_set('America/Sao_Paulo');


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

$sql = "SELECT ds_placa, id_colaborador FROM bdfrota.tbcarrosxcolaborador where id_colaborador in ('002623','002526','003172','002883','002795','003279','002962','002964','002607','002963','003185','002613','700057','002859','700010','00247','002987','002853','002882','003328','002785','003044','003421','003108','002884','002504','002475','002881','700034','002816','002641','003329','003041','002986','002904','700065','002762','700071','700080','002794','003398','002820','002610','700062','700061','002769','700058')";
$resultado = mysql_query($sql) or die (mysql_error());
while($row = mysql_fetch_array($resultado)){
    $placa = $row[0];
    $matricula = $row[1];


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
echo "$body <br>";
curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

$result = json_decode(curl_exec($curl));

if (count($result->itens)) {

    foreach ($result->itens as $itens) {
        
        $saldoaplicado = $itens->limiteAtual;
        $saldocartao = $itens->saldo;

        echo " <br> $saldoaplicado - $saldocartao <br>";

    }



}



$sql2 = "INSERT INTO bdfrota.tbplacaxvalor (matricula, placa, valor) VALUES ('$matricula', '$placa', '$saldoaplicado')";
echo "$sql2 <hr>";
$resultado2 = mysql_query($sql2) or die (mysql_error());


}



?>
