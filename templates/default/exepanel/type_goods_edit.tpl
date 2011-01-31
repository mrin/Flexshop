<script language="JavaScript">
function  number(summa){
	var LenValue = summa.toString().length;
	if(LenValue == 3) return false;
	var LastSimvol;
	for(i=0; i<LenValue; i++){
		LastSimvol = summa.toString().substring(i, i+1);
		if(LastSimvol != '1' && LastSimvol != '2' && LastSimvol != '3' && LastSimvol != '4' && LastSimvol != '5'
		&& LastSimvol != '6' && LastSimvol != '7' && LastSimvol != '8' && LastSimvol != '9' && LastSimvol != '0'
		){
			return false;
		}
		if((LastSimvol == '.' ) && i==0){
			return false;
		}
	}
	return true;
}
function check() {
	if(document.all) {
			if(!number(type_goods.inputtext.value)) {
			type_goods.inputtext.value='0'; alert('{_TYP_ERRNUMB}');
			return;
			}
			if(!number(type_goods.inputarea.value)) {
			type_goods.inputarea.value='0'; alert('{_TYP_ERRNUMB}');
			return;
			}
			type_goods.submit();
	}
}
function submits() {
	if(confirm('{_INST_SAVE} ?')) type_goods2.submit();
}
</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<span class=text><b>{_TYP_CHANGE}:</span> <font color=red style="font-size:8pt; font-family : Tahoma;">{TYPNAME}</font>
<form name="type_goods" action="{URLSITE}/exepanel/type_good.php?type=edit&method=add&typeid={ID}&" method=POST>
	<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_INPUTTEXT}: </span></td>
		<td align=left class="contlight"> <input type="text" name="inputtext" size=2 value="0" class="inputcenter"></td>
	</tr>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_TEXTAREA}: </span></td>
		<td align=left class="contlight"> <input type="text" name="inputarea" size=2 value="0" class="inputcenter"></td>
	</tr>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_INPUTFILE}: </span></td>
		<td align=left class="contlight"> <input type="checkbox" name="file" value="1" {CHECK}></td>
	</tr>
	</table>
<p><input type="button" onclick="check()" value="{_TYP_FIELD_ADD}" class=inputbutton></p>
</form>
<br>
<form name="type_goods2" action="{URLSITE}/exepanel/type_good.php?type=edit&method=change&typeid={ID}&" method=POST>
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=80>{_TYP_TYPFIELD}</td>
		<td align=center class="conttop" width=220>{_TYP_NAMEFIELD} *</td>
		<td align=center class="conttop" width=400>{_TYP_NAME_SETTING_FIELD}</td>
		<td align=center class="conttop" width=80>{_TYP_FIELD_SORT}</td>
		<td align=center class="conttop" width=80>{_CP_DEL}</td>
	</tr>
{TYPE_LIST}
</table>
<p><input type="button" onclick="submits()" value="{_INST_SAVE}" class=inputbutton></p>
<p><input type="button" onclick="window.location='{URLSITE}/exepanel/type_good.php?type=show';" value="{_INST_BACK}" class=inputbutton></p>
</form>
<small class="text">* {_LABEL_OBLIG}</small><br><br>
</div>