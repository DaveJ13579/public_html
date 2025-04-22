<?php 
if(!isset($_SESSION)) session_start();
require_once('Connections/piercecty.php');
require_once('includes/sqlcleaner.php');
$customfield='presstxt';
require_once('includes/customtxt.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Press</title>
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
 <div id="narrowbody"><br />
<?php if($swt=='show') { echo $pagetext;
 } else { ?>  
 <form id="survey" name="survey" method="POST" action="">
<input type="submit" name="submit" value="Save Update" style="font-weight:bold" />&nbsp;&nbsp;<a href="help/html-help.php" target="_blank">HTML Help</a>
<textarea name="pagetxt" cols="100" rows="20"><?php echo $pagetext; ?></textarea>
</form>
 <?php  }  
 
  if(isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup']=='all') { ?>
<div style="width:500px; height:30px; float:right; text-align:right;"><a href="<?php echo $here.'?ed='.$butswitch;?>" target="_self"><img src="images/Nav%20buttons/Edit.png" alt="edit"  height="20px" /></a>&nbsp;&nbsp;&nbsp;&nbsp;</div> <?php } ?> 
 </div>
<p><br />
</p>
 <!-- end #mainContent --></div>
<!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats --><br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container --></div>
</body>
</html>
