<div style="padding-left:5px;padding-right:5px;">
<p><b><a href="{URLSITE}/exepanel/category.php?type=add" class=inmenu>{_CAT_ADD}</a> ::
<a href="{URLSITE}/exepanel/category.php?type=show" class=inmenu>{_CAT_SHOW}</a></b></p>
<form action="{URLSITE}/exepanel/category.php?type=edit&method=change&cat={ID}&" method=POST>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=160>{_CAT_PARENT}</td>
		<td align=center class="conttop" width=230>{_CAT_NAMERU} *</td>
		<td align=center class="conttop" width=230>{_CAT_NAMEEN} *</td>
	</tr>
	<tr>
		<td align=center class="contlight"><select name="parentid" class="inputtext">{PARENTNAME}</select></td>
		<td align=center class="contlight"><input type="text" name="nameru" value="{NAMERU}" size=30 class="inputtext"></td>
		<td align=center class="contlight"><input type="text" name="nameen" value="{NAMEEN}" size=30 class="inputtext"></td>
	</tr>
</table>
<p align=center><input type="submit" value="{_CAT_CHANGE}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG3}</small><br><br>
</div>