
<script language="JavaScript">
function submitform(mes){
	if(confirm(mes)) document.checkgood.submit();
}
function maxi(id) {
	var price_good = document.all['price_good'];
	var skidka_good = document.all['skidka_good'];
	var agent_good = document.all['agent_good'];
	var max = Math.floor((price_good.value - 0.01) / (price_good.value * 0.01));
	if(price_good.value >= 0.01) {
		if(id == 'skidka') if((max - agent_good.value - skidka_good.value) < 0) skidka_good.value=max - agent_good.value;
		if(id == 'agent') if((max - agent_good.value - skidka_good.value) < 0) agent_good.value=max - skidka_good.value;
		document.all['skidka'].innerHTML = max - agent_good.value;
		document.all['agent'].innerHTML = max - skidka_good.value;
	} 
	else {
		document.all['skidka'].innerHTML =0;
		document.all['agent'].innerHTML =0;
		skidka_good.value = 0;
		agent_good.value = 0;
	}
}
</script>

<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>
<br>
<span class="text"><b>{_GOODS_STEP3}</span>
<br><br>
<form name="checkgood" method="POST" action="{URLSITE}/exepanel/goods.php?type=add&step=4&">
<input type="hidden" name="step" value="3">
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
</form>
<p>
<input type="button" value="{_INST_BACK}" onclick="window.location='{URLSITE}/exepanel/goods.php?type=add&step=2'" class="inputbutton">
<input type="button" value="{_GOODS_ADD}" onclick="javascript:submitform('{_GOODS_STEP3_FORM_ADD_CONFIRM}?')"; class="inputbutton">
</p>
</div>

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