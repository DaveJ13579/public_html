<?php 
require_once('../Connections/piercecty.php');
if(!isset($_SESSION)) session_start(); 
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>stats</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<style>
td {text-align:right; padding-left:10px;padding-right:5px;}
</style>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php'); ?>
<div id="mainContent">
<h2>Year-by-year stats</h2>
<table border="1";>
<?php
echo '<tr><th>Query</th>';
for($year='2014'; $year<=date('Y'); ++$year) { 	echo '<th>'.$year.'</th>'; }
echo '</tr>';

$qs=array();$hd=array();
// add $qs queries to make more rows. Be sure to pair $hd[] (headings) with $qs[] (queries) 
$hd[]='Distinct volunteers at harvests';
$qs[]="select year(h_date) as year, count(distinct(ID_picker)) as stat from rosters, harvests where year(h_date)>'2013' and rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'absent' and rosters.status<>'cancel' group by year(h_date) order by year(h_date)"; 
$hd[]='Distinct leaders at harvests';
$qs[]="select year(h_date) as year, count(distinct(ID_picker)) as stat from rosters, harvests where year(h_date)>'2013' and rosters.ID_harvest=harvests.ID_harvest and rosters.status='leader' group by year(h_date) order by year(h_date)"; 
$hd[]='Total roster slots';
$qs[]="select year(h_date) as year, sum(pick_num) as stat from harvests where year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 
$hd[]='Total attended';
$qs[]="select year(h_date) as year, count(rosters.status) as stat from rosters, harvests where year(h_date)>'2013' and rosters.ID_harvest=harvests.ID_harvest and rosters.status<>'cancel' and rosters.status<>'absent' group by year(h_date) order by year(h_date)"; 
$hd[]='Total absent';
$qs[]="select year(h_date) as year, count(rosters.status) as stat from rosters, harvests where year(h_date)>'2013' and rosters.ID_harvest=harvests.ID_harvest and rosters.status='absent' group by year(h_date) order by year(h_date)"; 
$hd[]='Total cancelled';
$qs[]="select year(h_date) as year, count(rosters.status) as stat from rosters, harvests where year(h_date)>'2013' and rosters.ID_harvest=harvests.ID_harvest and rosters.status='cancel' group by year(h_date) order by year(h_date)"; 
$hd[]=''; // blank row
$qs[]=''; // blank row
$hd[]='Volunteers registered each year';
$qs[]="select year(regdate) as year, count(ID_picker) as stat from pickers where year(regdate)>'2013' group by year(regdate) order by year(regdate)"; 
$hd[]='Volunteers\' most recent contact year';
$qs[]="select year(contactdate) as year, count(ID_picker) as stat from pickers where year(contactdate)>'2013' group by year(contactdate) order by year(contactdate)"; 
$hd[]=''; // blank row
$qs[]=''; // blank row
$hd[]='Total harvests';
$qs[]="select year(h_date) as year, count(ID_harvest) as stat from harvests where  totwgt>0  and year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 
$hd[]='Total harvests (to date)';
$qs[]="select year(h_date) as year, count(ID_harvest) as stat from harvests where substring(h_date,-5)<=substring(curdate(),-5) and totwgt>0  and year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 
$hd[]=''; // blank row
$qs[]=''; // blank row
$hd[]='Total weight';
$qs[]="select year(h_date) as year, sum(totwgt) as stat from harvests where totwgt>0 and year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 

$hd[]='Total  weight (to date)';
$qs[]="select year(h_date) as year, sum(totwgt) as stat from harvests where substring(h_date,-5)<=substring(curdate(),-5) and totwgt>0  and year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 
//$hd[]=''; // blank row
//$qs[]=''; // blank row
$hd[]='Sites registered each year';
$qs[]="select year(regdate) as year, count(ID_site) as stat from sites where year(regdate)>'2013' group by year(regdate) order by year(regdate)";
//$hd[]=''; // blank row
//$qs[]=''; // blank row
$hd[]='Distinct sites harvested';
$qs[]="select year(h_date) as year, count(distinct(sites.ID_site)) as stat from harvests,sites where sites.ID_site=harvests.ID_site and totwgt>0  and year(h_date)>'2013' group by year(h_date) order by year(h_date)"; 

// echo '<pre>'; print_r($hd);  print_r($qs);

// print rows 
foreach($qs as $key=>$q) { // each row
	if($hd[$key]=='') { 
		echo '<tr><th colspan="'.(date('Y')-2012).'"> </th></tr>';
		} else {
		echo '<tr><th>'.$hd[$key].'</th>';
		$query=$q;
		$rsQuery=mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
		$row=mysqli_fetch_assoc($rsQuery); // get first row
	for($yr=2014; $yr<=date('Y'); ++$yr)  { // cycle through all the years
		if(!isset($row['year']) || $yr<>$row['year']) { echo '<td> </td>'; } 
			else { echo  '<td>'.number_format($row['stat']).'</td>'; 
				$row=mysqli_fetch_assoc($rsQuery); } // get another row since have printed a year
		} // each year/column
		} // end of else do a stat row
} // each row
echo '</tr>';
?>
</table><h3>Monthly total weight</h3>
<table  border="1";>
<tr><th></th><th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th></tr>
<?php 
$totarr=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
for($yr=2017;$yr<=date('Y'); ++$yr)
{
?><tr><th><?php echo $yr; ?></th>
<?php
for($mth=1;$mth<=12;++$mth) { 
$mthwgt='';
$monthsq="select sum(totwgt) as mthwgt from harvests where year(h_date)=$yr and month(h_date)=$mth";
$rsMonths=mysqli_query($piercecty, $monthsq) or die(mysqli_error($piercecty));
$monthsrow=mysqli_fetch_assoc($rsMonths);  
echo '<td>'.number_format($monthsrow['mthwgt']).'</td>';
$totarr[$mth]+=$monthsrow['mthwgt'];
}
?>
</tr>
<?php } 
echo '<tr><th>Totals</th>';
// echo '<pre>'; print_r($totarr); exit;
unset($totarr[0]);
foreach($totarr as $mthwgt) { echo '<td>'.number_format($mthwgt).'</td>'; }
?>
</tr>
</table>
</div>
</div>
</body>
</html>
