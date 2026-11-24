<?php
 date_default_timezone_set('America/Sao_Paulo');
 session_start();

 $perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
 $matricula = isset($_SESSION['matricula']) ? trim($_SESSION['matricula']) : '';

 if ($perfil == null || $perfil != 2) {
     echo "<script>alert('Apenas coordenadores têm acesso'); window.location='../index.php';</script>";
     exit;
 }

 include '../conecta.php';

 mysqli_query($conn, "CREATE TABLE IF NOT EXISTS bdfrota.tbferiados_escala_tecnico (
     id INT NOT NULL AUTO_INCREMENT,
     data_feriado DATE NOT NULL,
     uf CHAR(2) NOT NULL,
     is_nacional TINYINT(1) NOT NULL DEFAULT 0,
     descricao VARCHAR(150) NOT NULL,
     observacao VARCHAR(255) DEFAULT NULL,
     matricula_cadastro VARCHAR(20) NOT NULL,
     data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (id),
     INDEX idx_data_uf (data_feriado, uf)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

 $mensagem = '';
 $tipoMensagem = '';

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $dataFeriadoInput = isset($_POST['data_feriado']) ? trim($_POST['data_feriado']) : '';
     $uf = isset($_POST['uf']) ? strtoupper(trim($_POST['uf'])) : '';
     $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
     $observacao = isset($_POST['observacao']) ? trim($_POST['observacao']) : '';
     $isNacional = isset($_POST['is_nacional']) ? 1 : 0;

     if ($isNacional === 1) {
         $uf = 'BR';
     }

     if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFeriadoInput)) {
         $mensagem = 'Data inválida. Selecione uma data válida no calendário.';
         $tipoMensagem = 'danger';
     } elseif (!preg_match('/^[A-Z]{2}$/', $uf)) {
         $mensagem = 'UF inválida. Informe 2 letras (ex: SP) ou marque como nacional.';
         $tipoMensagem = 'danger';
     } elseif ($descricao === '') {
         $mensagem = 'Informe a descrição do feriado.';
         $tipoMensagem = 'danger';
     } else {
         $partesData = explode('-', $dataFeriadoInput);
         $ano = isset($partesData[0]) ? (int)$partesData[0] : 0;
         $mes = isset($partesData[1]) ? (int)$partesData[1] : 0;
         $dia = isset($partesData[2]) ? (int)$partesData[2] : 0;

         if (!checkdate($mes, $dia, $ano)) {
             $mensagem = 'Data inválida. Selecione uma data válida no calendário.';
             $tipoMensagem = 'danger';
         } else {
             $dataFeriado = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);

             $stmtExiste = mysqli_prepare($conn, "SELECT id FROM bdfrota.tbferiados_escala_tecnico WHERE data_feriado = ? AND uf = ? LIMIT 1");
             mysqli_stmt_bind_param($stmtExiste, 'ss', $dataFeriado, $uf);
             mysqli_stmt_execute($stmtExiste);
             mysqli_stmt_store_result($stmtExiste);

             if (mysqli_stmt_num_rows($stmtExiste) > 0) {
                 $mensagem = 'Já existe um feriado cadastrado para esta data e UF.';
                 $tipoMensagem = 'warning';
             } else {
                 $stmt = mysqli_prepare($conn, "INSERT INTO bdfrota.tbferiados_escala_tecnico (data_feriado, uf, is_nacional, descricao, observacao, matricula_cadastro) VALUES (?, ?, ?, ?, ?, ?)");
                 mysqli_stmt_bind_param($stmt, 'ssisss', $dataFeriado, $uf, $isNacional, $descricao, $observacao, $matricula);

                 if (mysqli_stmt_execute($stmt)) {
                     $mensagem = 'Feriado cadastrado com sucesso.';
                     $tipoMensagem = 'success';
                 } else {
                     $mensagem = 'Não foi possível salvar o feriado.';
                     $tipoMensagem = 'danger';
                 }

                 mysqli_stmt_close($stmt);
             }

             mysqli_stmt_close($stmtExiste);
         }
     }
 }

 $feriados = [];
 $sqlFeriados = "SELECT id, data_feriado, uf, is_nacional, descricao, observacao, data_cadastro
                 FROM bdfrota.tbferiados_escala_tecnico
                 ORDER BY data_feriado ASC, uf ASC
                 LIMIT 200";
 $resultFeriados = mysqli_query($conn, $sqlFeriados);
 if ($resultFeriados) {
     while ($row = mysqli_fetch_assoc($resultFeriados)) {
         $feriados[] = $row;
     }
 }
 ?>

 <!DOCTYPE html>
 <html lang="pt-br">
 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Cadastro de Feriados - Escala Técnicos</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" rel="stylesheet">
 </head>
 <body class="bg-light">
 <div class="container-fluid py-4">
     <div class="card shadow-sm mb-4">
         <div class="card-header bg-primary text-white">
             <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Cadastro de Feriados</h5>
         </div>
         <div class="card-body">
             <?php if ($mensagem !== ''): ?>
                 <div class="alert alert-<?php echo $tipoMensagem; ?>" role="alert">
                     <?php echo htmlspecialchars($mensagem); ?>
                 </div>
             <?php endif; ?>

             <form method="post" class="row g-3">
                 <div class="col-md-4">
                     <label class="form-label">Data do feriado</label>
                     <input type="date" name="data_feriado" class="form-control" required>
                 </div>
                 <div class="col-md-2">
                     <label class="form-label">UF</label>
                     <input type="text" name="uf" class="form-control text-uppercase" maxlength="2" placeholder="SP" required>
                 </div>
                 <div class="col-md-4 d-flex align-items-end">
                     <div class="form-check">
                         <input class="form-check-input" type="checkbox" value="1" name="is_nacional" id="is_nacional">
                         <label class="form-check-label" for="is_nacional">
                             Feriado nacional (marca UF como BR)
                         </label>
                     </div>
                 </div>

                 <div class="col-md-6">
                     <label class="form-label">Descrição do feriado</label>
                     <input type="text" name="descricao" class="form-control" maxlength="150" placeholder="Ex: Tiradentes" required>
                 </div>
                 <div class="col-md-6">
                     <label class="form-label">Observação</label>
                     <input type="text" name="observacao" class="form-control" maxlength="255" placeholder="Opcional">
                 </div>

                 <div class="col-12 text-end">
                     <button type="submit" class="btn btn-success">
                         <i class="fas fa-save me-1"></i>Salvar feriado
                     </button>
                 </div>
             </form>
         </div>
     </div>

     <div class="card shadow-sm">
         <div class="card-header bg-dark text-white">
             <h6 class="mb-0"><i class="fas fa-list me-2"></i>Feriados cadastrados (últimos 200)</h6>
         </div>
         <div class="card-body p-0">
             <div class="table-responsive">
                 <table class="table table-striped mb-0">
                     <thead>
                         <tr>
                             <th>Data</th>
                             <th>UF</th>
                             <th>Descrição</th>
                             <th>Observação</th>
                             <th>Cadastro</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php if (empty($feriados)): ?>
                             <tr>
                                 <td colspan="5" class="text-center text-muted py-3">Nenhum feriado cadastrado.</td>
                             </tr>
                         <?php else: ?>
                             <?php foreach ($feriados as $feriado): ?>
                                 <tr>
                                     <td><?php echo date('d/m/Y', strtotime($feriado['data_feriado'])); ?></td>
                                     <td>
                                         <span class="badge <?php echo $feriado['is_nacional'] ? 'bg-success' : 'bg-secondary'; ?>">
                                             <?php echo htmlspecialchars($feriado['uf']); ?>
                                         </span>
                                     </td>
                                     <td><?php echo htmlspecialchars($feriado['descricao']); ?></td>
                                     <td><?php echo htmlspecialchars($feriado['observacao']); ?></td>
                                     <td><?php echo date('d/m/Y H:i', strtotime($feriado['data_cadastro'])); ?></td>
                                 </tr>
                             <?php endforeach; ?>
                         <?php endif; ?>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>

 <script>
     document.getElementById('is_nacional').addEventListener('change', function() {
         var ufInput = document.querySelector('input[name="uf"]');
         if (this.checked) {
             ufInput.value = 'BR';
             ufInput.setAttribute('readonly', 'readonly');
         } else {
             ufInput.value = '';
             ufInput.removeAttribute('readonly');
         }
     });
 </script>
 </body>
 </html>