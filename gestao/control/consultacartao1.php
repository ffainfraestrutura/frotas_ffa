<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

include("../../conecta2.php");

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

$sql="SELECT placaassoc FROM bdfrota.tbcondutor WHERE idtbcondutor>=826 AND idtbcondutor<=839;   ";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
  $placa=$row['placaassoc'];

  //print "$placa<br>";
  //$placa='JIK8477';

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
      } else{
        $numeroCartao = $itens->numeroCartao;
      }
    }
  } else {
    $situacao = '';
    $numeroCartao = '';
  }

  $sql2="UPDATE bdfrota.tbcondutor SET cartaoassoc='$numeroCartao' WHERE  placaassoc ='$placa' ;  ";
  $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

  echo "$sql2 <hr>";
  //echo"placa: $placa situação: $situacao cartão: $numeroCartao <br>";
  //break;
}

?>