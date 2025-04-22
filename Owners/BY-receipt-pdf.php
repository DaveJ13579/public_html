<?php require_once('../Connections/gleanslo.php'); 
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../fpdf/fpdf.php');

$year='';
if(isset($_POST['year'])) $year=$_POST['year'];
if(!$year) {
$yearq="select year(h_date) as year from harvests where totwgt>0 group by year(h_date) order by year(h_date)";
$rsYears=mysqli_query($gleanslo, $yearq) or die(mysqli_error($gleanslo)); 
?> 
<html>
<body>
<p><strong>Backyard year-end donation receipts</strong></p>
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
$donorq="select sites.ID_site, contact1, maddress, mcity, farm, mstate, mzip, sum(calcwgt) as weight from harvests, sites where year(h_date)=$year and venue='Backyard' and wgt1>0 and harvests.ID_site=sites.ID_site group by sites.ID_site order by farm, h_date";
$rsDonors = mysqli_query($gleanslo, $donorq) or die(mysqli_error($gleanslo));
while($donorrow=mysqli_fetch_assoc($rsDonors)) { // do all donors
extract($donorrow);

$text1="Thank you for being a part of GleanSLO! Your generous contribution helped provide fresh and nutritious food to families across San Luis Obispo County. Thanks to thoughtful and open-hearted donors like you, we are able to connect our less fortunate neighbors with our county's abundance of fruit and vegetables. Together, our community can build a stronger relationship and a deeper appreciation of our food. We hope to continue this partnership for many seasons to come!\n";

		$pdf->AddPage();
		$pdf->SetMargins(.5,1);
		$pdf->Image('../images/logos/square-logo.jpg',4.5,.5,1.2,0,'jpg');
		$pdf->Image('../images/logos/SLOFoodBank2014.png',6,.5,1.5,0,'png');
		$pdf->SetXY(.5,1.5);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(6.25,.17,"December 31, ".$year,0,'R');
		$pdf->MultiCell(0,.17,"\n\n".$contact1."\n".$maddress."\n".$mcity.", ".$mstate.", ".$mzip,0);
		$pdf->SetFont('Arial','B',10);
		$pdf->MultiCell(0,.17,"\nThank you for your donation in ".$year."!",0);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,.17,"\nDear ".$contact1.",\n\n".$text1."\n",0);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(2.5,.17,"Donor",1,0,'C',true);
		$pdf->Cell(.75,.17,"Date",1,0,'C',true);
		$pdf->Cell(1.75,.17,"Item",1,0,'C',true);
		$pdf->Cell(.5,.17,"Pounds",1,0,'C',true);
		$pdf->Cell(1,.17,"Value",1,1,'C',true);
		
	
$query="select harvests.ID_harvest, h_date, wgt1, crops.name as item  from harvests, sites,crops where year(h_date)=$year and venue='Backyard' and wgt1>0 and harvests.ID_site=sites.ID_site and crops.ID_crop=harvests.crop1 and sites.ID_site=$ID_site order by farm, h_date";
$rsReceipt = mysqli_query($gleanslo, $query) or die(mysqli_error($gleanslo));
while($row = mysqli_fetch_assoc($rsReceipt)) {
extract($row);

	for($x=1;$x<=10;++$x) { // cycle through 10 crop slots for this harvest
			$wgt=0;
			$wgtquery="select crops.name as item, wgt$x as wgt from harvests, crops where ID_harvest=$ID_harvest and crops.ID_crop=crop$x";
			$rsWgt=mysqli_query($gleanslo,$wgtquery) or die(mysqli_error($gleanslo));
			if(mysqli_num_rows($rsWgt)) {$wgtrow=mysqli_fetch_assoc($rsWgt); extract($wgtrow);}
			if($wgt>0) { 
			
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(2.5,.17,"$farm",1,0,'L');
		$pdf->Cell(.75,.17,"$h_date",1,0,'L');
		$pdf->Cell(1.75,.17,"$item",1,0,'C');
		$pdf->Cell(.5,.17,"$wgt",1,0,'R');
		$pdf->Cell(1,.17," ",1,1,'L');
		
			} // end of if wgt>0
	} // done all crop slots for this harvest
} //done all harvests this donor

		$pdf->Cell(2.5,.17," ",0,0,'L');
		$pdf->Cell(.75,.17," ",0,0,'L');
		$pdf->Cell(1.75,.17," ",0,0,'C');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(.5,.17,"$weight",1,0,'R',True);
		$pdf->Cell(1,.17," ",1,1,'L');
		
$text3="\nRetain this letter for tax purposes, as your donation is tax-deductible to the extent allowed by law. Your donation is accepted as follows: Items will not be sold, bartered or transferred for money or services. Items will be used only in a manner related to the tax-exempt purposes of the done organization. All items will be used to aid agencies feeding the hungry in compliance with clauses (1) and (11) of section 170.3 of the Tax Act of 1973. No goods or services were provided or promised by the Food Bank in exchange for this donation. The tax identification number is 77-0210727.\n\nOn behalf of those in need, we thank you again for your generosity and support. Best wishes for a \"fruitful\" ".($year+1)."!";

		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,.17,"$text3",0);
		$pdf->MultiCell(0,.17,"\nFood Bank Coalition of San Luis Obispo County\n1180 Kendall Road\nSan Luis Obispo, CA 97401",0);
		$pdf->Image('../images/SandersSignature.jpg',null,null,2,0,'jpg');
		$pdf->MultiCell(0,.17,"Roxanne Sanders, GleanSLO Program Manager\ngleanslo@slofoodbank.org\n(805) 835-3750",0);
		
} // done all donors
		$pdf->Output();
}?>
