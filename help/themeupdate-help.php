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
    <h2><strong>Theme update help</strong></h2>
    <p>This page is for editing the color theme of the website. </p>
    <p><strong>The Page</strong></p>
    <p>At the top left is a drop down menu for selecting from themes that have been stored in a database table. Selecting a theme and then clicking on the 'Show theme' button loads that theme from the database into the Theme update page. The colors of the theme that was selected appear in the page. </p>
    <p>The page elements whose color can be edited are listed down the left side. Next to the description of the page elements is a table cell filled with  a color and showing the hex format of the color. By clicking in the cell, the hex format of the color can be changed. A color picker palette also opens and a color can be selected by clicking on the palette. </p>
    <p>After colors are selected for page elements, clicking on either of the 'Update theme' buttons stores the new colors in the database table and changes the colors of those page elements in the sample page on the right side. This sample page displays all of the elements in roughly the positions that they appear on public pages so that color combinations can be assessed. The database tables are not updated until 'Update theme' is clicked on.</p>
    <p>The name of the currently displayed theme can be changed by typing in the form field at the top-left and then clicking the 'Update theme' button. This does not yet change the current, live theme that is used for the public web pages.</p>
    <p>New themes can be added to the database. First copy the currently displayed theme by clicking on the 'Copy theme' button. The new theme will be loaded with the same name and colors, but the number in square brackets shows that it is a different theme. Then, change the name and colors and click on 'Update theme.' The new theme is updated in the database and will appear on the drop down menu. </p>
    <p>A theme can be deleted after it is loaded by clicking on 'Delete theme.' Be sure that you have actually loaded the theme that you want to delete. You cannot delete the 'current, live theme in slot [1].'</p>
    <p>After all editing has been done and the theme has been updated in the database, the changes can be made to the actual live stylesheet that is used to display the colors on the website's public pages. Click on the 'Update the live stylesheet' buttton. All public pages will now show the colors that have been chosen in the current theme (refreshing pages may be necesssary for the changes to show up). Not that the new, current, live theme is also copied and stored as 'Current live theme' in the first slot [1] of the database table. This means that there is always a saved copy of the current, live theme. If you are experimenting with changes to the current, live theme, but lose track of the changes and want to restore the original, just select and load ('show theme') the copy of the current one, click on the 'Update the live stylesheet' buttton and the theme in slot [1] will again match the current live theme and its copy.</p>
    <p><strong>Technical details</strong></p>
    <p>The retrieval, update and storage of themes into the database is straighforward. All the colors are simply saved as separate fields in the table named 'custom3.' Changing the current live stylesheet is trickier. The code for the themeupdate page includes a template of the stylesheet with color number values replaced by the column name of the color in the database table. In the stylesheet template you will see lines like: '#sidebar	{ background: #sidebg1; } where 'sidebg1' is the name of the database table column 'sidebg1' which stands for 'sidebar background gradient top.' When the live stylesheet is changed, 'sidebg1' is replaced by the color number such as 'ca1254'. </p>
    <p>This means that if changes to the structure of the live stylesheet are made, or stylesheet elements are added, the exact same changes <em><strong>must</strong></em> also be made to the stylsheet template in the code of this page. If it is not, then when the theme is later changed the newly generated stylesheet will will not have the more recent structure changes. It might be best to always make stylesheet changes by doing them directly in the code for the Theme update page and then generating a new stylesheet from that. </p>
    <p>The template code is a string in double-quotes that is directly assigned to a variable. The contents of the template must not not contain double-quotes for things such as 	font-family: 'Times New Roman', Times, serif;. Always use single-quotes inside the stylesheet template.</p>
    <p>If a stylesheet needs to be changed by adding new, editable color values these steps must be taken:<br />
      1) Add the name of the field as a new column to the custom3 table, including its description in the Comments field.<br />
      2) Add a sample of the element, with its style information to the sample page, <br />
      3) Add the style code to the template string using the name of the color field in place of a color number, <br />
      4) Add the field to the mysql database update code above the html, <br />
      5) Load a theme,<br />
    6) change the color of the new field, update the theme, generate a new live stylesheet.
    </p>
    <p><br />
    </p>
    <p>&nbsp;</p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
