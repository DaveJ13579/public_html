<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>page help</title>
<link href="../database.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body class="SH">
<div id="help-container">
    <div id="narrower">
    <h2><strong>Download database help</strong></h2>
    <p>This link directly compiles the complete database into an sql file and prompts the user to save the file on the local computer. The file that is generated can be used to restore the complete database up to the time that it was compiled in the event of loss or corruption of the current database. The database is restored using the phpMyAdmin facility in the cPanel page. </p>
    <p>It is recommended that backups be done daily and that at least one week's worth of files be kept at all times. The file name will automatically have the day of the week appended. </p>
    <p><strong>To restore the database in to a previous state</strong></p>
    <p>First be sure that you have a file with the latest backup of the database. Restoring the database requires deleting all the records in the current one. </p>
    <p>Log on to the <a href="http://www.piercecountygleaningproject.org:2082/frontend/x3/index.html">cPanel</a> page with the username twpalygj and the assigned password.</p>
    <p>Scroll down to the Databases section and click on phpMyAdmin. A page opens that is used for direct access to the Harvest Pierce County's Gleaning Project database.</p>
    <p>Select the _piercecty database on the left side list. The main section will show a list of all the tables in that database.</p>
    <p>Below the list click on 'Check All' and then select 'Empty' from the drop-down list. </p>
    <p>Click the 'Import' tab above the list of database tables. Under 'File to Import' click 'Browse' and find the backup file on your computer. Finally, click the 'Go' button at the bottom.    </p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
