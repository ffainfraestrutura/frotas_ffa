<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$idtbveiculo = $_POST['idtbveiculo'];
$placa = strtoupper($_POST['placa']);
$uf = $_POST['uf'];
$aplicacaofrota = $_POST['aplicacaofrota'];
$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$versao = $_POST['versao'];
$categoria = utf8_decode($_POST['categoria']);
$tipoveic = utf8_decode($_POST['tipoveic']);
$cor = utf8_decode($_POST['cor']);
$zerokm = $_POST['zerokm'];
$anofabric = $_POST['anofabric'];
$anomodelo = $_POST['anomodelo'];
$velmax = $_POST['velmax'];
$renavam = $_POST['renavam'];
$chassi = $_POST['chassi'];
$nummotor = $_POST['nummotor'];
$combustivel = utf8_decode($_POST['combustivel']);
$tanque = $_POST['tanque'];
$motorizacao = $_POST['motorizacao'];
$nportas = $_POST['nportas'];
$npassageiros = $_POST['npassageiros'];
$calibragem = $_POST['calibragem'];
$aro = $_POST['aro'];
$qtdpneus = $_POST['qtdpneus'];
$qtdestepes = $_POST['qtdestepes'];
$qtdeixos = $_POST['qtdeixos'];
$gnv = $_POST['gnv'];
$gps = $_POST['gps'];
$tagpedagio = $_POST['tagpedagio'];
$status = $_POST['status'];
$hodometro = $_POST['hodometro'];
$tipoposse = utf8_decode($_POST['tipoposse']);
$locador = $_POST['locador'];
$matr_autor = $_POST['matr_autor'];
$hodometroinicial = $_POST['hodometroinicial'];
$obsveiculo = utf8_decode($_POST['obsveiculo']);
$statusvel = utf8_decode($_POST['statusvel']);
$datamovimentacao = $_POST['datamovimentacao'];
$matcond = $_POST['matcond'];
$oficina = utf8_decode($_POST['oficina']);
$dtentrega = $_POST['dtentrega'];
$dtdevolucao = $_POST['dtdevolucao'];
$tipovel = utf8_decode($_POST['tipovel']);
$situacao = utf8_decode($_POST['situacao']);
$doccrlv = utf8_decode($_POST['doccrlv']);
$airbag = $_POST['airbag'];
$gpsemp = utf8_decode($_POST['gpsemp']);
$rack = $_POST['rack'];
$ncontloc = utf8_decode($_POST['ncontloc']);
$dtdisponivelloc = $_POST['dtdisponivelloc'];
$dtdevolucaoloc = $_POST['dtdevolucaoloc'];
$valaquisicao = $_POST['valaquisicao'];
$totkmmensal = $_POST['totkmmensal'];
$blindagem = utf8_decode($_POST['blindagem']);

$baseffa = $_POST['baseffa'];
$basegestao = $_POST['basegestao'];
$centrocusto = $_POST['centrocusto'];

$dttermo = $_POST['dttermo'];
$dtdisp = $_POST['dtdisp'];

if($status==0){
  $matcond = '';
}

if($statusvel == '5' || $statusvel== '18' || $statusvel== '2' || $statusvel== '3' || $statusvel== '6' || $statusvel== '7' || $statusvel== '8' || $statusvel== '9' || $statusvel== '10' || $statusvel== '11' || $statusvel== '12' || $statusvel== '13' || $statusvel== '15' || $statusvel== '19' || $statusvel== '23' || $statusvel== '34' || $statusvel== '36' || $statusvel== '44') {
  $matcond = '';
}

if($statusvel == '32' && $matcond !=''){
  echo "<script language='javascript' type='text/javascript'> alert('Para status de Veículo INDISPONÍVEL, o campo matrícula do condutor não pode estar preenchido. Por favor, refaça o processo e se atente ao campo matrícula de condutor.');window.location=\"../listagemveiculos.php\" </script>";
}else{

  if (is_uploaded_file($_FILES['doc01']['tmp_name'])) {

    $dir1 = '/docs/';
    
    $_UP['pasta'] = '../docs/';
    $_UP['tamanho'] = 1024 * 1024 * 32; // 32Mb
    $_UP['extensoes'] = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    $_UP['renomeia'] = false;
    
    $_UP['erros'][0] = 'Não houve erro';
    $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
    $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
    $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
    $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
     
    
    if ($_FILES['doc01']['error'] != 0) {
      die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['doc01']['error']]);
      exit; // Para a execução do script
    }

    $extensao = strtolower(end(explode('.', $_FILES['doc01']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
      echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }else if ($_UP['tamanho'] < $_FILES['doc01']['size']) {
      echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    }else {
      
      if ($_UP['renomeia'] == true) {
      
        $nome_final = time().'.jpg';
      } else {
      
        $nomeaux = explode(".", $_FILES['doc01']['name']);
        $nomearq = str_replace(" ", "-", $nome );

        $nome_final = $placa."docbo.".$nomeaux[1];
        $dir_finald1 = $dir1.$nome_final;
      }

      if (move_uploaded_file($_FILES['doc01']['tmp_name'], $_UP['pasta'] . $nome_final)) {

      } else {
      // Não foi possível fazer o upload, provavelmente a pasta está incorreta
      echo "<script>alert('erro no envio do documento 01');</script>";
      echo "<script>window.location=\"../listagemveiculos.php.php\"</script>";
      }  
    }
  }

  $dtmovimentacao = $datamovimentacao." ".$horamovimentacao;

  //tratando valores
  $valaquisicao = explode(" ", $valaquisicao);
  $valaquisicao = str_replace(".", " ", $valaquisicao[1]);
  $valaquisicao = str_replace(",", ".", $valaquisicao);

  if(strpos($placa, "-") !== false){
    $placa = str_replace("-", "", $placa);
  } else {
    $placa = str_replace(" ", "", $placa);
  }

  if(strpos($hodometro, ".") !== false){
    $hodometro = str_replace(".", "", $hodometro);
  }

  //print "$statusvel";
  //echo "hodometroinicial = '$hodometroinicial', obsveiculo = '$obsveiculo', statusvel = '$statusvel', datamovimentacao = '$datamovimentacao', matcond = '$matcond', oficina = '$oficina', dtentrega = '$dtentrega', dtdevolucao = '$dtdevolucao', tipovel = '$tipovel', situacao = '$situacao', doccrlv = '$doccrlv', airbag = '$airbag', gpsemp = '$gpsemp', rack = '$rack', ncontloc = '$ncontloc', dtdisponivelloc = '$dtdisponivelloc', dtdevolucaoloc = '$dtdevolucaoloc', valaquisicao = '$valaquisicao', blindagem = '$blindagem'";

  $sql1 = "UPDATE bdfrota.tbveiculo SET placa = '$placa', uf = '$uf', aplicacao = '$aplicacaofrota', marca = '$marca', modelo='$modelo', versao='$versao', categoria ='$categoria', tipo='$tipoveic', cor='$cor', zerokm='$zerokm', anofabr='$anofabric', anomodelo='$anomodelo', velocmax='$velmax', renavam='$renavam', chassi='$chassi', nummotor='$nummotor', combustivel='$combustivel', tanque='$tanque', motorizacao='$motorizacao', nportas='$nportas', npassageiros='$npassageiros', calibragem= '$calibragem', aro='$aro', qtdpneus='$qtdpneus', qtdestepe='$qtdestepes', qtdeixos= '$qtdeixos', gnv='$gnv', gps='$gps',tagpedagio = '$tagpedagio', status='$status', hodometro = '$hodometro', tipoposse='$tipoposse', idlocador='$locador', hodometroinicial = '$hodometroinicial', obsveiculo = '$obsveiculo', statusvel = '$statusvel', datamovimentacao = '$dtmovimentacao', matcond = '$matcond', oficina = '$oficina', dtentrega = '$dtentrega', dtdevolucao = '$dtdevolucao', tipovel = '$tipovel', situacao = '$situacao', doccrlv = '$doccrlv', airbag = '$airbag', gpsemp = '$gpsemp', rack = '$rack', ncontloc = '$ncontloc', dtdisponivelloc = '$dtdisponivelloc', dtdevolucaoloc = '$dtdevolucaoloc', valaquisicao = '$valaquisicao', blindagem = '$blindagem', bo = '$dir_finald1', baseffa='$baseffa', basegestao='$basegestao', ccusto='$centrocusto', dttermodisp='$dttermo',dtdesmobilizacao='$dtdisp' where idtbveiculo = '$idtbveiculo';";

  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

  if($matcond != ''){
    $sql3="SELECT nome, codempresa, codfilial, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond' ";
    $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
    $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
      $nomecond = $row3['nome'];
      $codempresa = $row3['codempresa'];
      $codfilial = $row3['codfilial'];
      $ccustofunc = $row3['ccusto'];

    $sql3a="SELECT Estado, nome FROM bdaniel.tbfilial WHERE codempresa ='$codempresa' AND codfilial='$codfilial' ";
    $resultado3a = mysqli_query($conexao, $sql3a) or die(mysqli_error($conexao));
    $row3a = mysqli_fetch_array($resultado3a, MYSQLI_BOTH);
      $estado = $row3['Estado'];
      $nomefilial = $row3['nome'];

    $sql2="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1'; ";
    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
    $linhas2 = mysqli_num_rows($resultado2);
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
      $id = $row2['idtbcondutor'];
    
    if($linhas2<=0){
      $sql3="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nomecond', '$matcond', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
      $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
    
    }else{//$datavistoriaf
      $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$hoje', ativo = 0, statuscond='' WHERE idtbcondutor='$id'; ";
      $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));

      $sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nomecond', '$matcond', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
      $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($con));
    }

    $sql3b = "UPDATE bdfrota.tbveiculo SET unidade='$estado', ccusto='$ccustofunc' WHERE placa='$placa' ";
    $resultado3b = mysqli_query($conexao, $sql3b) or die(mysqli_error($conexao));


    /*$sql4 = "INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc) VALUES ('$nomecond', '$matcond','1','$placa', '$hoje');";//$dtmovimentacao
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));*/
  }

  if($status==0){

    $sql2="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1'; ";
    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($con));
    $linhas2 = mysqli_num_rows($resultado2);
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $id = $row2['idtbcondutor'];

    $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$hoje', ativo = 0, statuscond='' WHERE idtbcondutor='$id'; ";
    $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($con));
  }

  //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $acao='Editou veiculo';
  $tipo = 'cadastro';
  $logresult = enviarlognovo($hoje, $acao, $matcond, $matr_autor, $tipo, $placa);
  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

  echo "<script language='javascript' type='text/javascript'> alert('Cadastro de veículo editado com sucesso.');window.location=\"../listagemveiculos.php\" </script>";
}
?>