<?php
    setlocale(LC_ALL, 'pt_BR.utf8');
    date_default_timezone_set('America/Sao_Paulo');
    // Defina o tempo de vida da sessão para 1 hora
    $lifetime = 3600; // 1 hora em segundos

    // Configurar os parâmetros do cookie da sessão
    session_set_cookie_params($lifetime);

    // Iniciar a sessão
    session_start();

    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];

    if(empty($perfil)){
        $matricula = $_POST['matricula'];
        $perfil = $_POST['perfil'];
    }

    /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"http://ffasip.ddns.net:4242/frotas/index.php\"</script>";
    } elseif ($perfil <> '4' && $perfil <> '11' && $perfil<>'5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";  
    }*/

    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;

    include"../conecta03.php";

    header("Content-type: text/html; charset=utf-8");

    $unidadef = $_POST['unidade'];
    $tpossef = $_POST['tp'];
    $statusa = $_POST['statusa'];

    if ($matricula == '160030'||$matricula == '410109'||$matricula == '500422'||$matricula == '501285'||$matricula == '410039'||$matricula == '501640'||$matricula == '500459' || $matricula=='411425' || $matricula == '003931'){
        $npode='nao';
    }

    $in = "('1241','1767','1764','1765', '1893', '1894', '1895', '1896')";

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="../src/images/favicon.png"/>
        <title>Relatório de Inventário </title>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <!--data tables - css e js-->
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu-teste.php"; ?>

        <div id="layoutSidenav_content">

            <main>
                <div class="container-fluid px-4">
                    <h1 class="h1 mt-2"> Relatório Inventário de Veículos </h1>

                    <!--div filtros-->
                    <div class="mt-3 mb-3">
                    <form action='./relinventario.php' method='POST'>
                        <div class="d-flex">
                        <div class="col-4">
                            <label class='form-label' for='unidade'>Selecione a unidade:</label>
                            <select class='form-select' name='unidade' id='unidade'>
                            <option value=''>Selecione...</option>
                            <option value='TODOS' <?=($unidadef == 'TODOS')?'selected':''?>>TODOS</option>
                                <?php                      
                                    $sql="SELECT DISTINCT(unidade) FROM bdfrota.tbveiculo WHERE unidade IS NOT NULL AND unidade <> '' ORDER BY unidade;";
                                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));                      

                                    while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                                    $unidade1= $row['unidade'];
                                    $selected = ($unidadef == $unidade1) ? "selected=\"selected\"" : null;

                                    print"<option value='$unidade1' $selected>$unidade1</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        

                        <div class="ms-1 col-4">
                            <label class='form-label' for='statusa'>Status:</label>
                            <select class='form-select' name='statusa' id='statusa'>
                            <option value=''>Selecione...</option>
                            <option value='1' <?=($statusa == '1')?'selected':''?>>ATIVO</option>                 
                            <option value='0' <?=($statusa == '0')?'selected':''?>>INATIVO</option>
                            </select>
                        </div>
                        </div>

                        <div class="mt-1">
                        <button class='btn btn-success' type='submit'>Filtrar</button>
                        </div>            
                    </form>          
                    </div>

                    <?php
                    // Verificar se algum filtro foi aplicado
                    $filtro_aplicado = false;
                    $filtros_texto = array();
                    
                    if (!empty($unidadef)) {
                        $filtro_aplicado = true;
                        $unidade_texto = ($unidadef == 'TODOS') ? 'TODOS' : $unidadef;
                        $filtros_texto[] = "<strong>Unidade:</strong> " . $unidade_texto;
                    }
                    
                    if ($statusa !== '' && $statusa !== null) {
                        $filtro_aplicado = true;
                        $status_texto = ($statusa == '1') ? 'ATIVO' : 'INATIVO';
                        $filtros_texto[] = "<strong>Status:</strong> " . $status_texto;
                    }
                    
                    // Exibir aviso de filtros aplicados
                    if ($filtro_aplicado) {
                        echo "<div class='alert alert-info mt-3' role='alert'>";
                        echo "<i class='fas fa-filter me-2'></i><strong>Filtros Aplicados:</strong><br>";
                        echo implode(' | ', $filtros_texto);
                        echo "</div>";
                    } else {
                        // Exibir aviso para aplicar filtros
                        echo "<div class='alert alert-warning mt-3' role='alert'>";
                        echo "<i class='fas fa-info-circle me-2'></i><strong>Atenção:</strong> Selecione os filtros desejados e clique em <strong>Filtrar</strong> para visualizar os veículos.";
                        echo "</div>";
                    }
                    ?>

                    <?php if ($filtro_aplicado): ?>
                    <!--tabela para entrada de dados-->
                    <table id='datatablesSimple' class='mt-4 table table-striped'>
                        <thead class="mt-2">
                            <tr>
                                <th>Editar</th>
                                <th>Placa</th>
                                <th>Condutor</th>
                                <th>Matricula</th>
                                <th>CPF</th>
                                <th>Status Veículo</th>
                                <th>Centro de Custo</th>
                                <th>Status</th>

                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Hodometro</th>
                                <th>Propriedade</th>               
                                
                                <th>Oficina</th>
                                
                                <th>CNH</th>
                                <th>Vencimento CNH</th>
                                <th>Dias p/ Vencimento</th>
                                
                                <th>Cargo</th>
                                <th>Data Entrega</th>
                                <th>Data Devolução</th>
                                <th>Tipo Frota</th>
                                <th>Unidade</th>
                                
                                <th>Ano Fabricação</th>
                                <th>Ano Modelo</th>
                                <th>GPS</th>
                                <th>TAG</th>
                                <th>Locadora</th>
                                <th>Data Disp Locadora</th>
                                <th>Data Dev Locadora</th>
                                <th>Valor Aquisição</th>                
                                
                                <th>Data Últ. Vistoria</th>

                                <th>Últ. Movimentação</th>
                                <th>Data Últ. Movimentação</th>
                                <th>Realizada Por</th>

                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php     
                            //$sql="SELECT * FROM bdfrota.tbveiculo WHERE idtbveiculo<>'1241';";
                            //!empty($statusa)
                            
                            // Tratar TODOS como filtro que não filtra por unidade
                            $unidade_filtro = ($unidadef == 'TODOS') ? '' : $unidadef;
                            
                            if($tpossef<>'' && $unidade_filtro<>'' && $statusa<>''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in AND tipoposse='$tpossef' AND unidade='$unidade_filtro' AND status='$statusa' ;";
                            }elseif($tpossef<>'' && $unidade_filtro<>'' && $statusa==''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in AND tipoposse='$tpossef' AND unidade='$unidade_filtro' ;";
                            }elseif($tpossef<>'' && $unidade_filtro=='' && $statusa==''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in AND tipoposse='$tpossef';";
                            }elseif($tpossef=='' && $unidade_filtro<>'' && $statusa<>''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in  AND unidade='$unidade_filtro' AND status='$statusa' ;";
                            }elseif($tpossef=='' && $unidade_filtro<>'' && $statusa==''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in  AND unidade='$unidade_filtro'";
                            }elseif($tpossef<>'' && $unidade_filtro=='' && $statusa<>''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in  AND tipoposse='$tpossef' AND status='$statusa';";
                            }elseif($tpossef=='' && $unidade_filtro=='' && $statusa<>''){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in  AND status='$statusa'";
                            }else{
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo NOT IN $in;";
                            }

                            //print "$sql $statusa; $unidadef; $tpossef";


                            /*if(empty($unidadef)){
                                if(empty($tpossef)){
                                    if(empty($statusa)){
                                        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241';";
                                    }else{
                                        $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='$statusa' ";
                                    }
                                
                                }else{

                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND tipoposse='$tpossef'; ";
                                }
                                
                            }else{
                                if(empty($tpossef)){
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND unidade='$unidadef'";
                                }else{
                                $sql="SELECT * FROM bdfrota.tbveiculo WHERE  idtbveiculo<>'1241' and status='1' AND unidade='$unidadef' AND tipoposse='$tpossef' ";
                                }
                                
                            }*/

                            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                            while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                                $idtbveiculo=$row['idtbveiculo'];
                                $aplaca = $row['placa'];
                                $bmarca = $row['marca'];
                                $cmodelo = $row['modelo'];
                                $ahodometro = $row['hodometro'];
                                $atipoposse = $row['tipoposse'];
                                $status = $row['status'];

                                $dstatus = $row['statusvel'];

                                $aoficina = $row['oficina'];
                                $enome = $row['nomecol'];
                                $fnumcnh = $row['numcnh'];
                                $fvalidade = $row['validade'];
                                $dias_vencimento = $row['dias_vencimento'];
                                $amatcond = $row['matcond'];
                                $ecargo = $row['cargo'];
                                $adtentrega = $row['dtentrega'];
                                $adtdevolucao = $row['dtdevolucao'];

                                //$atipovel = $row['tipovel'];
                                $atipovel = $row['tipovel'];
                                //$gnome = utf8_encode($row['nomeund']);
                                $gnome = $row['unidade'];
                                $eccusto = $row['ccusto'];
                                $aanofabr = $row['anofabr'];
                                $aanomodelo = $row['anomodelo'];
                                $agpsemp = $row['gpsemp'];
                                $atagpedagio = $row['tagpedagio'];
                                $hfantasia = $row['idlocador'];
                                
                                $adtdisponivelloc = $row['dtdisponivelloc'];
                                $adtdevolucaoloc = $row['dtdevolucaoloc'];
                                $avalaquisicao = $row['valaquisicao'];
                                $aobsveiculo = $row['obsveiculo'];
                                $adatamovimentacao = $row['datamovimentacao'];

                                if($atagpedagio == '1'){
                                $atagpedagio = 'SIM';
                                } elseif($atagpedagio == '0'){
                                $atagpedagio = 'NÃO';
                                }

                                if($dias_vencimento<0){
                                $style = "style= 'color: red;'";
                                } else {
                                $style = "style= 'color: blue;'";
                                }

                                if($status == '0'){
                                $statusf = 'INATIVO';
                                }elseif($status == '1'){
                                $statusf = 'ATIVO';
                                }

                                $sql1="SELECT status FROM bdfrota.tbastatusvel WHERE  idtbastatusvel ='$dstatus' ";
                                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                                $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                                    $dstatus=$row1['status'];

                                //idtbmarcaveic, marca
                                $sql2="SELECT marca FROM bdfrota.tbmarcaveic WHERE  idtbmarcaveic ='$bmarca' ";
                                $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                                    $bmarca=$row2['marca'];

                                //idtbmodeloveic, modelo, marca
                                $sql3="SELECT modelo FROM bdfrota.tbmodeloveic WHERE  idtbmodeloveic ='$cmodelo' ";
                                $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
                                $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                                    $cmodelo=$row3['modelo'];

                                //nomecol
                                $sql4="SELECT nome, cargo, codfilial, codempresa, cpf, ccusto FROM bdaniel.tbfuncionario WHERE  matricula ='$amatcond' ";
                                $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
                                $row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
                                    $enome=$row4['nome'];
                                    $ecargo= $row4['cargo'];
                                    $codfilial = $row4['codfilial'];
                                    $codempresa  = $row4['codempresa'];
                                    $cpf  = $row4['cpf'];
                                    $ccustofunc= $row4['ccusto'];

                                //uf, categoria, matricula, pontos, consulta, suspensa
                                $sql5="SELECT numcnh,validade,DATEDIFF(validade, CURDATE()) AS dias_vencimento FROM bdfrota.tbcnh WHERE  matricula ='$amatcond' ";
                                $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
                                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                    $fnumcnh=$row5['numcnh'];
                                    $fvalidade= $row5['validade'];
                                    $dias_vencimento= $row5['dias_vencimento'];


                                //$atipovel idtbatipovel, tipo
                                $sql6="SELECT tipo FROM bdfrota.tbatipovel WHERE  idtbatipovel ='$atipovel' ";
                                $resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
                                $row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
                                    $atipovel= $row6['tipo'];

                                //$gnome bdaniel.tbfilial g
                                /*$sql7="SELECT nome FROM bdaniel.tbfilial WHERE  codempresa ='$codempresa' AND codfilial = '$codfilial' ";
                                $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
                                $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
                                    $gnome=$row7['nome'];*/
                    
                                //$hfantasia idtbfornecedor fantasia,
                                $sql8="SELECT fantasia FROM bdfrota.tbfornecedor WHERE  idtbfornecedor ='$hfantasia'";
                                $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
                                $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
                                    $hfantasia= $row8['fantasia'];

                                //ult vistoria
                                $sql9="SELECT MAX(idtbvistoria) AS idtbvistoria FROM bdfrota.tbvistoria WHERE  placa ='$aplaca'";
                                $resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));
                                $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
                                    $maxidtbvistoria= $row9['idtbvistoria'];

                                $sql10="SELECT datavistoria FROM bdfrota.tbvistoria WHERE  idtbvistoria ='$maxidtbvistoria'";
                                $resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
                                $row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
                                    $datavistoria= $row10['datavistoria'];

                                //ult movimentação
                                $sql7="SELECT MAX(idtblog) AS idtblog FROM bdfrota.tblog WHERE placa='$aplaca' AND (tipo='cadastro' OR tipo='edição') ";
                                $resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
                                $row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
                                $idtblog=$row7['idtblog'];

                                $sql8="SELECT acao, mat_autor, data_e_hora FROM bdfrota.tblog WHERE idtblog='$idtblog' ";
                                $resultado8 = mysqli_query($con, $sql8) or die(mysqli_error($con));
                                $row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
                                $acao=$row8['acao'];
                                $autor=$row8['mat_autor'];
                                $datamov=$row8['data_e_hora'];

                                //autor ult movimentacao
                                if($autor=='003535' || $autor=='003427'){
                                    $autor='EQUIPE TI';
                                }else{
                                    $sql9="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$autor'; ";
                                    $resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));
                                    $row9 = mysqli_fetch_array($resultado9, MYSQLI_BOTH);
                                    $autor=$row9['nome'];
                                }

                                /*if(empty($eccusto) || $eccusto=='' || $eccusto==' '){*/
                                    $ccustofinal = $ccustofunc;
                                    
                                /*}else{
                                    $ccustofinal = $eccusto;
                                }*/

                                $fvalidade1 = explode(" ", $fvalidade);
                                $fvalidade2 = explode("-", $fvalidade1[0]);
                                $fvalidadef = $fvalidade2[2]."/".$fvalidade2[1]."/".$fvalidade2[0];
                                if($fvalidade == ''){
                                    $fvalidadef = '';
                                }

                                $adtentrega1 = explode(" ", $adtentrega);
                                $adtentrega2 = explode("-", $adtentrega1[0]);
                                $adtentregaf = $adtentrega2[2]."/".$adtentrega2[1]."/".$adtentrega2[0];
                                if($adtentrega == ''){
                                    $adtentregaf = '';
                                }

                                $adtdisponivelloc1 = explode(" ", $adtdisponivelloc);
                                $adtdisponivelloc2 = explode("-", $adtdisponivelloc1[0]);
                                $adtdisponivellocf = $adtdisponivelloc2[2]."/".$adtdisponivelloc2[1]."/".$adtdisponivelloc2[0];
                                if($adtdisponivelloc == ''){
                                    $adtdisponivellocf = '';
                                }

                                $adtdevolucao1 = explode(" ", $adtdevolucao);
                                $adtdevolucao2 = explode("-", $adtdevolucao1[0]);
                                $adtdevolucaof = $adtdevolucao2[2]."/".$adtdevolucao2[1]."/".$adtdevolucao2[0];
                                if($adtdevolucao == ''){
                                    $adtdevolucaof = '';
                                }

                                $adtdevolucaoloc1 = explode(" ", $adtdevolucaoloc);
                                $adtdevolucaoloc2 = explode("-", $adtdevolucaoloc1[0]);
                                $adtdevolucaolocf = $adtdevolucaoloc2[2]."/".$adtdevolucaoloc2[1]."/".$adtdevolucaoloc2[0];
                                if($adtdevolucaoloc == ''){
                                    $adtdevolucaolocf = '';
                                }//$adatamovimentacao

                                $adatamovimentacao1 = explode(" ", $adatamovimentacao);
                                $adatamovimentacao2 = explode("-", $adatamovimentacao1[0]);
                                $adatamovimentacaof = $adatamovimentacao2[2]."/".$adatamovimentacao2[1]."/".$adatamovimentacao2[0];
                                if($adatamovimentacao == ''){
                                    $adatamovimentacaof = '';
                                }

                                $datavistoria1 = explode(" ", $datavistoria);
                                $datavistoria2 = explode("-", $datavistoria1[0]);
                                $datavistoriaf = $datavistoria2[2]."/".$datavistoria2[1]."/".$datavistoria2[0];
                                if($datavistoria == ''){
                                    $datavistoriaf = '';
                                }

                                $datamov1 = explode(" ", $datamov);
                                $datamov2 = explode("-", $datamov1[0]);
                                $datamovf = $datamov2[2]."/".$datamov2[1]."/".$datamov2[0];
                                
                                if($datamov == ''){
                                    $datamovf = '';
                                }

                                if(strpos($avalaquisicao, ".") !== false){
                                    $avalaquisicao1 = str_replace(".", ",", $avalaquisicao);
                                    $avalaquisicaof = "R$ ".$avalaquisicao1;
                                }else{
                                    $avalaquisicao1 = $avalaquisicao.",00";
                                    $avalaquisicaof = "R$ ".$avalaquisicao1;
                                }

                                if($perfil==4 && empty($npode)){
                                    $editar="
                                    <td>
                                        <form method='post' action='./editarcadveiculo.php' target='_blank'>
                                            <input type='hidden' name='idtbveiculo' id='nome' value='$idtbveiculo'>
                                            <button style='border: none; background-color: transparent;' type='submit' title='Editar Veículo'> <span class='material-symbols-outlined'> edit_note </span> </button>
                                        </form> 
                                    </td>";
                                }else{
                                    $editar="<td> </td>";
                                }


                                if($aplaca != ""){
                                    print "
                                    <tr>
                                        $editar
                                        <td>$aplaca</td>
                                        <td>$enome</td>
                                        <td>$amatcond</td>
                                        <td>$cpf</td>
                                        <td>$dstatus</td>
                                        <td>$ccustofinal</td>
                                        <td>$statusf</td>

                                        <td>$bmarca</td>
                                        <td>$cmodelo</td>
                                        <td>$ahodometro</td>
                                        <td>$atipoposse</td> 
                                        <td>$aoficina</td>
                                        
                                        <td>$fnumcnh</td>
                                        <td>$fvalidadef</td>
                                        <td $style>$dias_vencimento</td>
                                        
                                        <td>$ecargo</td>

                                        <td>$adtentregaf</td>
                                        <td>$adtdevolucaof</td>

                                        <td>$atipovel</td>
                                        <td>$gnome</td>
                                        <td>$aanofabr</td>
                                        <td>$aanomodelo</td>
                                        <td>$agpsemp</td>
                                        <td>$atagpedagio</td>
                                        <td>$hfantasia</td>

                                        <td>$adtdisponivellocf</td>
                                        <td>$adtdevolucaolocf</td>
                                        <td>$avalaquisicaof</td>
                                        
                                        <td>$datavistoriaf $datavistoria1[1]</td>

                                        <td>$acao</td>
                                        <td>$datamovf</td>
                                        <td>$autor</td>

                                        <td>$aobsveiculo</td>
                                    </tr>";
                                }
                            }     
                        ?>                        
                        </tbody>
                    </table>

                    <div class="mb-2">
                        <form action="./control/gerarexcelrelinventario.php" method="post">
                            <?php 
                            print "<input type='hidden' name='unidade' value='$unidadef'>";
                            print "<input type='hidden' name='tp' value='$tpossef'>";
                            print "<input type='hidden' name='statusa' value='$statusa'>
                            <input type='hidden' name='matricula' value='$matricula'>"; ?>
                            <div class="form-group col-md-2">
                                <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
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
    <script src="../src/js/scripts.js"></script>

    <!--script para criar data table-->
    <script>
    //função iniciar data table 
        $(document).ready( function () {
            $('#datatablesSimple').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
                },               

                /*lengthMenu: [
                    [50, 100, 150, -1], [50, 100, 150, 'All']
                ]*/

                columnDefs: [{ 
                    type: 'date-eu',
                    targets: [14,17,18,26,27,29,31],
                }],
            });
        });
    </script>
</html>