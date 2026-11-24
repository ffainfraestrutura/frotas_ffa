<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
include './vendor/autoload.php';

while (2 > 1) {

/* selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$diahoje = date('Y-m-d');




include "./conecta.php";



// Nome do diretório
$dirfim = 'destinoreclaro/';
// Listando arquivos
$diretorio = dir($dirfim);

while($arquivo = $diretorio -> read()){
 
  if($arquivo != '.' && $arquivo != '..'){
$sql = "SELECT matricula FROM bdfrota.tbimportrotareclaro where data >= '$diahoje 00:00:00' and importado = 0";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$matriculaat = $row[0];
    $reader = IOFactory::createReader('Xlsx', 'xls'); 
    //$spreadsheet = $reader->load($_FILES['import_excel']['tmp_name']);
    $spreadsheet = $reader->load("./".$dirfim.$arquivo); 
    //  $reader->load("./destino/000aaa.xlsx"); ./destino/202304031125nome.xlsx

    $data = $spreadsheet->getActiveSheet()->toArray();

    foreach($data as $row){
      
      
      if($row[0]<>'Matricula'){
        $matricula = $row[0];
        $nome=$row[1];
        $endereco1 = $row[2];
        $numero = $row[3];
        $bairro = $row[4];
        $cidade = $row[5];
        $data = $row[6];
        $hora = $row[7];
        //echo "end 1 $endereco1, $numero, $bairro - $cidade, $estado <br>";
        $enderecocomp = str_replace("'", "", "$endereco1, $numero, $bairro - $cidade");   
        //echo "end 2 $enderecocomp <br";    
if ($enderecocomp <> 'Rua, Número, Bairro - Cidade, ' and $enderecocomp <> ', ,  - , ') {

       $dateParts = explode("/", $data);
          $day = $dateParts[0];
    $month = $dateParts[1];
    $year = $dateParts[2];
    $dataos = "$year-$month-$day";
    $dataos = str_replace("--", "", $dataos);


$sql = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matricula' and status <> 'demitido'";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$provedor = $row[0];


$address = "$enderecocomp"; // Substitua pelo endereço desejado
$accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA"; // Substitua pelo seu token do Mapbox

// URL da API de geocodificação do Mapbox
$apiUrllxl = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($address) . ".json?access_token=" . $accessToken;
//echo "<br> $apiUrllxl <br>";
// Fazer a requisição à API de geocodificação
$response = file_get_contents($apiUrllxl);

$geocodeData = json_decode($response, true);

$enderecocomp = utf8_decode($enderecocomp);
// Verificar se a resposta é válida e se há resultados
if (!empty($geocodeData['features']) && isset($geocodeData['features'][0]['geometry']['coordinates'])) {
    $latitude = $geocodeData['features'][0]['geometry']['coordinates'][1];
    $longitude = $geocodeData['features'][0]['geometry']['coordinates'][0];
    $longitude = str_replace(",", ".", $longitude);
    $latitude = str_replace(",", ".", $latitude);
    //echo "$latitude x $longitude <br>";
} else {


    $sql2 = "INSERT INTO bdfrota.tberroosrotamassa (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag, matautor, horasubida) 
                   VALUES('$enderecocomp', '$cidade', '$provedor', '$dataos', '$hora:00', '$hora:00', 'EM MASSA', '0', '$matriculaat', '$hoje')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());


    
}


if ($latitude == '' or $longitude == '') {
  
$sql2 = "INSERT INTO bdfrota.tberroosrotamassa (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag, matautor, horasubida) 
                   VALUES('$enderecocomp', '$cidade', '$provedor', '$dataos', '$hora:00', '$hora:00', 'EM MASSA', '0', '$matriculaat', '$hoje')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());



}

if ($latitude >= -26 && $latitude <= -20 && $longitude >= -50 && $longitude <= -40) {

$apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/$longitude,$latitude.json?access_token=$accessToken";
//echo "<br> $apiUrlReverseGeocode <br> ---EU QUERIA SER VIADO--- <br>";
$response = file_get_contents($apiUrlReverseGeocode);

if ($response !== false) {

  // Converter a resposta JSON em um array associativo
    $data = json_decode($response, true);

    // Verificar se há resultados de geocodificação

    if (!empty($data['features'])) {

      // O endereço estará no primeiro resultado (índice 0)
        $enderecolxl = str_replace("'", "", $data['features'][0]['place_name']);
        //echo "end 3 $enderecolxl <br>";
        
        $sql2 = "INSERT INTO bdfrota.tbosrotalatlong (provedor, endereco, data, eta, fim, matricula, operador, foratoa, motivo, latitude, longitude, dataenvio) values ('$provedor', '$enderecolxl', '$dataos', '$hora:00', '$hora:00', '$matricula', '$matriculaat', '1', 'EM MASSA', '$latitude', '$longitude', '$hoje')";
        //echo "$sql2 <br>";
$resultado2 = mysql_query($sql2) or die (mysql_error());
$ultimoIDInserido = mysql_insert_id();

    } else {
        $sql2 = "INSERT INTO bdfrota.tberroosrotamassa (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag, matautor, horasubida) 
                   VALUES('$enderecocomp', '$cidade', '$provedor', '$dataos', '$hora:00', '$hora:00', 'EM MASSA', '0', '$matriculaat', '$hoje')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    }

} else {
    $sql2 = "INSERT INTO bdfrota.tberroosrotamassa (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag, matautor, horasubida) 
                   VALUES('$enderecocomp', '$cidade', '$provedor', '$dataos', '$hora:00', '$hora:00', 'EM MASSA', '0', '$matriculaat', '$hoje')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

}
    
} else {
      $sql2 = "INSERT INTO bdfrota.tberroosrotamassa (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag, matautor, horasubida) 
                   VALUES('$enderecocomp', '$cidade', '$provedor', '$dataos', '$hora:00', '$hora:00', 'EM MASSA', '0', '$matriculaat', '$hoje')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

}

}}
}       
unlink("./".$dirfim.$arquivo); 

      }
    }
$sql = "UPDATE bdfrota.tbimportrotareclaro SET importado = 1 where data >= '$diahoje 00:00:00' and importado = 0";
$resultado = mysql_query($sql) or die (mysql_error());

mysql_close($conexao);

sleep(300);

}




?>