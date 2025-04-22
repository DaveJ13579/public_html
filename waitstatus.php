<?php 
require_once('Connections/piercecty.php'); 
require_once('includes/dencode.inc.php');
include_once('includes/converter.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Waiting List Status</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7;} 
-->
</style>
</head>

<body class="SH">

<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
<?php
 
$IDpicker=''; if (isset($_GET['ID'])) { $eIDpicker =$_GET['ID'];
$IDpicker=decode($_GET['ID']); }

if($IDpicker>0 && $IDpicker<99999) { // if valid picker number

$namequery="select fname from pickers where ID_picker=$IDpicker";
$rsName = mysqli_query($piercecty, $namequery) or die(mysqli_error($piercecty));
$rowname=mysqli_fetch_assoc($rsName);
$fname=$rowname['fname'];

// find all waiting harvests for IDpicker
$query= "SELECT pickers.fname, rosters.ID_harvest, h_date,  h_time, harvests.otherinfo as info, sites.branch as area, rosters.regdate as regdate FROM harvests, sites, rosters, pickers WHERE harvests.ID_harvest=rosters.ID_harvest and harvests.ID_site=sites.ID_site and rosters.ID_picker=pickers.ID_picker and rosters.ID_picker=$IDpicker and rosters.status='waiting'";
$rsHarvest = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsHarvest);

echo "<p>$fname, you are currently on the waiting list for $numrows ";
if($numrows==1) { echo "harvest.</p>"; } else { echo "harvests.</p>"; }

if($numrows>0)  { // there are harvests to list
?>

<p>You will be sent an email if there are enough cancellations so that you are moved up to the actual roster of a harvest. That email will have the address and directions for the harvest. Because you are now on the waiting list, you do not need to check the Harvests page. However, you should check your email before the harvest to see if you have been added to the actual roster.</p>

<?php  

if($numrows>1) { echo "<p>The harvests are listed below with links that you can use to cancel your spot on the waiting list.<p/>"; }
		else { echo "<p>The harvest is listed below with a link that you can use to cancel your spot on the waiting list.</p>"; }
?>
<table border="4" cellspacing="5" cellpadding="3" align="center">
<tr>
<th align="center">Position on the<br />waiting list</th>
<th align="center">Link to cancel<br />waiting list spot</th>
<th align="center">Crop</th>
<th align="center">Harvest date</th>
<th align="center">Time</th>
<th>Information</th>
</tr>

<?php 
while($row = mysqli_fetch_assoc($rsHarvest)) { // cycle through harvests picker is on wait list for

$hdate=$row['h_date'];
$harvest=$row['ID_harvest'];
$regdate=$row['regdate'];
	// count pickers ahead on waiting list for each harvest
	$query2="select count(ID_picker) as ahead from rosters where ID_harvest=$harvest and status='waiting' and regdate<'$regdate'";
	$Result2 = mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
	$row2=mysqli_fetch_assoc($Result2);
	$place=$row2['ahead']+1;
?>

<tr>
<td align="center"><?php echo $place; ?></td>
<?php if($hdate>=date('Y-m-d')) {  ?>
	<td align="center"><a href="cancel.php?ID=<?php echo $eIDpicker; ?>&amp;h=<?php echo $harvest; ?>">Cancel</a></td>
	<?php } else { ?> 
    <td align="center">---</td> <?php } ?>
<td align="center"><?php $crops=cropstring($harvest); echo $crops;?></td>
<td align="center"><?php echo date('l, M d',strtotime($row['h_date']));?></td>
<td align="center"><?php  echo date('g:i A',strtotime($row['h_time'])); ?></td>
<td><?php echo $row['area']."<br />".$row['info'];?></td>
</tr>
<?php  
} // end of while cycle through harvests 
?>
</table>
<?php 
}  // end of if harvests to list 

} // end of if valid picker number

else { // invalid picker number
?> <p>The volunteer could not be identified.</p>
<?php } // end of could not be identified
?>

<p>If you have questions about registering as a volunteer or signing up for harvests, contact the webmaster at: <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
<p>&nbsp;</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p></p>
<!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
