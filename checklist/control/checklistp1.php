<?php 
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');

session_start();
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

include_once"../../func/log.php";
include_once"../../conecta03.php";

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

$cnh2 = $_POST['cnh2'];
$categoriacnh2 = $_POST['categoriacnh2'];
$validadecnh2 = $_POST['validadecnh2'];

if($cnh == ''){
	$cnhfim = $cnh2;
	$categoriacnhfim = $categoriacnh2;
	$validadecnhfim = $validadecnh2;
} else {
	$cnhfim = $cnh;
	$categoriacnhfim = $categoriacnh;
	$validadecnhfim = $validadecnh;
}

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
$centrocusto = utf8_decode($_POST['centrocusto']);
$tipo = utf8_decode($_POST['tipo']);
$vistoriador = utf8_decode($_POST['vistoriador']);

$datavistoria = $_POST['datavistoria'];
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$observacao = utf8_decode($_POST['observacao']);
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

$datavistoriaf = $datavistoria." ".$agora;

if($statusveic=='36'){
	$status = '0';
} else {
	$status='1';
}

$validadecnhfim = DateTime::createFromFormat('d/m/Y', $validadecnhfim)->format('Y-m-d');

$sql = "INSERT INTO `bdfrota-teste`.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, modelo, anofabricacao, unidade, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, teto, tetoesp, tetoesq, tetodir, frente, capo, parabrisa, farolesq, faroldir, parachoque, grade, latesq, paralamaesq, retrovesq, cxaresq, ptdiantesq, latdir, paralamadir, assinadocond, statusveic, retrovdir, cxardir, ptdiantdir, pttrasdir, traseira, lantesq, lantdir, tmpmala, parachoquet, itinterno, painel, som, bancos, ilumint, tmpbag, retrovint, tapetes, pneus, step, marcapneus, kitstep, calotas, bateria, safecar, limpint, limpext, assinadovist, pttrasesq) VALUES ('$nome', '$matricula', '$cpf', '$cnhfim', '$categoriacnhfim', '$validadecnhfim', '$placa', '$modelo', '$anofabricacao', '$unidade', '$centrocusto', '$tipo', '$vistoriador', '$matrvistoriador', '$datavistoriaf', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao', '$teto', '$tetoesp', '$tetoesq', '$tetodir', '$frente', '$capo', '$parabrisa', '$farolesq', '$faroldir', '$parachoque', '$grade', '$latesq', '$paralamaesq', '$retrovesq', '$cxaresq', '$ptdiantesq', '$latdir', '$paralamadir', '0', '$statusveic', '$retrovdir', '$cxardir', '$ptdiantdir', '$pttrasdir', '$traseira', '$lantesq', '$lantdir', '$tmpmala', '$parachoquet', '$itinterno', '$painel', '$som', '$bancos', '$ilumint', '$tmpbag', '$retrovint', '$tapetes', '$pneus', '$step', '$marcapneus', '$kitstep', '$calotas', '$bateria', '$safecar', '$limpint', '$limpext', '0', '$pttrasesq');";
//$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

$sql2 = "UPDATE `bdfrota-teste`.tbveiculo SET hodometro = '$hodometro', statusvel='$statusveic', ccusto='$centrocusto', datamovimentacao='$datavistoriaf' WHERE placa ='$placa' ";
//$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));


if($tipo == 2){
	//recebimento/associação do veículo
    
    //atualizando tbveiculo
    $sql3 = "UPDATE `bdfrota-teste`.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datavistoriaf', ccusto='$centrocusto', unidade='$unidade' WHERE placa ='$placa' ";//datamovimentacao='$hoje',
	//$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
	
    //pegar maximo registro do condutor como ativo
    $sqla="SELECT idtbcondutor, placaassoc FROM `bdfrota-teste`.tbcondutor WHERE matricula='$matricula' AND ativo=1 ORDER BY idtbcondutor DESC";
    $resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
    $linhasa = mysqli_num_rows($resultadoa);

    //pegar maximo registro da placa como ativo
    $sqlb="SELECT idtbcondutor, placaassoc, matricula FROM `bdfrota-teste`.tbcondutor WHERE placaassoc='$placa' AND ativo=1 ORDER BY idtbcondutor DESC";
    $resultadob= mysqli_query($con, $sqlb) or die(mysqli_error($con));
    $linhasb = mysqli_num_rows($resultadob);   
   
    if($linhasa>0){// "se o registro ativo existe, conferir placa<br>";
        $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
            $idtbcondutor = $rowa['idtbcondutor'];
            $placaassoc1 = $rowa['placaassoc'];
            
            
        //se a placa for diferente, tenho que desvincular e vincular na nova
        if($placaassoc1 != $placa){
            //desvincula a antiga
            $sql4="UPDATE `bdfrota-teste`.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
	        $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            
            //print "$idtbcondutor;$placaassoc1; $placa<br> ";
            
            //vincula a nova
            $sql4="INSERT INTO `bdfrota-teste`.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
	        $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));            
        }

    }else{// "se não há registro ativo, basta incluir novo registro";
        
        $sql5="INSERT INTO `bdfrota-teste`.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
	    $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

    }

    if($linhasb>0){print "se a placa já está ativa, conferir se é com o mesmo condutor<br>";
        $rowb = mysqli_fetch_array($resultadob, MYSQLI_BOTH);
            $idtbcondutorpl = $rowb['idtbcondutor'];
            $mat_assoc1 = $rowb['matricula'];
        
        //se a placa for diferente, tenho que desvincular e vincular na nova
        if($mat_assoc1 != $matricula){
            //print "desvincula a antiga";
            $sql4="UPDATE `bdfrota-teste`.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutorpl'; ";
	        $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            
        }

    }else{//"placa nao esta ativa,";

    }


	//print "<BR> $sql3 <br> $sql4";
} elseif($tipo == 3){
	
	//devolução/desvinculação do veículo
	$sql3="UPDATE `bdfrota-teste`.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

	$sql4 = "UPDATE `bdfrota-teste`.tbveiculo SET datamovimentacao='$datavistoriaf', status='$status', statusvel='$statusveic', matcond='', ccusto='$centrocusto', unidade='$unidade' WHERE placa ='$placa' ";
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

	/*print "<br> $sql3 <br> $sql4";*/
 
}
/*
//PARTE 2 DO CHECKLIST
$sql1="SELECT MAX(idtbvistoria) FROM `bdfrota-teste`.tbvistoria WHERE placa='$placa'; ";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
  $idtbvistoria = $row[0];

//DEFINIÇÕES GERAIS
// Nome do diretório para salvar
$dir1 = '/docs/';
$dir1 = '/docs_old/';


// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '../docs/';
$_UP['pasta'] = '../docs_old/';

 
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

if (is_uploaded_file($_FILES['frontal']['tmp_name'])) {

    if ($_FILES['frontal']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['frontal']['error']]);
        exit; 
    } 
    
    $extensao = strtolower(end(explode('.', $_FILES['frontal']['name']))); 
    if (array_search($extensao, $_UP['extensoes']) === false) {
        //echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['frontal']['size']) {
        //echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { // Primeiro verifica se deve trocar o nome do arquivo

        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo

            $nomeaux = explode(".", $_FILES['frontal']['name']);

            $nomearq = str_replace(" ", "-", $nome );

            $nome_final = $placa."-".$datacerta.$hora."-frontal.".$extensao;
            $frontal = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['frontal']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
           // $imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp = imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade); 
    }
    
}

// Refazendo o processo para foto traseira
if (is_uploaded_file($_FILES['traseira']['tmp_name'])) {
 
    if ($_FILES['traseira']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['traseira']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['traseira']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['traseira']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo

            $nomeaux = explode(".", $_FILES['traseira']['name']);

            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-traseira.".$extensao;
            $traseira = $dir1.$nome_final;
        }
        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        $path = $_FILES['traseira']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto do painel
if (is_uploaded_file($_FILES['painel']['tmp_name'])) {
 
    if ($_FILES['painel']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['painel']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['painel']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['painel']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            
            $nomeaux = explode(".", $_FILES['painel']['name']);

            
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-painel.".$extensao;
            $painel = $dir1.$nome_final;
        }
       
        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['painel']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto da lateral esquerda
if (is_uploaded_file($_FILES['esquerda']['tmp_name'])) {
 
    if ($_FILES['esquerda']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['esquerda']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['esquerda']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['esquerda']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            
            $nomeaux = explode(".", $_FILES['esquerda']['name']);
           
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-esquerda.".$extensao;
            $esquerda = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['esquerda']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp = imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto da lateral direita
if (is_uploaded_file($_FILES['direita']['tmp_name'])) {
 
    if ($_FILES['direita']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['direita']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['direita']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['direita']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            
            $nomeaux = explode(".", $_FILES['direita']['name']);
            
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-direita.".$extensao;
            $direita = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['direita']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para selfie
if (is_uploaded_file($_FILES['selfie']['tmp_name'])) {
 
    if ($_FILES['selfie']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['selfie']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['selfie']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['selfie']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            $nomeaux = explode(".", $_FILES['selfie']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-selfie.".$extensao;

            $selfie = $dir1.$nome_final;
        }
        
        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['selfie']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto do cnh
if (is_uploaded_file($_FILES['cnh']['tmp_name'])) {
 
    if ($_FILES['cnh']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['cnh']['error']]);
        exit; // Para a execução do script
    }
    
    $extensao = strtolower(end(explode('.', $_FILES['cnh']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['cnh']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            
            $nomeaux = explode(".", $_FILES['cnh']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-cnh.".$extensao;
            $cnh = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['cnh']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra1
if (is_uploaded_file($_FILES['extra1']['tmp_name'])) {
 
    if ($_FILES['extra1']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra1']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra1']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra1']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            $nomeaux = explode(".", $_FILES['extra1']['name']);
            
            $nomearq = str_replace(" ", "-", $nome );
            
            $nome_final = $placa."-".$datacerta.$hora."-extra1.".$extensao;
            $extra1 = $dir1.$nome_final;
        }
        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra1']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra2
if (is_uploaded_file($_FILES['extra2']['tmp_name'])) {
 
    if ($_FILES['extra2']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra2']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra2']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra2']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            $nomeaux = explode(".", $_FILES['extra2']['name']);

            $nomearq = str_replace(" ", "-", $nome );

            $nome_final = $placa."-".$datacerta.$hora."-extra2.".$extensao;
            $extra2 = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra2']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra3
if (is_uploaded_file($_FILES['extra3']['tmp_name'])) {
 
    if ($_FILES['extra3']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra3']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra3']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra3']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            $nomeaux = explode(".", $_FILES['extra3']['name']);
            
            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datacerta.$hora."-extra3.".$extensao;

            $extra3 = $dir1.$nome_final;
        }

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra3']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

$sql = "INSERT INTO `bdfrota-teste`.tbvistoriafotos (placa, frontal, traseira, direita, esquerda, selfie,  extra1, extra2, extra3, datavistoriaf, idtbvistoria) values ('$placa', '$frontal', '$traseira', '$direita', '$esquerda', '$selfie',  '$extra1', '$extra2', '$extra3', '$datavistoriaf', '$idtbvistoria')";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));


//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Realizou vistoria/checklist';
$tipo = 'checklist';
//$logresult = enviarlognovo($hoje, $acao, $matricula, $vistoriador, $tipo, $placa);

if($perfil == '4' || $perfil == '5' || $perfil == '6'){
   
  print "<script language='javascript' type='text/javascript'> window.alert('Vistoria finalizada com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../verrelatorio.php?id=$idtbvistoria\"; </script>";

} elseif($perfil == '0' || $perfil == '1' || $perfil == '2'){
   
  print "<script language='javascript' type='text/javascript'> window.alert('Vistoria finalizada com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../gerandorelatorio.php?id=$idtbvistoria\";</script>";
       
}*/

?>