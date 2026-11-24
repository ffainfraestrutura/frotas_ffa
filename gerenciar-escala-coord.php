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
// Apenas coordenadores (perfil 2) podem acessar
if ($perfil != '2') {
        echo "<script language='javascript' type='text/javascript'>alert('Apenas coordenadores têm acesso a esta página');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

// Buscar dados do coordenador logado
$sql_coord_logado = "SELECT c.idtbcoordenador, c.idtbgerente, f.nome, f.ccusto 
                     FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f 
                     WHERE c.matricula = f.matricula 
                     AND c.matricula = '$matricula' 
                     AND f.status = 'Ativo'";
$resultado_coord_logado = mysql_query($sql_coord_logado) or die(mysql_error());

if (mysql_num_rows($resultado_coord_logado) == 0) {
    echo "<script language='javascript' type='text/javascript'>alert('Coordenador não encontrado no sistema');window.location=\"index.php\"</script>";
    exit();
}

$dados_coord = mysql_fetch_array($resultado_coord_logado);
$idtbcoordenador_logado = $dados_coord[0];
$idtbgerente_coord = $dados_coord[1];
$nome_coord_logado = $dados_coord[2];
$ccusto_coord = $dados_coord[3];

// Buscar nome do gerente
$sql_gerente = "SELECT f.nome FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f 
                WHERE g.matricula = f.matricula AND g.idtbgerente = '$idtbgerente_coord'";
$resultado_gerente = mysql_query($sql_gerente) or die(mysql_error());
$row_gerente = mysql_fetch_array($resultado_gerente);
$nome_gerente = $row_gerente[0];

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
        <title>Gerenciar Equipe - Coordenador</title>
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
                                <h1 class="h1 pt-2 pb-2">
                                        <i class="fas fa-users-cog me-2"></i>
                                        Gerenciar Minha Equipe
                                </h1>

                                <!-- Card de Informações do Coordenador -->
                                <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                                <i class="fas fa-user-tie me-1"></i>
                                                Informações do Coordenador
                                        </div>
                                        <div class="card-body">
                                                <div class="row">
                                                        <div class="col-md-4">
                                                                <strong>Nome:</strong> <?php echo $nome_coord_logado; ?>
                                                        </div>
                                                        <div class="col-md-3">
                                                                <strong>Matrícula:</strong> <?php echo $matricula; ?>
                                                        </div>
                                                        <div class="col-md-3">
                                                                <strong>Gerente:</strong> <?php echo $nome_gerente; ?>
                                                        </div>
                                                        <div class="col-md-2">
                                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalGerenciarEquipe">
                                                                        <i class="fas fa-users-cog me-2"></i>Gerenciar Equipe
                                                                </button>
                                                        </div>
                                                </div>
                                        </div>
                                </div>

                                <!-- Tabela de Supervisores -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-users me-1"></i>
                                                Meus Supervisores
                                        </div>
                                        
                                        <div class="card-body">
                                                <table id='tabelaSupervisores' class='table table-bordered table-striped'>
                                                        <thead class="table-dark">
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Cargo</th>
                                                                        <th>Status</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                // Busca supervisores deste coordenador
                                                                $sql_sup = "SELECT u.matricula, f.nome, f.cargo, f.status, s.idtbsupervisor 
                                                                           FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s 
                                                                           WHERE u.matricula = f.matricula 
                                                                           AND u.matricula = s.matricula 
                                                                           AND u.perfil = 1 
                                                                           AND f.status = 'Ativo' 
                                                                           AND s.idtbcoordenador = '$idtbcoordenador_logado'
                                                                           ORDER BY f.nome";
                                                                
                                                                $resultado_sup = mysql_query($sql_sup) or die (mysql_error());
                                                                $total_sups = mysql_num_rows($resultado_sup);
                                                                
                                                                if ($total_sups == 0) {
                                                                        echo "<tr>";
                                                                        echo "<td colspan='4' class='text-center text-muted'>";
                                                                        echo "<i class='fas fa-info-circle me-2'></i>";
                                                                        echo "Você ainda não possui supervisores vinculados.";
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                } else {
                                                                        while($row_sup = mysql_fetch_array($resultado_sup)){
                                                                                $matricula_sup = $row_sup[0];
                                                                                $nome_sup = $row_sup[1];
                                                                                $cargo_sup = $row_sup[2];
                                                                                $status_sup = $row_sup[3];

                                                                                echo "<tr>";
                                                                                echo "<td>$matricula_sup</td>";
                                                                                echo "<td>$nome_sup</td>";
                                                                                echo "<td>$cargo_sup</td>";
                                                                                echo "<td><span class='badge bg-success'>$status_sup</span></td>";
                                                                                echo "</tr>";
                                                                        }
                                                                }
                                                                ?>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>

                                <!-- Tabela de Técnicos -->
                                <div class="card mb-4">
                                        <div class="card-header">
                                                <i class="fas fa-tools me-1"></i>
                                                Meus Técnicos
                                        </div>
                                        
                                        <div class="card-body">
                                                <table id='tabelaTecnicos' class='table table-bordered table-striped'>
                                                        <thead class="table-dark">
                                                                <tr>
                                                                        <th>Matrícula</th>
                                                                        <th>Nome</th>
                                                                        <th>Supervisor</th>
                                                                </tr>
                                                        </thead>

                                                        <tbody>
                                                                <?php
                                                                // Busca supervisores deste coordenador para depois buscar técnicos
                                                                $sql_sups_ids = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE idtbcoordenador = '$idtbcoordenador_logado'";
                                                                $resultado_sups_ids = mysql_query($sql_sups_ids) or die(mysql_error());
                                                                
                                                                $lista_ids_sups = array();
                                                                while($row_sup_id = mysql_fetch_array($resultado_sups_ids)){
                                                                        $lista_ids_sups[] = $row_sup_id[0];
                                                                }
                                                                
                                                                if (!empty($lista_ids_sups)) {
                                                                        $lista_ids_sups = implode(',', $lista_ids_sups);
                                                                        $sql_tec = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor 
                                                                                   FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f 
                                                                                   WHERE u.matricula = f.matricula 
                                                                                   AND u.perfil = 0 
                                                                                   AND f.status = 'Ativo' 
                                                                                   AND u.idtbsupervisor IN ($lista_ids_sups)
                                                                                   ORDER BY f.nome";
                                                                } else {
                                                                        $sql_tec = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f WHERE 1=0";
                                                                }
                                                                
                                                                $resultado_tec = mysql_query($sql_tec) or die (mysql_error());
                                                                $total_tecs = mysql_num_rows($resultado_tec);
                                                                
                                                                if ($total_tecs == 0) {
                                                                        echo "<tr>";
                                                                        echo "<td colspan='3' class='text-center text-muted'>";
                                                                        echo "<i class='fas fa-info-circle me-2'></i>";
                                                                        echo "Não há técnicos vinculados aos seus supervisores.";
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                } else {
                                                                        while($row_tec = mysql_fetch_array($resultado_tec)){
                                                                                $matricula_tec = $row_tec[0];
                                                                                $nome_tec = $row_tec[1];
                                                                                $idtbsup = $row_tec[2];
                                                                                
                                                                                // Busca nome do supervisor
                                                                                $sql_sup_nome = "SELECT s.matricula, f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$idtbsup'";
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

                        </div>

                </main>

        <!-- Modal Gerenciar Equipe -->
        <div class="modal fade" id="modalGerenciarEquipe" tabindex="-1" aria-labelledby="modalGerenciarEquipeLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="modalGerenciarEquipeLabel">
                                                <i class="fas fa-users-cog me-2"></i>
                                                <span id="carouselTitle">Gerenciar Equipe</span>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0">
                                        <div id="teamCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                                                <!-- Indicadores -->
                                                <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="0" class="active"></button>
                                                        <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="1"></button>
                                                </div>

                                                <!-- Slides -->
                                                <div class="carousel-inner">
                                                        <!-- Slide 0: Supervisores -->
                                                        <div class="carousel-item active">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-users me-2"></i>Selecionar Supervisor
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Coordenador -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-12">
                                                                                        <div class="alert alert-info">
                                                                                                <strong><i class="fas fa-user-tie me-2"></i>Coordenador:</strong> 
                                                                                                <?php echo $nome_coord_logado; ?> (Mat: <?php echo $matricula; ?>)
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione <strong>1 supervisor</strong> para vincular técnicos
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
                                                                                                        <td>$nomecoordenador_sup_modal</td>
                                                                                                        <td>
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

                                                        <!-- Slide 1: Técnicos -->
                                                        <div class="carousel-item">
                                                                <div class="container-fluid p-4">
                                                                        <h6 class="mb-3 text-center">
                                                                                <i class="fas fa-tools me-2"></i>Selecionar Técnicos
                                                                        </h6>
                                                                        
                                                                        <!-- Informação do Supervisor Selecionado -->
                                                                        <div class="row mb-3">
                                                                                <div class="col-md-12">
                                                                                        <div class="alert alert-success">
                                                                                                <strong><i class="fas fa-users me-2"></i>Supervisor Selecionado:</strong> 
                                                                                                <span id="supervisorSelecionadoInfo">Nenhum supervisor selecionado</span>
                                                                                        </div>
                                                                                        <p class="text-muted">
                                                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                                                Selecione os <strong>técnicos</strong> para vincular ao supervisor
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
                                                                                                // Busca técnicos ativos com DISTINCT
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
                                </div>
                        </div>
                </div>
        </div>

                <footer class="py-4 bg-light mt-auto">
                        <div class="container-fluid px-4">
                                <div class="d-flex align-items-center justify-content-between small">
                                        <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                                </div>
                        </div>
                </footer>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
                crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"
                crossorigin="anonymous"></script>
        
        <!-- jQuery primeiro -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- DataTables JS depois do jQuery -->
        <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

        <script>
        // Dados do coordenador logado (PHP para JavaScript)
        var coordenadorLogadoId = <?php echo $idtbcoordenador_logado; ?>;
        var coordenadorLogadoNome = "<?php echo $nome_coord_logado; ?>";
        var coordenadorLogadoMatricula = "<?php echo $matricula; ?>";
        
        // Inicializar DataTables
        $(document).ready(function() {
                setTimeout(function() {
                        if ($('#tabelaSupervisores').length) {
                                $('#tabelaSupervisores').DataTable({
                                        pageLength: 10,
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                        
                        if ($('#tabelaTecnicos').length) {
                                $('#tabelaTecnicos').DataTable({
                                        pageLength: 10,
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                        
                        if ($('#tabelaSupModal').length) {
                                $('#tabelaSupModal').DataTable({
                                        pageLength: 10,
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                        
                        if ($('#tabelaTecModal').length) {
                                $('#tabelaTecModal').DataTable({
                                        pageLength: 10,
                                        language: {
                                                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                                        }
                                });
                        }
                }, 500);
        });
        
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
                        'Etapa 1: Selecionar Supervisor', 
                        'Etapa 2: Selecionar Técnicos'
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

        // Atualizar informações quando selecionar supervisor
        $('input[name="supervisor_selecionado"]').on('change', function() {
                if ($(this).is(':checked')) {
                        var supervisorNome = $(this).data('nome');
                        var supervisorId = $(this).data('id');
                        
                        $('#supervisorSelecionadoInfo').text(supervisorNome + ' (Mat: ' + $(this).data('matricula') + ')');
                }
        });

        // Função para resetar o modal quando aberto
        function resetModal() {
                // Resetar checkboxes e radio buttons
                $('input[name="supervisor_selecionado"]').prop('checked', false);
                $('input[name="tecnico_selecionado[]"]').prop('checked', false);
                
                // Resetar textos informativos
                $('#supervisorSelecionadoInfo').text('Nenhum supervisor selecionado');
                
                // Voltar para o primeiro slide
                setCarouselSlide(0);
        }
        
        // Evento quando o modal é aberto
        $('#modalGerenciarEquipe').on('shown.bs.modal', function() {
                resetModal();
        });
        
        // Evento quando o carousel muda de slide
        $('#teamCarousel').on('slid.bs.carousel', function(e) {
                var activeIndex = $(e.target).find('.carousel-item.active').index();
                updateCarouselTitle(activeIndex);
        });

        // Função para salvar equipe
        function salvarEquipe() {
                var supervisorSelecionado = $('input[name="supervisor_selecionado"]:checked').data('id');
                var supervisorMatricula = $('input[name="supervisor_selecionado"]:checked').val();
                var tecnicosSelecionados = [];
                
                $('input[name="tecnico_selecionado[]"]:checked').each(function() {
                        tecnicosSelecionados.push($(this).val());
                });
                
                // Validações
                if (!supervisorSelecionado) {
                        alert('Por favor, selecione um supervisor.');
                        setCarouselSlide(0);
                        return;
                }
                
                if (tecnicosSelecionados.length === 0) {
                        alert('Por favor, selecione pelo menos um técnico.');
                        setCarouselSlide(1);
                        return;
                }
                
                // Confirmação
                if (!confirm('Confirma a vinculação de ' + tecnicosSelecionados.length + ' técnico(s) ao supervisor selecionado?')) {
                        return;
                }
                
                // Preparar dados para envio
                var dados = {
                        action: 'salvar_equipe_coordenador',
                        coordenador_id: coordenadorLogadoId,
                        coordenador_matricula: coordenadorLogadoMatricula,
                        supervisor_id: supervisorSelecionado,
                        supervisor_matricula: supervisorMatricula,
                        tecnicos: tecnicosSelecionados
                };
                
                // Mostrar loading
                var btnSalvar = $('button[onclick="salvarEquipe()"]');
                var textoOriginal = btnSalvar.html();
                btnSalvar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
                
                // Enviar via AJAX
                $.ajax({
                        url: 'control/salvar_equipe_coordenador.php',
                        method: 'POST',
                        data: dados,
                        dataType: 'text', // Alterado para text primeiro
                        success: function(response) {
                                console.log('Resposta do servidor:', response);
                                
                                try {
                                        var jsonResponse = JSON.parse(response);
                                        
                                        if (jsonResponse.success) {
                                                alert('Equipe salva com sucesso!\n\n' + jsonResponse.message);
                                                $('#modalGerenciarEquipe').modal('hide');
                                                // Recarregar a página para atualizar as tabelas
                                                location.reload();
                                        } else {
                                                alert('Erro ao salvar equipe:\n' + jsonResponse.message);
                                                btnSalvar.prop('disabled', false).html(textoOriginal);
                                        }
                                } catch (e) {
                                        console.error('Erro ao parsear JSON:', e);
                                        console.error('Resposta recebida:', response);
                                        alert('Erro ao processar resposta do servidor.\nVerifique o console para mais detalhes.');
                                        btnSalvar.prop('disabled', false).html(textoOriginal);
                                }
                        },
                        error: function(xhr, status, error) {
                                console.error('Status:', status);
                                console.error('Erro:', error);
                                console.error('Resposta completa:', xhr.responseText);
                                alert('Erro na comunicação com o servidor:\n' + error + '\n\nVerifique o console para mais detalhes.');
                                btnSalvar.prop('disabled', false).html(textoOriginal);
                        }
                });
        }
        </script>

        <style>
        /* Estilos para o modal */
        .modal-header.bg-success {
                background-color: #28a745 !important;
        }
        
        .carousel-item .alert {
                border-left: 4px solid;
        }

        .carousel-item .alert-info {
                border-left-color: #17a2b8;
        }

        .carousel-item .alert-success {
                border-left-color: #28a745;
        }

        .modal .table-responsive {
                max-height: 400px;
                overflow-y: auto;
        }

        .carousel-indicators button {
                width: 12px;
                height: 12px;
                border-radius: 50%;
        }
        
        .badge {
                padding: 0.5em 0.75em;
        }
        </style>

</body>

</html>
