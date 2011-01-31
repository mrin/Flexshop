<div>
<b><span class="text">{_INST_INFO}</span></b><br><br>
<form action="{URLSITE}/exepanel/s_shop.php?change_other" method="POST">
<table align='center' border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark"><span class="text">{_INST_ADDRSURL}:</span></td>
		<td class="contlight"><input type='text' name='url' value='{INS_URL}' size=20 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_EMAIL}:</span></td>
		<td class="contlight"><input type='text' name='mail' value='{INS_MAIL}' size=25 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_HOMEDIR}:</span></td>
		<td class="contlight"><input type='text' name='homedir' value='{INS_HOMEDIR}' size=40 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_SITE_TITLE}:</span></td>
		<td class="contlight"><input type='text' name='title' value='{INS_TITLE}' size=40 class="inputtext"></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_MINI_TITLE}:</span></td>
		<td class="contlight"><input type='text' name='mini_title' value='{INS_MINI_TITLE}' size=40 class="inputtext"></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_META_DESCR}:</span></td>
		<td class="contlight"><input type='text' name='meta_description' value='{INS_META_DESCR}' size=40 class="inputtext"></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_META_KEY}:</span></td>
		<td class="contlight"><input type='text' name='meta_keywords' value='{INS_META_KEY}' size=40 class="inputtext"></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_DEFLANG}:</span></td>
		<td class="contlight"><select name='deflang' class="inputtext">{INS_OPTLANG}</td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_DEFTPL}:</span></td>
		<td class="contlight"><select name='deftpl' class="inputtext">{INS_OPTTPL}</select></td>
	</tr>
</table>
<p align=center><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG}</small><br><br><br>
<b><span class="text">{_INST_MYSQL}</span></b><br><br>
<form action="{URLSITE}/exepanel/s_shop.php?change_db" method="POST">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_DBHOST}:</span></td>
		<td class="contlight"><input type='text' name='dbhost' value='{INS_DBHOST}' size=16 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_DBNAME}:</span></td>
		<td class="contlight"><input type='text' name='dbname' value='{INS_DBNAME}' size=16 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_DBLOGIN}:</span></td>
		<td class="contlight"><input type='text' name='dblogin' value='{INS_DBLOGIN}' size=16 class="inputtext"> <span class="text">*</span></td>
	</tr>
	<tr>
		<td class="contdark" align="right"><span class="text">{_INST_PWD}:</span></td>
		<td class="contlight"><input type='password' name='dbpwd' value='{INS_DBPWD}' size=16 class="inputtext"> <span class="text">*</span></td>
	</tr>
</table>
<p align=center><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG2}</small><br><br>
</div>
