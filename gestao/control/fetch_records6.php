<?php
//header("Content-Type: application/json; charset=utf-8;");
// db settings
$hostname = '10.27.0.100';
$username = 'blvp';
$password = 'qrdrps';
$database = 'bdfrota';

$con = mysqli_connect("$hostname", "$username", "$password", "$database") or die("Error a conexão falhou " . mysqli_error($con));

mysqli_set_charset($con, "utf8");

//query que puxa todos cadastrados ativos na tabela condutor
$sql = "SELECT nome, matricula FROM bdfrota.tbcondutor;";


$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));


while($row = mysqli_fetch_array($resultado)) {
    $array[] = $row;
}

$dataset = array(
    "echo" => 1,
    "totalrecords" => count($array),
    "totaldisplayrecords" => count($array),
    "data" => $array
);

echo json_encode($dataset, JSON_PRETTY_PRINT);

mysql_close($con);
?>
