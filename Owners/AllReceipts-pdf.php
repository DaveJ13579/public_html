<?php require_once('../Connections/piercecty.php'); 
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../fpdf/fpdf.php');

$year='';
if(isset($_POST['year'])) $year=$_POST['year'];
if(!$year) {
$yearq="select year(h_date) as year from harvests where totwgt>0 group by year(h_date) order by year(h_date)";
$rsYears=mysqli_query($piercecty, $yearq) or die(mysqli_error($piercecty)); 
?> 
<html>
<body>
<p><strong>All  sites year-end donation receipts</strong></p>
<form action="" method="post">
<select name="year">
             <option value="" selected="selected"></option>
               <?php while ($droprow=mysqli_fetch_assoc($rsYears)) { ?>
              <option value="<?php echo $droprow['year']; ?>"><?php echo $droprow['year']; ?></option>
	 		  <?php } ?>
  </select>
  <input type="submit" name="submit" id="submit" value="Select year" />
</form> 
</body>
</html>
<?php
} else {
		$pdf = new FPDF('P','in',array(8.5,11));
		$pdf -> SetFillColor(200,200,200);
// outer loop finds all donors as 'contact1' in the sites table
$donorq="select sites.ID_site, farm, contact1, maddress, mcity, mstate, mzip, sum(calcwgt) as weight from harvests, sites where year(h_date)=$year and calcwgt>0 and harvests.ID_site=sites.ID_site group by sites.ID_site order by farm, h_date";
$rsDonors = mysqli_query($piercecty, $donorq) or die(mysqli_error($piercecty));
while($donorrow=mysqli_fetch_assoc($rsDonors)) { // do all donors
extract($donorrow);

$text1="\nYou are receiving this certificate as documentation for donations of fresh fruits and/or fresh vegetables you made in ".$year." to Harvest Pierce County, a program of the Pierce County Conservation District. Thank you for your donations! Your generosity helped to ensure that more fresh and healthy food was available to residents of Pierce County most in need. Under state and federal tax law you may be eligible for a deduction or tax credit for your donation. No goods or services were provided or promised in exchange for this donation.\n\nThe tax identification number is 91-0894461.";

		$pdf->AddPage();
		$pdf->SetMargins(.5,1);
		$pdf->Image('../images/logos/logo-square.png',3.5,.5,2.2,0,'png');
		$pdf->SetXY(.5,2);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,.17,"December 31, ".$year,0);
		$pdf->MultiCell(0,.17,"\n\n".$contact1."\n".$farm."\n".$maddress."\n".$mcity.", ".$mstate.", ".$mzip,0);
		$pdf->MultiCell(0,.17,"$text1",0);
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(2.5,.17,"Donor",1,0,'C',true);
		$pdf->Cell(.75,.17,"Date",1,0,'C',true);
		$pdf->Cell(1.75,.17,"Item",1,0,'C',true);
		$pdf->Cell(1,.17,"Origin",1,0,'C',true);
		$pdf->Cell(.5,.17,"Pounds",1,0,'C',true);
		$pdf->Cell(1,.17,"Value",1,1,'C',true);

$query="select harvests.ID_harvest as ID_harvest, h_date from harvests, sites where year(h_date)=$year and calcwgt>0 and harvests.ID_site=sites.ID_site and sites.ID_site=$ID_site order by farm, h_date";
$rsReceipt = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
while($row = mysqli_fetch_assoc($rsReceipt)) {
extract($row);

	for($x=1;$x<=10;++$x) { // cycle through 10 crop slots for this harvest
			$wgt=0;
			$wgtquery="select crops.name as item, wgt$x as wgt from harvests, crops where ID_harvest=$ID_harvest and crops.ID_crop=crop$x";
			$rsWgt=mysqli_query($piercecty,$wgtquery) or die(mysqli_error($piercecty));
			if(mysqli_num_rows($rsWgt)) {$wgtrow=mysqli_fetch_assoc($rsWgt); extract($wgtrow);}
			if($wgt>0) { 
			
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(2.5,.17,"$farm",1,0,'L');
		$pdf->Cell(.75,.17,"$h_date",1,0,'L');
		$pdf->Cell(1.75,.17,"$item",1,0,'C');
		$pdf->Cell(1,.17," ",1,0,'L');
		$pdf->Cell(.5,.17,"$wgt",1,0,'R');
		$pdf->Cell(1,.17," ",1,1,'L');
		
			} // end of if wgt>0
	} // done all crop slots for this harvest
} //done all harvests this donor

		$pdf->Cell(2.5,.17," ",0,0,'L');
		$pdf->Cell(.75,.17," ",0,0,'L');
		$pdf->Cell(1.75,.17," ",0,0,'C');
		$pdf->Cell(1,.17," ",0,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(.5,.17,"$weight",1,0,'R',True);
		$pdf->Cell(1,.17," ",1,1,'L');
		
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,.17,"\nHarvest Pierce County's Gleaning Project\n501 South I St.\nTacoma, WA 98371\n(253) 278-6215\n\n",0);
		// $pdf->Image('../images/SandersSignature.jpg',null,null,2,0,'jpg');
		$pdf->MultiCell(0,.17,"\n\n\nDevon Kerr\devonk@piercecd.org\n253.290.8232");
} // done all donors
		$pdf->Output();
} 
?>
