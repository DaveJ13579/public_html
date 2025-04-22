<?php require_once('../Connections/piercecty.php'); 
include_once('../includes/converter.inc.php');

if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$harvest=0;
if(isset($_GET['htemp'])) { // process the harvest 
$harvest=$_GET['htemp'];
$crops=cropstring($harvest);

$query="select contact1, farm, maddress, mcity, mstate, mzip, calcwgt, totwgt, h_date, KeyRec from sites, harvests where sites.ID_site=harvests.ID_site and ID_harvest=$harvest";
$rsEnvelope = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsEnvelope);
$weight= $row['totwgt']>0 ? $row['totwgt'].' pounds' : '______________ pounds';

$text0="Harvest Pierce County's Gleaning Project\n501 South I St\nTacoma, WA 98371\n(253) 278-6215";
$title="Donor Receipt";
$text1="\n\nThank you for your contribution to Pierce County's Gleaning Project!";

$text2="\nDonor: ".$row['contact1']." - [".$row['farm']."]";
$text2.="\n".$row['maddress'];
$text2.="\n".$row['mcity'].", ".$row['mstate']." ".$row['mzip'];
$text2.="\n\nCrop type: ".$crops;
$text2.="\nDonated weight: ".$weight;
$text2.="\nValue (to be filled in by the crop owner): _____________________________";

$text2.=" \n\nDonation date: ".date( 'F j, Y',strtotime($row['h_date']));
$text2.="\nReceipt date: ".date('F j, Y');

$text2.="\n\nRetain this letter for tax purposes, as your donation is tax-deductible to the extent allowed by law. Your donation is accepted as follows: Items willl not be sold, bartered or transferred for money or services. Items will be used only in a manner related to the tax-exempt purposes of the donee organization. All items will be used to aid agencies feeding the hungry in compliance with clauses (1) and (11) of section 170.3 of the Tax Act of 1973. No goods or services were provided or promised by the food bank in exchange for this donation.\n\nThe tax identification number is 91-0894461.";
$text2.="\n\nHarvest Pierce County's Gleaning Project";
require('../fpdf/fpdf.php');
$pdf = new FPDF('P','in',array(8.5,11));
$pdf->AddPage();
$pdf->SetMargins(1,1);
$pdf->SetXY(4,1);
$pdf->SetFont('Times','',30);
$pdf->MultiCell(0,.2,"$title",0);
$pdf->SetXY(1,2.3);
$pdf->SetFont('Times','',14);
$pdf->MultiCell(0,.2,"$text0",0);
$pdf->SetFont('Times','B',14);
$pdf->MultiCell(0,.2,"$text1",0);
$pdf->SetFont('Times','',12);
$pdf->MultiCell(0,.2,"$text2",0);
$pdf->Ln(.5);
$pdf->Output();
} // processed a receipt
else {echo "Add a htemp= query string to the url";}
?>
