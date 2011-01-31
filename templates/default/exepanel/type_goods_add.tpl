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
</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;"><br>
<span class=text><b>{_TYP_CREATEINFO}</span>
<br><br>
<form name="type_goods" action="{URLSITE}/exepanel/type_good.php?type=add&method=add&step=0&" method=POST>
<input type=hidden name="id" value="{ID}">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=right class="contdark" width=200><span class=text><b>{_TYP_NAMERU}: </span></td>
		<td align=left class="contlight" width=150> <input type="text" name="nameru" value="{NAMERU}" class="inputtext"></td>
	</tr>
	<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_NAMEEN}: </span></td>
		<td align=left class="contlight"> <input type="text" name="nameen" value="{NAMEEN}" class="inputtext"></td>
	</tr>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_INPUTTEXT}: </span></td>
		<td align=left class="contlight"> <input type="text" name="inputtext" size=2 value="{TEXT}" class="inputcenter"></td>
	</tr>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_TEXTAREA}: </span></td>
		<td align=left class="contlight"> <input type="text" name="inputarea" size=2 value="{AREA}" class="inputcenter"></td>
	</tr>
		<tr>
		<td align=right class="contdark"><span class=text><b>{_TYP_INPUTFILE}: </span></td>
		<td align=left class="contlight"> <input type="checkbox" name="file" value="1" {CHECK}></td>
	</tr>
</table>
<p><input type="button" onclick="check()" value="{_TYP_NEXTSTEP}" class=inputbutton></p>
</form>
</div>