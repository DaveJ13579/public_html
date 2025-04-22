<?php
require('../fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times','B',14);
$pdf->Cell(5,5,'Salem Harvest');
$pdf->Output();
?>
