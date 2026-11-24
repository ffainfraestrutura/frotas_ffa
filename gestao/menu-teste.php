<head>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.js"></script>
</head>

<body>
    <?php
    $hoje = date('Y-m-d');

    if (empty($matricula)) {
        $matricula = $matricula1;
    }
    ?>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn" src="../src/images/logoredonda.png" width="45"
                height="45"></a>

        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        </div>
        <div class="col align-self-sm-center ps-5 ms-5">
            <div id="form-container" class="col">
                <div class="row">
                    <div class="w-9 d-flex justify-content-around">
                        <input type="radio" class="d-none" value="driver" id="search_driver" name="search_type"
                            checked></input>
                        <label for="search_driver" class=""><i class="text-custom fa fa-user"
                                aria-hidden="true"></i></label>
                        <input type="radio" class="d-none" value="car" id="search_car" name="search_type"></input>
                        <label for="search_car" class=""><i class="text-custom fa-solid fa-car"></i></label>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <input type="text" id="search-input" autocomplete="off"
                                class="shadow-none col rounded-start border-0 p-1" name="search_input"
                                placeholder="Nome | CNH | CPF | Centro de Custo | Matricula">
                            <button class="col-sm-1 rounded-end border-0 p-1 ms-1"><i
                                    class=" fas fa-search"></i></button>
                            <div id="dropdown-container" class="bg-gray pt-1 rounded-bottom position-absolute d-none">
                                <div id="dropdown" class="dropdown">
                                </div>
                                <div id="roll-down" class="row h3 p-2 text-center w-100 d-none"><i
                                        class="fa fa-angle-down gray-transparent" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
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

                            print "
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Memorando
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../memorando.php'>Memorando
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../tecnico.php'>Pedir Cota Extra</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistorias
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='../checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>

                                    <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                    <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
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
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Documentos do veículo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/docsveiculocrvl.php'>CRVL</a>

                                    <a class='nav-link' href='../gestao/docsveiculocrv.php'>CRV</a>

                                    <a class='nav-link' href='../gestao/docsveiculoipva.php'>Cert IPVA</a>
                                </nav>
                            </div>";
                        } elseif ($perfil == '1') {
                            print "
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                        Memorando
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../memorando.php'>Memorando</a>
                                </nav>
                            </div> 

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../aprovacaocota.php'>Aprovar Cotas</a>
                                    
                                    <a class='nav-link' href='../visaoforca.php'>Visão Força</a>
                                    
                                    <a class='nav-link' href='../supervisor.php'>Pedir Orçamento Complementar</a>
                                    
                                    <a class='nav-link' href='../supervisorcota.php'>Pedir Cota Extra</a>
                                    
                                    <a class='nav-link' href='../transferirtec.php'>Transferir</a>
                                    
                                    <a class='nav-link' href='../aceitetoaqui.php'>Aceitar Tô Aqui!</a>

                                    <a class='nav-link' href='../calculorotafrotas.php'>Cálculo de Rota
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
                                    <a class='nav-link' href='../detalhes-kmsem-sup.php'>KMs Semanais</a>
                                    
                                    <a class='nav-link' href='../relsaldoatual-sup.php'>Saldo Semanal</a>
                                    
                                    <a class='nav-link' href='../gpsxfolga-sup.php'>Fora da Escala</a>
                                    
                                    <a class='nav-link' href='../gpsxhora-sup.php'>Deslocamento Fora do Horário</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-aprovadas-sup.php'>Cotas Extras Aprovadas</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    
                                    <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    
                                    <a class='nav-link' href='../detalhes-endaniel.php'>Endereços Casa Aniel</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas</a>
                                    
                                    <a class='nav-link' href='../detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS</a>
                                    
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                    
                                    <a class='nav-link' href='../detalhes-memorando.php'>Relatório Memorando Aceitas</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
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
                                    <a class='nav-link' href='../checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>

                                    <a class='nav-link' href='../checklist/checklistinicio.php'>Iniciar vistoria</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./checklist/checklistmc.php'>Iniciar vistoria frota</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../checklist/gerarrelatorio.php'>Visualizar relatórios</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Documentos do veículo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./docsveiculocrvl.php'>CRVL</a>

                                    <a class='nav-link' href='./docsveiculocrv.php'>CRV</a>

                                    <a class='nav-link' href='./docsveiculoipva.php'>Cert IPVA</a>
                                </nav>
                            </div>";
                        } elseif ($perfil == '2') {
                            print "
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Memorando
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../memorando.php'>Memorando</a>
                                </nav>
                            </div>
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Solicitações
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../coordenador.php'>Aprovar Orçamento Complementar</a>
                                    
                                    <a class='nav-link' href='../remanejamentocoord.php'>Remanejar Orçamentos</a>
                                    
                                    <a class='nav-link' href='../aprovacaocotacoord.php'>Aprovar Cota Supervisor</a>
                                    
                                    <a class='nav-link' href='../aprovacaocotaesc.php'>Aprovar Cota Escalonada</a>
                                    
                                    <a class='nav-link' href='../coordenadorcota.php'>Pedir Cota Extra</a>
                                    
                                    <a class='nav-link' href='../distorccoord.php'>Distribuir Orçamento</a>
                                    
                                    <a class='nav-link' href='../coordenadororc.php'>Pedir Orçamento</a>

                                    <a class='nav-link' href='../calculorotafrotas.php'>Cálculo de Rota
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
                                    <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais</a>
                                    
                                    <a class='nav-link' href='../detalhes-rssem.php'>Gastos Semanais</a>
                                    
                                    <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal</a>

                                    <a class='nav-link' href='../relsaldodiario.php'>Saldo Diário</a>
                                    
                                    <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual</a>
                                    
                                    <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala</a>
                                    
                                    <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    
                                    <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    
                                    <a class='nav-link' href='../detalhes-endaniel.php'>Endereços Casa Aniel</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas</a>
                                    
                                    <a class='nav-link' href='../detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS</a>
                                    
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                    
                                    <a class='nav-link' href='../detalhes-memorando.php'>Relatório Memorando Aceitas</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Importar
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../importoperadora.php'>Atividades Complementar em Massa</a>
                                    
                                </nav>
                            </div> 

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                            <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                            Chamados
                            <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
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
                                    <a class='nav-link' href='../checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                    <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Documentos do veículo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/docsveiculocrvl.php'>CRVL</a>

                                    <a class='nav-link' href='../gestao/docsveiculocrv.php'>CRV</a>

                                    <a class='nav-link' href='../gestao/docsveiculoipva.php'>Cert IPVA</a>
                                </nav>
                            </div>";
                        } elseif ($perfil == '3') {
                            print "
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../dashboard.php'>Inicio</a>
                                </nav>                            
                            </div>
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais</a>
                                    
                                    <a class='nav-link' href='../detalhes-rssem.php'>Gastos Semanais</a>
                                    
                                    <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal</a>
                                    
                                    <a class='nav-link' href='../relsaldodiario.php'>Saldo Diário</a>
                                    
                                    <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual</a>
                                    
                                    <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala</a>
                                    
                                    <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    
                                    <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    
                                    <a class='nav-link' href='../detalhes-endaniel.php'>Endereços Casa Aniel</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas</a>
                                    
                                    <a class='nav-link' href='../detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS</a>
                                    
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                    
                                    <a class='nav-link' href='../detalhes-memorando.php'>Relatório Memorando Aceitas</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Importar
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../importoperadora.php'>Atividades Complementar em Massa</a>
                                    
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
                               <a class='nav-link' href='../checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                <a class='nav-link' href='../checklist/condutorinicial.php'>Visualizar Relatórios A Assinar
                                </a>
                                <a class='nav-link' href='../checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios
                                </a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Documentos do veículo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./docsveiculocrvl.php'>CRVL</a>

                                    <a class='nav-link' href='./docsveiculocrv.php'>CRV</a>

                                    <a class='nav-link' href='./docsveiculoipva.php'>Cert IPVA</a>
                                </nav>
                            </div>";
                        } elseif ($perfil == '4') {
                            print "
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
                                    <a class='nav-link' href='../dashboard.php'>Inicio</a>                                    
                                    <a class='nav-link' href='../aprovacaocotafrotas.php'>Aprovar Cota Coordenador</a>
                                        
                                    <a class='nav-link' href='../frotaorccoord.php'>Aprovar Orçamento Coordenador</a>
                                        
                                    <a class='nav-link' href='../orcamento.php'>Orçamento</a>
                                        
                                    <a class='nav-link' href='../cotadiaria.php'>Cota Diária</a>
                                        
                                    <a class='nav-link' href='../detalhes-kmoscotaind-edit.php'>Editar KMs</a>
                                        
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                        
                                    <a class='nav-link' href='../detalhes-kmos-grande.php'>KMs Indevidos</a>
                                        
                                    <a class='nav-link' href='../detalhes-kmshodometro.php'>Rel Hodometros</a>
                                        
                                    <a class='nav-link' href='../importoperadora.php'>Atividades Complementar em Massa</a>
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
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais</a>
                                    
                                    <a class='nav-link' href='../detalhes-rssem.php'>Gastos Semanais</a>
                                    
                                    <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal</a>
                                    
                                    <a class='nav-link' href='../relsaldodiario.php'>Saldo Diário</a>
                                    
                                    <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual</a>
                                    
                                    <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala</a>
                                    
                                    <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    
                                    <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    
                                    <a class='nav-link' href='../detalhes-endaniel.php'>Endereços Casa Aniel</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas</a>
                                    
                                    <a class='nav-link' href='../detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS</a>
                                    
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                    
                                    <a class='nav-link' href='../detalhes-memorando.php'>Relatório Memorando Aceitas</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../importoperadora.php'>Atividades Complementar em Massa</a>    
                                </nav>
                            </div> 

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/abrirchamado.php'>Abrir Chamado</a>
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus Chamados</a>
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
                                    <a class='nav-link' href='../checklist/checklistinicio.php'>Iniciar vistoria</a>

                                    <a class='nav-link' href='../checklist/checklistinicio-manual.php'>Iniciar vistoria manual</a>
                                
                                    <!--<a class='nav-link' href='../checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='../checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>-->

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php' target='_blank'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php' target='_blank'>Relatório de Vistoria por Período</a>

                                    <a class='nav-link' href='../checklist/vistoriadorpendentes.php'>Relatórios Pendentes de Assinatura</a>

                                    <a class='nav-link' href='../checklist/assinaturaspendentes.php'>Relatórios Condutores Pendentes de Assinatura</a>
                                </nav>
                            </div>";

                            if ($matricula <> '160030' || $matricula <> '410109' || $matricula <> '500422' || $matricula <> '501285' || $matricula <> '410039' || $matricula <> '501640' || $matricula <> '500459') {
                                print "

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Multas 
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../../multas/multasfrota.php'>Listar Multas</a>

                                        <a class='nav-link' href='../multas/multasfrota.php'>Acompanhar Assinaturas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./cadastromulta.php'>Cadastrar Multa</a>
                                    </nav>

                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/gestao/importmultasnovas.php?mat=$matricula&perfil=$perfil' target='_blank'> Importar Multas Novas </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemcodmulta.php'>Códigos de Multa</a>
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
                                    <a class='nav-link' href='./listagemmultas.php'>Listagem de Multas
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./cadastromulta.php'>Cadastro de Multa
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
                                    <a class='nav-link' href='./cadastrar_condutorespj.php'>Cadastrar Condutor PJ
                                    </a>
                                    </nav>                            
                                </div>
                                 <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfornecedores.php'>Fornecedores
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfiliais.php'>Filiais
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemcondutores.php'>Condutores Ativos</a>
                                </nav> 

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemgeralcondutores.php'>Todos Condutores</a>
                                </nav>                            
                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemveiculos.php'>Veículos
                                    </a>
                                    </nav>                            
                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencao.php'>Todas Manutenções</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./farolmanutencaopreventiva.php'>Farol Manutenção
                                    </a>
                                </nav>                            
                                </div>";
                            }
                        } elseif ($perfil == '5') {
                            print "
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
                                    <a class='nav-link' href='../dashboard.php'>Inicio</a>
                                </nav>                            
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Relatórios
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../detalhes-kmsem.php'>KMs Semanais</a>
                                    
                                    <a class='nav-link' href='../detalhes-rssem.php'>Gastos Semanais</a>
                                    
                                    <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal</a>
                                    
                                    <a class='nav-link' href='../relsaldodiario.php'>Saldo Diário</a>
                                    
                                    <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual</a>
                                    
                                    <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala</a>
                                    
                                    <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    
                                    <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    
                                    <a class='nav-link' href='../detalhes-endaniel.php'>Endereços Casa Aniel</a>
                                    
                                    <a class='nav-link' href='../detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas</a>
                                    
                                    <a class='nav-link' href='../detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS</a>
                                    
                                    <a class='nav-link' href='../reltoaqui.php'>Rel Tô Aqui!</a>
                                    
                                    <a class='nav-link' href='../detalhes-memorando.php'>Relatório Memorando Aceitas</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                </nav>
                            </div> 

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <!--<a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>-->
                                
                                    <a class='nav-link' href='../chamados/abrirchamado.php'>Abrir Chamado</a>
                                        
                                    <a class='nav-link' href='../chamados/listagemchamadoscliente.php'>Meus Chamados</a>

                               
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>
                            </div>

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Vistoria
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../checklist/checklistinicio.php'>Iniciar vistoria</a>
                                </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <!--<a class='nav-link' href='../checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='../checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>-->

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php' target='_blank'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php' target='_blank'>Relatório de Vistoria por Período</a>
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
                            </div>-->";

                            if ($tipo == 8) {
                                print "

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                                               <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multasfrota.php'>Listar Multas</a>
                                </nav>
                         <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multasfinanceiro.php'>Multas a pagar</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multascontroladoria.php'>Multas Controladoria</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/relatoriomultasetapa.php'>Relatório Multas por Etapa</a>
                                </nav>
                            </div>";
                            }
                            print "

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmultas.php'>Listagem de Multas</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./cadastromulta.php'>Cadastro de Multa</a>
                                </nav>                          
                            </div>-->                            

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfornecedores.php'>Fornecedores</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfiliais.php'>Filiais</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemcondutores.php'>Condutores Ativos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemgeralcondutores.php'>Todos Condutores</a>
                                </nav> 
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemveiculos.php'>Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./relinventario.php'>Inventário de Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                </nav> 

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencao.php'>Todas Manutenções</a>
                                </nav>                            
                            </div>";
                        } elseif ($perfil == '6') {
                            print "
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
                                    <!--<a class='nav-link' href='../checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='../checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>-->

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php' target='_blank'>Relatório de Vistoria por Placa</a>

                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php' target='_blank'>Relatório de Vistoria por Período</a>
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
                                    <a class='nav-link' href='../../multas/multasfrota.php'>Listar Multas</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./cadastromulta.php'>Cadastrar Multa</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>
                            </div>

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
                                    <a class='nav-link' href='../gestao/listagemcondutores.php'>Condutores Ativos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemgeralcondutores.php'>Todos Condutores</a>
                                </nav>                            
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemveiculos.php'>Veículos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmanutencaoaberto.php'>Manutenções em Aberto</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemmanutencao.php'>Todas Manutenções</a>
                                </nav>                            
                            </div>";
                        } elseif ($perfil == '9') {
                            print "
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages00' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Sist Frotas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../telainicio.php'>Sist Frotas</a>
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
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multasfrota.php'>Listar Multas Movida</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmultas.php'>Listar Multas</a>
                                </nav>-->                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./cadastromulta.php'>Cadastrar Multa</a>
                                </nav>
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemcodmulta.php'>Códigos de Multa</a>
                                </nav>                   
                            </div>

                            <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Outras Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemmultas.php'>Listagem de Multas</a>
                                </nav>                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./cadastromulta.php'>Cadastro de Multa</a>
                                </nav>                            
                            </div>-->

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfornecedores.php'>Fornecedores</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemfiliais.php'>Filiais</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemcondutores.php'>Condutores Ativos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemgeralcondutores.php'>Todos Condutores</a>
                                </nav>                            
                            
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./listagemveiculos.php'>Veículos</a>
                                </nav>                            
                            </div>
                            
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Chamados
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/listagemchamados.php'>Chamados</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                </nav>                        
                            </div>";
                        } elseif ($perfil == '7') {
                            print "

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Gestão
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./relinventario.php'>Relatório de Veículos</a>
                                </nav>                            
                            </div>";
                        } elseif ($perfil == '11') {
                            print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../detalhes-kmsem.php'>KMs</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../relsaldoatual.php'>Saldo Semanal</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../detalhes-saldo-supxcoord.php'>Saldo Atual</a>
                                    </nav>
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../gpsxfolga.php'>Fora da Escala</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../gpsxhora.php'>Deslocamento Fora do Horário</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../detalhes-cota-extra-aprovadas.php'>Cotas Extras Aprovadas</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../detalhes-cota-extra-indevidas.php'>Cotas Extras Indevidas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Gestão
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemfornecedores.php'>Fornecedores</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemfiliais.php'>Filiais</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemcondutores.php'>Condutores Ativos</a>
                                    </nav> 

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemgeralcondutores.php'>Todos Condutores</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemveiculos.php'>Veículos</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./listagemmanutencao.php'>Todas Manutenções</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='./farolmanutencaopreventiva.php'>Farol Manutenção
                                        </a>
                                    </nav>                            
                                </div>
                            ";
                        }

                        ?>

                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Usuário:</div>
                    <?php print "$nome"; ?>
                </div>
            </nav>
        </div>
        <script>
            mouseLeaveImput = 0;
            mouseLeaveDropdown = 0;
            ["keydown", "mouseover", "click"].forEach((event) => {
                $("#search-input")[0].addEventListener(event, (e) => {
                    mouseLeaveImput = 1;
                    formHeight = $("#form-container").height();
                    formWidth = $("#search-input").outerWidth();
                    $("#search-input").removeClass("rounded-start");
                    $("#search-input").addClass("rounded-top");
                    $("#dropdown-container").css("transform", "translateY(" + formHeight + "px)");
                    $("#dropdown-container").css("width", formWidth);
                    $("#dropdown-container").removeClass("d-none");
                });
            });


            ["keydown", "mouseover", "click"].forEach((event) => {
                $("#dropdown-container")[0].addEventListener(event, (e) => {
                    mouseLeaveDropdown = 1;
                });
            });


            ["mouseout"].forEach((event) => {
                $("#search-input")[0].addEventListener(event, (e) => {
                    mouseLeaveImput = 0;
                    setTimeout(mouseLeftBoth, 10);
                });
            });

            ["mouseout"].forEach((event) => {
                $("#dropdown-container")[0].addEventListener(event, (e) => {
                    mouseLeaveDropdown = 0;
                    setTimeout(mouseLeftBoth, 10);
                });
            });


            function mouseLeftBoth() {
                if (mouseLeaveImput == 0 && mouseLeaveDropdown == 0) {
                    $("#dropdown-container").addClass("d-none");
                    $("#search-input").addClass("rounded-start");
                    $("#search-input").removeClass("rounded-top");
                }
            }


            $(document).ready(function () {
                $('input:radio[name=search_type]').change(function () {
                    if ($("input[name='search_type']:checked").val() == 'car') {
                        $("#search-input")[0].placeholder = "Placa | Renavan | Unidade | Centro de Custo";
                    }
                    if ($("input[name='search_type']:checked").val() == 'driver') {
                        $("#search-input")[0].placeholder = "Nome | CNH | CPF | Centro de Custo | Matricula";
                    }
                });
            });


            let typingTimer;
            const delay = 50; // Tempo de atraso em milissegundos

            $("#search-input")[0].addEventListener("input", (e) => {
                clearTimeout(typingTimer); // Limpa o temporizador anterior, se houver
                typingTimer = setTimeout(() => {
                    search(e.target.value);
                }, delay);
            });


            $("#search_driver")[0].addEventListener("click", function () {
                search($("#search-input")[0].value)
            })

            $("#search_car")[0].addEventListener("click", function () {
                search($("#search-input")[0].value)
            })

            function each(el, callback) {
                var allDivs = document.querySelectorAll(el),
                    alltoArr = Array.prototype.slice.call(allDivs);
                Array.prototype.forEach.call(alltoArr, function (selector, index) {
                    if (callback) return callback(selector);
                });
            };


            function search(value) {
                var dropdown = document.getElementById('dropdown');
                dropdownContainer = document.getElementById('dropdown-container');
                dropdown.innerHTML = ''; // Limpa os resultados anteriores
                $('#roll-down').addClass('d-none');
                if ($("input[name='search_type']:checked").val() == 'car') {
                    var url = 'http://10.17.0.3/frotas/gestao/control/api/veiculo/select/' + value.replace(/ /g, '+');

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                var responseText = xhr.responseText;
                                try {
                                    var response = JSON.parse(responseText);
                                    if (response.status === "sucesso") {
                                        var dados = response.dados;

                                        for (var i = 0; i < dados.length; i++) {
                                            var valor = dados[i];
                                            var oficina = valor.oficina ? '<div class="row mt-4"><i class="fa-solid fa-screwdriver-wrench"></i></div>' : '';

                                            var dropdownitem = document.createElement('div');
                                            dropdownitem.className = "search-item row py-1 shadow-bottom";
                                            dropdownitem.innerHTML = '<form method = "post" class="row" action="./editarcadveiculo.php">' +
                                                '<button class ="row py-1 button-search-dropdown" type="submit">' +
                                                '<input type="hidden" name="idtbveiculo" value="' + valor.idtbveiculo + '">' +
                                                '<div class="col-sm-1 w-15 align-self-sm-center text-center overflow-hidden">' +
                                                '<div class="row"><i class="text-custom fa-solid fa-car"></i></div>' +
                                                oficina +
                                                '</div>' +
                                                '<div class="col-sm-8 bl-drop text-center overflow-hidden">' +
                                                '<div class="row h5 ">' +
                                                '<p>' + valor.modelo + '</p>' +
                                                '</div>' +
                                                '<div class="row text-start ps-1">' +
                                                '<p>Placa: ' + valor.placa + '</p>' +
                                                '</div>' +
                                                '<div class="row text-start ps-1">' +
                                                '<p>Renavam: ' + valor.renavam + '</p>' +
                                                '</div>' +
                                                '</div>' +
                                                '<div class="col bl-drop text-center overflow-hidden py-2">' +
                                                '<div class="row">' +
                                                '<p>' + valor.unidade + '</p>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                '<p class="f-size-12">' + valor.ccusto + '</p>' +
                                                '</div>' +
                                                '</div></button></form>';

                                            formWidth = $("#search-input").outerWidth();
                                            dropdownitem.style.width = formWidth + 'px !important';
                                            dropdown.appendChild(dropdownitem);
                                        }
                                        if (dados.length > 1) {
                                            $('#roll-down').removeClass('d-none');
                                        } else {
                                            $('#roll-down').addClass('d-none');
                                        }
                                    } else {
                                        console.log('Nenhum veículo encontrado.');
                                    }
                                } catch (error) {
                                    console.log('Erro ao decodificar a resposta JSON: ' + error);
                                }
                            } else {
                                console.log('Erro ao fazer a requisição: ' + xhr.status);
                            }
                        }
                    };

                    xhr.send();
                }
                if ($("input[name='search_type']:checked").val() == 'driver') {
                    // Lógica para busca de motoristas
                    var url = 'http://10.17.0.3/frotas/gestao/control/api/condutor/select/' + value.replace(/ /g, '+');

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                var responseText = xhr.responseText;
                                try {
                                    var response = JSON.parse(responseText);
                                    if (response.status === "sucesso") {
                                        var dados = response.dados;

                                        for (var i = 0; i < dados.length; i++) {
                                            var valor = dados[i];

                                            var dropdownitem = document.createElement('div');
                                            dropdownitem.className = "search-item row shadow-bottom";
                                            dropdownitem.innerHTML = '<form method = "post" class="row" action="./dadoscondutor.php">' +
                                                '<button class ="row button-search-dropdown py-1" type="submit">' +
                                                '<input type="hidden" name="matcond" value="' + valor.matricula + '">' +
                                                '<div class="col-sm-1 w-15 align-self-sm-center overflow-hidden">' +
                                                '<div class="row text-center"><i class="text-custom fa fa-user"></i></div>' +
                                                '</div>' +
                                                '<div class="bl-drop col-sm-8 text-center overflow-hidden">' +
                                                '<div class="row h5">' +
                                                '<p>' + valor.nome + '</p>' +
                                                '</div>' +
                                                '<div class="row text-start ps-1">' +
                                                '<p>Matricula: ' + valor.matricula + '</p>' +
                                                '</div>' +
                                                '<div class="row text-start ps-1">' +
                                                '<p>CNH: ' + valor.cnh + '</p>' +
                                                '</div>' +
                                                '<div class="row text-start ps-1">' +
                                                '<p>CPF: ' + valor.cpf + '</p>' +
                                                '</div>' +
                                                '</div>' +
                                                '<div class="bl-drop col-sm-2 text-center overflow-hidden py-4">' +
                                                '<div class="row">' +
                                                '<p>' + valor.centrocusto + '</p>' +
                                                '</div>' +
                                                '</div></button></form>';

                                            formWidth = $("#search-input").outerWidth();
                                            dropdownitem.style.width = formWidth + 'px !important';
                                            dropdown.appendChild(dropdownitem);
                                        }
                                        if (dados.length > 1) {
                                            $('#roll-down').removeClass('d-none')
                                        } else {
                                            $('#roll-down').addClass('d-none')
                                        }
                                    } else {
                                        console.log('Nenhum veículo encontrado.');
                                    }
                                } catch (error) {
                                    console.log('Erro ao decodificar a resposta JSON: ' + error);
                                }
                            } else {
                                console.log('Erro ao fazer a requisição: ' + xhr.status);
                            }
                        }
                    };

                    xhr.send();
                }
            }
            var scrollInterval;
            var dropdownElement = document.getElementById('dropdown');

            $('#roll-down')[0].addEventListener('mouseenter', function () {
                scrollInterval = setInterval(function () {
                    dropdownElement.scrollTop += 1; // Rolar o elemento "dropdown" em 10 pixels para baixo
                }, 8);
            });

            $('#roll-down')[0].addEventListener('mouseleave', function () {
                clearInterval(scrollInterval);
            });
        </script>
</body>