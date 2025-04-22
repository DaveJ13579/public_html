<?php require_once('../Connections/piercecty.php'); 
require_once('../includes/converter.inc.php');

$IP = $_SERVER['REMOTE_ADDR'];

// if($IP=='67.189.67.198') { header("Location: HitsDiverter.php?IP=$IP"); exit(); } // jimfromoz@gmail.com says was doing it manually
// if($IP=='67.168.243.21') { header("Location: HitsDiverter.php?IP=$IP"); exit(); }
// if($IP=='127.0.0.1') { header("Location: HitsDiverter.php?IP=$IP"); exit(); }

// find all open and future harvests

$query_rsHarvests = "SELECT harvests.h_date, harvests.h_time, harvests.pick_num, sites.inlimits, sites.branch, harvests.ID_harvest, harvests.status, sites.spray, harvests.otherinfo FROM harvests, sites WHERE harvests.ID_site = sites.ID_site and harvests.h_date >= curdate() AND harvests.status = 'open' ORDER BY harvests.h_date ASC";
$rsHarvests = mysqli_query($piercecty, $query_rsHarvests) or die(mysqli_error($piercecty));
$row_rsHarvests = mysqli_fetch_assoc($rsHarvests);
$totalRows_rsHarvests = mysqli_num_rows($rsHarvests);

// compile weights for 2012 harvests so far
$query_rsHarvestHistory = "SELECT harvests.ID_harvest, harvests.h_date, harvests.calcwgt, harvests.totwgt, harvests.where_to FROM harvests, sites WHERE harvests.ID_site = sites.ID_site and harvests.calcwgt > 0 and DATE_FORMAT(harvests.h_date,'%Y') = '2012' ORDER BY harvests.h_date DESC";
$rsHarvestHistory = mysqli_query($piercecty, $query_rsHarvestHistory) or die(mysqli_error($piercecty));
$row_rsHarvestHistory = mysqli_fetch_assoc($rsHarvestHistory);
$totalRows_rsHarvestHistory = mysqli_num_rows($rsHarvestHistory);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvests</title>
<link href="piercecty-m.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.clearcell {
	background-color: #FFF;
	border-top-width: 0px;
	border-right-width: 0px;
	border-bottom-width: 0px;
	border-left-width: 0px;
}
table tr {
	text-align: center;
	line-height: normal;
	border-top-width: thin;
	border-right-width: thin;
	border-bottom-width: thin;
	border-left-width: thin;
}
th {
	line-height: normal;
	background-color: #b2c2d7;
}
.leftjustify {
	text-align: left;
}
-->
</style>
</head>

<body class="SH">
<div id="container">
<div id="mainContent">
 <p><strong>Harvest Pierce County's Gleaning Project Harvests</strong></p>
      <?php 

if($totalRows_rsHarvests == 0 ) { // if there are no future open harvests

$botkiller=rand(1,2)==1 ? "sign" : "signing ";
echo "<p>There are <strong>no harvests currently available</strong> for ".$botkiller."up. If you have heard of a harvest through email, Facebook or your neighbor, but you do not see it listed here, it means that the roster is closed.</p><br /><br />"; } 

else { // show all harvests and headings 

?>
    <p>Before signing up for a harvest, please be sure that you and each person signing up has registered previously on the <a href="../pickerinsert.php" title="Harvester registration">Volunteers</a> page. If you have already registered as a volunteer you do not have to do so again. Sign up each person separately for harvests. To sign up for a harvest, click on the 'Sign up for harvest' button. If the roster is full you can add your name to a waiting list.</p>
    <p><strong>Sign up for harvests</strong></p>

      <table width="680" border="3" align="center" cellpadding="3" cellspacing="2" id="harvestlist">
      <?php

	mysqli_data_seek($rsHarvests, 0);// reset data pointer so can again cycle through current open harvests to see if should be displayed
	$row_rsHarvests=mysqli_fetch_assoc($rsHarvests);

	  do { // Do all current open harvests 

				$IDharvest = $row_rsHarvests['ID_harvest'];
				$query_count = "SELECT COUNT(ID_picker) as pickers FROM rosters WHERE ID_harvest = $IDharvest";
				$counttot = mysqli_query($piercecty, $query_count) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($counttot);
				$totalct=$row['pickers'];			
				
				$query_cancel = "SELECT COUNT(ID_picker) as cancel FROM rosters WHERE ID_harvest = $IDharvest AND status = 'cancel'";
				$canceltot = mysqli_query($piercecty, $query_cancel) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($canceltot);
				$cancelct=$row['cancel'];
				
				$query_wait = "SELECT COUNT(ID_picker) as waiting FROM rosters WHERE ID_harvest = $IDharvest AND status = 'waiting'";
				$waittot = mysqli_query($piercecty, $query_wait) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($waittot);				
				$waitct=$row['waiting'];
				
				$signedup = $totalct - $cancelct - $waitct;
				$picknum=$row_rsHarvests['pick_num'];	
				
			?>
			<tr>
            <th rowspan="2" valign="middle" bgcolor="#99FFFF"><form id="signup" name="signup" method="post" action="signup-m.php?access=public">
              <p>
                <input name="harvesttemp" type="hidden" value="<?php echo $row_rsHarvests['ID_harvest'];?>"/>
                <input type="submit" name="Submit2" id="Submit2" style="height:60px; font-weight:bold; font-size:1.1em" value="<?php if($signedup<$picknum) { echo "Sign up\nfor harvest"; } else { echo "Add name\nto waiting list"; }?>" />
              </p>
            </form></th>
            <th><p>Harvest date</p></th>
            <th>Time</th>
            <th><p>Harvesting</p></th>
            <th><p>Crop sprayed?</p></th>
          </tr>
          <tr>
            <td><?php echo date('l  m/d/Y',strtotime($row_rsHarvests['h_date'])); ?></td>
            <td><?php echo date('g:i A',strtotime($row_rsHarvests['h_time'])); ?></td>
            <td><?php $crops=cropstring($IDharvest); echo $crops; ?></td>
            <td><?php echo $row_rsHarvests['spray']; ?></td>
          </tr>
          <tr>
            <th class="clearcell"></th>
            <th>Total volunteers needed</th>
            <th><p>Number  on roster</p></th>
            <th><p>Number on waiting list</p></th>
            <th>In city limits?</th>
            <th>General location</th>
          </tr>
          <tr>
            <td class="clearcell"></td>
            <td><?php echo $picknum; ?></td>
            <td><?php echo $signedup ?></td>
            <td><?php echo $waitct; ?></td>
            <td><?php echo $row_rsHarvests['inlimits']; ?></td>
            <td><?php echo $row_rsHarvests['branch']; ?></td>
          </tr>
          <tr>
          	<td class="clearcell"></td>
            <td colspan = "5" class="leftjustify"><?php echo $row_rsHarvests['otherinfo']; ?> </td>
          </tr>
          <tr>
            <td class="clearcell">&nbsp;</td>
            <td class="clearcell">&nbsp;</td>
            <td class="clearcell">&nbsp;</td>
            <td class="clearcell">&nbsp;</td>
            <td class="clearcell">&nbsp;</td>
            <td class="clearcell">&nbsp;</td>
          </tr>
          
  <?php } while ($row_rsHarvests = mysqli_fetch_assoc($rsHarvests)); // End loop do all harvests 
 ?>
      </table>
      	<br />
<?php 	} // end of else show all harvests and headings
?>
<p>&nbsp;</p>
</div>
  <div id="footer">
<?php require_once('../includes/footer.inc.php'); ?>
  <!-- end #footer --></div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsHarvests) || (is_object($rsHarvests) && (get_class($rsHarvests) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsHarvestHistory) || (is_object($rsHarvestHistory) && (get_class($rsHarvestHistory) == "mysqli_result"))) ? true : false);
?>
