<div id="formupdate" style="border:0px;">
<span class="text"><b>{_GOODS_STEP4_ADD_UPDATE}</span>
<br><br>
<form id="apost"  method="POST" enctype="multipart/form-data" onsubmit="return false" >
<input type="hidden" name="step" value="4">
<input type="hidden" name="nextadd" value="yes">
<table align='center' border="1" width="80%" class='dash' cellpadding='5' cellspacing='0'>
	{TABLE_TYPE_GOOD}
</table>
<p><input type="button" name="buttonload" value="{_GOODS_STEP4_ADD_UPDATE_ACTION}" onClick="doLoad(document.getElementById('apost'))" class="inputbutton"></p>
</form>

</div>

