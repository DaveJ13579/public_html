<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$delswitch='no';
if(isset($_POST['ID_spot']))  $ID_spot=$_POST['ID_spot'];
if(isset($_POST['delete'])) { // delete spot
$delswitch=$_POST['delswitch'];
if($delswitch=='no') {$delswitch='yes';}  
elseif($delswitch=='yes') {
	$dquery="delete from spots where ID_spot=$ID_spot";
	$rsDelete=mysqli_query($piercecty, $dquery);
	$delswitch='no';
	}
} // end of if delete

if(isset($_POST['update']) and $_POST['ID_spot']<>'') { // update spot
$ID_spot=GetSQLValueString($_POST['ID_spot'], "int");
$updatequery = sprintf("UPDATE spots SET ID_spot=$ID_spot, name=%s, address=%s, city=%s, state=%s, zip=%s where ID_spot=$ID_spot",
                       GetSQLValueString(trim($_POST['name']), "text"),
                       GetSQLValueString(trim($_POST['address']), "text"),
                       GetSQLValueString(trim($_POST['city']), "text"),
                       GetSQLValueString(trim($_POST['state']), "text"),
                       GetSQLValueString(trim($_POST['zip']), "text"));
$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));
} // end of update

if(isset($_POST['insert'])) {
$insertquery="INSERT INTO spots (name) VALUES (' - New spot - ')";
$Result1 = mysqli_query( $piercecty, $insertquery);
} // end of insert

$queryspots="select * from spots where ID_spot>0 order by name";
$rsUsers = mysqli_query( $piercecty, $queryspots) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>spot manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Meeting spots manager</strong></h2>
    <table  border="1" cellpadding="1" cellspacing="1">
      <tr>
        <th>Index</th>
        <th>Spot name</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>Zip code</th>
        <th>&nbsp;</th>
   	  <th><form action="spotmanager.php" name="inserting" method="post"><input type="submit" name="insert" value="Add a spot" /></form></td>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsUsers)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['ID_spot']==$ID_spot)) {
	  ?>
	    <form action="spotmanager.php" name="spots" method="post">
        <tr class="centercell">
          <td><input name="ID_spot" type="hidden" value="<?php echo $row['ID_spot']; ?>" /><?php echo $row['ID_spot']; ?></td>
          <td><input name="name" type="text" value="<?php echo $row['name']; ?>" size="40" maxlength="60" />
</td>
          <td><input name="address" type="text" value="<?php echo $row['address']; ?>" size="30" maxlength="30" /></td>
          <td><input name="city" type="text" value="<?php echo $row['city']; ?>" size="20" maxlength="30" /></td>
          <td><input name="state" type="text" value="<?php echo $row['state']; ?>" size="3" maxlength="2" /></td>
          <td><input name="zip" type="text" value="<?php echo $row['zip']; ?>" size="5" maxlength="5" /></td>
          <td>&nbsp;<input type="submit" name="update" value="update" />&nbsp;</td>
          <?php if($delswitch=='yes')  {?>
          <td style="color:red; background-color:pink">Are you very, very sure?<br />(See <a href="../help/spotmanager-help.php">Page Help</a>)<br /><input type="submit" name="cancel" value="cancel" /></td>
          <?php } ?>
		  <td><input type="submit" name="delete" value="delete" /></td>
          <input type="hidden" name="delswitch" value="<?php echo $delswitch;?>" />
          <input type="hidden" name="ID_spot" value="<?php echo $row['ID_spot'];?>" />
        </tr>
          </form>
        <?php } // end of if delswitch
		  } // end of all spots loop
		 ?>
</table>
  <br class="clearfloat" />
  <!-- end #container --></div>
</div>
</body>
</html>
