<?php require_once('../../Connections/piercecty.php'); 


if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";


$MM_restrictGoTo = "../../login.php";
require_once('../../includes/levelcheck.php');
require_once('../../includes/sqlcleaner.php'); 

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "mailupdateform")) {

$updateSQL = sprintf("UPDATE maillist SET fname=%s, lname=%s, organization=%s, email=%s, phone=%s, 
					phone2=%s, address=%s, city=%s, state=%s, zip=%s, otherinfo=%s WHERE ID_mail=%s",
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
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['ID_mail'], "int"));



  $Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

  $updateGoTo = "mailupdate.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ID_mail = "-1"; if (isset($_GET['temp1'])) { $ID_mail = $_GET['temp1']; }

$query = sprintf("SELECT * FROM maillist WHERE ID_mail = %s", GetSQLValueString($ID_mail, "int"));
$rsMail = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsMail);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>mail list update</title>
<style type="text/css">
<!--
-->
</style>
<link href="../../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH" >
<div id="container">
<?php include_once('../../includes/AdminNav3.inc.php');?>
<div id="mainContent">
    <h2><strong>Mailing list update</strong></h2>

    <form action="<?php echo $editFormAction; ?>" id="mailupdateform" name="mailupdateform" method="POST">
		

      <table  border="2" cellpadding="2" cellspacing="10">
		<input name="ID_mail" type="hidden" id="ID_mail" value="<?php echo $row['ID_mail'] ?>" />       
        <tr><td>First name </td><td><input name="fname" type="text" id="fname" size="15" maxlength="15" value="<?php echo $row['fname']; ?>" /></td></tr>
        <tr><td>Last name </td><td><input name="lname" type="text" id="lname" size="40" maxlength="40" value="<?php echo $row['lname']; ?>" /></td></tr>
        <tr><td>Organization </td><td><input name="organization" type="text" id="organization" size="40" maxlength="40" value="<?php echo $row['organization']; ?>" /></td></tr>
        <tr><td>Email address </td><td><input name="email" type="text" id="email" size="40" maxlength="40" value="<?php echo $row['email']; ?>" /></td></tr>
        <tr><td>Phone 1 </td><td><input name="phone" type="text" id="phone" size="25" maxlength="25" value="<?php echo $row['phone']; ?>" /></td></tr>
        <tr><td>Phone 2 </td><td><input name="phone2" type="text" id="phone2" size="25" maxlength="25" value="<?php echo $row['phone2']; ?>" /></td></tr>
        <tr><td>Address </td><td><input name="address" type="text" id="address" size="30" maxlength="30" value="<?php echo $row['address']; ?>" /></td>
		<tr><td>City </td><td><input name="city" type="text" id="city" size="20" maxlength="20"  value="<?php echo $row['city']; ?>" /></td></tr>
		<tr><td>State </td><td><input name="state" type="text" id="state" size="2" maxlength="2" value="<?php echo $row['state']; ?>" /></td></tr>
        <tr><td>Zip code </td><td><input name="zip" type="text" id="zip" size="5" maxlength="5" value="<?php echo $row['zip']; ?>" /></td></tr>
        <tr><td colspan="2">Other info <textarea name="otherinfo" cols="50" rows="10"> <?php echo $row['otherinfo']; ?> </textarea></td></tr>
      </table>


      <p><label><input type="submit" name="submit" id="submit" value="Save changes" /></label></p>
      <input type="hidden" name="MM_update" value="mailupdateform" />
      
    </form>
    <p>
      <!-- end #mainContent -->
    </p>
  </div>

  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php ((mysqli_free_result($rsMail) || (is_object($rsMail) && (get_class($rsMail) == "mysqli_result"))) ? true : false); ?>
