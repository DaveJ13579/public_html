<?php require_once('Connections/piercecty.php'); 
include_once('includes/converter.inc.php');
$IP = $_SERVER['REMOTE_ADDR'];

// if($IP=='67.189.67.198') { header("Location: HitsDiverter.php?IP=$IP"); exit(); } // jimfromoz@gmail.com says was doing it manually
// if($IP=='67.168.243.21') { header("Location: HitsDiverter.php?IP=$IP"); exit(); }
// if($IP=='127.0.0.1') { header("Location: HitsDiverter.php?IP=$IP"); exit(); }

// find all open and future harvests
$harvests = "SELECT harvests.h_date, harvests.h_time, harvests.pick_num, sites.branch, sites.region, carpool, spots.city as spotcity, harvests.ID_harvest, harvests.status, sites.spray, harvests.otherinfo FROM harvests, sites, spots WHERE harvests.ID_site = sites.ID_site and harvests.spot=spots.ID_spot and harvests.h_date >= curdate() AND harvests.status = 'open' and harvests.SHT<>'Yes' ORDER BY harvests.h_date, harvests.h_time";
$rsHarvests = mysqli_query($piercecty, $harvests) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsHarvests);

// echo '<pre>'; while($test=mysqli_fetch_assoc($rsHarvests)) { print_r($test);} exit; // prints all harvests in $rsHarvests

// compile weights for harvests since 2012
$query_rsHarvestHistory = "SELECT format(sum(calcwgt),0) as weight, date_format(h_date,'%Y') as year from harvests where year(h_date)>'2012' group by year(h_date) order by year(h_date) desc";
$rsHarvestHistory = mysqli_query($piercecty, $query_rsHarvestHistory) or die(mysqli_error($piercecty));

function rosterstats($IDharvest) { // figures out if harvest is full so that colors can be set in the calendar
				global $piercecty;
 				$query_count = "SELECT COUNT(ID_picker) as pickers FROM rosters where  ID_harvest = $IDharvest";
				$counttot = mysqli_query($piercecty, $query_count) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($counttot);
				$totalct=$row['pickers'];
				$query_cancel = "SELECT COUNT(ID_picker) as cancel FROM rosters WHERE ID_harvest = $IDharvest AND status = 'cancel'";
				$canceltot = mysqli_query($piercecty, $query_cancel) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($canceltot);
				$cancelct=$row['cancel'];			
				$query_wait = "SELECT COUNT(ID_picker) as waiting from rosters WHERE ID_harvest = $IDharvest AND rosters.status = 'waiting'";
				$waittot = mysqli_query($piercecty, $query_wait) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($waittot);				
				$waitct=$row['waiting'];
				$signedup = $totalct - $cancelct - $waitct;
				$query_picknum = "SELECT pick_num FROM harvests WHERE ID_harvest = $IDharvest";
				$rsPicknum = mysqli_query($piercecty, $query_picknum) or die(mysqli_error($piercecty));
				$picknumrow = mysqli_fetch_assoc($rsPicknum);				
				$picknum=$picknumrow['pick_num'];
				$full= $signedup<$picknum ? false : true;
				return $full;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvests</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
th {
	line-height: normal;
	background-color: #b2c2d7;
	text-align:center;
}
td { text-align:center;}
#blogbutton { font-size: 1.1em; }
/* calendar view harvest cells */
.full { background-color:#FFC;}
.notfull {background-color:#CFC;}
#caltable {
	border-collapse: collapse;
	width: 800px;	
	border:4px solid #444488;
}
#caltable tr { vertical-align: top; }
#caltable tr td { overflow:auto; }
.pop {
	display: none;
	width: 860px;
	overflow: auto;
	padding: 5px;
}
.pop th { border:1px solid black; }
#calendar { width: 840px; }
#cell { 	width: 114px; }
#cell tr {
	height: 1pt;
	background-color: #DBFEFF;
}
#cell tr td {
	height: 1pt;
	border:1px solid #000000;
	padding-left: 3px;
}
#harvestlist { 	border-collapse: collapse; }
#harvestlist td { 
	border:1px solid black;
	background-color:#ffffff;
 }
 #daysofweek th { border:1px solid white;background-color:#444444;color:#ffffff;}
</style>
<script type="text/javascript">
var tempvar = null;
function popup(show){
	show.style.display="block"
	if (tempvar && (tempvar !== show)) tempvar.style.display="none"
	tempvar=show
}
function scrollWin() {
if(window.pageYOffset<=470)
{
    setTimeout(function() {
		window.scrollTo(0,window.pageYOffset+3);
        scrollWin();
    }, 5);
   }
}
function lookdown(ele) { ele.innerHTML="Click to see details below"; }
function lookup(ele,crop) { ele.innerHTML=crop; }
</script>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
   <?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
<h3><strong>Harvests and Other Events</strong></h3>
<?php 
if($numrows == 0 ) { // if there are no future open harvests
$botkiller=rand(1,2)==1 ? "sign" : "signing ";
echo "<p>There are <strong>no harvests currently available</strong> for ".$botkiller."up. If you have heard of a harvest through email, Facebook or your neighbor, but you do not see it listed here, it means that the roster is closed.</p><br /><br />"; } 

else { // show all harvests and headings 
?>
<div id="calendar">
<?php 
$ts=strtotime(date('Y-m-d'))+3610; //$ts will be the time stamp incremented by 86400 seconds each day
// find day of week of today
$startday=date('w',$ts); //  
?>
<p>Before signing up for an event, please be sure that you and each person signing up has registered previously on the <a href="pickerinsert.php" title="Harvester registration">Volunteers</a> page. If you have already registered as a volunteer you do not have to do so again. Each adult must sign up separately  for events. To sign up for an event, click on the 'Sign up for this event'  button. Once you sign up, you will receive a confirmation letter with the exact  address and detailed information about the event. If the roster is full, your name will be  added to a waiting list and you will receive an email if anyone ahead of you  cancels their spot.</p>
<p>Move the mouse over harvests on the calendar to see details and a sign up link below the calendar. Green harvests have roster openings left; yellow harvests have a waiting list.</p>

<table id="caltable" align="center">
<tr id="daysofweek"><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
<tr>
<?php 
for ($d=0; $d<$startday; ++$d) echo "<td> </td>" ; // blank days for first row
$calharv=mysqli_fetch_assoc($rsHarvests);
if(isset($calharv['ID_harvest'])) {
	$full=rosterstats($calharv['ID_harvest']);
}
for ($d=$startday; $d<=27; ++$d) { // $d will count  up to 27 days --------------------
	if($d%7==0) { // if day is Sunday, start new row and add anchor  for auto scroll to current week
		$wk=date('W',$ts)+2; ?>
		</tr><tr id="<?php echo 'wk'.$wk;?>"><?php  }
$bgcolor= date('Y-m-d')==date('Y-m-d',$ts) ? '#ffcccc' : '#e2f2ff'; // today has pink background, else lightblue
?><td style="border:1px solid black;background-color:<?php echo $bgcolor;?>">
<table id="cell"> 
<tr><th><?php  echo date('M j',$ts);?></th></tr>
<?php while(isset($calharv['h_date']) && substr($calharv['h_date'],0,10)==date('Y-m-d',$ts) )  { // a harvest is on this day ?> 
<tr onclick="popup(pop<?php echo $calharv['ID_harvest'];?>),scrollWin()">
<?php $bg= $full ? 'full' : 'notfull'; 
	// compile crops list from a harvest number
	$crops=cropstring($calharv['ID_harvest']);
?>
<td class="<?php echo $bg; ?>" onmouseover="lookdown(this)" onmouseout="lookup(this,'<?php echo $crops;?>')"><?php echo $crops;?> </td>
</tr>     
<?php $calharv=mysqli_fetch_assoc($rsHarvests); 
			if(isset($calharv['ID_harvest'])) $full=rosterstats($calharv['ID_harvest']);
} // keep pulling rows and adding to cell table until get to a harvest not on that day ?>
</table>
&nbsp;<br/>&nbsp;
</td> 
<?php $ts=$ts+86400; // add another day to timestamp value

} // end of 27 days ------------------------------------------------------------------
?></tr>
</table>
<br class="clearfloat" />
</div><!-- end of calendar div----------------------------------------------- -->
<br />
<div style="width:820px;"> <!-- shell placeholder for popup divs div spacing------------------------------- -->
<?php  // construct the popups for all harvests ------------------------------------------
mysqli_data_seek($rsHarvests,0); // reset harvest rows array
while($harv=mysqli_fetch_assoc($rsHarvests)) {  
 ?>
<div class="pop" id="pop<?php echo $harv['ID_harvest']; ?>">
<table width="820" cellpadding="3" cellspacing="0" id="harvestlist">
      <?php
				$IDharvest = $harv['ID_harvest'];
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
				$picknum=$harv['pick_num'];
				$full=$signedup<$picknum ? '#CFC' : '#FFC';	

				$query_seats="select sum(seats) as seats from rosters where ID_harvest = $IDharvest and status<>'cancel' and status<>'absent' and status<>'waiting'";
				$rsSeats=mysqli_query($piercecty,$query_seats) or die(mysqli_error($piercecty));
				$row = mysqli_fetch_assoc($rsSeats);
				$seats = $row['seats'];				

			?>
			<tr>
            <th rowspan="6" style="background-color:<?php echo $full; ?>; padding:10px;"><form id="signup" name="signup" method="post" action="signup.php?access=public">
                <input name="harvesttemp" type="hidden" value="<?php echo $harv['ID_harvest'];?>"/>
                <input type="submit" name="Submit2" id="Submit2" style="font-size:18px;height:70px; width:150px; background-color:#bdedff;white-space:normal;" onmouseover="style.fontWeight='bold'" onmouseout="style.fontWeight='normal'" value="<?php if($signedup<$picknum) { echo "Sign up for this event"; } else { echo "Add name to waiting list"; }?>" />
             </form></th>
            <th>When</th>
            <th>Crop or task</th>
            <th>Crop<br />sprayed?</th>
            <th>General<br />location</th>
          </tr>
          <tr>
            <td><?php echo date('l, M j',strtotime($harv['h_date'])).'<br />'.date('g:i A',strtotime($harv['h_time'])); ?></td>
            <td><?php $crops=cropstring($IDharvest); echo $crops; ?></td>
            <td><?php echo $harv['spray']; ?></td>
            <td><?php echo $harv['region']; ?></td>
          </tr>
          <tr>
            <th>Total volunteers<br />needed</th>
            <th>Number on<br />roster</th>
            <th>Number on<br />waiting list</th>
            <th><?php
				if($harv['carpool']=='option' || $harv['carpool']=='all') {
					 if($seats>=0) {echo 'Available extra<br />carpool seats';} else { echo 'Carpool seats<br />still needed';}
				}?></th>
          </tr>
          <tr>
            <td><?php echo $picknum; ?></td>
            <td><?php echo $signedup ?></td>
            <td><?php echo $waitct; ?></td>
            <td><?php 
			if($harv['carpool']=='option' || $harv['carpool']=='all') echo abs($seats); ?>&nbsp;</td>
          </tr>
          <tr>
            <td colspan = "4" class="leftjustify"><?php echo $harv['otherinfo']; ?> </td>
          </tr>
			<tr>
            <td colspan = "4" class="leftjustify">
            Getting there:
				<?php 
					switch($harv['carpool'])
					{  	case "option": echo 'You may join the carpool, meeting in '.$harv['spotcity'].', or drive to the harvest yourself.'; break;
						case "all": echo ' This harvest is by carpool <strong><em>only</em></strong>, meeting in '.$harv['spotcity'].'.'; break;
						default: echo ' You will see the harvest address after signing up.'; break;
					} ?>
            </td>
            </tr>
      </table></div>
  <!-- end of pop -->
  <?php } // end of build popups loop---------------------------------------------------------------------
} // end of else show all harvests and headings
?>
<br class="clearfloat" />
<p><span style="color:red; font-style:italic; font-weight:bold;">New feature</span>: While you are waiting for new harvests to be posted, sign up for a new feature called My Page which will show all of your current signups and let you view harvest details and your complete harvest history. You will also be able to get on waiting lists without waiting for an email. To use this feature, you must first give yourself a password. Go to the <a href="Pickers/ContactUpdateLink.php">Volunteer Update</a> page to be sent a link where you can set the password and update your contact information.</p>
<br class="clearfloat" />
</div> <!-- end of popup div spacing shell----------------------------------------------- -->
  <p>&nbsp;</p>
<div style="width:95%;align:center;"><!-- loser section div -->
  <table width="425" border="0" cellpadding="1" cellspacing="1" id="blogbutton" align="center">
    <tr>
      <td width="375" height="36" align="left" id="blogbutton">To get the latest news and updates about Harvest Pierce County's Gleaning Project, visit our Facebook page.</td>
      <td width="125"><a href="https://www.facebook.com/pages/Pierce-County-Gleaning-Project/465366500064" target="_blank"><img src="images/Facebook.png" width="120" height="36" /></a></td>
    </tr>
  </table>
  <br />
  <table width="400" align="center" border="2" cellspacing="2" cellpadding="5">
	 <tr>
		  <th width="80">Year</th>
		  <th width="286">Pounds of food that would otherwise have gone to waste</th>
        </tr>
 <?php 
 while($rowp = mysqli_fetch_assoc($rsHarvestHistory)) {
?><tr><td><?php echo $rowp['year'];?></td>
			<td><?php echo $rowp['weight']; ?></td>
	 </tr>
<?php } ?>
    </table>
  <p>&nbsp;</p>
</div><!-- end of lower section div -->
</div><!-- end of MainContent div -->
<?php require_once('includes/footer.inc.php'); ?>
</div>
</body>
</html>
<?php
((mysqli_free_result($rsHarvests) || (is_object($rsHarvests) && (get_class($rsHarvests) == "mysqli_result"))) ? true : false);
((mysqli_free_result($rsHarvestHistory) || (is_object($rsHarvestHistory) && (get_class($rsHarvestHistory) == "mysqli_result"))) ? true : false);
?>
