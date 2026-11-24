<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje = date('Y-m-d H:i:s');

$idtbmanprev = $_POST['idtbmanprev'];
$placa = $_POST['placa'];
$manutencao = utf8_decode($_POST['manutencao']);
$hodometro = $_POST['hodometro'];
$data = $_POST['data'];
$matr_autor = $_POST['matr_autor'];

//$statusveic = utf8_decode($_POST['statusveic']);
$atualizadoem = $_POST['atualizadoem'];
$solicitante = utf8_decode($_POST['solicitante']);
$status = utf8_decode($_POST['status']);
$etapa = utf8_decode($_POST['etapa']);
$dataocorrencia = $_POST['dataocorrencia'];
$modelo = utf8_decode($_POST['modelo']);

//$Km = $_POST['Km'];

$fornman = utf8_decode($_POST['fornman']);
$descricao = utf8_decode($_POST['descricao']);

$ccusto = utf8_decode($_POST['ccusto']);
if ($ccusto == '') {
    $stmt = $conexao->prepare("SELECT ccusto FROM bdfrota.tbveiculo WHERE placa = ?");
    $stmt->bind_param("s", $placa); 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $ccusto = $row['ccusto'];
    }
    $stmt->close();
}


//$condutor = utf8_decode($_POST['condutor']);

$oficina = utf8_decode($_POST['oficina']);
$dataagendamento = $_POST['dataagendamento'];
$horaagendamento = $_POST['horaagendamento'];
$prevsaida = $_POST['prevsaida'];
$dataentrada = $_POST['dataentrada'];
$dataretirada = $_POST['dataretirada'];
$tipopagamento = utf8_decode($_POST['tipopagamento']);
$reembolsoaprov = $_POST['reembolsoaprov'];
$valorreembolso = $_POST['valorreembolso'];
$valoroficina = $_POST['valoroficina'];
$valordesconto = $_POST['valordesconto'];
$valormaoobra = $_POST['valormaoobra'];
$valormaterial = $_POST['valormaterial'];
$valortransp = $_POST['valortransp'];
$outrosvalor = $_POST['outrosvalor'];
$descontarcond = utf8_decode($_POST['descontarcond']);
$datavencimento = $_POST['datavencimento'];
$datapagamento = $_POST['datapagamento'];
$formapagam = utf8_decode($_POST['formapagam']);
$condicaopag = utf8_decode($_POST['condicaopag']);
$numparc = $_POST['numparc'];
$valorparcela = $_POST['valorparcela'];
$dataprimparc = $_POST['dataprimparc'];
$protocolo = $_POST['protocolo'];
$dataconclusao = $_POST['dataconclusao'];
$placaanterior = $_POST['placaanterior'];
$observacao = utf8_decode($_POST['observacao']);



$agendamento = $dataagendamento." ".$horaagendamento.":00";

//tratando placa
if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} else {
  $placa = str_replace(" ", "", $placa);
}

//tratando valores
if(strpos($valorreembolso, " ") !== false){
  $valorreembolsoaux = explode(" ", $valorreembolso);
  $valorreembolso = str_replace(".", "", $valorreembolsoaux[1]);
  $valorreembolso = str_replace(",", ".", $valorreembolso);
}

if(strpos($valoroficina, " ") !== false){
  $valoroficinaaux = explode(" ", $valoroficina);
  $valoroficina = str_replace(".", "", $valoroficinaaux[1]);
  $valoroficina = str_replace(",", ".", $valoroficina);
}

if(strpos($valordesconto, " ") !== false){
  $valordescontoaux = explode(" ", $valordesconto);
  $valordesconto = str_replace(".", "", $valordescontoaux[1]);
  $valordesconto = str_replace(",", ".", $valordesconto);
}

/*if(strpos($valtotal, " ") !== false){
  $valtotalaux = explode(" ", $valtotal);
  $valtotal = str_replace(".", "", $valtotalaux[1]);
  $valtotal = str_replace(",", ".", $valtotal);
}*/

if(strpos($valormaoobra, " ") !== false){
  $valormaoobraaux = explode(" ", $valormaoobra);
  $valormaoobra = str_replace(".", "", $valormaoobraaux[1]);
  $valormaoobra = str_replace(",", ".", $valormaoobra);
}

if(strpos($valormaterial, " ") !== false){
  $valormaterialaux = explode(" ", $valormaterial);
  $valormaterial = str_replace(".", "", $valormaterialaux[1]);
  $valormaterial = str_replace(",", ".", $valormaterial);
}

if(strpos($valortransp, " ") !== false){
  $valortranspaux = explode(" ", $valortransp);
  $valortransp = str_replace(".", "", $valortranspaux[1]);
  $valortransp = str_replace(",", ".", $valortransp);
}

if(strpos($outrosvalor, " ") !== false){
  $outrosvaloraux = explode(" ", $outrosvalor);
  $outrosvalor = str_replace(".", "", $outrosvaloraux[1]);
  $outrosvalor = str_replace(",", ".", $outrosvalor);
}

if(strpos($valorparcela, " ") !== false){
  $valorparcelaaux = explode(" ", $valorparcela);
  $valorparcela = str_replace(".", "", $valorparcelaaux[1]);
  $valorparcela = str_replace(",", ".", $valorparcela);
}

//tratando valores
/*$valorreembolso = explode(" ", $valorreembolso);
$valorreembolso = str_replace(",", ".", $valorreembolso[1]);

$valoroficina = explode(" ", $valoroficina);
$valoroficina = str_replace(",", ".", $valoroficina[1]);

$valordesconto = explode(" ", $valordesconto);
$valordesconto = str_replace(",", ".", $valordesconto[1]);

$valtotal = explode(" ", $valtotal);
$valtotal = str_replace(",", ".", $valtotal[1]);

$valormaoobra = explode(" ", $valormaoobra);
$valormaoobra = str_replace(",", ".", $valormaoobra[1]);

$valormaterial = explode(" ", $valormaterial);
$valormaterial = str_replace(",", ".", $valormaterial[1]);

$valortransp = explode(" ", $valortransp);
$valortransp = str_replace(",", ".", $valortransp[1]);

$outrosvalor = explode(" ", $outrosvalor);
$outrosvalor = str_replace(",", ".", $outrosvalor[1]);

$valorparcela = explode(" ", $valorparcela);
$valorparcela = str_replace(",", ".", $valorparcela[1]);*/

//echo "ID $idtbmanprev, PLACA $placa, DATA $data, TIPO $manutencao,HODOMETRO/KM $hodometro, STATUSVEIC $statusveic, ATUALIZ $atualizadoem, SOLICITANTE $solicitante, STATUS MAN $status, ETAPA $etapa, DT OCOR $dataocorrencia, MODELO $modelo, FORNEC $fornman, DESC $descricao, CCUSTO $ccusto, COND $condutor,OFICINA $oficina,DT AGEND $dataagendamento, PREV SAIDA $prevsaida, DT ENT $dataentrada, DT RET $dataretirada, TIPO PAG $tipopagamento,REEMBOLSO $reembolsoaprov, VALOR REMB $valorreembolso, VAL OF $valoroficina, VAL DESC $valordesconto, VAL MO $valormaoobra, VAL MT $valormaterial, VAL TRANSP $valortransp,VAL OUTROS $outrosvalor, DESCONTADO $descontarcond, DT VENC $datavencimento, DT PAG $datapagamento, FORM PAG $formapagam, COND $condicaopag, NUM PARC $numparc, VALOR $valorparcela, DATA $dataprimparc, PROT $protocolo, DT CONCL $dataconclusao, PLACA ANT $placaanterior";

$arquivo=$_FILES['arquivo']['name']; 

  if(!empty($arquivo)){

    
    $_UP['pasta'] = '../docs/docmanutencao/';
    //$_UP['tamanho'] = 1024 * 1024 * 4; // 2Mb
   
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
    }/*else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
      echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
    }*/else {
      
      if ($_UP['renomeia'] == true) {
      
        //$nome_final = time().'.jpg';
        $nome_final = $placa."-".$hoje."-".$manutencao.$extensao;
        $nome_final1 = $_UP['pasta'].$nome_final;

      } else {    
        //$nome_final = $_FILES['arquivo']['name'];
        $nome_final = $placa."-".$hoje."-".$manutencao.$extensao;
        $nome_final1 = $_UP['pasta'].$nome_final;

      }
   
      // Depois verifica se é possível mover o arquivo para a pasta escolhida
      if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
      
      /*echo "Upload efetuado com sucesso!";
      echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';*/
      } else {
      
        //echo "Não foi possível enviar o arquivo, tente novamente";
      }
   
    }
  }

$sql = "UPDATE bdfrota.tbmanprev SET placa='$placa', data='$data', tipo='$manutencao', hodometro='$hodometro', atualizadoem='$atualizadoem', solicitante='$solicitante', status='$status', etapa='$etapa', dataocorrencia='$dataocorrencia', modelo='$modelo', fornman='$fornman', descricao='$descricao', ccusto='$ccusto', oficina='$oficina', dataagendamento='$agendamento', prevsaida='$prevsaida', dataentrada='$dataentrada', dataretirada='$dataretirada', tipopagamento='$tipopagamento', reembolsoaprov='$reembolsoaprov', valorreembolso='$valorreembolso', valoroficina='$valoroficina', valordesconto='$valordesconto', valormaoobra='$valormaoobra', valormaterial='$valormaterial', valortransp='$valortransp', outrosvalor='$outrosvalor', descontarcond='$descontarcond', datavencimento='$datavencimento', datapagamento='$datapagamento', formapagam='$formapagam', condicaopag='$condicaopag', numparc='$numparc', valorparcela='$valorparcela', dataprimparc='$dataprimparc', protocolo='$protocolo', dataconclusao='$dataconclusao', placaanterior='$placaanterior', observacao='$observacao', doc='$nome_final1' where idtbmanprev = '$idtbmanprev'";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

//echo $nome_final;

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Editou manutencao';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, $placa);

//echo "$hoje, $acao, '', $matr_autor, $tipo, $placa";

//echo "<script language='javascript' type='text/javascript'> window.alert ('Manutenção de veículo editada com sucesso.'); window.location=\"http://ffasip.ddns.net:4545/emailmanutencaoeditada.php?idtbmanprev=$idtbmanprev\"	</script>";

//echo "<script>window.alert('Manutenção de veículo editada com sucesso.');window.location=\"../listagemmanutencaoaberta.php\" </script>";

echo "
  <script language='javascript' type='text/javascript'>
    alert ('Manutenção de veículo editada com sucesso.'); 
    window.open('../../pdf/fpdf/ordemdeservicoget.php?id=$idtbmanprev', '_blank');
    window.location=\"../listagemmanutencaoaberta.php\";
  </script>";


?>