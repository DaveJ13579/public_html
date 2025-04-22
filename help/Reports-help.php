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
        <h2><strong>Report Generator help</strong></h2>
        <p>The Report Generator draws information from all the different database tables and displays it in a table that can be viewed and copied. The <strong>sql query</strong> that produces the report can be modified and saved as a new report. </p>
        <p>The form field below the heading shows the title of the current report. </p>
        <p>The box below the title shows the <strong>sql query</strong> that produces the current report. If these two fields are blank then no report has been loaded. </p>
        <p>The Reports Library always is shown at the bottom of the page. It is a list of the titles of all reports that have been saved. The titles are links that, when clicked on, load the report intot he report generator. </p>
        <p><strong>Filtering the library</strong></p>
        <p>The titles of reports in the Reports Library can be filtered. Enter one or more words without commas to search for that term. Enter two words separated by a comma to search for all titles with either word.</p>
        <p>Examples:<br />
          </p>
        <p>- Entering 'leader' (without quotes) filters the library  down to just the reports with that word in the title.<br />
        </p>
        <p>- Entering 'pounds per picker' (without quotes) filters to  just those reports with that exact phrase in the title.<br />
        </p>
        <p>- Entering 'pounds, leader' (without quotes) yields all reports  with 'pounds' or 'leader' in the title.</p>
        <p><strong>Organizing the library</strong></p>
        <p>You can organize the Reports Library by defining categories of reports. These are like folders, but a report may be in more than one folder. Categories are defined by putting the name of a category at the end of the title. Put a plus sign (+) before and after each category name. The category name can be more than one word. A report title may have any number of categories attached to it. The categories that you include in report titles will appear in a table at the start of the Reports Library. Categories  can be whatever you want. Possibilities are: all the reports that you use to prepare an annual report, or all the reports that one person most commonly uses. </p>
        <p>If there are categories defined, then the Reports Library is headed by a table that includes the different categories and the reports that are in them. All of the reports that are not in any category follow the table in a list.</p>
        <p><strong>Showing a report</strong></p>
<p>After a report has been loaded (or written from scratch), clicking on 'Show this report' generates the report in a table. The table can be copied and pasted elsewhere, if needed.</p>
        <p>The sql query for a report can be changed to make a different report. After changing the sql query, click on 'Show this report' to see the results of the change. If the sql query or title is changed, for instance to change a date range, it can then be saved as a new report by clicking in the third button. </p>
<p>For some signons, two other buttons can be seen: 'Save changes to report'  and 'Delete.' </p>
        <p>Most changes to reports will require detailed knowledge of the database structure, but many details can be changed without such knowledge, for instance dates, names, crops, etc. Most of these easy changes will be to elements in the sql query that are enclosed in single quotation marks.</p>
        <p>Some signons can also use 'copy', 'update' and 'delete' commands. Update and delete use standard MySql format. The format for 'copy' is:</p>
        <p>copy tablename where primarykey=xxx</p>
        <p>to copy one record from tablename that has a primary key index number of xxx. </p>
        <p><strong>Writing report queries</strong></p>
        <p>A query is the string of words and symbols that appears in the main form field under the title. Many of the queries that generate the report can be modified slightly for other conditions. The situations that are the easiest to recognize are dates in clauses such as '...where h_date&gt;'2012-06-01'.' h_date is the field name for 'harvest date' and this fragment limits the report to harvests that happened after June 1, 2012. Changing the date makes a different report. Other parts of queries can be changed with a little knowledge of the fields that organize the tables in the database. A table of the fields for the four main tables is at the end of this help file.</p>
        <p>Report queries use the mysql syntax. Many tutorials are available on the internet. Using those resources, this guide, and looking closely at the structure of queries that are already inthe library you can begin to write your own. Here is the basic structure of a query:</p>
        <ul>
          <li><strong>'Select'</strong> -  All scripts begin with the word 'select.' A query is a command to select data from the database tables. </li>
          <li><strong>Fields list</strong> - After 'select' comes a list of the fields that you want in the report. These show up as the headings of the table columns of the report. Field names are separated by commas. The table at the end of this help file lists all of the table fields that you can write queries about. </li>
          <li><strong>Tables list</strong> - A list of tables that those fields whose data you want are found in. Table names are separateed by commas.</li>
          <li><strong>'Where'</strong> - You rarely want all the data in a table. The 'where' clauses of the query limit the data to just what you need. 'Where' conditions are connected by the word <em>and.</em></li>
          <li><strong>'Group by'</strong> - When you want data summarized, your query can group it by fields that you specify.</li>
          <li><strong>'Order by'</strong> - The order that the data appear in the table can be specified.</li>
          <li><strong>'Limit' </strong>- The maximum number of rows that you want in the report table.</li>
        </ul>
        <p><strong>Example 1: <em>select tlname, address from sites where crop_type='cherries' and city='Salem'</em></strong></p>
        <p>This query lists owner's last name and the crop address all of the cherry crops that are located in the city of Salem. Looking in the fields table at the end of this help file, you will see that 'tlname' is the name of the field for the owner's last name. The other field names are obvious in this query.</p>
      <p><strong>Example 2: <em>select ID_picker, fname, lname, leader from pickers where leader&lt;&gt;''</em></strong></p>
      <p>This query lists registered volunteers' first and last names and their interest in being a harvest leader if they have one and checked that box when they registered. The last term would be read as &quot;where the leader field is not equal to an empty string.&quot;</p>
      <p><strong>Example 3: <em>select year(h_date), count(ID_harvest), sum(weight) from harvests where pick_num&gt;10 and h_date&lt;'2012-12-31' group by year(h_date)</em></strong></p>
      <p>This query lists the year of the harvest dates, the number of harvests and sum of the weight of produce donated for harvests that had more than 10 pickers and happened before the year 2013. The list is grouped by the harvest year so the sum is calculated for each year and the number of harvests in each group (year) is counted.</p>
      <p><strong>Example 4: <em>select year(h_date), rosters.status, count(rosters.status) from rosters, harvests where rosters.ID_harvest=harvests.ID_harvest and h_date&lt;'2012-01-01' group by year(harvests.h_date), rosters.status order by year(harvests.h_date), rosters.status</em></strong></p>
      <p>This example shows how to extract data from two different tables at once. First, this query lists the year of the harvest date, the roster status (absent, harvested, etc.), and the number of volunteers in each status category and groups the results by year of the harvest and roster status. The list is limited to those harvests before 2012. It puts the list in order of, first, the year of the harvest and then the roster status.</p>
      <p>Because information from two different tables is sought, a clause in the 'where' section is needed to join the tables together. That can be done with these two tables because both tables have a field for the number of the harvest,  <br />
        ID_harvest. We need to get the roster status from the rosters table and the harvest date from the harvests table. Every row of the rosters table includes a field that says which harvest that row is from. The tables are joined by the clause <strong><em> where rosters.ID_harvest=harvests.ID_harvest</em></strong> that shows the field that is common to both tables. Also, because two tables are used, the name of the table is added to the field names as in <em><strong>rosters.status</strong></em>. </p>
      <p>This query outputs this table for Salem Harvest:</p>
      <table cellpadding-left="4" id="rsearchresults" border="1" cellspacing="2">
        <tbody>
          <tr>
            <th colspan="6">Roster status summary by year prior to 2012</th>
          </tr>
          <tr>
            <td colspan="6"> </td>
          </tr>
          <tr>
            <th>line #</th>
            <th>year(h_date)</th>
            <th>status</th>
            <th>count(rosters.status)</th>
          </tr>
          <tr>
            <td>1</td>
            <td>2010</td>
            <td>absent</td>
            <td>565</td>
          </tr>
          <tr>
            <td>2</td>
            <td>2010</td>
            <td>cancel</td>
            <td>60</td>
          </tr>
          <tr>
            <td>3</td>
            <td>2010</td>
            <td>harvested</td>
            <td>1419</td>
          </tr>
          <tr>
            <td>4</td>
            <td>2010</td>
            <td>intake</td>
            <td>185</td>
          </tr>
          <tr>
            <td>5</td>
            <td>2010</td>
            <td>leader</td>
            <td>65</td>
          </tr>
          <tr>
            <td>6</td>
            <td>2011</td>
            <td>absent</td>
            <td>652</td>
          </tr>
          <tr>
            <td>7</td>
            <td>2011</td>
            <td>cancel</td>
            <td>192</td>
          </tr>
          <tr>
            <td>8</td>
            <td>2011</td>
            <td>harvested</td>
            <td>1537</td>
          </tr>
          <tr>
            <td>9</td>
            <td>2011</td>
            <td>intake</td>
            <td>194</td>
          </tr>
          <tr>
            <td>10</td>
            <td>2011</td>
            <td>leader</td>
            <td>86</td>
          </tr>
        </tbody>
      </table>
      <p>It is left as an exercise for the reader to determine what this query does:</p>
      <p><strong><em>select round(sqrt(abs(pow(70*(avg(pickers.latitude)-sites.latitude),2)-pow(50*(avg(pickers.longitude)-sites.longitude),2))),2) as miles, harvests.ID_harvest, crop_type from sites, pickers, harvests, rosters where pickers.ID_picker=rosters.ID_picker and rosters.ID_harvest=harvests.ID_harvest and harvests.ID_crop=sites.ID_crop and pickers.latitude&gt;44.7 and pickers.longitude&lt;-122.7 group by crop_type order by miles desc </em></strong></p>
      <p><strong>Custom inputs for sql queries</strong></p>
      <p>There is a custom extenskion to the sql language invented specifically for the Report Generator. Any value in a where clause, for instance the 68 in '...where ID_harvest=68...' or Dick in '...where fname='Dick'... can be assigned in a text field input or a drop-down list. The syntax is shown in this example:</p>
      <p><strong>Example 5: select fname from pickers where ID_picker&lt;|ID_picker,Picker number,20| and zip=|zip,zip code,97301|</strong></p>
      <p>In place of the 'where' value is a section that starts and ends with the pipe symbol '|' (This is found on the keyboard as shift-\). There are three parts to this section:</p>
      <p>The database table record name, for instance	h_date, or ID_picker.<br />
        The name of the field if it is a text input field. If a dropdown list is desired, then this part starts with 'dd-' followed by the database table name with the table record name. For instance, 'dd-sites'.<br />
        The default value to use if there is nothing in the input field. </p>
      <p>The example above will make two input fields: one labeled 'Picker number', using the record name ID_picker, and a default value of 20.</p>
      <p><strong>Example 6: select count(ID_crop) from sites where crop_type=|crop_type,dd-sites,'Apples'| group by crop_type</strong></p>
      <p>This example makes a drop-down list of crop types to select from and displays the number of such crops.</p>
      <h2><strong>The Four Main Database Tables and their Fields</strong></h2>
      <p>There are four main database tables: sites, pickers, harvests and rosters. Each field row shows the name of the field, whether it is a primary or secondary key or has a defualt value, the type of variable for that field, and a description of the field, sometimes with the format that is used for it. Secondary keys are indexes that are the primary keys in other tables and are used to join tables when queries require information from more than one table.</p>
      <table border="4" cellspacing="2" cellpadding="5" align="center">
          <col span="4" />
          <tr>
            <th width="44"><h3>Table</h3></th>
            <th width="114"><h3>Field name</h3></th>
            <th width="181"><h3>Key or Default value,</h3></th>
            <th width="142">Variable type</th>
            <th width="1004"><h3>Description</h3></th>
          </tr>
          <tr>
            <td><h3>&nbsp;</h3></td>
            <td><h3>&nbsp;</h3></td>
            <td><h3>&nbsp;</h3></td>
            <td>&nbsp;</td>
            <td><h3>&nbsp;</h3></td>
          </tr>
          <tr>
            <th colspan="2"><h3>sites</h3></th>
            <th><h3>&nbsp;</h3></th>
            <th>&nbsp;</th>
            <th><h3>registered crops (not just trees)</h3></th>
          </tr>
          <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2"><strong>ID_crop</strong></td>
            <td><strong>PRIMARY KEY</strong></td>
            <td rowspan="2">integer</td>
            <td rowspan="2"><strong>unique crop number</strong></td>
          </tr>
          <tr>
            <td><strong>auto_increment</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>tlname</td>
            <td> </td>
            <td>string</td>
            <td>owner last name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>tfname</td>
            <td> </td>
            <td>string</td>
            <td>owner first name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>tphone</td>
            <td> </td>
            <td>string</td>
            <td>owner phone</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>tphone2</td>
            <td> </td>
            <td>string</td>
            <td>owner alternate phone</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>temail</td>
            <td> </td>
            <td>string</td>
            <td>owner email</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>address</td>
            <td> </td>
            <td>string</td>
            <td>crop address</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>city</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>state</td>
            <td>&nbsp;</td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>zip</td>
            <td> </td>
            <td>integer</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>maddress</td>
            <td> </td>
            <td>string</td>
            <td>owner mailing address</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>mcity</td>
            <td> </td>
            <td>string</td>
            <td>owner mailing city</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>mstate</td>
            <td>&nbsp;</td>
            <td>string</td>
            <td>owner mailing state</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>mzip</td>
            <td> </td>
            <td>integer</td>
            <td>owner mailing zip</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>inlimits</td>
            <td> </td>
            <td>string</td>
            <td>In city limits? (Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>neighbor</td>
            <td> </td>
            <td>string</td>
            <td>general area </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>property_rel</td>
            <td> </td>
            <td>string</td>
            <td>owner or renter</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>landlord</td>
            <td> </td>
            <td>string</td>
            <td>text if renting</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>crop_type</td>
            <td> </td>
            <td>string</td>
            <td>type of crop (not specific    variety)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>crop_num</td>
            <td> </td>
            <td>string</td>
            <td>Amount (trees, acres, bushes)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>location</td>
            <td> </td>
            <td>string</td>
            <td>where on property</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>height</td>
            <td> </td>
            <td>intege</td>
            <td>highest produce</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>lowest</td>
            <td> </td>
            <td>integer</td>
            <td>lowest produce</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>productivity</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>when_ripe</td>
            <td> </td>
            <td>string</td>
            <td>mm-dd. This gets updated when    actual harvests occur</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>disease</td>
            <td> </td>
            <td>string</td>
            <td>(Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>disease_text</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>spray</td>
            <td> </td>
            <td>string</td>
            <td>(Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>spray_text</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>how_hear</td>
            <td> </td>
            <td>string</td>
            <td>How did you hear about Harvest Pierce County's Gleaning Project</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>other_info</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>latitude</td>
            <td> </td>
            <td>floating point</td>
            <td>latitude</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>longitude</td>
            <td> </td>
            <td>floating point</td>
            <td>longitude</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>regdate</td>
            <td> </td>
            <td>date</td>
            <td>yyyy-mm-dd Registration date</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>Active</td>
            <td> Yes</td>
            <td>string</td>
            <td>Crops wthdrawn from donation are marked 'No' rather than deleted</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>venue</td>
            <td> </td>
            <td>string</td>
            <td>(Backyard, Farm, Market, Pickup) </td>
          </tr>
          <tr>
            <th colspan="2"><h3><strong>pickers</strong></h3></th>
            <th><h3><strong> </strong></h3></th>
            <th>&nbsp;</th>
            <th><h3><strong>registered volunteers</strong></h3></th>
          </tr>
          <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2"><strong>ID_picker</strong></td>
            <td><strong>PRIMARY KEY</strong></td>
            <td rowspan="2">integer</td>
            <td rowspan="2"><strong>unique picker number</strong></td>
          </tr>
          <tr>
            <td><strong>auto_increment</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>lname</td>
            <td> </td>
            <td>string</td>
            <td>last name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>fname</td>
            <td> 
            <h3>&nbsp;</h3></td>
            <td>string</td>
            <td>first name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>phone</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>phone2</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>email</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>address</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>city</td>
            <td> </td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>state</td>
            <td> OR</td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>zip</td>
            <td> </td>
            <td>integer</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>dupname</td>
            <td> No</td>
            <td>string</td>
            <td>is there a duplicate name in the    picker table? (Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>neighbor</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>harvester</td>
            <td> Yes</td>
            <td>string</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>coordinator</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>board</td>
            <td> </td>
            <td>string</td>
            <td>board member? (Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>leader</td>
            <td> </td>
            <td>string</td>
            <td>interested in being a harvest    leader? (No, Yes, Ripe)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>intake</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>laddermover</td>
            <td> </td>
            <td>string</td>
            <td>can transport orchard ladders?    (Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>driver</td>
            <td> </td>
            <td>string</td>
            <td>MPFS approved truck driver? (Yes /    No / Pending )</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>ladder</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>scale</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>truck</td>
            <td> </td>
            <td>string</td>
            <td>Has a pickup truck to transport    produce? (Yes / No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>shortnotice</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>how_hear</td>
            <td> </td>
            <td>string</td>
            <td>how heard of the organization</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>other_info</td>
            <td> </td>
            <td>string</td>
            <td>general comments from the registration page</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>regdate</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd hh:mm:ss</em> Picker registration date</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>contactdate</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd hh:mm:ss</em> Most recent roster signup or    registration renewal date</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>waive_date</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd</em> Select Harvest Team yearly waiver    date</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>latitude</td>
            <td> </td>
            <td>float</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>longitude</td>
            <td> </td>
            <td>float</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>IP_picker</td>
            <td> </td>
            <td>string</td>
            <td>Most recent IP address from    signups or registration</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>cell</td>
            <td> </td>
            <td>string</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>ladderdate</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd</em> date took the ladder safety test</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>ladderscore</td>
            <td> </td>
            <td>integer</td>
            <td>best ladder safety test score</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>selectteam</td>
            <td> No</td>
            <td>string</td>
            <td>on the select harvest team? (Yes /    No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>emerg</td>
            <td>&nbsp;</td>
            <td>string</td>
            <td>emergency contact name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>ephone</td>
            <td>&nbsp;</td>
            <td>string</td>
            <td>emergency contact phone</td>
          </tr>
          <tr>
            <th colspan="2"><h3>rosters</h3></th>
            <th><h3>&nbsp;</h3></th>
            <th>&nbsp;</th>
            <th><h3>roster slots from all harvests</h3></th>
          </tr>
          <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2"><strong>ID_rosters</strong></td>
            <td><strong>PRIMARY KEY</strong></td>
            <td rowspan="2"><strong>integer</strong></td>
            <td rowspan="2"><strong>unique roster number</strong></td>
          </tr>
          <tr>
            <td><strong>auto_increment</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_harvest</strong></td>
            <td><strong>secondary key</strong></td>
            <td><strong>integer</strong></td>
            <td><strong>unique harvest number</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_picker</strong></td>
            <td><strong>secondary key</strong></td>
            <td><strong>integer</strong></td>
            <td><strong>unique picker number</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>lname</td>
            <td> </td>
            <td>string</td>
            <td>last name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>fname</td>
            <td> </td>
            <td>string</td>
            <td>first name</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>regdate</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd hh:mm:ss</em> Signup date</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>status</td>
            <td> signup</td>
            <td>string</td>
            <td>(signup, cancel, absent, leader,    intake, waiting)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>IPaddress</td>
            <td> </td>
            <td>string</td>
            <td>IP address of signup</td>
          </tr>
          <tr>
            <th colspan="2"><h3>harvests</h3></th>
            <th><h3>&nbsp;</h3></th>
            <th>&nbsp;</th>
            <th><h3>all harvests</h3></th>
          </tr>
          <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2"><h3><strong>ID_harvest</strong></h3></td>
            <td><strong>PRIMARY KEY</strong></td>
            <td rowspan="2">integer</td>
            <td rowspan="2"><strong>unique harvests number</strong></td>
          </tr>
          <tr>
            <td><strong>auto_increment</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_crop</strong></td>
            <td><strong> secondary key</strong></td>
            <td>integer</td>
            <td><strong>unique sites number</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_leader</strong></td>
            <td><strong> secondary key</strong></td>
            <td>integer</td>
            <td><strong>same as ID_picker in pickers table</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_leader2</strong></td>
            <td><strong> secondary key</strong></td>
            <td>integer</td>
            <td><strong>same as ID_picker in pickers table</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_scout</strong></td>
            <td><strong> secondary key</strong></td>
            <td>integer</td>
            <td><strong>not used</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>ID_coordinator</strong></td>
            <td><strong> secondary key</strong></td>
            <td>integer</td>
            <td><strong>same as ID_picker in pickers table</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>h_date</td>
            <td> 0000-00-00</td>
            <td>date</td>
            <td>harvest date in yyyy-mm-dd format</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>h_time</td>
            <td> </td>
            <td>time</td>
            <td>harvest time in hh:mm (24 hours)    format</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>weight</td>
            <td> </td>
            <td>integer</td>
            <td>donated weight </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>total_lbs</td>
            <td> </td>
            <td>integer</td>
            <td>total weight picked </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>pick_num</td>
            <td> </td>
            <td>integer</td>
            <td>picker number limit</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>SHT</td>
            <td> No</td>
            <td>string</td>
            <td>For only Select Harvest Team?    (Yes, No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>regdate</td>
            <td> </td>
            <td>date</td>
            <td>not used</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>where_to</td>
            <td> </td>
            <td>string</td>
            <td>food agency receiving produce</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>otherinfo</td>
            <td> </td>
            <td>string</td>
            <td>short description for the    harvestlist.php (harvest parties) page. </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>longinfo</td>
            <td> </td>
            <td>string</td>
            <td>Long info for the hthanks.php    page. Typically includes directions and special instructions.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>status</td>
            <td> closed</td>
            <td>string</td>
            <td>(closed, open, unscheduled)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>taxdate</td>
            <td> </td>
            <td>date</td>
            <td><em>yyyy-mm-dd</em> date tax donation receipt was    sent</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>scouting</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>entry</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>roster</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>assts</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>summary</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>donation</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>temppick</td>
            <td> </td>
            <td>string</td>
            <td>paperwork returned </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>surveysent</td>
            <td> No</td>
            <td>string</td>
            <td>flag for owner survey sent    (Yes/No)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>KeyRec</td>
            <td>&nbsp;</td>
            <td>integer</td>
            <td>5 digit integer for accounting</td>
          </tr>
        </table>
      <p>&nbsp;</p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
