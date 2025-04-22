<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

$rangeq="select max(h_date) maxgdate, min(h_date) as mingdate from harvests";
$rsRange=mysqli_query($piercecty, $rangeq);
$rangerow=mysqli_fetch_assoc($rsRange);
$from=$rangerow['mingdate'];
$through=$rangerow['maxgdate'];

if(isset($_SESSION['fromdate']) and $_SESSION['fromdate']<>'') $from=$_SESSION['fromdate']; 
if(isset($_SESSION['throughdate']) and $_SESSION['throughdate']<>'') $through=$_SESSION['throughdate']; 

if (isset($_POST['from']) and $_POST['from']<>'') {$from = $_POST['from']; }// set date range from POST
if (isset($_POST['through']) and $_POST['through']<>'') {$through = $_POST['through'];}

$_SESSION['fromdate']=$from; $_SESSION['through']=$through;

if(isset($_POST['goto'])) {
	switch($_POST['goto']) {
		case "crops report": header("Location: statistics-crops.php"); break;
		case "farms report": header("Location: statistics-farms.php"); break;
		case "harvests report": header("Location: statistics-harvests.php"); break;
		case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST goto
$rangestring="h_date>='$from' and h_date<='$through'";

$totals=array();
$totalwgt=0;$totalharvests=0; $totaltriphours=0;
$totalsq="select type, sum(totwgt) as totwgt, count(ID_harvest) as totharvests, sum(triphours) as tottriphours from harvests where $rangestring group by type";
$rsTotals=mysqli_query($piercecty, $totalsq);
while($totalsrow=mysqli_fetch_assoc($rsTotals)) {
	extract($totalsrow); 
	if($type=='') $type='unknown';
//	echo '<pre>';print_r($totalsrow);
	$totwgtarr["$type"]=$totwgt; $totalwgt+=$totwgt;
	$totharvestsarr["$type"]=$totharvests;  $totalharvests+=$totharvests;
	$tothoursarr["$type"]=$tottriphours; $totaltriphours+=$tottriphours;
// echo $type.' wgt:'.$totwgt.' harvests:'.$totharvests.' value:'.$totvalue.'<br />';
	}
// echo ' totalwgt'.$totalwgt.' harvests:'.$totalharvests.' value:'.$totalvalue.'<br />';
$totservings=$totalwgt*4;

$farmsq="select count(distinct(ID_site)) as farms from harvests where $rangestring";
$rsFarms=mysqli_query($piercecty, $farmsq) or die(mysqli_error($piercecty));
$farmsrow=mysqli_fetch_assoc($rsFarms);
$farms=$farmsrow['farms'];

$croparr=array();
$cropsq="select ID_harvest from harvests where $rangestring";
$rsCrops=mysqli_query($piercecty, $cropsq);
while($cropsrow=mysqli_fetch_assoc($rsCrops)) {
	$ID_harvest=$cropsrow['ID_harvest'];
	$harvestcrops=convarr($ID_harvest);
	foreach($harvestcrops as $cropnames){
		$croparr[]=$cropnames['name'];
		}
}
$croparr=array_unique($croparr);
//echo '<pre>';print_r($croparr); echo '<br />';

$combos=0;
$comboq="select count(ID_harvest)  as combos, h_date, ID_site from harvests where $rangestring group by h_date, ID_site";
$rsCombo=mysqli_query($piercecty, $comboq);
while($comborow=mysqli_fetch_assoc($rsCombo)) { $combos= $comborow['combos']>1 ?  $combos+1 :  $combos;}

// Total volunteer attendance, Unique volunteers
$volsq="select count(distinct(ID_picker)) as distvols, count(ID_picker) as vols from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'cancel' and rosters.status<>'absent' and rosters.status<>'leader' and $rangestring";
$rsVols=mysqli_query($piercecty, $volsq) or die(mysqli_error($piercecty));
$volsrow=mysqli_fetch_assoc($rsVols);
$vols=$volsrow['vols'];
$distvols=$volsrow['distvols'];

// harvesting harvests
$q="select count(distinct(harvests.ID_harvest)) as harvestswith from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status='harvested' and $rangestring";
$rsQ=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
$qrow=mysqli_fetch_assoc($rsQ);
$harvestswith=$qrow['harvestswith'];

// Pounds harvested
$wgtwith=0; 
$q="select distinct(harvests.ID_harvest) as harvests, totwgt from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status='harvested' and $rangestring";
$rsQ=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
while($qrow=mysqli_fetch_assoc($rsQ)){ 	$wgtwith+=$qrow['totwgt'];}

// Total hours harvested by volunteers
$volhours=0;
$q="select count(rosters.ID_picker) as volunteers, duration from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status='harvested' and $rangestring group by harvests.ID_harvest";
$rsQ=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
while($qrow=mysqli_fetch_assoc($rsQ)){ $volhours+=$qrow['volunteers']*$qrow['duration']; } // echo '<br />volhours: '.$volhours;

// Avg. Volunteers per Field harvest
$volsq="select  count(ID_picker) as vols, count(distinct(harvests.ID_harvest)) as fharvests from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'cancel' and rosters.status<>'absent' and rosters.status<>'leader' and type='Field' and $rangestring";
$rsVols=mysqli_query($piercecty, $volsq) or die(mysqli_error($piercecty));
$volsrow=mysqli_fetch_assoc($rsVols);
$fieldvols=$volsrow['vols'];
$fharvestswith=$volsrow['fharvests'];

// Avg. Volunteers per Post-harvest harvest
$volsq="select count(ID_picker) as vols, count(distinct(harvests.ID_harvest)) as sharvests from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'cancel' and rosters.status<>'absent' and rosters.status<>'leader' and type='Post-harvest' and $rangestring";
$rsVols=mysqli_query($piercecty, $volsq) or die(mysqli_error($piercecty));
$volsrow=mysqli_fetch_assoc($rsVols);
$storagevols=$volsrow['vols'];
$sharvestswith=$volsrow['sharvests'];

// Avg. Volunteers per Pickup harvest
$volsq="select count(ID_picker) as vols, count(distinct(harvests.ID_harvest)) as pharvests  from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'cancel' and rosters.status<>'absent' and rosters.status<>'leader' and type='Pickup' and $rangestring";
$rsVols=mysqli_query($piercecty, $volsq) or die(mysqli_error($piercecty));
$volsrow=mysqli_fetch_assoc($rsVols);
$pickupvols=$volsrow['vols'];
$pharvestswith=$volsrow['pharvests'];

// Avg. Volunteer attendance
$volattend=0;
$attendq="select count(rosters.status) as harvested from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and $rangestring and rosters.status='harvested'";
$rsAttend=mysqli_query($piercecty, $attendq) or die(mysqli_error($piercecty));
$attendrow=mysqli_fetch_assoc($rsAttend);
$harvested=$attendrow['harvested'];
// echo $harvested;
$absentq="select count(rosters.status) as absent from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and $rangestring and rosters.status='absent'";
$rsAbsent=mysqli_query($piercecty, $absentq) or die(mysqli_error($piercecty));
$absentrow=mysqli_fetch_assoc($rsAbsent);
$absent=$absentrow['absent'];
if($harvested>0) $volattend=round($harvested/($harvested+$absent)*100);

// average percent signup
$signupq="select count(rosters.status)/pick_num*100 as percentsignup from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and (rosters.status='harvested' or rosters.status='absent' or rosters.status='leader' or rosters.status='assisted') and pick_num>1 and $rangestring group by harvests.ID_harvest";
$rsSignup=mysqli_query($piercecty, $signupq) or die(mysqli_error($piercecty));
$signuprowcount=mysqli_num_rows($rsSignup);
if($signuprowcount) {
	$signuptotal=0;
	while($signuprow=mysqli_fetch_assoc($rsSignup)) {
		$signuptotal+=$signuprow['percentsignup'];
		}
	$volsignup=round($signuptotal/$signuprowcount);
} // end of if any rows
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-totals</title>
<style type="text/css">
<!--
td {text-align:right;}
.left {text-align:left;}
#rightdiv {float:right; width:600px;}
#leftdiv {float:left; width:600px;}
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
    <h1>Totals report</h1>
    <p>Limit a report by date interval in  'yyyy-mm-dd' format and click 'Get report'.<br />

<form id="getdates" name="getdates" method="post" action="">
<table border="1" cellspacing="1" cellpadding="2"><tr><td><strong>From:</strong></td>
<td class="left"><input width = "10"  type="text" value="<?php echo $from; ?>" style="background-color:#ccff88" name="from" id="from" />
      (leave blank for 'earliest')
</td></tr>
<tr><td><strong>Through:</strong></td>
<td  class="left"><input width = "10"  type="text" value="<?php echo $through; ?>" style="background-color:#ccff88" name="through" id="through" />
      (leave blank for 'latest')<br />
      <input type="hidden" name="dates" value="dates" />
</td></tr>
 <tr><td colspan="2" style="text-align:center;">
 <input type="submit" name="goto" id="Submit" value="totals report" />
 <input type="submit" name="goto" id="Submit" value="distributions report" />
 <input type="submit" name="goto" id="Submit" value="farms report" />
 <input type="submit" name="goto" id="Submit" value="crops report" />
 <input type="submit" name="goto" id="Submit" value="volunteers report" />
 <input type="submit" name="goto" id="Submit" value="harvests report" />
 </td></tr>
</table>      
</form>
<br /><br />
<div id="rightdiv">
<div>

<br />
</div>
<div >
<table border="1" cellspacing="1" cellpadding="2">
<tr><th colspan="2">Volunteers</th></tr>
<tr><th>Total volunteer attendance</th><td><?php echo number_format($vols); ?></td></tr>
<tr><th>Unique volunteers</th><td><?php echo $distvols; ?></td></tr>
<tr>
  <th>Harvests with volunteers</th><td><?php echo $harvestswith; ?></td></tr>
<tr><th>Pounds harvested with volunteers</th><td><?php echo number_format($wgtwith); ?></td></tr>
<tr><th>Avg. Volunteers per trip</th><td><?php echo $harvestswith ? number_format($vols/$harvestswith,1) : 0; ?></td></tr>
<tr>
<th>Avg. Volunteers per Field harvest</th><td><?php echo $fharvestswith ? number_format($fieldvols/$fharvestswith,1) : 0; ?></td></tr>
<tr>
<th>Avg. Volunteers per Post-harvest</th><td><?php echo $sharvestswith ? number_format($storagevols/$sharvestswith,1) : 0; ?></td></tr>
<tr>
<th>Avg. Volunteers per Pickup</th><td><?php echo $pharvestswith ? number_format($pickupvols/$pharvestswith,1) : 0; ?></td></tr>
<tr><th>Avg. pounds harvested per trip with volunteers</th><td><?php echo $harvestswith ? number_format($wgtwith/$harvestswith) : 0 ;  ?></td></tr>
<tr><th>Avg. volunteer hours per trip with volunteers</th><td><?php echo $harvestswith ? number_format($volhours/$harvestswith) : 0; ?></td></tr>
<tr><th>Avg. pounds harvested per volunteer hour</th><td><?php echo $volhours ? number_format($wgtwith/$volhours) : 0; ?></td></tr>
<tr>
  <th>Avg. % attendance on harvesting harvests with volunteers:<br />
    harvested/(harvested+absent)</th><td><?php echo $volattend.'%'; ?></td></tr>
<tr>
  <th>Avg. % sign up on harvesting harvests:<br />
    (harvested+absent+leader+assisted)/pickers needed</th>
  <td><?php echo $volsignup.'%'; ?></td></tr>
<tr><th>Total Volunteer Hours</th><td><?php echo number_format($volhours);  ?></td></tr>
<tr>
  <th>Equal to how many full time staff?</th><td><?php echo number_format($volhours/2080,1) ?></td></tr>
</table>
<br />
</div>
<div ><br />
</div>
</div> <!-- end of right div -->

<div id="leftdiv">
<div >
  <table border="1" cellspacing="1" cellpadding="2">
  <tr><th><table border="1" cellspacing="1" cellpadding="2">
    <tr>
      <th>Harvesting Totals</th>
      <th>Pounds</th>
      <th>harvests</th>
    </tr>
    <tr>
      <th>TOTAL</th>
      <td><?php echo number_format($totalwgt); ?></td>
      <td><?php echo number_format($totalharvests); ?></td>
    </tr>
    <tr>
      <th>Field harvest</th>
      <td><?php echo isset($totwgtarr['Field']) ? number_format($totwgtarr['Field'])  : 0; ?></td>
      <td><?php echo isset($totwgtarr['Field']) ? number_format($totharvestsarr['Field']) : 0; ?></td>
    </tr>
    <tr>
      <th>Post-harvest</th>
      <td><?php  echo isset($totwgtarr['Post-harvest']) ? number_format($totwgtarr['Post-harvest']) : 0; ?></td>
      <td><?php echo isset($totwgtarr['Post-harvest']) ? number_format($totharvestsarr['Post-harvest']) : 0; ?></td>
    </tr>
    <tr>
      <th>Pickup</th>
      <td><?php echo isset($totwgtarr['Pickup']) ? number_format($totwgtarr['Pickup']) : 0;  ?></td>
      <td><?php echo isset($totwgtarr['Pickup']) ? number_format($totharvestsarr['Pickup']) : 0; ?></td>
    </tr>
    <tr>
      <th>Total 4 oz servings</th>
      <td><?php echo number_format($totservings); ?></td>
    </tr>
  </table></th></tr>
  </table>
  <br />
</div>
<div >

<table border="1" cellspacing="1" cellpadding="2">
<tr><th>Averages</th><th>Pounds</th></tr>
<tr><th>Per trip</th><td><?php echo number_format($totalwgt/$totalharvests); ?></td></tr>
<tr><th>Per Field harvest</th><td><?php echo number_format($totwgtarr['Field']/$totharvestsarr['Field']); ?></td></tr>
<tr><th>Per Post-harvest</th><td><?php echo isset($totwgtarr['Post-harvest']) ? number_format($totwgtarr['Post-harvest']/$totharvestsarr['Post-harvest']) :0; ?></td></tr>
<tr><th>Per Pick-up</th><td><?php echo number_format($totwgtarr['Pickup']/$totharvestsarr['Pickup']);   ?></td></tr>
</table>
<br />
</div>
<div >
<table border="1" cellspacing="1" cellpadding="2">
<tr><th colspan="2">Other Totals</th></tr>
<tr><th>Number of Farms</th><td><?php echo $farms; ?></td></tr>
<tr><th>Number of Crop Types</th><td><?php echo sizeof($croparr); ?></td></tr>
</table>
<br />
</div>
<div>
<br />
</div>
</div> <!-- end of left div -->
 </div> <!-- end main content div -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
