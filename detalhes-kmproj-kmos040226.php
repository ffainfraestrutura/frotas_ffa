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

$useg = $_POST["useg"];
if ($useg == '') {
    $useg = date('Y-m-d', strtotime('last monday'));
    $datam1 = date("Y-m-d", strtotime("-1 day"));
} else {
    $datam1 = date('Y-m-d', strtotime($useg . "+6 days"));
}

// Filtro por gerente
$gerente_filter = $_POST['gerente_filter'] ?? '';
$filtro_gerente_sql = '';
if (!empty($gerente_filter)) {
    $filtro_gerente_sql = " AND EXISTS (
        SELECT 1 FROM bdfrota.tbusuario u2
        JOIN bdfrota.tbsupervisor s2 ON u2.idtbsupervisor = s2.idtbsupervisor
        JOIN bdfrota.tbcoord c2 ON s2.idtbcoordenador = c2.idtbcoordenador
        JOIN bdfrota.tbgerente g2 ON c2.idtbgerente = g2.idtbgerente
        WHERE u2.matricula = bdfrota.tbsaldo.matricula AND g2.matricula = '$gerente_filter'
    )";
}


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
    <title>Relatorio Orc Semanal</title>
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
                <h1 class="h1 mt-3 mb-3"> Orçamento Semanal </h1>
                
                <h5 class="mb-4"><b>Período
                    <?php
                    $u = DateTime::createFromFormat('Y-m-d', $useg);
                    $d = DateTime::createFromFormat('Y-m-d', $datam1);
                    if ($u && $d) {
                        echo ' ' . $u->format('d/m/Y') . ' até ' . $d->format('d/m/Y');
                    } else {
                        echo ' ' . htmlspecialchars($useg . ' até ' . $datam1, ENT_QUOTES, 'UTF-8');
                    }
                    
                    // Mostrar filtro ativo
                    if (!empty($gerente_filter)) {
                        $sql_nome_gerente = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$gerente_filter' AND status = 'Ativo'";
                        $resultado_nome_gerente = mysql_query($sql_nome_gerente) or die(mysql_error());
                        $row_nome_gerente = mysql_fetch_array($resultado_nome_gerente);
                        $nome_gerente_filtro = $row_nome_gerente[0] ?? '';
                        if ($nome_gerente_filtro) {
                            echo ' - <span class="badge bg-primary">Gerente: ' . htmlspecialchars($nome_gerente_filtro, ENT_QUOTES, 'UTF-8') . '</span>';
                        }
                    }
                    ?>
                </b></h5>

                <div id="collapseExample4" class="mb-4" style="width: 70%;">
                    <form action="./detalhes-kmproj-kmos040226.php" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inputGroupSelect04" class="form-label">Período:</label>
                                <select class="form-select" id="inputGroupSelect04" name="useg">
                                    <?php
                                    $sql = "SELECT distinct(data) from bdfrota.tbsaldo order by data desc";
                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {
                                        $dataa = $row[0];
                                        $usega = explode(" ", $dataa);
                                        $usegf = $usega[0];
                                        $datam1f = date('Y-m-d', strtotime($usegf . "+6 days"));

                                        $dataesca = explode("-", $usegf);
                                        $dataescf1 = $dataesca[2] . "/" . $dataesca[1] . "/" . $dataesca[0];

                                        $dataesca = explode("-", $datam1f);
                                        $dataescf2 = $dataesca[2] . "/" . $dataesca[1] . "/" . $dataesca[0];

                                        $selected = ($usegf == $useg) ? 'selected' : '';
                                        print "<option value='$usegf' $selected>$dataescf1 até $dataescf2</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="gerenteFilter" class="form-label">Gerente:</label>
                                <select class="form-select" id="gerenteFilter" name="gerente_filter">
                                    <option value="">Todos os Gerentes</option>
                                    <?php
                                    // Buscar todos os gerentes ativos
                                    $sql_gerentes = "SELECT DISTINCT g.matricula, f.nome 
                                                   FROM bdfrota.tbgerente g
                                                   JOIN bdaniel.tbfuncionario f ON g.matricula = f.matricula
                                                   WHERE f.status = 'Ativo'
                                                   ORDER BY f.nome";
                                    $resultado_gerentes = mysql_query($sql_gerentes) or die(mysql_error());
                                    
                                    $gerente_selecionado = $_POST['gerente_filter'] ?? '';
                                    
                                    while ($row_gerente = mysql_fetch_array($resultado_gerentes)) {
                                        $matricula_gerente = $row_gerente[0];
                                        $nome_gerente = $row_gerente[1];
                                        $selected = ($matricula_gerente == $gerente_selecionado) ? 'selected' : '';
                                        print "<option value='$matricula_gerente' $selected>$nome_gerente</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <input type="submit" class="btn btn-success" value="Filtrar">
                                <input type="button" class="btn btn-outline-secondary" value="Limpar Filtros" onclick="window.location.href='detalhes-kmproj-kmosDEV.php'">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard Inicial
                        </a>
                        <button id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i>
                            Exportar Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered' style="font-size: 12px !important;">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Técnico</th>
                                    <th>Supervisor</th>
                                    <th>Coordenador</th>
                                    <th>Gerente</th>
                                    <th>Cota Semanal 100%</th>
                                    <th>Cota Semanal 65%</th>
                                    <th>KM Proj. 100%</th>
                                    <th>KM Proj. 65%</th>
                                    <th>Cota Extra</th>
                                    <th>KM OS</th>
                                    <th>Deslocamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php


                                if (strftime('t') >= 21) {

                                    $mesini = date('m');
                                    $anoini = date('y');
                                    if (date('m') == 12) {
                                        $mesfim = 1;
                                        $anofim = date('y', strtotime('next year'));
                                    } else {

                                        $mesfim = date('m', strtotime('next month'));
                                        $anofim = date('y');
                                    }

                                } else {

                                    $mesfim = date('m');
                                    $anofim = date('y');
                                    if (date('m') == 1) {
                                        $mesini = 12;
                                        $anoini = date('y', strtotime('last year'));
                                    } else {
                                        $mesini = date('m', strtotime('last month'));
                                        $anoini = date('y');
                                    }

                                }

                                $diah = strftime('t');
                                $ontem = $diah - 1;

                                $dataini = "$anoini-$mesini-21";
                                $datafim = "$anofim-$mesfim-$ontem";


                                if (strftime('%A') == "segunda") {

                                    $sql = "SELECT matricula, kmproj, kmosatual, orcsemanal, totalextra, kmproj-kmosatual as diferenca, valoraplicado, kmorcsem, slddscnt FROM bdfrota.tbsaldo where data >= '$useg' and data <= '$datam1' $filtro_gerente_sql group by matricula order by diferenca desc";
                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $mattec = $row[0];
                                        $kmproj = $row[1];
                                        $kmos = $row[2];
                                        $diff = $kmproj - $kmos;
                                        $orcsem = $row[3];
                                        $orcsem65 = round(($row[3] * 0.65), 2);
                                        $totcot = $row[4];
                                        $valortotal = $row[6];
                                        $kmorcsem = $row[7];
                                        $kmorcsem65 = round($kmorcsem * 0.65, 2);
                                        $saldodesc = $row[8];


                                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $placa = $row2[0];

                                        $servidor5 = "10.17.0.5";
                                        $usuario5 = "blvp";
                                        $senha5 = "@F3r7n6c2";
                                        $db5 = "bdfrota";
                                        $conexao5 = mysqli_connect("$servidor5", "$usuario5", "$senha5", "$db5") or die(mysqli_connect_error());

                                        $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$useg' and datagps <= '$datam1' group by placa";
                                        $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
                                        $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                        $kmgps = $row5[0];

                                        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $nometec = $row2[0];

                                        $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $idsup = $row2[0];

                                        
                                        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $nomesup = $row2[0];

                                        // Buscar o coordenador baseado na hierarquia
                                        $sql_coordenador = "SELECT fc.nome 
                                                           FROM bdfrota.tbusuario u
                                                           JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                                           JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                                           JOIN bdaniel.tbfuncionario fc ON c.matricula = fc.matricula
                                                           WHERE u.matricula = '$mattec' AND fc.status = 'Ativo'";
                                        $resultado_coordenador = mysql_query($sql_coordenador) or die(mysql_error());
                                        $row_coordenador = mysql_fetch_array($resultado_coordenador);
                                        $nomecoordenador = $row_coordenador[0] ?? 'N/A';

                                        // Buscar o gerente baseado na hierarquia
                                        $sql_gerente = "SELECT fg.nome 
                                                       FROM bdfrota.tbusuario u
                                                       JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                                       JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                                       JOIN bdfrota.tbgerente g ON c.idtbgerente = g.idtbgerente
                                                       JOIN bdaniel.tbfuncionario fg ON g.matricula = fg.matricula
                                                       WHERE u.matricula = '$mattec' AND fg.status = 'Ativo'";
                                        $resultado_gerente = mysql_query($sql_gerente) or die(mysql_error());
                                        $row_gerente = mysql_fetch_array($resultado_gerente);
                                        $nomegerente = $row_gerente[0] ?? 'N/A';

                                        if ($nomesup <> '') {
                                            print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$nomesup</td>
              <td>$nomecoordenador</td>
              <td>$nomegerente</td>
              <td style='white-space:  nowrap;'>R$ $orcsem</td>
              <td style='white-space:  nowrap;'>R$ $orcsem65</td>
              <td style='white-space:  nowrap;'>$kmorcsem</td>
              <td style='white-space:  nowrap;'>$kmorcsem65</td>
              <td style='white-space:  nowrap;'>R$ $valortotal</td>
              <td style='white-space:  nowrap;'>$kmos</td>
              <td><button class='btn btn-primary btn-sm' onclick=\"window.open('/frotas/painel-suporte/rotaspmatric.php?mattec=$mattec&diai=$useg', '_blank')\"><i class='fas fa-external-link-alt'></i> Detalhes</button></td>
              </tr> ";
                                        }

                                    }

                                } else {

                                    $domingo = date('Y-m-d', strtotime('last sunday'));
                                    $hoje = date('Y-m-d');

                                    $datetime1 = new DateTime($domingo);
                                    $datetime2 = new DateTime($hoje);
                                    $interval = $datetime1->diff($datetime2);
                                    $differenceInDays = $interval->format('%a');


                                    $sql = "SELECT matricula, kmproj, kmosatual, orcsemanal, totalextra, kmproj-kmosatual as diferenca, valoraplicado, kmorcsem, slddscnt FROM bdfrota.tbsaldo where data >= '$useg' and data <= '$datam1' $filtro_gerente_sql group by matricula order by diferenca desc";
                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $mattec = $row[0];
                                        $kmproj = $row[1];
                                        $kmos = $row[2];
                                        $diff = $kmproj - $kmos;
                                        $orcsem = $row[3];
                                        $orcsem65 = round(($row[3] * 0.65), 2);
                                        $totcot = $row[4];
                                        $valortotal = $row[6];
                                        $kmorcsem = $row[7];
                                        $kmorcsem65 = round($kmorcsem * 0.65, 2);
                                        $saldodesc = $row[8];


                                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $placa = $row2[0];

                                        $servidor5 = "10.17.0.5";
                                        $usuario5 = "blvp";
                                        $senha5 = "@F3r7n6c2";
                                        $db5 = "bdfrota";
                                        $conexao5 = mysqli_connect("$servidor5", "$usuario5", "$senha5", "$db5") or die(mysqli_connect_error());

                                        $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$domingo' and datagps <= '$hoje' group by placa";
                                        $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
                                        $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                        $kmgps = $row5[0];

                                        $sql2 = "SELECT nome FROM bdfrota.tbusuario where matricula = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $nometec = $row2[0];

                                        $sql2 = "SELECT idtbsupervisor FROM bdfrota.tbusuario where matricula = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $idsup = $row2[0];

                                        $sql2 = "SELECT b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idsup'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $nomesup = $row2[0];

                                        // Buscar o coordenador baseado na hierarquia
                                        $sql_coordenador = "SELECT fc.nome 
                                                           FROM bdfrota.tbusuario u
                                                           JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                                           JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                                           JOIN bdaniel.tbfuncionario fc ON c.matricula = fc.matricula
                                                           WHERE u.matricula = '$mattec' AND fc.status = 'Ativo'";
                                        $resultado_coordenador = mysql_query($sql_coordenador) or die(mysql_error());
                                        $row_coordenador = mysql_fetch_array($resultado_coordenador);
                                        $nomecoordenador = $row_coordenador[0] ?? 'N/A';

                                        // Buscar o gerente baseado na hierarquia
                                        $sql_gerente = "SELECT fg.nome 
                                                       FROM bdfrota.tbusuario u
                                                       JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                                       JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                                       JOIN bdfrota.tbgerente g ON c.idtbgerente = g.idtbgerente
                                                       JOIN bdaniel.tbfuncionario fg ON g.matricula = fg.matricula
                                                       WHERE u.matricula = '$mattec' AND fg.status = 'Ativo'";
                                        $resultado_gerente = mysql_query($sql_gerente) or die(mysql_error());
                                        $row_gerente = mysql_fetch_array($resultado_gerente);
                                        $nomegerente = $row_gerente[0] ?? 'N/A';

                                        if ($nomesup <> '') {
                                            print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$nomesup</td>
              <td>$nomecoordenador</td>
              <td>$nomegerente</td>
              <td style='white-space:  nowrap;'>R$ $orcsem</td>
              <td style='white-space:  nowrap;'>R$ $orcsem65</td>
              <td style='white-space:  nowrap;'>$kmorcsem</td>
              <td style='white-space:  nowrap;'>$kmorcsem65</td>
              <td style='white-space:  nowrap;'>R$ $valortotal</td>
              <td style='white-space:  nowrap;'>$kmos</td>
              <td><button class='btn btn-primary btn-sm' onclick=\"window.open('/frotas/painel-suporte/rotaspmatric.php?mattec=$mattec&diai=$useg', '_blank')\"><i class='fas fa-external-link-alt'></i> Detalhes</button></td>
              </tr> ";
                                        }

                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                        
                        <!-- Formulário oculto para exportação Excel -->
                        <form id="excelForm" action="./control/gerarexcelorcamentosemanal.php" method="post" style="display: none;">
                            <input type="hidden" name="useg" value="<?php echo htmlspecialchars($useg, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="datam1" value="<?php echo htmlspecialchars($datam1, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="gerente_filter" value="<?php echo htmlspecialchars($gerente_filter, ENT_QUOTES, 'UTF-8'); ?>">
                        </form>

                    </div>
                </div>
            </div>

        </main>

        <footer class="py-4 bg-light mt-auto">
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
    //função iniciar data table 
    $(document).ready(function () {
        $('#datatablesSimple').DataTable();
    });
    
    // Script para exportação Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        document.getElementById('excelForm').submit();
    });
</script>
</html>