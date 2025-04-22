<?php 
require_once('Connections/piercecty.php'); 
require_once('includes/sqlcleaner.public.php');
require_once('includes/htmlmailer-phpmailer.inc.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$error='';
if((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "pickerinsertform") && ($_SESSION["code"]<>$_POST["captcha"])) {
	$error="spam"; }
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "pickerinsertform") && ($_SESSION["code"]==$_POST["captcha"])) { // if the form has been submitted
// if(!isset($_POST['waiver1']))  { header("Location: pickerinsert-error.php?error=waiver"); exit(); }

$fname=GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text");
$lname=GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))), "text");
$email=GetSQLValueString($_POST['email'], "text");

if($fname=='Reaseqfuol' or $fname=='Agertom' or $fname=='barbaraker' or $fname=='renoplerss'  or $fname=='gertiolk' or strpos($fname,'@')) { exit; } // persistent bots
	
	$query="select fname, lname, ID_picker from pickers where fname=$fname and lname=$lname";
	$possdupe = mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
	if(mysqli_num_rows($possdupe)==0) { $demog5=''; } 
		else { $demog5=" Possible duplicate registration: http://www.piercecountygleaningproject.org/Utilities/duplicates.php"; }
	
	$b = time();
	$subject = "New volunteer registered ".date("m/d/y",$b)." ".date("G",$b).":".date("i",$b);
		$demog1 = $_POST['fname']; $demog2 = $_POST['lname']; $demog3 = $_POST['email'];
	$demog4 = "http://www.piercecountygleaningproject.org/Utilities/pickerfind.php?nametemp=".$demog2;
	$message = "new volunteer registered: ".$demog1." ".$demog2.", ".$demog3.", ".$demog4.", ".$demog5;
	$email =  "info@piercecountygleaningproject.org";
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");

	if ($_POST['email']<>'') { $email = $_POST['email'];
	$fname = $_POST['fname'];
	$subject = 'Welcome to Pierce County Gleaning Project';
	$message = '
	<html>
	<head><title>Welcome to Pierce County Gleaning Project</title></head>
	<body>
	<p>Hi '.$fname.',</p>
	<p>Thank you for registering as a volunteer with Pierce County Gleaning Project. 
	You will find all that you need to know about how to sign up for harvests and how they work on 
	our web pages. Start at the home page, <a href="http://www.piercecountygleaningproject.org">Pierce County Gleaning Project</a>,
	and click on the Volunteer button. On that page you will find information 
	and other resources.</p></ br>
	<p>As soon as you have time, please read the information for new volunteers that 
	you will find <a href="http://www.piercecountygleaningproject.org/Pickers/NewPickers.php">here</a>. 
	If you have any questions that are not covered by those pages, please go the 
	<a href="http://www.piercecountygleaningproject.org/contact.php">Contacts Page</a>.</p>
	<p>You can join our mailing list if you would like to receive our newsletters, updates or information about larger harvests. If you have already been receiving our emails, there is no need to sign up again. Sign up for the mailing list at the <a href="http://www.piercecountygleaningproject.org/volunteer.php">Volunteer</a> page.</p>
	</ br>
	<p>We look forward to meeting you at future harvests!</p>
	</body></html>';
smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
 } //end of if email<>''

$IP_picker = $_SERVER["REMOTE_ADDR"];
$regdate = date("Y-m-d H:i:s");

  $insertSQL = sprintf("INSERT INTO pickers (lname, fname, phone, phone2, email, address, city, state, zip, assistance, harvester, leader, scout, volpass, other_info, weekemail, weekphone, special, how_hear, emerg, ephone, regdate, contactdate, IP_picker) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$regdate', '$regdate', '$IP_picker')",
                       GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))), "text"),
                       GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text"),
                       GetSQLValueString($_POST['phone'], "text"),
							  GetSQLValueString($_POST['phone2'], "text"),					   
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "int"),
                       GetSQLValueString($_POST['assistance'], "text"),
                       GetSQLValueString($_POST['harvester'], "text"),
                       GetSQLValueString($_POST['leader'], "text"),
                       GetSQLValueString($_POST['scout'], "text"),
                       GetSQLValueString($_POST['volpass'], "text"),
                       GetSQLValueString($_POST['other_info'], "text"),
                       GetSQLValueString($_POST['weekemail'], "text"),
                       GetSQLValueString($_POST['weekphone'], "text"),
                       GetSQLValueString($_POST['special'], "text"),
                       GetSQLValueString($_POST['how_hear'], "text"),
                       GetSQLValueString($_POST['emerg'], "text"),
                       GetSQLValueString($_POST['ephone'], "text"));

$Result1 = mysqli_query($piercecty, $insertSQL);
  
$insertGoTo = "thankyou.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo)); exit();
} // end of if form isset

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>register volunteer</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.tooltip {
    position: relative;
    display: inline-block;
}
.tooltip .tooltiptext {
    visibility: hidden;
    width: 400px;
    background-color: white;
    color: #000;
    text-align: left;
	border: 2px solid green;
    border-radius: 6px;
    padding: 5px ;

    /* Position the tooltip */
   position: absolute;
    z-index: 1;
}
.tooltip:hover .tooltiptext {visibility: visible;}
-->
</style>
<script type="application/javascript">
function validateForm() {
   var x = document.forms["pickerinsertform"]["fname"].value;
    if (x=="" || x==null) { alert("First name is required"); return false; }
    var x = document.forms["pickerinsertform"]["lname"].value;
    if (x=="" || x==null) { alert("Last name is required"); return false; }
    var x = document.forms["pickerinsertform"]["emerg"].value;
    if (x=="" || x==null ) { alert("An emergency contact name is required"); return false; }
	var x = document.forms["pickerinsertform"]["ephone"].value;
    if (x.length<7 || x=="(000) 000-0000") { alert("An emergency contact phone number is required"); return false; } 
if(document.forms["pickerinsertform"].waiver1.checked==false) { alert("Please read the Terms of Participation and indicate your agreement with its terms"); return false; }
}
</script>

</head>
<body class="SH">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
    <p></p>
    <div class="indentdiv">
      <form action="<?php echo $editFormAction; ?>" id="pickerinsertform" name="pickerinsertform" method="POST" onsubmit="return validateForm()">
        <h3>Volunteer Registration Form</h3>
	 <?php if($error=='spam') echo "<h2>At the bottom of the form, please type the sum of the numbers in the space provided, review what you have entered into the form, and click Save again.</h2>";  ?>
        <p>Please fill out this form completely, then click Save.  Please note that this page will not sign you up to participate in a specific  harvest; remember to visit the Harvests  page to sign up for a particular  harvest. </p>
        <p><strong><em>If you have already registered before, you do not have to register again</em></strong>. If you want to <strong><em>update</em></strong> your contact information, go to the <a href="volunteer.php">Volunteer</a> page and click on 'Update.' If you have a name change, send an email to Dick at <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>
        <p>Privacy: Information entered here is used solely by  Harvest Pierce County's Gleaning Project. We do not share, sell or otherwise distribute  your personal information. </p>
        <p>You must be 18 or older to register as a volunteer, and each adult must register separately.</p>
		<p><label>First name <input name="fname" type="text" id="fname" size="15" maxlength="15" value="<?php if(isset($_POST['fname'])) echo $_POST['fname'];?>"/></label>
          <label>&nbsp;&nbsp;&nbsp;&nbsp;Last name <input name="lname" type="text" id="lname" size="20" maxlength="20" value="<?php if(isset($_POST['lname'])) echo $_POST['lname'];?>"/></label></p>
        <p>
           Phone 
             <input name="phone" type="text" id="phone" size="15" maxlength="15" value="<?php if(isset($_POST['phone'])) echo $_POST['phone'];?>" />
           Alternate Phone
           <input name="phone2" type="text" id="phone2" size="15" maxlength="15"  value="<?php if(isset($_POST['phone2'])) echo $_POST['phone2'];?>" /></p>
        <p><label>Email <input name="email" type="text" id="email" size="30" maxlength="40" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>"/></label></p>
        <p>
          <label>Address
            <input name="address" type="text" id="address" size="30" maxlength="50" value="<?php if(isset($_POST['address'])) echo $_POST['address'];?>"/>
          </label>
        </p>
        <p>
          <label>City
            <input name="city" type="text" id="city" size="15" maxlength="50" value="<?php if(isset($_POST['city'])) echo $_POST['city'];?>"/>
          </label>
          <label>State
            <input name="state" type="text" id="state" size="4" maxlength="2"  value="<?php if(isset($_POST['state'])) echo $_POST['state'];?>"/>
          </label>
        <p><label>Zip code  <input name="zip" type="text" id="zip" size="5" maxlength="5" value="<?php if(isset($_POST['zip'])) echo $_POST['zip'];?>"/></label></p>
        <p>Funding sources ask us to track the following information:</p>
        <blockquote>Are you receiving food assistance? 
   <input name="assistance" type="radio" value="Yes" /> Yes
   <input name="assistance" type="radio" value="No" checked="checked" /> No</blockquote>

<p>How do you want to help? (Check as many as you want)</p>
        <blockquote>
        <p class="tooltip">Harvester 
          <input type="radio" name="harvester" value="Yes" checked="checked" /> Yes
          <input type="radio" name="harvester" value="No"  /> No <span class="tooltiptext">Harvesters are volunteers who participate in gleaning events by helping to pick, sort, and weigh produce that is harvested. You do not need to attend a training to become a harvester because a Branch Leader will train you during the gleaning event.  </span></p><br />
        <p class="tooltip">Branch Leader
          <input type="radio" name="leader" value="Yes" /> Yes 
        <input type="radio" name="leader" value="No" checked="checked" /> No  <span class="tooltiptext">This person will work closely with the Harvest Pierce County team. They are responsible for leading urban fruit harvests and scouting fruit trees to assure the fruit is ripe for the picking. In addition, Branch Leaders are responsible for assuring the produce gets dropped off to local food banks and recording the harvest data. </span></p>
        <!-- <p>Tree Scout – assess fruit tree  health and productivity
          <input type="radio" name="scout" value="Yes" /> Yes
           <input type="radio" name="scout" value="No" checked="checked" /> No</p>-->
			  <input type="hidden" name="scout" value="No">
         <p>Other <input name="other_info" type="text"  size="50" maxlength="50" value="<?php if(isset($_POST['other_info'])) echo $_POST['other_info'];?>"/></p>
        </blockquote>
<p>How would you like to be notified about gleans during harvest season?</p>
        <blockquote>
        <p>Weekly email of upcoming harvests
         <input type="radio" name="weekemail" value="Yes" /> Yes
         <input type="radio" name="weekemail" value="No"  checked="checked" /> No</p>
        <p>Weekly phone call listing upcoming harvests 
        <input type="radio" name="weekphone" value="Yes" i/> Yes 
        <input type="radio" name="weekphone" value="" checked="checked" /> No</label><br /></p>
        </blockquote>
<p>Do you have any physical limitations or require special accommodations? Please describe:
<textarea name="special" rows="2" cols="80" value="<?php if(isset($_POST['special'])) echo $_POST['special'];?>"> </textarea></p>
<p>How did you hear about Harvest Pierce County's Gleaning Project?
          <select name="how_hear" id="how_hear">
            <option value=" " selected="selected">[select]</option>
            <option value="Neighbor">Neighbor</option>
            <option value="Food Is Free Tacoma">Food Is Free Tacoma</option>
            <option value="Harvest Pierce County's Gleaning Project web site">Harvest Pierce County's Gleaning Project web site</option>
            <option value="Food Bank">Food Bank</option>
            <option value="Harvest Pierce County's Gleaning Project web site volunteer">Harvest Pierce County's Gleaning Project volunteer</option>
            <option value="Newspaper">Newspaper</option>
            <option value="Facebook">Facebook</option>
            <option value="Flyer">Flyer</option>
            <option value="Other harvesting group">Other harvesting group</option>
            <option value="Friend">Friend</option>
            <option value="Other">Other</option>
            <option value="Web search">Web search</option>
          </select>
</p>
<p>Emergency contact (first and last name)
  <input name="emerg" type="text" size="40" maxlength="40" value="<?php if(isset($_POST['emerg'])) echo $_POST['emerg'];?>"/></label></p>
<p>Emergency contact phone number <input name="ephone" type="text" size="20" maxlength="20"  value="<?php if(isset($_POST['ephone'])) echo $_POST['ephone'];?>"/></p>
<p>You may enter an optional password (up to 15 letters and numbers only) that you can use to check your signups, history and waiting list status anytime instantly on a web page rather than waiting for an email. <br /><br />
		  <label>Password (optional): <input name="volpass" type="password" id="code" size="15" maxlength="15" value="<?php if(isset($_POST['volpass'])) echo $_POST['volpass'];?>"/></label>
</p>
<p><input type="checkbox" name="waiver1" id="waiver1" />
   I agree to the <a href="Pickers/ParticipationTerms.php" target="_blank">Terms of Participation</a>.</p>
	
<p>SPAM prevention - do the math and type in the number:
<img src="includes/captcha.inc.php" />	<input name="captcha" type="text"></p>

<p><label><input type="submit" name="submit" id="submit" value="Save" /></label>
        After registering as a volunteer, please visit our <a href="harvestlist.php">harvests</a> page to  see what harvests are scheduled and to sign up for one.</p>
        <input type="hidden" name="MM_insert" value="pickerinsertform" />
      </form>
<p>&nbsp;</p>
</div>
 <!-- end #mainContent --></div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
