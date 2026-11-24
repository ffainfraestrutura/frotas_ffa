<?php

include("../../conecta03.php");
include_once"../../func/log3.php";


header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matricula'];

$unidadef = $_POST['unidade'];
$tpossef = $_POST['tp'];
$statusa = $_POST['statusa'];
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Inventário Veículos</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "rel-inventario-veiculos.xls";


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
            <th>Data Disp Locadora</th>
            <th>Data Dev Locadora</th>
            <th>Valor Aquisição</th>                
            
            <th>Data Últ. Vistoria</th>
            <th>Últ. Movimentação</th>
            <th>Data Últ. Movimentação</th>
            <th>Realizada Por</th>
            <th>Observação</th>
        </tr>";
    
 /*$sql = "SELECT a.placa, b.marca, c.modelo, a.hodometro AS hodometro, a.tipoposse,  a.oficina, e.nome as nomecol, f.numcnh, f.validade, DATEDIFF(f.validade, CURDATE()) AS dias_vencimento,
 a.matcond, e.cargo, a.dtentrega,  g.nome as nomeund, e.ccusto, a.anofabr, a.anomodelo, a.gpsemp, a.tagpedagio, h.fantasia, d.status, i.tipo AS tipovel
FROM bdfrota.tbveiculo a, bdfrota.tbmarcaveic b, bdfrota.tbmodeloveic c, bdfrota.tbastatusvel d, bdaniel.tbfuncionario e, bdfrota.tbcnh f, bdaniel.tbfilial g, bdfrota.tbfornecedor h, bdfrota.tbatipovel i
WHERE a.marca = b.idtbmarcaveic and a.modelo = c.idtbmodeloveic and a.matcond = e.matricula and a.matcond = f.matricula and e.codempresa = g.codempresa
 and e.codfilial = g.codfilial and a.idlocador = h.idtbfornecedor AND a.statusvel=d.idtbastatusvel AND a.tipovel = i.idtbatipovel";*/
//$sql="SELECT * FROM bdfrota.tbveiculo WHERE idtbveiculo<>'1241';";

if($tpossef<>'' && $unidadef<>'' && $statusa<>''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND tipoposse='$tpossef' AND unidade='$unidadef' AND status='$statusa' ;";
}elseif($tpossef<>'' && $unidadef<>'' && $statusa==''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND tipoposse='$tpossef' AND unidade='$unidadef' ;";
}elseif($tpossef<>'' && $unidadef=='' && $statusa==''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND tipoposse='$tpossef';";
}elseif($tpossef=='' && $unidadef<>'' && $statusa<>''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765')  AND unidade='$unidadef' AND status='$statusa' ;";
}elseif($tpossef=='' && $unidadef<>'' && $statusa==''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765')  AND unidade='$unidadef'";
}elseif($tpossef<>'' && $unidadef=='' && $statusa<>''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765')  AND tipoposse='$tpossef' AND status='$statusa';";
}elseif($tpossef=='' && $unidadef=='' && $statusa<>''){
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765') AND status='$statusa'";
}else{
    $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN ('1241','1767','1764','1765');";
}

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
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
    $eccusto = utf8_encode($row['ccusto']);
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

    $avalaquisicaof = str_replace(".", ",", $avalaquisicao);

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
    $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
    $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
        $dstatus= utf8_encode($row1['status']);

    //idtbmarcaveic, marca
    $sql2="SELECT marca FROM bdfrota.tbmarcaveic WHERE  idtbmarcaveic ='$bmarca' ";
     $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $bmarca=$row2['marca'];

    //idtbmodeloveic, modelo, marca
    $sql3="SELECT modelo FROM bdfrota.tbmodeloveic WHERE  idtbmodeloveic ='$cmodelo' ";
    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
    $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
        $cmodelo=$row3['modelo'];

    //nomecol
    $sql4="SELECT nome, cargo, codfilial, codempresa, cpf FROM bdaniel.tbfuncionario WHERE  matricula ='$amatcond' ";
    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
    $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
        $enome=utf8_encode($row4['nome']);
        $ecargo= utf8_encode($row4['cargo']);
        $codfilial = $row4['codfilial'];
        $codempresa  = $row4['codempresa'];
        $cpf  = $row4['cpf'];


    // , uf, categoria, matricula, pontos, consulta, suspensa
    $sql5="SELECT numcnh,validade,DATEDIFF(validade, CURDATE()) AS dias_vencimento FROM bdfrota.tbcnh WHERE  matricula ='$amatcond' ";
    $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
    $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
        $fnumcnh=$row5['numcnh'];
        $fvalidade= $row5['validade'];
        $dias_vencimento= $row5['dias_vencimento'];


    //$atipovel idtbatipovel, tipo
    $sql6="SELECT tipo FROM bdfrota.tbatipovel WHERE  idtbatipovel ='$atipovel' ";
    $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
    $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
        $atipovel=utf8_encode($row6['tipo']);

    //$gnome bdaniel.tbfilial g
    /*$sql7="SELECT nome FROM bdaniel.tbfilial WHERE  codempresa ='$codempresa' AND codfilial = '$codfilial' ";
    $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
    $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
        $gnome=$row7['nome'];*/
     
    //$hfantasia idtbfornecedor fantasia,
    $sql8="SELECT fantasia FROM bdfrota.tbfornecedor WHERE  idtbfornecedor ='$hfantasia'";
    $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
    $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
        $hfantasia=utf8_encode($row8['fantasia']);

    $sql9="SELECT MAX(idtbvistoria) AS idtbvistoria FROM bdfrota.tbvistoria WHERE  placa ='$aplaca'";
    $resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));
    $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
        $maxidtbvistoria= utf8_encode($row9['idtbvistoria']);

    $sql10="SELECT datavistoria FROM bdfrota.tbvistoria WHERE  idtbvistoria ='$maxidtbvistoria'";
    $resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
    $row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
        $datavistoria= utf8_encode($row10['datavistoria']);

    //ult movimentação
    $sql7="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE placa='$aplaca' AND (tipo='cadastro' OR tipo='edição') ";
    $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
    $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
      $idtblog=$row7['idtblog'];*/

    $sql8="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog' ";
    $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
    $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
      $acao=$row8['acao'];
      $autor=$row8['mat_autor'];
      $datamov=$row8['data_e_hora'];

    //autor ult movimentacao
    if($autor=='003535' || $autor=='003427'){
        $autor='EQUIPE TI';
    }else{
        $sql9="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor'; ";
        $resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));
        $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
          $autor=$row9['nome'];
    }

    if($placa!=''){
        $html .="
            <tr>
                <td>$aplaca</td>
                <td>$enome</td>
                <td>'$amatcond</td>
                <td>'$cpf</td>
                <td>$dstatus</td>
                <td>$eccusto</td>
                <td>$statusf</td>

                <td>$bmarca</td>
                <td>$cmodelo</td>
                <td>'$ahodometro</td>
                <td>$atipoposse</td> 
                <td>$aoficina</td>
                
                <td>'$fnumcnh</td>
                <td>$fvalidade</td>
                <td>'$dias_vencimento</td>
                
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
                <td>R$ $avalaquisicaof</td>
                
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

    $log = new Log($con);
    $datahora = $hoje;
    $acao = 'extraiu relatorio';
    //$flag = '0';
    $logresult = $log->enviarlog($datahora, $acao, '', '',$matricula, '', '');
    
    ?>
  </body>
</html>