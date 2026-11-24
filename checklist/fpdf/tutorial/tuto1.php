<?php
require('../fpdf.php');

$str = 'Hello World! á é í ó ú';
$tuto = iconv('UTF-8', 'windows-1252', $str);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,$tuto);
$pdf->Output();
?>
