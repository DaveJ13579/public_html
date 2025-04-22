<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
include_once('../includes/converter.inc.php');
require_once('../includes/levelcheck.php');

$distyear=date('Y');
if(isset($_POST['distyear'])) $distyear=$_POST['distyear'];
$fyear=" and year(d_date)='$distyear' ";  // default filter terms

// get all distsites by date
$query = "select distsites.distsite, d_date, name from distributions, distsites where distributions.distsite=distsites.distsite $fyear group by d_date, distsites.distsite order by d_date, distsites.distsite";
//echo $query.'<br />';
$rsSites = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsSites);
//echo $numrows.'<br />';
// put all sitesdates into array for calendar
$dists=array(); $k=1;
while($distrow=mysqli_fetch_assoc($rsSites)) { 	
	$dists[$k]=$distrow; 
// print_r($distrow);echo '<br />';
	++$k;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="600">
<title>Distribution calendar</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
<style>
.poptable { margin:auto; }	
.poptable  td {border: 1px solid black; text-align: center;}
.poptable  th {border: 1px solid black;}
</style>
<script type="text/javascript">
var tempvar = null;
function popup(show){
	show.style.display="block"
	if (tempvar && (tempvar !== show)) tempvar.style.display="none"
	tempvar=show
}
</script>
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
  <div id="mainContent">
<h2>Distributions calendar</h2>
 <?php  // construct the popups for each dist ------------------------------------------
for($k=1;$k<=$numrows;++$k)  {   
extract($dists[$k]); // distsite, date and name
// get all distributions this distsite and date
$detailsq="select pounds, crops.name as cropname, harvests.ID_harvest, farm from distributions, crops, harvests, sites where sites.ID_site=harvests.ID_site and distributions.ID_harvest=harvests.ID_harvest and distributions.ID_crop=crops.ID_crop and distsite=$distsite and d_date='$d_date'";
//echo $detailsq.'<br />';
$rsDetails=mysqli_query($piercecty,$detailsq) or die(mysqli_error($piercecty));
// echo mysqli_num_rows($rsDetails); 
?>
<div class="pop" id="pop<?php echo $k; ?>">
<br /><strong><center><h2><?php echo $name.'</h2><br />'.date('l  m/d/Y',strtotime($d_date)); ?></strong></center>
<table class="poptable">
	<tr><th>Crop</th><th>Pounds</th><th>harvest</th><th>farm/owner</th></tr>	
<?php

while($detailsrow=mysqli_fetch_assoc($rsDetails)) {
$cropname=$detailsrow['cropname']; $pounds=$detailsrow['pounds']; $ID_harvest=$detailsrow['ID_harvest']; $farm=$detailsrow['farm'];
echo "<tr><td>".$cropname."</td><td>".$pounds."</td><td><a href='harvestdistribution.php?harvesttemp=".$ID_harvest." ' target='_blank'>".$ID_harvest."</a></td><td>".$farm."</td></tr>";
} 
	
?>
</table>
</div><!-- end of pop -->
<?php } // end of build popups loop---------------------------------------------------------------------
 ?>
<div id="filtersdiv">
<form action="distributioncalendar.php#<?php echo 'wk'.date('W');?>" method="POST" name="filters">
<select name="distyear">
<?php 
$yearquery="select distinct(year(d_date)) as year from distributions order by year desc";
$rsYear=mysqli_query($piercecty, $yearquery);
if($rsYear) {
	while ($yrow=mysqli_fetch_assoc($rsYear)) {
	$year=$yrow['year'];
	echo "<option value=$year ";
		if($year==$distyear) echo 'selected="selected"';
	echo ">$year</option>";
	}
}
?>
</select>
<input name="submit" type="submit" value="Select" />
</form>
</div>
  
<div id="calendar">
<?php 
$ts=strtotime("01-01-".$distyear." 6:00am"); //$ts will be the time stamp incremented by 86400 seconds each day
// find day of week of first day of year
$startday=date('w',$ts); //  
?>
<table id="caltable" align="center">
<tr>
<?php
	
$k=1; // index to dists[]
for ($d=0; $d<$startday; ++$d) echo "<td> </td>" ; // blank days for first row
	
for ($d=$startday; $d<=367; ++$d) { // $d will count  up to 365 days -----------------------------------------------------
	if($d%7==0) { // if day is Sunday, start new row and add anchor  for auto scroll to current week
		$wk=date('W',$ts)+2; if(strlen($wk)==1) $wk='0'.$wk; ?>
		</tr><tr id="<?php echo 'wk'.$wk;?>"><?php  }	
?>
<td>
<table id="cell"> 
<tr><th><?php  echo date('D, M j',$ts);?></th></tr>
<?php while ($k<=$numrows and substr($dists[$k]['d_date'],0,10)==date('Y-m-d',$ts) ) { // a dist is on this day 
?><tr onmouseover="popup(pop<?php echo $k;?>)">
<td><?php echo $dists[$k]['name']; 
++$k;
} // keep pulling rows and adding to cell table until get to a dist not on that day 
?>
</td></tr>     
</table>
&nbsp;
</td> 
<?php $ts=$ts+86400; // add another day to timestamp value

} // end of 365 days ------------------------------------------------------------------
?></tr>
</table>
</div><!-- end of calendar div----------------------------------------------- -->
</div><!-- end of mainContent -->
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<br class="clearfloat" />
</div><!-- end #container -->
</body>
</html>
