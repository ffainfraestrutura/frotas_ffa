<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$idpedidotec = $_POST['idpedidotec'];
$valdesc = $_POST['valdesc'];
$id = $_POST['id'];


include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        
        <link href="src/css/styles.css" rel="stylesheet" />
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <script type="text/javascript">
              
  $(document).ready(function () {
    setInterval(function(){
      var doc01 = document.getElementById("inputGroupFile04");

      if(doc01.value=="") {
        $("#inputGroupFileAddon04").addClass('d-none');
      }else{
        $("#inputGroupFileAddon04").removeClass('d-none');
      }
    });
  });



        </script>     
    </head>

    <body class="sb-nav-fixed vsc-initialized">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<div class='container-fluid px-4'><div class='card mb-4 mt-4 shadow-lg border-0 rounded-lg'><div class='card-body'>
    <h1>Política de Utilização da Frota de Veículos</h1>
    
    <h2>1. OBJETIVO</h2>
    <p>Estabelecer critérios e diretrizes a fim de disciplinar à utilização da frota de veículos por colaboradores da FFA Infraestrutura e Serviços e prestadores de serviços, visando assegurar a sua correta utilização.</p>
    
    <h2>2. CAMPO DE APLICAÇÃO</h2>
    <p>Essa política se aplica a todos os colaboradores e prestadores de serviço da FFA Infraestrutura e Serviços que utilizam a frota de veículos da empresa.</p>
    
    <h2>3. DEFINIÇÕES</h2>
    <p><strong>Frota de Veículos:</strong> Conjunto de veículos próprios e/ou contratados pela FFA Infraestrutura e Serviços, utilizados para atender às necessidades da empresa.</p>
    <p><strong>Veículos Locados:</strong> Veículos locados junto a empresas especializadas em locação de veículos.</p>
    <p><strong>Veículos Agregados:</strong> Veículos locados de pessoa física, normalmente o próprio colaborador, utilizado por este para execução de suas atividades laborais.</p>
    
    <h3>3.1. Símbolos e Abreviaturas</h3>
    <ul>
        <li><strong>CNH:</strong> Carteira Nacional de Habilitação</li>
        <li><strong>DETRAN:</strong> Departamento Estadual de Trânsito</li>
    </ul>
    
    <h2>4. FISCALIZAÇÃO</h2>
    <p>A área de Frota deve fiscalizar, aleatoriamente, o uso da frota de veículos da empresa, seja através de vistorias presenciais ou fazendo uso de relatórios e ferramentas de monitoramento remoto.</p>
    <p>Sempre que for constatada alguma irregularidade, a área de Frota notificará o gestor responsável para que sejam tomadas as medidas administrativas cabíveis.</p>
    
    <h2>5. NORMAS DE UTILIZAÇÃO DOS VEÍCULOS</h2>
    
    <h3>5.1. Uso Exclusivo em Serviço</h3>
    <p>Os veículos devem ser utilizados exclusivamente para o desempenho de atividades laborais, dentro dos horários e locais autorizados.</p>
    <p>É terminantemente proibido o uso dos veículos para fins pessoais ou quaisquer outras finalidades não autorizadas pela FFA Infraestrutura e Serviços.</p>
    <p>Qualquer desvio de uso será considerado falta gravíssima, acarretando medidas disciplinares cabíveis, incluindo demissão por justa causa para os casos mais graves.</p>
    
    <h3>5.2. Autorização para Conduzir Veículos</h3>
    <p>Apenas pessoas autorizadas pela área de Frota podem dirigir os veículos da frota da FFA Infraestrutura e Serviços. É proibido transportar pessoas que não tenham relação direta com a empresa ou com a atividade laboral executada.</p>
    <ul>
        <li>O colaborador deve possuir CNH válida e em conformidade com os requisitos de sua categoria.</li>
        <li>Não ter multas graves ou gravíssimas nos últimos 6 meses.</li>
        <li>Não possuir mais de 20 pontos acumulados na CNH nos últimos 12 meses.</li>
        <li>Estar aprovado em teste de direção prático, caso exigido pela área de Frota.</li>
    </ul>
    
    <h3>5.3. Comunicação de Danos</h3>
    <p>Qualquer dano no veículo deve ser comunicado imediatamente à área de Frota. Em caso de acidente, o condutor deve registrar um Boletim de Ocorrência e coletar todas as informações necessárias.</p>
    
    <h3>5.4. Apuração de Responsabilidade de Acidente</h3>
    <p>A responsabilidade pelo acidente será determinada por órgão oficial ou pela área de Frota, com base na análise dos fatos.</p>
    
    <h3>5.5. Multas de Trânsito</h3>
    <p>O condutor é responsável por qualquer infração de trânsito cometida durante o uso do veículo. A empresa se reserva o direito de descontar os custos de multas e taxas administrativas, se aplicável.</p>
    
    <h3>5.6. Verificação e Manutenção</h3>
    <p>O colaborador é responsável pelas verificações de manutenção de primeiro nível, como checagem de óleo, pneus, luzes e outros itens essenciais. Anomalias devem ser comunicadas imediatamente à área de Frota.</p>
    
    <h3>5.7. Direção Defensiva</h3>
    <p>Todos os condutores devem adotar práticas de direção defensiva e cumprir as normas de trânsito. É obrigatório o uso do cinto de segurança por todos os ocupantes do veículo.</p>
    
    <h3>5.8. Diretrizes de Velocidade</h3>
    <p>Os motoristas devem respeitar os limites de velocidade estabelecidos pelas autoridades de trânsito e seguir as normas locais.</p>
    
    <h3>5.9. Diretrizes de Utilização do Cartão de Combustível</h3>
    <p>O cartão de combustível deve ser usado exclusivamente para abastecer veículos da empresa. Qualquer uso indevido está sujeito a sanções disciplinares.</p>
    
<form method='post' id='deacordo' action='./control/memorando.php' enctype='multipart/form-data'>
<div class='input-group'>
    <label for="inputGroupFile04" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
  <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
  <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
</svg>  Assinar digitalmente
</label>
  <input type='file' class='form-control' id='inputGroupFile04' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='arquivo' style='display: none;'required>
  <?php

    print"<input type='hidden' name='idpedidotec' value='$idpedidotec'>
                            <input type='hidden' name='valdesc' value='$valdesc'>
                            <input type='hidden' name='matricula' value='$matricula'>";


  ?>
  <button class='btn btn-success' type='submit' id='inputGroupFileAddon04' style="margin-left: 25px;">Confirmar</button>

</form>

</div></div></div>
<div class='container-fluid px-0'><div class='card mb-4 mt-4 shadow-lg border-0 rounded-lg'><div class='card-body m-auto'>
<form method="post" action='./control/semacordo.php' style="text-align: center;">
    <button type="submit" class="btn btn-link d-flex align-items-center justify-content-center">
        <h4 class="text-black m-0 text-center">Não aceitar e voltar</h4>
    </button>
    <?php

    print"<input type='hidden' name='idpedidotec' value='$idpedidotec'>
                            <input type='hidden' name='valdesc' value='$valdesc'>
                            <input type='hidden' name='id' value='$id'>";


  ?>
</form>
</div></div></div>
</main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                            <!--<div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>!-->
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    </body>
</html>
