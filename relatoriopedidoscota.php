<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$data = date('Y-m-d');
include_once "conecta2.php";
include_once "func/log.php";

$datai = isset($_POST['datai']) ? $_POST['datai'] : date('Y-m-01'); // Primeiro dia do mês atual
$dataf = isset($_POST['dataf']) ? $_POST['dataf'] : date('Y-m-d');   // Hoje
$status_filtro = isset($_POST['status_filtro']) ? $_POST['status_filtro'] : 'todos';
$coordenador_filtro = isset($_POST['coordenador_filtro']) ? $_POST['coordenador_filtro'] : $matricula; // Por padrão, coordenador logado

// Função para buscar informações do funcionário
function infoFuncionario($conexao, $matricula)
{
    $sql = "SELECT * FROM bdaniel.tbfuncionario WHERE matricula='$matricula'";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
    return $row;
}

// Função para buscar informações do coordenador
function infoCoordenador($conexao, $matricula)
{
    $sql = "SELECT * FROM bdfrota.tbcoord WHERE matricula='$matricula'";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
    return $row;
}

// Função para formatar status
function formatarStatus($status)
{
    switch($status) {
        case 'aprovado': return '<span class="badge bg-success">Aprovado</span>';
        case 'reprovado': return '<span class="badge bg-danger">Reprovado</span>';
        case 'pendente': return '<span class="badge bg-warning text-dark">Pendente</span>';
        default: return '<span class="badge bg-secondary">N/A</span>';
    }
}

if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
}

header("Content-type: text/html; charset=utf-8");
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
    <link href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />

    <style>
        .status-pendente { background-color: #fff3cd; }
        .status-aprovado { background-color: #d1edff; }
        .status-reprovado { background-color: #f8d7da; }
        .valor-alto { font-weight: bold; color: #dc3545; }
        .valor-normal { color: #28a745; }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">

                <h1 class="h1 pt-2 pb-2">
                    <i class="fas fa-chart-line"></i>
                    Relatório de Pedidos de Cota
                </h1>
                
                <form action="./relatoriopedidoscota.php" method="post" class="mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-filter"></i> Filtros do Relatório</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="datai">
                                        <h6>Data Inicial:<span style='color: red;'>*</span></h6>
                                    </label>
                                    <input type="date" class="form-control" id="datai" name="datai" value="<?php echo $datai; ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label for="dataf">
                                        <h6>Data Final:<span style='color: red;'>*</span></h6>
                                    </label>
                                    <input type="date" class="form-control" id="dataf" name="dataf" value="<?php echo $dataf; ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label for="status_filtro">
                                        <h6>Status:</h6>
                                    </label>
                                    <select class="form-select" id="status_filtro" name="status_filtro">
                                        <option value="todos" <?php echo $status_filtro == 'todos' ? 'selected' : ''; ?>>Todos os Status</option>
                                        <option value="pendente" <?php echo $status_filtro == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="aprovado" <?php echo $status_filtro == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                        <option value="reprovado" <?php echo $status_filtro == 'reprovado' ? 'selected' : ''; ?>>Reprovado</option>
                                    </select>
                                </div>

                                <?php if($perfil == '0' || $perfil == '1'): // Admin pode ver todos os coordenadores ?>
                                <div class="col-md-3">
                                    <label for="coordenador_filtro">
                                        <h6>Coordenador:</h6>
                                    </label>
                                    <select class="form-select" id="coordenador_filtro" name="coordenador_filtro">
                                        <option value="">Todos os Coordenadores</option>
                                        <?php
                                        $sql_coords = "SELECT DISTINCT c.matricula, f.nome 
                                                      FROM bdfrota.tbcoord c 
                                                      JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula 
                                                      ORDER BY f.nome";
                                        $result_coords = mysqli_query($con, $sql_coords);
                                        while($coord = mysqli_fetch_assoc($result_coords)):
                                        ?>
                                        <option value="<?php echo $coord['matricula']; ?>" 
                                                <?php echo $coordenador_filtro == $coord['matricula'] ? 'selected' : ''; ?>>
                                            <?php echo $coord['matricula'] . ' - ' . $coord['nome']; ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                                    <i class="fas fa-eraser"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-table"></i>
                            Relatório de Pedidos - Período: <?php echo date('d/m/Y', strtotime($datai)) . ' a ' . date('d/m/Y', strtotime($dataf)); ?>
                        </div>
                        <div>
                            <a href="./dashboard.php" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                Voltar para Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered table-hover'>
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Data/Hora</th>
                                    <th>Funcionário</th>
                                    <th>Centro de Custo</th>
                                    <th>Tipo de Cota</th>
                                    <th>Valor Pedido (R$)</th>
                                    <th>Valor Aprovado (R$)</th>
                                    <th>Status</th>
                                    <th>Coordenador</th>
                                    <th>Justificativa</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // Montar query base
                                $sql = "SELECT p.*, f.nome as nome_funcionario, f.ccusto, 
                                              coord.nome as nome_coordenador,
                                              CASE 
                                                WHEN p.decisao = 1 THEN 'aprovado'
                                                WHEN p.decisao = 2 THEN 'reprovado' 
                                                ELSE 'pendente'
                                              END as status_descricao
                                        FROM bdfrota.tbsaldo p 
                                        JOIN bdaniel.tbfuncionario f ON p.matricula = f.matricula
                                        LEFT JOIN bdaniel.tbfuncionario coord ON p.coordenador = coord.matricula
                                        WHERE DATE(p.data) BETWEEN '$datai' AND '$dataf'";
                                
                                // Adicionar filtros
                                if($status_filtro != 'todos') {
                                    switch($status_filtro) {
                                        case 'pendente': $sql .= " AND (p.decisao IS NULL OR p.decisao = 0)"; break;
                                        case 'aprovado': $sql .= " AND p.decisao = 1"; break;
                                        case 'reprovado': $sql .= " AND p.decisao = 2"; break;
                                    }
                                }
                                
                                if(!empty($coordenador_filtro)) {
                                    $sql .= " AND p.coordenador = '$coordenador_filtro'";
                                } elseif($perfil != '0' && $perfil != '1') {
                                    // Se não for admin, mostrar apenas pedidos do coordenador logado
                                    $sql .= " AND p.coordenador = '$matricula'";
                                }
                                
                                $sql .= " ORDER BY p.data DESC";
                                
                                $stmt = mysqli_query($con, $sql) or die(mysqli_error($con));
                                $total_pedidos = 0;
                                $total_valor_pedido = 0;
                                $total_valor_aprovado = 0;
                                $total_pendentes = 0;
                                $total_aprovados = 0;
                                $total_reprovados = 0;

                                while($row = mysqli_fetch_assoc($stmt)) {
                                    $total_pedidos++;
                                    
                                    // Formatação de valores
                                    $valor_pedido = number_format($row['valor'], 2, ',', '.');
                                    $valor_aprovado = $row['valorapp'] ? number_format($row['valorapp'], 2, ',', '.') : '-';
                                    $data_formatada = date('d/m/Y H:i', strtotime($row['data']));
                                    
                                    // Contabilização
                                    $total_valor_pedido += $row['valor'];
                                    if($row['valorapp']) $total_valor_aprovado += $row['valorapp'];
                                    
                                    switch($row['status_descricao']) {
                                        case 'pendente': $total_pendentes++; break;
                                        case 'aprovado': $total_aprovados++; break;
                                        case 'reprovado': $total_reprovados++; break;
                                    }
                                    
                                    // Classe CSS baseada no status
                                    $classe_linha = 'status-' . $row['status_descricao'];
                                    
                                    // Classe para valores altos (acima de R$ 100)
                                    $classe_valor = $row['valor'] > 100 ? 'valor-alto' : 'valor-normal';
                                    
                                    // Tipo de cota formatado
                                    $tipo_cota = '';
                                    switch($row['tipocota']) {
                                        case '1': $tipo_cota = 'Cota Normal'; break;
                                        case '2': $tipo_cota = 'Cota Extra'; break;
                                        case '3': $tipo_cota = 'Plantão'; break;
                                        default: $tipo_cota = 'Não definido'; break;
                                    }
                                    
                                    echo "
                                    <tr class='$classe_linha'>
                                        <td><strong>#{$row['idtbsaldo']}</strong></td>
                                        <td>$data_formatada</td>
                                        <td>
                                            <strong>{$row['matricula']}</strong><br>
                                            <small>{$row['nome_funcionario']}</small>
                                        </td>
                                        <td>{$row['ccusto']}</td>
                                        <td>
                                            <span class='badge bg-info'>$tipo_cota</span>
                                        </td>
                                        <td class='$classe_valor'>R$ $valor_pedido</td>
                                        <td>R$ $valor_aprovado</td>
                                        <td>" . formatarStatus($row['status_descricao']) . "</td>
                                        <td>
                                            <small>{$row['coordenador']}</small><br>
                                            <small>{$row['nome_coordenador']}</small>
                                        </td>
                                        <td>
                                            <small>" . (strlen($row['justsup']) > 50 ? 
                                                      substr($row['justsup'], 0, 50) . '...' : 
                                                      $row['justsup']) . "</small>
                                        </td>
                                        <td>
                                            <button class='btn btn-sm btn-outline-info' 
                                                    onclick='verDetalhes({$row['idtbsaldo']})' 
                                                    title='Ver detalhes'>
                                                <i class='fas fa-eye'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Resumo Estatístico -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5><?php echo $total_pedidos; ?></h5>
                                        <small>Total de Pedidos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <h5><?php echo $total_pendentes; ?></h5>
                                        <small>Pendentes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5><?php echo $total_aprovados; ?></h5>
                                        <small>Aprovados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5><?php echo $total_reprovados; ?></h5>
                                        <small>Reprovados</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5>R$ <?php echo number_format($total_valor_pedido, 2, ',', '.'); ?></h5>
                                        <small>Total Valor Pedido</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>R$ <?php echo number_format($total_valor_aprovado, 2, ',', '.'); ?></h5>
                                        <small>Total Valor Aprovado</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modal para detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalhesContent">
                    <!-- Conteúdo carregado via AJAX -->
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.6/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>


<script src="src/js/scripts.js"></script>

<script>
    $(document).ready(function () {
        new DataTable('#datatablesSimple', {
            layout: {
                topStart: {
                    //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    buttons: [{ extend: 'excelHtml5', title: 'Relatório de Pedidos de Cota' }]
                }
            },
            scrollX: true,
            language: {
                decimal: "",
                emptyTable: "Nada para exibir",
                info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                infoEmpty: "Exibindo página 0 de 0 de 0 registros",
                infoFiltered: "(filtrado do total de _MAX_ registros)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Exibir _MENU_ registros",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Buscar:",
                zeroRecords: "Nenhum resultado encontrado",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Próxima",
                    previous: "Anterior"
                }
            },
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, 'Todos']
            ],
            order: [[1, 'desc']] // Ordenar por data decrescente
        });
    });

    function limparFiltros() {
        document.getElementById('datai').value = '<?php echo date('Y-m-01'); ?>';
        document.getElementById('dataf').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('status_filtro').value = 'todos';
        <?php if($perfil == '0' || $perfil == '1'): ?>
        document.getElementById('coordenador_filtro').value = '';
        <?php endif; ?>
    }

    function verDetalhes(id) {
        // Implementar AJAX para carregar detalhes do pedido
        $.ajax({
            url: './ajax/detalhes_pedido.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                $('#detalhesContent').html(response);
                $('#modalDetalhes').modal('show');
            },
            error: function() {
                alert('Erro ao carregar detalhes do pedido.');
            }
        });
    }
</script>

</html>
