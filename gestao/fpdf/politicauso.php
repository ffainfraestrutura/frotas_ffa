<?php
require'fpdf.php';
require('../../conecta2.php');
//require('../../conecta.php');

header("Content-type: text/html; charset=utf-8");

$matcondutor = $_GET['matcond'];

//nome, cpf, cnh, tipocnh, validadecnh

$sql = "SELECT nome, cpf FROM bdaniel.tbfuncionario WHERE matricula = '$matcondutor';";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
	$nome = utf8_encode($row['nome']);
	$cpf = $row['cpf'];

$sql2="SELECT numcnh, categoria, validade FROM bdfrota.tbcnh WHERE matricula='$matcondutor';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
	$numcnh= $row2['numcnh'];
	$categoria = $row2['categoria'];
	$validade = $row2['validade'];

	$validade1 = explode(" ", $validade);
    $validaded = explode("-", $validade1[0]);
    $validadef = $validaded[2]."/".$validaded[1]."/".$validaded[0];

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
	$this->Cell(140,10,utf8_decode('Política de Uso de Veículos - FFA INFRAESTRUTURA'),1,1,'C','false');
	// Line break
	$this->Ln(2);
}

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',12);

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("Eu, $nome, CPF: $cpf, portador da carteira de habilitação do tipo $categoria, sob o número, $numcnh, com validade até $validadef concordo com a política abaixo apresentada, tendo ciência que em caso de não cumprimento da mesma serei penalizado conforme previsto na mesma."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("1) Esta Política de uso de veículos está sendo adotada no sentido de conscientizar os empregados quanto à responsabilidade de uso dos veículos e também buscando diminuir o ônus quanto a manutenção dos mesmos, visto que, muitas vezes esta, é feita por haver negligência por parte de seus usuários."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("2) Como é do conhecimento de todos, os veículos são recursos muito importantes para a realização de trabalho em campo. É de extrema importância que o funcionário cuide bem desses recursos no sentido de conservá-lo, pois é essencial para o andamento do trabalho."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3) Estas normas foram criadas com o intuito de manter os veículos em boas condições de manutenção, aumentar a segurança e fazer com que todos possam compartilhá-los de maneira justa."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.1 - Os veículos devem ser usados SOMENTE para assuntos relacionados a empresa nos horários e locais por ela determinados, sendo VEDADO seu uso fora do descrito acima. Em hipótese alguma, o veículo poderá ser usado para fins particulares, ou de qualquer outra natureza que não atendam a FFA Infraestrutura e Serviços. O uso do veículo em assuntos não relacionados a empresa, remete o infrator a FALTA GRAVE, sujeita a medidas administrativas condizentes com a mesma."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.1 - Os veículos devem ser usados SOMENTE para assuntos relacionados a empresa nos horários e locais por ela determinados, sendo VEDADO seu uso fora do descrito acima. Em hipótese alguma, o veículo poderá ser usado para fins particulares, ou de qualquer outra natureza que não atendam a FFA Infraestrutura e Serviços. O uso do veículo em assuntos não relacionados a empresa, remete o infrator a FALTA GRAVE, sujeita a medidas administrativas condizentes com a mesma."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.2 - Pessoas não autorizadas pela empresa não devem dirigir ou serem transportada em veículos da empresa."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.3 - Qualquer dano, seja por acidente ou falha no veículo, deverá ser comunicado imediatamente. Em caso de acidente ou furto, o usuário deverá preencher um Boletim de Ocorrência na polícia imediatamente após o fato. O não cumprimento desse procedimento poderá acarretar custo que será cobrado ao usuário."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.4 - A Empresa se reserva o direito de cobrar dos motoristas o custo de quaisquer danos causados no veículo por negligência ou abuso."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.5 - O funcionário que receber qualquer multa por infração de trânsito deverá comunicar o fato imediatamente ao Responsável da Empresa."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.6 - Qualquer infração cometida, seja registrada por foto de satélite (onde houver esse recurso) ou por guarda de trânsito municipal ou rodoviário, será cobrada do empregado que estiver com o veículo, no dia da infração."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.7 - Os motoristas devem apresentar ao escritório, uma cópia atualizada de sua carteira de motorista. Os funcionários que não estiverem devidamente licenciados não terão permissão para conduzir o veículo da empresa."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.8 - O cancelamento ou a suspensão da carteira deverá ser comunicado imediatamente a empresa."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.9 - O funcionário é responsável pela verificação do nível de água e de óleo lubrificante do veículo, Quaisquer anomalias com os itens citados devem provocar IMEDIATA parada do veículo e o condutor deverá contatar o Setor de Frotas da FFA Infraestrutura que providenciará um reboque para remoção do veículo a uma oficina credenciada. A não observância desta orientação, sujeita o condutor a ARCAR COM OS CUSTOS de eventual reparo do veículo sob sua responsabilidade."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.10 - Fica o condutor responsável por acompanhar a quilometragem do veículo para futuras revisões ou manutenções desde ato do recebimento do veículo até a sua devolução, nossos veículos fazem revisão a cada 10.000 km rodado ou de seis em seis meses de acordo com o manual do fabricante, a frota deve ser comunicada com antecedência para que possamos agendar a revisão ou manutenção do veículo junto a locadora, caso a frota não seja comunicada e ultrapasse a quilometragem informada para revisão no veículo, todos os danos causados no veículo será inteiramente de responsabilidade do condutor."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.11 - Sobre a limpeza do veículo, entregamos o veículo limpo e higienizado para o condutor, fica a cargo do motorista condutor manter o veículo limpo e em condições de trafegar, caso não seja cumprido as determinações sobre a limpeza do veículo será cobrado o valor de 35,00 para geral do carro no ato da entrega do veículo sujo."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("3.12 - Em hipótese alguma, os itens de segurança, tais como: extintor, macaco, chave de roda, triângulo, estepe, tapetes, capa dos bancos e peito de aço podem ser removidos dos veículos, sob pena das sanções administrativas e jurídicas aplicadas para correção do problema."),0,'J');

$pdf->Ln(5);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("Estando de acordo com as condições estabelecidas neste termo, declaro estar ciente das informações acima."),0,'J');

$pdf->Ln(8);
$pdf->Cell(15);
$pdf->MultiCell(160,5,utf8_decode("RIO DE JANEIRO, _____ de, _____________ de 2023.\n\n\n___________________________________________\nASSINATURA DO COLABORADOR"),0,'C');

$pdf->Output();
?>

