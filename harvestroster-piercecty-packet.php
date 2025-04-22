<?php require_once('Connections/piercecty.php'); 
if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change,view,roster";
$MM_restrictGoTo = "login.php";
require_once('includes/levelcheck.php');
require_once('includes/converter.inc.php');
require_once('includes/jobsconversion.inc.php');
if(isset($_GET['harvesttemp'])) { $harvest= $_GET['harvesttemp']; } else {$harvest=0;}

$query="select * from harvests, sites where sites.ID_site=harvests.ID_site and ID_harvest=$harvest";
$rsInfo = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row = mysqli_fetch_assoc($rsInfo);

$queryasst = "select pickers.fname, pickers.lname, pickers.email, pickers.phone from rosters, pickers where ID_harvest=$harvest and rosters.ID_picker=pickers.ID_picker and rosters.status = 'intake' order BY pickers.lname, pickers.fname";
$rsAsst = mysqli_query($piercecty, $queryasst) or die(mysqli_error($piercecty));

$querywait = "select pickers.fname, pickers.lname, pickers.email, pickers.phone from rosters, pickers where ID_harvest=$harvest and rosters.ID_picker=pickers.ID_picker and rosters.status = 'waiting' order BY pickers.lname, pickers.fname";
$rsWait = mysqli_query($piercecty, $querywait) or die(mysqli_error($piercecty));

$queryroster1 = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone as phone, pickers.emerg, pickers.ephone, special, jobs from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvest and rosters.status <> 'waiting' and substring(pickers.lname,1,1)<'M' order BY lname, fname";
$rsRoster1 = mysqli_query($piercecty, $queryroster1) or die(mysqli_error($piercecty));

$queryroster2 = "select rosters.ID_picker, rosters.status, pickers.fname as fname, pickers.lname as lname, pickers.phone as phone, pickers.emerg, pickers.ephone, special, jobs from rosters, pickers where rosters.ID_picker=pickers.ID_picker and rosters.ID_harvest=$harvest and rosters.status <> 'waiting' and substring(pickers.lname,1,1)>'L' order BY lname, fname";
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
</head>
<body class="SH">
<div id="container">
<h2>Harvest number: <?php echo $row['ID_harvest']; ?>&nbsp;&nbsp;&nbsp;Date: <?php echo $row['h_date']; ?>
&nbsp;&nbsp;Site number: <?php echo $row['ID_site']; ?></h2>
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
        <th>Crops</th>
        <th>Number</th>
        <th>Location</th>
      </tr>
      <tr>
        <td><?php $crops=cropstring($harvest); echo $crops; ?></td>
        <td><?php echo $row['size']; ?></td>
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
        <th >Harvest Leader</th>
        <th >Phone</th>
        <th >Phone2</th>
        <th >Email</th>
      </tr>
      <tr>
        <td><?php echo $rowleader['fname']; ?> <?php echo $rowleader['lname']; ?></td>
        <td><?php echo $rowleader['phone']; ?></td>
        <td><?php echo $rowleader['phone2']; ?></td>
        <td><?php echo $rowleader['email']; ?></td>
      </tr>
      <tr>
        <th >co-Leader</th>
        <th >Phone</th>
        <th >Phone2</th>
        <th >Email</th>
      </tr>
      <tr>
        <td><?php echo $rowleader2['fname']; ?> <?php echo $rowleader2['lname']; ?></td>
        <td> <?php echo $rowleader2['phone']; ?></td>
        <td> <?php echo $rowleader2['phone2']; ?></td>
        <td><?php echo $rowleader2['email']; ?></td>
      </tr>
    </table>
	<br />
    <table  border="1">
      <tr>
        <th>Name</th>
        <th width="10%">Checked in</th>
        <th>Status</th>
        <th>Previous<br/>harvests</th>
        <th>Emergency<br />contact</th>
	</tr>
<?php while($rowrost1 = mysqli_fetch_assoc($rsRoster1)) { // do roster
 ?>
      <tr>
        <td><?php echo $rowrost1['lname'].", ".$rowrost1['fname']." ".$rowrost1['phone'].'<br />['.jobnames($rowrost1['jobs']).']';?>
        </td>
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
        <td><?php echo $rowrost1['emerg']; ?>,<br /><?php echo $rowrost1['ephone']; ?></td>
      </tr>
<?php } // end of do roster1 
while($rowrost2 = mysqli_fetch_assoc($rsRoster2)) { // do roster
 ?>
      <tr>
        <td><?php echo $rowrost2['lname'].", ".$rowrost2['fname']." ".$rowrost2['phone'].'<br />['.jobnames($rowrost2['jobs']).']';?></td>
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
        <td><?php echo $rowrost2['emerg']; ?>,<br /><?php echo $rowrost2['ephone']; ?></td>
      </tr>
<?php } // end of do roster2 ?>
    </table>
   <br />
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
<table  border="1" >
     <tr><th width="35%" ><strong>Waiting List</strong></th>
      </tr>
     <tr>
        <th>Name</th>
        <th>email</th>
        <th>phone</th>
      </tr>
      <?php while($rowwait = mysqli_fetch_assoc($rsWait)) {  ?>
      <tr>
        <td><?php echo $rowwait['lname']; ?>, <?php echo $rowwait['fname']; ?></td>
        <td><?php echo $rowwait['email']; ?></td>
        <td><?php echo $rowwait['phone']; ?></td>
      <?php }  ?>
    </table>
<!-- end #container --></div>
</body>
</html>
