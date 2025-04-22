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
        <h2><strong>Harvest Update help</strong></h2>
            <p>This page changes or adds information to an existing  harvest. Adding new harvests is done on the Harvest Insert page.</p>
            <p><strong>Filters</strong> can be used to display existing harvests.  Usually you will enter the number of the harvest that you want to update in the  first space and click on 'Display harvest info'. <br />
              You can also, if you are not sure of the harvest number,  instead enter the crop number, the leader number, or the date. Each of these  will list <em>all</em> of the harvests that meet those criteria, for instance all  of the harvests done by ar particular harvest leader. You can then scroll to  the one that you want and change or add information. If you do this rather than  putting in the harvest number, be sure that you update the right harvest. If  you change the wrong one, it could be very hard to figure out what needed to be  changed back.<br />
              After adding or changing information about a harvest,  click on 'Save changes' and the new information will be saved and redisplayed  so you can confirm the change.<br />
              <br />
  <strong>Fields</strong></p>
            <p><strong>Site ID</strong> - The number assigned on the crop list to  the crop that will be harvested. <br />
              <br />
            <strong>Leader, and Co-leader </strong> - Selected from a drop down menu of volunteers that have been marked as leaders in Volunteer Update. The leader and co-leader are also automatically entered into the roster for this harvest.</p>
            <p><strong>Leader carpool seats</strong> - the number of carpool seats that the leader will provide. This is entered into the roster automatically with the leader.</p>
            <p><strong>Harvest Date</strong> - Dates are entered using the format  'yyyy-mm-dd'. If the date is blank it will show up on the Season Planner as  'unscheduled.'</p>
            <p><strong>Time</strong> - The time of the harvest. This is entered from  a dropdown menu.<br />
              <strong> </strong><br />
              <strong>Duration – </strong>How  long the harvest will take.</p>
            <p><strong>Type</strong> –  What kind of harvest such as Field, Pickup.</p>
            <p><strong>Calculated weight</strong> - The number of pounds of  produce that were donated. It is derived by adding the weights of the separate  crops.</p>
            <p><strong>Weight adjustment – </strong>A positive or negative number that can be entered to adjust the total  weight when it is known that the weight calculated from summing the crops is  not correct.<br />
              <strong> </strong><br />
              <strong>Total weight</strong> – A calculated numberthat  is the calculated weight plus the weight adjustment.</p>
            <p><strong>Where donated</strong> - The food agency, where the produce  was donated or delivered. NOTE: This should be filled in ONLY when the place delivered-to is not on the dropdown menu on the Distributions page and cannot be added. Staff with suffucient database access level can see this and add the agency to the dropdown, assign the distribution there and, last, remove the text from this field.</p>
            <p><strong>Status</strong> - A harvest <em>must</em> also have a status  to be added to the harvest list. This can be: <strong>closed</strong> - This means that  the harvest will not be shown on the Harvest Parties page for public signup. <strong>open</strong> - This means that the harvest can be displayed on the public Harvest Parties  page. To be displayed there it must also have a leader assigned. Note: even if  a harvest has the status 'open' it will not appear on the Harvest Parties page  if all slots are filled or if the date of the harvest has passed..</p>
            <p><strong>Tax date</strong> - The date that the tax donation receipt  was sent to the crop owner. </p>
            <p><strong>Pre-signup info</strong> - This section is for text about  the harvest. It will be displayed in the Season Planner and so can be used for  short pieces of information before the harvest is scheduled, such as noting  that the harvest date is tentative, or who will be scouting the site. However,  when the harvest is opened for signup (status = 'open') then the text in this  section is shown with the open harvest list on the public Harvests page as a  short description of the crop and location so volunteers can decide if they  want to sign up.</p>
            <p><strong>Post signup info</strong> - This section is for text that  will appear on the harvest information page that volunteers see after they sign  up for the harvest. It will be inserted on that page under 'Specific  information for this harvest'. It typically includes directions to the harvest,  what to bring, and special considerations about the site or crop. </p>
            <p><strong>Meeting spot –</strong> the location the carpool participants will meet.</p>
            <p><strong>Carpool – </strong>Whether  there is no carpool, optional carpool, or required carpool.</p>
            <p><strong>Carpool time</strong> –  When the carpool will meet.</p>
            <p><strong>Key Rec#</strong> -  Accounting index to coordinate with another database of donations.</p>
            <p><strong>Delivered </strong>–  Whether the produce was delivered directly to a food pantry or was taken to the  warehouse.</p>
            <p><strong>Trip miles</strong> –  Distance traveled for the harvest.</p>
            <p><strong>Trip hours</strong> – Time  for the entire harvest trip.</p>
            <p><strong>In-kind miles</strong> –  Distance donated by volunteers.</p>
            <p><strong>Cars</strong> – Number of  volunteer cars used.</p>
            <p><strong>Extra hours</strong> – Additional  volunteer hours donated beyond the duration of the harvest itself.</p>
      <p><strong>Crop</strong> - Ten  fields for selecting the types of crops to be harvested.</p>
            <p><strong>Pounds</strong> – Weight  of each type of produce harvested.</p>
            <p><strong>Update site  address</strong> – Some sites have crops at different addresses, but the sites table  lists just one crop address. This field can be used to update the actual  address of the crop itself when it differs from what was previously entered  into the Sites table. It does the same thing as the Site Update page, but is  convenient here when a harvest is being posted.</p>
<p>There is a checkbox for &quot;Include Google Maps directions after signup:&quot; When a person signs up for a harvest they are sent to a page (hthank.php) that gives details about the harvest including the text of &quot;Long info.&quot; Traditionally this text has included directions to the harvest. If the checkbox is left checked then, when the hthank.php page is produced it will use the pickers' registered address and the harvest address to look up custom driving directions on Google Maps from the picker's house to the harvest, and insert them on the page. The option to uncheck this is there because there may be occasions when Google Maps cannot do that correctly. If this is the case, then simply unchecking the box in &quot;Harvest update&quot; will leave those directions off the page. It is recommended to leave the box checked and then sign up for the harvest (using the direct link) before opening the harvest and seeing if the directions are correct.</p>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
