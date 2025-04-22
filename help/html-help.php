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
        <h2><strong>Formatting text</strong></h2>
        <div class="indented">
          <p>Text to be inserted into pages can be typed normally. However, it will all run together into one paragraph. You can insert <strong>HTML tags </strong>with the text to format it into bold, italic, separated paragraphs, indented blocks, links and even tables. There are many websites where you can learn about all of the html tags. Here we will show the basic ones and how to use them. </p>
          <p>Most HTML tags are used in pairs. One goes in front of the text you are formatting to show where that format starts, and the other goes at the end to show where it stops. Each one of a pair of HTML tags is enclosed in angle brackets. 
            A 
          starting tag for a paragraph looks like this: &lt;p&gt;. The ending tag for a paragraph is: &lt;/p&gt;. It is the same as the opening tag but with a slash after the first angle bracket. The following table shows the tag names, their effect, and an example. </p>
          <table  border="1">
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Effect</th>
              <th scope="col">Tag</th>
              <th scope="col">Example</th>
            </tr>
            <tr>
              <td>paragraph</td>
              <td>Groups sentences and puts a blank line before and after them.</td>
              <td>&lt;p&gt;..text...&lt;/p&gt;</td>
              <td><p>Two. Sentences.</p><p>One More.</p></td>
            </tr>
            <tr>
              <td>break</td>
              <td>Ends the line and starts a new line. Notice that this tag is used by itself, not in pairs.</td>
              <td>&lt;br /&gt;</td>
              <td>Line 1.<br />Line 2.</td>
            </tr>
            <tr>
              <td>strong</td>
              <td>Bold text.</td>
              <td>&lt;strong&gt;...text...&lt;/strong&gt;</td>
              <td><strong>...text...</strong></td>
            </tr>
            <tr>
              <td>emphasis</td>
              <td>Italic text</td>
              <td>&lt;em&gt;...text...&lt;/em&gt;</td>
              <td><em>...text...</em></td>
            </tr>
            <tr>
              <td>heading</td>
              <td>Large text</td>
              <td>&lt;h2&gt;...text...&lt;/h2&gt;</td>
              <td><h2>...text...</h2></td>
            </tr>
            <tr>
              <td>blockquote</td>
              <td>Indents and groups sentences.</td>
              <td>&lt;blockquote&gt;...sentences...&lt;/blockquote&gt;</td>
              <td>A sentence.<blockquote>Indented text.</blockquote>
              Back to normal.</td>
            </tr>
            <tr>
              <td>Links</td>
              <td>Hyperlinked text</td>
              <td>&lt;a href=&quot;The-full-url-goes-here&quot;&gt;Name of link&lt;/a&gt;</td>
              <td><a href="http://www.gleanweb.org">GleanWeb</a></td>
            </tr>
            <tr>
              <td>Image</td>
              <td>Links to photos and other images</td>
              <td>&lt;img src=&quot;The-full-url-goes-here&quot; height=25px&quot;&gt;&lt;/img&gt;</td>
              <td><img src="http://www.gleanweb.org/images/buttons/SalemHarvest.png" height="25px"></img></td>
            </tr>
            <tr>
              <td>Lists</td>
              <td>Indented and bulleted lists</td>
              <td>&lt;ul&gt;<br />
                &lt;li&gt;The first item in the list.&lt;/li&gt;<br />
                &lt;li&gt;The second item in the list&lt;/li&gt;<br />
                &lt;/ul&gt;<br />
              </td>
              <td><ul>
					<li>The first item in the list.</li>
					<li>The second item in the list</li>
			  </ul></td>
            </tr>
          </table>
          <p>HTML tags can be combined, but must always be nested correctly.</p>
          <table  border="1">
            <tr>
              <th scope="col">Effect</th>
              <th scope="col">Tag</th>
              <th scope="col">Example</th>
            </tr>
            <tr>
              <td>bold plus italic</td>
              <td>&lt;strong&gt;&lt;em&gt;...text...&lt;/em&gt;&lt;/strong&gt;</td>
              <td><strong><em>...text...</em></strong></td>
            </tr>
            <tr>
              <td>bold inside a paragraph</td>
              <td>&lt;p&gt;start of a sentence, then &lt;strong&gt;bold text&lt;/strong&gt;, then normal text.&lt;/p&gt;</td>
              <td><p>Start of a sentence, then <strong>bold text</strong>, then normal text.</p></td>
            </tr>
          </table>
          <p><strong>Tables</strong></p>
        <p> Tables require several tags.The basic structure is shown here. It is suggested that you consult a book on html formatting if you have trouble with this feature. </p>
        <p>&lt;table border=1 cellspacing=5 cellpadding=5&gt;<br />
          &lt;tr&gt;&lt;th&gt;heading, column 1&lt;/th&gt;&lt;th&gt;heading, column 2&lt;/th&gt;&lt;/tr&gt;<br />
        &lt;tr&gt;&lt;td&gt;row cell, column 1&lt;/td&gt;&lt;td&gt;row cell, column 2&lt;/td&gt;&lt;/tr&gt;<br />
        &lt;/table&gt;</p>
        <p>Produces:</p>
<table border=2 cellspacing=5 cellpadding=5>
<tr><th>heading, column 1</th><th>heading, column 2</th></tr>
<tr><td>row cell, column 1</td><td>row cell, column 2</td></tr>
</table>
<p>&nbsp;</p>
      </div>
    </div>
  <!-- end #container -->
</div>
</body>
</html>
