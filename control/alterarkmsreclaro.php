<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupere os valores enviados pelo formulário
    $mattec = $_POST['mattec'];
    $datadia = $_POST['datadia'];
    $matop = $_POST['matop'];
    $dataagora = $_POST['dataagora'];
    $decisaocol = $_POST['decisaocol'];
    $numericInput = $_POST['numericInput'];
    $justificativacol = $_POST['jutificativacol'];

    $sql = "SELECT * FROM bdfrota.tbkmdiarioedit where matricula = '$mattec' and dataedit = '$datadia'";
        
    $resultado = mysql_query($sql) or die (mysql_error()); 
    if (mysql_num_rows($resultado)<=0){

        $sql = "INSERT INTO bdfrota.tbkmdiarioedit (matricula, dataedit, kmsedit, posneg, matop, datamud, motivo) VALUES ('$mattec', '$datadia', '$numericInput', '$decisaocol', '$matop', '$dataagora', '$justificativacol')";
        //echo "$sql";
        $resultado = mysql_query($sql) or die (mysql_error()); 


    } else {

        $sql = "UPDATE bdfrota.tbkmdiarioedit SET motivo = '$justificativacol', kmsedit = $numericInput, posneg = '$decisaocol', matop = '$matop' where matricula = '$mattec' and dataedit = '$datadia'";
        
        //echo "$sql";
        $resultado = mysql_query($sql) or die (mysql_error()); 

    }


    echo"<script language='javascript' type='text/javascript'>alert('Inserido com sucesso');window.location=\"../detalhes-kmoscotaind-edit.php\"</script>";

} else {
    echo"<script language='javascript' type='text/javascript'>alert('Erro no formulário');window.location=\"../detalhes-kmoscotaind-edit.php\"</script>";
}
?>
