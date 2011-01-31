<div style="padding-left:5px;padding-right:5px;">
<p>
<b>
<a href="{URLSITE}/exepanel/mygoods.php?method=customers" class=inmenu>{_CUTSOMER}</a> ::
<a href="{URLSITE}/exepanel/mygoods.php?method=request_money" class=inmenu>{_CUSTOMER_REQUEST_PAYMENTS}</a> ::
<a href="{URLSITE}/exepanel/mygoods.php?method=option_fields" class=inmenu>{_CUTSOMER_OPTION_MENU}</a>
</b>
</p>
	<div id="infolabel">{_CUTSOMER_OPTION_INFO}</div>
</br>
{INFO_MESSAGE}
<form action="{URLSITE}/exepanel/mygoods.php?method=option_fields" method=POST>
<input type="hidden" name="type" value="add">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=150>{_CUTSOMER_OPTION_NAME} (RUS) *</td>
		<td align=center class="conttop" width=150>{_CUTSOMER_OPTION_NAME} (ENG) *</td>
		<td align=center class="conttop" width=100>{_CUTSOMER_OPTION_TYPE}</td>
	</tr>
	<tr>
		<td align=center class="contlight" width=150><input class="inputtext" type="text" name="nameru" value=""></td>
		<td align=center class="contlight" width=150><input class="inputtext" type="text" name="nameen" value=""></td>
		<td align=center class="contlight" width=100>
			<table>
				<tr class="contlight">
					<td><input type="radio" name="type_field" value="1" checked></td>
					<td>{_CUTSOMER_OPTION_TYPE1}</td>
				</tr>
				<tr class="contlight">
					<td><input type="radio" name="type_field" value="3"></td>
					<td>{_CUTSOMER_OPTION_TYPE3}</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p align="center"><input class="inputbutton" type="submit" value="{_CP_ADD}"></p>
</form>
<small class="text">* {_LABEL_OBLIG3}</small><br><br>

<p>
{INFO_MESSAGE2}
<form action="{URLSITE}/exepanel/mygoods.php?method=option_fields" method=POST>
<input type="hidden" name="type" value="edit">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=150>{_CUTSOMER_OPTION_NAME} (RUS) *</td>
		<td align=center class="conttop" width=150>{_CUTSOMER_OPTION_NAME} (ENG) *</td>
		<td align=center class="conttop" width=100>{_CUTSOMER_OPTION_TYPE}</td>
		<td align=center class="conttop" width=50>{_CUTSOMER_OPTION_SORT}</td>
		<td align=center class="conttop" width=50>{_CUTSOMER_OPTION_DEL}</td>
	</tr>
	{OPTION_LIST}
</table>
</form>
</p>
</div>