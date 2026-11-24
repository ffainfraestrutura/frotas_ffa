<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\";</script>";
} /*elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\'index.php\'</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

include'conecta.php';
include_once'./func/log.php';
header('Content-type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang='pt-br'>
    <head>
        <meta charset='utf-8' />
        <meta http-equiv='X-UA-Compatible' content='IE=edge' />
        <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no' />
        <meta name='description' content='FFA' />
        <meta name='author' content='FFA' />
        <link rel='icon' type='image/png' href='src/images/favicon.png'/>
        <title>Portal FFA</title>
        <link href='https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css' rel='stylesheet' />
        <link href='src/css/styles.css' rel='stylesheet' />
        <script src='https://use.fontawesome.com/releases/v6.1.0/js/all.js' crossorigin='anonymous'></script>
    </head>

    <body class='sb-nav-fixed vsc-initialized sb-sidenav-toggled'>
    	<!--nav-->
    	<?php include"menu-teste.php"; ?>

<div id='layoutSidenav_content'>
    <main>

<div class='container-fluid px-4'>
    <h1 class='h1 mt-2'> Tela Geral </h1>

        <div class='container-fluid mt-4' style='width: 80%;'>        
          <div class='row col-12'>

          <!-- div painel -->
          <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Painel
                </div>
                <div class='card-body' style='height: 167px;'>
                    <ul>
                        <li><a style='text-decoration: none;' href='./dashboard.php'> Início </a></li>
                <?php 
                    if($perfil=='4'){ print"
                        <li><a style='text-decoration: none;' href='./aprovacaocotafrotas.php'> Aprovar Cota Coordenador </a></li>
                        <li><a style='text-decoration: none;' href='./frotaorccoord.php'> Aprovar Orçamento Coordenador </a></li>
                        <li><a style='text-decoration: none;' href='./orcamento.php'> Orçamento </a></li>
                        <li><a style='text-decoration: none;' href='./cotadiaria.php'> Cota Diária </a></li>";}?>
                    </ul>
                </div>
              </div>
            </div>

        <?php if($perfil =='4'){print"
          <!-- div rotas -->
          <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Rotas
                </div>
                <div class='card-body' style='height: 167px;'>
                    <ul>
                        <li><a style='text-decoration: none;' href='./calculorotafrotas.php'> Calcular Rota </a></li>
                        <li><a style='text-decoration: none;' href='./atividadecomplementar.php'> Atividade Complementar </a></li>                        
                    </ul>
                </div>
              </div>
            </div>";}?>

            <!-- div relatórios-->
            <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Relatórios
                </div>
                <div class='card-body'>
                    <ul>
                        <li><a style='text-decoration: none;' href='./detalhes-kmsem.php'>KMs Semanais </a></li>
                        <li><a style='text-decoration: none;' href='./relsaldoatual.php'>Saldo Semanal </a></li>
                        <li><a style='text-decoration: none;' href='./detalhes-saldo-supxcoord.php'> Saldo Atual </a></li>
                        <li><a style='text-decoration: none;' href='./gpsxfolga.php'>Fora da Escala </a></li>
                        <li><a style='text-decoration: none;' href='./gpsxhora.php'> Deslocamento fora do horário </a></li>
                        <li><a style='text-decoration: none;' href='./detalhes-cota-extra-aprovadas.php'> Cotas extra aprovadas </a></li>
                        <li><a style='text-decoration: none;' href='./detalhes-cota-extra-indevidas.php'> Cotas extra indevidas </a></li>
                    </ul>
                </div>
              </div>
            </div>

            <!-- div chamados -->
            <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Chamados
                </div>
                <div class='card-body' style='height: 169px;'>
                    <ul>
                    <?php if($perfil=='4'){print"
                        <li><a style='text-decoration: none;' href='./chamados/listagemchamados.php'> Chamados Abertos </a></li>";}
                    ?>
                        <li><a style='text-decoration: none;' href='./chamados/relatoriochamados.php'> Relatório de Chamados </a></li>
                    </ul>
                </div>
              </div>
            </div>

          <!--div vistoria-->
            <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Vistoria
                </div>
                <div class='card-body' style='height: 167px;'>
                    <ul>
                    <?php if($perfil=='4'){print"
                        <li><a style='text-decoration: none;' href='./checklist/checklistinicio.php'> Iniciar uma vistoria </a></li>";}
                    ?>
                        <li><a style='text-decoration: none;' href='./checklist/gerarrelatorio.php'> Visualizar Relatórios </a></li>                    
                    </ul>
                </div>
              </div>
            </div>

            <!--div multas -->
            <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Multas
                </div>
                <div class='card-body' style='height: 167px;'>
                    <ul>
                        <li><a style='text-decoration: none;' href='../multas/testemultas2.php'> Multas Movida </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/cadastromulta.php'> Cadastro de multa avulsa </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/listagemmultas.php'> Listagem de multas avulsas </a></li>
                    </ul>
                </div>
              </div>
            </div>

            <div class='col-12 col-md-4'>
              <div class='card mb-4'>
                <div class='card-header'>
                  <i class='fas fa-table'></i>
                  Gestão
                </div>
                <div class='card-body' style='height: 169px;'>
                    <ul>
                        <li><a style='text-decoration: none;' href='./gestao/listagemfornecedores.php'> Fornecedores </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/listagemfiliais.php'> Filiais </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/listagemcondutores.php'> Condutores </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/listagemveiculos.php'> Veículos </a></li>
                        <li><a style='text-decoration: none;' href='./gestao/listagemmanutencao.php'> Manutenção </a></li>
                    </ul>
                </div>
              </div>
            </div>
            </div>
          </div>
    </div>
</main>

    <footer class='py-4 bg-light mt-auto'>
        <div class='container-fluid px-4'>
            <div class='d-flex align-items-center justify-content-between small'>
                <div class='text-muted'>Copyright &copy; FFA Infraestrutura</div>
                <!--<div>
                    <a href='#'>Privacy Policy</a>
                    &middot;
                    <a href='#'>Terms &amp; Conditions</a>
                </div>!-->
            </div>
        </div>
    </footer>
</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' crossorigin='anonymous'></script>
<script src='src/js/scripts.js'></script>
<!--<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.0/chart.min.js'></script>-->
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js' crossorigin='anonymous'></script>

<!--<script src='assets/demo/chart-area-demo.js'></script>
<script src='assets/demo/chart-bar-demo.js'></script>-->
<script src='src/js/simple-datatable.js' crossorigin='anonymous'></script>
<script src='src/js/datatables-simple-demo.js'></script>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js'></script>
    	
</body>

</html>