<?php 

header("Location: pickerinsert.php"); exit();
// require_once('Connections/piercecty.php'); 

require_once('includes/sqlcleaner.public.php');

require_once('includes/htmlmailer-phpmailer.inc.php');



$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {

  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);

}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "pickerinsertform")) { // if the form has been submitted

// if(!isset($_POST['waiver1']))  { header("Location: pickerinsert-error.php?error=waiver"); exit(); }



$fname=GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text");

$lname=GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))), "text");

$email=GetSQLValueString($_POST['email'], "text");



if($fname=='Reaseqfuol' or $fname=='Agertom' or $fname=='barbaraker' or $fname=='renoplerss'  or $fname=='gertiolk' or strpos($fname,'@')) { exit; } // persistent bots



	// check for dup fname, lname AND email. Update instead of insert if found

	$dupquery="select ID_picker, fname, lname, email from pickers where fname=$fname and lname=$lname and email=$email";

	$dupe = mysqli_query($piercecty, $dupquery);

	if(mysqli_num_rows($dupe)<>0) { // is a duplicate so update instead of insert

	$duprow=mysqli_fetch_assoc($dupe);

	$ID=$duprow['ID_picker'];

	  $updateSQL = sprintf("UPDATE pickers SET lname=%s, fname=%s, phone=%s, phone2=%s, email=%s, address=%s, city=%s, state=%s, zip=%s, assistance=%s, harvester=%s, leader=%s, scout=%s, volpass=%, other_info=%s, weekemail=%s, weekphone=%s, special=%s, how_hear=%s, emerg=%s, ephone=%s WHERE ID_picker=$ID",

                       GetSQLValueString(trim(ucwords(strtolower($_POST['lname']))), "text"),

                       GetSQLValueString(trim(ucwords(strtolower($_POST['fname']))), "text"),

                       GetSQLValueString($_POST['phone'], "text"),

					   GetSQLValueString($_POST['phone2'], "text"),					

                       GetSQLValueString($_POST['email'], "text"),

                       GetSQLValueString($_POST['adddress'], "text"),

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



  		$Update1 = mysqli_query($piercecty, $updateSQL);

	  	header("Location: thankyou-update.php"); exit();

	

	} // end of update instead of insert

	

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

<!DOCTYPE HTML>

<!--

	Spectral by Pixelarity

	pixelarity.com | hello@pixelarity.com

	License: pixelarity.com/license

-->

<html>

	<head>

		<title>Register to Volunteer</title

		<meta charset="utf-8" />

		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

		<link rel="stylesheet" href="assets/css/main.css" />

		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>

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





<body class="is-preload">

<div id="page-wrapper">

<!-- Header -->

					<header id="header">

						<h1><a href="index.php">Pierce County Gleaning Project</a></h1>

						<nav id="nav">

							<ul>

								<li class="special">

									<a href="#menu" class="menuToggle"><span>Menu</span></a>

									<div id="menu">

										<ul>

											<li><a href="index.php">Home</a></li>

											<li><a href="register_form.php">Register to Volunteer</a></li>

											<li><a href="donate_crop.php">Donate your Crop</a></li>

											<li><a href="harvestlist.php">Upcoming Harvests</a></li>

											<li><a href="Utilities/PagesIndex.php">Log In</a></li>

											

										</ul>

									</div>

								</li>

							</ul>

						</nav>

					</header>

<article id="main">

<header>

	<h2>Register to Volunteer</h2>

</header>

<section class="wrapper style5">

	<div class="inner">

    <p></p>

    <div class="inner">

      <form action="<?php echo $editFormAction; ?>" id="pickerinsertform" name="pickerinsertform" method="POST" onsubmit="return validateForm()">

        <p>Please fill out this form completely, then click "Save".  Please note that this page will not sign you up to participate in a specific  harvest; remember to visit the "Upcoming Harvests" page to sign up for a particular  harvest. </p>

        <p><strong><em>If you have already registered before, you do not have to register again</em></strong>. If you want to <strong><em>update</em></strong> your contact information, go to the <a href="volunteer.php">Volunteer</a> page and click on "Update". If you need to request a name change, send an email to our database moderator at <a href="mailto:piercecty@gleanweb.org">piercecty@gleanweb.org</a>.</p>

        <p>Privacy: Information entered here is used solely by  Harvest Pierce County's Gleaning Project. We do not share, sell or otherwise distribute  your personal information. </p>

        <p>You must be 18 or older to register as a volunteer, and each adult must register separately.</p>

		<p><label>First name <input name="fname" type="text" id="fname" size="15" maxlength="15" /></label>

          <label>Last name <input name="lname" type="text" id="lname" size="20" maxlength="20" /></label></p>

        <p>

           Phone 

             <input name="phone" type="text" id="phone" size="15" maxlength="15" value="(000) 000-0000" style="color:#666;" onFocus="if(this.value == '(000) 000-0000') {this.value = '';}" onBlur="if (this.value == '') {this.value = '(000) 000-0000';}"/>

           Alternate Phone

           <input name="phone2" type="text" id="phone2" size="15" maxlength="15"  value="(000) 000-0000"  style="color:#666; " onFocus="if(this.value == '(000) 000-0000') {this.value = '';}" onBlur="if (this.value == '') {this.value = '(000) 000-0000';}"/></p>

        <p><label>Email <input name="email" type="text" id="email" size="30" maxlength="40" /></label></p>

        <p>

          <label>Address

            <input name="address" type="text" id="address" size="30" maxlength="50" />

          </label>

        </p>

        <p>

          <label>City

            <input name="city" type="text" id="city" size="15" maxlength="50"/>

          </label>

          <label>State

            <input name="state" type="text" id="state" size="4" maxlength="2"  />

          </label>

        <p><label>Zip code  <input name="zip" type="text" id="zip" size="5" maxlength="5" /></label></p>

        



<p>Do you have any physical limitations or require special accommodations? Please describe:

<textarea name="special" rows="2" cols="80"> </textarea></p>

<p>How did you hear about Harvest Pierce County's Gleaning Project?

          <select name="how_hear" id="how_hear">

            <option value=" " selected="selected">[select]</option>

            <option value="Neighbor">Neighbor</option>

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

<p>Do you or anyone you know experience food insecurity?

          <select name="assistance" id="assistance">

            <option value=" " selected="selected">[select]</option>

            <option value="Yes">Yes</option>

            <option value="No">No</option>

            <option value="Unknown">I don't know</option>

            

          </select>

</p>

<p>Emergency contact (first and last name)

  <input name="emerg" type="text" size="40" maxlength="40" /></label></p>

<p>Emergency contact phone number <input name="ephone" type="text" size="20" maxlength="20"  value="(000) 000-0000"  style="color:#666; " onFocus="if(this.value == '(000) 000-0000') {this.value = '';}" onBlur="if (this.value == '') {this.value = '(000) 000-0000';}"/></p>

<p>You may enter an optional password (up to 15 letters and numbers only) that you can use to check your signups, history and waiting list status anytime instantly on a web page rather than waiting for an email. <br /><br />

		  <label>Password (optional): <input name="volpass" type="password" id="code" size="15" maxlength="15" /></label>

</p>

<p>SPAM prevention - do the math and type in the number:

		<img src="includes/captcha.inc.php" />	<input name="captcha" type="text"></p>

      <p>



<!-- TERMS OF AGREEMENT NEED UPDATE TO WAIVER-->

<!--<p><input type="checkbox" name="waiver1" id="waiver1" />

   I agree to the <a href="Pickers/ParticipationTerms.php" target="_blank">Terms of Participation</a>.</p>-->



<p><label><input type="submit" name="submit" id="submit" value="Save" /></label>

        After registering as a volunteer, please visit our <a href="harvestlist.php">harvests</a> page to  see what harvests are scheduled and to sign up for one.</p>

        <input type="hidden" name="MM_insert" value="pickerinsertform" />

      </form>

<p>&nbsp;</p>

</div>

 <!-- end #mainContent --></div>

</section>

</article>

<footer id="footer">

						<ul class="icons">

							<li><a href="https://www.facebook.com/harvestpiercecounty/" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>

							<li><a href="https://www.instagram.com/piercecountyharvest/" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>

						</ul>

						<ul class="copyright">

							<li>&copy; Copyright 2020 Harvest Pierce County's Gleaning Project</li>

						</ul>

					</footer>

</div>

<!-- Scripts -->

			<script src="assets/js/jquery.min.js"></script>

			<script src="assets/js/jquery.scrollex.min.js"></script>

			<script src="assets/js/jquery.scrolly.min.js"></script>

			<script src="assets/js/browser.min.js"></script>

			<script src="assets/js/breakpoints.min.js"></script>

			<script src="assets/js/util.js"></script>

			<script src="assets/js/main.js"></script> 

</body>

</html>