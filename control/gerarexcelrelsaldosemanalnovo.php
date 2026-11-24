<?php
setlocale(LC_ALL, 'pt_BR.utf8');
setlocale(LC_TIME, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

include("../conecta.php");
$diai = isset($_POST["diai"]) ? $_POST["diai"] : '';



$datetime = new DateTime($diai);

// Define o dia da semana para domingo (0 é domingo, 1 é segunda-feira, e assim por diante)
$datetime->setISODate($datetime->format('Y'), $datetime->format('W'), 7);

// Formate a data para 'Y-m-d' (ou o formato desejado)
$diaf = $datetime->format('Y-m-d');
$datasem = new DateTime($diai);
if (strftime('%A') == "domingo" ){
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}
header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel
 *-->
 <!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Rel Saldo Semanal</title>
  <head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Relatorio-Saldo-Semanal.xls";


    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>";
    $datetimesem = new DateTime($diai);
                        
                        $nomesDiasSemana = array(
                            'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'
                        );

                        $html .= '<tr>';
                        $html .= '<th></th>';

                        for ($i = 0; $i < 7; $i++) {
                            $diaFormatado = $datetimesem->format('l, d/m/Y');
                            $nomeDia = $nomesDiasSemana[$datetimesem->format('w')];
                            $html = '<th>' . $nomeDia . ', ' . $datetimesem->format('d/m/Y') . '</th>';
                            $datetimesem->modify('+1 day');
                        }

                        $html .= '<th>Acumulado Semana</th>';
                        $html .= '<th></th>';
                        $html .= '</tr>';
                $html .= "<tr>
                            <td>UF</td>
                            <td>Matricula</td>
                            <td>Nome</td>
                            <td>Centro de Custo</td>
                            <td>Placa</td>
                            <td>Supervisor</td>
                            <td>Orçamento Semanal</td>
                            <td>KM Projetado</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Saldo Inicial</td>
                            <td>KM Proj</td>
                            <td>KM OS</td>
                            <td>Cota Extra Devida</td>
                            <td>Cota Extra Indevida</td>
                            <td>Valor Remanejado</td>
                            <td>Valor Gasto</td>
                            <td>Saldo Final</td>
                            <td>Projeção Semanal</td>
                            <td>Diferença</td>
                            <td>Variação</td>

                        </tr>";


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
    $html = $html;
    ?>
  </body>
</html>