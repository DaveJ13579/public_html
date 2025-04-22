<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "all,change,view";


$MM_restrictGoTo = "../login.php";
require_once('../includes/levelcheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>pages list</title>
    <style type="text/css">
<!--
-->
    </style>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
<!--
//-->
</script>
</head>

<body class="SH">
<div id="container">
<?php include_once('../includes/AdminNav2.inc.php');?>
<div id="mainContent">
    <h3><strong>Annotated index of non-public web pages</strong></h3>
    <table width="1200" border="2" cellpadding="2" cellspacing="10">
      <tr>
        <th width="200" height="39" align="center">Page (Root directory)</th>
        <th>Description</th>
      </tr>
      <tr>
        <td align="center" id=tdlink >cancel.php</td>
        <td>Accessed from coded link sent to volunteers when they sign up for harvests. Checks several contingencies and changes harvest roster entry to 'cancel.'</td>
      </tr>
       <tr>
        <td align="center">confirm.php</td>
        <td>Confirms pickers' request to be added to the waiting list for a harvest. Adds the picker to the waiting list</td>
      </tr>
       <tr>
        <td align="center">harvestroster-selectpages.php</td>
        <td>Allows custom design of the printed packet for a harvest. Select pages and page breaks.</td>
      </tr>
      <tr>
        <td align="center">waitstatus.php</td>
        <td>Looks up picker's harvests for which he is on the waiting list. Shows the position on the waiting list for each.
	</td>
      </tr>
      <tr>
        <th width="200" height="39" align="center">Page (Utilities folder)</th>
        <th>Description</th>
      </tr>
      <tr>
        <td align="center" id=tdlink onmouseover="MM_changeProp('tdlink','','backgroundColor','#cccc66','TD')" onmouseout="MM_changeProp('tdlink','','backgroundColor','#ffff88','TD')">attendance-data.php</td>
        <td>Sortable lists of harvest attendance information. Allows primary and secondart sorts on all fields.</td>
      </tr>
      <tr>
        <td align="center">cropupdate.php</td>
        <td>Input crop ID number and update crop information.</td>
      </tr>
      <tr>
        <td align="center">duplicates.php</td>
        <td>Finds duplicates in the picker list.</td>
      </tr>
      <tr>
        <td align="center">EmailLists-Excel format.php</td>
        <td>Gernerates email list from pickers and rosters in Excel format.</td>
      </tr>
      <tr>
        <td align="center">EmailLists.php</td>
        <td>Gernerates email list from pickers and rosters in  format for pasting into email client.</td>
      </tr>
      <tr>
        <td align="center">EntryAuthorization.php</td>
        <td>Accepts query string as '?crop=xx' and displays custom Entry Authorization for printing</td>
      </tr>
      <tr>
        <td align="center">FarmHarvestPlanning.php</td>
        <td>Accepts query string as '?crop=xx' and displays custom Harvest Planning/Scouting form for printing</td>
      </tr>
      <tr>
        <td align="center">harvestdelete.php</td>
        <td>Accepts query string as '?harvesttemp=xx' and deletes the harvest</td>
      </tr>
      <tr>
        <td align="center">harvestupdate-paper.php</td>
        <td>Input harvest number and update paperwork fields. </td>
      </tr>
      <tr>
        <td align="center">harvestupdate.php</td>
        <td>Input harvest number and update harvest fields</td>
      </tr>
      <tr>
        <td align="center">how_heard.php</td>
        <td>Displays and categorizes 'how heard about Harvest Pierce County's Gleaning Project' table entries from picker registrations.</td>
      </tr>
      <tr>
        <td align="center">IntervalReport.php</td>
        <td>Input date interval and see stats on harvests, pickers, rosters, attendance. </td>
      </tr>
      <tr>
        <td align="center">IPfinder.php</td>
        <td>Input IP address and find same in roster signups and pickr registrations.</td>
      </tr>
      <tr>
        <td align="center">ladderlog.php</td>
        <td>DEMO: Simple page for checking ladders in and out.</td>
      </tr>
      <tr>
        <td align="center">ldrexists.php</td>
        <td>OBS: Message page when an existing leader is entered into leader table</td>
      </tr>
      <tr>
        <td align="center">ldrnopicker.php</td>
        <td>OBS: Message when an leader that is not a picker is inserted into the leader table.</td>
      </tr>
      <tr>
        <td align="center">ldrnotexists.php</td>
        <td>OBS: Message page that a leader in not in the table.</td>
      </tr>
      <tr>
        <td align="center">leaderinsert.php</td>
        <td>OBS: Add a leader to the leader table.</td>
      </tr>
      <tr>
        <td align="center">leaderupdate.php</td>
        <td>OBS: update information about a leader.</td>
      </tr>
      <tr>
        <td align="center">loginviewer.php</td>
        <td>Web master page for viewing login attempts.</td>
      </tr>
      <tr>
        <td align="center">merge.php</td>
        <td>Linked from the 'duplicates.php' page that merges duplicate picker entries by replacing old info with new info and changing new roster entries to old numbers to consolidate them.</td>
      </tr>
      <tr>
        <td align="center">PagesIndex.php</td>
        <td>Index and descriptions of the main database viewing and updateing pages. </td>
      </tr>
      <tr>
        <td align="center">PHPconfiguration.php</td>
        <td>Displays the PHP server configuration.</td>
      </tr>
      <tr>
        <td align="center">pickerdelete.php</td>
        <td>From a query string, deletes a picker from the database. linked from 'pickerupdate.php'.</td>
      </tr>
      <tr>
        <td height="41" align="center">pickerfind.php</td>
        <td>Input name or ID number and get list of matching pickers.</td>
      </tr>
      <tr>
        <td align="center">pickerupdate.php</td>
        <td>Input picker ID number and update information on that picker.</td>
      </tr>
      <tr>
        <td align="center">Reports.php</td>
        <td>Utility for designing, saving, modifying SQL reports on the database tables. Includes linked list of those in the Reports Library.</td>
      </tr>
      <tr>
        <td align="center">roster-duplicates.php</td>
        <td>Finds duplicate entries in rosters.</td>
      </tr>
      <tr>
        <td align="center">rosterdelete.php</td>
        <td>.Deletes an entry from a roster.</td>
      </tr>
      <tr>
        <td align="center">rosterdupdelete.php</td>
        <td> Deletes an entry from a roster. Linked from 'roster-duplicates.php'</td>
      </tr>
      <tr>
        <td align="center">rosterinsert.php</td>
        <td>Adds a record to the roster table. Should be used only rarely since the liability release has not been checked off.</td>
      </tr>
      <tr>
        <td align="center">rosterupdate-attendance.php</td>
        <td>Quick method for entering attendance for one harvest. Allows abbreviated entries and updates all records at once. </td>
      </tr>
      <tr>
        <td align="center">rosterupdate.php</td>
        <td>Changes records in the roster table. </td>
      </tr>
      <tr>
        <td align="center">rosterviewer.php</td>
        <td>Displays extracts from the roster table.</td>
      </tr>
      <tr>
        <td align="center">seasonplanner.php</td>
        <td>Displays one season's harvests sortable by harvest date, ripe date, or harvest leader. Allows changes to harvest and crop info</td>
      </tr>
      <tr>
        <td align="center">SQL-library.php</td>
        <td>List of saved reports generated with Reports.php</td>
      </tr>
      <tr>
        <td align="center">surveyresults-owners.php</td>
        <td>OBS: Displays results of tree owner's survey from 2010</td>
      </tr>
      <tr>
        <td align="center">surveyresults-respondent.php</td>
        <td>OBS: Displays 2010 picker survey results collated by respondent.</td>
      </tr>
      <tr>
        <td align="center">surveyresults.php</td>
        <td>OBS: Displays 2010 picker survey results collated by question.</td>
      </tr>
      <tr>
        <td align="center">TextingList.php</td>
        <td>OBS: Retrieves cell numbers from pickers table and stores them as celltext.txt and allows downloading the text file.</td>
      </tr>       
      <tr>
        <td align="center">waitinglist-manager.php</td>
        <td>Utility for promoting pickers from the waiting list to the regular roster.</td>	
      </tr>
      <tr>
        <th height="39" align="center">Page (MailAPI folder)</th>
        <th>Description</th>
      </tr>
      <tr>
        <td align="center">eMailer</td>
        <td>Email utility that can look up addresses in volunteer, owner, and maillist tables, or in harvest rosters, or in several predefined groups. </td>
      </tr>
      <tr>
        <td align="center">emails</td>
        <td>Folder containing html text for harvest announcements and email address lists or upload to GraphicMail. </td>
      </tr>
      <tr>
        <td align="center">CompileEmail.php</td>
        <td>Compiles harvest announcement email, saves it, uploads and sends to two lists: pickerlist0 and pickerlist1.</td>
      </tr>
      <tr>
        <td align="center">GraphicmailAPIlinks.php</td>
        <td>Displays links to retrieve info from GraphicMail about lists and messages.</td>
      </tr>
      <tr>
        <td align="center">Purge.php</td>
        <td>Retrieves all email lists, extracts unsubscribed and hard-bounced addresses, deletes these from the pickers table and purges them from the GraphicMail email lists. </td>
      </tr>
      <tr>
        <th height="39" align="center">Page (includes folder)</th>
        <th>Description</th>
      </tr>
    
      <tr>
        <td align="center">AdminNav.inc.php</td>
        <td>Navigation block for database pages. Has three versisons ..Nav1, ..Nav2, ..Nav3 that are used depending on the folder depth of the file that is 'inclucing' them. This means that any changes need to be made to all three files..</td>
      </tr>
      <tr>
        <td align="center">dencode.inc.php</td>
        <td>Two functions to encode and decode picker ID numbers to obscure query strings on roster cancel links. Code is: Used in signup.php, resend email, cancel.php and ContactUpdate.php.</td>
      </tr>
      <tr>
        <td align="center">PagesLog.inc.php</td>
        <td>Adds record to pageslog table when a page is loaded.</td>
      </tr>
      <tr>
        <td align="center">pagetracker.inc.php</td>
        <td>Sends email when a page is loaded.</td>
      </tr>
      <tr>
        <td align="center">sqlcleaner.php</td>
        <td>function for cleaning form fields for mysql entry</td>
      </tr>
      <tr>
        <td align="center">footer.inc.php</td>
        <td>div for copyright notice footer on public pages</td>
      </tr>
      <tr>
        <th height="39" align="center">Page (Owners folder)</th>
        <th>Description</th>
      </tr>
      
      <tr>
        <td align="center">owners-sendsurvey.php</td>
        <td>Allows editing and emailing a pre-composed request to crop owner to fill out a survey about a harvest. Includes coded link to the survey. Linked directly from the Season Planner. Flags the harvest record as 'seurveysent=Yes'</td>
      </tr>
      <tr>
        <td align="center">Survey-owners.php</td>
        <td>Survey form for crop owners about a particular harvest. Checkbox for allowing use of the comments. Stores result in table and sends result to harvest directors and harvest leader</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    </div>
  <!-- This clearing element should immediately follow the #mainContent div in order to force the #container div to contain all child floats -->
  <br class="clearfloat" />
  <!-- end #container -->
</div>
</body>
</html>