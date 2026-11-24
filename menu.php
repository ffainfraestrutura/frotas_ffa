<?php
$hoje = date('Y-m-d');
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn"
            src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; ?>/frotas/src/images/logoredonda.png"
            width="45" height="45"></a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
            class="fas fa-bars"></i></button>
    <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
    </div>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item"
                        href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; ?>/frotas/index.php">Logout</a>
                </li>
                <li><a class="dropdown-item"
                        href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; ?>/frotas/altsenha.php">Alterar
                        senha</a></li>
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
                    $matricula = $_SESSION['matricula'];
                    $_SESSION['matricula'] = $matricula;
                    if ($perfil == '0') {
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Memorando
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/memorando.php'>Memorando
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
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/tecnico.php'>Pedir Cota Extra
                                    </a>
                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/localizacao.php'>Tô Aqui!
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
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                    </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                        Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                    </nav>
                                </div>
                                
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Documentos do veículo
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrvl.php'>CRVL</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrv.php'>CRV</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculoipva.php'>Cert IPVA</a>
                                    </nav>
                                </div>    ";
                    } elseif ($perfil == '1') {
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Memorando
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/memorando.php'>Memorando
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
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/supervisor/retiradadesaldosup.php'>Retirada de Saldo
                                    </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/supervisor/pedircotaextra.php'>Pedir Cota Extra
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/visaoforca.php'>Visão Força
                                    </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/transferirtec.php'>Transferir Técnico
                                    </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='https://ffainfraestrutura.com.br/frotas/toaquisup.php'>Fazer Tô Aqui!
                                    </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/aceitetoaqui.php'>Aceitar Tô Aqui!
                                    </a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/calculorotasup.php'>Calculo de Rota
                                    </a>
                                </nav>";
                        $ccusto = $_SESSION['ccusto'];
                        $_SESSION['ccusto'] = $ccusto;
                        if ($ccusto == 'tim') {
                            print "<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atividadecomplementartimsup.php'>Atividade Complementar
                                        </a>
                                    </nav>";

                        } elseif ($ccusto == 'claro') {

                            print "<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atividadecomplementarclarosup.php'>Atividade Complementar
                                        </a>
                                    </nav>";
                        } elseif ($ccusto == 'ligga') {

                            print "<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atividadecomplementarliggasup.php'>Atividade Complementar
                                        </a>
                                    </nav>";
                        }

                        print "</div>

                                
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento
                                    </a>
                                    </nav>-->
                                
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual-sup.php'>Rel. Saldo Semanal
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga-sup.php'>Rel. Deslocamento Fora de Escala
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora-sup.php'>Rel. Deslocamento Fora do Horário
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas-sup.php'>Rel. Cotas Extras Aprovadas
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                                    </a>
                                    </nav> <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                                    </a>
                                    </nav>  <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                                    </a>
                                    </nav> 
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
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
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                    </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistorias
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                    </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Documentos do veículo
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrvl.php'>CRVL</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrv.php'>CRV</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculoipva.php'>Cert IPVA</a>
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
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/memorando.php'>Memorando
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
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/visaoforcacoord.php'>Visão Força
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/coordenador/retiradadesaldo.php'>Retirada de Saldo
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/coordenador/status-saldo-combustivel.php'>Aprovar Cotas Extras
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/coordenador/solicitarcotaextra.php'>Solicitar Cota Extra
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/coordenador/solicitarorcamentocomplementar.php'>Solicitar Orçamento Complementar
                                    </a>
                                    </nav>
                                                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriodescontousoindevido.php'>Avaliação de Desconto por Uso Indevido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/coordenador/previsaomanutencao.php'>Previsão de Manutenção
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/transferirfuncionario.php' 
                                       title='Puxe supervisores para sua força e verifique seus coordenadores' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Transferir Supervisor
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciar-equipe-coord.php' 
                                       title='Gerencie supervisores e técnicos puxando para sua força aqui' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Gerenciar Equipe</a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescalatecnicos.php' 
                                       title='Gerencie a Escala de técnicos da sua força aqui' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Gerenciar Escala</a>
                                    </nav> 
                                            <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescalatecnicosferiado.php' 
                                       title='Gerencie a Escala de Feriado de técnicos da sua força aqui' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Gerenciar Escala Feriado</a>
                                    </nav>                                              
                                </div>
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmproj-kmos.php'>Orçamento Semanal
                                    </a>
                                    </nav>
                                    
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs Semanais
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmshodometro.php'>Rel. Hodometros</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-rssem.php'>Gastos Semanais
                                    </a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Rel. Saldo Semanal
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldodiario.php'>Rel. Saldo Diário
                                    </a>
                                    </nav><nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Rel. Saldo do Sup por Período
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                                    </a>
                                    </nav> <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                                    </a>
                                    </nav>  <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                                    </a>
                                    </nav> 
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                                    </a>
                                    </nav>
                                                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/ranking-uso-indevido.php'>Relatório de Ranking Uso Indevido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorio-uso-indevido.php'>Relatório de Uso Indevido por Tempo Decorrido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
                                    </a>
                                    </nav>      
                                </div>
                                
                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                    </nav>
                                </div> 

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                    </nav>
                                </div>-->



                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistorias
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                    </nav>
                                </div> 
                                
                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Documentos do veículo
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrvl.php'>CRVL</a>

                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrv.php'>CRV</a>

                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculoipva.php'>Cert IPVA</a>
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
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciar-equipe-adm.php' 
                                           title='Gerencie equipes puxando coordenadores, supervisores e técnicos para força ou equipe' 
                                           data-bs-toggle='tooltip' 
                                           data-bs-placement='right'>Gerenciar Equipe
                                        </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/visaoforca.php'>Visão Força SUP
                                        </a>
                                    </nav> 

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapmat.php'>Deslocamento por Funcionário
                                        </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapccusto.php'>Deslocamento por Centro De Custo
                                        </a>
                                    </nav>

                                </div>
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Solicitações
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>

                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/solicitacao_orcamento_diretor.php'>Solicitação Orçamento Diretor
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/aprovarcota.php'>Aprovar Cota
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/aprovarcotaescalonada.php'>Aprovar Cota Escalonada
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/aprovarorcamentocomplementar.php'>Aprovar Orçamento Complementar
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/distribuirorcamento.php'>Distribuir Orçamento
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/remanejarorcamento.php'>Remanejar Orçamento
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/visaoforca.php'>Visão Força
                                    </a>

                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/retiradadesaldo.php'>Retirada de Saldo </a>

                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/transferirfuncionario.php'>Transferir Coordenador                                </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciar_equipe.php' 
                                       title='Gerencie equipes puxando coordenadores, supervisores e técnicos para força ou equipe' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Gerenciar Equipe
                                    </a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerente/previsaomanutencao.php'>Previsão de Manutenção
                                        </a>
                                    </nav>                            
                                </div>
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-
                                
                                parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmproj-kmos.php'>Orçamento Semanal
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/relatorio_condutores_pontos.php'>Relatório Condutores Inativos no Pontos
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriosaldocalculado.php'>Calculo Cota
                                    </a>
                                    </nav>
                                      <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapmat.php'>Deslocamento por Funcionário
                                        </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapccusto.php'>Deslocamento por Centro De Custo
                                        </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs Semanais
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-rssem.php'>Gastos Semanais
                                    </a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Saldo Semanal
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatualnovo.php'>Apuração Semanal
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldodiario.php'>Saldo Diário
                                    </a>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriodescontousoindevidofrota.php' target='_blank'>Relatório Desconto por Uso Indevido

                                    </nav><nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Saldo do Sup por Período
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                                    </a>
                                    </nav> <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                                    </a>
                                    </nav>  <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                                    </a>
                                    </nav> 
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                                    </a>
                                    </nav> 
                                     <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/ranking-uso-indevido.php'>Relatório de Ranking Uso Indevido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorio-uso-indevido.php'>Relatório de Uso Indevido por Tempo Decorrido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
                                    </a>
                                    </nav>  
                                </div>
                                 
                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                    </nav>
                                </div> 

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus Chamados</a>
                                    </nav>
                                </div>-->

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistorias
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                    </nav>
                                </div>
                                
                                
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Documentos do veículo
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrvl.php'>CRVL</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculocrv.php'>CRV</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/docsveiculoipva.php'>Cert IPVA</a>
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
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/telainicio.php'>Sist Frotas
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
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/aprovacaocotafrotas.php'>Aprovar Cota Coordenador
                                    </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/frotaorccoord.php'>Aprovar Orçamento Gerente e Diretor
                                    </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/orcamento.php'>Orçamento Coordenador
                                    </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/orcamentogerente.php'>Orçamento Gerente
                                    </a>
                                    </nav>
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciarsaldofrota.php'>Gerenciar Saldo da Frota
                                    </a>
                                    </nav>
                    

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/visaoforcafrotas.php'>Visão Força SUP
                                    </a>
                                    </nav> 


                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/aprovacaocotatecfrota.php'>Aprovar Cota Técnico</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoveiculos.php'>Inserir Saldo Veiculos
                                        </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciar-equipe-adm.php' 
                                           title='Gerencie equipes puxando coordenadores, supervisores e técnicos para força ou equipe' 
                                           data-bs-toggle='tooltip' 
                                           data-bs-placement='right'>Gerenciar Equipe
                                        </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescalatecnicos.php' 
                                       title='Gerencie a Escala de técnicos da sua força aqui' 
                                       data-bs-toggle='tooltip' 
                                       data-bs-placement='right'>Gerenciar Escala</a>
                                    </nav>  
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/cotafixa.php'>Gerenciar Cotas Fixas
                                        </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listar_condutorespj.php'>Cadastrar Condutor PJ
                                        </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorio_condutores_pontos.php'>Condutores x Ponto
                                        </a>
                                    </nav>
                                    ";

                        // <nav class='sb-sidenav-menu-nested nav'>
                        //     <a class='nav-link' href='".$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']."/frotas/fundofrota.php'>Fundo Frota - Superiores</a>
                        // </nav>
                    
                        // <nav class='sb-sidenav-menu-nested nav'>
                        //     <a class='nav-link' href='".$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']."/frotas/fundofrota-tecnico.php'>Fundo Frota - Técnicos</a>
                        // </nav>
                    
                        // <nav class='sb-sidenav-menu-nested nav'>
                        //     <a class='nav-link' href='".$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']."/frotas/fundofrota-extrato.php'>Fundo Frota - Extrato</a>
                        // </nav>
                    
                        echo "</div>
                               
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmproj-kmos.php'>Orçamento Semanal
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/extratoorcamentogerente.php'>Extrato Transações Gerente
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriosaldocalculado.php'>Calculo Cota
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento por OS
                                    </a>
                                    </nav>-->
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relorcsemanalsup.php'>Rel. Orc. Semanal Sup.
                                    </a>
                                    </nav>-->
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs Semanais
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-rssem.php'>Gastos Semanais
                                    </a>
                                    </nav>-->
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Saldo Semanal
                                    </a>
                                    </nav>-->
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatualnovo.php'>Apuração Semanal
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Saldo do Sup por Período
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário
                                    </a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                                    </a>
                                    </nav> <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                                    </a>
                                    </nav>  <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                                    </a>
                                    </nav> 
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                                    </a>
                                    </nav>
<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriodescontousoindevidofrota.php' target='_blank'>Relatório Desconto por Uso Indevido
                                    </a>
                                    </nav>
                                                                        <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/ranking-uso-indevido.php'>Relatório de Ranking Uso Indevido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorio-uso-indevido.php'>Relatório de Uso Indevido por Tempo Decorrido
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapmat.php'>Rel Deslocamento por Funcionário
                                        </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapccusto.php'>Rel Deslocamento por Centro De Custo
                                        </a>
                                    </nav>      
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                    </nav>
                                </div> 

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus Chamados</a>
                                    
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamados.php'>Chamados</a>

                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamados-periodo.php'>Chamados do dia</a>

                                        
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                    </nav>                            
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistoria
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistinicio.php'>Iniciar vistoria</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistmc.php'>Iniciar vistoria frota</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistinicio-manual.php'>Iniciar vistoria manual</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php' target='_blank'>Relatório de Vistoria por Placa</a>
                                </nav>

                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php' target='_blank'>Relatório de Vistoria por Período</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/vistoriadorpendentes.php'>Relatórios Pendentes de Assinatura</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/assinaturaspendentes.php'>Relatórios Condutores Pendentes de Assinatura</a>
                                </nav>

                                </div>

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Dano ao Patrimônio
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                    </nav>                            
                                </div>-->

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Multas
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/multas/multasfrota.php'>Listar Multas</a>

                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/multas/multasfrota.php'>Acompanhar Assinaturas</a>
                                    </nav>                                

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/gestao/cadastromulta.php'>Cadastrar Multa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/gestao/importarmultasnovas.php?mat=$matricula&perfil=$perfil'> Importar Multas Novas </a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4242/multas/relatoriomultasetapa.php'>Relatório Multa por Etapa</a>
                                    </nav>                           
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Gestão
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <!--<div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfornecedores.php'>Fornecedores
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfiliais.php'>Filiais
                                    </a>
                                    </nav>                            
                                </div>-->
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemcondutores.php'>Condutores Ativos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemgeralcondutores.php'>Listagem Geral de Condutores</a>
                                </nav> 

                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemveiculos.php'>Veículos
                                    </a>
                                    </nav>                            
                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/previsaomanutencao.php'>Previsão de Manutenção
                                    </a>
                                </nav>         
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencao.php'>Todas Manutenções
                                    </a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/farolmanutencaopreventiva.php'>Farol Manutenção
                                    </a>
                                </nav>
                                 <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagem-formularios-conserto-pneu.php'>Borracheiro
                                    </a>
                                </nav>                                                          
                                </div>
                                    ";
                    } elseif ($perfil == '5') {
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Sist Frotas
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/telainicio.php'>Sist Frotas</a>
                                    </nav>                            
                                </div>

                                <div class='collapse' id='collapsePages00' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>                         
                                </div>
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs Semanais
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-rssem.php'>Gastos Semanais
                                    </a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Saldo Semanal
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatualnovo.php'>Apuração Semanal
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldodiario.php'>Saldo Diário
                                    </a>
                                    </nav><nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Saldo do Sup por Período
                                    </a>
                                    </nav>
                                <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário
                                    </a>
                                    </nav>-->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas
                                    </a>
                                    </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>->
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                                    </a>
                                    </nav>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                                    </a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                                    </a>
                                    </nav> <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                                    </a>
                                    </nav>  <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                                    </a>
                                    </nav> 
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                                    </a>
                                    </nav>
                                                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
                                    </a>
                                    </nav>
                                </div>

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                    </nav>
                                </div> 
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus Chamados</a>
                                        
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                    </nav>                            
                                </div>-->

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistoria
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>
                                    </nav>-->

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php' target='_blank'>Relatório de Vistoria por Placa</a>
                                    </nav>

                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                    </nav>-->

                                    <nav class='sb-sidenav-menu-nested nav'>
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
                                        <a class='nav-link' href='https://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                    </nav>                            
                                </div>-->";

                        if ($tipo == 5) {
                            print "

                            <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Multas
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multasfinanceiro.php'>Multas a pagar</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../../multas/multascontroladoria.php'>Multas Controladoria</a>
                                </nav>
                            </div>";
                        }
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Gestão
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfornecedores.php'>Fornecedores</a>
                                    </nav>
                                                                 
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfiliais.php'>Filiais</a>
                                    </nav> 
                                    
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemcondutores.php'>Condutores Ativos</a>
                                </nav>-->

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemgeralcondutores.php'>Listagem Geral de Condutores</a>
                                </nav>     
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemveiculos.php'>Veículos</a>
                                    </nav>
                                    
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/relinventario.php'>Inventário de Veículos</a>
                                    </nav> 

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencao.php'>Todas Manutenções</a>
                                    </nav>                            
                                </div>
                                    ";
                    } elseif ($perfil == '6') {
                        print "                           
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Rotas
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                
                                <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atividadecomplementar.php'>Atividade Complementar
                                    </a>
                                    </nav>
                                </div>

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages07' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages07' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamados.php'>Chamados</a>
                                        
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/relatoriochamados.php'>Relatório de Chamados</a>
                                    </nav>                            
                                </div>-->

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistoria
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistinicio.php'>Iniciar vistoria</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorio.php'>Relatório de Vistoria por Placa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffa.painel-telecom.com:4545/frotas/checklist/gerarrelatorioperiodo.php'>Relatório de Vistoria por Período</a>
                                    </nav>
                                </div>-->

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages003' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Dano ao Patrimônio
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages003' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='https://ffainfraestrutura.com.br/danoaopatrimonio/' target='_blank'>Dano ao Patrimônio</a>
                                    </nav>                            
                                </div>-->

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages002' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Importar
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages002' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/importoperadora.php'>Atividades Complementar em Massa</a>
                                        
                                    </nav>
                                </div> 

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages04' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Multas
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages04' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='../multas/multasfrota.php'>Listar Multas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/cadastromulta.php'>Cadastrar Multa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmultas.php'>Editar Multa</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemcodmulta.php'>Códigos de Multa</a>
                                    </nav>
                                </div>-->

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages05' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Outras Multas
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmultas.php'>Listagem de Multas
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages05' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/cadastromulta.php'>Cadastro de Multa
                                    </a>
                                    </nav>                            
                                </div>-->

                                <!--<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Gestão
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                 <!--<div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                               <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfornecedores.php'>Fornecedores
                                    </a>
                                    </nav>                            
                                </div>
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfiliais.php'>Filiais
                                    </a>
                                    </nav>                            
                                </div>-->
                                <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemcondutores.php'>Condutores Ativos</a>
                                </nav>

                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemgeralcondutores.php'>Listagem Geral de Condutores</a>
                                </nav>                            
                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemveiculos.php'>Veículos
                                    </a>
                                    </nav>                            
                                </div><div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                </nav>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencao.php'>Todas Manutenções
                                    </a>
                                    </nav>                            
                                </div>-->";
                    } elseif ($perfil == '7') {
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages01' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Vistorias
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages01' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/checklistp1-condutor.php'>Realizar Vistoria de Auditoria</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicial.php'>Visualizar Relatórios A Assinar</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/checklist/condutorinicialtodos.php'>Visualizar Todos Relatórios</a>
                                    </nav>
                                </div>";
                    } elseif ($perfil == '10') {
                        print "
                        <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages001' aria-expanded='false' aria-controls='collapsePages'>
                            <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                            Sist Frotas
                            <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                        </a>
                        <div class='collapse' id='collapsePages001' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/telainicio.php'>Sist Frotas
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
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/diretor/pedirorcamento.php'>Pedir Orçamento Extra</a>
                                <a class='nav-link' href='https://ffa.painel-telecom.com:4242/frotas/diretor/aprovarorcamentocomplementar.php'>Aprovar Orçamento Complementar</a>
                            </nav>
                        </div>

                        <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePagesPainel' aria-expanded='false' aria-controls='collapsePages'>
                            <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                            Painel
                            <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                        </a>
                        <div class='collapse' id='collapsePagesPainel' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gerenciar-equipe-adm.php' 
                                   title='Gerencie equipes puxando coordenadores, supervisores e técnicos para força ou equipe' 
                                   data-bs-toggle='tooltip' 
                                   data-bs-placement='right'>Gerenciar Equipe
                                </a>
                            </nav>

                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/visaoforcafrotas.php'>Visão Força SUP
                                </a>
                            </nav> 

                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapmat.php'>Deslocamento por Funcionário
                                </a>
                            </nav>

                            <nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/painel-suporte/rotapccusto.php'>Deslocamento por Centro De Custo
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
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmproj-kmos.php'>Orçamento Semanal
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/extratoorcamentogerente.php'>Extrato Transações Gerente
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriosaldocalculado.php'>Calculo Cota
                            </a>
                            </nav>
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmoscotaind-rel.php'>Relatório Deslocamento por OS
                            </a>
                            </nav>-->
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relorcsemanalsup.php'>Rel. Orc. Semanal Sup.
                            </a>
                            </nav>-->
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs Semanais
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-rssem.php'>Gastos Semanais
                            </a>
                            </nav>-->
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Saldo Semanal
                            </a>
                            </nav>-->
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatualnovo.php'>Apuração Semanal
                            </a>
                            </nav>-->
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Saldo do Sup por Período
                            </a>
                            </nav>
                        <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala
                            </a>
                            </nav>
                        <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário
                            </a>
                            </nav>-->
                        <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas
                            </a>
                            </nav>
                        <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas
                            </a>
                            </nav>
                            <!--<nav class='sb-sidenav-menu-nested nav'>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                            </nav>-->
                        <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-endaniel.php'>Endereços Casa Aniel
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-descontadaac.php'>Relatório Cotas Extra Indevidas Aceitas
                            </a>
                            </nav>
                            <!--<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-ofensores-gpsxos.php'>Relatório Rotas GPS x OS
                            </a>
                            </nav>-->
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaqui.php'>Rel Tô Aqui!
                            </a>
                            </nav> <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/reltoaquisup.php'>Rel Tô Aqui! Sup
                            </a>
                            </nav>  <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/rel-log.php'>Relatório de Log
                            </a>
                            </nav> 
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-memorando.php'>Relatório Memorando Aceitas
                            </a>
                            </nav>
<nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatoriodescontousoindevidofrota.php' target='_blank'>Relatório Desconto por Uso Indevido
                            </a>
                            </nav>
                                                                <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorioremanejamento.php'>Relatório de remanejamento
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/ranking-uso-indevido.php'>Relatório de Ranking Uso Indevido
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relatorio-uso-indevido.php'>Relatório de Uso Indevido por Tempo Decorrido
                            </a>
                            </nav>
                            <nav class='sb-sidenav-menu-nested nav'>
                            <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas
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
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus Chamados</a>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamados.php'>Chamados</a>
                                <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/relatoriochamados.php'>Relatório de Chamados</a>
                            </nav>                            
                        </div>"
                        ;
                    } elseif ($perfil == '11') {
                        print "
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages02' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Relatórios
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages02' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-kmsem.php'>KMs</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/relsaldoatual.php'>Rel. Saldo Semanal</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-saldo-supxcoord.php'>Rel. Saldo do Sup por Período</a>
                                    </nav>
                                    
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxfolga.php'>Rel. Deslocamento Fora de Escala</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gpsxhora.php'>Rel. Deslocamento Fora do Horário</a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-aprovadas.php'>Rel. Cotas Extras Aprovadas</a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/detalhes-cota-extra-indevidas.php'>Rel. Cotas Extras Indevidas</a>
                                    </nav>

                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/atvforatoa.php'>Atividades Fora TOA</a>
                                    </nav>-->
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/gerenciarescala_tecnicos.php'>Relatório de Escalas</a>
                                    </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages03' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Chamados
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages03' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/abrirchamado.php'>Abrir Chamado</a>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/chamados/listagemchamadoscliente.php'>Meus chamados</a>
                                   </nav>
                                </div>

                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages06' aria-expanded='false' aria-controls='collapsePages'>
                                    <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                    Gestão
                                    <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                    <div class='collapse' id='collapsePages06' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                    <!--<nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfornecedores.php'>Fornecedores</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemfiliais.php'>Filiais</a>
                                    </nav>-->              
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemcondutores.php'>Condutores Ativos</a>
                                    </nav> 

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/listagemgeralcondutores.php'>Listagem Geral de Condutores</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemveiculos.php'>Veículos</a>
                                    </nav>                            
                                
                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/listagemmanutencaoaberta.php'>Manutenções em Aberto</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencaoconc.php'>Manutenções Concluídas</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/listagemmanutencao.php'>Todas Manutenções</a>
                                    </nav>

                                    <nav class='sb-sidenav-menu-nested nav'>
                                        <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/gestao/farolmanutencaopreventiva.php'>Farol Manutenção
                                        </a>
                                    </nav>                            
                                </div>
                            ";
                    }
                    print "<a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages100' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Ajuda
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                            </a>
                            <div class='collapse' id='collapsePages100' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/frotas/ajuda/index.php'>Manuais do sistema</a>
                                </nav>
                            </div>";
                    ?>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Usuário:</div>
                <?php print "$nome"; ?>
            </div>
        </nav>
    </div>