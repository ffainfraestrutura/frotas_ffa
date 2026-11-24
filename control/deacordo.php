<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once"../func/log.php";

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

$idpedidotec = $_POST['idpedidotec'];
$valdesc = $_POST['valdesc'];
$id = $_POST['id'];

if (is_uploaded_file($_FILES['arquivo']['tmp_name'])) {

$dir1 = '/docs/';
// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '../docs/';
 
// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 32; // 32Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
 
// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;
 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
 
// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
if ($_FILES['arquivo']['error'] != 0) {
die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
exit; // Para a execução do script
}
 
// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
 
// Faz a verificação da extensão do arquivo
$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
if (array_search($extensao, $_UP['extensoes']) === false) {
echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
}
 
// Faz a verificação do tamanho do arquivo
else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
}
 
// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
else {
// Primeiro verifica se deve trocar o nome do arquivo
if ($_UP['renomeia'] == true) {
// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
$nome_final = time().'.jpg';
} else {
// Mantém o nome original do arquivo
$nomeaux = explode(".", $_FILES['arquivo']['name']);

$nomearq = str_replace(" ", "-", $nome );

$nome_final = $matricula.$idpedidotec.".jpg";
$dir_finald1 = $dir1.$nome_final;
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {

    $sql = "SELECT b.matricula FROM bdfrota.tbusuario a, bdfrota.tbsupervisor b where a.idtbsupervisor = b.idtbsupervisor and a.matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
            $matriculasup = $row[0];


    $sql = "UPDATE bdfrota.tbcotaextraindevida SET actec = 1, docactec = '$dir_finald1', dataac = '$hoje' where idtbcotaextraindevida = '$id'";

    $resultado = mysql_query($sql) or die (mysql_error());




        $sql = "UPDATE bdfrota.tbpedidostec SET flag = '2', valorinserido = valorinserido + $valdesc where idtbpedidostec = $idpedidotec";

    $resultado = mysql_query($sql) or die (mysql_error());

                $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc) SELECT matricula, '$matriculasup', valor, justificativa, data, '2', '$hoje', dir, valorinserido, kmhodometro, '', '', '', '', '', '', '', '', 'desc', '$valdesc' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $idpedidotec";
                 echo "$sql <br>";
            $resultado = mysql_query($sql) or die (mysql_error());

$sql = "SELECT * FROM bdfrota.tbsaldo where matricula = '$matricula' order by idtbsaldo desc limit 1";

            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $idtbsaldo = $row[0];

            $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valdesc, valoraplicado = valoraplicado + $valdesc, totalextra = totalextra + $valdesc where idtbsaldo = '$idtbsaldo'";
       
            $resultado = mysql_query($sql) or die (mysql_error());

$sql = "UPDATE bdfrota.tbsupervisor SET valor = valor - $valdesc where matricula = '$matriculasup'";

            $resultado = mysql_query($sql) or die (mysql_error());
    

                        $sql = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matricula'";
     
            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];


 $sql = "SELECT codempresa FROM bdaniel.tbfuncionario where matricula = '$matricula'";

            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $codempresa = $row[0];



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
                
                echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

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
      "valorLimite": '.$valdesc.',
      "valorLimiteProxPeriodo": null,
      "mensagem": "Controle Combustivel"
    }
  ]
}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                              
                            $result = json_decode(curl_exec($curl));
}





    $acao = 'Aceitou desconto';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matricula, $matricula, '', $valdesc);
    echo"<script language='javascript' type='text/javascript'>alert('Desconto aceito e Combustível creditado');window.location=\"../cotasdesconto.php\"</script>";
} else {
// Não foi possível fazer o upload, provavelmente a pasta está incorreta
echo "<script>alert('erro no envio da foto, tente novamente.');</script>";
echo "<script>window.location=\"../tecnico.php\"</script>";
}  }}


        
?>
