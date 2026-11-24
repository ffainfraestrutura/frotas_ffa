<?php
setlocale(LC_ALL, 'pt_BR.utf8');
setlocale(LC_TIME, 'pt_BR');
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

$diai = isset($_POST["diai"]) ? $_POST["diai"] : '';
$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : '';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : '';
$empresafi = isset($_POST["empresa"]) ? $_POST["empresa"] : '';
$coordenadorfi = isset($_POST["coordenador"]) ? $_POST["coordenador"] : '';
$matsupfi = isset($_POST["matsupf"]) ? $_POST["matsupf"] : '';
$mattecfi = isset($_POST["mattecf"]) ? $_POST["mattecf"] : '';


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
        <h1 class="h1 mt-2"> Relatório Saldo Semanal </h1>
<form action="./relsaldoatualnovo.php" method="post">
 <h5><b>Selecione o Periodo:</b></h5>

     <div class="row">

<div class="col-md-6">
      <label for="exampleInputEmail1"><h5>Semana:</h5></label>
      <select class="form-select mb-3" name="diai">
                <option value=" "> Selecione a data </option>

            <?php
            
            $hoje = time(); // Obtém a data e hora atual
            $dia_da_semana = date('w', $hoje); // Obtém o dia da semana (0 é domingo, 1 é segunda-feira, 2 é terça-feira, etc.)

            if ($dia_da_semana == 1) {
                $segunda_atual = $hoje; // Hoje é uma segunda-feira
            } else {
                $segunda_atual = strtotime('last Monday', $hoje); // Caso contrário, obtenha a última segunda-feira
            }

            $contador = 0;

            while ($contador < 4) {
                $dataval = date('Y-m-d', $segunda_atual);
                
                $dataFormatada = date('d/m/Y', strtotime($dataval));
                print"<option value='$dataval'>$dataFormatada</option>";

                $segunda_atual = strtotime('-7 days', $segunda_atual); // Subtrai 7 dias para a próxima segunda-feira
                $contador++;
            }
 
            ?>

            </select>
    </div>

     <div class="col-md-3">
   <label for="exampleInputDate1"><h5>Centro de Custo:</h5></label>
<select class="form-select mb-3" name="ccusto" id="ccusto">
<option value="td"> Todos </option>

<?php

$sql = "SELECT DISTINCT COALESCE(ccusto, 'Sem centro de custo') AS ccusto
FROM bdaniel.tbfuncionario where ccusto like '%TIM%' or ccusto like '%CLARO%' or ccusto like '%LIGGA%' or ccusto is null ORDER BY ccusto;";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $ccusto = utf8_encode($row[0]);
    print"<option value='$ccusto'>$ccusto</option>";

}


?>

</select>
    </div>     
    <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Filial:</h5></label>
<select class="form-select mb-3" name="filial" id="filial">
<option value="td"> Todos </option>
<?php

$sql = "SELECT codempresa, codfilial, nome FROM bdaniel.tbfilial order by codfilial, codempresa";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $codempresa = $row[0];
    $codfilial = $row[1];
    $nome = utf8_encode($row[2]);
    if ($nome <> 'FFA SP') {
        print"<option value='$codempresa-$codfilial'>$nome</option>";
    }
    

}


?>
</select>
    </div>
</div>

     <div class="row mt-2">


        <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Empresa:</h5></label>
<select class="form-select mb-3" name="empresa" id="empresa">
    <option value="td"> Todos </option>
    <?php

$sql = "SELECT distinct(nome) FROM bdaniel.tbaempresa ";
$resultado = mysql_query($sql) or die(mysql_error());
while($row = mysql_fetch_array($resultado)){
    $empresa = $row[0];
    print"<option value='$empresa'>$empresa</option>";
    
    

}


?>
</select>
    </div>

 <div class="col-md-3">
   <label for="exampleInputDate1"><h5>Coordenador:</h5></label>
<select class="form-select mb-3" name="coordenador" id="selectcoordenador">
                <option value="td"> Todos </option>

            <?php
            $i=0;

            $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula;";
            $resultado = mysql_query($sql) or die(mysql_error());
            while($row = mysql_fetch_array($resultado)){
                $idcoord = $row[0];
                $nomecoord = $row[1];

                $addmatriz = array($idcoord => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]' value='$idcoord'>$nomecoord</option>";
            }

            ?>

            </select>
</select>
    </div>     
    <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Supervisor:</h5></label>
<select class="form-select mb-3" name="matsupf" id="selectsupervisor">
               <option value="td"> Todos </option>

            <?php
            $i=0;
            $idselect;

            $sql2 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome, a.idtbsupervisor FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador;";
             
            $resultado2 = mysql_query($sql2) or die(mysql_error());
            while($row2 = mysql_fetch_array($resultado2)){
                $matsupf = $row2[0];
                $idcoord = $row2[1];
                $nomesup = $row2[2];
                $idtbsupervisor = $row2[3];

                $addmatriz = array($matsup => $i );
                foreach($addmatriz as $key => $value)
                {
                    $idselect[$key] = $value;
                }
                $i++;
                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]' value='$idtbsupervisor'>$nomesup</option>";
            }

            ?>

            </select>
</select>
    </div>
        <div class="col-md-3">
   <label for="exampleInputDate2"><h5>Técnico:</h5></label>
<select class="form-select mb-3" name="mattecf" id="selecttecnico">
                <option value="td"> Todos </option>

            <?php
            $i=0;

            $sql3 ="SELECT DISTINCT (a.matricula), b.nome, c.matricula, d.idtbcoordenador FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbsupervisor c, bdfrota.tbcoord d where a.matricula = b.matricula AND a.idtbsupervisor = c.idtbsupervisor AND c.idtbcoordenador = d.idtbcoordenador;";
             
            $resultado3 = mysql_query($sql3) or die(mysql_error());
            while($row3 = mysql_fetch_array($resultado3)){
                $mattecf = $row3[0];
                $nometec = $row3[1];
                $matsupf = $row3[2];
                $idcoord = $row3[3];
                
                print"<option id='$idselect[$idcoord]-$idselect[$matsupf]-$i' value='$mattecf'>$nometec </option>";
            }
            $i++;

            ?>

            </select>
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
<?php

                        
                        $datetimesem = new DateTime($diai);
                        
                        $nomesDiasSemana = array(
                            'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'
                        );

                        echo '<tr>';
                        echo '<th colspan="8"></th>';

                        for ($i = 0; $i < 7; $i++) {
                            $diaFormatado = $datetimesem->format('l, d/m/Y');
                            $nomeDia = $nomesDiasSemana[$datetimesem->format('w')];
                            echo '<th colspan="8">' . $nomeDia . ', ' . $datetimesem->format('d/m/Y') . '</th>';
                            $datetimesem->modify('+1 day');
                        }

                        echo '<th colspan="8">Acumulado Semana</th>';
                        echo '<th colspan="3"></th>';
                        echo '</tr>';

?>
                        <tr>
                            <th>UF</th>
                            <th>Matricula</th>
                            <th>Nome</th>
                            <th>Centro de Custo</th>
                            <th>Placa</th>
                            <th>Supervisor</th>
                            <th>Orçamento Semanal</th>
                            <th>KM Projetado</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Saldo Inicial</th>
                            <th>KM Proj</th>
                            <th>KM OS</th>
                            <th>Cota Extra Devida</th>
                            <th>Cota Extra Indevida</th>
                            <th>Valor Remanejado</th>
                            <th>Valor Gasto</th>
                            <th>Saldo Final</th>
                            <th>Projeção Semanal</th>
                            <th>Diferença</th>
                            <th>Variação</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php     

if ($diai == '' or $diaf == '') {    
print "<tr>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
                <td>Sem Dados</td>
               </tr>";
} else {

if ($ccustofi <> 'td') {
    $ccustosql = "and b.ccusto = '$ccustofi'";
} 
if ($filialfi <> 'td') {
    $filialsp = explode("-", $filialfi);
    $filialsql = "and b.codempresa = $filialsp[0] and b.codfilial = $filialsp[1]";
} 
if ($empresafi <> 'td') {
    $empresasql = "and b.ccusto like '%$empresafi%'";
} 
if ($coordenadorfi <> 'td' and $coordenadorfi <> '') {
    $coordenadorsql = "and c.idtbsupervisor in (SELECT idtbsupervisor FROM bdfrota.tbsupervisor where idtbcoordenador = '$coordenadorfi')";
} 
if ($matsupfi <> 'td' and $matsupfi <> '') {
    $supervisorsql = "and c.idtbsupervisor = '$matsupfi'";
} 
if ($mattecfi <> 'td' and $mattecfi <> '') {
    $tecnicosql = "and a.matricula = '$mattecfi'";
} 

$sql = "SELECT a.matricula FROM bdfrota.tbsaldosemanal a, bdaniel.tbfuncionario b, bdfrota.tbusuario c WHERE a.matricula = b.matricula and a.matricula = c.matricula and a.dia BETWEEN '$diai' AND '$diaf' $ccustosql $filialsql $empresasql $coordenadorsql $supervisorsql $tecnicosql GROUP BY matricula";

$resultado = mysql_query($sql) or die(mysql_error());

while ($row = mysql_fetch_array($resultado)) {
    $mattec = $row[0];

    // Consulta para tabela b
    $sql_b = "SELECT estado, nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$mattec'";
    $resultado_b = mysql_query($sql_b) or die(mysql_error());
    $row_b = mysql_fetch_array($resultado_b);

    // Consulta para tabela c
    $sql_c = "SELECT placa FROM bdfrota.tbveiculo WHERE matcond = '$mattec'";
    $resultado_c = mysql_query($sql_c) or die(mysql_error());
    $row_c = mysql_fetch_array($resultado_c);

    // Consulta para tabela d
    $sql_d = "SELECT idtbsupervisor FROM bdfrota.tbusuario WHERE matricula = '$mattec'";
    $resultado_d = mysql_query($sql_d) or die(mysql_error());
    $row_d = mysql_fetch_array($resultado_d);

    // Consulta para tabela e
    $sql_e = "SELECT ROUND(orcsemanal,0) as orcsemanal, ROUND(kmorcsem,0) as kmorcsem FROM bdfrota.tbsaldo WHERE matricula = '$mattec'";
    $resultado_e = mysql_query($sql_e) or die(mysql_error());
    $row_e = mysql_fetch_array($resultado_e);

    // Criar variáveis para cada tabela
    $estado = $row_b['estado'];
    $nome = utf8_encode($row_b['nome']);
    $ccusto = utf8_encode($row_b['ccusto']);
    $placa = $row_c['placa'];
    $idtbsupervisor = $row_d['idtbsupervisor'];
    $orcsemanal = $row_e['orcsemanal'];
    $kmorcsem = $row_e['kmorcsem'];

    if ($placa <> '') {
        print "<tr>
                            <td>$estado</td>
                            <td>$mattec</td>
                            <td>$nome</td>
                            <td>$ccusto</td>
                            <td>$placa</td>
                            <td>$idtbsupervisor</td>
                            <td>$orcsemanal</td>
                            <td>$kmorcsem</td>";

        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diai' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diai . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";
$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

$diaAdicionado = date('Y-m-d', strtotime($diaAdicionado . ' +1 day'));
        $sql2 = "SELECT sldinicial, kmproj, kmos, ctxdevida, ctxindevida, valremanejado, valgasto, sldfinal FROM bdfrota.tbsaldosemanal WHERE dia = '$diaAdicionado' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        print "
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$row2[5]</td>
                            <td>$row2[6]</td>
                            <td>$row2[7]</td>";

        $sql2 = "SELECT SUM(kmos), SUM(ctxdevida), SUM(ctxindevida), SUM(valremanejado), SUM(valgasto) FROM bdfrota.tbsaldosemanal WHERE dia BETWEEN '$diai' AND '$diaf' and matricula = '$mattec'";
        $resultado2 = mysql_query($sql2) or die(mysql_error());
        $row2 = mysql_fetch_array($resultado2);

        $saldofinalsemanal = $orcsemanal + $row2[1] + $row2[2] - $row2[3] - $row2[4];

        print "             <td>$orcsemanal</td>
                            <td>$kmorcsem</td>
                            <td>$row2[0]</td>
                            <td>$row2[1]</td>
                            <td>$row2[2]</td>
                            <td>$row2[3]</td>
                            <td>$row2[4]</td>
                            <td>$saldofinalsemanal</td>";

               $variacao = ROUND($row2[4] / $orcsemanal * 100,2);
        print "             <td>$orcsemanal</td>
                            <td>$row2[4]</td>
                            <td>$variacao%</td>
                            
               </tr>";
        
    }
            

}
}

      ?>
                        
                    </tbody>
                </table>
                <form action="./control/gerarexcelrelsaldosemanalnovo.php" method="post">
              
                <input type="hidden" name="diai" value="<?php echo htmlspecialchars($diai); ?>">
                <input type="hidden" name="ccusto" value="<?php echo htmlspecialchars($ccustofi); ?>">
                <input type="hidden" name="filial" value="<?php echo htmlspecialchars($filialfi); ?>">
                <input type="hidden" name="empresa" value="<?php echo htmlspecialchars($empresafi); ?>">
                <input type="hidden" name="coordenador" value="<?php echo htmlspecialchars($coordenadorfi); ?>">
                <input type="hidden" name="matsupf" value="<?php echo htmlspecialchars($matsupfi); ?>">
                <input type="hidden" name="mattecf" value="<?php echo htmlspecialchars($mattecfi); ?>">
                
              
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


<script>
        
       $("#selectcoordenador").change(function() {

          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var selecttecnico = document.getElementById('selecttecnico');
          
          for (var i = 0; i < selectsupervisor.length; i++) {
            var idsupervisor = selectsupervisor.options[i].id;
            if (!idsupervisor.startsWith(idcoordenador)) {
              $(selectsupervisor.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selectsupervisor.options[i]).removeAttr('disabled').show();
            }
          }
          
          for (var i = 0; i < selecttecnico.length; i++) {
            var idtecnico = selecttecnico.options[i].id;
            if (!idtecnico.startsWith(idcoordenador)) {
              $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selecttecnico.options[i]).removeAttr('disabled').show();
            }
          }
        });

         $("#selectsupervisor").change(function() {
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
          var selecttecnico = document.getElementById('selecttecnico');
          for (var i = 0; i < selecttecnico.length; i++) {
            var idtecnico = selecttecnico.options[i].id;
            if (!idtecnico.startsWith(idsupervisor)) {
              $(selecttecnico.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selecttecnico.options[i]).removeAttr('disabled').show();
            }
          }
          const id = idsupervisor.split('-');
          const [coordenador, supervisor] = id;
          for (var i = 0; i < selectcoordenador.length; i++) {
            if (selectcoordenador[i].id == coordenador) {
              selectcoordenador[i].selected = true;
            }else{
              selectcoordenador[i].selected = false;
            }
          }
        });

        $("#selecttecnico").change(function() {
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
          var selecttecnico = document.getElementById('selecttecnico');
          var idtecnico = selecttecnico[selecttecnico.selectedIndex].id;
          const id = idtecnico.split('-');
          const [coordenador, supervisor, tecnico] = id;
          for (var i = 0; i < selectcoordenador.length; i++) {
            if (selectcoordenador[i].id == coordenador) {
              selectcoordenador[i].selected = true;
            }else{
              selectcoordenador[i].selected = false;
            }
          }
          for (var i = 0; i < selectsupervisor.length; i++) {
            if (selectsupervisor[i].id == coordenador+'-'+supervisor) {
              selectsupervisor[i].selected = true;
            }else{
              selectsupervisor[i].selected = false;
            }
          }
        });
        </script>

<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready( function () {
        $('#datatablesSimple').DataTable();
    });
</script>
</html>