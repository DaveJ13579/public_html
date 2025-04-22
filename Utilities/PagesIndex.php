<?php
if (!isset($_SESSION)) session_start();
$MM_authorizedUsers = "all,change,view";
$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pages Index</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
<!--
-->
</style>
</head>

<body class="SH">
<div id="container">
  <?php include_once('../includes/AdminNav2.inc.php'); ?>
  <div id="mainContent">
    <h3><strong>Pages Index</strong></h3>
    <table width="1200" border="2" cellpadding="2" cellspacing="10" align="center">
      <tr>
        <th align="center">Page Link</th>
        <th>Change Database Information (requires 'change' authority)</th>
      </tr>
      <tr>
        <td align="center"><a href="rosterupdate-attendance.php">Roster Attendance Update</a></td>
        <td>The same as Change: Roster except that it is optimized for efficiency in entering attendance records following large harvests. Only the 'status' field can be changed, allowing a rapid tab to the next record. One key shortcuts are implemented.</td>
      </tr>
      <tr>
        <td align="center"><a href="spotmanager.php">Meeting spots manager</a></td>
        <td>Add, change, delete carpool meeting spots.</td>
      </tr>
      <tr>
        <td align="center"><a href="cropmanager.php">Crop list manager</a></td>
        <td>Add, change, delete crops from the croplist.</td>
      </tr>
      <tr>
	      <td align="center"><a href="branchesmanager.php">Branches list manager</a></td>
        <td>Add, change, delete branch names, their zip codes and branch leaders.</td>
      </tr>
	<tr>
        <td align="center"><a href="../mobile/attendance-m2.php">Roster Attendance Update - mobile version</a></td>
        <td>Update the attendance information on a harvest roster. Layout is designed for small screens and is intended for use on a cell phone at a harvest. (Be sure that you will have cell coverage before relying on this.</td>
      </tr>
	      <tr>
        <td align="center"><a href="distributionsitesmanager.php">Distribution sites manager</a></td>
        <td>Add, change, delete distribution sites.</td>
      </tr>   
      <tr>
        <td align="center"><a href="../customize/uploadslides.php">Upload slider photos</a></td>
        <td>Manages the photos that appear in the Home Page slider. </td>
      </tr>
      <tr>
        <td align="center"><a href="jobsmanager.php">Jobs manager</a></td>
        <td>Add and change the master list of assistants' jobs at harvests.</td>
      </tr>
      <tr>
        <td align="center"><a href="smtpmanager.php">SMTP manager</a></td>
        <td>Edits email addresses used in automatic mail sending functions. [requires &quot;all&quot; authority] </td>
      </tr>
      <tr>
        <td align="center"><a href="php routines/SitesToInactive.php">Deactivate sites</a></td>
        <td>Changes to 'Inactive' all sites that were registered more than two years ago and with no harvests in the last two years. [requires &quot;all&quot; authority] </td>
      </tr>
      <tr>
        <td align="center"><a href="../Branch/branch-home.php">Branch leader home</a></td>
        <td>Entry for all database users with only 'branch' level of access.</td>
      </tr>
       <tr>
        <th width="200" height="39" align="center">Page link</th>
        <th>Mobile pages</th>
      </tr>
      <tr>
        <td align="center"><a href="../mobile/harvestupdate-m.php">Harvest Update - mobile</a></td>
        <td>Small screen version of Harvest Update</td>
      </tr>
      <tr>
        <td align="center"><a href="../mobile/attendance-m2.php">Attendance Update - mobile</a></td>
        <td>Small screen version of Attendance Update</td>
      </tr>
       <tr>
        <th width="200" height="39" align="center">Page link</th>
        <th>PCD Reports</th>
      </tr>
<tr>
<td align="center"><a href="../PCD_Reports/PCD_Gleaning_Contacts.php">PCD Gleaning Contacts</a></td>
<td>Lists of all volunteers with contact information</td>
</tr>
<tr>
<td align="center"><a href="../PCD_Reports/PCD_Gleaning_Crops.php">PCD Gleaning Crops</a></td>
<td>Lists of all crop types gleaned</td>
</tr>
<tr>
<td align="center"><a href="../PCD_Reports/PCD_Gleaning_Harvests.php">PCD Gleaning Harvests</a></td>
<td>Lists of all gleans by selected year</td>
</tr>
<tr>
<td align="center"><a href="../PCD_Reports/PCD_Gleaning_ProjectSites.php">PCD Gleaning Sites</a></td>
<td>Lists of all gleaning sites with address and contact information</td>
</tr>
 <tr>
        <th width="200" height="39" align="center">Page link</th>
        <th>View Database Information (requires any signon)</th>
      </tr>
      <tr>
        <td align="center"><a href="distributioncalendar.php<?php echo '#wk'.date('W');?>">Distribution calendar</a></td>
        <td>Calendar format showing all distributions.</td>
      </tr>
      <tr>
        <td align="center"><a href="Reports.php">Reports Generator</a></td>
        <td>The Reports Generator draws information from all the different database tables and displays it in a table that can be viewed and copied. The sql query that produces the report can be modified and saved as a new report. There is a library of reports to choose from. Some users can delete reports or copy and delte records in the database.</td>
      </tr>
		<tr>
        <td align="center"><a href="statistics-totals.php">Statistics</a></td>
        <td>A set of six statistics pages detailing: totals, distributions, harvests, sites, volunteers, and crops. </td>
      </tr>
	<tr>
        <td align="center"><a href="year-by-year.php">Year-by-year Statistics</a></td>
        <td>Assorted stats grouped by year.</td>
      </tr>
      <tr>
        <td align="center"><a href="http://www.salemharvest.org/training/videos">Database Training Videos</a></td>
        <td>A set of training videos about how to use the main pages and functions of the database. </td>
      </tr>
      <tr>
        <td align="center"><a href="duplicates.php">Picker Duplicates Finder</a></td>
        <td>Locates duplicate names in the picker list. Includes links to the picker detail pages. Includes a button to merge all newer info wth the older info - including roster history [requires 'all' authority].</td>
      </tr>
      <tr>
        <td align="center"><a href="siteduplicates.php">Site Duplicates Finder</a></td>
        <td>Locates duplicates in the site list. Includes links to the site update pages. Includes a button to merge all newer info wth the older info - including harvest history [requires 'all' authority].</td>
      </tr>
      <tr>
        <td align="center"><a href="roster-duplicates.php">Roster Duplicates Finder</a></td>
        <td>Locates duplicate entries in the harvest rosters. Includes a link to delete duplicate entries ('all' authority required to delete).</td>
      </tr>
	  <tr>
	    <td align="center"><a href="../Owners/surveyresults-owners.php">Owner Survey Results </a>
	    <td>Results of crop owner surveys solicited by email.</td>
      </tr>
	  <tr>
	    <td align="center"><a href="IntervalReport.php">Interval Report      </a>        
	    <td>Enter start and end dates for an interval and tables summarizing harvests, crops, attendance and leaders are displayed.</td>
      </tr>
      <tr>
        <td align="center"><a href="IPfinder.php">IP address finder</a></td>
        <td>Looks up IP addresses in both picker registrations and rosters. Usually used by direct link from the email that is sent to the webmaster when pickers attempt to cancel roster signups, but do so from a different computer. </td>
      </tr>
      <tr>
        <td align="center"><a href="../Owners/AllReceipts-pdf.php">Year-end receipts</a></td>
        <td>Generates one pdf with all receipts for a selected year</td>
      </tr>
      <tr>
        <td align="center"><a href="../Owners/AllEnvelopes-pdf.php">Year-end envelopes</a></td>
        <td>Generates one pdf with all receipt envelopes for a selected year</td>
      </tr>
            <tr>
        <td align="center"><a href="../Utilities/Hits/page-hits.php">Page hits viewer</a></td>
        <td>Displays  hits on volunteer registration and volunteer update pages by date range and IP address and name. NOTE: All IP addresses are squishy because one person may have several IP addresses in rosters, and one IP address may be associated with several people. </td>
      </tr>
      <tr>
        <td align="center"><a href="PageDescriptions.php">Page descriptions</a></td>
        <td>Alphabetical listing of web pages with descriptions of function.</td>
      </tr>
      <tr>
        <th align="center">Page Link</th>
        <th>PCD Reports for Export</th>
      </tr>
      <tr>
        <td align="center"><a href="../PCD_Reports/PCD_Gleaning_Crops.php">Crops</a></td>
        <td>Page with table for export of all crops</td>
      </tr>
      <tr>
        <td align="center"><a href="../PCD_Reports/PCD_Gleaning_Harvests.php">Harvests</a></td>
        <td>Page with table for export of all harvests in a selected year</td>
      </tr>
      <tr>
        <td align="center"><a href="../PCD_Reports/PCD_Gleaning_Contacts.php">Contacts</a></td>
        <td>Page with table for export of all volunteers</td>
      </tr>
      <tr>
        <td align="center"><a href="../PCD_Reports/PCD_Gleaning_ProjectSites.php">Sites</a></td>
        <td>Page with table for export of all gleaning sites</td>
      </tr>
		      <tr>
        <th align="center">Page Link</th>
        <th>Obscure Utilities</th>
      </tr>
      <tr>
        <td align="center"><a href="databasebu.php">Download database</a></td>
        <td>Compiles, saves and downloads the database for backup. <a href="../help/databasebu-help.php"  onClick="wopen('../help/databasebu-help.php', 'popup', 640, 480); return false;">Page Help</a></td>
      </tr>
      <tr>
        <td align="center"><a href="DatabaseSpecs.php">Database specs</a></td>
        <td>Lists database tables and fields.</td>
      </tr>
      <tr>
        <td align="center"><a href="users.php">Database users manager</a></td>
        <td>Add, update and delete database users. NOTE for registered volunteers, use ID_picker for the ID_user</td>
      </tr>
      <tr>
        <td align="center"><a href="ErrorLogs.php">Error logs viewer</a></td>
        <td><p>Displays error_log files in selected folders. Allows selective deletion of rows in the files. </p></td>
      </tr>
      <tr>
        <td align="center"><a href="loginviewer.php">Login viewer</a></td>
        <td>Miscellaneous webmaster data about logins</td>
      </tr>
      <tr>
        <td align="center"><a href="pageslogviewer.php">Pages tracking log</a></td>
        <td>Miscellaneous webmaster data about page views</td>
      </tr>
   </table>
    <p>&nbsp;</p>
    </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>