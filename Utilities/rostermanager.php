<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$ID_harvest=0;
if(isset($_GET['ID_harvest']) and $_GET['ID_harvest']>0) $ID_harvest=$_GET['ID_harvest'];
if(isset($_POST['ID_harvest']) and $_POST['ID_harvest']>0) $ID_harvest=$_POST['ID_harvest'];

if(isset($_POST['harvestupdate'])) {
	$sendto="harvestupdate.php?harvesttemp=".$ID_harvest; 
	header("Location: $sendto"); exit(); }

if($ID_harvest) { // get all the harvest info for heading
$harvestq="select duration, farm, address, city, ID_leader, h_date, h_time  from harvests, sites where harvests.ID_site=sites.ID_site and ID_harvest=$ID_harvest";
$rsGlean=mysqli_query($piercecty,$harvestq);
$harvestrow=mysqli_fetch_assoc($rsGlean);
extract($harvestrow);

$ldrname='not assigned';
if($ID_leader) { // get leader name
   $ldrq="select fname, lname from pickers, harvests where pickers.ID_picker=harvests.ID_leader and ID_harvest=$ID_harvest";
   $rsLdr=mysqli_query($piercecty, $ldrq);
   $ldrrow=mysqli_fetch_assoc($rsLdr);
   $ldrname=$ldrrow['fname'].' '.$ldrrow['lname'];
}
$rosterq="select rosters.ID_picker, rosters.ID_rosters, fname, lname, rosters.regdate, status, seats  from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$ID_harvest";
$rsRoster = mysqli_query($piercecty, $rosterq) or die(mysqli_error($pierce));
$numrows = mysqli_num_rows($rsRoster);

if(isset($_POST["Submit"])) {

//echo '<pre>';print_r($_POST);
	$statusarr=$_POST['status'];
	$seatsarr=$_POST['seats'];
	$ID_rostersarr=$_POST['ID_rosters'];

for($i=0;$i<sizeof($ID_rostersarr);$i++) {
	$ID_rosters = $ID_rostersarr[$i];
	$status = $statusarr[$i];
   $seats = $seatsarr[$i];
	
$sql1="UPDATE rosters SET status = '$status', seats=$seats  WHERE ID_rosters = $ID_rosters";
//echo '<br />'.$sql1;exit;
$result1=mysqli_query($piercecty, $sql1);

     if($status=='delete') {
            $deleteq="delete from rosters where ID_rosters=$ID_rosters";
            $rsDelete=mysqli_query($piercecty,$deleteq);   
         }    
} // end of all roster rows

// exit;
$updateGoTo = "rostermanager.php?ID_harvest=$ID_harvest";
header("Location: $updateGoTo");
}

if(isset($_POST['submitinsert']) and $_POST['ID_harvest']>0 and $ID_picker=$_POST['ID_picker']>0) {
      $ID_harvest=$_POST['ID_harvest'];
      $ID_picker=$_POST['ID_picker'];
      $status=$_POST['status'];
      $seats=$_POST['seats'];
      $insertq="insert into rosters (ID_harvest, ID_picker, regdate, status, seats) values ($ID_harvest, $ID_picker, current_date(), '$status', '$seats')";
// echo $insertq;exit;
      $rsInsert=mysqli_query($piercecty, $insertq);
      $updateGoTo = "rostermanager.php?ID_harvest=$ID_harvest";
      header("Location: $updateGoTo");
} // end of new row
} // end of if there is a harvest number
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>roster manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
  <div id="mainContent">
    <h2><strong>Glean Roster manager</strong></h2>
    <form action="rostermanager.php" method="get" name="filtersform">
   Glean number <input width = "20" type="int" name="ID_harvest" value="<?php if(isset($ID_harvest)) echo $ID_harvest; ?>"/>
   <input type="submit" name="submit" id="submit" value="Show roster" />
   </form>
<br />
<?php if($ID_harvest) { // do not show harvest row tables until there is a harvest nuimber ?> 
<div style="font-size:1.3em; text-align:center; width:40%; margin:auto; border: 3px solid black;"><?php  echo '<strong>'.$farm.'</strong></br />'.date('l, M j, Y',strtotime($h_date)).', '.date('g:i A',strtotime($h_time)).', '.$address.'<br />Leader: '.$ldrname; ?></div>
<br />
<table width="880px" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
   <tr>
      <th>harvest ID</th>
      <th>Name</th>
      <th>Status</th>
      <th>Seats</th>
   </tr>
      <tr class="centercell">
<form action="rostermanager.php?ID_harvest=<?php echo $ID_harvest;?>" name="rosterinsert" method="POST">
<td><?php echo $ID_harvest; ?></td>
<td>
<select name="ID_picker">
<option value=''> 
</option>
<?php 
$pickerq="select fname, lname, ID_picker, email from pickers order by lname, fname";
	$rsPickers=mysqli_query($piercecty,$pickerq);
	while($pickersdrop=mysqli_fetch_assoc($rsPickers)) { ?>          			
	<option value="<?php echo $pickersdrop['ID_picker']; ?>"><?php echo $pickersdrop['lname'].','.$pickersdrop['fname'].', '.$pickersdrop['email'];?></option>
<?php } ?>
</select></td>
<td><select name="status" >
   <option value="">[select]</option>
	<option value="harvested">harvested</option>
   <option value="leader">leader</option>
  <option value="signup">signup</option>
   <option value="absent">absent</option>
   <option value="cancel">cancel</option>
   <option value="waiting">waiting</option>

</select></td>
<td><input name="seats" type="text"  size="3" maxlength="3" /></td>
<td><input type="submit" name="submitinsert" value="Insert into roster" /></td>
<input type="hidden" name="ID_harvest" value="<?php echo $ID_harvest;?>" />
<input type="hidden" name="rosterinsertform" /></td>
</form>
</tr>
</table>
<br class="clearfloat" />
<br />
<form action="rostermanager.php?ID_harvest=<?php echo $ID_harvest;?>" name="rosterupdate" method="POST">
<table width="800px" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
<tr>
<th>Name</th>
<th>Status</th>
<th>Seats</th>
</tr>
<?php  

while ($rosterrow = mysqli_fetch_assoc($rsRoster)) { 
      ?>
      <tr>
      <td>
      <input name="ID_rosters[]" type="hidden" value="<?php echo $rosterrow['ID_rosters'];?>" />
      <?php echo $rosterrow['lname'].', '.$rosterrow['fname'];?></td>
      <td>
<select name="status[]" >
   <option value="" <?php if($rosterrow['status']=='') echo " selected='selected' "; ?>>[select]</option>
	<option value="harvested" <?php if($rosterrow['status']=='harvested') echo " selected='selected' "; ?>>harvested</option>
   <option value="leader" <?php if($rosterrow['status']=='leader') echo " selected='selected' "; ?>>leader</option>
  <option value="delete">delete</option>
 <option value="signup" <?php if($rosterrow['status']=='signup') echo " selected='selected' "; ?>>signup</option>
   <option value="absent" <?php if($rosterrow['status']=='absent') echo " selected='selected' "; ?>>absent</option>
   <option value="cancel" <?php if($rosterrow['status']=='cancel') echo " selected='selected' "; ?>>cancel</option>
   <option value="waiting" <?php if($rosterrow['status']=='waiting') echo " selected='selected' "; ?>>waiting</option>
</select>
</td>
<td><input name="seats[]" type="text" value="<?php echo $rosterrow['seats'];?>" size="3" maxlength="3" /></td>
</tr>
 <?php }   ?>
<tr><td colspan="3"><input type="submit" name="Submit" value="Update roster" /></td>
<td><input type="submit" name="harvestupdate" value="Back to Glean Update"></td>
</tr>
</table>
</form>
<?php } // end of if there is a harvest number ?>
</div>
</div>
</body>
</html>
