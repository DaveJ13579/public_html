<?php
if (!isset($_SESSION)) { session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php');

$IDharvest=-1;
if(isset($_GET['harvesttemp'])) $IDharvest=$_GET['harvesttemp'];
if (isset($_POST["MM_filter"]) && ($_POST["MM_filter"] == "filtersform")) {
      $IDharvest = $_POST['harvesttemp']=='' ? -1 : $_POST['harvesttemp']; }
if(isset($_POST["Submit"])) 	$IDharvest=$_POST['IDharvest'];

$query_rsRoster = "SELECT ID_rosters, ID_harvest, rosters.ID_picker, pickers.lname, pickers.fname, status FROM rosters, pickers WHERE rosters.ID_picker=pickers.ID_picker and ID_harvest = $IDharvest and status<>'cancel' ORDER BY lname, fname";
$rsRoster = mysqli_query( $piercecty, $query_rsRoster);
$totalRows_rsRoster = mysqli_num_rows($rsRoster);
if(isset($_POST["Submit"])) {
	
for($i=0;$i<$totalRows_rsRoster;$i++) {
	$status=$_POST['status'];	
	$ID_rosters=$_POST['ID_rosters'];
	$rost = $ID_rosters[$i];
	$stat = $status[$i];
	if($stat<>'unchanged') {
		$sql1="UPDATE rosters SET status = '$stat' WHERE ID_rosters = $rost";
		$result1=mysqli_query($piercecty, $sql1);
		} // end of status unchanged
	} // end of all rows
header("Location: attendance-m2.php?harvesttemp=$IDharvest");
} // end of if submit
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest attendance</title>
<link href="piercecty-m.css" rel="stylesheet" type="text/css" />
<style type="text/css">
</style>
</head>
<body class="SH">
<div id="container">
<?php if(isset($IDharvest)) {?> <a href="harvestupdate-m.php?harvesttemp=<?php echo $IDharvest;?>">To harvest update</a><?php } ?>

<p><strong>Harvest attendance</strong></p>
<form action="attendance-m2.php" method="POST" name="filtersform">
       Harvest number<input width = "50" type="int" name="harvesttemp" id="harvesttemp" value="<?php if($IDharvest<>-1) echo $IDharvest; ?>"/>
      <input type="submit" name="submit" id="submit" value="Show records" />
      <input type="hidden" name="MM_filter" value="filtersform" />
  </form> 
<br />
<div id="rostertable">
  <table border="1" cellpadding="1" cellspacing="1" id="rosterlist" >
      <tr>
        <th>Name</th>
        <th>Update</th>
        <th>Status</th>
      </tr>
<form action="attendance-m2.php" name="attendanceform" method="POST">
      
	  <?php  $ct=0; while ($row = mysqli_fetch_assoc($rsRoster)) { ?>
        <tr><td>
          <input name="IDharvest" type="hidden" id="IDharvest" value="<?php echo $IDharvest;?>" />
          <input name="ID_rosters[<?php echo $ct;?>]" type="hidden" id="ID_rosters" value="<?php echo $row['ID_rosters'];?>" />
          <?php echo $row['lname'].', '.$row['fname'];?></td>
          <td><select name="status[<?php echo $ct;?>]" id="status">
          				<option value="harvested" <?php // if($row['status']=='harvested') echo 'selected="selected" ';  ?>>harvested</option>
						<option value="leader" <?php if($row['status']=='leader') echo 'selected="selected" ';  ?>>leader</option>
				        <option value="absent" <?php if($row['status']=='absent') echo 'selected="selected" ';  ?>>absent</option>
			            <option value="signup" <?php if($row['status']=='signup') echo 'selected="selected" ';  ?>>signup</option>
	  </select>    
           </td> 
           <td><?php echo $row['status'];?></td>
        </tr>
        <?php  $ct++;}   ?>
		  <tr><td colspan="3"><br />Only when all 'Update' lines are complete:<br /><input type="submit" name="Submit" value="Submit" /></td></tr>
</form>
    </table>
    </div>
  </div> 
<p>&nbsp;</p>
<p><br class="clearfloat" /></p>
</div>
</body>
</html>
