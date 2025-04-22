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
    <h2><strong>Jobs Manager</strong></h2>
    <p>This page is for editing the master list of assistants' jobs. Changes to the existing entries in this list should be very rare after initial configuration. If the names of jobs are changed, then those changes are reflected in <em><strong>all previous</strong></em> rosters.</p>
    <p>There are slots for twelve jobnames and descriptions. The descriptions appear on the Signup page. Add new jobs in empty spaces by typing the one-word job name and the text to appear onthe Signup page, and clicking on 'Update.' </p>
    <p>------------------------ </p>
    <p><strong>Technical details</strong></p>
    <p>The database table named 'jobs' contains 12 entries indexed 1-12 with a name for each entry ('jobname'). The Signup page captures the jobs signed up for and compiles them into a 12 character string to store with the roster row. The jobs string with none selected is '000000000000'. With job 1 selected it is '100000000000', and so on. The roster pages unpack this string to display the job names for the harvest leader.</p>
    <p>&nbsp;</p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
