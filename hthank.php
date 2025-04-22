<?php 
require_once('Connections/piercecty.php');
require_once('includes/dencode.inc.php'); 
require_once('includes/jobsconversion.inc.php'); 
include_once('includes/converter.inc.php');
$harvest = 0;
$ID=-1;
if (isset($_GET['ht'])) $harvest = decode($_GET['ht']); 
if (isset($_GET['pt']) && $_GET['pt']<>'') $ID = decode($_GET['pt']); 

$query_rsharvestDetail = "SELECT farm, h_date, h_time, sites.ID_site, sites.address, sites.city, duration, sites.state, sites.zip, carpool, pooltime, pickers.fname, pickers.lname, harvests.ID_leader, pickers.email, pickers.phone, harvests.otherinfo, harvests.longinfo, gmap FROM harvests, sites, pickers WHERE ID_harvest = $harvest and harvests.ID_site = sites.ID_site and harvests.ID_leader = pickers.ID_picker";

$rsharvestDetail = mysqli_query($piercecty, $query_rsharvestDetail) or die(mysqli_error($piercecty));
$harvestrow = mysqli_fetch_assoc($rsharvestDetail);
$totalRows_rsharvestDetail=mysqli_num_rows($rsharvestDetail);

$query2="select pickers.fname, substring_index(address,'#',1) as address, city, state, seats from pickers, rosters where rosters.ID_picker=pickers.ID_picker and pickers.ID_picker=$ID and rosters.ID_harvest=$harvest";
$rsPicker=mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
$row2=mysqli_fetch_assoc($rsPicker);
$tempID=0; if($row2['address']=='') $tempID=-1;
$fname=  $tempID==-1 ? " " : ", ".$row2['fname'].", ";
$origin=$row2['address']." ".$row2['city'].", ".$row2['state'];
if($row2['address']=="") $origin="501 S I St, Tacoma, WA, 98405";

$pickerq="select seats, jobs from rosters where ID_picker=$ID and ID_harvest=$harvest";
$rsPicker=mysqli_query($piercecty,$pickerq) or die(mysqli_error($piercecty));
$pickerrow=mysqli_fetch_assoc($rsPicker);
$seats=$pickerrow['seats'];
$jobstr=$pickerrow['jobs'];

$spotq="select name, address, city, state, zip from spots, harvests where harvests.spot=spots.ID_spot and ID_harvest=$harvest"; 
$rsSpot=mysqli_query($piercecty,$spotq);
if($rsSpot) {
	$spotrow=mysqli_fetch_assoc($rsSpot);
	$spotname=$spotrow['name'];
	$spotaddress=$spotrow['address'].','.$spotrow['city'].','.$spotrow['state'].' '.$spotrow['zip']; }
else { $spotname='none'; $spotaddress='no meeting spot';}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Signup thanks</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
hr { border: none; height: 2px; color: #666; background-color: #666; }
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
<?php if($totalRows_rsharvestDetail==0) { ?>
<p>I'm sorry, the link that you have used to reach this page is not correct. If you clicked on the link in an email that you received after signing up for a harvest, please make sure that the link was not broken across two lines.</p>	
	
<?php } else { ?>
      <h3>Thank you<?php echo $fname; ?> for signing up for the harvest!</h3>
    <p><span class="BigRed">P</span>lease read all instructions and print out or write down the address of the harvest. This is the only place the address is displayed on our website.</p>
    <p>The harvest that you have signed up for is at:</p>
    <h4 align="center"><?php echo $harvestrow['address']; ?>, <?php echo $harvestrow['city']; ?>, <?php echo $harvestrow['state']; ?></h4>
   <p align="center">Date: <?php echo date('m/d/Y',strtotime($harvestrow['h_date'])); ?><br />
    Time: <?php echo date('g:i A',strtotime($harvestrow['h_time'])); ?><br />Crop: 
	<?php $crops=cropstring($harvest); echo $crops; ?><br />
      <?php if(is_numeric($harvestrow['duration'])) echo 'We expect to be harvesting for '.$harvestrow['duration'].' hours.<br />'; ?>
   <p align="center">Your Harvest Leader will be: <?php echo $harvestrow['fname'].' '.$harvestrow['lname'].': '.$harvestrow['phone']; ?></p>
<p><strong>You have volunteered for the following harvest jobs:</strong>
<blockquote><?php
 	$jobtexts=jobtexts($jobstr);
 	foreach($jobtexts as $job) { echo $job.'<br />'; }?>
    </blockquote>
</p> 
     <p><em>At large harvests, parking will not begin until 15 minutes before the start of the harvest.</em></p> 
    <h3>Information specifically for this harvest: </h3>
    <p><?php echo $harvestrow['longinfo']; ?></p>
<hr />
<?php 
if($harvestrow['carpool']=='none') { ?>
<!--<p><strong>There is no carpool for this harvesting trip.</strong></p>-->
<p><strong>Driving Directions to the Farm from <?php 
if($ID==-1 || $row2['address']=="") {echo " ".$orgrow['orgname']." in ".$orgrow['city']; }
	else { echo " your address";}
$destination=$harvestrow['address']." ".$harvestrow['city']." ".$harvestrow['state']." ".$harvestrow['zip'];?></strong> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php 
if($harvestrow['gmap']=='Yes') directions($origin, $destination);
} // end of no carpool

elseif($harvestrow['carpool']=='option') { 
if($seats<>0) { ?>
<p style="text-decoration:underline;"><strong>Carpool information</strong></p>
<p><strong>There is a carpool available for this harvesting trip.</strong> </p>
<?php if($seats<0){ ?>
<p> You have indicated that you need a ride in the carpool from another harvester. Currently there is a seat for you, but if a driver cancels you will be notified by email that the seat is not longer available. Please check your email shortly before coming to the meeting spot.</p>
<?php } ?>
  	<p><strong>Meeting Spot: </strong><?php echo $spotname.', '.$spotaddress;?><br />
    <strong>Meeting Time: </strong><?php echo date('g:i A',strtotime($harvestrow['pooltime'])); ?></p>
     <p><strong>Driving Directions to the Meeting Spot from <?php 
if($ID==-1 || $row2['address']=="") {echo " ".$orgrow['orgname']." in ".$orgrow['city']; }
	else { echo " your address";}
$destination=$spotaddress;?>
</strong> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php 
directions($origin, $destination);

} // end of if seats<>0

if($seats>0) { ?>
<p>We have you as a carpool driver for this trip, thank you! Please print out the directions below and write down the harvest leader’s cell phone number (see above).  We will go over the directions at the meeting spot, and you will be able to follow the harvest leader to the farm.</p>

<p><strong>Driving Directions to the Farm from the Meeting Spot:</strong>
<?php 
$origin=$spotaddress;
$destination=$harvestrow['address']." ".$harvestrow['city']." ".$harvestrow['state']." ".$harvestrow['zip'];
?> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php if($harvestrow['gmap']=='Yes') directions($origin, $destination);  ?>  
</p>
<?php } // end of signed up to drive
if($seats==0) { ?>
<p>You have chosen to drive directly to the farm.</p>
 <p><strong>Driving Directions to the Farm from <?php 
if($ID==-1 || $row2['address']=="") {echo " ".$orgrow['orgname']." in ".$orgrow['city']; }
	else { echo " your address";}
$destination=$harvestrow['address']." ".$harvestrow['city']." ".$harvestrow['state']." ".$harvestrow['zip'];?></strong> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php 
if($harvestrow['gmap']=='Yes') directions($origin, $destination);
} // end of driving solo
} // end of optional carpool
	
else { // remaining possibility is only 'all' carpool ?>
<p style="text-decoration:underline;"><strong>Carpool information</strong></p>
<p><strong>All vehicles must travel together to this harvest.</strong></p>
 <?php if($seats<0) { ?>
<p>You have indicated that you need a ride in the carpool from another harvester. Currently there is a seat for you, but if a driver cancels you will be notified by email that the seat is not longer available. Please check your email shortly before coming to the meeting spot.</p>
<?php } ?>
 <?php if($seats>0) { ?>
<p>We have you as a carpool driver for this trip, thank you! Please print out the directions below and write down the harvest leader’s cell phone number (see below). We will go over the directions at the meeting spot, and you will be able to follow the harvest leader to the farm.</p>
<?php } ?>
  	<p><strong>Meeting Spot: </strong><?php echo $spotname.', '.$spotaddress;?><br />
    <strong>Meeting  Time: </strong><?php echo date('g:i A',strtotime($harvestrow['pooltime'])); ?></p>
     <p><strong>Driving Directions to the Meeting Spot from <?php 
if($ID==-1 || $row2['address']=="") {echo " Tacoma:"; }
	else { echo " your address";}
$destination=$spotaddress;?>
</strong> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php 
directions($origin, $destination);
?>
<p><strong>Driving Directions to the Farm from the Meeting Spot:</strong>
<?php 
$origin=$spotaddress;
$destination=$harvestrow['address']." ".$harvestrow['city']." ".$harvestrow['state']." ".$harvestrow['zip'];
?> (<a href="https://maps.google.com/maps?f=d&saddr=<?php echo $origin;?>&daddr=<?php echo $destination;?>" target="_blank">Google Maps</a>):</p>
<?php if($harvestrow['gmap']=='Yes') directions($origin, $destination);

 } // end of all carpool
 ?> 
 <hr />
<p>If you have questions about registering as a volunteer or signing up for harvesting trips, contact the webmaster at: <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.
    <p>If you have never registered as a volunteer, please go as soon as possible to the <a href="pickerinsert.php">volunteer registration page.</a></p>
     <h3>Important Notes:</h3>
    <p>If you have questions about this harvest, contact the Harvest Leader, <?php echo $harvestrow['fname']; ?> <?php echo $harvestrow['lname']; ?> at: <a href="mailto:<?php echo $harvestrow['email']; ?>"><?php echo $harvestrow['email']; ?></a> or <?php echo $harvestrow['phone']; ?>.</p>
    <p>You will find useful information about  harvests on the <a href="FAQ.php">Frequently Asked Questions</a> page.</p>
    <p>If you have questions about registering  as a volunteer or signing up for harvests, contact the webmaster at: <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
<p>&nbsp;</p>
   <?php } ?>
<p><a href="http://www.piercecountygleaningproject.org" class="indent">Return to Home Page</a></p>
<p></p>
<!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
<?php 
function directions($origin, $destination) {

$txt=""; 
$apicall="http://maps.googleapis.com/maps/api/directions/xml?origin=".$origin."&destination=".$destination."&sensor=false";
$xml = simplexml_load_file($apicall);
$directions = json_decode(json_encode((array) $xml),1);
if($directions['status']=='OK') {
$steps=count($directions['route']['leg']['step']);
$txt=""; 
if(isset($directions['route']['leg']['step'][0])) { // needed in case origin is near destination and so no steps
	for ($i=0 ; $i<$steps ;++$i) { 
	$txt.=strip_tags($directions['route']['leg']['step'][$i]['html_instructions']);
	$txt.=" and go ".strip_tags($directions['route']['leg']['step'][$i]['distance']['text']).".<br />";
} // end of for loop
} // end of if (isset($directions))
// extract the misplaced 'destination will be on the ...'
$pos=strpos($txt,'Destination will be',0);
if($pos>0) {
	$len=strlen($txt);
	$txt=substr($txt,0,$pos)." ".substr($txt,$pos+32)."<br />".rtrim(substr($txt,$pos,32)).".";}
// find 'Continue' not preceded by a space and insert break
$pos=strpos($txt,'Continue ',0);
if($pos>0 and substr($txt,$pos-1,1)<>' ') {
	$txt=substr($txt,0,$pos-1)."<br />".substr($txt,$pos); }
echo $txt; } // end of if status OK 
if(strlen($txt)<20) echo 'Directions not found'; 
} // end of directions function
?>
