<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

// Validação de sessão
if ($perfil == null) {
  echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
  exit();
}

// Validação de perfil
$matriculasPermitidas = ['601000', 'adm'];
$perfisPermitidos = ['3', '4', 3, 4];

if (!in_array($perfil, $perfisPermitidos) && !in_array($matricula, $matriculasPermitidas)) {
  echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta página');window.location=\"index.php\"</script>";
  exit();
}

include "../conecta.php";

// Capturar parâmetros
$mattec = $_POST["mattec"] ?? $_GET["mattec"] ?? '';
$dataSelecionada = $_POST["diai"] ?? $_GET["diai"] ?? '';

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Debug Consulta Rotas - Portal FFA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <style>
    .select2-container .select2-selection--single {
      height: 38px !important;
      display: flex;
      align-items: center;
    }
    .null-value { background-color: #ffcccc; }
    .empty-value { background-color: #fff3cd; }
    .has-value { background-color: #d4edda; }
    pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
  </style>
</head>
<body>
  <?php include "../menu.php"; ?>

  <div class="container-fluid px-4 mt-4">
    <h1 class="h1 mb-4">🔍 Debug - Consulta de Rotas</h1>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
      </div>
      <div class="card-body">
        <form method="post">
          <div class="row">
            <div class="col-md-5">
              <label><h6>Semana:</h6></label>
              <select class="form-select mb-3" name="diai" id="diai">
                <option value="">Selecione a semana</option>
                <?php
                $hoje_time = time();
                $dia_da_semana = date('w', $hoje_time);
                $segunda_atual = ($dia_da_semana == 1) ? $hoje_time : strtotime('last Monday', $hoje_time);
                for($contador = 0; $contador < 4; $contador++) {
                  $dataval = date('Y-m-d', $segunda_atual);
                  $dataFormatada = date('d/m/Y', strtotime($dataval));
                  $selected = ($dataSelecionada && $dataSelecionada === $dataval) ? ' selected' : '';
                  print "<option value='$dataval'$selected>$dataFormatada</option>";
                  $segunda_atual = strtotime('-7 days', $segunda_atual);
                }
                ?>
              </select>
            </div>
            <div class="col-md-5">
              <label><h6>Funcionário:</h6></label>
              <select class="select2 form-select mb-3" name="mattec" id="mattec" style="width: 100%;">
                <option value="">Selecione um funcionário</option>
                <?php
                $sql = "SELECT matricula, nome 
                        FROM bdfrota.tbusuario 
                        WHERE matricula IS NOT NULL 
                        AND matricula != '' 
                        AND nome IS NOT NULL 
                        AND nome != ''
                        AND nome NOT REGEXP '[0-9]'
                        AND nome NOT REGEXP '^adm'
                        AND LOWER(nome) NOT LIKE '%administrador%'
                        AND LOWER(nome) NOT LIKE '%admin%'
                        AND matricula NOT IN ('000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999')
                        AND matricula NOT REGEXP '^(.)\\1{5}$'
                        GROUP BY matricula, nome
                        ORDER BY nome";
                $resultado = mysql_query($sql) or die(mysql_error());
                while ($row = mysql_fetch_array($resultado)) {
                  $mat = $row[0];
                  $nomeFunc = $row[1];
                  $selected = ($mattec && $mattec === $mat) ? ' selected' : '';
                  print "<option value='$mat'$selected>$nomeFunc ($mat)</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-2">
              <label style="width:100%;color:white;"><h6>.</h6></label>
              <input type="submit" class="btn btn-success w-100" value="Buscar">
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php
    if ($mattec <> '' && $dataSelecionada <> '') {
      // Calcular a semana
      $dataac = $dataSelecionada;
      $data_referencia = new DateTime($dataac);
      if ($data_referencia->format('N') == 1) {
        $usegunda = $data_referencia->format('Y-m-d');
      } else {
        $data_referencia->modify('last monday');
        $usegunda = $data_referencia->format('Y-m-d');
      }
      
      $dataInicial = new DateTime($usegunda);
      $dataInicial->modify('next sunday');
      $pdomingo = $dataInicial->format('Y-m-d');
      $dataInicial = new DateTime($usegunda);
      
      // Buscar informações do funcionário
      $sql = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$mattec'";
      echo "<div class='card mb-4'>
        <div class='card-header bg-info text-white'>
          <h5 class='mb-0'><i class='fas fa-user'></i> Informações do Funcionário</h5>
        </div>
        <div class='card-body'>
          <p><strong>Consulta SQL:</strong></p>
          <pre>" . htmlspecialchars($sql) . "</pre>";
      
      $resultado = mysql_query($sql) or die(mysql_error());
      $row = mysql_fetch_array($resultado);
      $nome = $row[0];
      $ccusto = $row[1];
      
      echo "<table class='table table-bordered mt-3'>
        <tr><th>Matrícula</th><td>$mattec</td></tr>
        <tr><th>Nome</th><td>$nome</td></tr>
        <tr><th>Centro de Custo</th><td>$ccusto</td></tr>
      </table>";
      
      // Determinar tabela
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
      
      echo "<div class='alert alert-warning'><strong>Tabela Selecionada:</strong> $tabela (ID: $id)</div>";
      echo "</div></div>";
      
      // Buscar rotas
      $dataDia = $usegunda;
      $sql = "SELECT endereco, bairro, cidade, data, eta, numero_de_ordem, ROUND(kmrota,1), motivo, foratoa, latitude, longitude, $id 
              FROM $tabela 
              WHERE matricula = '$mattec' AND data = '$dataDia' 
              GROUP BY eta 
              ORDER BY data, eta";
      
      echo "<div class='card mb-4'>
        <div class='card-header bg-warning'>
          <h5 class='mb-0'><i class='fas fa-database'></i> Consulta SQL - Rotas do dia $dataDia</h5>
        </div>
        <div class='card-body'>
          <pre>" . htmlspecialchars($sql) . "</pre>
          
          <div class='alert alert-info mt-3'>
            <strong>Legenda:</strong>
            <span class='badge has-value ms-2'>Verde = Tem valor</span>
            <span class='badge empty-value ms-2'>Amarelo = String vazia</span>
            <span class='badge null-value ms-2'>Vermelho = NULL</span>
          </div>";
      
      $resultado = mysql_query($sql) or die("<div class='alert alert-danger'>Erro SQL: " . mysql_error() . "</div>");
      $total_rows = mysql_num_rows($resultado);
      
      echo "<p><strong>Total de registros encontrados:</strong> $total_rows</p>";
      
      if ($total_rows > 0) {
        echo "<div class='table-responsive'>
          <table class='table table-bordered table-striped table-sm'>
            <thead class='table-dark'>
              <tr>
                <th>#</th>
                <th>ID</th>
                <th>Endereço</th>
                <th>Bairro</th>
                <th>Cidade</th>
                <th>Data</th>
                <th>ETA</th>
                <th>OS/Nº Ordem</th>
                <th>KM</th>
                <th>Motivo</th>
                <th>Fora TOA</th>
                <th>Latitude</th>
                <th>Longitude</th>
              </tr>
            </thead>
            <tbody>";
        
        $contador = 1;
        while ($row = mysql_fetch_array($resultado)) {
          $idtbos = $row[11];
          $endereco = $row[0];
          $bairro = $row[1];
          $cidade = $row[2];
          $data = $row[3];
          $eta = $row[4];
          $os = $row[5];
          $kmrota = $row[6];
          $motivo = $row[7];
          $foratoa = $row[8];
          $latitude = $row[9];
          $longitude = $row[10];
          
          // Função para determinar classe CSS
          function getCellClass($value) {
            if (is_null($value)) return 'null-value';
            if ($value === '') return 'empty-value';
            return 'has-value';
          }
          
          echo "<tr>
            <td>$contador</td>
            <td>$idtbos</td>
            <td class='" . getCellClass($endereco) . "'>" . ($endereco ? htmlspecialchars($endereco) : '<em>NULL/VAZIO</em>') . "</td>
            <td class='" . getCellClass($bairro) . "'>" . ($bairro ? htmlspecialchars($bairro) : '<em>NULL/VAZIO</em>') . "</td>
            <td class='" . getCellClass($cidade) . "'>" . ($cidade ? htmlspecialchars($cidade) : '<em>NULL/VAZIO</em>') . "</td>
            <td>$data</td>
            <td>$eta</td>
            <td>$os</td>
            <td>$kmrota</td>
            <td class='" . getCellClass($motivo) . "'>" . ($motivo ? htmlspecialchars($motivo) : '<em>NULL/VAZIO</em>') . "</td>
            <td>$foratoa</td>
            <td class='" . getCellClass($latitude) . "'>$latitude</td>
            <td class='" . getCellClass($longitude) . "'>$longitude</td>
          </tr>";
          
          $contador++;
        }
        
        echo "</tbody>
          </table>
        </div>";
      } else {
        echo "<div class='alert alert-warning'>Nenhum registro encontrado para esta consulta.</div>";
      }
      
      echo "</div></div>";
      
      // Análise detalhada - verificar estrutura da tabela
      echo "<div class='card mb-4'>
        <div class='card-header bg-secondary text-white'>
          <h5 class='mb-0'><i class='fas fa-table'></i> Estrutura da Tabela $tabela</h5>
        </div>
        <div class='card-body'>";
      
      $sql_describe = "DESCRIBE $tabela";
      $resultado_describe = mysql_query($sql_describe);
      
      if ($resultado_describe) {
        echo "<div class='table-responsive'>
          <table class='table table-bordered table-sm'>
            <thead class='table-dark'>
              <tr>
                <th>Campo</th>
                <th>Tipo</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
              </tr>
            </thead>
            <tbody>";
        
        while ($row = mysql_fetch_array($resultado_describe)) {
          $destaque = '';
          if (in_array($row[0], ['endereco', 'bairro', 'cidade', 'latitude', 'longitude'])) {
            $destaque = ' class="table-warning"';
          }
          echo "<tr$destaque>
            <td><strong>{$row[0]}</strong></td>
            <td>{$row[1]}</td>
            <td>{$row[2]}</td>
            <td>{$row[3]}</td>
            <td>{$row[4]}</td>
            <td>{$row[5]}</td>
          </tr>";
        }
        
        echo "</tbody>
          </table>
        </div>";
      }
      
      echo "</div></div>";
      
      // Estatísticas
      echo "<div class='card mb-4'>
        <div class='card-header bg-success text-white'>
          <h5 class='mb-0'><i class='fas fa-chart-bar'></i> Estatísticas de Preenchimento</h5>
        </div>
        <div class='card-body'>";
      
      $sql_stats = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN endereco IS NULL OR endereco = '' THEN 1 ELSE 0 END) as endereco_vazio,
        SUM(CASE WHEN bairro IS NULL OR bairro = '' THEN 1 ELSE 0 END) as bairro_vazio,
        SUM(CASE WHEN cidade IS NULL OR cidade = '' THEN 1 ELSE 0 END) as cidade_vazio,
        SUM(CASE WHEN latitude IS NULL OR latitude = '' THEN 1 ELSE 0 END) as latitude_vazio,
        SUM(CASE WHEN longitude IS NULL OR longitude = '' THEN 1 ELSE 0 END) as longitude_vazio,
        SUM(CASE WHEN numero_de_ordem IS NULL OR numero_de_ordem = '' THEN 1 ELSE 0 END) as os_vazio
      FROM $tabela 
      WHERE matricula = '$mattec' AND data = '$dataDia'";
      
      echo "<p><strong>Consulta de Estatísticas:</strong></p>
      <pre>" . htmlspecialchars($sql_stats) . "</pre>";
      
      $resultado_stats = mysql_query($sql_stats);
      if ($resultado_stats && $row_stats = mysql_fetch_assoc($resultado_stats)) {
        echo "<table class='table table-bordered mt-3'>
          <thead class='table-dark'>
            <tr>
              <th>Campo</th>
              <th>Total de Registros</th>
              <th>Vazios/NULL</th>
              <th>Preenchidos</th>
              <th>% Preenchimento</th>
            </tr>
          </thead>
          <tbody>";
        
        $campos = [
          'Endereço' => 'endereco_vazio',
          'Bairro' => 'bairro_vazio',
          'Cidade' => 'cidade_vazio',
          'Latitude' => 'latitude_vazio',
          'Longitude' => 'longitude_vazio',
          'OS/Nº Ordem' => 'os_vazio'
        ];
        
        foreach ($campos as $nome => $campo) {
          $total = $row_stats['total'];
          $vazios = $row_stats[$campo];
          $preenchidos = $total - $vazios;
          $percentual = $total > 0 ? ($preenchidos / $total) * 100 : 0;
          
          $cor_classe = $percentual >= 80 ? 'table-success' : ($percentual >= 50 ? 'table-warning' : 'table-danger');
          
          echo "<tr class='$cor_classe'>
            <td><strong>$nome</strong></td>
            <td>$total</td>
            <td>$vazios</td>
            <td>$preenchidos</td>
            <td>" . number_format($percentual, 1) . "%</td>
          </tr>";
        }
        
        echo "</tbody>
        </table>";
      }
      
      echo "</div></div>";
    }
    ?>

  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <script>
  $(document).ready(function () {
    $('.select2').select2({
      matcher: function (params, data) {
        if ($.trim(params.term) === '') {
          return data;
        }
        if (
          (data.id && data.id.toString().toLowerCase().includes(params.term.toLowerCase())) ||
          (data.text && data.text.toLowerCase().includes(params.term.toLowerCase()))
        ) {
          return data;
        }
        return null;
      }
    });
  });
  </script>

</body>
</html>
