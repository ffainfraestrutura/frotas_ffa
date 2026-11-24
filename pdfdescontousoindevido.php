<?php
require './pdf//fpdf/fpdf.php';
require('./conecta.php');
header("Content-type: text/html; charset=utf-8");

$id = $_GET['id'];

$sql = "SELECT * FROM bdfrota.tbdescontousoindevido where id= '$id';";
$resultado = mysql_query($sql) or die(mysqli_error($conexao));
$row = mysql_fetch_assoc($resultado);

$matcond = $row['matricula'];
$placa = $row['placa'];
$dataocorrencia = $row['data_ocorrencia'];
$tipoUsoIndevido = $row['tipo_uso_indevido'];
$valor = $row['valor_desconto'];
$dataAssinatura = ($row['horario_aceite_colaborador'] != "" && $row['horario_aceite_colaborador'] != null) ? $row['horario_aceite_colaborador'] : "______________________________________________________";
$photoAceite = $row['url_photo_aceite'];

$sql = "SELECT * FROM bdaniel.tbfuncionario where matricula= '$matcond';";
$resultado = mysql_query($sql) or die(mysqli_error($conexao));
$row = mysql_fetch_assoc($resultado);

$nome = $row['nome'];
$cpf = $row['cpf'];
$codFilial = $row['codfilial'];

$sql = "SELECT cnpj, Estado FROM bdaniel.tbfilial WHERE codfilial='$codFilial'; ";
$resultado = mysql_query($sql) or die(mysqli_error($conexao));
$row = mysql_fetch_assoc($resultado);

$cnpj = $row['cnpj'];
$estadofilial = $row['Estado'];

class PDF extends FPDF
{

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        if (strpos($corners, '2') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '3') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($corners, '4') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '1') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$startX = 10;
$startY = $pdf->GetY();
$boxWidth = 190;
$pdf->Rect(10, 10, $boxWidth, 275);
// Logo e título
$pdf->Image('./src/images/logo.png', 15, 15, 25);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(215);
$pdf->Ln(15);
$pdf->Cell(15);
$pdf->Cell($boxWidth, 5, utf8_decode('Notificação de Desconto em Folha - Uso Indevido do Veículo'), 0, 0, 'C');
$pdf->Ln(20);
$currentY = $pdf->GetY();
$pdf->rect($startX, $currentY, $boxWidth, 0);

// Salva a posição inicial para calcular altura depois
$yBeforeBox = $startY;

// Inicia buffer para medir altura
ob_start();

// Começa o conteúdo da notificação dentro da borda
$pdf->SetX($startX);
// Dados da empresa
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("Nome do Empregador: "));
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("FFA INFRAESTRUTURA E SERVIÇOS LTDA\nCNPJ n.º $cnpj"), 0);
$pdf->Ln(2);
$currentY = $pdf->GetY();
$pdf->rect($startX, $currentY, $boxWidth, 0);

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("Nome do Empregado: "), 0);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("$nome\nCPF: $cpf"), 0);
$pdf->Ln(2);
$currentY = $pdf->GetY();
$pdf->rect($startX, $currentY, $boxWidth, 0);

$pdf->Ln(4);

// Texto da notificação
// Define posição inicial e altura da caixa

$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("NOTIFICAÇÃO DE DESCONTO EM FOLHA DE PAGAMENTO"), 0);

$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell($boxWidth, 5, utf8_decode("Prezado(a) $nome"), 0);
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("Após análise das informações relativas ao uso do veículo da empresa, foi constatado que o mesmo foi utilizado de forma indevida, em desacordo com as diretrizes estabelecidas pela política interna de utilização dos veículos corporativos, que determina que os veículos devem ser usados "), 0);


$pdf->SetFont('Arial', 'B', 10);
$pdf->Write(5, utf8_decode("exclusivamente para atividades relacionadas ao desempenho das funções profissionais."));

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("Em razão do uso indevido do veículo, conforme apurado, informamos que será realizado um desconto no valor de "));

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Write(5, utf8_decode("R$ $valor "));

$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("referente ao período em que o veículo foi utilizado fora das condições permitidas, em conformidade com a política interna e o artigo "));

$pdf->SetFont('Arial', 'B', 10);
$pdf->Write(5, utf8_decode("462 da Consolidação das Leis do Trabalho (CLT)."));

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("O referido desconto será efetuado diretamente em sua folha de pagamento, na próxima data de pagamento."), 0);

$pdf->Ln(7);
$pdf->Write(5, utf8_decode("Salientamos que o uso indevido do veículo pode resultar em "), 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Write(5, utf8_decode("novas sanções disciplinares"), 0);

$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode(", conforme o regulamento da empresa, e poderá acarretar em "), 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Write(5, utf8_decode("medidas mais severas em caso de reincidência."), 0);

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("Solicitamos que o(a) colaborador(a) assine abaixo, reconhecendo a ciência e aceitação das condições aqui expostas."), 0);

$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("Foto registrada pelo funcionário na data: $dataAssinatura"), 0);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Write(5, utf8_decode("Assinatura do colaborador:"), 0);

$y = $pdf->GetY(); // linha atual antes das imagens

$assinaturaURL = "https://ffa.painel-telecom.com:4242/assinaturas/docs/colass/$matcond.png";
$headers = @get_headers($assinaturaURL);
$arquivoExiste = $headers && strpos($headers[0], '200') !== false;

if ($arquivoExiste) {

    $pdf->SetXY(11, $y + 15);
    $pdf->Write(5, utf8_decode($nome));
    $pdf->Line(11, $y + 26, 11 + 60, $y + 26); // linha para assinatura
    $pdf->Image($assinaturaURL, 11, $y + 27, 40);
    // Agora a assinatura remota
}

if ($photoAceite != null) {
    $larguraImagem = 50;
    $xDireitaAuto = 200 - $margemDireita - $larguraImagem;
    $pdf->Image('./docs/assinaturaDesconto/' . $photoAceite, $xDireitaAuto, $y + 7, $larguraImagem);
}
// Espaçamento após as assinaturas
$pdf->Ln(50);
// Calcula a altura da caixa
$yAfterBox = $pdf->GetY();
$boxHeight = $yAfterBox - $yBeforeBox;

// Desenha a borda ao redor do conteúdo

$pdf->Output();

?>