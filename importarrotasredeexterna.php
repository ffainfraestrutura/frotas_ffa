<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/* selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matriculaat = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matriculaat;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$matriculaat = $_POST['matriculaat'];

include './vendor/autoload.php';

include "./conecta.php";

//include "./conecta2.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//print "<a href='./vendor/autoload.php'>caminho</a>";
$nome = $_FILES["arquivo"]["name"];

if(is_uploaded_file($_FILES['arquivo']['tmp_name']))
{ 
$dir1 = '/destinoreclaro/';

// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = './destinoreclaro/';
 
// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 64; // 64Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('xls', 'xlsx');
 
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
echo "O arquivo enviado é muito grande, envie arquivos de até 64Mb.";
}
 
// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
else {
// Primeiro verifica se deve trocar o nome do arquivo
if ($_UP['renomeia'] == true) {
// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
$nome_final = time().'.xlsx';
} else {
// Mantém o nome original do arquivo
$nomeaux = explode(".", $_FILES['arquivo']['name']);

$nomearq = str_replace(" ", "-", $nome );

$nome_final = $matriculaat.".".$nomeaux[1];
$dir_finald1 = $dir1.$nome_final;
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {

} else {
// Não foi possível fazer o upload, provavelmente a pasta está incorreta
echo "<script>alert('erro no envio do documento');</script>";
echo "<script>window.location=\"./rotasredeexterna.php\"</script>";
}  }}
//echo "Foi $dataos";

$sql = "INSERT INTO bdfrota.tbimportrotareclaro (matricula, data) VALUES ('$matriculaat', '$hoje')";
$resultado = mysql_query($sql) or die (mysql_error());
echo"<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"./rotasredeexternaretorno.php\"</script>";



?>