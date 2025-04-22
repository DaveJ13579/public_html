<?php 
require_once('Connections/piercecty.php'); 
require_once('includes/sqlcleaner.php');
include_once('includes/converter.inc.php');
require_once('includes/jobsconversion.inc.php');

if (!isset($_SESSION)) {  session_start(); }
$MM_authorizedUsers = "all,change,view,roster";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');
$harvestnumber = $_GET['harvesttemp'];
$colname_rsHarvestroster = "-1";
if (isset($_GET['harvesttemp'])) {
  $colname_rsHarvestroster = $_GET['harvesttemp'];
}
$query_rsHarvestroster = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone, pickers.emerg, pickers.ephone,seats, special, jobs from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvestnumber and rosters.status <> 'waiting' order BY lname, fname";
$rsHarvestroster = mysqli_query($piercecty, $query_rsHarvestroster) or die(mysqli_error($piercecty));

$colname_rsHarvestcrop = "-1";
if (isset($_GET['harvesttemp'])) {
  $colname_rsHarvestcrop = $_GET['harvesttemp'];
}
$query_rsHarvestcrop = "SELECT sites.farm, sites.contact1, sites.phone1, sites.phone2, sites.email1, sites.address, sites.city, sites.branch, sites.size, sites.location, sites.spray, sites.spray_text, harvests.pick_num, harvests.h_date, time_format(harvests.h_time, '%l:%i %p') as time, harvests.ID_harvest, sites.ID_site FROM harvests, sites WHERE ID_harvest = $colname_rsHarvestcrop AND harvests.ID_site = sites.ID_site";
$rsHarvestcrop = mysqli_query($piercecty, $query_rsHarvestcrop) or die(mysqli_error($piercecty));
$row_rsHarvestcrop = mysqli_fetch_assoc($rsHarvestcrop);
$totalRows_rsHarvestcrop = mysqli_num_rows($rsHarvestcrop);

$colname_rsHarvestcrop = "-1";
if (isset($_GET['harvesttemp'])) {   $colname_rsHarvestcrop = $_GET['harvesttemp']; }


$colname_rsHarvestleader = "-1";
if (isset($_GET['harvesttemp'])) {
  $colname_rsHarvestleader = $_GET['harvesttemp'];
}
$query_rsHarvestleader = sprintf("SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.address FROM harvests, pickers  WHERE ID_harvest = %s AND harvests.ID_leader = pickers.ID_picker", GetSQLValueString($colname_rsHarvestleader, "int"));
$rsHarvestleader = mysqli_query($piercecty, $query_rsHarvestleader) or die(mysqli_error($piercecty));
$row_rsHarvestleader = mysqli_fetch_assoc($rsHarvestleader);
$totalRows_rsHarvestleader = mysqli_num_rows($rsHarvestleader);

$colname_rsHarvestleader2 = "-1";
if (isset($_GET['harvesttemp'])) {   $colname_rsHarvestleader2 = $_GET['harvesttemp']; }

$query_rsHarvestleader2 = sprintf("SELECT pickers.lname, pickers.fname, pickers.phone, pickers.phone2, pickers.email, pickers.address FROM harvests, pickers  WHERE ID_harvest = %s AND harvests.ID_leader2 = pickers.ID_picker", GetSQLValueString($colname_rsHarvestleader2, "int"));
$rsHarvestleader2 = mysqli_query($piercecty, $query_rsHarvestleader2) or die(mysqli_error($piercecty));
$row_rsHarvestleader2 = mysqli_fetch_assoc($rsHarvestleader2);
$totalRows_rsHarvestleader2 = mysqli_num_rows($rsHarvestleader2);

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
	padding: 30;
	text-align: left; /* this centers the container in IE 5* browsers. The text is then set to the left aligned default in the #container selector */
	color: #000000;
	font-size: 100.01%;
	clear: none;
	background-color: #FFF;
	font-family: Arial, Helvetica, sans-serif;
	overflow: visible;
}
.newpage {
	page-break-before: always;
}
.keeptable {
	page-break-inside: avoid;
}
.SH #container {
	width: 8.5 in;  /* using 20px less than a full 800px width allows for browser chrome and avoids a horizontal scroll bar */
	height: 10 in;
	background: #FFFFFF; /* the auto margins (in conjunction with a width) center the page */
	border: 8px none #280800; /* this overrides the text-align: center on the body element. */
	font-size: 12pt;
	margin-bottom: 0px;
	padding: 0.75in;
} 
#header {
	width: 7in;
	font-size: 16pt;
}


h2 {
	font-size: 12pt;
}
th {
	background-color: #CCC;
	text-align: center;
	line-height: normal;
}
tr {
	background-color: #FFF;
	text-align: center;
}
.centercell {
	text-align: center;
}
.section {
	page-break-before: always;
	font-size: 12pt;
}
#checklist {
	font-size: 12pt;
	margin-left: 0in;
	margin-right: 0in;
	line-height: 150%;
}
.rightcell {
	text-align: right;
}
.tallrow {
	height: 18pt;
	font-size: 12pt;
	text-align: left;
	font-weight: bold;
}
.waiver {
	font-size: 11pt;
	font-style: italic;
}
.spacerrow {
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	background-color: #ccc;
	text-decoration: line-through;
}
.checkbox {
	border-top-width: thin;
	border-right-width: thin;
	border-bottom-width: thin;
	border-left-width: thin;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	list-style-type: square;
}
-->
</style>
<style type="text/css">
<!--
p.MsoNormal {
	margin:0in;
	margin-bottom:.0001pt;
	font-size:12pt;
	font-family:"Times New Roman", "serif";
}
.leftcell {
	text-align: left;
}
-->
</style>
</head>

<body class="SH">
<div id="container">
  <div id="header">
Harvest number: <?php echo $row_rsHarvestcrop['ID_harvest']; ?>&nbsp;&nbsp;&nbsp;Date: <?php echo $row_rsHarvestcrop['h_date']; ?>
       &nbsp;&nbsp;
       <!-- end #header -->
Crop number: <?php echo $row_rsHarvestcrop['ID_site']; ?></div>
  <div id="mainContent">
    <h3>Site information</h3>
    <table border="1">
      <tr>
        <th>Owner</th>
        <th>Phone</th>
        <th>Phone2</th>
        <th>Email</th>
        <th>Pickers needed</th>
      </tr>
      <tr>
        <td><?php echo $row_rsHarvestcrop['farm']; ?> <?php echo $row_rsHarvestcrop['contact1']; ?></td>
        <td><?php echo $row_rsHarvestcrop['phone1']; ?></td>
        <td><?php echo $row_rsHarvestcrop['phone2']; ?></td>
        <td><?php echo $row_rsHarvestcrop['email1']; ?></td>
        <td align="center"><?php echo $row_rsHarvestcrop['size']; ?></td>
      </tr>
      <tr>
        <th>Address</th>
        <th>City</th>
        <th>General area</th>
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td><?php echo $row_rsHarvestcrop['address']; ?></td>
        <td><?php echo $row_rsHarvestcrop['city']; ?></td>
        <td><?php echo $row_rsHarvestcrop['branch']; ?></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>Crop type</th>
        <th>Number</th>
        <th>Location</th>
      </tr>
      <tr>
        <td><?php $crops=cropstring($harvestnumber);echo $crops; ?></td>
        <td class="centercell"><?php echo $row_rsHarvestcrop['size']; ?></td>
        <td><?php echo $row_rsHarvestcrop['location']; ?></td>
      </tr>
      <tr>
        <th class="centercell">Spray?</th>
        <th>Spray info</th>
      </tr>
      <tr>
        <td align="center" class="centercell"><?php echo $row_rsHarvestcrop['spray']; ?></td>
        <td><?php echo $row_rsHarvestcrop['spray_text']; ?></td>
      </tr>
    </table>
    <h3>Harvest leader information</h3>
    <table width="" border="1">
      <tr>
        <th scope="col">Harvest Leader</th>
        <th scope="col">Phone</th>
        <th scope="col">Phone2</th>
        <th scope="col">Email</th>
      </tr>
      <tr>
        <td><?php echo $row_rsHarvestleader['fname']; ?> <?php echo $row_rsHarvestleader['lname']; ?></td>
        <td><?php echo $row_rsHarvestleader['phone']; ?></td>
        <td><?php echo $row_rsHarvestleader['phone2']; ?></td>
        <td><?php echo $row_rsHarvestleader['email']; ?></td>
      </tr>
      <tr>
        <th scope="col">co-Leader</th>
        <th scope="col">Phone</th>
        <th scope="col">Phone2</th>
        <th scope="col">Email</th>
      </tr>
      <tr>
        <td><?php echo $row_rsHarvestleader2['fname']; ?> <?php echo $row_rsHarvestleader2['lname']; ?></td>
        <td> <?php echo $row_rsHarvestleader2['phone']; ?></td>
        <td> <?php echo $row_rsHarvestleader2['phone2']; ?></td>
        <td><?php echo $row_rsHarvestleader2['email']; ?></td>
      </tr>
    </table>
    <h3>Harvest roster</h3>
    <p>For volunteers on the roster, put a check mark in the 'Checked in' space on the row with the picker's name. Add a number for minors that are accompanying the picker.</p>
    <p>If the volunteer's row says [no email] ask them if they want us to add an email address to the database.</p>
    <p>If  volunteers arrive who are not on the roster: </p>
    <p>1. Tell them that registration and sign up for the harvest are required.<br />
    2. Have them leave the line to get a picker registration form to fill out from another harvest assistant and then return to the line to hand in the form.</p>
    <p>If they say they are already registered volunteers, but are not on the roster, they still must fill out the volunteer registration form and hand it in.</p>
    <table width="95%" border="1" id="roster" >
   	  <tr><th><h3>Harvest roster</h3></th>
      </tr>
      <tr>
        <th class="tallrow" scope="col">Name, phone, carpool seats</th>
        <th width="20%">Checked in</th>
        <th>Status</th>
        <th class="centercell">Previous<br/>gleans</th>
        <th>Emergency<br />contact</th>
	</tr>
<?php while($rowrost1 = mysqli_fetch_assoc($rsHarvestroster)) { // do roster
 ?>
      <tr>
        <td class="tallrow"><?php echo $rowrost1['lname'].", ".$rowrost1['fname']." ".$rowrost1['phone']." [".$rowrost1['seats'].']'.'<br />['.jobnames($rowrost1['jobs']).']'; ?></td>
       	<td>&nbsp;</td>
       	<td class="centercell"><?php if($rowrost1['status'] != 'signup') { echo $rowrost1['status']; } ?></td>
				<?php  // calculate previous harvests attended
				$picker = $rowrost1['ID_picker']; 
				$hdate=$row_rsHarvestcrop['h_date'];
				$queryprev = "select count(ID_rosters) as previous from rosters, harvests where ID_picker=$picker and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='harvested')";
				$rsPrev = mysqli_query($piercecty, $queryprev) or die(mysqli_error($piercecty));
				$rowprev = mysqli_fetch_assoc($rsPrev);
				$previous=$rowprev['previous'];
				?>  
        <td class="centercell">
		<?php if($previous==0) {echo "First Harvest";} else {echo $previous; } ?> </td>
        <td><?php echo $rowrost1['emerg']; ?>,<br /><?php echo $rowrost1['ephone']; ?></td>
      </tr>
<?php } // end of do roster1 ?>
    </table>
    <br />
  <table  border="1">
  <tr><th>Name</th>
  <th>Special</th>
  </tr>
<?php mysqli_data_seek($rsHarvestroster,0);
while($rowrost1 = mysqli_fetch_assoc($rsHarvestroster)) { // do roster
	if($rowrost1['special'] != '') echo '<tr><td>'.$rowrost1['lname'].", ".$rowrost1['fname'].'</td><td>'.$rowrost1['special'].'</td></tr>';
	} ?>
</table>
    <p><!-- end #mainContent --></p>
  </div>
  <div id="checklist">
    <p class="newpage"><strong>POST-GLEAN SUMMARY</strong></p>
    <p><strong><em>Please enclose this roster and checklist and the following items in the envelope  provided and mail to the webmaster right after the glean.</em></strong></p>
    <p>1. Volunteer registration forms   □<br />
      2. Food donation form □<br />
    </p>
    <p>▪  Harvest  date:  <?php echo $row_rsHarvestcrop['h_date']; ?>    Harvest time: <?php echo $row_rsHarvestcrop['time']; ?></p>
    <p>▪  Site ID  number: <?php echo $row_rsHarvestcrop['ID_site']; ?>              Harvest ID number: <?php echo $row_rsHarvestcrop['ID_harvest']; ?></p>
<p>▪  leader:  <?php echo $row_rsHarvestleader['fname']; ?> <?php echo $row_rsHarvestleader['lname']; ?></p>
    <p>▪  The property or  landowner: <?php echo $row_rsHarvestcrop['farm']; ?> <?php echo $row_rsHarvestcrop['contact1']; ?>, <?php echo $row_rsHarvestcrop['phone1']; ?>, <?php echo $row_rsHarvestcrop['address']; ?>, <?php echo $row_rsHarvestcrop['city']; ?></p>
<p>▪  The crop: <?php echo $crops; ?></p>
<p>▪  Total pounds picked________ Total actual pickers__________ Total hours_______</p>
<p>▪ Number and height of ladders used_______________________________________  </p>
    <p>▪  The supervision of  children___________________________________________</p>
    <p>___________________________________________________________________</p>
    <p>▪  The food pantry or  other recipient_______________________________________</p>
    <p>▪ Pounds of produce donated___________________________________________</p>
    <p>▪  What is the ideal  number of volunteers to recruit next time? ___________</p>
    <p>▪  Other comments or  suggestions about the next harvest at this location (use the back of this  form).</p>
    <p>Leader ___________________________________   Date ____________</p>
<p>revised 9/2/12</p>
</div>
  <div id="footer">
    <!-- end #footer -->
  </div>
<!-- end #container --></div>
</body>
</html>
<?php
((mysqli_free_result($rsHarvestroster) || (is_object($rsHarvestroster) && (get_class($rsHarvestroster) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsHarvestcrop) || (is_object($rsHarvestcrop) && (get_class($rsHarvestcrop) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsHarvestleader) || (is_object($rsHarvestleader) && (get_class($rsHarvestleader) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsHarvestleader2) || (is_object($rsHarvestleader2) && (get_class($rsHarvestleader2) == "mysqli_result"))) ? true : false);

?>
