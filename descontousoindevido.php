<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
//if ($perfil == null){
//echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
//} elseif ($perfil == '0'){
//echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
//}
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
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://combustivelapi.com.br/api/precos/");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 0);
$resultado = json_decode(curl_exec($curl), true);
$precos = [
    "gasolina" => $resultado['precos']['gasolina']['br'],
    "diesel" => $resultado["precos"]["diesel"]["br"],
]
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

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
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
                <h1 class="h1 mt-2">Relatório de uso indevido de veículos</h1>
                <form method="post">
                    <div class='d-flex'>
                        <!-- tipo de relatório-->
                        <div>
                            <h5><b>Supervisor:</b></h5>
                            <div class="input-group">
                                <select class="form-select mb-3" name="matsupf" id="selectsupervisor">
                                    <option value="ss"> Selecione o supervisor </option>

                                    <?php
                                    $i = 0;
                                    $idselect;
                                    $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador and c.matricula = '$matricula';";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    while ($row2 = mysql_fetch_array($resultado2)) {
                                        $matsupf = $row2[0];
                                        $idcoord = $row2[1];
                                        $nomesup = $row2[2];

                                        $addmatriz = array($matsup => $i);
                                        foreach ($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        print "<option id='$idselect[$matsupf]' value='$matsupf'>$nomesup</option>";
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o periodo:</b></h5>
                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Início:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md" name="diai"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Fim:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md" name="diaf"
                                required>
                        </div>
                    </div>

                    <div class='mt-2' style="width: 50%;">

                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>

                    </div>

                </form>

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
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard Inicial
                        </a href="dashboard.php">
                    </div>
                    <div class="card-body">
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>Placa</th>
                                    <th>Data</th>
                                    <th>KM Rodados</th>
                                    <th>Tipo de Uso Indevido</th>
                                    <th>Custo</th>
                                    <th>Situação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diai != "") {
                                    $sql = "SELECT td.*, tc.tipocombustivel, tc.consumo_litro FROM bdfrota.tbdescontousoindevido td join bdfrota.tbveiculo tv on td.placa = tv.placa join bdfrota.tbconsumocombustivel tc on tv.tipovel = tc.idtbconsumocombustivel WHERE aval_coordenador is null and data_ocorrencia >= '2025-05-06' AND data_ocorrencia <= '2025-05-07'";
                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while ($row = mysql_fetch_array($resultado)) {
                                        $consumoLitro = $row["consumo_litro"];
                                        $tipoCombustivel = $row["tipocombustivel"];
                                        if (strpos(strtolower($tipoCombustivel), "gasolina") !== false) {
                                            $custoKM = $precos['gasolina'] / $consumoLitro;
                                        } else {
                                            $custoKM = $precos['diesel'] / $consumoLitro;
                                        }
                                        $custoTotal = round(($row['kmtotal'] * $custoKM), 2, PHP_ROUND_HALF_UP);
                                        $sql = "select nome from bdaniel.tbfuncionario where matricula = '" . $row['matricula'] . "'";
                                        $funcionario = mysql_query($sql) or die(mysql_error());
                                        $funcionario = mysql_fetch_assoc($funcionario);
                                        $situacao = $row['situacao'];
                                        echo "<tr>
                                                    <td>{$row['matricula']}</td>
                                                    <td>{$funcionario['nome']}</td>
                                                    <td>{$row['data_ocorrencia']}</td>
                                                    <td>{$row['placa']}</td>
                                                    <td>{$row['kmtotal']}</td>
                                                    <td>{$row['tipo_uso_indevido']}</td>
                                                    <td>{$custoTotal}</td>
                                                    <td>{$situacao}</td>
                                                    <td><div class='d-flex gap-4'><button class='btn btn-primary' onclick='descontar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Enviar para Desconto</button><button class='btn btn-primary' onclick='justificar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Justificar</button></div></td>
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
                                            <td>Sem Dados</td>
                                            <td></td>
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
    var action = null;
    var id = null;
    var custoTotal = null;
    //função iniciar data table 
    $(document).ready(function () {
        $('#datatablesSimple').DataTable();
    });

    function justificar(itemId, valorCusto) {
        $('#modalJustificativa').modal('show');
        $('#modalJustificativaLabel').text("Justifique o motivo do não desconto");
        action = 0;
        id = itemId;
        custoTotal = valorCusto;
    }

    function descontar(itemId, valorCusto) {
        $('#modalJustificativa').modal('show');
        $('#modalJustificativaLabel').text("Justifique o desconto");
        action = 1;
        id = itemId;
        custoTotal = valorCusto;
    }

    function confirm() {
        if (action != null) {
            id = id;
            matricula = <?php echo ($matricula ? $matricula : 003712) ?>;
            valor = custoTotal;
            justificativa = $('#justificativaText').val();
            aval = action;
            data = {
                id: id,
                matricula: matricula,
                valor: valor,
                justificativa: justificativa,
                aval: aval
            }
            console.log(data);
            fetch("./control/decisaousoindevido.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                }
            )
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(result => {
                    // Sucesso: status 200
                    console.log("Resposta:", result);
                    alert(result.message); // ou use um toast, modal, etc.
                    window.location.reload();
                })
                .catch(error => {
                    // Erro de rede ou erro HTTP
                    console.error("Erro na requisição:", error);
                    alert("Ocorreu um erro ao enviar a justificativa. Tente novamente.");
                });
        }
    }

    function cancel() {
        $('#justificativaText').val('');
        $('#modalJustificativa').modal('hide');
        action = null;
        id = null;
        custoTotal = null;
    }
</script>

</html>