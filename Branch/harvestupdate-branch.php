<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change,branch";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');

// get passed in harvest number
$ID_harvest=0; 
if(isset($_GET['harvesttemp']) and $_GET['harvesttemp']<>'') { $ID_harvest =  $_GET['harvesttemp']; }
$harvestq= "SELECT * FROM harvests WHERE ID_harvest = $ID_harvest";
$rsHarvest=mysqli_query($piercecty, $harvestq);
$harvestrow=mysqli_fetch_assoc($rsHarvest);

// get site info
$sq="select sites.ID_site, farm from sites, harvests where sites.ID_site=harvests.ID_site and ID_harvest=$ID_harvest";
$rsS=mysqli_query($piercecty, $sq ) or die(mysqli_error($piercecty)); 
$siterow=mysqli_fetch_assoc($rsS); 
if(isset($siterow)) extract($siterow); 
// set up crops dropdown
$cropq="select ID_crop, name from crops order by name";
$rsCrops = mysqli_query($piercecty, $cropq) or die(mysqli_error($piercecty));

if(isset($_POST['delete'])) {
	$delete="delete from harvests where ID_harvest=$ID_harvest";
	$rsDelete=mysqli_query($piercecty, $delete);
	$delete="delete from rosters where ID_harvest=$ID_harvest";
	$rsDelete=mysqli_query($piercecty, $delete);
	header("Location: branch-home.php"); exit(); }
if(isset($_POST['details'])) {
	$details="harvestroster-branch.php?harvesttemp=".$ID_harvest;
	header("Location: $details"); exit(); }
if(isset($_POST['attendance'])) {
	$attendance="rostermanager-branch.php?ID_harvest=".$ID_harvest;
	header("Location: $attendance"); exit(); }
if(isset($_POST['distributions'])) {
	$distributions="../Utilities/distributions.php?harvesttemp=".$ID_harvest;
	header("Location: $distributions"); exit(); }

if((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "harvestupdateform")) { // update form submitted
	$curyear=substr($harvestrow['h_date'],0,4);
	$status=$_POST['status'];
	if(substr($_POST['h_date'],-5)>'12-31' || substr($_POST['h_date'],-5)<'01-01') {
		$h_date=$curyear.'-00-00';
		$status='unsched';
		} else {
		$h_date=$_POST['h_date'];
		if($status=='unsched') $status='closed';
		}
$leader=$_POST['ID_leader']=='' ? 0 : $_POST['ID_leader'];

// process the donations     $donations[][ID_donation], $donations[][ID_crop], $donations[][pounds] 

$donations=$_POST['donations'];
//echo '<pre>';print_r($donations);
foreach($donations as $donation) {
	if(isset($donation['ID_donation']) and $donation['ID_donation']<>'' and $donation['ID_crop']=='') { //delete donation
		$deleteq="delete from donations where ID_donation=".$donation['ID_donation'];
		$rsDeleteq=mysqli_query($piercecty, $deleteq);
	} elseif(isset($donation['ID_donation']) and $donation['ID_donation']>0) { // update donation
		$updateq="update donations set ID_crop=".$donation['ID_crop'].", pounds=".$donation['pounds']." where ID_donation=".$donation['ID_donation'];
		$rsUpdateq=mysqli_query($piercecty, $updateq);
	} elseif($donation['ID_donation']=='' and $donation['ID_crop']<>'') { // insert donation
		$insertq="insert into donations (ID_harvest, ID_crop, pounds) values (".$ID_harvest.",".$donation['ID_crop'].",".$donation['pounds'].")";
		$rsInsertq=mysqli_query($piercecty, $insertq) or die(mysqli_error($piercecty));
		}
	} // end of all donations posted

$convarr=convarr($ID_harvest);
$calcwgt=0; 
foreach($convarr as $convrow) { $calcwgt+=$convrow['pounds'];}
$totwgt=$calcwgt;
$updateSQL = sprintf("UPDATE harvests SET ID_leader=$leader, ID_leader2=%s, h_date=%s, h_time=%s, duration=%s, calcwgt=$calcwgt, totwgt=$totwgt, pick_num=%s, type=%s, where_to=%s, otherinfo=%s, longinfo=%s, status=%s WHERE ID_harvest=$ID_harvest",
                       GetSQLValueString($_POST['ID_leader2'], "int"),
				GetSQLValueString($h_date, "text"),
                       GetSQLValueString($_POST['h_time'], "text"),
                       GetSQLValueString($_POST['duration'], "double"),
                       GetSQLValueString($_POST['pick_num'], "int"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['where_to'], "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['longinfo'], "text"),
			  GetSQLValueString($status, "text"));
$Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

 // update roster with leaders if NOT a past harvest
if($h_date>=date('Y-m-d') or substr($h_date,-5)=='00-00') {
	
$delldrs="delete from rosters where ID_harvest=$ID_harvest and status='leader'";
$rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));

$ldr=$leader;
$ldr2=$_POST['ID_leader2'];
$ldrseats=$_POST['ldrseats']<>'' ? $_POST['ldrseats'] : 0;
// delete roster entries for this leader and coleader in case they already signed up
if($ldr) {
	$delldrs="delete from rosters where ID_harvest=$ID_harvest and ID_picker=$ldr";
	 $rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));
}
if($ldr2) {
	$delldrs="delete from rosters where ID_harvest=$ID_harvest and ID_picker=$ldr2";
	$rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));
}

// insert leaders if <>'' or 0
if($ldr<>'' and $ldr>0 and is_numeric($ldr)) {
$insertldr="insert into rosters (ID_harvest, ID_picker, seats, regdate, status) values ($ID_harvest, $ldr, $ldrseats, now(), 'leader')";
$rsRoster=mysqli_query( $piercecty, $insertldr) or die(mysqli_error($piercecty));

$contactdateq="update pickers set contactdate=now() where ID_picker=$ldr";
$rsContact=mysqli_query($piercecty,$contactdateq);
}
	
if($ldr2<>'' and $ldr2>0 and is_numeric($ldr2)) {
$insertldr2="insert into rosters (ID_harvest, ID_picker, regdate, status) values ($ID_harvest, $ldr2, now(), 'leader')";

$rsRoster2=mysqli_query( $piercecty, $insertldr2);

$contactdateq="update pickers set contactdate=now() where ID_picker=$ldr2";
$rsContact=mysqli_query($piercecty,$contactdateq);
}
} // end of if not past harvest
header("Location: harvestupdate-branch.php?harvesttemp=$ID_harvest");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvest update</title>
<style type="text/css">
<!--
#add {
width:200px;
float:left;
height:70px;}
.cropdrop {width:100px;}
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="../datepick/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"h_date",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2017,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
	};
</script>
<?php include('../includes/branchhelp.inc.php'); ?>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav-branch.inc.php');?>
<div id="mainContent">
<a href="../help/help.php"  onClick="wopen('../help/help.php', 'popup', 640, 480); return false;"><div class="branchhelp">Page help</div></a>
<h2>Harvest update (branch)</h2>
    <form action="harvestupdate-branch.php?harvesttemp=<?php echo $ID_harvest;?>" name="harvestupdateform" method="POST">
      <table  border="1" cellpadding="1" cellspacing="1" id="harvestlist" width="1200px">
        <tr>
          <th >Harvest ID</th>
          <th >Leader</th>
          <th >Date<br />
          yyyy-mm-dd</th>
          <th >Time hh:mm<br />(24 hour)</th>
          <th >Duration</th>
          <th>Type</th>
          <th >Pickers<br />needed</th>
          <th >Status</th>
          <th ><?php if($ID_harvest) { ?><input type="submit" name="submit2" id="submit2" value="Save changes" /><?php } ?>
         <input type="hidden" name="MM_update" value="harvestupdateform" /></th>
        </tr>
          <tr class="centercell">
            <td><?php echo $harvestrow['ID_harvest']; ?></td>
          <td> <select name="ID_leader" id="ID_leader" onfocus="hints(this)">
          			<option value=0>--UNREGISTERED--</option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
		            </select></td>
            <td><input name="h_date" type="text" id="h_date"  onfocus="hints(this)" value="<?php echo $harvestrow['h_date']; ?>" size="10" maxlength="10" /></td>
            <td> <select name="h_time" id="h_time" onfocus="hints(this)">
          			<option value="00:00"> </option>
					<?php $times=mktime(7,30); 
					for($ct=1; $ct<=48;++$ct) {
					$times+=900;	 ?>          			
            		<option value="<?php echo date('H:i',$times); ?>" <?php if(substr($harvestrow['h_time'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            		<?php } ?>
		            </select></td>
            <td><input name="duration" type="text" id="duration"  onfocus="hints(this)" value="<?php echo $harvestrow['duration']; ?>" size="5" maxlength="5" /></td>
          <td><select style="width:100px;" name="type"  id="type" onfocus="hints(this)" >
		            <option value="" <?php if($harvestrow['type']=='') echo 'selected="selected"';?>>-select-</option>
		            <option value="Field" <?php if($harvestrow['type']=='Field') echo 'selected="selected"';?>>Field harvest</option>
		            <option value="Post-harvest" <?php if($harvestrow['type']=='Post-harvest') echo 'selected="selected"';?>>Post-harvest harvest</option>
		            <option value="Pickup" <?php if($harvestrow['type']=='Pickup') echo 'selected="selected"';?>>Pickup</option>
		            </select></td>
                    <td><input  style="width:60px;" name="pick_num" type="number" id="pick_num"  onfocus="hints(this)" value="<?php echo $harvestrow['pick_num']; ?>" size="5" maxlength="5" /></td>
          <td><select name="status"  id="status" onfocus="hints(this)" >
		            <option value="closed" <?php if( $harvestrow['status']=='closed') echo 'selected="selected"';?>>closed </option>
            		<option value="open" <?php if( $harvestrow['status']=='open') echo 'selected="selected"';?>>open</option>
            		<option value="unsched" <?php if( $harvestrow['status']=='unsched') echo 'selected="selected"';?>>unsched</option>
		            </select></td>
		  <th><?php if($ID_harvest) { ?><input onclick="return confirm('Do you really want to delete this havest? It cannot be undone.');" name="delete" type="submit" value="Delete" /><?php } ?></th>
          </tr>
<tr class="centercell">
   <th>Site</Site></th>
 <th >co-Leader</th>
<!-- <th>Where donated</th> -->
<th>Where delivered<br />
[use only when the agency is NOT on the Distributions dropdown menu]</th>
<th>Total weight</th>
<td></td>
<td></td>
<td></td>
<td></td>
<th><?php if($ID_harvest) { ?><input name="details" type="submit" value="Harvest details" /><?php } ?></th>
</tr>
<tr class="centercell">
<td><?php echo $ID_site.'<br />'.$farm;?> </td>
<td> <select name="ID_leader2" id="ID_leader2" onfocus="hints(this)">
       			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
           		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader2']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
        </select></td>
<td><input name="where_to" type="text" id="where_to"  onfocus="hints(this)" value="<?php echo $harvestrow['where_to']; ?>" size="15" maxlength="50" /></td>
<td><?php echo $harvestrow['totwgt']; ?></td>
<td></td>
<td></td>
<td></td>
<th><?php if($ID_harvest) { ?><input name="distributions" type="submit" value="Distributions" /><?php } ?></th>
<th><?php if($ID_harvest) { ?><input name="attendance" type="submit" value="Attendance" /><?php } ?></th>
</tr>
</table>
<br />
 <table border="1" cellpadding="1" cellspacing="1">
  <?php
  // print existing filled crop and pounds slots
  $donationsq="select * from donations where ID_harvest=$ID_harvest";
  $rsDonationsq=mysqli_query($piercecty, $donationsq) or die(mysqli_error($piercecty));
  $dcount=mysqli_num_rows($rsDonationsq);
  $x=0; 
  while($drow=mysqli_fetch_assoc($rsDonationsq)) {
  if($x%5==0) { ?><tr class="centercell"><th>Crop:<br />Pounds:</th><?php } // new row
  ?><td><select name="donations[<?php echo $x;?>][ID_crop]" class="cropdrop">
        <option value="" <?php if($drow['ID_crop']=='') echo 'selected="selected"';?>> - </option>
		<?php mysqli_data_seek($rsCrops,0); while($croprow = mysqli_fetch_assoc($rsCrops)) { ?>
        	<option value="<?php echo $croprow['ID_crop'];?>"
			<?php if($croprow['ID_crop']==$drow['ID_crop']) echo 'selected="selected"';?>><?php echo $croprow['name'];?></option>
        <?php } ?>
        </select><br />
		  <input type="text" name="donations[<?php echo $x;?>][pounds]" value="<?php echo $drow['pounds'];?>">
		  <input type="hidden" name="donations[<?php echo $x;?>][ID_donation]"  value="<?php echo $drow['ID_donation'];?>">
	</td>
	<?php
	++$x;
	if($x%5==0) {?> </tr> <?php }
  } // end of all stored donations
  for($y=1;$y<=5;++$y) { // do 5 blank slots to enter donations
    if($x%5==0) { ?><tr class="centercell"><th>Crop:<br />Pounds:</th><?php } // new row
  ?><td><select name="donations[<?php echo $x;?>][ID_crop]" class="cropdrop">
        <option value=""  selected="selected"> - </option>
		<?php mysqli_data_seek($rsCrops,0); while($croprow = mysqli_fetch_assoc($rsCrops)) { ?>
        	<option value="<?php echo $croprow['ID_crop'];?>"><?php echo $croprow['name'];?></option>
        <?php } ?>
        </select><br />
		  <input type="text" name="donations[<?php echo $x;?>][pounds]" value="0">
		  <input type="hidden" name="donations[<?php echo $x;?>][ID_donation]>"  value="">
	</td>
	<?php
	++$x; if($x%5==0) {?> </tr> <?php }
  } // end of 5 blanks
 ?> </table>
 s
<br />
<table border="1" cellpadding="1" cellspacing="1" id="harvestlist">
          <tr>
            <td align=center>Pre-signup info:</td>
            <td colspan = "12"><textarea name="otherinfo" cols="100" rows="2" id="otherinfo"  onfocus="hints(this)" ><?php echo $harvestrow['otherinfo']; ?></textarea></td>
          </tr>
 <tr>
<td align=center>Post signup info:</td>
<td><textarea name="longinfo" cols="100" rows="3"><?php echo $harvestrow['longinfo']; ?></textarea></td>
</tr>
</table>
</form>
<br class="clearfloat" />
</div>
</div>
</body>
</html>
