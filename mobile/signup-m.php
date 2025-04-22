<?php 
require_once('../Connections/piercecty.php');
require_once('../includes/dencode.inc.php');
require_once('../includes/sqlcleaner.php');
require_once('../includes/smtpmailer.inc.php');

$editFormAction = $_SERVER['PHP_SELF']; // form will go back to current page
if (isset($_SERVER['QUERY_STRING'])) { $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); } // current query string added back onto URL

$harvest="-1";
if (isset($_POST['harvesttemp'])) { $harvest = $_POST['harvesttemp']; } // checks POST for harvesttemp from 'signup for this harvest' button
if (isset($_GET['harvesttemp'])) { $harvest = $_GET['harvesttemp']; } // checks GET for harvesttemp from external direct link signup. Either one is the same harvest

$sorry="";


$query_rsHarvest = sprintf("SELECT ID_harvest, h_date, h_time, pick_num, status FROM harvests WHERE ID_harvest = %s", GetSQLValueString($harvest, "int"));
$rsHarvest = mysqli_query($piercecty, $query_rsHarvest) or die(mysqli_error($piercecty));
$numrows = mysqli_num_rows($rsHarvest);
if($numrows<>1) { 	$sorry='noharvest'; header("Location: sorry-m.php?sorry=$sorry"); }
$row_rsHarvest = mysqli_fetch_assoc($rsHarvest);

$sw=''; $emailstr=''; $fname=''; $lname='';  $email=''; $IDpicker=0; // $sw is a switch to sort out whether need email address to distinguish duplicate names
$helper=''; $waiver=''; $linkaccess='';

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "rosternsertform")) { // if submit pressed

// capture form variables 

	if(isset($_POST['lname']))  $lname = trim(ucwords(strtolower($_POST['lname'])));
	if(isset($_POST['fname']))  $fname = trim(ucwords(strtolower($_POST['fname'])));
	if(isset($_POST['email']))  { $email = trim($_POST['email']); $emailstr=" AND email='$email'"; }
	if(isset($_POST['waiver1'])) $waiver= "yes";
	if(isset($_POST['helper']))  $helper= "yes"; 

if(!isset($_GET['access']))  { $sorry='badlink';  header("Location: sorry-m.php?sorry=$sorry"); exit(); } // no access link

$linkaccess=$_GET['access'];

if ($linkaccess=="public") {   // from public harvestlist
// is the harvest already closed?
if ($row_rsHarvest['status']<>"open") { $sorry="closed";   header("Location: sorry-m.php?sorry=$sorry"); exit(); }
} // end from public harvestlist

elseif($linkaccess<>'link' && $linkaccess<>'select') {  $sorry='badlink';  header("Location: sorry-m.php?sorry=$sorry"); exit();  } // incorrect access code
// legitimate signup - either public and open  OR private and correct access (either 'link' or 'select'

// harvest past?
$hdate=$row_rsHarvest['h_date'];
$htime=$row_rsHarvest['h_time'];
$hdatetime=$hdate." ".$htime;

if(date('Y-m-d H:i')>$hdatetime) { $sorry="past";   header("Location: sorry-m.php?sorry=$sorry"); exit(); }

// waiver checked?
if ($waiver!='yes') { $sorry='nowaiver'; header("Location: sorry-m.php?sorry=$sorry"); exit(); } 

// is the name in the database?

$query_rsName = "SELECT ID_picker, lname, fname, email FROM pickers  WHERE fname='$fname' AND lname='$lname'".$emailstr;

$rsName = mysqli_query($piercecty, $query_rsName) or die(mysqli_error($piercecty));
$row_rsName = mysqli_fetch_assoc($rsName);

$totalRows_rsName = mysqli_num_rows($rsName);
 
if( $totalRows_rsName==0) { $sorry='noname'; header("Location: sorry-m.php?sorry=$sorry"); exit(); }  

// is only one name found?
if($totalRows_rsName == 1) { 

$email = $row_rsName['email'];
$IDpicker=$row_rsName['ID_picker'];

// get roster status
$status='';
$rosterquery="select status from rosters where ID_harvest=$harvest and ID_picker=$IDpicker";
$rsStatus= mysqli_query($piercecty, $rosterquery) or die(mysqli_error($piercecty));
$row_rsStatus= mysqli_fetch_assoc($rsStatus);
$numrowsStatus = mysqli_num_rows($rsStatus);
if($numrowsStatus==1) $status=$row_rsStatus['status'];

//  Is the roster full (total signups minus cancels minus waiting)?
$query_count = "SELECT COUNT(ID_picker) FROM rosters WHERE ID_harvest = $harvest";
$counttot = mysqli_query($piercecty, $query_count) or die(mysqli_error($piercecty));
$row_count = mysqli_fetch_assoc($counttot);
$query_minus = "SELECT COUNT(ID_picker) FROM rosters WHERE ID_harvest = $harvest AND (status='cancel' OR status='waiting')";
$minustot = mysqli_query($piercecty, $query_minus) or die(mysqli_error($piercecty));
$row_minus = mysqli_fetch_assoc($minustot);
$signedup = $row_count['COUNT(ID_picker)'] - $row_minus['COUNT(ID_picker)'];

if ($signedup < $row_rsHarvest['pick_num'] || $linkaccess=='link') { // not full or access by direct link

// already on roster as...?
if($status=='') {  // not on roster

// insert into roster
$status = "signup"; if($helper=='yes') { $status = "intake"; } 
$IPaddress = $_SERVER["REMOTE_ADDR"]; 
$insertSQL = sprintf("INSERT INTO rosters (ID_harvest, lname, fname, regdate, ID_picker, status, IPaddress) VALUES (%s, %s, %s, now(), $IDpicker, '$status', '$IPaddress')",
                       GetSQLValueString($harvest, "int"),
                       GetSQLValueString(trim($lname), "text"),
                       GetSQLValueString(trim($fname), "text"));

$Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));

// pickeralert($IDpicker, $harvest); (add later if needed)

$IPquery="update pickers set IP_picker='$IPaddress' where ID_picker=$IDpicker"; // update the picker IP address from the roster signup IP address
$Result2 = mysqli_query($piercecty, $IPquery) or die(mysqli_error($piercecty));
$contactquery="update pickers set contactdate=now() where ID_picker=$IDpicker"; // update the picker's date of most recent contact
$Result3 = mysqli_query($piercecty, $contactquery) or die(mysqli_error($piercecty));

} // end of not on roster

if($status=='signup' || $status=='intake') {  // if on roster as signup or intake
// change status to signup or intake 
$status = "signup";  if($helper=='yes') { $status = "intake"; }
$queryUpdate="update rosters set status='$status', regdate=now() where ID_picker=$IDpicker and ID_harvest=$harvest";
$rsUpdate = mysqli_query($piercecty, $queryUpdate) or die(mysqli_error($piercecty));
} // end of signup or intake

if($status=='cancel' || $status=='waiting') { // if on roster as cancel or waiting
// change status to signup or intake 
$status = "signup";  if($helper=='yes') { $status = "intake"; }
$queryUpdate="update rosters set status='$status', regdate=now() where ID_picker=$IDpicker and ID_harvest=$harvest";

$rsUpdate = mysqli_query($piercecty, $queryUpdate) or die(mysqli_error($piercecty));
} // end of cancel or waiting


// done  already on roster as...?

$switch='normal';
$eID=encode($IDpicker);
$thanksgoto ="../hthank.php?pt=$eID&ht=$harvest"; 
emailer($IDpicker, $fname, $email, $harvest, $switch, $thanksgoto);
header("Location: $thanksgoto"); exit();

} // end of harvest not full or direct link access

else { // harvest is full and NOT direct link access

if($status=='' || $status=='cancel') {
$switch='waiting';
$waitinggoto = "../hwaiting.php?pt='$fname'&ht=$harvest"; 
if($email=='') { $sorry='noemail'; header("Location: sorry-m.php?sorry=$sorry"); exit(); } 
emailer($IDpicker, $fname, $email, $harvest, $switch, $waitinggoto);
header("Location: $waitinggoto"); exit();
} // end of status is 'not on' or cancel

elseif($status=='waiting') { // send email 
$switch='confirm';
$waitstatus="../waitstatus.php?ID=".encode($IDpicker);
emailer($IDpicker,$fname, $email, $harvest, $switch, $waitstatus);
header("Location: $waitstatus"); exit();

}  // end of status = waiting

else  { // all other statuses 
$switch='normal';
$eID=encode($IDpicker);
$thanksgoto = "../hthank.php?pt=$eID&ht=$harvest"; 
emailer($IDpicker, $fname, $email, $harvest, $switch, $thanksgoto);
header("Location: $thanksgoto"); exit();
} // end of all other statuses

} // end of harvest is full

} // end of only one name found

// email address already obtained then exit
if($totalRows_rsName>1 && $emailstr<>"") { // more than one picker (same name and email) so exit and explain about duplicates 
  	header('Location: dup-registrations.html');
	exit(); }

if($totalRows_rsName>1 && $sw=="") { // more than one name found and have not asked for email so set switch and continue with html
 $sw="need"; }

} // submit not pressed

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>signup</title>
<link href="piercecty-m.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
tr { 	background-color: #d2e2f7; }
th { 	background-color: #b2c2d7; }
-->
</style>
</head>

<body class="SH">
<div id="container">
<div id="mainContent">
 <p><strong>Harvest Pierce County's Gleaning Project Harvest Signup</strong><strong></strong></p>
    <p>Enter your first name and last name, read the Release and Waiver of Liability, check off the agreement box, and then press the signup button.</p>
<p>If you can also help the harvest leader for a half hour with jobs like signing in volunteers, weighing produce, or directing parking, please click on the checkbox before pressing the signup button.</p>
    <form action="<?php echo $editFormAction; ?>" id="rosternsertform" name="rosternsertform" method="POST">
      <table width="680" border="3" cellpadding="2" cellspacing="2" id="rosterlist">
        <tr align="center">
          <th>Date</th>
          <th>Time</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th><p>Harvest Number</p></th>
        </tr>
        <tr>
          <td align="center"><?php echo date('m/d/Y',strtotime($row_rsHarvest['h_date'])); ?></td>
          <td align="center"><?php echo date('g:i A',strtotime($row_rsHarvest['h_time'])); ?></td>
          <td><label>
            <input name="fname" type="text" id="fname" value="<?php echo $fname; ?>" maxlength="20" />
          </label></td>
          <td><label>
            <input name="lname" type="text" id="lname" value="<?php echo $lname; ?>" maxlength="15" />
          </label></td>
          <td align="center"><input name="hiddenField" type="hidden" id="hiddenField" value="<?php echo $harvest ?>" />
          <?php echo $harvest ?></td>
        </tr>
   		<?php if($sw=="need") { // if need email also then show input field ?>
        	<tr align="left">
        	  <td colspan="5">There is more than one volunteer with that name. Please add your registered email address and check the waiver box.</td></tr>
			<tr>
          	  <td align="right" colspan="2">email address:</td>
       		  <td colspan="3"><input name="email" type="text" id="email" size="40" maxlength="40" /></td>
		</tr> <?php  } // end of show email input field  ?>
 <!--       <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td colspan= "3"><input type="checkbox" name="helper" id="helper" />
            I can also help the harvest leader for a half hour, if needed.</td>
        </tr> -->
      </table>
      <p></p>
    <p>[You may also download the text of this document <a href="../documents/Release-Waiver-2013-04.pdf" target="_blank">here</a>.]</p>
<p><strong>RELEASE  AND WAIVER OF LIABILITY,&nbsp;ASSUMPTION OF RISK, AND INDEMNITY AGREEMENT</strong></p>
<p>  BY  ENTERING INTO THIS AGREEMENT, YOU ARE GIVING UP CERTAIN LEGAL RIGHTS, INCLUDING  THE RIGHT TO RECOVER DAMAGES IN CASE OF INJURY, DEATH, OR PROPERTY DAMAGE.</p>
<p>  <strong>READ  THIS AGREEMENT CAREFULLY BEFORE CHECKING OFF THE BOX BELOW.</strong>&nbsp;YOUR CHECKING THE BOX INDICATES  YOUR UNDERSTANDING AND AGREEMENT TO ITS TERMS.</p>
<p>  In  consideration of being given the opportunity to participate voluntarily in  harvest events organized by Harvest Pierce County's Gleaning Project,  I,  on behalf of myself, my legal and personal representatives, heirs, next of kin,  spouse, agents, and assigns, hereby:</p>
<p>  1.  Fully understand and acknowledge that:</p>
<p>  a.  The harvest  events in which I voluntarily choose to participate will involve entering onto  privately owned or publicly owned real property (the &ldquo;<strong>Premises</strong>&rdquo;) to harvest fruit and/or vegetables (the &ldquo;<strong>Activity</strong>&rdquo;). The Activity may involve the use of certain equipment,  including, without limitation, ladders and heavy/sharp harvesting tools. </p>
<p>  b.  There are risks and dangers associated with entering onto the Premises and  using equipment to harvest the fruit and/or vegetables.&nbsp; These risks and  dangers include, without limitation, injury from falling branches or fruit, dog  bites, allergic reaction to an insect bite, falling or tripping on uneven  surfaces or debris, falling from heights, contracting food-borne illnesses  arising from eating the fruit and/or vegetables being harvested, losing or  injuring a limb.</p>
<p>  c.  The use of equipment including, without limitation, ladders and harvesting  implements, is dangerous and could cause serious injury and in some cases  death.</p>
<p>  d.  The risks described in this section 1 (the &ldquo;<strong>Risks</strong>&rdquo;) may be caused by my own actions or  inactions, the actions or inactions of others participating in the Activity,  the condition of the Premises where the Activity takes place, the conditions in  which the Activity takes place, the condition of the equipment that I am using,  or the negligence of the Released Parties named below.</p>
<p>  2.  Voluntarily assume and accept all the Risks and all responsibility for any  losses, liability, costs, damages, claims, demands, or costs that I may incur  as a result of or related to my participation in the Activity.</p>
<p>  3.  Voluntarily release, discharge, and covenant not to sue i) Harvest Pierce County's Gleaning Project, the Food  Bank Coalition of San Luis Obispo County, or any of their employees,  independent contractors, agents, owners, officers, directors, shareholders, and  subsidiaries; and ii) the owner/operator of the Premises or any of its  employees, independent contractors, agents, owners, officers, directors,  shareholders, and subsidiaries (hereinafter the &quot;<strong>Released  Parties</strong>&quot;) for  any losses, liabilities, damages, claims, demands, expenses, or costs that I  may incur and which arise out of or are related to my participation in the  Activity, the condition of the Premises, or any act, omission, or negligence of  the Released Parties.&nbsp; I further agree that if, despite this Agreement, I  or anyone on my behalf, makes a claim against any of the Released Parties, I  will indemnify, save, and hold harmless each of the Released Parties from  losses, liability, damages, claims, demands, expenses, or costs that any of the  Released Parties may incur as a result of any such claim.</p>
<p>  4.  Voluntarily agree to indemnify, save, and hold harmless each of the Released  Parties from losses, liability, damages, claims, demands, expenses, or costs  that any of the Released Parties may incur as a result of my participation in  the Activity.</p>
<p>  5.  Voluntarily agree to abide by any rules established or instructions given with  respect to my participation in the Activity. I further agree that while  participating in the Activity I will refrain from conducting the Activity in an  unlawful manner.</p>
<p>  6.  Voluntarily agree to:</p>
<p>  a.  Enter only those areas designated for use by volunteers;</p>
<p>  b.  Use care to avoid damaging the Premises;</p>
<p>  c.  Refrain from climbing trees or fences to retrieve produce;</p>
<p> d.  Refrain from sharing the name, address, or other private information of the  owner of the Premises;</p>
<p>  e.  Pick only fruit that appears mature and ready to be picked, leaving less mature  fruit on the tree;</p>
<p>  f.  Use great care and caution when using a ladder;&nbsp;</p>
<p>  g.  Ensure that any minor(s) (individuals who are under the age of 18 years) for  whom I am responsible will be supervised at all times by me; and</p>
<p>  h.  Ensure that any minor for whom I am responsible will not be permitted to climb  on a ladder unless the minor is at least 14 years of age and is directly  supervised at all times by me.</p>
<p>  7.  Voluntarily consent to the taking and use of my picture / image.&nbsp;Harvest Pierce County's Gleaning Project  may use my voice and likeness, or those of any minors for whom I am  responsible, in any manner or form, for any lawful purpose, at any time.&nbsp;  I waive any right that I may have to inspect or approve the finished product.</p>
<p>  8.  Voluntarily agree that this Agreement is governed by the laws of the State of  California, and is intended to be as broad and inclusive as is permitted by  California law.&nbsp; In the event any portion of this Agreement is determined  to be invalid, illegal, or unenforceable, the validity, legality, and  enforceability of the balance of the Agreement shall not be affected or  impaired in any way and shall continue in full force and effect.</p>
<p>  9.  Voluntarily agree that any dispute or claim that arises out of or that relates  to i) my participation in the Activity; or ii) this Agreement, or to the  interpretation or breach thereof, or to the existence, scope, or validity of  this Agreement, shall first be submitted to mediation before a mediator doing  business in San Luis Obispo County, to be selected by Harvest Pierce County's Gleaning Project.  The mediator fees to be equally shared by all  parties to the dispute.  If not resolved  through mediation, the dispute shall be submitted to binding arbitration before  an arbitrator doing business in San Luis Obispo County, to be selected by  Harvest Pierce County's Gleaning Project.  The arbitrator fees to be  equally shared by all parties to the dispute.    Any award rendered pursuant to such arbitration is binding and may be  entered as a judgement in any court having jurisdiction thereof.</p>
<p>  10.  Voluntarily agree that this Agreement will remain in effect for all Harvest Pierce County's Gleaning Project  activities in which I participate until either revoked by a writing executed by  Harvest Pierce County's Gleaning Project and me or replaced by a new Agreement executed by Harvest Pierce County's Gleaning Project and me.</p>
<p>  11.  Voluntarily agree that Harvest Pierce County's Gleaning Project may, at any time, with or without cause, revoke  my right to volunteer for or participate in Harvest Pierce County's Gleaning Project events and activities.</p>
<p>  <strong>PARENT /  GUARDIAN WAIVER</strong>&nbsp;-  Any person entering into this Agreement and who is responsible for the  supervision of any minors participating in the Activity must read and agree to  the following:</p>
<p>  I,  acting as parent, natural guardian, or a person authorized by the parent or  natural guardian, have read the foregoing Agreement, understand and consent to  its terms on behalf of myself and on behalf of the minors for whom I am  responsible, and agree to indemnify and save and hold harmless the Released  Parties from any loss, liability, damage, or cost that they may incur because  of any defect in or lack of capacity to act on behalf of the minors in executing  this Agreement.</p>
<p>  <strong>I  HAVE READ THIS AGREEMENT, FULLY UNDERSTAND ITS TERMS, UNDERSTAND THAT I HAVE  GIVEN UP SUBSTANTIAL RIGHTS BY AGREEING TO IT, HAVE AGREED TO IT FREELY AND  WITHOUT ANY INDUCEMENT OR ASSURANCE OF ANY NATURE, AND INTEND IT TO BE A COMPLETE  AND UNCONDITIONAL RELEASE AND WAIVER OF ALL LIABILITY TO THE GREATEST EXTENT  ALLOWED BY LAW.</strong></p>
      <p>
        <input type="checkbox" name="waiver1" id="waiver1" /> I agree to the terms of the Release and Waiver of Liability, Assumption of risk, and Indemnity Agreement.      		      </p>
      <p>
  <label>
     <input type="submit" name="submit" id="submit" style="height:60px; font-weight:bold; font-size:1.1em" value=" Sign up for this harvest"/>
  </label>
 		<input name="harvesttemp" type="hidden" value="<?php echo $harvest ?>" />
        <input type="hidden" name="MM_insert" value="rosternsertform" />
      </p>
    </form>
    <p>
      <!-- end #mainContent -->
</p></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
  <div id="footer">
<?php require_once('../includes/footer.inc.php'); ?>
  <!-- end #footer --></div>
<!-- end #container --></div>
</body>
</html>
<?php

function emailer($IDpicker, $fname, $email, $harvest, $switch, $goto) { 
$goto = "http://www.piercecountygleaningproject.org/".$goto; // adds on domain to relative link passed into the function
if($switch=='normal') {
	$eIDpicker=encode($IDpicker);
	$subject = "Pierce County Gleaning Project roster status";
	$cancelgoto="http://www.piercecountygleaningproject.org/cancel.php?ID=".$eIDpicker."&h=".encode($harvest);
	$historygoto="http://www.piercecountygleaningproject.org/PickersInfo.html";
	$message = 'Hello '.$fname.','."\n\n".'You have signed up for a harvest sponsored by Pierce County Gleaning Project. Go to this web page for details: '."\n".$goto.'.';
	$message.="\n\n\n".'If you find that you cannot attend and want to cancel this sign up, it may allow someone else to take your place. Go to this page to cancel your sign up: '."\n".$cancelgoto;
	$message.="\n\n".'You can check your attendance history, and verify your signup for this harvest, any time at this web page: '."\n".$historygoto; 
    if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

	// echo $email."<br />". $headers."<br />".$message;
} // end of switch normal

if($switch=='waiting') {
		$eIDpicker=encode($IDpicker);
	$subject = "Pierce County Gleaning Project roster status";
	$confirmgoto="http://www.piercecountygleaningproject.org/confirm.php?ID=".$eIDpicker."&h=".$harvest;
	$message = 'Hello '.$fname.','."\n\n".'You have asked to be added to the waiting list of a harvest sponsored by Pierce County Gleaning Project. ';
	$message.= 'To get on the roster, you must confirm this request by going to the website. Just click on the link below to be ';
	$message.= 'added to the waiting list:'."\n\n";
	$message.= $confirmgoto;
    if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

	// echo $email."<br />". $headers."<br />".$message;
} // end of switch waiting

if($switch=='confirm') {
		$eIDpicker=encode($IDpicker, $harvest);
$subject = "Pierce County Gleaning Project roster status";
$message = 'Hello '.$fname.','."\n\n".'You have already been added to the waiting list of a harvest sponsored by Pierce County Gleaning Project. ';
$message .= 'You will be sent an email if there are enough cancellations so that you are moved up to the actual roster. ';
$message .= 'That email will have the address and directions for the harvest. Because you are now on the waiting list, ';
$message .= 'you do not need to check the Harvests page. You should:'."\n\n";
$message .= '- Check your email before the harvest to see if you have been added to the actual roster'."\n\n";
$message .= '- Check the following web page to see your position on the waiting list:'."\n\n";
$message .= $goto;
	
if($email<>'') 	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

// echo $email."<br />". $headers."<br />".$message;

} // end of switch==confirm
} // end of emailer function

?>
