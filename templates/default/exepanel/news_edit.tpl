<div>
<a class="inmenu" href="{URLSITE}/exepanel/news.php?type=add">{_NEWS_ADD}</a>::<a class="inmenu" href="{URLSITE}/exepanel/news.php?type=show">{_NEWS_LIST}</a>
<br><br>
<form action="{URLSITE}/exepanel/news.php?type=edit&method=change&id={ID}&" method="POST">
<table align="center" border="1"  class="dash" cellpadding="5" cellspacing="0">
	<tr>
		<td align="right" class="contdark" width="150"><b>{_NEWS_DATE}:</b>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi1')" onMouseOver="show_help('hi1')">
		</td>
		<td class="contlight"><input type="text" name="dat" value="{DATE}" size="10" class="inputtext"><span class="text">*</span></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150"><b>{_NEWS_TITLERU}:</b>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi2')" onMouseOver="show_help('hi2')">
		</td>
		<td class="contlight"><input type="text" name="titleru" size="80" value="{TITLERU}" class="inputtext"><span class="text">**</span></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150"><b>{_NEWS_TITLEEN}:</b>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi2')" onMouseOver="show_help('hi2')">
		</td>
		<td class="contlight"><input type="text" name="titleen" size="80" value="{TITLEEN}" class="inputtext"><span class="text">**</span></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150"><b>{_NEWS_MSGRU} (HTML):</b>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi3')" onMouseOver="show_help('hi3')">
		</td>
		<td class="contlight"><textarea name="msgru" cols="80" rows="10">{MSGRU}</textarea><span class="text">**</span></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="150"><b>{_NEWS_MSGEN} (HTML):</b>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi3')" onMouseOver="show_help('hi3')">
		</td>
		<td class="contlight"><textarea name="msgen" cols="80" rows="10">{MSGEN}</textarea><span class="text">**</span></td>
	</tr>
</table>
<br>
<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi4')" onMouseOver="show_help('hi4')">
<input type="checkbox" name="subscribe_send" value="1" class="inputtext"><span class="text">{_NEWS_SUBSCR_SEND}</span>
<p align="center"><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG}<br> ** {_LABEL_OBLIG3}</small><br><br>
</div>

<DIV class="hi" id="hi1">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_NEWS_DATE_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>

<DIV class="hi" id="hi2">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_NEWS_TITLE_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>

<DIV class="hi" id="hi3">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_NEWS_MSG_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>

<DIV class="hi" id="hi4">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_NEWS_SUBSCR_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>