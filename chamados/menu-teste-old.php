<?php
$hoje = date('Y-m-d');
?>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn" src="../src/images/logoredonda.png" width="45" height="45"></a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            </div>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../index.php">Logout</a></li>
                        <li><a class="dropdown-item" href="../altsenha.php">Alterar senha</a></li>                   
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <!--<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-fw fa-chart-area"></i></div>
                                Bonificação
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="./bonificacao.php">Produção</a>
                                </nav>
                            </div>!-->

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

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./abrirchamado.php'>Abrir Chamado</a>
                                    <a class='nav-link' href='./listagemchamadoscliente.php'>Meus chamados</a>
                                </nav>
                            </div>";
                            } elseif ($perfil == '1') {
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

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../telainicio2-teste.php'>Inicio
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
                                <a class='nav-link' href='../frotas/dashboard.php'>Inicio
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../frotas/aprovacaocotafrotas.php'>Aprovar Cota Coordenador
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../frotas/orcamento.php'>Orçamento
                                </a>
                                </nav>
                            </div>-->
                            
                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
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
                                <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Movida
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../../multas/testemultas2.php'>Listagem de Multas
                                </a>
                            </nav> 

                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../../multas/multasgeral.php'>Listagem de Multas Geral
                                </a>
                            </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Avulsas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas
                                </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../frotas/gestao/cadastromulta.php'>Cadastro de Multa
                                </a>
                                </nav>                          
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemfornecedores.php'>Fornecedores
                                    </a>
                                    <a class='nav-link' href='../gestao/listagemfiliais.php'>Filiais    </a>
                                    <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores</a>
                                    <a class='nav-link' href='../gestao/listagemveiculos.php'> Veículos </a>
                                </nav>
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>";
                            } elseif ($perfil == '2') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../coordenador.php'>Aprovar Orçamento Complementar
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../remanejamentocoord.php'>Remanejar Orçamentos
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../emergencialcoord.php'>Cota Emergencial
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../aprovacaocotacoord.php'>Aprovar Cota Supervisor
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../coordenadorcota.php'>Pedir Cota Extra
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../distorccoord.php'>Distribuir Orçamento
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
                                <a class='nav-link' href='../calculorotacoord.php'>Calcular Rota
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

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>-->
                           
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                            <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                            Chamados
                            <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>
                            </div>";
                            } elseif ($perfil == '3') {
                            print"
                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../telainicio-teste.php'>Inicio
                                </a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>
                            </div>";
                            } elseif ($perfil == '4') {
                                print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../telainicio2-teste.php'>Inicio
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
                                <a class='nav-link' href='../orcamento.php'>Orçamento
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
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../checklist/checklistinicio.php'>Iniciar vistoria
                                </a>
                                </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../checklist/gerarrelatorio.php'>Visualizar relatórios
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Movida
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/testemultas2.php'>Listagem de Multas</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multasgeral.php'>Listagem de Multas Geral</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Avulsas
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
                            </div>

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
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                            
                            </div>";
                            } elseif ($perfil == '5') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../telainicio2-teste.php'>Inicio
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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
                                Multas Movida
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../multas/testemultas2.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Avulsas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas</a>
                                </nav>                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>
                            </div>";
                            }  elseif ($perfil == '6') {
                            print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../telainicio2-teste.php'>Inicio</a>
                                </nav>                            
                            </div>
                            
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

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Movida
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../../multas/testemultas2.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Avulsas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas</a>
                                </nav>                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>";
                            } elseif($perfil=='9'){
                                print"
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Tela Inicial
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../telainicio2-teste.php'>Inicio</a>
                                </nav>                            
                            </div>
                            
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

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Movida
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../../multas/testemultas2.php'>Listagem de Multas
                                </a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas Avulsas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmultas.php'>Listagem de Multas</a>
                                </nav>                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/cadastromulta.php'>Cadastro de Multa</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemchamados.php'>Chamados</a>
                                    <a class='nav-link' href='./relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>";
                            }

                            ?>
                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                HC Campo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                
                                <a class='nav-link' href='./hccampotim.php'>Tim
                                </a>
                                </nav>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='./hccampoclaro.php'>Claro
                                </a>
                                </nav>
                            </div>!-->
                            <!--<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages11" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Bussola Tim
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages11" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                               <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="http://10.17.0.60//bussola/bussola.php">Bussola TIM
                                </a>
                                </nav>
                            </div>!-->
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Usuário:</div>
                        <?php print"$nome"; ?>
                    </div>
                </nav>
            </div>
