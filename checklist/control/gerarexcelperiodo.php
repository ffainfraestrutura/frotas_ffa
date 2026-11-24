<?php
include_once"../../func/log.php";
include_once"../../conecta.php";

$inicio=$_POST['inicio'];
$fim = $_POST['fim'];

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
    $arquivo = "gerarexcelperiodo-$inicio-$fim.xls";


    // Criamos uma tabela HTML com o formato da planilha       
    $html = "
    <table border='1' class='table table-bordered'>
        <tr>
            <th>Nome do Condutor</th>
            <th>Matricula do Condutor</th>
            <th>CPF do Condutor</th>
            <th>CNH do Condutor</th>
            <th>Categoria CNH do Condutor</th>
            <th>Validade CNH do Condutor</th>
            <th>Placa</th>
            <th>Modelo</th>
            <th>Ano de Fabricação</th>
            <th>Unidade</th>
            <th>Centro Custo</th>
            <th>Tipo de Vistoria</th>
            <th>Vistoriador</th>
            <th>Matrícula do Vistoriador</th>
            <th>Data da Vistoria</th>
            <th>Estado do Veículo</th>
            <th>Possui avaria?</th>
            <th>Hodômetro</th>
            <th>Nível Tanque</th>
            <th>Observação</th>            
            <th>Documentação do veículo</th>
            
            <th>Teto</th>
            <th>Parabrisa</th>
            <th>Capo</th>
            <th>Faraol Direito</th>
            <th>Farol Esquerdo</th>
            <th>Para-choque Dianteiro</th>
            <th>Paralama Lado Esquedo</th>
            <th>Retrovisor Lado Esquerdo</th>
            <th>Caixa de Ar Lado Esquerdo</th>
            <th>Porta Dianteira Esquerda</th>
            <th>Porta Traseira Esquerda</th>
            <th>Coluna do Teto Lado Esquerdo</th>
            <th>Lanterna Esquerda</th>
            <th>Lanterna Direita</th>
            <th>Tampa da Mala</th>
            <th>Para-choque Traseiro</th>
            <th>Kit Estepe</th>
            <th>Paralama Lado Direito</th>
            <th>Retrovisor Lado Direito</th>
            <th>Caixa de Ar Lado Direito</th>
            <th>Porta Dianteira Lado Direito</th>
            <th>Porta Traseira Lado Direito</th>
            <th>Coluna do Teto Lado Direito</th>
            <th>Painel</th>
            <th>Som</th>
            <th>Iluminação Interna</th>
            <th>Retrovisor Interno</th> 
            <th>Bancos</th>
            <th>Tapetes</th>
            <th>Tampa de Bagagito</th>
            <th>Calotas</th>
            <th>Bateria</th>
            <th>Safe Car</th>
            <th>Qual marcas dos pneus?</th>
            <th>Limpeza externa</th>
            <th>Limpeza interna</th>
            <th>Relatório assinado pelo condutor?</th>
            <th>Status do veículo</th>            
        </tr>";

    $sql = "SELECT DISTINCT * FROM bdfrota.tbvistoria WHERE datavistoria>='$inicio' AND datavistoria<='$fim' AND (idtbvistoria<2995 or  idtbvistoria>3015) AND idtbvistoria<>3028 AND idtbvistoria<>3031 AND idtbvistoria<>3032 AND placa<>'ABC1234'; ";
    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
    
    while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
        $nomec=utf8_encode($row['nome']);
        $matriculac=$row['matricula'];
        $cpfc=$row['cpf'];
        $cnhc=$row['cnh'];
        $categoriacnhc=$row['categoriacnh'];
        $validadecnhc=$row['validadecnh'];
        $placa=$row['placa'];
        $modelo=utf8_encode($row['modelo']);
        $anofabricacao=$row['anofabricacao'];
        $unidade=$row['unidade'];
        $centrocusto=utf8_encode($row['centrocusto']);
        $tipo=$row['tipo'];
        $vistoriador=utf8_encode($row['vistoriador']);
        $matrvistoriador=$row['matrvistoriador'];

        $datavistoria=$row['datavistoria'];
        $estado=strtoupper($row['estado']);
        $avaria=strtoupper($row['avaria']);
        $hodometro=$row['hodometro'];
        $documentacao=$row['documentacao'];
        $niveltanque=$row['niveltanque'];
        $observacao=utf8_encode($row['observacao']);

        $statusveic=$row['statusveic'];
        $assinado = $row['assinadocond'];

        $drenagem=strtoupper($row['drenagem']);
        $teto=strtoupper($row['teto']);
        $tetoesp=strtoupper($row['tetoesp']);
          $tetoesq=strtoupper($row['tetoesq']);
          $tetodir=strtoupper($row['tetodir']);
          $frente=strtoupper($row['frente']);
          $capo=strtoupper($row['capo']);
          $parabrisa=strtoupper($row['parabrisa']);
          $farolesq=strtoupper($row['farolesq']);
          $faroldir=strtoupper($row['faroldir']);
          $parachoque=strtoupper($row['parachoque']);
          $grade=strtoupper($row['grade']);
          $latesq=strtoupper($row['latesq']);
          $paralamaesq=strtoupper($row['paralamaesq']);
          $retrovesq=strtoupper($row['retrovesq']);
          $cxaresq=strtoupper($row['cxaresq']);
          $ptdiantesq=strtoupper($row['ptdiantesq']);
          $pttrasesq=strtoupper($row['pttrasesq']);
          $latdir=strtoupper($row['latdir']);
          $paralamadir= strtoupper($row['paralamadir']);
          $retrovdir = strtoupper($row['retrovdir']);
          $cxardir = strtoupper($row['cxardir']);
          $ptdiantdir = strtoupper($row['ptdiantdir']);
          $pttrasdir = strtoupper($row['pttrasdir']);
          $traseira = strtoupper($row['traseira']);
          $lantesq = strtoupper($row['lantesq']);
          $lantdir = strtoupper($row['lantdir']);
          $tmpmala = strtoupper($row['tmpmala']);
          $parachoquet = strtoupper($row['parachoquet']);
          $itinterno = strtoupper($row['itinterno']);
          $painel = strtoupper($row['painel']);
          $som = strtoupper($row['som']);
          $bancos = strtoupper($row['bancos']);
          $ilumint = strtoupper($row['ilumint']);
          $tmpbag = strtoupper($row['tmpbag']);
          $retrovint = strtoupper($row['retrovint']);
          $tapetes = strtoupper($row['tapetes']);
          $pneus = strtoupper($row['pneus']);
          $step = strtoupper($row['step']);
          $marcapneus = strtoupper($row['marcapneus']);
          $kitstep = strtoupper($row['kitstep']);
          $calotas = strtoupper($row['calotas']);
          $bateria = strtoupper($row['bateria']);
          $safecar = strtoupper($row['safecar']);
          $limpint = strtoupper($row['limpint']);
          $limpext = strtoupper($row['limpext']);

        if($avaria=='1'){
            $avaria= 'SIM';
        } elseif($avaria=='0'){
        $avaria= 'NÃO';
        }

        //tratando NAOOK
          if($frente == 'NAOOK'){
            $frente = 'NÃO OK';
          } 

          if($teto == 'NAOOK'){
            $teto = 'NÃO OK'; 
          } 

          if($latesq == 'NAOOK'){
            $latesq = 'NÃO OK';
          } 

          if($latdir == 'NAOOK'){
            $latdir = 'NÃO OK';
          } 

          if($traseira == 'NAOOK'){
            $traseira = 'NÃO OK';
          } 

          if($itinterno == 'NAOOK'){
            $itinterno = 'NÃO OK';
          } 

          if($painel == 'NAOOK'){
            $painel = 'NÃO OK';
          } 

          if($som == 'NAOOK'){
            $som = 'NÃO OK';
          } 

          if($bancos == 'NAOOK'){
            $bancos = 'NÃO OK';
          } 

          if($ilumint == 'NAOOK'){
            $ilumint = 'NÃO OK';
          } 

          if($pneus == 'NAOOK'){
            $pneus = 'NÃO OK';
          } 

          if($step == 'NAOOK'){
            $step = 'NÃO OK';
          } 

          if($kitstep == 'NAOOK'){
            $kitstep = 'NÃO OK';
          } 

          if($calotas == 'NAOOK'){
            $calotas = 'NÃO OK';
          }

        //retornando tipo de vistoria
        $sql1="SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist='$tipo' ";
        $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
        $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
            $tipo=utf8_encode($row1['tipo']);

        //tratando relatorio assinado
        if($assinado=='1'){
            $assinado= 'SIM';
        } elseif($assinado=='0'){
            $assinado= 'NÃO';
        }

        //retornando status do veículo
        $sql2="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusveic' ";
        $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
        $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
            $statusveic=utf8_encode($row2['status']);

        /*tratando o nivel do tanque
        $niveltanque1=explode("/", $niveltanque);
        $niveltanque = $niveltanque1[0]." de ".$niveltanque1[1];*/

        $html .="
        <tr>
            <td>$nomec</td>
            <td>'$matriculac</td>
            <td>$cpfc</td>
            <td>$cnhc</td>
            <td>$categoriacnhc</td>
            <td>$validadecnhc</td>
            <td>$placa</td>
            <td>$modelo</td>
            <td>$anofabricacao</td>
            <td>$unidade</td>
            <td>$centrocusto</td>
            <td>$tipo</td>
            <td>$vistoriador</td>
            <td>'$matrvistoriador</td>
            <td>$datavistoria</td>
            <td>$estado</td>
            <td>$avaria</td>
            <td>$hodometro</td>
            <td>'$niveltanque</td>
            <td>$observacao</td>            
            <td>$documentacao</td>
            
            <td>$teto</td>
            <td>$parabrisa</td>
            <td>$capo</td>
            <td>$faroldir</td>
            <td>$farolesq</td>
            <td>$parachoque</td>
            <td>$paralamaesq</td>
            <td>$retrovesq</td>
            <td>$cxaresq</td>
            <td>$ptdiantesq</td>
            <td>$pttrasesq</td>
            <td>$tetoesq</td>
            <td>$lantesq</td>
            <td>$lantdir</td>
            <td>$tmpmala</td>
            <td>$parachoquet</td>
            <td>$kitstep</td>
            <td>$paralamadir</td>
            <td>$retrovdir</td>
            <td>$cxardir</td>
            <td>$ptdiantdir</td>
            <td>$pttrasdir</td>
            <td>$tetodir</td>
            <td>$painel</td>
            <td>$som</td>
            <td>$ilumint</td>
            <td>$retrovint</td> 
            <td>$bancos</td>
            <td>$tapetes</td>
            <td>$tmpbag</td>
            <td>$calotas</td>
            <td>$bateria</td>
            <td>$safecar</td>
            <td>$marcapneus</td>
            <td>$limpext</td>
            <td>$limpint</td>
            <td>$assinado</td>
            <td>$statusveic</td>
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

