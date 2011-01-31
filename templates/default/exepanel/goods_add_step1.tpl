<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>
<br>
<span class=text><b>{_GOODS_STEP1}</span>
<form action="{URLSITE}/exepanel/goods.php?type=add&step=2&" method="POST">
<input type="hidden" name="step" value="1">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="180">
			<b>{_GOODS_STEP1_PROP}</b> :<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi1')" onMouseOver="show_help('hi1')"></td>
		<td align="left" class="contlight" width="400">
			<input type="radio" name="prop_good" value="0" {CHECK_PROP_GOOD0}> {_GOODS_STEP1_PROP0}<br>
			<input type="radio" name="prop_good" value="1" {CHECK_PROP_GOOD1}> {_GOODS_STEP1_PROP1}<br>
			<input type="radio" name="prop_good" value="2" {CHECK_PROP_GOOD2}> {_GOODS_STEP1_PROP2}
		</td>
	</tr>
</table>
<p><input type="submit" value="{_GOODS_ADD_NEXT}" class="inputbutton"></p>
</form>
<br>
</div>

<DIV class="hi" id="hi1">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP1_PROP_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>