<?php
session_start();
include("../conecta.php");

// Verificar se o usuário está logado
$perfil = $_SESSION['perfil'] ?? null;
if ($perfil == null) {
    die('Acesso negado');
}

// Verificar se é uma requisição de exportação
if (!isset($_POST['action']) || $_POST['action'] != 'exportar_hierarquia_completa') {
    die('Ação inválida');
}

header("Content-type: text/html; charset=utf-8");

?>
<!--**
  * gera excel - Hierarquia da Frota
 *-->
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Hierarquia da Frota</title>
  </head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Hierarquia-Frotas-" . date('Y-m-d_H-i-s') . ".xls";

    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>
                <tr>
                    <th>Nível</th>
                    <th>Tipo</th>
                    <th>Matrícula</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Centro Custo</th>
                    <th>Gerente</th>
                    <th>Coordenador</th>
                    <th>Supervisor</th>
                </tr>";

    // Buscar todos os gerentes
    $sql_gerentes = "SELECT g.idtbgerente, g.matricula, f.nome, f.ccusto 
                     FROM bdfrota.tbgerente g, bdaniel.tbfuncionario f 
                     WHERE g.matricula = f.matricula AND f.status = 'Ativo' 
                     ORDER BY f.nome";
    
    $resultado_gerentes = mysql_query($sql_gerentes) or die(mysql_error());
    
    while($row_gerente = mysql_fetch_array($resultado_gerentes)) {
        $gerente_id = $row_gerente[0];
        $gerente_matricula = $row_gerente[1];
        $gerente_nome = $row_gerente[2];
        $gerente_ccusto = $row_gerente[3];
        
        // Inserir gerente na tabela
        $html .= "<tr style='background-color: #E7F3FF; font-weight: bold;'>
                    <td>1</td>
                    <td>GERENTE</td>
                    <td>$gerente_matricula</td>
                    <td>$gerente_nome</td>
                    <td></td>
                    <td>$gerente_ccusto</td>
                    <td>$gerente_nome</td>
                    <td></td>
                    <td></td>
                  </tr>";
        
        // Buscar coordenadores do gerente
        $sql_coord = "SELECT c.idtbcoordenador, c.matricula, f.nome, f.cargo, f.ccusto 
                     FROM bdfrota.tbcoord c, bdaniel.tbfuncionario f 
                     WHERE c.matricula = f.matricula AND f.status = 'Ativo' 
                     AND c.idtbgerente = '$gerente_id'
                     ORDER BY f.nome";
        
        $resultado_coord = mysql_query($sql_coord) or die(mysql_error());
        
        while($row_coord = mysql_fetch_array($resultado_coord)) {
            $coord_id = $row_coord[0];
            $coord_matricula = $row_coord[1];
            $coord_nome = $row_coord[2];
            $coord_cargo = $row_coord[3];
            $coord_ccusto = $row_coord[4];
            
            // Inserir coordenador na tabela
            $html .= "<tr style='background-color: #F0F8FF;'>
                        <td>2</td>
                        <td>COORDENADOR</td>
                        <td>$coord_matricula</td>
                        <td>$coord_nome</td>
                        <td>$coord_cargo</td>
                        <td>$coord_ccusto</td>
                        <td>$gerente_nome</td>
                        <td>$coord_nome</td>
                        <td></td>
                      </tr>";
            
            // Buscar supervisores do coordenador
            $sql_sup = "SELECT s.idtbsupervisor, u.matricula, f.nome, f.cargo, f.ccusto 
                       FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f, bdfrota.tbsupervisor s 
                       WHERE u.matricula = f.matricula AND u.matricula = s.matricula 
                       AND u.perfil = 1 AND f.status = 'Ativo' 
                       AND s.idtbcoordenador = '$coord_id'
                       ORDER BY f.nome";
            
            $resultado_sup = mysql_query($sql_sup) or die(mysql_error());
            
            while($row_sup = mysql_fetch_array($resultado_sup)) {
                $sup_id = $row_sup[0];
                $sup_matricula = $row_sup[1];
                $sup_nome = $row_sup[2];
                $sup_cargo = $row_sup[3];
                $sup_ccusto = $row_sup[4];
                
                // Inserir supervisor na tabela
                $html .= "<tr style='background-color: #F8F8FF;'>
                            <td>3</td>
                            <td>SUPERVISOR</td>
                            <td>$sup_matricula</td>
                            <td>$sup_nome</td>
                            <td>$sup_cargo</td>
                            <td>$sup_ccusto</td>
                            <td>$gerente_nome</td>
                            <td>$coord_nome</td>
                            <td>$sup_nome</td>
                          </tr>";
                
                // Buscar técnicos do supervisor
                $sql_tec = "SELECT u.matricula, u.nome, f.ccusto 
                           FROM bdfrota.tbusuario u, bdaniel.tbfuncionario f 
                           WHERE u.matricula = f.matricula AND u.perfil = 0 
                           AND f.status = 'Ativo' AND u.idtbsupervisor = '$sup_id'
                           ORDER BY u.nome";
                
                $resultado_tec = mysql_query($sql_tec) or die(mysql_error());
                
                while($row_tec = mysql_fetch_array($resultado_tec)) {
                    $tec_matricula = $row_tec[0];
                    $tec_nome = $row_tec[1];
                    $tec_ccusto = $row_tec[2];
                    
                    // Inserir técnico na tabela
                    $html .= "<tr>
                                <td>4</td>
                                <td>TÉCNICO</td>
                                <td>$tec_matricula</td>
                                <td>$tec_nome</td>
                                <td></td>
                                <td>$tec_ccusto</td>
                                <td>$gerente_nome</td>
                                <td>$coord_nome</td>
                                <td>$sup_nome</td>
                              </tr>";
                }
            }
        }
        
        // Linha em branco entre gerentes
        $html .= "<tr><td colspan='9'>&nbsp;</td></tr>";
    }

    $html .= "</table>";

    // Configurações header para forçar o download
    header ("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
    header ("Content-Description: PHP Generated Data" );
    
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>
?>