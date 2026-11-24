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
                        <th>Matricula</th>
                        <th>Nome</th>
                        <th>Estado</th>
            </tr>";
                $sql = "SELECT a.matricula, b.nome, b.codempresa FROM bdindicador.tbusuariobonificacao a, bdaniel.tbfuncionario b where a.matricula = b.matricula and b.status <> 'Demitido' and a.assinatura = 0 and a.matricula in (SELECT distinct(matricula) FROM bdindicador.tbrv)";
                $resultado = mysql_query($sql) or die (mysql_error());
                while($row = mysql_fetch_array($resultado)){
                  
                    $matricula = $row[0];
                    $nome = $row[1];
                    $codestado = $row[2];
                    if ($codestado == 1) {
                      $estado = 'RJ';
                    } else {
                      $estado = 'SP';
                    }
                  $html .="<tr>
                        <td>$matricula</td>
                        <td>$nome</td>
                        <td>$estado</td>
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