<?php
$hoje = date('Y-m-d');
?>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="#"><img class="imgicon mr-mn" src="src/images/logoredonda.png" width="45" height="45"></a>
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
                            
                                print"
                                <a class='nav-link collapsed' href='#' data-bs-toggle='collapse' data-bs-target='#collapsePages000' aria-expanded='false' aria-controls='collapsePages'>
                                <div class='sb-nav-link-icon'><i class='fas fa-columns'></i></div>
                                Painel
                                <div class='sb-sidenav-collapse-arrow'><i class='fas fa-angle-down'></i></div>
                                </a>
                                <div class='collapse' id='collapsePages000' aria-labelledby='headingTwo' data-bs-parent='#sidenavAccordion'>
                                   <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./addnovosup.php'>Alterar Supervisor
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./addnovocoord.php'>Alterar Coordenador
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../gestao/listagemcondutores.php' target='_blank'>Alterar Condutor
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./usuariosativos.php'>Listagem Usuários
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./rotaspmat.php'>Deslocamento por Funcionário
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./rotaspccusto.php'>Deslocamento por Centro de Custo
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../detalhes-kmos-zero.php' target='_blank'>KMs Zerados
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='../detalhes-semprocessamento.php' target='_blank'>Sem Processamento
                                    </a>
                                    </nav>
                                    <nav class='sb-sidenav-menu-nested nav'>
                                    <a class='nav-link' href='./aprovar_cota_adm.php'>Aprovar Cota ADM
                                    </a>
                                    </nav>
                                </div>
                                ";
                            ?>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Usuário:</div>
                        <?php print"$nome"; ?>
                    </div>
                </nav>
            </div>
