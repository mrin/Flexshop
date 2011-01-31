<div style="padding-top:10px;">
<br>
<span class="text"><b>{_GOODS_UPDATING_LABEL}</span>
<table align='center' border="1" width="700" class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP4_IDGOOD}</b></a>:</td>
		<td align="left" class="contlight">{ID_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP3_TYPE_GOOD}</b></a>:</td>
		<td align="left" class="contlight">{TYPE_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP1_PROP}</b></a>:
		</td>
		<td align="left" class="contlight">{PROP_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP4_NAMEGOOD}</b></a>:</td>
		<td align="left" class="contlight">{NAME_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP3_PRICE}</b></a>:</td>
		<td align="left" class="contlight">{PRICE_GOOD} y.e.</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP4_COUNTGOOD}</b></a>:
		</td>
		<td align="left" class="contlight"><b><span id="kolvo">{COUNT_GOOD}</span> &nbsp;&nbsp; 
		<span id="btnupdating">{BTN_UPDATE_GOOD}</span></a>
	<br>
</b></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_UPDATING_KEY}</b></a>:
		</td>
		<td align="left" class="contlight">
			{MEM_KEY}
		</td>
	</tr>
</table>
<p><input class="inputbutton" type="button" onClick="window.location='{URLSITE}/exepanel/goods.php?type=view&tolistgood={CAT_GOOD}'" value="{_GOODS_UPDATING_GOTO_GOODS}"></p>

<div id="loader2"></div>
<div id="update_msg"></div>
<div id="secret_list"></div>

</div>
<!-- BOX FOR DELETE -->
<div id="delblock" class="boxblock">
<div id="delmoveblock" class="boxmoveblock" onmousedown="BoxDiv('delblock')"><b>{_GOODS_DELETE}</b><span id="idtovid"></span></div>
<br>{_GOODS_DELETE_INFO}</br><br>
<b>{_GOODS_DELETE_CONTINUE} ?</b>
<br><br>
<input type="button" class="inputbutton" value="{_CP_YES}" onclick="updating(3, '', '', '', false)"> &nbsp;
<input type="button"  class="inputbutton" value="{_CP_NO}" onclick="hidediv(document.getElementById('delblock'))">
<br><br>
</div>

<!-- BOX FOR ADD -->
<div id="addblock" class="boxblock2">
<div id="addmoveblock" class="boxmoveblock" onmousedown="BoxDiv('addblock')"><b>{_GOODS_STEP4_ADD_UPDATE}</b></div>
<div id="errorupdate"></div>
<form id="apost"  method="POST" enctype="multipart/form-data" onsubmit="return false" >
<input type="hidden" name="nextadd" value="yes">
<table align='center' class='dash' cellpadding='5' cellspacing='0'>
	{TABLE_TYPE_GOOD}
</table>
<br><br>
<input type="button" class="inputbutton" name="btn" value="{_GOODS_STEP4_ADD_UPDATE_ACTION}" onclick="updating(4, {ID_GOOD}, document.getElementById('apost'), '', true)"> &nbsp;
<input type="button" class="inputbutton" value="{_CP_CLOSE}" onclick="hidediv(document.getElementById('addblock'))"> &nbsp;
</form>
<br>
</div>

<div class="hi" id="hifile">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="left"><span class="text">{_GOODS_STEP4_TYPE_FILE_HELP}</span></p>
		</td>
	</tr>
</table>
</div>