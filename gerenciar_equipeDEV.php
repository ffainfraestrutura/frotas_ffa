<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
        echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil != '3') {
        echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
include "conecta.php";
include "conecta02.php";

include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

// Filtro de centro de custo
$sqlaux = "SELECT ccusto FROM bdaniel.tbfuncionario where matricula = '$matricula'";
$resultadoaux = mysql_query($sqlaux) or die(mysql_error());
$rowaux = mysql_fetch_array($resultadoaux);
$ccusto = $rowaux[0];

// Verifica se o valor de $ccusto contém "IHS"
if (strpos($ccusto, 'IHS') !== false) {
        $ccusto = 'IHS';
}
// Verifica se o valor de $ccusto contém "CLARO"
elseif (strpos($ccusto, 'CLARO') !== false) {
        $ccusto = 'CLARO';
} elseif (strpos($ccusto, 'LIGGA') !== false) {
        $ccusto = 'LIGGA';
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
        <link rel="icon" type="image/png" href="src/images/favicon.png" />
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">

        <!--nav-->
        <?php include "menu.php"; ?>

        <div id="layoutSidenav_content">

                <main style="width: 100%;">

                        <div class="container-fluid px-4" style="width: 100%;">
                                <!-- Arquivo original, servidor 05  -->
                                <h1 class="h1 pt-2 pb-2"> Gerenciar Equipe </h1>



                                <?php if ($perfil == '3'): ?>
                                <!-- Primeira tabela - Coordenadores - Perfil 3 (Gerente)-->
                                <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                                <span>
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        Lista de Coordenadores
                                                </span>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCarousel" onclick="setCarouselSlide(0)">
                                                        <i class="fas fa-plus"></i> Gerenciar Equipe
                                                </button>
                                        </div>
                                        
                                        <div class="card-body">
                                        <!--tabela para coordenadores-->
                                                <table id='datatablesSimple3' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Gerente Atual</th>            
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                $sqlaux = "SELECT ccusto FROM bdaniel.tbfuncionario where matricula = '$matricula'";
                                                                $resultadoaux = mysql_query($sqlaux) or die(mysql_error());
                                                                $rowaux = mysql_fetch_array($resultadoaux);
                                                                $ccusto = $rowaux[0];

                                                                // Verifica se o valor de $ccusto contém "IHS"
                                                                if (strpos($ccusto, 'IHS') !== false) {
                                                                        $ccusto = 'IHS';
                                                                }
                                                                // Verifica se o valor de $ccusto contém "CLARO"
                                                                elseif (strpos($ccusto, 'CLARO') !== false) {
                                                                        $ccusto = 'CLARO';
                                                                } elseif (strpos($ccusto, 'LIGGA') !== false) {
                                                                        $ccusto = 'LIGGA';
                                                                }

                                                                // Busca o ID do gerente logado
                                                                $sql_ger_id = "SELECT idtbgerente FROM bdfrota.tbgerente WHERE matricula = '$matricula'";
                                                                $resultado_ger_id = mysql_query($sql_ger_id) or die(mysql_error());
                                                                $row_ger_id = mysql_fetch_array($resultado_ger_id);
                                                                $id_gerente_logado = $row_ger_id[0];

                                                                // Busca apenas coordenadores que estão sob este gerente
                                                                $sql_coord = "SELECT c.matricula, f.nome, f.cargo, c.idtbgerente FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND f.status = 'Ativo' AND c.idtbgerente = '$id_gerente_logado'";
                                                                
                                                                $resultado_coord = mysql_query($sql_coord) or die (mysql_error());
                                                                while($row_coord = mysql_fetch_array($resultado_coord)){
                                                                        $matricula_coord = $row_coord[0];
                                                                        $nome_coord = $row_coord[1];
                                                                        $cargo_coord = $row_coord[2];
                                                                        $idtbgerente_coord = $row_coord[3];
                                                                        
                                                                        // Buscar nome do gerente atual do coordenador
                                                                        $nomegerente_coord = '';
                                                                        if($idtbgerente_coord) {
                                                                                $sql_ger = "SELECT f.nome FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f WHERE g.matricula = f.matricula AND g.idtbgerente = '$idtbgerente_coord'";
                                                                                $resultado_ger = mysql_query($sql_ger) or die (mysql_error());
                                                                                $row_ger = mysql_fetch_array($resultado_ger);
                                                                                $nomegerente_coord = $row_ger[0] ? $row_ger[0] : 'Sem gerente';
                                                                        } else {
                                                                                $nomegerente_coord = 'Sem gerente';
                                                                        }
                                                                
                                                                print"                        
                                                                <tr>
                                                                <td >$matricula_coord</td>
                                                                <td style='white-space: nowrap;'>$nome_coord</td>
                                                                <td >$nomegerente_coord</td>            
                                                                </tr>
                                                                ";
                                                                
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($perfil == '2' || $perfil == '3'): ?>
                                <!-- Segunda tabela - Supervisores - Perfil 2 (Coordenador) e Perfil 3 (Gerente)-->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <span>
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        Lista de Supervisores 
                                                </span>
                                        </div>

                                        <div class="card-body">
                                                <!--tabela para supervisores-->
                                                <table id='datatablesSimple2' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Coordenador Atual</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                if ($perfil == '2') {
                                                                    // Para coordenadores: busca o ID do coordenador logado
                                                                    $sql_coord_id = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE matricula = '$matricula'";
                                                                    $resultado_coord_id = mysql_query($sql_coord_id) or die(mysql_error());
                                                                    $row_coord_id = mysql_fetch_array($resultado_coord_id);
                                                                    $id_coordenador_logado = $row_coord_id[0];

                                                                    // Busca apenas supervisores que estão sob este coordenador
                                                                    $sql_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE u.matricula = f.matricula AND u.matricula = s.matricula AND u.perfil = 1 AND f.status = 'Ativo' AND s.idtbcoordenador = '$id_coordenador_logado'";
                                                                    
                                                                } elseif ($perfil == '3') {
                                                                    // Para gerentes: busca supervisores apenas dos coordenadores sob sua responsabilidade
                                                                    // Primeiro, busca os IDs dos coordenadores sob responsabilidade do gerente
                                                                    $sql_ger_id = "SELECT idtbgerente FROM bdfrota.tbgerente WHERE matricula = '$matricula'";
                                                                    $resultado_ger_id = mysql_query($sql_ger_id) or die(mysql_error());
                                                                    $row_ger_id = mysql_fetch_array($resultado_ger_id);
                                                                    $id_gerente_logado = $row_ger_id[0];
                                                                    
                                                                    // Busca os IDs dos coordenadores sob este gerente
                                                                    $sql_coords_gerente = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE idtbgerente = '$id_gerente_logado'";
                                                                    $resultado_coords_gerente = mysql_query($sql_coords_gerente) or die(mysql_error());
                                                                    
                                                                    $ids_coordenadores = array();
                                                                    while($row_coord_ger = mysql_fetch_array($resultado_coords_gerente)) {
                                                                        $ids_coordenadores[] = $row_coord_ger[0];
                                                                    }
                                                                    
                                                                    if (!empty($ids_coordenadores)) {
                                                                        $lista_ids = implode(',', $ids_coordenadores);
                                                                        // Busca supervisores que têm como coordenadores os coordenadores do gerente
                                                                        $sql_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE u.matricula = f.matricula AND u.matricula = s.matricula AND u.perfil = 1 AND f.status = 'Ativo' AND s.idtbcoordenador IN ($lista_ids)";
                                                                    } else {
                                                                        // Se não há coordenadores sob o gerente, não mostra supervisores
                                                                        $sql_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE 1=0";
                                                                    }
                                                                }
                                                                
                                                                $resultado_sup = mysql_query($sql_sup) or die (mysql_error());
                                                                while($row_sup = mysql_fetch_array($resultado_sup)){
                                                                        $matricula_sup = $row_sup[0];
                                                                        $nome_sup = $row_sup[1];
                                                                        $cargo_sup = $row_sup[2];
                                                                        $idtbcoordenador_sup = $row_sup[3];
                                                                        
                                                                        // Buscar nome do coordenador atual do supervisor
                                                                        $nomecoordenador_sup = '';
                                                                        if($idtbcoordenador_sup) {
                                                                                // Busca matrícula do coordenador na tbcoord
                                                                                $sql_coord = "SELECT c.matricula FROM bdfrota.tbcoord c WHERE c.idtbcoordenador = '$idtbcoordenador_sup'";
                                                                                $resultado_coord = mysql_query($sql_coord) or die (mysql_error());
                                                                                $row_coord = mysql_fetch_array($resultado_coord);
                                                                                
                                                                                if($row_coord[0]) {
                                                                                        $matricula_coord = $row_coord[0];
                                                                                        // Busca nome na tbfuncionario
                                                                                        $sql_nome = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matricula_coord'";
                                                                                        $resultado_nome = mysql_query($sql_nome) or die (mysql_error());
                                                                                        $row_nome = mysql_fetch_array($resultado_nome);
                                                                                        $nomecoordenador_sup = $row_nome[0] ? $row_nome[0] : 'Sem coordenador';
                                                                                } else {
                                                                                        $nomecoordenador_sup = 'Sem coordenador';
                                                                                }
                                                                        } else {
                                                                                $nomecoordenador_sup = 'Sem coordenador';
                                                                        }
                                                                
                                                                print"                        
                                                                <tr>
                                                                <td >$matricula_sup</td>
                                                                <td style='white-space: nowrap;'>$nome_sup</td>
                                                                <td >$nomecoordenador_sup</td>            
                                                                </tr>
                                                                ";
                                                                
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($perfil == '1' || $perfil == '3'): ?>
                                <!-- Terceira tabela - Técnicos - Perfil 1 (Supervisor) e Perfil 3 (Gerente)-->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <span>
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        Lista de Técnicos 
                                                </span>
                                        </div>

                                        <div class="card-body">
                                                <!--tabela para técnicos-->
                                                <table id='datatablesSimple' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Supervisor Atual</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                if ($perfil == '1') {
                                                                    // Para supervisores: busca o ID do supervisor logado
                                                                    $sql_supervisor_id = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE matricula = '$matricula'";
                                                                    $resultado_supervisor_id = mysql_query($sql_supervisor_id) or die(mysql_error());
                                                                    $row_supervisor_id = mysql_fetch_array($resultado_supervisor_id);
                                                                    $id_supervisor_logado = $row_supervisor_id[0];

                                                                    // Busca apenas técnicos que estão sob este supervisor
                                                                    $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.perfil = 0 AND b.status = 'Ativo' AND a.idtbsupervisor = '$id_supervisor_logado'";
                                                                    
                                                                } elseif ($perfil == '3') {
                                                                    // Para gerentes: busca técnicos apenas dos supervisores sob responsabilidade dos coordenadores do gerente
                                                                    // Primeiro, busca os IDs dos coordenadores sob responsabilidade do gerente
                                                                    $sql_ger_id = "SELECT idtbgerente FROM bdfrota.tbgerente WHERE matricula = '$matricula'";
                                                                    $resultado_ger_id = mysql_query($sql_ger_id) or die(mysql_error());
                                                                    $row_ger_id = mysql_fetch_array($resultado_ger_id);
                                                                    $id_gerente_logado = $row_ger_id[0];
                                                                    
                                                                    // Busca os IDs dos coordenadores sob este gerente
                                                                    $sql_coords_gerente = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE idtbgerente = '$id_gerente_logado'";
                                                                    $resultado_coords_gerente = mysql_query($sql_coords_gerente) or die(mysql_error());
                                                                    
                                                                    $ids_coordenadores = array();
                                                                    while($row_coord_ger = mysql_fetch_array($resultado_coords_gerente)) {
                                                                        $ids_coordenadores[] = $row_coord_ger[0];
                                                                    }
                                                                    
                                                                    if (!empty($ids_coordenadores)) {
                                                                        $lista_ids_coords = implode(',', $ids_coordenadores);
                                                                        
                                                                        // Busca os IDs dos supervisores que têm como coordenadores os coordenadores do gerente
                                                                        $sql_sups_coords = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE idtbcoordenador IN ($lista_ids_coords)";
                                                                        $resultado_sups_coords = mysql_query($sql_sups_coords) or die(mysql_error());
                                                                        
                                                                        $ids_supervisores = array();
                                                                        while($row_sup_coord = mysql_fetch_array($resultado_sups_coords)) {
                                                                            $ids_supervisores[] = $row_sup_coord[0];
                                                                        }
                                                                        
                                                                        if (!empty($ids_supervisores)) {
                                                                            $lista_ids_sups = implode(',', $ids_supervisores);
                                                                            // Busca técnicos que têm como supervisores os supervisores da 2ª tabela
                                                                            $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.perfil = 0 AND b.status = 'Ativo' AND a.idtbsupervisor IN ($lista_ids_sups)";
                                                                        } else {
                                                                            // Se não há supervisores sob os coordenadores do gerente, não mostra técnicos
                                                                            $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE 1=0";
                                                                        }
                                                                    } else {
                                                                        // Se não há coordenadores sob o gerente, não mostra técnicos
                                                                        $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE 1=0";
                                                                    }
                                                                }

                                                                $resultado_tec = mysql_query($sql_tec) or die(mysql_error());
                                                                while ($row_tec = mysql_fetch_array($resultado_tec)) {
                                                                        $matriculatec = $row_tec[0];
                                                                        $nometec = $row_tec[1];
                                                                        $idtbsup = $row_tec[2];

                                                                        // Busca nome do supervisor atual do técnico
                                                                        $sql_sup_nome = "SELECT a.matricula, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.idtbsupervisor = '$idtbsup'";
                                                                        $resultado_sup_nome = mysql_query($sql_sup_nome) or die(mysql_error());
                                                                        $row_sup_nome = mysql_fetch_array($resultado_sup_nome);
                                                                        $nomesup = $row_sup_nome[1] ? $row_sup_nome[1] : 'Sem supervisor';

                                                                        print "
                                                                        <tr>
                                                                            <td>$matriculatec</td>
                                                                            <td style='white-space: nowrap;'>$nometec</td>
                                                                            <td>$nomesup</td>
                                                                        </tr>";
                                                                }

                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>
                                <?php endif; ?>

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

        <script language="javascript">
                //função para formatar valor do input - moeda
                function formatarMoeda(i) {
                        var v = i.value.replace(/\D/g, '');
                        v = (v / 100).toFixed(2) + '';
                        v = v.replace(".", ",");
                        v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                        i.value = v;
                }
        </script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
                crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"
                crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

        <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>



        <style>
        /* Aumentar espaçamento entre busca e tabela nos modais */
        .modal .dataTables_filter {
                margin-bottom: 20px !important;
        }
        
        .modal .dataTables_wrapper .dataTables_filter input {
                margin-bottom: 10px;
        }
        
        /* Espaçamento adicional para todos os controles do DataTables nos modais */
        .modal .dataTables_wrapper .row:first-child {
                margin-bottom: 15px;
        }

        /* Estilos para seletores de destino */
        .carousel-item .form-select {
                border: 2px solid #007bff;
                background-color: #f8f9fa;
        }

        .carousel-item .form-select:focus {
                border-color: #0056b3;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Destacar checkboxes selecionados */
        .table tbody tr:has(input[type="checkbox"]:checked) {
                background-color: #e7f3ff !important;
        }

        /* Melhorar aparência dos indicadores do carousel - Estilo Barra */
        .carousel-indicators button {
                width: 60px;
                height: 4px;
                border-radius: 2px;
                margin: 0 5px;
                background-color: #e9ecef !important;
                border: none !important;
                opacity: 1;
                transition: all 0.3s ease;
        }

        /* Estilizar botões de navegação direta */
        .modal-footer .btn-group .btn {
                border-radius: 20px;
                margin: 0 2px;
                font-size: 0.875rem;
        }

        /* Estilo específico para o botão Início */
        #homeStepBtn {
                background-color: #17a2b8;
                border-color: #17a2b8;
                color: white;
                font-weight: 500;
        }

        #homeStepBtn:hover {
                background-color: #138496;
                border-color: #117a8b;
                color: white;
        }

        #homeStepBtn:focus {
                box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }

        /* Animação para botão de salvar */
        .btn-success.pulse {
                animation: pulse 1s infinite;
        }

        @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
        }

        /* Melhorar aparência dos labels dos checkboxes "Selecionar Todos" */
        #selectAllCoord + label,
        #selectAllSup + label,
        #selectAllTec + label {
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
        }

        /* Destacar linha do cabeçalho com seleção */
        .table-dark th:has(input[type="checkbox"]) {
                background-color: #0056b3 !important;
        }

        /* Estilos para indicador de passo */
        #stepIndicator {
                font-size: 0.9rem;
                background-color: #f8f9fa;
                padding: 8px 12px;
                border-radius: 15px;
                border: 1px solid #dee2e6;
        }

        /* Animação para botão próximo */
        #nextStepBtn {
                transition: all 0.3s ease;
        }

        #nextStepBtn:hover {
                transform: translateX(3px);
        }

        /* Desabilitar navegação manual do carousel */
        .carousel-item {
                transition: none !important;
        }

        .carousel-indicators {
                pointer-events: none;
                opacity: 1;
                position: relative;
                margin-bottom: 20px;
        }

        /* Destacar passo atual nos indicadores - Estilo Barra */
        .carousel-indicators .active {
                background-color: #007bff !important; /* Azul para etapa atual */
                height: 6px;
                transform: scaleY(1.5);
        }

        /* Estilos para indicadores completados - Estilo Barra */
        .carousel-indicators .completed {
                background-color: #28a745 !important; /* Verde para etapas completadas */
                height: 5px;
        }
        </style>

        <!-- Modal Carousel com Todas as Tabelas -->
        <div class="modal fade" id="modalCarousel" tabindex="-1" aria-labelledby="modalCarouselLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                                <div class="modal-header">
                                        <h5 class="modal-title" id="modalCarouselLabel">
                                                <i class="fas fa-users-cog me-2"></i>
                                                <span id="carouselTitle">Gerenciar Equipe</span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                        <!-- Carousel -->
                                        <div id="teamCarousel" class="carousel slide" data-bs-ride="false">
                                                <!-- Indicadores -->
                                                <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="0" class="active"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="1"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="2"></button>
                                                </div>

                                                <!-- Slides -->
                                                <div class="carousel-inner">
                                                        <!-- Slide 1: Coordenadores -->
                                                        <div class="carousel-item active">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-user-tie me-2"></i>Lista de Coordenadores
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Gerente Destino -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-8">
                                                                                        <div class="alert alert-info">
                                                                                                <strong><i class="fas fa-info-circle me-2"></i>Gerente Destino:</strong> 
                                                                                                <?php echo $nome; ?> (Matrícula: <?php echo $matricula; ?>)
                                                                                                <input type="hidden" id="gerenteDestino" value="<?php 
                                                                                                        // Buscar ID do gerente logado
                                                                                                        $sql_gerente_logado = "SELECT idtbgerente FROM bdfrota.tbgerente WHERE matricula = '$matricula'";
                                                                                                        $resultado_gerente_logado = mysql_query($sql_gerente_logado);
                                                                                                        $row_gerente_logado = mysql_fetch_array($resultado_gerente_logado);
                                                                                                        echo $row_gerente_logado[0];
                                                                                                ?>">
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione <strong>1 coordenador</strong> para sua equipe
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaCoordenadores" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Centro de Custo</th>
                                                                                                        <th>Gerente Atual</th>
                                                                                                        <th>Selecionar</th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca coordenadores ativos priorizando mesmo centro de custo
                                                                                                $sql_todos_coord = "SELECT c.matricula, f.nome, f.cargo, c.idtbgerente, c.idtbcoordenador, f.ccusto 
                                                                                                FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f 
                                                                                                WHERE c.matricula = f.matricula 
                                                                                                AND f.status = 'Ativo' 
                                                                                                ORDER BY 
                                                                                                    CASE 
                                                                                                        WHEN f.ccusto LIKE '%$ccusto%' THEN 0 
                                                                                                        ELSE 1 
                                                                                                    END, 
                                                                                                    f.nome ASC";
                                                                                                
                                                                                                $resultado_todos_coord = mysql_query($sql_todos_coord) or die (mysql_error());
                                                                                                $total_coord = mysql_num_rows($resultado_todos_coord);
                                                                                                
                                                                                                if($total_coord > 0) {
                                                                                                    while($row_todos_coord = mysql_fetch_array($resultado_todos_coord)){
                                                                                                        $matricula_coord_modal = $row_todos_coord[0];
                                                                                                        $nome_coord_modal = $row_todos_coord[1];
                                                                                                        $cargo_coord_modal = $row_todos_coord[2];
                                                                                                        $idtbgerente_coord_modal = $row_todos_coord[3];
                                                                                                        $idtbcoordenador_modal = $row_todos_coord[4];
                                                                                                        $ccusto_coord_modal = $row_todos_coord[5];
                                                                                                        
                                                                                                        // Buscar nome do gerente atual do coordenador
                                                                                                        $nomegerente_coord_modal = '';
                                                                                                        if($idtbgerente_coord_modal) {
                                                                                                                $sql_ger_modal = "SELECT f.nome FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f WHERE g.matricula = f.matricula AND g.idtbgerente = '$idtbgerente_coord_modal'";
                                                                                                                $resultado_ger_modal = mysql_query($sql_ger_modal) or die (mysql_error());
                                                                                                                $row_ger_modal = mysql_fetch_array($resultado_ger_modal);
                                                                                                                $nomegerente_coord_modal = $row_ger_modal[0] ? $row_ger_modal[0] : 'Sem gerente';
                                                                                                        } else {
                                                                                                                $nomegerente_coord_modal = 'Sem gerente';
                                                                                                        }
                                                                                                
                                                                                                        print"                        
                                                                                                        <tr>
                                                                                                        <td>$matricula_coord_modal</td>
                                                                                                        <td style='white-space: nowrap;'>$nome_coord_modal</td>
                                                                                                        <td>$ccusto_coord_modal</td>
                                                                                                        <td>$nomegerente_coord_modal</td>
                                                                                                        <td>
                                                                                                            <input type='radio' value='$matricula_coord_modal' name='coordenador_selecionado' data-id='$idtbcoordenador_modal' data-matricula='$matricula_coord_modal'>
                                                                                                        </td>         
                                                                                                        </tr>
                                                                                                        ";
                                                                                                    }
                                                                                                } else {
                                                                                                    print"
                                                                                                    <tr>
                                                                                                        <td colspan='4' class='text-center text-muted py-4'>
                                                                                                            <i class='fas fa-inbox fa-2x mb-2'></i>
                                                                                                            <br>Nenhum coordenador encontrado
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    ";
                                                                                                }
                                                                                                ?>
                                                                                        </tbody>
                                                                                </table>
                                                                        </div>
                                                                </div>
                                                        </div>

                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-user-tie me-2"></i>Lista de Supervisores
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Coordenador Destino -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-8">
                                                                                        <div class="alert alert-success">
                                                                                                <strong><i class="fas fa-user-tie me-2"></i>Coordenador Selecionado:</strong> 
                                                                                                <span id="coordenadorSelecionadoNome">Nenhum coordenador selecionado</span>
                                                                                                <input type="hidden" id="coordenadorDestino" value="">
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione <strong>1 supervisor</strong> para este coordenador
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaSupervisores" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Centro de Custo</th>
                                                                                                        <th>Coordenador Atual</th>
                                                                                                        <th>Selecionar</th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca supervisores ativos priorizando mesmo centro de custo
                                                                                                $sql_todos_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador, s.idtbsupervisor, f.ccusto 
                                                                                                FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s 
                                                                                                WHERE u.matricula = f.matricula 
                                                                                                AND u.matricula = s.matricula 
                                                                                                AND u.perfil = 1 
                                                                                                AND f.status = 'Ativo' 
                                                                                                ORDER BY 
                                                                                                    CASE 
                                                                                                        WHEN f.ccusto LIKE '%$ccusto%' THEN 0 
                                                                                                        ELSE 1 
                                                                                                    END, 
                                                                                                    f.nome ASC";
                                                                                                
                                                                                                $resultado_todos_sup = mysql_query($sql_todos_sup) or die (mysql_error());
                                                                                                $total_sup = mysql_num_rows($resultado_todos_sup);
                                                                                                
                                                                                                if($total_sup > 0) {
                                                                                                    while($row_todos_sup = mysql_fetch_array($resultado_todos_sup)){
                                                                                                        $matricula_sup_modal = $row_todos_sup[0];
                                                                                                        $nome_sup_modal = $row_todos_sup[1];
                                                                                                        $cargo_sup_modal = $row_todos_sup[2];
                                                                                                        $idtbcoordenador_sup_modal = $row_todos_sup[3];
                                                                                                        $idtbsupervisor_modal = $row_todos_sup[4];
                                                                                                        $ccusto_sup_modal = $row_todos_sup[5];
                                                                                                        
                                                                                                        // Buscar nome do coordenador atual do supervisor
                                                                                                        $nomecoordenador_sup_modal = '';
                                                                                                        if($idtbcoordenador_sup_modal) {
                                                                                                                // Busca matrícula do coordenador na tbcoord
                                                                                                                $sql_coord_modal = "SELECT c.matricula FROM bdfrota.tbcoord c WHERE c.idtbcoordenador = '$idtbcoordenador_sup_modal'";
                                                                                                                $resultado_coord_modal = mysql_query($sql_coord_modal) or die (mysql_error());
                                                                                                                $row_coord_modal = mysql_fetch_array($resultado_coord_modal);
                                                                                                                
                                                                                                                if($row_coord_modal[0]) {
                                                                                                                        $matricula_coord_modal = $row_coord_modal[0];
                                                                                                                        // Busca nome na tbfuncionario
                                                                                                                        $sql_nome_modal = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matricula_coord_modal'";
                                                                                                                        $resultado_nome_modal = mysql_query($sql_nome_modal) or die (mysql_error());
                                                                                                                        $row_nome_modal = mysql_fetch_array($resultado_nome_modal);
                                                                                                                        $nomecoordenador_sup_modal = $row_nome_modal[0] ? $row_nome_modal[0] : 'Sem coordenador';
                                                                                                                } else {
                                                                                                                        $nomecoordenador_sup_modal = 'Sem coordenador';
                                                                                                                }
                                                                                                        } else {
                                                                                                                $nomecoordenador_sup_modal = 'Sem coordenador';
                                                                                                        }
                                                                                                
                                                                                                        print"                        
                                                                                                        <tr>
                                                                                                        <td>$matricula_sup_modal</td>
                                                                                                        <td style='white-space: nowrap;'>$nome_sup_modal</td>
                                                                                                        <td>$ccusto_sup_modal</td>
                                                                                                        <td>$nomecoordenador_sup_modal</td>
                                                                                                        <td>
                                                                                                            <input type='radio' value='$matricula_sup_modal' name='supervisor_selecionado' data-id='$idtbsupervisor_modal' data-matricula='$matricula_sup_modal'>
                                                                                                        </td>         
                                                                                                        </tr>
                                                                                                        ";
                                                                                                    }
                                                                                                } else {
                                                                                                    print"
                                                                                                    <tr>
                                                                                                        <td colspan='4' class='text-center text-muted py-4'>
                                                                                                            <i class='fas fa-inbox fa-2x mb-2'></i>
                                                                                                            <br>Nenhum supervisor encontrado
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    ";
                                                                                                }
                                                                                                ?>
                                                                                        </tbody>
                                                                                </table>
                                                                        </div>
                                                                </div>
                                                        </div>

                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-user-tie me-2"></i>Lista de Técnicos
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Supervisor Destino -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-8">
                                                                                        <div class="alert alert-warning">
                                                                                                <strong><i class="fas fa-user-friends me-2"></i>Supervisor Selecionado:</strong> 
                                                                                                <span id="supervisorSelecionadoNome">Nenhum supervisor selecionado</span>
                                                                                                <input type="hidden" id="supervisorDestino" value="">
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione os <strong>técnicos</strong> para este supervisor
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaTecnicos" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Centro de Custo</th>
                                                                                                        <th>Supervisor Atual</th>
                                                                                                        <th>
                                                                                                                <input type="checkbox" id="selectAllTec" onchange="toggleAllCheckboxes('tabelaTecnicos', this.checked)">
                                                                                                                <label for="selectAllTec" class="text-white ms-1">Selecionar Todos</label>
                                                                                                        </th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca técnicos ativos priorizando mesmo centro de custo
                                                                                                $sql_todos_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor, b.ccusto 
                                                                                                FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b 
                                                                                                WHERE a.matricula = b.matricula 
                                                                                                AND a.perfil = 0 
                                                                                                AND b.status = 'Ativo' 
                                                                                                ORDER BY 
                                                                                                    CASE 
                                                                                                        WHEN b.ccusto LIKE '%$ccusto%' THEN 0 
                                                                                                        ELSE 1 
                                                                                                    END, 
                                                                                                    a.nome ASC";

                                                                                                $resultado_todos_tec = mysql_query($sql_todos_tec) or die(mysql_error());
                                                                                                $total_tec = mysql_num_rows($resultado_todos_tec);
                                                                                                
                                                                                                if($total_tec > 0) {
                                                                                                    while ($row_todos_tec = mysql_fetch_array($resultado_todos_tec)) {
                                                                                                        $matriculatec_modal = $row_todos_tec[0];
                                                                                                        $nometec_modal = $row_todos_tec[1];
                                                                                                        $idtbsup_modal = $row_todos_tec[2];
                                                                                                        $ccusto_tec_modal = $row_todos_tec[3];

                                                                                                        // Busca nome do supervisor atual do técnico
                                                                                                        $nomesup_modal = '';
                                                                                                        if($idtbsup_modal) {
                                                                                                            $sql_sup_nome_modal = "SELECT a.matricula, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.idtbsupervisor = '$idtbsup_modal'";
                                                                                                            $resultado_sup_nome_modal = mysql_query($sql_sup_nome_modal) or die(mysql_error());
                                                                                                            $row_sup_nome_modal = mysql_fetch_array($resultado_sup_nome_modal);
                                                                                                            $nomesup_modal = $row_sup_nome_modal[1] ? $row_sup_nome_modal[1] : 'Sem supervisor';
                                                                                                        } else {
                                                                                                            $nomesup_modal = 'Sem supervisor';
                                                                                                        }

                                                                                                        print "
                                                                                                        <tr>
                                                                                                            <td>$matriculatec_modal</td>
                                                                                                            <td style='white-space: nowrap;'>$nometec_modal</td>
                                                                                                            <td>$ccusto_tec_modal</td>
                                                                                                            <td>$nomesup_modal</td>
                                                                                                            <td>
                                                                                                                <input type='checkbox' value='$matriculatec_modal' name='tecnicos[]'>
                                                                                                            </td>
                                                                                                        </tr>";
                                                                                                    }
                                                                                                } else {
                                                                                                    print"
                                                                                                    <tr>
                                                                                                        <td colspan='4' class='text-center text-muted py-4'>
                                                                                                            <i class='fas fa-inbox fa-2x mb-2'></i>
                                                                                                            <br>Nenhum técnico encontrado
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    ";
                                                                                                }
                                                                                                ?>
                                                                                        </tbody>
                                                                                </table>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>

                                                <!-- Controles do Carousel Desabilitados -->
                                                <!-- Navegação será controlada via JavaScript baseado na seleção -->
                                        </div>
                                </div>
                                <div class="modal-footer">
                                        <div class="me-auto">
                                                <span id="stepIndicator" class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        <span id="stepText">Selecione coordenadores para prosseguir</span>
                                                </span>
                                        </div>
                                        <div>
                                                <button type="button" class="btn btn-outline-info me-2" id="homeStepBtn" onclick="goToHome()" title="Voltar ao início">
                                                        <i class="fas fa-home me-1"></i>Início
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary me-2" id="prevStepBtn" onclick="prevStep()" style="display: none;">
                                                        <i class="fas fa-arrow-left me-1"></i>Voltar
                                                </button>
                                                <button type="button" class="btn btn-primary me-2" id="nextStepBtn" onclick="nextStep()" style="display: none;">
                                                        <i class="fas fa-arrow-right me-1"></i>Próximo
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="salvarProgresso()">
                                                        <i class="fas fa-exchange-alt me-1"></i>Transferir Profissionais Selecionados
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>

        <script>
        let currentStep = 0; // 0 = Coordenadores, 1 = Supervisores, 2 = Técnicos
        let coordenadorSelecionado = null;
        let supervisorSelecionado = null;
        
        // Função para definir slide do carousel (sempre inicia em coordenadores)
        function setCarouselSlide(slideIndex) {
                currentStep = 0; // Sempre começar pelos coordenadores
                coordenadorSelecionado = null;
                supervisorSelecionado = null;
                setTimeout(() => {
                        $('#teamCarousel').carousel(0); // Sempre ir para o primeiro slide
                        updateCarouselTitle(0);
                        updateStepIndicator();
                }, 100);
        }

        // Atualizar título baseado no slide atual
        function updateCarouselTitle(slideIndex) {
                const titles = ['Selecione 1 Coordenador', 'Selecione 1 Supervisor', 'Selecione Técnicos'];
                document.getElementById('carouselTitle').textContent = titles[slideIndex] || 'Gerenciar Equipe';
        }

        // Atualizar indicador de passo
        function updateStepIndicator() {
                const stepTexts = [
                        'Escolha 1 coordenador para sua equipe',
                        'Escolha 1 supervisor para o coordenador selecionado',
                        'Escolha os técnicos para o supervisor selecionado'
                ];
                document.getElementById('stepText').textContent = stepTexts[currentStep];
                
                // Atualizar indicadores visuais do carousel
                updateCarouselIndicators();
                
                // Mostrar/esconder botão próximo baseado no passo atual e seleções
                updateNextButton();
        }

        // Função para atualizar os indicadores visuais do carousel
        function updateCarouselIndicators() {
                const indicators = document.querySelectorAll('.carousel-indicators button');
                
                indicators.forEach((indicator, index) => {
                        // Remover todas as classes
                        indicator.classList.remove('active', 'completed');
                        
                        if (index < currentStep) {
                                // Passos já completados
                                indicator.classList.add('completed');
                        } else if (index === currentStep) {
                                // Passo atual
                                indicator.classList.add('active');
                        }
                        // Passos futuros ficam com estilo padrão (branco)
                });
        }

        // Função para avançar para próximo passo
        function nextStep() {
                if (currentStep === 0 && coordenadorSelecionado) {
                        // Passar para supervisores e atualizar informação do coordenador
                        currentStep = 1;
                        $('#teamCarousel').carousel(1);
                        updateCarouselTitle(1);
                        updateStepIndicator();
                        updateNavigationButtons();
                        
                        // Atualizar nome do coordenador na segunda etapa
                        const coordRadio = document.querySelector('input[name="coordenador_selecionado"]:checked');
                        if (coordRadio) {
                                const coordRow = coordRadio.closest('tr');
                                const coordNome = coordRow.cells[1].textContent;
                                document.getElementById('coordenadorSelecionadoNome').textContent = coordNome;
                                document.getElementById('coordenadorDestino').value = coordenadorSelecionado;
                        }
                        
                } else if (currentStep === 1 && supervisorSelecionado) {
                        // Passar para técnicos e atualizar informação do supervisor
                        currentStep = 2;
                        $('#teamCarousel').carousel(2);
                        updateCarouselTitle(2);
                        updateStepIndicator();
                        updateNavigationButtons();
                        
                        // Atualizar nome do supervisor na terceira etapa
                        const supRadio = document.querySelector('input[name="supervisor_selecionado"]:checked');
                        if (supRadio) {
                                const supRow = supRadio.closest('tr');
                                const supNome = supRow.cells[1].textContent;
                                document.getElementById('supervisorSelecionadoNome').textContent = supNome;
                                document.getElementById('supervisorDestino').value = supervisorSelecionado;
                        }
                }
        }

        // Função para voltar ao passo anterior
        function prevStep() {
                if (currentStep === 1) {
                        // Voltar de supervisores para coordenadores
                        currentStep = 0;
                        $('#teamCarousel').carousel(0);
                        updateCarouselTitle(0);
                        updateStepIndicator();
                        updateNavigationButtons();
                } else if (currentStep === 2) {
                        // Voltar de técnicos para supervisores
                        currentStep = 1;
                        $('#teamCarousel').carousel(1);
                        updateCarouselTitle(1);
                        updateStepIndicator();
                        updateNavigationButtons();
                }
        }

        // Atualizar visibilidade dos botões de navegação
        function updateNavigationButtons() {
                const nextBtn = document.getElementById('nextStepBtn');
                const prevBtn = document.getElementById('prevStepBtn');
                
                // Botão Voltar
                if (currentStep === 0) {
                        // Primeiro passo: esconder voltar
                        prevBtn.style.display = 'none';
                } else {
                        // Outros passos: mostrar voltar
                        prevBtn.style.display = 'inline-block';
                }
                
                // Botão Próximo
                if (currentStep === 0) {
                        // Passo Coordenadores: mostrar se há coordenador selecionado
                        nextBtn.style.display = coordenadorSelecionado ? 'inline-block' : 'none';
                } else if (currentStep === 1) {
                        // Passo Supervisores: mostrar se há supervisor selecionado
                        nextBtn.style.display = supervisorSelecionado ? 'inline-block' : 'none';
                } else {
                        // Passo Técnicos: esconder (último passo)
                        nextBtn.style.display = 'none';
                }
        }

        // Função para voltar sempre ao início (primeira etapa)
        function goToHome() {
                currentStep = 0;
                coordenadorSelecionado = null;
                supervisorSelecionado = null;
                
                // Limpar seleções
                document.querySelectorAll('input[name="coord[]"]').forEach(radio => radio.checked = false);
                document.querySelectorAll('input[name="supervisor[]"]').forEach(radio => radio.checked = false);
                document.querySelectorAll('input[name="tecnicos[]"]').forEach(checkbox => checkbox.checked = false);
                
                // Limpar informações de destino
                document.getElementById('coordenadorSelecionadoNome').textContent = 'Nenhum coordenador selecionado';
                document.getElementById('supervisorSelecionadoNome').textContent = 'Nenhum supervisor selecionado';
                document.getElementById('coordenadorDestino').value = '';
                document.getElementById('supervisorDestino').value = '';
                
                // Voltar ao primeiro slide
                $('#teamCarousel').carousel(0);
                updateCarouselTitle(0);
                updateStepIndicator();
                updateNavigationButtons();
        }

        // Manter função antiga para compatibilidade
        function updateNextButton() {
                updateNavigationButtons();
        }

        // Event listener para mudanças de slide (desabilitado para navegação manual)
        document.getElementById('teamCarousel').addEventListener('slide.bs.carousel', function (event) {
                // Prevenir navegação manual - só permitir via JavaScript
                if (event.to !== currentStep) {
                        event.preventDefault();
                        return false;
                }
                updateCarouselTitle(event.to);
        });

        // Função para transferir profissionais selecionados
        function salvarProgresso() {
                // Verificar se o fluxo foi completado
                if (!coordenadorSelecionado) {
                        alert('Por favor, selecione um coordenador primeiro.');
                        return;
                }
                
                if (!supervisorSelecionado) {
                        alert('Por favor, selecione um supervisor primeiro.');
                        return;
                }

                // Coletar técnicos selecionados
                const tecnicos = [];
                document.querySelectorAll('input[name="tecnicos[]"]:checked').forEach(function(checkbox) {
                        tecnicos.push(checkbox.value);
                });

                if (tecnicos.length === 0) {
                        alert('Por favor, selecione pelo menos um técnico.');
                        return;
                }

                // Mostrar loading
                const btnSalvar = document.querySelector('.btn-success');
                const textoOriginal = btnSalvar.innerHTML;
                btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';
                btnSalvar.disabled = true;

                // Preparar dados para envio
                const dados = {
                        action: 'transferir_equipe',
                        coordenador_selecionado: coordenadorSelecionado,
                        supervisor_selecionado: supervisorSelecionado,
                        tecnicos: tecnicos,
                        gerente_destino: document.getElementById('gerenteDestino').value,
                        coordenador_destino: document.getElementById('coordenadorDestino').value,
                        supervisor_destino: document.getElementById('supervisorDestino').value,
                        sincronizar_servidor2: true  // Flag para sincronizar ambos servidores
                };

                // Fazer requisição AJAX
                $.ajax({
                        url: 'control/gerenciar_equipeDEV.php',
                        type: 'POST',
                        data: dados,
                        dataType: 'json',
                        success: function(response) {
                                if (response.success) {
                                        // Criar mensagem detalhada com contagem de profissionais transferidos
                                        const coordCount = coordenadorSelecionado ? 1 : 0;
                                        const supCount = supervisorSelecionado ? 1 : 0;
                                        const tecCount = tecnicos.length;
                                        
                                        let resumo = [];
                                        if (coordCount > 0) resumo.push(`${coordCount} coordenador`);
                                        if (supCount > 0) resumo.push(`${supCount} supervisor`);
                                        if (tecCount > 0) resumo.push(`${tecCount} técnico${tecCount > 1 ? 's' : ''}`);
                                        
                                        const mensagemDetalhada = resumo.join(', ');
                                        
                                        alert(`Equipe transferida com sucesso!\n\nProfissionais transferidos: ${mensagemDetalhada}`);
                                        $('#modalCarousel').modal('hide');
                                        // Recarregar a página para atualizar as tabelas
                                        location.reload();
                                } else {
                                        alert('Erro ao transferir equipe:\n\n' + response.message);
                                }
                        },
                        error: function(xhr, status, error) {
                                console.error('Erro na requisição:', error);
                                alert('Erro ao processar a solicitação. Tente novamente.');
                        },
                        complete: function() {
                                // Restaurar botão
                                btnSalvar.innerHTML = textoOriginal;
                                btnSalvar.disabled = false;
                        }
                });
        }

        // Função para selecionar/deselecionar todos os checkboxes de uma tabela
        function toggleAllCheckboxes(tableId, checked) {
                document.querySelectorAll('#' + tableId + ' input[type="checkbox"]:not(#selectAllCoord):not(#selectAllSup):not(#selectAllTec)').forEach(function(checkbox) {
                        checkbox.checked = checked;
                });
                updateSelectionCount();
        }

        // Função para atualizar contador de seleções (sem mostrar count no botão)
        function updateSelectionCount() {
                const tecCount = document.querySelectorAll('input[name="tecnicos[]"]:checked').length;
                
                // Atualizar botão de transferência sem mostrar contador
                const btnSalvar = document.querySelector('.btn-success');
                if (coordenadorSelecionado && supervisorSelecionado && tecCount > 0) {
                        btnSalvar.innerHTML = '<i class="fas fa-exchange-alt me-1"></i>Transferir Profissionais Selecionados';
                        btnSalvar.classList.add('btn-success');
                        btnSalvar.classList.remove('btn-secondary');
                } else {
                        btnSalvar.innerHTML = '<i class="fas fa-exchange-alt me-1"></i>Transferir Profissionais Selecionados';
                        btnSalvar.classList.add('btn-secondary');
                        btnSalvar.classList.remove('btn-success');
                }
        }

        // Adicionar event listeners para radio buttons e checkboxes
        $(document).ready(function() {
                // Event listener para seleção de coordenador
                $(document).on('change', 'input[name="coordenador_selecionado"]', function() {
                        if (this.checked) {
                                coordenadorSelecionado = this.dataset.id;
                                updateNextButton();
                        }
                });
                
                // Event listener para seleção de supervisor
                $(document).on('change', 'input[name="supervisor_selecionado"]', function() {
                        if (this.checked) {
                                supervisorSelecionado = this.dataset.id;
                                updateNextButton();
                        }
                });
                
                // Event listener para checkboxes de técnicos
                $(document).on('change', 'input[name="tecnicos[]"]', function() {
                        updateSelectionCount();
                });
                
                // Inicializar estado quando modal abrir
                $('#modalCarousel').on('shown.bs.modal', function () {
                        currentStep = 0;
                        coordenadorSelecionado = null;
                        supervisorSelecionado = null;
                        
                        // Limpar seleções anteriores
                        $('input[name="coordenador_selecionado"]').prop('checked', false);
                        $('input[name="supervisor_selecionado"]').prop('checked', false);
                        $('input[name="tecnicos[]"]').prop('checked', false);
                        
                        $('#teamCarousel').carousel(0);
                        updateCarouselTitle(0);
                        updateStepIndicator();
                        updateNavigationButtons();
                        
                        // Inicializar DataTables para todas as tabelas
                        if (!$.fn.DataTable.isDataTable('#tabelaCoordenadores')) {
                                $('#tabelaCoordenadores').DataTable({
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                        
                        if (!$.fn.DataTable.isDataTable('#tabelaSupervisores')) {
                                $('#tabelaSupervisores').DataTable({
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                        
                        if (!$.fn.DataTable.isDataTable('#tabelaTecnicos')) {
                                $('#tabelaTecnicos').DataTable({
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }                        });
        });
        </script>

</body>

</html>
