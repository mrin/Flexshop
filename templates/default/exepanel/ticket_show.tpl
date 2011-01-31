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
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="130">{_TICKET_ID}:</td>
		<td align="left" class="contlight" width="400">[#{TICKETID}]</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="130">{_TICKET_MAIL}:</td>
		<td align="left" class="contlight" width="400">{MAIL}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="130">{_TICKET_DATCREATE}:</td>
		<td align="left" class="contlight" width="400">{DATECREATE}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="130">{_TICKET_DATCLOSE}:</td>
		<td align="left" class="contlight" width="400">{DATECLOSE}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="130">{_TICKET_SUBJECT_TICKET}:</td>
		<td align="left" class="contlight" width="400">{SUBJECT_TICKET}</td>
	</tr>
</table>
<br><br>
<p align=center>{PAGES_NUMBER}</p>
{TICKET_FORM}
<p align=center>{PAGES_NUMBER}</p>
</div>