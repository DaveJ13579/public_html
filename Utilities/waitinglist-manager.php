<?php 
require_once('../Connections/piercecty.php'); 
require_once('../includes/dencode.inc.php');
require_once('../includes/sqlcleaner.php');
require_once('../includes/smtpmailer.inc.php');

if (!isset($_SESSION)) {   session_start(); }
$MM_authorizedUsers = "all,change";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$harvest=0; $newlimit=0; $err=''; $promote=0; $pick_num=''; $infoms='';

if(!isset($_GET['harvest']))  { // no harvest number submitted
	$err="no harvest number in query string.<br />"; }

else { // have harvest number

$harvest=$_GET['harvest'];

// get this harvest's info
$queryharvest = "select harvests.ID_harvest, pick_num, h_date, h_time, ID_leader, pickers.lname, sites.farm from harvests, sites, pickers where harvests.ID_site=sites.ID_site and harvests.ID_leader=pickers.ID_picker and ID_harvest=$harvest";
$rsHarvest = mysqli_query($piercecty, $queryharvest) or die(mysqli_error($piercecty));
$rowharv = mysqli_fetch_assoc($rsHarvest);
$numharvest = mysqli_num_rows($rsHarvest);
$pick_num=$rowharv['pick_num'];
if($numharvest<>1) { $err="no harvest with that number<br />"; } else
	{$infoms="Harvest info loaded:"; }

} // end of have harvest number

if(isset($_POST['empty']) and $numharvest==1) { // empty the waiting list for this harvest
	$queryempty="delete from rosters where ID_harvest=$harvest and status='waiting'";
	$rsEmpty = mysqli_query($piercecty, $queryempty) or die(mysqli_error($piercecty)); 
	$infoms="Waiting list emptied.<br />"; 
} // end of empty waiting list

if(isset($_POST['newlimit']) and $err=='') {
	
	// new limit submitted
	$newlimit=$_POST['newlimit']; 
	
	if($pick_num<$newlimit) { // update pick limit
		$querylimit="update harvests set pick_num=$newlimit where ID_harvest=$harvest";
		$rsLimit = mysqli_query($piercecty, $querylimit) or die(mysqli_error($piercecty)); 
		$infoms="Picker number changed to ".$newlimit."<br />"; 
		$pick_num=$newlimit;
		}
} // end of if isset newlimit

if(isset($_POST['promote'])) { // promote number submitted
$promote=$_POST['promote'];
if($promote>0) { // positive promote number
$queryroster = "select pickers.ID_picker, fname from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvest and status='waiting' order by rosters.regdate  limit $promote";
$rsRoster = mysqli_query($piercecty, $queryroster) or die(mysqli_error($piercecty));
$numwaiting = mysqli_num_rows($rsRoster); // how many are waiting up to the promote number

while ($rowwait = mysqli_fetch_assoc($rsRoster)) { // update the waiters
	$fname=$rowwait['fname'];
	$waiter=$rowwait['ID_picker'];
	$query3="update rosters set status='signup', regdate=now() where ID_picker=$waiter and ID_harvest=$harvest";
	$Result3 = mysqli_query($piercecty, $query3) or die(mysqli_error($piercecty));

// send waiter an email
$getemail="select email from pickers where ID_picker=$waiter";
$Result4 = mysqli_query($piercecty, $getemail) or die(mysqli_error($piercecty));
$row4=mysqli_fetch_assoc($Result4);
$email=$row4['email'];

$eIDpicker=encode($waiter);
$thanksgoto = "http://www.piercecountygleaningproject.org/hthank.php?pt=".$eIDpicker."&ht=".encode($harvest); 
$subject = "Pierce County Gleaning Project roster status";
$cancelgoto="http://www.piercecountygleaningproject.org/cancel.php?ID=".$eIDpicker."&h=".encode($harvest); 
$historygoto="http://www.piercecountygleaningproject.org/volunteer.php";
$message = 'Hello '.$fname.','."\n\n".'You have been added to the roster of the harvest you were on the waiting list for. Go to this web page for details: '."\n".$thanksgoto.'.';
$message.="\n\n\n".'If you find that you cannot attend and want to cancel this sign up, it may allow someone else to take your place. Go to this page to cancel your sign up: '."\n".$cancelgoto;
$message.="\n\n".'You can check your attendance history, and verify your signup for this harvest, any time at this web page: '."\n".$historygoto; 
if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

} // end of cycle through waiters
$infoms.=$numwaiting." waiters promoted.<br />";
} // end of if promote>0

} // end of promote number submitted

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>waiting list manager</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
  <div id="mainContent">
    <h2>Waiting list manager</h2>

<p> <strong>Actions: </strong><?php echo $infoms; ?></p>

<?php if(!$err=='') { echo $err; }

else { // okay to display harvest and roster information
?>
	<p>Harvest number: <?php echo $rowharv['ID_harvest']; ?><br />
		Date and time: <?php echo $rowharv['h_date']." ".$rowharv['h_time']; ?><br />
		Harvest leader: <?php echo $rowharv['lname']; ?><br />
		Picker limit: <?php echo $pick_num; ?><br />

<?php 
		$querysignedup="select count(status) as expected from rosters where ID_harvest=$harvest and (status='signup' or status='leader' or status='intake' or status='harvested')";
        $rsSignedup=mysqli_query($piercecty, $querysignedup) or die(mysqli_error($piercecty)); 
		$rowsignedup=mysqli_fetch_assoc($rsSignedup);
		$expected=$rowsignedup['expected'];
?>
		Pickers signed up: <?php echo $expected; ?></p>
 
<p><strong>Roster statistics</strong></p>
 <?php 		
 $querystatus="select status, count(status) as ctstatus from rosters where ID_harvest=$harvest group by status";
 $rsStatus=mysqli_query($piercecty, $querystatus) or die(mysqli_error($piercecty)); 
 ?>
  <table width="900" border="1">
    <tr>
<?php while ($row=mysqli_fetch_array($rsStatus)) {  ?>
      	<td align="center"><?php echo $row['status']."=".$row['ctstatus'] ?></td>
     	<?php } ?>
    </tr>
  </table>
<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="newlimit">
Enter new picker limit <input  size="5" maxlength="5" type="text" name="newlimit"  value="<?php echo $pick_num; ?>"/>
      <input type="submit" name="submitlimit" value="Update" />
      </form> 
 <br  />
<form action="<?php echo $editFormAction; ?>" method="post" name="promote">
Enter number of waiters to promote <input  size="5" maxlength="5" type="text" name="promote" />
      <input type="submit" name="submitpromote" value="Promote" /> 
Note: promoting does not distinguish those needing carpool seats and those who do not. 
      </form> 
<?php  } // end of okay to display harvest info and roster

$querywaiting = "select rosters.ID_picker, pickers.fname, pickers.lname, rosters.regdate, rosters.status from rosters, pickers where rosters.ID_picker=pickers.ID_picker and ID_harvest=$harvest and status='waiting' order by rosters.regdate";
$rsWaiting = mysqli_query($piercecty, $querywaiting) or die(mysqli_error($piercecty));

?>
<p><strong>Waiting list for harvest <?php echo $harvest; ?></strong>&nbsp; &nbsp;&nbsp;&nbsp; [<a href="rosterviewer.php?harvesttemp=<?php echo $harvest;?> " target="_blank">view entire roster</a>]</p>
<table width="900" border="1" cellpadding="1" cellspacing="1" id="rosterlist">
      <tr>
        <th scope="col">ID_picker</th>
        <th scope="col">Name</th>
        <th scope="col">Regdate</th>
        <th scope="col">status</th>
      </tr>
      <?php while ($rowwaiting = mysqli_fetch_assoc($rsWaiting)) { ?>
        <tr class="centercell">
          <td><?php echo $rowwaiting['ID_picker']; ?></td>
          <td><?php echo $rowwaiting['lname'].", ". $rowwaiting['fname']; ?></td>
          <td><?php echo $rowwaiting['regdate']; ?></td>
          <td><?php echo $rowwaiting['status']; ?></td>
        </tr>
        <?php }  ?>
    </table>
<br />
<table><tr><td>Delete all waiters. <em><strong>Caution:</strong></em> This is permanent and should be done <em><strong>only </strong></em>after a harvest is completed.</td>
<td><form action="<?php echo $editFormAction; ?>" method="post" name="empty">
<input type="submit" name="empty" value="Delete" />
</form></td>
</tr></table>


  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</div>
</body>
</html>
