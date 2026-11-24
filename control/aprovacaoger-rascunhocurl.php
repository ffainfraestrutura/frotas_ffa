//selecionar/descobrir o numero do cartão
/*
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
    }
  }
}*/



//colocar dinheiro no cartão
/*$curl = curl_init();
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
    $result = json_decode(curl_exec($curl));*/

