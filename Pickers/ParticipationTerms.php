<?php 
if(!isset($_SESSION)) session_start();
require_once('../Connections/piercecty.php');
require_once('../includes/sqlcleaner.php');
$customfield='termstxt';
require_once('../includes/customtxt.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
		<title>Terms and Conditions</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="../assets/css/main.css" />
		<noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h1><a href="../index.php">Pierce County Gleaning Project</a></h1>
						<nav id="nav">
							<ul>
								<li class="special">
									<a href="#menu" class="menuToggle"><span>Menu</span></a>
									<div id="menu">
										<ul>
											<li><a href="../index.php">Home</a></li>
											<li><a href="../register_form.php">Register to Volunteer</a></li>
											<li><a href="../donate_crop.php">Donate your Crop</a></li>
											<li><a href="../harvestlist.php">Upcoming Harvests</a></li>
											<li><a href="../Utilities/PagesIndex.php">Log In</a></li>
											
										</ul>
									</div>
								</li>
							</ul>
						</nav>
					</header>
<article id="main">
						<header>
							<h2>Terms of Participation</h2>
						</header>
						<section class="wrapper style5">
							<div class="inner">
    <div id="narrowbody"><br />
 
<?php if($swt=='show') { echo $pagetext; 
 } else { ?>  
 <form id="survey" name="survey" method="POST" action="">
<input type="submit" name="submit" value="Save Update" style="font-weight:bold" />&nbsp;&nbsp;<a href="../help/html-help.php" target="_blank">HTML Help</a>
<textarea name="pagetxt" cols="100" rows="20"><?php echo $pagetext; ?></textarea>
</form>
 <?php  }  ?>
 </div>
 <?php if(isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup']=='all') { ?>
<div style="width:500px; height:30px; float:right; text-align:right;"><a href="<?php echo $here.'?ed='.$butswitch;?>" target="_self"><img src="../images/Nav buttons/Edit.png" alt="edit"  height="20px" /></a>&nbsp;&nbsp;&nbsp;&nbsp;</div> <?php } ?> 
 <br class="clearfloat" />
 <p><a href="../index.php" class="indent">Return to Home Page</a></p>
							</div>
						</section>
					</article>

				<!-- Footer -->
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
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/jquery.scrollex.min.js"></script>
			<script src="../assets/js/jquery.scrolly.min.js"></script>
			<script src="../assets/js/browser.min.js"></script>
			<script src="../assets/js/breakpoints.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<script src="../assets/js/main.js"></script>

	</body>
</html>