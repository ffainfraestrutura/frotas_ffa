<?php
date_default_timezone_set('America/Sao_Paulo');

header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");

include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];
$cnh = $_POST['cnh'];
$validade = $_POST['validade'];
$uf = $_POST['uf'];
$categoria = $_POST['categoria'];
$pontos = $_POST['pontos'];
$consulta = $_POST['consulta'];
$suspensa = $_POST['suspensa'];
$matr_autor = $_POST['matr_autor'];

//echo $nome, $matricula, $cnh, $validade, $uf, $categoria, $pontos, $consulta, $suspensa;

$sql2 = "SELECT * FROM bdfrota.tbcnh WHERE matricula = '$matricula';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_num_rows($resultado2);

if($row2 == 0){	

	$arquivo=$_FILES['arquivo']['name']; 

	if(!empty($arquivo)){

		
		//$_UP['pasta'] = 'docs/docmanutencao/';
		$_UP['pasta'] = '../docs/cnh/';
		$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
	 
		$_UP['extensoes'] = array('jpg', 'png', 'gif', 'pdf');

		$_UP['renomeia'] = false;
	 
		$_UP['erros'][0] = 'Não houve erro';
		$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
		$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
		$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
		$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
	 
		// Verifica se houve algum erro com o upload. 
		if ($_FILES['arquivo']['error'] != 0) {
			die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
			exit; // Para a execução do script
		}
	 
		// Faz a verificação da extensão do arquivo
		$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
		if (array_search($extensao, $_UP['extensoes']) === false) {
		echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, gif ou pdf";
		}else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
			echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
		}else {
			
			if ($_UP['renomeia'] == true) {
			
				//$nome_final = time().'.jpg';
				$nome_final = "cnh-".$matricula.$extensao;
				$pasta = "/docs/cnh/";

				$nome_final1 = $pasta.$nome_final;

			} else {		
				//$nome_final = $_FILES['arquivo']['name'];
				$pasta = "/docs/cnh/";
				$nome_final = "cnh-".$matricula.".".$extensao;
				
				$nome_final1 = $pasta.$nome_final;

			}
	 
			// Depois verifica se é possível mover o arquivo para a pasta escolhida
			if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
			
			//echo "Upload efetuado com sucesso!";
			//echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';
			} else {
			
				//echo "Não foi possível enviar o arquivo, tente novamente".$_UP['pasta'];
			}
	 
		}
	}

	$sql1 = "INSERT INTO bdfrota.tbcnh (numcnh, validade, uf, categoria, matricula, pontos, consulta, suspensa, doc1) VALUES ('$cnh', '$validade', '$uf', '$categoria', '$matricula', '$pontos', '$consulta', '$suspensa', '$nome_final1');";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	  //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	  $acao='Cadastrou cnh';
	  $tipo = 'cadastro';
	  //$logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');

	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";
	  
	echo "<script language='javascript' type='text/javascript'> alert('CNH cadastrada com sucesso.');window.location=\"../listagemcnh.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('CNH já cadastrada.');window.location=\"../listagemcnh.php\"	</script>";	
}
?>