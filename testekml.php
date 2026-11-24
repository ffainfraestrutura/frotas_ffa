<?php
// Conexão com o banco de dados (substitua as informações do seu banco de dados)
$host = '10.27.0.100';
$usuario = 'blvp';
$senha = 'qrdrps';
$banco_de_dados = 'bdfrota';


$conexao = new mysqli($host, $usuario, $senha, $banco_de_dados);

// Verificar conexão
if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}

// Query SQL para obter todas as matrículas
$sql_matriculas = "SELECT DISTINCT matricula FROM tbabastecimento WHERE data >= '2024-01-29' AND data <= '2024-02-04'";
$resultado_matriculas = $conexao->query($sql_matriculas);

// Verificar se há matrículas
if ($resultado_matriculas->num_rows > 0) {
    // Loop através das matrículas
    while ($row_matricula = $resultado_matriculas->fetch_assoc()) {
        $matricula = $row_matricula['matricula'];
        
        // Query SQL para obter os abastecimentos na semana para cada matrícula
        $sql_abastecimentos = "SELECT 
                                    hodometro,
                                    litros
                                FROM tbabastecimento
                                WHERE data >= '2024-01-29' AND data <= '2024-02-04' AND matricula = '$matricula'
                                ORDER BY data ASC";

        $resultado_abastecimentos = $conexao->query($sql_abastecimentos);

        // Verificar se há abastecimentos
        if ($resultado_abastecimentos->num_rows > 0) {
            // Inicializar variáveis
            $litros = array();
            $primeiro_hodometro = 0;
            $ultimo_hodometro = 0;
            
            // Loop através dos resultados
            while ($row = $resultado_abastecimentos->fetch_assoc()) {
                // Atualizar o hodômetro atual (será o último ao final do loop)
                $ultimo_hodometro = $row['hodometro'];
                
                // Adicionar litros ao array
                $litros[] = $row['litros'];
                
                // Atualizar o hodômetro inicial (será o primeiro ao final do loop)
                if ($primeiro_hodometro == 0) {
                    $primeiro_hodometro = $row['hodometro'];
                }
            }
            
            // Excluir o último elemento do array de litros
            array_pop($litros);
            
            // Somar os litros, excluindo o último
            $total_litros = array_sum($litros);
            
            // Calcular a distância percorrida
            $distancia_percorrida = $ultimo_hodometro - $primeiro_hodometro;
            
            // Calcular os quilômetros por litro (KPL)
            $kpl = ($distancia_percorrida > 0) ? $distancia_percorrida / $total_litros : 0;
            $kpl =  round($kpl,2);
            // Imprimir os resultados para cada matrícula
            echo "Matrícula: " . $matricula . "<br>";
            echo "Distância percorrida: " . $distancia_percorrida . " km<br>";
            echo "Total de litros abastecidos: " . $total_litros . " litros<br>";
            echo "Quilômetros por litro (KPL): " . $kpl . " km/l<br>";
            echo "-----------------------------------<br>";
        } else {
            echo "Nenhum abastecimento encontrado para a matrícula $matricula na semana especificada.<br>";
        }
    }
} else {
    echo "Nenhuma matrícula encontrada.";
}

// Fechar conexão
$conexao->close();
?>