<?php 
require_once('Connections/piercecty.php');
require_once('includes/dencode.inc.php');
include_once('includes/converter.inc.php');
require_once('includes/smtpmailer.inc.php');

$IDpicker=''; $harvest=-1; $seats=0; $switch='noton'; $status='';
if (isset($_GET['ID'])) { $eIDpicker =$_GET['ID']; $IDpicker=decode($_GET['ID']); } 
	else {$switch='nopickerget';}
if($IDpicker<1 || $IDpicker>99999) $switch='nopicker';
if (isset($_GET['h']))  $harvest = decode($_GET['h']); 
if($harvest<=0) $switch='noharvest';
if (isset($_GET['s']))  $seats = $_GET['s']; 

// check harvest date (cannot get on waiting list for past harvest)
$datequery="select h_date, h_time from harvests where ID_harvest=$harvest";
$rsDate = mysqli_query($piercecty, $datequery) or die(mysqli_error($piercecty));
$daterow=mysqli_fetch_assoc($rsDate);
$countdaterow=mysqli_num_rows($rsDate);

if($countdaterow==0) { $switch='noharvest'; } 
	else { if($daterow['h_date']." ".$daterow['h_time']<date('Y-m-d H:i:s')) $switch='pastharvest'; }

if($switch=='noton') { // okay to look up picker
$query="select fname, IP_picker, email from pickers where ID_picker=$IDpicker";
$Result1 = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows=mysqli_num_rows($Result1);
if($numrows<>1) $switch='nopicker';
$row=mysqli_fetch_assoc($Result1);
$fname=$row['fname']; 
$IPpicker=$row['IP_picker'];
if(isset($_SERVER["REMOTE_ADDR"])) $IPpicker = $_SERVER["REMOTE_ADDR"]; 
 $email=$row['email'];
} // end of okay to look up picker

if($switch=='noton')  { // okay to check if on roster already
$rosterquery="select count(ID_picker) as onroster, status from rosters where ID_picker=$IDpicker and ID_harvest=$harvest";
$Result3 = mysqli_query($piercecty, $rosterquery) or die(mysqli_error($piercecty));
$row3=mysqli_fetch_assoc($Result3);
if($row3['onroster']>0) {$status=$row3['status']; $switch='alreadyon'; }
} // end of  okay to check if on roster already

// if already on roster as cancel then change status to waiting
if($status=='cancel') {
$changeSQL="update rosters set status='waiting', IPaddress='$IPpicker', seats=$seats, regdate=now() where ID_picker=$IDpicker and ID_harvest=$harvest";
$Result4 = mysqli_query($piercecty, $changeSQL) or die(mysqli_error($piercecty));
} // end of change status to waiting

if($switch=='noton') { //okay to insert into roster as waiting
$insertSQL = "INSERT into rosters (ID_harvest, regdate, ID_picker, seats, status, IPaddress) VALUES ($harvest, now(), $IDpicker, $seats, 'waiting', '$IPpicker')";
$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));

$IPquery="update pickers set IP_picker='$IPpicker' where ID_picker=$IDpicker"; // update the picker IP address from the roster signup IP address
$Result2 = mysqli_query($piercecty, $IPquery) or die(mysqli_error($piercecty));
$contactquery="update pickers set contactdate=now() where ID_picker=$IDpicker"; // update the picker's date of most recent contact
$Result3 = mysqli_query($piercecty, $contactquery) or die(mysqli_error($piercecty));

}// end of okay to insert

if($switch=='noton' || $status=='waiting' || $status=='cancel') { // send or resend email confirmation
	$subject = "Pierce County Gleaning Project roster status";
	$statuslink="http://www.piercecountygleaningproject.org/waitstatus.php?ID=".$eIDpicker;
	$message = 'Hello '.$fname.','."\n\n".'You have been added to the waiting list of a harvest sponsored by Pierce County Gleaning Project. ';
	$message .= 'You will be sent an email if there are enough cancellations so that you are moved up to the actual roster. ';
	$message .= 'That email will have the address and directions for the harvest. Because you are now on the waiting list, ';
	$message .= 'you do not need to check the harvests page. You should:'."\n\n";
	$message .= '- Check your email before the harvest to see if you have been added to the actual roster'."\n\n";
	$message .= '- Check the following web page to see your position on the waiting list:'."\n\n";
	$message .= $statuslink."\n\n";
	$message .= 'If you are on the waiting list because you need a ride in the carpool, but then find that you can drive, simply go back to the Harvests page and sign up again. If there are still harvest roster slots available you can get directly on the roster.';
	if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
} // end of send or resend email

// get harvest details
$query2= "SELECT h_date,  h_time, harvests.otherinfo as info FROM harvests, sites WHERE ID_harvest=$harvest and harvests.ID_site = sites.ID_site";
$rsharvest = mysqli_query($piercecty, $query2) or die(mysqli_error($piercecty));
$row2 = mysqli_fetch_assoc($rsharvest);
$numrows2 = mysqli_num_rows($rsharvest);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Confirm waiting list</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7;} 
-->
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">

<?php if($switch=='noton' || $status=='cancel') { // okay to show message 
  ?>
  <p><?php echo $fname; ?>, you have been added to the waiting list for the harvest listed below. You will be sent an email if there are enough cancellations so that you are moved up to the actual roster. That email will have the address and directions for the harvest.</p>
  <p>Because you are now on the waiting list, you do not need to check the Harvests page. You should:</p>
  <p>- Check your email before the harvest to see if you have been added to the actual roster.</p>
  <p>- Check the <a href="<?php echo 'waitstatus.php?ID='.$eIDpicker;?>">waiting status</a> page to see your position on the waiting list. This link is also in the email that you have just been sent. </p>
<p>If you are on the waiting list because you need a ride in the carpool, but then find that you can drive, simply go back to the <a href="harvestlist.php">Harvests</a> page and sign up again. If there are still harvest roster slots available you can get directly on the roster.</p>
  <table border="1" cellspacing="5" cellpadding="5" align="center">
    <tr>
    <td>Picking: <?php $crops=cropstring($harvest); echo $crops ?></td>
    <td>Date: <?php echo date('l  m/d/Y',strtotime($row2['h_date'])); ?></td>
    <td>Time: <?php echo date('g:i A',strtotime($row2['h_time'])); ?></td>
  </tr>
  <tr>
    <td colspan="3"><?php echo $row2['info']; ?></td>
  </tr>
</table>
<?php } // end of okay to show instructions

elseif($status=='waiting') { // display already on waiting list
?>
  <p><?php echo $fname; ?>, you are already on the waiting list for the harvest listed below. You will be sent an email if there are enough cancellations so that you are moved up to the actual roster. That email will have the address and directions for the harvest.</p>
  <p>Because you are now on the waiting list, you do not need to check the harvesting Trips page. You should:</p>
  <p>- Check your email before the harvest to see if you have been added to the actual roster.</p>
  <p>- Check the <a href="waitstatus.php?ID=<?php echo $eIDpicker;?>">waiting status</a> page to see your position on the waiting list. This link is also in the email that you have just been sent. </p>
<p>If you are on the waiting list because you need a ride in the carpool, but then find that you can drive, simply go back to the <a href="harvestlist.php">Harvests</a> page and sign up again. If there are still harvest roster slots available you can get directly on the roster.</p>
  <table border="1" cellspacing="5" cellpadding="5" align="center">
    <tr>
    <td>Picking: <?php $crops=cropstring($harvest); echo $crops ?></td>
    <td>Date: <?php echo date('l  m/d/Y',strtotime($row2['h_date'])); ?></td>
    <td>Time: <?php echo date('g:i A',strtotime($row2['h_time'])); ?></td>
  </tr>
  <tr>
    <td colspan="3"><?php echo $row2['info']; ?></td>
  </tr>
</table>
<?php } // end of display already on waiting list

elseif($switch=='alreadyon') { // already on roster but not as cancel
?><p>You are already on the roster for this harvest.</p>
<?php
} // end of already on roster

elseif($switch=='noharvest') { // harvest number not found
?><p>The harvest could not be identified.</p>
<?php
} // end of harvest number not found

elseif($switch=='nopicker' || $switch=='nopickerget') { // no picker id ded
?><p>The identification number for the volunteer is missing or corrupted.</p>
<?php } // end of no picker

elseif($switch=='pastharvest') { // past harvest
?><p>You cannot be added to the waiting list of a harvest that is over.</p>
<?php } // end of past harvest
?>

<p>&nbsp;</p>
<p>If you have questions about registering as a volunteer or signing up for harvesting trips, contact the webmaster at: <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
<p>&nbsp;</p>
<p><a href="http://www.piercecountygleaningproject.org" class="indent">Return to Home Page</a></p>
<p></p>
<!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
