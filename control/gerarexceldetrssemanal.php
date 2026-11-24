<?php
session_start();
include("../conecta.php");
$idsup = $_POST["idtbsup"];
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Detalhes Gasto Semanal</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Detalhes-gasto-semanal.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                  <th>Nome</th>
                <th>Supervisor</th>
                <th>Centro de Custo</th>
                <th colspan='5' >Média</th>";
  if ($idsup == 'td' or $idcoord == 'td') {
$sql = "SELECT distinct(data) FROM bdfrota.tbrssemanal order by data desc LIMIT 8";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
$html .= "<th colspan='5' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
$html .= "</tr>
<tr>
                            <th></th>
                            <th></th>
                            <th></th>
                                                        <th>R$ Proj.</th>
                            <th>R$ Rod.</th>
                            <th>R$ Cota Ex.</th>
                            <th>R$ Total</th>
                            <th>R$ Diferença</th>";

$nsema = 4;

while ($nsema > $nsem) { 

    $html .= "                            <th>R$ Proj.</th>
                            <th>R$ Rod.</th>
                            <th>R$ Cota Ex.</th>
                            <th>R$ Total</th>
                            <th>R$ Diferença</th>";  
    $nsema = $nsema - 1;
}

  } else {
    $sql = "SELECT distinct(data) FROM bdfrota.tbrssemanal order by data desc LIMIT 8";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
$html .= "<th colspan='5' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
$html .= "</tr>
<tr>
                            <th></th>
                            <th></th>
                            <th></th>
                                                       <th>R$ Proj.</th>
                            <th>R$ Rod.</th>
                            <th>R$ Cota Ex.</th>
                            <th>R$ Total</th>
                            <th>R$ Diferença</th>";

$nsema = 4;

while ($nsema > $nsem) { 

    $html .= "                            <th>R$ Proj.</th>
                            <th>R$ Rod.</th>
                            <th>R$ Cota Ex.</th>
                            <th>R$ Total</th>
                            <th>R$ Diferença</th>";  
    $nsema = $nsema - 1;
}
  }         
    
$html .= "</tr>";


if ($idsup == 'td' or $idcoord == 'td') {

  $sql = "SELECT matricula, ROUND(AVG(rsproj),2), ROUND(AVG(rsrod),2), ROUND(AVG(rscota),2), ROUND(AVG(rsgps),2), ROUND(ROUND(AVG(rsproj),2) + ROUND(AVG(rscota),2),2) as total, ROUND(ROUND(AVG(rsproj),2) + ROUND(AVG(rscota),2) - ROUND(AVG(rsrod),2),2) as diferenca FROM bdfrota.tbrssemanal group by matricula ";
  
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
        $medrsproj = $row[1];
        $medrsos = $row[2];
        $medrsce = $row[3];
        $medrsgps = $row[4];
        $medrstot = $row[5];
        $medrsdiff = $row[6];

        if ($medrsdiff < 0) {
           $medrsdiff = 0;
        }

        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];
        $ccusto = utf8_encode($row2[1]);

        $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $idtbsupa = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupa'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            $html .= "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td>$ccusto</td>
              <td style='white-space:  nowrap;'>$medrsproj</td>
              <td style='white-space:  nowrap;'>$medrsos</td>
              <td style='white-space:  nowrap;'>$medrsce</td>
              <td style='white-space:  nowrap;'>$medrstot</td>
              <td style='white-space:  nowrap;'>$medrsdiff</td>";

         $sql2 = "SELECT data FROM bdfrota.tbrssemanal group by data order by data desc";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {

    $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff  FROM bdfrota.tbrssemanal where matricula = '$mattec' and data = '".$row2['data']."' and data >= '2023-04-24' LIMIT 8";
    $resultadoa = mysql_query($sqla) or die (mysql_error());
    $rowa = mysql_fetch_array($resultadoa);

if ($rowa[5] < 0) {
           $resultdiff = 0;
        } else {
            $resultdiff = $rowa[5];
        }

        $html .= "<td style='white-space:  nowrap;'>$rowa[0]</td>
              <td style='white-space:  nowrap;'>$rowa[1]</td>
              <td style='white-space:  nowrap;'>$rowa[3]</td>
              <td style='white-space:  nowrap;'>$rowa[4]</td>
              <td style='white-space:  nowrap;'>$resultdiff</td>";


}
 print"</tr>";            
                
    }


} else {
                          
$sql = "SELECT matricula, ROUND(AVG(rsproj),2), ROUND(AVG(rsrod),2), ROUND(AVG(rscota),2), ROUND(AVG(rsgps),2), ROUND(ROUND(AVG(rsproj),2) + ROUND(AVG(rscota),2),2) as total, ROUND(ROUND(AVG(rsproj),2) + ROUND(AVG(rscota),2) - ROUND(AVG(rsrod),2),2) as diferenca FROM bdfrota.tbrssemanal where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = $idsup) group by matricula";
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
        $medrsproj = $row[1];
        $medrsos = $row[2];
        $medrsce = $row[3];
        $medrsgps = $row[4];
        $medrstot = $row[5];
        $medrsdiff = $row[6];

if ($medrsdiff < 0) {
           $medrsdiff = 0;
        }

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            $html .= "<tr><td>$nometec</td>
              <td>$nomesup</td>
              <td>$ccusto</td>
              <td style='white-space:  nowrap;'>$medrsproj</td>
              <td style='white-space:  nowrap;'>$medrsos</td>
              <td style='white-space:  nowrap;'>$medrsce</td>
              <td style='white-space:  nowrap;'>$medrstot</td>
              <td style='white-space:  nowrap;'>$medrsdiff</td>";

            $sql2 = "SELECT data FROM bdfrota.tbrssemanal group by data order by data desc";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {

    $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota,2) as tot, ROUND(rsproj + rscota - rsrod,2) as diff  FROM bdfrota.tbrssemanal where matricula = '$mattec' and data = '".$row2['data']."'";
    $resultadoa = mysql_query($sqla) or die (mysql_error());
    $rowa = mysql_fetch_array($resultadoa);

if ($rowa[5] < 0) {
           $resultdiff = 0;
        } else {
            $resultdiff = $rowa[5];
        }

        $html .= "<td style='white-space:  nowrap;'>$rowa[0]</td>
              <td style='white-space:  nowrap;'>$rowa[1]</td>
              <td style='white-space:  nowrap;'>$rowa[3]</td>
              <td style='white-space:  nowrap;'>$rowa[4]</td>
              <td style='white-space:  nowrap;'>$resultdiff</td>";


}
 print"</tr>";            
                
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
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>