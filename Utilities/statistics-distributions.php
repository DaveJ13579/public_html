<?php
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
require_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

if(isset($_GET['sort'])) 
	{ 
	$sort=$_GET['sort'];
	$direct=$_GET['direct'];
	if($direct=='ASC') { $direct='DESC'; } else { $direct='ASC'; }
	}
 else { $sort='pounds'; $direct='DESC'; }

switch($sort) {
	case 'name': 		$orderby='distsites.name'; 		break;
	case 'type': 	$orderby='distsitetype'; 		break;
	case 'deliveries': 	$orderby='count(ID_harvest)'; 		break;
	case 'pounds':		$orderby='sum(pounds)'; 		break;
	default:			  $orderby='sum(pounds)'; 		break;
	}

$rangeq="select max(d_date) maxgdate, min(d_date) as mingdate from distributions";
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
	case "harvests report": header("Location: statistics-harvests.php"); break;
	case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST dates form

$q="select name, distsitetype, count(ID_harvest) as deliveries, sum(pounds) as pounds from distributions, distsites where distributions.distsite=distsites.distsite and d_date>='$from' and d_date<='$through' group by distributions.distsite order by $orderby $direct";
$rsQ=mysqli_query($piercecty, $q);

$q="select distsitetype, count(ID_harvest) as deliveries, sum(pounds) as pounds from distributions, distsites where distributions.distsite=distsites.distsite and d_date>='$from' and d_date<='$through' group by distsitetype order by sum(pounds) desc ";
$rsQ2=mysqli_query($piercecty, $q) or die(mysqli_error($piercecty));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-distributions</title>
<style type="text/css">
<!--
td {text-align:right;}
.left {text-align:left;}
#rightdiv {float:right; margin-right:5%;}
#leftdiv {float:left; margin-left:5%;}
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
    <h1>Distributions report</h1>
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
<div id="rightdiv">
<table border="1" cellspacing="1" cellpadding="2">
<tr>
<th>Distribution site type</th>
<th>Deliveries</th>
<th>Total Pounds</a></th>
</tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ2)) {
extract($r); ?>
<tr><td><?php echo $distsitetype;?></td><td style="text-align:center;"><?php echo $deliveries;?></td><td><?php echo $pounds; ?></td></tr>
<?php } ?>
</table>
<br />
</div>
<div id="leftdiv">
<table border="1" cellspacing="1" cellpadding="2">
<tr>
<th><a href="statistics-distributions.php?sort=name&amp;direct=<?php echo $direct; ?>">Distribution site</a></th>
<th><a href="statistics-distributions.php?sort=type&amp;direct=<?php echo $direct; ?>">Type</a></th>
<th><a href="statistics-distributions.php?sort=deliveries&amp;direct=<?php echo $direct; ?>">Deliveries</a></th>
<th><a href="statistics-distributions.php?sort=pounds&amp;direct=<?php echo $direct; ?>">Total Pounds</a></th>
</tr>
<?php 
while($r=mysqli_fetch_assoc($rsQ)) {
extract($r); ?>
<tr><td><?php echo $name;?></td><td><?php echo $distsitetype;?></td><td style="text-align:center;"><?php echo $deliveries;?></td><td><?php echo $pounds; ?></td></tr>
<?php } ?>
</table>
<br />
</div>
 </div> <!-- end main content div -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
