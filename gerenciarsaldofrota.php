<?php
  date_default_timezone_set('America/Sao_Paulo');
  session_start();
  
  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];

  // Lista de matrículas autorizadas
  $matriculas_autorizadas = array('601004', '086272', '601000');

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"telainicio.php\"</script>";
  } elseif ($perfil <> '4'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"telainicio.php\"</script>";
  } elseif (!in_array($matricula, $matriculas_autorizadas)){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão para acessar esta tela');window.location=\"telainicio.php\"</script>";
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  $data = date('Y-m-d');
  $useg = date('Y-m-d', strtotime($data . 'last monday'));

  include"conecta.php";
  include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  // ============================================
  // PROCESSAR ATUALIZAÇÃO DE SALDO
  // ============================================
  if (isset($_POST['atualizar_saldo'])) {
    $novo_saldo = $_POST['novo_saldo'];
    // Como agora é type="number", já vem no formato correto com ponto
    $novo_saldo = floatval($novo_saldo);
    
    $hoje = date('Y-m-d H:i:s');
    
    // Insere novo registro com o saldo atualizado
    $sql_insert = "INSERT INTO bdfrota.tbsaldofrota (data_e_hora, saldoinicial, saldoatual, modificadoem) VALUES ('$hoje', $novo_saldo, $novo_saldo, '$hoje')";
    $resultado_insert = mysql_query($sql_insert) or die(mysql_error());
    
    if ($resultado_insert) {
      // Registra no log
      $acao = 'Atualizou saldo da frota';
      $flag = '1';
      $logresult = enviarlog($hoje, $acao, $flag, $matricula, $matricula, $saldofrota_original, $novo_saldo);
      
      echo"<script language='javascript' type='text/javascript'>alert('Saldo atualizado com sucesso!');window.location=\"frotaorccoordDEV.php\"</script>";
    } else {
      echo"<script language='javascript' type='text/javascript'>alert('Erro ao atualizar saldo!');window.location=\"frotaorccoordDEV.php\"</script>";
    }
  }
  // ============================================

  if (strftime('%A') == "domingo" ){
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
  } elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
  } else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
  }
  
  // Busca o último saldo, ID e data/hora
  $sql1 = "SELECT idtbsaldofrota, saldoatual, data_e_hora FROM bdfrota.tbsaldofrota order by data_e_hora desc limit 1";
    $resultado1 = mysql_query($sql1) or die(mysql_error());
    $row1 = mysql_fetch_array($resultado1);
    $id_saldo = $row1[0];
    $saldofrota = $row1[1];
    $data_hora_saldo = $row1[2];
    // Formata a data para exibição (dd/mm/aaaa)
    $data_hora_saldo_formatada = date('d/m/Y', strtotime($data_hora_saldo));
    $saldofrota_original = $saldofrota;
    
    // Mantém apenas o valor numérico (sem formatação)
    $saldofrota = number_format($saldofrota, 2, '.', '');
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
    <title>Gerenciar Saldo Frota</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>        

    <script language="javascript">   
      function moeda(a, e, r, t) {
          let n = ""
            , h = j = 0
            , u = tamanho2 = 0
            , l = ajd2 = ""
            , o = window.Event ? t.which : t.keyCode;
          if (13 == o || 8 == o)
              return !0;
          if (n = String.fromCharCode(o),
          -1 == "0123456789".indexOf(n))
              return !1;
          for (u = a.value.length,
          h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
              ;
          for (l = ""; h < u; h++)
              -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
          if (l += n,
          0 == (u = l.length) && (a.value = ""),
          1 == u && (a.value = "0" + r + "0" + l),
          2 == u && (a.value = "0" + r + l),
          u > 2) {
              for (ajd2 = "",
              j = 0,
              h = u - 3; h >= 0; h--)
                  3 == j && (ajd2 += e,
                  j = 0),
                  ajd2 += l.charAt(h),
                  j++;
              for (a.value = "",
              tamanho2 = ajd2.length,
              h = tamanho2 - 1; h >= 0; h--)
                  a.value += ajd2.charAt(h);
              a.value += r + l.substr(u - 2, u)
          }
          return !1
      }
    </script>  
  </head>
  
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <?php include"./menu.php"; ?>
      <div id="layoutSidenav_content">
        <main>
          <div class="container-fluid px-4">
            <h1 class="mt-4">Gerenciar Saldo da Frota</h1>
            <h6>Fundo atual da frota: R$ <?= $saldofrota;?></h6>
            
            <!-- Card para Editar Saldo -->
            <div class="card mb-4 mt-4">
              <div class="card-header bg-primary text-white">
                <i class="fas fa-wallet me-1"></i>
                Atualizar Saldo da Frota
              </div>
              <div class="card-body">
                <form method="post" action="" id="formSaldo">
                  <div class="row">
                    <div class="col-md-6">
                      <label for="data_hora_saldo" class="form-label"><b>Semana iniciada em:</b></label>
                      <input type="text" class="form-control" id="data_hora_saldo" value="<?= $data_hora_saldo_formatada; ?>" disabled>
                    </div>
                    <div class="col-md-6">
                      <label for="novo_saldo" class="form-label"><b>Novo Saldo:</b> <span style="color:red;">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <button class="btn btn-outline-danger" type="button" onclick="ajustarSaldo(-1)">
                          <i class="fas fa-minus"></i>
                        </button>
                        <input 
                          type="number" 
                          step="0.01"
                          class="form-control text-center" 
                          id="novo_saldo" 
                          name="novo_saldo" 
                          value="<?= $saldofrota; ?>" 
                          required>
                        <button class="btn btn-outline-success" type="button" onclick="ajustarSaldo(1)">
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                      <small class="text-muted">Use os botões + e - para ajustar R$ 1,00 por clique ou digite o valor desejado</small>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-12">
                      <button 
                        type="submit" 
                        name="atualizar_saldo" 
                        class="btn btn-success" 
                        onclick="return confirm('Tem certeza que deseja atualizar o saldo da frota?')">
                        <i class="fas fa-save me-1"></i> Salvar Novo Saldo
                      </button>
                      <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                        <i class="fas fa-undo me-1"></i> Cancelar
                      </button>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-12">
                      <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        <b>Atenção:</b> Verifique as informações antes de prosseguir.
                      </div>
                    </div>
                  </div>
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
            
      </div>
      
      <script>
        // Função para ajustar o saldo com os botões + e -
        function ajustarSaldo(tipo) {
          var input = document.getElementById('novo_saldo');
          
          // Converte para número
          var valor = parseFloat(input.value) || 0;
          
          // Ajusta o valor (tipo: 1 = adicionar, -1 = subtrair)
          valor = valor + tipo;
          
          // Garante que não fique negativo
          if (valor < 0) {
            valor = 0;
          }
          
          // Atualiza o valor com 2 casas decimais
          input.value = valor.toFixed(2);
        }
      </script>
      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
      <script src="src/js/scripts.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
      <script src="assets/demo/chart-area-demo.js"></script>
      <script src="assets/demo/chart-bar-demo.js"></script>
      <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
      <script src="src/js/datatables-simple-demo.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    </body>
</html>
