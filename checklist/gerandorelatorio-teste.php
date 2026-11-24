<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
/*$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;*/

$data = date('d/m/Y h:i:s');

$placaveic = $_POST['placa'];
if(strpos($placaveic, "-") !== false){
  $placacorreta = str_replace("-", "", $placaveic);
} else {
  $placacorreta = str_replace(" ", "", $placaveic);
}
include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="src/images/favicon.png"/>

  <title> Relatório de Vistoria </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- Vendor CSS
  <link href="src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">-->

  <!-- Bootstrap core CSS 
  <link href="src/index_files/bootstrap.css" rel="stylesheet">-->

  <!-- Custom styles for this template -->
  <!--<script src="src/index_files/ie-emulation-modes-warning.js.download"></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    @media print{
      *{
        margin: 0;
        padding: 0;
        /*width: 2480px;
        height: 3508px;*/
      }

      p{
        font-size: 12px;
        margin-bottom: 0;
      }

      h1{
        font-size: 14px;
      }

      h6{
        font-size: 13px;
      }

      nav {
        display: none;
      }

      #noprint {
        display: none;
      }

      div#fotos>div>div>p{
        font-size: 12px;
      }

      #assinatura{
        width: 80%;
      }

      footer{
        /*display: none;*/
        color: black;
        background-color: white;
        text-align: center;
      }
    }
  </style>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php //include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-3 pb-2 col-12 text-center"> Relatório de Vistoria </h1>
        <h2 class="h2 pb-2 col-12 text-center"> Checklist do Veículo </h2>

        <!-- div tabela informações do condutor-->
        <div>
        <?php
        //print $placaveic;
        //print $placacorreta;
        //PEGANDO INFORMAÇÕES EM OUTRAS TABELAS
        //$sql1 = "SELECT a.nome, a.matricula, b.id_colaborador, a.cpf, c.numcnh, c.categoria, max(c.validade) as validade FROM bdaniel.tbfuncionario a, bdfrota.tbcarrosxcolaborador b, bdfrota.tbcnh c WHERE b.ds_placa= '$placacorreta' AND a.matricula = b.id_colaborador;";

        //PEGANDO INFORMAÇÕES NA BDFROTA.TBVISTORIA
        $sql1 = "SELECT nome, matricula, cpf, cnh, categoriacnh, validadecnh FROM bdfrota.tbvistoria WHERE placa= '$placacorreta';";
        
        $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

        while($row = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
          $nome = $row['nome'];
          $matricula = $row['matricula'];
          $cpf = $row['cpf'];
          $cnh = $row['cnh'];
          $categoriacnh = $row['categoriacnh'];
          $validadecnh = $row['validadecnh'];

          $validadecnh1 = explode(" ",$validadecnh);
          $validadecnh2 = explode("-",$validadecnh1[0]);
          $validadecnhreal = $validadecnh2[2] . "/" .$validadecnh2[1] . "/" .$validadecnh2[0];
        }

        print "
          <input type='hidden' value='$placacorreta'>
            <!--div informações do condutor-->
            <div class='border-top border-dark pt-2 pb-3'>
              <h6> Informações do condutor </h6>

              <table class='table table-bordered border-dark'>
                <tr>
                  <th> Nome: </th>
                  <td> $nome </td>
                  <th> CNH: </th>
                  <td> $cnh </td>
                </tr>

                <tr>
                  <th> Matrícula: </th>
                  <td> $matricula </td>
                  <th> CNH categoria: </th>
                  <td> $categoriacnh </td>
                </tr>

                <tr>
                  <th> CPF: </th>
                  <td> $cpf </td>
                  <th> CNH vencimento: </th>
                  <td> $validadecnhreal </td>
                </tr>
              </table>
            </div>";
        ?>

        <?php
        //PEGANDO INFORMAÇÕES EM OUTRAS TABELAS
        //$sql2 = "SELECT ds_placa, ds_modelo, nu_ano_fabricacao, cd_centro_custo FROM bdfrota.tbcarrosxcolaborador WHERE ds_placa='$placacorreta';";

        $sql2 = "SELECT placa, modelo, anofabricacao, unidade, centrocusto FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
        $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

        while($row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
          $placa = $row['placa'];
          $modelo = $row['modelo'];
          $anofabricacao = $row['anofabricacao'];
          $unidade = utf8_encode($row['unidade']);
          $centrocusto = $row['centrocusto'];
        }

        print "
              <!--div informações do veículo -->
              <div class='border-top border-dark pt-2 pb-3'>
                <h6> Informações do veículo </h6>

                <table class='table table-bordered border-dark'>
                  <tr>
                    <th> Placa: </th>
                    <td> $placa </td>
                    <th> Unidade: </th>
                    <td> $unidade </td>
                  </tr>

                  <tr>
                    <th> Modelo: </th>
                    <td> $modelo </td>
                    <th> Centro Custo: </th>
                    <td> $centrocusto </td>
                  </tr>

                  <tr>
                    <th> Ano: </th>
                    <td> $anofabricacao </td>
                    <th> </th>
                    <td> </td>
                  </tr>
                </table>
              </div>";
        ?>

        <?php
        $sql3 = "SELECT tipo, vistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

        while($row = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
          $tipo = utf8_encode($row['tipo']);
          $vistoriador = utf8_encode($row['vistoriador']);
          $datavistoria = $row['datavistoria'];
          $estado = utf8_encode($row['estado']);
          $avaria = $row['avaria'];
          $hodometro = $row['hodometro'];
          $niveltanque = $row['niveltanque'];
          $observacao = utf8_encode($row['observacao']);

          $datavistoria1 = explode("-",$datavistoria);
          $datavistoriareal = $datavistoria1[2] . "/" .$datavistoria1[1] . "/" .$datavistoria1[0];  
        }

        print "
              <!--div informações da vistoriador -->
              <div class='border-top border-dark pt-2 pb-3'>
                <h6> Informações da vistoria </h6>

                <table class='table table-bordered border-dark'>
                  <tr>
                    <th> Tipo: </th>
                    <td colspan='3'> $tipo </td>
                  </tr>

                  <tr>
                    <th> Realizada por: </th>
                    <td> $vistoriador </td>
                    <th> Data: </th>
                    <td> $datavistoriareal </td>
                  </tr>

                  <tr>
                    <th> Estado Geral: </th>
                    <td> $estado </td>
                    <th> Possui avaria: </th>
                    <td> $avaria </td>
                  </tr>

                  <tr>
                    <th> Hodômetro: </th>
                    <td> $hodometro </td>
                    <th> Nível do tanque: </th>
                    <td> $niveltanque </td>
                  </tr>

                  <tr>
                    <th> Observação: </th>
                    <td colspan='3'> $observacao </td>
                  </tr>
                </table>
              </div>";
            ?>

            <?php
        $sql4 = "SELECT documentacao, drenagem, consplaca, alarme, conscabine, luzespainel, combustivel, arrefecimento, rodoar, mtcxdf, eletrica, baloesdear, macaco, alternador, carroceria, pneus, freios, niveloleo, pressaooleo, pressaopneu, direcao, tacografo FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
        $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

        while($row = mysqli_fetch_array($resultado4, MYSQLI_ASSOC)){
          $documentacao = $row['documentacao'];
          $drenagem = $row['drenagem'];
          $consplaca = $row['consplaca'];
          $alarme = $row['alarme'];
          $conscabine = $row['conscabine'];
          $luzespainel = $row['luzespainel'];
          $combustivel = $row['combustivel'];
          $arrefecimento = $row['arrefecimento'];
          $rodoar = $row['rodoar'];
          $mtcxdf = $row['mtcxdf'];
          $eletrica = $row['eletrica'];
          $baloesdear = $row['baloesdear'];
          $macaco = $row['macaco'];
          $alternador = $row['alternador'];
          $carroceria = $row['carroceria'];
          $pneus = $row['pneus'];
          $freios = $row['freios'];
          $niveloleo = $row['niveloleo'];
          $pressaooleo = $row['pressaooleo'];
          $pressaopneu = $row['pressaopneu'];
          $direcao = $row['direcao'];
          $tacografo = $row['tacografo'];
        }

        print "
              <!--div itens personalizados do checklist -->
              <div class='border-top border-dark pt-2 pb-3'>
                <h6> Itens personalizados da vistoria </h6>

                <table class='table table-bordered border-dark'>
                  <tr>
                    <th> Item: </th>
                    <th> Estado: </th>
                    <th> Item: </th>
                    <th> Estado: </th>                    
                  </tr>

                  <tr>
                    <td> Documentação do veículo </td>
                    <td> $documentacao </td>
                    <td> Balões de ar </td>
                    <td> $baloesdear </td>
                  </tr>

                  <tr>
                    <td> Drenagem do filtro separador de água </td>
                    <td> $drenagem </td>
                    <td> Macaco, chave de roda, triângulo e extintor </td>
                    <td> $macaco </td>
                  </tr>

                  <tr>
                    <td> Estado de conservação da placa </td>
                    <td> $consplaca </td>
                    <td> Luz indicadora do alternador </td>
                    <td> $alternador </td>
                  </tr>

                  <tr>
                    <td> Alarme sonoro </td>
                    <td> $alarme </td>
                    <td> Estado de conservação da carroceria </td>
                    <td> $carroceria </td>
                  </tr>

                  <tr>
                    <td> Estado de conservação da cabine </td>
                    <td> $conscabine </td>
                    <td> Estado geral dos pneus </td>
                    <td> $pneus </td>
                  </tr>

                  <tr>
                    <td> Estado de funciionamento das luzes do painel </td>
                    <td> $luzespainel </td>
                    <td> Estado de funciionamento do motor, caixa de mudanças e freio </td>
                    <td> $mtcxdf </td>
                  </tr>

                  <tr>
                    <td> Nível de combustível </td>
                    <td> $combustivel </td>
                    <td> Nível do óleo motor </td>
                    <td> $niveloleo </td>
                  </tr>

                  <tr>
                    <td> Sistema de arrefecimento </td>
                    <td> $arrefecimento </td>
                    <td> Pressão do óleo motor </td>
                    <td> $pressaooleo </td>
                  </tr>

                  <tr>
                    <td> Pressão do sistema pneumático, indicador rodoar </td>
                    <td> $rodoar </td>
                    <td> Pressão dos pneus </td>
                    <td> $pressaopneu </td>
                  </tr>

                  <tr>
                    <td> Sistema de direção </td>
                    <td> $direcao </td>
                    <td> Sistema elétrico </td>
                    <td> $eletrica </td>
                  </tr>

                  <tr>
                    <td> Tacógrafo </td>
                    <td> $tacografo </td>
                    <td>  </td>
                    <td>  </td>
                  </tr>

                </table>
              </div>";
            ?>

          <!-- div termo de concordância -->
          <div class="mt-4 pt-2 col-12 border-top border-dark">
            <p class="fw-bolder"> Ciente que: </p>
            <p> No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos. </p>
            <p> Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas. </p>
          </div>

          <!-- div fotos -->
          <div class="mt-4 pt-2 col-12 border-top border-dark" id='fotos'>
          <?php
          $sql5 = "SELECT placa, frontal, traseira, direita, esquerda, bateria, selfie, gps, extra1, extra2, extra3, extra4, extra5 FROM bdfrota.tbvistoriafotos WHERE placa = '$placacorreta';";

          $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

          while($row = mysqli_fetch_array($resultado5, MYSQLI_ASSOC)){
            $placa = $row['placa'];
            $frontal = $row['frontal'];
            $traseira = $row['traseira'];
            $direita = $row['direita'];
            $esquerda = $row['esquerda'];
            $bateria = $row['bateria'];
            $selfie = $row['selfie'];
            $gps = $row['gps'];
            $extra1 = $row['extra1'];
            $extra2 = $row['extra2'];
            $extra3 = $row['extra3'];
            $extra4 = $row['extra4'];
            $extra5 = $row['extra5'];
          }

          print "
            <h6> Fotos da vistoria: </h6>

            <!-- div linha 1 de fotos -->
            <div class='col-12 d-flex justify-content-start'>
              <!-- div foto frontal-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> FRONTAL </p>
                <img style='width: 90%;' src='.$frontal' alt='foto frontal'>
              </div>

              <!-- div foto traseira-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> TRASEIRA </p>
                <img style='width: 90%;' src='.$traseira' alt='foto traseira'>
              </div>

              <!-- div foto lateral direita-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> LATERAL DIREITA </p>
                <img style='width: 90%;' src='.$direita' alt='foto lateral direita'>
              </div>

              <!-- div foto lateral esquerda-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> LATERAL ESQUERDA </p>
                <img style='width: 90%;' src='.$esquerda' alt='foto lateral esuerda'>
              </div>
            </div>

            <!-- div linha 2 de fotos -->
            <div class='col-12 mt-2 d-flex justify-content-start'>
              <!-- div foto bateria-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> BATERIA </p>
                <img style='width: 90%;' src='.$bateria' alt='foto da bateria'>
              </div>

              <!-- div foto selfie-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> SELFIE VISTORIADOR </p>
                <img style='width: 90%;' src='.$selfie' alt='selfie do vistoriador'>
              </div>

              <!-- div foto gps-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> GPS </p>
                <img style='width: 90%;' src='.$gps' alt='foto do gps'>
              </div>

              <!-- div foto extra1 -->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> EXTRA 1 </p>
                <img style='width: 90%;' src='.$extra1' alt='foto extra1'>
              </div>
            </div>

            <!-- div linha 3 de fotos -->
            <div class='col-12 mt-2 d-flex justify-content-start'>
              <!-- div foto extra2 -->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> EXTRA 2 </p>
                <img style='width: 90%;' src='.$extra2' alt='foto extra2'>
              </div>

              <!-- div foto extra3 -->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> EXTRA 3 </p>
                <img style='width: 90%;' src='.$extra3' alt='selfie extra3'>
              </div>

              <!-- div foto extra4-->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> EXTRA 4 </p>
                <img style='width: 90%;' src='.$extra4' alt='foto extra4'>
              </div>

              <!-- div foto extra5 -->
              <div class='col-3'>
                <p class='fw-bolder mb-0'> EXTRA 5 </p>
                <img style='width: 90%;' src='.$extra5' alt='foto extra5'>
              </div>
            </div>";
          ?>
          </div>

          <!-- div assinatura -->
          <div class="mt-5 pt-5 col-12">
          <?php
          print"  
            <p class='col-6 border-top border-dark m-auto text-center mb-3' id='assinatura'> Assinatura condutor - $nome  </p>
            <p class='mb-0'> Atendido por: </p>
            <p> Nome: <span class='border-bottom border-dark'> $vistoriador</span>. Matrícula: <span class='border-bottom border-dark'> $vistoriador</span>. </p>
            ";
          ?>
            
          </div>

          <!-- div botão imprimir -->
          <div class="mt-3 pt-3 col-12 text-center border-top border-dark" id="noprint">
            <button class="col-3 btn btn-success" onclick="window.print()"> Imprimir relatório </button>
            <?php print"<form method='post' action='./fpdf/relatorio.php' target='_blank'>
              <input type='hidden' id='placa' name='placa' value='$placacorreta'/>
              <button class='btn btn-success' type='submit'> Imprimir relatório</button>
              </form>"; ?>
          </div>
          
        </div>   
      </div>
    </main>

    <footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="flex-column align-items-center justify-content-start small">
          <?php  
            //print "<p class='text-muted mb-0'> Checklist impresso em $data. </p>";
          ?>
          <p class="text-muted mb-0">Copyright &copy; FFA Infraestrutura - Todos direitos reservados. </p>
        </div>
      </div>
    </footer>
  </div>
  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>-->
  

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function () {
      //$('#tabela1').DataTable();

      //mascara de placa
      $('#placa').mask('AAA0U00', {
        translation: {
            'A': {
                pattern: /[A-Za-z]/
            },
            'U': {
                pattern: /[A-Za-z0-9]/
            },
        },
        onKeyPress: function (value, e, field, options) {
            // Convert to uppercase
            e.currentTarget.value = value.toUpperCase();

            // Get only valid characters
            let val = value.replace(/[^\w]/g, '');

            // Detect plate format
            let isNumeric = !isNaN(parseFloat(val[4])) && isFinite(val[4]);
            let mask = 'AAA 0U00';
            if(val.length > 4 && isNumeric) {
                mask = 'AAA-0000';
            }
            $(field).mask(mask, options);
        }
      });
    });

    /*function imprimir(){

    }*/
  </script>

</body>
</html>