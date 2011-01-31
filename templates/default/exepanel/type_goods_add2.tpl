<script language="JavaScript">
function submits() {
	if(confirm('{_TYP_ADD_WAIT}')) type_goods.submit();
}
</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;"><br>
<span class=text><b>{_TYP_CREATESTEP2}</span>
<br><br>
<form name="type_goods" action="{URLSITE}/exepanel/type_good.php?type=add&method=add&step=1&" method=POST>
<input type=hidden name="id" value="{ID}">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=80>{_TYP_TYPFIELD}</td>
		<td align=center class="conttop" width=220>{_TYP_NAMEFIELD} *</td>
		<td align=center class="conttop" width=400>{_TYP_NAME_SETTING_FIELD}</td>
		<td align=center class="conttop" width=80>{_TYP_FIELD_SORT}</td>
	</tr>
{TYPE_LIST}
</table>
<p><input type="button" onclick="submits()" value="{_CP_ADD}" class=inputbutton></p>
<p><input type="button" onclick="window.location='{URLSITE}/exepanel/type_good.php?type=add&step=0';" value="{_INST_BACK}" class=inputbutton></p>
</form>
<small class="text">* {_LABEL_OBLIG}</small><br><br>
</div>