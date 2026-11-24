<?php
session_start();
include("../../conecta2.php");

$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

$hoje = date("Y-m-d H:i:s");//

$hojef=explode(" ", $hoje);
$dia=$hojef[0].$hojef[1];

header("Content-type: text/html; charset=utf-8");

?>
<!--*** gera excel**-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    
  <head>
  <body>
    <?php
    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Chamados-$dia.xls";
    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
    <!--<tr>
    <td colspan='4'>Relatorio Chamados</td>
    </tr>
    <tr>
    <td colspan='4'>$autor</td>
    </tr>-->";
    
    $html .= "<tr  class='bg-primary'>
              <th>NOME</th>
              <th>MATRICULA</th>
              <th>TELEFONE</th>
              <th>CENTRO DE CUSTO</th>
              <th>PLACA</th>
              <th>HODÔMETRO</th>
              <th>TIPO DE CHAMADO</th>
              <th>TIPO DE SINISTRO</th>
              <th>TIPO DE MANUTENÇÃO PREVENTIVA</th>
              <th>MANUTENÇÃO PREVENTIVA E CORRETIVA</th>
              <th>TIPO MANUTENÇÃO CORRETIVA</th>
              <th>STATUS</th>
              <th>CIENTE</th>
              <th>DATA DE ABERTURA</th>
              <th>DATA DE FECHAMENTO</th>
              <th>RESPOSTA</th>
			</tr>";
//idtbchamados, matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, data_fech, resposta
    $sql = "SELECT * FROM bdfrota.tbchamados WHERE idtbchamados<>1 AND matricula NOT IN ('003535', '');";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
    	$idtbchamados=$row['idtbchamados'];
    	$matricula = $row['matricula'];
    	$nome = $row['nome'];
    	$telefone = $row['telefone'];
    	$ccusto = $row['ccusto'];
    	$placa = $row['placa'] 	;
    	$hodometro = $row['hodometro'];
    	$tipodechamado = $row['tipodechamado'];
    	$tiposinistro = $row['tiposinistro'];
    	$tipomanuprev = $row['tipomanuprev'];
    	$prevcord = $row['prevcord'];
    	$tipomanucor = $row['tipomanucor'];
    	$status = $row['status'];
    	$ciente = $row['ciente'];
    	$data_abert = $row['data_abert'];
    	$data_fech = $row['data_fech'];
    	$resposta = $row['resposta'];

    	//tratando datas
    	$data_abert = explode(" ", $data_abert);
	    $data_abert = explode("-", $data_abert[0]);
	    $data_abert1 = "$data_abert[2]".'/'."$data_abert[1]".'/'."$data_abert[0]";

	    $data_fech = explode(" ", $data_fech);
	    $data_fech = explode("-", $data_fech[0]);
	    $data_fech1 = "$data_fech[2]".'/'."$data_fech[1]".'/'."$data_fech[0]";

	    if($data_fech1=='00/00/0000'){
	    	$data_fech1 = '-';
	    } else {
	    	$data_fech1 = $data_fech1;
	    }

	    //tratando status
	    if($status == 1){
	    	$statusf = 'Aberto';
	    } elseif($status == 2){
	    	$statusf = 'Fechado';
	    } elseif($status == 3){
	    	$statusf = 'Agendado';
	    } elseif($status == 4){
			$statusf = 'Cancelado';
		} else {
	    	$statusf = '-';
	    }
                  
		$html .= "
			<tr>
				<td>$nome</td>
				<td>'$matricula</td>
				<td>$telefone</td>
				<td>$ccusto</td>
				<td>$placa</td>
				<td>$hodometro</td>
				<td>$tipodechamado</td>
				<td>$tiposinistro</td>
				<td>$tipomanuprev</td>
				<td>$prevcord</td>
				<td>$tipomanucor</td>
				<td>$statusf</td>
				<td>$ciente</td>
				<td>$data_abert1</td>
				<td>$data_fech1</td>
				<td>$resposta</td>
			</tr>";
	
	}

    
    $html .= "</table>";

    
    // Configurações header para forçar o download
    header ("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
    header ("Content-Description: PHP Generated Data" );
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>