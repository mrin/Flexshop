<div style="padding-left:5px;padding-right:5px;">
<p><b><a href="{URLSITE}/exepanel/category.php?type=add" class=inmenu>{_CAT_ADD}</a> ::
<a href="{URLSITE}/exepanel/category.php?type=show" class=inmenu>{_CAT_SHOW}</a></b></p>
<form action="{URLSITE}/exepanel/category.php?type=move&method=change&cat={ID}&" method=POST>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=160>{_CAT_POSITION_CAT}</td>
		<td align=center class="conttop" width=230>{_CAT_POSITION}</td>
		<td align=center class="conttop" width=230>{_CAT_NAME}</td>
	</tr>
	<tr>
		<td align=center class="contlight"><select name="to" class="inputtext">{POSITION_CAT}</select></td>
		<td align=center class="contlight"><select name="after_before" class="inputtext"><option value="before">{_CAT_BEFORE}<option value="after">{_CAT_AFTER}</select></td>
		<td align=center class="contlight"><span class=text>{NAME}</span></td>
	</tr>
</table>
<p align=center><input type="submit" value="{_CAT_CHANGE_POSITION}" class="inputbutton"></p>
</form>

</div>