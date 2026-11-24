<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once "../../func/log.php";
include("../../conecta2.php");

$hoje = date('Y-m-d H:i:s');

$placa = $_POST['placa'];
$filial = $_POST['filial'];
$ccusto = $_POST['ccusto'];
$fornecedor = $_POST['fornecedor'];
$anotacao = $_POST['anotacao'];
$tipoinfracao = $_POST['tipoinfracao'];
$autoinfracao = $_POST['autoinfracao'];
$datainfracao = $_POST['datainfracao'];
$horainfracao = $_POST['horainfracao'];
$datalimitecond = $_POST['datalimitecond'];
$datalimiteloc = $_POST['datalimiteloc'];
$expedicao = $_POST['expedicao'];
$recebimento = $_POST['recebimento'];
$vencimento = $_POST['vencimento'];
$codigom = $_POST['codigom'];
$pontos = $_POST['pontos'];
$gravidade = $_POST['gravidade'];
$valor = $_POST['valor'];
$valdesconto = $_POST['valdesconto'];
$taxaadm = $_POST['taxaadm'];
$juros = $_POST['juros'];
$valtotal = $_POST['valtotal'];
$orgao = $_POST['orgao'];
$endereco = $_POST['endereco'];
$municipio = $_POST['municipio'];
$matriculac = $_POST['matriculac'];
$nomec = $_POST['nomec'];
$numparcelas = $_POST['numparcelas'];
$valparcelas = $_POST['valparcelas'];
$descontoapartir = $_POST['descontoapartir'];
$descontadocondutor = $_POST['descontadocondutor'];
$tipodesconto = $_POST['tipodesconto'];
$datadesconto = $_POST['datadesconto'];
$realinfrator = $_POST['realinfrator'];
$pessoaindicada = $_POST['pessoaindicada'];
$condassinatura = $_POST['condassinatura'];
$reciboassinado = $_POST['reciboassinado'];
$recusaassinar = $_POST['recusaassinar'];
$matr_autor = $_POST['matr_autor'];

$status = $_POST['status'];
$etapa = $_POST['etapa'];
$descricaoinfra = $_POST['descricaoinfra'];
$valcomdesc = $_POST['valcomdesc'];
$datarecorg = $_POST['datarecorg'];
$protrecorg = $_POST['protrecorg'];
$comprpag = $_POST['comprpag'];
$pgempresa = $_POST['pgempresa'];
$datapagempr = $_POST['datapagempr'];
$motivonpg = $_POST['motivonpg'];
$numprotocolo = $_POST['numprotocolo'];
$expprotocolo = $_POST['expprotocolo'];
$recprotocolo = $_POST['recprotocolo'];

//tratando placa
if (strpos($placa, "-") !== false) {
    $placa = str_replace("-", "", $placa);
} else {
    $placa = str_replace(" ", "", $placa);
}

//tratando valores
$valor = explode(" ", $valor);
$valor = str_replace(".", " ", $valor[1]);
$valor = str_replace(",", ".", $valor);

$valdesconto = explode(" ", $valdesconto);
$valdesconto = str_replace(".", " ", $valdesconto[1]);
$valdesconto = str_replace(",", ".", $valdesconto);

$taxaadm = explode(" ", $taxaadm);
$taxaadm = str_replace(".", " ", $taxaadm[1]);
$taxaadm = str_replace(",", ".", $taxaadm);

$valtotal = explode(" ", $valtotal); // ["R$", "1.234,56"]
$valtotal = str_replace(".", "", $valtotal[1]); // "1234,56"
$valtotal = str_replace(",", ".", $valtotal);   // "1234.56"

if ($valtotal != "" && $valtotal != "0") {
    $valorMulta = (float) $valtotal; // Converte para número float
// Calcula o número de parcelas com teto de R$200

} else {
    $valorMulta = (float) $valor; // Converte para número float
}
$numparcelas = ceil($valorMulta / 200);
// Calcula o valor de cada parcela (pode ser inferior a 200)
$valparcelas = ceil(($valorMulta / $numparcelas) * 100) / 100;

$valcomdesc = explode(" ", $valcomdesc);
$valcomdesc = str_replace(".", " ", $valcomdesc[1]);
$valcomdesc = str_replace(",", ".", $valcomdesc);

//tratando hora da infração
$horainfracao = $horainfracao . ":00";

$diainfracao = $datainfracao . " " . $horainfracao;

//buscando cpf
$sql = "SELECT nome, cpf FROM bdaniel.tbfuncionario WHERE matricula='$matriculac';";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
$cpf = $row['cpf'];
$nomec = $row['nome'];

if ($etapa == '') {
    $etapa = 'EM ABERTO';
}

//print"$nomec - $matriculac - $cpf";
//print $placa."<br>". $filial."<br>". $ccusto."<br>".$fornecedor."<br>". $anotacao."<br>". $tipoinfracao."<br>". $autoinfracao."<br>". $diainfracao."<br>". $datalimitecond."<br>". $datalimiteloc."<br>". $expedicao."<br>". $recebimento."<br>". $vencimento."<br>". $codigom."<br>". $pontos."<br>". $gravidade."<br>". $valor."<br>". $valdesconto."<br>". $taxaadm."<br>". $juros."<br>". $valtotal."<br>". $orgao."<br>". $endereco."<br>". $municipio."<br>". $matriculac."<br>". $nomec."<br>". $numparcelas."<br>". $valparcelas."<br>". $descontoapartir."<br>". $descontadocondutor."<br>". $tipodesconto."<br>". $datadesconto."<br>". $realinfrator."<br>". $pessoaindicada."<br>". $condassinatura."<br>". $reciboassinado."<br>". $recusaassinar;

$sql1 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa';";
$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
$row1 = mysqli_num_rows($resultado1);

if ($row1 <= 0) {

    //echo "<script language='javascript' type='text/javascript'> alert ('Placa não cadastrada no sistema.');window.location=\"../../../multas/testemultas2.php\" </script>";
    echo "<script language='javascript' type='text/javascript'> alert ('Placa não cadastrada no sistema.');window.location=\"../../../multas/multasfrota.php\" </script>";




} else {
    $sql2 = "INSERT INTO bdfrota.tbmulta (placa, filial, ccusto, fornecedor, anotacao, tipoinfracao, autoinfracao, datainfracao, datalimitecond, datalimiteloc, expedicao,recebimento, vencimento, codigom, pontos, gravidade, valor, valdesconto, taxaadm, juros, valtotal, orgao, endereco, municipio, matriculac, nomec, numparcelas, valparcelas, descontoapartir, descontadocondutor, tipodesconto, datadesconto, realinfrator, pessoaindicada, condassinatura, reciboassinado, recusaassinar, status, etapa, descricaoinfra, valcomdesc, datarecorg , protrecorg, comprpag , pgempresa, datapagempr, motivonpg, numprotocolo, expprotocolo, recprotocolo) 
VALUES 
('$placa', '$filial', '$ccusto', '$fornecedor', '$anotacao', '$tipoinfracao', '$autoinfracao', '$diainfracao', '$datalimitecond', '$datalimiteloc', '$expedicao','$recebimento', '$vencimento', '$codigom', '$pontos', '$gravidade', '$valor', '$valdesconto', '$taxaadm', '$juros', '$valtotal', '$orgao', '$endereco', '$municipio', '$matriculac','$nomec', '$numparcelas', '$valparcelas', '$descontoapartir', '$descontadocondutor', '$tipodesconto', '$datadesconto', '$realinfrator', '$pessoaindicada', '$condassinatura', '$reciboassinado',  '$recusaassinar',  '$status', '$etapa', '$descricaoinfra',  '$valcomdesc',  '$datarecorg', '$protrecorg',  '$comprpag',  '$pgempresa',  '$datapagempr',  '$motivonpg', '$numprotocolo', '$expprotocolo', '$recprotocolo')";

    $resultado = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

    $sql8 = "SELECT MAX(idtbmulta) AS idtbmulta FROM bdfrota.tbmulta WHERE placa='$placa' AND autoinfracao='$autoinfracao'; ";
    $resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
    $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
    $idtbmulta = $row8[0];


    $sql2 = "INSERT INTO bdfrota.tbmovidatramite(placa, autoinfra, pontuacao, gravidade, apCondDV, dtinfra, dtvenc, valor, status, nome, matricula, cpf, tramite, locadora, idmulta) VALUES ('$placa', '$autoinfracao', '$pontos', '$gravidade', '$datalimitecondf', '$diainfracao', '$vencimentof', '$valor', 1, '$nomec','$matriculac', '$cpf', 'Inserir infrator', '$fornecedor', '$idtbmulta');";
    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

    //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
    $acao = 'Cadastrou multa';
    $tipo = 'cadastro';
    $logresult = enviarlognovo($hoje, $acao, $matriculac, $matr_autor, $tipo, $placa);
    //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

    //echo "<script language='javascript' type='text/javascript'> alert ('Multa cadastrada com sucesso.');window.location=\"../../../multas/testemultas2.php\"  </script>";
    echo "<script language='javascript' type='text/javascript'> alert ('Multa cadastrada com sucesso.');window.location=\"../../../multas/multasfrota.php\"  </script>";

}


//mysqli_close($conexao);

?>