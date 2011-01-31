<table align='center' border="1" width="750" class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_HEADERSENDER}:</td>
		<td align="left" class="contlight" width="600">{HEADER_IP}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_CHARSET}:</td>
		<td align="left" class="contlight" width="600">{CHARSET}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_STATUS}:</td>
		<td align="left" class="contlight" width="600"><b>{STATUS}</b></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_DATSEND}:</td>
		<td align="left" class="contlight" width="600">{DATESEND}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_FROM}:</td>
		<td align="left" class="contlight" width="600">{MAILSENDER}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_SUBJECT}:</td>
		<td align="left" class="contlight" width="600">{SUBJECT}</td>
	</tr>
	<tr>
		<td align="right" valign="top" class="contdark" width="150">{_TICKET_MSG}:</td>
		<td align="justify" class="contlight" width="600">{MESSAGE}</td>
	</tr>
</table>
<p>
<input type="button" value="{_TICKET_REPLY2}" onClick="window.location='{URLSITE}/exepanel/ticket.php?type=view&method=reply&ticketid={TICKETID}&msgid={MSGID}&'" class="inputbutton">
</p><br>