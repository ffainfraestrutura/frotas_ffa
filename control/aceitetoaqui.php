<?php
date_default_timezone_set('America/Sao_Paulo');
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '512M');
$hoje = date('Y-m-d H:i:s');
$data = date('Y-m-d');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once "../func/log.php";

$id = $_POST['id'];
$decisao = $_POST['decisao'];

$sql = "UPDATE bdfrota.tbtoaqui SET aceite = '$decisao', dthoraaceite = '$hoje' where idtbtoaqui = '$id'";
//echo "$sql";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);


if ($decisao == '1') {
    $mysqli2 = new mysqli("10.17.0.5", "blvp", "@F3r7n6c2", "bdaniel");
    $sql = "SELECT matricula, motivo, obs, latitude, longitude, TIME(hora) as hora, DATE(data) as data, endereco FROM bdfrota.tbtoaqui WHERE idtbtoaqui = '$id'";
    //echo "$sql <br>";
    $result = $mysqli2->query($sql);
    while ($row = $result->fetch_assoc()) {

        $resultado = mysql_query($sql) or die(mysql_error());
        $acao = 'aceitou endereco';
        $flag = '1';
        $logresult = enviarlog($hoje, $acao, $flag, $row['matricula'], $matricula, '', '');

        $matricula = $row['matricula'];
        $numero_de_ordem = 'To Aqui';
        $endereco = $row['endereco'];
        $eta = $row['hora'];
        $fim = $row['hora'];
        $latitude = $row['latitude'];
        $longitude = $row['longitude'];
        $datap = $row['data'];




        if ($endereco == '') {
            $insert_sql = "INSERT INTO bdfrota.tberroosrota (endereco,  provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco',  '$matricula', '$datap', '$eta', '$fim', '$numero_de_ordem', '0')";

            $mysqli2->query($insert_sql);
            echo "<script language='javascript' type='text/javascript'>alert('ERRO NO ACEITE');window.location=\"../aceitetoaqui.php\"</script>";

            exit();
        }

        // Segunda Query para verificar se é da Claro ou não
        $segunda_query = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
        $resultado_segunda_query = $mysqli2->query($segunda_query);

        if ($resultado_segunda_query) {
            $row_segunda_query = $resultado_segunda_query->fetch_assoc();
            $ccusto = $row_segunda_query['ccusto'];

            // Verificar se o ccusto é da Claro ou não
            if (stripos($ccusto, 'Claro') !== false) {
                $insert_sql = "INSERT INTO bdfrota.tbosrotalatlong (endereco, data, eta, fim, latitude, longitude, matricula, numero_de_ordem) VALUES('$endereco', '$datap', '$eta', '$fim', '$latitude', '$longitude', '$matricula', '$numero_de_ordem')";
                //echo "$insert_sql <br>";
                $mysqli2->query($insert_sql);
                // Faça algo específico para a Claro
            } else {
                $insert_sql = "INSERT INTO bdfrota.tbosrota (endereco, data, eta, fim, latitude, longitude, matricula, numero_de_ordem) VALUES('$endereco', '$datap', '$eta', '$fim', '$latitude', '$longitude', '$matricula', '$numero_de_ordem')";
                //echo "$insert_sql <br>";
                $mysqli2->query($insert_sql);
                // Faça algo específico para não ser da Claro
            }
        } else {
            echo "Erro ao executar a segunda query.\n";
        }






    }

}

echo "<script language='javascript' type='text/javascript'>alert('To Aqui Aceito com sucesso');window.location=\"../aceitetoaqui.php\"</script>";








?>