<?php
// Teste simples para verificar a conexão e estrutura da tabela
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../conecta.php";

echo "<h2>Teste de Conexão com Histórico</h2>";

// Testa se a tabela existe
$sql = "SHOW TABLES LIKE 'tb_historico_modificacoes'";
$resultado = mysql_query($sql);

if ($resultado && mysql_num_rows($resultado) > 0) {
    echo "<p style='color: green;'>✓ Tabela tb_historico_modificacoes existe!</p>";
} else {
    echo "<p style='color: red;'>✗ Tabela tb_historico_modificacoes NÃO existe!</p>";
    echo "<p>Erro: " . mysql_error() . "</p>";
    exit;
}

// Testa a estrutura da tabela
echo "<h3>Estrutura da Tabela:</h3>";
$sql = "DESCRIBE bdfrota.tb_historico_modificacoes";
$resultado = mysql_query($sql);

if ($resultado) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysql_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Erro ao descrever tabela: " . mysql_error() . "</p>";
}

// Conta quantos registros existem
echo "<h3>Contagem de Registros:</h3>";
$sql = "SELECT COUNT(*) as total FROM bdfrota.tb_historico_modificacoes";
$resultado = mysql_query($sql);

if ($resultado) {
    $row = mysql_fetch_assoc($resultado);
    echo "<p>Total de registros: <strong>" . $row['total'] . "</strong></p>";
} else {
    echo "<p style='color: red;'>Erro ao contar registros: " . mysql_error() . "</p>";
}

// Mostra os últimos 5 registros
echo "<h3>Últimos 5 Registros:</h3>";
$sql = "SELECT * FROM bdfrota.tb_historico_modificacoes ORDER BY data_hora DESC LIMIT 5";
$resultado = mysql_query($sql);

if ($resultado && mysql_num_rows($resultado) > 0) {
    echo "<table border='1' cellpadding='5' style='font-size: 12px;'>";
    echo "<tr><th>ID</th><th>Tabela</th><th>Registro ID</th><th>Ação</th><th>Autor</th><th>Data/Hora</th><th>Observações</th></tr>";
    while ($row = mysql_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['tabela'] . "</td>";
        echo "<td>" . $row['registro_id'] . "</td>";
        echo "<td>" . $row['acao'] . "</td>";
        echo "<td>" . $row['matricula_autor'] . "</td>";
        echo "<td>" . $row['data_hora'] . "</td>";
        echo "<td>" . substr($row['observacoes'], 0, 50) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum registro encontrado ainda.</p>";
    if (mysql_error()) {
        echo "<p style='color: red;'>Erro: " . mysql_error() . "</p>";
    }
}

// Testa uma query similar à do buscar_historico_rotas.php
echo "<h3>Teste de Query Completa:</h3>";
$sql = "SELECT 
            id,
            tabela,
            registro_id,
            acao,
            matricula_autor,
            nome_autor,
            DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') as data_hora_formatada,
            origem,
            ip_origem,
            observacoes
        FROM bdfrota.tb_historico_modificacoes 
        WHERE 1=1
        ORDER BY data_hora DESC LIMIT 5";

echo "<pre>" . $sql . "</pre>";

$resultado = mysql_query($sql);

if ($resultado) {
    echo "<p style='color: green;'>✓ Query executada com sucesso!</p>";
    echo "<p>Registros retornados: " . mysql_num_rows($resultado) . "</p>";
    
    if (mysql_num_rows($resultado) > 0) {
        echo "<h4>Dados em JSON:</h4>";
        $dados = array();
        while ($row = mysql_fetch_assoc($resultado)) {
            $dados[] = $row;
        }
        echo "<pre>" . json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>✗ Erro na query: " . mysql_error() . "</p>";
}

mysql_close();
?>
```
