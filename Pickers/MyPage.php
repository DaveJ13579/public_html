<?php
if (!isset($_SESSION)) {session_start();}
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php'); 
require_once('../includes/dencode.inc.php');
include_once('../includes/converter.inc.php');
if(isset($_GET['logout'])) {unset($_SESSION['ID_picker']); header('Location: ../index.php'); exit();}
if(isset($_POST['login'])) {
	extract($_POST);
	$q="select ID_picker from pickers where fname=".GetSQLValueString($fname,"text")." and lname=".GetSQLValueString($lname,"text")." and volpass='$volpass'";
	$rsQ=mysqli_query($piercecty, $q);
	if($rsQ and mysqli_num_rows($rsQ)<>1) {$err='Volunteer not found';
		} else { 
	$row=mysqli_fetch_assoc($rsQ);
	extract($row);
	$_SESSION['ID_picker']=$ID_picker;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>My Page</title>
<link href="../piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
}
#narrowbody {
	padding-right: 40px;
	padding-left: 40px;
}
#leftside {
	float: left;
	margin-left: 0px;
	padding-left: 10px;
	width:500px;
}
#rightside {
	float: right;
	background-color: #f0eada; 
	background-repeat: repeat-x;
	padding-left: 20px;
	padding-bottom: 10px;
	border: 1px solid #000;
	margin-bottom: 20px;
	width: 250px;
	height: 400px;
	margin-left: 20px;
}
-->
</style>
</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="../images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('../includes/navlinks2.inc.php'); ?>
<div id="mainContent">
<?php 
if(!(isset($_SESSION['ID_picker']))) { ?>
<h3><strong>Please Log In</strong></h3>
<p>If you do not yet have a password, you must give yourself one by going to the <a href="ContactUpdateLink.php">Volunteer Update Request</a> page to send yourself an email with a link to the Update page. 
<form name="login" method="POST" action="MyPage.php">
<table border="0" align="center" cellpadding="2" cellspacing="1">
<tr><td>First name:</td><td><input name="fname" type="text" size="30" maxlength="15" /></td></tr>
<tr><td>Last name:</td><td><input name="lname" type="text" size="30" maxlength="30"/></td></tr>
<tr><td>Password:</td><td><input name="volpass"  type="password" size="30" maxlength="15"/></td></tr>
<tr><td><input type="submit" name="login" value="Log In" /></td></tr>
</table>
</form>
<br />
<?php }  // end of no session set
else { // session is set
	$ID_picker=$_SESSION['ID_picker'];
	$q="select * from pickers where ID_picker=$ID_picker";
	$rsQ=mysqli_query($piercecty, $q);
	if(!$rsQ) {$err='Volunteer not found';
		} else { 
	$row=mysqli_fetch_assoc($rsQ);
	extract($row);
?>
<div id="rightside">
<?php echo 
"<br /><strong>$fname $lname</strong><br />
<br />
$address, $city<br />
$email<br />
$phone<br />$phone2<br /><br />";
?>
<a class="button" style="width:100px; padding:10px; font-size:1em;" href="ContactUpdate.php?ID=<?php echo encode($ID_picker);?>">Update</a>
<br /><br />
<p><strong>Links</strong></p>
<a href="ParticipationTerms.php">Terms of Participation</a><br />
<a href="https://piercecd.org/190/Urban-Agriculture">Contact</a><br />
<a href="MyPage.php?logout=yes">Log out</a>
</div>
<div id="leftside">
<h2 style="text-align:center;">My Page</h2>
<?php 
// compile attendance summary	
$queryatt = "SELECT status, COUNT(ID_picker) FROM rosters WHERE ID_picker = $ID_picker GROUP by status";
$resultatt = mysqli_query( $piercecty, $queryatt);
// compile harvest history
$queryhist = "SELECT harvests.ID_harvest, sites.farm,rosters.ID_harvest, harvests.h_date, rosters.status FROM rosters, harvests, sites WHERE ID_picker = $ID_picker and harvests.ID_harvest = rosters.ID_harvest and sites.ID_site = harvests.ID_site ORDER by harvests.h_date DESC";
$rsHist = mysqli_query( $piercecty, $queryhist) or die(mysqli_error($piercecty));
// generate summary table row
?><strong>Attendance summary</strong>
<br /><br />
<table border="2" cellpadding="5" cellspacing="2" align="center">
<tr>
<?php
// generate summary table row
while($rowatt = mysqli_fetch_array($resultatt)) { ?>
	<td style="background-color:#b1c9e5;">
    	<?php $st=($rowatt['status']=='intake' ? 'assisted' : $rowatt['status']);
		echo $st.": ".$rowatt['COUNT(ID_picker)'];?>
	</td>
  	<?php } ?>
	</tr></table>
<p><strong>Glean history</strong> - Click on the Status link to see details</p>
<div style="overflow:scroll; height:400px;">
<table border="4" cellspacing="2" cellpadding="3" align="center" style="font-size:.8em;">
<tr bgcolor="#ddd8c2">
	<th>Glean Date</th>
	<th>Farm</th>
    <th>Status</th>
    <th>Cancel</th>
</tr>
<?php
//generate history table rows	  
while ($rowhist = mysqli_fetch_assoc($rsHist)) { ?>
<tr >
	<td style="background-color:#ebdfcd;"><?php echo date('M d, Y',strtotime($rowhist['h_date']));?></td>  
	<td style="background-color:#ebdfcd;"><?php echo $rowhist['farm'];?></td>
    <td style="background-color:#ebdfcd;">
	<?php $st=$rowhist['status']=='cancel' ? 'cancelled' : $rowhist['status'];
		  if($rowhist['status']=='signup') $st='signed up';
		  if($st=='waiting') {
			  $waitstatus="../waitstatus.php?ID=".encode($ID_picker);?>
			<a href='<?php echo $waitstatus;?>' target='_blank'><?php echo $st;?></a>
			<?php } else {
		  	$thanksgoto = "../hthank.php?pt=".encode($ID_picker)."&ht=".encode($rowhist['ID_harvest']);?>
			<a href='<?php echo $thanksgoto;?>' target='_blank'><?php echo $st;?></a><?php 
			} ?>
	</td>
     <?php 
	$today=date('Y-m-d');
	if($rowhist['h_date']>=$today && $st<>'cancel') {
		$cancellink="../cancel.php?ID=".encode($ID_picker).'&h='.encode($rowhist['ID_harvest']);?>
		<td style="background-color:#ebdfcd;"><a href='<?php echo $cancellink;?>' target='_blank'>cancel</a></td><?php 
		}
	?>
</tr>
<?php } ?>
</table>
</div>
<br />
</div>
<?php
} // end of found volunteer from ID_picker
} // end of logged in
 ?>
 <!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
 <div id="footer">
 <?php include('../includes/footer.inc.php');?>
 <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
