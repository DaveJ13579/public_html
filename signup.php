<?php 
require_once('Connections/piercecty.php'); 
require_once('includes/sqlcleaner.public.php'); 
require_once('includes/dencode.inc.php'); 
require_once('includes/fixroster.inc.php'); 
require_once('includes/emailer.inc.php'); 
require_once('includes/smtpmailer.inc.php');

$editFormAction = $_SERVER['PHP_SELF']; // form will go back to current page
if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); } // current query string added back onto URL

$harvest="-1";
if (isset($_POST['harvesttemp'])) { $harvest = $_POST['harvesttemp']; } // checks POST for harvesttemp from 'signup for this harvest' button
if (isset($_GET['harvesttemp'])) { $harvest = $_GET['harvesttemp']; } // checks GET for harvesttemp from external direct link signup. Either one is the same harvest

$sorry=""; // initialize error code for various exit conditions

$query_rsharvest = sprintf("SELECT ID_harvest, h_date, h_time, pick_num, ID_leader, ID_leader2, harvests.status, city, carpool, pooltime FROM harvests, sites WHERE sites.ID_site=harvests.ID_site and ID_harvest = %s", GetSQLValueString($harvest, "int"));
$rsharvest = mysqli_query($piercecty, $query_rsharvest) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsharvest);
if($numrows<>1) { 	$sorry='noharvest'; header("Location: sorry.php?sorry=$sorry"); }
$row_rsharvest = mysqli_fetch_assoc($rsharvest);

$sw=''; $emailstr=''; $fname=''; $lname='';  $email=''; $IDpicker=0; $seats=0;// $sw is a switch to sort out whether need email address to distinguish duplicate names
$helper=''; $waiver1=''; $waiver2=''; $linkaccess='';

$jobsq="select * from jobs where jobname<>'' order by jobID";
$rsJobs=mysqli_query($piercecty,$jobsq);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "rosterinsertform")) { // if submit pressed

// capture form variables 

	if(isset($_POST['lname']))  $lname = trim(ucwords(strtolower($_POST['lname'])));
	if(isset($_POST['fname']))  $fname = trim(ucwords(strtolower($_POST['fname'])));
	if(isset($_POST['email']))  { $email = trim($_POST['email']); $emailstr=" AND email='$email'"; }
	if(isset($_POST['waiver1'])) $waiver1= "yes";
	if(isset($_POST['waiver2'])) $waiver2= "yes";
	if(isset($_POST['helper']))  $helper= "yes"; 
	if(isset($_POST['seats']) and $_POST['seats']>0)  $seats=$_POST['seats'];
	if(isset($_POST['solo']))  $seats=0;
	if(isset($_POST['needride']))  $seats=-1; 

// compress jobs checkboxes
$jobstr='000000000000';
for($x=1;$x<=12;++$x){
	if(isset($_POST["job$x"])) $jobstr=substr_replace($jobstr,'1',$x-1,1); 
	}

if(!isset($_GET['access']))  { $sorry='badlink';  header("Location: sorry.php?sorry=$sorry"); exit(); } // no access type in query string
$linkaccess=$_GET['access'];
if ($linkaccess=="public") {   // from public harvestlist
// is the harvest already closed?
if ($row_rsharvest['status']<>"open") { $sorry="closed";   header("Location: sorry.php?sorry=$sorry"); exit(); }
} // end from public harvestlist

elseif($linkaccess<>'link' && $linkaccess<>'select') {  $sorry='badlink';  header("Location: sorry.php?sorry=$sorry"); exit();  } // incorrect access code
// legitimate signup - either public and open  OR private and correct access (either 'link' or 'select')

// harvest past?
$hdate=$row_rsharvest['h_date'];
$htime=$row_rsharvest['h_time'];
$hdatetime=$hdate." ".$htime;

if(date('Y-m-d H:i')>$hdatetime && $linkaccess<>'link') { $sorry="past";   header("Location: sorry.php?sorry=$sorry"); exit(); }

// read waiver checked?
if ($waiver1!='yes') { $sorry='nowaiver'; header("Location: sorry.php?sorry=$sorry"); exit(); } 

// agree with waiver checked?
if ($waiver2!='yes') { $sorry='nowaiver'; header("Location: sorry.php?sorry=$sorry"); exit(); } 

// is the name in the database?
$query_rsName=sprintf("SELECT ID_picker, lname, fname, email FROM pickers WHERE fname=%s AND lname=%s $emailstr",
	GetSQLValueString($fname, "text"), 
	GetSQLValueString($lname, "text")); 
$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);
$totalRows_rsName = mysqli_num_rows($rsName);
 
if( $totalRows_rsName==0) { $sorry='noname'; header("Location: sorry.php?sorry=$sorry"); exit(); }  

// is only one name found?
if($totalRows_rsName == 1) { 

$email = $row_rsName['email'];
$IDpicker=$row_rsName['ID_picker'];

// delete from roster if already on it
$deleteq="delete from rosters where ID_harvest=$harvest and ID_picker=$IDpicker";
$rsDelete= mysqli_query($piercecty, $deleteq) or die(mysqli_error($piercecty));

//  How many on roster?
$signedupq="select count(ID_picker) as signedup from rosters where ID_harvest=$harvest and (status='leader' or status='signup' or status='assisted')";
$rsSignedup=mysqli_query($piercecty,$signedupq) or die(mysqli_error($piercecty));
$signeduprow=mysqli_fetch_assoc($rsSignedup);
$signedup=$signeduprow['signedup'];

// count seats	
$seatsq="select sum(seats) as seats from rosters where ID_harvest=$harvest and (status='signup' or status='leader' or status='assisted')";
$rsSeats=mysqli_query($piercecty,$seatsq) or die(mysqli_error($piercecty));
$seatsrow=mysqli_fetch_assoc($rsSeats);
$seatsavail=$seatsrow['seats'];

$waitornot=$seats+$seatsavail; // if waitornot is < 0 then must wait, else okay for roster

if(($signedup<$row_rsharvest['pick_num'] && ($waitornot>-1 || $seats>-1)) || $linkaccess=='link') { // not full or access is by direct link

// insert into roster
$status = "signup"; if($helper=='yes') { $status = "assisted"; } 
if(($row_rsharvest['ID_leader']==$IDpicker) or ($row_rsharvest['ID_leader2']==$IDpicker)) {$status='leader'; }
$IPaddress = $_SERVER["REMOTE_ADDR"]; 
$insertSQL ="insert into rosters (ID_harvest, regdate, ID_picker, seats, jobs, status, IPaddress) values ($harvest, now(), $IDpicker, $seats, '$jobstr', '$status', '$IPaddress')";
$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));

$IPquery="update pickers set IP_picker='$IPaddress' where ID_picker=$IDpicker"; // update the picker IP address from the roster signup IP address
$Result2 = mysqli_query($piercecty, $IPquery) or die(mysqli_error($piercecty));
$contactquery="update pickers set contactdate=now() where ID_picker=$IDpicker"; // update the picker's date of most recent contact
$Result3 = mysqli_query($piercecty, $contactquery) or die(mysqli_error($piercecty));

$switch='normal'; // switch is changed below to tell emailer function what type message to send
$eID=encode($IDpicker);
$eIDharvest=encode($harvest);
$thanksgoto ="hthank.php?pt=".$eID."&ht=".$eIDharvest; 
emailer($IDpicker, $fname, $email, $harvest, $switch, $thanksgoto,$seats); // calls emailer function to send email to picker
if($linkaccess<>'link') fixroster($harvest, $piercecty);
header("Location: $thanksgoto"); exit();
} // end of harvest not full and non-negative seats available (or direct link access)

else { // harvest is full or no seats available for needer and NOT direct link access
$eID=encode($IDpicker);
$switch='waiting';
$waitinggoto = "hwaiting.php?pt='$fname'&ht=$harvest&eID=$eID"; 
if($email=='') { $sorry='noemail'; header("Location: sorry.php?sorry=$sorry"); exit(); } 
emailer($IDpicker, $fname, $email, $harvest, $switch, $waitinggoto,$seats);
header("Location: $waitinggoto"); exit();

} // end of harvest is full

} // end of only one name found
// email address already obtained then exit
if($totalRows_rsName>1 && $emailstr<>"") { // more than one picker (same name and email) so exit and explain about duplicates 
  	header('Location: dup-registrations.php');
	exit(); }

if($totalRows_rsName>1 && $sw=="") { // more than one name found and have not asked for email so set switch and continue with html
 $sw="need"; }

} // submit not pressed
$cusquery="select pagetext from custom2 where pagename='waivertxt'";
$rsCustom=mysqli_query($piercecty,$cusquery);
$textrow=mysqli_fetch_assoc($rsCustom);
$pagetext=$textrow['pagetext'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Harvest Signup</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7; } 
-->
</style>
</head>

<body class="SH">

<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
    <h3 class="SH"><strong>Harvest Signup</strong><strong></strong></h3>
    <p>Enter your first name and last name, read the Release and Waiver of Liability, check off the box indicating that you have read it, check the box if you agree with the Release and Waiver of Liability, and then press the Signup button.</p>
<br />
    <form action="<?php echo $editFormAction; ?>" id="rosterinsertform" name="rosterinsertform" method="POST">
      <table width="860" border="3" cellpadding="2" cellspacing="2" id="rosterlist">
        <tr align="center">
          <th>When</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th><p>General location</p></th>
        </tr>
        <tr>
          <td align="center"><?php echo date('l  m/d/Y',strtotime($row_rsharvest['h_date'])).'<br />'. date('g:i A',strtotime($row_rsharvest['h_time'])); ?></td>
          <td>&nbsp;&nbsp;&nbsp;<input name="fname" type="text" id="fname" value="<?php echo $fname; ?>" maxlength="15" /></td>
          <td>&nbsp;&nbsp;&nbsp;<input name="lname" type="text" id="lname" value="<?php echo $lname; ?>" maxlength="30" /></td>
          <td align="center"><input name="hiddenField" type="hidden" id="hiddenField" value="<?php echo $harvest ?>" />
          <?php echo $row_rsharvest['city'] ?></td>
        </tr>
   		<?php if($sw=="need") { // if need email also then show input field ?>
        	<tr align="left">
        	  <td colspan="5" style="background-color:#ffa0a0;">There is more than one volunteer with that name. Please add your registered email address and check the waiver box.</td></tr>
			<tr>
          	  <td align="right" colspan="2">email address:</td>
       		  <td colspan="3"><input name="email" type="text" id="email" size="40" maxlength="40" /></td>
		</tr> <?php  } // end of show email input field  ?>
        <tr>
          <td align="center">&nbsp;</td>
          <td colspan= "3">
          <?php 
          	if($row_rsharvest['carpool']=='none'){ // echo '<strong>There is no carpool for this harvesting trip.</strong><br />You will be given the address to drive to after signing up.';
			}
			elseif($row_rsharvest['carpool']=='option') { ?>
				<strong>There is a carpool available for this harvesting trip.</strong> [<a href="help/carpool-help.php" target="_blank">How the carpool works</a>]<br />
				<input name="seats" type="int" size="3" maxlength="2" /> 
				If you can drive and provide extra seats for other volunteers, put the number of extra seats in the box.<br />
				<input type="checkbox" name="solo" value="0" /> 
				Check here if you will drive yourself to the harvest separately from the carpool. <br />
                <input type="checkbox" name="needride" value="-1"/> 
                Check here if you want a carpool ride from another volunteer.<br />
			<?php } else {  ?>
           		<strong>All vehicles must travel together to this harvest.</strong> [<a href="help/carpool-help.php" target="_blank">How the carpool works</a>]<br />
				<input name="seats" type="int" size="2" maxlength="2" /> 
				If you can drive and provide extra seats for other volunteers, put the number of extra seats in the box.<br />
                <input type="checkbox" name="needride" value="-1"/> 
                Check here if you want a carpool ride from another volunteer.<br />
			<?php } ?>
            </td>
        </tr>
      </table>
<p>New in 2016: We have listed all of the jobs that need to be done during a typical glean. We appreciate your coming and harvesting with us. If you would be interested in helping with any of the tasks below, please check any that apply. Please note that some of the tasks require you to lift 50 lbs.</p>
<blockquote>
<?php 
while($jobsrow=mysqli_fetch_assoc($rsJobs)) { ?>
    <input type="checkbox" name="job<?php echo $jobsrow['jobID'];?>" value="0" /> <?php echo $jobsrow['jobtext'].'<br />';
	} ?>
</blockquote>
<div style="padding:10px;width:80%;border:solid 1px black; color:#000000; font-family:'Times New Roman', Times, serif;">
  <p><strong>Liability Waiver and Indemnity Agreement</strong></p><p>By checking the boxes below, I agree to indemnify and hold the landowners, Pierce Conservation District, Harvest Pierce County, its employees, other volunteers, and any other third party for whom I am performing volunteer services, harmless from and against any liability, claim, injury, or costs arising from or resulting from my work as a volunteer. Furthermore, I acknowledge that there are potential hazards associated with Harvest Pierce County events and I agree to exercise common sense and follow all safety precautions to avoid accident and injury.</p>
</div>
      <p>
      <p><input type="checkbox" name="waiver1" id="waiver1" /> 
      I have read the Liability Waiver  and Indemnity Agreement.</p>
      <p><input type="checkbox" name="waiver2" id="waiver2" />
       I agree to the terms of the Liability Waiver  and Indemnity Agreement.</p>
 	 <p><a href="harvestlist.php">I do not want to check the waiver agreement.</a></p>
     <input type="submit" name="submit" id="submit" value="Signup for this harvest" />
  </label>
 		<input name="harvesttemp" type="hidden" value="<?php echo $harvest ?>" />
        <input type="hidden" name="MM_insert" value="rosterinsertform" />
      </p>
    </form>
<!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
