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
	case "harvests report": header("Location: statistics-harvests.php"); break;
	case "volunteers report": header("Location: statistics-volunteers.php"); break;
	case "distributions report": header("Location: statistics-distributions.php"); break;
}
} // end of isset POST dates form

// find largest ID_crop number and fill a totals array with blanks and zeroes
$sizecroparrq="select max(ID_crop) as maxcrop from crops";
$rsSize=mysqli_query($piercecty,$sizecroparrq);
$sizerow=mysqli_fetch_assoc($rsSize);
$cropssize=$sizerow['maxcrop'];
for($x=0;$x<=$cropssize;++$x) {  // will total weight for each crop

	$keys=array('ID_crop','name','weight');
	$values=array(0,'',0);
	$cropsarr[$x]=array_combine($keys, $values);
}
$cropq="select ID_harvest from harvests where h_date>='$from' and h_date<='$through'";
$rsCrops=mysqli_query($piercecty, $cropq);
if(mysqli_num_rows($rsCrops)) {
while($harvestrow=mysqli_fetch_assoc($rsCrops)) {
	$ID_harvest=$harvestrow['ID_harvest'];
	$convarr=convarr($ID_harvest);
	foreach($convarr as $croprow) {
			$ID_crop=$croprow['ID_crop'];
			$cropsarr[$ID_crop]['ID_crop']=$croprow['ID_crop'];
			$cropsarr[$ID_crop]['name']=$croprow['name'];
			$cropsarr[$ID_crop]['weight']+=$croprow['pounds'];
	}
} // end of this harvest
} // end of if any harvests in interval
// echo '<pre>';print_r($cropsarr);exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Statistics-crops</title>
<style type="text/css">
<!--
td {text-align:right;}
.left {text-align:left;}
#rightdiv {float:right; margin-right:350px;}
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
    <h1>Crops report</h1>
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

</div>
<div id="leftdiv">
<table border="1" cellspacing="1" cellpadding="2">
<tr>
<th>Crop</th><th>Weight</th>
</tr>
<?php 
// trim the array
$cropsarr2=array();
foreach($cropsarr as $cropsrow) {
	if($cropsrow['name']<>'') $cropsarr2[]=$cropsrow;
}
// sort the array on name
$names=array();
foreach($cropsarr2 as $cropp) {  $names[] = $cropp['name'];}
array_multisort($names, SORT_ASC, $cropsarr2);
foreach($cropsarr2 as $cropsrow) {	
extract($cropsrow);
?>
<tr>
<td class="left"><?php echo $name;?></td><td><?php echo $weight;?></td>
</tr>
<?php 
} // end all crops
?>
</table>
<br />
</div>
 </div> <!-- end main content div -->
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
