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
} elseif ($perfil == '0') {



    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;


$ccustofi = isset($_POST["ccusto"]) ? $_POST["ccusto"] : 'td';
$filialfi = isset($_POST["filial"]) ? $_POST["filial"] : 'td';
$cargofi = isset($_POST["cargo"]) ? $_POST["cargo"] : 'td';
$mattecfi = isset($_POST["mattec"]) ? $_POST["mattec"] : '';
$placatecfi = isset($_POST["placatec"]) ? $_POST["placatec"] : '';

if (strftime('%A') == "domingo") {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}

include "conecta.php";
include_once "./func/log.php";
include_once "./func/consultacartao.php";
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
    <title>REL SALDO VEICULOS</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Relatório Saldo Veiculos </h1>
                <form action="./relsaldoveiculos.php" method="post">


                    <div class="row">

                        <div class="col-md-3">
                            <label for="exampleInputDate1">
                                <h5>Centro de Custo:</h5>
                            </label>
                            <select class="form-select mb-3" name="ccusto" id="ccusto" style="text-transform: uppercase;">
                                <option value="td"> Todos </option>

                                <?php

                                $sql = "SELECT DISTINCT COALESCE(ccusto, 'Sem centro de custo') AS ccusto
FROM bdaniel.tbfuncionario where ccusto like '%IHS%' or ccusto like '%CLARO%' or ccusto like '%Alloha%' or ccusto like '%Controle e Eficiência Operacional%' or ccusto is null ORDER BY ccusto;";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $ccusto = $row[0];
                                    $selected = ($ccustofi == $ccusto) ? 'selected' : '';
                                    print "<option value='$ccusto' $selected>$ccusto</option>";

                                }


                                ?>

                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="exampleInputDate2">
                                <h5>Cargo:</h5>
                            </label>
                            <select class="form-select mb-3" name="cargo" id="cargo" style="text-transform: uppercase;">
                                <option value="td"> Todos </option>
                                <?php

                                $sql = "SELECT DISTINCT cargo
                                    FROM bdaniel.tbfuncionario
                                    WHERE status <> 'demitido'
                                    AND cargo <> ''
                                    AND cargo NOT REGEXP '^[0-9]+$'
                                    ORDER BY cargo;
                                    ";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $cargo = $row[0];
                                    $selected = ($cargofi == $cargo) ? 'selected' : '';
                                    print "<option value='$cargo' $selected>$cargo</option>";

                                }


                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="exampleInputDate2">
                                <h5>Estado:</h5>
                            </label>
                            <select class="form-select mb-3" name="filial" id="filial" style="text-transform: uppercase;">
                                <option value="td"> Todos </option>
                                <?php

                                $sql = "SELECT DISTINCT unidade FROM bdfrota.tbveiculo where unidade <> '' order by unidade";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $uf_trabalho = $row[0];
                                    $selected = ($filialfi == $uf_trabalho) ? 'selected' : '';
                                    print "<option value='$uf_trabalho' $selected>$uf_trabalho</option>";

                                }


                                ?>
                            </select>
                        </div>


                        <div class="col-md-2">
                            <label for="matricula">
                                <h5>Matrícula:</h5>
                            </label>
                            <input type="text" class="form-control mb-3" name="mattec" placeholder="Digite a matrícula" value="<?php echo htmlspecialchars($mattecfi); ?>">
                        </div>

                        <div class="col-md-2">
                            <label for="placatec">
                                <h5>Placa:</h5>
                            </label>
                            <input type="text" class="form-control mb-3" name="placatec" placeholder="Digite a placa">
                        </div>



                        <div class='mt-2'>


                            <div class="mt-4">
                                <input type="submit" class="btn btn-success" value="Filtrar">
                            </div>


                            <!--</form>
    </div>-->
                        </div>
                </form>

                <!-- Aviso de Filtros Aplicados -->
                <?php if ($ccustofi != 'td' || $filialfi != 'td' || $cargofi != 'td' || $mattecfi != '' || $placatecfi != ''): ?>
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    <strong><i class="fa-solid fa-filter"></i> Filtros Aplicados:</strong>
                    <?php
                    $filtros = [];
                    if ($ccustofi != 'td') $filtros[] = "Centro de Custo: " . htmlspecialchars($ccustofi);
                    if ($filialfi != 'td') $filtros[] = "Estado: " . htmlspecialchars($filialfi);
                    if ($cargofi != 'td') $filtros[] = "Cargo: " . htmlspecialchars($cargofi);
                    if ($mattecfi != '') $filtros[] = "Matrícula: " . htmlspecialchars($mattecfi);
                    if ($placatecfi != '') $filtros[] = "Placa: " . htmlspecialchars($placatecfi);
                    echo implode(' | ', $filtros);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalJustificativas">
                            <i class="fa-solid fa-list"></i>
                            Histórico de Justificativas
                        </button>
                    </div>
                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Placa</th>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>Supervisor</th>
                                    <th>Cargo</th>
                                    <th>Centro de Custo</th>
                                    <th>Saldo Cartão</th>
                                    <th>Cota Inicial</th>
                                    <th>Total Cota Extra</th>
                                    <th>Total Cota Recebida</th>
                                    <th>Tipo de Ação:</th>
                                    <th>Justificativa:</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if ($ccustofi <> 'td') {
                                    $ccustosql = " and f.ccusto = '$ccustofi'";
                                }
                                if ($filialfi <> 'td') {
                                    $filialsql = " and f.uf_trabalho = '$filialfi'";
                                }
                                if ($cargofi <> 'td') {
                                    $cargosql = " and f.cargo = '$cargofi'";
                                }
                                if ($mattecfi <> '') {
                                    $mattecsql = " and f.matricula = '$mattecfi'";
                                }
                                if ($placatecfi <> '') {
                                    $placasql = " and v.placa = '$placatecfi'";
                                }

                                if ($ccustofi == 'td' and $filialfi == 'td' and $cargofi == 'td' and $mattecfi == '' and $placatecfi == '') {
                                    $sql = "SELECT * FROM bdaniel.tbfuncionario LIMIT 0; ";
                                } else {
                                    // CORREÇÃO: Query agora busca sa.saldo do banco de dados (tbsaldo)
                                    // O campo "Saldo" na tela agora exibe o valor do banco, não da API
                                    // Filtrado pela semana atual ($segunda) e matrícula do condutor (v.matcond)
                                    $sql = "SELECT 
                                            v.placa,
                                            COALESCE(f.matricula, '') AS matricula,
                                            COALESCE(f.nome, '') AS nome,
                                            COALESCE(sf.nome, '') AS supervisor_nome,
                                            COALESCE(f.ccusto, '') AS ccusto,
                                            COALESCE(f.cargo, '') AS cargo,
                                            v.unidade,
                                            COALESCE(sa.totalextra, 0) AS totalextra,
                                            COALESCE(sa.saldo, 0) AS saldo,
                                            f.codempresa,
                                            COALESCE(sa.orcsemanal, 0) as orcinicial
                                        FROM bdfrota.tbveiculo v
                                        JOIN bdfrota.tbastatusvel a 
                                            ON v.statusvel = a.idtbastatusvel
                                        LEFT JOIN bdfrota.tbsaldo sa
                                            ON sa.matricula = v.matcond
                                            AND sa.data = '$segunda'
                                        LEFT JOIN bdaniel.tbfuncionario f 
                                            ON v.matcond = f.matricula
                                        LEFT JOIN bdfrota.tbusuario u
                                            ON f.matricula = u.matricula 
                                            AND (u.idtbsupervisor IS NOT NULL AND u.idtbsupervisor <> 0)
                                        LEFT JOIN bdfrota.tbsupervisor s
                                            ON u.idtbsupervisor = s.idtbsupervisor
                                        LEFT JOIN bdaniel.tbfuncionario sf
                                            ON s.matricula = sf.matricula
                                        WHERE a.statusgeral = 1 
                                        AND f.status <> 'demitido'
                                        AND a.status NOT IN ('INDISPONÍVEL', 'SINISTRO/MANUTENÇÃO', 'EM DESMOBILIZAÇÃO', 'RESERVADO: PROJETO') $ccustosql $filialsql $mattecsql $cargosql $placasql; 
                                        ";
                                }
                                //echo "$sql";
                                

                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {

                                    $placa = $row[0];
                                    $matriculatec = $row[1];
                                    $nometec = $row[2];
                                    $supervisor_nome = $row[3];
                                    $ccusto = $row[4];
                                    $cargo = $row[5];
                                    $unidade = $row[6];
                                    $totalextra = $row[7];
                                    $saldobd = number_format($row[8], 2, ',', '.');
                                    $codempresa = $row[9];
                                    $orcinicial = number_format($row[10], 2, ',', '.');

                                    $saldocartao = consultacartao($codempresa, $placa);



                                    $formId = "form_" . str_replace("-", "_", $placa);
                                    $justificativaId = "justificativa_" . str_replace("-", "_", $placa);
                                    
                                    print "<tr>
                            <td>$placa</td>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td>$supervisor_nome</td>
                            <td>$cargo</td>
                            <td>$ccusto</td>
                            <td>$saldocartao</td>
                            <td>R$ $orcinicial</td>
                            <td>R$ $totalextra</td>
                            <td>R$ $saldobd</td>
                            <td style='width: 180px; min-width: 180px;'>
                                <form id='$formId' action='./control/remanejamentofrota.php' method='post' onsubmit=\"return validarFormulario('$justificativaId')\">
                                    <input type='hidden' name='matriculatec' value='$matriculatec'> 
                                    <input type='hidden' name='saldoatual' value='$saldobd'>
                                    <input type='hidden' name='saldosup' value='$saldosup'>
                                    <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                    <input type='hidden' name='unidade' value='$unidade'>
                                    <input type='hidden' name='placa' value='$placa'>
                                    <input type='hidden' name='idtbsupervisor' value='$idtbsupervisor'>
                                    <input type='hidden' name='numeroCartao' value='$numeroCartao'>

                                    <div style='font-size: 0.9rem;'>
                                        <div class='mb-1'>
                                            <input type='radio' name='tipoacao' value='1' id='add_$formId' required onchange=\"habilitarJustificativa('$justificativaId')\">
                                            <label for='add_$formId' style='margin-bottom: 0;'>Adicionar Saldo</label>
                                        </div>
                                        <div class='mb-2'>
                                            <input type='radio' name='tipoacao' value='0' id='rem_$formId' onchange=\"habilitarJustificativa('$justificativaId')\">
                                            <label for='rem_$formId' style='margin-bottom: 0;'>Remover Saldo</label>
                                        </div>
                                    </div>
                                    
                                    <div class='d-flex gap-1'>
                                        <input type='text' class='form-control form-control-sm numerico' placeholder='Valor' name='valor' $d required style='flex: 1; min-width: 80px;'>
                                        <button class='btn btn-outline-success btn-sm' type='submit' $d style='padding: 0.25rem 0.5rem;'>></button>
                                    </div>
                                </form>
                            </td>
                                                
                            <td style='width: 300px; min-width: 250px;'>
                                <div style='position: relative;'>
                                    <textarea id='$justificativaId' class='form-control form-control-sm' name='justificativa' rows='3' placeholder='Justificativa obrigatória (mín. 10 caracteres)...' form='$formId' required maxlength='500' oninput=\"atualizarContador('$justificativaId')\" style='resize: vertical; min-height: 80px; font-size: 0.9rem; padding-bottom: 25px;'></textarea>
                                    <small id='contador_$justificativaId' class='text-muted' style='position: absolute; bottom: 5px; right: 10px; font-size: 0.75rem;'>0/500</small>
                                </div>
                            </td>

               </tr>";



                                }

                                ?>

                            </tbody>
                        </table>
                        <button id="btnExportExcel" type="button" class="btn btn-success mt-3">Gerar Excel</button>
                    </div>
                </div>
            </div>

        </main>

        <footer class="py-4 bg-light mt-auto" style=" bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                <a href="#">Privacy Policy</a>
                                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>!-->
                </div>
            </div>
        </footer>

    </div>

    <!-- Modal de Histórico de Justificativas -->
    <div class="modal fade" id="modalJustificativas" tabindex="-1" aria-labelledby="modalJustificativasLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalJustificativasLabel">
                        <i class="fa-solid fa-list"></i> Histórico de Justificativas de Saldo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroMatricula" class="form-label">Matrícula Técnico:</label>
                            <input type="text" class="form-control" id="filtroMatricula" placeholder="Digite a matrícula">
                        </div>
                        <div class="col-md-3">
                            <label for="filtroPlaca" class="form-label">Placa:</label>
                            <input type="text" class="form-control" id="filtroPlaca" placeholder="Digite a placa">
                        </div>
                        <div class="col-md-2">
                            <label for="filtroTipoAcao" class="form-label">Tipo Ação:</label>
                            <select class="form-select" id="filtroTipoAcao">
                                <option value="">Todas</option>
                                <option value="1">Adição</option>
                                <option value="0">Remoção</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtroDataInicio" class="form-label">Data Início:</label>
                            <input type="date" class="form-control" id="filtroDataInicio">
                        </div>
                        <div class="col-md-2">
                            <label for="filtroDataFim" class="form-label">Data Fim:</label>
                            <input type="date" class="form-control" id="filtroDataFim">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" onclick="buscarJustificativas()">
                                <i class="fa-solid fa-search"></i> Buscar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                                <i class="fa-solid fa-eraser"></i> Limpar
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loadingJustificativas" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>

                    <!-- Tabela de resultados -->
                    <div id="resultadosJustificativas" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-bordered table-hover" id="tabelaJustificativas">
                            <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th>ID</th>
                                    <th>Data/Hora</th>
                                    <th>Tipo</th>
                                    <th>Placa</th>
                                    <th>Mat. Técnico</th>
                                    <th>Valor (R$)</th>
                                    <th>Mat. Autor</th>
                                    <th>Justificativa</th>
                                </tr>
                            </thead>
                            <tbody id="corpoTabelaJustificativas">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fa-solid fa-info-circle"></i> Utilize os filtros acima para buscar justificativas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<script>
    function corrigirMojibake(texto) {
        var mapa = {
            'Ã¡': 'á', 'Ã ': 'à', 'Ã¢': 'â', 'Ã£': 'ã', 'Ã¤': 'ä',
            'Ã©': 'é', 'Ãª': 'ê', 'Ã¨': 'è', 'Ã«': 'ë',
            'Ã­': 'í', 'Ã¬': 'ì', 'Ã®': 'î', 'Ã¯': 'ï',
            'Ã³': 'ó', 'Ã²': 'ò', 'Ã´': 'ô', 'Ãµ': 'õ', 'Ã¶': 'ö',
            'Ãº': 'ú', 'Ã¹': 'ù', 'Ã»': 'û', 'Ã¼': 'ü',
            'Ã§': 'ç', 'Ã‘': 'Ñ', 'Ã±': 'ñ',
            'Ã': 'Á', 'Ã€': 'À', 'Ã‚': 'Â', 'Ãƒ': 'Ã', 'Ã„': 'Ä',
            'Ã‰': 'É', 'ÃŠ': 'Ê', 'Ãˆ': 'È', 'Ã‹': 'Ë',
            'Ã': 'Í', 'ÃŒ': 'Ì', 'ÃŽ': 'Î', 'Ã': 'Ï',
            'Ã“': 'Ó', 'Ã’': 'Ò', 'Ã”': 'Ô', 'Ã•': 'Õ', 'Ã–': 'Ö',
            'Ãš': 'Ú', 'Ã™': 'Ù', 'Ã›': 'Û', 'Ãœ': 'Ü',
            'Âº': 'º', 'Âª': 'ª', 'Â°': '°'
        };

        var saida = String(texto || '');
        Object.keys(mapa).forEach(function(chave) {
            saida = saida.split(chave).join(mapa[chave]);
        });
        return saida;
    }

    function limparTexto(texto) {
        return corrigirMojibake(String(texto || '').replace(/\s+/g, ' ').trim());
    }

    function escapeHtml(texto) {
        return String(texto || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function extrairTipoAcao(celula) {
        if (!celula) {
            return 'Sem ação selecionada';
        }

        var radioMarcado = celula.querySelector("input[type='radio']:checked");
        if (!radioMarcado) {
            return 'Sem ação selecionada';
        }

        var idRadio = radioMarcado.getAttribute('id');
        var label = idRadio ? celula.querySelector("label[for='" + idRadio + "']") : null;
        return limparTexto(label ? label.textContent : '');
    }

    function extrairValorAcao(celula) {
        if (!celula) {
            return '';
        }

        var inputValor = celula.querySelector("input[name='valor']");
        if (!inputValor) {
            return '';
        }

        return limparTexto(inputValor.value);
    }

    function extrairJustificativa(celula) {
        if (!celula) {
            return '';
        }

        var textarea = celula.querySelector('textarea');
        if (!textarea) {
            return limparTexto(celula.textContent);
        }

        return limparTexto(textarea.value);
    }

    function exportTableToExcel(tableID, filename = '') {
        var tabela = document.getElementById(tableID);
        if (!tabela) {
            return;
        }

        var linhas = [];
        var cabecalhos = [];
        var ths = tabela.querySelectorAll('thead tr th');

        for (var h = 0; h < ths.length; h++) {
            cabecalhos.push(limparTexto(ths[h].textContent));
        }

        var trs = tabela.querySelectorAll('tbody tr');
        trs.forEach(function(tr) {
            var tds = tr.querySelectorAll('td');
            if (!tds || tds.length === 0) {
                return;
            }

            var linha = [];
            for (var i = 0; i < tds.length; i++) {
                if (i === 10) {
                    var tipoAcao = extrairTipoAcao(tds[i]);
                    var valorAcao = extrairValorAcao(tds[i]);

                    if (valorAcao !== '') {
                        tipoAcao += ' (R$ ' + valorAcao + ')';
                    }

                    linha.push(tipoAcao);
                } else if (i === 11) {
                    linha.push(extrairJustificativa(tds[i]));
                } else {
                    linha.push(limparTexto(tds[i].textContent));
                }
            }

            var temConteudo = linha.some(function(valor) {
                return valor !== '';
            });

            if (temConteudo) {
                linhas.push(linha);
            }
        });

        var html = '';
        html += '<html><head><meta charset="UTF-8">';
        html += '<style>';
        html += 'table { border-collapse: collapse; font-family: Arial, sans-serif; width: 100%; }';
        html += 'th, td { border: 1px solid #000; padding: 6px; }';
        html += 'th { background: #f2f2f2; }';
        html += '.texto { mso-number-format:"\\@"; }';
        html += '</style></head><body><table><thead><tr>';

        cabecalhos.forEach(function(coluna) {
            html += '<th>' + escapeHtml(coluna) + '</th>';
        });

        html += '</tr></thead><tbody>';

        linhas.forEach(function(linha) {
            html += '<tr>';
            linha.forEach(function(valor, indice) {
                // Matricula (coluna 2 da tabela exportada) como texto para preservar zeros à esquerda.
                if (indice === 1) {
                    html += '<td class="texto">' + escapeHtml(valor) + '</td>';
                } else {
                    html += '<td>' + escapeHtml(valor) + '</td>';
                }
            });
            html += '</tr>';
        });

        html += '</tbody></table></body></html>';

        var blob = new Blob(["\ufeff", html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        var downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = filename ? filename + '.xls' : 'excel_data.xls';

        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Dispara a função ao clicar no botão
    document.getElementById('btnExportExcel').addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var agora = new Date();
        var dia = String(agora.getDate()).padStart(2, '0');
        var mes = String(agora.getMonth() + 1).padStart(2, '0');
        var ano = agora.getFullYear();
        var hora = String(agora.getHours()).padStart(2, '0');
        var minuto = String(agora.getMinutes()).padStart(2, '0');

        var nomeArquivo = 'rel_saldo_veiculo_' + dia + '-' + mes + '-' + ano + '_' + hora + '-' + minuto;
        exportTableToExcel('datatablesSimple', nomeArquivo);
    });
</script>


<!--script para criar data table-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
            
            perPageSelect: [10, 25, 50, 1000]
        });
    });

    // Função para habilitar o campo de justificativa quando uma opção for selecionada
    function habilitarJustificativa(justificativaId) {
        const justificativa = document.getElementById(justificativaId);
        if (justificativa) {
            justificativa.disabled = false;
            justificativa.focus();
            atualizarContador(justificativaId);
        }
    }

    // Função para atualizar o contador de caracteres
    function atualizarContador(justificativaId) {
        const textarea = document.getElementById(justificativaId);
        const contador = document.getElementById('contador_' + justificativaId);
        
        if (textarea && contador) {
            const tamanho = textarea.value.length;
            const maxLength = textarea.maxLength || 500;
            contador.textContent = tamanho + '/' + maxLength;
            
            // Muda a cor conforme o tamanho
            if (tamanho < 10) {
                contador.style.color = '#dc3545'; // Vermelho - muito pouco
            } else if (tamanho >= maxLength - 50) {
                contador.style.color = '#ffc107'; // Amarelo - perto do limite
            } else {
                contador.style.color = '#28a745'; // Verde - ok
            }
        }
    }

    // Função para buscar justificativas
    function buscarJustificativas() {
        const matricula = document.getElementById('filtroMatricula').value;
        const placa = document.getElementById('filtroPlaca').value;
        const tipoAcao = document.getElementById('filtroTipoAcao').value;
        const dataInicio = document.getElementById('filtroDataInicio').value;
        const dataFim = document.getElementById('filtroDataFim').value;

        // Mostra loading
        document.getElementById('loadingJustificativas').style.display = 'block';
        document.getElementById('corpoTabelaJustificativas').innerHTML = '<tr><td colspan="8" class="text-center">Carregando...</td></tr>';

        // Faz a requisição AJAX
        $.ajax({
            url: './control/buscar_justificativas.php',
            type: 'POST',
            dataType: 'json',
            data: {
                matricula: matricula,
                placa: placa,
                tipo_acao: tipoAcao,
                data_inicio: dataInicio,
                data_fim: dataFim
            },
            success: function(dados) {
                document.getElementById('loadingJustificativas').style.display = 'none';
                
                console.log('Dados recebidos:', dados);
                
                if (dados.erro) {
                    document.getElementById('corpoTabelaJustificativas').innerHTML = 
                        '<tr><td colspan="8" class="text-center text-danger"><i class="fa-solid fa-exclamation-triangle"></i> ' + dados.erro + '</td></tr>';
                    return;
                }
                
                if (!Array.isArray(dados) || dados.length === 0) {
                    document.getElementById('corpoTabelaJustificativas').innerHTML = 
                        '<tr><td colspan="8" class="text-center text-muted"><i class="fa-solid fa-inbox"></i> Nenhum registro encontrado</td></tr>';
                    return;
                }
                
                let html = '';
                dados.forEach(function(item) {
                    const tipoAcaoBadge = item.tipo_acao == 1 
                        ? '<span class="badge bg-success">Adição</span>' 
                        : '<span class="badge bg-danger">Remoção</span>';
                    
                    const valorFormatado = parseFloat(item.valor).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    html += '<tr>';
                    html += '<td>' + (item.id || '') + '</td>';
                    html += '<td style="white-space: nowrap;">' + (item.data_hora_formatada || '') + '</td>';
                    html += '<td>' + tipoAcaoBadge + '</td>';
                    html += '<td><strong>' + (item.placa || 'N/A') + '</strong></td>';
                    html += '<td>' + (item.matricula_tecnico || 'N/A') + '</td>';
                    html += '<td>R$ ' + valorFormatado + '</td>';
                    html += '<td>' + (item.matricula_autor || 'N/A') + '</td>';
                    html += '<td><small>' + (item.justificativa || 'N/A') + '</small></td>';
                    html += '</tr>';
                });
                
                document.getElementById('corpoTabelaJustificativas').innerHTML = html;
            },
            error: function(xhr, status, error) {
                document.getElementById('loadingJustificativas').style.display = 'none';
                console.error('Erro na requisição:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                document.getElementById('corpoTabelaJustificativas').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-danger"><i class="fa-solid fa-exclamation-triangle"></i> Erro ao buscar dados. Verifique o console para mais detalhes.</td></tr>';
            }
        });
    }

    // Função para limpar filtros
    function limparFiltros() {
        document.getElementById('filtroMatricula').value = '';
        document.getElementById('filtroPlaca').value = '';
        document.getElementById('filtroTipoAcao').value = '';
        document.getElementById('filtroDataInicio').value = '';
        document.getElementById('filtroDataFim').value = '';
        document.getElementById('corpoTabelaJustificativas').innerHTML = 
            '<tr><td colspan="8" class="text-center text-muted"><i class="fa-solid fa-info-circle"></i> Utilize os filtros acima para buscar justificativas</td></tr>';
    }

    // Buscar automaticamente ao abrir o modal (últimos 7 dias)
    document.getElementById('modalJustificativas').addEventListener('shown.bs.modal', function () {
        const hoje = new Date();
        const seteDiasAtras = new Date(hoje.getTime() - (7 * 24 * 60 * 60 * 1000));
        
        document.getElementById('filtroDataInicio').value = seteDiasAtras.toISOString().split('T')[0];
        document.getElementById('filtroDataFim').value = hoje.toISOString().split('T')[0];
        
        buscarJustificativas();
    });
</script>

</html>