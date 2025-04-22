<?php 
if(!isset($_SESSION)) session_start();
require_once('Connections/piercecty.php');
require_once('includes/sqlcleaner.php');
$customfield='newslettertxt';
require_once('includes/customtxt.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Newsletter</title>
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
<div align="center">
<table border="0" cellspacing="0" cellpadding="3" bgcolor="#d2e2f7" style="border:2px solid #000000;">
<tr>
<td align="center" style="font-family:Arial; font-size:16px; color:#000000;"><strong>Newsletter</strong><br />Receive the latest news and updates</td>
</tr>
<tr>
<td align="center" style="border-top:2px solid #000000">
<form name="ccoptin" action="http://visitor.r20.constantcontact.com/d.jsp" target="_blank" method="post" style="margin-bottom:2;">
<input type="hidden" name="llr" value="4eiviyhab">
<input type="hidden" name="m" value="1107842211999">
<input type="hidden" name="p" value="oi">
<font style="font-weight: normal; font-family:Arial; font-size:16px; color:#000000;">Email:</font> <input type="text" name="ea" size="30" value="" style="font-size:10pt; border:1px solid #999999;">
<input type="submit" name="go" value="Sign up now" class="submit" style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif; font-size:10pt;">
</form>
</td>
</tr>
</table>
</div>
<?php if($swt=='show') { echo $pagetext;
 } else { ?>  
 <form id="survey" name="survey" method="POST" action="">
<input type="submit" name="submit" value="Save Update" style="font-weight:bold" />&nbsp;&nbsp;<a href="help/html-help.php" target="_blank">HTML Help</a>
<textarea name="pagetxt" cols="100" rows="20"><?php echo $pagetext; ?></textarea>
</form>
 <?php  }  ?>
 </div>
 <!-- end #mainContent -->
 <?php if(isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup']=='all') { ?>
<div style="width:500px; height:30px; float:right; text-align:right;"><a href="<?php echo $here.'?ed='.$butswitch;?>" target="_self"><img src="images/Nav%20buttons/Edit.png" alt="edit"  height="20px" /></a>&nbsp;&nbsp;&nbsp;&nbsp;</div> <?php } ?> 
 <br class="clearfloat" />
</div>
<?php require_once('includes/footer.inc.php'); ?>
<!-- end #container -->
 <br class="clearfloat" />
</div>
</body>
</html>
