<div>
<div style="padding-top:10px">
<span class=text>{_SHIPPING_INFO}<br><br><b>{_SHIPPING_EDIT}</span><br><br>
<form name="shipping" action="{URLSITE}/exepanel/modshipping.php?type=edit&" method="POST">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=150>{_SHIPPING_NAME} *</td>
		<td align=center class="conttop" width=100>{_SHIPPING_FIXAMOUNT} **</td>
		<td align=center class="conttop" width=170>{_SHIPPING_TYP} *</td>
		<td align=center class="conttop" width=200>{_SHIPPING_DESCR}</td>
		<td align=center class="conttop" width=50>{_CP_DEL}</td>
	</tr>
{SHIPPING_LIST}
</table>
<p align=center><input type="submit" value="{_INST_SAVE}" class="inputbutton"></p>
</form>
</div>

<span class=text><b>{_SHIPPING_ADD}</span><br><br>
<form action="{URLSITE}/exepanel/modshipping.php?type=add&" method="POST">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=150>{_SHIPPING_NAME} *</td>
		<td align=center class="conttop" width=150>{_SHIPPING_FIXAMOUNT} **</td>
		<td align=center class="conttop" width=170>{_SHIPPING_TYP} *</td>
		<td align=center class="conttop" width=250>{_SHIPPING_DESCR}</td>
	</tr>
	<tr>
		<td align=center class="contlight" width=150><input type=text maxlength=50 name="name" size=20 class="inputtext"></td>
		<td align=center class="contlight" width=150><input type=text name="amount" size=6 class="inputtext"></td>
		<td align=center class="contlight" width=170><input type=radio name="typ" value="0" class="inputtext"> / <input type=radio name="typ" value="1" checked class="inputtext"></td>
		<td align=center class="contlight" width=250><textarea name="descr" rows=5 cols=30 class="inputarea"></textarea></td>
	</tr>	
</table>
<p align=center><input type="submit" value="{_CP_ADD}" class="inputbutton"></p>
</form>
<small class="text">* {_LABEL_OBLIG}<br>** {_LABEL_SHIPPING}</small><br><br>
</div>