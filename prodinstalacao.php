<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$inicio = $_POST['inicio'];
$fim = $_POST['fim'];
if ($inicio == '' or $fim == '') {
  $inicio = date('Y-m-d');
  $fim = date('Y-m-d');
}
$gerentef = $_POST['gerente'];
$coordenadorf = $_POST['coordenador'];
$supervisorf = $_POST['supervisor'];
$tecnologia = $_POST['tecnologia'];
$tipoatividade = $_POST['tipoatividade'];
include"conecta.php";
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
 <body class="sb-nav-fixed">

        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">
                <main><div class="container-fluid px-4">
                    <h1 class="mt-4">Produção Instalação</h1>
                      <div class="callout callout-secondary">
    <h5><b>FILTRO</b></h5><p><b>Data da atualização: TOA</b>       
   <?php

      $sql = "SELECT max(datalog) FROM bdindicador.tbindicadorinstal_log";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $uattaux = $row[0];
      $uatt2 = explode(" ", $uattaux);
      $uatt3 = explode("-", $uatt2[0]);
      $uatt = "$uatt3[2]".'/'."$uatt3[1]".'/'."$uatt3[0]"." $uatt2[1]";
      print"$uatt | ";

  ?><b>HC Campo: </b>
   <?php

      $sql = "SELECT max(tbhccampo_log) FROM bdaniel.tbhccampo_log";
      $resultado = mysql_query($sql) or die (mysql_error());
      $row = mysql_fetch_array($resultado);
      $hcuattaux = $row[0];
      $hcuatt2 = explode(" ", $hcuattaux);
      $hcuatt3 = explode("-", $hcuatt2[0]);
      $hcuatt = "$hcuatt3[2]".'/'."$hcuatt3[1]".'/'."$hcuatt3[0]"." $hcuatt2[1]";
      print"$hcuatt";

  ?>
  <img src="./src/images/ajax-loading-c7.gif" width="18px" heigh="18px">
</p>
<div id="collapseExample4">
<form action="./prodinstalacao.php" method="post">
     <div class="row">

     <div class="col-md-2">
      <label for="exampleInputEmail1">Início:</label>
      <input type="date" class="form-control form-control-md" name="inicio">
    </div>

    <div class="col-md-2">
      <label for="exampleInputEmail1">Fim:</label>
      <input type="date" class="form-control form-control-md" name="fim">
    </div>

  <div class="col-md-2">
    <label for="exampleInputEmail1">Gerente:</label>
    <select class="form-select" name="gerente" id="selectgerente">
      <option value="">TODOS</option>
      <?php
      $i = 0;
      $sql = "SELECT DISTINCT(gerente) FROM bdindicador.tbindicadorinstal";
      $resultado = mysql_query($sql) or die (mysql_error());
      while($row = mysql_fetch_array($resultado)){
        $lgerente = $row[0];
        $addmatriz = [$lgerente => $i];
        foreach($addmatriz as $key => $value)
        {
          $idselect[$key] = $value;
        };
        $i++;
        print"<option id='$idselect[$lgerente]' value='$lgerente'>$lgerente</option>";
      }   ?>
    </select>
  </div>

  <div class="col-md-2">
    <label for="exampleInputEmail1">Coordenador:</label>
    <select class="form-select" name="coordenador" id="selectcoordenador">
      <option value="">TODOS</option>
      <?php
      $i = 0;
      $idselect;
      $sql = "SELECT DISTINCT(coordenador), gerente FROM bdindicador.tbindicadorinstal";
      $resultado = mysql_query($sql) or die (mysql_error());
      while($row = mysql_fetch_array($resultado)){
        $lcoordenador = $row[0];
        $lgerente = $row[1];
        $addmatriz = array($lcoordenador => $i );
        foreach($addmatriz as $key => $value)
        {
          $idselect[$key] = $value;
        }
        $i++;
        print"<option id='$idselect[$lgerente]-$idselect[$lcoordenador]' value='$lcoordenador'>$lcoordenador</option>";
      }
      ?>
    </select>
  </div>

  <div class="col-md-2">
    <label for="exampleInputEmail1">Supervisor:</label>
    <select class="form-select" name="supervisor" id="selectsupervisor">
      <option value="">TODOS</option>
      <?php
      $i = 0;
      $sql = "SELECT DISTINCT(supervisor), coordenador, gerente FROM bdindicador.tbindicadorinstal";
      $resultado = mysql_query($sql) or die (mysql_error());
      while($row = mysql_fetch_array($resultado)){
        $lsupervisor = $row[0];
        $lcoordenador = $row[1];
        $lgerente = $row[2];
        print"<option id='$idselect[$lgerente]-$idselect[$lcoordenador]-$i' value='$lsupervisor'>$lsupervisor</option>";
      }
      $i++;
      ?>
    </select>
  </div>

    <div class="col-md-2">
      <label for="exampleInputEmail1">Tecnologia:</label>
      <select class="form-select" name="tecnologia">
      <option value="">TODOS</option>
      <option value='FTTH'>FTTH</option>
      <option value='FTTC'>FTTC</option>
      </select>
    </div>

    <div class="col-md-2">
      <label for="exampleInputEmail1">Tipo de Atividade:</label>
      <select class="form-select" name="tipoatividade">
      <option value="">TODOS</option>
      <option value='trocatec'>Troca de Tecnologia</option>
      <option value='mudend'>Mudança de Endereço</option>
      <option value='gross'>Gross</option>
      </select>
    </div>

    <div class="form-group col-md-1">
      <label for="exampleInputEmail1"><b style="color: white">.</b></label>
    <input type="submit" class="form-control form-control-md btn btn-secondary" value="Filtrar">
  </div>

         <!--  <div class="form-group col-md-1">
      <label for="exampleInputEmail1"><b style="color: white">.</b></label>
       <a href="./download-grade" ><input  type="button" class="form-control form-control-md btn btn-secondary" value="Baixar excel"></a>
  </div> -->
</div></form>
</div>
</div></div>



<div class="container-fluid px-4">
    <h3 class="mt-4"></h3>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Instalação
        </div>
        <div class="card-body">
            <table id="datatablesSimple">
                <thead>
                    <tr>
                    <th><center>Filial</center></th>
                    <th><center>Gerencia</center></th>
                    <th title="Total de técnicos com atividade no TOA."><center>Presença</center></th>
                    <th title="Volume de produção acordado."><center>Meta</center></th>
                    <th title="Total de atividades executados no TOA."><center>Concluído</center></th>
                    <th title="concluído + em campo"><center>Proj.</center></th>
                    <th title="(Concluído - Meta)"><center>Gap</center></th>
                    <th title="Atividades canceladas no TOA."><center>Canc.</center></th>
                    <th title="Atividades pendenciadas no TOA."><center>Pend.</center></th>
                    <th title="Atividades suspenso no TOA."><center>Susp.</center></th>
                    <th title="(Atividades pendenciadas / Visitas)"><center>Efic.</center></th>
                    <th title="(Total de atividades executadas / Presença)"><center>Produt.</center></th>
                    <th title="Atividades iniciadas no TOA."><center>Iniciado</center></th>
                    <th title="Atividades não iniciadas no TOA."><center>Não Inic.</center></th>
                    <th title="Atividades não iniciadas + iniciadas no TOA."><center>Em campo</center></th>
                    <th title="Total de Atividades despachadas no TOA."><center>Desp.</center></th>
                    <th title="Média de Atividades despachadas no TOA."><center>Med. Desp.</center></th>
                    <th title="Total de técnicos sem atividades na caixa."><center>S/ Ativ.</center></th>
                    <th title="Total de técnicos com atividades a iniciar, porém não foram iniciadas."><center>Ociosos</center></th>
                    <th title="Total de técnicos que não produziram nada até o momento."><center>Zerados</center></th>
                    <th title="Total de técnicos que executaram apenas 01 atividade."><center>01 Prod.</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php

                  $sql = "SELECT DISTINCT(gerente), filial FROM bdindicador.tbindicadorinstal where gerente like '%$gerentef%' and coordenador like '%$coordenadorf%' and supervisor like '%$supervisorf%' and datareg >= '$inicio' and datareg <= '$fim' and datareg >= '$inicio' and datareg <= '$fim'";
                  $resultado = mysql_query($sql) or die (mysql_error());
                  while($row = mysql_fetch_array($resultado)){

                  $gerente = $row['gerente'];
                  $filial = $row['filial'];

                  $sql2 = "SELECT count(distinct(tecnico)) as presenca, count(distinct(tecnico))*3 as meta, SUM(concluido) as concluido, SUM(concluido+iniciado+niniciado) as proj, SUM(concluido) - count(distinct(tecnico))*3 as gap, SUM(cancel) as cancel, SUM(pend) as pend, SUM(susp) as susp, round(SUM(concluido+iniciado+niniciado)/SUM(concluido+iniciado+niniciado+pend)*100,0) as efic, round(SUM(concluido)/count(distinct(tecnico)),2) as produt, SUM(iniciado) as iniciado, SUM(niniciado) as niniciado, SUM(iniciado) + SUM(niniciado) as campo, SUM(concluido+iniciado+niniciado+pend+susp+cancel) as desp, round(SUM(concluido+iniciado+niniciado+pend+susp+cancel)/count(distinct(tecnico)),2) as meddesp FROM bdindicador.tbindicadorinstal where gerente = '$gerente' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%'";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_fetch_array($resultado2);

                    $presenca = $row2['presenca'];
                    $meta = $row2['meta'];
                    $concluido = $row2['concluido'];
                    $proj = $row2['proj'];
                    $gap = $row2['gap'];
                    $cancel = $row2['cancel'];
                    $pend = $row2['pend'];
                    $susp = $row2['susp'];
                    $efic = $row2['efic'];
                    $produt = $row2['produt'];
                    $iniciado = $row2['iniciado'];
                    $niniciado = $row2['niniciado'];
                    $campo = $row2['campo'];
                    $desp = $row2['desp'];
                    $meddesp = $row2['meddesp'];

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where gerente = '$gerente' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $satividade = $row2;

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where gerente = '$gerente' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) > 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $ocioso = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where gerente = '$gerente' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $zerado = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where gerente = '$gerente' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 1";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $umprod= $row2;

                    print"<tr class='text-center'>
                        <td>$filial</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='gerente' value='$gerente'><input type='hidden' name='coordenador' value='$coordenadorf'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><button class='btn btn-link-datatable' type='submit'>$gerente</button></form></td>
                        <td>$presenca</td>
                        <td>$meta</td>
                        <td>$concluido</td>
                        <td>$proj</td>
                        <td style='background-color:"; if ($gap >= 0) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$gap</td>
                        <td>$cancel</td>
                        <td>$pend</td>
                        <td>$susp</td>
                        <td style='background-color:"; if ($efic >= 95) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$efic%</td>
                        <td style='color:"; if ($produt >= 3.0) {
                          print"green";
                        }else{
                          print"red";
                          } print"'>$produt</td>
                        <td>$iniciado</td>
                        <td>$niniciado</td>
                        <td>$campo</td>
                        <td>$desp</td>
                        <td>$meddesp</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='gerente' value='$gerente'><input type='hidden' name='coordenador' value='$coordenadorf'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) = 0'><button class='btn btn-link-datatable' type='submit'>$satividade</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='gerente' value='$gerente'><input type='hidden' name='coordenador' value='$coordenadorf'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) > 0'><button class='btn btn-link-datatable' type='submit'>$ocioso</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='gerente' value='$gerente'><input type='hidden' name='coordenador' value='$coordenadorf'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 0'><button class='btn btn-link-datatable' type='submit'>$zerado</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='gerente' value='$gerente'><input type='hidden' name='coordenador' value='$coordenadorf'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 1'><button class='btn btn-link-datatable' type='submit'>$umprod</button></form></td>
                    </tr>";   
                  }
                    
                  $sql = "SELECT count(distinct(tecnico)) as presenca, count(distinct(tecnico))*3 as meta, SUM(concluido) as concluido, SUM(concluido+iniciado+niniciado) as proj, SUM(concluido) - count(distinct(tecnico))*3 as gap, SUM(cancel) as cancel, SUM(pend) as pend, SUM(susp) as susp, round(SUM(concluido+iniciado+niniciado)/SUM(concluido+iniciado+niniciado+pend)*100,0) as efic, round(SUM(concluido)/count(distinct(tecnico)),2) as produt, SUM(iniciado) as iniciado, SUM(niniciado) as niniciado, SUM(iniciado) + SUM(niniciado) as campo, SUM(concluido+iniciado+niniciado+pend+susp+cancel) as desp, round(SUM(concluido+iniciado+niniciado+pend+susp+cancel)/count(distinct(tecnico)),2) as meddesp FROM bdindicador.tbindicadorinstal where datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%'";
                  $resultado = mysql_query($sql) or die (mysql_error());
                  $row = mysql_fetch_array($resultado);

                    $presenca = $row['presenca'];
                    $meta = $row['meta'];
                    $concluido = $row['concluido'];
                    $proj = $row['proj'];
                    $gap = $row['gap'];
                    $cancel = $row['cancel'];
                    $pend = $row['pend'];
                    $susp = $row['susp'];
                    $efic = $row['efic'];
                    $produt = $row['produt'];
                    $iniciado = $row['iniciado'];
                    $niniciado = $row['niniciado'];
                    $campo = $row['campo'];
                    $desp = $row['desp'];
                    $meddesp = $row['meddesp'];


                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $satividade = $row2;

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) > 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $ocioso = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $zerado = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $umprod= $row2;

                    print"<tr class='text-center'>
                        <td colspan='2'>FFA</td>
                        <td>$presenca</td>
                        <td>$meta</td>
                        <td>$concluido</td>
                        <td>$proj</td>
                        <td style='background-color:"; if ($gap >= 0) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$gap</td>
                        <td>$cancel</td>
                        <td>$pend</td>
                        <td>$susp</td>
                        <td style='background-color:"; if ($efic >= 95) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$efic%</td>
                        <td style='color:"; if ($produt >= 3.0) {
                          print"green";
                        }else{
                          print"red";
                          } print"'>$produt</td>
                        <td>$iniciado</td>
                        <td>$niniciado</td>
                        <td>$campo</td>
                        <td>$desp</td>
                        <td>$meddesp</td>
                        <td>$satividade</td>
                        <td>$ocioso</td>
                        <td>$zerado</td>
                        <td>$umprod</td>
                    </tr>";   

                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <h3 class="mt-4"></h3>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Instalação Coordenador
        </div>
        <div class="card-body">
            <table id="datatablesSimple2">
                <thead>
                    <tr>
                    <th><center>Filial</center></th>
                    <th><center>Coordenador</center></th>
                    <th title="Total de técnicos com atividade no TOA."><center>Presença</center></th>
                    <th title="Volume de produção acordado."><center>Meta</center></th>
                    <th title="Total de atividades executados no TOA."><center>Concluído</center></th>
                    <th title="concluído + em campo"><center>Proj.</center></th>
                    <th title="(Concluído - Meta)"><center>Gap</center></th>
                    <th title="Atividades canceladas no TOA."><center>Canc.</center></th>
                    <th title="Atividades pendenciadas no TOA."><center>Pend.</center></th>
                    <th title="Atividades suspenso no TOA."><center>Susp.</center></th>
                    <th title="(Atividades pendenciadas / Visitas)"><center>Efic.</center></th>
                    <th title="(Total de atividades executadas / Presença)"><center>Produt.</center></th>
                    <th title="Atividades iniciadas no TOA."><center>Iniciado</center></th>
                    <th title="Atividades não iniciadas no TOA."><center>Não Inic.</center></th>
                    <th title="Atividades não iniciadas + iniciadas no TOA."><center>Em campo</center></th>
                    <th title="Total de Atividades despachadas no TOA."><center>Desp.</center></th>
                    <th title="Média de Atividades despachadas no TOA."><center>Med. Desp.</center></th>
                    <th title="Total de técnicos sem atividades na caixa."><center>S/ Ativ.</center></th>
                    <th title="Total de técnicos com atividades a iniciar, porém não foram iniciadas."><center>Ociosos</center></th>
                    <th title="Total de técnicos que não produziram nada até o momento."><center>Zerados</center></th>
                    <th title="Total de técnicos que executaram apenas 01 atividade."><center>01 Prod.</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php

                  $sql = "SELECT DISTINCT(coordenador), filial FROM bdindicador.tbindicadorinstal where gerente like '%$gerentef%' and coordenador like '%$coordenadorf%' and supervisor like '%$supervisorf%' and datareg >= '$inicio' and datareg <= '$fim'";
                  $resultado = mysql_query($sql) or die (mysql_error());
                  
                  while($row = mysql_fetch_array($resultado)){

                  $coordenador = $row['coordenador'];
                  $filial = $row['filial'];

                  $sql2 = "SELECT count(distinct(tecnico)) as presenca, count(distinct(tecnico))*3 as meta, SUM(concluido) as concluido, SUM(concluido+iniciado+niniciado) as proj, SUM(concluido) - count(distinct(tecnico))*3 as gap, SUM(cancel) as cancel, SUM(pend) as pend, SUM(susp) as susp, round(SUM(concluido+iniciado+niniciado)/SUM(concluido+iniciado+niniciado+pend)*100,0) as efic, round(SUM(concluido)/count(distinct(tecnico)),2) as produt, SUM(iniciado) as iniciado, SUM(niniciado) as niniciado, SUM(iniciado) + SUM(niniciado) as campo, SUM(concluido+iniciado+niniciado+pend+susp+cancel) as desp, round(SUM(concluido+iniciado+niniciado+pend+susp+cancel)/count(distinct(tecnico)),2) as meddesp FROM bdindicador.tbindicadorinstal where coordenador = '$coordenador' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%'";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_fetch_array($resultado2);

                    $presenca = $row2['presenca'];
                    $meta = $row2['meta'];
                    $concluido = $row2['concluido'];
                    $proj = $row2['proj'];
                    $gap = $row2['gap'];
                    $cancel = $row2['cancel'];
                    $pend = $row2['pend'];
                    $susp = $row2['susp'];
                    $efic = $row2['efic'];
                    $produt = $row2['produt'];
                    $iniciado = $row2['iniciado'];
                    $niniciado = $row2['niniciado'];
                    $campo = $row2['campo'];
                    $desp = $row2['desp'];
                    $meddesp = $row2['meddesp'];

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where coordenador = '$coordenador' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $satividade = $row2;

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where coordenador = '$coordenador' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) > 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $ocioso = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where coordenador = '$coordenador' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $zerado = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where coordenador = '$coordenador' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 1";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $umprod= $row2;

                    print"<tr class='text-center'>
                        <td>$filial</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='coordenador' value='$coordenador'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><button class='btn btn-link-datatable' type='submit'>$coordenador</button></form></td>
                        <td>$presenca</td>
                        <td>$meta</td>
                        <td>$concluido</td>
                        <td>$proj</td>
                        <td style='background-color:"; if ($gap >= 0) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$gap</td>
                        <td>$cancel</td>
                        <td>$pend</td>
                        <td>$susp</td>
                        <td style='background-color:"; if ($efic >= 95) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$efic%</td>
                        <td style='color:"; if ($produt >= 3.0) {
                          print"green";
                        }else{
                          print"red";
                          } print"'>$produt</td>
                        <td>$iniciado</td>
                        <td>$niniciado</td>
                        <td>$campo</td>
                        <td>$desp</td>
                        <td>$meddesp</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='coordenador' value='$coordenador'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) = 0'><button class='btn btn-link-datatable' type='submit'>$satividade</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='coordenador' value='$coordenador'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) > 0'><button class='btn btn-link-datatable' type='submit'>$ocioso</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='coordenador' value='$coordenador'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 0'><button class='btn btn-link-datatable' type='submit'>$zerado</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='coordenador' value='$coordenador'><input type='hidden' name='supervisor' value='$supervisorf'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 1'><button class='btn btn-link-datatable' type='submit'>$umprod</button></form></td>
                    </tr>";   
                  }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <h3 class="mt-4"></h3>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Instalação Supervisor
        </div>
        <div class="card-body">
            <table id="datatablesSimple3">
                <thead>
                    <tr>
                    <th><center>Filial</center></th>
                    <th><center>Supervisor</center></th>
                    <th title="Total de técnicos com atividade no TOA."><center>Presença</center></th>
                    <th title="Volume de produção acordado."><center>Meta</center></th>
                    <th title="Total de atividades executados no TOA."><center>Concluído</center></th>
                    <th title="concluído + em campo"><center>Proj.</center></th>
                    <th title="(Concluído - Meta)"><center>Gap</center></th>
                    <th title="Atividades canceladas no TOA."><center>Canc.</center></th>
                    <th title="Atividades pendenciadas no TOA."><center>Pend.</center></th>
                    <th title="Atividades suspenso no TOA."><center>Susp.</center></th>
                    <th title="(Atividades pendenciadas / Visitas)"><center>Efic.</center></th>
                    <th title="(Total de atividades executadas / Presença)"><center>Produt.</center></th>
                    <th title="Atividades iniciadas no TOA."><center>Iniciado</center></th>
                    <th title="Atividades não iniciadas no TOA."><center>Não Inic.</center></th>
                    <th title="Atividades não iniciadas + iniciadas no TOA."><center>Em campo</center></th>
                    <th title="Total de Atividades despachadas no TOA."><center>Desp.</center></th>
                    <th title="Média de Atividades despachadas no TOA."><center>Med. Desp.</center></th>
                    <th title="Total de técnicos sem atividades na caixa."><center>S/ Ativ.</center></th>
                    <th title="Total de técnicos com atividades a iniciar, porém não foram iniciadas."><center>Ociosos</center></th>
                    <th title="Total de técnicos que não produziram nada até o momento."><center>Zerados</center></th>
                    <th title="Total de técnicos que executaram apenas 01 atividade."><center>01 Prod.</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php

                  $sql = "SELECT DISTINCT(supervisor), filial FROM bdindicador.tbindicadorinstal where gerente like '%$gerentef%' and coordenador like '%$coordenadorf%' and supervisor like '%$supervisorf%' and datareg >= '$inicio' and datareg <= '$fim'";
                  $resultado = mysql_query($sql) or die (mysql_error());
                  while($row = mysql_fetch_array($resultado)){

                  $supervisor = $row['supervisor'];
                  $filial = $row['filial'];
                  
                  $sql2 = "SELECT count(distinct(tecnico)) as presenca, count(distinct(tecnico))*3 as meta, SUM(concluido) as concluido, SUM(concluido+iniciado+niniciado) as proj, SUM(concluido) - count(distinct(tecnico))*3 as gap, SUM(cancel) as cancel, SUM(pend) as pend, SUM(susp) as susp, round(SUM(concluido+iniciado+niniciado)/SUM(concluido+iniciado+niniciado+pend)*100,0) as efic, round(SUM(concluido)/count(distinct(tecnico)),2) as produt, SUM(iniciado) as iniciado, SUM(niniciado) as niniciado, SUM(iniciado) + SUM(niniciado) as campo, SUM(concluido+iniciado+niniciado+pend+susp+cancel) as desp, round(SUM(concluido+iniciado+niniciado+pend+susp+cancel)/count(distinct(tecnico)),2) as meddesp FROM bdindicador.tbindicadorinstal where supervisor = '$supervisor' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%'";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_fetch_array($resultado2);

                    $presenca = $row2['presenca'];
                    $meta = $row2['meta'];
                    $concluido = $row2['concluido'];
                    $proj = $row2['proj'];
                    $gap = $row2['gap'];
                    $cancel = $row2['cancel'];
                    $pend = $row2['pend'];
                    $susp = $row2['susp'];
                    $efic = $row2['efic'];
                    $produt = $row2['produt'];
                    $iniciado = $row2['iniciado'];
                    $niniciado = $row2['niniciado'];
                    $campo = $row2['campo'];
                    $desp = $row2['desp'];
                    $meddesp = $row2['meddesp'];

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where supervisor = '$supervisor' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $satividade = $row2;

                  $sql2 = "SELECT tecnico, sum(iniciado), sum(niniciado) FROM bdindicador.tbindicadorinstal where supervisor = '$supervisor' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(iniciado) = 0 and sum(niniciado) > 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $ocioso = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where supervisor = '$supervisor' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 0";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $zerado = $row2;

                  $sql2 = "SELECT tecnico, sum(concluido) FROM bdindicador.tbindicadorinstal where supervisor = '$supervisor' and datareg >= '$inicio' and datareg <= '$fim' and tecnologia like '%$tecnologia%' and tipoatividade like '%$tipoatividade%' group by tecnico having sum(concluido) = 1";
                  $resultado2 = mysql_query($sql2) or die (mysql_error());
                  $row2 = mysql_num_rows($resultado2);
                  $umprod= $row2;

                    print"<tr class='text-center'>
                        <td>$filial</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='supervisor' value='$supervisor'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><button class='btn btn-link-datatable' type='submit'>$supervisor</button></form></td>
                        <td>$presenca</td>
                        <td>$meta</td>
                        <td>$concluido</td>
                        <td>$proj</td>
                        <td style='background-color:"; if ($gap >= 0) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$gap</td>
                        <td>$cancel</td>
                        <td>$pend</td>
                        <td>$susp</td>
                        <td style='background-color:"; if ($efic >= 95) {
                          print"green";
                        }else{
                          print"red";
                          } print";color:white'>$efic%</td>
                        <td style='color:"; if ($produt >= 3.0) {
                          print"green";
                        }else{
                          print"red";
                          } print"'>$produt</td>
                        <td>$iniciado</td>
                        <td>$niniciado</td>
                        <td>$campo</td>
                        <td>$desp</td>
                        <td>$meddesp</td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='supervisor' value='$supervisor'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) = 0'><button class='btn btn-link-datatable' type='submit'>$satividade</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='supervisor' value='$supervisor'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(iniciado) = 0 and sum(niniciado) > 0'><button class='btn btn-link-datatable' type='submit'>$ocioso</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='supervisor' value='$supervisor'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 0'><button class='btn btn-link-datatable' type='submit'>$zerado</button></form></td>
                        <td><form method='post' action='prodinstalacaotec.php'><input type='hidden' name='supervisor' value='$supervisor'><input type='hidden' name='inicio' value='$inicio'><input type='hidden' name='fim' value='$fim'><input type='hidden' name='tecnologia' value='$tecnologia'><input type='hidden' name='tipoatividade' value='$tipoatividade'><input type='hidden' name='colfim' value='having sum(concluido) = 1'><button class='btn btn-link-datatable' type='submit'>$umprod</button></form></td>
                    </tr>";   
                  }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script>
        $("#selectgerente").change(function() {
          var selectgerente = document.getElementById('selectgerente');
          var idgerente = selectgerente[selectgerente.selectedIndex].id;
          var selectcoordenador = document.getElementById('selectcoordenador');
          var selectsupervisor = document.getElementById('selectsupervisor');
          for (var i = 0; i < selectcoordenador.length; i++) {
            var idcoordenador = selectcoordenador.options[i].id;
            if (!idcoordenador.startsWith(idgerente)) {
              $(selectcoordenador.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selectcoordenador.options[i]).removeAttr('disabled').show();
            }
          }
          for (var i = 0; i < selectsupervisor.length; i++) {
            var idsupervisor = selectsupervisor.options[i].id;
            if (!idsupervisor.startsWith(idgerente)) {
              $(selectsupervisor.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selectsupervisor.options[i]).removeAttr('disabled').show();
            }
          }
        });

        $("#selectcoordenador").change(function() {
          var selectgerente = document.getElementById('selectgerente');
          var idgerente = selectgerente[selectgerente.selectedIndex].id;
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          for (var i = 0; i < selectsupervisor.length; i++) {
            var idsupervisor = selectsupervisor.options[i].id;
            if (!idsupervisor.startsWith(idcoordenador)) {
              $(selectsupervisor.options[i]).attr('disabled', 'disabled').hide();
            } else {
              $(selectsupervisor.options[i]).removeAttr('disabled').show();
            }
          }
          const id = idcoordenador.split('-');
          const [gerente, coordenador] = id;
          for (var i = 0; i < selectgerente.length; i++) {
            if (selectgerente[i].id == gerente) {
              selectgerente[i].selected = true;
            }else{
              selectgerente[i].selected = false;
            }
          }
        });

        $("#selectsupervisor").change(function() {
          var selectgerente = document.getElementById('selectgerente');
          var idgerente = selectgerente[selectgerente.selectedIndex].id;
          var selectcoordenador = document.getElementById('selectcoordenador');
          var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
          var selectsupervisor = document.getElementById('selectsupervisor');
          var idsupervisor = selectsupervisor[selectsupervisor.selectedIndex].id;
          const id = idsupervisor.split('-');
          const [gerente, coordenador, supervisor] = id;
          for (var i = 0; i < selectgerente.length; i++) {
            if (selectgerente[i].id == gerente) {
              selectgerente[i].selected = true;
            }else{
              selectgerente[i].selected = false;
            }
          }
          for (var i = 0; i < selectcoordenador.length; i++) {
            if (selectcoordenador[i].id == gerente+'-'+coordenador) {
              selectcoordenador[i].selected = true;
            }else{
              selectcoordenador[i].selected = false;
            }
          }
        });
        </script>
    </body>
</html>
