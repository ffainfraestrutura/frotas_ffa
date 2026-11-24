<?php
session_start();
include("../conecta.php");
include("../conecta2.php");

$inicio = $_POST['inicio'];
$fim = $_POST['fim'];
$empresa = $_POST['empresa'];

header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Atividades Fora TOA</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Atividades-Fora-Toa.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                        <th> Data </th>
                        <th> Operador </th>
                        <th> Nome OP </th>
                        <th> Centro de Custo OP </th>
                        <th> Técnico </th>
                        <th> Motivo </th>
                        <th> Endereço </th>
                        <th> Hora </th>
            </tr>";


            if ($empresa == 'tim') {
                    $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrota WHERE data >= '$inicio' and data <= '$fim' and foratoa = 1 order by data, eta";
                } elseif ($empresa == 'claro') {
                    $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrotalatlong WHERE data >= '$inicio' and data <= '$fim' and foratoa = 1 order by data, eta";
                } else {
                    $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrota WHERE data >= '$inicio' and data <= '$fim' and foratoa = 1 order by data, eta";
                    $todas = 1;
                }
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                    $dataf = $row['data'];
                    $provedor = $row['provedor'];
                    $operador = $row['operador'];
                    $motivo = $row['motivo'];
                    $enderecof = $row['endereco'];
                    $eta = $row['eta'];

                    $dataf1 = explode(" ", $dataf);
                    $dataf2 = explode("-", $dataf1[0]);
                    $datafinal= $dataf2[2]."/".$dataf2[1]."/".$dataf2[0];

                    $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$operador'";
                    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                    $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);

                    $nomeop = $row2['nome'];
                    $ccusto = $row2['ccusto'];

                    $html .= "<tr>
                            <td>$datafinal</td>
                            
                            <td>$operador</td>
                            <td>$nomeop</td>
                            <td>$ccusto</td>
                            <td>$provedor</td>
                            <td>$motivo</td>
                            <td>$enderecof</td>
                            <td>$eta</td>
                        </tr>";
                } if ($todas == 1) {
                    $sql = "SELECT data, provedor, operador, motivo, endereco, eta FROM bdfrota.tbosrotalatlong WHERE data >= '$inicio' and data <= '$fim' and foratoa = 1 order by data, eta";
                    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                    $dataf = $row['data'];
                    $provedor = $row['provedor'];
                    $operador = $row['operador'];
                    $motivo = $row['motivo'];
                    $enderecof = $row['endereco'];
                    $eta = $row['eta'];

                    $dataf1 = explode(" ", $dataf);
                    $dataf2 = explode("-", $dataf1[0]);
                    $datafinal= $dataf2[2]."/".$dataf2[1]."/".$dataf2[0];

                    $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$operador'";
                    $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                    $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);

                    $nomeop = $row2['nome'];
                    $ccusto = $row2['ccusto'];

                    $html .= "<tr>
                            <td>$datafinal</td>
                            <td>$operador</td>
                            <td>$nomeop</td>
                            <td>$ccusto</td>
                            <td>$provedor</td>
                            <td>$motivo</td>
                            <td>$enderecof</td>
                            <td>$eta</td>
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
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>