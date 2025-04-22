<?php 
require_once('Connections/piercecty.php');
require_once('includes/sqlcleaner.public.php'); 
require_once('includes/smtpmailer.inc.php');
require_once('includes/branch.inc.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$error='';
if((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "site_reg") && ($_SESSION["code"]<>$_POST["captcha"])) {
	$error="spam"; }

if((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "site_reg") && ($_SESSION["code"]==$_POST["captcha"])) {
	
if(!(is_numeric($_POST['timeloaded'])) || time()-$_POST['timeloaded']<30) { header("Location: index.php"); exit; }  // exit if less than 30 seconds for form

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>site registration</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
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
<style>
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
</style>
</head>
<body class="SH" onload="setFocus()">
<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>
<?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
<p> </p>
<div class="indentdiv">
    <h3 class="SH"><strong>Crop Donation</strong></h3>    
 <?php if($error=='spam') echo "<h2>At the bottom of the form, please type the sum of the numbers in the space provided, review what you have entered into the form, and click Save again.</h2>";  ?>
<p>Please use the form below  to register your crop, you may also register your crop over the phone by  calling (253) 278-6215.<strong> Please do not register again if you have previously registered a crop with us. Just call instead.</strong> All harvest participants are trained in proper harvesting methods and sign waivers of liability that protect the crop owner as well as Harvest Pierce County's Gleaning Project.</p>
<p>More information about how Harvest Pierce County's Gleaning Project manages harvests of farm crops can be read <a href="farminfo.php">here</a>, and about homeowner's crops <a href="backyardinfo.php">here</a>.</p>
<p>As a farmer/backyard grower, you are protected:  Click <a href="http://feedingamerica.org/get-involved/corporate-opportunities/become-a-partner/become-a-product-partner/protecting-our-food-partners.aspx" target="_blank">here</a> to read about The Emerson Good Samaritan Food Donation Act. All donors   will receive a tax deductible donation receipt. Please read about <a href="coverage.php">coverage and our policies</a>.</p>
<p>Privacy: Information entered here is used solely by Harvest Pierce County's Gleaning Project and will not be available to the public. We do not share, sell or otherwise distribute your personal information.</p>
<p>If you have questions, please contact us at <a href="mailto:MasonD@piercecountycd.org">harvestpiercecounty@gmail.com</a>.</p>
<form action="<?php echo $editFormAction; ?>" id="site_reg" name="site_reg" method="post" onsubmit="return validateForm()">
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
       <p>What is your relationship to this property?</p>
      <p  id="indent">
        <label>
		<input name="property_rel" type="radio" id="property_rel_0" value="owner" checked="checked" />
Owner and occupant</label>
        <br />
        <label>
          <input type="radio" name="property_rel" value="landlord" id="property_rel_1" />
          Rental property (landlord)</label>
        <br />
        <label>
          <input type="radio" name="property_rel" value="renter" id="property_rel_2" />
          Renter</label>
        <br />
        <label>
          <input type="radio" name="property_rel" value="other" id="property_rel_3" />
          Other</label>
          <br />
        <label>Landlord contact information (if applicable)
          <input type="text" name="landlord" size="40" id="landlord" value="<?php if(isset($_POST['landlord'])) echo $_POST['landlord'];?>"/>
        </label>
      </p>
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
       <p>Does the crop have any fungus, disease or pest issues?</p>
      <p id="indent">
        <label><input name="disease" type="radio" id="disease_0" value="No" checked="checked" /> No</label>&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="disease" value="Yes" id="disease_1" /> Yes </label><br />
        <label>Describe <input name="disease_text" type="text" id="disease_text" value="<?php if(isset($_POST['disease_text'])) echo $_POST['disease_text'];?>"size="25" maxlength="25" />
        </label></p>
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
      <p>Has the crop been sprayed this year? </p>      
      <p id="indent">
        <label><input name="spray" type="radio" id="spray_0" value="No" checked="checked" /> No</label>&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="spray" value="Yes" id="spray_1" /> Yes</label><br />
        <label>Describe <input name="spray_text" type="text" id="spray_text" value="<?php if(isset($_POST['spray_text'])) echo $_POST['spray_text'];?>" size="25" maxlength="25" />
    </label></p>
      <p>Do you have to be home when your tree is scouted/harvested?</p>
      <p id="indent">
        <label><input name="present" type="radio" value="No" checked="checked" /> No</label>&nbsp;&nbsp;&nbsp;
        <label class="tooltip"><input name="present" type="radio" value="Yes" /> Yes <span class="tooltiptext">Since scheduling is a difficult variable, if you have to be at the site when it is harvested or registered, this may impact whether or not we are able to get to your tree this year.</span></label></p>
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
<input name="timeloaded" type="hidden" value="<?php echo time();?>"/>
  </form>
    </div>
<!-- end #mainContent --></div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
