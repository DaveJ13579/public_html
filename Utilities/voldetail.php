<?php require_once('../Connections/piercecty.php'); 
require_once('../includes/sqlcleaner.php'); 
include_once('../includes/converter.inc.php');
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

$colname_rsVoldetail = "-1";
if (isset($_GET['voltemp'])) {
  $colname_rsVoldetail = $_GET['voltemp'];
}

$query_rsVoldetail = sprintf("SELECT * FROM pickers WHERE ID_picker = %s", GetSQLValueString($colname_rsVoldetail, "int"));
$rsVoldetail = mysqli_query($piercecty, $query_rsVoldetail) or die(mysqli_error($piercecty));
$row_rsVoldetail = mysqli_fetch_assoc($rsVoldetail);
$totalRows_rsVoldetail = mysqli_num_rows($rsVoldetail);

$colname_rsHarvesthist = "-1";
if (isset($_GET['voltemp'])) {
  $colname_rsHarvesthist = $_GET['voltemp'];
}

$query_rsHarvesthist = "SELECT rosters.ID_harvest, harvests.h_date, rosters.status, sites.farm  FROM rosters, harvests, sites WHERE ID_picker=$colname_rsHarvesthist and harvests.ID_harvest=rosters.ID_harvest and sites.ID_site=harvests.ID_site ORDER by harvests.h_date DESC";
$rsHarvesthist = mysqli_query($piercecty, $query_rsHarvesthist) or die(mysqli_error($piercecty));
$totalRows_rsHarvesthist = mysqli_num_rows($rsHarvesthist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>volunteer detail</title>
<style type="text/css">
<!--
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
<div id="mainContent">
    <h2 class="SH"><strong>Volunteer detail</strong></h2>
    <p><a href="pickerupdate.php?temp1=<?php echo $colname_rsVoldetail; ?>">Go to update</a></p>
     <table width="1240" border="2" cellpadding="5" cellspacing="10" id="contactinfo">
      <tr>
        <th>ID number</th>
        <th>Name</th>
        <th>address</th>
        <th>city</th>
        <th>state</th>
        <th>zip</th>
</tr>
<tr>
        <td align="center"><?php echo $row_rsVoldetail['ID_picker']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['fname']; ?> <?php echo $row_rsVoldetail['lname']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['address']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['city']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['state']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['zip']; ?></td>
</tr>
<tr>  
		<th>email</th>
        <th>Phone</th>
        <th>Phone2</th>
       <th>On assistance</th>
</tr>
<tr>        
		<td align="center"><?php echo $row_rsVoldetail['email']; ?></td>
		<td align="center"><?php echo $row_rsVoldetail['phone']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['phone2']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['assistance']; ?></td>
 </tr>
      <tr>
        <th>Duplicate name alert</th>
       <th>Emergency contact</th>
       <th>Emergency phone</th>
       <th>How heard</th>
      </tr>
      <tr>
        <td align="center"><?php echo $row_rsVoldetail['dupname']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['emerg']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['ephone']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['how_hear']; ?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table width="1240" border="2" cellpadding="5" cellspacing="10" id="otherdetail">
      <tr align="center">
        <th nowrap="nowrap">Harvester</th>
        <th>Harvest Leader</th>
        <th>Scout</th>
        <th>Select team?</th>
       <th>Email notice</th>
        <th>Phone notice</th>
      </tr>
      <tr class="centercell">
        <td align="center"><?php echo $row_rsVoldetail['harvester']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['leader']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['scout']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['selectteam']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['weekemail']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['weekphone']; ?></td>
      </tr>
      <tr>
        <th>Most recent contact</th>
        <th>Registration Date</th>
        <th>IP address</th>
        <th>Select Team Waiver Date</th>
      </tr>
      <tr>
        <td align="center"><?php echo $row_rsVoldetail['contactdate']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['regdate']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['IP_picker']; ?></td>
        <td align="center"><?php echo $row_rsVoldetail['waive_date']; ?></td>
      </tr>
    </table>
    <table width="1240" border="2" cellpadding="5" cellspacing="10" id="notes">
	<tr>
	  <th>Physical Limitations or Special Accommodations</th></tr>
    <tr><td><?php echo $row_rsVoldetail['special']; ?></td></tr>
    </table>
    <table width="1240" border="2" cellpadding="5" cellspacing="10" id="notes">
	<tr><th>Notes</th></tr>
    <tr><td><?php echo $row_rsVoldetail['other_info']; ?></td></tr>
    </table>
    <p><strong>Harvest history</strong></p>
    
    <table border="2" cellpadding="5" cellspacing="10" id="otherdetail">
    <tr>
    <?php
    $colname_rsAttendance = "-1";
    if (isset($_GET['voltemp'])) {
    $colname_rsAttendance = $_GET['voltemp'];}
	
	$query_rsStatus = "SELECT status, COUNT(ID_picker) FROM rosters WHERE ID_picker = $colname_rsAttendance GROUP by status";
	$result = mysqli_query($piercecty, $query_rsStatus) or die(mysqli_error($piercecty));
	while($row = mysqli_fetch_array($result))
	{ ?>
	<td align="center" width="200"><?php echo $row['status']." = ".$row['COUNT(ID_picker)'] ?></td>
    <?php } ?>
    </tr></table>

    <p>&nbsp;</p>
    <table width="1000" border="2" cellspacing="10" cellpadding="5">
      <tr>
        <th width="100">Harvest ID</th>
        <th>Harvest Date</th>
        <th>Site</th>
        <th>Crop</th>
        <th>Status</th>
      </tr>
      <?php while ($row_rsHarvesthist = mysqli_fetch_assoc($rsHarvesthist)) { ?>
        <tr>
          <td align="center"><a href="../harvestroster.php?harvesttemp=<?php echo $row_rsHarvesthist['ID_harvest']; ?>"><?php echo $row_rsHarvesthist['ID_harvest']; ?></a></td>
          <td align="center"><?php echo $row_rsHarvesthist['h_date']; ?></td>
          <td align="center"><?php echo $row_rsHarvesthist['farm']; ?></td>
          <td align="center"><?php $crops=cropstring($row_rsHarvesthist['ID_harvest']); echo $crops;  ?></td>
          <td align="center"><?php echo $row_rsHarvesthist['status']; ?></td>
        </tr>
        <?php }  ?>
    </table>
    <p>&nbsp;</p>
  </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
<?php
((mysqli_free_result($rsVoldetail) || (is_object($rsVoldetail) && (get_class($rsVoldetail) == "mysqli_result"))) ? true : false);

((mysqli_free_result($rsHarvesthist) || (is_object($rsHarvesthist) && (get_class($rsHarvesthist) == "mysqli_result"))) ? true : false);
?>
