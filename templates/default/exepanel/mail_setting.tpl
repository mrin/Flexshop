<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<span class=text><b>{_MAIL_SETTING_INFO}</span>
<br><br>
<form action="{URLSITE}/exepanel/mail_setting.php" method=POST>
<input type="hidden" name="method" value="change">
<table align="center" border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_MAIL}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="mail" size="30" value="{MAIL}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_NAME}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="name" size="30" value="{NAME}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_SERVERS}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="smtp_server" size="30" value="{SMTP_SERVER}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_PORTS}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="smtp_port" size="5" value="{SMTP_PORT}" class="inputcenter"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_USER}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="user" value="{USER}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_PWD}: </span></td>
		<td align="left" class="contlight"> <input type="password" name="pwd" value="{PASSWORD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_MAIL_SETTING_LIMIT}: </span></td>
		<td align="left" class="contlight"> <input type="text" name="lim" size="5" value="{LIMIT}" class="inputcenter"></td>
	</tr>
</table>
<p><span class="text">{_MAIL_SETTING_SIGN}</span></p>
<table align="center" width="500" border="0">
	<tr>
		<td align="left" class="text">{_MAIL_SETTING_LABEL}</td>
	</tr>
</table>
<table align="center" border="1" width="500" class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width="180"><span class="text"><b>{_TICKET_SIGNTXT}: </span></td>
		<td align="left" class="contlight"><textarea name="sign" cols="50" rows="10" class="inputarea">{SIGN}</textarea></td>
	</tr>
</table>
<p><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<br>
</div>