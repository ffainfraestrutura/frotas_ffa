<?php
session_start();
include("../conecta.php");
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$perfil = $_SESSION['perfil'];
$_SESSION['perfil'] = $perfil;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$filtro = $_POST['filtro'];
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Pendente Assinatura</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Pendente-Assinatura.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                <th>Filial</th>
                <th>Segmento</th>
                <th>Nome</th>
                <th>Valor</th>
                <th>Elegível</th>
            </tr>";
                $sql = "SELECT matricula, gerente, supervisor, nome, valor_total, elegibilidade, tipo, acordo FROM bdindicador.tbrv $filtro group by matricula";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                  
                    $matricula = $row[0];
                    $gerente = $row[1];
                    $supervisor = $row[2];
                    $nome = $row[3];
                    $valor = $row[4];
                    $valor = str_replace(".", ",", $valor );
                    $eleg = $row[5];
                    $eleg = utf8_encode($eleg);
                    $tipo = $row[6];
                    $ger = explode(" ", $gerente);
                    if ($ger[0] == 'CESAR') {
                        $filial = 'RJ';
                    } elseif($ger[0] == 'RICARDO') {
                        $filial = 'SP';
                    }
                    $acordo = $row[7];
                    if ($tipo == 0) {
                      $tipon = 'Instalador';
                    } else {
                      $tipon = 'Reparador';
                    }
                  $html .="<tr>
                    <td>$filial</td>
                    <td>$tipon</td>
                    <td>$nome</td> 
                    <td>R$ $valor</td>
                    <td>$eleg</td>
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