<?php
session_start();
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
include("../conecta.php");

$dataInicialfi = isset($_POST["datai"]) ? $_POST["datai"] : '';
$dataFinalfi = isset($_POST["dataf"]) ? $_POST["dataf"] : '';
$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : '';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : '';
$empresafi = isset($_POST["empresa"]) ? $_POST["empresa"] : '';
$coordenadorfi = isset($_POST["coordenador"]) ? $_POST["coordenador"] : '';
$matsupfi = isset($_POST["matsupf"]) ? $_POST["matsupf"] : '';
$mattecfi = isset($_POST["mattecf"]) ? $_POST["mattecf"] : '';

//echo"$dataFinalfi - $dataInicialfi";

header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Relatório Tô Aqui!</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-To-Aqui.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Motivo</th>
                            <th>OBS</th>
                            <th>Data</th>
                            <th>Endereço</th>
            </tr>";

if ($ccustofi <> 'td') {
    $ccustosql = "and b.ccusto = '$ccustofi'";
} 
if ($filialfi <> 'td') {
    $filialsp = explode("-", $filialfi);
    $filialsql = "and b.codempresa = $filialsp[0] and b.codfilial = $filialsp[1]";
} 
if ($empresafi <> 'td') {
    $empresasql = "and b.ccusto like '%$empresafi%'";
} 
if ($coordenadorfi <> 'td' and $coordenadorfi <> '') {
    $coordenadorsql = "and a.idtbsupervisor in (SELECT idtbsupervisor FROM bdfrota.tbsupervisor where idtbcoordenador = '$coordenadorfi')";
} 
if ($matsupfi <> 'td' and $matsupfi <> '') {
    $supervisorsql = "and a.idtbsupervisor = '$matsupfi'";
} 
if ($mattecfi <> 'td' and $mattecfi <> '') {
    $tecnicosql = "and a.matricula = '$mattecfi'";
} 



       $sql = "SELECT a.matricula, b.nome, b.codempresa, b.ccusto, c.motivo, c.obs, TIME(c.hora) as hora, DATE(c.data) as data, c.endereco, c.aceite FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbtoaquisup c where a.matricula = b.matricula and a.matricula = c.matricula and c.matricula <> '' and c.data >= '$dataInicialfi' and c.data <= '$dataFinalfi'  $ccustosql $filialsql $empresasql $coordenadorsql $supervisorsql $tecnicosql order by c.hora";
      
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];
                        $ccusto = utf8_encode($row[3]);
                        $motivo = utf8_encode($row[4]);
                        $obs = $row[5];
                        $hora = $row[6];
                        $data = $row[7];
                        $endereco = $row[8];


      

        $html .="<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$ccusto</td>
                            <td>$motivo</td>
                            <td>$obs</td>
                            <td style=\"white-space: nowrap;\">$data $hora</td>
                            <td>$endereco</td>
                            <td>$aceitedesc</td>
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