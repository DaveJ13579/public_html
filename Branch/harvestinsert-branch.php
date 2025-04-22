<?php
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "harvestinsertform")) {  // if insert pressed

$h_date=$_POST['h_date'];
if($h_date=='') $h_date=date('Y').'-00-00';


$insertSQL = sprintf("INSERT INTO harvests (ID_site, ID_leader, ID_leader2, h_date, h_time, duration, pick_num, type, otherinfo, longinfo, gmap, status) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'Yes', %s)",
	GetSQLValueString($_POST['ID_site'], "int"),
	GetSQLValueString($_POST['ID_leader'], "int"),
	GetSQLValueString($_POST['ID_leader2'], "int"),
	GetSQLValueString($h_date, "text"),
	GetSQLValueString($_POST['h_time'], "text"),
	GetSQLValueString($_POST['duration'], "double"),
	GetSQLValueString($_POST['pick_num'], "int"),
	GetSQLValueString($_POST['type'], "text"),
	GetSQLValueString($_POST['otherinfo'], "text"),
	GetSQLValueString($_POST['longinfo'], "text"),
	GetSQLValueString($_POST['status'], "text"));
// echo $insertSQL; exit;
$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));


	
// find this harvest ID to jump to update
$q="select max(ID_harvest) as ID_harvest from harvests";
$rsQ=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
$r=mysqli_fetch_assoc($rsQ);
$ID_harvest=$r['ID_harvest'];
// echo 'max harvest: '.$ID_harvest; exit;
 header("Location: harvestupdate-branch.php?harvesttemp=$ID_harvest");
exit;
} // end of if form posted
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvest insert</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="../datepick/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"h_date",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2019,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
	};
</script>
<?php include('../includes/branchhelp.inc.php'); ?>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav-branch.inc.php');?>
<div id="mainContent">
<a href="../help/help.php"  onClick="wopen('../help/help.php', 'popup', 640, 480); return false;"><div class="branchhelp">Page help</div></a>
<h2>Add a new harvest</h2>
<p>Select a site and fill in the other information. Click on 'Insert' and the harvest will be added and you will go at once to a page where you can check that the informatioon is all correct.</p>
<form action="" id="harvestinsertform" name="harvestinsertform" method="POST">
<table  border="1" cellpadding="1" cellspacing="1"> 
<tr>
    <th>Site</th>
    <th>Leader</th>
    <th>Date<br />(yyyy-mm-dd)</th>
    <th>Harvest time</th>
<td><input type="submit" name="submit" id="submit" value="Add new harvest" /><input type="hidden" name="MM_insert" value="harvestinsertform" /></td>
</tr>
<tr class="centercell">
<td><select name="ID_site" id="sitedrop" style="width:200px;"  onfocus="hints(this)">
<option value="" selected="selected"> </option>
<?php 
	$branch=isset($_SESSION['branch']) ? $_SESSION['branch'] : 'not assigned'; 
	$branchclause=$branch=='not assigned' ? ' ' : " and branch='$branch' ";
	$sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' $branchclause order by farm";
	$rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
 while ($siterow=mysqli_fetch_assoc($rsSites)) { ?>
<option value="<?php echo $siterow['ID_site']; ?>"><?php echo $siterow['farm'].", ".$siterow['address'].", ".$siterow['city'].", ".$siterow['crops'];?></option>
<?php } ?>
</select></td>

<td><select name="ID_leader">
<option value=0> </option>
<?php 
$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
$rsLdrs=mysqli_query($piercecty,$ldrsq);
while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>
<option value="<?php echo $ldrsdrop['ID_picker']; ?>"><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
<?php } ?>
</select></td>
	
 <td><input name="h_date" type="text" id="h_date"  value="<?php // echo $row_rsharvest['h_date']; ?>" size="10" maxlength="10" /></td>
	
 <td> <select name="h_time" id="h_time">
 	<option value="00:00"> </option>
   <?php $times=mktime(7,30); 
	for($ct=1; $ct<=24;++$ct) {
	$times+=1800;	 ?>          			
<option value="<?php echo date('H:i',$times); ?>"><?php echo date('g:ia',$times);?></option>
<?php } ?>
</select></td>
</tr>
<tr>
<th>co-Leader</th>
<th>Pickers needed</th>
<th>Duration (hours)</th>
<th>Type</th>
 <th>Status</th
</tr>
<tr class="centercell">

<td><select name="ID_leader2" id="ID_leader2" onfocus="hints(this)">
<option value=''> </option>
<?php 
	$ldrsq="select fname, lname, ID_picker from pickers where leader<>'' order by lname, fname";
	$rsLdrs=mysqli_query($piercecty,$ldrsq);
	while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>
    <option value="<?php echo $ldrsdrop['ID_picker']; ?>"><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
      <?php } ?>
</select></td>
	
<td><input style="width:70px;" name="pick_num" type="number" id="pick_num" size="5" maxlength="5" /></td>
<td><input style="width:70px;" name="duration"  id="duration" size="5" maxlength="5" /></td>
<td><select style="width:100px;" name="type">
		            <option value="">-select-</option>
		            <option value="Field">Field harvest</option>
		            <option value="Post-harvest">Post-harvest harvest</option>
		            <option value="Pickup">Pickup</option>
</select></td>

<td><select name="status" id="status">
		            <option value="closed">closed </option>
            		<option value="open">open</option>
            		<option value="unsched">unsched</option>
		            </select></td>
</tr>
<tr>
</table>
<table  border="1" cellpadding="1" cellspacing="1"> 
<td align=center>Pre-signup info:</td>
<td><textarea name="otherinfo" type="textarea" cols="100" rows="2" id="otherinfo" onfocus="hints(this)" > </textarea></td></tr>
<tr>
<td align=center><p>Post-signup info:</td>
<td><textarea name="longinfo" type="textarea" cols="100" rows="2" id="longinfo" onfocus="hints(this)" > </textarea></td>
</tr>        
</table>
</form>
<br class="clearfloat" />
</div>
</div>  <!-- end #container -->
</body>
</html>
