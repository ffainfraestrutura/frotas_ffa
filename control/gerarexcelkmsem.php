<?php
session_start();
include("../conecta.php");
$usuariof = $_SESSION['usuario'];
$data = $_POST["data"];
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
    <title>Detalhes Km Semanal</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Detalhes-km-sem.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                  <th>Nome</th>
                            <th>Supervisor</th>
                            <th colspan='3' >Média</th>";


 $sql = "SELECT distinct(data) FROM bdfrota.tbkmsemanal where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsup') order by data desc LIMIT 3";
    $resultado = mysql_query($sql) or die (mysql_error());
$nsem = 4;
while ($row = mysql_fetch_array($resultado)) {
$dataa = $row[0];
$datas = explode(" ", $dataa);
$datasb = explode("-", $datas[0]);
$html .="<th colspan='4' >Semana $datasb[2]/$datasb[1]</th>";

$nsem = $nsem - 1;
}
$html .="</tr>
<tr>
                            <th> </th>
                            <th> </th>
                            <th>KM Proj.</th>
                            <th>KM Rod.</th>
                            <th>KM Cota Ex.</th>
";

$nsema = 4;

while ($nsema > $nsem) {
    
      $html .="<th>KM Proj.</th>
                            <th>KM OS</th>
                            <th>KM Cota Ex.</th>
                            <th>KM GPS</th>";  
    $nsema = $nsema - 1;
}
                   $html .=" </tr>";          

$sql = "SELECT matricula, ROUND(AVG(kmproj),2), ROUND(AVG(kmrod),2), ROUND(AVG(kmcota),2), ROUND(AVG(kmgps),2) FROM bdfrota.tbkmsemanal where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = $idsup) group by matricula";
    $resultado = mysql_query($sql) or die (mysql_error());


while ($row = mysql_fetch_array($resultado)) {
        
       $mattec = $row[0];
        $medkmproj = $row[1];
        $medkmos = $row[2];
        $medkmce = $row[3];
        $medkmgps = $row[4];

        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nometec = $row2[0];

        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        

            $html .="<tr>
                <td>$nometec</td>
              <td>$nomesup</td>
              <td>$medkmproj</td>
              <td>$medkmos</td>
              <td>$medkmce</td>";

            $sql2 = "SELECT kmproj, kmrod, data, kmcota, kmgps FROM bdfrota.tbkmsemanal where matricula = '$mattec' order by data desc LIMIT 3";
    $resultado2 = mysql_query($sql2) or die (mysql_error());


while ($row2 = mysql_fetch_array($resultado2)) {


        $html .="<td>$row2[0]</td>
              <td>$row2[1]</td>
              <td>$row2[3]</td>
              <td>$row2[4]</td>";


}
$html .="</tr>";       
                
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