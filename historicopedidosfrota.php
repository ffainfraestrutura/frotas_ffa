<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$perfil = isset($_SESSION['perfil']) ? (string) $_SESSION['perfil'] : '';
if ($perfil !== '4') {
    echo "<script>alert('Acesso permitido apenas para o perfil Frota');window.location='index.php'</script>";
    exit;
}

include 'conecta.php';
header('Content-type: text/html; charset=utf-8');

$sql = "SELECT p.idtbpedidosgerente AS id_pedido,
               p.matricula,
               p.valor,
               p.justificativa,
               p.data,
               p.flag,
               COALESCE(f.nome, p.matricula) AS solicitante
          FROM bdfrota.tbpedidosgerente p
     LEFT JOIN bdaniel.tbfuncionario f ON f.matricula = p.matricula
         WHERE p.flag IN (1, 2)
      ORDER BY p.data DESC";

$resultado = mysql_query($sql) or die(mysql_error());
$historicoPedidos = array();
while ($row = mysql_fetch_assoc($resultado)) {
    $historicoPedidos[] = $row;
}

$historicoTitulo = 'Histórico de Pedidos - Frota';
$historicoBaseUrl = '';
$historicoBaseDir = __DIR__ . '/';
include './func/historico_pedidos_view.php';