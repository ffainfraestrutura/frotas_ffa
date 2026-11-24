<?php 
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include_once"../../conecta.php";

/*$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";
$db="bdfrota";

$con=mysqlI_connect($servidor,$usuario,$senha,$db) or die (mysqli_error());*/

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

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

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];

$cpf = $_POST['cpf'];
//$cpf1 = $_POST['cpf1'];

/*if(empty($cpf)){
	$cpf = $cpf1;
}*/


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

$centrocusto = utf8_decode($_POST['centrocusto']);
$tipo = utf8_decode($_POST['tipo']);
$vistoriador = utf8_decode($_POST['vistoriador']);

$datavistoria = $_POST['datavistoria'];
$horavistoria = $_POST['horavistoria'].":00";
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$matrvistoriador = $_POST['matrvistoriador'];
$statusveic = $_POST['statusveic'];
$observacao = utf8_decode($_POST['observacao']);

$datafinal = $datavistoria." ".$horavistoria;

if($statusveic=='3' || $statusveic=='23' || $statusveic=='44'){
	$status = '0';
} else {
	$status='1';
}

if($tipo==2){
	$statusveic='1';
}

//print "$placa <br>$nome<br>$matricula<br>$cpf<br>$cnh <br>$categoriacnh <br>$validadecnh<br>$centrocusto <br>$tipo <br>$vistoriador <br>$datavistoria <br>$horavistoria <br>$estado <br>$avaria <br>$hodometro <br>$niveltanque <br>$matrvistoriador <br>$statusveic";

// Pasta onde o arquivo vai ser salvo
//$_UP['pasta'] = '../docs/';
$_UP['pasta'] = '../docs_old/';

//$dir1 = '/docs/';
$dir1 = '/docs_old/';

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

if (is_uploaded_file($_FILES['pdfvist']['tmp_name'])) {
 
    // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
    if ($_FILES['pdfvist']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['pdfvist']['error']]);
        exit; // Para a execução do script
    }

    $extensao = strtolower(end(explode('.', $_FILES['pdfvist']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['pdfvist']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    else { // Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.'.$extensao;
        } else {  // Mantém o nome original do arquivo            

            $nome_final = $placa."-".$datavistoria."-pdfvist.".$extensao;
            $pdfvist = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['pdfvist']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}

if(!empty($_FILES['imagem1']['tmp_name'])){
// Refazendo o processo para imagem1
if (is_uploaded_file($_FILES['imagem1']['tmp_name'])) {
 
    if ($_FILES['imagem1']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem1']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem1']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem1']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem1.".$extensao;
            $imagem1 = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['imagem1']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}
}

if(!empty($_FILES['imagem2']['tmp_name'])){
// Refazendo o processo para imagem2
if (is_uploaded_file($_FILES['imagem2']['tmp_name'])) {
 
    if ($_FILES['imagem2']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem2']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem2']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem2']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem2.".$extensao;
            $imagem2 = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['imagem2']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}
}

if(!empty($_FILES['imagem3']['tmp_name'])){
// Refazendo o processo para imagem3
if (is_uploaded_file($_FILES['imagem3']['tmp_name'])) {
 
    if ($_FILES['imagem3']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem3']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem3']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem3']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem3.".$extensao;
            $imagem3 = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['imagem3']['tmp_name'], $_UP['pasta'] . $nome_final);

        /*if (move_uploaded_file($_FILES['imagem3']['tmp_name'], $_UP['pasta'] . $nome_final)) {
				// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
				echo "Upload efetuado com sucesso!";
				echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';
				} else {
				// Não foi possível fazer o upload, provavelmente a pasta está incorreta
				echo "Não foi possível enviar o arquivo, tente novamente";
				}*/

    }
}
}


/*$sqla1="SELECT idtbstatusveic FROM bdfrota.tbcondutor WHERE matricula='$matricula' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];*/

//pegar idtbcondutor
$sqla="SELECT MAX(idtbcondutor) FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND placaassoc='$placa' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];

//print $centrocusto;
//print "$cnhfim, $categoriacnhfim, $validadecnhfim,<br>$cnh2, $categoriacnh2, $validadecnh2,<br>$cnh, $categoriacnh, $validadecnh,<br>";

// 

$sql = "INSERT INTO bdfrota.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, statusveic, anofabricacao, modelo) VALUES ('$nome', '$matricula', '$cpf', '$cnh', '$categoriacnh', '$validadecnh', '$placa', '$centrocusto', '$tipo', '$vistoriador', '$matrvistoriador', '$datafinal', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao','$statusveic','$anofabricacao', '$modelo');";

//print $sql;
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

if($tipo == 2){
	//recebimento/associação do veículo

    $sql2="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1'; ";
    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
    $linhas2 = mysqli_num_rows($resultado2);
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $id = $row2['idtbcondutor'];
	//print $id;
    if($linhas2<=0){
        $sql3="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
    
    }else{
        $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$hoje', ativo = 0, statuscond='' WHERE idtbcondutor='$id'; ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

        $sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
    }

	$sql4 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datafinal', ccusto='$centrocusto' WHERE placa ='$placa' ";//datamovimentacao='$hoje',, basegestao='$unidade'
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
    
    //print $linhas2;*/
	
    //print "<BR>tipo2 - $sql3 <br> $sql5";
} elseif($tipo == 3){
	//devolução/desvinculação do veículo
	
	$sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

	$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$datafinal', status='$status', statusvel='$statusveic', matcond='', ccusto='$centrocusto' WHERE placa ='$placa' ";
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

	//print "<br> $sql3 <br> $sql4";
 
}

$sql6="SELECT MAX(idtbvistoria) FROM bdfrota.tbvistoria WHERE placa='$placa'; ";
$resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
    $idtbvistoria = $row[0];

$sql7="INSERT INTO bdfrota.tbvistoriafotos(placa,extra1, extra2, extra3, extra4, datavistoriaf, idtbvistoria) values('$placa','$pdfvist','$imagem1','$imagem2', '$imagem3', '$datafinal', '$idtbvistoria') ;";
$resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
//
//print $sql7;

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Realizou vistoria/checklist-'.$tipo;
$tipo = 'checklist';
$logresult = enviarlognovo($hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa);
//print "$hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa";*

echo "

<script language='javascript' type='text/javascript'>window.alert('Vistoria finalizada com sucesso!');  window.location=\"../checklistinicio-manual.php\"	</script>";
//mysqli_close($con);

?>