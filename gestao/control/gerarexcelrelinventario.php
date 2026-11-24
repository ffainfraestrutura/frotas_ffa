<?php

//CONFIGURAÇÕES DE PERFORMANCE MÁXIMAS
set_time_limit(600);        // 10 minutos de timeout
ini_set('memory_limit', '1024M');  // 1GB de memória
ini_set('max_execution_time', '600');

include("../../conecta03.php");
include_once"../../func/log3.php";


header("Content-type: text/html; charset=utf-8");

$matricula = $_POST['matricula'];

$unidadef = $_POST['unidade'];
$tpossef = $_POST['tp'];
$statusa = $_POST['statusa'];

$in = "('1241','1767','1764','1765', '1893', '1894', '1895', '1896')";

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

// MEGA OTIMIZAÇÃO: Query única com JOINs para buscar TODOS os dados de uma vez
$sql = "
SELECT 
    v.*,
    st.status as status_nome,
    m.marca as marca_nome,
    mo.modelo as modelo_nome,
    f.nome as condutor_nome,
    f.cargo as condutor_cargo,
    f.cpf as condutor_cpf,
    f.ccusto as condutor_ccusto,
    c.numcnh,
    c.validade as cnh_validade,
    DATEDIFF(c.validade, CURDATE()) as dias_vencimento,
    tv.tipo as tipo_veiculo,
    fo.fantasia as locadora_nome,
    (SELECT datavistoria FROM bdfrota.tbvistoria WHERE placa = v.placa ORDER BY idtbvistoria DESC LIMIT 1) as ultima_vistoria,
    (SELECT acao FROM bdfrota.tblog WHERE placa = v.placa AND (tipo='cadastro' OR tipo='edição') ORDER BY idtblog DESC LIMIT 1) as ultima_acao,
    (SELECT mat_autor FROM bdfrota.tblog WHERE placa = v.placa AND (tipo='cadastro' OR tipo='edição') ORDER BY idtblog DESC LIMIT 1) as mat_autor,
    (SELECT data_e_hora FROM bdfrota.tblog WHERE placa = v.placa AND (tipo='cadastro' OR tipo='edição') ORDER BY idtblog DESC LIMIT 1) as data_ultima_mov
FROM bdfrota.tbveiculo v
LEFT JOIN bdfrota.tbastatusvel st ON v.statusvel = st.idtbastatusvel
LEFT JOIN bdfrota.tbmarcaveic m ON v.marca = m.idtbmarcaveic
LEFT JOIN bdfrota.tbmodeloveic mo ON v.modelo = mo.idtbmodeloveic
LEFT JOIN bdaniel.tbfuncionario f ON v.matcond = f.matricula
LEFT JOIN bdfrota.tbcnh c ON v.matcond = c.matricula
LEFT JOIN bdfrota.tbatipovel tv ON v.tipovel = tv.idtbatipovel
LEFT JOIN bdfrota.tbfornecedor fo ON v.idlocador = fo.idtbfornecedor
WHERE v.idtbveiculo NOT IN $in";

// Adicionar filtros conforme parâmetros
if($tpossef<>'' && $unidadef<>'' && $statusa<>''){
    $sql .= " AND v.tipoposse='$tpossef' AND v.unidade='$unidadef' AND v.status='$statusa'";
}elseif($tpossef<>'' && $unidadef<>'' && $statusa==''){
    $sql .= " AND v.tipoposse='$tpossef' AND v.unidade='$unidadef'";
}elseif($tpossef<>'' && $unidadef=='' && $statusa==''){
    $sql .= " AND v.tipoposse='$tpossef'";
}elseif($tpossef=='' && $unidadef<>'' && $statusa<>''){
    $sql .= " AND v.unidade='$unidadef' AND v.status='$statusa'";
}elseif($tpossef=='' && $unidadef<>'' && $statusa==''){
    $sql .= " AND v.unidade='$unidadef'";
}elseif($tpossef<>'' && $unidadef=='' && $statusa<>''){
    $sql .= " AND v.tipoposse='$tpossef' AND v.status='$statusa'";
}elseif($tpossef=='' && $unidadef=='' && $statusa<>''){
    $sql .= " AND v.status='$statusa'";
}

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
        
// OTIMIZAÇÃO: Dados já vêm da query principal com JOINs - sem necessidade de queries adicionais!
$aplaca = $row['placa'];
$bmarca = $row['marca_nome'];  // Já vem do JOIN
$cmodelo = $row['modelo_nome'];  // Já vem do JOIN
$ahodometro = $row['hodometro'];
$atipoposse = $row['tipoposse'];
$status = $row['status'];

$dstatus = $row['status_nome'];  // Já vem do JOIN
$aoficina = $row['oficina'];
$enome = $row['condutor_nome'];  // Já vem do JOIN
$fnumcnh = $row['numcnh'];  // Já vem do JOIN
$fvalidade = $row['cnh_validade'];  // Já vem do JOIN
$dias_vencimento = $row['dias_vencimento'];  // Já vem do JOIN calculado
$amatcond = $row['matcond'];
$ecargo = $row['condutor_cargo'];  // Já vem do JOIN
$adtentrega = $row['dtentrega'];
$adtdevolucao = $row['dtdevolucao'];

$atipovel = $row['tipo_veiculo'];  // Já vem do JOIN
$gnome = $row['unidade'];
$eccusto = $row['condutor_ccusto'];  // Já vem do JOIN (renomeado para ccusto)
$ccusto = $row['condutor_ccusto'];  // Para usar no HTML
$aanofabr = $row['anofabr'];
$aanomodelo = $row['anomodelo'];
$agpsemp = $row['gpsemp'];
$atagpedagio = $row['tagpedagio'];
$hfantasia = $row['locadora_nome'];  // Já vem do JOIN

$adtdisponivelloc = $row['dtdisponivelloc'];
$adtdevolucaoloc = $row['dtdevolucaoloc'];
$avalaquisicao = $row['valaquisicao'];
$aobsveiculo = $row['obsveiculo'];
$adatamovimentacao = $row['datamovimentacao'];

$basegestao = $row['basegestao'];
$cpf = $row['condutor_cpf'];  // Já vem do JOIN

// Dados de vistoria e log já vem da query principal
$datavistoria = $row['ultima_vistoria'];
$acao = $row['ultima_acao'];
$autor = $row['mat_autor'];
$datamov = $row['data_ultima_mov'];

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

 // REMOVIDAS TODAS AS QUERIES INDIVIDUAIS - dados já vêm da query principal!
 
    //autor ult movimentacao
    if($autor=='003535' || $autor=='003427'){
        $autor='EQUIPE TI';
    }elseif($autor<>''){  // Só busca se tiver autor
        $sql9="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor' LIMIT 1";
        $resultado9 = mysqli_query($con, $sql9);
        if($resultado9 && mysqli_num_rows($resultado9) > 0){
            $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
            $autor=$row9['nome'];
            mysqli_free_result($resultado9);
        }
    }

    


if($aplaca<>''){
$html .="
    <tr>
        <td>$aplaca</td>
        <td>$enome</td>
        <td>'$amatcond</td>
        <td>'$cpf</td>
        <td>$dstatus</td>

        <td>$ccusto</td>
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

//  Libera resultado da query principal
mysqli_free_result($resultado);
              
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