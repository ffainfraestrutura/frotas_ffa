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


$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : 'td';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : 'td';
$mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';
$cargofi = isset($_POST["cargo"]) ? $_POST["cargo"] : 'td';


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
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

    <main>
    <div class="container-fluid px-4">
        <h1 class="h1 mt-2"> Relatório Saldo Veiculos </h1>
<form action="./relsaldoveiculos.php" method="post">


     <div class="row">

     <div class="col-md-3">
   <label for="exampleInputDate1"><h5>Centro de Custo:</h5></label>
<select class="form-select mb-3" name="ccusto" id="ccusto">
<option value="td"> Todos </option>

<?php

$sql = "SELECT DISTINCT COALESCE(ccusto, 'Sem centro de custo') AS ccusto
FROM bdaniel.tbfuncionario where ccusto like '%IHS%' or ccusto like '%CLARO%' or ccusto like '%Alloha%' or ccusto is null ORDER BY ccusto;";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $ccusto = $row[0];
    print"<option value='$ccusto'>$ccusto</option>";

}


?>

</select>
    </div>     
    <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Estado:</h5></label>
<select class="form-select mb-3" name="filial" id="filial">
<option value="td"> Todos </option>
<?php

$sql = "SELECT DISTINCT uf_trabalho FROM bdaniel.tbfuncionario where status <> 'demitido' order by uf_trabalho";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $uf_trabalho = $row[0];
    print"<option value='$uf_trabalho'>$uf_trabalho</option>";

}


?>
</select>
    </div>

    <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Cargo:</h5></label>
<select class="form-select mb-3" name="cargo" id="cargo">
<option value="td"> Todos </option>
<?php

$sql = "SELECT DISTINCT cargo
FROM bdaniel.tbfuncionario
WHERE status <> 'demitido'
  AND cargo <> ''
  AND cargo NOT REGEXP '^[0-9]+$'
ORDER BY cargo;
";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $cargo = $row[0];
    print"<option value='$cargo'>$cargo</option>";

}


?>
</select>
    </div>

<div class="col-md-3">
            <label for="matricula"><h5>Matrícula:</h5></label>
            <input type="text" class="form-control mb-3" name="mattec" placeholder="Digite a matrícula">
        </div>


</div>


<div class='mt-2'>
   

        <div class="mt-4">
            <input type="submit" class="btn btn-success"  value="Filtrar">
        </div>


        <!--</form>
    </div>-->
</div>
      </form>      

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
                            <th>Placa</th>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Supervisor</th>
                            <th>Cargo</th>
                            <th>Centro de Custo</th>
                            <th>Limite</th>
                            <th>Saldo</th>
                            <th>Total Extra</th>
                            <th>Tipo de Ação:</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php     

if ($ccustofi <> 'td') {
    $ccustosql = " and f.ccusto = '$ccustofi'";
} 
if ($filialfi <> 'td') {
    $filialsql = " and f.uf_trabalho = '$filialfi'";
} 
if ($mattecfi <> '') {
    $mattecsql = " and f.matricula = '$mattecfi'";
} 
if ($cargofi <> 'td') {
    $cargosql = " and f.cargo = '$cargofi'";
} 

if ($ccustofi == 'td' and $filialfi == 'td' and $mattecfi == '' and $cargofi == 'td') {
     $sql = "SELECT LIMIT 0; 
";
} else {
         $sql = "SELECT 
    v.placa,
    COALESCE(f.matricula, '') AS matricula,
    COALESCE(f.nome, '') AS nome,
    COALESCE(sf.nome, '') AS supervisor_nome,
    COALESCE(f.ccusto, '') AS ccusto,
    COALESCE(f.cargo, '') AS cargo,
    v.unidade,
    COALESCE(sa.totalextra, 0) AS totalextra

FROM bdfrota.tbveiculo v
JOIN bdfrota.tbastatusvel a 
    ON v.statusvel = a.idtbastatusvel
LEFT JOIN bdfrota.tbsaldo sa
    ON sa.matricula = v.matcond
       AND sa.data = '$segunda'
LEFT JOIN bdaniel.tbfuncionario f 
    ON v.matcond = f.matricula
LEFT JOIN bdfrota.tbusuario u
    ON f.matricula = u.matricula 
       AND (u.idtbsupervisor IS NOT NULL AND u.idtbsupervisor <> 0)
LEFT JOIN bdfrota.tbsupervisor s
    ON u.idtbsupervisor = s.idtbsupervisor
LEFT JOIN bdaniel.tbfuncionario sf
    ON s.matricula = sf.matricula
WHERE a.statusgeral = 1 
  AND f.status <> 'demitido'
  AND a.status NOT IN ('INDISPONÍVEL', 'SINISTRO/MANUTENÇÃO', 'EM DESMOBILIZAÇÃO', 'RESERVADO: PROJETO') $ccustosql $filialsql $mattecsql $cargosql; 
";
}
//echo "$sql";
  
      
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){

                        $placa = $row[0];
                        $matriculatec = $row[1];
                        $nometec = $row[2];
                        $supervisor_nome = $row[3];
                        $ccusto = $row[4];
                        $cargo = $row[5];
                        $unidade = $row[6];
                        $totalextra = $row[7];



                        if ($unidade == 'SP'){//pesquisar na valecard
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_POST, TRUE);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
                            ));
                            curl_setopt($curl, CURLOPT_HEADER, 0);
                            $body = '{
                                "contratoCliente" : "524073",
                                "numeroCartao" : null,
                                "placa" : "'.$placa.'"
                            }';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);                                            

                            $result = json_decode(curl_exec($curl));                                          
                            
                            foreach ($result as $itens) {
                                
                                $statusCartao = $itens->statusCartao;

                                if ($statusCartao == 'A') {
                                    $saldocartao = $itens->saldo;
                                    $saldocartao = str_replace(".", ",", $saldocartao);
                                    $limiteAtual = $itens->limite;
                                    $limiteAtual = str_replace(".", ",", $limiteAtual);
                                   
                                }

                                } if ($saldocartao == '') {
                                    $saldocartao = 'Não encontrado';
                                    $limiteAtual = 'Não encontrado';
                                }

                            

                        }else{

                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_POST, TRUE);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'Accept-Charset: utf-8',
                                'Accept-Encoding: gzip',
                                'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                            ));
                            curl_setopt($curl, CURLOPT_HEADER, 0);
                            $body = '{
                            "codigoCliente": 183062,
                            "codigoProduto": 4,
                            "numeroCartao": null,
                            "especieCartao": null,
                            "numeroMatricula": null,
                            "nomeCompleto": null,
                            "placaVeiculo": "'.$placa.'",
                            "numeroFrota": null,
                            "codigoTipoFrota": null,
                            "codigoFamiliaVeiculo": null,
                            "codigoVeiculoModelo": null,
                            "codigoCidade": null,
                            "codigoClienteTCVeiculoResponsavel": null,
                            "nomeResponsavel": null,
                            "codigoGrupo": 4,
                            "codigoTipoCombustivel": null,
                            "informacoesAdicionais": [
                                {
                                "labelIA": null,
                                "codigoIA": null,
                                "descricaoIA": null
                                }
                            ],
                            "situacaoCartao": "T",
                            "ordem": "C",
                            "quebra": null,
                            "tipoMotorista": null,
                            "ativacao": null,
                            "tipoCartao": null,
                            "controlaHodometro": null,
                            "controlaHorimetro": null,
                            "situacaoVeiculo": null,
                            "trackOnLine": null
                            }';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                            $result = json_decode(curl_exec($curl));

                            if (count($result->itens)) {

                                foreach ($result->itens as $itens) {
                                    
                                    $situacao = $itens->situacao;

                                if ($situacao == 'A') {

                                    $saldocartao = $itens->saldo;
                                    $saldocartao = str_replace(".", ",", $saldocartao);
                                    $limiteAtual = $itens->limiteAtual;
                                    $limiteAtual = str_replace(".", ",", $limiteAtual);
                                                                      
                                }

                                } if ($saldocartao == '') {
                                    $saldocartao = 'Não encontrado';
                                    $limiteAtual = 'Não encontrado';
                                }
                            }
                        } 



        print "<tr>
                            <td>$placa</td>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$supervisor_nome</td>
                            <td>$cargo</td>
                            <td>$ccusto</td>
                            <td>$limiteAtual</td>
                            <td>$saldocartao</td>
                            <td>$totalextra</td>
                            <td><div class='h-25'>
                                                <form action='./control/remanejamentofrota.php' method='post'>
                                                <div class=''>
                                                    <input type='hidden' name='matriculatec' value='$matriculatec'> 
                                                    <input type='hidden' name='saldoatual' value='$saldocartao'>
                                                    <input type='hidden' name='saldosup' value='$saldosup'>
                                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                    <input type='hidden' name='unidade' value='$unidade'>
                                                    <input type='hidden' name='placa' value='$placa'>
                                                    <input type='hidden' name='idtbsupervisor' value='$idtbsupervisor'>
                                                    <input type='hidden' name='numeroCartao' value='$numeroCartao'>

                                                    <div>
                                                        <input type='radio' name='tipoacao' value='1' required>
                                                        <label for='adic'>Adicionar Saldo</label>
                                                        
                                                        <input type='radio'  name='tipoacao' value='0' class='ms-2'>
                                                        <label for='remov'>Remover Saldo</label><br>
                                                    </div>
                                                    
                                                    <div class='d-flex mt-1'>
                                                        <input type='text' class='form-control numerico' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor'  $d required>
                                                        
                                                        <button class='btn  btn-outline-success' type='submit' id='button-addon2' $d><span class='material-symbols-outlined'>></span></button>
                                                        
                                                    </div>
                                                </div>   
                                                </form>
                                                </div></td>

               </tr>";



      }

      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexceltoaqui.php" method="post">
              
                <input type="hidden" name="datai" value="<?php echo "$dataInicialfi"; ?>">
                <input type="hidden" name="dataf" value="<?php echo "$dataFinalfi"; ?>">
                <input type="hidden" name="ccusto" value="<?php echo "$ccustofi"; ?>">
                <input type="hidden" name="filial" value="<?php echo "$filialfi"; ?>">
                <input type="hidden" name="empresa" value="<?php echo "$empresafi"; ?>">
                <input type="hidden" name="coordenador" value="<?php echo "$coordenadorfi"; ?>">
                <input type="hidden" name="matsupf" value="<?php echo "$matsupfi"; ?>">
                <input type="hidden" name="mattecf" value="<?php echo "$mattecfi"; ?>">
                
              
              <div class="form-group col-md-2">
                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
              </div></form>
            </div>
        </div>
    </div>

</main>

<footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
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