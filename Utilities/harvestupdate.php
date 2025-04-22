<?php 
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//echo '<pre>';print_r($_POST);

$qstring=''; $selfstring='';
if(isset($_SERVER['QUERY_STRING'])) {$qstring=$_SERVER['QUERY_STRING']; }
if(isset($_SERVER['PHP_SELF'])) {$selfstring=htmlspecialchars($_SERVER['PHP_SELF']); }
if(!isset($_GET['m'])) { 
// no 'm' query variable set, so must find out if desk or mobile version is needed,
// then add the right 'm', and query string, to the file name and send to that page
echo "<script language=\"JavaScript\">     
<!--
if(navigator.userAgent.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile/i)){
	document.location=\"$selfstring?m=mobile&$qstring\"; }
	else {
	document.location=\"$selfstring?m=desk&$qstring\"; }
//-->     
</script>";
}
// to get here, must have an 'm' set in query string. Go to mobile or drop down to this file
if(isset($_GET['m']) && $_GET['m']=='mobile') {
header("Location: ../mobile/harvestupdate-m.php?m=mobile&$qstring"); exit();}     

require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
include_once('../includes/converter.inc.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {   $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

// picker finder search
$textin = "";
if (isset($_POST['textin'])) {$textin = $_POST['textin'];}
if (is_numeric($textin)) { 
$ID_picker = intval($textin);
$pickerq = sprintf("SELECT ID_picker, lname, fname FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsName = mysqli_query($piercecty, $pickerq) or die(mysqli_error($piercecty));
$pickerrow = mysqli_fetch_assoc($rsName);
} else {
$sfield='lname'; 
if(substr($textin,0,1)==' ') { $sfield='fname'; $textin=trim($textin); }
if(substr($textin,0,1)=='-') { $sfield='email'; $textin=substr($textin,1,strlen($textin)-1);
 }
$long=strlen(stripslashes($textin));
if($long==0) {$long=1; }
$pickerq = "SELECT ID_picker, lname, fname FROM pickers WHERE left($sfield,'$long') = '$textin' ORDER BY ID_picker ASC";
$rsName = mysqli_query($piercecty, $pickerq) or die(mysqli_error($piercecty));
$pickerrow = mysqli_fetch_assoc($rsName);
}

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
$IDharvest=$harvestrow['ID_harvest']; 
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

if(isset($_POST['delete']) && $_SESSION['MM_UserGroup']=='all')  {
	$delete="harvestdelete.php?harvesttemp=".$IDharvest; 
	header("Location: $delete"); exit(); }
if(isset($_POST['details'])) {
	$details="../harvestroster.php?harvesttemp=".$IDharvest;
	header("Location: $details"); exit(); }
if(isset($_POST['attendance'])) {
	$attendance="rostermanager.php?ID_harvest=".$IDharvest;
	header("Location: $attendance"); exit(); }
if(isset($_POST['distributions'])) {
	$distributions="distributions.php?harvesttemp=".$IDharvest;
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
$adjwgt=is_numeric($_POST['adjwgt']) ? $_POST['adjwgt'] : 0;
$totwgt=$calcwgt+$adjwgt;
$updateSQL = sprintf("UPDATE harvests SET ID_site=%s, ID_leader=$leader, ID_leader2=%s, h_date=%s, h_time=%s, duration=%s, spot=%s, carpool=%s, pooltime=%s, adjwgt=%s, donwgt=%s, calcwgt=$calcwgt, totwgt=$totwgt, pick_num=%s, SHT=%s, type=%s, delivered=%s, miles=%s, triphours=%s, kindmiles=%s, volcars=%s, volextra=%s, otherinfo=%s, longinfo=%s, gmap=%s, status=%s, where_to=%s, taxdate=%s, soon=%s WHERE ID_harvest=%s",
                       GetSQLValueString($_POST['ID_site'], "int"),
                       GetSQLValueString($_POST['ID_leader2'], "int"),
					   GetSQLValueString($h_date, "date"),
                       GetSQLValueString($_POST['h_time'], "text"),
                       GetSQLValueString($_POST['duration'], "double"),
                       GetSQLValueString($_POST['spot'], "int"),
                       GetSQLValueString($_POST['carpool'], "text"),
                       GetSQLValueString($_POST['pooltime'], "text"),
                       GetSQLValueString($_POST['adjwgt'], "int"),
                       GetSQLValueString($_POST['donwgt'], "int"),
                       GetSQLValueString($_POST['pick_num'], "int"),
                       GetSQLValueString($_POST['SHT'], "text"),
                       GetSQLValueString($_POST['type'], "text"),
                       GetSQLValueString($_POST['delivered'], "text"),
                       GetSQLValueString($_POST['miles'], "int"),
                       GetSQLValueString($_POST['triphours'], "double"),
                       GetSQLValueString($_POST['kindmiles'], "int"),
                       GetSQLValueString($_POST['volcars'], "int"),
                       GetSQLValueString($_POST['volextra'], "double"),
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['longinfo'], "text"),
					   GetSQLValueString($gmap, "text"),
					   GetSQLValueString($status, "text"),
                       GetSQLValueString($_POST['where_to'], "date"),
                       GetSQLValueString($_POST['taxdate'], "date"),
                       GetSQLValueString($_POST['soon'], "text"),
			   		   GetSQLValueString($_POST['hiddenfield'], "int"));

$Result1 = mysqli_query($piercecty, $updateSQL) or die(mysqli_error($piercecty));

 // update roster with leaders	
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

$updateGoTo = "harvestupdate.php";
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
#hints {
	width:700px;
	height:70px;
	float:right;
	margin-right:300px;
	border:1px solid #000;
	padding:3px;
}
#add {
width:200px;
float:left;
height:70px;}
.cropdrop {width:100px;}
-->
</style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
<!--
function hints(thisid) {
var which=thisid.id;
var hint='';
switch (which) {
	case 'ID_site': hint='The identification number of the site. You can find this on the Site List.'; break;
	case 'h_date': hint='The harvest date uses mm-dd format. The current year will be automatically added.  If the date is blank it will show up on the Season Planner as unscheduled.'; break;
	case 'carpool': hint='Transportation type:<br />option = volunteers may drive to the site or join the carpool as rider or driver<br />none = no carpool for this harvest<br />all = everyone must meet at the carpool location and convoy to the site'; break;
	case 'pick_num': hint='The number of pickers that are needed for the harvest.'; break;	
	case 'ldrseats': hint='The number of carpool seats that the leader will provide (default=0). This can be changed only for harvests in the future.'; break;
	case 'crop': hint='From the dropdown list, select a crop that was harvested. If the crop is not listed, go to the Crop Manager page and add it to the master crop list.'; break;
	case 'wgt': hint='The number of cases or bushels of that crop that were harvested. This can be a two place decimal number.'; break;
	case 'SHT': hint='Select Yes if this harvest is to be open for only the Select Harvest Team. If so, it will not appear on the public Harvests page.'; break;
	case 'status': hint='A harvest must also have a status to be added to the harvest list. This can be: \"closed\" - This means that the harvest will not be shown on the Harvests page for public signup. \"open\" - This means that the harvest can be displayed on the public Harvests page. To be displayed there it must also have a leader assigned. \"unscheduled\" - This means that the actual date has not been set, but a harvest is expected.'; break;
	case 'taxdate': hint='The date that the tax donation receipt was sent to the site owner. This is usually filled in by the Secretary, not the harvest leader.'; break;
	case 'otherinfo': hint='This section is for text about the harvest. It will be displayed in the Season Planner and so can be used for short pieces of information before the harvest is scheduled, such as noting that the harvest date is tentative, or who will be scouting the site. However, when the harvest is opened for signup (status = \"open\") then the text in this section is shown with the open harvest list on the public Harvests page as a short description of the site and location so volunteers can decide if they want to sign up.'; break;
	case 'longinfo': hint='This section is for text that will appear on the harvest information page that volunteers see after they sign up for the harvest. It will be inserted on that page under \"Specific information for this harvest\". It typically includes directions to the harvest, what to bring, and special considerations about the site or site. '; break;
	case 'gmap': hint='If the checkbox is left checked, then the \"Harvest Thank You\" page is produced using the picker\'s registered address and the harvest address to look up custom driving directions on Google Maps from the picker\'s house to the harvest, and insert them on the page. [See Page Help for more details.]'; break;
	case 'soon': hint='This indicates whether to use this harvest in the calculation of days to earliest previous harvest on the Sites list page.'; break;
	case 'type': hint='Type of trip.'; break;
	case 'miles': hint='Total miles put on the vehicle.'; break;
	case 'delivered': hint='Whether or not the harvest included delivery of the produce'; break;
	case 'triphours': hint='Number of hours for the trip'; break;
	case 'kindmiles': hint='Volunteer car miles as in-kind donation'; break;
	case 'volcars': hint='Volunteer cars used'; break;
	case 'volextra': hint='Extra volunteer hours not accounted for by harvest duration'; break;
	case 'adjwgt': hint='Weights are calculated as the sum of the pounds entered for each type or produce. Whenever the total weight is known to be different from this calculated weight, an adjustment to the total can be entered here. This can be a positive or negative integer.'; break;
}
document.getElementById('hints').innerHTML=hint;
}
// -->
</script>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="../datepick/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="../datepick/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"h_date",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2009,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
		new JsDatePick({
			useMode:2,
			target:"taxdate",
			dateFormat:"%Y-%m-%d",
			yearsRange:[2009,2035],
			cellColorScheme:"armygreen",
			imgPath:"img/",
			weekStartDay:0
		});
	};
</script>
</head>
<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php'); ?>
<div id="mainContent">
    <h2><strong>Harvest update</strong></h2>
    <div id="add">
    <table width="170" border="2" cellpadding="1" cellspacing="1" id="sort">
      <tr align="center">
        <td width="162"><a href="harvestinsert.php">Add a new harvest</a></td>
      </tr>
    </table>
    </div>
    <div id="hints">
    Help text appears here for each form field.</div>
    <br /><p><strong>Filters:</strong></p>
    <form action="" method="get" name="filtersform">
    <p>
       Harvest number 
         <input size="7" maxlength="5" type="text" name="harvesttemp" id="harvesttemp" value="<?php echo $IDharvest ?>"/>
   	  &nbsp;&nbsp;Select site to show its most recent harvest:
       <select name="sitedrop">
	  		<option value="" selected="selected"> </option>
          <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' order by farm";
		  			  $rsSitesq=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
					  while ($siterowq=mysqli_fetch_assoc($rsSitesq)) {
					?><option value="<?php echo $siterowq['ID_site']; ?>" <?php if($siterowq['ID_site']==$IDsite) echo 'selected="selected"';?>><?php echo $siterowq['farm'].", ".$siterowq['address'].", ".$siterowq['city'].", ".$siterowq['crops'];?></option>
					<?php } ?>
    </select>
    <p><input type="submit" name="submit" id="submit" value="Display harvest info" /></p>
     </form>
      
      <form action="<?php echo $editFormAction; ?>" id="harvestupdateform" name="harvestupdateform" method="POST">
      <table  border="1" cellpadding="1" cellspacing="1" id="harvestlist" width="1200px">
        <tr>
          <th >Harvest ID</th>
          <th >Site ID</th>
          <th >Leader</th>
          <th >co-Leader</th>
          <th >Date<br />
          yyyy-mm-dd</th>
          <th >Time hh:mm<br />(24 hour)</th>
          <th >Duration</th>
          <th>Type</th>
          <th >Pickers<br />needed</th>
          <th >Status</th>
          <th ><?php if($IDharvest) { ?><input type="submit" name="submit2" id="submit2" value="Save changes" /><?php } ?>
              <input type="hidden" name="MM_update" value="harvestupdateform" /></th>
        </tr>
          <tr class="centercell">
            <td><?php echo $harvestrow['ID_harvest']; ?>
              <input name="hiddenfield" type="hidden" id="hiddenfield" onfocus="hints(this)" value="<?php echo $harvestrow['ID_harvest']; ?>" /></td>
            <td><input name="ID_site" type="text" id="ID_site"  onfocus="hints(this)" value="<?php echo $harvestrow['ID_site']; ?>" size="5" maxlength="5" /></td>
          <td> <select name="ID_leader" id="ID_leader" onfocus="hints(this)">
          			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            		<?php } ?>
		            </select></td>
          <td> <select name="ID_leader2" id="ID_leader2" onfocus="hints(this)">
          			<option value=''> </option>
					<?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>          			
            		<option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($harvestrow['ID_leader2']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
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
		  <th><?php if($IDharvest) { ?><input name="delete" type="submit" value="Delete" /><?php } ?></th>
          </tr>
        <tr class="centercell">
          <th>Delivered</th>
          <th>Trip miles</th>
          <th>Trip hours</th>
          <th>In-kind miles</th>
          <th>Cars</th>
          <th>Extra hours</th>
          <th>Weight<br />adjustment</th>
  		<th colspan="2"><p>Where donated<br />
  			[use only when the agency is NOT on the Distributions dropdown menu and cannot be added]</p></th>
          <th>Tax date<br />yyyy-mm-dd</th>
   		  <th><?php if($IDharvest) { ?><input name="details" type="submit" value="Harvest details" /><?php } ?></th>
          </tr>
          <tr class="centercell">
             <td><select name="delivered"  id="delivered" onfocus="hints(this)" >
		            <option value="" <?php if($harvestrow['delivered']=='') echo 'selected="selected"';?>>-select-</option>
		            <option value="Yes" <?php if($harvestrow['delivered']=='Yes') echo 'selected="selected"';?>>Yes</option>
		            <option value="No" <?php if($harvestrow['delivered']=='No') echo 'selected="selected"';?>>No</option>
		            </select></td>
            <td ><input  style="width:60px;" name="miles" type="number" id="miles"  onfocus="hints(this)" value="<?php echo $harvestrow['miles']; ?>" size="3" maxlength="3" /></td>
            <td><input style="width:60px;" name="triphours" type="text" id="triphours"  onfocus="hints(this)" value="<?php echo $harvestrow['triphours']; ?>" size="5" maxlength="5" /></td>
            <td><input style="width:60px;" name="kindmiles" type="number" id="kindmiles"  onfocus="hints(this)" value="<?php echo $harvestrow['kindmiles']; ?>" size="3" maxlength="3" /></td>
            <td><input style="width:60px;" name="volcars" type="number" id="volcars"  onfocus="hints(this)" value="<?php echo $harvestrow['volcars']; ?>" size="2" maxlength="2" /></td>
            <td><input name="volextra" type="text" id="volextra"  onfocus="hints(this)" value="<?php echo $harvestrow['volextra']; ?>" size="5" maxlength="5" /></td>
            <td><input style="width:60px;" name="adjwgt" type="number" id="adjwgt"  onfocus="hints(this)" value="<?php echo $harvestrow['adjwgt']; ?>" size="5" maxlength="5" /></td>
             <td colspan="2"><input name="where_to" type="text" id="where_to"  onfocus="hints(this)" value="<?php echo $harvestrow['where_to']; ?>" size="15" maxlength="50" /></td>
             <td><input name="taxdate" type="text" id="taxdate"  onfocus="hints(this)" value="<?php echo $harvestrow['taxdate']; ?>" size="10" maxlength="10" /></td>
   		  <th><?php if($IDharvest) { ?><input name="attendance" type="submit" value="Attendance" /><?php } ?></th>
         </tr>
 <tr class="centercell">
 <td> </td>
<th>Meeting spot</th>
<th>Carpool</th>
<th>Carpool<br />
  time</th>
 <th >Use for due dates?</th>
 <th>Leader carpool seats</th>
 <th>Select<br />team only? </th>
 <th>Calculated<br />weight</th>
 <th>Total weight</th>
 <th>Donated weight</th>
<th><?php if($IDharvest) { ?><input name="distributions" type="submit" value="Distributions" /><?php } ?></th>
 </tr>
 <tr class="centercell">
 <td> </td>
  <td><select name="spot"  id="spot" onfocus="hints(this)"  class="cropdrop">
        <option value="0" <?php if($harvestrow['carpool']=='none') echo 'selected="selected"';?>> No carpool </option>
		<?php mysqli_data_seek($rsSpots,0); while($spotrow = mysqli_fetch_assoc($rsSpots)) { ?>
        	<option value="<?php echo $spotrow['ID_spot'];?>"
			<?php if($spotrow['ID_spot']==$harvestrow['spot']) echo 'selected="selected"';?>><?php echo $spotrow['name'];?></option>
        <?php } ?>
        </select></td>
<td><select name="carpool" id="carpool" onfocus="hints(this)">
		            <option value="none" <?php if($harvestrow['carpool']=='none') echo 'selected="selected"';?>>none</option>
            		<option value="option" <?php if($harvestrow['carpool']=='option') echo 'selected="selected"';?>>option</option>
            		<option value="all" <?php if($harvestrow['carpool']=='all') echo 'selected="selected"';?>>all</option>
		            </select></td>
          <td> <select name="pooltime" id="pooltime" onfocus="hints(this)">
          			<option value="00:00"> </option>
					<?php $times=mktime(7,30); 
					for($ct=1; $ct<=48;++$ct) {
					$times+=900;	 ?>          			
            		<option value="<?php echo date('H:i',$times); ?>" <?php if(isset($harvestrow['pooltime']) && substr($harvestrow['pooltime'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            		<?php } ?>
		            </select></td>
<td><select name="soon"  id="soon" onfocus="hints(this)" >
		            <option value="Yes" <?php if($harvestrow['soon']=='Yes') echo 'selected="selected"';?>>Yes</option>
		            <option value="No" <?php if($harvestrow['soon']<>'Yes') echo 'selected="selected"';?>>No</option>
		            </select></td>
<?php // get leader's carpool seats
if(isset($IDharvest)) {
$ldrseatsq="select seats from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and harvests.ID_harvest=$IDharvest and rosters.ID_picker=harvests.ID_leader";
$rsLdrseats=mysqli_query($piercecty, $ldrseatsq) or die(mysqli_error($piercecty));
$leaderseatsrow=mysqli_fetch_assoc($rsLdrseats);
$ldrseats=$leaderseatsrow['seats'];} 
else {$ldrseats=0;}
?>
<td><input style="width:60px;" name="ldrseats" type="number" id="ldrseats"  onfocus="hints(this)" value="<?php echo $ldrseats; ?>" size="5" maxlength="2" /></td>
<td><select name="SHT"  id="SHT" onfocus="hints(this)" >
		            <option value="Yes" <?php if($harvestrow['SHT']=='Yes') echo 'selected="selected"';?>>Yes</option>
		            <option value="No" <?php if($harvestrow['SHT']<>'Yes') echo 'selected="selected"';?>>No</option>
		            </select></td>
<td><?php echo $harvestrow['calcwgt']; ?> pounds</td>
<td><?php echo $harvestrow['totwgt'];  ?> pounds</td>
<td><input style="width:60px" name="donwgt" type="text" id="donwgt"  onfocus="hints(this)" value="<?php echo $harvestrow['donwgt']; ?>" size="5" maxlength="5" /></td>
</tr>
</table>
<br />
 <table border="1" cellpadding="1" cellspacing="1">
  <?php
  // print existing filled crop and pounds slots
  $donationsq="select * from donations where ID_harvest=$IDharvest";
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
<br />
<table border="1" cellpadding="1" cellspacing="1" id="harvestlist">
          <tr>
            <td align=center>Pre-signup info:</td>
            <td colspan = "12"><textarea name="otherinfo" cols="100" rows="2" id="otherinfo"  onfocus="hints(this)" ><?php echo $harvestrow['otherinfo']; ?></textarea></td>
          </tr>
          <tr>
            <td align=center>Post signup info:
              <p>&nbsp;</p>
              <p>Include Google<br />
          Maps directions<br />
          after signup:<br />
            <input type="checkbox" name="gmap" id="gmap" onmouseover="hints(this)"<?php if( $harvestrow['gmap']=='Yes') echo 'checked="checked"';?> />
          </p></td>
            <td colspan = "12"><textarea name="longinfo" cols="100" rows="10" id="longinfo"  onfocus="hints(this)"><?php echo $harvestrow['longinfo']; ?></textarea></td>
          </tr>
          <tr>
            <th colspan="15"></th>
            <th></th>
          </tr>
       </table>
        </form>
        <br />
        <table>
        <form name="updateaddress" method="POST"> 
         <tr>
         	<th>&nbsp;</th>
         	<th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            </tr>
         <tr>
         <td>
		 <?php if($IDharvest) { ?><input type="submit" name="submit3" value="Update site address" /><?php } ?>
              <input type="hidden" name="hiddenfield2" value="<?php echo $IDharvest;?>" />
           </td>
             <td><input name="address" type="text" id="address"  onfocus="hints(this)" value="<?php echo $address; ?>" size="30" maxlength="80" /></td>
             <td><input name="city" type="text" id="city"  onfocus="hints(this)" value="<?php echo $city ?>" size="30" maxlength="80" /></td>
             <td><input name="state" type="state" id="state"  onfocus="hints(this)" value="<?php echo $state; ?>" size="5" maxlength="2" /></td>
             <td><input name="zip" type="text" id="zip"  onfocus="hints(this)" value="<?php echo $zip; ?>" size="8" maxlength="5" /></td>
          </tr>
          </form>
</table>
<br />
      <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
<div><form id="lastname" name="lastname" method="post" action="<?php echo $editFormAction ?>">
      <label><strong>Picker finder</strong>: Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space <br />
<input width = "10" type="text"  style="background-color:#aaf969" name="textin" id="textin" /> and press 'Enter'</label>
      </form>
    <table width="825" border="1" cellpadding="5" cellspacing="5" id="Pickerlist"> 
        <tr>
        <?php 
	  	$colct=1; // initialize column count
	  	 while ($pickerrow = mysqli_fetch_assoc($rsName))  {  ?> 
          <td><a href="voldetail.php?voltemp=<?php echo $pickerrow['ID_picker'];?>"><?php echo $pickerrow['ID_picker']." ".$pickerrow['fname']." ".$pickerrow['lname'];?></a></td>
        <?php ++$colct; if($colct==6) {$colct=1; echo "</tr><tr>"; } // if done 5 columns go to new row
         }?>     
        </tr>
    </table>
    <p>&nbsp;</p>
</div>
</div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsHarvest) || (is_object($rsHarvest) && (get_class($rsHarvest) == "mysqli_result"))) ? true : false);
?>
