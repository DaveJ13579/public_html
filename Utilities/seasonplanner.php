<?php require_once('../Connections/piercecty.php'); 
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
include_once('../includes/converter.inc.php');
require_once('../includes/levelcheck.php');

$username=$_SESSION['MM_Username'];
$userquery="select postYear, postvenue, postStatus, postSort from users where user_name='$username'";
$rsUsers=mysqli_query($piercecty, $userquery) or die(mysqli_error($piercecty));
$rowu=mysqli_fetch_assoc($rsUsers);

$postYear=$rowu['postYear']? $rowu['postYear'] : date('Y'); 
$postvenue=$rowu['postvenue'] ? $rowu['postvenue'] : 'All'; 
$postStatus=$rowu['postStatus'] ? $rowu['postStatus'] : 'All'; 
$postSort=$rowu['postSort'] ? $rowu['postSort'] : 'asc'; // default filter post values 

$fyear=" and year(h_date)='$postYear' "; $fvenue=""; $fstatus=""; $fsort=" order by h_date desc "; // default filter terms

if(isset($_POST['submit'])) { // filters are posted  -----------------------------------

$postYear=$_POST['Year']; $postvenue=$_POST['venue']; $postStatus=$_POST['Status']; $postSort=$_POST['Sort'];

// save posted filter values per user
$userupdate="update users set postYear='$postYear', postvenue='$postvenue', postStatus='$postStatus', postSort='$postSort' where user_name='$username'";
$rsUsersdate=mysqli_query($piercecty, $userupdate) or die(mysqli_error($piercecty));

$currentdate=date('Y-m-d');

// update filter values from posted filters
$fyear=" and year(h_date)='$postYear' ";

if($_POST['venue']<>'All') $fvenue=" and venue='$postvenue' ";

if($_POST['Status']=='All')  $fstatus= " ";
if($_POST['Status']=='Future') $fstatus=" and h_date>='$currentdate' "; 
if($_POST['Status']=='Open') $fstatus= " and status='open' ";
if($_POST['Status']=='Closed') $fstatus= " and status='closed' ";
if($_POST['Status']=='Unsched') $fstatus= " and status='unsched' ";

$fsort= " order by h_date $postSort ";
} // end of isset filters ---------------------------------------------------------------

// get all data for pages
$query = "SELECT harvests.ID_harvest, ID_leader, ID_leader2, totwgt, calcwgt, h_date, h_time, when_ripe, pick_num, SHT, harvests.status as status, harvests.otherinfo, farm, contact1, phone1, email1, address, city, venue, carpool, pooltime, harvests.ID_site as ID_site, surveysent FROM harvests, sites WHERE harvests.ID_site = sites.ID_site $fyear $fvenue $fstatus $fsort ";

$rsGleans = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsGleans);

// $harv[$i]['key'] will be all harvests on the page. It has a key of s_date for sorting that combines h_date, previous harvest  and when_ripe 
// load $harv array from dataset
$harv = array(); $i=0;
while ($xrow=mysqli_fetch_assoc($rsGleans)) { 
	$harv[$i]=$xrow;  
	$harv[$i]['s_date']=$xrow['h_date']." ".$xrow['h_time'];
	++$i; }

// if sort date (s_date) from h_date is '00' (i.e. the harvest is nto yet scheduled), then use 1)prev harvest or 2)ripe date or 3)'01-01' for the date, in that order
for ($i=0; $i<$numrows ; $i++) {
	
	if(substr($harv[$i]['h_date'],-2)=='00') { 
		$ID_site=$harv[$i]['ID_site'];
   		$prevquery="select max(h_date) as h_date from harvests where ID_site=$ID_site and substring(h_date,-2)<>'00'";
		$rsPrev=mysqli_query($piercecty, $prevquery);
		$prevrow=mysqli_fetch_assoc($rsPrev);

		if($prevrow['h_date']<>'') { $harv[$i]['s_date']=substr($harv[$i]['h_date'],0,4).substr($prevrow['h_date'],4);  } 
		elseif($harv[$i]['when_ripe']>'00-00') { $harv[$i]['s_date'] = substr($harv[$i]['h_date'],0,5).$harv[$i]['when_ripe']; }
		else {$harv[$i]['s_date']=substr($harv[$i]['h_date'],0,4).'-01-01'; }
		} 

} // end of build sort dates in harv array
// order by new s_dates
function sortAscending ($a, $b) 	{ 
	if ($a['s_date'] == $b['s_date']) { return 0; }
  	return ($a['s_date'] < $b['s_date']) ? -1 : 1;
	}
	
function sortDescending ($a, $b) 	{ 
	if ($a['s_date'] == $b['s_date']) { return 0; }
  	return ($a['s_date'] > $b['s_date']) ? -1 : 1;
	}
	
if($postSort=='desc') {usort($harv, "sortDescending"); } else { usort($harv, "sortAscending"); }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Season planner</title>
<link href="../database.css" rel="stylesheet" type="text/css" />
<style type="text/css">
/* calendar view harvest cells */
.unsched { background-color:#FCC;}
.assigned {background-color:#FFC;}
.closed {background-color:#CCF;}
.open {background-color:#CFC;}

a:link { text-decoration: none; }
a:visited { text-decoration: none; }
a:hover { 	text-decoration: underline; }
a:active { text-decoration: none; }
</style>
<script type="text/javascript">


var tempvar = null;
function popup(show){
	show.style.display="block"
	if (tempvar && (tempvar !== show)) tempvar.style.display="none"
	tempvar=show
}
function switchdiv(show, hide,show2,hide2) {
	show.style.display="block" 
	hide.style.display="none"
	show2.style.display="block" 
	hide2.style.display="none"
}
</script>
</head>
<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">

 <?php  // construct the popups for each harvest ------------------------------------------

for($j=0 ; $j<$numrows ; ++$j ) {   

?>
  <div class="pop" id="pop<?php echo $harv[$j]['ID_harvest']; ?>">
    <?php 
$hinfo="<br /><strong><center>Harvest number: ".$harv[$j]['ID_harvest']."</strong></center>";

$hinfo.="<br /><strong>Status:</strong>  ". $harv[$j]['status'];
$hardate=date('l  m/d/Y',strtotime($harv[$j]['h_date']));
				$hartime=date('g:i A',strtotime($harv[$j]['h_time']));
				$showdate=$hardate." at ".$hartime;
				if(substr($harv[$j]['h_date'],-5)=='00-00')   $showdate="date shown is previous harvest or ripe date"; 
$hinfo.="<br /><strong>Date:</strong>  ".$showdate;
$hinfo.="<br /><strong>Address:</strong>  ".$harv[$j]['address']." ".$harv[$j]['city'];
$hinfo.="<br /><strong>Owner:</strong>  ".$harv[$j]['contact1']." ".$harv[$j]['farm'];
$hinfo.="<br /><strong>Owner contact:</strong>  ".$harv[$j]['phone1']." ".$harv[$j]['email1'];
$hinfo.="<br />";
$leader= isset($harv[$j]['ID_leader']) ?  $harv[$j]['ID_leader'] : 0;
	$lquery="select fname, lname, email, phone from pickers where ID_picker=$leader";
	$rsLeader = mysqli_query($piercecty, $lquery);
	$lrow=mysqli_fetch_assoc($rsLeader);
$hinfo.="<br /><strong>Leader:</strong>  ".$lrow['fname']." ".$lrow['lname'];
$hinfo.="<br /><strong>Leader contact:</strong>  ".$lrow['phone']." ".$lrow['email'];
	$leader= isset($harv[$j]['ID_leader2']) ?  $harv[$j]['ID_leader2'] : 0;
	$lquery="select fname, lname, email, phone from pickers where ID_picker=$leader";
	$rsLeader = mysqli_query($piercecty, $lquery) or die(mysqli_error($piercecty));
	$lrow=mysqli_fetch_assoc($rsLeader);
$hinfo.="<br /><strong>co-Leader:</strong>  ".$lrow['fname']." ".$lrow['lname'];
$hinfo.="<br /><strong>co-Leader contact:</strong>  ".$lrow['phone']." ".$lrow['email'];
	// get most recent harvest yield data
	$today=date('Y-m-d');
	if($harv[$j]['h_date']>$today or substr($harv[$j]['h_date'],-5)=='00-00') {
		$IDcrop=$harv[$j]['ID_site'];
		$maxdate="select calcwgt, totwgt, h_date from harvests where ID_site=$IDcrop and totwgt>0 order by h_date desc";
		$rsMaxdate=mysqli_query($piercecty, $maxdate);
		$maxrow=mysqli_fetch_assoc($rsMaxdate);
		$nummax=mysqli_num_rows($rsMaxdate);
			if($nummax==0) { 	$hinfo.="<br /><br /><strong>No previous harvest</strong>";
			} else {
			$totallbs=$maxrow['totwgt']; $wgt=$maxrow['calcwgt']; $maxdate=$maxrow['h_date']; 
			$hinfo.="<br /><br /><strong>Most recent yield:</strong> ".$totallbs." <strong>Calc wgt:</strong> ".$wgt." <strong>Date:</strong> ".$maxdate;
			}
	} else {	
		$totallbs=$harv[$j]['totwgt']; $wgt=$harv[$j]['calcwgt']; 
		$hinfo.="<br /><br /><strong>Yield:</strong> ".$totallbs." <strong>Calc wgt:</strong> ".$wgt;
	}
$hinfo.="<br /><strong>Picker limit: </strong>".$harv[$j]['pick_num'];
// calculate roster stats
		$thisharv=$harv[$j]['ID_harvest'];
		$signups="select status, count(status) as signedup from rosters where ID_harvest=$thisharv group by status";
		$rsSignups=mysqli_query($piercecty, $signups);
		if($rsSignups) { while ($stats=mysqli_fetch_assoc($rsSignups)) {$hinfo.="<br />&nbsp;&nbsp;&nbsp;&nbsp;<strong>".$stats['status'].": </strong>".$stats['signedup'];}}

	$crops=cropstring($harv[$j]['ID_harvest']);
$hinfo.="<br /><strong>Crops: </strong>".$crops;
$hinfo.="<br /><strong>Select Team?: </strong>".$harv[$j]['SHT'];
$hinfo.="<br /><strong>Carpool?: </strong>".$harv[$j]['carpool'];

echo $hinfo."<br /><br />";

// links included in the popup ---------------------------------------------------------
?>
    <a href="../harvestroster.php?harvesttemp=<?php echo $harv[$j]['ID_harvest']; ?>" target="_blank">View harvest details and roster</a><br />
    <a href="harvestupdate.php?harvesttemp=<?php echo $harv[$j]['ID_harvest']; ?>"  target="_blank">Update harvest details</a><br />
    <br />
    <a href="sitedetail.php?sitetemp=<?php echo $harv[$j]['ID_site'];?>" target="_blank">View site details</a><br />
    <a href="siteupdate.php?sitetemp=<?php echo $harv[$j]['ID_site'];?>" target="_blank">Update site details</a><br />
    <br />
    <?php 
 // owner survey link
	if(
	($harv[$j]['h_date'] <= date('Y-m-d')) &&
    ($harv[$j]['totwgt'] <> 0 ) &&
    ($harv[$j]['status']<>'open') &&
    ($harv[$j]['surveysent']<>'Yes') &&
	($harv[$j]['email1'] <> ''))
	{ ?>
    <a href="../Owners/owners-sendsurvey.php?harvesttemp=<?php echo $harv[$j]['ID_harvest'];?>" target="_blank">Send crop owner survey</a><br />
    <?php } // end of send survey
 // owner tax receipt envelope

	if(
	($harv[$j]['h_date'] <= date('Y-m-d')) &&
    ($harv[$j]['totwgt'] <> 0 ) &&
    ($harv[$j]['status']<>'open'))
	{ ?>
    <a href="../Owners/receipt-pdf.php?htemp=<?php echo $harv[$j]['ID_harvest'];?>" target="_blank">Tax donation receipt</a><br />
    <a href="../Owners/envelope-pdf.php?htemp=<?php echo $harv[$j]['ID_harvest'];?>" target="_blank">Tax donation receipt envelope</a><br />
        <?php } // end of envelope

echo "<br />".$harv[$j]['otherinfo'];

?>
  </div>
  <!-- end of pop -->
  <?php } // end of build popups loop---------------------------------------------------------------------
  ?>
  <div id="filtswitchdiv"><!-- data filters and calendar / list  switches---------------------------- -->
    <div id="filtersdiv">
      <form action="seasonplanner.php#<?php echo 'wk'.date('W');?>" method="POST" name="filters">
        <table  border="0" align="center" cellpadding="5" cellspacing="5">
          <tr>
            <th align="center">Year
              <select name="Year">
              	<?php
				$yr=date('Y')+1;
				while($yr>='2013') { ?>
                <option value="<?php echo $yr; ?>" <?php if($postYear==$yr) echo 'selected="selected"';?>><?php echo $yr;?></option>
				<?php --$yr; } ?>
              </select>
            </th>
            <th>Venue
              <select name="venue">
                <option value="All" <?php if($postvenue=='All') echo 'selected="selected"';?>>All</option>
                <option value="Backyard" <?php if($postvenue=='Backyard') echo 'selected="selected"';?>>Backyard</option>
                <option value="Farm" <?php if($postvenue=='Farm') echo 'selected="selected"';?>>Farm</option>
                <option value="Market" <?php if($postvenue=='Market') echo 'selected="selected"';?>>Market</option>
                <option value="Pickup" <?php if($postvenue=='Pickup') echo 'selected="selected"';?>>Pickup</option>
              </select>
            </th>
            <th>Status
              <select name="Status">
                <option value="All" <?php if($postStatus=='All') echo 'selected="selected"';?>>All</option>
                <option value="Future" <?php if($postStatus=='Future') echo 'selected="selected"';?>>Future</option>
                <option value="Open" <?php if($postStatus=='Open') echo 'selected="selected"';?>>Open</option>
                <option value="Closed" <?php if($postStatus=='Closed') echo 'selected="selected"';?>>Closed</option>
                <option value="Unsched" <?php if($postStatus=='Unsched') echo 'selected="selected"';?>>Unscheduled</option>
              </select>
            </th>
            <th>Sort
              <select name="Sort">
                <option value="desc" <?php if($postSort=='desc') echo 'selected="selected"';?>>Latest first</option>
                <option value="asc" <?php if($postSort=='asc') echo 'selected="selected"';?>>Earliest first</option>
              </select>
            </th>
            <th><input name="submit" type="submit" value="Filter" /></th>
          </tr>
        </table>
      </form>
    </div>
    <div id="listswitchdiv">
      <table align="right" width="100">
        <tr>
          <td width="100" height="42" align="center" valign="middle" bgcolor="#cccccc" onmouseover="switchdiv(calendar,list,calswitchdiv,listswitchdiv)" style:><strong>To Calendar</strong></td>
        </tr>
      </table>
    </div>
    <div id="calswitchdiv">
      <table align="left" width="100">
        <tr>
          <td width="100" height="42" align="center" valign="middle" bgcolor="#cccccc" onmouseover="switchdiv(list,calendar,listswitchdiv,calswitchdiv)" style:><strong>To List</strong></td>
        </tr>
      </table>
    </div>
  </div>
  <!-- end of filters+switch div---------------------------------------------------------------- -->
  
  <div id="list">
    <table id="harvests" width="700" align="center">
      <tr>
        <th>Owner</th>
        <th>Crop</th>
        <th>Leader</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
      <?php
// loop through harvests and build list table ---------------------------------------------------
for($j=0 ; $j<$numrows ; ++$j) { 	
		switch ($harv[$j]['status'])
			{case 'unsched': $bg= $harv[$j]['ID_leader']>0 ? 'assigned' : 'unsched'; break;
			  case 'closed': $bg='closed'; break;
			  case 'open': $bg='open'; break;
			  default: $bg='unsched'; break; } ?>

      <tr class="<?php echo $bg; ?>" onmouseover="popup(pop<?php echo $harv[$j]['ID_harvest'];?>)">
        <td><a href="harvestupdate.php?harvesttemp=<?php echo $harv[$j]['ID_harvest'];?>" target="_blank">
		<?php echo $harv[$j]['farm']; ?></a></td>
        <td><?php echo cropstring($harv[$j]['ID_harvest']); ?></td>
        <td><?php $leader= isset($harv[$j]['ID_leader']) ?  $harv[$j]['ID_leader'] : 0;
				$lquery="select fname, lname, email, phone from pickers where ID_picker=$leader";
				$rsLeader = mysqli_query($piercecty, $lquery) or die(mysqli_error($piercecty));
				$lrow=mysqli_fetch_assoc($rsLeader);
				echo $lrow['fname']." ".$lrow['lname']; ?></td>
        <td><?php	echo date('M-d',strtotime($harv[$j]['s_date'])); ?></td>
        <td><?php echo $harv[$j]['status']; ?></td>
      </tr>
      <?php } // end of harvest list loop --------------------------------------------------------------
	  ?>
    </table>
  </div>
  <!-- end of list div----------------------------------------------- -->
  
<div id="calendar">
<?php 
$ts=strtotime("01-01-".$postYear." 6:00am"); //$ts will be the time stamp incremented by 86400 seconds each day

// find day of week of first day of year
$startday=date('w',$ts); //  
?>
<table id="caltable" align="center">
<tr>
<?php 
for ($d=0; $d<$startday; ++$d) echo "<td> </td>" ; // blank days for first row

if($postSort=='desc')  usort($harv, "sortAscending"); // for calendar the list must be ascending
$k=0; // index for harv[k] list of harvests

for ($d=$startday; $d<=367; ++$d) { // $d will count  up to 365 days --------------------
	if($d%7==0) { // if day is Sunday, start new row and add anchor  for auto scroll to current week
		$wk=date('W',$ts)+2; ?>
		</tr><tr id="<?php echo 'wk'.$wk;?>"><?php  }
		
?><td>
<table id="cell"> 
<tr><th><?php  echo date('D, M j',$ts);?></th></tr>
<?php while ( $k<$numrows and substr($harv[$k]['s_date'],0,10)==date('Y-m-d',$ts) ) { // a harvest is on this day 
?><tr onmouseover="popup(pop<?php echo $harv[$k]['ID_harvest'];?>)">
<?php switch ($harv[$k]['status'])
			{case 'unsched': $bg= $harv[$k]['ID_leader']>0 ? 'assigned' : 'unsched'; break;
			  case 'closed': $bg='closed'; break;
			  case 'open': $bg='open'; break;
			  default: $bg='unsched'; break; } ?>
<td class="<?php echo $bg; ?>"><a href="harvestupdate.php?harvesttemp=<?php echo $harv[$k]['ID_harvest'];?>" target="_blank">
<?php echo $harv[$k]['farm']."<br />".cropstring($harv[$k]['ID_harvest']);?></a></td></tr>     
<?php ++$k;
} // keep pulling rows and adding to cell table until get to a harvest not on that day ?>
</table>
&nbsp;
</td> 
<?php $ts=$ts+86400; // add another day to timestamp value

} // end of 365 days ------------------------------------------------------------------
?></tr>
</table>
</div><!-- end of calendar div----------------------------------------------- -->

<script>switchdiv(calendar,list, calswitchdiv, listswitchdiv)</script>

</div><!-- end of mainContent -->
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
<br class="clearfloat" />
</div><!-- end #container -->
</body>
</html>
<?php
((mysqli_free_result($rsGleans) || (is_object($rsGleans) && (get_class($rsGleans) == "mysqli_result"))) ? true : false);
?>
