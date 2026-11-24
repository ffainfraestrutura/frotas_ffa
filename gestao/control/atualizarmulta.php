<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$data=date('Y-m-d');

include '../vendor/autoload.php';

include_once"../../func/log.php";
include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "atualizarmulta";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/';

$arquivo = $_POST['arquivo'];
$matricula = $_POST['matricula'];


if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array);

	if(in_array($file_extension, $allowed_extension)){
		$reader = IOFactory::createReader('Xlsx', 'xls');
		$arquivo = "$nome-$data.$file_extension";
		
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();

		foreach($data as $row){

			if($row[0]<>'AIT'){//AIT, Placa, Etapa

				$autoinfracao = $row[0];			
				$placa = $row[1];				
				$etapa = $row[2];

				//tratando autoinfracao
				if(strpos($autoinfracao, "'") !== false){
					$autoinfracao = substr($autoinfracao,1);
				}

				$etapaf=utf8_decode($etapa);

				//print $autoinfracao;
				if($etapa == 'RECIBO ENVIADO PARA O DP'){
					$tramite= 'Finalizado Frota';
					$tdp = 'Validar Recibo';
					$tfin = '';
					$tsup = '';
					$status=2;
				}elseif($etapa == 'AGUARDANDO ASSINATURA CONDUTOR'){
					$tramite= 'Imprimir Recibo DP';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status=1;					
				}elseif($etapa == 'ABERTO'){
					$tramite= 'Inserir infrator';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status=1;
				}elseif($etapa == 'COLABORADOR DEMITIDO'){
					$tramite= 'Finalizado Frota';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status=0;

				}elseif($etapa == 'AGUARDANDO DESCONTO'){
					$tramite= 'Finalizado Frota';
					$tdp = 'Confirmar Desconto';
					$tfin = 'Fazer Pagamento';
					$tsup = '';
					$status=2;

				}elseif($etapa == 'CONCLUÍDO'){
					$tramite= 'Finalizado Frota';
					$tdp = 'DP Finalizado';
					$tfin = 'Finalizado Financeiro';
					$tsup = '';
					$status = 5;
				}elseif($etapa == 'EM ANÁLISE'){
					$tramite= 'Finalizado Frota';
					$tdp = 'Validar recibo';
					$tfin = '';
					$tsup = '';
					$status=2;
				}elseif($etapa == 'MULTA INDEVIDA'){
					$tramite= 'Finalizado Frota';
					$tdp = 'DP Finalizado';
					$tfin = 'Finalizado Financeiro';
					$tsup = '';
					$status=5;
				}elseif($etapa == 'RECUSA'){
					$tramite= '';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status=0;
				}elseif($etapa == 'COLABORADOR NÃO COMPARECEU'){
					$tramite= '';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status=0;
				}elseif($etapa == 'MULTA ASSINADA'){
					$tramite= 'Finalizado Frota';
					$tdp = 'DP Finalizado';
					$tfin = 'Finalizado Financeiro';
					$tsup = '';
					$status=5;
				}elseif($etapa == 'FINALIZADO FINANCEIRO'){
					$tramite= 'Finalizado Frota';
					$tdp = 'DP Finalizado';
					$tfin = 'Finalizado Financeiro';
					$tsup = '';
					$status=5;
				}else{
					$tramite= 'Inserir infrator';
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$staus = 1;
				}

				$sql = "SELECT * FROM bdfrota.tbmulta WHERE autoinfracao = '$autoinfracao' AND placa='$placa' ";
				$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				$linhas = mysqli_num_rows($resultado);
				
				$row=mysqli_fetch_array($resultado, MYSQLI_BOTH);
					$idtbmulta=$row['idtbmulta'];
				
				if($linhas <= 0){

				}else{		

					$sql1="UPDATE bdfrota.tbmulta SET etapa='$etapaf' WHERE idtbmulta='$idtbmulta' ";
					$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

					$sql2="UPDATE bdfrota.tbmovidatramite SET tramite='$tramite', tdp='$tdp',tfin='$tfin', tsup='$tsup', status='$status' WHERE placa='$placa' AND autoinfra='$autoinfracao' ";
					$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

//idmulta='$idtbmulta' OR ()

					//print "$sql1 <br> $sql2 <hr>";

				}
			}
		}
	}
}

$datahora = $hoje;
$acao = 'atualizou multas';
//$flag = '0';
$logresult = enviarlog($datahora, $acao, '', '',$matricula, '', '');

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'].$arquivo)) {
	// Upload efetuado com sucesso, 

	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../atualizarmulta.php\"</script>";
} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta	
	
	echo "Não foi possível enviar o arquivo, tente novamente";
	print "<script language='javascript' type='text/javascript'>alert('Não foi possível importar o arquivo, por favor tente novamente.');window.location=\"../atualizarmulta.php\"</script>";
}


?>