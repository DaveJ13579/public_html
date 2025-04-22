<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');

if (!isset($_SESSION))  session_start(); 
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$delswitch='no';
if(isset($_POST['ID_crop']))  $ID_crop=$_POST['ID_crop'];
if(isset($_POST['delete'])) { // delete crop
$delswitch=$_POST['delswitch'];
if($delswitch=='no') {$delswitch='yes';}  
elseif($delswitch=='yes') {
	
	// check if the crop exists anywhere in donations
	
	$existq="select ID_crop from donations where ID_crop=$ID_crop";
	$rsExist=mysqli_query($piercecty, $existq);
	if(mysqli_num_rows($rsExist)==0) {	// never picked so delete
		$dquery="delete from crops where ID_crop=$ID_crop";
		$rsDelete=mysqli_query($piercecty, $dquery);
		$delswitch='no';
	} else { 
		$errmessage= '<h3>That crop has been harvested before and so cannot been deleted. Contact the webmaster for assistance.</h3>';
		$delswitch='no';}
	} // end of delswitch=yes
} // end of if delete

if(isset($_POST['update']) and $_POST['ID_crop']<>'') { // update crop
$ID_crop=GetSQLValueString($_POST['ID_crop'], "int");

$updatequery = sprintf("UPDATE crops SET ID_crop=$ID_crop, name=%s, whenripe=%s where ID_crop=$ID_crop",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['whenripe'], "text"));
$Result1 = mysqli_query($piercecty, $updatequery) or die(mysqli_error($piercecty));

} // end of update

if(isset($_POST['insert'])) {
$insertquery="INSERT INTO crops (name) VALUES (' - New Crop - ')";
$Result1 = mysqli_query( $piercecty, $insertquery);
} // end of insert

$querycrops="select * from crops order by name";
$rsUsers = mysqli_query( $piercecty, $querycrops) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>crop manager</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h2><strong>Crop manager</strong></h2>
	<?php if(isset($errmessage)) echo $errmessage;?>
    <table  border="1" cellpadding="1" cellspacing="1">
      <tr>
        <th>Index</th>
        <th>Crop name</th>
        <th>Ripe date<br />(mm-dd)</th>
        <th>&nbsp;</th>
   	  <th><form action="cropmanager.php" name="inserting" method="post"><input type="submit" name="insert" value="Add a crop" /></form></td>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($rsUsers)) { 
	  	if($delswitch=='no' or ($delswitch='yes' and $row['ID_crop']==$ID_crop)) {
	  ?>
	    <form action="cropmanager.php" name="crops" method="post">
        <tr class="centercell">
          <td><input name="ID_crop" type="hidden" value="<?php echo $row['ID_crop']; ?>" /><?php echo $row['ID_crop']; ?></td>
          <td><input name="name" type="text" value="<?php echo $row['name']; ?>" size="50" maxlength="50" />
</td>
          <td><input name="whenripe" type="text" value="<?php echo $row['whenripe']; ?>" size="15" maxlength="20" /></td>
          <td>&nbsp;<input type="submit" name="update" value="update" />&nbsp;</td>
          <?php if($delswitch=='yes')  {?>
          <td style="color:red; background-color:pink">Are you very, very sure?<br />(See <a href="../help/cropmanager-help.php">Page Help</a>)<br /><input type="submit" name="cancel" value="cancel" /></td>
          <?php } ?>
		  <td><input type="submit" name="delete" value="delete" /></td>
          <input type="hidden" name="delswitch" value="<?php echo $delswitch;?>" />
          <input type="hidden" name="ID_crop" value="<?php echo $row['ID_crop'];?>" />
        </tr>
          </form>
        <?php } // end of if delswitch
		  } // end of all crops loop
		 ?>
</table>
  <br class="clearfloat" />
  <!-- end #container --></div>
</div>
</body>
</html>
