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

  $hoje = date('Y-m-d H:i:s');
  // Se hoje for segunda-feira, use a data de hoje
  $diahoje = strftime('%A',strtotime("$hoje"));

  if($diahoje == 'segunda'){
    $dataUltimaSegunda = $hoje;
    
  }else{
    //$dataUltimaSegunda = date($hoje, strtotime('last monday'));
    $dataUltimaSegunda = date('Y-m-d', strtotime($data . 'last monday'));
    
  }
  
  
  $hoje = new DateTime();
  if ($hoje->format('N') == 1) {
    $dataUltimaSegunda = $hoje->format('Y-m-d');
  } else {
    // Caso contrário, subtrair o número de dias desde a última segunda-feira
    $diasParaSubtrair = $hoje->format('N') - 1;
    //$dataUltimaSegunda = $hoje->sub(new DateInterval('P' . $diasParaSubtrair . 'D'))->format('Y-m-d');
    $hoje = date("Y-m-d");
    $dataUltimaSegunda = date($hoje, strtotime('last monday'));
  }

  //$hoje = date("Y-m-d");

  /*$dataesp = date("2023-06-12");
  $dataUltimaSegunda = date($dataesp, strtotime('last monday'));*/

  include"conecta.php";
  include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $idsupesc = $_POST["idtbsup"];
  $idcoordesc = $_POST["coordenador"];

  if(empty($idcoord)){
    $sqla="SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE matricula='$matricula';";
    $resultadoa = mysql_query($sqla) or die(mysql_error());
    $rowa = mysql_fetch_array($resultadoa);
      $idcoordesc = $rowa['idtbcoordenador'];
  }
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
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!--datatable-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
  </head>

  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include"menu.php"; ?>

    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h1 class="h1 mt-2"> Gastos Semanais </h1>
          
          <form action="./detalhes-rssem.php" method="post">
            <div class='d-flex'>
              <div id="collapseExample4">
                <h5><b>Coordenador:</b></h5>
            
                <div class="input-group">
                  <select class="form-select mb-3" name="coordenador" id="selectcoordenador">
                    <option value=" "> Selecione o coordenador </option>
                    <?php
                      $i=0;
                      $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                      $resultado = mysql_query($sql) or die(mysql_error());
                      while($row = mysql_fetch_array($resultado)){
                        $idcoord = $row[0];
                          
                        $nomecoord = $row[1];

                        $selected = ($idcoord == $idcoordesc) ? "selected=\"selected\"" : null;

                        $addmatriz = array($idcoord => $i );

                        foreach($addmatriz as $key => $value){
                          $idselect[$key] = $value;
                        }
                        $i++;
                        print"<option id='$idselect[$idcoord]' value='$idcoord' $selected>$nomecoord</option>";
                      }
                    ?>
                  </select>
                </div>
        
              </div>

              <!-- selecionar supervisor-->
              <div class="ms-5">
                <h5><b>Supervisor:</b></h5>
                <div class="input-group">
                  <select class="form-select mb-3" name="idtbsup" id="selectsupervisor" required>
                    <option value=" "> Selecione o supervisor </option>
                    
                    <?php
                      $i=0;
                      $idselect;
                      $sql2 ="SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome, a.idtbsupervisor, b.ccusto FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador ORDER BY b.nome;";            
                      $resultado2 = mysql_query($sql2) or die(mysql_error());
                      while($row2 = mysql_fetch_array($resultado2)){
                        $matsup = $row2[0];
                        $idcoord = $row2[1];

                        $nomesup = $row2[2];
                        $idsupf = $row2[3];

                        $ccusto= $row2[4];

                        $selected = ($idsupf == $idsupesc) ? "selected=\"selected\"" : null;

                        $addmatriz = array($matsup => $i );

                        foreach($addmatriz as $key => $value){
                          $idselect[$key] = $value;
                        }

                        $i++;
                        print"<option id='$idselect[$idcoord]-$idselect[$matsup]' value='$idsupf' $selected>$nomesup </option>";
                      }
                    ?>
                    <option value="td"> TODOS </option>
                    
                  </select>
                </div>
              </div>    
            </div>

            <div class='mt-2' style="width: 50%;">
              <div class="mt-4">
                <input type="submit" class="btn btn-success"  value="Filtrar">
              </div>
            </div>
          </form>  
        
          <div class="card mb-4 mt-4">
            <div class="card-header">
              <a href="dashboard.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Voltar para Dashboard Inicial 
              </a>
            </div>
             
            <div class="card-body">
              <!--tabela para entrada de dados-->
              <table id='example' class='table table-bordered' style="font-size: 12px !important;">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Supervisor</th>
                    <th>Centro de Custo</th>
                    <th colspan="5" >Semana Atual</th>
                    <?php
                      if ($idsupesc == 'td' or $idcoordesc == 'td') {
                        $sql = "SELECT distinct(data) FROM bdfrota.tbrssemanal WHERE data<='$dataUltimaSegunda' order by data desc LIMIT 4";
                        $resultado = mysql_query($sql) or die (mysql_error());
                        $nsem = 4;
                        
                        while ($row = mysql_fetch_array($resultado)) {
                          $dataa = $row[0];
                          $datas = explode(" ", $dataa);
                          $datasb = explode("-", $datas[0]);
                          print"<th colspan='7' >Semana $datasb[2]/$datasb[1]</th>";

                          $nsem = $nsem - 1;
                        }
                        
                        print"
                          </tr>
                          <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>KM Projetado</th>
                            <th>Orçamento Semanal</th>
                            <th>Valor Descontado</th>
                            <th>Sld Remanej</th>
                            <th>Fd Frota</th>";

                        $nsema = 4;

                        while ($nsema > $nsem) {                            
                          print"                
                            <th>R$ Proj.</th>
                            <th>R$ OS</th>
                            <th>R$ Cota Ex.</th>
                            <th>R$ Sld Remanej</th>
                            <th>R$ Fd Frota</th>
                            <th>R$ Total</th>
                            <th>R$ Diferença</th>";  
                          $nsema = $nsema - 1;
                        }
                      } else {

                        $sql = "SELECT distinct(data) FROM bdfrota.tbrssemanal where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsupesc') AND data<='$dataUltimaSegunda' order by data desc LIMIT 4";
                        $resultado = mysql_query($sql) or die (mysql_error());
                        $nsem = 4;
                        while ($row = mysql_fetch_array($resultado)) {
                          $dataa = $row[0];
                          $datas = explode(" ", $dataa);
                          $datasb = explode("-", $datas[0]);
                          print"<th colspan='7' >Semana $datasb[2]/$datasb[1]</th>";

                          $nsem = $nsem - 1;
                        }
                        print"
                          </tr>
                          <tr>
                            <th></th>
                            <th></th>
                            <th></th>                            
                            <th>KM Projetado</th>
                            <th>Orçamento Semanal</th>
                            <th>Valor Descontado</th>
                            <th>Sld Remanej</th>
                            <th>Fd Frota</th>";

                        $nsema = 4;
                        
                        while ($nsema > $nsem) {    
                          print"
                          <th>R$ Proj.</th>
                          <th>R$ OS</th>
                          <th>R$ Cota Ex.</th>
                          <th>R$ Sld Remanej</th>
                          <th>R$ Fd Frota</th>
                          <th>R$ Total</th>
                          <th>R$ Diferença</th>";  
                          $nsema = $nsema - 1;
                        }
                      }
                    ?>
                </thead>

                <tbody>
                <?php
                  if ($idsupesc == 'td' or $idcoordesc == 'td') {
                    $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmorcsem,2), ROUND(slddscnt,2) FROM bdfrota.tbsaldo where data = '$dataUltimaSegunda'";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    
                    while ($row = mysql_fetch_array($resultado)) {        
                      $mattec = $row[0];
                      $orcsem = $row[1];
                      $kmproj = $row[2];
                      $valdesc = $row[3];

                      $orcsem = round($orcsem, 2);
                      $kmproj = round($kmproj, 2);
                      $valdesc = round($valdesc,2);

                      $sql2 = "SELECT nome, ccusto, status FROM bdaniel.tbfuncionario where matricula = '$mattec'";
                      $resultado2 = mysql_query($sql2) or die (mysql_error());
                      $row2 = mysql_fetch_array($resultado2);
                      $nometec = $row2[0];
                      $ccusto = $row2[1];
                      $statustec = $row2[2];

                      $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
                      $resultado2 = mysql_query($sql2) or die (mysql_error());
                      $row2 = mysql_fetch_array($resultado2);
                      $idtbsupa = $row2[0];

                      $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupa'";
                      $resultado2 = mysql_query($sql2) or die (mysql_error());
                      $row2 = mysql_fetch_array($resultado2);
                      $nomesup = $row2[0];

                      //remanejamento positivo
                      $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=1; ";                          
                      $resultado4 = mysql_query($sql4) or die (mysql_error());
                      $row4 = mysql_fetch_array($resultado4);
                      $remanejpositivo = $row4[0];
                      
                      //remanejamento negativo
                      $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=0; ";                          
                      $resultado5 = mysql_query($sql5) or die (mysql_error());
                      $row5 = mysql_fetch_array($resultado5);
                      $remanejnegativo = $row5[0];
                      
                      $saldoremanej = $remanejpositivo-$remanejnegativo;

                      //saldo fundo frota
                      $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                      $resultado6 = mysql_query($sql6) or die (mysql_error());
                      $row6 = mysql_fetch_array($resultado6);
                      $ffrota = $row6[0];
                      $ffrota = str_replace(".", ",", $ffrota);
                      
                      if($statustec =='Ativo'){
                        print "
                          <tr>
                            <td>$nometec</td>
                            <td>$nomesup</td>
                            <td>$ccusto</td>
                            <td style='white-space:  nowrap;'>$kmproj km</td>                            
                            <td style='white-space:  nowrap;'>R$ $orcsem</td>
                            <td>R$ $valdesc</td>
                            <td>R$ $saldoremanej</td>
                            <td>R$ $ffrota</td>";//saldo remanejamento

                        $sql2 = "SELECT data FROM bdfrota.tbrssemanal /*WHERE data<='$dataUltimaSegunda'*/ group by data order by data desc LIMIT 4;";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());

                        while ($row2 = mysql_fetch_array($resultado2)) {
                          $datasem = $row2['data']; //segunda
                          $domseg = date($datasem, strtotime('next sunday'));
                          
                          //$sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.tbrssemanal WHERE data<='$dataUltimaSegunda' group by data order by data desc LIMIT 4;";
                          //$sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.tbrssemanal WHERE data<='$datasem' group by data order by data desc LIMIT 4;";
                          $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.tbrssemanal where matricula = '$mattec' and data = '".$datasem."' and data >= '2023-04-24' /*LIMIT 4*/";
                          
                          $resultadoa = mysql_query($sqla) or die (mysql_error());
                          $rowa = mysql_fetch_array($resultadoa);

                          $rsproj = $rowa[0];
                          $rsproj = round($rsproj,2);
                          $rsrod = $rowa[1];
                          $rsrod = round($rsrod,2);
                          $data1 = $rowa[2];
                          $rscota = $rowa[3];
                          $rscota = round($rscota, 2);
                          $tot = $rowa[4];
                          $tot = round($tot, 2);
                          $diff = $rowa[5];
                          $diff = round($diff, 2);

                          $total = $rsproj + $rscota;
                          $resultdiff = $total - $rsrod;

                          $rsprojv = str_replace(".", ",", $rsproj);
                          $rsrodv = str_replace(".", ",", $rsrod);
                          $rscotav = str_replace(".", ",", $rscota);
                          $totalv = str_replace(".", ",", $total);
                          $resultdiffv = str_replace(".", ",", $resultdiff);
      
                          if ($resultdiff < 0) {
                            $resultdiffv = 0;
                          } else {
                            $resultdiffv = $resultdiff;
                          }

                          //remanejamento positivo
                          $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=1; ";                          
                          $resultado4 = mysql_query($sql4) or die (mysql_error());
                          $row4 = mysql_fetch_array($resultado4);
                          $remanejpositivo = $row4[0];
                          
                          //remanejamento negativo
                          $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=0; ";                          
                          $resultado5 = mysql_query($sql5) or die (mysql_error());
                          $row5 = mysql_fetch_array($resultado5);
                          $remanejnegativo = $row5[0];
                          
                          $saldoremanej = $remanejpositivo-$remanejnegativo;

                          //saldo fundo frota
                          $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                          $resultado6 = mysql_query($sql6) or die (mysql_error());
                          $row6 = mysql_fetch_array($resultado6);
                          $ffrota = $row6[0];
                          $ffrota = str_replace(".", ",", $ffrota);
        
                          print "<td style='white-space:  nowrap;'>R$ $rsprojv</td>";

                          print "<td style='white-space:  nowrap;'><a href='./rotassemana.php?matriculatec=$mattec&diai=$datasem' target='_blank'>R$ $rsrodv</a></td>"; //R$ projetado

                          print "
                            <td style='white-space:  nowrap;'>R$ $rscotav</td>
                            <td style='white-space:  nowrap;'>R$ $saldoremanej</td>
                            <td style='white-space:  nowrap;'>R$ $ffrota</td>
                            <td style='white-space:  nowrap;'>R$ $totalv</td>
                            <td style='white-space:  nowrap;'>R$ $resultdiff</td>";
                        }
                        
                        print"</tr>";
                      }
                    }
                    

                  } else {

                    $sql = "SELECT matricula, ROUND(orcsemanal,2), ROUND(kmorcsem,2) FROM bdfrota.tbsaldo where data = '$dataUltimaSegunda' and matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idsupesc') order by data desc limit 1";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    
                    while ($row = mysql_fetch_array($resultado)) {        
                      $mattec = $row[0];
                      $orcsem = $row[1];
                      $kmproj = $row[2];                     

                      $orcsemv = str_replace(".", ",", $orcsem);

                      if ($medrsdiff < 0) {
                        $medrsdiff = 0;
                      }

                      $sql2 = "SELECT nome, ccusto, status FROM bdaniel.tbfuncionario where matricula = '$mattec'";
                      $resultado2 = mysql_query($sql2) or die (mysql_error());
                      $row2 = mysql_fetch_array($resultado2);
                      $nometec = $row2[0];
                      $ccusto = $row2[1];
                      $status = $row2[2];

                      $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsupesc'";
                      $resultado2 = mysql_query($sql2) or die (mysql_error());
                      $row2 = mysql_fetch_array($resultado2);
                      $nomesup = $row2[0]; 

                      //remanejamento positivo
                      $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=1; ";                          
                      $resultado4 = mysql_query($sql4) or die (mysql_error());
                      $row4 = mysql_fetch_array($resultado4);
                      $remanejpositivo = $row4[0];
                      
                      //remanejamento negativo
                      $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=0; ";                          
                      $resultado5 = mysql_query($sql5) or die (mysql_error());
                      $row5 = mysql_fetch_array($resultado5);
                      $remanejnegativo = $row5[0];
                      
                      $saldoremanej = $remanejpositivo-$remanejnegativo;

                      //saldo fundo frota
                      $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$dataUltimaSegunda 00:00:00' AND '$hoje 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                      $resultado6 = mysql_query($sql6) or die (mysql_error());
                      $row6 = mysql_fetch_array($resultado6);
                      $ffrota = $row6[0];
                      $ffrota = str_replace(".", ",", $ffrota);

                      //if($status=='Ativo'){
                        print "
                          <tr>
                            <td>$nometec</td>
                            <td>$nomesup</td>
                            <td>$ccusto</td>                            
                            <td style='white-space:  nowrap;'>$kmproj km</td> <!--km projetado-->
                            <td style='white-space:  nowrap;'>R$ $orcsemv</td><!--orç. semanal-->
                            <td style='white-space:  nowrap;'>R$ 0</td> <!--valor descontado -->
                            <td style='white-space:  nowrap;'>R$ $saldoremanej</td>
                            <td style='white-space:  nowrap;'>R$ $ffrota</td>";//saldo remanejamento
                
                        $sql2 = "SELECT data FROM bdfrota.tbrssemanal /*WHERE data<='$dataUltimaSegunda'*/ group by data order by data desc LIMIT 4;";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());

                        while ($row2 = mysql_fetch_array($resultado2)) {
                          $datasem = $row2['data'];

                          $sqla = "SELECT rsproj, rsrod, data, rscota, ROUND(rsproj + rscota, 2) as tot, ROUND(rsproj + rscota - rsrod ,2) as diff FROM bdfrota.tbrssemanal where matricula = '$mattec' and data = '".$datasem."'";
                          $resultadoa = mysql_query($sqla) or die (mysql_error());
                          $rowa = mysql_fetch_array($resultadoa);

                          $rsproj = $rowa[0];
                          $rsprojv = round($rsproj,2);

                          $rsrod = $rowa[1];
                          $rsrodv = round($rsrod,2);

                          $data1 = $rowa[2];
                          $rscota = $rowa[3];
                          $rscotav = round($rscota,2);

                          $tot = $rowa[4];
                          $diff = $rowa[5];

                          $total = $rsprojv + $rscotav;
                          $resultdiff = $total - $rsrodv;

                          $rsprojv = str_replace(".", ",", $rsprojv);
                          $rsrodv = str_replace(".", ",", $rsrodv);
                          $rscotav = str_replace(".", ",", $rscotav);
                          $totalv = str_replace(".", ",", $total);
                          $resultdiffv = str_replace(".", ",", $resultdiff);

                          if ($resultdiff < 0) {
                            $resultdiffv = 0;
                          } else {
                            $resultdiffv = $resultdiffv;
                          }

                          //remanejamento positivo
                          $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=1; ";                          
                          $resultado4 = mysql_query($sql4) or die (mysql_error());
                          $row4 = mysql_fetch_array($resultado4);
                          $remanejpositivo = $row4[0];
                          
                          //remanejamento negativo
                          $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=0; ";                          
                          $resultado5 = mysql_query($sql5) or die (mysql_error());
                          $row5 = mysql_fetch_array($resultado5);
                          $remanejnegativo = $row5[0];
                          
                          $saldoremanej = $remanejpositivo-$remanejnegativo;

                          //saldo fundo frota
                          $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN '$datasem 00:00:00' AND '$domseg 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                          $resultado6 = mysql_query($sql6) or die (mysql_error());
                          $row6 = mysql_fetch_array($resultado6);
                          $ffrota = $row6[0];
                          $ffrota = str_replace(".", ",", $ffrota);
              
                          print "
                            <td style='white-space:  nowrap;'>
                              <a href='./detalhes-kmossemanais.php?matriculatec=$mattec&semana=$datasem' target='_blank'>R$ $rsprojv</a>
                            </td>
                            <td style='white-space:  nowrap;'>R$ $rscotav</td> <!--R$ os-->
                            <td style='white-space:  nowrap;'>R$  </td><!--R$ cota extra-->
                            <td style='white-space:  nowrap;'>R$  $saldoremanej</td>
                            <td style='white-space:  nowrap;'>R$  $ffrota</td>
                            <td style='white-space:  nowrap;'>R$ $totalv</td> <!--R$ total-->
                            <td style='white-space:  nowrap;'>R$ $resultdiffv</td> <!--R$ diferenca-->";
                        }
  
                        print"</tr>";
                      //}
                    }
                  } 
                            
                ?>

                </tbody>
              </table>
                           
              <!--<form action="./control/gerarexceldetrssemanal.php" method="post">
                <?php 
                  print "<input type='hidden' name='idtbsup' value='$idsupesc'>";
                ?>
                <div class="form-group col-md-2">
                  <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                  <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                </div>
              </form>-->
            </div>

          </div>
        </div>
      </main>
      <footer class="py-4 bg-light mt-auto" >
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
  

  <script>
          
    $("#selectcoordenador").change(function() {

      var selectcoordenador = document.getElementById('selectcoordenador');
      var idcoordenador = selectcoordenador[selectcoordenador.selectedIndex].id;
      var selectsupervisor = document.getElementById('selectsupervisor');
      var selecttecnico = document.getElementById('selecttecnico');
      
      for (var i = 0; i < selectsupervisor.length; i++) {
        var idsupervisor = selectsupervisor.options[i].id;
        if (!idsupervisor.startsWith(idcoordenador + "-")) {
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

      
    new DataTable('#example', {
      order: [[0, 'asc']],     
      
      dom: 'Bfrtip',
      buttons: [{
                    extend: 'excelHtml5',
                    title: 'Gastos Semanais'
                },],
      //buttons: ['copy', 'excel', 'pdf']
      
    });
     
  </script>
</html>