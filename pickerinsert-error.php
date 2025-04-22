<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>info missing</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
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
<?php 
if(isset($_GET['error']) and $_GET['error']=='emerg') { ?>
<p>An <strong>emergency contact</strong> name and phone number are required. Use the Back button on your browser to return to the registration page.</p>
<?php }
if(isset($_GET['error']) and $_GET['error']=='waiver') { ?>
<p>All volunteers who want to register with Harvest Pierce County's Gleaning Project must check off the box indicating that they agree to the Terms of Participation. Use the Back button on your browser to return to the registration page.</p>
<?php } ?>

<p>&nbsp;</p>
</div>
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
