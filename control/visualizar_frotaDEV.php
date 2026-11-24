<?php
ob_start();
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

// Lógica para salvar equipe completa do modal "Gerenciar Equipe"
if(isset($_POST['action']) && $_POST['action'] === 'salvar_equipe') {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $gerente_id = $_POST['gerente_id'] ?? '';
    $coordenador_id = $_POST['coordenador_id'] ?? '';
    $supervisor_id = $_POST['supervisor_id'] ?? '';
    $tecnicos = $_POST['tecnicos'] ?? [];
    
    $alteracoes_realizadas = 0;
    $erros = [];
    $debug = [];
    
    try {
        // 1. Atualizar coordenador para o gerente selecionado
        if (!empty($coordenador_id) && !empty($gerente_id)) {
            $sql_update_coord = "UPDATE bdfrota.tbcoord SET idtbgerente = '$gerente_id' WHERE idtbcoordenador = '$coordenador_id'";
            $debug[] = "SQL Coord: " . $sql_update_coord;
            
            // UPDATE no servidor A (principal)
            $resultado_a = mysql_query($sql_update_coord);
            if (!$resultado_a) {
                $debug[] = "Erro Servidor A (Coord): " . mysql_error();
            }
            
            // UPDATE no servidor B (secundário)
            $resultado_b = mysqli_query($conn, $sql_update_coord);
            if (!$resultado_b) {
                $debug[] = "Erro Servidor B (Coord): " . mysqli_error($conn);
            }
            
            if ($resultado_a && $resultado_b) {
                $alteracoes_realizadas++;
                
                // Log da alteração do coordenador
                $sql_coord_info = "SELECT c.matricula, f.nome FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND c.idtbcoordenador = '$coordenador_id'";
                $resultado_coord_info = mysql_query($sql_coord_info);
                $row_coord_info = mysql_fetch_array($resultado_coord_info);
                
                if ($row_coord_info) {
                    $matricula_coord = $row_coord_info[0];
                    $nome_coord = $row_coord_info[1];
                    $acao = "Atribuiu coordenador $nome_coord (Mat: $matricula_coord) via modal Gerenciar Equipe";
                    $flag = '9';
                    $logresult = enviarlog($hoje, $acao, $flag, $matricula_coord, $matricula, '', '');
                }
            } else {
                $erros[] = "Erro ao atualizar coordenador";
                if (!$resultado_a) $erros[] = "Servidor A falhou";
                if (!$resultado_b) $erros[] = "Servidor B falhou";
            }
        }
        
        // 2. Atualizar supervisor para o coordenador selecionado
        if (!empty($supervisor_id) && !empty($coordenador_id)) {
            $sql_update_sup = "UPDATE bdfrota.tbsupervisor SET idtbcoordenador = '$coordenador_id' WHERE idtbsupervisor = '$supervisor_id'";
            $debug[] = "SQL Supervisor: " . $sql_update_sup;
            
            // UPDATE no servidor A (principal)
            $resultado_a = mysql_query($sql_update_sup);
            if (!$resultado_a) {
                $debug[] = "Erro Servidor A (Supervisor): " . mysql_error();
            }
            
            // UPDATE no servidor B (secundário)
            $resultado_b = mysqli_query($conn, $sql_update_sup);
            if (!$resultado_b) {
                $debug[] = "Erro Servidor B (Supervisor): " . mysqli_error($conn);
            }
            
            if ($resultado_a && $resultado_b) {
                $alteracoes_realizadas++;
                
                // Log da alteração do supervisor
                $sql_sup_info = "SELECT s.matricula, f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$supervisor_id'";
                $resultado_sup_info = mysql_query($sql_sup_info);
                $row_sup_info = mysql_fetch_array($resultado_sup_info);
                
                if ($row_sup_info) {
                    $matricula_sup = $row_sup_info[0];
                    $nome_sup = $row_sup_info[1];
                    $acao = "Atribuiu supervisor $nome_sup (Mat: $matricula_sup) via modal Gerenciar Equipe";
                    $flag = '8';
                    $logresult = enviarlog($hoje, $acao, $flag, $matricula_sup, $matricula, '', '');
                }
            } else {
                $erros[] = "Erro ao atualizar supervisor";
                if (!$resultado_a) $erros[] = "Servidor A falhou";
                if (!$resultado_b) $erros[] = "Servidor B falhou";
            }
        }
        
        // 3. Atualizar técnicos para o supervisor selecionado
        if (!empty($tecnicos) && !empty($supervisor_id)) {
            foreach ($tecnicos as $matricula_tec) {
                $sql_update_tec = "UPDATE bdfrota.tbusuario SET idtbsupervisor = '$supervisor_id' WHERE matricula = '$matricula_tec'";
                $debug[] = "SQL Técnico ($matricula_tec): " . $sql_update_tec;
                
                // UPDATE no servidor A (principal)
                $resultado_a = mysql_query($sql_update_tec);
                if (!$resultado_a) {
                    $debug[] = "Erro Servidor A (Técnico $matricula_tec): " . mysql_error();
                }
                
                // UPDATE no servidor B (secundário)
                $resultado_b = mysqli_query($conn, $sql_update_tec);
                if (!$resultado_b) {
                    $debug[] = "Erro Servidor B (Técnico $matricula_tec): " . mysqli_error($conn);
                }
                
                if ($resultado_a && $resultado_b) {
                    $alteracoes_realizadas++;
                    
                    // Log da alteração do técnico
                    $sql_nome_tec = "SELECT nome FROM bdfrota.tbusuario WHERE matricula = '$matricula_tec'";
                    $resultado_nome_tec = mysql_query($sql_nome_tec);
                    $row_nome_tec = mysql_fetch_array($resultado_nome_tec);
                    
                    if ($row_nome_tec) {
                        $nome_tec = $row_nome_tec[0];
                        $acao = "Atribuiu técnico $nome_tec (Mat: $matricula_tec) via modal Gerenciar Equipe";
                        $flag = '7';
                        $logresult = enviarlog($hoje, $acao, $flag, $matricula_tec, $matricula, '', '');
                    }
                } else {
                    $erros[] = "Erro ao atualizar técnico $matricula_tec";
                    if (!$resultado_a) $erros[] = "Servidor A falhou para técnico $matricula_tec";
                    if (!$resultado_b) $erros[] = "Servidor B falhou para técnico $matricula_tec";
                }
            }
        }
        
        // Resposta JSON
        if ($alteracoes_realizadas > 0) {
            echo json_encode([
                'success' => true,
                'message' => "Equipe salva com sucesso! $alteracoes_realizadas alterações realizadas.",
                'alteracoes' => $alteracoes_realizadas,
                'debug' => $debug
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Nenhuma alteração foi realizada. ' . (count($erros) > 0 ? 'Erros: ' . implode(', ', $erros) : 'Verifique os dados selecionados.'),
                'erros' => $erros,
                'debug' => $debug
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro interno: ' . $e->getMessage(),
            'debug' => $debug,
            'erros' => $erros
        ]);
    }
    
    exit;
}

// Nova lógica: Transferência de equipe completa
if(isset($_POST['action']) && $_POST['action'] === 'transferir_equipe') {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    $coordenador_selecionado = $_POST['coordenador_selecionado'] ?? '';
    $supervisor_selecionado = $_POST['supervisor_selecionado'] ?? '';
    $tecnicos = $_POST['tecnicos'] ?? [];
    $gerente_destino = $_POST['gerente_destino'] ?? '';
    $coordenador_destino = $_POST['coordenador_destino'] ?? '';
    $supervisor_destino = $_POST['supervisor_destino'] ?? '';
    
    $transferencias_realizadas = 0;
    $erros = [];
    
    // Debug: Log das variáveis recebidas
    error_log("DEBUG - Coordenador: " . $coordenador_selecionado);
    error_log("DEBUG - Supervisor: " . $supervisor_selecionado);
    error_log("DEBUG - Gerente destino: " . $gerente_destino);
    error_log("DEBUG - Coord destino: " . $coordenador_destino);
    error_log("DEBUG - Sup destino: " . $supervisor_destino);
    
    try {
        // 1. Transferir coordenador para o gerente logado
        if (!empty($coordenador_selecionado) && !empty($gerente_destino)) {
            $sql_update_coord = "UPDATE bdfrota.tbcoord SET idtbgerente = '$gerente_destino' WHERE idtbcoordenador = '$coordenador_selecionado'";
            
            // UPDATE no servidor A (principal)
            $resultado_a = mysql_query($sql_update_coord);
            // UPDATE no servidor B (secundário)
            $resultado_b = mysqli_query($conn, $sql_update_coord);
            
            if ($resultado_a && $resultado_b) {
                $transferencias_realizadas++;
                
                // Log da transferência do coordenador - buscar matrícula para o log
                $sql_coord_info = "SELECT c.matricula, f.nome FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f WHERE c.matricula = f.matricula AND c.idtbcoordenador = '$coordenador_selecionado'";
                $resultado_coord_info = mysql_query($sql_coord_info);
                $row_coord_info = mysql_fetch_array($resultado_coord_info);
                
                if ($row_coord_info) {
                    $matricula_coord = $row_coord_info[0];
                    $nome_coord = $row_coord_info[1];
                    $acao = "Transferiu coordenador $nome_coord (Mat: $matricula_coord) para sua equipe";
                    $flag = '9';
                    $logresult = enviarlog($hoje, $acao, $flag, $matricula_coord, $matricula, '', '');
                }
            } else {
                $erros[] = "Erro ao transferir coordenador";
            }
        }
        
        // 2. Transferir supervisor para o coordenador selecionado
        if (!empty($supervisor_selecionado) && !empty($coordenador_destino)) {
            $sql_update_sup = "UPDATE bdfrota.tbsupervisor SET idtbcoordenador = '$coordenador_destino' WHERE idtbsupervisor = '$supervisor_selecionado'";
            
            // UPDATE no servidor A (principal)
            $resultado_a = mysql_query($sql_update_sup);
            // UPDATE no servidor B (secundário)
            $resultado_b = mysqli_query($conn, $sql_update_sup);
            
            if ($resultado_a && $resultado_b) {
                $transferencias_realizadas++;
                
                // Log da transferência do supervisor - buscar matrícula para o log
                $sql_sup_info = "SELECT s.matricula, f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$supervisor_selecionado'";
                $resultado_sup_info = mysql_query($sql_sup_info);
                $row_sup_info = mysql_fetch_array($resultado_sup_info);
                
                if ($row_sup_info) {
                    $matricula_sup = $row_sup_info[0];
                    $nome_sup = $row_sup_info[1];
                    $acao = "Transferiu supervisor $nome_sup (Mat: $matricula_sup) para coordenador";
                    $flag = '8';
                    $logresult = enviarlog($hoje, $acao, $flag, $matricula_sup, $matricula, '', '');
                }
            } else {
                $erros[] = "Erro ao transferir supervisor";
            }
        }
        
        // 3. Transferir técnicos para o supervisor selecionado
        if (!empty($tecnicos) && !empty($supervisor_destino)) {
            foreach ($tecnicos as $matricula_tec) {
                $sql_update_tec = "UPDATE bdfrota.tbusuario SET idtbsupervisor = '$supervisor_destino' WHERE matricula = '$matricula_tec'";
                
                // UPDATE no servidor A (principal)
                $resultado_a = mysql_query($sql_update_tec);
                // UPDATE no servidor B (secundário)
                $resultado_b = mysqli_query($conn, $sql_update_tec);
                
                if ($resultado_a && $resultado_b) {
                    $transferencias_realizadas++;
                    
                    // Log da transferência do técnico
                    $sql_nome_tec = "SELECT nome FROM bdfrota.tbusuario WHERE matricula = '$matricula_tec'";
                    $resultado_nome_tec = mysql_query($sql_nome_tec);
                    $row_nome_tec = mysql_fetch_array($resultado_nome_tec);
                    
                    if ($row_nome_tec) {
                        $nome_tec = $row_nome_tec[0];
                        $acao = "Transferiu técnico $nome_tec (Mat: $matricula_tec) para supervisor";
                        $flag = '7';
                        $logresult = enviarlog($hoje, $acao, $flag, $matricula_tec, $matricula, '', '');
                    }
                } else {
                    $erros[] = "Erro ao transferir técnico $matricula_tec";
                }
            }
        }
        
        // Resposta JSON
        if ($transferencias_realizadas > 0) {
            $total_tecnicos = count($tecnicos);
            
            // Criar mensagem detalhada
            $detalhes = [];
            $detalhes[] = "=== DETALHES DA TRANSFERÊNCIA ===";
            $detalhes[] = "Coordenador ID recebido: " . $coordenador_selecionado;
            $detalhes[] = "Supervisor ID recebido: " . $supervisor_selecionado;
            $detalhes[] = "Gerente destino: " . $gerente_destino;
            $detalhes[] = "Coordenador destino: " . $coordenador_destino;
            $detalhes[] = "Supervisor destino: " . $supervisor_destino;
            $detalhes[] = "Técnicos: " . count($tecnicos);
            $detalhes[] = "Transferências realizadas: " . $transferencias_realizadas;
            
            if (!empty($erros)) {
                $detalhes[] = "ERROS: " . implode(', ', $erros);
            }
            
            $mensagem = implode("\n", $detalhes);
                        
            echo json_encode([
                'success' => true,
                'message' => $mensagem,
                'transferencias' => $transferencias_realizadas
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

// Verifica se é transferência de técnico ou supervisor
if(isset($_POST['matriculatec'])) {
    // TRANSFERÊNCIA DE TÉCNICO PARA SUPERVISOR
    $matriculaf = $_POST['matriculatec'];

    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
    
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "UPDATE bdfrota.tbusuario SET idtbsupervisor = $idtbsupervisor where matricula = '$matriculaf'";
    
    // UPDATE no servidor A (principal)
    $resultado = mysql_query($sql) or die (mysql_error());
    // UPDATE no servidor B (secundário)
    mysqli_query($conn, $sql) or die (mysqli_error($conn));

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
        
        // UPDATE no servidor A (principal)
        $resultado = mysql_query($sql) or die (mysql_error());
        // UPDATE no servidor B (secundário)
        mysqli_query($conn, $sql) or die (mysqli_error($conn));
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
        
        // UPDATE no servidor A (principal)
        $resultado = mysql_query($sql) or die (mysql_error());
        // UPDATE no servidor B (secundário)
        mysqli_query($conn, $sql) or die (mysqli_error($conn));
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
