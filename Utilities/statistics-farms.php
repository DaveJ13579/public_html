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
	case "harvests report": header("Location: statistics-harvests.php"); break;
	case "crops report": header("Location: statistics-crops.php"); break;
	case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST goto
$siteq="select sites.ID_site, farm, branch, count(ID_harvest) as harvests, sum(totwgt) as totwgt from sites, harvests where sites.ID_site=harvests.ID_site and h_date>='$from' and h_date<='$through' group by harvests.ID_site order by farm";
$rsSites=mysqli_query($piercecty, $siteq) or die(mysqli_error($piercecty)); 

$totalsq="select sum(totwgt) as tottotwgt, count(ID_harvest) as totharvests from harvests where h_date>='$from' and h_date<='$through'";
$rsTotals=mysqli_query($piercecty, $totalsq) or die(mysqli_error($piercecty)); 
$totalsrow=mysqli_fetch_assoc($rsTotals);
$tottotwgt=$totalsrow['tottotwgt'];
$totharvests=$totalsrow['totharvests'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-farms</title>
<style type="text/css">
<!--
td {text-align:right;}
.left {text-align:left;}
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
    <h1>Farms report</h1>
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
<th>Farm</th><th>Branch</th><th>Total<br />harvests</th><th>Field<br />harvests</th>
<th>Food bank<br />pickups</th>
<th>piercecty<br />Pickups</th><th>Pounds<br />donated</th><th>Pounds/<br />trip</th><th>percent of<br />total pounds</th>
</tr>
<?php 
while($siterow=mysqli_fetch_assoc($rsSites)) {
extract($siterow);
$typesq="select type, count(ID_harvest) as harvestct from harvests where harvests.ID_site=$ID_site and h_date>='$from' and h_date<='$through' group by type order by type";
$rsTypes=mysqli_query($piercecty,$typesq) or die(mysqli_error($piercecty));
$Field=0;$PickupFoodbank=0;$Pickuppiercecty=0; // 3 types of harvests
while($typesrow=mysqli_fetch_assoc($rsTypes)) {
	$type=$typesrow['type']; // e.g. 'Field' 
	$harvestct=$typesrow['harvestct'];
	if($type<>'' and ($type!=NULL)) $$type=$harvestct;	// dynamic variable, e.g. $Field
}
?>
<tr>
<td class="left"><?php echo $farm;?></td><td class="left"><?php echo $branch;?></td><td><?php echo $harvests;?></td>
<td><?php echo $Field;?></td><td><?php echo $PickupFoodbank;?></td><td><?php echo $Pickuppiercecty;?></td>
<td><?php echo $totwgt;?></td>
<td><?php echo round($totwgt/$harvests);?></td>
<td><?php echo round($totwgt/$tottotwgt*100);?></td>
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
