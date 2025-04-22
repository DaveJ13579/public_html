<?php 
require_once('Connections/piercecty.php');
require_once('includes/sqlcleaner.public.php'); 
require_once('includes/smtpmailer-phpmailer.inc.php');
require_once('includes/branch.inc.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$error='';
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "site_reg") && ($_SESSION["code"]<>$_POST["captcha"])) {
	$error="spam"; }
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "site_reg") && ($_SESSION["code"]==$_POST["captcha"])) {

$maddress= $_POST['maddress']==''  ? GetSQLValueString($_POST['address'], "text") :  GetSQLValueString($_POST['maddress'], "text") ;
$mcity= $_POST['mcity']==''  ? GetSQLValueString($_POST['city'], "text") :  GetSQLValueString($_POST['mcity'], "text") ;
$mstate= $_POST['mstate']==''  ? GetSQLValueString($_POST['state'], "text") :  GetSQLValueString($_POST['mstate'], "text") ;
$mzip= $_POST['mzip']==''  ? GetSQLValueString($_POST['zip'], "text") :  GetSQLValueString($_POST['mzip'], "text") ;

$branch=zipbranch(GetSQLValueString($_POST['zip'], "text"));
$branch=GetSQLValueString($branch, "text");
// echo '<br />branch:'.$branch; exit;

$insertSQL = sprintf("INSERT INTO sites (farm, crops, contact1, phone1, email1, phone2, maddress, mcity, mstate, mzip, branch, address, city, state, zip, venue, size, height, disease, disease_text, present, howhear, property_rel, landlord, location, spray, spray_text, otherinfo, regdate) VALUES (%s, %s, %s, %s, %s, %s, $maddress, $mcity, $mstate, $mzip, $branch, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, current_date())",
                       GetSQLValueString(trim($_POST['farm']), "text"),
                       GetSQLValueString($_POST['crops'], "text"),
                       GetSQLValueString(trim(ucwords(strtolower($_POST['contact1']))), "text"),
                       GetSQLValueString($_POST['phone1'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['phone2'], "text"),
                       GetSQLValueString(trim($_POST['address']), "text"),
                       GetSQLValueString(trim($_POST['city']), "text"),
                       GetSQLValueString(trim($_POST['state']), "text"),
                       GetSQLValueString(trim($_POST['zip']), "text"),
                       GetSQLValueString(trim($_POST['venue']), "text"),
                       GetSQLValueString(trim($_POST['size']), "text"),
                       GetSQLValueString(trim($_POST['height']), "text"),
                       GetSQLValueString(trim($_POST['disease']), "text"),
                       GetSQLValueString(trim($_POST['disease_text']), "text"),
                       GetSQLValueString($_POST['present'], "text"),
                       GetSQLValueString($_POST['howhear'], "text"),
                       GetSQLValueString(trim($_POST['property_rel']), "text"),
                       GetSQLValueString(trim($_POST['landlord']), "text"),
                       GetSQLValueString(trim($_POST['location']), "text"),
                       GetSQLValueString(trim($_POST['spray']), "text"),
                       GetSQLValueString(trim($_POST['spray_text']), "text"),
                       GetSQLValueString($_POST['otherinfo'], "text"));
// echo $insertSQL.' '; exit;
  $Result1 = mysqli_query($piercecty, $insertSQL) or die(mysqli_error($piercecty));
 
  // check for duplicates
  $farm=GetSQLValueString(str_replace("'", "", trim($_POST['farm'])), "text");
  $dupquery="select farm from sites where farm=$farm";
  $rsDup= mysqli_query($piercecty, $dupquery);
  $dupflag='';
  if(mysqli_num_rows($rsDup)>1)  $dupflag='(Possible duplicate crop. Check site list and if it is a duplicate, update the contact information in the previous registration and then delete this site.)';
  
  // find branch leader
  $ldremail='';
  $leaderq="select email from branches, pickers where branches.ID_leader=pickers.ID_picker and branch=$branch";
  $rsLeader=mysqli_query($piercecty,$leaderq);
  if(mysqli_num_rows($rsLeader)>0) {
  		$ldrrow=mysqli_fetch_assoc($rsLeader);
		$ldremail=$ldrrow['email']; }  
  $query="select MAX(ID_site) as ID_site from sites";
  $Result2= mysqli_query($piercecty, $query) or die(mysqli_error($piercecty));
  $row = mysqli_fetch_assoc($Result2);
  $ID = $row['ID_site'];
	$crops = $_POST['crops'];
	$subject = "A new site has been registered - ".$farm." ".$crops." ".$dupflag;
	$message = "See registration details at http://www.piercecountygleaningproject.org/Utilities/sitedetail.php?sitetemp=".$ID;
	$email =  "info@piercecountygleaningproject.org";
	smtpmail($email, $subject, $message, "info@piercecountygleaningproject.org");
	if($ldremail) 	smtpmail($ldremail, $subject, $message, "info@piercecountygleaningproject.org");

  $insertGoTo = "thankyou-site.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
		<title>Donate Your Crop</title
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script type="application/javascript">
		
function setFocus() { document.getElementById('farm').focus();}
function isInArray(array, search) { return array.indexOf(search) >= 0; }

function validateForm() {
 var x = document.forms["site_reg"]["contact1"].value;
    if (x=="" || x==null) { alert("Please enter the name of whom we can contact."); return false; }
   var x = document.forms["site_reg"]["phone1"].value;
    if (x=="" || x==null) { alert("Please enter a telephone number."); return false; } 
<?php

$zipq="select zips from branches";
$rsZips=mysqli_query($piercecty,$zipq);
$allzips='';
while($ziprow=mysqli_fetch_assoc($rsZips)) {
$allzips.=$ziprow['zips'].',';}
$allzips=substr($allzips,0,strlen($allzips)-1);
echo 'var zips = ['.$allzips.'];'; // this prints a line of javascript for the section of form validation below
?>
var formzip = document.forms["site_reg"]["zip"].value; 
if(!isInArray(zips, formzip)) { alert("That zip code is not in our service area. Please contact us directly at (253) 244-2177"); return false; }
} // end of form validation

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
	<h2>Donate your crop</h2>
</header>
<section class="wrapper style5">

    <div class="inner">
      <form action="<?php echo $editFormAction; ?>" id="site_reg" name="site_reg" method="post" onsubmit="return validateForm()">
        <p>Please use the form below  to register your crop, you may also register your crop over the phone by  calling (253) 290-8232.<strong> Please do not register again if you have previously registered a crop with us. Just call instead.</strong> All harvest participants are trained in proper harvesting methods and sign waivers of liability that protect the crop owner as well as Harvest Pierce County's Gleaning Project.</p>
		<p>More information about how Harvest Pierce County's Gleaning Project manages harvests of farm crops can be read <a href="farminfo.php">here</a>, and about homeowner's crops <a href="backyardinfo.php">here</a>.</p>
		<p>As a farmer/backyard grower, you are protected:  Click <a href="http://feedingamerica.org/get-involved/corporate-opportunities/become-a-partner/become-a-product-partner/protecting-our-food-partners.aspx" target="_blank">here</a> to read about The Emerson Good Samaritan Food Donation Act. All donors   will receive a tax deductible donation receipt. Please read about <a href="coverage.php">coverage and our policies</a>.</p>
		<p>Privacy: Information entered here is used solely by Harvest Pierce County's Gleaning Project and will not be available to the public. We do not share, sell or otherwise distribute your personal information.</p>
		<p>If you have questions, please contact us at <a href="mailto:info@piercecountygleaningproject.org">info@piercecountygleaningproject.org</a>.</p>
		<p>
        <label>Farm name or owner's last name
          <input name="farm" id="farm" type="text" value="<?php if(isset($_POST['farm'])) echo $_POST['farm'];?>" size="40"maxlength="60" />
        </label>
      </p>
      <p>
        <label>Contact person's name
          <input name="contact1" type="text" value="<?php if(isset($_POST['contact1'])) echo $_POST['contact1'];?>" size="40" maxlength="80" />
        </label>
      </p>
      <p>Phone
        number
        <label>
          <input name="phone1" type="text" value="<?php if(isset($_POST['phone1'])) echo $_POST['phone1'];?>" size="20" maxlength="50" />
        </label>
      Alternate phone number
        <label>
          <input name="phone2" type="text" value="<?php if(isset($_POST['phone2'])) echo $_POST['phone2'];?>"size="20" maxlength="50" />
        </label>
      </p>
      <p>Email address
        <input name="email1" type="text" value="<?php if(isset($_POST['email1'])) echo $_POST['email1'];?>" size="40" maxlength="60" />
      </p>
      <p>
        <label>Address of the crop
          <input name="address" type="text" value="<?php if(isset($_POST['address'])) echo $_POST['address'];?>" size="50" maxlength="80" />
        </label>
		</p>
        <p>
		<label>City <input name="city" type="text" value="<?php if(isset($_POST['city'])) echo $_POST['city'];?>" size="30" maxlength="30" /></label>
        <label>State <input name="state" type="text" value="<?php if(isset($_POST['state'])) echo $_POST['state'];?>" size="2" maxlength="2" /></label>
        <label>Zip <input name="zip" type="text" value="<?php if(isset($_POST['zip'])) echo $_POST['zip'];?>" size="5" maxlength="5" /></label>
        </p>
        <p>
     <label>Mailing address (<em>leave blank if the same as the crop address</em>)
<input name="maddress" type="text"value="<?php if(isset($_POST['maddress'])) echo $_POST['maddress'];?>" size="60" maxlength="80" />
        </label>
      </p>
        <p><label>City <input name="mcity" type="text" value="<?php if(isset($_POST['mcity'])) echo $_POST['mcity'];?>" size="30" maxlength="30" /></label>
        <label>State <input name="mstate" type="text" value="<?php if(isset($_POST['mstate'])) echo $_POST['mstate'];?>" size="2" maxlength="2" /></label>
        <label>Zip <input name="mzip" type="text" value="<?php if(isset($_POST['mzip'])) echo $_POST['mzip'];?>" size="5" maxlength="5" /></label>
        </p>

      <p>
      <label>Relationship to property:
          <select name="property_rel">
            <option value=" " selected="selected">[select]</option>
            <option name="property_rel" id="property_rel_0" value="owner">Owner and Occupant</option>
            <option name="property_rel" id="property_rel_1" value="landlord">Rental Property (Landlord)</option>
            <option name="property_rel" id="property_rel_2" value="renter">Renter</option>
            <option name="property_rel" id="property_rel_3" value="other">Other</option>
          </select>
	<label>Landlord contact information (if applicable)
          <input type="text" name="landlord" size="40" id="landlord" value="<?php if(isset($_POST['landlord'])) echo $_POST['landlord'];?>"/>
        </label>
      </label>  </p>


       <p>
      <label>What type of site?
          <select name="venue" >
            <option value=" " selected="selected">[select]</option>
            <option value="Backyard">Backyard</option>
            <option value="Farm">Commercial farm</option>
            <option value="Pickup">Small amount to pick up</option>
            <option value="Market">Market</option>
          </select>
      </label>  </p>
	  
	  
       <p>
        <label>What type(s) of produce and when are they usually ready?<br />
For instance: &quot;apples: mid-June, pears: early July, plums: right now&quot;<br />
<input name="crops" type="text" id="crops" value="<?php if(isset($_POST['crops'])) echo $_POST['crops'];?>"size="100" maxlength="150" />
        </label>
      </p>
      <p>How much (for instance '2 trees' or '3 acres' or '300 pounds')
        <input name="size" type="text" id="size" value="<?php if(isset($_POST['size'])) echo $_POST['size'];?>"size="30" maxlength="30" />
      </p>
      <p>Location of plants on the property
        <input name="location" type="text" id="location" size="30" value="<?php if(isset($_POST['location'])) echo $_POST['location'];?>" maxlength="20" />      
      <p>Crop height (feet)
      <input name="height" type="text" id="height" size="5" value="<?php if(isset($_POST['height'])) echo $_POST['height'];?>"maxlength="10" />      
     
	 <p>
	  <label>Does the crop have any fungus, disease, or pest issues?
	  <select name="disease">
            <option value=" " selected="selected">[select]</option>
            <option name="disease" id="disease_0" value="No">No</option>
            <option name="disease" id="disease_1" value="Yes">Yes</option>
      </select>
	  <label>Describe <input name="disease_text" type="text" id="disease_text" value="<?php if(isset($_POST['disease_text'])) echo $_POST['disease_text'];?>"size="25" maxlength="25" />
        </label>  
	  </label>
		</p>
		<!--      <p>

       What time of year is the crop usually ripe?
          <select name="month_ripe" id="when_ripe">
            <option value="01" selected="selected">[month]</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
      </select>
          <select name="week_ripe" id="when_ripe">
            <option value="01" selected="selected">[week]</option>
            <option value="03">first week</option>
            <option value="10">second week</option>
            <option value="17">third week</option>
            <option value="24">fourth week</option>
      </select></p> 
	-->
   
      <p>
	  <label>Has the crop been sprayed?
		<select name="spray">
            <option value=" " selected="selected">[select]</option>
            <option name="spray" id="spray_0" value="No">No</option>
            <option name="spray" id="spray_1" value="Yes">Yes</option>
      </select>
		<label>Describe <input name="spray_text" type="text" id="spray_text" value="<?php if(isset($_POST['spray_text'])) echo $_POST['spray_text'];?>" size="25" maxlength="25" />
		</label>
	  </label>
	  </p>
	  
	  
 
      <p>
	  <label>Do you have to be home when the site is harvested?
	  <select name="present" >
            <option value=" " selected="selected">[select]</option> 
	    <option name="present" value="No" checked="checked">No</option>
            <option name="present" value="Yes">Yes</option>
      </select>
	  </label>
	  </p>
	  
<p>Is there anything else you would like us to know (condition of the produce, access to the crops, preferred ways and times to contact you )?</p>
      <p>
        <label>
          <textarea name="otherinfo" cols="80" rows="3"><?php if(isset($_POST['otherinfo'])) echo $_POST['otherinfo'];?></textarea>
        </label></p>
        <p><label>How did you hear about Harvest Pierce County's Gleaning Project?
          <select name="howhear" id="howhear">
            <option value=" " selected="selected">[select]</option>
            <option value="Neighbor">Neighbor</option>
            <option value="Pierce County Gleaning Project web site">Harvest Pierce County's Gleaning Project Web Site</option>
            <option value="Pierce County Gleaning Project volunteer">Harvest Pierce County's Gleaning Project volunteer</option>
            <option value="Newspaper">Newspaper</option>
            <option value="Facebook">Facebook</option>
            <option value="Flyer">Flyer</option>
            <option value="Craigslist">Craigslist</option>
            <option value="Other urban harvesting group">Other urban harvesting group</option>
            <option value="Friend">Friend</option>
            <option value="Other">Other</option>
            <option value="Web search">Web search</option>
          </select>
      </label></p> 
      <p>SPAM prevention - do the math and type in the number:
		<img src="includes/captcha.inc.php" />	<input name="captcha" type="text"></p>
      <p>
        <label>
        <input type="submit" name="submit" id="submit" value="Save" />
      </label>
        <input type="hidden" name="MM_insert" value="site_reg" /></p>
  </form>
<p>&nbsp;</p>
</div>

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