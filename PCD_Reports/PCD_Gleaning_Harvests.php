<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$year=date('Y');
if(isset($_POST['submit'])) $year=$_POST['year'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>PCD Gleaning Harvests</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent" style="font-size: 1em;">
<h1>PCD Gleaning Harvests - <?php echo $year;?></h1>
<form action="" method="post" name="year">
Year 
<select name="year">
<?php 
	$yearquery="select distinct(year(h_date)) as year from harvests order by year desc";
	$rsYear=mysqli_query($piercecty, $yearquery);
		while ($yrow=mysqli_fetch_assoc($rsYear)) {
			$dropyear=$yrow['year'];
			echo "<option value=$dropyear ";
			if($dropyear==$year) echo 'selected="selected"';
			echo ">$dropyear</option>";
			}
	?>
</select>
<input name="submit" type="submit" value="Select year" />
 </form>
<?php
//$year='2020';
$q="select ID_harvest, farm, address, city, state, zip, h_date, totwgt from harvests, sites where sites.ID_site=harvests.ID_site and year(h_date)='$year' and month(h_date)<>0 order by h_date";
$rsQ=mysqli_query($piercecty,$q);
?>
<table border="1" cellspacing="1" cellpadding="2">
<tr><th>Harvest Location</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Date of Harvest</th><th>Crops Harvested</th><th>Leader</th><th>Number of Pickers</th><th>Total Weight</th></tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ)) {
extract($r);

echo "<tr><td>$farm</td><td>$address</td><td>$city</td><td>$state</td><td>$zip</td><td>$h_date</td><td>".cropstring($ID_harvest)."</td>";

$ldrq="select fname, lname from pickers, harvests where pickers.ID_picker=harvests.ID_leader and ID_harvest=$ID_harvest";
$rsLdr=mysqli_query($piercecty,$ldrq);
$ldrrow=mysqli_fetch_assoc($rsLdr);
extract($ldrrow);
echo '<td>'.$fname.' '.$lname.'</td>';

$pickersq="select count(ID_picker) as pickers from rosters where ID_harvest=$ID_harvest and status<>'cancel' and status<>'absent' ";
$rsPickers=mysqli_query($piercecty,$pickersq); 
$pickersrow=mysqli_fetch_assoc($rsPickers);
extract($pickersrow);
echo '<td>'.$pickers.'</td><td>'.$totwgt.'</td></tr>';

} ?>
</table> 
</div> <!-- end main content div -->
  <br class="clearfloat" />
</div>
<?php include_once('../includes/AdminNav2.inc.php');?>  
<!-- end #container -->
</body>
</html>
