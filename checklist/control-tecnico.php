<?php
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

$matriculaf = $_POST['matricula'];
$justificativa = $_POST['justificativa'];

$datacomp = date('m-d h:i');
$datacomp = str_replace(' ','',$datacomp);
$datacomp = str_replace('-','',$datacomp);
$datacomp = str_replace(':','',$datacomp);
$codigopro = $matriculaf.$datacomp;


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

$nome_final = $codigopro.$nomeaux[1];
$dir_finald1 = $dir1.$nome_final;
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
$valor = $_POST['valor'];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
    $sql = "INSERT INTO bdfrota.tbpedidostec (matricula, valor, justificativa, data, dir) values ('$matriculaf', '$valor', '$justificativa', '$hoje', '$dir_finald1')";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Pediu cota extra';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', $valor);
    echo"<script language='javascript' type='text/javascript'>alert('Pedido realizado com sucesso');window.location=\"../tecnico.php\"</script>";
} else {
// Não foi possível fazer o upload, provavelmente a pasta está incorreta
echo "<script>alert('erro no envio da foto, tente novamente.');</script>";
echo "<script>window.location=\"../tecnico.php\"</script>";
}  }}


        
?>
