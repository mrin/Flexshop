<div>
<form action="{URLSITE}/exepanel/profile.php?change" method="POST">
<table align="center" border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark"><span class="text">{_INST_FIO}:</span></td>
		<td class="contlight"><input type="text" name="name" size="38" class="inputtext" value="{NAME}"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td align="right" class="contdark"><span class="text">{_INST_PWD}:</span></td>
		<td class="contlight"><input type="password" name="pwd1" size="16" class="inputtext"> / <input type="password" name="pwd2" size=16 class="inputtext"> <span class="text">**</span></td>
	</tr>
</table>
<p align="center"><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG}<br>** {_LABEL_PWD}</small><br><br>
</div>