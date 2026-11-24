<?php
    setlocale(LC_ALL, 'pt_BR.utf8');
    date_default_timezone_set('America/Sao_Paulo');
    session_start();

    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];
    $tipo=$_SESSION['tipo'];

    if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
    } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '6'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
    }

    $_SESSION['perfil'] = $perfil; 
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;
    $_SESSION['tipo']=$tipo;

    include_once"../func/log.php";
    include"conecta.php";
    header("Content-type: text/html; charset=utf-8");

    $hoje = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="../src/images/favicon.png"/>
        <title>Listagem de Chamados</title>
        <link href="../src/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">        
        
        <!-- CSS customizado para WhatsApp -->
        <style>
            .btn-whatsapp {
                background-color: #25D366 !important;
                border-color: #25D366 !important;
                color: white !important;
                transition: all 0.3s ease;
                font-size: 12px !important;
                padding: 4px 6px !important;
                border-radius: 50% !important;
                width: 24px !important;
                height: 24px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .btn-whatsapp:hover {
                background-color: #128C7E !important;
                border-color: #128C7E !important;
                color: white !important;
                transform: scale(1.1);
            }
            
            .btn-whatsapp i {
                margin: 0 !important;
                font-size: 12px !important;
            }
        </style>
    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"../menu.php"; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="h1 pt-1"> Listagem de Chamados </h1>
                    <a href="listagemchamados-periodo.php" class="btn btn-outline-primary">Consultar por período</a>

                    <!-- Filtros e Busca -->
                    <div class="row mt-4 mb-4">
                        <div class="col-md-3">
                            <label for="filtroStatus" class="form-label">Filtrar por Status:</label>
                            <select id="filtroStatus" class="form-select">
                                <option value="">Todos</option>
                                <option value="Aberto">Aberto</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtroCategoria" class="form-label">Filtrar por Categoria:</label>
                            <select id="filtroCategoria" class="form-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="buscaGeral" class="form-label">Buscar:</label>
                            <input type="text" id="buscaGeral" class="form-control" placeholder="Pesquisar...">
                        </div>
                    </div>

                    <!--div tabela-->
                    <div class="mt-4 table-responsive"> <!-- Adicionado table-responsive para melhor visualização em telas pequenas -->
                        <table id='tabela1' class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Data de abertura</th>
                                    <th>Dias</th>
                                    <th> Placa </th>
                                    <th>Base gestão</th>
                                    <th> Status </th>
                                    <th> Resposta </th>
                                    <th>Categoria</th>
                                    <th>Problema/End. p/ Guincho:</th>
                                    <!-- ALTERAÇÃO 1: Adicionada a nova coluna para as fotos -->
                                    <th>Fotos</th>
                                    <th>Técnico</th>
                                    <th>Telefone</th>
                                    <th>Centro de custo</th>
                                    <th>Responder</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php
                            // Consulta otimizada: busca apenas as colunas necessárias
                            $sql="SELECT idtbchamados, matricula, nome, ccusto, placa, tipodechamado, tiposinistro, 
                                  tipomanuprev, tipomanucor, data_abert, status, enderecog, telefone, resposta, 
                                  img1, img2, img3,
                                  (SELECT v.basegestao FROM bdfrota.tbveiculo v
                                   WHERE v.placa = ch.placa
                                   ORDER BY v.idtbveiculo DESC LIMIT 1) AS base_gestao
                                  FROM bdfrota.tbchamados ch
                                  WHERE status<>2 
                                  ORDER BY idtbchamados DESC;";
                            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                            while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                                $numero = $row['idtbchamados'];
                                $matricula = $row['matricula'];
                                $nome =  $row['nome'];
                                $ccusto =  $row['ccusto'];
                                $placa=$row['placa'];
                                $baseGestao = htmlspecialchars(trim((string)($row['base_gestao'] ?? '')) ?: 'Não informado', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                $tipodechamado= $row['tipodechamado'];
                                $tiposinistro= $row['tiposinistro'];
                                $tipomanuprev= $row['tipomanuprev'];
                                $tipomanucor= $row['tipomanucor'];
                                $data_abert=$row['data_abert'];
                                $status = $row['status'];
                                $enderecog =  $row['enderecog'];
                                $telefone = $row['telefone'];
                                $resposta = isset($row['resposta']) ? $row['resposta'] : '-';

                                // ALTERAÇÃO 2: Capturar os nomes dos arquivos de imagem
                                $img1 = $row['img1'];
                                $img2 = $row['img2'];
                                $img3 = $row['img3'];

                                // Tratando datas
                                $data_aberta = new DateTime($data_abert);
                                $data_aberta1 = $data_aberta-> format('d/m/Y');
                                $fim = new DateTime($hoje);
                                $dateInterval = $data_aberta->diff($fim);
                                $intervalo = ($dateInterval->format('%y')*365)+($dateInterval->format('%m')*30)+$dateInterval->format('%d');

                                // Definindo o problema/tipo
                                if($tipodechamado == 'Comunicado de Sinistro'){ $tipo = $tiposinistro; } 
                                elseif($tipodechamado == 'Solicitação de Manutenção Preventiva'){ $tipo = $tipomanuprev; } 
                                elseif($tipodechamado == 'Solicitação de Manutenção Corretiva'){ $tipo = $tipomanucor; }
                                elseif($tipodechamado == 'Solicitação de Guincho'){ $tipo= "$enderecog"; }
                                else { $tipo= ""; }

                                // Tratando status
                                if($status==0){ 
                                    $status_html = "<span class='badge bg-success'>Aberto</span>"; 
                                } elseif($status==1){ 
                                    $status_html = "<span class='badge bg-success'>Aberto</span>"; 
                                } elseif($status==3){ 
                                    $status_html = "<span class='badge bg-warning text-dark'>Pendente</span>"; 
                                } elseif($status==4){ 
                                    $status_html = "<span class='badge bg-secondary'>Cancelado</span>"; 
                                } else {
                                    $status_html = $status;
                                }
                                
                                // ALTERAÇÃO 3: Construir o HTML para exibir as imagens
                                $fotos_html = '';
                                // IMPORTANTE: Verifique se o caminho '../image/' está correto a partir DESTE arquivo de listagem.
                                $caminho_base_imagem = './image/'; 
                                // Verifica se existe imagem 1 e cria o link/thumbnail
                                if (!empty($img1) && $img1 != '' && $img1 != null) {
                                    $fotos_html .= "<a href='{$caminho_base_imagem}{$img1}' target='_blank'><img src='{$caminho_base_imagem}{$img1}' width='45' loading='lazy' title='Clique para ampliar' style='margin: 2px; border: 1px solid #ccc; border-radius: 4px; object-fit: cover; height: 45px;' onerror=\"this.style.display='none'\"></a>";
                                }
                                // Verifica se existe imagem 2
                                if (!empty($img2) && $img2 != '' && $img2 != null) {
                                    $fotos_html .= "<a href='{$caminho_base_imagem}{$img2}' target='_blank'><img src='{$caminho_base_imagem}{$img2}' width='45' loading='lazy' title='Clique para ampliar' style='margin: 2px; border: 1px solid #ccc; border-radius: 4px; object-fit: cover; height: 45px;' onerror=\"this.style.display='none'\"></a>";
                                }
                                // Verifica se existe imagem 3
                                if (!empty($img3) && $img3 != '' && $img3 != null) {
                                    $fotos_html .= "<a href='{$caminho_base_imagem}{$img3}' target='_blank'><img src='{$caminho_base_imagem}{$img3}' width='45' loading='lazy' title='Clique para ampliar' style='margin: 2px; border: 1px solid #ccc; border-radius: 4px; object-fit: cover; height: 45px;' onerror=\"this.style.display='none'\"></a>";
                                }
                                
                                // Se nenhuma foto foi encontrada, exibe um traço
                                if(empty($fotos_html)) {
                                    $fotos_html = '-';
                                }

                                // Formatando o número de telefone para WhatsApp (apenas dígitos)
                                $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
                                
                                // Criando o botão do WhatsApp se houver telefone
                                $whatsapp_button = '';
                                if (!empty($telefone_limpo)) {
                                    $whatsapp_button = "<a href='https://wa.me/55{$telefone_limpo}' target='_blank' class='btn btn-whatsapp' title='Abrir conversa no WhatsApp'><i class='fab fa-whatsapp'></i></a>";
                                } else {
                                    $whatsapp_button = '-';
                                }

                                // ALTERAÇÃO 4: Adicionada a nova célula (<td>) com as fotos na tabela
                                print"
                                <tr>
                                    <td> $numero </td>
                                    <td> $data_aberta1 </td>
                                    <td> $intervalo </td>
                                    <td> $placa </td>
                                    <td> $baseGestao </td>
                                    <td> $status_html </td>
                                    <td> $resposta </td>
                                    <td> $tipodechamado </td>
                                    <td> $tipo </td>
                                    <td> $fotos_html </td>
                                    <td> $nome </td>
                                    <td style='text-align: center;'> $whatsapp_button </td>
                                    <td> $ccusto </td>
                                    <td> <form method='post' action='./editarchamado.php'><input type='hidden' name='idtbchamados' id='idtbchamados' value='$numero'><button style='border: none; background-color: transparent;' type='submit' title='Editar chamado'> <span class='material-symbols-outlined'> edit_note </span></button></form> </td>
                                </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- botão gerar excel -->
                    <div class='mt-4 mb-4'>
                        <form action="./control/gerarexcelgeral.php" method="post">
                        <?php print "<input type='hidden' name='autor1' value='$autor'/>"; ?>
                            <button type='submit' class='btn btn-success'> Gerar Excel Geral </button>
                        </form>
                    </div> 	
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright © FFA Infraestrutura</div>
                    </div>
                </div>
            </footer>
        </div>

        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.10.12/sorting/date-eu.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../src/js/scripts.js"></script>

        <script type="text/javascript">
            $(document).ready(function() { 
                var tabela = $('#tabela1').DataTable({
                    order: [ 0, 'desc' ],
                    pageLength: 25,
                    deferRender: true,
                    processing: false,

                    // ALTERAÇÃO 5: Adicionado um 'null' para a nova coluna de fotos
                    columns: [
                        null, // Coluna Nº
                        { "type": "date-eu" }, // Coluna Data de abertura
                        null, // Coluna Dias
                        null, // Coluna Placa
                        null, // Coluna Base gestão
                        { "orderable": false }, // Coluna Status - não é ordenável por causa do HTML
                        null, // Coluna Resposta
                        null, // Coluna Categoria
                        null, // Coluna Problema/End. p/ Guincho
                        { "orderable": false }, // Coluna Fotos - não é ordenável
                        null, // Coluna Técnico
                        null, // Coluna Telefone
                        null, // Coluna Centro de custo
                        null, // Coluna Editar               
                    ],

                    language: {
                        decimal:        "",
                        emptyTable:     "Nada para exibir",
                        info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
                        infoFiltered:   "(filtrado do total de _MAX_ registros)",
                        infoPostFix:    "",
                        thousands:      ".",
                        lengthMenu:     "Exibir _MENU_ registros",
                        loadingRecords: "Carregando...",
                        processing:     "Processando...",
                        search:         "Buscar:",
                        zeroRecords:    "Nenhum resultado encontrado",
                        paginate: {
                            first:      "Primeira",
                            last:       "Última",
                            next:       "Próxima",
                            previous:   "Anterior"
                        }
                    },
                    // Desabilita a busca padrão do DataTables
                    dom: 'lrtip'
                });

                // Busca customizada
                $('#buscaGeral').on('keyup', function() {
                    tabela.search(this.value).draw();
                });

                // Popular o filtro de categoria com valores únicos
                var categoriasUnicas = [];
                tabela.column(7).data().unique().sort().each(function (d, j) {
                    if (d && d.trim() !== '') {
                        categoriasUnicas.push(d);
                    }
                });
                
                var selectCategoria = $('#filtroCategoria');
                categoriasUnicas.forEach(function(categoria) {
                    selectCategoria.append('<option value="' + categoria + '">' + categoria + '</option>');
                });

                // Filtro de Status
                $('#filtroStatus').on('change', function() {
                    var valorStatus = this.value;
                    
                    if (valorStatus === '') {
                        // Se "Todos" for selecionado, remove o filtro
                        tabela.column(5).search('').draw();
                    } else {
                        // Filtra pela coluna de Status (índice 4)
                        tabela.column(5).search(valorStatus).draw();
                    }
                });

                // Filtro de Categoria
                $('#filtroCategoria').on('change', function() {
                    var valorCategoria = this.value;
                    tabela.column(7).search(valorCategoria).draw();
                });
            } );
        </script>
    </body>
</html>