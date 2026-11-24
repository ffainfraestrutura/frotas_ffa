<?php

include("../../conecta2.php");

header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matr_autor'];
$unidadef = $_POST['unidades'];
$basesf = $_POST['bases'];
$statusa = $_POST['statusa'];
$statusi = $_POST['statusi'];

if(empty($unidadef)){
  $unidadef = 'TODOS';
}

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
    //$arquivo = "rel-veiculos-ativos-$unidadef-$basef.xls";
    $arquivo = "relatorio-reduzido-veiculos-ativos-$unidadef-$basesf.xls";
    


    // Criamos uma tabela HTML com o formato da planilha
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Placa</th>
            <th>Condutor</th>
            <th>Matricula</th>
            <th>CPF</th>            
            <th>Status Veículo</th>
            <th>Centro de Custo</th>
            <th>Status</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Hodometro</th>
            <th>Propriedade</th>            
            <th>Oficina</th>            
            <th>CNH</th>
            <th>Vencimento CNH</th>
            <th>Dias p/ Vencimento</th>            
            <th>Cargo</th>
            <th>Data Entrega</th>
            <th>Data Devolução</th>
            <th>Tipo Frota</th>
            <th>Base Gestão</th>
            <th>Unidade</th>            
            <th>Ano Fabricação</th>
            <th>Ano Modelo</th>
            <th>GPS</th>
            <th>TAG</th>
            <th>Locadora</th>
            <th>Data Disponibilização Locadora</th>
            <th>Data Devolução Locadora</th>
            <th>Valor Aquisição</th>

            <th>Data Termo de Disponibilização</th>
            <th>Data de Desmobilização</th>
            
            <th>Data Últ. Vistoria</th>

            <th>Últ. Movimentação</th>
            <th>Data Últ. Movimentação</th>
            <th>Feita por</th>

            <th>Observação</th>
        </tr>";
    
if($statusa != ''){

    if($statusi != ''){

      if($basesf != '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND unidade ='$unidadef' AND basegestao IN $basesf;";
      }elseif($basesf != '()' && $unidadef == 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND basegestao IN $basesf;";
      }elseif($basesf == '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa') AND unidade = '$unidadef';";
      }else{
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and (status='$statusi' OR status='$statusa');";
      }

    }else{

      if($basesf != '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND unidade = '$unidadef' AND basegestao IN $basesf;";print "aqui";
      }elseif($basesf != '()' && $unidadef == 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND basegestao IN $basesf;";
      }elseif($basesf == '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa' AND unidade = '$unidadef';";
      }else{
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') and status='$statusa';";
      }
    }

}else{

    if($statusi != ''){
      if($basesf != '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadef' AND basegestao IN $basesf AND status='$statusi';";
      }elseif($basesf != '()' && $unidadef == 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND basegestao IN $basesf  AND status='$statusi';";
      }elseif($basesf == '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadef'  AND status='$statusi';";
      }else{
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765')  AND status='$statusi';";
      }
    }else{
      if($basesf != '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadef' AND basegestao IN $basesf;";
      }elseif($basesf != '()' && $unidadef == 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND basegestao IN $basesf;";
      }elseif($basesf == '()' && $unidadef != 'TODOS'){
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND unidade = '$unidadef';";
      }else{
        $sql="SELECT * FROM bdfrota.tbveiculo WHERE idtbveiculo NOT IN ('1241','1767','1764','1765');";
      }
    }
} 

$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
        
$aplaca = $row['placa'];
$bmarca = $row['marca'];
$cmodelo = utf8_encode($row['modelo']);
$ahodometro = $row['hodometro'];
$atipoposse = utf8_encode($row['tipoposse']);
$status = utf8_encode($row['status']);

$dstatus = utf8_encode($row['statusvel']);
$aoficina = utf8_encode($row['oficina']);
$enome = utf8_encode($row['nomecol']);
$fnumcnh = $row['numcnh'];
$fvalidade = $row['validade'];
$dias_vencimento = $row['dias_vencimento'];
$amatcond = $row['matcond'];
$ecargo = utf8_encode($row['cargo']);
$adtentrega = $row['dtentrega'];
$adtdevolucao = $row['dtdevolucao'];

//$atipovel = $row['tipovel'];
$atipovel = utf8_encode($row['tipovel']);
$gnome = utf8_encode($row['unidade']);
$eccusto = $row['ccusto'];
$aanofabr = $row['anofabr'];
$aanomodelo = $row['anomodelo'];
$agpsemp = $row['gpsemp'];
$atagpedagio = $row['tagpedagio'];
$hfantasia = utf8_encode($row['idlocador']);

$adtdisponivelloc = $row['dtdisponivelloc'];
$adtdevolucaoloc = $row['dtdevolucaoloc'];
$avalaquisicao = $row['valaquisicao'];
$aobsveiculo = utf8_encode($row['obsveiculo']);
$adatamovimentacao = $row['datamovimentacao'];
$basegestao = utf8_encode($row['basegestao']);

$dttermo = $row['dttermodisp'];
$dtdisp = $row['dtdesmobilizacao'];

 if($atagpedagio == '1'){
    $atagpedagio = 'SIM';
 } elseif($atagpedagio == '0'){
    $atagpedagio = 'NÃO';
 }

 if($status == '0'){
    $statusf = 'INATIVO';
 }elseif($status == '1'){
    $statusf = 'ATIVO';
 }

 $sql1="SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$dstatus' ";
     $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
        $dstatus= utf8_encode($row1['status']);

    //idtbmarcaveic, marca
    $sql2="SELECT marca FROM bdfrota.tbmarcaveic WHERE  idtbmarcaveic ='$bmarca' ";
     $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $bmarca=$row2['marca'];

    //idtbmodeloveic, modelo, marca
    $sql3="SELECT modelo FROM bdfrota.tbmodeloveic WHERE  idtbmodeloveic ='$cmodelo' ";
    $resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
    $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
        $cmodelo=$row3['modelo'];

    //nomecol
    $sql4="SELECT nome, cargo, codfilial, codempresa, cpf FROM bdaniel.tbfuncionario WHERE  matricula ='$amatcond' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
    $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        $enome=utf8_encode($row4['nome']);
        $ecargo= utf8_encode($row4['cargo']);
        $codfilial = $row4['codfilial'];
        $codempresa  = $row4['codempresa'];
        $cpf  = $row4['cpf'];


    // , uf, categoria, matricula, pontos, consulta, suspensa
    $sql5="SELECT numcnh,validade,DATEDIFF(validade, CURDATE()) AS dias_vencimento FROM bdfrota.tbcnh WHERE  matricula ='$amatcond' ";
    $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
    $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $fnumcnh=$row5['numcnh'];
        $fvalidade= $row5['validade'];
        $dias_vencimento= $row5['dias_vencimento'];


    //$atipovel idtbatipovel, tipo
    $sql6="SELECT tipo FROM bdfrota.tbatipovel WHERE  idtbatipovel ='$atipovel' ";
    $resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
    $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
        $atipovel=utf8_encode($row6['tipo']);

    //$gnome bdaniel.tbfilial g
    /*$sql7="SELECT nome FROM bdaniel.tbfilial WHERE  codempresa ='$codempresa' AND codfilial = '$codfilial' ";
    $resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));
    $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
        $gnome=$row7['nome'];*/
     
    //$hfantasia idtbfornecedor fantasia,
    $sql8="SELECT fantasia FROM bdfrota.tbfornecedor WHERE  idtbfornecedor ='$hfantasia'";
    $resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
    $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
        $hfantasia=utf8_encode($row8['fantasia']);

    //ultima vistoria
    $sql9="SELECT MAX(idtbvistoria) AS idtbvistoria FROM bdfrota.tbvistoria WHERE  placa ='$aplaca'";
    $resultado9 = mysqli_query($conexao, $sql9) or die(mysqli_error($conexao));
    $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
        $maxidtbvistoria= utf8_encode($row9['idtbvistoria']);

    $sql10="SELECT datavistoria FROM bdfrota.tbvistoria WHERE  idtbvistoria ='$maxidtbvistoria'";
    $resultado10 = mysqli_query($conexao, $sql10) or die(mysqli_error($conexao));
    $row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
        $datavistoria= utf8_encode($row10['datavistoria']);

    //ultima movimentacao
    $sql11="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE placa='$aplaca' AND (tipo='cadastro' OR tipo='edição') ";
    $resultado11 = mysqli_query($conexao, $sql11) or die(mysqli_error($conexao));
    $row11 = mysqli_fetch_array($resultado11, MYSQLI_BOTH);
      $idtblog=$row11['idtblog'];

    $sql12="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog' ";
    $resultado12 = mysqli_query($conexao, $sql12) or die(mysqli_error($conexao));
    $row12 = mysqli_fetch_array($resultado12, MYSQLI_BOTH);
      $acao=$row12['acao'];
      $autor=$row12['mat_autor'];
      $datamov=$row12['data_e_hora'];

    //autor da ultima movimentacao
    if($autor=='003535' || $autor=='003427'){
      $autor = 'EQUIPE TI';
    }else{
      $sql13="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor'; ";
        $resultado13 = mysqli_query($conexao, $sql13) or die(mysqli_error($conexao));
        $row13 = mysqli_fetch_array($resultado13, MYSQLI_BOTH);
          $autor=$row13['nome'];
    }
    
  if($aplaca<>''){
    $html .="
        <tr>
          <td>$aplaca</td>
          <td>$enome</td>
          <td>'$amatcond</td>
          <td>'$cpf</td>
          <td>$statusf</td>
          <td>$eccusto</td>
          <td>$dstatus</td>
          <td>$bmarca</td>
          <td>$cmodelo</td>
          <td>'$ahodometro</td>
          <td>$atipoposse</td>
          <td>$aoficina</td>
          
          <td>'$fnumcnh</td>
          <td>$fvalidade</td>
          <td $style>$dias_vencimento</td>
          
          <td>$ecargo</td>

          <td>$adtentrega</td>
          <td>$adtdevolucao</td>

          <td>$atipovel</td>
          <td>$basegestao</td>        
          <td>$gnome</td>
          
          <td>$aanofabr</td>
          <td>$aanomodelo</td>
          <td>$agpsemp</td>
          <td>$atagpedagio</td>
          <td>$hfantasia</td>

          <td>$adtdisponivelloc</td>
          <td>$adtdevolucaoloc</td>
          <td>$avalaquisicao</td>

          <td>$dttermo</td>
          <td>$dtdisp</td>
          
          <td>$datavistoria</td>

          <td>$acao</td>
          <td>$datamov</td>
          <td>$autor</td>

          <td>$aobsveiculo</td>
        </tr>";
  }

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
    // Envia o conteúdo do arquivo*/
    echo $html;

$datahora = $hoje;
$acao = 'extraiu relatorio';
//$flag = '0';
$logresult = enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>