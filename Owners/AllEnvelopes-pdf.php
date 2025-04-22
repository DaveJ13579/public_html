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
<p><strong>Year-end donation receipts envelopes </strong></p>
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
$pdf = new FPDF('L','in',array(4.125,9.5));
// loop finds all donors as 'farm' in the sites table
$donorq="select farm, contact1, maddress, mcity, mstate, mzip from harvests, sites where year(h_date)='$year' and calcwgt>0 and harvests.ID_site=sites.ID_site group by farm order by farm, h_date";
$rsDonors = mysqli_query($piercecty, $donorq) or die(mysqli_error($piercecty));

//echo $donorq; 
//echo mysqli_num_rows($rsDonors); exit;
	
while($donorrow=mysqli_fetch_assoc($rsDonors)) { // do all donors
extract($donorrow);

$pdf->AddPage();
$pdf->SetMargins(0,0);
$pdf->SetXY(.2,.2);
$pdf->SetFont('Arial','B',14);
$pdf->MultiCell(2,.25,"",0,2);
$pdf->SetXY(3.5,2);
$pdf->SetFont('Arial','B',18);
$pdf->MultiCell(0,.3,$contact1."\n".$farm."\n".$maddress."\n".$mcity.", ".$mstate.", ".$mzip,0,2);
	
} // done all donors
$pdf->Output();
}
?>
