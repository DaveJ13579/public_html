<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php'); 
require_once('../includes/smtpmailer.inc.php');
include_once('../includes/converter.inc.php');

$formgoto = $_SERVER['PHP_SELF'];
$fname=''; $lname=''; $email=''; $emailstr=''; $sw=''; $volpass=''; $msg=''; 

if(isset($_POST['lname']))  { // if name isset
	$IDpicker = 0;
	$fname = GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text");
	$lname = GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))), "text");
	if(isset($_POST['email'])) { $email = trim($_POST['email']); $emailstr=" AND email='$email'"; }
		
	$query="SELECT ID_picker, fname, lname, email FROM pickers WHERE fname=$fname AND lname=$lname $emailstr";
	
	// echo $query; exit;
	$rsName=mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$row_rsName = mysqli_fetch_assoc($rsName);
	$numrows = mysqli_num_rows($rsName);

	if( $numrows == 0) { // name not in database
		header('Location: noname-signups.php');
		exit(); }
	if($numrows == 1) { // only one name found so calc history and send
		$IDpicker = $row_rsName['ID_picker']; 
		$email = $row_rsName['email']; 
		require_once('../includes/dencode.inc.php');
		$eIDpicker=encode($IDpicker);
		
		// compile current signups
		$querysignups = "SELECT rosters.ID_harvest, h_date, h_time, rosters.status FROM rosters, harvests WHERE rosters.ID_harvest=harvests.ID_harvest AND rosters.ID_picker=$IDpicker AND harvests.h_date>=curdate() AND rosters.status<>'cancel'";
		
		$rsSignups = mysqli_query($piercecty, $querysignups) or die(mysqli_error($piercecty));
		$numsignups=mysqli_num_rows($rsSignups);
		$caption="No signups were found.";
		if($numsignups>0) {$caption="Current signups were sent to your registered address."; }

	// send emails for harvests
 	while ($rowhist = mysqli_fetch_assoc($rsSignups)) { 
	
	if($rowhist['status']<>'waiting') { // not waiting so send usual signup email
	$subject = "Pierce County Gleaning Project reminder";
	$thisharvest=$rowhist['ID_harvest'];
	$thanksgoto = "http://www.piercecountygleaningproject.org/hthank.php?pt=".$eIDpicker."&ht=".encode($thisharvest); 
	$cancelgoto="http://www.piercecountygleaningproject.org/cancel.php?ID=".$eIDpicker."&h=".encode($thisharvest);
	$historygoto="http://www.piercecountygleaningproject.org/volunteer.php";
	$message = 'Hello '.$fname.','."\n\n".'You have signed up for a harvest sponsored by Pierce County Gleaning Project. Go to this web page for details:'."\n\n".$thanksgoto;
	$message.="\n\n".'If you find that you cannot attend and want to cancel this sign up, it may allow someone else to take your place. Go to this page to cancel your sign up:'."\n\n".$cancelgoto;
	$message.="\n\n".'You can check your attendance history, and verify your signup for this harvest, any time at this web page:'."\n\n".$historygoto; 
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
	} // end of send normal signup email
	
	else { // must be waiting list
		$subject = "Pierce County Gleaning Project roster status";
		$statuslink="http://www.piercecountygleaningproject.org/waitstatus.php?ID=".$eIDpicker;
		$message = 'Hello '.$fname.','."\n\n".'You have been added to the waiting list of a harvest sponsored by Pierce County Gleaning Project. ';
		$message .= 'You will be sent an email if there are enough cancellations so that you are moved up to the actual roster. ';
		$message .= 'That email will have the address and directions for the harvest. Because you are now on the waiting list, ';
		$message .= 'you do not need to check the Harvests page. You should:'."\n\n";
		$message .= '- Check your email before the harvest to see if you have been added to the actual roster'."\n\n";
		$message .= '- Check the following web page to see your position on the waiting list:'."\n\n";
		$message .= $statuslink;
   		if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
		} // end of must be on waiting list

	} // end of while $rowhist
	
	$sw="sent";
	// check for password
if(isset($_POST['volpass'])) $volpass=$_POST['volpass'];
if($volpass<>'') {
	$passq="select volpass from pickers where ID_picker=$IDpicker";
	$rsvolpass=mysqli_query($piercecty,$passq);
	$passrow=mysqli_fetch_assoc($rsvolpass);
	if($passrow['volpass']<>$volpass) {
		$msg='nomatch';} else { // password matches so reset the signups list
		mysqli_data_seek($rsSignups, 0);
		$msg='match';
			} // end of reset signups list
	} // password<>''
} // end of only one name found

	if($numrows > 1) { // more than one name found so need email
		$sw="need";
		} // end of more than one email found
} // end of if name isset

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Signups request</title>
<link href="../piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { background-color: #d2e2f7; }
-->
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="../images/banners/banner-home.jpg" width="876" height="180" border="2" /> 
  </div>
<?php require_once('../includes/navlinks2.inc.php'); ?>
<div id="mainContent">
    <h3 class="SH"><strong>Current harvest signups request</strong><strong></strong></h3>
    <p>Fill in the form and click on the 'Get current signups' button. Emails containing links to your current harvest signups will be sent to the email address you used when you registered as a volunteer.</p>
<form action="<?php echo $formgoto; ?>" id="getatt" name="getattform" method="POST">
  <table width="300" border="3" cellpadding="5" cellspacing="2" id="attend">
        <tr>
          <th align="right">First name:</th>
          <th ><input name="fname" id="fname" type="text" value="<?php echo isset($_POST['fname']) ? $_POST['fname'] : ''; ?>"  maxlength="20" /></th>
        </tr>
        <tr>
          <th align="right">Last name:</th>
          <th><input name="lname" type="text" value="<?php echo isset($_POST['lname']) ? $_POST['lname'] : ''; ?>"  maxlength="30" /></th>
        </tr>
		<?php if($sw=="need") { ?>
        	<tr align="left">
        	  <th colspan="2">There is more than one volunteer with that name. Please add your registered email address.</th></tr>
			<tr>
          	  <th align="right">email address:</th>
       		  <th><input name="email" type="text" id="email" value="<?php echo $email; ?>" maxlength="40" /></th>
		</tr> <?php } ?>
<tr><th  align="right">Password (optional): </th><th><input name="volpass" type="password" id="volpass" value="<?php echo $volpass; ?>" size="20" maxlength="15" /></th></tr>
  </table>
      <p>
        <input type="submit" name="submit" id="submit" value="Get current signups" />
	    <input type="hidden" name="getatt" value="getattform" />
      </p>
</form>
    <p><?php if($sw=="sent") { echo $caption; } ?></p>
 <?php 
if($msg=='nomatch') echo 'The password is not correct.';
if($msg=='match') { ?>
<table width="840" border="3" cellpadding="5" cellspacing="2"><tr>
<th>Name</th>
<th>Date</th>
<th>Time</th>
<th>Crop</th>
<th>Status</th>
<th>Details</th>
<th>Cancel this roster<br />or waiting list spot</th>
</tr><?php
while($row=mysqli_fetch_assoc($rsSignups)) { ?>
<tr>
<td><?php echo $fname.' '.$lname;?></td>
<td><?php echo $row['h_date'];?></td>
<td><?php echo $row['h_time'];?></td>
<td><?php echo cropstring($row['ID_harvest']); ?></td>
<td><?php 
	$status=$row['status'];
	switch($status) {
		case "leader" : $status="leader"; break;
		case "waiting" : $status="waiting list"; break;
		case "signup" : $status="signed up"; break;
		default: $status="signed up"; break;
	}
echo $status; ?></td>
<td><a href="<?php if($row['status']<>'waiting'){ echo '../hthank.php?pt='.$eIDpicker.'&ht='.encode($row['ID_harvest']); 
		} else { echo '../waitstatus.php?ID='.$eIDpicker; }?>">Details</a></td>        
<td><a href="<?php if($row['status']<>'waiting') { echo '../cancel.php?ID='.$eIDpicker.'&h='.encode($row['ID_harvest']);
		} else { echo '../waitstatus.php?ID='.$eIDpicker; }?>">Cancel</a></td>
<?php
	} // end of signups list
} // end of passwords match
?>
</table>
<br /><br />

 <!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('../includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>