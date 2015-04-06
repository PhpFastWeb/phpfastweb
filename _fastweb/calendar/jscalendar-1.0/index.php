<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<!-- $Id: index.html,v 1.16 2005/05/07 15:24:08 mishoo Exp $ -->

<head>
<meta http-equiv="content-type" content="text/xml; charset=utf-8" />
<title>The Coolest DHTML Calendar - Online Demo</title>
<link rel="stylesheet" type="text/css" media="all" href="skins/aqua/theme.css" title="Aqua" />
<link rel="alternate stylesheet" type="text/css" media="all" href="skins/tiger/theme.css" title="Tiger" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-blue.css" title="winter" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-blue2.css" title="blue" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-brown.css" title="summer" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-green.css" title="green" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-win2k-1.css" title="win2k-1" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-win2k-2.css" title="win2k-2" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-win2k-cold-2.css" title="win2k-cold-2" />
<link rel="alternate stylesheet" type="text/css" media="all" href="calendar-system.css" title="system" />

<!-- import the calendar script -->
<script type="text/javascript" src="calendar.js"></script>

<!-- import the language module -->
<script type="text/javascript" src="lang/calendar-en.js"></script>

<!-- other languages might be available in the lang directory; please check
your distribution archive. -->

<!-- helper script that uses the calendar -->
<script type="text/javascript">

var oldLink = null;
// code to change the active stylesheet
function setActiveStyleSheet(link, title) {
  var i, a;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) a.disabled = false;
    }
  }
  if (oldLink) oldLink.style.fontWeight = 'normal';
  oldLink = link;
  link.style.fontWeight = 'bold';
  return false;
}

// This function gets called when the end-user clicks on some date.
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
  if (cal.dateClicked && (cal.sel.id == "sel1" || cal.sel.id == "sel3"))
    // if we add this call we close the calendar on single-click.
    // just to exemplify both cases, we are using this only for the 1st
    // and the 3rd field, while 2nd and 4th will still require double-click.
    cal.callCloseHandler();
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks on the "Close" button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
  cal.hide();                        // hide the calendar
//  cal.destroy();
  _dynarch_popupCalendar = null;
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.
    var cal = new Calendar(1, null, selected, closeHandler);
    // uncomment the following line to hide the week numbers
    // cal.weekNumbers = false;
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use

  // the reference element that we pass to showAtElement is the button that
  // triggers the calendar.  In this example we align the calendar bottom-right
  // to the button.
  _dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");        // show the calendar

  return false;
}

var MINUTE = 60 * 1000;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR;
var WEEK = 7 * DAY;

// If this handler returns true then the "date" given as
// parameter will be disabled.  In this example we enable
// only days within a range of 10 days from the current
// date.
// You can use the functions date.getFullYear() -- returns the year
// as 4 digit number, date.getMonth() -- returns the month as 0..11,
// and date.getDate() -- returns the date of the month as 1..31, to
// make heavy calculations here.  However, beware that this function
// should be very fast, as it is called for each day in a month when
// the calendar is (re)constructed.
function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
}

function flatSelected(cal, date) {
  var el = document.getElementById("preview");
  el.innerHTML = date;
}

function showFlatCalendar() {
  var parent = document.getElementById("display");

  // construct a calendar giving only the "selected" handler.
  var cal = new Calendar(0, null, flatSelected);

  // hide week numbers
  cal.weekNumbers = false;

  // We want some dates to be disabled; see function isDisabled above
  cal.setDisabledHandler(isDisabled);
  cal.setDateFormat("%A, %B %e");

  // this call must be the last as it might use data initialized above; if
  // we specify a parent, as opposite to the "showCalendar" function above,
  // then we create a flat calendar -- not popup.  Hidden, though, but...
  cal.create(parent);

  // ... we can show it here.
  cal.show();
}
</script>

<style type="text/css">
.ex { font-weight: bold; background: #fed; color: #080 }
.help { color: #080; font-style: italic; }
body { background: #fea; font: 10pt tahoma,verdana,sans-serif; }
table { font: 13px verdana,tahoma,sans-serif; }
a { color: #00f; }
a:visited { color: #00f; }
a:hover { color: #f00; background: #fefaf0; }
a:active { color: #08f; }
.key { border: 1px solid #000; background: #fff; color: #008;
padding: 0px 5px; cursor: default; font-size: 80%; }
</style>

</head>
<body onload="showFlatCalendar()">

<h2><a href="http://www.dynarch.com/projects/calendar/"
title="Visit the project website">jscalendar</a>-1.0
"It is happening again"</h2>

<p>
<div style="float: right; border: 1px solid #b87; padding: 2px; font-size: 90%; background: #ffb;">
Theme:<br />
<a href="#" id="defaultTheme" onclick="return setActiveStyleSheet(this, 'Aqua');">Aqua</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'Tiger');">Tiger</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'winter');">winter</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'blue');">blue</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'summer');">summer</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'green');">green</a>
<br />
<a href="#" onclick="return setActiveStyleSheet(this, 'win2k-1');">win2k-1</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'win2k-2');">win2k-2</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'win2k-cold-1');">win2k-cold-1</a>
|
<a href="#" onclick="return setActiveStyleSheet(this, 'win2k-cold-2');">win2k-cold-2</a>
<br />
<a href="#" onclick="return setActiveStyleSheet(this, 'system');">system</a>
<script type="text/javascript">
setActiveStyleSheet(document.getElementById("defaultTheme"), "Aqua");
</script>
</div>
<a href="release-notes.html">Release notes</a>.
<br />
Set it up in minutes:
  <a href="simple-1.html">popup calendar</a>,
  <a href="simple-2.html">flat calendar</a>.
Other samples:
  <a href="simple-3.html">special days</a>,
  <a href="dayinfo.html">day info</a>,
  <a href="multiple-dates.html">multiple dates selection</a>
<br />
Documentation:
  <a href="doc/html/reference.html">HTML</a>,
  <a href="doc/reference.pdf">PDF</a>.
<br />


<div style="padding-left:20px; font-size: 90%; font-style: italic;">

</div>

<table style="width: 100%">
<tr valign="top">
<td style="background: #ffa; padding: 5px; border: 1px solid #995;">

<form action="#">
<div style="background: #995; color: #ffa; font-weight: bold; padding: 2px;">
Popup examples
</div>

<br />

<b>Date #1:</b> <input type="text" name="date1" id="sel1" size="30"
><input type="reset" value=" ... "
onclick="return showCalendar('sel1', '%Y-%m-%d [%W] %H:%M', '24', true);" ></input> 
%Y-%m-%d [%W] %H:%M -- single click<br />

<b>Date #2:</b> <input type="text" name="date2" id="sel2" size="30"
></input><input type="reset" value=" ... "
onclick="return showCalendar('sel2', '%a, %b %e, %Y [%I:%M %p]', '12');" ></input> 
%a, %b %e, %Y [%I:%M %p] -- double click

<br /><br />
<!--
if you remove this comment and leave the following HTML code
you will see a horrible effect, in all supported browsers (IE and Mozilla).
-->
<SELECT multiple size="4" name="component-select">
  <OPTION selected value="Component_1_a">Component_1</OPTION>
  <OPTION selected value="Component_1_b">Component_2</OPTION>
  <OPTION>Component_3</OPTION>
  <OPTION>Component_4</OPTION>
  <OPTION>Component_5</OPTION>
  <OPTION>Component_6</OPTION>
  <OPTION>Component_7</OPTION>
</SELECT>
this select should hide when the calendar is above it.
<br /><br />

<b>Date #3:</b> <input type="text" name="date3" id="sel3" size="30"
><input type="reset" value=" ... "
onclick="return showCalendar('sel3', '%d/%m/%Y');"><?php htmlspecialchars(" %d/%m/%Y
-- single click"); ?>
<br />

<b>Date #4:</b> <input type="text" name="date4" id="sel4" size="30"
><input type="reset" value=" ... "
onclick="return showCalendar('sel4', '%A, %B %e, %Y');"><?php htmlspecialchars(" %A, %B %e, %Y --
double click"); ?>

</form>

<p>This is release <b>1.0</b>.  Works on MSIE/Win 5.0 or better (really),
Opera 7+, Mozilla, Firefox, Netscape 6.x, 7.0 and all other Gecko-s,
Konqueror and Safari.</p>

<h4>Keyboard navigation</h4>

<p>Starting with version 0.9.2, you can also use the keyboard to select
dates (only for popup calendars; does <em>not</em> work with Opera
7 or Konqueror/Safari).  The following keys are available:</p>

<ul>

  <li><span class="key">&larr;</span> , <span class="key">&rarr;</span> ,
  <span class="key">&uarr;</span> , <span class="key">&darr;</span> -- select date</li>
  <li><span class="key">CTRL</span> + <span class="key">&larr;</span> ,
  <span class="key">&rarr;</span> -- select month</li>
  <li><span class="key">CTRL</span> + <span class="key">&uarr;</span> ,
  <span class="key">&darr;</span> -- select year</li>
  <li><span class="key">SPACE</span> -- go to <em>today</em> date</li>
  <li><span class="key">ENTER</span> -- accept the currently selected date</li>
  <li><span class="key">ESC</span> -- cancel selection</li>

</ul>

          </td>

          <td style="padding: 5px; margin: 5px; border: 1px solid #984; background: #ed9; width: 19em;">

            <div style="background: #984; color: #fea; font-weight: bold; padding: 2px; text-align: center">
              Flat calendar
            </div>

            <p style="width: 12em"><small>A non-popup version will appear below as soon
              as the page is loaded.  Note that it doesn't show the week number.</small></p>

            <!-- the calendar will be inserted here -->
            <div id="display" style="float: right; clear: both;"></div>
            <div id="preview" style="font-size: 80%; text-align: center; padding: 2px">&nbsp;</div>

            <p style="clear: both;"><small>
              The example above uses the <code>setDisabledHandler()</code> member function
              to setup a handler that would only enable days withing a range of 10 days,
              forward or backward, from the current date.
            </small></p>

          

          </td>

        </tr>
      </table>

<hr /><address>
&copy; <a href="http://www.dynarch.com/">dynarch.com</a> 2002-2005 <br />
Author: <a href="http://www.bazon.net/mishoo/">Mihai
Bazon</a><br /> Distributed under the <a
href="http://www.gnu.org/licenses/lgpl.html">GNU LGPL</a>.</address>

<p style="font-size: smaller">If you use this script on a public page we
would love it if you would <a href="http://www.dynarch.com/contact.html">let us
know</a>.</p>

</body></html>
