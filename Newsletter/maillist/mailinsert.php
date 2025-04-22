<?php require_once('../../Connections/piercecty.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change";


$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');

require_once('../../includes/sqlcleaner.php'); 

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "mailinsertform")) {
	
$insertSQL = sprintf("INSERT INTO maillist (fname, lname, organization, email, phone, phone2, address, city, state, zip, otherinfo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['organization'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['phone2'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"));

  
  $Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));

  $insertGoTo = "mailinsert.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>mail insert</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../../includes/AdminNav3.inc.php');?>
<div id="mainContent">
    <h2><strong>Mailing list insert</strong></h2>

    <form action="<?php echo $editFormAction; ?>" id="mailinsertform" name="mailinsertform" method="POST">
      <table  border="2" cellpadding="2" cellspacing="10">
        <tr><td>First name </td><td><input name="fname" type="text" id="fname" size="15" maxlength="15" /></td></tr>
        <tr><td>Last name </td><td><input name="lname" type="text" id="lname" size="40" maxlength="40" /></td></tr>
        <tr><td>Organization </td><td><input name="organization" type="text" id="organization" size="40" maxlength="40" /></td></tr>
        <tr><td>Email address </td><td><input name="email" type="text" id="email" size="40" maxlength="40" /></td></tr>
        <tr><td>Phone 1 </td><td><input name="phone" type="text" id="phone" size="25" maxlength="25" /></td></tr>
        <tr><td>Phone 2 </td><td><input name="phone2" type="text" id="phone2" size="15" maxlength="15" /></td></tr>
        <tr><td>Address </td><td><input name="address" type="text" id="address" size="30" maxlength="30" /></td>
		<tr><td>City </td><td><input name="city" type="text" id="city" size="20" maxlength="20" /></td></tr>
		<tr><td>State </td><td><input name="state" type="text" id="state" size="2" maxlength="2" /></td></tr>
        <tr><td>Zip code </td><td><input name="zip" type="text" id="zip" size="5" maxlength="5" /></td></tr>
        <tr><td colspan="2">Other info <textarea name="otherinfo" cols="50" rows="10"></textarea></td></tr>
      </table>
      <p><label><input type="submit" name="submit" id="submit" value="Insert new record" /></label></p>
      <input type="hidden" name="MM_insert" value="mailinsertform" />
      </form>
    <p></p>

  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
