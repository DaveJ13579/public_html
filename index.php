<?php require_once('Connections/piercecty.php');  

require_once('includes/sqlcleaner.php');

$customfield='hometxt';

require_once('includes/customtxt.inc.php');

?>

<!DOCTYPE HTML>

<!--

	Spectral by Pixelarity

	pixelarity.com | hello@pixelarity.com

	License: pixelarity.com/license

-->





<!-- PAGES TO UPDATE



BRANCH LEADER LOGIN 

HARVESTS PAGE (HARVESTLIST.PHP)



-->

<html>

	<head>

		<title>Gleaning Project</title>

		<meta charset="utf-8" />

		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

		<link rel="stylesheet" href="assets/css/main.css" />

		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>

	</head>

	<body class="landing is-preload">



		<!-- Page Wrapper -->

			<div id="page-wrapper">



				<!-- Header -->

					<header id="header" class="alt">

						<h1><a href="index.php">Pierce County Gleaning Project</a></h1>

						<nav id="nav">

							<ul>

								<li class="special">

									<a href="#menu" class="menuToggle"><span>Menu</span></a>

									<div id="menu">

										<ul>

											<li><a href="index.php">Home</a></li>

											<li><a href="pickerinsert.php">Register to Volunteer</a></li>

											<li><a href="donate_crop.php">Donate your Crop</a></li>

											<li><a href="harvestlist.php">Upcoming Harvests</a></li>

											<li><a href="Utilities/PagesIndex.php">Log In</a></li>

											

										</ul>

									</div>

								</li>

							</ul>

						</nav>

					</header>



				<!-- Banner -->

					<section id="banner">

						<div class="inner">

							<h2>Pierce County Gleaning Project</h2>

							<p>A Harvest Pierce County Program<br />

							Aiming to reduce food waste<br />

							Provide food to those in need<br />

							And build community</p>

							<ul class="actions special">

								<li><a href="pickerinsert.php" class="button primary">Register to Volunteer</a></li>

								<li><a href="donate_crop.php" class="button primary">Donate your Crop</a></li>

								<li><a href="harvestlist.php" class="button primary">Upcoming Harvests</a></li>

							</ul>

						</div>

						<a href="#one" class="more scrolly">Learn More</a>

					</section>



				<!-- One -->

					<section id="one" class="wrapper style1 special">

						<div class="inner">

							<header class="major">

								<h2>What is Gleaning?</h2>

								<p>Gleaning is the ancient practice of collecting leftover produce after commercial harvest. 

								For centuries field gleaning has been a valuable resource to people who want or need fresh produce. 

								Many formal gleaning programs like Harvest Pierce Countys Gleaning Project have been established to help meet the growing need for food assistance, 

								minimizing food waste, and building community.</p>

							</header>

						</div>

					</section>



				<!-- Two -->

					<section id="two" class="wrapper alt style2">

						<section class="spotlight">

							<div class="image"><img src="images/potatoimg2.jpg" alt="" /></div><div class="content">

								<h2>What do we do?<br /></h2>

								<p>The Gleaning Project is a volunteer powered program of 

								Harvest Pierce County that works to reduce local produce waste, 

								provide more fresh food to those in need, and build community. 

								This project is a response to the dramatic increase in the number of people seeking food assistance in our county. 

								Our Glean Teams harvest from both farms and backyard fruit trees, 

								then share the bounty with local food banks, shelters, and families in need. 

								Volunteers also take a portion home as a thank you for all their hard work! </p>

							</div>

						</section>

						<section class="spotlight">

							<div class="image"><img src="images/group.jpg" alt="" /></div><div class="content">

								<h2>What can you do?<br /></h2>

								<p><h4>Volunteer to be a Harvester</h4>

								Harvesters are volunteers who participate in gleaning events by helping to pick, sort, and weigh produce that is harvested. Harvesters do not need to attend any training to start helping as your Glean Lead or Branch Leader will train you during the gleaning event. </p>
								
								<p>On top of building a better community, volunteers get to take home a portion of the harvest!

								</p>

								<p><h4>Apply to be a Branch Leader</h4>

								Branch Leaders will work closely with Fruit Tree Assessors, Site Stewards (homeowners, renters, etc.), Harvest Pierce County staff, and volunteer Harvesters. They are responsible for scheduling and leading urban fruit harvests with a team of volunteer Harvesters. In addition, Branch Leaders are responsible for assuring the produce gets dropped off to local hunger relief organizations, and recording harvest data and details. </p> 

							<p>	Branch Leaders also get to take home a portion of the harvest, which means they have access to fresh fruit all season long. This is the perfect opportunity for people who like to preserve food or bake, and want to give back to the community. </p>

								<p>We ask that Branch Leaders have a drivers liscense and a reliable form of transportation that can accomodate an 8-foot ladder and numerous gleaning bins filled with freshly picked produce. Branch Leaders should also be able to commit to the entire season and have ability to perform their duties for 3-5 hours/week. Branch Leaders are provided mileage reimbursement and a $1000 end of season pay for gleaning a minimum of 30 trees.

								</p>

									<p><h4>Apply to be a Fruit Tree Assessor</h4>

								Fruit Tree Assessors will work with Branch Leaders, Site Stewards (renters, homeowners, farmers, etc.), and Harvest Pierce County staff. They are responsible for scheduling fruit tree assessments with Site Stewards, and assessing both the tree and their fruit to determine if it has pest and disease issues, requires pruning or replacement, and to come up with an action plan for Site Stewards to achieve a healthy orchard. In addition, Fruit Tree Assessors are responsible for letting our Branch Leaders know if the fruit is healthy and ripe for the picking, and recording site data and details. </p>

							<p>	We ask that Fruit Tree Assessors have a drivers liscense and a reliable form of transportation that can accomodate an 8-foot ladder. Fruit Tree Assessors should also be able to commit to the entire season and have ability to perform their duties for 2-4 hours/week. Fruit Tree Assessors are provided mileage reimbursement and a $1500 end of season pay for assessing a minimum of 60 trees.

								</p>


								<ul class="actions special">

									<li><a href="register_form.php" class="button primary">Register to Volunteer</a></li>

									<li><a href="harvestlist.php" class="button primary">Upcoming Harvests</a></li>

								</ul>

							</div>

						</section>

											

						

						<section class="spotlight">

							<div class="image"><img src="images/apples2.jpg" alt="" /></div><div class="content">

								<h2>Frequently Asked Questions<br /></h2>

								<p><h4>Where does the Gleaning Project Harvest?</h4>

								We primarily harvest from farms in the Puyallup/Orting valley and pick fruit trees in 

								Tacoma, Parkland, Summit/Waller, Steilacoom, Lakewood, Gig Harbor, and the Puyallup/Sumner/Bonney Lake Area. 

								</p>

								

								<p><h4>Where does the food go?</h4>

								For farm harvests, volunteers can take a serving of produce home if the farmer allows it - the rest is donated to local hunger relief organizations near the harvest site. </p> 

							<p>	At least 50% of the food from fruit tree harvests is donated, with the other 

								50% divided between the volunteer Harvesters and the Site Stewards (25% for each).

								</p>

								

								<p><h4>When do you harvest?</h4>

								Our harvest season generally runs from June -September, however, we have sporadic harvests throughout the winter and spring as well! 

								Our harvest days and times are largely based on the availability of our wonderful volunteer harvest coordinators and Site Stewards (meaning farmer, renter, homeowner, organization, community garden lead, etc.) schedules'. 

								Even if we don't have a harvest that fits your schedule one week, check back again in another week or two and we might have something that works for you! In fact you should bookmark the "Upcoming Harvests" page so you can regularly and easily check to see when knew gleans get posted and need your help. </p>
								
							<p>	We are, unfortunately, unable to send out mass emails to our gleaning network to let them know when a new harvest gets scheduled. Checking our "Upcoming Harvests" page is the only way to stay updated.

								</p>

							</div>

						</section>

						

						<section class="spotlight">

							<div class="image"><img src="images/apples.jpg" alt="" /></div><div class="content">

								<h2>Frequently Asked Questions<br /></h2>

								

								<p><h4>Can I bring my kids?</h4>

								Most gleaning events are family friendly unless otherwise stated. 

								We love having kids around and exposing them to the food system. 

								If you are registering for an event and your child is under 18 years old, 

								please only register for yourself. We ask that you only register for ONE volunteer 

								space to make sure we have enough capable hands to complete the harvest. 

								Lastly, when you arrive at the harvest please ask the 

								Harvest Leaders for a Youth Liability Waiver so you may complete one for your child.

								</p>

								

								<p><h4>Why don't I see the address listed for a harvest?</h4>

								The address for a glean will be emailed to you after you are registered. 

								If you are on the waitlist, you will not receive the address unless a spot opens. 

								If you are registered and do not receive an email, please let us know at harvestpiercecounty@piercecd.org

								</p>

								

								<p><h4>What if I signed up for a harvest I cannot attend?</h4>

								Please UNREGISTER yourself by finding the harvest confirmation email,

								and clicking the "unregister" link. If you have trouble, email

								info@piercecountygleaningproject.org.

								</p>

							</div>

						</section>

					</section>





				<!-- CTA -->

				<article id="main">

						<section class="wrapper style5">

							<div class="inner" id="mc_embed_signup">

<form action="https://piercecountycd.us5.list-manage.com/subscribe/post?u=9710b4a42ecaa96221d2dd036&amp;id=32be8bfce9" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="novalidate">

    <div id="mc_embed_signup_scroll">

               <h4>Subscribe</h4>

<div class="indicates-required"><span class="asterisk">*</span> indicates required</div>

<div class="mc-field-group">

               <label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>

</label>

               <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" aria-required="true" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">

</div>

<div class="mc-field-group">

               <label for="mce-FNAME">First Name </label>

               <input type="text" value="" name="FNAME" class="" id="mce-FNAME">

</div>

<div class="mc-field-group">

               <label for="mce-LNAME">Last Name </label>

               <input type="text" value="" name="LNAME" class="" id="mce-LNAME">

</div>

               <div id="mce-responses" class="clear">

                              <div class="response" id="mce-error-response" style="display:none"></div>

                              <div class="response" id="mce-success-response" style="display:none"></div>

               </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->

    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_9710b4a42ecaa96221d2dd036_32be8bfce9" tabindex="-1" value=""></div>

    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>

    </div>

</form>

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

			<script src="assets/js/jquery.min.js"></script>

			<script src="assets/js/jquery.scrollex.min.js"></script>

			<script src="assets/js/jquery.scrolly.min.js"></script>

			<script src="assets/js/browser.min.js"></script>

			<script src="assets/js/breakpoints.min.js"></script>

			<script src="assets/js/util.js"></script>

			<script src="assets/js/main.js"></script>



	</body>

</html>