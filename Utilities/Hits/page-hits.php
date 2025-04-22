<?php require_once('../../Connections/piercecty.php'); 


if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change,view";


$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');
$query4="select date_format(min(whenhit),'%Y-%m-%d') as earliest from hits";
// echo $query4; exit;
$rsEarliest= mysqli_query($piercecty, $query4) or die(mysqli_error($piercecty));
$row=mysqli_fetch_assoc($rsEarliest);
$firstdate=$row['earliest'];
$lastdate=date('Y-m-d'); 

if(isset($_POST['firstdate'])) { 	$firstdate="2013-".$_POST['firstdate']; }
if(isset($_POST['lastdate'])) { 	$lastdate="2013-".$_POST['lastdate']; }

$lastdisplay=$lastdate; // keep a lastdate for display in the form before adding a day
$lastdate=strtotime($lastdate);
$lastdate=$lastdate+(60*60*24);
$lastdate=date('Y-m-d',$lastdate);	

$checked1=' checked="checked" '; $checked0="";
if(isset($_POST['raworstats'])) { 
	if($_POST['raworstats']=='raw') { $checked0=' checked="checked" '; $checked1=""; }
	}
$queryfilter='';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Page views</title>
<style type="text/css">
<!--
-->
</style>
<link href="../../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
  <?php include_once('../../includes/AdminNav3.inc.php'); ?>
<div id="mainContent">
  <h2 class="SH">Page views</h2>
  <p>NOTE: All IP addresses are squishy data because one person may have several IP addresses in rosters, and one IP address may be associated with several people.</p>

  <form action="" method="post" name="year">
    <label><input type="radio" name="raworstats" value="raw" id="raworstats_0" <?php echo $checked0; ?>/>Raw hits</label> 
    <label><input type="radio" name="raworstats" value="grouped" id="raworstats_1" <?php echo $checked1;?>/>Grouped hits</label><br  />
First date (mm-dd):
<input name="firstdate" type="text" value=<?php echo substr($firstdate,-5); ?>>
  Last date (mm-dd): <input name="lastdate" type="text" value=<?php echo substr($lastdisplay,-5); ?>><br />
  Filter by IP address: <input name="IPfilter" type="text" value="<?php if(isset($_POST['IPfilter'])) echo $_POST['IPfilter']; ?>"><br />
  <input name="daterange" type="submit" value="Show" />
  </form>

      <p>
        <?php 
if(isset($_POST['raworstats']) && $_POST['raworstats']=='raw') { // raworstats==raw

if($_POST['IPfilter']<>'') { $queryfilter=$_POST['IPfilter']; $queryfilter="and IPaddress='$queryfilter'"; }
							  	  
$query1 = "SELECT IPaddress, page, whenhit from hits where whenhit>'$firstdate' and whenhit<'$lastdate'  $queryfilter order by whenhit desc";
$result1 = mysqli_query($piercecty, $query1) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($result1); ?>
<p>There were <?php echo $numrows; ?> views of the tracked pages.</p>
<table border="1" cellspacing="1" cellpadding="1" width="700">
  <tr>
  	<th>&nbsp;</th>
	<th>IP address</th>
	<th>Name(s)</th>
	<th>Page</th>
	<th width="150">When viewed</th>
 </tr>

<?php 
$ct=1;
while($row1 = mysqli_fetch_assoc($result1)) {
		$page=$row1['page'];
		$IP=$row1['IPaddress']; $name='';
		$query5="select fname, lname from pickers where IP_picker='$IP' order by ID_picker";
		$result5 = mysqli_query($piercecty, $query5) or die(mysqli_error($piercecty));
		$numIDs=mysqli_num_rows($result5);
		if($numIDs==0) 	{$name="----";} else { 
			while ($row5=mysqli_fetch_assoc($result5)) {
			$name.=$row5['fname']." ".$row5['lname'].",";
			} 
			$name=substr($name,0,-1);
		} // end of else numIDs>0
	?>
    <tr><td><?php echo $ct;?></td><td><?php echo $IP;?></td><td><?php echo $name;?></td><td><?php echo $page;?></td><td align="center"><?php echo $row1['whenhit'];?></td></tr>
	<?php $ct=$ct+1;  } // end of while $row1 ?>
</table>
<?php 
} // end of else raworstats==raw

else { 
    // compile page hit stats
	$query4 = "select IPaddress, count(IPaddress) as hits from hits where whenhit>'$firstdate' and whenhit<'$lastdate' group by IPaddress  order by hits DESC ";	
	$result4 = mysqli_query($piercecty, $query4) or die(mysqli_error($piercecty));
	$numrows=mysqli_num_rows($result4);
	$seconds=strtotime($lastdate)-strtotime($firstdate);
	$days=$seconds/60/60/24;
?>
      </p>
      <p><?php echo $numrows; ?> IP addresses viewed the tracked pages.</p>
    <table border="1" cellspacing="1" cellpadding="1" width="600">
      <tr>
    <th>&nbsp;</th>
	<th>IP address</th>
	<th>Name</th>
	<th>Page views</th>
    <th>Views per day</th>  
  </tr>

<?php $ct=1;
	while($row4 = mysqli_fetch_assoc($result4)) {
		$IP=$row4['IPaddress']; $name='';
		$query5="select fname, lname from pickers where IP_picker='$IP' order by ID_picker";
		$result5 = mysqli_query($piercecty, $query5) or die(mysqli_error($piercecty));
		$numIDs=mysqli_num_rows($result5);
		if($numIDs==0) 	{$name="----";} else { 
			while ($row5=mysqli_fetch_assoc($result5)) {
			$name.=$row5['fname']." ".$row5['lname'].",";
			} 
			$name=substr($name,0,-1); } // end of else numids>0
		$hits=$row4['hits']; $rate=round($hits/$days,1);
	?>
    <tr><td><?php echo $ct; ?></td><td><?php echo $IP;?></td><td><?php echo $name;?></td><td><?php echo $hits;?></td><td><?php echo $rate;?></td></tr>
	<?php $ct=$ct+1; } // end of while row4 ?>
</table>
<?php 
} // end of if raworstats == grouped




/*	
	
	
	if($row['status']=="absent") { $abs = $row['ctstatus']; } // extract absent, cancel and total for calc attendance percent
	if($row['status']=="cancel") { $can = $row['ctstatus']; }
	$tot = $tot+ $row['ctstatus']; ?>
      <td align="center"><?php echo $row['status']."=".$row['ctstatus'] ?></td>
      <?php } 
	if(($tot-$can) > 0) { $percent = round((($tot-$abs-$can)/($tot-$can))*100); } else { $percent=0; }
	?>
      <td align="center">total signups=<?php echo $tot;?></td>
      <td align="center">attendance %=<?php echo $percent;?></td>
</tr></table>
      <?php 
	  $i=0;
	  while ($row = mysql_fetch_assoc($rsRoster)) { 
	  $exparray[$i]=$row['attended']; $i=$i+1;
	  }
	$i=count($exparray);
	  $result = array_count_values($exparray);
	  for ($ct=0 ; $ct<=$i-1 ; ++$ct )  {
      echo isset($result[$ct]) ? "<br />".$result[$ct]." volunteers attended: ".$ct." harvests" : "" ;
      }	
	  ?>
<img src="../../graphs/harvestroster-experience.php?mydata=<?php echo urlencode(serialize($result)); ?>" />

*/
?>
</div>
</div>
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
