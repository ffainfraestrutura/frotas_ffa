<?php
require_once __DIR__ . '/formulario-conserto-pneu-funcoes.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$registro = $id ? formularioBuscar($conexao, $id) : null;
if (!$registro) { http_response_code(404); exit('Formulário não encontrado.'); }
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Conserto de Pneu/Borracharia</title>
<link href="../src/css/styles.css" rel="stylesheet">
<style>
body { background:#eee; } .folha { width:190mm; min-height:260mm; margin:20px auto; background:#fff; padding:12mm; box-shadow:0 1px 8px #aaa; }
.cabecalho { border-bottom:3px solid #2e2851; margin-bottom:30px; } .rotulo { color:#666; font-size:.85rem; margin-bottom:2px; } .resposta { white-space:pre-wrap; overflow-wrap:anywhere; }
.cabecalho-empresa { display:grid; grid-template-columns:75px minmax(0, 1fr); align-items:center; gap:10px; border-bottom:3px solid #2e2851; padding:0 5px 14px; }
.logo-ffa { width:72px; height:72px; object-fit:contain; } .titulo-empresa { margin:0; color:#20204e; font-size:23px; font-weight:800; letter-spacing:.02em; white-space:nowrap; }
.cabecalho-conteudo { min-width:0; } .data-empresa { width:120px; margin:4px 0 0 auto; color:#111; font-size:12px; line-height:1.35; text-align:left; } .data-empresa strong { display:block; font-size:12px; }
.ficha-empresa { width:100%; border-collapse:collapse; margin-top:22px; color:#111; } .ficha-empresa th,.ficha-empresa td { border:1px solid #222; padding:8px 11px; vertical-align:top; font-size:12px; }
.ficha-empresa th { width:28%; text-transform:uppercase; font-size:11px; font-weight:800; } .campo-longo { min-height:64px; white-space:pre-wrap; }
@page { size:A4 portrait; margin:10mm; }
@media print { body { background:#fff; } .folha { width:auto; min-height:0; margin:0; box-shadow:none; padding:0; } .nao-imprimir { display:none!important; } }
</style></head><body>
<div class="folha">
  <div class="d-flex justify-content-end gap-2 nao-imprimir"><a class="btn btn-secondary" href="listagem-formularios-conserto-pneu.php">Voltar</a><button class="btn btn-primary" onclick="window.print()">Imprimir / Salvar como PDF</button></div>
  <header class="cabecalho-empresa">
    <img class="logo-ffa" src="../src/images/logoffa.png" alt="Logo FFA Infraestrutura">
    <div class="cabecalho-conteudo">
      <h1 class="titulo-empresa">CONSERTO DE PNEU/BORRACHARIA</h1>
      <div class="data-empresa"><strong>DATA:</strong><?= date('d/m/Y', strtotime($registro['data'])) ?></div>
    </div>
  </header>
  <table class="ficha-empresa">
    <tr><th>CC</th><td><?= formularioEscapar($registro['cc']) ?></td></tr>
    <tr><th>Motorista</th><td><?= formularioEscapar($registro['motorista_nome']) ?></td></tr>
    <tr><th>Placa</th><td><?= formularioEscapar($registro['placa']) ?></td></tr>
    <tr><th>Defeito</th><td><div class="campo-longo"><?= formularioEscapar($registro['defeito']) ?></div></td></tr>
    <tr><th>Serviço executado</th><td><div class="campo-longo"><?= formularioEscapar($registro['servico_executado']) ?></div></td></tr>
    <tr><th>Valor total</th><td>R$ <?= formularioEscapar($registro['valor_total']) ?></td></tr>
    <tr><th>Autorizado por</th><td><?= formularioEscapar($registro['autorizado_por']) ?></td></tr>
    <tr><th>Endereço</th><td><div class="campo-longo"><?= formularioEscapar($registro['endereco']) ?></div></td></tr>
  </table>
</div></body></html>
