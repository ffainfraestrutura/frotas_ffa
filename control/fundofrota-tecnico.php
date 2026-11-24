<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$hoje = date('Y-m-d H:i:s');
$ultdom = date('Y-m-d', strtotime($data . 'last sunday')); //última domingo

//print "$hoje<br>";

include"../conecta.php";
include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

print "<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js'></script>";

$valor = $_POST['valor'];
if(strpos($valor, ".") !== false){
    $valor = str_replace(".","",$valor);
}

$valor = str_replace(",",".",$valor);

$saldofrota = $_POST['saldofrota'];
$saldofrota = str_replace(",",".",$saldofrota);

$idtbsaldo = $_POST['idtbsaldo'];
$matriculatec = $_POST['mattec'];
$matsuperv = $_POST['matsuperv'];
$mat_autor = $matricula;
$placa = $_POST['placa'];
$codempresa = $_POST['codempresa'];
$sldcartao = $_POST['sldcartao'];

//print "$saldofrota, $valor, $idtbsaldo, $matriculatec, $matsuperv, $mat_autor<br>";
//window.location=\"../fundofrota-tecnico.php\";

if($valor>$saldofrota){
    echo "
    <head>
        
        <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
        <script type='text/javascript'>
            $(document).ready(function() {                
                
                alert('Saldo insuficiente!');
            
                // monta o formulário
                
                var formulario = \"<form id='form' hidden method='post' action='../fundofrota-tecnico.php'>\"
                +\"<input name='tecnico' value='$matriculatec'>\"                       
                +\"</form>\";
    
                // insere o formulário no body
                $('body').append(formulario);
                // faz o submit e remove o formulário
                $('#form').submit().remove();

            });
        </script>
    </head>";
}else{
    //nivel tecnico
    $sql1 = "SELECT saldo, valoraplicado, totalextra FROM bdfrota.tbsaldo WHERE idtbsaldo = '$idtbsaldo';";
    $resultado1 = mysql_query($sql1) or die (mysql_error());
    $row1 = mysql_fetch_array($resultado1);
        $saldoant = $row1[0];
        $valoraplicadoant = $row1[1];
        $totalextraant = $row1[2];

    //print"$sql1<br>";
    //print"$saldoant, $valoraplicadoant, $totalextraant<br>";
    
    $saldonovo = $saldoant + $valor;
    $valoraplicadonovo = $valoraplicadoant + $valor;
    $totalextranovo = $totalextraant + $valor;

    if(strpos($saldonovo, ",") !== false){
        $saldonovo = str_replace(",",".",$saldonovo);
    }

    if(strpos($valoraplicadonovo, ",") !== false){
        $valoraplicadonovo = str_replace(",",".",$valoraplicadonovo);
    }

    if(strpos($totalextranovo, ",") !== false){
        $totalextranovo = str_replace(",",".",$totalextranovo);
    }


    //print"$saldonovo, $valoraplicadonovo, $totalextranovo<br>";

    $sql2 = "UPDATE bdfrota.tbsaldo SET saldo = $saldonovo, valoraplicado = $valoraplicadonovo, totalextra = $totalextranovo WHERE idtbsaldo = '$idtbsaldo'";
    $resultado = mysql_query($sql2) or die (mysql_error());
    //print $sql2;

    //tirar da frota
    $sql3="SELECT saldoatual, idtbsaldofrota FROM bdfrota.tbsaldofrota WHERE data_e_hora='$ultdom'; ";
    $resultado3 = mysql_query($sql3) or die (mysql_error());
    $row3 = mysql_fetch_array($resultado3);
        $saldofrotaant = $row3[0];
        $idtbsaldofrota = $row3[1];
    //print "$saldofrotaant, $idtbsaldofrota<br>";

    $saldofrotanovo = $saldofrotaant - $valor;
    if(strpos($saldofrotanovo, ",") !== false){
        $saldofrotanovo = str_replace(",",".",$saldofrotanovo);
    }

    $sql4 = "UPDATE bdfrota.tbsaldofrota SET saldoatual=$saldofrotanovo, modificadoem='$hoje' WHERE idtbsaldofrota='$idtbsaldofrota';";
    $resultado4 = mysql_query($sql4) or die (mysql_error());
    //print "$saldofrotaant,$valor, $saldofrotanovo<br>";

    $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES ('$hoje', '$matriculatec', '$mat_autor', '$valor', 4,1, '$sldcartao');";
    $resultado2 = mysql_query($sql2) or die (mysql_error());

    $sql = "SELECT codempresa FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
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
                    
                    /*if ($saldocartao >= 40) {
            
                        echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                    }*/
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
    
    $acao = 'Enviou cota - Fundo frota';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculatec, $mat_autor, $valor_ant, $valor);

    //echo"<script language='javascript' type='text/javascript'>alert('Saldo inserido com sucesso!');window.location=\"../fundofrota-tecnico.php\"</script>";
    echo "
    <head>
        
        <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
        <script type='text/javascript'>
            $(document).ready(function() {                
                
                alert('Saldo inserido com sucesso!');
            
                // monta o formulário
                
                var formulario = \"<form id='form' hidden method='post' action='../fundofrota-tecnico.php'>\"
                +\"<input name='tecnico' value='$matriculatec'>\"                       
                +\"</form>\";
    
                // insere o formulário no body
                $('body').append(formulario);
                // faz o submit e remove o formulário
                $('#form').submit().remove();

            });
        </script>
    </head>";
}



?>