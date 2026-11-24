<?php

include("../conecta2.php");

header("Content-type: text/html; charset=utf-8");
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
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Placa</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Hodometro</th>
                            <th>Propriedade</th>
                            <th>Status Veículo</th>
                            <th>Oficina</th>
                            <th>Condutor</th>
                            <th>CNH</th>
                            <th>Vencimento CNH</th>
                            <th>Dias p/ Vencimento</th>
                            <th>Matricula</th>
                            <th>Cargo</th>
                            <th>Data Entrega</th>
                            <th>Tipo Frota</th>
                            <th>Unidade</th>
                            <th>Centro de Custo</th>
                            <th>Ano Fabricação</th>
                            <th>Ano Modelo</th>
                            <th>GPS</th>
                            <th>TAG</th>
                            <th>Propriedade</th>
            </tr>";



    $sql = "SELECT a.placa, b.marca, c.modelo, a.hodometro, a.tipoposse, a.oficina, e.nome as nomecol, f.numcnh, f.validade, DATEDIFF(f.validade, CURDATE()) AS dias_vencimento,
 a.matcond, e.cargo, a.dtentrega, a.tipovel, g.nome as nomeund, e.ccusto, a.anofabr, a.anomodelo, a.gpsemp, a.tagpedagio, h.fantasia
FROM bdfrota.tbveiculo a, bdfrota.tbmarcaveic b, bdfrota.tbmodeloveic c, bdaniel.tbfuncionario e, bdfrota.tbcnh f, bdaniel.tbfilial g, bdfrota.tbfornecedor h
where a.marca = b.idtbmarcaveic and a.modelo = c.idtbmodeloveic and a.matcond = e.matricula and a.matcond = f.matricula and e.codempresa = g.codempresa
 and e.codfilial = g.codfilial and a.idlocador = h.idtbfornecedor";
   

       $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
      while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
        
$aplaca = $row['placa'];
 $bmarca = $row['marca'];
 $cmodelo = $row['modelo'];
 $ahodometrp = $row['hodometro'];
 $atipoposse = $row['tipoposse'];
 $dstatus = $row['status'];
 $aoficina = $row['oficina'];
 $enome = $row['nomecol'];
 $fnumcnh = $row['numcnh'];
 $fvalidade = $row['validade'];
 $dias_vencimento = $row['as_vencimento'];
 $amatcond = $row['matcond'];
 $ecargo = $row['cargo'];
 $adtentrega = $row['dtentrega'];
 $atipovel = $row['tipovel'];
 $gnome = $row['nomeund'];
 $eccusto = $row['ccusto'];
 $aanofabr = $row['anofabr'];
 $aanomodelo = $row['anomodelo'];
 $agpsemp = $row['gpsemp'];
 $atagpedagi = $row['tagpedagio'];
 $hfantasia = $row['fantasia'];
     

        $html .="<tr>
            <td>$aplaca</td>
            <td>$bmarca</td>
            <td>$cmodelo</td>
            <td>$ahodometro</td>
            <td>$atipoposse</td>
            <td>$dstatus</td>
            <td>$aoficina</td>
            <td>$enome</td>
            <td>$fnumcnh</td>
            <td>$fvalidade</td>
            <td>$dias_vencimento</td>
            <td>$amatcond</td>
            <td>$ecargo</td>
            <td>$adtentrega</td>
            <td>$atipovel</td>
            <td>$gnome</td>
            <td>$eccusto</td>
            <td>$aanofabr</td>
            <td>$aanomodelo</td>
            <td>$agpsemp</td>
            <td>$atagpedagio</td>
            <td>$hfantasia</td>
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