<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null){
echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0'){
echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_GET["diai"] ?? $_POST["diai"] ?? "";
$diaf = $_GET["diaf"] ?? $_POST["diaf"] ?? "";
$situacaoFiltro = $_GET["situacao"] ?? $_POST["situacao"] ?? "";
$matsup = $_GET["matsupf"] ?? $_POST["matsupf"] ?? "";

$filtrosAplicados = [];
if ($matsup !== "") {
    $filtrosAplicados[] = "Supervisor selecionado";
}
if ($diai !== "") {
    $filtrosAplicados[] = "Início: " . date("d/m/Y", strtotime($diai));
}
if ($diaf !== "") {
    $filtrosAplicados[] = "Fim: " . date("d/m/Y", strtotime($diaf));
}
if ($situacaoFiltro !== "") {
    $filtrosAplicados[] = "Situação: " . $situacaoFiltro;
}
$mostrarAvisoFiltro = count($filtrosAplicados) > 0;

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
                <h1 class="h1 mt-2">Relatório de uso indevido de veículos</h1>
                <form method="get">
                    <div class='d-flex'>
                        <!-- tipo de relatório-->
                        <div>
                            <h5><b>Supervisor:</b></h5>
                            <div class="input-group">
                                <select class="form-select mb-3" name="matsupf" id="selectsupervisor">
                                    <option value="" <?php echo $matsup === "" ? "selected" : ""; ?>>Todos</option>

                                    <?php
                                    $i = 0;
                                    $idselect;
                                    if($perfil == 3){

                                        $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c, bdfrota.tbgerente g where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador and c.idtbgerente = g.idtbgerente and g.matricula = '$matricula' ORDER BY b.nome ASC;";
                                    }else{

                                        $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador and c.matricula = '$matricula' ORDER BY b.nome ASC;";
                                    }
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    while ($row2 = mysql_fetch_array($resultado2)) {
                                        $matsupf = $row2[0];
                                        $idcoord = $row2[1];
                                        $nomesup = strtoupper($row2[2]);

                                        $addmatriz = array($matsup => $i);
                                        foreach ($addmatriz as $key => $value) {
                                            $idselect[$key] = $value;
                                        }
                                        $i++;
                                        $selectedSupervisor = $matsup === $matsupf ? "selected" : "";
                                        print "<option id='$idselect[$matsupf]' value='$matsupf' $selectedSupervisor>$nomesup</option>";
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
                            <input type="date" class="form-control form-control-md mb-3" name="diai" value = "<?php echo $diai?>"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Fim:</h5>
                            </label>
                            <input type="date" class="form-control form-control-md mb-3" name="diaf" value = "<?php echo $diaf?>"
                                required>
                        </div>
                    </div>
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o status</b></h5>
                        <div class="col-md-3">
                            <select class="form-control form-control-md mb-3" name="situacao" id="Situacao">
                                <option value="" <?php echo $situacaoFiltro === "" ? "selected" : ""; ?>>Todos</option>
                                <?php
                                $sql = "select distinct(situacao) from bdfrota.tbdescontousoindevido";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $selected = $row[0] == $situacaoFiltro ? "selected" : "";
                                    print "
                                    <option
                                    value='{$row[0]}' $selected>
                                    {$row[0]}</
                                    option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class='mt-2' style="width: 50%;">

                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>

                    </div>

                </form>

                <?php if ($mostrarAvisoFiltro) { ?>
                <div class="alert alert-warning mt-4 mb-0 py-2 d-flex align-items-center gap-2" role="alert" style="white-space: nowrap; overflow-x: auto;">
                    <strong>Filtros aplicados:</strong>
                    <span><?php echo htmlspecialchars(implode(' | ', $filtrosAplicados), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <?php } ?>

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
                                    <th>Data</th>
                                    <th>Placa</th>
                                    <th>KM Rodados</th>
                                    <th>Tipo de Uso Indevido</th>
                                    <th>Custo</th>
                                    <th>Situação</th>
                                    <th>Detalhes Trajeto</th>
                                    <th>Histórico de Uso Indevido</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diai != "") {
                                    $whereSituacao = $situacaoFiltro !== "" ? "td.situacao = '$situacaoFiltro' AND " : "";
                                    $whereSupervisor = $matsup !== "" ? " AND a.matricula = '$matsup'" : "";
                                    $sql = "SELECT td.*, tc.tipocombustivel, tc.consumo_litro FROM bdfrota.tbdescontousoindevido td join bdfrota.tbveiculo tv on td.placa = tv.placa join bdfrota.tbconsumocombustivel tc on tv.tipovel = tc.idtbconsumocombustivel WHERE {$whereSituacao} data_ocorrencia >= '$diai' AND data_ocorrencia <= '$diaf' and td.matricula in (SELECT 
                                                b.matricula
                                            FROM bdfrota.tbsupervisor a
                                            JOIN bdaniel.tbfuncionario b 
                                                ON a.matricula = b.matricula
                                            JOIN bdfrota.tbcoord c 
                                                ON a.idtbcoordenador = c.idtbcoordenador
                                            WHERE c.matricula = '$matricula'{$whereSupervisor}
                                            AND b.status != 'demitido'

                                            UNION

                                            SELECT 
                                                t.matricula
                                            FROM bdfrota.tbusuario t
                                            JOIN bdfrota.tbsupervisor a 
                                                ON t.idtbsupervisor = a.idtbsupervisor
                                            JOIN bdfrota.tbcoord c 
                                                ON a.idtbcoordenador = c.idtbcoordenador
                                            WHERE c.matricula = '$matricula'{$whereSupervisor})";
                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while ($row = mysql_fetch_array($resultado)) {
                                        if($row['valor_desconto'] == null){
                                            $consumoLitro = $row["consumo_litro"];
                                            $tipoCombustivel = $row["tipocombustivel"];
                                            if (strpos(strtolower($tipoCombustivel), "gasolina") !== false) {
                                                $custoKM = $precos['gasolina'] / $consumoLitro;
                                            } else {
                                                $custoKM = $precos['diesel'] / $consumoLitro;
                                            }
                                            $custoTotal = round(($row['kmtotal'] * $custoKM), 2, PHP_ROUND_HALF_UP);
                                        }else{
                                            $custoTotal = $row['valor_desconto'];
                                        }
// IMPORTANTE: Se o custo total for maior que 15, somente então é exibido na tela, caso contrário, não é exibido. Feito em 230926
                                        if ($custoTotal > 15) {
                                             $sql = "select nome from bdaniel.tbfuncionario where matricula = '" . $row['matricula'] . "'";
                                        $funcionario = mysql_query($sql) or die(mysql_error());
                                        $funcionario = mysql_fetch_assoc($funcionario);
                                        $situacaoLinha = $row['situacao'];
                                        echo "<tr>
                                                <td>{$row['matricula']}</td>
                                                <td>{$funcionario['nome']}</td>
                                                <td>{$row['data_ocorrencia']}</td>
                                                <td>{$row['placa']}</td>
                                                <td>{$row['kmtotal']}</td>
                                                <td>{$row['tipo_uso_indevido']}</td>
                                                <td>R$ {$custoTotal}</td>
                                            <td>{$situacaoLinha}</td>
                                                <td>
                                                    <form action='./detalhes-gpsxfolga.php' method='post'>                     
                                                        <input type='hidden' name='matriculatec' value='{$row['matricula']}'>
                                                        <input type='hidden' name='nometec' value='{$funcionario['nome']}'>
                                                        <input type='hidden' name='placatec' value='{$row['placa']}'>
                                                        <input type='hidden' name='diaf' value='{$row['data_ocorrencia']}'>
                                                        <input type='hidden' name='diai' value='{$row['data_ocorrencia']}'>
                                                        <input type='hidden' name='matsupf' value='$matricula'>
                                                        <input type='submit' class='btn btn-sm btn-success' value='detalhes'>
                                                    </form>
                                                </td>
                                                <td><a href='historico_uso_indevido.php?matricula={$row['matricula']}'><i class='fa-solid fa-eye'></i></a></td>
                                                <td><div class='d-flex gap-4'>";
                                                
                                                // Verificar se passaram mais de 5 dias da ocorrência
                                                $dataOcorrencia = new DateTime($row['data_ocorrencia']);
                                                $dataAtual = new DateTime();
                                                $diferencaDias = $dataAtual->diff($dataOcorrencia)->days;
                                                $maisDe5Dias = $diferencaDias > 5;
                                                
                                                if($situacaoLinha == "Aguardando Avaliação do Gestor"){
                                                    if($maisDe5Dias) {
                                                        // Botões desabilitados se passou mais de 5 dias
                                                        echo"<button class='btn btn-primary' disabled onclick='descontar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Enviar para Desconto</button>
                                                            <button class='btn btn-primary' disabled onclick='justificar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Justificar</button>";
                                                    } else {
                                                        // Botões normais se ainda está dentro do prazo
                                                        echo"<button class='btn btn-primary' onclick='descontar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Enviar para Desconto</button>
                                                            <button class='btn btn-primary' onclick='justificar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Justificar</button>";
                                                    }
                                                }
                                                echo "</div></td>
                                            </tr>";
                                        }
                                       
                                            
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
            matricula = '<?php echo $matricula?>';
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
<!--  -->