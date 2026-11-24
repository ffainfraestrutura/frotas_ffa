<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$hoje = date('Y-m-d H:i:s');
$hoje = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($hoje)));
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

include "conecta.php";
include_once "./func/log.php";

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$mattec = $_POST["mattec"] ?? '';
$dataac = $hoje;

$data_referencia = new DateTime($dataac);

if ($data_referencia->format('N') == 1) {
    $usegunda = $data_referencia->format('Y-m-d');
} else {
    $data_referencia->modify('last monday');
    $usegunda = $data_referencia->format('Y-m-d');
}

$nomesDiasDaSemana = array(
    1 => 'Segunda-feira',
    2 => 'Terça-feira',
    3 => 'Quarta-feira',
    4 => 'Quinta-feira',
    5 => 'Sexta-feira',
    6 => 'Sábado',
    7 => 'Domingo'
);

header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png"/>
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .numeric-input {
            display: none;
        }
    </style>
</head>
<body>
    <!--nav-->
    <?php include "menu.php"; include "popupgercol.php"; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2">Relatório Deslocamento OS</h1>
                <div id="collapseExample4">
                    <form id="mainForm" action="./detalhes-kmos-edicao.php" method="post">
                        <h5><b>Selecione o Técnico:</b></h5>
                        <div class="row align-items-center">
                            <!-- div select coordenador -->
                            <div class="col-sm-4 text-center">
                                <select class="form-select mb-3" name="coordenador" id="selectcoordenador">
                                    <option value=" ">Selecione o coordenador</option>
                                    <?php
                                    $i=0;
                                    $sql = "SELECT DISTINCT (a.idtbcoordenador), b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula;";
                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while($row = mysql_fetch_array($resultado)){
                                        $idcoord = $row[0];
                                        $nomecoord = $row[1];
                                        $addmatriz = array($idcoord => $i);
                                        foreach($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        print"<option id='$idselect[$idcoord]' value='$idcoord'>$nomecoord</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- div select supervisor -->
                            <div class="col-sm-4 text-center">
                                <select class="form-select mb-3" name="supervisor" id="selectsupervisor">
                                    <option value=" ">Selecione o supervisor</option>
                                    <?php
                                    $i=0;
                                    $idselect;
                                    $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador;";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    while($row2 = mysql_fetch_array($resultado2)){
                                        $matsup = $row2[0];
                                        $idcoord = $row2[1];
                                        $nomesup = $row2[2];
                                        $addmatriz = array($matsup => $i);
                                        foreach($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        print"<option id='$idselect[$idcoord]-$idselect[$matsup]' value='$matsup'>$nomesup </option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- div select tecnico -->
                            <div class="col-sm-4 text-center">
                                <select class="form-select mb-3" name="mattec" id="selecttecnico">
                                    <option value=" ">Selecione o técnico</option>
                                    <?php
                                    $i=0;
                                    $sql3 = "SELECT DISTINCT (a.matricula), b.nome, c.matricula, d.idtbcoordenador FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b, bdfrota.tbsupervisor c, bdfrota.tbcoord d where a.matricula = b.matricula AND a.idtbsupervisor = c.idtbsupervisor AND c.idtbcoordenador = d.idtbcoordenador AND a.perfil = 0;";
                                    $resultado3 = mysql_query($sql3) or die(mysql_error());
                                    while($row3 = mysql_fetch_array($resultado3)){
                                        $mattecf = $row3[0];
                                        $nometec = $row3[1];
                                        $matsup = $row3[2];
                                        $idcoord = $row3[3];
                                        print"<option id='$idselect[$idcoord]-$idselect[$matsup]-$i' value='$mattecf'>$nometec </option>";
                                    }
                                    $i++;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-success" value="Filtrar">
                    </form>
                </div>

                <?php if ($mattec !== ''): ?>
                <div id="dataContainer">
                    <form id="dataForm" action="./salvar_alteracoes.php" method="post">
                        <button id="saveChangesBtn" class="btn btn-success mb-3" type="submit">Salvar Alterações</button>
                        <input type="hidden" name="mattec" value="<?php echo $mattec; ?>">
                        <input type="hidden" id="deletedRows" name="deletedRows" value="">
                        <?php
                        $dataInicial = new DateTime($usegunda);
                        $dataInicial->modify('next sunday');
                        $pdomingo = $dataInicial->format('Y-m-d');
                        $dataFinal = new DateTime($pdomingo);
                        $dataInicial = new DateTime($usegunda);

                        while ($dataInicial <= $dataFinal) {
                            $dataDia = $dataInicial->format('Y-m-d');
                            $timestamp = strtotime($dataDia);
                            $diaSemanaNumero = date('N', $timestamp);
                            $nomeDiaSemana = $nomesDiasDaSemana[$diaSemanaNumero];

                            echo "<div class='card mt-4 mb-4'>
                                    <div class='card-header'>
                                        $dataDia - $nomeDiaSemana
                                        <button type='button' class='btn btn-primary float-end addRowBtn' data-dia='$dataDia'>Adicionar Linha</button>
                                    </div>
                                    <div class='card-body'>
                                        <table class='table table-bordered'>
                                            <thead>
                                                <tr>
                                                    <th>Matricula</th>
                                                    <th>Endereço</th>
                                                    <th>Cidade</th>
                                                    <th>Dia</th>
                                                    <th>Hora</th>
                                                    <th>OS</th>
                                                    <th>Latitude</th>
                                                    <th>Longitude</th>
                                                    <th>KM</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody id='tbody_$dataDia'>";

                            $sql = "SELECT ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
                            $resultado = mysql_query($sql) or die(mysql_error());
                            $row = mysql_fetch_array($resultado);
                            $ccusto = $row[0];

                            if (strpos($ccusto, 'IHS') !== false) {
                                $tabela = "bdfrota.tbosrota";
                                $id = 'idtbosrota';
                            } elseif (strpos($ccusto, 'CLARO') !== false) {
                                $tabela = "bdfrota.tbosrotalatlong";
                                $id = 'idtbosrotalatlong';
                            } elseif(strpos($ccusto, 'LIGGA') !== false) {
                                $tabela = "bdfrota.tbosrota";
                                $id = 'idtbosrota';
                            } else{
                                $tabela = "bdfrota.tbosrota";
                                $id = 'idtbosrota';
                            }

                            $sql = "SELECT endereco, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa, latitude, longitude, $id FROM $tabela where matricula = '$mattec' and data = '$dataDia' group by eta order by data, eta";
                            $resultado = mysql_query($sql) or die(mysql_error());

                            while ($row = mysql_fetch_array($resultado)) {
                                $data = $row[2];
                                $datac = explode(" ", $data);
                                $datat = $datac[0];

                                $endereco = $row[0];
                                $cidade = $row[1];
                                $eta = $row[3];
                                $os = $row[4];
                                if ($os == '') {
                                    $os = $row[6];
                                }
                                $kmrota = $row[5];
                                if ($kmrota == '') {
                                    $kmrota = 0;
                                }

                                $foratoa = $row[7];
                                if ($foratoa == 1) {
                                    $origem = 'MANUAL';
                                } elseif ($os == 'CASA') {
                                    $origem = 'ANIEL';
                                } else {
                                    $origem = 'TOA';
                                }
                                $latitude = $row[8];
                                $longitude = $row[9];
                                $idtbos = $row[10];

                                $endcomp = "$endereco $cidade";

                                if (!mb_detect_encoding($endcomp, 'UTF-8', true)) {
                                    $endcomp = utf8_encode($endcomp);
                                }

                                echo "<tr data-id='$idtbos' data-table='$tabela'>
                                        <td><input type='text' class='form-control-plaintext' name='matricula[]' value='$mattec' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='endereco[]' value='$endereco' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='cidade[]' value='$cidade' readonly></td>
                                        <td><input type='date' class='form-control-plaintext' name='dia[]' value='$datat' readonly></td>
                                        <td><input type='time' class='form-control-plaintext' name='hora[]' value='$eta' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='os[]' value='$os' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='latitude[]' value='$latitude' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='longitude[]' value='$longitude' readonly></td>
                                        <td><input type='text' class='form-control-plaintext' name='km[]' value='$kmrota' readonly></td>
                                        <td><button type='button' class='btn btn-danger removeRowBtn'>Excluir</button></td>
                                        <input type='hidden' name='ids[]' value='$idtbos'>
                                        <input type='hidden' name='tables[]' value='$tabela'>
                                    </tr>";
                            }

                            echo "</tbody>
                                    </table>
                                </div>
                            </div>";

                            $dataInicial->add(new DateInterval('P1D'));
                        }
                        ?>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.addRowBtn', function () {
                var dia = $(this).data('dia');
                var tbody = $('#tbody_' + dia);
                var matricula = '<?php echo $mattec; ?>';
                var tabela = '<?php echo $tabela; ?>'; // Tabela correta

                tbody.append(`
                    <tr data-id='' data-table='${tabela}'>
                        <td><input type='text' class='form-control-plaintext' name='matricula[]' value='${matricula}' readonly></td>
                        <td><input type='text' class='form-control' name='endereco[]' value=''></td>
                        <td><input type='text' class='form-control' name='cidade[]' value=''></td>
                        <td><input type='date' class='form-control-plaintext' name='dia[]' value='${dia}' readonly></td>
                        <td><input type='time' class='form-control' name='hora[]' value=''></td>
                        <td><input type='text' class='form-control' name='os[]' value=''></td>
                        <td><input type='text' class='form-control' name='latitude[]' value=''></td>
                        <td><input type='text' class='form-control' name='longitude[]' value=''></td>
                        <td><input type='text' class='form-control-plaintext' name='km[]' value='0' readonly></td>
                        <td><button type='button' class='btn btn-danger removeRowBtn'>Excluir</button></td>
                        <input type='hidden' name='ids[]' value=''>
                        <input type='hidden' name='tables[]' value='${tabela}'>
                    </tr>
                `);
            });

            $(document).on('click', '.removeRowBtn', function () {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var table = row.data('table');

                if (id && table) {
                    var deletedRows = $('#deletedRows').val();
                    deletedRows += id + ',' + table + ';';
                    $('#deletedRows').val(deletedRows);
                }
                row.remove();
            });

            $('#saveChangesBtn').click(function () {
                $('#dataForm').submit();
            });
        });
    </script>
</body>
</html>
