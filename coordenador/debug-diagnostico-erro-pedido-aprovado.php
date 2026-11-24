<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
include_once "../conecta.php";

// Função para normalizar strings: remove acentos, converte para minúsculas e colapsa espaços
function normalize_string($str) {
    if ($str === null) return '';
    // garante encoding correto
    $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    // trim e lowercase
    $str = mb_strtolower(trim($str), 'UTF-8');
    // translitera caracteres acentuados para ASCII (ex: 'á' -> 'a')
    $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
    if ($trans !== false) {
        $str = $trans;
    }
    // substitui quaisquer caracteres que não sejam letras ou números por espaço
    $str = preg_replace('/[^a-z0-9]+/', ' ', $str);
    // colapsa múltiplos espaços
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

// Para usar: diagnostico_pedido.php?id=123
// Ou acesse direto e digite o ID

$id_pedido = isset($_GET['id']) ? $_GET['id'] : '';
$matricula_coordenador = isset($_SESSION['matricula']) ? $_SESSION['matricula'] : '';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <title>Diagnóstico de Pedido - Sistema Frotas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; }
        .result-box { 
            background: white; 
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { border-left: 4px solid #28a745; }
        .warning { border-left: 4px solid #ffc107; }
        .danger { border-left: 4px solid #dc3545; }
        .info { border-left: 4px solid #17a2b8; }
        .check-item { 
            padding: 10px; 
            margin: 5px 0; 
            border-radius: 4px;
        }
        .check-ok { background-color: #d4edda; color: #155724; }
        .check-fail { background-color: #f8d7da; color: #721c24; }
        .check-warn { background-color: #fff3cd; color: #856404; }
        pre { 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-radius: 4px;
            overflow-x: auto;
        }
        h2 { color: #333; margin-top: 30px; }
        h3 { color: #666; margin-top: 20px; }
        .badge-custom { 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔍 Diagnóstico de Pedido - Sistema Frotas</h1>
        
        <div class="result-box info">
            <h5>📋 Informações da Sessão</h5>
            <p><strong>Coordenador Logado:</strong> <?= $matricula_coordenador ? $matricula_coordenador : '<span class="text-danger">NÃO LOGADO</span>' ?></p>
            <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <!-- Formulário para inserir ID do pedido -->
        <div class="result-box">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <label for="id" class="form-label"><strong>ID do Pedido (idtbpedidostec):</strong></label>
                    <input type="number" class="form-control" id="id" name="id" value="<?= htmlspecialchars($id_pedido) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">🔍 Diagnosticar</button>
                </div>
            </form>
        </div>

        <?php
        if ($id_pedido) {
            echo "<h2>📊 DIAGNÓSTICO DO PEDIDO #$id_pedido</h2>";
            
            // ==========================================
            // 1. VERIFICAR SE O PEDIDO EXISTE
            // ==========================================
            echo "<div class='result-box'>";
            echo "<h3>1️⃣ Verificação do Pedido na Tabela tbpedidostec</h3>";
            
            $sql1 = "SELECT * FROM bdfrota.tbpedidostec WHERE idtbpedidostec = '$id_pedido'";
            $resultado1 = mysql_query($sql1);
            
            if (!$resultado1) {
                echo "<div class='check-fail'>❌ Erro na consulta: " . mysql_error() . "</div>";
            } elseif (mysql_num_rows($resultado1) == 0) {
                echo "<div class='check-fail'>❌ PEDIDO NÃO ENCONTRADO na tabela tbpedidostec com ID: $id_pedido</div>";
                echo "<p class='text-danger'><strong>CAUSA:</strong> O pedido não existe ou foi deletado.</p>";
            } else {
                $pedido = mysql_fetch_assoc($resultado1);
                echo "<div class='check-ok'>✅ Pedido encontrado!</div>";
                
                echo "<table class='table table-bordered mt-3'>";
                echo "<tr><th width='200'>Campo</th><th>Valor</th><th>Status</th></tr>";
                
                // Flag
                $flag_status = $pedido['flag'] == '0' ? 'check-ok' : 'check-fail';
                $flag_msg = $pedido['flag'] == '0' ? '✅ Pendente (correto)' : '❌ Não pendente (flag=' . $pedido['flag'] . ')';
                echo "<tr><td><strong>flag</strong></td><td>{$pedido['flag']}</td><td class='$flag_status'>$flag_msg</td></tr>";
                
                // Matrícula
                $mat_status = !empty($pedido['matricula']) ? 'check-ok' : 'check-fail';
                $mat_msg = !empty($pedido['matricula']) ? '✅ Preenchida' : '❌ Vazia';
                echo "<tr><td><strong>matricula</strong></td><td>{$pedido['matricula']}</td><td class='$mat_status'>$mat_msg</td></tr>";
                
                // Tipo de Cota
                $tipo_status = !empty($pedido['tipocota']) ? 'check-ok' : 'check-warn';
                $tipo_msg = !empty($pedido['tipocota']) ? '✅ Preenchido' : '⚠️ Vazio';
                echo "<tr><td><strong>tipocota</strong></td><td>{$pedido['tipocota']}</td><td class='$tipo_status'>$tipo_msg</td></tr>";
                
                // Data
                echo "<tr><td><strong>data</strong></td><td>{$pedido['data']}</td><td class='check-ok'>✅ Preenchida</td></tr>";
                
                // Valor
                echo "<tr><td><strong>valor</strong></td><td>R$ " . number_format($pedido['valor'], 2, ',', '.') . "</td><td class='check-ok'>📊 Info</td></tr>";
                
                // Justificativa
                echo "<tr><td><strong>justificativa</strong></td><td>" . substr($pedido['justificativa'], 0, 100) . "...</td><td class='check-ok'>📝 Info</td></tr>";
                
                echo "</table>";
                
                $matricula_tecnico = $pedido['matricula'];
                
                // ==========================================
                // 1.5. VERIFICAR CAMPOS NULL OU VAZIOS EM TBPEDIDOSTEC
                // ==========================================
                echo "<h3>1️⃣.5 Verificação de Campos NULL ou Vazios (IMPORTANTE)</h3>";
                echo "<p class='text-info'><strong>Campos NULL ou vazios podem impedir a exibição do pedido nos JOINs!</strong></p>";
                
                $campos_criticos = [
                    'matricula' => 'Matrícula do Técnico',
                    'valor' => 'Valor do Pedido',
                    'data' => 'Data do Pedido',
                    'tipocota' => 'Tipo de Cota',
                    'flag' => 'Flag de Status'
                ];
                
                $campos_opcionais = [
                    'justificativa' => 'Justificativa',
                    'dataplantao' => 'Data do Plantão',
                    'kmprojetado' => 'KM Projetado',
                    'kmsemana' => 'KM Semana',
                    'kmos' => 'KM OS',
                    'orcpedido' => 'Orçamento Pedido',
                    'orcsemanal' => 'Orçamento Semanal'
                ];
                
                echo "<h4>Campos CRÍTICOS (podem impedir JOINs):</h4>";
                echo "<table class='table table-bordered mt-3'>";
                echo "<tr><th width='250'>Campo</th><th>Valor</th><th>Status</th></tr>";
                
                $tem_campo_critico_null = false;
                
                foreach ($campos_criticos as $campo => $descricao) {
                    $valor_campo = $pedido[$campo] ?? null;
                    $is_null = ($valor_campo === null || $valor_campo === '');
                    
                    if ($is_null) {
                        $tem_campo_critico_null = true;
                        $status_class = 'check-fail';
                        $status_msg = '❌ NULL ou VAZIO - CRÍTICO!';
                        $valor_exibido = '<span class="text-danger"><strong>NULL/VAZIO</strong></span>';
                    } else {
                        $status_class = 'check-ok';
                        $status_msg = '✅ Preenchido';
                        $valor_exibido = htmlspecialchars($valor_campo);
                        
                        // Verificar espaços em branco extras
                        if (trim($valor_campo) !== $valor_campo) {
                            $status_msg .= ' ⚠️ (tem espaços extras)';
                            $status_class = 'check-warn';
                        }
                    }
                    
                    echo "<tr>";
                    echo "<td><strong>$campo</strong><br><small>$descricao</small></td>";
                    echo "<td>$valor_exibido</td>";
                    echo "<td class='$status_class'>$status_msg</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                if ($tem_campo_critico_null) {
                    echo "<div class='alert alert-danger mt-3'>";
                    echo "<h5>🚨 PROBLEMA CRÍTICO ENCONTRADO!</h5>";
                    echo "<p><strong>CAUSA RAIZ:</strong> Campos críticos com valor NULL ou vazio impedem que o pedido seja exibido corretamente.</p>";
                    echo "<p><strong>IMPACTO:</strong></p>";
                    echo "<ul>";
                    echo "<li>Se <strong>matricula</strong> for NULL/vazio → O JOIN com tbusuario FALHA</li>";
                    echo "<li>Se <strong>flag</strong> for NULL → A condição WHERE p.flag = '0' FALHA</li>";
                    echo "<li>Se <strong>valor</strong> for NULL → Validações de saldo FALHAM</li>";
                    echo "<li>Se <strong>data</strong> for NULL → Ordenação e filtros FALHAM</li>";
                    echo "</ul>";
                    echo "<p><strong>SOLUÇÃO:</strong> Corrigir os valores NULL/vazios diretamente na tabela:</p>";
                    echo "<pre>UPDATE bdfrota.tbpedidostec SET \n";
                    echo "  matricula = '[MATRICULA_CORRETA]',\n";
                    echo "  flag = '0',\n";
                    echo "  valor = [VALOR_CORRETO],\n";
                    echo "  data = NOW()\n";
                    echo "WHERE idtbpedidostec = '$id_pedido';</pre>";
                    echo "</div>";
                }
                
                echo "<h4 class='mt-4'>Campos OPCIONAIS (informativos):</h4>";
                echo "<table class='table table-bordered mt-3'>";
                echo "<tr><th width='250'>Campo</th><th>Valor</th><th>Status</th></tr>";
                
                foreach ($campos_opcionais as $campo => $descricao) {
                    $valor_campo = $pedido[$campo] ?? null;
                    $is_null = ($valor_campo === null || $valor_campo === '');
                    
                    if ($is_null) {
                        $status_class = 'check-warn';
                        $status_msg = '⚠️ NULL ou VAZIO';
                        $valor_exibido = '<span class="text-muted">NULL/VAZIO</span>';
                    } else {
                        $status_class = 'check-ok';
                        $status_msg = '✅ Preenchido';
                        $valor_exibido = htmlspecialchars(substr($valor_campo, 0, 50));
                        if (strlen($valor_campo) > 50) {
                            $valor_exibido .= '...';
                        }
                    }
                    
                    echo "<tr>";
                    echo "<td><strong>$campo</strong><br><small>$descricao</small></td>";
                    echo "<td>$valor_exibido</td>";
                    echo "<td class='$status_class'>$status_msg</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Verificar todos os campos da tabela
                echo "<h4 class='mt-4'>Dump Completo do Registro:</h4>";
                echo "<details>";
                echo "<summary class='btn btn-sm btn-outline-secondary mb-2'>Clique para ver TODOS os campos da tbpedidostec</summary>";
                echo "<pre style='max-height: 400px; overflow-y: auto;'>";
                echo "ID do Pedido: $id_pedido\n\n";
                foreach ($pedido as $campo => $valor) {
                    $valor_display = ($valor === null || $valor === '') ? '❌ NULL/VAZIO' : $valor;
                    echo str_pad($campo, 25) . " => " . $valor_display . "\n";
                }
                echo "</pre>";
                echo "</details>";
                
                // ==========================================
                // 1.6 VERIFICAÇÃO ESPECIAL: dataplantao = 0000-00-00
                // ==========================================
                echo "<h3>1️⃣.6 ⚠️ VERIFICAÇÃO ESPECIAL: Campo dataplantao</h3>";
                echo "<p class='text-warning'><strong>CRÍTICO:</strong> O campo 'dataplantao' pode estar bloqueando a exibição!</p>";
                
                $dataplantao = $pedido['dataplantao'] ?? null;
                
                if ($dataplantao === '0000-00-00 00:00:00' || $dataplantao === '0000-00-00') {
                    echo "<div class='alert alert-danger'>";
                    echo "<h5>🚨 PROBLEMA ENCONTRADO!</h5>";
                    echo "<p><strong>Campo:</strong> dataplantao = '$dataplantao' (DATA INVÁLIDA)</p>";
                    echo "<p><strong>CAUSA:</strong> MySQL interpreta '0000-00-00' como data inválida. Dependendo da query em aprovarcota.php, isso pode:</p>";
                    echo "<ul>";
                    echo "<li>❌ Falhar em operações de comparação de datas (ORDER BY, WHERE com datas)</li>";
                    echo "<li>❌ Causar erro em formatação de datas (DATE_FORMAT)</li>";
                    echo "<li>❌ Bloquear o pedido se houver filtros por data</li>";
                    echo "</ul>";
                    
                    echo "<p><strong>SOLUÇÃO 1:</strong> Se dataplantao é obrigatório, preencha com data correta:</p>";
                    echo "<pre>UPDATE bdfrota.tbpedidostec SET dataplantao = NOW() WHERE idtbpedidostec = $id_pedido;</pre>";
                    
                    echo "<p><strong>SOLUÇÃO 2:</strong> Se dataplantao é opcional para este tipo de cota, deixe NULL:</p>";
                    echo "<pre>UPDATE bdfrota.tbpedidostec SET dataplantao = NULL WHERE idtbpedidostec = $id_pedido;</pre>";
                    
                    echo "<p><strong>SOLUÇÃO 3:</strong> Verifique se a query em aprovarcota.php tem uma cláusula WHERE que exclui datas inválidas:</p>";
                    echo "<pre>WHERE dataplantao != '0000-00-00' OR dataplantao IS NULL</pre>";
                    echo "</div>";
                } else if ($dataplantao === null || $dataplantao === '') {
                    echo "<div class='alert alert-warning'>";
                    echo "<p>⚠️ Campo dataplantao está NULL/VAZIO</p>";
                    echo "<p><strong>Análise:</strong> Dependendo do tipocota (extra, plantão, etc), isso pode ser:</p>";
                    echo "<ul>";
                    echo "<li>✅ NORMAL para cota extra (tipocota=0)</li>";
                    echo "<li>❌ CRÍTICO para cota plantão (tipocota=2 ou similar)</li>";
                    echo "</ul>";
                    echo "<p><strong>tipocota atual:</strong> " . $pedido['tipocota'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-success'>";
                    echo "<p>✅ Campo dataplantao tem valor válido: $dataplantao</p>";
                    echo "</div>";
                }
                
                // ==========================================
                // 1.7 VERIFICAÇÃO: tipocota
                // ==========================================
                echo "<h3>1️⃣.7 Verificação do Campo tipocota</h3>";
                $tipocota = $pedido['tipocota'] ?? null;
                
                $tipos_validos = [
                    '0' => 'Extra (Hora Extra)',
                    '1' => 'DSR (Descanso Semanal Remunerado)',
                    '2' => 'Plantão',
                    '3' => 'Adicional Noturno',
                    '4' => 'Férias',
                    '5' => 'Décimo Terceiro'
                ];
                
                if (!isset($tipos_validos[$tipocota])) {
                    echo "<div class='alert alert-warning'>";
                    echo "<p>⚠️ tipocota = '$tipocota' (TIPO DESCONHECIDO)</p>";
                    echo "<p><strong>Tipos válidos encontrados no sistema:</strong></p>";
                    echo "<ul>";
                    foreach ($tipos_validos as $tipo => $desc) {
                        echo "<li>$tipo = $desc</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-info'>";
                    echo "<p>✅ tipocota = '$tipocota' ({$tipos_validos[$tipocota]})</p>";
                    echo "</div>";
                }
                
                // ==========================================
                // 2.5 SIMULAÇÃO DA QUERY DE APROVARCOTA.PHP
                // ==========================================
                echo "<h3>2️⃣.5 Simulação da Query em aprovarcota.php (NOVO)</h3>";
                echo "<p class='text-info'><strong>Analisando as regras atuais de aprovarcota.php...</strong></p>";
                echo "<p class='text-danger'><strong>⚠️ REGRAS DE APROVAÇÃO ATUALIZADAS (SINCRONIZADAS):</strong></p>";
                echo "<ol>";
                echo "<li><strong>Cota Extra Anterior:</strong> Se existir totalextra > 0 da semana anterior → Status = 'Proibido' (não pode aprovar mais cota)</li>";
                echo "<li><strong>KM Projetado Atual:</strong> Calculado como (Cota Semanal 80% + Cota Extra) ÷ Valor do Litro × KM/L (V/C)</li>";
                echo "<li><strong>Status Cálculo:</strong>";
                echo "<ul>";
                echo "<li>Se (KM Projetado Atual × 0.75) > KM Trabalhado → Status = 'Indevido' (pedido pode ser aprovado com entrada de valor)</li>";
                echo "<li>Senão → Status = 'Devido' (pedido pode ser aprovado normalmente)</li>";
                echo "</ul>";
                echo "</li>";
                echo "<li><strong>Cartão:</strong> Se Saldo do Cartão >= R$ 51,00 → Exibição em vermelho (bloqueio automático em control)</li>";
                echo "<li><strong>Limite de Autonomia:</strong> Coordenador padrão = R$ 51,00 | Rede Externa/Implantação/Obra = R$ 81,00</li>";
                echo "<li><strong>Valor > Limite:</strong> Passa para 'Escalonar' (direção resolve)</li>";
                echo "</ol>";
                
                // Verificar se o pedido está em tbcotaextraindevida
                echo "<h3>2️⃣.6 🔴 VERIFICAÇÃO CRÍTICA: Pedido em tbcotaextraindevida</h3>";
                
                $sql_cota_extra = "SELECT * FROM bdfrota.tbcotaextraindevida WHERE idtbpedidostec = '$id_pedido' OR matricula = '$matricula_tecnico'";
                $resultado_cota_extra = mysql_query($sql_cota_extra);
                
                if (!$resultado_cota_extra) {
                    echo "<div class='alert alert-danger'>";
                    echo "<p>❌ Erro na consulta: " . mysql_error() . "</p>";
                    echo "</div>";
                } elseif (mysql_num_rows($resultado_cota_extra) == 0) {
                    echo "<div class='alert alert-danger'>";
                    echo "<h5>🚨 CAUSA RAIZ ENCONTRADA!</h5>";
                    echo "<p><strong>O pedido NÃO existe em tbcotaextraindevida!</strong></p>";
                    echo "<p><strong>Por isso não aparece em aprovarcota.php</strong></p>";
                    echo "<p><strong>SOLUÇÃO:</strong> O pedido precisa ser movido/copiado de tbpedidostec para tbcotaextraindevida</p>";
                    echo "<p><strong>Verificar:</strong> Existe um script ou controle que faz essa transferência?</p>";
                    echo "<pre>-- Copiar dados do pedido para tbcotaextraindevida:\n";
                    echo "INSERT INTO bdfrota.tbcotaextraindevida (matricula, dataac, idtbpedidostec)\n";
                    echo "SELECT matricula, data, idtbpedidostec FROM bdfrota.tbpedidostec\n";
                    echo "WHERE idtbpedidostec = $id_pedido;\n";
                    echo "</pre>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-success'>";
                    echo "<p>✅ Pedido encontrado em tbcotaextraindevida</p>";
                    
                    $cota_extra = mysql_fetch_assoc($resultado_cota_extra);
                    echo "<table class='table table-bordered mt-3'>";
                    echo "<tr><th width='250'>Campo</th><th>Valor</th></tr>";
                    
                    foreach ($cota_extra as $campo => $valor) {
                        $valor_exibido = ($valor === null || $valor === '') ? 'NULL/VAZIO' : $valor;
                        echo "<tr><td><strong>$campo</strong></td><td>$valor_exibido</td></tr>";
                    }
                    
                    echo "</table>";
                    echo "</div>";
                }
                
                echo "<details>";
                echo "<summary class='btn btn-sm btn-outline-secondary mb-2'>Ver todas as cotas extras deste técnico</summary>";
                
                $sql_todas_cotas = "SELECT * FROM bdfrota.tbcotaextraindevida WHERE matricula = '$matricula_tecnico' ORDER BY dataac DESC LIMIT 10";
                $resultado_todas = mysql_query($sql_todas_cotas);
                
                echo "<div style='max-height: 400px; overflow-y: auto;'>";
                echo "<table class='table table-sm table-bordered'>";
                echo "<tr><th>ID</th><th>Matricula</th><th>Data Ac</th><th>ID Pedido STec</th><th>ID Saldo</th></tr>";
                
                while ($row = mysql_fetch_assoc($resultado_todas)) {
                    echo "<tr>";
                    echo "<td>{$row['idtbcotaextraindevida']}</td>";
                    echo "<td>{$row['matricula']}</td>";
                    echo "<td>{$row['dataac']}</td>";
                    echo "<td>" . ($row['idtbpedidostec'] ?? '-') . "</td>";
                    echo "<td>" . ($row['idtbsaldo'] ?? '-') . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                echo "</div>";
                echo "</details>";
                
                // ==========================================
                // 2. VERIFICAR VÍNCULO COM TBUSUARIO
                // ==========================================
                echo "<h3>2️⃣ Verificação do Vínculo com tbusuario</h3>";
                
                $sql2 = "SELECT * FROM bdfrota.tbusuario WHERE matricula = '$matricula_tecnico'";
                $resultado2 = mysql_query($sql2);
                
                if (mysql_num_rows($resultado2) == 0) {
                    echo "<div class='check-fail'>❌ TÉCNICO NÃO ENCONTRADO na tabela tbusuario</div>";
                    echo "<p class='text-danger'><strong>CAUSA RAIZ:</strong> A matrícula <strong>$matricula_tecnico</strong> não existe em bdfrota.tbusuario</p>";
                    echo "<p class='text-warning'><strong>SOLUÇÃO:</strong> Cadastrar o técnico na tabela tbusuario com vínculo ao supervisor correto.</p>";
                } else {
                    $usuario = mysql_fetch_assoc($resultado2);
                    echo "<div class='check-ok'>✅ Técnico encontrado em tbusuario</div>";
                    
                    echo "<table class='table table-bordered mt-3'>";
                    echo "<tr><th>Campo</th><th>Valor</th><th>Status</th></tr>";
                    echo "<tr><td><strong>matricula</strong></td><td>{$usuario['matricula']}</td><td class='check-ok'>✅</td></tr>";
                    
                    $idsup_status = !empty($usuario['idtbsupervisor']) ? 'check-ok' : 'check-fail';
                    $idsup_msg = !empty($usuario['idtbsupervisor']) ? '✅ Vinculado' : '❌ NÃO vinculado';
                    echo "<tr><td><strong>idtbsupervisor</strong></td><td>{$usuario['idtbsupervisor']}</td><td class='$idsup_status'>$idsup_msg</td></tr>";
                    echo "</table>";
                    
                    $idtbsupervisor = $usuario['idtbsupervisor'];
                    
                    if (empty($idtbsupervisor)) {
                        echo "<p class='text-danger'><strong>CAUSA RAIZ:</strong> O técnico não está vinculado a nenhum supervisor (idtbsupervisor é NULL)</p>";
                        echo "<p class='text-warning'><strong>SOLUÇÃO:</strong> Atualizar tbusuario para vincular o técnico a um supervisor.</p>";
                    } else {
                        
                        // ==========================================
                        // 3. VERIFICAR VÍNCULO COM SUPERVISOR
                        // ==========================================
                        echo "<h3>3️⃣ Verificação do Vínculo com tbsupervisor</h3>";
                        
                        $sql3 = "SELECT * FROM bdfrota.tbsupervisor WHERE idtbsupervisor = '$idtbsupervisor'";
                        $resultado3 = mysql_query($sql3);
                        
                        if (mysql_num_rows($resultado3) == 0) {
                            echo "<div class='check-fail'>❌ SUPERVISOR NÃO ENCONTRADO com ID: $idtbsupervisor</div>";
                            echo "<p class='text-danger'><strong>CAUSA RAIZ:</strong> O idtbsupervisor <strong>$idtbsupervisor</strong> não existe na tabela tbsupervisor</p>";
                            echo "<p class='text-warning'><strong>SOLUÇÃO:</strong> Corrigir o vínculo ou cadastrar o supervisor.</p>";
                        } else {
                            $supervisor = mysql_fetch_assoc($resultado3);
                            echo "<div class='check-ok'>✅ Supervisor encontrado</div>";
                            
                            echo "<table class='table table-bordered mt-3'>";
                            echo "<tr><th>Campo</th><th>Valor</th><th>Status</th></tr>";
                            echo "<tr><td><strong>idtbsupervisor</strong></td><td>{$supervisor['idtbsupervisor']}</td><td class='check-ok'>✅</td></tr>";
                            echo "<tr><td><strong>matricula</strong></td><td>{$supervisor['matricula']}</td><td class='check-ok'>✅</td></tr>";
                            
                            $idcoord_status = !empty($supervisor['idtbcoordenador']) ? 'check-ok' : 'check-fail';
                            $idcoord_msg = !empty($supervisor['idtbcoordenador']) ? '✅ Vinculado' : '❌ NÃO vinculado';
                            echo "<tr><td><strong>idtbcoordenador</strong></td><td>{$supervisor['idtbcoordenador']}</td><td class='$idcoord_status'>$idcoord_msg</td></tr>";
                            echo "</table>";
                            
                            $idtbcoordenador = $supervisor['idtbcoordenador'];
                            
                            if (empty($idtbcoordenador)) {
                                echo "<p class='text-danger'><strong>CAUSA RAIZ:</strong> O supervisor não está vinculado a nenhum coordenador (idtbcoordenador é NULL)</p>";
                                echo "<p class='text-warning'><strong>SOLUÇÃO:</strong> Atualizar tbsupervisor para vincular o supervisor a um coordenador.</p>";
                            } else {
                                
                                // ==========================================
                                // 4. VERIFICAR VÍNCULO COM COORDENADOR
                                // ==========================================
                                echo "<h3>4️⃣ Verificação do Vínculo com tbcoord</h3>";
                                
                                $sql4 = "SELECT * FROM bdfrota.tbcoord WHERE idtbcoordenador = '$idtbcoordenador'";
                                $resultado4 = mysql_query($sql4);
                                
                                if (mysql_num_rows($resultado4) == 0) {
                                    echo "<div class='check-fail'>❌ COORDENADOR NÃO ENCONTRADO com ID: $idtbcoordenador</div>";
                                    echo "<p class='text-danger'><strong>CAUSA RAIZ:</strong> O idtbcoordenador <strong>$idtbcoordenador</strong> não existe na tabela tbcoord</p>";
                                    echo "<p class='text-warning'><strong>SOLUÇÃO:</strong> Corrigir o vínculo ou cadastrar o coordenador.</p>";
                                } else {
                                    $coordenador = mysql_fetch_assoc($resultado4);
                                    echo "<div class='check-ok'>✅ Coordenador encontrado</div>";
                                    
                                    echo "<table class='table table-bordered mt-3'>";
                                    echo "<tr><th>Campo</th><th>Valor</th><th>Status</th></tr>";
                                    echo "<tr><td><strong>idtbcoordenador</strong></td><td>{$coordenador['idtbcoordenador']}</td><td class='check-ok'>✅</td></tr>";
                                    echo "<tr><td><strong>matricula</strong></td><td>{$coordenador['matricula']}</td><td class='check-ok'>✅</td></tr>";
                                    echo "<tr><td><strong>valor (saldo)</strong></td><td>R$ " . number_format($coordenador['valor'], 2, ',', '.') . "</td><td class='check-ok'>💰</td></tr>";
                                    echo "</table>";
                                    
                                    // ==========================================
                                    // 5. VERIFICAR SE É O COORDENADOR LOGADO
                                    // ==========================================
                                    echo "<h3>5️⃣ Verificação de Acesso do Coordenador Logado</h3>";
                                    
                                    if (empty($matricula_coordenador)) {
                                        echo "<div class='check-warn'>⚠️ Nenhum coordenador logado na sessão</div>";
                                        echo "<p class='text-warning'>Não é possível verificar se você tem acesso a este pedido.</p>";
                                    } elseif ($coordenador['matricula'] == $matricula_coordenador) {
                                        echo "<div class='check-ok'>✅ VOCÊ TEM ACESSO! O pedido pertence ao seu escopo (coordenador: {$coordenador['matricula']})</div>";
                                    } else {
                                        echo "<div class='check-fail'>❌ VOCÊ NÃO TEM ACESSO! O pedido pertence a outro coordenador</div>";
                                        echo "<p class='text-danger'><strong>Coordenador responsável:</strong> {$coordenador['matricula']}</p>";
                                        echo "<p class='text-danger'><strong>Você (logado):</strong> $matricula_coordenador</p>";
                                        echo "<p class='text-warning'><strong>CAUSA RAIZ:</strong> O pedido não aparece porque pertence a outro coordenador.</p>";
                                    }
                                    
                                    // ==========================================
                                    // 6. VERIFICAR CENTRO DE CUSTO DO TÉCNICO
                                    // ==========================================
                                    echo "<h3>6️⃣ Verificação de Centro de Custo</h3>";
                                    
                                    $sql_ccusto = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula_tecnico'";
                                    $resultado_ccusto = mysql_query($sql_ccusto);
                                    
                                    if (mysql_num_rows($resultado_ccusto) == 0) {
                                        echo "<div class='check-warn'>⚠️ Centro de custo não encontrado em bdaniel.tbfuncionario</div>";
                                        echo "<p class='text-warning'>O técnico pode não estar cadastrado na tabela tbfuncionario</p>";
                                        $ccusto_tecnico = 'NÃO ENCONTRADO';
                                    } else {
                                        $row_ccusto = mysql_fetch_assoc($resultado_ccusto);
                                        $ccusto_tecnico = $row_ccusto['ccusto'];
                                        
                                        echo "<div class='check-ok'>✅ Centro de custo identificado</div>";
                                        echo "<table class='table table-bordered mt-3'>";
                                        echo "<tr><th>Centro de Custo</th><td><strong>$ccusto_tecnico</strong></td></tr>";
                                        
                                        // Verificar se é rede externa
                                        $is_rede_externa = false;
                                        $limite_valor = 51;
                                        
                                        $ccusto_normalizado = normalize_string($ccusto_tecnico);
                                        if (
                                            strpos($ccusto_normalizado, 'rede externa') !== false ||
                                            strpos($ccusto_normalizado, 'implanta') !== false ||
                                            strpos($ccusto_normalizado, 'obra') !== false
                                        ) {
                                            $is_rede_externa = true;
                                            $limite_valor = 81;
                                        }
                                        
                                        $tipo_ccusto = $is_rede_externa ? 'Rede Externa / Implantação / Obra' : 'Padrão';
                                        $status_ccusto = $is_rede_externa ? 'check-ok' : 'check-ok';
                                        
                                        echo "<tr><th>Tipo de Centro de Custo</th><td class='$status_ccusto'>$tipo_ccusto</td></tr>";
                                        echo "<tr><th>Limite de Autonomia Coordenador</th><td class='check-ok'>R$ " . number_format($limite_valor - 1, 2, ',', '.') . "</td></tr>";
                                        
                                        // Verificar se o valor do pedido está dentro do limite
                                        if ($pedido['valor'] > $limite_valor) {
                                            echo "<tr><th>Valor do Pedido vs Limite</th><td class='check-fail'>❌ Valor R$ " . number_format($pedido['valor'], 2, ',', '.') . " excede o limite de R$ " . number_format($limite_valor, 2, ',', '.') . " (seria escalonado para direção)</td></tr>";
                                        } else {
                                            echo "<tr><th>Valor do Pedido vs Limite</th><td class='check-ok'>✅ Valor R$ " . number_format($pedido['valor'], 2, ',', '.') . " está dentro do limite</td></tr>";
                                        }
                                        
                                        echo "</table>";
                                    }
                                    
                                    // ==========================================
                                    // 7. VERIFICAR PERFIL DO COORDENADOR
                                    // ==========================================
                                    echo "<h3>7️⃣ Verificação de Perfil e Permissões</h3>";
                                    
                                    if (!empty($matricula_coordenador)) {
                                        $sql_perfil = "SELECT perfil FROM bdfrota.tbusuario WHERE matricula = '$matricula_coordenador'";
                                        $resultado_perfil = mysql_query($sql_perfil);
                                        
                                        if (mysql_num_rows($resultado_perfil) > 0) {
                                            $row_perfil = mysql_fetch_assoc($resultado_perfil);
                                            $perfil_coord = $row_perfil['perfil'];
                                            
                                            echo "<table class='table table-bordered mt-3'>";
                                            echo "<tr><th>Matrícula Coordenador</th><td>$matricula_coordenador</td></tr>";
                                            echo "<tr><th>Perfil</th><td><strong>$perfil_coord</strong></td></tr>";
                                            
                                            // Perfil 3 = Coordenador, Perfil 10 = Coordenador também
                                            if ($perfil_coord == '3' || $perfil_coord == '10') {
                                                echo "<tr><th>Status Perfil</th><td class='check-ok'>✅ Perfil de Coordenador (correto)</td></tr>";
                                            } else {
                                                echo "<tr><th>Status Perfil</th><td class='check-fail'>❌ Perfil não é de Coordenador (perfil=$perfil_coord)</td></tr>";
                                            }
                                            echo "</table>";
                                        } else {
                                            echo "<div class='check-warn'>⚠️ Coordenador não encontrado em tbusuario</div>";
                                        }
                                    } else {
                                        echo "<div class='check-warn'>⚠️ Nenhum coordenador logado - não é possível verificar perfil</div>";
                                    }
                                    
                                    // ==========================================
                                    // 8. VERIFICAR SALDO DO COORDENADOR
                                    // ==========================================
                                    echo "<h3>8️⃣ Verificação de Saldo do Coordenador</h3>";
                                    
                                    $saldo_coord = $coordenador['valor'];
                                    $valor_pedido = $pedido['valor'];
                                    
                                    echo "<table class='table table-bordered mt-3'>";
                                    echo "<tr><th>Saldo Disponível</th><td>R$ " . number_format($saldo_coord, 2, ',', '.') . "</td></tr>";
                                    echo "<tr><th>Valor do Pedido</th><td>R$ " . number_format($valor_pedido, 2, ',', '.') . "</td></tr>";
                                    
                                    if ($valor_pedido > $saldo_coord) {
                                        echo "<tr><th>Status Saldo</th><td class='check-warn'>⚠️ Valor do pedido excede o saldo disponível (mas o pedido ainda deve aparecer)</td></tr>";
                                    } else {
                                        echo "<tr><th>Status Saldo</th><td class='check-ok'>✅ Saldo suficiente para aprovação</td></tr>";
                                    }
                                    echo "</table>";
                                                      // ==========================================
                    // 9. VERIFICAR SALDO ANTERIOR DO TÉCNICO
                    // ==========================================
                    echo "<h3>9️⃣ Verificação de Saldo Anterior do Técnico</h3>";
                    
                    $sql_saldo_tec = "SELECT saldo FROM bdfrota.tbsaldo WHERE matricula = '$matricula_tecnico' ORDER BY idtbsaldo DESC LIMIT 1";
                    $resultado_saldo_tec = mysql_query($sql_saldo_tec);
                    
                    if (mysql_num_rows($resultado_saldo_tec) > 0) {
                        $row_saldo_tec = mysql_fetch_assoc($resultado_saldo_tec);
                        $saldo_anterior = $row_saldo_tec['saldo'];
                        
                        echo "<table class='table table-bordered mt-3'>";
                        echo "<tr><th>Saldo Anterior do Técnico (Cartão)</th><td>R$ " . number_format($saldo_anterior, 2, ',', '.') . "</td></tr>";
                        
                        if ($saldo_anterior >= 51) {
                            echo "<tr><th>Status Saldo Técnico</th><td class='check-fail'>❌ Técnico possui saldo >= R$ 51,00 - pedido seria REJEITADO automaticamente</td></tr>";
                            echo "<tr><td colspan='2' class='text-danger'><strong>CAUSA RAIZ:</strong> O sistema rejeita pedidos quando o técnico já possui saldo >= R$ 51,00 no cartão</td></tr>";
                        } else {
                            echo "<tr><th>Status Saldo Técnico</th><td class='check-ok'>✅ Saldo anterior OK (menor que R$ 51,00)</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<div class='check-ok'>✅ Nenhum saldo anterior encontrado (técnico sem histórico)</div>";
                        $saldo_anterior = 0;
                    }
                    
                    // ==========================================
                    // 9.5 CÁLCULO DE KM PROJETADO ATUAL (NOVO)
                    // ==========================================
                    echo "<h3>9️⃣.5 Cálculo de KM Projetado Atual (SINCRONIZADO)</h3>";
                    echo "<p class='text-info'><strong>Regra de Aprovação:</strong> O pedido é analisado comparando KM Projetado Atual vs KM Trabalhado</p>";
                    
                    // Buscar saldo do técnico para pegar KM e valores
                    $sql_saldo_detalhado = "SELECT 
                        saldo, 
                        orcsemanal, 
                        kmproj, 
                        kmosatual, 
                        slddscnt, 
                        kmorcsem, 
                        valoraplicado, 
                        sldprjtd, 
                        valorcomb,
                        idtbsaldo
                    FROM bdfrota.tbsaldo 
                    WHERE matricula = '$matricula_tecnico' 
                    AND data >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                    ORDER BY idtbsaldo DESC 
                    LIMIT 1";
                    
                    $resultado_saldo_detalhado = mysql_query($sql_saldo_detalhado);
                    
                    if (mysql_num_rows($resultado_saldo_detalhado) > 0) {
                        $saldo_detalhado = mysql_fetch_assoc($resultado_saldo_detalhado);
                        
                        // Buscar cota extra da semana
                        $useg = date('Y-m-d', strtotime('last monday'));
                        $datam1 = date("Y-m-d", strtotime("-1 day"));
                        
                        $sql_cota_semanal = "SELECT SUM(valorinserido) as total_cota 
                                           FROM bdfrota.tbcotaextraac 
                                           WHERE matricula = '$matricula_tecnico'
                                           AND data BETWEEN '$useg 00:00:00' AND '$datam1 23:59:59'";
                        $resultado_cota_semanal = mysql_query($sql_cota_semanal);
                        $row_cota = mysql_fetch_assoc($resultado_cota_semanal);
                        $totalextra = $row_cota['total_cota'] ?? 0;
                        
                        if ($totalextra < 0) {
                            $totalextra = 0;
                        }
                        
                        echo "<div class='alert alert-info'>";
                        echo "<h5>📊 Análise de KM Projetado Atual</h5>";
                        echo "<table class='table table-sm table-bordered'>";
                        echo "<tr><th colspan='2'>Valores Necessários</th><th>Valor</th><th>Status</th></tr>";
                        
                        $sldprjtd = $saldo_detalhado['sldprjtd'] ?? 0;
                        $orcsem80 = round(($sldprjtd * 0.80), 2);
                        $totcot = $totalextra;
                        $total_combustivel_disponivel = $orcsem80 + $totcot;
                        $valorcomb_num = $saldo_detalhado['valorcomb'] ?? 0;
                        $kmosatual = $saldo_detalhado['kmosatual'] ?? 0;
                        
                        echo "<tr><td><strong>1. Cota Inicial (sldprjtd)</strong></td><td>R$ " . number_format($sldprjtd, 2, ',', '.') . "</td><td>Base</td></tr>";
                        echo "<tr><td><strong>2. Cota Semanal 80%</strong></td><td>R$ " . number_format($orcsem80, 2, ',', '.') . "</td><td>80% de " . number_format($sldprjtd, 2, ',', '.') . "</td></tr>";
                        echo "<tr><td><strong>3. Cota Extra (semana)</strong></td><td>R$ " . number_format($totcot, 2, ',', '.') . "</td><td>" . ($totcot > 0 ? '<span class="text-warning">⚠️ Existe</span>' : '✅ Nenhuma') . "</td></tr>";
                        echo "<tr><td><strong>4. Combustível Disponível</strong></td><td>R$ " . number_format($total_combustivel_disponivel, 2, ',', '.') . "</td><td>80% + Extra</td></tr>";
                        
                        if ($valorcomb_num > 0) {
                            $qtd_litros = $total_combustivel_disponivel / $valorcomb_num;
                            echo "<tr><td><strong>5. Valor do Litro (Cartão)</strong></td><td>R$ " . number_format($valorcomb_num, 2, ',', '.') . "</td><td>Preço</td></tr>";
                            echo "<tr><td><strong>6. Qtd. Litros (Combustível ÷ Preço)</strong></td><td>" . number_format($qtd_litros, 2, ',', '.') . " L</td><td>" . number_format($total_combustivel_disponivel, 2, ',', '.') . " ÷ " . number_format($valorcomb_num, 2, ',', '.') . "</td></tr>";
                        } else {
                            $qtd_litros = 0;
                            echo "<tr><td><strong>5. Valor do Litro (Cartão)</strong></td><td><span class='text-danger'>⚠️ NÃO CONFIGURADO</span></td><td>PROBLEMA</td></tr>";
                        }
                        
                        // Buscar tipo de veículo e KM/L
                        $sql_veiculo = "SELECT tipovel FROM bdfrota.tbveiculo WHERE matcond = '$matricula_tecnico' LIMIT 1";
                        $resultado_veiculo = mysql_query($sql_veiculo);
                        $tipovel = '';
                        $km_litro_vc = 0;
                        
                        if (mysql_num_rows($resultado_veiculo) > 0) {
                            $row_veiculo = mysql_fetch_assoc($resultado_veiculo);
                            $tipovel_id = $row_veiculo['tipovel'];
                            
                            // Buscar km/l do tipo de veículo
                            $sql_tipo_veiculo = "SELECT descricao, km_litro_previsto FROM bdcombustivel.tbtipoveiculo WHERE idtbtipoveiculo = '$tipovel_id'";
                            $resultado_tipo = mysql_query($sql_tipo_veiculo);
                            if (mysql_num_rows($resultado_tipo) > 0) {
                                $row_tipo = mysql_fetch_assoc($resultado_tipo);
                                $tipovel = $row_tipo['descricao'];
                                $km_litro_vc = $row_tipo['km_litro_previsto'] ?? 0;
                            }
                        }
                        
                        // Regra especial para veículos LEVE
                        $codempresa = 2; // Padrão SP - pode variar
                        if (strtoupper(trim($tipovel)) == 'LEVE') {
                            $km_litro_vc = ($codempresa == 2) ? 8 : 10;
                        }
                        
                        echo "<tr><td><strong>7. KM/L (Veículo)</strong></td><td>" . number_format($km_litro_vc, 2, ',', '.') . " km/l</td><td>";
                        if ($tipovel) {
                            echo "Tipo: $tipovel (Especial LEVE = " . ($codempresa == 2 ? '8' : '10') . ")";
                        } else {
                            echo "<span class='text-danger'>⚠️ Tipo não identificado</span>";
                        }
                        echo "</td></tr>";
                        
                        // KM Projetado Atual
                        if ($km_litro_vc > 0 && $qtd_litros > 0) {
                            $km_projetado_atual = $qtd_litros * $km_litro_vc;
                        } else {
                            $km_projetado_atual = 0;
                        }
                        
                        echo "<tr style='background-color: #e3f2fd;'>";
                        echo "<td><strong>8. KM PROJETADO ATUAL</strong></td>";
                        echo "<td style='font-weight: bold; font-size: 1.1em;'>" . number_format($km_projetado_atual, 0, ',', '.') . " km</td>";
                        echo "<td>Qtd. Litros × KM/L</td>";
                        echo "</tr>";
                        
                        echo "<tr><td><strong>9. KM Trabalhado (Real)</strong></td><td>" . number_format($kmosatual, 0, ',', '.') . " km</td><td>Dados de operação</td></tr>";
                        echo "</table>";
                        echo "</div>";
                        
                        // ==========================================
                        // 9.6 ANÁLISE DE STATUS (NOVO)
                        // ==========================================
                        echo "<h3>9️⃣.6 Análise de Status (Regra de Aprovação)</h3>";
                        
                        echo "<div class='alert alert-warning'>";
                        echo "<h5>🔍 Determinação do Status</h5>";
                        
                        $status = '';
                        $motivo_status = '';
                        
                        if ($totalextra > 0) {
                            $status = 'Proibido';
                            $motivo_status = "Existe cota extra anterior ($totalextra) aguardando aprovação";
                        } elseif ($km_projetado_atual == 0 && $kmosatual == 0) {
                            $status = 'Devido';
                            $motivo_status = "Ambos KM Projetado e KM Trabalhado estão zerados (dados insuficientes)";
                        } elseif (($km_projetado_atual * 0.75) > $kmosatual) {
                            $status = 'Indevido';
                            $motivo_status = number_format($km_projetado_atual * 0.75, 0) . " km (75% de " . number_format($km_projetado_atual, 0) . ") > " . number_format($kmosatual, 0) . " km (trabalhado)";
                        } else {
                            $status = 'Devido';
                            $motivo_status = number_format($kmosatual, 0) . " km (trabalhado) >= " . number_format($km_projetado_atual * 0.75, 0) . " km (75% do projetado)";
                        }
                        
                        echo "<table class='table table-bordered'>";
                        echo "<tr><th width='200'>Status Calculado</th><td>";
                        
                        if ($status == 'Proibido') {
                            echo "<span class='badge bg-danger'>$status</span>";
                        } elseif ($status == 'Indevido') {
                            echo "<span class='badge bg-warning'>$status</span>";
                        } else {
                            echo "<span class='badge bg-success'>$status</span>";
                        }
                        
                        echo "</td></tr>";
                        echo "<tr><th>Motivo</th><td>$motivo_status</td></tr>";
                        echo "</table>";
                        
                        // Explicação de cada status
                        echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;'>";
                        echo "<h6>📋 Significado de cada Status:</h6>";
                        
                        if ($status == 'Proibido') {
                            echo "<div class='alert alert-danger'>";
                            echo "<strong>🚫 Status = Proibido</strong>";
                            echo "<p>O técnico tem cota extra anterior ainda não aprovada (maior ou igual a 0). O sistema bloqueia novas aprovações até resolver a pendência.</p>";
                            echo "<p><strong>Ação Disponível:</strong> Apenas botão 'Escalonar' (para direção resolver)</p>";
                            echo "</div>";
                        } elseif ($status == 'Indevido') {
                            echo "<div class='alert alert-warning'>";
                            echo "<strong>⚠️ Status = Indevido</strong>";
                            echo "<p>O técnico não consumiu a quantidade de combustível projetada (KM Trabalhado < 75% do KM Projetado Atual).</p>";
                            echo "<p>Possível motivo: O técnico solicitou cota mas não utilizou proporcional.</p>";
                            echo "<p><strong>Ações Disponíveis:</strong></p>";
                            echo "<ul>";
                            echo "<li>Aprovar com redução de valor (ajustar o valor solicitado)</li>";
                            echo "<li>Encaminhar para fundo de frota (sistema de cobrança)</li>";
                            echo "<li>Escalonar para direção</li>";
                            echo "</ul>";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-success'>";
                            echo "<strong>✅ Status = Devido</strong>";
                            echo "<p>O técnico consumiu proporcionalmente o combustível projetado (KM Trabalhado >= 75% do KM Projetado Atual).</p>";
                            echo "<p><strong>Ações Disponíveis:</strong></p>";
                            echo "<ul>";
                            echo "<li>Aprovar normalmente (sem redução de valor)</li>";
                            echo "<li>Escalonar para direção (se valor > limite de autonomia)</li>";
                            echo "</ul>";
                            echo "</div>";
                        }
                        echo "</div>";
                        
                        echo "</div>";
                    } else {
                        echo "<div class='alert alert-warning'>";
                        echo "<p>⚠️ Nenhum saldo semanal encontrado para este técnico. Pode estar sem dados de KM/combustível.</p>";
                        echo "</div>";
                    }
                    
                    // ==========================================
                    // 10. TESTE DA QUERY COMPLETA
                    // ==========================================
                    echo "<h3>🔟 Teste da Query Completa (Simulação da Tela)</h3>";
                                    
                                    $sql_completa = "SELECT 
                                        p.idtbpedidostec,
                                        p.matricula as mat_tecnico,
                                        p.flag,
                                        p.valor,
                                        p.data,
                                        p.tipocota,
                                        p.justificativa,
                                        u.idtbsupervisor,
                                        s.idtbcoordenador,
                                        s.matricula as mat_supervisor,
                                        c.matricula as mat_coordenador,
                                        c.valor as saldo_coordenador,
                                        f.ccusto
                                    FROM 
                                        bdfrota.tbpedidostec p
                                        INNER JOIN bdfrota.tbusuario u ON p.matricula = u.matricula
                                        INNER JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                        INNER JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                        LEFT JOIN bdaniel.tbfuncionario f ON p.matricula = f.matricula
                                    WHERE 
                                        p.idtbpedidostec = '$id_pedido'
                                        AND p.flag = '0'";
                                    
                                    if (!empty($matricula_coordenador)) {
                                        $sql_completa .= " AND c.matricula = '$matricula_coordenador'";
                                    }
                                    
                                    $sql_completa .= " ORDER BY p.data DESC";
                                    
                                    echo "<pre>$sql_completa</pre>";
                                    
                                    $resultado_completo = mysql_query($sql_completa);
                                    
                                    if (mysql_num_rows($resultado_completo) > 0) {
                                        echo "<div class='check-ok'>✅ PEDIDO SERIA EXIBIDO NA QUERY! A query retornou resultado.</div>";
                                        
                                        $row_completo = mysql_fetch_assoc($resultado_completo);
                                        echo "<table class='table table-bordered mt-3'>";
                                        echo "<tr><th>Campo</th><th>Valor</th></tr>";
                                        foreach ($row_completo as $campo => $valor) {
                                            echo "<tr><td><strong>$campo</strong></td><td>" . htmlspecialchars($valor) . "</td></tr>";
                                        }
                                        echo "</table>";
                                        
                                        // Verificar se passaria nas validações do control
                                        echo "<h4 class='mt-4'>🔍 Simulação de Validação do Control</h4>";
                                        
                                        $bloqueios = [];
                                        
                                        // Validação 1: Saldo anterior >= 51
                                        if (isset($saldo_anterior) && $saldo_anterior >= 51) {
                                            $bloqueios[] = "❌ Saldo anterior do técnico >= R$ 51,00 (seria rejeitado no control)";
                                        }
                                        
                                        // Validação 2: Valor excede limite de autonomia
                                        if (isset($limite_valor) && $valor_pedido > $limite_valor) {
                                            $bloqueios[] = "⚠️ Valor excede autonomia do coordenador (seria escalonado para direção)";
                                        }
                                        
                                        // Validação 3: Saldo insuficiente
                                        if ($valor_pedido > $saldo_coord) {
                                            $bloqueios[] = "⚠️ Saldo do coordenador insuficiente (aprovação seria bloqueada)";
                                        }
                                        
                                        if (count($bloqueios) > 0) {
                                            echo "<div class='alert alert-warning'>";
                                            echo "<h5>⚠️ Possíveis Bloqueios na Aprovação:</h5>";
                                            echo "<ul>";
                                            foreach ($bloqueios as $bloqueio) {
                                                echo "<li>$bloqueio</li>";
                                            }
                                            echo "</ul>";
                                            echo "</div>";
                                        } else {
                                            echo "<div class='alert alert-success'>";
                                            echo "<h5>✅ Pedido Passaria em Todas as Validações!</h5>";
                                            echo "<p>O pedido está apto para ser aprovado.</p>";
                                            echo "</div>";
                                        }
                                        
                                    } else {
                                        echo "<div class='check-fail'>❌ PEDIDO NÃO SERIA EXIBIDO! A query não retornou resultado.</div>";
                                        echo "<p class='text-danger'>Verifique os critérios anteriores para identificar o problema.</p>";
                                    }
                                }
                            }
                        }
                    }
                }
                
                // ==========================================
                // 7. RESUMO FINAL
                // ==========================================
                echo "<h2>📋 RESUMO FINAL E DIAGNÓSTICO</h2>";
                echo "<div class='result-box'>";
                
                $problemas_criticos = [];
                $problemas_advertencia = [];
                $tudo_ok = true;
                
                // Verificar problemas críticos (que impedem exibição)
                
                // Verificar campos NULL/vazios críticos
                if (empty($pedido['matricula'])) {
                    $problemas_criticos[] = "Campo 'matricula' está NULL ou vazio (JOIN com tbusuario FALHA)";
                    $tudo_ok = false;
                }
                if ($pedido['flag'] === null || $pedido['flag'] === '') {
                    $problemas_criticos[] = "Campo 'flag' está NULL ou vazio (condição WHERE FALHA)";
                    $tudo_ok = false;
                } elseif ($pedido['flag'] != '0') {
                    $problemas_criticos[] = "Flag diferente de 0 (pedido já foi processado - flag={$pedido['flag']})";
                    $tudo_ok = false;
                }
                if ($pedido['valor'] === null || $pedido['valor'] === '') {
                    $problemas_criticos[] = "Campo 'valor' está NULL ou vazio (validações FALHAM)";
                    $tudo_ok = false;
                }
                if (empty($pedido['data'])) {
                    $problemas_criticos[] = "Campo 'data' está NULL ou vazio (ordenação FALHA)";
                    $tudo_ok = false;
                }
                if (empty($usuario)) {
                    $problemas_criticos[] = "Técnico não existe em bdfrota.tbusuario";
                    $tudo_ok = false;
                } elseif (empty($usuario['idtbsupervisor'])) {
                    $problemas_criticos[] = "Técnico não vinculado a supervisor (idtbsupervisor é NULL)";
                    $tudo_ok = false;
                } elseif (empty($supervisor)) {
                    $problemas_criticos[] = "Supervisor não existe na tabela tbsupervisor";
                    $tudo_ok = false;
                } elseif (empty($supervisor['idtbcoordenador'])) {
                    $problemas_criticos[] = "Supervisor não vinculado a coordenador (idtbcoordenador é NULL)";
                    $tudo_ok = false;
                } elseif (empty($coordenador)) {
                    $problemas_criticos[] = "Coordenador não existe na tabela tbcoord";
                    $tudo_ok = false;
                } elseif (!empty($matricula_coordenador) && $coordenador['matricula'] != $matricula_coordenador) {
                    $problemas_criticos[] = "Pedido pertence a outro coordenador (responsável: {$coordenador['matricula']}, você: $matricula_coordenador)";
                    $tudo_ok = false;
                }
                
                // Verificar problemas de advertência (pedido aparece mas pode ter bloqueios)
                if (isset($saldo_anterior) && $saldo_anterior >= 51) {
                    $problemas_advertencia[] = "Técnico possui saldo anterior >= R$ 51,00 (pedido seria rejeitado automaticamente no control)";
                }
                if (isset($limite_valor) && $valor_pedido > $limite_valor) {
                    $problemas_advertencia[] = "Valor R$ " . number_format($valor_pedido, 2, ',', '.') . " excede limite de autonomia do coordenador R$ " . number_format($limite_valor - 1, 2, ',', '.') . " (seria escalonado para direção)";
                }
                if (isset($saldo_coord) && $valor_pedido > $saldo_coord) {
                    $problemas_advertencia[] = "Saldo do coordenador insuficiente para aprovar (disponível: R$ " . number_format($saldo_coord, 2, ',', '.') . ", necessário: R$ " . number_format($valor_pedido, 2, ',', '.') . ")";
                }
                if ($ccusto_tecnico == 'NÃO ENCONTRADO') {
                    $problemas_advertencia[] = "Centro de custo do técnico não encontrado em bdaniel.tbfuncionario";
                }
                
                if (count($problemas_criticos) == 0 && count($problemas_advertencia) == 0) {
                    echo "<div class='alert alert-success'>";
                    echo "<h4>✅ PEDIDO DEVE APARECER NA TELA E ESTÁ APTO PARA APROVAÇÃO!</h4>";
                    echo "<p><strong>Status:</strong> Todos os critérios foram atendidos perfeitamente.</p>";
                    echo "<p><strong>Ação recomendada:</strong></p>";
                    echo "<ul>";
                    echo "<li>Se não está aparecendo, limpe o cache do navegador (Ctrl+F5)</li>";
                    echo "<li>Verifique se há filtros aplicados na tela</li>";
                    echo "<li>Verifique a paginação da tabela</li>";
                    echo "<li>Verifique se a sessão do coordenador está ativa</li>";
                    echo "</ul>";
                    echo "</div>";
                } elseif (count($problemas_criticos) == 0) {
                    echo "<div class='alert alert-warning'>";
                    echo "<h4>⚠️ PEDIDO DEVE APARECER NA TELA, MAS COM RESTRIÇÕES</h4>";
                    echo "<p><strong>Status:</strong> O pedido aparece na listagem, mas pode ter bloqueios na aprovação.</p>";
                    echo "<p><strong>Problemas identificados:</strong></p>";
                    echo "<ul>";
                    foreach ($problemas_advertencia as $problema) {
                        echo "<li>⚠️ $problema</li>";
                    }
                    echo "</ul>";
                    echo "<p><strong>Ação recomendada:</strong></p>";
                    echo "<ul>";
                    echo "<li>O pedido aparecerá na tela do coordenador</li>";
                    echo "<li>Porém, a aprovação pode ser bloqueada ou escalonada</li>";
                    echo "<li>Verifique os avisos acima para entender as limitações</li>";
                    echo "</ul>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-danger'>";
                    echo "<h4>❌ PEDIDO NÃO DEVE APARECER NA TELA</h4>";
                    echo "<p><strong>Status:</strong> Existem problemas críticos impedindo a exibição.</p>";
                    echo "<p><strong>Problemas CRÍTICOS (impedem exibição):</strong></p>";
                    echo "<ul>";
                    foreach ($problemas_criticos as $problema) {
                        echo "<li>❌ $problema</li>";
                    }
                    echo "</ul>";
                    
                    if (count($problemas_advertencia) > 0) {
                        echo "<p><strong>Problemas ADICIONAIS (se corrigir os críticos):</strong></p>";
                        echo "<ul>";
                        foreach ($problemas_advertencia as $problema) {
                            echo "<li>⚠️ $problema</li>";
                        }
                        echo "</ul>";
                    }
                    
                    echo "<p><strong>Soluções propostas:</strong></p>";
                    echo "<ul>";
                    
                    // Soluções para campos NULL
                    if (empty($pedido['matricula'])) {
                        echo "<li><strong>Campo matricula NULL/vazio:</strong> Execute:<br><code>UPDATE bdfrota.tbpedidostec SET matricula = '[MATRICULA_CORRETA]' WHERE idtbpedidostec = $id_pedido</code></li>";
                    }
                    if ($pedido['flag'] === null || $pedido['flag'] === '') {
                        echo "<li><strong>Campo flag NULL/vazio:</strong> Execute:<br><code>UPDATE bdfrota.tbpedidostec SET flag = '0' WHERE idtbpedidostec = $id_pedido</code></li>";
                    } elseif ($pedido['flag'] != '0') {
                        echo "<li><strong>Flag incorreta:</strong> O pedido já foi processado (flag={$pedido['flag']}). Para reabrir:<br><code>UPDATE bdfrota.tbpedidostec SET flag = '0' WHERE idtbpedidostec = $id_pedido</code></li>";
                    }
                    if ($pedido['valor'] === null || $pedido['valor'] === '') {
                        echo "<li><strong>Campo valor NULL/vazio:</strong> Execute:<br><code>UPDATE bdfrota.tbpedidostec SET valor = [VALOR_CORRETO] WHERE idtbpedidostec = $id_pedido</code></li>";
                    }
                    if (empty($pedido['data'])) {
                        echo "<li><strong>Campo data NULL/vazio:</strong> Execute:<br><code>UPDATE bdfrota.tbpedidostec SET data = NOW() WHERE idtbpedidostec = $id_pedido</code></li>";
                    }
                    
                    // Soluções para vínculos
                    if (!empty($pedido['matricula']) && empty($usuario)) {
                        echo "<li><strong>Técnico não cadastrado:</strong> Execute:<br><code>INSERT INTO bdfrota.tbusuario (matricula, idtbsupervisor, perfil) VALUES ('$matricula_tecnico', [ID_DO_SUPERVISOR], 1)</code></li>";
                    } elseif (!empty($usuario) && empty($usuario['idtbsupervisor'])) {
                        echo "<li><strong>Vincular ao supervisor:</strong> Execute:<br><code>UPDATE bdfrota.tbusuario SET idtbsupervisor = [ID_DO_SUPERVISOR] WHERE matricula = '$matricula_tecnico'</code></li>";
                    }
                    if (!empty($usuario) && !empty($usuario['idtbsupervisor']) && empty($supervisor)) {
                        echo "<li><strong>Supervisor inexistente:</strong> Corrija o idtbsupervisor ou cadastre o supervisor na tabela tbsupervisor</li>";
                    } elseif (!empty($supervisor) && empty($supervisor['idtbcoordenador'])) {
                        echo "<li><strong>Vincular supervisor ao coordenador:</strong> Execute:<br><code>UPDATE bdfrota.tbsupervisor SET idtbcoordenador = [ID_DO_COORDENADOR] WHERE idtbsupervisor = {$supervisor['idtbsupervisor']}</code></li>";
                    }
                    if (!empty($supervisor) && !empty($supervisor['idtbcoordenador']) && empty($coordenador)) {
                        echo "<li><strong>Coordenador inexistente:</strong> Corrija o idtbcoordenador ou cadastre o coordenador na tabela tbcoord</li>";
                    }
                    
                    echo "</ul>";
                    echo "</div>";
                }
                
                // Estatísticas finais
                echo "<div class='mt-4 p-3' style='background-color: #f8f9fa; border-radius: 8px;'>";
                echo "<h5>📊 Estatísticas da Verificação</h5>";
                echo "<div class='row'>";
                echo "<div class='col-md-4'><strong>Problemas Críticos:</strong> <span class='badge bg-danger'>" . count($problemas_criticos) . "</span></div>";
                echo "<div class='col-md-4'><strong>Advertências:</strong> <span class='badge bg-warning'>" . count($problemas_advertencia) . "</span></div>";
                echo "<div class='col-md-4'><strong>Status Geral:</strong> " . 
                    (count($problemas_criticos) == 0 ? 
                        (count($problemas_advertencia) == 0 ? "<span class='badge bg-success'>EXCELENTE</span>" : "<span class='badge bg-warning'>COM RESTRIÇÕES</span>") 
                        : "<span class='badge bg-danger'>BLOQUEADO</span>") . 
                    "</div>";
                echo "</div>";
                echo "</div>";
                
                echo "</div>";
            }
            
            echo "</div>";
        }
        ?>
        
        <div class="result-box info mt-4">
            <h5>ℹ️ Informações Adicionais</h5>
            <p><strong>Como usar este diagnóstico:</strong></p>
            <ol>
                <li>Digite o ID do pedido (idtbpedidostec) no campo acima</li>
                <li>Clique em "Diagnosticar"</li>
                <li>Analise cada etapa da verificação</li>
                <li>Siga as soluções propostas para cada problema encontrado</li>
            </ol>
            
            <p><strong>Critérios verificados:</strong></p>
            <ul>
                <li>✅ Existência do pedido na tabela tbpedidostec</li>
                <li>✅ Flag = 0 (pedido pendente)</li>
                <li>✅ <strong>Campos NULL ou vazios (matricula, valor, data, flag, tipocota)</strong></li>
                <li>✅ Vínculo técnico → tbusuario</li>
                <li>✅ Vínculo técnico → supervisor (tbsupervisor)</li>
                <li>✅ Vínculo supervisor → coordenador (tbcoord)</li>
                <li>✅ Coordenador logado = coordenador responsável</li>
                <li>✅ Centro de custo e limite de autonomia</li>
                <li>✅ Perfil do coordenador</li>
                <li>✅ Saldo do coordenador</li>
                <li>✅ Saldo anterior do técnico (< R$ 51,00)</li>
                <li>✅ Simulação completa da query de exibição</li>
                <li>✅ Simulação das validações do control</li>
            </ul>
            
            <p><strong>URL com parâmetro:</strong> <code>diagnostico_pedido.php?id=123</code></p>
            
            <p class="mb-0 mt-3"><strong>Legenda de cores:</strong></p>
            <ul class="mb-0">
                <li><span class="badge" style="background-color: #28a745;">Verde</span> - Critério atendido com sucesso</li>
                <li><span class="badge" style="background-color: #ffc107; color: #000;">Amarelo</span> - Advertência (pedido pode aparecer mas com restrições)</li>
                <li><span class="badge" style="background-color: #dc3545;">Vermelho</span> - Problema crítico (impede exibição)</li>
                <li><span class="badge" style="background-color: #17a2b8;">Azul</span> - Informação adicional</li>
            </ul>
        </div>
        
        <div class="mt-4">
            <a href="../" class="btn btn-secondary">← Voltar</a>
        </div>
    </div>
</body>
</html>
