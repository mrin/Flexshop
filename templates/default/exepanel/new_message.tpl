<script language="JavaScript">
	function change(flag) {
		if(flag == 0) {
			new_msg.rec.disabled = false;
		}
			else {
				new_msg.rec.disabled = true;
			}
	}
</script>
<div>
<p><span class="text"><b>{_SEND_MAIL_TO}</b></span></p>
<form name="new_msg" action="{URLSITE}/exepanel/new_message.php" method="POST">
<input type="hidden" name="action" value="yes">
<table align="center" border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width="150">
		<span class="text"><b>{_SEND_MAIL_TO}:</b></span>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi1')" onMouseOver="show_help('hi1')">
		</td>
		<td class="contlight">
			<table border="0">
				<tr>
					<td align="left" colspan="2">
					<input type="radio" name="all" value="1" onClick="change(1)" checked>
					<span class="text">{_SEND_MAIL_TO_ALL}:</span><br>
					<input type="radio" name="all" value="0" onClick="change(0)">
					<span class="text">{_SEND_MAIL_TO_SELECT}:</span>
					
					</td>
				</tr>
				<tr>
					<td><span class="text">{_SEND_MAIL_RECIPIENT}:</span></td>
					<td><input type="text" name="rec" size="40" value="{RECIPIENT}" disabled class="inputtext"></td>
				</tr>
			</table>			
		</td>
	</tr>
</table>
<p><span class="text"><b>{_SEND_MAIL_LETTER}</b></span></p>
<table align="center" border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width="150"><span class="text"><b>{_TICKET_SUBJECT}:</b></span></td>
		<td class="contlight"><input type="text" name="subject" size="60" value="{SUBJECT}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" valign="top" class="contdark" width="150"><span class="text"><b>{_TICKET_MSG}:</b></span></td>
		<td class="contlight"><textarea name="msg" cols="60" rows="10">{MSG}</textarea></td>
	</tr>
</table>
<p align="center"><input type="submit" value="{_CP_SEND}" class="inputbutton"></p>
</form>
</div>

<DIV class="hi" id="hi1">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_SEND_MAIL_TO_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
