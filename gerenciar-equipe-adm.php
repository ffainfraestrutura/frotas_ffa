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
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

// Visualiza funcionários de todos os centros de custo

// Pega o gerente selecionado via GET (se houver)
$gerente_selecionado = isset($_GET['gerente']) ? $_GET['gerente'] : '';

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
        <title>Gerenciar Equipe - FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />

        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

        <!--data tables - css -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
</head>

<body class="sb-nav-fixed vsc-initialized">

        <!--nav-->
        <?php include "menu.php"; ?>

        <div id="layoutSidenav_content">

                <main style="width: 100%;">

                        <div class="container-fluid px-4" style="width: 100%;">
                                <h1 class="h1 pt-2 pb-2"> Visualizar Hierarquia da Frota </h1>

                                <!-- Seletor de Gerente -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-filter me-1"></i>
                                                Selecionar Gerente
                                        </div>
                                        <div class="card-body">
                                                <form method="GET" action="">
                                                        <div class="row">
                                                                <div class="col-md-8">
                                                                        <select name="gerente" class="form-select" required>
                                                                                <option value="">Selecione um gerente...</option>
                                                                                <?php
                                                                                // Busca todos os gerentes (sem filtro de centro de custo)
                                                                                $sql_gerentes = "SELECT g.idtbgerente, g.matricula, f.nome, f.ccusto FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f WHERE g.matricula = f.matricula AND f.status = 'Ativo' ORDER BY f.nome";
                                                                                $resultado_gerentes = mysql_query($sql_gerentes) or die(mysql_error());
                                                                                while($row_gerente = mysql_fetch_array($resultado_gerentes)){
                                                                                        $id_gerente = $row_gerente[0];
                                                                                        $matricula_gerente = $row_gerente[1];
                                                                                        $nome_gerente = $row_gerente[2];
                                                                                        $ccusto_gerente = $row_gerente[3];
                                                                                        $selected = ($gerente_selecionado == $id_gerente) ? 'selected' : '';
                                                                                        echo "<option value='$id_gerente' $selected>$nome_gerente (Mat: $matricula_gerente) - $ccusto_gerente</option>";
                                                                                }
                                                                                ?>
                                                                        </select>
                                                                </div>                                                <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary me-2">
                                                                <i class="fas fa-search"></i> Visualizar Hierarquia
                                                        </button>
                                                        <button type="button" class="btn btn-success me-2" onclick="exportarHierarquiaExcel()">
                                                                <i class="fas fa-file-excel me-2"></i>Exportar Excel
                                                        </button>
                                                        <?php if ($perfil == '3' || $perfil == '4' || $perfil == '5'): ?>
                                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCarousel" onclick="setCarouselSlide(0)" 
                                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" data-bs-html="true"
                                                                title="Gerencie equipes movendo coordenadores, supervisores e técnicos entre forças ou equipes">
                                                                <i class="fas fa-users-cog me-2"></i>Gerenciar Equipe
                                                        </button>
                                                        <?php endif; ?>
                                                </div>
                                                        </div>
                                                </form>
                                        </div>
                                </div>

                                <?php if ($gerente_selecionado != ''): ?>
                                
                                <!-- Primeira tabela - Coordenadores -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-user-tie me-1"></i>
                                                Lista de Coordenadores
                                        </div>
                                        
                                        <div class="card-body">
                                                <table id='tabelaCoordenadores' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Cargo</th>
                                                                        <th>Gerente</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                // Busca apenas coordenadores que estão sob este gerente
                                                                $sql_coord = "SELECT c.matricula, f.nome, f.cargo, c.idtbgerente FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND f.status = 'Ativo' AND c.idtbgerente = '$gerente_selecionado'";
                                                                
                                                                $resultado_coord = mysql_query($sql_coord) or die (mysql_error());
                                                                $total_coords = mysql_num_rows($resultado_coord);
                                                                
                                                                if ($total_coords == 0) {
                                                                        echo "<tr>";
                                                                        echo "<td colspan='4' class='text-center text-muted'>";
                                                                        echo "<i class='fas fa-info-circle me-2'></i>";
                                                                        echo "Não foi encontrado nenhum coordenador para este gerente.";
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                } else {
                                                                        while($row_coord = mysql_fetch_array($resultado_coord)){
                                                                                $matricula_coord = $row_coord[0];
                                                                                $nome_coord = $row_coord[1];
                                                                                $cargo_coord = $row_coord[2];
                                                                                $idtbgerente_coord = $row_coord[3];
                                                                                
                                                                                // Buscar nome do gerente atual do coordenador
                                                                                $sql_ger = "SELECT f.nome FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f WHERE g.matricula = f.matricula AND g.idtbgerente = '$idtbgerente_coord'";
                                                                                $resultado_ger = mysql_query($sql_ger) or die(mysql_error());
                                                                                $row_ger = mysql_fetch_array($resultado_ger);
                                                                                $nome_gerente = $row_ger[0];

                                                                                echo "<tr>";
                                                                                echo "<td>$matricula_coord</td>";
                                                                                echo "<td>$nome_coord</td>";
                                                                                echo "<td>$cargo_coord</td>";
                                                                                echo "<td>$nome_gerente</td>";
                                                                                echo "</tr>";
                                                                        }
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>

                                <!-- Segunda tabela - Supervisores -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-users me-1"></i>
                                                Lista de Supervisores
                                        </div>
                                        
                                        <div class="card-body">
                                                <table id='tabelaSupervisores' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Cargo</th>
                                                                        <th>Coordenador</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                // Busca coordenadores deste gerente para depois buscar supervisores
                                                                $sql_coords_gerente = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE idtbgerente = '$gerente_selecionado'";
                                                                $resultado_coords_gerente = mysql_query($sql_coords_gerente) or die(mysql_error());
                                                                
                                                                $lista_ids = array();
                                                                while($row_coord_id = mysql_fetch_array($resultado_coords_gerente)){
                                                                        $lista_ids[] = $row_coord_id[0];
                                                                }
                                                                
                                                                if (!empty($lista_ids)) {
                                                                        $lista_ids = implode(',', $lista_ids);
                                                                        $sql_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE u.matricula = f.matricula AND u.matricula = s.matricula AND u.perfil = 1 AND f.status = 'Ativo' AND s.idtbcoordenador IN ($lista_ids)";
                                                                } else {
                                                                        $sql_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE 1=0";
                                                                }
                                                                
                                                                $resultado_sup = mysql_query($sql_sup) or die (mysql_error());
                                                                $total_sups = mysql_num_rows($resultado_sup);
                                                                
                                                                if ($total_sups == 0) {
                                                                        echo "<tr>";
                                                                        echo "<td colspan='4' class='text-center text-muted'>";
                                                                        echo "<i class='fas fa-info-circle me-2'></i>";
                                                                        echo "Não foi encontrado nenhum supervisor para este gerente.";
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                } else {
                                                                        while($row_sup = mysql_fetch_array($resultado_sup)){
                                                                                $matricula_sup = $row_sup[0];
                                                                                $nome_sup = $row_sup[1];
                                                                                $cargo_sup = $row_sup[2];
                                                                                $idtbcoordenador_sup = $row_sup[3];
                                                                                
                                                                                // Busca nome do coordenador
                                                                                $sql_coord_nome = "SELECT c.matricula, f.nome FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND c.idtbcoordenador = '$idtbcoordenador_sup'";
                                                                                $resultado_coord_nome = mysql_query($sql_coord_nome) or die(mysql_error());
                                                                                $row_coord_nome = mysql_fetch_array($resultado_coord_nome);
                                                                                $nome_coordenador = $row_coord_nome[1];

                                                                                echo "<tr>";
                                                                                echo "<td>$matricula_sup</td>";
                                                                                echo "<td>$nome_sup</td>";
                                                                                echo "<td>$cargo_sup</td>";
                                                                                echo "<td>$nome_coordenador</td>";
                                                                                echo "</tr>";
                                                                        }
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>

                                <!-- Terceira tabela - Técnicos -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-tools me-1"></i>
                                                Lista de Técnicos
                                        </div>
                                        
                                        <div class="card-body">
                                                <table id='tabelaTecnicos' class='table table-bordered'>
                                                        <thead>
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Supervisor</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                // Busca supervisores dos coordenadores deste gerente
                                                                $sql_coords_gerente2 = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE idtbgerente = '$gerente_selecionado'";
                                                                $resultado_coords_gerente2 = mysql_query($sql_coords_gerente2) or die(mysql_error());
                                                                
                                                                $lista_ids_coords = array();
                                                                while($row_coord_id2 = mysql_fetch_array($resultado_coords_gerente2)){
                                                                        $lista_ids_coords[] = $row_coord_id2[0];
                                                                }
                                                                
                                                                if (!empty($lista_ids_coords)) {
                                                                        $lista_ids_coords = implode(',', $lista_ids_coords);
                                                                        $sql_sups_ids = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE idtbcoordenador IN ($lista_ids_coords)";
                                                                        $resultado_sups_ids = mysql_query($sql_sups_ids) or die(mysql_error());
                                                                        
                                                                        $lista_ids_sups = array();
                                                                        while($row_sup_id = mysql_fetch_array($resultado_sups_ids)){
                                                                                $lista_ids_sups[] = $row_sup_id[0];
                                                                        }
                                                                        
                                                                        if (!empty($lista_ids_sups)) {
                                                                                $lista_ids_sups = implode(',', $lista_ids_sups);
                                                                                $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.perfil = 0 AND b.status = 'Ativo' AND a.idtbsupervisor IN ($lista_ids_sups)";
                                                                        } else {
                                                                                $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE 1=0";
                                                                        }
                                                                } else {
                                                                        $sql_tec = "SELECT a.matricula, a.nome, a.idtbsupervisor FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b WHERE 1=0";
                                                                }
                                                                
                                                                $resultado_tec = mysql_query($sql_tec) or die (mysql_error());
                                                                $total_tecs = mysql_num_rows($resultado_tec);
                                                                $tem_tecnicos = ($total_tecs > 0);
                                                                
                                                                if ($total_tecs == 0) {
                                                                        echo "<tr>";
                                                                        echo "<td colspan='3' class='text-center text-muted'>";
                                                                        echo "<i class='fas fa-info-circle me-2'></i>";
                                                                        echo "Não foi encontrado nenhum técnico para este gerente.";
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                } else {
                                                                        while($row_tec = mysql_fetch_array($resultado_tec)){
                                                                                $matricula_tec = $row_tec[0];
                                                                                $nome_tec = $row_tec[1];
                                                                                $idtbsup = $row_tec[2];
                                                                                
                                                                                // Busca nome do supervisor
                                                                                $sql_sup_nome = "SELECT a.matricula, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b WHERE a.matricula = b.matricula AND a.idtbsupervisor = '$idtbsup'";
                                                                                $resultado_sup_nome = mysql_query($sql_sup_nome) or die(mysql_error());
                                                                                $row_sup_nome = mysql_fetch_array($resultado_sup_nome);
                                                                                $nome_supervisor = $row_sup_nome[1];

                                                                                echo "<tr>";
                                                                                echo "<td>$matricula_tec</td>";
                                                                                echo "<td>$nome_tec</td>";
                                                                                echo "<td>$nome_supervisor</td>";
                                                                                echo "</tr>";
                                                                        }
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>

                                <?php endif; ?>

                        </div>

                </main>

        <?php if ($perfil == '3' || $perfil == '4' || $perfil == '5'): ?>
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
                                        <!-- Primeiro: Seletor de Gerente -->
                                        <div id="teamCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                                                <!-- Indicadores -->
                                                <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="0" class="active"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="1"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="2"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="3"></button>
                                                </div>

                                                <!-- Slides -->
                                                <div class="carousel-inner">
                                                        <!-- Slide 0: Seleção de Gerente -->
                                                        <div class="carousel-item active">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-user-crown me-2"></i>Selecionar Gerente Destino
                                                                        </h6>
                                                                        
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-12">
                                                                                        <div class="alert alert-warning">
                                                                                                <strong><i class="fas fa-info-circle me-2"></i>Instrução:</strong> 
                                                                                                Selecione apenas dois níveis relacionados por vez: gerente e coordenador, coordenador e supervisor, ou supervisor e técnico.
                                                                                        </div>
                                                                                        <label for="gerenteDestinoSelect" class="form-label">Gerente Destino:</label>
                                                                                        <select id="gerenteDestinoSelect" class="form-select">
                                                                                                <option value="">Selecione um gerente...</option>
                                                                                                <?php
                                                                                                // Busca todos os gerentes
                                                                                                $sql_gerentes_modal = "SELECT g.idtbgerente, g.matricula, f.nome, f.ccusto FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f WHERE g.matricula = f.matricula AND f.status = 'Ativo' ORDER BY f.nome";
                                                                                                $resultado_gerentes_modal = mysql_query($sql_gerentes_modal) or die(mysql_error());
                                                                                                while($row_gerente_modal = mysql_fetch_array($resultado_gerentes_modal)){
                                                                                                        $id_gerente_modal = $row_gerente_modal[0];
                                                                                                        $matricula_gerente_modal = $row_gerente_modal[1];
                                                                                                        $nome_gerente_modal = $row_gerente_modal[2];
                                                                                                        $ccusto_gerente_modal = $row_gerente_modal[3];
                                                                                                        echo "<option value='$id_gerente_modal' data-matricula='$matricula_gerente_modal' data-nome='$nome_gerente_modal'>$nome_gerente_modal (Mat: $matricula_gerente_modal) - $ccusto_gerente_modal</option>";
                                                                                                }
                                                                                                ?>
                                                                                        </select>
                                                                                        <input type="hidden" id="gerenteDestino" value="">
                                                                                        <input type="hidden" id="gerenteDestinoNome" value="">
                                                                                        <input type="hidden" id="coordenadorDestino" value="">
                                                                                        <input type="hidden" id="supervisorDestino" value="">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>

                                                        <!-- Slide 1: Coordenadores -->
                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-user-tie me-2"></i>Lista de Coordenadores
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Gerente Destino -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-8">
                                                                                        <div class="alert alert-info">
                                                                                                <strong><i class="fas fa-info-circle me-2"></i>Gerente Destino:</strong> 
                                                                                                <span id="gerenteDestinoInfo">Nenhum gerente selecionado</span>
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione <strong>1 coordenador</strong> somente se também selecionou um gerente ou um supervisor
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaCoordModal" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Gerente Atual</th>
                                                                                                        <th>Selecionar</th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca coordenadores ativos
                                                                                                $sql_todos_coord = "SELECT c.matricula, f.nome, f.cargo, c.idtbgerente, c.idtbcoordenador FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND f.status = 'Ativo' ORDER BY f.nome";
                                                                                                
                                                                                                $resultado_todos_coord = mysql_query($sql_todos_coord) or die (mysql_error());
                                                                                                $total_coord = mysql_num_rows($resultado_todos_coord);
                                                                                                
                                                                                                if($total_coord > 0) {
                                                                                                    while($row_todos_coord = mysql_fetch_array($resultado_todos_coord)){
                                                                                                        $matricula_coord_modal = $row_todos_coord[0];
                                                                                                        $nome_coord_modal = $row_todos_coord[1];
                                                                                                        $cargo_coord_modal = $row_todos_coord[2];
                                                                                                        $idtbgerente_coord_modal = $row_todos_coord[3];
                                                                                                        $idtbcoordenador_modal = $row_todos_coord[4];
                                                                                                        
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
                                                                                                
                                                                                                        echo "                        
                                                                                                        <tr>
                                                                                                        <td>$matricula_coord_modal</td>
                                                                                                        <td style='white-space: nowrap;'>$nome_coord_modal</td>
                                                                                                        <td>$nomegerente_coord_modal</td>
                                                                                                        <td>
                                                                                                            <input type='radio' value='$matricula_coord_modal' name='coordenador_selecionado' data-id='$idtbcoordenador_modal' data-matricula='$matricula_coord_modal' data-nome='$nome_coord_modal'>
                                                                                                        </td>         
                                                                                                        </tr>
                                                                                                        ";
                                                                                                    }
                                                                                                } else {
                                                                                                    echo "
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

                                                        <!-- Slide 2: Supervisores -->
                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-users me-2"></i>Lista de Supervisores
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
                                                                                                Selecione <strong>1 supervisor</strong> somente se também selecionou um coordenador ou um técnico
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaSupModal" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Coordenador Atual</th>
                                                                                                        <th>Selecionar</th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca supervisores ativos
                                                                                                $sql_todos_sup = "SELECT u.matricula, f.nome, f.cargo, s.idtbcoordenador, s.idtbsupervisor FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s WHERE u.matricula = f.matricula AND u.matricula = s.matricula AND u.perfil = 1 AND f.status = 'Ativo' ORDER BY f.nome";
                                                                                                
                                                                                                $resultado_todos_sup = mysql_query($sql_todos_sup) or die (mysql_error());
                                                                                                $total_sup = mysql_num_rows($resultado_todos_sup);
                                                                                                
                                                                                                if($total_sup > 0) {
                                                                                                    while($row_todos_sup = mysql_fetch_array($resultado_todos_sup)){
                                                                                                        $matricula_sup_modal = $row_todos_sup[0];
                                                                                                        $nome_sup_modal = $row_todos_sup[1];
                                                                                                        $cargo_sup_modal = $row_todos_sup[2];
                                                                                                        $idtbcoordenador_sup_modal = $row_todos_sup[3];
                                                                                                        $idtbsupervisor_modal = $row_todos_sup[4];
                                                                                                        
                                                                                                        // Buscar nome do coordenador atual do supervisor
                                                                                                        $nomecoordenador_sup_modal = '';
                                                                                                        if($idtbcoordenador_sup_modal) {
                                                                                                                $sql_coord_sup_modal = "SELECT c.matricula, f.nome FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND c.idtbcoordenador = '$idtbcoordenador_sup_modal'";
                                                                                                                $resultado_coord_sup_modal = mysql_query($sql_coord_sup_modal) or die (mysql_error());
                                                                                                                $row_coord_sup_modal = mysql_fetch_array($resultado_coord_sup_modal);
                                                                                                                $nomecoordenador_sup_modal = $row_coord_sup_modal[1] ? $row_coord_sup_modal[1] : 'Sem coordenador';
                                                                                                        } else {
                                                                                                                $nomecoordenador_sup_modal = 'Sem coordenador';
                                                                                                        }
                                                                                                
                                                                                                        echo "                        
                                                                                                        <tr>
                                                                                                        <td>$matricula_sup_modal</td>
                                                                                                        <td style='white-space: nowrap;'>$nome_sup_modal</td>
                                                                                                        <td>$nomecoordenador_sup_modal</td>                                                                                                    <td>
                                                                                                            <input type='radio' value='$matricula_sup_modal' name='supervisor_selecionado' data-id='$idtbsupervisor_modal' data-matricula='$matricula_sup_modal' data-nome='$nome_sup_modal'>
                                                                                                    </td>
                                                                                                        </tr>
                                                                                                        ";
                                                                                                    }
                                                                                                } else {
                                                                                                    echo "
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

                                                        <!-- Slide 3: Técnicos -->
                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-tools me-2"></i>Lista de Técnicos
                                                                        </h6>
                                                                        
                                                                        <!-- Informação dos Supervisores Selecionados -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-12">
                                                                                        <div class="alert alert-primary">
                                                                                                <strong><i class="fas fa-users me-2"></i>Supervisores Selecionados:</strong> 
                                                                                                <span id="supervisoresSelecionadosInfo">Nenhum supervisor selecionado</span>
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione <strong>1 técnico</strong> somente se também selecionou um supervisor
                                                                                        </p>
                                                                                </div>
                                                                        </div>
                                                                        <div class="table-responsive">
                                                                                <table id="tabelaTecModal" class="table table-bordered table-striped">
                                                                                        <thead class="table-dark">
                                                                                                <tr>
                                                                                                        <th>Matrícula</th>
                                                                                                        <th>Nome</th>
                                                                                                        <th>Supervisor Atual</th>
                                                                                                        <th>Selecionar</th>
                                                                                                </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                                <?php
                                                                                                // Busca técnicos ativos com DISTINCT para evitar duplicatas
                                                                                                $sql_todos_tec = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f WHERE u.matricula = f.matricula AND u.perfil = 0 AND f.status = 'Ativo' ORDER BY f.nome";
                                                                                                
                                                                                                $resultado_todos_tec = mysql_query($sql_todos_tec) or die (mysql_error());
                                                                                                $total_tec = mysql_num_rows($resultado_todos_tec);
                                                                                                
                                                                                                if($total_tec > 0) {
                                                                                                    while($row_todos_tec = mysql_fetch_array($resultado_todos_tec)){
                                                                                                        $matricula_tec_modal = $row_todos_tec[0];
                                                                                                        $nome_tec_modal = $row_todos_tec[1];
                                                                                                        $idtbsupervisor_tec_modal = $row_todos_tec[2];
                                                                                                        
                                                                                                        // Buscar nome do supervisor atual do técnico
                                                                                                        $nomesupervisor_tec_modal = '';
                                                                                                        if($idtbsupervisor_tec_modal) {
                                                                                                                $sql_sup_tec_modal = "SELECT s.matricula, f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$idtbsupervisor_tec_modal'";
                                                                                                                $resultado_sup_tec_modal = mysql_query($sql_sup_tec_modal) or die (mysql_error());
                                                                                                                $row_sup_tec_modal = mysql_fetch_array($resultado_sup_tec_modal);
                                                                                                                $nomesupervisor_tec_modal = $row_sup_tec_modal[1] ? $row_sup_tec_modal[1] : 'Sem supervisor';
                                                                                                        } else {
                                                                                                                $nomesupervisor_tec_modal = 'Sem supervisor';
                                                                                                        }
                                                                                                
                                                                                                        echo "                        
                                                                                                        <tr>
                                                                                                        <td>$matricula_tec_modal</td>
                                                                                                        <td style='white-space: nowrap;'>$nome_tec_modal</td>
                                                                                                        <td>$nomesupervisor_tec_modal</td>
                                                                                                        <td>
                                                                                                            <input type='checkbox' value='$matricula_tec_modal' name='tecnico_selecionado[]' data-matricula='$matricula_tec_modal' data-nome='$nome_tec_modal'>
                                                                                                        </td>         
                                                                                                        </tr>
                                                                                                        ";
                                                                                                    }
                                                                                                } else {
                                                                                                    echo "
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
                                        </div>
                                </div>
                                <div class="modal-footer">
                                        <div class="btn-group me-auto" role="group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="previousSlide()">
                                                        <i class="fas fa-chevron-left"></i> Anterior
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="nextSlide()">
                                                        Próximo <i class="fas fa-chevron-right"></i>
                                                </button>
                                        </div>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success" onclick="salvarEquipe()">
                                                <i class="fas fa-save me-2"></i>Salvar Equipe
                                        </button>
                                </div>                </div>
        </div>
        </div>
        <?php endif; ?>

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
        
        <!-- jQuery primeiro -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- DataTables JS depois do jQuery -->
        <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

        <script>
        // Variável PHP para JavaScript
        var temTecnicos = <?php echo isset($tem_tecnicos) ? ($tem_tecnicos ? 'true' : 'false') : 'false'; ?>;
        
        // Função para exportar hierarquia completa para Excel
        function exportarHierarquiaExcel() {
                // Mostrar loading
                var btnExportar = $('button[onclick="exportarHierarquiaExcel()"]');
                var textoOriginal = btnExportar.html();
                btnExportar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Gerando Excel...');
                
                // Criar formulário temporário para download
                var form = $('<form>', {
                        'method': 'POST',
                        'action': 'control/gerarexcelequipe.php'
                });
                
                // Adicionar campo de ação
                form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'action',
                        'value': 'exportar_hierarquia_completa'
                }));
                
                // Adicionar ao body e submeter
                $('body').append(form);
                form.submit();
                form.remove();
                
                // Restaurar botão após um tempo
                setTimeout(function() {
                        btnExportar.prop('disabled', false).html(textoOriginal);
                }, 3000);
        }
        
        // Inicializar DataTable apenas para a tabela de técnicos
        $(document).ready(function() {
                // Aguardar um pouco para garantir que a página carregou
                setTimeout(function() {
                        // Só inicializar DataTable se há técnicos
                        if (temTecnicos && $('#tabelaTecnicos').length) {
                                $('#tabelaTecnicos').DataTable({
                                        "pageLength": 10,
                                        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                                        "order": [[1, 'asc']],
                                        "searching": true,
                                        "paging": true,
                                        "info": true,
                                        "lengthChange": true,
                                        "language": {
                                                "lengthMenu": "Mostrar _MENU_ registros por página",
                                                "zeroRecords": "Nenhum registro encontrado",
                                                "info": "Mostrando página _PAGE_ de _PAGES_",
                                                "infoEmpty": "Nenhum registro disponível",
                                                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                                                "search": "Buscar:",
                                                "paginate": {
                                                        "first": "Primeiro",
                                                        "last": "Último",
                                                        "next": "Próximo",
                                                        "previous": "Anterior"
                                                }
                                        }
                                });
                        }
                }, 500);
        });

        <?php if ($perfil == '4' || $perfil == '5'): ?>
        // Função para configurar o slide do carousel
        function setCarouselSlide(slideIndex) {
                setTimeout(function() {
                        var carousel = new bootstrap.Carousel(document.getElementById('teamCarousel'));
                        carousel.to(slideIndex);
                        updateCarouselTitle(slideIndex);
                }, 100);
        }
        
        // Função para atualizar o título do carousel
        function updateCarouselTitle(slideIndex) {
                var titles = [
                        'Etapa 1: Selecionar Gerente',
                        'Etapa 2: Selecionar Coordenador', 
                        'Etapa 3: Selecionar Supervisor',
                        'Etapa 4: Selecionar Técnicos'
                ];
                
                if (titles[slideIndex]) {
                        $('#carouselTitle').text(titles[slideIndex]);
                }
        }

        // Navegação do carousel
        function nextSlide() {
                var carousel = new bootstrap.Carousel(document.getElementById('teamCarousel'));
                carousel.next();
        }

        function previousSlide() {
                var carousel = new bootstrap.Carousel(document.getElementById('teamCarousel'));
                carousel.prev();
        }

        // Atualizar informações quando selecionar gerente
        $('#gerenteDestinoSelect').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var gerenteId = selectedOption.val();
                var gerenteNome = selectedOption.data('nome');
                var gerenteMatricula = selectedOption.data('matricula');
                
                $('#gerenteDestino').val(gerenteId);
                $('#gerenteDestinoNome').val(gerenteNome);
                
                if (gerenteId) {
                        $('#gerenteDestinoInfo').text(gerenteNome + ' (Mat: ' + gerenteMatricula + ')');
                } else {
                        $('#gerenteDestinoInfo').text('Nenhum gerente selecionado');
                }
        });

        // Atualizar informações quando selecionar coordenador
        $('input[name="coordenador_selecionado"]').on('change', function() {
                if ($(this).is(':checked')) {
                        var coordenadorNome = $(this).data('nome');
                        var coordenadorId = $(this).data('id');
                        
                        $('#coordenadorSelecionadoNome').text(coordenadorNome);
                        $('#coordenadorDestino').val(coordenadorId);
                }
        });

        // Atualizar informações quando selecionar supervisor
        $('input[name="supervisor_selecionado"]').on('change', function() {
                if ($(this).is(':checked')) {
                        var supervisorNome = $(this).data('nome');
                        var supervisorId = $(this).data('id');
                        
                        $('#supervisoresSelecionadosInfo').text(supervisorNome);
                        $('#supervisorDestino').val(supervisorId);
                } else {
                        $('#supervisoresSelecionadosInfo').text('Nenhum supervisor selecionado');
                }
        });

        // Permitir apenas um técnico para manter a regra de dois colaboradores selecionados
        $('input[name="tecnico_selecionado[]"]').on('change', function() {
                if ($(this).is(':checked')) {
                        $('input[name="tecnico_selecionado[]"]').not(this).prop('checked', false);
                }
        });

        // Função para resetar o modal quando aberto
        function resetModal() {
                // Resetar formulários
                $('#gerenteDestinoSelect').val('');
                $('#gerenteDestino').val('');
                $('#gerenteDestinoNome').val('');
                $('#coordenadorDestino').val('');
                $('#supervisorDestino').val('');
                
                // Resetar checkboxes e radio buttons
                $('input[name="coordenador_selecionado"]').prop('checked', false);
                $('input[name="supervisor_selecionado"]').prop('checked', false);
                $('input[name="tecnico_selecionado[]"]').prop('checked', false);
                
                // Resetar textos informativos
                $('#gerenteDestinoInfo').text('Nenhum gerente selecionado');
                $('#coordenadorSelecionadoNome').text('Nenhum coordenador selecionado');
                $('#supervisoresSelecionadosInfo').text('Nenhum supervisor selecionado');
                
                // Voltar para o primeiro slide
                setCarouselSlide(0);
        }
        
        // Evento quando o modal é aberto
        $('#modalCarousel').on('shown.bs.modal', function() {
                resetModal();
        });
        
        // Evento quando o carousel muda de slide
        $('#teamCarousel').on('slid.bs.carousel', function(e) {
                var activeIndex = $(e.target).find('.carousel-item.active').index();
                updateCarouselTitle(activeIndex);
        });

        // Função para salvar equipe
        function salvarEquipe() {
                var gerenteDestino = $('#gerenteDestino').val();
                var coordenadorSelecionado = $('input[name="coordenador_selecionado"]:checked').data('id');
                var supervisorSelecionado = $('input[name="supervisor_selecionado"]:checked').data('id');
                var tecnicosSelecionados = [];
                
                $('input[name="tecnico_selecionado[]"]:checked').each(function() {
                        tecnicosSelecionados.push($(this).val());
                });
                
                var totalSelecionado = (gerenteDestino ? 1 : 0) + (coordenadorSelecionado ? 1 : 0) + (supervisorSelecionado ? 1 : 0) + tecnicosSelecionados.length;
                var parGerenteCoordenador = gerenteDestino && coordenadorSelecionado && !supervisorSelecionado && tecnicosSelecionados.length === 0;
                var parCoordenadorSupervisor = !gerenteDestino && coordenadorSelecionado && supervisorSelecionado && tecnicosSelecionados.length === 0;
                var parSupervisorTecnico = !gerenteDestino && !coordenadorSelecionado && supervisorSelecionado && tecnicosSelecionados.length === 1;
                
                // Validações: permitir apenas dois colaboradores de hierarquias relacionadas e um grau abaixo
                if (totalSelecionado !== 2 || (!parGerenteCoordenador && !parCoordenadorSupervisor && !parSupervisorTecnico)) {
                        alert('Selecione apenas dois colaboradores de hierarquias relacionadas:\n- Gerente e coordenador;\n- Coordenador e supervisor;\n- Supervisor e técnico.');
                        return;
                }
                
                // Preparar dados para envio
                var dados = {
                        action: 'salvar_equipe',
                        gerente_id: gerenteDestino || '',
                        coordenador_id: coordenadorSelecionado || '',
                        supervisor_id: supervisorSelecionado || '',
                        tecnicos: tecnicosSelecionados
                };
                
                // Mostrar loading
                var btnSalvar = $('button[onclick="salvarEquipe()"]');
                var textoOriginal = btnSalvar.html();

                btnSalvar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
                
                // Enviar via AJAX
                $.ajax({
                        url: 'control/visualizar_frota.php',
                        method: 'POST',
                        data: dados,
                        dataType: 'json',
                        success: function(response) {
                                if (response.success) {
                                        alert('Equipe salva com sucesso!\n\n' + response.message);
                                        $('#modalCarousel').modal('hide');
                                        // Recarregar a página para atualizar as tabelas
                                        location.reload();
                                } else {
                                        alert('Erro ao salvar equipe:\n' + response.message);
                                }
                        },
                        error: function(xhr, status, error) {
                                alert('Erro na comunicação com o servidor:\n' + error);
                                console.error('Erro AJAX:', xhr.responseText);
                        },
                        complete: function() {
                                // Restaurar botão
                                btnSalvar.prop('disabled', false).html(textoOriginal);
                        }                        });
        }
        <?php endif; ?>

        <?php if ($perfil == '4' || $perfil == '5'): ?>
        // Inicializar DataTables nos modais
        setTimeout(function() {
                if (!$.fn.DataTable.isDataTable('#tabelaCoordModal')) {
                        $('#tabelaCoordModal').DataTable({
                                language: {
                                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                }
                        });
                }
                
                if (!$.fn.DataTable.isDataTable('#tabelaSupModal')) {
                        $('#tabelaSupModal').DataTable({
                                language: {
                                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                }
                        });
                }
                
                if (!$.fn.DataTable.isDataTable('#tabelaTecModal')) {
                        $('#tabelaTecModal').DataTable({
                                language: {
                                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                }
                        });
                }
        }, 1000);
        <?php endif; ?>
        
        </script>

        <style>
        /* Estilos simples para paginação da tabela de técnicos */
        #tabelaTecnicos_wrapper .dataTables_filter {
                margin-bottom: 15px;
        }
        
        #tabelaTecnicos_wrapper .dataTables_length {
                margin-bottom: 15px;
        }
        
        #tabelaTecnicos_wrapper .dataTables_info {
                margin-top: 10px;
                color: #6c757d;
        }
        
        #tabelaTecnicos_wrapper .dataTables_paginate {
                margin-top: 10px;
        }
        
        /* Estilos para mensagens de tabelas vazias */
        .text-muted {
                color: #6c757d !important;
                font-style: italic;
                padding: 20px;
        }
        
        .text-muted i {
                color: #28a745;
        }
        
        /* Estilos para o modal de gerenciar equipe */
        .modal .dataTables_filter {
                margin-bottom: 20px !important;
        }
        
        .modal .dataTables_wrapper .dataTables_filter input {
                margin-bottom: 10px;
        }
        
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

        /* Estilos para alertas de informação */
        .carousel-item .alert {
                border-left: 4px solid;
        }

        .carousel-item .alert-info {
                border-left-color: #17a2b8;
        }

        .carousel-item .alert-success {
                border-left-color: #28a745;
        }

        .carousel-item .alert-primary {
                border-left-color: #007bff;
        }

        .carousel-item .alert-warning {
                border-left-color: #ffc107;
        }

        /* Estilos para botões do modal */
        .modal-footer .btn-group .btn {
                min-width: 100px;
        }

        /* Estilo para botão de exportar Excel */
        .btn-success {
                background-color: #28a745;
                border-color: #28a745;
        }

        .btn-success:hover {
                background-color: #218838;
                border-color: #1e7e34;
        }

        .btn-success:focus, .btn-success.focus {
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }

        /* Responsividade para tabelas do modal */
        .modal .table-responsive {
                max-height: 400px;
                overflow-y: auto;
        }

        /* Indicadores do carousel */
        .carousel-indicators button {
                width: 12px;
                height: 12px;
                border-radius: 50%;
        }
        </style>

</body>

</html>