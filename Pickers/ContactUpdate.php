<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.public.php'); 

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "pickerinsertform")) { // if the form has been submitted

$emerg=$_POST['emerg']; $ephone=$_POST['ephone'];
if(strlen($emerg)<5 or strlen($ephone)<7 or $emerg=='--missing--' or $ephone=='--missing--') { header("Location: ../pickerinsert-error.php?error=emerg"); exit(); }

$IP=$_SERVER['REMOTE_ADDR'];
$ID=$_POST['ID'];
$updateSQL = sprintf("UPDATE pickers SET phone=%s, phone2=%s, email=%s, address=%s, city=%s, `state`=%s, zip=%s, emerg=%s, ephone=%s, harvester=%s, leader=%s, scout=%s, volpass=%s, special=%s, IP_picker=%s, contactdate=now() WHERE ID_picker=$ID",
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['phone2'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString(ucwords(strtolower($_POST['city'])), "text"),
                       GetSQLValueString(strtoupper($_POST['state']), "text"),
                       GetSQLValueString($_POST['zip'], "int"),
                       GetSQLValueString($_POST['emerg'], "text"),
                       GetSQLValueString($_POST['ephone'], "text"),
                       GetSQLValueString($_POST['harvester'], "text"),
                       GetSQLValueString($_POST['leader'], "text"),
                       GetSQLValueString($_POST['scout'], "text"),
                       GetSQLValueString(trim($_POST['volpass']), "text"),
                       GetSQLValueString($_POST['special'], "text"),
					   GetSQLValueString($IP, "text"));				   

  		$Update1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

header("Location: thankyou-update2.php"); exit();
} // end of if form isset


// check if query code is included
if(!isset($_GET['ID'])) {header('Location: noquerycode2.php');exit();}

if(isset($_GET['ID'])) {

// Decode the query string encoding the picker ID number

$eID=($_GET['ID']);
require_once('../includes/dencode.inc.php');
$ID= decode($eID);
$whoquery="select * from pickers where ID_picker=$ID";
$whoresult=mysqli_query($piercecty, $whoquery);
$numrows=mysqli_num_rows($whoresult);
$row=mysqli_fetch_assoc($whoresult);

// If the decoded query string is not a picker then go to 'Sorry - cannot be identified'
if($numrows==0) { header('Location: noquerycode2.php');exit(); }
} // end of if isset get ID
// Valid picker number so go to html

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Attendance request</title>
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
    <p></p>
    <div class="indentdiv">
      <form action="<?php echo $editFormAction; ?>" id="pickerinsertform" name="pickerinsertform" method="POST">
        <h3>Volunteer Update and Renewal Form</h3>
        <p>Change any information shown in the form and then press 'Save changes'. Even if you make no changes in the form, your registration will still be renewed.</p>
        <p>If you need to make a name change, send an email to Dick at <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
        <p>
          Name: <?php echo $row['fname']." ".$row['lname']; ?>
        </p>
        <p>
          <label>Phone
            <input name="phone" type="text" id="phone" size="15" maxlength="15" value="<?php echo $row['phone'];?>"/>
          </label> Alternate Phone
            <input name="phone2" type="text" id="phone2" size="15" maxlength="15" value="<?php echo $row['phone2'];?>"/>
        </p>
        <p>
          <label>Email
            <input name="email" type="text" id="email" size="30" maxlength="40" value="<?php echo $row['email'];?>"/>
          </label>
        </p>
        <p>
          <label>Address
            <input name="address" type="text" id="address" size="30" maxlength="30" value="<?php echo $row['address'];?>"/>
          </label>
        </p>
        <p>
          <label>City
            <input name="city" type="text" id="city" size="15" maxlength="20" value="<?php echo $row['city'];?>"/>
          </label>
          <label>State
            <input name="state" type="text" id="state" size="4" maxlength="2" value=<?php echo $row['state'];?> />
          </label>
          <label>Zip code
            <input name="zip" type="text" id="zip" size="5" maxlength="5" value="<?php echo $row['zip'];?>"/>
          </label>
        </p>
        <p>
          <label>Emergency contact name 
            <input name="emerg" type="text"  size="40" maxlength="40" value="<?php echo $row['emerg'];?>"/></label><br />
       <label> Emergency contact's phone <input name="ephone" type="text" size=" 20" maxlength="20" value="<?php echo $row['ephone'];?>"/></label></p>
        <p>How do you want to help? (Check as many as you want)</p>
         <blockquote>
        <p>Harvester – help pick from farms and fruit trees
          <input type="radio" name="harvester" value="Yes" checked="checked" /> Yes
          <input type="radio" name="harvester" value="No"  /> No</p>
        <p>Harvest Leader – lead harvests
          <input type="radio" name="leader" value="Yes" /> Yes 
          <input type="radio" name="leader" value="No" checked="checked" /> No</p>
        <p>Tree Scout – assess fruit tree  health and productivity
          <input type="radio" name="scout" value="Yes" /> Yes
           <input type="radio" name="scout" value="No" checked="checked" /> No</p>
        </blockquote>
        <p>Do you have any physical limitations or require special accommodations? Please describe:
<textarea name="special" rows="2" cols="80"><?php echo $row['special'];?></textarea></p>
<p>You may enter an optional password (up to 15 letters and numbers only) that you can use to check your signup and waiting list status anytime instantly on a web page rather than waiting for an email. <br /><br />
		  <label>Password (optional): <input name="volpass" type="password" size="15" maxlength="15" value="<?php echo $row['volpass'];?>"/></label>
        </p>
        <p>
          <label>
            <input type="submit" name="submit" id="submit" value="Save changes" />
          </label>
        <input type="hidden" name="MM_insert" value="pickerinsertform" />
        <input type="hidden" name="ID" value="<?php echo $ID;?>" /> 
        </p>
</form>
</div>
<p><a href="../index.php" class="indent">Return to Home Page</a></p>
<!-- end #mainContent --></div>
<br class="clearfloat" />
<?php require_once('../includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>