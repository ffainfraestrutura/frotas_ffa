<?php
//header("Content-Type: application/json; charset=utf-8;");
// db settings
$hostname = '10.27.0.100';
$username = 'blvp';
$password = 'qrdrps';
$database = 'bdfrota';

$con = mysqli_connect("$hostname", "$username", "$password", "$database") or die("Error a conexão falhou " . mysqli_error($con));

mysqli_set_charset($con, "utf8");

//query que puxa o nome de todos os funcionarios do banco de dados/da empresa            
//$sql = "SELECT idtbfuncionario, matricula, nome FROM bdaniel.tbfuncionario;";

//query que puxa o nome de todos os funcionarios não demitidos do banco de dados/da empresa
$sql = "SELECT idtbfuncionario, matricula, nome FROM bdaniel.tbfuncionario where status <> 'Demitido';";

//query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela cnh, lista apenas os que tem cnh cadastrada
//$sql = "SELECT a.matricula, a.nome from bdaniel.tbfuncionario a, bdfrota.tbcnh b where a.matricula = b.matricula; ";

//query que puxa o nome e o id de todos os funcionarios que estão cadastrados na tabela carrosxcolaborador, lista apenas os que tem cnh cadastrada
//$sql = "SELECT a.matricula, a.nome from bdaniel.tbfuncionario a, bdfrota.tbcarrosxcolaborador b where a.matricula = b.id_colaborador; ";

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
