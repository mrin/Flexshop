<form action="{URLSITE}/exepanel/ticket.php?type=view&method=reply&ticketid={TICKETID}&retr={RETR}&msgid={MSGID}" method="POST">
<input type="hidden" name="action" value="yes">
<table align='center' border="1" width="750" class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_CHARSET}:</td>
		<td align="left" class="contlight" width="600"><input type="text" name="charset" size="12" value="{CHARSET}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_TO}:</td>
		<td align="left" class="contlight" width="600"><input type="text" name="to" value="{TO}" size="30" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150">{_TICKET_SUBJECT}:</td>
		<td align="left" class="contlight" width="600"><input type="text" name="subject" value="{SUBJECT}" size="60" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" valign="top" class="contdark" width="150">{_TICKET_MSG}:</td>
		<td align="justify" class="contlight" width="600"><textarea name="msg" cols="100" rows="20" class="inputarea">{MESSAGE}</textarea></td>
	</tr>
</table>
<p align="center"><input type="submit" value="{_TICKET_SEND_REPLY}" class="inputbutton"></p>
</form>
<br><br>	