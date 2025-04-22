<?php 
require_once('Connections/piercecty.php'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sorry</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
</head>

<body class="SH">

<div id="container">
  <div id="header">
   <img src="images/banners/banner-home.jpg" width="876" height="180" border="0" /> 
  </div>

<div id="mainContent">
  <p>&nbsp;</p>
<?php 
$sorry= isset($_GET['sorry']) ? $_GET['sorry'] : 'default';

switch($sorry)
{
case "closed":
?>
<h3>Sorry!</h3>
<p>That harvest is closed and not available for signup.</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p>&nbsp;</p>

<?php
break;
case "noemail":
?>
<h3>Sorry!</h3>
<p>To get on the waiting list, you must have a current email address. I can find no address with your volunteer registration. If you want to add an email address, contact <a href="mailto:piercecty@gleanweb.org">Dick Yates</a>.</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p>&nbsp;</p>

<?php
break;
case "noname":
?>
  <h3>Sorry, your name was not found</h3>
<p>The name or email address that you put in the harvest sign up form does not seem to be in our list of registered volunteers. If you have not already registered as a volunteer please go to <a href="pickerinsert.php">this</a> page to register first.</p>
<p>The name that you use to sign up for a harvest must <strong><em>exactly</em></strong> match the one that you used to register as a volunteer. If you are already registered, use the Back button of your browser and try again.</p>
<p>If you have already registered as a volunteer and the sign up page still will not accept your name, send a note to <a href="mailto: piercecty@gleanweb.org">Dick Yates</a> with your name and the harvest that you are trying to sign up for.</p>
<p>&nbsp;</p>

<?php
break;
case "past":
?>
<h3>Sorry!</h3>
<p>That harvest has already started and the roster cannot be changed.</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p>&nbsp;</p>

<?php
break;
case "badlink":
?>
<h3>Sorry!</h3>
<p>You do not have the correct authorization to sign up for that harvest.</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p>&nbsp;</p>

<?php 
break;
case "noharvest":
?>
<h3>Sorry!</h3>
<p>I am unable to identify the harvest.</p>
<p><a href="index.php" class="indent">Return to Home Page</a></p>
<p>&nbsp;</p>

<?php 

break;
case "nowaiver":
?>
<h3>Please check off the liability waiver box </h3>
<p>All volunteers who want to sign up for a harvest must check off the box indicating that they agree to the liability waiver. Use the Back button on your browser to return to the sign up page. </p>
<p>&nbsp;</p>

<?php
break;
default: 
?>
<h3>Sorry!</h3>
<p>An unknown contingency has sent you to this page.</p>
<p>&nbsp;</p>

<?php
break;
} // end of switch?>
 


<!-- end #mainContent --></div>
	<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
