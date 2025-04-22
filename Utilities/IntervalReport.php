<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');

$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$from=""; $through=""; $siteclause=''; $ID_site=''; $cropclause=''; $ID_crop='';
if (isset($_POST['dates'])) { // set date range from POST
	$from = $_POST['from']; $through = $_POST['through'];  
	if ($from=="") {$from="2010-01-01"; }
	if ($through=="") {$through="2030-01-01"; }
if($_POST['sitedrop']<>'') { 
	$ID_site=$_POST['sitedrop'];
	$siteclause=" and sites.ID_site=$ID_site ";
	$siteq="select farm from sites where ID_site=$ID_site";
	$rsSite=mysqli_query($piercecty, $siteq);
	$siterow=mysqli_fetch_assoc($rsSite);
	$sitename= mysqli_num_rows($rsSite) ? $siterow['farm'] : 'All sites';
	}
if($_POST['cropdrop']<>'') { 
	$ID_crop=$_POST['cropdrop'];
	$cropclause=" and (crop1=$ID_crop or crop2=$ID_crop or crop3=$ID_crop or crop4=$ID_crop or crop5=$ID_crop or crop6=$ID_crop or crop7=$ID_crop or crop8=$ID_crop or crop9=$ID_crop or crop10=$ID_crop) ";
	$nameq="select name from crops where ID_crop=$ID_crop";
	$rsName=mysqli_query($piercecty, $nameq);
	$namerow=mysqli_fetch_assoc($rsName);
	$cropname= mysqli_num_rows($rsName) ? $namerow['name'] : 'All crops';
	}
} // end of isset POST
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Interval Report</title>
<style type="text/css">
<!--
.SH #container #left {
	float: left;
	width: 300px;
	margin-left: 0px;
}
.SH #container #right {
	float: right;
	width: 400px;
	margin-right: 10px;
}
.SH #container #center {
	float: right;
	width: 400px;
	margin-right: 30px;
}
#mainContent2 {
	padding: 5px;
	width: 800px;
	overflow: scroll;
	background-color: #FFF;
}
.SH #container #mainContent2 #getdates { 	
margin-left: 50px; 
}
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="../datepick/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"from",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2009,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
		new JsDatePick({
			useMode:2,
			target:"through",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2009,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
	};
</script>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h1>Dates, sites and crops reports</h1>
    <p>Limit a report by date interval in  'yyyy-mm-dd' format, Site or 'All sites,' Crop or 'All crops,' and click 'Get report'.</p>

      <form id="getdates" name="getdates" method="post" action="IntervalReport.php">
<table border="1" cellspacing="1" cellpadding="2"><tr><td><strong>From:</strong></td>
<td><input width = "10"  type="text" value="<?php echo $from; ?>" style="background-color:#ccff88" name="from" id="from" />
      (leave blank for 'earliest')
</td></tr>
<tr><td><strong>Through:</strong></td>
<td><input width = "10"  type="text" value="<?php echo $through; ?>" style="background-color:#ccff88" name="through" id="through" />
      (leave blank for 'latest')<br />
      <input type="hidden" name="dates" value="dates" />
</td></tr>
<tr><td><strong>Site:</strong></td>
<td><select name="sitedrop" style="background-color:#ccff88" >
 	     <option value=""  selected="selected">-- All sites --</option>
         <?php $sitesq="select ID_site, farm, address from sites order by farm";
		  			  $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
					  while ($siterow=mysqli_fetch_assoc($rsSites)) {
						?><option value="<?php echo $siterow['ID_site']; ?>" <?php if($siterow['ID_site']==$ID_site) echo 'selected="selected"';?>><?php echo $siterow['farm'].' - '.$siterow['address'];?> </option>
	 				   <?php } ?>
      </select>
 </td></tr>
<tr><td><strong>Crop:</strong></td>
<td><select name="cropdrop" style="background-color:#ccff88" >
 	     <option value=""  selected="selected">-- All crops --</option>
         <?php $cropsq="select ID_crop, name from crops order by name";
		  			  $rsCrops=mysqli_query($piercecty, $cropsq) or die(mysqli_error($piercecty));
					  while ($croprow=mysqli_fetch_assoc($rsCrops)) {
						?><option value="<?php echo $croprow['ID_crop']; ?>" <?php if($croprow['ID_crop']==$ID_crop) echo 'selected="selected"';?>><?php echo $croprow['name'];?> </option>
	 				   <?php } ?>
      </select>
 </td></tr>
 <tr><td colspan="2" style="text-align:center;"><input type="submit" name="Submit" id="Submit" value="Get report" /></td></tr>
</table>      
     </form>
<div id="right">
<h1>Crops</h1>
<table border="1" cellspacing="1" cellpadding="2">
      <tr>
        <th>Crop</th>
        <th>Harvests</th>
        <th>Pounds</th>
      </tr>
<?php 
$query3 = "SELECT * from harvests, sites where harvests.ID_site=sites.ID_site and h_date>'$from' and h_date<'$through' $siteclause $cropclause "; 
$rsCrops = mysqli_query($piercecty, $query3) or die(mysqli_error($piercecty));
$sumarr=array(); //[cropname] [total harvests, total pounds]

while($harvestrow = mysqli_fetch_assoc($rsCrops)) { 
	$convarr=convarr($harvestrow['ID_harvest']);
	foreach($convarr as $convrow) {
		if($convrow['ID_crop']==$ID_crop or $ID_crop=='') {
		$name=$convrow['name'];
		@++$sumarr["$name"]['harvests'];
		@$sumarr["$name"]['pounds']+=$convrow['pounds'];
		} // end of if crop list is restricted by $ID_crop
	} // end of all convarr rows for this harvest
} // end of all harvests
ksort($sumarr);
$cx=0;
$prodname= array_keys($sumarr);
foreach($sumarr as $sumout) { 	?>
       <tr>
        <td><?php  echo @$prodname[$cx]; ?></td>
        <td><?php echo @$sumout['harvests'];?></td>
        <td><?php echo @$sumout['pounds'];?></td>
      </tr>
 <?php  ++$cx;  } ?>
    </table>
    </div> <!-- end right div -->
 <div id="center">
<h1>Harvests</h1>
<table border="1" cellspacing="1" cellpadding="2">
      <tr>
        <th>Site</th>
        <th>Harvests</th>
        <th>Pounds</th>
      </tr>
	<?php
	$query4 = "SELECT sites.ID_site, farm, count(harvests.ID_site) as 'times' from harvests, sites where harvests.ID_site=sites.ID_site and h_date>'$from' and h_date<'$through' $siteclause $cropclause  group by farm"; 
	$rsHarvests = mysqli_query($piercecty, $query4) or die(mysqli_error($piercecty));
	while($row4=mysqli_fetch_assoc($rsHarvests)) {  
	$site= $row4['farm'];
	$ID_site=$row4['ID_site'];?>
	<tr>
    	<td><?php echo $site;?></td>
        <td><?php echo $row4['times'];?></td>
        <td>
        <?php	$query5="SELECT * from harvests, sites where sites.ID_site=harvests.ID_site and sites.ID_site=$ID_site and h_date>'$from' and h_date<'$through' $cropclause"; 
//		echo $query5;
		$rsQuery5=mysqli_query($piercecty,$query5) or die(mysqli_error($piercecty));
		$pounds=0;
		while($row5=mysqli_fetch_assoc($rsQuery5)) {
			$convarr=convarr($row5['ID_harvest']);
			for($ct=1; $ct<=10; ++$ct) {
				if($ID_crop) { // if specific crop then compile only pounds of that crop
					$pounds+= isset($convarr[$ct]['wgt']) && $convarr[$ct]['crop']==$ID_crop ? $convarr[$ct]['wgt']: 0;
				} else { // compile all pounds 
					$pounds+= isset($convarr[$ct]['wgt']) ? $convarr[$ct]['wgt']: 0;
					}
			} // end of while convarr
		} // end of while rows of harvests of this site
		echo $pounds; ?></td>
     </tr>		
	 <?php } ?>    
</table>
  </div> <!-- end of center div -->
<div id="left">
    <h1>Totals</h1>
<table  border="1" cellspacing="1" cellpadding="2">
  <tr>
    <th>Venue</th>
    <th>Harvests</th>
    <th>Calculated<br />weight</th>
    <th>Total<br />weight</th>
    <th>Donated<br />weight</th>
    </tr>
<?php 
$query1 = "SELECT venue, COUNT(ID_harvest) as count, sum(totwgt) as 'totwgt', sum(calcwgt) as 'calcwgt', sum(donwgt) as 'donwgt' FROM harvests, sites  WHERE h_date>'$from' and h_date<'$through' and harvests.ID_site=sites.ID_site $siteclause $cropclause  group by sites.venue";
$rsCoords = mysqli_query($piercecty, $query1) or die(mysqli_error($piercecty));
$totweight=0; $totyield=0; $totharvs=0;
while($row = mysqli_fetch_assoc($rsCoords)) { 
?>
  <tr>
    <td><?php echo $row['venue']; ?></td>
    <td align="center"><?php echo $row['count']?></td>
    <td align="center"><?php echo $row['calcwgt']?></td>
    <td align="center"><?php echo $row['totwgt']?></td>
    <td align="center"><?php echo $row['donwgt']?></td>
    </tr>
 <?php } ?> 
</table>

    <h1>Leaders</h1>
    <table  border="1" cellspacing="1" cellpadding="2">
      <tr>
        <th scope="col">Leader or Co-leader</th>
        <th scope="col">Harvests</th>
      </tr>      
<?php 
$query2 = "SELECT pickers.fname as fname, pickers.lname as lname, COUNT(ID_harvest) as count FROM harvests, pickers, sites WHERE h_date>'$from' and h_date<'$through' and (harvests.ID_leader=pickers.ID_picker or harvests.ID_leader2=pickers.ID_picker) and harvests.ID_site=sites.ID_site $siteclause $cropclause  group by pickers.ID_picker order by pickers.lname";
$rsLeads = mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
while($row = mysqli_fetch_assoc($rsLeads)) { ?>
      
      <tr>
        <td><?php echo $row['fname']." ".$row['lname']; ?></td>
        <td align="center"><?php echo $row['count']?></td>
      </tr>
<?php } ?>      
    </table>
    <h1>Attendance</h1>
    <table  border="1" cellpadding="2" cellspacing="1">
      <tr>
        <th>Roster status</th>
        <th>Count</th>
      </tr>
<?php
$can=0; $abs=0; $tot=0;
$query4 = "SELECT rosters.status as status, count(rosters.status) as count from rosters, harvests, sites where rosters.ID_harvest=harvests.ID_harvest and h_date>'$from' and h_date<'$through' and harvests.ID_site=sites.ID_site $siteclause $cropclause group by rosters.status";
$rsStatus = mysqli_query($piercecty, $query4) or die(mysqli_error($piercecty));
while($row = mysqli_fetch_assoc($rsStatus)) { 
$tot+=$row['count'];
if($row['status']=="cancel") {$can=$row['count'];}
if($row['status']=="absent") {$abs=$row['count'];}
?>
      <tr>
        <td><?php echo $row['status'] ?></td>
        <td align="center"><?php echo $row['count'] ?></td>
      </tr>
<?php } ?>
	  <tr>
        <td>Total signups</td>
        <td align="center"><?php echo $tot;?></td>
      </tr> 
	  <tr>
      	 <td>Attended</td>
      	 <td align="center"><?php 
			if($tot > 0) { $percent = round((($tot-$abs-$can)/($tot-$can))*100); } else { $percent=0;}
			echo $percent." percent";?></td>
	  </tr>
    </table>
 </div>
  </div> <!-- end main content div -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
