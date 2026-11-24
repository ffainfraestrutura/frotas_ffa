<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

// Validação de sessão - redireciona se não estiver logado
if ($perfil == null) {
  echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
  exit();
}

// Validação de perfil - permite acesso APENAS para perfis 3 e 4, ou matrículas específicas (601000, adm)
$matriculasPermitidas = ['601000', 'adm'];
$perfisPermitidos = ['3', '4', 3, 4];

if (!in_array($perfil, $perfisPermitidos) && !in_array($matricula, $matriculasPermitidas)) {
  echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta página - Acesso restrito ao perfil administrativo');window.location=\"index.php\"</script>";
  exit();
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include "../conecta.php";
include_once "../func/log.php";

// Capturar parâmetros GET ou POST
$mattecGet = isset($_GET["mattec"]) ? $_GET["mattec"] : "";
$diaiGet = isset($_GET["diai"]) ? $_GET["diai"] : "";
$ccustoGet = isset($_GET["ccusto"]) ? $_GET["ccusto"] : "";
$diaiPost = $_POST["diai"] ?? '';
$mattecPost = $_POST["mattec"] ?? '';
$ccustoPost = $_POST["ccusto"] ?? '';

// Definir variáveis finais baseado em GET ou POST
if (!empty($mattecGet) && !empty($diaiGet)) {
    $mattec = $mattecGet;
    $dataSelecionada = $diaiGet;
    $ccustoSelecionado = $ccustoGet;
} else {
    $mattec = $mattecPost;
    $dataSelecionada = $diaiPost;
    $ccustoSelecionado = $ccustoPost;
}

$dataac = $dataSelecionada;

if(!empty($dataac)){
  $data_referencia = new DateTime($dataac);
  if ($data_referencia->format('N') == 1) {
    $usegunda = $data_referencia->format('Y-m-d');
  } else {
    $data_referencia->modify('last monday');
    $usegunda = $data_referencia->format('Y-m-d');
  }
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
  <title>Relatório Deslocamento por Centro de Custo - Portal FFA </title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <style>
    /* Garantir altura consistente entre selects normais e select2 */
    .select2-container .select2-selection--single {
      height: 38px !important;
      display: flex;
      align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
    }
  </style>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <?php include "../menu.php"; include "popupgercol.php"; ?>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4">
        <h1 class="h1 mt-2">Relatório Deslocamento por Centro de Custo</h1>

        <div class="d-flex justify-content-end mb-3 gap-2">
          <button type="button" class="btn btn-success" id="btnExportarExcel" title="Exportar para Excel">
            <i class="fa-solid fa-file-excel"></i>
            Exportar Excel
          </button>
        </div>

        <div id="collapseExample4">
          <form action="./rotaspccusto.php" method="post">
            <div class="row">
              <div class="col-md-5">
                <label><h5>Semana:</h5></label>
                <select class="form-select mb-3" name="diai" id="diai" style="height: 38px;" <?php echo (!empty($mattecGet) && !empty($diaiGet)) ? 'disabled' : ''; ?>>
                  <option value="">Selecione a semana</option>
                  <?php
                  $hoje_time = time();
                  $dia_da_semana = date('w', $hoje_time);
                  $segunda_atual = ($dia_da_semana == 1) ? $hoje_time : strtotime('last Monday', $hoje_time);
                  for($contador = 0; $contador < 4; $contador++) {
                    $dataval = date('Y-m-d', $segunda_atual);
                    $dataFormatada = date('d/m/Y', strtotime($dataval));
                    // Verificar qual data está selecionada (POST ou GET)
                    $selected = ($dataSelecionada && $dataSelecionada === $dataval) ? ' selected' : '';
                    print "<option value='$dataval'$selected>$dataFormatada</option>";
                    $segunda_atual = strtotime('-7 days', $segunda_atual);
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-5">
                <label><h5>Centro de Custo:</h5></label>
                <select class="select2-ccusto form-select mb-3" name="ccusto" id="ccusto" style="width: 100%; height: 38px;" <?php echo (!empty($mattecGet) && !empty($diaiGet)) ? 'disabled' : ''; ?>>
                  <option value="">Selecione um Centro de Custo</option>
                  <?php
                  $sqlCcusto = "SELECT DISTINCT UPPER(TRIM(ccusto)) as ccusto_limpo
                                FROM bdaniel.tbfuncionario 
                                WHERE ccusto IS NOT NULL 
                                AND ccusto != ''
                                AND ccusto NOT REGEXP '^[0-9]'
                                AND status = 'Ativo'
                                ORDER BY ccusto_limpo";
                  $resultadoCcusto = mysql_query($sqlCcusto) or die(mysql_error());
                  while ($rowCcusto = mysql_fetch_array($resultadoCcusto)) {
                    $ccustoItem = $rowCcusto[0]; // Já vem em maiúscula do banco
                    // Verificar novamente se não começa com número após o trim
                    if (!preg_match('/^[0-9]/', $ccustoItem) && !empty($ccustoItem)) {
                      $ccustoSelecionado = isset($_POST['ccusto']) ? $_POST['ccusto'] : (isset($_GET['ccusto']) ? $_GET['ccusto'] : '');
                      $selected = ($ccustoSelecionado && strtoupper($ccustoSelecionado) === $ccustoItem) ? ' selected' : '';
                      print "<option value='$ccustoItem'$selected>$ccustoItem</option>";
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-2">
                <label style="width:100%;color:white;"><h5>.</h5></label>
                <?php if (!empty($mattecGet) && !empty($diaiGet)): ?>
                  <button type="button" class="btn btn-secondary" disabled>Filtros Fixos</button>
                <?php else: ?>
                  <input type="submit" class="btn btn-success" value="Filtrar">
                <?php endif; ?>
              </div>
            </div>
          </form>
          
          <?php 
          // Mostrar aviso de filtro aplicado
          if ($mattec <> '' && $dataSelecionada <> '') {
            // Buscar nome do funcionário
            $sql = "SELECT nome FROM bdfrota.tbusuario WHERE matricula = '$mattec'";
            $resultado = mysql_query($sql);
            $row = mysql_fetch_array($resultado);
            $nomeFuncFiltro = $row[0];
            $dataFormatadaFiltro = date('d/m/Y', strtotime($dataSelecionada));
            
            echo "<div class='alert alert-info alert-dismissible fade show mt-3' role='alert'>
              <i class='fa-solid fa-filter me-2'></i>
              <strong>Filtro Aplicado:</strong> 
              Funcionário: <strong>$nomeFuncFiltro ($mattec)</strong> | 
              Semana: <strong>$dataFormatadaFiltro</strong>
              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
          } elseif ($ccustoSelecionado <> '' && $dataSelecionada <> '') {
            // Filtro por centro de custo
            $dataFormatadaFiltro = date('d/m/Y', strtotime($dataSelecionada));
            
            echo "<div class='alert alert-info alert-dismissible fade show mt-3' role='alert'>
              <i class='fa-solid fa-filter me-2'></i>
              <strong>Filtro Aplicado:</strong> 
              Centro de Custo: <strong>$ccustoSelecionado</strong> | 
              Semana: <strong>$dataFormatadaFiltro</strong>
              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
          }
          ?>
        </div>

        <?php
        // Determinar quais funcionários exibir
        $funcionariosParaExibir = array();
        
        if ($mattec <> '') {
          // Se um funcionário específico foi selecionado
          $funcionariosParaExibir[] = $mattec;
        } elseif ($ccustoSelecionado <> '' && $dataSelecionada <> '') {
          // Se um centro de custo foi selecionado, buscar todos os funcionários
          $sqlFuncs = "SELECT DISTINCT f.matricula, u.nome 
                       FROM bdaniel.tbfuncionario f
                       INNER JOIN bdfrota.tbusuario u ON f.matricula = u.matricula
                       WHERE UPPER(TRIM(f.ccusto)) = UPPER('$ccustoSelecionado')
                       AND f.status = 'Ativo'
                       AND f.matricula IS NOT NULL 
                       AND f.matricula != ''
                       AND u.nome IS NOT NULL 
                       AND u.nome != ''
                       AND u.nome NOT REGEXP '[0-9]'
                       AND u.nome NOT REGEXP '^adm'
                       AND LOWER(u.nome) NOT LIKE '%administrador%'
                       AND LOWER(u.nome) NOT LIKE '%admin%'
                       AND f.matricula NOT IN ('000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999')
                       AND f.matricula NOT REGEXP '^(.)\\1{5}$'
                       ORDER BY u.nome";
          $resultFuncs = mysql_query($sqlFuncs) or die(mysql_error());
          while ($rowFunc = mysql_fetch_array($resultFuncs)) {
            $funcionariosParaExibir[] = $rowFunc[0];
          }
        }
        
        // Array para armazenar totais gerais (todos os funcionários)
        $totaisGerais = array();
        $totalGeralSemanal = 0;
        
        // Percorrer cada funcionário
        foreach ($funcionariosParaExibir as $matFuncionario) {
          $mattec = $matFuncionario;
          
          if ($mattec <> '') {
          $dataInicial = new DateTime($usegunda);
          $dataInicial->modify('next sunday');
          $pdomingo = $dataInicial->format('Y-m-d');
          $dataFinal = new DateTime($pdomingo);
          $dataInicial = new DateTime($usegunda);
          
          // Buscar informações do funcionário
          $sqlInfoFunc = "SELECT u.nome, f.ccusto 
                          FROM bdfrota.tbusuario u
                          LEFT JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                          WHERE u.matricula = '$mattec'";
          $resultInfoFunc = mysql_query($sqlInfoFunc) or die(mysql_error());
          $rowInfoFunc = mysql_fetch_array($resultInfoFunc);
          $nomeFuncionarioAtual = $rowInfoFunc[0];
          $ccustoFuncionarioAtual = $rowInfoFunc[1];
          
          // Não exibir cabeçalho individual quando há múltiplos funcionários
          // Será exibido apenas na tabela de resumo geral
          
          // Array para armazenar os totais de cada dia
          $totaisDiarios = array();

          while ($dataInicial <= $dataFinal) {
            $dataDia = $dataInicial->format('Y-m-d');
            $timestamp = strtotime($dataDia);
            $diaSemanaNumero = date('N', $timestamp);
            $nomeDiaSemana = $nomesDiasDaSemana[$diaSemanaNumero];
            
            // Buscar informações do funcionário para determinar a tabela
            $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$mattec' AND status = 'Ativo'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $nome = $row[0];
            $ccusto = strtoupper($row[1]);

            // Determinar qual tabela usar baseado no centro de custo
            if (strpos($ccusto, 'TIM') !== false) {
              $tabela = "bdfrota.tbosrota";
              $id = 'idtbosrota';
            } elseif (strpos($ccusto, 'CLARO') !== false) {
              $tabela = "bdfrota.tbosrotalatlong";
              $id = 'idtbosrotalatlong';
            } elseif (strpos($ccusto, 'LIGGA') !== false) {
              $tabela = "bdfrota.tbosrota";
              $id = 'idtbosrota';
            } elseif (strpos($ccusto, 'IHS') !== false) {
              $tabela = "bdfrota.tbosrota";
              $id = 'idtbosrota';
            } else {
              $tabela = "bdfrota.tbosrota";
              $id = 'idtbosrota';
            }
            
            // Mostrar detalhes das rotas APENAS se for um único funcionário
            if (count($funcionariosParaExibir) == 1) {
              echo "<div class='card mt-4 mb-4'>
              <div class='card-header d-flex justify-content-between align-items-center'>
                <span>$dataDia - $nomeDiaSemana</span>
                <button type='button' class='btn btn-sm btn-success btn-add-rota' data-tabela='$tabela' data-mattec='$mattec' data-dia='$dataDia'>
                  <i class='fas fa-plus'></i> Adicionar Rota
                </button>
              </div>
              <div class='card-body'>
                <table class='table table-bordered table-sm'>
                  <thead>
                    <tr>
                      <th>Matrícula</th>
                      <th>Nome</th>
                      <th>Endereço</th>
                      <th>Dia</th>
                      <th>Hora</th>
                      <th>OS</th>
                      <th>Latitude</th>
                      <th>Longitude</th>
                      <th>Origem</th>
                      <th>KM</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>";

              $sql = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa, latitude, longitude, $id FROM $tabela where matricula = '$mattec' and data = '$dataDia' group by eta order by data, eta";
              $resultado = mysql_query($sql) or die(mysql_error());

              while ($row = mysql_fetch_array($resultado)) {
                $data = $row[3];
                $datac = explode(" ", $data);
                $datat = $datac[0];
                $endereco = $row[0];
                $bairro = $row[1];
                $cidade = $row[2];
                $eta = $row[4];
                $os = $row[5];
                if ($os == '') $os = $row[7];
                $kmrota = $row[6];
                if ($kmrota == '') $kmrota = 0;
                $foratoa = $row[8];
                if ($foratoa == 1) {
                  $origem = 'MANUAL';
                } elseif ($os == 'CASA') {
                  $origem = 'ANIEL';
                } else {
                  $origem = 'TOA';
                }
                $latitude = $row[9];
                $longitude = $row[10];
                $idtbos = $row[11];

                $endcomp = "$endereco $bairro $cidade";
                if (!mb_detect_encoding($endcomp, 'UTF-8', true)) {
                  $endcomp = utf8_encode("$endereco $bairro $cidade");
                }

                echo "<tr class='rota-row' data-id='$idtbos' data-tabela='$tabela'>
                  <td>$mattec</td>
                  <td>$nome</td>
                  <td class='campo-editavel' data-field='endereco' data-value='$endcomp'>$endcomp</td>
                  <td class='text-nowrap'>$datat</td>
                  <td class='campo-editavel' data-field='eta' data-value='$eta'>$eta</td>
                  <td class='text-nowrap campo-editavel' data-field='os' data-value='$os'>$os</td>
                  <td class='campo-editavel' data-field='latitude' data-value='$latitude'>$latitude</td>
                  <td class='campo-editavel' data-field='longitude' data-value='$longitude'>$longitude</td>
                  <td>$origem</td>
                  <td class='campo-editavel' data-field='kmrota' data-value='$kmrota'>$kmrota</td>
                  <td class='text-nowrap'>
                    <button class='btn btn-sm btn-warning btn-edit-rota' data-id='$idtbos' title='Editar'>
                      <i class='fas fa-edit'></i>
                    </button>
                    <button class='btn btn-sm btn-success btn-save-rota d-none' data-id='$idtbos' title='Salvar'>
                      <i class='fas fa-save'></i>
                    </button>
                    <button class='btn btn-sm btn-secondary btn-cancel-edit d-none' data-id='$idtbos' title='Cancelar'>
                      <i class='fas fa-times'></i>
                    </button>
                    <button class='btn btn-sm btn-danger btn-delete-rota' data-id='$idtbos' data-tabela='$tabela' title='Excluir'>
                      <i class='fas fa-trash'></i>
                    </button>
                  </td>
                 </tr>";
              }
            } // Fim do if para mostrar detalhes

            $sql = "SELECT ROUND(kmsrodados,1) FROM bdfrota.tbkmrodadosdia where matricula = '$mattec' and dia = '$dataDia'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $kmsrodados = $row[0];

            // Mostrar linhas de ALMOÇO e edições APENAS para funcionário único
            if (count($funcionariosParaExibir) == 1) {
              if ($kmsrodados > 0) {
                echo "<tr>
                  <td>$mattec</td>
                  <td>$nome</td>
                  <td>-</td>
                  <td class='text-nowrap'>$datat</td>
                  <td>-</td>
                  <td>ALMOÇO</td>
                  <td>-</td>
                  <td>-</td>
                  <td>ALMOÇO</td>
                  <td>5.0</td>
                  <td>-</td>
                 </tr>";
              }

              $sql = "SELECT kmsedit, posneg, matop, motivo FROM bdfrota.tbkmdiarioedit where matricula = '$mattec' and dataedit = '$dataDia'";
              $resultado = mysql_query($sql) or die(mysql_error());
              if (mysql_num_rows($resultado) >= 1) {
                $row = mysql_fetch_array($resultado);
                $kmsedit = $row[0];
                $posneg = $row[1];
                if ($posneg == 1) {
                  $sinal = '-';
                  $kmsrodados = $kmsrodados - $kmsedit;
                } else {
                  $kmsrodados = $kmsrodados + $kmsedit;
                  $sinal = '+';
                }
                $matup = $row[2];
                $motivo = $row[3];
                echo "<tr>
                  <td colspan='1'><b>Mat. Operador</b></td>
                  <td colspan='8'><b>Motivo</b></td>
                  <td colspan='1'><b>KMs</b></td>
                  <td>-</td>
                 </tr>
                 <tr>
                  <td colspan='1'>$matup</td>
                  <td colspan='8'>$motivo</td>
                  <td colspan='1'>$sinal $kmsedit</td>
                  <td>-</td>
                 </tr>";
              }

              // Armazenar o total do dia no array
              $totaisDiarios[] = floatval($kmsrodados);
              
              echo "<tr>
                <td colspan='9'></td>
                <td><b>Total</b></td>
                <td><b>$kmsrodados</b></td>
              </tr>
              </tbody>
              </table>
              </div>
            </div>";
            } else {
              // Para múltiplos funcionários, apenas calcular total sem exibir detalhes
              $sql = "SELECT kmsedit, posneg FROM bdfrota.tbkmdiarioedit where matricula = '$mattec' and dataedit = '$dataDia'";
              $resultado = mysql_query($sql) or die(mysql_error());
              if (mysql_num_rows($resultado) >= 1) {
                $row = mysql_fetch_array($resultado);
                $kmsedit = $row[0];
                $posneg = $row[1];
                if ($posneg == 1) {
                  $kmsrodados = $kmsrodados - $kmsedit;
                } else {
                  $kmsrodados = $kmsrodados + $kmsedit;
                }
              }
              // Armazenar o total do dia no array
              $totaisDiarios[] = floatval($kmsrodados);
            }
            
            $dataInicial->add(new DateInterval('P1D'));
          }
          
          // Calcular total semanal e média
          $totalSemanal = array_sum($totaisDiarios);
          $mediaDiaria = count($totaisDiarios) > 0 ? $totalSemanal / 6 : 0;
          $totalSemanalFormatado = number_format($totalSemanal, 1, ',', '.');
          $mediaDiariaFormatada = number_format($mediaDiaria, 1, ',', '.');
          
          // Adicionar ao total geral
          $totalGeralSemanal += $totalSemanal;
          $totaisGerais[] = array(
            'matricula' => $mattec,
            'nome' => $nomeFuncionarioAtual,
            'total' => $totalSemanal,
            'media' => $mediaDiaria
          );
          
          // Exibir tabela de resumo - formato completo APENAS para funcionário único
          if (count($funcionariosParaExibir) == 1) {
            echo "<div class='card mt-4 mb-4'>
              <div class='card-header bg-primary text-white'>
                <h5 class='mb-0'><i class='fas fa-chart-line me-2'></i>Resumo da Semana - $nomeFuncionarioAtual</h5>
              </div>
              <div class='card-body'>
                <table class='table table-bordered table-striped'>
                  <thead class='table-dark'>
                    <tr>
                      <th class='text-center'>Dia</th>";
            
            // Cabeçalhos dos dias
            $dataResumo = new DateTime($usegunda);
            for($i = 0; $i < 7; $i++) {
              $diaSemana = $nomesDiasDaSemana[$dataResumo->format('N')];
              $diaFormatado = $dataResumo->format('d/m');
              echo "<th class='text-center'>$diaSemana<br><small>($diaFormatado)</small></th>";
              $dataResumo->add(new DateInterval('P1D'));
            }
            
            echo "<th class='text-center bg-info text-white'>Total Semanal</th>
                      <th class='text-center bg-success text-white'>Média Diária*</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class='text-center align-middle'><strong>KM Rodados</strong></td>";
            
            // Valores de cada dia
            foreach($totaisDiarios as $total) {
              $totalFormatado = number_format($total, 1, ',', '.');
              echo "<td class='text-center align-middle'><strong>$totalFormatado</strong></td>";
            }
            
            echo "<td class='text-center align-middle bg-info bg-opacity-25'><strong style='font-size: 1.1em;'>$totalSemanalFormatado km</strong></td>
                      <td class='text-center align-middle bg-success bg-opacity-25'><strong style='font-size: 1.1em;'>$mediaDiariaFormatada km</strong></td>
                    </tr>
                  </tbody>
                </table>
                <div class='alert alert-info mt-3 mb-0'>
                  <i class='fas fa-info-circle me-2'></i>
                  <small><strong>* Média Diária:</strong> Calculada dividindo o total semanal por 6 (1 dia de descanso).</small>
                </div>
              </div>
            </div>";
          }
          // Para múltiplos funcionários, NÃO exibir nada individual, apenas acumular dados
        }
        } // Fim do foreach de funcionários
        
        // Se houver múltiplos funcionários, exibir resumo geral
        if (count($totaisGerais) > 1) {
          $mediaGeralDiaria = $totalGeralSemanal / 6;
          $totalGeralFormatado = number_format($totalGeralSemanal, 1, ',', '.');
          $mediaGeralFormatada = number_format($mediaGeralDiaria, 1, ',', '.');
          
          // Ordenar funcionários do maior total para o menor
          usort($totaisGerais, function($a, $b) {
            return $b['total'] <=> $a['total']; // Ordem decrescente
          });
          
          echo "<div class='card mt-5 mb-4 border-success'>
            <div class='card-header bg-success text-white'>
              <h4 class='mb-0'><i class='fas fa-chart-bar me-2'></i>Resumo Geral - $ccustoSelecionado</h4>
            </div>
            <div class='card-body'>
              <table class='table table-bordered table-hover'>
                <thead class='table-success'>
                  <tr>
                    <th class='text-center'>#</th>
                    <th>Matrícula</th>
                    <th>Nome do Funcionário</th>
                    <th class='text-center'>Total Semanal (km)</th>
                    <th class='text-center'>Média Diária (km)</th>
                  </tr>
                </thead>
                <tbody>";
          
          $posicao = 1;
          foreach ($totaisGerais as $funcTotal) {
            $totalFunc = number_format($funcTotal['total'], 1, ',', '.');
            $mediaFunc = number_format($funcTotal['media'], 1, ',', '.');
            
            echo "<tr>
              <td class='text-center'><strong>$posicao</strong></td>
              <td>
                {$funcTotal['matricula']}
                <a href='/frotas/painel-suporte/rotaspmat.php?mattec={$funcTotal['matricula']}&diai=$dataSelecionada' target='_blank' class='btn btn-sm btn-primary ms-2' title='Conferir deslocamento detalhado do condutor'>
                  <i class='fas fa-external-link-alt'></i>
                </a>
              </td>
              <td>{$funcTotal['nome']}</td>
              <td class='text-center'><strong>$totalFunc</strong></td>
              <td class='text-center'><strong>$mediaFunc</strong></td>
            </tr>";
            $posicao++;
          }
          
          echo "</tbody>
                <tfoot class='table-dark'>
                  <tr>
                    <td colspan='5' class='text-center'>
                      <i class='fas fa-users me-2'></i>Total de funcionários: <strong>" . count($totaisGerais) . "</strong>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>";
        }
        ?>

      </div>
    </main>

    <footer class="py-4 bg-light mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
          <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
        </div>
      </div>
    </footer>
  </div>

  <!-- Modal de Adicionar Rota -->
  <div class="modal fade" id="modalAdicionarRota" tabindex="-1" aria-labelledby="modalAdicionarRotaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAdicionarRotaLabel">
            <i class="fa-solid fa-plus-circle"></i> Adicionar Nova Rota
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formAdicionarRota">
          <div class="modal-body">
            <input type="hidden" id="addMatricula" name="matricula">
            <input type="hidden" id="addData" name="data">
            <input type="hidden" id="addTabela" name="tabela">
            
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="addEndereco" class="form-label">Endereço Completo: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="addEndereco" name="endereco" placeholder="Rua, Número - Bairro, Cidade-UF" required>
                <small class="text-muted">Exemplo: Rua das Flores, 123 - Centro, Belo Horizonte-MG</small>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="addEta" class="form-label">Hora (ETA): <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="addEta" name="eta" required>
              </div>
              <div class="col-md-6">
                <label for="addOS" class="form-label">Número da OS: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="addOS" name="os" placeholder="Digite o número da OS" required>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="addLatitude" class="form-label">Latitude:</label>
                <input type="text" class="form-control" id="addLatitude" name="latitude" placeholder="-19.9167" step="any">
              </div>
              <div class="col-md-4">
                <label for="addLongitude" class="form-label">Longitude:</label>
                <input type="text" class="form-control" id="addLongitude" name="longitude" placeholder="-43.9345" step="any">
              </div>
              <div class="col-md-4">
                <label for="addKm" class="form-label">KM:</label>
                <input type="number" class="form-control" id="addKm" name="kmrota" placeholder="0" step="0.1" value="0">
              </div>
            </div>
            
            <div class="alert alert-info">
              <i class="fa-solid fa-info-circle"></i> <strong>Informação:</strong> A rota será marcada como origem MANUAL e foratoa = 1.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">
              <i class="fa-solid fa-save"></i> Salvar Rota
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="src/js/scripts.js"></script>

<script>
$(document).ready(function () {
  // Inicializar Select2 para Centro de Custo com busca
  $('.select2-ccusto').select2({
    placeholder: 'Digite para buscar o Centro de Custo',
    allowClear: true,
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

      // Pesquisar no texto do Centro de Custo
      if (data.text && data.text.toLowerCase().includes(params.term.toLowerCase())) {
        return data;
      }

      // Se não houver correspondência, retorna null
      return null;
    }
  });
});

(function(){
  // Editar - torna os campos editáveis
  $(document).on('click', '.btn-edit-rota', function(){
    var $row = $(this).closest('tr');
    var id = $(this).data('id');
    
    // Salvar valores originais para poder cancelar
    $row.data('original-values', {});
    
    // Transformar cada campo editável em input
    $row.find('.campo-editavel').each(function(){
      var $td = $(this);
      var field = $td.data('field');
      var value = $td.data('value');
      
      // Salvar valor original
      $row.data('original-values')[field] = value;
      
      var inputType = 'text';
      var inputClass = 'form-control form-control-sm';
      var extraAttrs = '';
      
      // Definir tipo de input baseado no campo
      if(field === 'eta') {
        inputType = 'time';
      } else if(field === 'latitude' || field === 'longitude' || field === 'kmrota') {
        inputType = 'number';
        extraAttrs = 'step="any"';
      } else if(field === 'endereco') {
        extraAttrs = 'style="min-width: 250px;"';
      }
      
      // Criar o input
      var $input = $('<input>', {
        type: inputType,
        class: inputClass,
        value: value,
        'data-field': field
      });
      
      if(extraAttrs) {
        if(extraAttrs.includes('step')) {
          $input.attr('step', 'any');
        }
        if(extraAttrs.includes('style')) {
          $input.attr('style', 'min-width: 250px;');
        }
      }
      
      // Substituir conteúdo do TD pelo input
      $td.html($input);
    });
    
    // Mostrar botões de salvar e cancelar, esconder botão de editar
    $row.find('.btn-edit-rota').addClass('d-none');
    $row.find('.btn-save-rota').removeClass('d-none');
    $row.find('.btn-cancel-edit').removeClass('d-none');
    $row.find('.btn-delete-rota').addClass('d-none');
  });
  
  // Cancelar edição
  $(document).on('click', '.btn-cancel-edit', function(){
    var $row = $(this).closest('tr');
    var originalValues = $row.data('original-values');
    
    // Restaurar valores originais
    $row.find('.campo-editavel').each(function(){
      var $td = $(this);
      var field = $td.data('field');
      var originalValue = originalValues[field];
      
      // Restaurar o texto original
      $td.html(originalValue);
    });
    
    // Restaurar botões
    $row.find('.btn-edit-rota').removeClass('d-none');
    $row.find('.btn-save-rota').addClass('d-none');
    $row.find('.btn-cancel-edit').addClass('d-none');
    $row.find('.btn-delete-rota').removeClass('d-none');
  });
  
  // Salvar edição
  $(document).on('click', '.btn-save-rota', function(){
    var $row = $(this).closest('tr');
    var id = $row.data('id');
    var tabela = $row.data('tabela');
    
    // Coletar valores dos inputs
    var dados = {
      id: id,
      tabela: tabela
    };
    
    $row.find('.campo-editavel input').each(function(){
      var $input = $(this);
      var field = $input.data('field');
      dados[field] = $input.val();
    });
    
    // Validação básica
    if(!dados.endereco || !dados.eta || !dados.os) {
      alert('Por favor, preencha todos os campos obrigatórios (Endereço, Hora e OS)');
      return;
    }
    
    // Enviar dados via AJAX
    $.post('salvar_rotaDEV.php', dados, function(resp){
      if(resp && resp.success){
        alert('Rota atualizada com sucesso!');
        location.reload();
      } else {
        alert('Erro: '+(resp && resp.message ? resp.message : 'erro desconhecido'));
      }
    }, 'json').fail(function(xhr){
      alert('Falha na requisição: '+xhr.statusText);
      console.error('Erro:', xhr.responseText);
    });
  });

  // Excluir
  $(document).on('click', '.btn-delete-rota', function(){
    if(!confirm('Confirma exclusão desta rota?')) return;
    var id = $(this).data('id');
    var tabela = $(this).data('tabela');
    $.post('excluir_rota.php', {id:id, tabela:tabela}, function(resp){
      if(resp && resp.success){
        alert('Rota excluída com sucesso!');
        location.reload();
      } else {
        alert('Erro: '+(resp && resp.message ? resp.message : 'erro desconhecido'));
      }
    }, 'json').fail(function(xhr){
      alert('Falha: '+xhr.statusText);
    });
  });

  // Adicionar rota
  $(document).on('click', '.btn-add-rota', function(){
    var mattec = $(this).data('mattec');
    var dia = $(this).data('dia');
    var tabela = $(this).data('tabela');
    
    // Preenche os campos hidden do modal
    $('#addMatricula').val(mattec);
    $('#addData').val(dia);
    $('#addTabela').val(tabela);
    
    // Limpa os campos do formulário
    $('#formAdicionarRota')[0].reset();
    $('#addMatricula').val(mattec);
    $('#addData').val(dia);
    $('#addTabela').val(tabela);
    
    // Abre o modal
    var modal = new bootstrap.Modal(document.getElementById('modalAdicionarRota'));
    modal.show();
  });

  // Submeter formulário de adicionar rota
  $('#formAdicionarRota').on('submit', function(e){
    e.preventDefault();
    
    var formData = $(this).serialize();
    
    $.post('adicionar_rota.php', formData, function(resp){
      if(resp && resp.success){
        alert('Rota adicionada com sucesso!');
        $('#modalAdicionarRota').modal('hide');
        location.reload();
      } else {
        alert('Erro: '+(resp && resp.message ? resp.message : 'erro desconhecido'));
      }
    }, 'json').fail(function(xhr){
      console.error('Erro na requisição:', xhr.responseText);
      alert('Falha na requisição: '+xhr.statusText);
    });
  });
})();

// Função para exportar dados para Excel - Todos os técnicos de um centro de custo
document.getElementById('btnExportarExcel')?.addEventListener('click', function() {
  // Pegar o período e centro de custo selecionados
  const periodoSelecionado = document.getElementById('diai')?.value;
  const ccustoSelecionado = document.getElementById('ccusto')?.value;
  
  if (!periodoSelecionado || periodoSelecionado === '') {
    alert('Por favor, selecione uma semana antes de exportar.\n\nUse o filtro "Semana" para escolher o período desejado.');
    return;
  }
  
  if (!ccustoSelecionado || ccustoSelecionado === '') {
    alert('Por favor, selecione um Centro de Custo antes de exportar.\n\nUse o filtro "Centro de Custo" para escolher.');
    return;
  }
  
  // Formatar data para exibição
  const dataObj = new Date(periodoSelecionado + 'T00:00:00');
  const dataFormatada = dataObj.toLocaleDateString('pt-BR');
  
  // Confirmar exportação
  const confirmar = confirm(
    'Exportar relatório para Excel?\n\n' +
    'Centro de Custo: ' + ccustoSelecionado + '\n' +
    'Período: Semana de ' + dataFormatada + '\n\n' +
    'Conteúdo:\n' +
    '• Resumo de todos os funcionários\n' +
    '• Total semanal e média diária\n' +
    '• Ordenado do maior para o menor\n\n' +
    'Deseja continuar?'
  );
  
  if (confirmar) {
    // Mostrar mensagem de processamento
    const btnExportar = document.getElementById('btnExportarExcel');
    const textoOriginal = btnExportar.innerHTML;
    btnExportar.disabled = true;
    btnExportar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Gerando Excel...';
    
    // Redirecionar para o script de exportação
    window.location.href = 'exportar_rotas_ccusto_excel.php?periodo=' + periodoSelecionado + '&ccusto=' + encodeURIComponent(ccustoSelecionado);
    
    // Restaurar botão após 3 segundos
    setTimeout(function() {
      btnExportar.disabled = false;
      btnExportar.innerHTML = textoOriginal;
    }, 3000);
  }
});
</script>

</body>
</html>
