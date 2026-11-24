<?php

include("../../conecta03.php");
include_once "../../func/log3.php";

header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matr_autor'];
$unidadesf = $_POST['unidades'];
$basesf = $_POST['bases'];
$statusa = $_POST['statusa'];
$statusi = $_POST['statusi'];

$in = "('1241','1767','1764','1765','1326')";


//print "$basesf<br>$unidadesf<br>$status";

/*if(empty($status)){
    print "<br>ok<br>";
}*/
?>
<!--**
  * gera excel
 *-->
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <title>Relatório Veículos Ativos</title>

  <head>

  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    if ($statusa != '') {
      if ($statusi != '') {
        $arquivo = "relatorio-completo-veiculos-$unidadesf-$basesf.xls";

      } else {
        $arquivo = "relatorio-completo-veiculos-ativos-$unidadesf-$basesf.xls";
      }

    } elseif ($statusi != '') {
      if ($statusa != '') {
        $arquivo = "relatorio-completo-veiculos-$unidadesf-$basesf.xls";
      } else {
        $arquivo = "relatorio-completo-veiculos-inativos-$unidadesf-$basesf.xls";

      }

    } else {
      $arquivo = "relatorio-completo-veiculos-$unidadesf-$basesf.xls";

    }

    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Placa</th>
            <th>Nome</th>
            <th>Cargo</th>
            <th>Matrícula</th>
            <th>Centro Custo</th>           
            <th>Status Veículo</th>
            <th>Status</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Hodometro</th>            
            <th>Propriedade</th>
            <th>Tipo Frota</th>
            <th>Unidade</th>            
            <th>Ano Fabricação</th>
            <th>Ano Modelo</th>
            <th>GPS</th>
            <th>TAG Pedágio</th>
            <th>Locadora</th>
            <th>Data Disponibilização Locadora</th>
            <th>Data Devolução Locadora</th>
            <th>Valor Aquisição</th>
            <th>Data de Desmobilização</th>
            <th>Cor</th>
            <th>Combustível</th>
            <th>Chassi</th>
            <th>Renavam</th>                       
            <th>Nº Contrato Locação (Pedido)</th>
            <th>Qtde Eixos</th>
            <th>Contrato</th>
            <th>Base Gestão</th>
            <th>Status do Funcionário</th>
        </tr>";


    if ($statusa != '') {

      if ($statusi != '') {

        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in  and (status='$statusi' OR status='$statusa') AND unidade ='$unidadesf' AND basegestao IN $basesf;";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and (status='$statusi' OR status='$statusa') AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and (status='$statusi' OR status='$statusa') AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and (status='$statusi' OR status='$statusa');";
        }

      } else {

        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and status='$statusa' AND unidade = '$unidadesf' AND basegestao IN $basesf;";
          
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and status='$statusa' AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and status='$statusa' AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in and status='$statusa';";
        }
      }

    } else {

      if ($statusi != '') {
        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND unidade = '$unidadesf' AND basegestao IN $basesf AND status='$statusi';";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND basegestao IN $basesf  AND status='$statusi';";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND unidade = '$unidadesf'  AND status='$statusi';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in  AND status='$statusi';";
        }
      } else {
        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND unidade = '$unidadesf' AND basegestao IN $basesf;";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE visivel=1 AND idtbveiculo NOT IN $in;";
        }
      }
    }
    //print $sql;
    
    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {

      $placa = $row['placa'];
      $matricula = $row['matcond'];
      $statusvel = $row['statusvel'];
      $status = $row['status'];
      $marca = $row['marca'];
      $modelo = $row['modelo'];
      $hodometro = $row['hodometro'];
      $propriedade = $row['tipoposse'];
      $tipovel = $row['tipovel'];
      $unidade = $row['unidade'];
      $anofabr = $row['anofabr'];
      $anomodelo = $row['anomodelo'];
      $gps = $row['gps'];
      $tagpedagio = $row['tagpedagio'];
      $idlocador = $row['idlocador'];
      /*$dtabcontrato = $row['dtabcontrato'];
      $dtfechcontrato = $row['dtfechcontrato'];*/
      $dtdisponivelloc = $row['dtdisponivelloc'];
      $dtdevolucaoloc = $row['dtdevolucaoloc'];

      $valoraquisicao = $row['valaquisicao'];
      $dtdesmob = $row['dtdesmobilizacao'];
      $cor = $row['cor'];
      $combustivel = $row['combustivel'];
      $motorizacao = $row['motorizacao'];
      $chassi = $row['chassi'];
      $renavam = $row['renavam'];
      $uf = $row['uf'];
      $crvl = $row['doccrlv'];
      $ncontrato = $row['ncontloc'];
      $qteixos = $row['qtdeixos'];
      $qtneus = $row['qtdpneus'];
      $tanque = $row['tanque'];
      $ccusto = utf8_encode($row['ccusto']);
      $contrato = $row['contrato'];
      $basegestao = $row['basegestao'];

      if ($status == '0') {
        $statusf = 'INATIVO';
      } elseif ($status == '1') {
        $statusf = 'ATIVO';
      }

      if ($gps == '0') {
        $gps = 'NÃO';
      } elseif ($gps == '1') {
        $gps = 'SIM';
      }


      $sql1 = "SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$statusvel' ";
      $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
      $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
      $statusvel = $row1['status'];

      //idtbmarcaveic, marca
      $sql2 = "SELECT marca FROM bdfrota.tbmarcaveic WHERE  idtbmarcaveic ='$marca' ";
      $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
      $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
      $marcaf = $row2['marca'];

      //idtbmodeloveic, modelo, marca
      $sql3 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE  idtbmodeloveic ='$modelo' ";
      $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
      $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
      $modelof = $row3['modelo'];

      //nomecond
      $sql4 = "SELECT nome,cargo, status FROM bdaniel.tbfuncionario WHERE  matricula ='$matricula' ";
      $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
      $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
      $nomecond = $row4['nome'];
      $statuscond = $row4['status'];
      $cargocond = $row4['cargo'];

      //$atipovel idtbatipovel, tipo
      $sql6 = "SELECT tipo FROM bdfrota.tbatipovel WHERE  idtbatipovel ='$tipovel' ";
      $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
      $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
      $tipovel = $row6['tipo'];

      //$hfantasia idtbfornecedor fantasia,
      $sql8 = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE  idtbfornecedor ='$idlocador'";
      $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
      $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
      $locadora = $row8['fantasia'];

      /*if(($ccusto==' ' || empty($ccusto)) && !empty($matricula)){*/
      // $sql9="SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
      // $resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));
      // $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
      //   $ccusto= $row9['ccusto'];
      //}
    
      if ($propriedade == 'PROPRIO') {
        $propriedade = 'PRÓPRIO';
      }

      if ($placa != '') {
        $html .= "
        <tr>
            <td>$placa</td>
            <td>$nomecond</td> 
            <td>$cargocond</td> 
            <td>$matricula</td> 
            <td>$ccusto</td>
            <td>$statusvel</td>
            <td>$statusf</td>
            <td>$marcaf</td>
            <td>$modelof</td>
            <td>$hodometro</td>
            <td>$propriedade</td>
            <td>$tipovel</td>
            <td>$unidade</td>            
            <td>$anofabr</td>
            <td>$anomodelo</td>
            <td>$gps</td>
            <td>$tagpedagio</td>
            <td>$locadora</td>

            <td>$dtdisponivelloc</td>
            <td>$dtdevolucaoloc</td>

            <td>'$valoraquisicao</td>
            <td>$dtdesmob</td>
            <td>$cor</td>
            <td>$combustivel</td>
            <td>'$chassi</td>
            <td>'$renavam</td>
            <td>'$ncontrato</td>
            <td>'$qteixos</td>
            <td>$contrato</td>
            <td>$basegestao</td>
            <td>$statuscond</td>
        </tr>";
      }

    }

    $html .= "</table>";


    // Configurações header para forçar o download
    header("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
    header("Content-Description: PHP Generated Data");
    // Envia o conteúdo do arquivo
    echo $html;

    $log = new Log($con);
    $datahora = $hoje;
    $acao = 'extraiu relatorio';
    //$flag = '0';
    $logresult = $log->enviarlog($datahora, $acao, '', '', $matricula, '', '');

    ?>
  </body>

</html>