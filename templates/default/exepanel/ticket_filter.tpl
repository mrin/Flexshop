<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/ticket.php?type=new" class=inmenu>{_TICKET_NOREAD}</a> ::
<a href="{URLSITE}/exepanel/ticket.php?type=opened" class=inmenu>{_TICKET_OPENED}</a> ::
<a href="{URLSITE}/exepanel/ticket.php?type=closed" class=inmenu>{_TICKET_CLOSED}</a> ::
<a href="{URLSITE}/exepanel/ticket.php?type=search" class=inmenu>{_TICKET_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/ticket.php?type=filter" class=inmenu>{_TICKET_FILTER}</a> ::
<a href="{URLSITE}/exepanel/ticket.php?type=setting" class=inmenu>{_TICKET_SETTING}</a>
</b><br>
<br>
<span class=text><b>{_TICKET_FILTER}<br><br>{_TICKET_FILTER_INFO} ({_TICKET_FILTER_ALL} {COUNTALL})
<a href="{URLSITE}/exepanel/ticket.php?type=filter&showall=yes&" target="_blank" class="inmenu">{_TICKET_FILTER_SHOW}</a>
</span>
<br><br>
<form action="{URLSITE}/exepanel/ticket.php?type=filter&" method=POST>
<input type="hidden" name="method" value="add">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_MAIL}: </span></td>
		<td align=left class="contlight"> <input type="text" name="mail" size="30" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_FILTER_DESC}: </span></td>
		<td align=left class="contlight"> <input type="text" name="descr" size="30" class="inputtext"></td>
	</tr>
</table>
<p align="center"><input type="submit" value="{_CP_ADD}" class="inputbutton"></p>
</form>
<br>
<span class=text><b>{_TICKET_FILTER_DEL}</span>
<br><br>
<form action="{URLSITE}/exepanel/ticket.php?type=filter&" method=POST>
<input type="hidden" name="method" value="del">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_MAIL}: </span></td>
		<td align=left class="contlight"> <input type="text" name="mail" size="30" class="inputtext"></td>
	</tr>
</table>
<p align="center"><input type="submit" value="{_CP_DEL}" class="inputbutton"></p>
</form>