<br><form name="editgood" method="POST" enctype="multipart/form-data" onsubmit="return false">
<input type="hidden" name="good" value="{ID_GOOD}">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark">
		<b>{_GOODS_STEP3_TYPE_GOOD}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi1')" onMouseOver="show_help('hi1')">
		</td>
		<td align="left" class="contlight">{TYPE_GOOD}
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark"><b>{_GOODS_DATE_ADD}</b>:</td>
		<td align="left" class="contlight">{DATE_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark"><b>{_GOODS_STEP3_CATEGORY}</b>:</td>
		<td align="left" class="contlight">
			<input type="hidden" name="cat_good" value="{CAT_GOOD}">
			<input type="text" name="category_good" size="60" value="{CATEGORY_GOOD}" disabled class="inputtext">
			<input type="button" value="{_GOODS_CATEGORY_SELECT}" onClick="javascript:open_window('{URLSITE}/exepanel/goods.php?type=category&',850,600)" class="inputbutton">
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_ARTICUL}</b> : </span>
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi2')" onMouseOver="show_help('hi2')">
		</td>
		<td align="left" class="contlight"><input type="text" name="articul_good" value="{ARCTICUL_GOOD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_PRICE}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi3')" onMouseOver="show_help('hi3')">
		</td>
		<td align="left" class="contlight"><input type="text" name="price_good" onkeyup="maxi('price');" size="6" value="{PRICE_GOOD}" class="inputcenter"> y.e.</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_SKIDKA}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi4')" onMouseOver="show_help('hi4')">
		</td>
		<td align="left" class="contlight"><input type="text" name="skidka_good" onkeyup="maxi('skidka');" size="4" value="{SKIDKA_GOOD}" class="inputcenter"> %
		&nbsp;
		max <span id="skidka">0</span> %
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_AGENT}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi5')" onMouseOver="show_help('hi5')">
		</td>
		<td align="left" class="contlight"><input type="text" name="agent_good" onkeyup="maxi('agent');" size="4" value="{AGENT_GOOD}" class="inputcenter"> %
		&nbsp;
		max <span id="agent">0</span> %
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_SKLAD}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi6')" onMouseOver="show_help('hi6')">
		</td>
		<td align="left" class="contlight"><input type="text" name="sklad_good" size="4" value="{SKLAD_GOOD}" {SKLAD_GOOD_CHECK} class="{SKLAD_GOOD_CLASS}"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_TITLERU}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi7')" onMouseOver="show_help('hi7')">
		</td>
		<td align="left" class="contlight"><input type="text" name="titleru_good" size="60" value="{TITLERU_GOOD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="250">
		<b>{_GOODS_STEP3_TITLEEN}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi7')" onMouseOver="show_help('hi7')">
		</td>
		<td align="left" class="contlight"><input type="text" name="titleen_good" size="60" value="{TITLEEN_GOOD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>{_GOODS_STEP3_DESCRRU}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi8')" onMouseOver="show_help('hi8')">
		</td>
		<td align="left" class="contlight"><textarea name="descrru_good" rows="10" cols="60" class="inputarea">{DESCRRU_GOOD}</textarea></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>{_GOODS_STEP3_DESCREN}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi8')" onMouseOver="show_help('hi8')">
		</td>
		<td align="left" class="contlight"><textarea name="descren_good" rows="10" cols="60" class="inputarea">{DESCREN_GOOD}</textarea></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>{_GOODS_STEP3_ADDITIONALRU}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi9')" onMouseOver="show_help('hi9')">
		</td>
		<td align="left" class="contlight"><textarea name="addtitionalru_good" rows="10" cols="60" class="inputarea">{ADDITIONALRU_GOOD}</textarea></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>{_GOODS_STEP3_ADDITIONALEN}</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi9')" onMouseOver="show_help('hi9')">
		</td>
		<td align="left" class="contlight"><textarea name="addtitionalen_good" rows="10" cols="60" class="inputarea">{ADDITIONALEN_GOOD}</textarea></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>Meta description</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi10')" onMouseOver="show_help('hi10')">
		</td>
		<td align="left" class="contlight"><input type="text" name="meta_desc_good" size="60" value="{META_DESC_GOOD}" class="inputtext"></td>
	</tr>
	<tr>
		<td align="right" valign=top class="contdark" width="250">
		<b>Meta keywords</b>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi11')" onMouseOver="show_help('hi11')">
		</td>
		<td align="left" class="contlight"><input type="text" name="meta_key_good" size="60" value="{META_KEY_GOOD}" class="inputtext"></td>
	</tr>
</table>
<p>
<input type="button" value="{_INST_SAVE}" id="btn" onclick="submitform('{_INST_SAVE}?')"; class="inputbutton">
</p>
</form>

<DIV class="hi" id="hi1">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_TYPE_GOOD_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi2">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_ARTICUL_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi3">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_PRICE_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi4">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_SCIDKA_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi5">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_AGENT_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi6">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_SKLAD_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi7">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_TITLE_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi8">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_DESCR_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi9">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_ADDITIONAL_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi10">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_METADESC_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hi11">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP3_METAKEY_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>