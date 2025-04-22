<?php
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// picker finder div 
$colname_rsName = "";
if (isset($_POST['nametemp'])) {$colname_rsName = $_POST['nametemp'];}
if (is_numeric($colname_rsName)) { 
$ID_picker = intval($colname_rsName);
$query_rsName = sprintf("SELECT ID_picker, lname, fname FROM pickers WHERE ID_picker = '$ID_picker' ORDER BY ID_picker ASC");
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
} else {
$sfield='lname'; 
if(substr($colname_rsName,0,1)==' ') { $sfield='fname'; $colname_rsName=trim($colname_rsName); }
if(substr($colname_rsName,0,1)=='-') { $sfield='email'; $colname_rsName=substr($colname_rsName,1,strlen($colname_rsName)-1);
 }
$long=strlen(stripslashes($colname_rsName));
if($long==0) {$long=1; }
$query_rsName = "SELECT ID_picker, lname, fname FROM pickers WHERE left($sfield,'$long') = '$colname_rsName' ORDER BY ID_picker ASC";
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
}

// get passed in harvest or site number
$IDharvest = ""; $IDsite = ""; $query="SELECT * FROM harvests WHERE ID_harvest = 0"; 

if(isset($_GET['harvesttemp']) and $_GET['harvesttemp']<>'') {
	$IDharvest =  $_GET['harvesttemp'];
	$query= "SELECT * FROM harvests WHERE ID_harvest = $IDharvest ORDER BY h_date DESC, ID_harvest DESC";
	}
	
if(isset($_GET['sitetemp']) and $_GET['sitetemp']<>'') {
	$IDsite = $_GET['sitetemp'];
	$query= "SELECT * FROM harvests WHERE ID_site = $IDsite ORDER BY h_date DESC, ID_harvest DESC";
	}

if(isset($_GET['sitedrop']) and $_GET['sitedrop']<>'') {
	$IDsite = $_GET['sitedrop'];
	$IDharvest='';
	$query= "SELECT * FROM harvests WHERE ID_site = $IDsite ORDER BY h_date DESC, ID_harvest DESC";
	}

$rsharvest = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$row_rsharvest = mysqli_fetch_assoc($rsharvest);

if(mysqli_num_rows($rsharvest)>0)  { $IDsite=$row_rsharvest ['ID_site']; $IDharvest=$row_rsharvest ['ID_harvest']; }

$warn=''; // flag to set for missing required entries (i.e. site ID)

// set up spots dropdown
$spotq="select ID_spot, name from spots where ID_spot<>0 order by ID_spot";
$rsSpots = mysqli_query($piercecty, $spotq) or die(mysqli_error($piercecty));

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "harvestinsertform")) { 	 // if insert pressed
	
	if(!is_numeric($_POST['ID_site']) || $_POST['ID_site']=='') {  // site id must be included  
		$warn="no site number";		
	} else {  // okay to insert

	$curyear=date('Y');
	$status=$_POST['status'];
	if(substr($_POST['h_date'],-5)>'12-31' ||  substr($_POST['h_date'],-5)<'01-01') {
		$h_date=$curyear.'-00-00';
		$status='unsched';
		} else {
		$h_date=$_POST['h_date'];
			if($status=='unsched') $status='closed';
		}

$gmap= !isset($_POST['gmap'])	 ? 'No' : 'Yes';
$ID_leader=$_POST['ID_leader']=='' ? 0 : $_POST['ID_leader'];

$insertSQL = sprintf("INSERT INTO harvests (ID_site, ID_leader, ID_leader2, h_date, h_time, duration, carpool, pooltime, spot, pick_num, SHT, otherinfo, longinfo, gmap, status) VALUES (%s, $ID_leader, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['ID_site'], "int"),
                       GetSQLValueString($_POST['ID_leader2'], "int"),
					   GetSQLValueString($h_date, "date"),
                       GetSQLValueString($_POST['h_time'], "text"),
                       GetSQLValueString($_POST['duration'], "double"),
                       GetSQLValueString($_POST['carpool'], "text"),
                       GetSQLValueString($_POST['pooltime'], "text"),
                       GetSQLValueString($_POST['spot'], "int"),
                       GetSQLValueString($_POST['pick_num'], "int"),
                       GetSQLValueString($_POST['SGT'], "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"),
                       GetSQLValueString($_POST['longinfo'], "text"),
                       GetSQLValueString($gmap, "text"),
					   GetSQLValueString($status, "text"));

  $Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));
  
  $insertGoTo = "../harvestlist-master.php";
  header(sprintf("Location: %s", $insertGoTo));
} // end of okay to insert
} // end of if form posted
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>harvest insert</title>
    <style type="text/css">
<!--
#hints {
	width:750px;
	height:70px;
	float:right;
	margin-right:250px;
	border:1px solid #000;
	padding:3px;
}
#add {
width:200px;
float:left;
height:75px;
padding:3px;
}
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
	case 'harvesttemp': hint='A harvest number entered here followed by clicking \'Show records\' displays the information for that harvest. This can be useful for adding a new harvest that is similar to the previous one. Be sure to change all fields that are not the same as the previous harvest.'; break;
	case 'sitetemp': hint='A site number entered here followed by clicking \'Show records\' displays the information for the most recent harvest at that site. This can be useful for adding a new harvest that is similar to the previous one. Be sure to change all fields that are not the same as the previous harvest.'; break;
	case 'sitedrop': hint='A site selected here followed by clicking \'Show records\' displays the information for the most recent harvest at that site. This can be useful for adding a new harvest that is similar to the previous one. Be sure to change all fields that are not the same as the previous harvest.'; break;
	case 'ID_site': hint='The identification number of the site. You can find this on the Site List.'; break;
	case 'h_date': hint='The harvest date uses mm-dd format. The current year will be automatically added.  If the date is blank it will show up on the Season Planner as unscheduled.'; break;
	case 'duration': hint='The length of the harvest in hours.'; break;
	case 'carpool': hint='Transportation type:<br />option = volunteers may drive to the site or join the carpool as rider or driver<br />none = no carpool for this harvest<br />all = everyone must meet at the carpool location and convoy to the site'; break;
	case 'duration': hint='The length of the harvest in hours.'; break;
	case 'pick_num': hint='The number of pickers that are needed for the harvest.'; break;
	case 'SGT': hint='Select Yes if this harvest is to be open for only the Select harvest Team. If so, it will not appear on the public harvests page.'; break;
	case 'status': hint='A harvest must also have a status to be added to the harvest list. This can be: \"closed\" - This means that the harvest will not be shown on the harvests page for public signup. \"open\" - This means that the harvest can be displayed on the public harvests page. To be displayed there it must also have a leader assigned. \"unscheduled\" - This means that the actual date has not been set, but a harvest is expected.'; break;
	case 'weight': hint=' The number of pounds of produce that were donated. This must be just a number. This will normally not be known when the harvest is first listed and will be left blank.'; break;
	case 'KeyRec': hint='This is an optional five-digit number that can be used to coordinate the record of this harvest with a record number in another database system such as for inventory control or accounting.'; break;
	case 'calcwgt': hint='The number of pounds that were picked at the harvest. This must be just a number. It is commonly determined by doubling the known donated weight.'; break;
	case 'where_to': hint='The food agency, such as Marion Polk Food Share or Union Gospel Mission where the produce was donated. '; break;
	case 'taxdate': hint='The date that the tax donation receipt was sent to the site owner. This is usually filled in by the Secretary, not the harvest leader.'; break;
	case 'otherinfo': hint='This section is for text about the harvest. It will be displayed in the Season Planner and so can be used for short pieces of information before the harvest is scheduled, such as noting that the harvest date is tentative, or who will be scouting the site. However, when the harvest is opened for signup (status = \"open\") then the text in this section is shown with the open harvest list on the public harvests page as a short description of the site and location so volunteers can decide if they want to sign up.'; break;
	case 'longinfo': hint='This section is for text that will appear on the harvest information page that volunteers see after they sign up for the harvest. It will be inserted on that page under \"Specific information for this harvest\". It typically includes directions to the harvest, what to bring, and special considerations about the site or crop. '; break;
	case 'gmap': hint='If the checkbox is left checked, then the \"harvest Thank You\" page is produced using the picker\'s registered address and the harvest address to look up custom driving directions on Google Maps from the picker\'s house to the harvest, and insert them on the page. [See Page Help for more details.]'; break;
}
document.getElementById('hints').innerHTML=hint;
}
// -->
</script>
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
	};
</script>

</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
<div id="mainContent">
    <h2><strong>Add a new harvest</strong></h2>
    <div id="add"><table width="170" border="2" cellpadding="1" cellspacing="1" id="sort">
      <tr align="center">
        <td width="162"><a href="harvestupdate.php">to harvest update</a></td>
      </tr></table>
      </div>
    <div id="hints">Help text appears here for each form field.</div>
      <p>&nbsp;</p>
    <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p><strong>Filters:</strong>  Enter a harvest number or a site number or select a site and then click on 'Show records' to show the most recent harvest (that you may want to copy when adding a new harvest). When doing this, be sure to then change the date (or leave it blank if it is not  yet known), and delete the 'weights' 'Tax date' and 'Where donated.' When all the information is correct, click on the 'Insert' button at the far right.</p>
    <form action="harvestinsert.php" method="get" name="filtersform">
    <p>
       Previous harvests: harvest number 
         <input size="7" maxlength="7" type="text" name="harvesttemp" id="harvesttemp"  onfocus="hints(this)" value="<?php echo $IDharvest ?>"/>
       site number
        <input size="7" maxlength="7" type="text" name="sitetemp" id="sitetemp"  onfocus="hints(this)" value="<?php echo $IDsite ?>"/>
        &nbsp; sites 
        <select name="sitedrop" id="sitedrop" onfocus="hints(this)">
	  		<option value="" selected="selected"> </option>
          <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' order by farm";
		  			  $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
					  while ($siterow=mysqli_fetch_assoc($rsSites)) {
					?><option value="<?php echo $siterow['ID_site']; ?>" <?php if($siterow['ID_site']==$IDsite) echo 'selected="selected"';?>><?php echo $siterow['farm'].", ".$siterow['address'].", ".$siterow['city'].", ".$siterow['crops'];?></option>
					<?php } ?>
    </select>
    <p>
      <input type="submit" name="submit" id="submit" value="Show most recent" />
      <input type="hidden" name="MM_filter" value="filtersform" />
    </form></p>
	  <?php 
	  if($warn<>'') echo "<br /><strong>NOTE: site ID number is required to insert a new harvest.</strong>";?>
    <form action="<?php echo $editFormAction; ?>" id="harvestinsertform" name="harvestinsertform" method="POST">
    <table  border="1" cellpadding="1" cellspacing="1" id="harvestlist" width="1220px"> 
      <tr>
        <th>Harvest ID</th>
        <th>Site</th>
        <th>Leader</th>
        <th>Date<br />yyyy-mm-dd</th>
        <th>Harvest time</th>
        <th>Duration</th>
        <th>Status</th>
        </tr>
        <tr class="centercell">
          <td><?php echo $row_rsharvest['ID_harvest']; ?>
              <input name="hiddenfield" type="hidden" id="hiddenfield" value="<?php echo $row_rsharvest['ID_harvest']; ?>" /></td>
              <?php if(isset($_GET['sitetemp'])) {$sitetemp=$_GET['sitetemp'];}
			  				else {$sitetemp=$row_rsharvest['ID_site'];} ?>
          <td><select name="ID_site" id="sitedrop" style="width:200px;"  onfocus="hints(this)">
	  		<option value="" selected="selected"> </option>
          <?php $sitesq="select ID_site, farm, address, city, crops from sites where Active='Yes' order by farm";
		  			  $rsSites=mysqli_query($piercecty, $sitesq) or die(mysqli_error($piercecty));
					  while ($siterow=mysqli_fetch_assoc($rsSites)) {
						?><option value="<?php echo $siterow['ID_site']; ?>" <?php if($siterow['ID_site']==$IDsite) echo 'selected="selected"';?>><?php echo $siterow['farm'].", ".$siterow['address'].", ".$siterow['city'].", ".$siterow['crops'];?></option>
					<?php } ?>
    </select></td>
          <td><select name="ID_leader" id="ID_leader" onfocus="hints(this)">
            <option value=''> </option>
            <?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader='Yes' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq) or die(mysqli_error($piercecty).' line239'); 
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>
            <option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($row_rsharvest['ID_leader']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            <?php } ?>
          </select></td>
          <td><input name="h_date" type="text" id="h_date" onfocus="hints(this)" value="<?php // echo $row_rsharvest['h_date']; ?>" size="10" maxlength="10" /></td>
          <td> <select name="h_time" id="h_time" onfocus="hints(this)">
          			<option value="00:00"> </option>
					<?php $times=mktime(7,30); 
					for($ct=1; $ct<=24;++$ct) {
					$times+=1800;	 ?>          			
            		<option value="<?php echo date('H:i',$times); ?>" <?php if(substr($row_rsharvest['h_time'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            		<?php } ?>
		            </select></td>
          <td><input style="width:70px;" name="duration"  id="duration" onfocus="hints(this)" value="<?php // echo $row_rsharvest['duration']; ?>" size="5" maxlength="5" /></td>
          <td><select name="status" id="status" onfocus="hints(this)">
		            <option value="closed" <?php if( $row_rsharvest['status']=='closed') echo 'selected="selected"';?>>closed </option>
            		<option value="open" <?php if( $row_rsharvest['status']=='open') echo 'selected="selected"';?>>open</option>
            		<option value="unsched" <?php if( $row_rsharvest['status']=='unsched') echo 'selected="selected"';?>>unsched</option>
		            </select></td>
          <td width="57"><input type="submit" name="submit" id="submit" value="Insert" /><input type="hidden" name="MM_insert" value="harvestinsertform" /></td>
 		</tr>
        <tr>
        <th>&nbsp;</th>
        <th>co-Leader</th>
        <th>Meeting spot</th>
        <th>Carpool</th>
        <th>Carpool<br />
          time</th>
        <th>Pickers<br />
          needed</th>
        <th>Select Team<br />
          only?</th>
        </tr>
        <tr class="centercell">
          <td>&nbsp;</td>
              <?php if(isset($_GET['sitetemp'])) {$sitetemp=$_GET['sitetemp'];}
			  				else {$sitetemp=$row_rsharvest['ID_site'];} ?>
          <td><select name="ID_leader2" id="ID_leader2" onfocus="hints(this)">
            <option value=''> </option>
            <?php 
					$ldrsq="select fname, lname, ID_picker from pickers where leader<>'' order by lname, fname";
					$rsLdrs=mysqli_query($piercecty,$ldrsq);
					while($ldrsdrop=mysqli_fetch_assoc($rsLdrs)) { ?>
            <option value="<?php echo $ldrsdrop['ID_picker']; ?>" <?php if($row_rsharvest['ID_leader2']==$ldrsdrop['ID_picker']) echo 'selected="selected"'; ?>><?php echo $ldrsdrop['lname'].','.$ldrsdrop['fname'];?></option>
            <?php } ?>
          </select></td>
          <td><select name="spot"  id="spot" onfocus="hints(this)"  class="cropdrop">
        <option value="0" <?php if($row_rsharvest['carpool']=='none') echo 'selected="selected"';?>> </option>
		<?php mysqli_data_seek($rsSpots,0); while($spotrow = mysqli_fetch_assoc($rsSpots)) { ?>
        	<option value="<?php echo $spotrow['ID_spot'];?>"
			<?php if($spotrow['ID_spot']==$row_rsharvest['spot']) echo 'selected="selected"';?>><?php echo $spotrow['name'];?></option>
        <?php } ?>
        </select></td>
          <td><select name="carpool" id="carpool" onfocus="hints(this)">
            <option value="none" <?php if( $row_rsharvest['carpool']=='none') echo 'selected="selected"';?>>none</option>
            <option value="option" <?php if( $row_rsharvest['carpool']=='option') echo 'selected="selected"';?>>option</option>
            <option value="all" <?php if( $row_rsharvest['carpool']=='all') echo 'selected="selected"';?>>all</option>
          </select></td>
          <td><select name="pooltime" id="pooltime" onfocus="hints(this)">
            <option value="00:00"> </option>
            <?php $times=mktime(7,30); 
					for($ct=1; $ct<=24;++$ct) {
					$times+=1800;	 ?>
            <option value="<?php echo date('H:i',$times); ?>" <?php if(substr($row_rsharvest['pooltime'],0,5)==date("H:i",$times)) echo 'selected="selected"';?>><?php echo date('g:ia',$times);?></option>
            <?php } ?>
          </select></td>
          <td><input style="width:70px;" name="pick_num" type="number" id="pick_num" onfocus="hints(this)" value="<?php // echo $row_rsharvest['pick_num']; ?>" size="5" maxlength="5" /></td>
          <td><select  name="SGT" id="SGT" onfocus="hints(this)" >
              <option value="Yes" <?php if($row_rsharvest['SHT']=='Yes') echo 'selected="selected"';?>>Yes</option>
              <option value="No" <?php if($row_rsharvest['SHT']<>'Yes') echo 'selected="selected"';?>>No</option>
          </select></td>
        </tr>
        <tr><td></td>
        <td align=center>Pre-signup info:</td><td colspan = "11"><textarea name="otherinfo" type="textarea" cols="100" rows="3" id="otherinfo" onfocus="hints(this)" ><?php echo $row_rsharvest['otherinfo']; ?></textarea></td></tr>
<tr>
            <td></td>
            <td align=center><p>Post-signup info:</p>
              <p>&nbsp;</p>
              <p>Include Google<br />
          Maps directions<br />
          after signup:<br />
            <input type="checkbox" name="gmap" id="gmap" onmouseover="hints(this)" value="Yes"  <?php if( $row_rsharvest['gmap']=='Yes') echo 'checked="checked"';?>/>
          </p></td>
            <td colspan = "10"><textarea name="longinfo" type="textarea" cols="100" rows="10" id="longinfo" onfocus="hints(this)" ><?php echo $row_rsharvest['longinfo']; ?></textarea></td>
          </tr>        <tr><th colspan="15"><th></th></tr>
		</table>
        </form>

  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->

<div><form id="lastname" name="lastname" method="post" action="<?php echo $editFormAction ?>">
      <label><strong>Picker finder</strong>: Type letters from the start of the last name OR the ID number OR letters from the start of the first name preceded by a space <br />
        <input width = "10" type="text"  style="background-color:#aaf969" name="nametemp" id="nametemp" />
and press 'Enter'</label>
      </form>
    <table width="825" border="1" cellpadding="5" cellspacing="5" id="Pickerlist"> 
        <tr>
        <?php 
	  	$colct=1; // initialize column count
	  	do {  ?> 
          <td><a href="voldetail.php?voltemp=<?php echo $row_rsName['ID_picker'];?>"><?php echo $row_rsName['ID_picker']." ".$row_rsName['fname']." ".$row_rsName['lname'];?></a></td>
        <?php ++$colct; if($colct==6) {$colct=1; echo "</tr><tr>"; } // if done 5 columns go to new row
         } while ($row_rsName = mysqli_fetch_assoc($rsName)); ?>     
        </tr>
    </table>
    <p>&nbsp;</p>
</div>
</div>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsharvest) || (is_object($rsharvest) && (get_class($rsharvest) == "mysqli_result"))) ? true : false);
?>
