<?php


include"conecta.php";

$sql = "SELECT matricula, placa, valor FROM bdfrota.tbplacaxvalor";
$resultado = mysql_query($sql) or die (mysql_error());
while($row = mysql_fetch_array($resultado)){

	$matricula = $row[0];
	$saldo = $row[2];

	$kmproj = Round($saldo * 5.01 * 11,2);


	$sql2 = "INSERT INTO bdfrota.tbsaldo (matricula, data, sldprjtd, saldo, valoraplicado, orcsemanal, kmproj) VALUES ('$matricula', '2023-02-13', '$saldo', '$saldo', '$saldo', '$saldo', '$kmproj')";
	echo "$sql2 <br>";
	$resultado2 = mysql_query($sql2) or die (mysql_error());

}



?>