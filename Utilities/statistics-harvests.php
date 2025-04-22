<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

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
	case "totals report": header("Location: statistics-totals.php"); break;
	case "farms report": header("Location: statistics-farms.php"); break;
	case "crops report": header("Location: statistics-crops.php"); break;
	case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST goto

$harvestq="select * from harvests where h_date>='$from' and h_date<='$through'";
$rsGleans=mysqli_query($piercecty, $harvestq); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-harvests</title>
<style type="text/css">
<!--
#MainContent, th, td {font-size:10px;}
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
    <h1>Glean harvests report</h1>
    <p>Limit a report by date interval in  'yyyy-mm-dd' format and click 'Get report'.<br />

<form id="getdates" name="getdates" method="post" action="">
<table border="1" cellspacing="1" cellpadding="2"><tr><td><strong>From:</strong></td>
<td  class="left"><input width = "10"  type="text" value="<?php echo $from; ?>" style="background-color:#ccff88" name="from" id="from" />
      (leave blank for 'earliest')
</td></tr>
<tr><td><strong>Through:</strong></td>
<td class="left"><input width = "10"  type="text" value="<?php echo $through; ?>" style="background-color:#ccff88" name="through" id="through" />
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
<table border="1" cellspacing="1" cellpadding="2">
<tr>
<th>Number</th><th>Date</th><th>Farm name</th><th>Branch</th><th>Type</th><th>Total weight</th><th>Leader</th><th>Co-Leader</th><th>Trip Hours </th><th>Duration</th><th>Total Volunteer labor hours</th><th>Gleaned</th><th>Absent</th><th>Leaders</th><th>Needed</th><th>Percent attendance</th><th>Round trip miles</th><th>In-kind mileage</th><th>Volunteer cars</th><th>Carpool riders</th>
</tr>
<?php 
while($harvestrow=mysqli_fetch_assoc($rsGleans)) {
extract($harvestrow);
$siteq="select farm, branch from sites, harvests where sites.ID_site=harvests.ID_site and harvests.ID_site=$ID_site";
$rsSite=mysqli_query($piercecty,$siteq) or die(mysqli_error($piercecty));
$siterow=mysqli_fetch_assoc($rsSite);
// echo '<tr><td>'.$siteq.'</td></tr>';
extract($siterow);

$leadername='';
$leaderq="select fname, lname from pickers where ID_picker=$ID_leader";
$rsLeader=mysqli_query($piercecty, $leaderq) or die(mysqli_error($piercecty));
if($rsLeader) {
	$leaderrow=mysqli_fetch_assoc($rsLeader);
	$leadername=$leaderrow['fname'].' '.$leaderrow['lname'];
}
$coleader='';
if($ID_leader2>0) {
$coleaderq="select fname, lname from pickers where ID_picker=$ID_leader2";
$rsColeader=mysqli_query($piercecty, $coleaderq);
if($rsColeader) {
	$coleaderrow=mysqli_fetch_assoc($rsColeader);
	$coleader=$coleaderrow['fname'].' '.$coleaderrow['lname'];
}
} // is a coleader
$harvested=0;$absent=0;$leader=0;
$rosterq="select status, count(status) as statuscount from rosters where ID_harvest=$ID_harvest group by status";
$rsRoster=mysqli_query($piercecty, $rosterq) or die(mysqli_error($piercecty));
while($rosterrow=mysqli_fetch_assoc($rsRoster)) {
	$status=$rosterrow['status'];
	$$status=$rosterrow['statuscount'];
	}
$attpercent= $pick_num>0 ? round($harvested/$pick_num*100) : ' ';
$volhours=$harvested*$duration;

$riders=0;
$seatsq="select count(ID_picker) as riders from rosters where seats=-1 and ID_harvest=$ID_harvest and (status='harvested' or status='leader')";
$rsRiders=mysqli_query($piercecty, $seatsq);
if($rsRiders) {
	$ridersrow=mysqli_fetch_assoc($rsRiders);
	$riders=$ridersrow['riders'];
	}
?>
<tr>
<td><?php echo $ID_harvest;?></td><td><?php echo $h_date;?></td><td><?php echo $farm;?></td><td><?php echo $branch;?></td><td><?php echo $type;?></td>
<td><?php echo $totwgt;?></td><td><?php echo $leadername;?></td><td><?php echo $coleader;?></td>
<td><?php echo $triphours;?></td><td><?php echo $duration;?></td><td><?php echo $volhours;?></td><td><?php echo $harvested;?></td>
<td><?php echo $absent;?></td><td><?php echo $leader;?></td><td><?php echo $pick_num;?></td><td><?php echo $attpercent;?></td>
<td><?php echo $miles;?></td><td><?php echo $kindmiles;?></td><td><?php echo $volcars;?></td><td><?php echo $riders;?></td> 
</tr>
<?php } // end all harvests
?>
</table>
<br /><br />
  </div> <!-- end main content div -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
