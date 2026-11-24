<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

/*$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];*/

include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$hoje = date('Y-m-d');
?>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn" src="../src/images/logoredonda.png" width="45" height="45"></a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" href="#!" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
    </div>

    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"> <!-- href="../index-teste.php"-->
                <li><a class="dropdown-item" href="../index.php">Logout</a></li>
                <li><a class="dropdown-item" href="../altsenha.php">Alterar senha</a></li>                   
            </ul>
        </li>
    </ul>
</nav>
        
<div id="layoutSidenav"  style="height: 100%;">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">

                    <?php

                    if ($perfil == '0') {
                        print"
                        <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                        </a>
                        <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../tecnico-teste.php'>Pedir Cota Extra
                                    </a>
                                </nav>
                        </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='./checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                    <a class='nav-link' href='./condutorinicial.php'>Visualizar Relatórios A Assinar
                                    </a>
                                    <a class='nav-link' href='./condutorinicialtodos.php'>Visualizar Todos Relatórios
                                    </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/abrirchamado.php'>Abrir Chamado</a>
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus Chamados</a>
                                </nav>
                            </div>";
                    } elseif($perfil =='1'){
                        print"
                        <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../aprovacaocota.php'>Aprovar Cotas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../remanejamento.php'>Remanejar Cotas
                                </a>
                            </nav>
                            
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../supervisor.php'>Pedir Orçamento Complementar
                                </a>
                            </nav>
                            
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../supervisorcota.php'>Pedir Cota Extra
                                </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../transferirtec.php'>Transferir
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Rotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../calculorotasup.php'>Calcular Rota
                                </a>
                                </nav>
                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-kmsem-sup.php'>KMs Semanais
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../relsaldoatual-sup.php'>Saldo Semanal
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxfolga-sup.php'>Fora da Escala
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxhora-sup.php'>Deslocamento Fora do Horário
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-aprovadas-sup.php'>Cotas Extras Aprovadas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-indevidas-sup.php'>Cotas Extras Indevidas
                                </a>
                                </nav>
                            </div> 
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/abrirchamado.php'>Abrir chamado</a>
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                               <a class='nav-link' href='./checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                <a class='nav-link' href='./condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='./condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>";
                    }elseif($perfil =='2'){
                        print"
                        <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../aprovacaocota-teste.php'>Aprovar Cotas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../remanejamento.php'>Remanejar Cotas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../emergencial.php'>Cotas Emergenciais
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../supervisor.php'>Pedir Orçamento Complementar
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../supervisorcota.php'>Pedir Cota Extra
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../transferirtec.php'>Transferir
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Rotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../calculorotasup.php'>Calcular Rota
                                    </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                               <a class='nav-link' href='./checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>

                                <a class='nav-link' href='./condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='./condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>";
                    } elseif($perfil == '3'){
                        print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../dashboard.php'>Inicio
                                </a>
                                </nav>                            
                            </div>
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal
                                </a>
                                </nav><nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas
                                </a>
                                </nav>
                            </div>
                             
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/abrirchamado.php'>Abrir Chamado</a>
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                               <a class='nav-link' href='./checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                               
                                <a class='nav-link' href='./condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='./condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>";
                    } elseif($perfil =='4'){
                        print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Sist Frotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../telainicio.php'>Sist Frotas
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../dashboard.php'>Inicio
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../aprovacaocotafrotas.php'>Aprovar Cota Coordenador
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../frotaorccoord.php'>Aprovar Orçamento Coordenador
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../orcamento.php'>Orçamento
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../cotadiaria.php'>Cota Diária
                                </a>
                                </nav>
                            </div>
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Rotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../calculorotafrotas.php'>Calcular Rota
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../atividadecomplementar.php'>Atividade Complementar
                                </a>
                                </nav>
                            </div>
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal
                                </a>
                                </nav><nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas
                                </a>
                                </nav>
                            </div>
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./checklistinicio.php'>Iniciar vistoria
                                </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>                                
                                    <a class='nav-link' href='./gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='./gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>

                                    <a class='nav-link' href='./vistoriadorpendentes.php'>Relatórios Pendentes de Assinatura</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Dano ao Patrimônio
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='http://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/testemultas2.php'>Listar Multas</a>
                                </nav>                 
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastrar Multa</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/importmultas.php'> Importar Multas </a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Editar Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultastramites.php'>Acompanhar Trâmites</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/relatoriomultasetapa.php'>Relatório Multa por Etapa</a>
                                </nav>                           
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa
                                </a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemfornecedores.php'>Fornecedores
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemfiliais.php'>Filiais
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores
                                </a>
                                </nav>                            
                            </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemveiculos.php'>Veículos
                                </a>
                                </nav>                            
                            </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmanutencao.php'>Manutenção
                                </a>
                            </nav>

                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/farolmanutencaopreventiva.php'>Farol Manutenção
                                </a>
                            </nav>                            
                            </div>";
                    } elseif ($perfil == '5') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Sist Frotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../telainicio.php'>Sist Frotas</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../dashboard.php'>Dashboard
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxfolga.php'>Deslocamento Indevido
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                </nav>-->                          
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='./gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Dano ao Patrimônio
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='http://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/testemultas2.php'>Listar Multas</a>
                                </nav>                           
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/importmulta.php'>Importar Multas</a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Editar Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultastramites.php'>Acompanhar Trâmites</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa
                                </a>
                                </nav>                            
                            </div>-->                           

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfornecedores.php'>Fornecedores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfiliais.php'>Filiais</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemveiculos.php'>Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/relinventario.php'>Inventário de Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmanutencao.php'>Manutenção</a>
                                </nav>                            
                            </div>";
                    }elseif ($perfil == '6') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Rotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../atividadecomplementar.php'>Atividade Complementar</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='./gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Dano ao Patrimônio
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='http://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                </nav>                            
                            </div>-->

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Dano ao Patrimônio
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='http://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                </nav>                            
                            </div>-->                            

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/testemultas2.php'>Listar Multas</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/importmulta.php'>Importar Multas</a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Editar Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultastramites.php'>Acompanhar Trâmites</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa
                                </a>
                                </nav>                            
                            </div>-->                            

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfornecedores.php'>Fornecedores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfiliais.php'>Filiais</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemveiculos.php'>Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmanutencao.php'>Manutenção</a>
                                </nav>                            
                            </div>";
                    }
                    
                    elseif ($perfil == '9') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Sist Frotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../telainicio.php'>Sist Frotas</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../dashboard.php'>Inicio
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxfolga.php'>Deslocamento Indevido
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/testemultas2.php'>Listar Multas Movida
                                    </a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/importmultas.php'> Importar Multas </a>
                                </nav> -->                           
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultastramites.php'>Acompanhar Trâmites</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa
                                </a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='./gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                </nav>
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Dano ao Patrimônio
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='http://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfornecedores.php'>Fornecedores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfiliais.php'>Filiais</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemveiculos.php'>Veículos</a>
                                </nav>                            
                            </div>
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>";
                    }elseif($perfil='7'){
                            print"
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./checklistinicio.php'>Iniciar vistoria
                                </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>                                
                                    <a class='nav-link' href='./gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='./gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>

                                    <a class='nav-link' href='./vistoriadorpendentes.php'>Relatórios Pendentes de Assinatura</a>
                                </nav>
                            </div>";

                    }
                    
                    ?>
                </div>
                
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">Usuário: </div>
                <?php print"$nome1"; //print $tipo; ?>
            </div>
        </nav>
    </div>

    
<!--</div>-->
