<?php require_once('Connections/piercecty.php'); 
include_once('includes/converter.inc.php');
require_once('includes/jobsconversion.inc.php');

if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change,view,roster";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');
// get the harvest number
$harvest=-1;
if (!isset($_GET['harvesttemp'])) { echo "No harvest ID number included in url."; exit; }

$harvest=$_GET['harvesttemp'];

// compile site info and harvest info

$queryh = "select * from harvests  where ID_harvest=$harvest";
$rsHarvest = mysqli_query($piercecty, $queryh) or die(mysqli_error($piercecty));
$rowh = mysqli_fetch_assoc($rsHarvest);

$site=$rowh['ID_site'];
$queryc="select * from sites where ID_site=$site";
$rssite = mysqli_query($piercecty, $queryc) or die(mysqli_error($piercecty));
$rowc = mysqli_fetch_assoc($rssite);

$leader=$rowh['ID_leader'];
$queryl="select * from pickers where ID_picker=$leader";
$rsLeader = mysqli_query($piercecty, $queryl) or die(mysqli_error($piercecty));
$rowl = mysqli_fetch_assoc($rsLeader);

// get the roster
$query_rsHarvestroster = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone as phone, pickers.emerg, pickers.ephone, seats, special from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvest and rosters.status <> 'waiting' order BY lname, fname";
$rsRoster = mysqli_query($piercecty, $query_rsHarvestroster) or die(mysqli_error($piercecty));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest information and roster</title>
<style type="text/css">
<!--

body  {
	background: #666666;
	margin: 0; /* it's good practice to zero the margin and padding of the body element to account for differing browser defaults */
	text-align: left; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
	font-size: 100.01%;
	clear: none;
	background-color: #FFF;
	font-family: Arial, Helvetica, sans-serif;
}
.newpage { 	page-break-before: always; }
.keeptable { 	page-break-inside: avoid; }
.SH #container {
	width: 7.5 in;  
	height: 10 in;
	font-size: 12pt;
} 
#header {
	width: 7in;
	font-size: 16pt;
}
th { 	background-color: #CCC;
	text-align: center;
	}
tr { 	text-align: center; }
.section { 	page-break-before: always; }

p.MsoNormal {
	margin:0in;
	margin-bottom:.0001pt;
	font-size:12pt;
	font-family:"Times New Roman", "serif";
}
-->
</style>
</head>

<body class="SH">
<div id="container">
  <div id="header">
Harvest number: <?php echo $rowh['ID_harvest']; ?>&nbsp;&nbsp;&nbsp;Date: <?php echo $rowh['h_date']; ?>
       &nbsp;&nbsp;
       <!-- end #header -->
site number: <?php echo $rowh['ID_site']; ?></div>
  <div id="mainContent">
  <br />
    <table border="1" cellpadding="3">
      <tr>
        <th>Owner</th>
        <th>Phone</th>
        <th colspan="3">Email</th>
      </tr>
      <tr>
        <td><?php echo $rowc['farm']; ?> <?php echo $rowc['contact1']; ?></td>
        <td><?php echo $rowc['phone1']; ?></td>
        <td colspan="3"><?php echo $rowc['email1']; ?></td>
      </tr>
      <tr>
        <th colspan="2">Address</th>
        <th>General area</th>
         <th>Pickers needed</th>
     </tr>
      <tr>
        <td colspan="2"><?php echo $rowc['address']." ".$rowc['city'];?></td>
        <td><?php echo $rowc['branch']; ?></td>
        <td align="center"><?php echo $rowh['pick_num']; ?></td>
     </tr>
      <tr>
        <th>Crop type</th>
        <th>Number</th>
        <th>Location</th>
      </tr>
      <tr>
        <td><?php $crops=cropstring($harvest); echo $crops; ?></td>
        <td class="centercell"><?php echo $rowc['size']; ?></td>
        <td><?php echo $rowc['location']; ?></td>
      </tr>
      <tr>
        <th class="centercell">Spray?</th>
        <th>Spray info</th>
      </tr>
      <tr>
        <td align="center" class="centercell"><?php echo $rowc['spray']; ?></td>
        <td><?php echo $rowc['spray_text']; ?></td>
      </tr>
    </table>
    <p><strong>Crop Scouting Information</strong></p>
    <p>How much  produce is accessible?</p>
    <p> When will it  be ripe? </p>
    <p>Crop  quality:  Check for pests, over-ripeness, other signs of damage. </p>
    <p>Describe:</p>
    <p>&nbsp;</p>
    <p>Are ladders  needed? ______ How many? _____  What size?  ___________ </p>
<p>At what height  range is the fruit?   _____________________________________ </p>
    <p>Is there room  to safely place ladders? ________   Is area flat? _____________ </p>
    <p>Diagram the  property on the  other side if necessary.  Include any  special concerns for this property. </p>
    <p>How many pounds of produce are available? _________</p>
<p> How many pickers are needed?__________ </p>
    <p>How many harvest sessions are needed?  __________</p>
<p>Ask the property owner to sign and date the Temporary  Right-of-Entry Authorization Form</p>
    <p>Discuss harvest dates with property owner   (Tell property owner that the date needs to be confirmed with Harvest Pierce County's Gleaning Project to avoid conflicts and ensure equipment is available.)</p>
    <p><strong>TEMPORARY RIGHT OF ENTRY AUTHORIZATION</strong><br />
    </p>
    <p>Crop registry  number: <?php echo $rowc['ID_site']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop type(s): <?php echo $crops; ?><br />
    </p>
    <p>I, <?php echo $rowc['farm']." ".$rowc['contact1'];?>, grant permission to Harvest Pierce County's Gleaning Project  members and volunteers to enter onto my property located at <?php echo $rowc['address'].", ".$rowc['city'].", ".$rowc['state']." ".$rowc['zip']; ?> for the sole purpose of picking and/or gathering fruit/vegetables that I have  agreed to donate to Harvest Pierce County's Gleaning Project. This agreement remains in effect through  December 31, <?php echo date('Y');?>, or until specifically revoked by me.</p>
<p>________________________________________    ______________<br />
    <em>Signature of Owner                                                     Date</em></p>
<p><?php echo $rowc['farm']." - ".$rowc['contact1'];?></p>
  </div>
    <div id="roster">
    <p class="newpage"></p>
     <table width="" border="1" cellpadding="5">
      <tr>
        <th scope="col">Harvest Leader</th>
        <th scope="col">ID number</th>
      </tr>
      <tr>
        <td><?php echo $rowl['fname']; ?> <?php echo $rowl['lname']; ?></td>
        <td><?php echo $rowl['ID_picker']; ?></td>
      </tr>
    </table>
    <h3>Roster</h3>
    
    <table width="95%" border="1" id="roster" >
   	  <tr><th><h3>Harvest roster</h3></th>
      </tr>
      <tr>
        <th class="tallrow" scope="col">Name, phone, carpool seats</th>
        <th width="20%">Checked in</th>
        <th>Status</th>
        <th>Emergency<br />contact</th>
	</tr>
<?php while($rowrost1 = mysqli_fetch_assoc($rsRoster)) { // do roster
 ?>
      <tr>
        <td class="tallrow"><?php echo $rowrost1['lname'].", ".$rowrost1['fname']." ".$rowrost1['phone']." [".$rowrost1['seats'].']'.'<br />['.jobnames($rowrost1['jobs']).']'; ?></td>
       	<td>&nbsp;</td>
       	<td class="centercell"><?php if($rowrost1['status'] != 'signup') { echo $rowrost1['status']; } ?></td>
        <td><?php echo $rowrost1['emerg']; ?>,<br /><?php echo $rowrost1['ephone']; ?></td>
      </tr>
<?php } // end of do roster1 ?>
    </table>
    <br />
  <table  border="1">
  <tr><th>Name</th>
  <th>Special</th>
  </tr>
<?php mysqli_data_seek($rsRoster,0);
while($rowrost1 = mysqli_fetch_assoc($rsRoster)) { // do roster
	if($rowrost1['special'] != '') echo '<tr><td>'.$rowrost1['lname'].", ".$rowrost1['fname'].'</td><td>'.$rowrost1['special'].'</td></tr>';
	} ?>
</table>
<p><!-- end #mainContent --></p>
</div>
  <div id="checklist">
    <p class="newpage"><strong>POST-GLEAN SUMMARY</strong></p>
    <p></p>
    <p>▪  Total pounds picked________ Total actual pickers__________ Total hours_______</p>
<p>▪ Number and height of ladders used_______________________________________  </p>
    <p>▪  The food pantry or  other recipient_______________________________________</p>
<p>▪ Pounds of produce donated___________________________________________</p>
    <p>▪  What is the ideal  number of volunteers to recruit next time? ___________</p>
    <p>▪  Other comments or  suggestions about the next harvest at this location (use the bottom of this  page).</p>
    <p>Harvest Leader ___________________________________   Date ____________</p>
    <p>Food Agency recipient signature (if available)______________________________</p>
<p>&nbsp;</p>
    <p>&nbsp;</p>
</div>
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
