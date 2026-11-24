<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$inicioPost = isset($_POST["inicio"]) ? trim($_POST["inicio"]) : '';
$fimPost = isset($_POST["fim"]) ? trim($_POST["fim"]) : '';
$matsup = isset($_POST["matsupf"]) ? $_POST["matsupf"] : '';

$hoje = time();
$diaSemana = date('N', $hoje);
$inicioSemana = date('Y-m-d', strtotime('-' . ($diaSemana - 1) . ' days', $hoje));
$fimSemana = date('Y-m-d', strtotime('+' . (7 - $diaSemana) . ' days', $hoje));

function dataFiltroValida($data) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) && strtotime($data) !== false;
}

if (dataFiltroValida($inicioPost) && dataFiltroValida($fimPost)) {
    $inicio = $inicioPost;
    $fim = $fimPost;
} else {
    $inicio = $inicioSemana;
    $fim = $fimSemana;
}

if (strtotime($inicio) > strtotime($fim)) {
    $dataAuxiliar = $inicio;
    $inicio = $fim;
    $fim = $dataAuxiliar;
}

$inicioExibicao = date('d/m/Y', strtotime($inicio));
$fimExibicao = date('d/m/Y', strtotime($fim));
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
        <title>Cotas Extra Indevidas Aceitas | Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"./menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Cotas Extra Indevidas Aceitas </h1>
        <form action='./detalhes-cota-extra-descontadaac.php' method='post' autocomplete='off'>
        <div class="row mb-3 pb-3 justify-content-start border-bottom align-items-center">
            <h5>Selecione o período</h5>
        <!-- Depois procurar com que a pessoa escreva o endereço pelo cep -->
            <!--input data-->
            <div class="ms-0 mb-1 me-1 d-flex col-sm-2 input-group-sm" style='height: 100%;'>
                <label class="fw-bolder form-label mt-2" for="data">Inicio: </label>
                <input class="ms-1 form-control" style= "width: 200px;" type="date" name="inicio" id="inicio" value="<?php echo $inicio; ?>" required autocomplete="off">
            </div>
            
            <div class="ms-0 mb-1 me-1 d-flex col-sm-2 input-group-sm" style='height: 100%;'>
                <label class="fw-bolder form-label mt-2" for="data">Fim: </label>
                <input class="ms-1 form-control" style= "width: 200px;" type="date" name="fim" id="fim" value="<?php echo $fim; ?>" required autocomplete="off">
            </div>

            <!--div botão confirmar-->
            <div class='ms-2 col-sm-2'>
                <button class='btn btn-success mb-1' type='submit'> Confirmar </button>
            </div>
        </div>
    </form>

        <div class="alert alert-info" role="alert">
            <i class="fa-solid fa-filter"></i>
            Filtro aplicado: registros aceitos entre <?php echo $inicioExibicao; ?> e <?php echo $fimExibicao; ?>.
        </div>

        <div class="card mt-4 mb-4">
            <div class="card-header">
                <a href="./dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Voltar para Dashboard
                </a href="./dashboard.php">
            </div>
             <div class="card-body">
                <!--tabela para entrada de dados-->
                <table id='datatablesSimple' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Valor Pedido</th>
                            <th>Valor Descontado</th>
                            <th>Data</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php     

    $sql = "SELECT
                ce.matricula,
                ce.valdesc,
                ce.idpedidotec,
                ce.actec,
                ce.dataac,
                ped.valor AS valorpedido,
                carro.ds_placa AS placa,
                func.nome AS nometec,
                func.ccusto AS ccusto,
                (
                    SELECT sup.nome
                    FROM bdfrota.tbusuario sup
                    WHERE sup.idtbsupervisor = utec.idtbsupervisor
                    LIMIT 1
                ) AS nomesup
            FROM bdfrota.tbcotaextraindevida ce
            LEFT JOIN bdfrota.tbpedidostec ped ON ped.idtbpedidostec = ce.idpedidotec
            LEFT JOIN bdfrota.tbcarrosxcolaborador carro ON carro.id_colaborador = ce.matricula
                        LEFT JOIN bdaniel.tbfuncionario func ON func.matricula = ce.matricula
                        LEFT JOIN bdfrota.tbusuario utec ON utec.matricula = ce.matricula
                        WHERE ce.actec = 1
                            AND DATE(COALESCE(
                                        STR_TO_DATE(ce.dataac, '%Y-%m-%d %H:%i:%s'),
                                        STR_TO_DATE(ce.dataac, '%d/%m/%Y %H:%i:%s')
                                    )) BETWEEN '$inicio' AND '$fim'
                        ORDER BY DATE(COALESCE(
                                        STR_TO_DATE(ce.dataac, '%Y-%m-%d %H:%i:%s'),
                                        STR_TO_DATE(ce.dataac, '%d/%m/%Y %H:%i:%s')
                                    )) DESC";
        
      $resultado = mysql_query($sql) or die (mysql_error());
      
      while ($row = mysql_fetch_array($resultado)){

        $mattec = $row['matricula'];
        $valdesc = $row['valdesc'];
        $idpedidotec = $row['idpedidotec'];
        $valorpedido = $row['valorpedido'];
        $placa = $row['placa'];
        $nometec = $row['nometec'];
        $ccusto = utf8_encode($row['ccusto']);
        $nomesup = $row['nomesup'];
        $dataac = $row['dataac'];

        $dataaceitef = '';
        if (!empty($dataac)) {
            $dataaceiteObj = date_create($dataac);
            if ($dataaceiteObj) {
                $dataaceitef = date_format($dataaceiteObj, 'd/m/Y H:i:s');
            }
        }


        print "<tr>
                <td>$mattec</td>
                <td>$nometec</td>
                <td>$ccusto</td>
                <td>$placa</td>
                <td>$nomesup</td>
                <td>$valorpedido</td>
                <td>$valdesc</td>
                <td>$dataaceitef</td>
               </tr>";

        


      }

      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcel-detalhes-cota-extra-descontadaac.php" method="post">
              <?php 
              print "<input type='hidden' name='diai' value='$inicio'>";
              print "<input type='hidden' name='diaf' value='$fim'>";?>
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
            </div>
        </div>
    </div>

</main>

<footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
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
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>
</html>