<?php require_once('Connections/piercecty.php');  
require_once('includes/sqlcleaner.php');
$customfield='hometxt';
require_once('includes/customtxt.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="harvest, fruit, gleaning, gleaners, non-profit, food insecurity, pick" />
<title>Pierce County Gleaning Project</title>
<link href="piercecty.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#picture1 { position: relative;  left: 0px;  }
#picture2 { position: relative; left: -505px; }
#window {
	float:left;
	height: 200px;
	width: 500px;
	border: 3px solid #642;
	overflow: hidden;
	position: relative;
	z-index: 2;
}
.fullj {
text-align:justify;
text-justify:inter-word;
}
#rightdiv {
	font-size:1.3em;
	float:right;
	width:275px;
	text-align:justify;
}
#bigbuttons {
	width:800px;
	margin:auto;
	text-align:center;
}
</style>
<script type="text/javascript">
var photoarray = new Array();
for (k=0 ; k<=<?php echo count(glob('images/slides/'.'*'))-1;?>; k=k+1) {
	t=k+1;
	photoarray[k] = "images/slides/"+t+".jpg" }
</script>
<script type="text/javascript" src="includes/photoswap.js"></script> 
</head>
 <body class="SH">
<div id="container">
<div id="header" style="border:none">
<a href="http://harvestpiercecounty.org/" target="_blank" style="outline:0" ><img src="images/banners/banner-home.jpg" alt="Harvest Pierce County's Gleaning Project" width="880" height="180"/></a>
</div>
 <?php require_once('includes/navlinks.inc.php'); ?>
<div id="mainContent">
<h3>Harvest Pierce County's Gleaning Project</h3>
<br  />
<div id="window">
<table border="0" cellpadding="0" cellspacing="0" align="center" width="1500px" bordercolor="664422">
<tr bgcolor="#226"><td >
  <script>
      f('photoseq',photoarray,20,4000);
   </script></td></tr></table>
</div> <!-- end #window -->
<div id="rightdiv">
<i>The Gleaning Project is a volunteer program of Harvest Pierce County that works to reduce the amount of food waste in our community, provide more fresh food to those in need, and build community.</i>
</div>
<br class="clearfloat" /><br /><br />

<div id="bigbuttons">
  <a href="pickerinsert.php" class="button" style="width:200px; font-size:1.3em; border-width:2px;">Register to<br />Volunteer</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="site_registration.php"  class="button" style="width:200px; font-size:1.3em; border-width:2px;">Donate your<br />Crop</a>
</div>
<br /><br />
<hr style="width:85%;"/>
<div class="fullj"><?php if($swt=='show') { echo $pagetext; } else { ?>  
<form id="edittext" name="edittext" method="POST" action="">
<input type="submit" name="submit" value="Save Update" style="font-weight:bold" />&nbsp;&nbsp;<a href="help/html-help.php" target="_blank">HTML-Help</a>
<textarea name="pagetxt" cols="100" rows="20"><?php echo $pagetext; ?></textarea>
</form>
 <?php  }  ?>
</div>
 <?php if(isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup']=='all') { ?>
<div style="width:800px; height:30px; float:right; text-align:right;"><a href="<?php echo $here.'?ed='.$butswitch;?>" target="_self"><img src="images/Nav%20buttons/Edit.png" alt="edit"  height="20px" /></a>&nbsp;&nbsp;&nbsp;&nbsp;</div> <?php } ?> 
<br /><br />
</div><!-- end #mainContent -->
<br class="clearfloat" />
<?php require_once('includes/footer.inc.php'); ?>
<br class="clearfloat" />
</div><!-- end Container -->
</body>
</html>
