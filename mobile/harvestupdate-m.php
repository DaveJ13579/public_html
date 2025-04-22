<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {   $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }
// get passed in harvest or site number
$IDharvest=0; $harvestq="SELECT * FROM harvests WHERE ID_harvest = 0"; 

if(isset($_GET['sitedrop']) and $_GET['sitedrop']<>'') {
	$IDsite = $_GET['sitedrop'];
	$IDharvest=0;
	$harvestq= "SELECT * FROM harvests WHERE ID_site = $IDsite ORDER BY h_date DESC, ID_harvest DESC limit 1";
	}
if(isset($_GET['harvesttemp']) and $_GET['harvesttemp']<>'') {
	$IDharvest =  $_GET['harvesttemp'];
	$harvestq= "SELECT * FROM harvests WHERE ID_harvest = $IDharvest ORDER BY h_date DESC, ID_harvest DESC";
	}
if(isset($_POST['hiddenfield']) and $_POST['hiddenfield']<>'') {
	$IDharvest =  $_POST['hiddenfield'];
	$harvestq= "SELECT * FROM harvests WHERE ID_harvest = $IDharvest ORDER BY h_date DESC, ID_harvest DESC";
	}
// update site address form handling	
if(isset($_POST['hiddenfield2']) and $_POST['hiddenfield2']<>'') {
	$IDharvest =  $_POST['hiddenfield2'];
	$siteq2="select sites.ID_site from sites, harvests where sites.ID_site=harvests.ID_site and ID_harvest=$IDharvest";
	$rsSiteq2=mysqli_query($piercecty,$siteq2) or die(mysqli_error($piercecty));
	$siterow2=mysqli_fetch_assoc($rsSiteq2);
	$IDsite=$siterow2['ID_site'];
	$updateAddress=sprintf("update sites set address=%s, city=%s, state=%s, zip=%s where ID_site=$IDsite", 
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"));
$Result3 = mysqli_query($piercecty, $updateAddress) or die(mysqli_error($piercecty));
}
// set up site dropdown
$rsHarvest = mysqli_query($piercecty, $harvestq) or die(mysqli_error($piercecty));
$harvestrow = mysqli_fetch_assoc($rsHarvest);
$IDsite=$harvestrow['ID_site']; 

// set up crops dropdown
$cropq="select ID_crop, name from crops order by name";
$rsCrops = mysqli_query($piercecty, $cropq) or die(mysqli_error($piercecty));
// set up spots dropdown
$spotq="select ID_spot, name from spots where ID_spot<>0 order by ID_spot";
$rsSpots = mysqli_query($piercecty, $spotq) or die(mysqli_error($piercecty));

// get site address for possible update
$address=$city=$state=$zip='';
if(isset($IDharvest)) {
$siteq="select address, city, state, zip from sites, harvests where sites.ID_site=harvests.ID_site and ID_harvest=$IDharvest";
$rsAddress = mysqli_query($piercecty, $siteq) or die(mysqli_error($piercecty));
$siteaddress = mysqli_fetch_assoc($rsAddress);
$address=$siteaddress['address'];$city=$siteaddress['city'];$state=$siteaddress['state']; $zip=$siteaddress['zip'];
}
if(isset($_POST['details'])) {
	$details="../harvestroster.php?harvesttemp=".$IDharvest;
	header("Location: $details"); exit(); }
if(isset($_POST['attendance'])) {
	$attendance="attendance-m2.php?harvesttemp=".$IDharvest;
	header("Location: $attendance"); exit(); }

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
$gmap= !isset($_POST['gmap']) ? 'No' : 'Yes';
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
		$insertq="insert into donations (ID_harvest, ID_crop, pounds) values (".$IDharvest.",".$donation['ID_crop'].",".$donation['pounds'].")";
		$rsInsertq=mysqli_query($piercecty, $insertq) or die(mysqli_error($piercecty));
		}
	} // end of all donations posted

$convarr=convarr($IDharvest);
$calcwgt=0; 
foreach($convarr as $convrow) { $calcwgt+=$convrow['pounds'];}
$totwgt=$calcwgt;
$updateSQL = sprintf("UPDATE harvests SET ID_site=%s, ID_leader=$leader, ID_leader2=%s, h_date=%s, h_time=%s, duration=%s, spot=%s, carpool=%s, pooltime=%s, donwgt=%s, calcwgt=$calcwgt, totwgt=$totwgt, pick_num=%s, SHT=%s, type=%s, delivered=%s, otherinfo=%s, longinfo=%s, gmap=%s, status=%s, where_to=%s WHERE ID_harvest=%s",
                       GetSQLValueString($_POST['ID_site'], "int"),
                       GetSQLValueString($_POST['ID_leader2'], "int"),
							  GetSQLValueString($h_date, "date"),
                       GetSQLValueString($_POST['h_time'], "text"),
                       GetSQLValueString($_POST['duration'], "double"),
                       GetSQLValueString($_POST['spot'], "int"),
                       GetSQLValueString($_POST['carpool'], "text"),
                       GetSQLValueString($_POST['pooltime'], "text"),
                       GetSQLValueString($_POST['donwgt'], "int"),
                       GetSQLValueString($_POST['pick_num'], "int"),
                       GetSQLValueString($_POST['SHT'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['delivered'], "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['longinfo'], "text"),
							  GetSQLValueString($gmap, "text"),
							  GetSQLValueString($status, "text"),
                       GetSQLValueString($_POST['where_to'], "text"),
			   		   GetSQLValueString($_POST['hiddenfield'], "int"));

$Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

 // update roster with leaders if NOT a past harvest
if($h_date>=date('Y-m-d') or substr($h_date,-5)=='00-00') {
	
$delldrs="delete from rosters where ID_harvest=$IDharvest and status='leader'";
$rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));

$ldr=$leader;
$ldr2=$_POST['ID_leader2'];
$ldrseats=$_POST['ldrseats']<>'' ? $_POST['ldrseats'] : 0;
// delete roster entries for this leader and coleader in case they already signed up
if($ldr) {
	$delldrs="delete from rosters where ID_harvest=$IDharvest and ID_picker=$ldr";
	 $rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));
}
if($ldr2) {
	$delldrs="delete from rosters where ID_harvest=$IDharvest and ID_picker=$ldr2";
	$rsDelete=mysqli_query($piercecty, $delldrs) or die(mysqli_error($piercecty));
}

// insert leaders if <>'' or 0
if($ldr<>'' and $ldr>0 and is_numeric($ldr)) {
$insertldr="insert into rosters (ID_harvest, ID_picker, seats, regdate, status) values ($IDharvest, $ldr, $ldrseats, now(), 'leader')";
$rsRoster=mysqli_query( $piercecty, $insertldr) or die(mysqli_error($piercecty));

$contactdateq="update pickers set contactdate=now() where ID_picker=$ldr";
$rsContact=mysqli_query($piercecty,$contactdateq);
}
	
if($ldr2<>'' and $ldr2>0 and is_numeric($ldr2)) {
$insertldr2="insert into rosters (ID_harvest, ID_picker, regdate, status) values ($IDharvest, $ldr2, now(), 'leader')";

$rsRoster2=mysqli_query( $piercecty, $insertldr2);

$contactdateq="update pickers set contactdate=now() where ID_picker=$ldr2";
$rsContact=mysqli_query($piercecty,$contactdateq);
}
} // end of if not past harvest

$updateGoTo = "harvestupdate-m.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
<link href="piercecty-m.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
</script>
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
</head>
<body class="SH">
<div id="container">
<a href="../Utilities/PagesIndex.php">Index</a>
<p><strong><strong>Harvest update</strong></strong><br />
<form action="" method="get" name="filtersform">
<strong>Filters:</strong><br />
Harvest number: 
  <input size="7" maxlength="5" type="text" name="harvesttemp" id="harvesttemp" value="<?php echo $IDharvest ?>"/><br />
Select site to show its most recent harvest:<br />
<select name="sitedrop">
	    <option value="" selected="selected"> </option>
          <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' order by farm";
		  			  $rsSitesq=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
					  while ($siterowq=mysqli_fetch_assoc($rsSitesq)) {
					?><option style="width:400px;" value="<?php echo $siterowq['ID_site']; ?>" <?php if($siterowq['ID_site']==$IDsite) echo 'selected="selected"';?>><?php echo $siterowq['farm'].", ".$siterowq['address'].", ".$siterowq['city'].", ".$siterowq['crops'];?></option>
					<?php } ?>
</select>
<br /><input type="submit" name="submit" id="submit" value="Display harvest info" />
</form>
 <br />
<form action="<?php echo $editFormAction; ?>" id="harvestupdateform" name="harvestupdateform" method="POST">
      <table  border="1" cellpadding="1" cellspacing="1" id="harvestlist" >
          <tr><th><?php if($IDharvest) { ?><input type="submit" name="submit2" id="submit2" value="Save changes" /><?php } ?>
              <input type="hidden" name="MM_update" value="harvestupdateform" /></th>
   		  <th><?php if($IDharvest) { ?><input name="details" type="submit" value="Harvest details" /><?php } ?> <?php if($IDharvest) { ?><input name="attendance" type="submit" value="Attendance" /><?php } ?></th></tr>
        <tr><th >Harvest ID</th><td><?php echo $harvestrow['ID_harvest']; ?>              
        		<input name="hiddenfield" type="hidden" id="hiddenfield" value="<?php echo $harvestrow['ID_harvest']; ?>" /></td></tr>
          <tr><th >Site ID</th><td><input name="ID_site" type="text" value="<?php echo $harvestrow['ID_site']; ?>" size="5" maxlength="5" /></td></tr>
          <tr><th >Leader</th><td> <select name="ID_leader">
          			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
		            </select></td></tr>
          <tr><th >co-Leader</th><td> <select name="ID_leader2">
          			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader2']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
		            </select></td></tr>
          <tr><th >Date yyyy-mm-dd</th><td><input name="h_date" type="text" value="<?php echo $harvestrow['h_date']; ?>" size="10" maxlength="10" /></td></tr>
          <tr><th>Time hh:mm (24 hour)</th><td> <select name="h_time" id="h_time" onfocus="hints(this)">
          			<option value="00:00"> </option>
					<?php $times=mktime(7,30); 
					for($ct=1; $ct<=48;++$ct) {
					$times+=900;	 ?>          			
            		<option value="<?php echo date('H:i',$times); ?>" <?php if(substr($harvestrow['h_time'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            		<?php } ?>
		            </select></td></tr>
          <tr><th>Duration</th><td><input name="duration" type="text" value="<?php echo $harvestrow['duration']; ?>" size="5" maxlength="5" /></td></tr>
          <tr><th>Type</th><td><select style="width:100px;" name="type">
		            <option value="" <?php if($harvestrow['type']=='') echo 'selected="selected"';?>>-select-</option>
		            <option value="Field" <?php if($harvestrow['type']=='Field') echo 'selected="selected"';?>>Field harvest</option>
		            <option value="Post-harvest" <?php if($harvestrow['type']=='Post-harvest') echo 'selected="selected"';?>>Post-harvest harvest</option>
		            <option value="Pickup" <?php if($harvestrow['type']=='Pickup') echo 'selected="selected"';?>>Pickup</option>
		            </select></td></tr>
          <tr><th>Pickers needed</th><td><input  style="width:60px;" name="pick_num" type="number" value="<?php echo $harvestrow['pick_num']; ?>" size="5" maxlength="5" /></td></tr>
          <tr><th>Status</th><td><select name="status"  id="status" onfocus="hints(this)" >
		            <option value="closed" <?php if( $harvestrow['status']=='closed') echo 'selected="selected"';?>>closed </option>
            		<option value="open" <?php if( $harvestrow['status']=='open') echo 'selected="selected"';?>>open</option>
            		<option value="unsched" <?php if( $harvestrow['status']=='unsched') echo 'selected="selected"';?>>unsched</option>
		            </select></td></tr>
        <tr><th>Delivered</th><td><select name="delivered"  id="delivered" onfocus="hints(this)" >
		            <option value="" <?php if($harvestrow['delivered']=='') echo 'selected="selected"';?>>-select-</option>
		            <option value="Yes" <?php if($harvestrow['delivered']=='Yes') echo 'selected="selected"';?>>Yes</option>
		            <option value="No" <?php if($harvestrow['delivered']=='No') echo 'selected="selected"';?>>No</option>
		            </select></td></tr>
          <tr><th>Where donated</th><td><input name="where_to" type="text" value="<?php echo $harvestrow['where_to']; ?>" size="35" maxlength="50" /></td></tr>
<tr><th>Meeting spot</th><td><select name="spot" class="cropdrop">
        <option value="0" <?php if($harvestrow['carpool']=='none') echo 'selected="selected"';?>> No carpool </option>
		<?php mysqli_data_seek($rsSpots,0); while($spotrow = mysqli_fetch_assoc($rsSpots)) { ?>
        	<option value="<?php echo $spotrow['ID_spot'];?>"
			<?php if($spotrow['ID_spot']==$harvestrow['spot']) echo 'selected="selected"';?>><?php echo $spotrow['name'];?></option>
        <?php } ?>
        </select></td></tr>
<tr><th>Carpool</th><td><select name="carpool">
		            <option value="none" <?php if($harvestrow['carpool']=='none') echo 'selected="selected"';?>>none</option>
            		<option value="option" <?php if($harvestrow['carpool']=='option') echo 'selected="selected"';?>>option</option>
            		<option value="all" <?php if($harvestrow['carpool']=='all') echo 'selected="selected"';?>>all</option>
		            </select></td></tr>
<tr><th>Carpool time</th><td> <select name="pooltime" id="pooltime" onfocus="hints(this)">
          			<option value="00:00"> </option>
					<?php $times=mktime(7,30); 
					for($ct=1; $ct<=48;++$ct) {
					$times+=900;	 ?>          			
            		<option value="<?php echo date('H:i',$times); ?>" <?php if(substr($harvestrow['pooltime'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            		<?php } ?>
		            </select></td></tr>
<?php // get leader's carpool seats
if(isset($IDharvest)) {
$ldrseatsq="select seats from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and harvests.ID_harvest=$IDharvest and rosters.ID_picker=harvests.ID_leader";
$rsLdrseats=mysqli_query($piercecty, $ldrseatsq) or die(mysqli_error($piercecty));
$leaderseatsrow=mysqli_fetch_assoc($rsLdrseats);
$ldrseats=$leaderseatsrow['seats'];} 
else {$ldrseats=0;}
?>
<tr><th>Leader carpool seats</th><td><input style="width:60px;" name="ldrseats" type="number" value="<?php echo $ldrseats; ?>" size="5" maxlength="2" /></td></tr>
<tr><th>Select team only? </th><td><select name="SHT"  id="SHT" onfocus="hints(this)" >
		            <option value="Yes" <?php if($harvestrow['SHT']=='Yes') echo 'selected="selected"';?>>Yes</option>
		            <option value="No" <?php if($harvestrow['SHT']<>'Yes') echo 'selected="selected"';?>>No</option>
		            </select></td></tr>
<tr><th>Calculated weight</th><td><?php echo $harvestrow['calcwgt']; ?> pounds</td></tr>
<tr><th>Total weight</th><td><?php echo $harvestrow['totwgt'];  ?> pounds</td></tr>
<tr><th>Donated weight</th><td><input style="width:60px" name="donwgt" type="text" value="<?php echo $harvestrow['donwgt']; ?>" size="5" maxlength="5" /></td></tr>
</table>
<br />
<table border="1" cellpadding="1" cellspacing="1" id="harvestlist">
  <?php
  // print existing filled crop and pounds slots
  $donationsq="select * from donations where ID_harvest=$IDharvest";
  $rsDonationsq=mysqli_query($piercecty, $donationsq) or die(mysqli_error($piercecty).' line 333');
  $dcount=mysqli_num_rows($rsDonationsq);
  $x=0; 
  ?><tr class="centercell">
<th>Crop</th>
<th>Pounds</th></tr>
  <?php while($drow=mysqli_fetch_assoc($rsDonationsq)) { ?>
  <tr><td><select name="donations[<?php echo $x;?>][ID_crop]" class="cropdrop">
        <option value="" <?php if($drow['ID_crop']=='') echo 'selected="selected"';?>> - </option>
		<?php mysqli_data_seek($rsCrops,0); while($croprow = mysqli_fetch_assoc($rsCrops)) { ?>
        	<option value="<?php echo $croprow['ID_crop'];?>"
			<?php if($croprow['ID_crop']==$drow['ID_crop']) echo 'selected="selected"';?>><?php echo $croprow['name'];?></option>
        <?php } ?>
        </select>
		  </td><td>
		  <input type="text" name="donations[<?php echo $x;?>][pounds]" value="<?php echo $drow['pounds'];?>"  size="5" maxlength="5" >
		  <input type="hidden" name="donations[<?php echo $x;?>][ID_donation]"  value="<?php echo $drow['ID_donation'];?>">
	</td></tr>
	<?php  
	++$x;
	} // end of all stored donations
	
  for($y=1;$y<=2;++$y) { // do 2 blank slots to enter donations
  ?>
  <tr><td><select name="donations[<?php echo $x;?>][ID_crop]" class="cropdrop">
        <option value=""  selected="selected"> - </option>
		<?php mysqli_data_seek($rsCrops,0); while($croprow = mysqli_fetch_assoc($rsCrops)) { ?>
        	<option value="<?php echo $croprow['ID_crop'];?>"><?php echo $croprow['name'];?></option>
        <?php } ?>
        </select>
		  </td><td>
		  <input type="text" name="donations[<?php echo $x;?>][pounds]" value="0"  size="5" maxlength="5" >
		  <input type="hidden" name="donations[<?php echo $x;?>][ID_donation]>"  value="">
	</td></tr>
	<?php
	++$x;
  } // end of 2 blanks
 ?> </table>
<br />
<table border="1" cellpadding="1" cellspacing="1" id="harvestlist" >
<tr><td style="width:100px;">Pre-signup info:</td>
<td><textarea name="otherinfo" cols="60" rows="2" id="otherinfo"  onfocus="hints(this)" ><?php echo $harvestrow['otherinfo']; ?></textarea></td></tr>
<tr><td>Post signup info:
       <p>&nbsp;</p>
       <p>Include Google<br />Maps directions: 
            <input type="checkbox" name="gmap" id="gmap" onmouseover="hints(this)"<?php if( $harvestrow['gmap']=='Yes') echo 'checked="checked"';?> />
          </p></td>
            <td><textarea name="longinfo" cols="60" rows="10" id="longinfo"  onfocus="hints(this)"><?php echo $harvestrow['longinfo']; ?></textarea></td>
          </tr>
       </table>
        </form>
        <br />
<table border="1" cellpadding="1" cellspacing="1" >
<form name="updateaddress" method="POST"> 
<tr><td><?php if($IDharvest) { ?><input type="submit" name="submit3" value="Update site address" /><?php } ?>
           <input type="hidden" name="hiddenfield2" value="<?php echo $IDharvest;?>" /></td></tr>
<tr><th>Address</th><td><input name="address" type="text" value="<?php echo $address; ?>" size="30" maxlength="80" /></td></tr>
<tr><th>City</th><td><input name="city" type="text" value="<?php echo $city ?>" size="30" maxlength="80" /></td></tr>
<tr><th>State</th><td><input name="state" type="state" value="<?php echo $state; ?>" size="5" maxlength="2" /></td></tr>
<tr><th>Zip</th><td><input name="zip" type="text" value="<?php echo $zip; ?>" size="8" maxlength="5" /></td></tr>
</form>
</table>
<br />
      <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>
