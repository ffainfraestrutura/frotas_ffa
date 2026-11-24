<?php 
header("Content-Type: application/json");

// Conectar ao banco de dados (substitua com suas credenciais)
$db = new mysqli("10.27.0.100", "blvp", "qrdrps", "bdfrota");

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];
$cpf = $_POST['cpf'];

if(strpos($cpf, ".") !== false){
  $cpf= str_replace(".", "", $cpf);
  
  if(strpos($cpf, "-") !== false){
  	$cpf = str_replace("-", "", $cpf);
  }
} else {
  $cpf = $cpf;
}
	
$cnh = $_POST['cnh'];
$categoriacnh = $_POST['categoriacnh'];
$validadecnh = $_POST['validadecnh'];

/*$cnh2 = $_POST['cnh2'];
$categoriacnh2 = $_POST['categoriacnh2'];
$validadecnh2 = $_POST['validadecnh2'];

if($cnh == ''){
	$cnhfim = $cnh2;
	$categoriacnhfim = $categoriacnh2;
	$validadecnhfim = $validadecnh2;

	$validadecnhfim =  str_replace('/', '-', $validadecnhfim);
	$validadecnhfim = date("Y-m-d", strtotime($validadecnhfim));
} else {
	$cnhfim = $cnh;
	$categoriacnhfim = $categoriacnh;
	$validadecnhfim = $validadecnh;
}*/

$placa = $_POST['placa'];

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} elseif(strpos($placa, " ") !== false) {
  $placa = str_replace(" ", "", $placa);
} else{
	$placa=$placa;
}

$modelo = $_POST['modelo'];
$anofabricacao = $_POST['anofabricacao'];
$unidade = utf8_decode($_POST['unidade']);
$basegestao = $unidade;

$centrocusto = utf8_decode($_POST['centrocusto']);
$tipo = utf8_decode($_POST['tipo']);
$vistoriador = utf8_decode($_POST['vistoriador']);

$datavistoria = $_POST['datavistoria'];
$horavistoria = $_POST['horavistoria'];
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$observacao = utf8_decode($_POST['observacao']);
$documentacao = $_POST['documentacao'];
$matrvistoriador = $_POST['matrvistoriador'];
$statusveic = $_POST['statusveic'];

$teto= $_POST['teto'];
$tetoesp=$_POST['tetoesp'];
$tetoesq=$_POST['tetoesq'];
$tetodir=$_POST['tetodir'];
$frente=$_POST['frente'];
$capo=$_POST['capo'];
$parabrisa=$_POST['parabrisa'];
$farolesq=$_POST['farolesq'];
$faroldir=$_POST['faroldir'];
$parachoque=$_POST['parachoque'];
$grade=$_POST['grade'];
$latesq=$_POST['latesq'];
$paralamaesq=$_POST['paralamaesq'];
$retrovesq=$_POST['retrovesq'];
$cxaresq=$_POST['cxaresq'];
$ptdiantesq=$_POST['ptdiantesq'];
$pttrasesq=$_POST['pttrasesq'];
$latdir=$_POST['latdir'];
$paralamadir= $_POST['paralamadir'];
$retrovdir = $_POST['retrovdir'];
$cxardir = $_POST['cxardir'];
$ptdiantdir = $_POST['ptdiantdir'];
$pttrasdir = $_POST['pttrasdir'];
$traseira =  $_POST['traseira'];
$lantesq = $_POST['lantesq'];
$lantdir = $_POST['lantdir'];
$tmpmala = $_POST['tmpmala'];
$parachoquet = $_POST['parachoquet'];
$itinterno = $_POST['itinterno'];
$painel = $_POST['painel'];
$som = $_POST['som'];
$bancos = $_POST['bancos'];
$ilumint = $_POST['ilumint'];
$tmpbag = $_POST['tmpbag'];
$retrovint = $_POST['retrovint'];
$tapetes = $_POST['tapetes'];
$pneus = $_POST['pneus'];
$step = $_POST['step'];
$marcapneus = $_POST['marcapneus'];
$kitstep = $_POST['kitstep'];
$calotas = $_POST['calotas'];
$bateria = $_POST['bateria'];
$safecar = $_POST['safecar'];
$limpint = $_POST['limpint'];
$limpext = $_POST['limpext'];

//$datavistoriaf = $datavistoria." ".$agora;
$datavistoriaf = $datavistoria." ".$horavistoria;


if($unidade == 'CAMPOS' || $unidade == 'LAGOS' || $unidade=='MACAE' || $unidade=='NITEROI' || $unidade == 'NOVA FRIBURGO' || $unidade=='OLARIA' || $unidade == 'PETROPOLIS' || $unidade=='TERESOPOLIS'|| $unidade=='TRES RIOS' || $unidade=='VASSOURAS'){
	$unidadef='RJ';
}elseif($unidade=='CASCAVEL' || $unidade=='CURITIBA' || $unidade=='PONTA GROSSA'){
	$unidadef='PR';
}elseif($unidade=='SAO MATEUS' || $unidade=='SAO MATHEUS'){
	$unidadef = 'SP';
}

if($statusveic=='3' || $statusveic=='23' || $statusveic=='44'){
	$status = '0';
} else {
	$status='1';
}

//pegar idtbcondutor
/*$sqla="SELECT MAX(idtbcondutor) FROM bdfrota.tbcondutor WHERE matricula='$matricula' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];*/

// Dados que você deseja enviar para a API em formato JSON
$data = array(
"nome" => "$nome",
"matricula" => "$matricula",
"cpf" => "$cpf",
"cnhfim" => "$cnh",
"categoriacnhfim" => "$categoriacnh",
"validadecnhfim" => "$validadecnh",
"placa" => "$placa",
"modelo" => "$modelo",
"anofabricacao" => "$anofabricacao",
"unidadef" => "$unidadef",
"centrocusto" => "$centrocusto",
"tipo" => "$tipo",
"vistoriador" => "$vistoriador",
"matrvistoriador" => "$matrvistoriador",
"datavistoriaf" => "$datavistoriaf",
"estado" => "$estado",
"avaria" => "$avaria",
"hodometro" => "$hodometro",
"niveltanque" => "$niveltanque",
"observacao" => "$observacao",
"documentacao" => "$documentacao",
"teto" => "$teto",
"tetoesp" => "$tetoesp",
"tetoesq" => "$tetoesq",
"tetodir" => "$tetodir",
"frente" => "$frente",
"capo" => "$capo",
"parabrisa" => "$parabrisa",
"farolesq" => "$farolesq",
"faroldir" => "$faroldir",
"parachoque" => "$parachoque",
"grade" => "$grade",
"latesq" => "$latesq",
"paralamaesq" => "$paralamaesq",
"retrovesq" => "$retrovesq",
"cxaresq" => "$cxaresq",
"ptdiantesq" => "$ptdiantesq",
"latdir" => "$latdir",
"paralamadir" => "$paralamadir",
"assinadocond" => "$assinadocond",
"statusveic" => "$statusveic",
"retrovdir" => "$retrovdir",
"cxardir" => "$cxardir",
"ptdiantdir" => "$ptdiantdir",
"pttrasdir" => "$pttrasdir",
"traseira" => "$traseira",
"lantesq" => "$lantesq",
"lantdir" => "$lantdir",
"tmpmala" => "$tmpmala",
"parachoquet" => "$parachoquet",
"itinterno" => "$itinterno",
"painel" => "$painel",
"som" => "$som",
"bancos" => "$bancos",
"ilumint" => "$ilumint",
"tmpbag" => "$tmpbag",
"retrovint" => "$retrovint",
"tapetes" => "$tapetes",
"pneus" => "$pneus",
"step" => "$step",
"marcapneus" => "$marcapneus",
"kitstep" => "$kitstep",
"calotas" => "$calotas",
"bateria" => "$bateria",
"safecar" => "$safecar",
"limpint" => "$limpint",
"limpext" => "$limpext",
"assinadovist" => "$assinadovist",
"pttrasesq" => "$pttrasesq",
);

// Converte os dados em formato JSON
$json_data = json_encode($data);

// URL da sua API
$api_url = "10.17.0.5/apifrotas/apichecklist.php"; // Substitua pela URL correta

// Inicializa uma sessão cURL
$ch = curl_init($api_url);

// Define as opções da solicitação cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json_data)
));

// Executa a solicitação
$response = curl_exec($ch);

// Verifica se houve erros
if (curl_errno($ch)) {
    echo 'Erro cURL: ' . curl_error($ch);
} else {
    // Exibe a resposta da API
    //echo 'Resposta da API: ' . $response;
	/*print "
<script language='javascript' type='text/javascript'> window.location=\"../checklistp2.php?placa=$placa&datavistoria=$datavistoria&matricula=$matricula\"</script>";*/
}

// Fecha a sessão cURL
curl_close($ch);

$db->close();

header("Location: ../checklistp2.php?placa=$placa&datavistoria=$datavistoria&matricula=$matricula");


/*print "
<script language='javascript' type='text/javascript'> window.location=\"../checklistp2.php?placa=$placa&datavistoria=$datavistoria&matricula=$matricula\"</script>";*/

?>