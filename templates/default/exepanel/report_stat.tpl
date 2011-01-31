<script language="JavaScript">
var aMonths = {AMONTHS}
var aDaysOfWeek = {ADATOFWEEK}
</script>
<script src="{URLSITE}/templates/{TEMPLDEF}/exepanel/calendar.js"></script>

<div style="padding-left:5px;padding-right:5px;paddin-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/sell_stat.php?type=profit&" class="inmenu">{_SELL_STAT_LABEL1}</a> ::
<a href="{URLSITE}/exepanel/sell_stat.php?type=report" class="inmenu">{_SELL_STAT_LABEL2}</a>
</b>
<p class="text">{INFO}<br><br>
<form name="profit" method="post" action="{URLSITE}/exepanel/sell_stat.php?type=report">
{_SELL_STAT_PERIOD}
{_SELL_STAT_WITH}: <input class="inputcenter" type="text" onfocus="openCalendar(this, -30, 1);" name="from" readonly value="{FROM}"> 
{_SELL_STAT_TO}: <input class="inputcenter" type="text" onfocus="openCalendar(this, -30, 1);" name="to" readonly value="{TO}">
<p>
<fieldset style="width:200px;">
<legend><span class="text">{_SELL_STAT_REPORT_TYPE}</span></legend>
<table width="200">
	<tr>
		<td><span class="text">
			<input type="radio" name="type_report" value="1" {CHECK1}>{_SELL_STAT_REPORT_TYPE1}<br>
			<input type="radio" name="type_report" value="2" {CHECK2}>{_SELL_STAT_REPORT_TYPE2}<br>
			<input type="radio" name="type_report" value="3" {CHECK3}>{_SELL_STAT_REPORT_TYPE3}<br>
			<input type="radio" name="type_report" value="4" {CHECK4}>{_SELL_STAT_REPORT_TYPE4}<br>
			</span>
		</td>
	</tr>
</table>
</fieldset>
</p>
<p><input class="inputbutton" type="submit" value="{_CP_SHOW}"></p>
</form>
</p>
<p align="right" style="padding-right:100px;">{OPEN_REPORT}</p>
<p>{CREATE_GRAPH}</p><br>
</div>