<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$hoje = date('Y-m-d');
?>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn" src="src/images/logoredonda.png" width="45" height="45"></a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" href="#!" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <div class="d-none form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
    </div>

    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="./index.php">Logout</a></li>                   
            </ul>
        </li>
    </ul>
</nav>
        
<div id="layoutSidenav"> <!--style="height: 100%;"-->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <?php  
                    if($perfil <>'0'){
                    print"
                    <a class='nav-link' href='#' data-bs-toggle='collapse' data-bs-target='#collapseLayouts' aria-expanded='false' aria-controls='collapseLayouts'>
                            <div class='sb-nav-link-icon'><i class='fas fa-fw fa-chart-area'></i></div>
                            Administrativo
                            <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                        </a>

                        <div class='collapse' id='collapseLayouts' aria-labelledby='headingOne' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='./checklistinicio.php'>Realizar vistoria</a>
                            </nav>
                        </div>

                        <div class='collapse' id='collapseLayouts' aria-labelledby='headingOne' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='gerarrelatorio.php'>Visualizar relatório</a>
                            </nav>
                        </div>";
                    }
                    ?>
                </div>
                <!--<div class="nav">
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                        <div class="sb-nav-link-icon"><i class="fas fa-fw fa-chart-area"></i></div>
                        Listagens
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                   <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemcnh.php">CNHs Cadastradas</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemcondutores.php">Condutores Ativos</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemfiliais.php">Filiais Ativas</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemfornecedores.php">Fornecedores Ativos</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemmanutencao.php">Veículos em Manutenção</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemveiculos.php">Veículos Cadastrados</a>
                        </nav>
                    </div>

                    <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="./listagemmultas.php">Multas</a>
                        </nav>
                    </div>
                </div>-->
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Usuário: </div>
                <?php print"$nome1"; ?>
            </div>
        </nav>
    </div>

    
<!--</div>-->
