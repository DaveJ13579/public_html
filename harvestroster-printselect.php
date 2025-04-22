<?php require_once('Connections/piercecty.php'); 
include_once('includes/converter.inc.php');
require_once('includes/jobsconversion.inc.php');

if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change,view,roster";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');

if(isset($_POST['harvest'])) { $harvest= $_POST['harvest']; } else {$harvest=0;}

$query="select * from harvests, sites where sites.ID_site=harvests.ID_site and ID_harvest=$harvest";
$rsInfo = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsInfo);

$queryasst = "select pickers.fname, pickers.lname, pickers.email, pickers.phone from rosters, pickers where ID_harvest=$harvest and rosters.ID_picker=pickers.ID_picker and rosters.status = 'intake' order BY lname, fname";
$rsAsst = mysqli_query($piercecty, $queryasst) or die(mysqli_error($piercecty));

$querywait = "select pickers.fname, pickers.lname, pickers.email, pickers.phone from rosters, pickers where ID_harvest=$harvest and rosters.ID_picker=pickers.ID_picker and rosters.status = 'waiting' order BY lname, fname";
$rsWait = mysqli_query($piercecty, $querywait) or die(mysqli_error($piercecty));

$queryroster1 = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone as phone, pickers.emerg, pickers.ephone, seats, special, jobs from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvest and rosters.status <> 'waiting' and substring(pickers.lname,1,1)<'M' order BY lname, fname";
$rsRoster1 = mysqli_query($piercecty, $queryroster1) or die(mysqli_error($piercecty));

$queryroster2 = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone as phone, pickers.emerg, pickers.ephone, seats, special, jobs from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvest and rosters.status <> 'waiting' and substring(pickers.lname,1,1)>'L' order BY lname, fname";
$rsRoster2 = mysqli_query($piercecty, $queryroster2) or die(mysqli_error($piercecty));

$queryleader="select lname, fname, phone, phone2, email from harvests, pickers  where ID_harvest=$harvest and  harvests.ID_leader=pickers.ID_picker";
$rsLeader = mysqli_query($piercecty, $queryleader) or die(mysqli_error($piercecty));
$rowleader = mysqli_fetch_assoc($rsLeader);

$queryleader2="select lname, fname, phone, phone2, pickers.email from harvests, pickers  where ID_harvest=$harvest and  harvests.ID_leader2=pickers.ID_picker";
$rsLeader2 = mysqli_query($piercecty, $queryleader2) or die(mysqli_error($piercecty));
$rowleader2 = mysqli_fetch_assoc($rsLeader2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest information and roster</title>
<link href="Print-separate.css" rel="stylesheet" type="text/css" />
<style type="text/css" media="print">
 div.page
      {
        page-break-before: always;
        page-break-inside: avoid;
      }
    </style>

</head>
<body class="SH">
<div id="container">
<?php if(isset($_POST['info'])) { // print info section ?>
<div id="header">
Harvest number: <?php echo $row['ID_harvest']; ?>&nbsp;&nbsp;&nbsp;Date: <?php echo $row['h_date']; ?>
       &nbsp;&nbsp;
       <!-- end #header -->
Crop number: <?php echo $row['ID_site']; ?>
</div>
<div id="mainContent">
    <h3>Crop information</h3>
    <table border="1">
      <tr>
        <th>Owner</th>
        <th>Phone</th>
        <th>Phone2</th>
        <th>Email</th>
        <th>Pickers needed</th>
      </tr>
      <tr>
        <td><?php echo $row['farm']; ?> <?php echo $row['contact1']; ?></td>
        <td><?php echo $row['phone1']; ?></td>
        <td><?php echo $row['phone2']; ?></td>
        <td><?php echo $row['email1']; ?></td>
        <td align="center"><?php echo $row['pick_num']; ?></td>
      </tr>
      <tr>
        <th>Address</th>
        <th>City</th>
        <th>Branch</th>
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td><?php echo $row['address']; ?></td>
        <td><?php echo $row['city']; ?></td>
        <td><?php echo $row['branch']; ?></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>Crop type</th>
        <th>Number</th>
        <th>Location</th>
      </tr>
      <tr>
        <td><?php $crops=cropstring($harvest); echo $crops; ?></td>
        <td class="centercell"><?php echo $row['size']; ?></td>
        <td><?php echo $row['location']; ?></td>
      </tr>
      <tr>
        <th class="centercell">Spray?</th>
        <th>Spray info</th>
      </tr>
      <tr>
        <td align="center" class="centercell"><?php echo $row['spray']; ?></td>
        <td><?php echo $row['spray_text']; ?></td>
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
        <td><?php echo $rowleader['fname']; ?> <?php echo $rowleader['lname']; ?></td>
        <td><?php echo $rowleader['phone']; ?></td>
        <td><?php echo $rowleader['phone2']; ?></td>
        <td><?php echo $rowleader['email']; ?></td>
      </tr>
      <tr>
        <th scope="col">co-Leader</th>
        <th scope="col">Phone</th>
        <th scope="col">Phone2</th>
        <th scope="col">Email</th>
      </tr>
      <tr>
        <td><?php echo $rowleader2['fname']; ?> <?php echo $rowleader2['lname']; ?></td>
        <td> <?php echo $rowleader2['phone']; ?></td>
        <td> <?php echo $rowleader2['phone2']; ?></td>
        <td><?php echo $rowleader2['email']; ?></td>
      </tr>
    </table>
  </div>
<?php } // end of print info section  ?>
   
<?php if(isset($_POST['scouting'])) { // print scouting section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['scouting-brk'])) $pbreak="page" ;
?>
<div class="<?php echo $pbreak;?>">
<p><strong>Crop Scouting Information</strong></p>
 <p>Describe the crop:</p>
<p>&nbsp;</p>
    <p></p>
<p>When will it  be ripe? _______________</p>
<p>Crop  quality:  Check for pests, over-ripeness, other signs of damage. </p>
    <p>Are ladders  needed? ______ How many? _____  What size?  __________   Is area flat? _____________</p>
<p>At what height  range is the fruit?   __________    How many pounds of produce are available? _________</p>
<p>Describe parking areas ___________________________________</p>
<p>Diagram the  property on the  other side if necessary.  Include any  special concerns for this property. </p>
    <p> How many pickers are needed?__________ How many harvest sessions are needed?  __________</p>
<p>Ask the property owner to sign and date the Temporary  Right-of-Entry Authorization Form</p>
    <p>Discuss  harvest dates with property owner   (Tell property owner that the date needs to be confirmed with Harvest Pierce County's Gleaning Project to avoid conflicts and ensure equipment is available.)</p>
    </div>
 <?php } // end of print scouting section ?>

<?php if(isset($_POST['auth'])) { // print auth section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['auth-brk'])) $pbreak="page" ;
?>
<div class="<?php echo $pbreak;?>">
    <p ><strong>TEMPORARY RIGHT OF ENTRY AUTHORIZATION</strong><br />
    </p>
    <p>Crop registry  number: <?php echo $row['ID_site']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop type(s): <?php $crops; ?><br />
    </p>
    <p>Harvest Pierce County's Gleaning Project  members and volunteers are granted permission to enter onto my property located at <?php echo $row['address'].", ".$row['city'].", ".$row['state']." ".$row['zip']; ?> for the sole purpose of picking and/or gathering fruit/vegetables that I have  agreed to donate to Harvest Pierce County's Gleaning Project. I may revoke this permission at any time by contating Harvest Pierce County's Gleaning Project.</p>
<p>________________________________________    ______________<br />
    <em>Signature of Owner                                                     Date</em></p>
<p><?php echo $row['contact1']." - ".$row['farm'];?></p>
 </div>
<?php } // end of print auth section ?>

<?php if(isset($_POST['roster1'])) { // print roster section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['roster1-brk'])) $pbreak="page" ;
?>
  <div class="<?php echo $pbreak;?>">
    <table width="95%" border="1" id="roster" >
   	  <tr><th><h3>Harvest roster A-L</h3></th>
      </tr>
      <tr>
        <th class="tallrow" scope="col">Name, phone, carpool seats</th>
        <th width="20%">Checked in</th>
        <th>Status</th>
        <th class="centercell">Previous<br/>harvests</th>
        <th>Emergency<br />contact</th>
	</tr>
<?php while($rowrost1 = mysqli_fetch_assoc($rsRoster1)) { // do roster
 ?>
      <tr>
        <td class="tallrow"><?php echo $rowrost1['lname'].", ".$rowrost1['fname']." ".$rowrost1['phone'].", [".$rowrost1['seats'].']'.'<br />['.jobnames($rowrost1['jobs']).']';?></td>
       	<td>&nbsp;</td>
       	<td class="centercell"><?php if($rowrost1['status'] != 'signup') { echo $rowrost1['status']; } ?></td>
				<?php  // calculate previous harvests attended
				$picker = $rowrost1['ID_picker']; 
				$hdate=$row['h_date'];
				$queryprev = "select count(ID_rosters) as previous from rosters, harvests where ID_picker=$picker and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='intake' or rosters.status='harvested')";
				$rsPrev = mysqli_query($piercecty, $queryprev) or die(mysqli_error($piercecty));
				$rowprev = mysqli_fetch_assoc($rsPrev);
				$previous=$rowprev['previous'];
				?>  
        <td class="centercell">
		<?php if($previous==0) {echo "First Harvest";} else {echo $previous; } ?> </td>
        <td><?php echo $rowrost1['emerg']; ?>, <?php echo $rowrost1['ephone']; ?></td>
      </tr>
<?php } // end of do roster1 ?>
    </table>
</div>

<?php } // end of print roster1 section ?>

<?php if(isset($_POST['roster2'])) { // print roster 2 section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['roster2-brk'])) $pbreak="page" ;
?>
  <div class="<?php echo $pbreak;?>">
  <p>&nbsp;</p>
         <table width="95%" border="1" id="roster" >
   	  <tr><th scope="col"><h3>Harvest roster M-Z</h3></th>
      </tr>
      <tr>
        <th class="tallrow" scope="col">Name, phone, carpool seats</th>
        <th width="20%">Checked in</th>
        <th>Status</th>
        <th>Previous<br/>harvests</th>
        <th>Emergency<br />contact</th>
	</tr>
<?php while($rowrost2 = mysqli_fetch_assoc($rsRoster2)) { // do roster
 ?>
      <tr>
        <td class="tallrow"><?php echo $rowrost2['lname'].", ".$rowrost2['fname']." ".$rowrost2['phone'].", [".$rowrost2['seats'].']'.'<br />['.jobnames($rowrost2['jobs']).']';?></td>
       	<td>&nbsp;</td>
       	<td class="centercell"><?php if($rowrost2['status'] != 'signup') { echo $rowrost2['status']; } ?></td>
				<?php  // calculate previous harvests attended
				$picker = $rowrost2['ID_picker']; 
				$hdate=$row['h_date'];
				$queryprev = "select count(ID_rosters) as previous from rosters, harvests where ID_picker=$picker and harvests.ID_harvest=rosters.ID_harvest and harvests.h_date<'$hdate' and (rosters.status='leader' or rosters.status='intake' or rosters.status='harvested')";
				$rsPrev = mysqli_query($piercecty, $queryprev) or die(mysqli_error($piercecty));
				$rowprev = mysqli_fetch_assoc($rsPrev);
				$previous=$rowprev['previous'];
				?>  
        <td class="centercell">
		<?php if($previous==0) {echo "First Harvest";} else {echo $previous; } ?> </td>
        <td><?php echo $rowrost2['emerg']; ?>, <?php echo $rowrost2['ephone']; ?></td>
      </tr>
<?php } // end of do roster2 ?>
    </table>
</div>
<?php } // end of print roster2 section ?>

<?php if(isset($_POST['special'])) { // print special section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['special-brk'])) $pbreak="page" ;
?>
  <div class="<?php echo $pbreak;?>">
  <p>&nbsp;</p>
  <table  border="1">
  <tr><th>Name</th>
  <th>Special</th>
  </tr>
<?php mysqli_data_seek($rsRoster1,0);
while($rowrost1 = mysqli_fetch_assoc($rsRoster1)) { // do roster
	if($rowrost1['special'] != '') echo '<tr><td>'.$rowrost1['lname'].", ".$rowrost1['fname'].'</td><td>'.$rowrost1['special'].'</td></tr>';
	}
mysqli_data_seek($rsRoster2,0);
while($rowrost2 = mysqli_fetch_assoc($rsRoster2)) { // do roster
	if($rowrost2['special'] != '') echo '<tr><td>'.$rowrost2['lname'].", ".$rowrost2['fname'].'</td><td>'.$rowrost2['special'].'</td></tr>';
	}	?>
</table>
<br />
</div>
<?php } // end of print special section ?>


<?php if(isset($_POST['waiting'])) { // print waiting section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['waiting-brk'])) $pbreak="page" ;
?>
<div class="<?php echo $pbreak;?>">
  <p>&nbsp;</p>

    <table width="70%" border="1" >
     <tr><th width="35%" scope="col"><strong>WAITING LIST</strong></th>
      </tr>
     <tr>
        <th class="tallrow" >Name</th>
        <th class="tallrow" >email</th>
        <th class="tallrow" >phone</th>
      </tr>
      <?php while($rowwait = mysqli_fetch_assoc($rsWait)) {  ?>
      <tr>
        <td class="tallrow"><?php echo $rowwait['lname']; ?>, <?php echo $rowwait['fname']; ?></td>
        <td class="tallrow"><?php echo $rowwait['email']; ?></td>
        <td class="tallrow"><?php echo $rowwait['phone']; ?></td>
      <?php }  ?>
    </table>
  </div>
<?php } // end of print waiting section ?>

<?php if(isset($_POST['assist'])) { // print assistants section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['assist-brk'])) $pbreak="page" ;
?>

    <div class="<?php echo $pbreak;?>">
  <p>&nbsp;</p>
    <table width="70%" border="1" >
   	  <tr>
   	    <th width="35%" scope="col"><strong>GLEAN ASSISTANTS</strong></th>
      </tr>
      <tr>
        <th class="tallrow" >Name</th>
        <th class="tallrow" >email</th>
        <th class="tallrow" >phone</th>
      </tr>
      <?php while($rowasst = mysqli_fetch_assoc($rsAsst)) {  ?>
      <tr>
        <td class="tallrow"><?php echo $rowasst['lname']; ?>, <?php echo $rowasst['fname']; ?></td>
        <td class="tallrow"><?php echo $rowasst['email']; ?></td>
        <td class="tallrow"><?php echo $rowasst['phone']; ?></td>
      <?php }  ?>
    </table>
  </div>
<?php } // end of print assistants section ?>

<?php if(isset($_POST['summary'])) { // print summary section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['summary-brk'])) $pbreak="page" ;
 ?> 
  
  <div class="<?php echo $pbreak;?>">
    <p ><strong>POST-GLEAN SUMMARY</strong></p>
    <p>Please enclose this roster and checklist and the following items in the envelope  provided and mail to the webmaster right after the harvest.</p>
    <p>1. Volunteer registration forms   □<br />
      2. Food donation form □<br />
    </p>
    <p>▪  Harvest  date:  <?php echo $row['h_date']; ?>    Harvest time: <?php echo $row['h_time']; ?></p>
    <p>▪  Crop ID  number: <?php echo $row['ID_site']; ?>              Harvest ID number: <?php echo $row['ID_harvest']; ?></p>
<p>▪  Harvest leader:  <?php echo $rowleader['fname']; ?> <?php echo $rowleader['lname']; ?></p>
    <p>▪  The property or  landowner: <?php echo $row['farm']; ?>, <?php echo $row['contact1']; ?>, <?php echo $row['phone1']; ?>, <?php echo $row['address']; ?>, <?php echo $row['city']; ?></p>
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
<p>&nbsp;</p>
</div>
<?php } // end of summary section ?>

<?php if(isset($_POST['donation'])) { // print donation section
$pbreak='nopage';
if($_POST['pages']=='separate' or isset($_POST['donation-brk'])) $pbreak="page" ;
 ?> 
  <div class="<?php echo $pbreak;?>">
  <p></p>
  <strong>Harvest Pierce County's Gleaning Project - FOOD DONATION FORM</strong></p>
  <p><br />
    Harvest date: <?php echo $row['h_date']; ?><strong>   </strong>Harvest  number: <strong></strong><?php echo $row['ID_harvest']; ?><br />
  <strong>                                                    </strong><br />
  <strong>Donor Information</strong><br />
    <strong></strong><br />
    Harvest Leader: <?php echo $rowleader['fname']; ?> <?php echo $rowleader['lname']; ?></p>
  <p>Donor name: <?php echo $row['farm']; ?> - <?php echo $row['contact1']; ?><br />
    <br />
    Type and amount of produce  harvested <em>(e.g. 40 lbs apples) </em></p>
  <p>_________________________________________________________      </p>
  <p><strong>Recipient Agency Information</strong></p>
  <p>Recipient name_____________________________________________</p>
  <p>Agency name_______________________________________________</p>
  <p>Address___________________________________________________</p>
  <p>Type and amount of  produce accepted (<em>e.g. 20 lbs apples)</em></p>
  <p><em>__________________________________________________________</em></p>
  <p> Leader signature_____________________________________     </p>
  <p>Recipient signature__________________________________________</p>
  <br class="clearfloat" />
  </div>
  <?php } // end of donation section ?>

<!-- end #container --></div>
</body>
</html>
