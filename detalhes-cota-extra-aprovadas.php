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

$diaf = $_POST["diaf"] ?? '';
$diai = $_POST["diai"] ?? '';
$matsup = $_POST["matsupf"] ?? '';
$coordenador_id = $_POST["coordenador"] ?? '';
$gerente_id = $_POST["gerente"] ?? '';


include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

// Busca nome do coordenador selecionado
$coordenador_nome = '';
$coordenador_matricula = '';
if (!empty($coordenador_id) && trim($coordenador_id) != '') {
    $sql_coord = "SELECT b.nome, a.matricula FROM bdfrota.tbcoord a 
                  INNER JOIN bdaniel.tbfuncionario b ON a.matricula = b.matricula 
                  WHERE a.idtbcoordenador = '$coordenador_id' AND UPPER(TRIM(b.status)) = 'ATIVO'";
    $resultado_coord = mysql_query($sql_coord);
    if ($row_coord = mysql_fetch_array($resultado_coord)) {
        $coordenador_nome = $row_coord[0];
        $coordenador_matricula = $row_coord[1];
    }
}

// Busca nome do supervisor selecionado
$supervisor_nome = '';
if (!empty($matsup) && $matsup != 'ss') {
    $sql_sup = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matsup' AND UPPER(TRIM(status)) = 'ATIVO'";
    $resultado_sup = mysql_query($sql_sup);
    if ($row_sup = mysql_fetch_array($resultado_sup)) {
        $supervisor_nome = $row_sup[0];
    }
}

// Busca nome do gerente selecionado
$gerente_nome = '';
$gerente_matricula = '';
if (!empty($gerente_id) && trim($gerente_id) != '') {
    $sql_ger = "SELECT b.nome, a.matricula FROM bdfrota.tbgerente a
                INNER JOIN bdaniel.tbfuncionario b ON a.matricula = b.matricula
                WHERE a.idtbgerente = '$gerente_id' AND UPPER(TRIM(b.status)) = 'ATIVO'";
    $resultado_ger = mysql_query($sql_ger);
    if ($row_ger = mysql_fetch_array($resultado_ger)) {
        $gerente_nome = $row_ger[0];
        $gerente_matricula = $row_ger[1];
    }
}
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

    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- jQuery PRIMEIRO -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    
    <!-- Select2 para autocomplete (DEPOIS do jQuery) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!--datatable (DEPOIS do Select2) -->
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
    
    <style>
        /* Estilo personalizado para Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container {
            width: 100% !important;
        }
        .alert-info {
            background-color: #e7f3ff;
            border-color: #b3d9ff;
            color: #004085;
        }
    </style>

</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Relatório Cotas Extra Aprovadas </h1>

                <div class="alert alert-info mt-3" role="alert">
                    Selecione o período e as aprovações realizadas por um gerente, ou por um coordenador, ou por um supervisor ou deixe em todos.
                </div>

                <form action="./detalhes-cota-extra-aprovadas.php" method="post">
                    <div class="row g-3 align-items-end">
                        <!-- Gerente -->
                        <div class="col">
                            <label class="form-label"><b>Gerente:</b></label>
                            <select class="select2 form-select" name="gerente" id="selectgerente" style="width: 100%;">
                                <option value="">Todos os gerentes</option>
                                <?php
                                $sql_gerente = "SELECT DISTINCT (a.idtbgerente), a.matricula, b.nome FROM bdfrota.tbgerente a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                $resultado_gerente = mysql_query($sql_gerente) or die(mysql_error());
                                while ($row_gerente = mysql_fetch_array($resultado_gerente)) {
                                    $idgerente = $row_gerente[0];
                                    $matgerente = $row_gerente[1];
                                    $nomegerente = $row_gerente[2];
                                    $selected = ($gerente_id == $idgerente) ? ' selected' : '';
                                    print "<option value='$idgerente'$selected>$nomegerente ($matgerente)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Coordenador -->
                        <div class="col">
                            <label class="form-label"><b>Coordenador:</b></label>
                            <select class="select2 form-select" name="coordenador" id="selectcoordenador" style="width: 100%;">
                                <option value="">Todos os coordenadores</option>
                                <?php
                                $i = 0;
                                $sql = "SELECT DISTINCT (a.idtbcoordenador), a.matricula, b.nome FROM bdfrota.tbcoord a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $idcoord = $row[0];
                                    $matcoord = $row[1];
                                    $nomecoord = $row[2];
                                    $selected = ($coordenador_id == $idcoord) ? ' selected' : '';
                                    $addmatriz = array($idcoord => $i);
                                    foreach ($addmatriz as $key => $value) {
                                        $idselect[$key] = $value;
                                    }
                                    $i++;
                                    print "<option value='$idcoord'$selected>$nomecoord ($matcoord)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Supervisor -->
                        <div class="col">
                            <label class="form-label"><b>Supervisor:</b></label>
                            <select class="select2 form-select" name="matsupf" id="selectsupervisor" style="width: 100%;">
                                <option value="ss">Todos os supervisores</option>
                                <?php
                                $i = 0;
                                $idselect;
                                $sql2 = "SELECT DISTINCT (a.matricula), c.idtbcoordenador, b.nome FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b, bdfrota.tbcoord c where a.matricula = b.matricula and a.idtbcoordenador = c.idtbcoordenador ORDER BY b.nome;";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());
                                while ($row2 = mysql_fetch_array($resultado2)) {
                                    $matsupf = $row2[0];
                                    $idcoord = $row2[1];
                                    $nomesup = $row2[2];
                                    $selected = ($matsup == $matsupf) ? ' selected' : '';
                                    $addmatriz = array($matsup => $i);
                                    foreach ($addmatriz as $key => $value) {
                                        $idselect[$key] = $value;
                                    }
                                    $i++;
                                    print "<option value='$matsupf'$selected>$nomesup ($matsupf)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Data Início -->
                        <div class="col">
                            <label class="form-label"><b>Início:</b></label>
                            <input min="2023-03-17" type="date" class="form-control" name="diai" value="<?php echo $diai; ?>">
                        </div>

                        <!-- Data Fim -->
                        <div class="col">
                            <label class="form-label"><b>Fim:</b></label>
                            <input min="2023-03-17" type="date" class="form-control" name="diaf" value="<?php echo $diaf; ?>">
                        </div>

                        <!-- Botão Filtrar -->
                        <div class="col-auto">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-success px-5">Filtrar</button>
                        </div>
                    </div>
                </form>



                <div class="mt-4">
                    <?php if ($diaf <> '' && $diai <> '') { ?>
                        <div class="alert alert-info" role="alert">
                            <h5 class="mb-3"><i class="fas fa-filter"></i> <b>Filtros Aplicados:</b></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Período:</strong> 
                                        <?php
                                        $dataf = explode("-", $diaf);
                                        $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                                        $datai = explode("-", $diai);
                                        $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                                        echo "$datati até $datat";
                                        ?>
                                    </p>
                                </div>
                                <?php if (!empty($gerente_nome)) { ?>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Gerente:</strong> <?php echo $gerente_nome; ?></p>
                                </div>
                                <?php } ?>
                                <?php if (!empty($coordenador_nome)) { ?>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Coordenador:</strong> <?php echo $coordenador_nome; ?></p>
                                </div>
                                <?php } ?>
                                <?php if (!empty($supervisor_nome) && $matsup != 'ss') { ?>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Supervisor:</strong> <?php echo $supervisor_nome; ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="card mt-4 mb-4">
                    <div class="card-header">
                        <a href="./dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a href="./dashboard.php">
                    </div>
                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr style='font-size:12px;'>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>C. Custo</th>
                                    <th>Placa</th>
                                    <th>Orc. Semanal</th>
                                    <th>KM Proj.</th>
                                    <th>KM OS</th>
                                    <th>KM GPS</th>
                                    <th>Sld. Cartão</th>
                                    <th>Remanejado</th>
                                    <th>Data e Hora</th>
                                    <th>Justificativa</th>
                                    <th>Valor Pedido</th>
                                    <th>Valor Inserido</th>
                                    <th>Data Aceito</th>
                                    <th>Tipo Cota</th>
                                    <th>Aprovado Por</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diaf == '' or $diai == '') {

                                    print "
                                    <tr>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>                            
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td>Sem Dados</td>
                                        <td> </td>
                                    
                                    </tr>";
                                } else {
                                    $whereFiltro = "where dataaceite > '$diai 00:00:00' and dataaceite < '$diaf 23:39:59'";

                                    if (!empty($coordenador_matricula)) {
                                        $whereFiltro .= " and matsup = '$coordenador_matricula'";
                                        } elseif (!empty($gerente_matricula)) {
                                        $whereFiltro .= " and matsup = '$gerente_matricula'";
                                        } elseif ($matsup != 'ss') {
                                            $whereFiltro .= " and matsup = '$matsup'";
                                    }

                                    $sql = "SELECT matricula, orcsemanal, ROUND(kmproj,0), ROUND(kmos,0), gps, sldcartao, totalextra, data, justificativa, valor, valorinserido, dataaceite, tipo_cota, matsup FROM bdfrota.tbcotaextraac $whereFiltro order by data";

                                    //echo $sql;
                                
                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {
                                        $mattec = $row[0];
                                        $orcsemanal = $row[1];
                                        $kmproj = $row[2];
                                        $kmos = $row[3];
                                        $gps = $row[4];
                                        $sldcartao = $row[5];
                                        //$totalextra = $row[6];
                                        $data = $row[7];
                                        $justificativa = $row[8];
                                        $valor = $row[9];
                                        $valorinserido = $row[10];
                                        $dataaceite = $row[11];
                                        $tipo_cota = $row[12];
                                        $matsup_aprovador = $row[13];

                                        if ($tipo_cota == 1) {
                                            $tipo_cota = 'Emergencial';
                                        } else {
                                            $tipo_cota = "Extra";
                                        }

                                        $data1 = new DateTime("$data");
                                        $datav = $data1->format('d/m/Y H:i:s');

                                        $dataaceite1 = new DateTime("$dataaceite");
                                        $dataaceitev = $dataaceite1->format('d/m/Y H:i:s');

                                        $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$mattec'";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $placa = $row2[0];

                                        $sql_kmproj = "SELECT kmorcsem, matricula FROM bdfrota.tbsaldo where matricula = '$mattec' order by idtbsaldo desc limit 1;";
                                        $resultado_kmproj = mysql_query($sql_kmproj) or die(mysql_error());
                                        $row_kmproj = mysql_fetch_array($resultado_kmproj);
                                        $kmproj = $row_kmproj[0];

                                        $sql3 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$mattec'";
                                        $resultado3 = mysql_query($sql3) or die(mysql_error());
                                        $row3 = mysql_fetch_array($resultado3);
                                        $nometec = $row3[0];
                                        $ccusto = utf8_encode($row3[1]);

                                        $nome_aprovador = '';
                                        if (!empty($matsup_aprovador)) {
                                            $sql_aprovador = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matsup_aprovador'";
                                            $resultado_aprovador = mysql_query($sql_aprovador) or die(mysql_error());
                                            $row_aprovador = mysql_fetch_array($resultado_aprovador);
                                            $nome_aprovador = $row_aprovador[0] ?? '';
                                        }

                                        //remanejamento positivo
                                        $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=1;";
                                        $resultado4 = mysql_query($sql4) or die(mysql_error());
                                        $row4 = mysql_fetch_array($resultado4);
                                        $remanejpositivo = $row4[0];

                                        //remanejamento negativo
                                        $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=0;";
                                        $resultado5 = mysql_query($sql5) or die(mysql_error());
                                        $row5 = mysql_fetch_array($resultado5);
                                        $remanejnegativo = $row5[0];

                                        $sldremanej = $remanejpositivo - $remanejnegativo;

                                        //saldo fundo frota
                                        $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$mattec' AND data_e_hora BETWEEN  '$diai 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4";
                                        $resultado6 = mysql_query($sql6) or die(mysql_error());
                                        $row6 = mysql_fetch_array($resultado6);
                                        $ffrota = $row6[0];
                                        $ffrota = str_replace(".", ",", $ffrota);

                                        print "
                                        <tr style='font-size:12px;'>
                                            <td>$mattec</td>
                                            <td>$nometec</td>
                                            <td>$ccusto</td>
                                            <td>$placa</td>
                                            <td>R$ $orcsemanal</td>
                                            <td>$kmproj km</td>
                                            <td>$kmos km</td>
                                            <td>$gps km</td>                
                                            <td>R$ $sldcartao</td>
                                            <td>R$ $sldremanej</td>
                                            <td>$datav</td>
                                            <td>$justificativa</td>
                                            <td>R$ $valor</td>
                                            <td>R$ $valorinserido</td>
                                            <td>$dataaceitev</td>
                                            <td>$tipo_cota</td>
                                            <td>$matsup_aprovador - $nome_aprovador</td>
                                            <td> <form action='./detalhes-deslocamento.php' method='post'>
                                                <input type='hidden' name='diai2' value='$diai'/>
                                                <input type='hidden' name='diaf2' value='$diaf'/>
                                                <input type='hidden' name='mattec' value='$mattec' />
                                                <input type='hidden' name='matsup' value='$matsup' />
                                                <input type='submit' class='btn btn-success'  value='Detalhes'> 
                                            </form> 
                                            </td>
                                        </tr>";

                                    }

                                    //print $sql4;
                                }
                                ?>
                            </tbody>
                        </table>
                        <!--<form action="./control/gerarexcelcota-extra-aprovada.php" method="post">
                    <?php
                    print "<input type='hidden' name='diai' value='$diai'>";
                    print "<input type='hidden' name='diaf' value='$diaf'>";
                    print "<input type='hidden' name='matsupf' value='$matsup'>"; ?>
                    <div class="form-group col-md-2">
                        <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                        <input type="submit" class="form-control form-control-md btn btn-success" value="GERAR EXCEL">
                    </div>
                </form>-->
                    </div>
                </div>
            </div>

        </main>

        <footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
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

<!--script para criar data table-->
<script>
    $(document).ready(function () {
        // Inicializar Select2 com busca customizada (mesma config do rotapmat.php)
        $('.select2').select2({
            templateResult: function (data, container) {
                if (data.element) {
                    $(container).addClass($(data.element).attr("class"));
                }
                return data.text;
            },
            matcher: function (params, data) {
                // Se a pesquisa estiver vazia, exibe todos os itens
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Pesquisar no `id` (matrícula/ID) ou no `text` (nome)
                if (
                    (data.id && data.id.toString().toLowerCase().includes(params.term.toLowerCase())) ||
                    (data.text && data.text.toLowerCase().includes(params.term.toLowerCase()))
                ) {
                    return data;
                }

                // Se não houver correspondência, retorna null
                return null;
            }
        });
        
        // Inicializar DataTable
        new DataTable('#datatablesSimple', {
            "scrollX": true,
            order: [[10, 'desc']],
            layout: {
                topStart: {
                    buttons: ['excel']
                }
            }
        });
    });
</script>

</html>