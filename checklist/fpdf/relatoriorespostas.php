<?php
require('fpdf/fpdf.php');

header("Content-type: text/html; charset=utf-8");

// Função para conectar ao banco de dados
function conectarBD() {
    $servidor = "localhost";
    $usuario = "root";
    $senha = "@F3r7n6c2";
    $db = "bdaniel";

    $conexao = new mysqli($servidor, $usuario, $senha, $db);

    if ($conexao->connect_error) {
        die("Conexão falhou: " . $conexao->connect_error);
    }

    // Configuração da codificação
    $conexao->set_charset("utf8");

    return $conexao;
}

// Função para obter as respostas do formulário
function obterRespostas($id) {
    $conexao = conectarBD();

    $query = "SELECT matricula, datahora, msgresposta FROM bdaniel.tbresultado WHERE idtbresultado = $id";
    $resultado = $conexao->query($query);

    return $resultado->fetch_assoc();
}

function obterPerguntasERespostas($matricula, $datahora) {
    $conexao = conectarBD();

    $query = "SELECT p.pergunta, r.resposta 
              FROM bdaniel.tbperguntas p
              JOIN bdaniel.tbrespostas r ON p.id = r.idpergunta
              WHERE r.matricula = '$matricula' AND r.datahora = '$datahora'";

    $resultado = $conexao->query($query);

    $perguntasERespostas = array();

    while ($linha = $resultado->fetch_assoc()) {
        $perguntasERespostas[] = $linha;
    }

    return $perguntasERespostas;
}

// Criação do PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Relatório de Respostas do Formulário', 0, 1, 'C');
        $this->Ln(10);
    }

    function ChapterTitle($num, $label) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Pergunta ' . $num . ': ' . $label, 0, 1);
        $this->Ln(4);
    }

    function ChapterBody($body) {
        $this->SetFont('Arial', '', 12);
        $this->Write(10, $this->UTF8ToISO8859($body)); // Corrige a codificação
        $this->Ln();
    }

    function UTF8ToISO8859($text) {
        return utf8_decode($text);
    }
}

$id = 25; // Substitua pelo seu ID de resultado

$respostas = obterRespostas($id);

if ($respostas) {
    $matricula = $respostas['matricula'];
    $datahora = $respostas['datahora'];
    $msgresposta = $respostas['msgresposta'];

    $perguntasERespostas = obterPerguntasERespostas($matricula, $datahora);

    $pdf = new PDF();
    $pdf->AddPage();

    // Título principal
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Respostas do Formulário', 0, 1, 'C');
    $pdf->Ln(10);

    // Resumo do resultado
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Matrícula: ' . $matricula, 0, 1);
    $pdf->Cell(0, 10, 'Data e Hora: ' . $datahora, 0, 1);
    $pdf->Cell(0, 10, 'Mensagem de Resposta: ' . $msgresposta, 0, 1);
    $pdf->Ln(10);

    // Perguntas e Respostas
    foreach ($perguntasERespostas as $indice => $perguntaEResposta) {
        $pdf->ChapterTitle($indice + 1, $perguntaEResposta['pergunta']);
        $pdf->ChapterBody('Resposta: ' . $perguntaEResposta['resposta']);
    }

    $pdf->Output();
} else {
    echo "Não foram encontrados resultados para o ID especificado.";
}
?>
