<?php
include("../../conecta2.php");

$hoje = date('Y-m-d H:i:s');
?>

<html>
<head>
    <title>Upload de Arquivos com PHP</title>
</head>
<body>
 
<!--<form method="post" action="teste2.php" enctype="multipart/form-data">
<div class="ms-2 d-flex flex-column col-sm-4">
                <label class="form-label mb-0" for="arquivo"> Excel <span style="color: red;">*</span> </label>
                <input class="form-control" type="file" name="arquivo" required>
              </div>
<input type="submit" value="Enviar" />

</form>-->

<?php
$sql = "SELECT datavistoria FROM bdfrota.tbvistoria";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH) ){
    $datavistoria = $row['datavistoria'];

    $ultseg = date('Y-m-d', strtotime($datavistoria . 'last monday'));
    //$ultseg =

    print"$datavistoria - $ultseg<br>";
}

?>
 
</body>
</html>
