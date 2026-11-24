<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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

include "conecta2.php";
$datai = "";
if (isset($_POST['datai'])) {
    $datai = $_POST['datai'];
    $diaSemana = date('N', strtotime($_POST['datai'])); // 1 = segunda, 7 = domingo
    if ($diaSemana != 1) {
        $diaSemana = date('Y-m-d', strtotime('monday this week', strtotime($datai)));
    } else {
        $diaSemana = date('Y-m-d', strtotime($datai));
    }

    // Consulta funcionários
    $sqlFuncionarios = "SELECT nome, matricula, ccusto FROM bdaniel.tbfuncionario";
    $stmt = mysqli_query($conexao, $sqlFuncionarios) or die(mysqli_error($conexao));

    $funcionarios = [];
    while ($rowFunc = mysqli_fetch_assoc($stmt)) {
        $funcionarios[$rowFunc['matricula']] = [
            'nome' => $rowFunc['nome'],
            'ccusto' => $rowFunc['ccusto']
        ];
    }

    $sqlGerente = "";
    if(isset($_POST['gerente']) && !empty(trim($_POST['gerente']))){
        $gerente = trim($_POST['gerente']);
        $sqlGerente = "WHERE matricula_gerente = '$gerente'";
    }

    // Consulta principal
    $sqlSaldo = "
        WITH cte_debito AS (
            SELECT 
                tg.matricula AS matricula_gerente,
                tl.matricula AS matricula_aprovante,
                tl.valor_novo as valor,
                '' as saldo_atual,
                'debito' AS tipo,
                tl.acao AS descricao,
                tl.data_e_hora as data_transacao
            FROM
                bdfrota.tbgerente tg
                JOIN bdfrota.tblog tl ON tl.mat_autor = tg.matricula
            WHERE
                tl.flag = 2
                and (tl.acao = 'Aceitou orçamento extra' or tl.acao = 'Aceitou or?amento extra' or tl.acao = 'Distribuição de orçamento extra' or tl.acao = 'Aceitou cota extra Escalonada')
            AND DATE(tl.data_e_hora) >= '$datai'
        ),
        cte_credito AS (
            SELECT 
                tg.matricula AS matricula_gerente,
                tl.mat_autor AS matricula_aprovante,
                tl.valor_novo as valor,
                '' as saldo_atual,
                'credito' AS tipo,
                'Recebimento de Orçamento Complementar' AS descricao,
                tl.data_e_hora as data_transacao
            FROM
                bdfrota.tbgerente tg
                JOIN bdfrota.tblog tl ON tl.matricula = tg.matricula
            WHERE
                tl.flag = 2
                and tl.acao = 'Orçamento extra frota'
            AND DATE(tl.data_e_hora) >= '$datai'
        ),
        cte_saldo_inicial as (
            SELECT tg.matricula as matricula_gerente,
                '' as matricula_aprovante,
                hc.valor as valor,
                tg.valor as saldo_atual,
                'inicial' as tipo,
                'Saldo Inicial' as descricao,
                data_referencia as data_transacao
            FROM bdfrota.tbgerente tg
            JOIN bdfrota.historico_cota_recebida_gerente hc
            ON tg.matricula = hc.matricula
            where data_referencia = '$datai'
        )
        SELECT * FROM
        (
        SELECT *
        FROM cte_debito
        UNION ALL
        SELECT *
        FROM cte_credito
        UNION ALL 
        SELECT *
        FROM cte_saldo_inicial
        ) AS uniao
        $sqlGerente
        ORDER BY uniao.matricula_gerente, uniao.data_transacao;
    ";

    $stmt = mysqli_query($conexao, $sqlSaldo) or die(mysqli_error($conexao));

    $arrayFinal = [];

    while ($row = mysqli_fetch_assoc($stmt)) {
        $matricula = $row['matricula_gerente'];
        $matricula_aprovante = trim((string)$row['matricula_aprovante']);
        // Inicializa o gerente se ainda não existir
        if (!isset($arrayFinal[$matricula])) {
            $arrayFinal[$matricula] = [
                'nome' => $funcionarios[$matricula]['nome'] ?? 'Desconhecido',
                'ccusto' => $funcionarios[$matricula]['ccusto'] ?? '',
                'saldo_inicial' => 0,
                'saldo_atual' => 0,
                'transacoes' => []
            ];
        }

        // Se for o saldo inicial, salva os saldos
        if ($row['tipo'] === 'inicial') {
            $arrayFinal[$matricula]['saldo_inicial'] = (float) $row['valor'];
            $arrayFinal[$matricula]['saldo_atual'] = (float) $row['saldo_atual'];
        } else {
            // Adiciona transação
            $arrayFinal[$matricula]['transacoes'][] = [
                'tipo' => $row['tipo'],
                'descricao' => $row['descricao'],
                'autor' => $funcionarios[$matricula_aprovante]['nome'] ?? $matricula_aprovante,
                'valor' => (float) $row['valor'],
                'data' => $row['data_transacao']
            ];
        }
    }
}

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
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
    <title>Extrato de Orçamento Gerente | Portal FFA</title>
    <link href="https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <style>
.bg-red {
    background-color: #ffd6d6; /* vermelho pastel */
}

.bg-green {
    background-color: #d6ffda; /* verde pastel */
}
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Extrato de Orçamento por Gerente</h1>
                <form action="#" method="post">
                    <div class="row">
                    <div class="col">
                        <h5>Selecione a semana:</h5>
                        <label for="datai">
                            <h5>Semana (Segunda-feira):<span style='color: red;'>*</span></h5>
                        </label>
                        <input type="date" class="form-control" id="datai" name="datai" <?php print "value='$datai'"; ?>
                            required>
                    </div>
                    <div class="col">
                        <h5>Selecione um gerente:</h5>
                        <label for="gerente">
                            <h5>Gerente:</h5>
                        </label>
                        <select class="form-control" id="gerente" name="gerente">
                            <option value="">-- Todos --</option>
                            <?php
                            // Buscar gerentes
                            $sqlG = "SELECT g.matricula, f.nome FROM bdfrota.tbgerente g join bdaniel.tbfuncionario f on g.matricula = f.matricula order by f.nome";
                            $resG = mysqli_query($conexao, $sqlG);

                            while ($g = mysqli_fetch_assoc($resG)) {
                                $selected = (isset($_POST['gerente']) && $_POST['gerente'] == $g['matricula']) ? 'selected' : '';
                                echo "<option value='{$g['matricula']}' $selected>{$g['nome']} ({$g['matricula']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                    </div>
                    <div class='mt-2'>
                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>
                    </div>

                </form>
                <div class="card mt-4 mb-4">
                    <!-- <div class="card-header">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a>
                    </div> -->

                    <?php if (!empty($arrayFinal)) { ?>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th></th>
                                        <th>Gerente</th>
                                        <th>Centro de Custo</th>
                                        <th>Saldo Inicial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($arrayFinal as $matricula => $gerente) { ?>
                                        <tr data-transacoes='<?= json_encode($gerente["transacoes"]); ?>'>
                                            <td class="details-control" style="cursor:pointer;">+</td>
                                            <td><?= $gerente['nome'] ?></td>
                                            <td><?= $gerente['ccusto'] ?></td>
                                            <td><?= number_format($gerente['saldo_inicial'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="card-body">
                            <p>Selecione uma semana
                                (<?php echo $datai; ?>).</p>
                        </div>
                    <?php } ?>
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

    const table = $('#datatablesSimple').DataTable({
        ordering: false,
        layout: {
            topStart: {
                buttons: [{ extend: 'excelHtml5', title: 'Relatório Remanejamento' }]
            }
        },
        language: {
            decimal: "",
            emptyTable: "Nada para exibir",
            info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            infoEmpty: "Exibindo página 0 de 0 de 0 registros",
            infoFiltered: "(filtrado do total de _MAX_ registros)",
            search: "Buscar:",
            zeroRecords: "Nenhum resultado encontrado",
            paginate: {
                first: "Primeira",
                last: "Última",
                next: "Próxima",
                previous: "Anterior"
            }
        }
    });

    // evento de clique para expandir/collapse
    $('#datatablesSimple tbody').on('click', 'td.details-control', function () {

        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // fechar
            row.child.hide();
            tr.removeClass('shown');
            $(this).text('+');
        } else {
            // abrir
            let dataTransacoes = tr.data('transacoes');
            row.child(format(dataTransacoes)).show();
            tr.addClass('shown');
            $(this).text('–');
        }

    });
});
</script>

<script>
function format(transacoes) {
    if (transacoes.length === 0) {
        return '<div class="p-3 text-muted">Nenhuma transação encontrada</div>';
    }

    let html = `
        <table class="table table-sm table-bordered bg-light">
            <thead class="table-secondary">
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Autor / Beneficiário</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
    `;

    transacoes.forEach(t => {
        html += `
<tr class="${t.tipo == 'debito' ? 'bg-red' : 'bg-green'}">
                <td>${t.data}</td>
                <td>${t.tipo}</td>
                <td>${t.descricao}</td>
                <td>${t.autor}</td>
                <td>${parseFloat(t.valor).toLocaleString('pt-BR', {minimumFractionDigits:2})}</td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;
    return html;
}
</script>


</html>