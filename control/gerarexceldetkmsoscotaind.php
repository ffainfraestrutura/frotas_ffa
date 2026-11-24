<?php
session_start();
include("../conecta.php");
$mattec = $_POST['mattec'];
$datai = $_POST["diai"];
$dataf = $_POST["diaf"];
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Detalhes Km Os</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Detalhes-kmsos.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                  <th>Matricula</th>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Dia</th>
                            <th>Hora</th>
                            <th>OS</th>
                            <th>Origem</th>
                            <th>KM</th>
            </tr>";

             $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
             
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];
// Verifica se o valor de $ccusto contém "TIM"
        if (strpos($ccusto, 'TIM') !== false) {
            $tabela = "bdfrota.tbosrota";
        }
        // Verifica se o valor de $ccusto contém "CLARO"
        elseif (strpos($ccusto, 'CLARO') !== false) {
            $tabela = "bdfrota.tbosrotalatlong";
        }elseif(strpos($ccusto, 'LIGGA') !== false) {
            $tabela = "bdfrota.tbosrota";
        }


        $sql = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa FROM $tabela where matricula = '$mattec' and data >= '$datai' and data <= '$dataf' group by data, eta order by data, eta";
        
      $resultado = mysql_query($sql) or die (mysql_error());

       while ($row = mysql_fetch_array($resultado)){

        $data = $row[3];
        $datac = explode(" ", $data);
        $datat = $datac[0];

        $endereco = $row[0];
        $bairro = $row[1];
        $cidade = $row[2];
        $eta = $row[4];
        $os = $row[5];
        if ($os == '') {
            $os = $row[7];
        }
        $kmrota = $row[6];
        if ($kmrota == '') {
            $kmrota = 0;
        }
        $foratoa = $row[8];
        if ($foratoa == 1) {
            $origem = 'MANUAL';
        } elseif ($os == 'CASA') {
            $origem = 'ANIEL';
        } else {
            $origem = 'TOA';
        }

        $endcomp = "$endereco $bairro $cidade";

if (mb_detect_encoding($endcomp, 'UTF-8', true)) {
    $endcomp = "$endereco $bairro $cidade";
} else {
    $endcomp = utf8_encode("$endereco $bairro $cidade");
}
        


        $kmsrodados = $row2[0];

    
                  $html .="<tr>
                <td>$mattec</td>
                <td>$nome</td>
                <td>$endcomp</td>
                 <td class='text-nowrap'>$datat</td>
                <td>$eta</td>
                <td>$os</td>
                <td>$origem</td>
                <td>$kmrota</td>
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