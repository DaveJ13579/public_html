<?php require_once('Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');
include_once('includes/converter.inc.php');

$harvest = "-1";
if (isset($_GET['harvesttemp'])) { $harvest = $_GET['harvesttemp']; }

$rosterquery = "SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.ID_picker, rosters.regdate, rosters.seats, rosters.status FROM rosters, pickers WHERE rosters.ID_harvest = $harvest AND pickers.ID_picker = rosters.ID_picker and rosters.status<>'cancel' and rosters.status<>'waiting' order by rosters.regdate";
$rsRoster = mysqli_query($piercecty, $rosterquery) or die(mysqli_error($piercecty));

$cancelquery = "SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.ID_picker, rosters.regdate, rosters.seats, rosters.status FROM rosters, pickers WHERE rosters.ID_harvest = $harvest AND pickers.ID_picker = rosters.ID_picker and rosters.status='cancel' order by rosters.regdate";
$rsCancel = mysqli_query($piercecty, $cancelquery) or die(mysqli_error($piercecty));

$waitingquery = "SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.ID_picker, rosters.regdate, rosters.seats, rosters.status FROM rosters, pickers WHERE rosters.ID_harvest = $harvest AND pickers.ID_picker = rosters.ID_picker and rosters.status='waiting' order by rosters.regdate";
$rsWaiting = mysqli_query($piercecty, $waitingquery) or die(mysqli_error($piercecty));

$sitequery = "SELECT * FROM sites, harvests WHERE ID_harvest = $harvest AND harvests.ID_site = sites.ID_site";
$rsSite = mysqli_query($piercecty, $sitequery);
$siterow = mysqli_fetch_assoc($rsSite);
$site=$siterow['ID_site'];
$histquery = "SELECT h_date, calcwgt, pick_num, ID_harvest from harvests WHERE harvests.ID_site=$site  order by h_date desc limit 10";
$rsHist = mysqli_query($piercecty, $histquery);

$ldrquery = "SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.address FROM harvests, pickers  WHERE ID_harvest=$harvest AND harvests.ID_leader = pickers.ID_picker";
$rsLeader = mysqli_query($piercecty, $ldrquery);
if($rsLeader) $ldrrow = mysqli_fetch_assoc($rsLeader);

$coldrquery = "SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.address FROM harvests, pickers  WHERE ID_harvest=$harvest AND harvests.ID_leader2 = pickers.ID_picker";
$rsColeader = mysqli_query( $piercecty, $coldrquery);
$coldrrow = mysqli_fetch_assoc($rsColeader);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest information and roster</title>
<style type="text/css">
<!--
.clearcell {
	background-color: #ffffff;
	border: 0px;
}
-->
</style>
<link href="database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
  <?php include_once('includes/AdminNav1.inc.php'); ?>
<div id="mainContent" style="padding-left:2em;">
  <h2 class="SH"><strong>Harvest Information and Roster</strong></h2>
  <table align="center" width="1000" border="5" cellpadding="10" cellspacing="5">
    <tr>
      <th>Harvest <?php if($harvest>0) echo $harvest;?></th>
      <th>Site</th>
      <th>Owner</th>
      <th width="25%">Site History (up to 10)</th>
    </tr>
    <tr>
      <td><strong>harvest date</strong>: <?php echo $siterow['h_date'];?><br />
	  	<strong>time</strong>: <?php echo substr($siterow['h_time'],0,5); ?><br />
        <strong>duration</strong>: <?php echo $siterow['duration']; ?><br />
        <strong>harvest status</strong>: <?php echo $siterow['status']; ?><br />
        <strong>pickers</strong>: <?php echo $siterow['pick_num']; ?><br />
        <strong>carpool</strong>: <?php echo $siterow['carpool'].', '.date('g:i A',strtotime($siterow['pooltime'])); ?><br />
			<?php 
            $ID_spot=$siterow['spot']; 
            $spotq="select name from spots where ID_spot=$ID_spot"; 
            $rsSpot=mysqli_query($piercecty,$spotq);
			if($rsSpot) {
	            $spotrow=mysqli_fetch_assoc($rsSpot);
    	        $spot=$spotrow['name']; }
			else { $spot='none'; }	
            ?>
        <strong>meeting spot</strong>: <?php echo $spot; ?><br />
        <strong>calculated weight</strong>:<?php echo $siterow['calcwgt']; ?><br />
      <td><strong>site</strong>: <?php echo $siterow['farm']; ?><br />
        <strong>address</strong>: <?php echo $siterow['address'].', '.$siterow['city']; ?><br />
        <strong>venue</strong>: <?php echo $siterow['venue']; ?><br />
         <strong>more site details</strong>: <a href ="Utilities/sitedetail.php?sitetemp=<?php echo $siterow['ID_site'];?>"><?php echo $siterow['ID_site']; ?></a></td>
     <td><strong>contact: </strong>: <?php echo $siterow['contact1']; ?><br />
        <strong>phone</strong>: <?php echo $siterow['phone1']; ?><br />
        <strong>email</strong>: <?php echo $siterow['email1']; ?><br />
	</td>
    <td>
	<?php if($rsHist) {while($histrow = mysqli_fetch_assoc($rsHist)) {
		echo '<a href="harvestroster.php?harvesttemp='.$histrow['ID_harvest'].'"<strong>'.$histrow['h_date'].'</strong></a>, '.$histrow['calcwgt'].' lbs, '.$histrow['pick_num'].' pickers<br />';		
	}} else { echo 'no previous harvests';}?>
    </td>
    </tr>
	  <td colspan="4"><strong>crops: </strong>
    <?php 
	$convarr=convarr($harvest);
	$crops='';
	foreach($convarr as $convrow) {
		$crops.=$convrow['name'].':'.$convrow['pounds'].' pounds, ';		
		}
	if($crops) {
		$crops=substr($crops,0,-1);
		echo $crops;
	}
	?>
    </td>
    <tr>
      <td colspan="4"><strong>pre-signup info</strong>: <?php echo $siterow['otherinfo']; ?></td>
    </tr>
    <tr>
      <td colspan="4"><strong>post-signup info</strong>: <?php echo $siterow['longinfo']; ?></td>
    </tr>

<tr>
      <td colspan="4"><strong>Distributed to: </strong>
	<?php
	$distlistq="select name from distributions, distsites where distributions.distsite=distsites.distsite and ID_harvest=$harvest group by name order by name";
	$rsDistlist=mysqli_query($piercecty, $distlistq);
	if(mysqli_num_rows($rsDistlist)>0) { 
	while($distrow=mysqli_fetch_assoc($rsDistlist)) { echo $distrow['name'].', ';	} 
	} else { echo 'No distributions yet.'; }
		?></td>	
</tr>
    <tr>
    </tr>
  </table>
<br /><br />
  <table  align="center" cellpadding="10" cellspacing="2" border="1" width="1000" >
    <tr><th colspan="2">Harvest Leader</th><th colspan="2">co-Leader</th></tr>
    <tr><td colspan="2"><?php echo $ldrrow['fname']; ?> <?php echo $ldrrow['lname']; ?>, <?php echo $ldrrow['phone']; ?> <?php echo $ldrrow['phone2']; ?>, <?php echo $ldrrow['email']; ?></td>
	<td colspan="2"><?php if(isset($colderrow['fname'])) { echo $coldrrow['fname']; ?> <?php echo $coldrrow['lname']; ?>, <?php echo $coldrrow['phone']; ?> <?php echo $coldrrow['phone2']; ?>, <?php echo $coldrrow['email']; } ?></td></tr><tr>
<th colspan="4">Harvest signup links</th>
</tr>
<tr>
<td colspan="4">This link will add the person to a harvest regardless of how many are already on the roster (or even if the harvest is past). It is usually used to add  leaders or selected assistants who will be at the harvest but did not get on the roster before it was filled. The link may copied and pasted to send it to volunteers who do not have database access.<table align="center"><tr><th><strong><a href="http://www.piercecountygleaningproject.org/signup.php?access=link&amp;harvesttemp=<?php echo $harvest;?>"> http://www.piercecountygleaningproject.org/signup.php?access=link&amp;harvesttemp=<?php echo $harvest;?></a></strong></th></tr></table>
</td>
</tr>
<tr>
<td colspan="4">This link will add the person to a harvest that is not 'open' on the public page. It does enforce the picker limit so that only the waiting list option is shown if the harvest is full. It is usually used for small harvests with limited roster slots that are not posted publicly and are filled by invitation. The link may copied and pasted to send it to volunteers who do not have database access.<table align="center"><tr><th><strong><a href="http://www.piercecountygleaningproject.org/signup.php?access=select&amp;harvesttemp=<?php echo $harvest;?>"> http://www.piercecountygleaningproject.org/signup.php?access=select&amp;harvesttemp=<?php echo $harvest;?></a></strong></th></tr></table>
</td>
</tr>
<tr>
  <th>Utilities: </th>
<td  align="center"><a href="Utilities/harvestupdate.php?harvesttemp=<?php echo $harvest;?>">Update harvest information</a></td>
<td  align="center"><a href="Utilities/siteupdate.php?sitetemp=<?php echo $siterow['ID_site']?>">Update site information</a></td>
<td  align="center"><a href="Utilities/waitinglist-manager.php?harvest=<?php echo $harvest; ?>">Waiting list manager</a></td>
</tr>
<tr>
<th>Harvest packets for printing: </th>
<td  align="center"><a href="harvestroster-print.php?harvesttemp=<?php echo $harvest; ?>">Standard packet</a></td>
<td  align="center"><a href="harvestroster-piercecty-packet.php?harvesttemp=<?php echo $harvest; ?>">Short packet</a></td>
<td  align="center" colspan="2"><a href="harvestroster-selectpages.php?harvest=<?php echo $harvest; ?>">Custom packet</a></td>
</tr>
</table>
<h3><strong>Roster statistics</strong></h3>
  <table  border="1"  cellpadding="10" cellspacing="2" width="900" >
    <tr>
      <?php
	
	$statusquery = "SELECT status, COUNT(status) as ctstatus FROM rosters WHERE ID_harvest = $harvest GROUP by status";
	$result = mysqli_query($piercecty, $statusquery);
	$numrows=mysqli_num_rows($result);
	$abs = 0;
	$tot = 0;
	$can = 0;
	while($row = mysqli_fetch_array($result)) 	{ 
		if($row['status']=="absent") { $abs = $abs + $row['ctstatus']; }
		if($row['status']=="cancel") { $can = $can + $row['ctstatus']; }
		$tot = $tot+ $row['ctstatus']; ?>
		<td align="center"><?php echo $row['status']."=".$row['ctstatus'] ?></td>
      <?php } 
	if(($tot-$can) > 0) { $percent = round((($tot-$abs-$can)/($tot-$can))*100); } else { $percent=0; }
	?>
    <td align="center">total signups=<?php echo $tot;?></td>
    <td align="center">attendance %=<?php echo $percent;?></td>
    <?php 
	// calculate predicted attendance based on picker histories - if there are any on roster yet
	if(($tot-$can)>0) { // skip predictions if no one on list
	$query = "SELECT ID_picker from rosters where ID_harvest = $harvest and status<>'cancel' and status<>'waiting' and ID_picker<>0";
	$rsPickers = mysqli_query($piercecty, $query);
	$signups=mysqli_num_rows($rsPickers);
	$numprevious=0; $predatt=0;
	// cycle through all pickers for this harvest
	while($pickerrow = mysqli_fetch_array($rsPickers)) {
		// clear totals array for each picker
		$attarr['absent']=0;$attarr['cancel']=0;$attarr['harvested']=0;$attarr['assisted']=0;$attarr['leader']=0;$attarr['added']=0; $attarr['waiting']=0;
	$picker=$pickerrow['ID_picker'];
	$pickquery ="SELECT status from rosters where ID_picker = $picker and status<>'signup' and status<>'waiting'";
	$rsPickatt = mysqli_query($piercecty, $pickquery);
	$numsignup = mysqli_num_rows($rsPickatt);
	// cycle through this picker's history	
	while($attendrow = mysqli_fetch_array($rsPickatt)) {
		$stat=$attendrow['status'];
		if(isset($attarr[$stat])) $attarr[$stat]++;
		} // end of all signups for this picker
	if($numsignup>0) { $numprevious++; $predatt+=($numsignup-$attarr['absent'])/$numsignup; }
	} // end of all pickers for this harvest
	$predperc=100;
	if($numprevious>0 and isset($predperc)) {$predperc=round($predatt/$numprevious*100); }
	$predshowup=round($predperc/100*$signups);
	?>
      <td align="center">predicted %= <?php echo $predperc; ?></td><td align="center">predicted showups: <?php  echo $predshowup;?></td>
    </tr>
  </table>
  <h3><strong>Active roster</strong></h3>
  <table width="1220" border="1"  cellpadding="3" cellspacing="2"id="roster">
    <tr>
      <th>Details</th>
      <th>Name</th>
      <th>Email</th>
      <th>Cell</th>
      <th>Other phone</th>
      <th>Carpool</th>
      <th>Reg Date</th>
      <th>Status</th>
      <th>Previous harvests attended</th>
    </tr>
    <?php $inc=0; $exparray[$inc]=0; 
				$seats=0;// set initial values for finding average experience
	 while ($rosterrow = mysqli_fetch_assoc($rsRoster)) {  // cycle through the roster and compile and display the experience of each picker 
	 ?>  
    <tr>
      <td class="centercell"><a href="Utilities/voldetail.php?voltemp=<?php echo $rosterrow['ID_picker']; ?>" class="bold"><?php echo $rosterrow['ID_picker']; ?></a></td>
      <td><?php echo $rosterrow['fname']; ?> <?php echo $rosterrow['lname']; ?></td>
      <td><?php echo $rosterrow['email']; ?></td>
      <td><?php echo $rosterrow['phone']; ?></td>
      <td><?php echo $rosterrow['phone2']; ?></td>
      <td><?php 
	  		if($rosterrow['seats']==0) $carpool='Driving self';
			if($rosterrow['seats']<0) $carpool='Needs a seat';
			if($rosterrow['seats']>0) $carpool='Has '.$rosterrow['seats'].' extra seats';
			echo $carpool; ?></td>
      <td class="centercell"><?php echo $rosterrow['regdate']; ?></td>
      <td class="centercell"><?php echo $rosterrow['status']; ?></td>
      <?php $pickervar_rsTotals = "-1"; // compile the experience for this picker
				if (isset($rosterrow['ID_picker'])) { $pickervar_rsTotals = $rosterrow['ID_picker']; }
				$hdate=$siterow['h_date']; // set hdate to be able to compile only harvest experience before this date
				$query_rsTotals = "SELECT ID_picker, count(ID_picker) as priorharvests FROM rosters, harvests WHERE ID_picker='$pickervar_rsTotals' and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='assisted' or rosters.status='harvested' or rosters.status='added')";
				$rsTotals = mysqli_query( $piercecty, $query_rsTotals);
				$row_rsTotals = mysqli_fetch_assoc($rsTotals); 
				$totalRows_rsTotals = $row_rsTotals['priorharvests'];
				if($rosterrow['ID_picker']<>0){  $exparray[$inc]=$totalRows_rsTotals; $inc=$inc+1;}  // skip pickers with ID = 0 ?>
      <td align="center"><?php if($row_rsTotals['ID_picker']>0) {echo $totalRows_rsTotals;} else {echo '0';} ?></td>
    </tr>
    <?php $seats+=$rosterrow['seats']; 
	}  // end of active harvest roster
	?>
	<tr><td class="clearcell" colspan="5"> </td>
    <th><?php if($seats<0) {echo 'seats needed: '.abs($seats); } else {echo 'extra seats: '.$seats; } ?></th>
    </tr>
</table>
  <h3><strong>Waiting list</strong></h3>
  <table width="1220" border="1"  cellpadding="3" cellspacing="2"id="roster">
    <tr>
      <th>Details</th>
      <th>Name</th>
      <th>Email</th>
      <th>Cell</th>
      <th>Other phone</th>
      <th>Carpool</th>
      <th>Reg Date</th>
      <th>Status</th>
      <th>Previous harvests attended</th>
    </tr>
    <?php $inc=0; $exparray[$inc]=0; // set initial values for finding average experience
	while ($waitingrow = mysqli_fetch_assoc($rsWaiting)) {  // cycle through the roster and compile and display the experience of each picker 
	 ?>  
    <tr>
      <td class="centercell"><a href="Utilities/voldetail.php?voltemp=<?php echo $waitingrow['ID_picker']; ?>" class="bold"><?php echo $waitingrow['ID_picker']; ?></a></td>
      <td><?php echo $waitingrow['fname']; ?> <?php echo $waitingrow['lname']; ?></td>
      <td><?php echo $waitingrow['email']; ?></td>
      <td><?php echo $waitingrow['phone']; ?></td>
      <td><?php echo $waitingrow['phone2']; ?></td>
      <td><?php 
	  		if($waitingrow['seats']==0) $carpool='Driving self';
			if($waitingrow['seats']<0) $carpool='Needs a seat';
			if($waitingrow['seats']>0) $carpool='Has '.$waitingrow['seats'].' extra seats';
			echo $carpool; ?></td>
      <td class="centercell"><?php echo $waitingrow['regdate']; ?></td>
      <td class="centercell"><?php echo $waitingrow['status']; ?></td>
      <?php $pickervar_rsTotals = "-1"; // compile the experience for this picker
				if (isset($waitingrow['ID_picker'])) { $pickervar_rsTotals = $waitingrow['ID_picker']; }
				$hdate=$siterow['h_date']; // set hdate to be able to compile only harvest experience before this date
				$query_rsTotals = "SELECT ID_picker, count(ID_picker) as priorharvests FROM rosters, harvests WHERE ID_picker='$pickervar_rsTotals' and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='assisted' or rosters.status='harvested' or rosters.status='added')";
				$rsTotals = mysqli_query( $piercecty, $query_rsTotals);
				$row_rsTotals = mysqli_fetch_assoc($rsTotals); 
				$totalRows_rsTotals = $row_rsTotals['priorharvests'];
				if($waitingrow['ID_picker']<>0){  $exparray[$inc]=$totalRows_rsTotals; $inc=$inc+1;}  // skip pickers with ID = 0 ?>
      <td align="center"><?php if($row_rsTotals['ID_picker']>0) {echo $totalRows_rsTotals;} else {echo '0';} ?></td>
    </tr>
    <?php }  ?>
</table>
  <h3><strong>Cancelled</strong></h3>
 <table width="1220" border="1"  cellpadding="3" cellspacing="2"id="roster">
    <tr>
      <th>Details</th>
      <th>Name</th>
      <th>Email</th>
      <th>Cell</th>
      <th>Other phone</th>
      <th>Carpool</th>
      <th>Reg Date</th>
      <th>Status</th>
      <th>Previous harvests attended</th>
    </tr>
    <?php $inc=0; $exparray[$inc]=0; // set initial values for finding average experience
	 while ($cancelrow = mysqli_fetch_assoc($rsCancel)) {  // cycle through the roster and compile and display the experience of each picker 
	 ?>  
    <tr>
      <td class="centercell"><a href="Utilities/voldetail.php?voltemp=<?php echo $cancelrow['ID_picker']; ?>" class="bold"><?php echo $cancelrow['ID_picker']; ?></a></td>
      <td><?php echo $cancelrow['fname']; ?> <?php echo $cancelrow['lname']; ?></td>
      <td><?php echo $cancelrow['email']; ?></td>
      <td><?php echo $cancelrow['phone']; ?></td>
      <td><?php echo $cancelrow['phone2']; ?></td>
      <td><?php 
	  		if($cancelrow['seats']==0) $carpool='Driving self';
			if($cancelrow['seats']<0) $carpool='Needs a seat';
			if($cancelrow['seats']>0) $carpool='Has '.$cancelrow['seats'].' extra seats';
			echo $carpool; ?></td>
      <td class="centercell"><?php echo $cancelrow['regdate']; ?></td>
      <td class="centercell"><?php echo $cancelrow['status']; ?></td>
      <?php $pickervar_rsTotals = "-1"; // compile the experience for this picker
				if (isset($cancelrow['ID_picker'])) { $pickervar_rsTotals = $cancelrow['ID_picker']; }
				$hdate=$siterow['h_date']; // set hdate to be able to compile only harvest experience before this date
				$query_rsTotals = "SELECT ID_picker, count(ID_picker) as priorharvests FROM rosters, harvests WHERE ID_picker='$pickervar_rsTotals' and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='assisted' or rosters.status='harvested' or rosters.status='added')";
				$rsTotals = mysqli_query( $piercecty, $query_rsTotals);
				$row_rsTotals = mysqli_fetch_assoc($rsTotals); 
				$totalRows_rsTotals = $row_rsTotals['priorharvests'];
				if($cancelrow['ID_picker']<>0){  $exparray[$inc]=$totalRows_rsTotals; $inc=$inc+1;}  // skip pickers with ID = 0 ?>
      <td align="center"><?php if($row_rsTotals['ID_picker']>0) {echo $totalRows_rsTotals;} else {echo '0';} ?></td>
    </tr>
    <?php }  ?>
</table>
    <?php } // end of skip predictions if no one on roster ?>
  <!-- end #mainContent -->
</div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <div id="footer">
    <!-- end #footer -->
  </div>
  <!-- end #container --></div>
</body>
</html>
<?php
((mysqli_free_result($rsRoster) || (is_object($rsRoster) && (get_class($rsRoster) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsSite) || (is_object($rsSite) && (get_class($rsSite) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsLeader) || (is_object($rsLeader) && (get_class($rsLeader) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsColeader) || (is_object($rsColeader) && (get_class($rsColeader) == "mysqli_result"))) ? true : false);
?>
