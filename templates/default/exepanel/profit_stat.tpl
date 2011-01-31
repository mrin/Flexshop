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
<form name="profit" method="post" action="{URLSITE}/exepanel/sell_stat.php?type=profit">
{_SELL_STAT_PERIOD}:
<select name="period">
	<option value="1">{_SELL_STAT_TODAY}
	<option value="2">{_SELL_STAT_YESTERDAY}
	<option value="3">{_SELL_STAT_WEEK}
	<option value="4">{_SELL_STAT_MONTH}
	<option value="5">{_SELL_STAT_TOTAL}
</select>
<b>{_SELL_STAT_OR}</b>
{_SELL_STAT_WITH}: <input class="inputcenter" type="text" onfocus="openCalendar(this, -30, 1);" name="from" readonly value="{FROM}"> 
{_SELL_STAT_TO}: <input class="inputcenter" type="text" onfocus="openCalendar(this, -30, 1);" name="to" readonly value="{TO}">
<p>
<input class="inputbutton" type="submit" value="{_CP_SHOW}">
</p>
</form>
</p>
<p>{CREATE_GRAPH}</p><br>
</div>