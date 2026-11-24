<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diaf = isset($_POST["diaf"]) ? $_POST["diaf"] : '';
$diai = isset($_POST["diai"]) ? $_POST["diai"] : '';
$matsup = isset($_POST["matsupf"]) ? $_POST["matsupf"] : 'ss';


include "conecta.php";
include_once "./func/log.php";
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
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>

    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!--datatable - Versão estável compatível-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    
    <!-- CSS para garantir que a paginação apareça -->
    <style>
        .dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: block !important;
            visibility: visible !important;
            margin: 10px 0;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block !important;
            padding: 6px 12px;
            margin: 0 2px;
            border: 1px solid #ddd;
            background: #fff;
            color: #333;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            color: white !important;
            border-color: #007bff;
        }
        
        /* Força a tabela a ser responsiva */
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Estilo dos badges */
        .badge-origem {
            font-size: 0.75em;
            padding: 0.25rem 0.5rem;
        }
        
        .status-pendente {
            color: #fd7e14;
            font-weight: bold;
            background: rgba(253, 126, 20, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(253, 126, 20, 0.3);
        }
        
        /* Melhoria na tabela */
        #datatablesSimple {
            font-size: 0.875rem;
        }
        
        #datatablesSimple th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        #datatablesSimple td {
            vertical-align: middle;
        }
        
        /* Loading indicator */
        .loading-indicator {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #6c757d;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .d-flex {
                flex-direction: column !important;
            }
            .ms-5 {
                margin-left: 0 !important;
                margin-top: 1rem;
            }
            .table-responsive {
                font-size: 0.75rem;
            }
        }
        
        /* Manual pagination styles */
        .manual-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .manual-pagination button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Filter summary improvements */
        .filter-summary {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #2196f3;
        }
        
        /* Info box improvements */
        .info-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #ffc107;
        }
        
        .info-box code {
            background: rgba(0,0,0,0.1);
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>

</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Relatório Cotas Extra Pendentes </h1>
                
                <!-- ℹ️ EXPLICAÇÃO DO FUNCIONAMENTO -->
                <div class="alert alert-info info-box mb-4">
                    <h5><i class="fas fa-info-circle"></i> <strong>Como funciona a verificação de PENDENTES:</strong></h5>
                    <ul class="mb-0">
                        <li><strong>Pedidos de Técnicos:</strong> Tabela <code>bdfrota.tbpedidostec</code> WHERE <code>flag = 0</code> AND <code>escalonado = 0</code></li>
                        <li><strong>Pedidos de Supervisores:</strong> Tabela <code>bdfrota.tbpedidossupcota</code> WHERE <code>flag = 0</code></li>
                        <li><strong>Status dos Flags:</strong> 
                            <span class="badge bg-warning text-dark">flag = 0</span> Pendente | 
                            <span class="badge bg-success">flag = 2</span> Aprovado | 
                            <span class="badge bg-danger">flag = 1 ou 3</span> Negado
                        </li>
                        <li><strong>Aprovados:</strong> Movidos para <code>bdfrota.tbcotaextraac</code> com <code>dataaceite</code> preenchida</li>
                        <li><strong>Filtros:</strong> Os filtros por Coordenador e Supervisor são aplicados a ambas as tabelas automaticamente</li>
                    </ul>
                </div>

                <form action="./detalhes-cota-extra-pendentes.php" method="post">
                    <div class='d-flex'>
                        <div id="collapseExample4">

                            <h5><b>Coordenador:</b></h5>
                            <div class="input-group">
                                <select class="form-select mb-3" name="coordenador" id="selectcoordenador">
                                    <option value=" "> Selecione o coordenador </option>

                                    <?php
                                    $i = 0;

                                    $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while ($row = mysql_fetch_array($resultado)) {
                                        $idcoord = $row[0];
                                        $nomecoord = $row[1];

                                        $addmatriz = array($idcoord => $i);
                                        foreach ($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        
                                        // Verifica se este coordenador foi selecionado
                                        $selected = (isset($_POST['coordenador']) && $_POST['coordenador'] == $idcoord) ? 'selected' : '';
                                        
                                        print "<option id='$idselect[$idcoord]' value='$idcoord' $selected>$nomecoord</option>";
                                    }

                                    ?>

                                </select>
                            </div>

                        </div>

                        <!-- tipo de relatório-->
                        <div class="ms-5">
                            <h5><b>Supervisor:</b></h5>
                            <div class="input-group">
                                <select class="form-select mb-3" name="matsupf" id="selectsupervisor">
                                    <?php
                                    $matsup_selecionado = isset($_POST['matsupf']) ? $_POST['matsupf'] : 'ss';
                                    ?>
                                    <option value="ss" <?php echo ($matsup_selecionado == 'ss') ? 'selected' : ''; ?>>Selecione o supervisor</option>

                                    <?php
                                    $i = 0;
                                    $idselect;

                                    $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador ORDER BY b.nome;";

                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    while ($row2 = mysql_fetch_array($resultado2)) {
                                        $matsupf = $row2[0];
                                        $idcoord = $row2[1];
                                        $nomesup = $row2[2];

                                        $addmatriz = array($matsup => $i);
                                        foreach ($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        
                                        // Verifica se este supervisor foi selecionado  
                                        $selected = ($matsup_selecionado == $matsupf) ? 'selected' : '';
                                        
                                        print "<option id='$idselect[$idcoord]-$idselect[$matsupf]' value='$matsupf' $selected>$nomesup</option>";
                                    }

                                    ?>

                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o periodo:</b></h5>
                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Início:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md" name="diai" 
                                   value="<?php echo isset($_POST['diai']) ? $_POST['diai'] : ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Fim:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md" name="diaf"
                                   value="<?php echo isset($_POST['diaf']) ? $_POST['diaf'] : ''; ?>">
                        </div>
                    </div>

                    <div class='mt-2' style="width: 50%;">


                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>


                        <!--</form>
    </div>-->
                    </div>
                </form>



                <div class="mt-4">
                    <?php if ($diaf <> '' && $diai <> '') { ?>
                        <div class="alert alert-info filter-summary">
                            <h6><i class="fas fa-filter"></i> <strong>Filtros Aplicados:</strong></h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Período:</strong> 
                                    <?php 
                                    $dataf = explode("-", $diaf);
                                    $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                                    $datai = explode("-", $diai);
                                    $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                                    echo "$datati até $datat";
                                    ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Coordenador:</strong> 
                                    <?php 
                                    if (isset($_POST['coordenador']) && $_POST['coordenador'] != ' ' && $_POST['coordenador'] != '') {
                                        // Buscar nome do coordenador
                                        $sql_coord_nome = "SELECT b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.idtbcoordenador = '{$_POST['coordenador']}'";
                                        $result_coord_nome = mysql_query($sql_coord_nome);
                                        if ($row_coord_nome = mysql_fetch_array($result_coord_nome)) {
                                            echo "<span class='badge bg-primary'>" . $_POST['coordenador'] . " - " . $row_coord_nome[0] . "</span>";
                                        } else {
                                            echo "<span class='badge bg-primary'>" . $_POST['coordenador'] . "</span>";
                                        }
                                    } else {
                                        echo "<span class='badge bg-secondary'>Todos</span>";
                                    }
                                    ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Supervisor:</strong> 
                                    <?php 
                                    if ($matsup == 'ss' || $matsup == '') {
                                        echo "<span class='badge bg-secondary'>Todos</span>";
                                    } else {
                                        // Buscar nome do supervisor
                                        $sql_sup_nome = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.matricula = '$matsup'";
                                        $result_sup_nome = mysql_query($sql_sup_nome);
                                        if ($row_sup_nome = mysql_fetch_array($result_sup_nome)) {
                                            echo "<span class='badge bg-info'>$matsup - " . $row_sup_nome[0] . "</span>";
                                        } else {
                                            echo "<span class='badge bg-info'>$matsup</span>";
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-warning text-dark">Pendentes (flag = 0)</span>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Selecione o período para visualizar as cotas pendentes.</strong>
                        </div>
                    <?php } ?>
                </div>

                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a href="./dashboard.php">
                    </div>
                    <div class="card-body">
                        <?php if ($diaf <> '' && $diai <> '') { ?>
                            <div id="loading-indicator" class="loading-indicator">
                                <i class="fas fa-spinner fa-spin"></i> Carregando dados...
                            </div>
                            <div id="table-container" style="display: none;">
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-table"></i> 
                                        <span id="record-count">Carregando registros...</span>
                                    </small>
                                </div>
                        <?php } ?>
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered table-hover'>
                            <thead>
                                <tr style='font-size:12px;'>
                                    <th>Origem</th>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>C. Custo</th>
                                    <th>Placa</th>
                                    <th>Orc. Semanal</th>
                                    <th>KM Proj.</th>
                                    <th>KM OS</th>
                                    <th>KM GPS</th>
                                    <th>Sld. Cartão</th>
                                    <th>Remanejado</th>
                                    <th>Data e Hora</th>
                                    <th>Justificativa</th>
                                    <th>Valor Pedido</th>
                                    <th>Tipo Cota</th>
                                    <th>Status</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diaf == '' or $diai == '') {

                                    print "
                                    <tr>
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
                                        <td> </td>
                                    
                                    </tr>";
                                } else {
                                    // Construir filtros adicionais
                                    $filtro_coordenador = "";
                                    $filtro_supervisor_tec = "";
                                    $filtro_supervisor_sup = "";
                                    
                                    // Filtro por coordenador para técnicos
                                    if (isset($_POST['coordenador']) && $_POST['coordenador'] != ' ' && $_POST['coordenador'] != '') {
                                        $id_coordenador = $_POST['coordenador'];
                                        $filtro_coordenador = " AND coord.idtbcoordenador = '$id_coordenador'";
                                    }
                                    
                                    if ($matsup == 'ss') {
                                        // Busca TODOS os pedidos pendentes (técnicos + supervisores) com filtro de coordenador
                                        $sql = "
                                            SELECT 
                                                'TECNICO' as origem,
                                                p.matricula, 
                                                COALESCE(s.orcsemanal, 0) as orcsemanal, 
                                                COALESCE(ROUND(s.kmproj,0), 0) as kmproj, 
                                                COALESCE(ROUND(s.kmos,0), 0) as kmos, 
                                                0 as gps, 
                                                COALESCE(s.saldo, 0) as sldcartao, 
                                                COALESCE(s.totalextra, 0) as totalextra, 
                                                p.data, 
                                                p.justificativa, 
                                                p.valor,
                                                'extra' as tipo_cota
                                            FROM bdfrota.tbpedidostec p
                                            LEFT JOIN bdfrota.tbsaldo s ON p.matricula = s.matricula 
                                            LEFT JOIN bdfrota.tbusuario u ON p.matricula = u.matricula
                                            LEFT JOIN bdfrota.tbsupervisor sup ON u.idtbsupervisor = sup.idtbsupervisor
                                            LEFT JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
                                            WHERE p.flag = 0 AND p.escalonado = 0 
                                                AND p.data BETWEEN '$diai 00:00:00' AND '$diaf 23:59:59'
                                                $filtro_coordenador
                                            
                                            UNION ALL
                                            
                                            SELECT 
                                                'SUPERVISOR' as origem,
                                                ps.matricula, 
                                                0 as orcsemanal, 
                                                0 as kmproj, 
                                                0 as kmos, 
                                                0 as gps, 
                                                0 as sldcartao, 
                                                0 as totalextra, 
                                                ps.data, 
                                                ps.justificativa, 
                                                ps.valor,
                                                'extra' as tipo_cota
                                            FROM bdfrota.tbpedidossupcota ps
                                            LEFT JOIN bdfrota.tbsupervisor sup ON ps.matricula = sup.matricula
                                            LEFT JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
                                            WHERE ps.flag = 0 AND ps.data BETWEEN '$diai 00:00:00' AND '$diaf 23:59:59'
                                                $filtro_coordenador
                                            
                                            ORDER BY data DESC
                                            LIMIT 100
                                        ";

                                    } else {
                                        // Busca pedidos pendentes filtrados por supervisor
                                        $sql = "
                                            SELECT 
                                                'TECNICO' as origem,
                                                p.matricula, 
                                                COALESCE(s.orcsemanal, 0) as orcsemanal, 
                                                COALESCE(ROUND(s.kmproj,0), 0) as kmproj, 
                                                COALESCE(ROUND(s.kmos,0), 0) as kmos, 
                                                0 as gps, 
                                                COALESCE(s.saldo, 0) as sldcartao, 
                                                COALESCE(s.totalextra, 0) as totalextra, 
                                                p.data, 
                                                p.justificativa, 
                                                p.valor,
                                                'extra' as tipo_cota
                                            FROM bdfrota.tbpedidostec p
                                            LEFT JOIN bdfrota.tbsaldo s ON p.matricula = s.matricula 
                                            LEFT JOIN bdfrota.tbusuario u ON p.matricula = u.matricula
                                            LEFT JOIN bdfrota.tbsupervisor sup ON u.idtbsupervisor = sup.idtbsupervisor
                                            LEFT JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
                                            WHERE p.flag = 0 AND p.escalonado = 0 
                                                AND sup.matricula = '$matsup'
                                                AND p.data BETWEEN '$diai 00:00:00' AND '$diaf 23:59:59'
                                                $filtro_coordenador
                                            
                                            UNION ALL
                                            
                                            SELECT 
                                                'SUPERVISOR' as origem,
                                                ps.matricula, 
                                                0 as orcsemanal, 
                                                0 as kmproj, 
                                                0 as kmos, 
                                                0 as gps, 
                                                0 as sldcartao, 
                                                0 as totalextra, 
                                                ps.data, 
                                                ps.justificativa, 
                                                ps.valor,
                                                'extra' as tipo_cota
                                            FROM bdfrota.tbpedidossupcota ps
                                            LEFT JOIN bdfrota.tbsupervisor sup ON ps.matricula = sup.matricula
                                            LEFT JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
                                            WHERE ps.flag = 0 AND ps.matricula = '$matsup'
                                                AND ps.data BETWEEN '$diai 00:00:00' AND '$diaf 23:59:59'
                                                $filtro_coordenador
                                            
                                            ORDER BY data DESC
                                            LIMIT 100
                                        ";
                                    }                                //echo $sql;
                                
                                $resultado = mysql_query($sql) or die(mysql_error());
                                $total_registros = mysql_num_rows($resultado);

                                while ($row = mysql_fetch_array($resultado)) {
                                        $origem = $row[0];        // TECNICO ou SUPERVISOR
                                        $mattec = $row[1];        // matricula
                                        $orcsemanal = $row[2];    // orcsemanal
                                        $kmproj = $row[3];        // kmproj
                                        $kmos = $row[4];          // kmos
                                        $gps = $row[5];           // gps
                                        $sldcartao = $row[6];     // sldcartao
                                        //$totalextra = $row[7];    // totalextra
                                        $data = $row[8];          // data
                                        $justificativa = $row[9]; // justificativa
                                        $valor = $row[10];        // valor
                                        $tipo_cota = $row[11];    // tipo_cota

                                        if ($tipo_cota == 1) {
                                            $tipo_cota = 'Emergencial';
                                        } else {
                                            $tipo_cota = "Extra";
                                        }

                                        $data1 = new DateTime("$data");
                                        $datav = $data1->format('d/m/Y H:i:s');

                                        $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $placa = $row2[0];

                                        $sql_kmproj = "SELECT kmorcsem, matricula FROM bdfrota.tbsaldo where matricula = '$mattec' order by idtbsaldo desc limit 1;";
                                        $resultado_kmproj = mysql_query($sql_kmproj) or die(mysql_error());
                                        $row_kmproj = mysql_fetch_array($resultado_kmproj);
                                        $kmproj = $row_kmproj[0];

                                        $sql3 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
                                        $resultado3 = mysql_query($sql3) or die(mysql_error());
                                        $row3 = mysql_fetch_array($resultado3);
                                        $nometec = $row3[0];
                                        $ccusto = utf8_encode($row3[1]);

                                        //remanejamento positivo
                                        $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=1;";
                                        $resultado4 = mysql_query($sql4) or die(mysql_error());
                                        $row4 = mysql_fetch_array($resultado4);
                                        $remanejpositivo = $row4[0];

                                        //remanejamento negativo
                                        $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=0;";
                                        $resultado5 = mysql_query($sql5) or die(mysql_error());
                                        $row5 = mysql_fetch_array($resultado5);
                                        $remanejnegativo = $row5[0];

                                        $sldremanej = $remanejpositivo - $remanejnegativo;

                                        //saldo fundo frota
                                        $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4";
                                        $resultado6 = mysql_query($sql6) or die(mysql_error());
                                        $row6 = mysql_fetch_array($resultado6);
                                        $ffrota = $row6[0];
                                        $ffrota = str_replace(".", ",", $ffrota);                        print "
                        <tr style='font-size:12px;'>
                            <td><span class='badge " . ($origem == 'TECNICO' ? 'bg-primary' : 'bg-warning text-dark') . " badge-origem'>$origem</span></td>
                            <td><strong>$mattec</strong></td>
                            <td>$nometec</td>
                            <td>$ccusto</td>
                            <td><code>$placa</code></td>
                            <td>R$ " . number_format($orcsemanal, 2, ',', '.') . "</td>
                            <td>$kmproj km</td>
                            <td>$kmos km</td>
                            <td>$gps km</td>                
                            <td>R$ " . number_format($sldcartao, 2, ',', '.') . "</td>
                            <td>R$ " . number_format($sldremanej, 2, ',', '.') . "</td>
                            <td><small>$datav</small></td>
                            <td><small>$justificativa</small></td>
                            <td><strong>R$ " . number_format($valor, 2, ',', '.') . "</strong></td>
                            <td><span class='badge bg-light text-dark'>$tipo_cota</span></td>
                            <td><span class='status-pendente'>⏳ Pendente</span></td>
                            <td>
                                <form action='./detalhes-deslocamento.php' method='post'>
                                    <input type='hidden' name='diai2' value='$diai'/>
                                    <input type='hidden' name='diaf2' value='$diaf'/>
                                    <input type='hidden' name='mattec' value='$mattec' />
                                    <input type='hidden' name='matsup' value='$matsup' />
                                    <button type='submit' class='btn btn-sm btn-outline-primary' title='Ver detalhes'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </form> 
                            </td>
                        </tr>";                                }

                                // Exibir contagem de registros
                                echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        document.getElementById('record-count').textContent = 'Total: $total_registros registro(s) encontrado(s)';
                                        document.getElementById('loading-indicator').style.display = 'none';
                                        document.getElementById('table-container').style.display = 'block';
                                    });
                                </script>";
                                
                                //print $sql4;
                                }
                                ?>
                            </tbody>
                        </table>
                        
                        <?php if ($diaf <> '' && $diai <> '') { ?>
                            </div> <!-- fim table-container -->
                        <?php } ?>
                        <!--<form action="./control/gerarexcelcota-extra-aprovadaaprovada.php" method="post">
                    <?php
                    print "<input type='hidden' name='diai' value='$diai'>";
                    print "<input type='hidden' name='diaf' value='$diaf'>";
                    print "<input type='hidden' name='matsupf' value='$matsup'>"; ?>
                    <div class="form-group col-md-2">
                        <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                        <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                    </div>
                </form>-->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<!--script para criar data table-->
<script>
// Aguarda todos os scripts carregarem
window.addEventListener('load', function() {
    console.log('🔍 === DEBUG DATATABLES ===');
    console.log('jQuery:', typeof $);
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable função:', typeof $.fn.DataTable);
    console.log('DataTable disponível:', typeof $.fn.dataTable);
    console.log('Tabela existe:', $('#datatablesSimple').length);
    
    // Aguarda um pouco mais para garantir carregamento
    setTimeout(function() {
        console.log('⏰ Tentando inicializar DataTables após delay...');
        
        if (typeof $.fn.DataTable === 'undefined' && typeof $.fn.dataTable === 'undefined') {
            console.error('❌ DataTables não carregou! Tentando carregar manualmente...');
            
            // Fallback: Carrega DataTables manualmente
            var script = document.createElement('script');
            script.src = 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js';
            script.onload = function() {
                console.log('✅ DataTables carregado manualmente!');
                initializeDataTable();
            };
            script.onerror = function() {
                console.error('❌ Falha ao carregar DataTables manualmente!');
                implementManualPagination();
            };
            document.head.appendChild(script);
        } else {
            console.log('✅ DataTables disponível, inicializando...');
            initializeDataTable();
        }
    }, 1500);
});

function initializeDataTable() {
    try {
        console.log('🚀 Inicializando DataTables...');
        
        // Verifica se a tabela tem dados antes de inicializar
        var tableRows = $('#datatablesSimple tbody tr').length;
        var hasNoDataRow = $('#datatablesSimple tbody td').first().text().includes('Sem Dados');
        
        if (tableRows === 0 || hasNoDataRow) {
            console.log('📝 Tabela sem dados válidos, não inicializando DataTables');
            return;
        }
        
        var table = $('#datatablesSimple').DataTable({
            "destroy": true,
            "paging": true,
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthChange": true,
            "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, "Todos"]],
            "scrollX": true,
            "order": [[11, 'desc']], // Ordena por data
            "columnDefs": [
                { "width": "80px", "targets": 0 }, // Origem
                { "width": "100px", "targets": 1 }, // Matrícula
                { "width": "200px", "targets": 2 }, // Nome
                { "orderable": false, "targets": -1 } // Última coluna (ações)
            ],
            "language": {
                "emptyTable": "Nenhum registro encontrado",
                "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 até 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "search": "🔍 Buscar:",
                "zeroRecords": "Nenhum registro encontrado com os critérios de busca",
                "processing": "⏳ Processando...",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                }
            },
            "drawCallback": function(settings) {
                console.log('🎨 Tabela redesenhada - Registros visíveis:', this.api().rows({page:'current'}).count());
                
                // Atualiza contador de registros
                var info = this.api().page.info();
                var recordCountElement = document.getElementById('record-count');
                if (recordCountElement) {
                    recordCountElement.innerHTML = 'Total: <strong>' + info.recordsTotal + '</strong> registro(s) | ' +
                                                 'Exibindo: <strong>' + info.recordsDisplay + '</strong> registro(s)';
                }
            },
            "initComplete": function(settings, json) {
                console.log('✅ DataTables inicializado com sucesso!');
                console.log('📊 Total de registros:', this.api().rows().count());
                console.log('📄 Registros por página:', this.api().page.len());
                
                // Force mostrar controles
                $('.dataTables_wrapper').css('display', 'block');
                $('.dataTables_paginate').css('display', 'block');
                $('.dataTables_info').css('display', 'block');
                $('.dataTables_filter').css('display', 'block');
                
                // Esconde loading e mostra tabela
                $('#loading-indicator').hide();
                $('#table-container').show();
            }
        });
        
        console.log('🎯 Instância DataTables criada:', table);
        
    } catch(error) {
        console.error('❌ ERRO ao inicializar DataTables:', error);
        console.error('Stack trace:', error.stack);
        
        // Fallback: Implementação manual simples
        implementManualPagination();
    }
}

function implementManualPagination() {
    console.log('🔧 Implementando paginação manual...');
    
    var rows = $('#datatablesSimple tbody tr');
    var totalRows = rows.length;
    var rowsPerPage = 10;
    var currentPage = 1;
    var totalPages = Math.ceil(totalRows / rowsPerPage);
    
    // Verifica se tem dados válidos
    var hasNoDataRow = rows.first().find('td').first().text().includes('Sem Dados');
    if (totalRows === 0 || hasNoDataRow) {
        console.log('� Sem dados para paginar');
        $('#loading-indicator').hide();
        $('#table-container').show();
        return;
    }
    
    console.log('�📊 Paginação manual: ' + totalRows + ' registros, ' + totalPages + ' páginas');
    
    function showPage(page) {
        rows.hide();
        var start = (page - 1) * rowsPerPage;
        var end = start + rowsPerPage;
        rows.slice(start, end).show();
        
        // Atualiza info
        var infoText = 'Mostrando <strong>' + (start + 1) + '</strong> até <strong>' + 
                      Math.min(end, totalRows) + '</strong> de <strong>' + totalRows + '</strong> registros';
        $('#manual-info').html(infoText);
        
        // Atualiza record-count
        var recordCountElement = document.getElementById('record-count');
        if (recordCountElement) {
            recordCountElement.innerHTML = 'Total: <strong>' + totalRows + '</strong> registro(s) encontrado(s)';
        }
    }
    
    // Cria controles
    var controls = $('<div class="manual-pagination alert alert-light" style="margin: 20px 0;">' +
        '<div class="d-flex justify-content-between align-items-center flex-wrap">' +
            '<div id="manual-info" class="mb-2"></div>' +
            '<div class="btn-group" role="group">' +
                '<button id="prev-btn" class="btn btn-sm btn-outline-primary" title="Página anterior">' +
                    '<i class="fas fa-chevron-left"></i> Anterior' +
                '</button>' +
                '<button id="page-info" class="btn btn-sm btn-outline-secondary" disabled></button>' +
                '<button id="next-btn" class="btn btn-sm btn-outline-primary" title="Próxima página">' +
                    'Próximo <i class="fas fa-chevron-right"></i>' +
                '</button>' +
            '</div>' +
        '</div>' +
        '</div>');
    
    $('#datatablesSimple').after(controls);
    
    function updateControls() {
        $('#page-info').text('Página ' + currentPage + ' de ' + totalPages);
        $('#prev-btn').prop('disabled', currentPage === 1);
        $('#next-btn').prop('disabled', currentPage === totalPages);
    }
    
    $('#prev-btn').click(function() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
            updateControls();
        }
    });
    
    $('#next-btn').click(function() {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
            updateControls();
        }
    });
    
    // Inicializa
    showPage(1);
    updateControls();
    
    // Esconde loading e mostra tabela
    $('#loading-indicator').hide();
    $('#table-container').show();
    
    console.log('✅ Paginação manual implementada com sucesso!');
}
</script>

</html>