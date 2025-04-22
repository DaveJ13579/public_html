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
        <h2><strong>eMailer help</strong></h2>
        <p>The eMailer utility looks up names and email addresses from several sources in the database and allows the user to compose and send emails without using a separate email program. It is subject to several limits on the number of emails that restrict its use for mass mailings: There is an hourly limit of 2000 mails. There is  another limit of 210 mails per each 5 minutes. The maximum number of email  addresses recipients in one email is 50. The eMailer notifies you only of this last limit. If the other limits are exceeded, the server will not send anything even if the eMailer thinks that it has.</p>
        <p>There are three divisions of the page:</p>
        <p><strong>Email Directory</strong></p>
        <p>Five sources of names and addresses can be searched:</p>
        <p><strong>Groups:</strong> A drop-down list allows selecting whole groups: Salem Harvest Board, MPFS drivers, Ladder movers, Harvest leaders, Database users and the Select Harvest Team. Selecting a group and clicking on 'add' adds the members of that group to the 'to' list of recipients of the email. Other groups or individuals can be further added to the recipients.</p>
        <p><strong>Rosters: </strong>Enter a harvest number and selecting a roster status category from the drop-down list. Click on 'add' and the volunteers on the roster for that harvest are added  to the 'to' list of recipients of the email. The options include the usual categories of roster status: <strong>signup</strong>, <strong>harvested</strong>, <strong>intake</strong>, <strong>cancel</strong>, <strong>leader</strong>, <strong>waiting</strong>, <strong>added</strong> and <strong>absent</strong>. There are three other options that combine these:</p>
        <blockquote>
          <p><strong>All on roster:</strong> Every line on the roster for that harvest including the seven types listed above.<br />
            <strong>All attended:</strong> After the harvest, this includes leader, intake, added and harvested, but not cancel, absent or waiting.<br />
            <strong>All expected:</strong> Before the harvest, everyone that is expected to attend including signup, intake and leader. </p>
        </blockquote>
<p><strong>Individuals:</strong> Enter all or part of a first or last name, even just one letter, and then click on 'find.' All matching volunteers, crop owners and mailing list people are listed below. Clicking on 'add' next to those matching names adds them to the 'to' list of recipients of the email.</p>
        <p><strong>Pasted list:</strong> This form field accepts a portion of a table pasted from the Reports Generator. It is for customized lists of names and email addresses that are generated from the database tables. The requirements for the list are:</p>
        <ul>
          <li> The first three columns in the table are the first name, the last name, and the email address in that order.</li>
          <li>The table headings are <em><strong>not</strong></em> copied. </li>
          <li>Copy full rows, for example:</li>
          <li>
            <table cellpadding-left="4" id="rsearchresults" border="1" cellspacing="2">
              <tbody>
                <tr>
                  <td>Tom</td>
                  <td>Thomas</td>
                  <td>tthomas@msn.com</td>
                </tr>
                <tr>
                  <td>Sam</td>
                  <td>Smith</td>
                  <td>piercecty@gleanweb.org</td>
                </tr>
                <tr>
                  <td>John</td>
                  <td>Jones</td>
                  <td>piercecty@gleanweb.org</td>
                </tr>
              </tbody>
            </table>
          </li>
          </ul>
        <p>After pasting the list into the form, click on 'to' or 'bcc' as needed. Check the 'to' and 'bcc' lists to make sure that everything was added correctly.</p>
        <p><strong>Attachment</strong></p>
        <p> One file can be attached to emails. Click 'Browse...' to find a file on your computer, then 'Upload File.' Only .jpg, .txt, .rtf, .doc and .pdf files less than 1MB in size can be attached.</p>
<p><strong>Headers</strong></p>
        <p>The middle section of the page contains the field for entering the email subject, the sender name and email address (which is derived from the user login), and the list of email recipients.</p>
        <p>Each recipient can be removed from the list by clicking on 'remove.'</p>
        <p>The entire list of recipients can be cleared by clicking on 'Clear 'to' list and 'bcc' lists.'</p>
        <p><strong>Message</strong></p>
        <p>The left section of the page has the field for the email message. When there are recipients selected, and a message subject entered, and a message typed, clicking on 'Send group email' will send the email to everyone on the 'to' and 'bcc' lists and also send a copy to the sender.</p>
        <p>Emails to several people can be sent individually rather than as one 'to' list. To do this, put all addresses in the 'to' list; do not put any addresses in the 'bcc' list. Compose the message and click on 'Send individual emails.' A message sent this way can also be personalized with the recipient's first name. Compose the message with the percent character ('%') where you want it to be replaced by the first name. For instance: Dear %, how are you? Only the first occurrence of the % symbol will be replaced with the first name.</p>
        <p>When the individual emails option is chosen, a copy is <em><strong>not</strong></em> automatically sent to the sender. If you want a copy, add yourself to the 'to' list before sending. </p>
        <p>The box at the bottom of this section shows messages as actions are taken by the user.      </p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
