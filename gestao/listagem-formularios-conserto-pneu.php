<?php
require_once __DIR__ . '/formulario-conserto-pneu-funcoes.php';

$sql = 'SELECT id, data, cc, motorista_nome, motorista_matricula, placa, defeito,
               servico_executado, valor_total, autorizado_por, endereco
        FROM bdfrota.tbconsertopneu ORDER BY data DESC, id DESC';
$resultado = mysqli_query($conexao, $sql);
$erroBanco = $resultado ? '' : mysqli_error($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/png" href="../src/images/favicon.png"><title>Consertos de pneu</title>
<link href="../src/css/styles.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
<style>table.dataTable thead th{background-color:#2e2851;color:#fff}.acoes{white-space:nowrap}</style></head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled"><?php include '../menu.php'; ?>
<div id="layoutSidenav_content"><main class="mb-5" style="width:100%"><div class="container-fluid px-4">
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 pb-3">
  <h1 class="h1 mb-0">Consertos de pneu/borracharia</h1><div class="d-flex flex-wrap gap-2">
  <a class="btn btn-dark" href="preencher-formulario-conserto-pneu.php"><i class="fas fa-plus me-2"></i>Novo</a>
  <button class="btn btn-outline-success" id="exportarExcel" type="button" disabled><i class="fas fa-file-excel me-2"></i>Exportar Excel</button></div>
</div>
<?php if (isset($_GET['salvo'])): ?><div class="alert alert-success" role="alert">Formulário salvo com sucesso.</div><?php endif; ?>
<?php if ($erroBanco): ?>
<div class="alert alert-danger" role="alert">Não foi possível carregar os registros. Execute o arquivo <strong>sql/consertos_pneu.sql</strong>.</div>
<?php else: ?><div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-striped table-hover" id="tabelaFormularios"><thead><tr>
<th>Data</th><th>CC</th><th>Motorista</th><th>Placa</th><th>Defeito</th><th>Serviço executado</th><th>Valor total</th><th>Autorizado por</th><th>Endereço</th><th class="text-center">Ações</th>
</tr></thead><tbody><?php while ($registro = mysqli_fetch_assoc($resultado)): ?><tr>
<td data-order="<?= formularioEscapar($registro['data']) ?>"><?= date('d/m/Y', strtotime($registro['data'])) ?></td>
<td><?= formularioEscapar($registro['cc']) ?></td><td><?= formularioEscapar($registro['motorista_nome']) ?></td>
<td><?= formularioEscapar($registro['placa']) ?></td><td><?= formularioEscapar($registro['defeito']) ?></td>
<td><?= formularioEscapar($registro['servico_executado']) ?></td><td><?= formularioEscapar($registro['valor_total']) ?></td>
<td><?= formularioEscapar($registro['autorizado_por']) ?></td><td><?= formularioEscapar($registro['endereco']) ?></td>
<td class="text-center acoes"><a class="btn btn-sm btn-warning" href="editar-formulario-conserto-pneu.php?id=<?= (int) $registro['id'] ?>" title="Editar"><i class="fas fa-pen"></i> Editar</a>
<a class="btn btn-sm btn-secondary" href="visualizar-formulario-conserto-pneu.php?id=<?= (int) $registro['id'] ?>" target="_blank" title="Visualizar e imprimir"><i class="fas fa-file-pdf"></i> Visualizar PDF</a></td>
</tr><?php endwhile; ?></tbody></table></div></div></div><?php endif; ?>
</div></main></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script><script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script><script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script>$(function(){var tabela=$('#tabelaFormularios').DataTable({order:[[0,'desc']],dom:'frtipB',buttons:[{extend:'excelHtml5',title:'',messageTop:'',className:'d-none',filename:'consertos-pneu',exportOptions:{columns:[0,1,2,3,4,5,6,7,8],modifier:{search:'applied',order:'applied',page:'all'}}}],language:{url:'//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'}});$('#exportarExcel').prop('disabled',false).on('click',function(){tabela.button('.buttons-excel').trigger();});});</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script><script src="../src/js/scripts.js"></script>
</body></html>
