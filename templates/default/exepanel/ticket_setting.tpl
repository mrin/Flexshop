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
<span class=text><b>{_TICKET_SETTINGMAIL}</span>
<br><br>
<form action="{URLSITE}/exepanel/ticket.php?type=setting&" method=POST>
<input type="hidden" name="method" value="change">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_MAIL}: </span></td>
		<td align=left class="contlight"> <input type="text" name="mail" size="30" value="{MAIL}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_NAME}: </span></td>
		<td align=left class="contlight"> <input type="text" name="name" size="30" value="{NAME}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_SERVERS}: </span></td>
		<td align=left class="contlight"> <input type="text" name="smtp_server" size="30" value="{SMTP_SERVER}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_PORTS}: </span></td>
		<td align=left class="contlight"> <input type="text" name="smtp_port" size="5" value="{SMTP_PORT}" class="inputcenter"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_SERVERP}: </span></td>
		<td align=left class="contlight"> <input type="text" name="pop3_server" size="30" value="{POP3_SERVER}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_PORTP}: </span></td>
		<td align=left class="contlight"> <input type="text" name="pop3_port" size="5" value="{POP3_PORT}" class="inputcenter"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_USER}: </span></td>
		<td align=left class="contlight"> <input type="text" name="user" value="{USER}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_PWD}: </span></td>
		<td align=left class="contlight"> <input type="password" name="pwd" value="{PASSWORD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_SAVE}: </span></td>
		<td align=left class="contlight"> <input type="checkbox" name="save" value="1" {CHECK_SAVE} class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_ENABLE}: </span></td>
		<td align=left class="contlight"> <input type="checkbox" name="on_off" value="1" {CHECK_ON_OFF} class="inputtext"></td>
	</tr>
</table>
<p><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
<br>
<span class=text><b>{_TICKET_OPTIONAL}</span>
<br><br>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TICKET_FILTER_BLACKLIST}: </span></td>
		<td align=left class="contlight"> 
			<table align='left' border="0">
				<tr>
					<td><span class=text>{_TICKET_ONOFF}</span></td>
					<td> <input type="checkbox" name="autofilter_status" value="1" {CHECK_AUTOFILTER} class="inputtext"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark"><span class=text><b>{_TICKET_AUTOCLOSE}: </span></td>
		<td align="left" class="contlight"> 
			<table align="center" border="0">
				<tr>
					<td><span class="text">{_TICKET_ONOFF}</span></td>
					<td> <input type="checkbox" name="autoclose_status" value="1" {CHECK_AUTOCLOSE} class="inputtext"></td>
					<td><span class="text">{_TICKET_THROUGHT}</span></td>
					<td> <input type="text" name="autoclose_days" size="5" value="{AUTOCLOSE_DAYS}" class="inputcenter"> <span class="text">{_TICKET_DAYS}</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark"><span class="text"><b>{_TICKET_AUTODELETE}: </span></td>
		<td align="left" class="contlight">
			<table align="center" border="0">
				<tr>
					<td><span class="text">{_TICKET_ONOFF}</span></td>
					<td> <input type="checkbox" name="autodelete_status" value="1" {CHECK_AUTODELETE} class="inputtext"></td>
					<td><span class="text">{_TICKET_THROUGHT}</span></td>
					<td> <input type="text" name="autodelete_days" size="5" value="{AUTODELETE_DAYS}" class="inputcenter"> <span class="text">{_TICKET_DAYS}</span></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
<br>
<span class="text"><b>{_TICKET_TEMPLATE}</span>
<br>
<table align="center" width="90%" border="0">
	<tr>
		<td align="left" class="text">{_TICKET_LABEL}</td>
	</tr>
</table>
<table align="center" width="90%" border="1"  class="dash" cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width=200><span class="text"><b>{_TICKET_SUBJECT}: </span></td>
		<td align="left"  class="contlight"> <input type="text" name="subject" value="{SUBJECTT}" size=60 class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" valign="top" class="contdark" width=200><span class="text"><b>{_TICKET_MESSAGE}: </span></td>
		<td align="left" class="contlight"><textarea name="msg" cols="80" rows="20" class="inputarea">{MSG}</textarea></td>
	</tr>
</table>
<p><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
<br>
<span class="text"><b>{_TICKET_TEMPLATE2}</span>
<br>
<table align="center" width="90%" border="0">
	<tr>
		<td align="left" class="text">{_TICKET_LABEL2}</td>
	</tr>
</table>
<table align="center" border="1" width="90%" class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width=200><span class="text"><b>{_TICKET_SIGNTXT}: </span></td>
		<td align="left" class="contlight"><textarea name="sign" cols="80" rows="15" class="inputarea">{SIGN}</textarea></td>
	</tr>
</table>
<p><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<br>
</div>