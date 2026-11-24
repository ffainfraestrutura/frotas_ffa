<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$datahora = date('Y-m-d H:i:s');

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$mat_autor = $matricula;

include("../conecta.php");
include_once "../func/log.php";
include("../func/latlong.php");

$supervisores = array('13', '14', '27', '39', '40', '57', '66', '82', '93', '94', '95', '96', '109');
$coordenadores = array('7', '28', '35', '36', '44', '45', '63');
$gerentes = array('9', '37', '80', '112');

$idtbfuncionario = $_POST['idtbfuncionario'];
$nome = $_POST['nome'];
$dtnasc = $_POST['dtnasc'];
$sexo = $_POST['sexo'];
$nacionalidade = $_POST['nacionalidade'];
$naturalidade = $_POST['naturalidade'];
$instrucao = $_POST['instrucao'];
$estcivil = $_POST['estcivil'];
$nomepai = $_POST['nomepai'];
$nomemae = $_POST['nomemae'];
$deficiencia = $_POST['deficiencia'];
$deficiente = $_POST['deficiente'];
$cpf = $_POST['cpf'];
$rg = $_POST['rg'];
$ufrg = $_POST['ufrg'];
$emissorrg = $_POST['emissorrg'];
$datarg = $_POST['datarg'];
$pis = $_POST['pis'];
$nctps = $_POST['ctps'];
$serctps = $_POST['serctps'];
$datactps = $_POST['datactps'];
$telefone = $_POST['telefone'];
$celular = $_POST['celular'];
$email = $_POST['email'];
$emailcop = $_POST['emailcop'];
$cep = $_POST['cep'];
$logradouro = $_POST['logradouro'];
$numcasa = $_POST['numcasa'];
$complem = $_POST['complem'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$long = $_POST['long'];
$latid = $_POST['latid'];
$matfunc = $_POST['matfunc'];
$mataux = $_POST['mataux'];
$situacao = $_POST['situacao'];
$dtadmissao = $_POST['dtadmissao'];
$dtfolha = $_POST['dtfolha'];
$empresa = $_POST['empresa'];
$filial = $_POST['filial'];
$departamento = $_POST['departamento'];
$gestor = $_POST['gestor'];
$estadotrab = $_POST['estadotrab'];
$cidadetrab = $_POST['cidadetrab'];
$cargo = $_POST['cargo'];
$jornada = $_POST['jornada'];
$valorh = $_POST['valorh'];
$semmarcacao = $_POST['semmarcacao'];
$projeto = $_POST['projeto'];
$salario = $_POST['salario'];
$periculosidade = $_POST['periculosidade'];
//$escala = $_POST['escala'];
$visivel = 1;

if (strpos($cep, "-") !== false) {
  $cep = str_replace("-", "", $cep);
}

/*print strlen($cep)."<br>";*/

if (strpos($cep, " ") !== false) {
  $cep = str_replace(" ", "", $cep);
}

if (strpos($cpf, ".") !== false) {
  $cpf = str_replace(".", "", $cpf);
}

if (strpos($cpf, "-") !== false) {
  $cpf = str_replace("-", "", $cpf);
}

if (strpos($rg, ".") !== false) {
  $rg = str_replace(".", "", $rg);
}

if (strpos($rg, "-") !== false) {
  $rg = str_replace("-", "", $rg);
}

if (strpos($pis, ".") !== false) {
  $pis = str_replace(".", "", $pis);
}

if (strpos($pis, "-") !== false) {
  $pis = str_replace("-", "", $pis);
}

if (strpos($serctps, "-") !== false) {
  $serctps = str_replace("-", "", $serctps);
}

if (strpos($telefone, "-") !== false) {
  $telefone = str_replace("-", "", $telefone);
}

if (strpos($telefone, "(") !== false) {
  $telefone = str_replace("(", "", $telefone);
}

if (strpos($telefone, ")") !== false) {
  $telefone = str_replace(")", "", $telefone);
}

if (strpos($telefone, " ") !== false) {
  $telefone = str_replace(" ", "", $telefone);
}

if (strpos($celular, "-") !== false) {
  $celular = str_replace("-", "", $celular);
}

if (strpos($celular, "(") !== false) {
  $celular = str_replace("(", "", $celular);
}

if (strpos($celular, ")") !== false) {
  $celular = str_replace(")", "", $celular);
}

if (strpos($celular, " ") !== false) {
  $celular = str_replace(" ", "", $celular);
}

if (strpos($salario, ".") !== false) {
  $salario = str_replace(".", "", $salario);
}

if (strpos($salario, ",") !== false) {
  $salario = str_replace(",", ".", $salario);
}

if ($periculosidade == "") {
  $periculosidade = "0.00";
}

if (strpos($periculosidade, ".") !== false) {
  $periculosidade = str_replace(".", "", $periculosidade);
}

if (strpos($periculosidade, ",") !== false) {
  $periculosidade = str_replace(",", ".", $periculosidade);
}

//print "$salario, $periculosidade<br>";

if ($semmarcacao != '0') {
  $semmarcacao = '1';
}

if ($deficiente != '1') {
  $deficiente = '0';
}

if ($datarg == "") {
  $datarg = NULL;
}

if ($datactps == "") {
  $datactps = NULL;
}

if ($salario == "") {
  $salario = NULL;
}

if ($estcivil == "") {
  $estcivil = NULL;
}

if ($projeto == "") {
  $projeto = NULL;
}

$endereco_comp = "$logradouro, $numcasa, $bairro, $cidade - $estado";
$latlong = latlog($endereco_comp);

if ($latlong == "Não foi possível converter o endereço em coordenadas.") {
  $latitude = "";
  $longitude = "";
} else {
  $auxiliar1 = $latlong;
  $auxiliar1 = explode(",", $auxiliar1);

  $latitude = $auxiliar1[0];
  $longitude = $auxiliar1[1];
}

//$datactps = "$datactps 00:00:00";

//print "Latitude: $latitude, Longitude: $longitude";

/*print "$nome;$dtnasc;$sexo;$nacionalidade;$naturalidade;$instrucao;$estcivil;$nomepai;$nomemae;$deficiente; $deficiencia;$cpf ;$rg;$ufrg;$emissorrg;$datarg;$pis;$serctps;$datactps;$telefone;$celular;$email;$cep;$logradouro;$numcasa;$complem;$bairro;$cidade;$estado;$long;$latid;$matfunc;$mataux;$situacao;$dtadmissao;$dtfolha;$empresa;$filial;$departamento;$gestor;$estadotrab;$cidadetrab;$cargo;$jornada;$valorh; $semmarcacao";*/


try {
  // Conexão com o banco de dados usando PDO
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

  // Define o modo de erro do PDO para exceção
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //preparar query
  //cadastro na tbfuncionario
  $sql = "CALL inserirFuncionario(:nome, :dtnasc, :sexo, :nacionalidade, :naturalidade, :instrucao,  :estcivil, :nomepai, :nomemae,  :deficiente, :deficiencia, :cpf, :rg, :ufrg,  :emissorrg, :datarg,  :pis, :nctps, :serctps, :datactps,  :telefone, :celular, :email, :emailcop ,:cep, :logradouro, :numcasa,  :complem, :bairro, :cidade, :estado, :matfunc, :mataux, :situacao, :dtadmissao, :dtfolha, :empresa, :filial, :departamento, :gestor, :estadotrab,  :cidadetrab, :cargo, :jornada, :valorh, :semmarcacao, :projeto, :visivel, :salario, :periculosidade, :data_alteracao, :latitude, :longitude)";

  // Prepara a chamada da procedure
  $stmt = $pdo->prepare($sql);

  // Vincula os parâmetros
  //$stmt->bindParam(':idtbfuncionario', $idtbfuncionario, PDO::PARAM_STR);
  $stmt->bindParam(':semmarcacao', $semmarcacao, PDO::PARAM_STR);
  $stmt->bindParam(':valorh', $valorh, PDO::PARAM_STR);
  $stmt->bindParam(':jornada', $jornada, PDO::PARAM_STR);
  $stmt->bindParam(':cargo', $cargo, PDO::PARAM_STR);
  $stmt->bindParam(':cidadetrab', $cidadetrab, PDO::PARAM_STR);
  $stmt->bindParam(':estadotrab', $estadotrab, PDO::PARAM_STR);
  $stmt->bindParam(':gestor', $gestor, PDO::PARAM_STR);
  $stmt->bindParam(':departamento', $departamento, PDO::PARAM_STR);
  $stmt->bindParam(':filial', $filial, PDO::PARAM_STR);
  $stmt->bindParam(':empresa', $empresa, PDO::PARAM_STR);
  $stmt->bindParam(':dtfolha', $dtfolha, PDO::PARAM_STR);
  $stmt->bindParam(':dtadmissao', $dtadmissao, PDO::PARAM_STR);
  $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
  $stmt->bindParam(':mataux', $mataux, PDO::PARAM_STR);
  $stmt->bindParam(':matfunc', $matfunc, PDO::PARAM_STR);
  $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
  $stmt->bindParam(':cidade', $cidade, PDO::PARAM_STR);
  $stmt->bindParam(':bairro', $bairro, PDO::PARAM_STR);
  $stmt->bindParam(':complem', $complem, PDO::PARAM_STR);
  $stmt->bindParam(':numcasa', $numcasa, PDO::PARAM_STR);
  $stmt->bindParam(':logradouro', $logradouro, PDO::PARAM_STR);
  $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
  $stmt->bindParam(':dtnasc', $dtnasc, PDO::PARAM_STR);
  $stmt->bindParam(':sexo', $sexo, PDO::PARAM_STR);
  $stmt->bindParam(':nacionalidade', $nacionalidade, PDO::PARAM_STR);
  $stmt->bindParam(':naturalidade', $naturalidade, PDO::PARAM_STR);
  $stmt->bindParam(':instrucao', $instrucao, PDO::PARAM_STR);
  $stmt->bindParam(':estcivil', $estcivil, PDO::PARAM_STR);
  $stmt->bindParam(':nomepai', $nomepai, PDO::PARAM_STR);
  $stmt->bindParam(':nomemae', $nomemae, PDO::PARAM_STR);
  $stmt->bindParam(':deficiencia', $deficiencia, PDO::PARAM_STR);
  $stmt->bindParam(':deficiente', $deficiente, PDO::PARAM_STR);
  $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
  $stmt->bindParam(':rg', $rg, PDO::PARAM_STR);
  $stmt->bindParam(':ufrg', $ufrg, PDO::PARAM_STR);
  $stmt->bindParam(':emissorrg', $emissorrg, PDO::PARAM_STR);
  $stmt->bindParam(':datarg', $datarg, PDO::PARAM_STR);
  $stmt->bindParam(':pis', $pis, PDO::PARAM_STR);
  $stmt->bindParam(':serctps', $serctps, PDO::PARAM_STR);
  $stmt->bindParam(':datactps', $datactps, PDO::PARAM_STR);
  $stmt->bindParam(':telefone', $telefone, PDO::PARAM_STR);
  $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
  $stmt->bindParam(':email', $email, PDO::PARAM_STR);
  $stmt->bindParam(':emailcop', $emailcop, PDO::PARAM_STR);
  $stmt->bindParam(':cep', $cep, PDO::PARAM_STR);
  $stmt->bindParam(':nctps', $nctps, PDO::PARAM_STR);
  $stmt->bindParam(':projeto', $projeto, PDO::PARAM_STR);
  $stmt->bindParam(':visivel', $visivel, PDO::PARAM_STR);
  $stmt->bindParam(':salario', $salario, PDO::PARAM_STR);
  $stmt->bindParam(':periculosidade', $periculosidade, PDO::PARAM_STR);
  $stmt->bindParam(':data_alteracao', $datahora, PDO::PARAM_STR);
  $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
  $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);

  $result = $stmt->execute();

//----------------------------------//
//                                  //
//                                  //
//    Integração com a API da FFA   //
//                                  //
//                                  //
//----------------------------------//
  $data = [
    "idtbfuncionario" => $_POST['idtbfuncionario'],
    "matricula" => $_POST['matfunc'],
    "nome" => $_POST['nome'],
    "dtnasc" => $_POST['dtnasc'],
    "cpf" => $_POST['cpf'],
    "endereco" => $_POST['logradouro'],
    "bairro" => $_POST['bairro'],
    "cidade" => $_POST['cidade'],
    "estado" => $_POST['estado'],
    "cep" => $_POST['cep'],
    "dtadmissao" => $_POST['dtadmissao'],
    "dtemfolha" => $_POST['dtfolha'],
    "status" => $_POST['situacao'],
    "mataux" => $_POST['mataux'],
    "cargo" => $_POST['cargo'],
    "matricula_aux" => $_POST['mataux'],
    "ccusto" => $_POST['departamento'],
    "numres" => $_POST['numcasa'],
    "longitude" => $_POST['long'],
    "latitude" => $_POST['latid'],
    "id_jornada" => $_POST['jornada'],
    "codempresa" => $_POST['empresa'],
    "projeto" => $_POST['projeto'],
    "rg" => $_POST['rg'],
    "codfilial" => $_POST['filial'],
    "email" => $_POST['email'],
    "uf_trabalho" => $_POST['estadotrab'],
  ];


  $funcionario = json_encode($data, JSON_UNESCAPED_UNICODE);

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, 'https://ffa.painel-telecom.com:4545/FFA/public/api/funcionario');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $funcionario);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json'
  ));
  $response = curl_exec($curl);
  curl_close($curl);

//----------------------------------//
//                                  //
//                                  //
//        Fim da Integração         //
//                                  //
//                                  //
//----------------------------------//

  if (in_array($cargo, $supervisores)) {
    $nivelgestao = 1;
    try {
      // Conexão com o banco de dados usando PDO
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

      // Define o modo de erro do PDO para exceção
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //preparar query 
      $sql = "CALL inserirGestor(:matfunc, :nomefunc, :nivelgestao)";

      // Prepara a chamada da procedure
      $stmt = $pdo->prepare($sql);

      // Vincula os parâmetros
      $stmt->bindParam(':matfunc', $matfunc, PDO::PARAM_STR);
      $stmt->bindParam(':nomefunc', $nome, PDO::PARAM_STR);
      $stmt->bindParam(':nivelgestao', $nivelgestao, PDO::PARAM_STR);

      $result = $stmt->execute();
    } catch (PDOException $e) {
      // Em caso de erro, exibe a mensagem de erro
      echo "Erro: " . $e->getMessage();
    }

  } elseif (in_array($cargo, $coordenadores)) {
    $nivelgestao = 2;
    try {
      // Conexão com o banco de dados usando PDO
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

      // Define o modo de erro do PDO para exceção
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //preparar query 
      $sql = "CALL inserirGestor(:matfunc, :nomefunc, :nivelgestao)";

      // Prepara a chamada da procedure
      $stmt = $pdo->prepare($sql);

      // Vincula os parâmetros
      $stmt->bindParam(':matfunc', $matfunc, PDO::PARAM_STR);
      $stmt->bindParam(':nomefunc', $nome, PDO::PARAM_STR);
      $stmt->bindParam(':nivelgestao', $nivelgestao, PDO::PARAM_STR);

      $result = $stmt->execute();
    } catch (PDOException $e) {
      // Em caso de erro, exibe a mensagem de erro
      echo "Erro: " . $e->getMessage();
    }
  } elseif (in_array($cargo, $gerentes)) {
    $nivelgestao = 3;
    try {
      // Conexão com o banco de dados usando PDO
      $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

      // Define o modo de erro do PDO para exceção
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //preparar query 
      $sql = "CALL inserirGestor(:matfunc, :nomefunc, :nivelgestao)";

      // Prepara a chamada da procedure
      $stmt = $pdo->prepare($sql);

      // Vincula os parâmetros
      $stmt->bindParam(':matfunc', $matfunc, PDO::PARAM_STR);
      $stmt->bindParam(':nomefunc', $nome, PDO::PARAM_STR);
      $stmt->bindParam(':nivelgestao', $nivelgestao, PDO::PARAM_STR);

      $result = $stmt->execute();
    } catch (PDOException $e) {
      // Em caso de erro, exibe a mensagem de erro
      echo "Erro: " . $e->getMessage();
    }
  }

  $resultadofinal = 1;

  echo "<script language='javascript' type='text/javascript'>window.location=\"../listagemfuncionarios.php\"</script>";

} catch (PDOException $e) {
  // Em caso de erro, exibe a mensagem de erro
  echo "Erro: " . $e->getMessage();
}

// Fecha a conexão
$pdo = null;

if ($resultadofinal == 1) {
  $acao = "Cadastrou funcionário";
  $log = enviarlog($datahora, $matfunc, $mat_autor, $acao);
}

?>