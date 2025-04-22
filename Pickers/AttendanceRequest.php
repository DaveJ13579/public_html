<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php'); 
include_once('../includes/converter.inc.php');
require_once('../includes/smtpmailer.inc.php');

$formgoto = $_SERVER['PHP_SELF'];
$fname=""; $lname=""; $email=""; $emailstr=""; $sw="";

if(isset($_POST['lname']))  { // if name isset
	$IDpicker = 0;
	$lname = GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))),"text");
	$fname = GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text");
	if(isset($_POST['email'])) { $email = trim($_POST['email']); $emailstr=" AND email='$email'"; }
	
	$query="SELECT ID_picker, fname, lname, email FROM pickers WHERE fname=$fname AND lname=$lname".$emailstr ;
//	echo $query;
	$rsName=mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	$row_rsName = mysqli_fetch_assoc($rsName);
	$numrows = mysqli_num_rows($rsName);

	if( $numrows == 0) { // name not in database
		header('Location: noname-history.php');
		exit(); }
	if($numrows == 1) { // only one name found so calc history and send
		$IDpicker = $row_rsName['ID_picker']; 
		$email = $row_rsName['email']; 

		// compile attendance summary	
		
		$queryatt = "SELECT status, COUNT(ID_picker) FROM rosters WHERE ID_picker = $IDpicker GROUP by status";
		$resultatt = mysqli_query($piercecty, $queryatt) or die(mysqli_error($piercecty));
	
		// compile harvest history
		$queryhist = "SELECT rosters.ID_harvest, harvests.h_date, rosters.status FROM rosters, harvests WHERE ID_picker = $IDpicker and harvests.ID_harvest = rosters.ID_harvest  ORDER by harvests.h_date DESC";
		$rsHist = mysqli_query($piercecty, $queryhist) or die(mysqli_error($piercecty));

	// set up email structure
	$subject = 'Pierce County Gleaning Project attendance history';
	$message = "Attendance\n\nHi".$_POST['fname']."\nHere is the harvest attendance history that you requested.\n\nIf you have any questions about it, send a note to piercecty@gleanweb.org.\n\nAttendance summary";		
	while($rowatt = mysqli_fetch_array($resultatt)) { 
		$message.= "\n";
        $st=($rowatt['status']=='intake' ? 'assisted' : $rowatt['status']);
        $message.= $st." = ".$rowatt['COUNT(ID_picker)'];
  	  }
   	$message.="\n\nHarvest history\n\nHarvest Date\tCrop\tStatus\n";
      
	  //generate history table rows
	  while ($rowhist = mysqli_fetch_assoc($rsHist)) { 
	  	$crops=cropstring($rowhist['ID_harvest']);
        $message.=$rowhist['h_date']."\t$crops\t";
		  $st=($rowhist['status']=='intake' ? 'assisted' : $rowhist['status']);
		  $message.="$st\n";
         } 
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org", false);
	$message.="\n\n\nemail sent to $email, ".$_POST['fname']." ".$_POST['lname'];
//	smtpmail('dyates@gleanweb.org', $subject, $message, "info@piercecountygleaningproject.org");
//	echo $email." ".$subject." ".$message;
	$sw="sent";
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
<title>Attendance Request</title>
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
    <h3 class="SH"><strong>Attendance history request</strong><strong></strong></h3>
    <p>Fill in the form and click on the 'Get attendance' button. An email containing your harvest attendance history will be sent to the email address you used when you registered as a volunteer.</p>
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
  </table>
      <p>
        <input type="submit" name="submit" id="submit" value="Get attendance" />
	    <input type="hidden" name="getatt" value="getattform" />
      </p>
</form>
    <p><?php if($sw=="sent") { ?> An email with your attendance history has been sent. <?php } ?></p>
 <!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('../includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>