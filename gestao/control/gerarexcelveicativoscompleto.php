<?php

include("../../conecta2.php");

header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matr_autor'];
$unidadesf = $_POST['unidades'];
$basesf = $_POST['bases'];
$statusa = $_POST['statusa'];
$statusi = $_POST['statusi'];


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
            <th>Nome e Matrícula condutor</th>
            <th>Centro Custo</th>           
            <th>Status Veículo</th>
            <th>Status</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Hodometro</th>
            <th>Hodometro Inicial</th>
            <th>Propriedade</th>            
            <th>Oficina</th>
            <th>Tipo Frota</th>
            <th>Unidade</th>            
            <th>Ano Fabricação</th>
            <th>Ano Modelo</th>
            <th>GPS</th>
            <th>GPS Empresa</th>
            <th>TAG Pedágio</th>
            <th>Locadora</th>
            <th>Data Disponibilização Locadora</th>
            <th>Data Devolução Locadora</th>
            <th>Valor Aquisição</th>
            <th>Data Termo de Disponibilização</th>
            <th>Data de Desmobilização</th>
            <th>Versão</th>
            <th>Cor</th>
            <th>Tipo Veículo</th>
            <th>Aplicação</th>
            <th>Categoria</th>
            <th>Combustível</th>
            <th>Motorização</th>
            <th>Chassi</th>
            <th>Renavam</th>
            <th>Nº Portas</th>
            <th>UF Emplacamento</th>
            <th>Documento Ano (CRLV)</th>
            <th>Air Bag</th>
            <th>Zero KM</th>
            <th>GNV</th>
            <th>Rack</th>
            <th>Nº Contrato Locação (Pedido)</th>
            <th>Nº Motor</th>
            <th>Qtde Eixos</th>
            <th>Qtde Pneus</th>
            <th>Qtde Estepe</th>
            <th>Capacidade Tanque</th>
            <th>Nível Blindagem</th>
        </tr>";


    if ($statusa != '') {

      if ($statusi != '') {

        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND unidade ='$unidadesf' AND basegestao IN $basesf;";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa');";
        }

      } else {

        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND unidade = '$unidadesf' AND basegestao IN $basesf;";
          print "aqui";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa';";
        }
      }

    } else {

      if ($statusi != '') {
        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadesf' AND basegestao IN $basesf AND status='$statusi';";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND basegestao IN $basesf  AND status='$statusi';";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadesf'  AND status='$statusi';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765')  AND status='$statusi';";
        }
      } else {
        if ($basesf != '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadesf' AND basegestao IN $basesf;";
        } elseif ($basesf != '()' && $unidadesf == 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND basegestao IN $basesf;";
        } elseif ($basesf == '()' && $unidadesf != 'TODOS') {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadesf';";
        } else {
          $sql = "SELECT * FROM bdfrota.tbveiculo WHERE idtbveiculo NOT IN ('1241','1767','1764','1765');";
        }
      }
    }
    //print $sql;
    
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {

      $placa = $row['placa'];
      $matricula = $row['matcond'];
      $statusvel = $row['statusvel'];
      $status = $row['status'];
      $marca = $row['marca'];
      $modelo = $row['modelo'];
      $hodometro = $row['hodometro'];
      $hodometroi = $row['hodometroinicial'];
      $propriedade = $row['tipoposse'];
      $oficina = $row['oficina'];
      $tipo = $row['tipo'];
      $unidade = $row['unidade'];
      $anofabr = $row['anofabr'];
      $anomodelo = $row['anomodelo'];
      $gps = $row['gps'];
      $gpsemp = utf8_encode($row['gpsemp']);
      $tagpedagio = $row['tagpedagio'];
      $idlocador = $row['idlocador'];
      $dtdisplocadora = $row['dtdisponivelloc'];
      $dtdevlocadora = $row['dtdevolucaoloc'];
      $valoraquisicao = $row['valaquisicao'];
      $dttermo = $row['dttermodisp'];
      $dtdesmob = $row['dtdesmobilizacao'];
      $versao = utf8_encode($row['versao']);
      $cor = utf8_encode($row['cor']);
      $tipovel = $row['tipovel'];
      $aplicacao = $row['aplicacao'];
      $categoria = utf8_encode($row['categoria']);
      $combustivel = utf8_encode($row['combustivel']);
      $motorizacao = $row['motorizacao'];
      $chassi = $row['chassi'];
      $renavam = $row['renavam'];
      $nportas = $row['nportas'];
      $uf = $row['uf'];
      $crvl = $row['doccrlv'];
      $airbag = $row['airbag'];
      $zerokm = $row['zerokm'];
      $gnv = $row['gnv'];
      $rack = $row['rack'];
      $ncontrato = $row['ncontloc'];
      $nmotor = $row['nummotor'];
      $qteixos = $row['qtdeixos'];
      $qtneus = $row['qtdpneus'];
      $qtestepe = $row['qtdestepe'];
      $tanque = $row['tanque'];
      $blindagem = $row['blindagem'];
      $ccusto = $row['ccusto']; 
    
      if ($tagpedagio == '1') {
        $tagpedagio = 'SIM';
      } elseif ($tagpedagio == '0') {
        $tagpedagio = 'NÃO';
      }

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

      if ($airbag == '0') {
        $airbag = 'NÃO';
      } elseif ($airbag == '1') {
        $airbag = 'SIM';
      }

      if ($zerokm == '0') {
        $zerokm = 'NÃO';
      } elseif ($zerokm == '1') {
        $zerokm = 'SIM';
      }

      if ($gnv == '0') {
        $gnv = 'NÃO';
      } elseif ($gnv == '1') {
        $gnv = 'SIM';
      }

      if ($rack == '0') {
        $rack = 'NÃO';
      } elseif ($rack == '1') {
        $rack = 'SIM';
      }

      $sql1 = "SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$statusvel' ";
      $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
      $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
      $statusvel = utf8_encode($row1['status']);

      //idtbmarcaveic, marca
      $sql2 = "SELECT marca FROM bdfrota.tbmarcaveic WHERE  idtbmarcaveic ='$marca' ";
      $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
      $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
      $marcaf = $row2['marca'];

      //idtbmodeloveic, modelo, marca
      $sql3 = "SELECT modelo FROM bdfrota.tbmodeloveic WHERE  idtbmodeloveic ='$modelo' ";
      $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
      $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
      $modelof = $row3['modelo'];

      //nomecond
      $sql4 = "SELECT nome, cargo, codfilial, codempresa, cpf FROM bdaniel.tbfuncionario WHERE  matricula ='$matricula' ";
      $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
      $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
      $nomecond = utf8_encode($row4['nome']);

      //$atipovel idtbatipovel, tipo
      $sql6 = "SELECT tipo FROM bdfrota.tbatipovel WHERE  idtbatipovel ='$tipovel' ";
      $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
      $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
      $tipovel = utf8_encode($row6['tipo']);

      $sql7 = "SELECT aplicacao FROM bdfrota.tbaplicacaoveic WHERE  idtbaplicacaoveic ='$aplicacao'; ";
      $resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));
      $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
      $aplicacao = utf8_encode($row7['aplicacao']);


      //$hfantasia idtbfornecedor fantasia,
      $sql8 = "SELECT fantasia FROM bdfrota.tbfornecedor WHERE  idtbfornecedor ='$idlocador'";
      $resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
      $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
      $locadora = utf8_encode($row8['fantasia']);



      $html .= "
        <tr>
            <td>$placa</td>
            <td>$nomecond - '$matricula</td> 
            <td>$ccusto</td>
            <td>$statusvel</td>
            <td>$statusf</td>
            <td>$marcaf</td>
            <td>$modelof</td>
            <td>'$hodometro</td>
            <td>'$hodometroi</td>
            <td>$propriedade</td>            
            <td>$oficina</td> 
            <td>$tipo</td>
            <td>$unidade</td>            
            <td>$anofabr</td>
            <td>$anomodelo</td>
            <td>$gps</td>
            <td>$gpsemp</td>
            <td>$tagpedagio</td>
            <td>$locadora</td>
            <td>$dtdisplocadora</td>
            <td>$dtdevlocadora</td>
            <td>'$valoraquisicao</td>
            <td>$dttermo</td>
            <td>$dtdesmob</td>
            <td>$versao</td>
            <td>$cor</td>
            <td>$tipovel</td>
            <td>$aplicacao</td>
            <td>$categoria</td>
            <td>$combustivel</td>
            <td>'$motorizacao</td>
            <td>'$chassi</td>
            <td>'$renavam</td>
            <td>'$nportas</td>
            <td>$uf</td>
            <td>'$crvl</td>
            <td>$airbag</td>
            <td>$zerokm</td>
            <td>$gnv</td>
            <td>$rack</td>
            <td>'$ncontrato</td>
            <td>'$nmotor</td>
            <td>'$qteixos</td>
            <td>'$qtneus</td>
            <td>'$qtestepe</td>
            <td>'$tanque</td>
            <td>$blindagem</td>
        </tr>";

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

    $datahora = $hoje;
    $acao = 'extraiu relatorio veiculos';
    //$flag = '0';
//$logresult = enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>

</html>