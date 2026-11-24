<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_POST["diai"];
$diaf = $_POST["diaf"];

$matsup = $_POST["matsupf"];

include "conecta.php";
include_once "./func/log.php";
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
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>
<div id="loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
    background: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div id="spinner" class="d-flex justify-content-center align-items-center"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: none;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>
</div>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <!-- Modal -->
        <div class="modal fade" id="modalJustificativa" tabindex="-1" role="dialog"
            aria-labelledby="modalJustificativaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJustificativaLabel">Justificativa</h5>
                        <button type="button" class="close bg-transparent border-0" data-dismiss="modal"
                            aria-label="Close">
                            <span class="h2" aria-hidden="true" onclick="cancel()">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" name="justificativa" id="justificativaText" rows="4"
                            placeholder="Digite a justificativa..."></textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onclick="cancel()">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="confirm()">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2">Assinatura de desconto por uso indevido de veículos</h1>
                <div class="mt-4">
                    <h5 class="text-danger"><b><?php if ($diaf <> '') {
                        $dataf = explode("-", $diaf);
                        $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                        $datai = explode("-", $diai);
                        $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                        print "Dados dos dias $datati até $datat";
                    } ?></b></h5>
                </div>
                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <a href="tecnico.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para o Inicio
                        </a href="tecnico.php">
                    </div>
                    <div class="card-body">
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Assinar</th>
                                    <th>Data</th>
                                    <th>Placa</th>
                                    <th>KM Rodados</th>
                                    <th>Tipo de Uso Indevido</th>
                                    <th>Valor a ser descontado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "select * from bdfrota.tbdescontousoindevido where matricula = '$matricula' and aval_coordenador = 1 and aceite_colaborador is null";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                if (mysql_num_rows($resultado) > 0) {
                                    while ($row = mysql_fetch_array($resultado)) {
                                        $custoTotal = $row['valor_desconto'];
                                        $sql = "select nome from bdaniel.tbfuncionario where matricula = '" . $row['matricula'] . "'";
                                        $funcionario = mysql_query($sql) or die(mysql_error());
                                        $funcionario = mysql_fetch_assoc($funcionario);
                                        $situacao = $row['situacao'];
                                        echo "<tr>
                                                    <td><button class='btn btn-primary' onclick='assinar(\"{$row['id']}\")'>Assinar</button></td>
                                                    <td>{$row['data_ocorrencia']}</td>
                                                    <td>{$row['placa']}</td>
                                                    <td>{$row['kmtotal']}</td>
                                                    <td>{$row['tipo_uso_indevido']}</td>
                                                    <td>{$custoTotal}</td>
                                                    <input 
                                                        type='file' 
                                                        id='cameraInput-{$row['id']}'
                                                        accept='image/*'
                                                        capture='user' 
                                                        style='display: none;'
                                                        data-id='{$row['id']}'
                                                        onchange='handleAssinatura(this)'
                                                    >
                                                </tr>";
                                    }
                                } else {
                                    print "<tr>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            </tr>";
                                }

                                ?>

                            </tbody>
                        </table>
                        <!--<form action="./control/gerarexcelgpsxfolga2.php" method="post">
                            <?php
                            /*
                            print "<input type='hidden' name='diai' value='$diai'>";
                            print "<input type='hidden' name='diaf' value='$diaf'>";
                            print "<input type='hidden' name='matsupf' value='$matsup'>"; */ ?>
                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                                <input type="submit" class="form-control form-control-md btn btn-success"
                                    value="GERAR EXCEL">
                            </div>
                        </form>
                        !-->
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>

    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<!--script para criar data table-->
<script>
    var id = null;
    //função iniciar data table 
    $(document).ready(function () {
        $('#datatablesSimple').DataTable();
    });

    function assinar(idAssinatura) {
        id = idAssinatura;
        document.getElementById(`cameraInput-${id}`).click();
    }

    function handleAssinatura(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const base64 = e.target.result;

                if (base64 != '') {
                    // Mostrar o loader
                    document.getElementById('loader').style.display = 'flex';

                    const data = {
                        id: id,
                        assinatura: base64,
                        matricula: <?php echo $matricula ?>
                    }

                    fetch('./control/assinardescontousoindevido.php', {
                        method: 'POST',
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    }).then(response => {
                        // Ocultar o loader após a resposta
                        document.getElementById('loader').style.display = 'none';

                        if (!response.ok) {
                            id = null;
                            response.json().then(json => {
                                window.alert(`Erro ao assinar o desconto: ${json.message || 'Erro desconhecido'}`);
                            });
                        } else {
                            id = null;
                            window.alert("Desconto assinado com sucesso");
                            window.location.reload();
                        }
                    }).catch(error => {
                        // Ocultar o loader em caso de erro
                        document.getElementById('loader').style.display = 'none';
                        window.alert("Erro na conexão com o servidor.");
                    });
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

</script>

</html>