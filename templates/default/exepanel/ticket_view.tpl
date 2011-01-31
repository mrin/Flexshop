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
<span class=text><b>{CURRENT_SELECT}</span>
{NOTFOUND}
<br><br>
{SEARCH_FORM}
<br>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width="70">{_TICKET_ID}</td>
		<td align=center class="conttop" width="170">{_TICKET_SUBJECT}</td>
		<td align=center class="conttop" width="100">{_TICKET_DATCREATE}</td>
		<td align=center class="conttop" width="100">{_TICKET_DATCLOSE}</td>
		<td align=center class="conttop" width="60">{_TICKET_REPLY}</td>
		<td align=center class="conttop" width="80">{_TICKET_NEWMSG}</td>
		<td align=center class="conttop" width="100">{_NEWS_ACTION}</td>
	</tr>
	{TICKET_LIST}
</table>
<p align=center>{PAGES_NUMBER}</p>
</div>