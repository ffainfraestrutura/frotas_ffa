<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$data = date('Y-m-d H:i:s', strtotime('2023-08-21 23:55:00'));
$menos1h = date("Y-m-d H:i:s", strtotime($data . "-1 hour"));
$mais5min = date("Y-m-d H:i:s", strtotime($data . "+5 minutes"));
$hoje = date('Y-m-d',strtotime("$data"));
$mes = date('m',strtotime("$data"));
$ano = date('Y',strtotime("$data"));
$diasmes = date('t',strtotime("$data"));
$mesesc = strftime('%B',strtotime("$data"));
$semesc = strftime('%A',strtotime("$data"));
if (strftime($data . '%A') == "domingo" ){
	$segunda = date('Y-m-d', strtotime($data . 'last monday'));
	$domingo = date('Y-m-d');
} elseif (strftime($data . '%A') == "segunda") {
	$segunda = date('Y-m-d');
	$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
} else {
	$segunda = date('Y-m-d', strtotime($data . 'last monday'));
	$domingo = date('Y-m-d', strtotime($data . 'next sunday'));
}
echo "1h: $menos1h <br> 5min: $mais5min <br> Hoje: $hoje <br> Mes: $mes <br> Ano: $ano <br> Dias Mes: $diasmes <br> Nome Mes: $mesesc <br> Nome dia da semana: $semesc <br> Segunda: $segunda <br> Domingo: $domingo";
?>