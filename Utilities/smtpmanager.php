<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$delswitch='no';
if(isset($_POST['ID_smtp']))  $ID_smtp=$_POST['ID_smtp'];
if(isset($_POST['delete'])) { // delete smtp
$delswitch=$_POST['delswitch'];
if($delswitch=='no') {$delswitch='yes';}  
elseif($delswitch=='yes') {
	$dquery="delete from smtplogins where ID_smtp=$ID_smtp";
	$rsDelete=mysqli_query($piercecty, $dquery);
	$delswitch='no';
	}
} // end of if delete

if(isset($_POST['update']) and $_POST['ID_smtp']<>'') { // update smtp
$ID_smtp=GetSQLValueString($_POST['ID_smtp'], "int");
$updatequery = sprintf("UPDATE smtplogins SET ID_smtp=$ID_smtp, email=%s, password=%s, description=%s where ID_smtp=$ID_smtp",
                       GetSQLValueString(trim($_POST['email']), "text"),
                       GetSQLValueString(trim($_POST['password']), "text"),
                       GetSQLValueString(trim($_POST['description']), "text"));
$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));
} // end of update

if(isset($_POST['insert'])) {
$insertquery="INSERT INTO smtplogins (email) VALUES (' - New smtp - ')";
$Result1 = mysqli_query( $piercecty, $insertquery);
} // end of insert

$querysmtps="select * from smtplogins where ID_smtp>0 order by email";
$rsUsers = mysqli_query( $piercecty, $querysmtps) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>smtp manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>SMTP  manager</strong></h2>
    <table  border="1" cellpadding="1" cellspacing="1">
      <tr>
        <th>Index</th>
        <th>Email address</th>
        <th>Password</th>
        <th>Description</th>
   	  <th><form action="smtpmanager.php" name="inserting" method="post"><input type="submit" name="insert" value="Add a smtp" /></form></td>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsUsers)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['ID_smtp']==$ID_smtp)) {
	  ?>
	    <form action="smtpmanager.php" name="smtps" method="post">
        <tr class="centercell">
          <td><input name="ID_smtp" type="hidden" value="<?php echo $row['ID_smtp']; ?>" /><?php echo $row['ID_smtp']; ?></td>
          <td><input name="email" type="text" value="<?php echo $row['email']; ?>" size="40" maxlength="60" />
</td>
          <td><input name="password" type="text" value="<?php echo $row['password']; ?>" size="30" maxlength="30" /></td>
          <td><input name="description" type="text" value="<?php echo $row['description']; ?>" size="30" maxlength="60" /></td>
          <td>&nbsp;<input type="submit" name="update" value="update" />&nbsp;</td>
          <?php if($delswitch=='yes')  {?>
          <td style="color:red; background-color:pink">Are you very, very sure?<br />(See <a href="../help/smtpmanager-help.php">Page Help</a>)<br /><input type="submit" name="cancel" value="cancel" /></td>
          <?php } ?>
		  <td><input type="submit" name="delete" value="delete" /></td>
          <input type="hidden" name="delswitch" value="<?php echo $delswitch;?>" />
          <input type="hidden" name="ID_smtp" value="<?php echo $row['ID_smtp'];?>" />
        </tr>
          </form>
        <?php } // end of if delswitch
		  } // end of all smtps loop
		 ?>
</table>
  <br class="clearfloat" />
  <!-- end #container --></div>
</div>
</body>
</html>
