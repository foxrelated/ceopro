<ul class= "global_form_box" style="padding: 0 0 10px; margin-bottom: 15px;">
    <div style="">
        <body  onload="DrawCalendar()">
        	<div  id = "dpCalendar"/>
        </body>
    </div>
</ul>

<script language="JavaScript" type="text/javascript">
// User Changeable Vars
var HighlightToday  = true;    // use true or false to have the current day highlighted
var DisablePast    = true;    // use true or false to allow past dates to be selectable
// The month names in your native language can be substituted below
var jan = "<?php echo $this->translate("January"); ?>";
var feb = "<?php echo $this->translate("February"); ?>";
var mar = "<?php echo $this->translate("March"); ?>";
var apr = "<?php echo $this->translate("April"); ?>";
var may = "<?php echo $this->translate("May"); ?>";
var jun = "<?php echo $this->translate("June"); ?>";
var jul = "<?php echo $this->translate("July"); ?>";
var aug = "<?php echo $this->translate("August"); ?>";
var sep = "<?php echo $this->translate("September"); ?>";
var oct = "<?php echo $this->translate("October"); ?>";
var nov = "<?php echo $this->translate("November"); ?>";
var dec = "<?php echo $this->translate("December"); ?>";
var MonthNames = new Array(jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec);
// Global Vars

var url_string = "<?php echo $this->url_string;?>";
var now = new Date();
var dest = null;
var ny = now.getFullYear(); // Today's Date
var nm = now.getMonth();
var nd = now.getDate();
var sy = 0; // currently Selected date
var sm = 0;
var sd = 0;
var ynblog_year = now.getFullYear(); // Working Date
var m = now.getMonth();
var d = now.getDate();
var l = 0;
var t = 0;
var MonthLengths = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
function DestroyCalendar() {
  var cal = document.getElementById("dpCalendar");
  if(cal != null) {
    cal.innerHTML = null;
    cal.style.display = "none";
  }
  return
}



function DrawCalendar() {
  DestroyCalendar();
  cal = document.getElementById("dpCalendar");
  var su = "<?php echo $this->translate("Su"); ?>";
  var mo = "<?php echo $this->translate("Mo"); ?>";
  var tu = "<?php echo $this->translate("Tu"); ?>";
  var we = "<?php echo $this->translate("We"); ?>";
  var th = "<?php echo $this->translate("Th"); ?>";
  var fr = "<?php echo $this->translate("Fr"); ?>";
  var sa = "<?php echo $this->translate("Sa"); ?>";
  var sCal = "<table class='ynblog-view-by-date'><tr class ='layout_core_menu_main' style=\"height: 42px;\"><td class=\"cellButton1\"><a href=\"javascript: PrevMonth();\" title=\"Previous Month\" style=\"text-align: center;\";><i class='fa fa-angle-left'></i></a></td>"+
    "<td class=\"cellMonth\" colspan=\"2\">"+MonthNames[m]+"</td>"+
    "<td class=\"cellButton2\"><a href=\"javascript: NextMonth();\" title=\"Next Month\" style=\"text-align: center;\"><i class='fa fa-angle-right'></i></a></td>"+
    "<td class=\"cellButton1\"><a href=\"javascript: PrevYear();\" title=\"Previous Month\" style=\"text-align: center;\";><i class='fa fa-angle-double-left'></i></a></td>"+
    "<td class=\"cellMonth\" colspan=\"1\">"+ ynblog_year + "</td>"+
    "<td class=\"cellButton2\"><a href=\"javascript: NextYear();\" title=\"Next Month\" style=\"text-align: center;\"><i class='fa fa-angle-double-right'></i></a></td></tr>"+
    "<tr class=\"cellThu\"><td style=\"text-align:center;font-size:8pt;\">" +    
    su + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    mo + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    tu + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    we + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    th + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    fr + "</td><td style=\"text-align:center; font-size:8pt;\">" +
    sa + "</td></tr>";
  var wDay = 1;
  var wDate = new Date(ynblog_year,m,wDay);
  if(isLeapYear(wDate)) {
    MonthLengths[1] = 29;
  } else {
    MonthLengths[1] = 28;
  }
  var dayclass = "";
  var isToday = false;
  for(var r=1; r<7; r++) {
    sCal = sCal + "<tr>";
    for(var c=0; c<7; c++) {
      var wDate = new Date(ynblog_year,m,wDay);
      if(wDate.getDay() == c && wDay<=MonthLengths[m]) {
        if(wDate.getDate()==sd && wDate.getMonth()==sm && wDate.getFullYear()==sy) {
          dayclass = "cellSelected";
          isToday = true;  // only matters if the selected day IS today, otherwise ignored.
        } else if(wDate.getDate()==nd && wDate.getMonth()==nm && wDate.getFullYear()==ny && HighlightToday) {
          dayclass = "cellToday";
          isToday = true;
        } else {
          dayclass = "cellDay";
          isToday = false;
        }
          // user wants past dates selectable
          sCal = sCal + "<td class=\""+dayclass+"\"><a href=\"javascript: ReturnDay("+wDay+");\">"+wDay+"</a></td>";
        wDay++;
      } else {
        sCal = sCal + "<td class=\"unused\"></td>";
      }
    }
    sCal = sCal + "</tr>";
  }
  sCal = sCal + "</table>"
  cal.innerHTML = sCal; // works in FireFox, opera
  cal.style.display = "block";
}

function PrevMonth() {
  m--;
  if(m==-1) {
    m = 11;
    ynblog_year--;
  }
  DrawCalendar();
}

function NextMonth() {
  m++;
  if(m==12) {
    m = 0;
    ynblog_year++;

  }
  DrawCalendar();
}

function PrevYear() {
  ynblog_year--;
  DrawCalendar();
}

function NextYear() {
  ynblog_year++;
  DrawCalendar();
}
<?php
function selfURL() {
      $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 }
?>

function ReturnDay(day) {
  var date = ynblog_year+"-"+ (m+1)+"-"+day;
  var url = "<?php echo  selfURL()?>";
  window.location  =  url + url_string + "/date/" + date;
}

function EnsureCalendarExists() {
  if(document.getElementById("dpCalendar") == null) {
    var eCalendar = document.createElement("div");
    eCalendar.setAttribute("id", "dpCalendar");
    document.body.appendChild(eCalendar);
  }
}

function isLeapYear(dTest) {

  var y = dTest.getYear();
  var bReturn = false;
  if(y % 4 == 0) {
    if(y % 100 != 0) {
      bReturn = true;

    } else {
      if (y % 400 == 0) {
        bReturn = true;
      }
    }
  }
  return bReturn;
}
  </script>
  <style type="text/css">
/* The containing DIV element for the Calendar */

#dpCalendar {
  display: none;
  font-size: 8pt;
}

/* The table of the Calendar */

#dpCalendar table {
  margin: 0;
  padding: 0;
  font-size: 8pt;
  width: 100%;
  border-collapse: collapse;
}

#dpCalendar table td,#dpCalendar table th
{
	margin: 0;
	padding: 0;
}

/* The Next/Previous buttons */
#dpCalendar .cellButton1 {
text-align: center;
}
#dpCalendar .cellButton2 {
text-align: center;
}

/* The Month/Year title cell */

#dpCalendar .cellMonth {
  text-align: center;
  padding:1px;
}

.cellYear
{
     font-weight:bold;
}

/* Any regular day of the month cell */

#dpCalendar .cellDay {
  text-align: center;
  width: 14%;
  *padding-top:5px;
}

#dpCalendar .cellButton1,#dpCalendar .cellButton2{
}

/* The day of the month cell that is Today */

#dpCalendar .cellToday {
  text-align: center;
  font-size:7pt;
  font-weight:bold;
  padding:2px;
}

/* Any cell in a month that is unused (ie: Not a Day in that month) */

#dpCalendar .unused {
  background-color: transparent;
  color: black;
}

.cellThu
{
	background-color:transparent;
	height: 17px;
}

/* The clickable text inside the calendar */

#dpCalendar a {
background-color:transparent;
font-size:7pt;
font-weight:bold;
text-decoration:none;
}
  </style>

