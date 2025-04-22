<?php require_once('../Connections/piercecty.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$harvest=0;
if(isset($_GET['htemp'])) { // process the harvest 
	
$harvest=$_GET['htemp'];

$query="select contact1, farm, maddress, mcity, mstate, mzip from sites, harvests where sites.ID_site=harvests.ID_site and ID_harvest=$harvest";
$rsEnvelope = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsEnvelope);

$tname=$row['contact1']."\n".$row['maddress']."\n".$row['mcity'].", ".$row['mstate']." ".$row['mzip'];
$retadd="Harvest Pierce County's Gleaning Project\n501 South I St\nTacoma, WA 98371";

require('../fpdf/fpdf.php');
$pdf = new FPDF('L','in',array(4.125,9.5));
$pdf->AddPage();
$pdf->SetMargins(0,0);
$pdf->SetXY(.1,.1);
$pdf->SetFont('Times','B',14);
$pdf->MultiCell(4,.25,"$retadd",0,2);
$pdf->SetXY(4,2);
$pdf->SetFont('Times','B',18);
$pdf->MultiCell(0,.3,"$tname",0,2);
$pdf->Output();
} // processed an envelope
else {echo "Add a htemp= query string to the url";}
?>
