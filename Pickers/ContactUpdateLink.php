<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php'); 
require_once('../includes/smtpmailer.inc.php');

$formgoto = $_SERVER['PHP_SELF'];
$fname=""; $lname=""; $email=""; $emailstr=""; $sw=""; $msg='';$volpass='';

if(isset($_POST['lname']))  { // if name isset
	$IDpicker = 0;
	$lname = trim(ucwords(strtolower($_POST['lname'])));
	$fname = trim(ucwords(strtolower($_POST['fname'])));
	if(isset($_POST['email'])) { $email = trim($_POST['email']); $emailstr=" AND email='$email'"; }
	
	$query=sprintf("SELECT ID_picker, lname, fname, email FROM pickers WHERE fname=%s AND lname=%s $emailstr",
		GetSQLValueString($fname, "text"), 
		GetSQLValueString($lname, "text")); 
	$rsName = mysqli_query( $piercecty, $query);
	$row_rsName = mysqli_fetch_assoc($rsName);
	$numrows = mysqli_num_rows($rsName);

	if( $numrows == 0) { // name not in database
		header('Location: noname-update.php');
		exit(); }
	if($numrows == 1) { // only one name found so calc history and send
		$IDpicker = $row_rsName['ID_picker']; 
		$email = $row_rsName['email']; 
		require_once('../includes/dencode.inc.php');
		$eIDpicker=encode($IDpicker);
// check for password
if($IDpicker) {
if(isset($_POST['volpass'])) $volpass=$_POST['volpass'];
if($volpass<>'') {
	$passq="select volpass from pickers where ID_picker=$IDpicker";
	$rsvolpass=mysqli_query($piercecty,$passq);
	$passrow=mysqli_fetch_assoc($rsvolpass);
	if($passrow['volpass']<>$volpass) {
		$msg='nomatch';} else { // go to update
		$updatedirect="ContactUpdate.php?ID=".encode($IDpicker);
		header("Location:$updatedirect"); exit;
		}
	} // volpass<>''
} // end of if ID_picker
// send email
	$updategoto="http://www.piercecountygleaningproject.org/Pickers/ContactUpdate.php?ID=".$eIDpicker;
	$subject = "Pierce County Gleaning Project Volunteer Update page link";
	$message = 'Hello '.$fname.','."\n\n".'You have asked for a link to the Volunteer Update page. By using this link you can update your contact information such as  email address or phone number.'; 
	$message.="\n\n".'The link to the Volunteer Update page is:'."\n\n".$updategoto;
    if($email=='')  { $sw="no email"; }
	else {
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
		$sw="sent"; }
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
<title>Update request</title>
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
    <h3 class="SH"><strong>Volunteer Update request</strong><strong></strong></h3>
    <p>Fill in your first and last names and click on the 'Get Volunteer Update link' button. An email with a link to the update page will be sent to the email address you used when you registered as a volunteer.</p>
<form action="<?php echo $formgoto; ?>" id="getatt" name="getattform" method="POST">
  <table width="300" border="3" cellpadding="5" cellspacing="2" id="attend">
        <tr>
          <th align="right">First name:</th>
          <th ><input name="fname" id="fname" type="text" value="<?php echo $fname; ?>"  maxlength="20" /></th>
        </tr>
        <tr>
          <th align="right">Last name:</th>
          <th><input name="lname" type="text" value="<?php echo $lname; ?>"  maxlength="30" /></th>
        </tr>
		<?php if($sw=="need") { ?>
        	<tr align="left">
        	  <th colspan="2">There is more than one volunteer with that name. Please add your registered email address.</th></tr>
			<tr>
          	  <th align="right">email address:</th>
       		  <th><input name="email" type="text" id="email" value="<?php echo $email; ?>" maxlength="40" /></th>
		</tr> <?php } ?>
  </table>
  <p><strong><em>Optional</em></strong>: If you have already set a password you may enter it here to go directly to the Contact Update page. If you have not set one, just enter your name, click the button, and you will be sent an email with a link to set your password.</p>
		<input name="volpass" type="password" id="volpass" size="15" maxlength="15" value=""/>  
      <p>
        <input type="submit" name="submit" id="submit" value="Get Volunteer Update link" />
      </p>
</form>
	<?php if($msg=='nomatch') { ?><p>The password is not correct</p> <?php } ?>
    <?php if($sw=="sent") {  ?><p>An email was sent to your registered address.</p> <?php } ?>
    <?php if($sw=="no email") {  ?><p>There is no email address for your name in the database. Contact <a href="mailto:piercecty@gleanweb.org">Dick Yates</a> to add a new email address.</p> 
  <?php } ?>
 <!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('../includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
