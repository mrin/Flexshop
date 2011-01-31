<form name="type_goods" action="{URLSITE}/exepanel/ticket.php" method=GET>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<input type="hidden" name="type" value="search">
	<input type="hidden" name="method" value="search">
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_ID}: </span></td>
		<td align=left class="contlight"><input type="text" name="id" value="{ID}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_MAIL}: </span></td>
		<td align=left class="contlight"><input type="text" name="email" value="{EMAIL}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_SUBJECT}: </span></td>
		<td align=left class="contlight"><input type="text" name="subject" size=30 value="{SUBJECT}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_SEARCH_IN}: </span></td>
		<td align=left class="contlight">
			<select name="search_in" class="inputtext">
				<option value="2">{_TICKET_NOREAD}
				<option value="1">{_TICKET_OPENED}
				<option value="0">{_TICKET_CLOSED}
				<option value="3">{_TICKET_SEARCH_ALL}
			</select>
		</td>
	</tr>
</table>
<p><input type="submit" value="{_TICKET_SEARCH}" class=inputbutton></p>
</form>