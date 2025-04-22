<?php 
require_once('Connections/piercecty.php'); 
include_once('includes/converter.inc.php');
require_once('includes/dencode.inc.php'); 

$harvest = -1; $fname=''; $IDpicker=0; $volpass=''; $msg='';
if (isset($_GET['ht'])) $harvest = $_GET['ht']; 
if (isset($_GET['pt']))  { $fname = stripslashes($_GET['pt']); 
	$fname=substr($fname,1,strlen($fname)-2);  } // takes off single quotes
if (isset($_GET['eID'])) $IDpicker=decode($_GET['eID']); 
$query= "SELECT h_date,  h_time, harvests.otherinfo as info FROM harvests WHERE ID_harvest=$harvest";
$rsHarvest = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsHarvest);
$numrows = mysqli_num_rows($rsHarvest);

// check for password
if(isset($_POST['volpass'])) $volpass=$_POST['volpass'];
if($volpass<>'') {
	$passq="select volpass from pickers where ID_picker=$IDpicker";
	$rsPassword=mysqli_query($piercecty,$passq);
	$passrow=mysqli_fetch_assoc($rsPassword);
	if($passrow['volpass']<>$volpass) {
		$msg='nomatch';} else { // go to confirm
		$confirmgoto="confirm.php?ID=".encode($IDpicker)."&h=".encode($harvest);
		header("Location:$confirmgoto"); exit;
		}
	} // volpass<>''
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest Signup</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7; } 
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
<?php if($numrows==0) { echo "<p>The harvest could not be identified.</p>"; }
  else { // okay to display html message
  ?>
  <p><?php echo $fname; ?>, you have asked to be added to the waiting list for the harvest listed below. </p>
  <p><em><strong>To get on the waiting list you must reply to the link in the email that has been sent to you. </strong></em></p>
<p>After you are on the waiting list, you will be sent another email if there are enough cancellations so that you are moved up to the actual roster. That email will have the address and directions for the harvest.</p>
<table border="1" cellspacing="5" cellpadding="5" align="center">
  <tr>
    <td>Picking: <?php $crops=cropstring($harvest); echo $crops; ?></td>
    <td>Date: <?php echo date('l  m/d/Y',strtotime($row['h_date'])); ?></td>
    <td>Time: <?php echo date('g:i A',strtotime($row['h_time'])); ?></td>
  </tr>
  <tr>
    <td colspan="3"><?php echo $row['info']; ?></td>
  </tr>
</table>
<p><strong><em>Optional</em></strong>: If you have a password you may enter it here to be placed on the waiting list immediately. </p>
<form name="pw" method="POST">
<input type="submit" name="submit" value="Submit password" />
<input name="volpass" type="password" id="volpass" size="15" maxlength="15" value=""/>
</form>
<?php if($msg=='nomatch') { ?>
<p>The password is not correct.</p>
<?php } ?>
<p>If you have a password, you will also be able to see your roster status immediately by going to the Volunteer page and clicking on the My Page button.</p>

<?php } // end of okay to display html
?>
<p>If you have questions about registering as a volunteer or signing up for harvests, contact the webmaster at: <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
<p>&nbsp;</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p></p>
<!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
