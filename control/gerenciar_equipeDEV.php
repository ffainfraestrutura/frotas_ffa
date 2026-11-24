<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['perfil'] = $perfil;
include("../conecta.php");
include("../conecta02.php");
include_once"../func/log.php";

// ====================================
// FUNÇÃO PARA EXECUTAR QUERY EM DOIS SERVIDORES
// ====================================
function executarEmAmbosServidores($sql, $conexao_principal, $conexao_secundaria, $sincronizar = false) {
    global $conexao, $conn;
    
    // Executar no servidor principal (mysql_connect)
    $resultado_principal = mysql_query($sql, $conexao_principal);
    
    if (!$resultado_principal) {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro no servidor principal: ' . mysql_error($conexao_principal),
            'servidor_principal' => false,
            'servidor_secundario' => false
        ];
    }
    
    $servidor_secundario_ok = true;
    
    // Se sincronizar for true, também executar no servidor secundário
    if ($sincronizar) {
        $resultado_secundario = mysqli_query($conexao_secundaria, $sql);
        
        if (!$resultado_secundario) {
            $servidor_secundario_ok = false;
            error_log("Erro ao sincronizar servidor secundário: " . mysqli_error($conexao_secundaria));
        }
    }
    
    return [
        'sucesso' => true,
        'mensagem' => 'Execução concluída',
        'servidor_principal' => true,
        'servidor_secundario' => $servidor_secundario_ok
    ];
}

// Nova lógica: Transferência de equipe completa
if(isset($_POST['action']) && $_POST['action'] === 'transferir_equipe') {
    header('Content-Type: application/json');
    
    $coordenador_selecionado = $_POST['coordenador_selecionado'] ?? '';
    $supervisor_selecionado = $_POST['supervisor_selecionado'] ?? '';
    $tecnicos = $_POST['tecnicos'] ?? [];
    $gerente_destino = $_POST['gerente_destino'] ?? '';
    $coordenador_destino = $_POST['coordenador_destino'] ?? '';
    $supervisor_destino = $_POST['supervisor_destino'] ?? '';
    $sincronizar_servidor2 = isset($_POST['sincronizar_servidor2']) && $_POST['sincronizar_servidor2'] == true;
    
    $transferencias_realizadas = 0;
    $erros = [];
    
    error_log("DEBUG - Sincronizar Servidor 2: " . ($sincronizar_servidor2 ? 'SIM' : 'NÃO'));
    
    try {
        // 1. Transferir coordenador para o gerente logado
        if (!empty($coordenador_selecionado) && !empty($gerente_destino)) {
            $sql_update_coord = "UPDATE bdfrota.tbcoord SET idtbgerente = '$gerente_destino' WHERE idtbcoordenador = '$coordenador_selecionado'";
            
            $resultado = executarEmAmbosServidores($sql_update_coord, $conexao, $conn, $sincronizar_servidor2);
            
            if ($resultado['sucesso']) {
                $transferencias_realizadas++;
                
                // Log da transferência do coordenador
                $sql_coord_info = "SELECT c.matricula, f.nome FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND c.idtbcoordenador = '$coordenador_selecionado'";
                $resultado_coord_info = mysql_query($sql_coord_info);
                $row_coord_info = mysql_fetch_array($resultado_coord_info);
                $matricula_coord = $row_coord_info[0];
                $nome_coord = $row_coord_info[1];
                
                $acao = "Transferiu coordenador $nome_coord (Mat: $matricula_coord) para sua equipe";
                $flag = '9';
                $logresult = enviarlog($hoje, $acao, $flag, $matricula_coord, $matricula, '', '');
            } else {
                $erros[] = "Erro ao transferir coordenador: " . $resultado['mensagem'];
            }
        }
        
        // 2. Transferir supervisor para o coordenador selecionado
        if (!empty($supervisor_selecionado) && !empty($coordenador_destino)) {
            $sql_update_sup = "UPDATE bdfrota.tbsupervisor SET idtbcoordenador = '$coordenador_destino' WHERE idtbsupervisor = '$supervisor_selecionado'";
            
            $resultado = executarEmAmbosServidores($sql_update_sup, $conexao, $conn, $sincronizar_servidor2);
            
            if ($resultado['sucesso']) {
                $transferencias_realizadas++;
                
                // Log da transferência do supervisor
                $sql_sup_info = "SELECT s.matricula, f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$supervisor_selecionado'";
                $resultado_sup_info = mysql_query($sql_sup_info);
                $row_sup_info = mysql_fetch_array($resultado_sup_info);
                $matricula_sup = $row_sup_info[0];
                $nome_sup = $row_sup_info[1];
                
                $acao = "Transferiu supervisor $nome_sup (Mat: $matricula_sup) para coordenador";
                $flag = '8';
                $logresult = enviarlog($hoje, $acao, $flag, $matricula_sup, $matricula, '', '');
            } else {
                $erros[] = "Erro ao transferir supervisor: " . $resultado['mensagem'];
            }
        }
        
        // 3. Transferir técnicos para o supervisor selecionado
        if (!empty($tecnicos) && !empty($supervisor_destino)) {
            foreach ($tecnicos as $matricula_tec) {
                $sql_update_tec = "UPDATE bdfrota.tbusuario SET idtbsupervisor = '$supervisor_destino' WHERE matricula = '$matricula_tec'";
                
                $resultado = executarEmAmbosServidores($sql_update_tec, $conexao, $conn, $sincronizar_servidor2);
                
                if ($resultado['sucesso']) {
                    $transferencias_realizadas++;
                    
                    // Log da transferência do técnico
                    $sql_nome_tec = "SELECT nome FROM bdfrota.tbusuario WHERE matricula = '$matricula_tec'";
                    $resultado_nome_tec = mysql_query($sql_nome_tec);
                    $row_nome_tec = mysql_fetch_array($resultado_nome_tec);
                    $nome_tec = $row_nome_tec[0];
                    
                    $acao = "Transferiu técnico $nome_tec (Mat: $matricula_tec) para supervisor";
                    $flag = '7';
                    $logresult = enviarlog($hoje, $acao, $flag, $matricula_tec, $matricula, '', '');
                } else {
                    $erros[] = "Erro ao transferir técnico $matricula_tec";
                }
            }
        }
        
        // Resposta JSON
        if ($transferencias_realizadas > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Transferência concluída com sucesso! (' . ($sincronizar_servidor2 ? 'Sincronizado' : 'Servidor Principal') . ')',
                'transferencias' => $transferencias_realizadas,
                'sincronizacao' => $sincronizar_servidor2
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Nenhuma transferência foi realizada. Erros: ' . implode(', ', $erros)
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro interno: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// ...existing code...

// Verifica se é transferência de técnico ou supervisor
if(isset($_POST['matriculatec'])) {
    // TRANSFERÊNCIA DE TÉCNICO PARA SUPERVISOR
    $matriculaf = $_POST['matriculatec'];

    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
    
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "UPDATE bdfrota.tbusuario SET idtbsupervisor = $idtbsupervisor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Transferiu tecnico';
    $flag = '7';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de técnico concluida!');window.location=\"../gerenciar_equipe.php\"</script>";

} elseif(isset($_POST['matricula_sup'])) {
    // TRANSFERÊNCIA DE SUPERVISOR PARA COORDENADOR
    $matriculaf = $_POST['matricula_sup'];

    // Busca o ID do coordenador logado na tabela correta (tbcoord)
    $sql = "SELECT idtbcoordenador FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbcoordenador = $row[0];

    // Verificar se o ID do coordenador foi encontrado
    if($idtbcoordenador) {
        // Atualiza o campo idtbcoordenador do supervisor selecionado ($matriculaf)
        // para apontar para o coordenador logado ($idtbcoordenador)
        $sql = "UPDATE bdfrota.tbsupervisor SET idtbcoordenador = '$idtbcoordenador' WHERE matricula = '$matriculaf'";
        $resultado = mysql_query($sql) or die (mysql_error());
    } else {
        echo"<script language='javascript' type='text/javascript'>alert('Erro: Coordenador não encontrado!');window.location=\"../gerenciar_equipe.php\"</script>";
        exit;
    }

    $acao = 'Transferiu supervisor';
    $flag = '8';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de supervisor concluida!');window.location=\"../gerenciar_equipe.php\"</script>";

} elseif(isset($_POST['matricula_coord'])) {
    // TRANSFERÊNCIA DE COORDENADOR PARA GERENTE
    $matriculaf = $_POST['matricula_coord'];

    // Busca o ID do gerente logado na tabela correta (tbgerente)
    $sql = "SELECT idtbgerente FROM bdfrota.tbgerente where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbgerente = $row[0];

    if($idtbgerente) {
        // Atualiza o campo idtbgerente do coordenador selecionado
        $sql = "UPDATE bdfrota.tbcoord SET idtbgerente = '$idtbgerente' WHERE matricula = '$matriculaf'";
        $resultado = mysql_query($sql) or die (mysql_error());
    } else {
        echo"<script language='javascript' type='text/javascript'>alert('Erro: Gerente não encontrado!');window.location=\"../gerenciar_equipe.php\"</script>";
        exit;
    }

    $acao = 'Transferiu coordenador';
    $flag = '9';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de coordenador concluida!');window.location=\"../gerenciar_equipe.php\"</script>";
}    
?>
