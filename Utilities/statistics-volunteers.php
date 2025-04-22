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
	case "harvests report": header("Location: statistics-harvests.php"); break;
	case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST goto

$rostersq="select h_date, rosters.ID_picker, pickers.fname, pickers.lname, count(ID_rosters) as harvests, sum(seats) as netseats from rosters, harvests, pickers where rosters.ID_picker=pickers.ID_picker and harvests.ID_harvest=rosters.ID_harvest and h_date>='$from' and h_date<='$through' and rosters.status<>'absent' and rosters.status<>'cancel' group by rosters.ID_picker order by count(ID_rosters) desc";
$rsRosters=mysqli_query($piercecty, $rostersq); 

$volsq="select count(ID_picker) as vols, regdate from pickers where regdate<'$from'";
$rsVols=mysqli_query($piercecty,  $volsq);
$volsrow=mysqli_fetch_assoc($rsVols);
$fromvols=$volsrow['vols'];
$volsq="select count(ID_picker) as vols, regdate from pickers where regdate<='$through'";
$rsVols=mysqli_query($piercecty,  $volsq);
$volsrow=mysqli_fetch_assoc($rsVols);
$throughvols=$volsrow['vols'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-volunteers</title>
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
    <h1>Volunteer report</h1>
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
<br />
<div id="rightdiv">
<p><strong>Registratons</strong></p>
<table border="1" cellspacing="1" cellpadding="2">
<tr><th>Volunteers as of <?php echo $from ?></th><td align="center"><?php echo number_format($fromvols); ?></td></tr>
<tr><th>Volunteers as of <?php echo $through ?></th><td align="center"><?php echo number_format($throughvols); ?></td></tr>
<tr><th>Increase</th><td align="center"><?php echo $throughvols-$fromvols; ?></td></tr>
</table>
<br />
<p><strong>Attendance</strong></p>
    <table  border="1" cellpadding="2" cellspacing="1">
      <tr>
        <th>Roster status</th>
        <th>Count</th>
      </tr>
<?php
$query4 = "SELECT rosters.status as status, count(rosters.status) as count from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and h_date>='$from' and h_date<='$through' group by rosters.status";
$rsStatus = mysqli_query( $piercecty, $query4);
while($row = mysqli_fetch_assoc($rsStatus)){
?>
<tr><th><?php echo $row['status'] ?></th><td align="center"><?php echo $row['count'] ?></td></tr>
<?php } ?>
</table>
<p><strong>Distinct Individuals Attending</strong></p>
    <table  border="1" cellpadding="2" cellspacing="1">
      <tr>
        <th>Non-leaders</th>
        <th>Leaders</th>
        <th>Total</th>
      </tr>
<?php
$query10 = "SELECT count(distinct(rosters.ID_picker)) as distinctvols from rosters, pickers, harvests where rosters.ID_picker=pickers.ID_picker and harvests.ID_harvest=rosters.ID_harvest and h_date>='$from' and h_date<='$through' and (pickers.leader is NULL or pickers.leader<>'Yes') and rosters.status<>'absent' and rosters.status<>'cancel'";
$rsStatus = mysqli_query( $piercecty, $query10);
$row = mysqli_fetch_assoc($rsStatus);
$distinctvols=$row['distinctvols']; 
?>
<td><?php echo $distinctvols; ?></td>
<?php
$query11 = "SELECT count(distinct(rosters.ID_picker)) as distinctleds from rosters, pickers, harvests where rosters.ID_picker=pickers.ID_picker and harvests.ID_harvest=rosters.ID_harvest and h_date>='$from' and h_date<='$through' and rosters.status<>'absent' and pickers.leader='Yes' and rosters.status<>'cancel'";
$rsStatus = mysqli_query( $piercecty, $query11);
$row = mysqli_fetch_assoc($rsStatus);
$distinctleds=$row['distinctleds'];
?>
<td><?php echo $distinctleds; ?></td>
<td><?php echo $distinctleds+$distinctvols; ?></td>
</tr>
</table></div>
<div id="leftdiv">
<table border="1" cellspacing="1" cellpadding="2">
<tr>
<th>Name</th><th>harvests</th><th>Net seats</th><th>Absent</th><th>Cancel</th>
</tr>
<?php 
while($rosterrow=mysqli_fetch_assoc($rsRosters)) {
extract($rosterrow);
$absentq="select count(rosters.status) as status from rosters, harvests where harvests.ID_harvest=rosters.ID_harvest and h_date>='$from' and h_date<='$through' and ID_picker=$ID_picker and  rosters.status='absent'";
$rsAbsent=mysqli_query($piercecty, $absentq);
$absentrow=mysqli_fetch_assoc($rsAbsent);
$absent=$absentrow['status'];
?>
<tr>
<td class="left"><?php echo $lname.', '.$fname;?></td><td><?php echo $harvests;?></td><td><?php echo $netseats;?></td><td><?php echo $absent ?></td>
<?php $cancelq="select count(rosters.status) as cancel from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and ID_picker=$ID_picker and rosters.status='cancel' and h_date>='$from' and h_date<='$through' "; 
$rsCancel=mysqli_query($piercecty,$cancelq) or die(mysqli_error($piercecty));
$cancel=0;
if(mysqli_num_rows($rsCancel)) { 
	$cancelrow=mysqli_fetch_assoc($rsCancel);
	$cancel=$cancelrow['cancel']; }
?>
<td><?php echo $cancel; ?></td>
</tr>
<?php } // end all harvests
?>
</table>
<br /><br />
</div> <!-- end left div -->
  </div> <!-- end main content div -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
