<?php
require'fpdf.php';
require('../../conecta2.php');
//require('../../conecta.php');

header("Content-type: text/html; charset=utf-8");

$matcondutor = $_GET['matcond'];
//nome, cpf, cnh, tipocnh, validadecnh
$sql = "SELECT nome, cpf, codempresa, codfilial FROM bdaniel.tbfuncionario WHERE matricula = '$matcondutor';";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
	$nome = utf8_encode($row['nome']);
	$cpf = $row['cpf'];
	$codempresa = $row['codempresa'];
	$codfilial = $row['codfilial'];

$sql2="SELECT numcnh, categoria, validade FROM bdfrota.tbcnh WHERE matricula='$matcondutor';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
	$numcnh= $row2['numcnh'];
	$categoria = $row2['categoria'];
	$validade = $row2['validade'];

$sql3="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial';";
$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
	$filial= $row3['nome'];

$cidade = strtoupper(substr($filial, 3));
 
//print "$nome = 	$cpf = $numcnh= $categoria = $validade ";


class PDF extends FPDF
{
// Page header
function Header()
{
	// Logo
	$this->Image('../../src/images/logoredonda.png',10,6,30);
	// Arial bold 15
	$this->SetFont('Arial','B',12);
	// Cor de fundo
	$this->SetFillColor(255);
	// Line break
	$this->Ln(10);
	// Move to the right
	$this->Cell(35);
	// Title
	$this->Cell(140,10,utf8_decode('Termo de Responsabilidade'),1,1,'C','false');
	// Line break
	$this->Ln(2);
}

}

define('PRC',chr(37));
define('CFR',chr(36));//define $


// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',12);

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("Eu, $nome, CPF: $cpf, declaro para os devidos fins que nesta data recebi desta empresa o cartão ticket log, e estou ciente que é de minha inteira responsabilidade a utilização deste cartão, cujo qual será carregado SEMANALMENTE no valor de R".CFR." ____________ reais e de uso exclusivo para abastecimento para atividades relacionadas ao interesse da empresa, diante das seguintes atribuições que se deve seguir:"),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("1- Caso o veículo possua GNV o abastecimento deve ser de no mínimo 90".PRC." deste combustível;\n2- Informar a quilometragem real do veículo, no momento do abastecimento;\n3- Verificar se o frentista digitou corretamente a quantidade de litros, o valor do combustível e a quilometragem;\n4- Informar o número da matrícula e digitar a minha senha pessoal, se solicitado;\n5- Conferir os dados impressos no cupom (quilometragem, litros e valor);\n6- Assinar o cupom da transação."),0,'J');

$pdf->SetFont('Times','B',14);
$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("Também estou ciente dos seguintes procedimentos:"),0,'C');

$pdf->SetFont('Times','',12);
$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("1- Caso as informações impressas no cupom estiverem incorretas, deverei solicitar imediatamente o cancelamento da transação e a realização de uma nova transação de dados corretos;\n2- Este cartão somente poderá ser utilizado para serviços autorizados (abastecimento e demais serviços contratados) pela empresa;\n3- Não é permitida, em hipótese alguma, a retenção do cartão pelo posto de combustível;\n4- Declaro também estar ciente de que todos os dados inseridos no sistema serão registrados em tempo real (on-line) e serão avaliados pelo gestor da empresa;\n5- O cartão é de uso especifico do carro relacionado ao condutor, sendo terminantemente proibida a utilização deste em outro veículo e qualquer utilização indevida será caracterizada como tentativa de fraude.\n6- Em caso de extravio e danos que acarretem a perda total ou parcial do bem, fica o usuário obrigado a ressarcir a empresa, com o custo de reposição do cartão ao qual será descontado em folha de pagamento, devendo o usuário informar imediatamente ao RH da empresa;"),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("Em caso de não cumprimento das atribuições acima descritas à empresa se reserva o direito em atribuir a sanções punitivas que são facultadas pelas leis trabalhistas e pelo Código Penal Brasileiro."),0,'J');



$pdf->Ln(8);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("$cidade, _____ de, _____________ de 2023.\n\n\n___________________________________________\nASSINATURA DO COLABORADOR"),0,'C');

$pdf->Output();
?>

