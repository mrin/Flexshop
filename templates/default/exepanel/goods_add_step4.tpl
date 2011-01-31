<script src="{URLSITE}/templates/{TEMPLDEF}/exepanel/JsHttpRequest.js"></script>
<script language="JavaScript">
function doLoad(value) {
    var req = new JsHttpRequest();
	value.buttonload.disabled = true;
	document.getElementById('info').innerHTML="<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/loader.gif'><br>";
	value.buttonload.value='{_GOODS_STEP4_WAIT}';
	var mn = document.getElementById('main');
	mn.style.cursor='wait';
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
				document.getElementById('info').innerHTML = req.responseJS.msg;
				document.getElementById('kolvo').innerHTML = req.responseJS.kolvo;
				if(req.responseJS.visibility == 'visible') document.getElementById('formupdate').style.visibility='visible';
					else 
						with(document.getElementById('formupdate')) {
							style.visibility='hidden';
							style.position='absolute';
							style.top=0;
							style.left=0;
						}
				value.buttonload.value='{_GOODS_STEP4_ADD_UPDATE_ACTION}';
				mn.style.cursor='default';
				value.buttonload.disabled = false;
				value.reset();
        }
    }
    req.open(null, '{URLSITE}/exepanel/goods.php?type=add&', true);
    req.send( { q: value } );
}

</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>
<br>
<span class="text"><b>{_GOODS_STEP4}</span>
<br><br>
<form method="POST" action="{URLSITE}/exepanel/goods.php?type=add">
<input type="hidden" name="step" value="4">
<input type="hidden" name="finish" value="yes">
<table align='center' border="1" width="80%" class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP4_IDGOOD}</b></a>:</td>
		<td align="left" class="contlight">{ID_GOOD}</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP1_PROP}</b></a>:
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi1')" onMouseOver="show_help('hi1')">
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
		<img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hi2')" onMouseOver="show_help('hi2')">
		</td>
		<td align="left" class="contlight"><b><span id="kolvo">{COUNT_GOOD}</span></b></td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_PHOTO_GALLERY}</b></a>:</td>
		<td align="left" class="contlight">
		<a target="_blank" href="{URLSITE}/exepanel/photo_gallery.php?good={ID_GOOD}" title="{_CP_ADD}"><img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/photo.gif"></a>
		</td>
	</tr>
	<tr>
		<td align="right" class="contdark" width="180"><b>{_GOODS_STEP4_ONOFF}</b></a>:</td>
		<td align="left" class="contlight"><input type="checkbox" name="check_onoff_good" value="1"></td>
	</tr>
</table>
<p>
<input type="submit" value="{_GOODS_STEP4_SAVE}" class="inputbutton">
</p>
</form>
<span id="bug"></span>
<span id="info"></span>
{ADD_UPDATE}

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
<DIV class="hi" id="hi2">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="justify"><span class="text">{_GOODS_STEP4_COUNTGOOD_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>
<DIV class="hi" id="hifile">
<table cellSpacing="4" cellPadding="4" width="100%" border="0">
	<tr>
		<td>
		<p align="left"><span class="text">{_GOODS_STEP4_TYPE_FILE_HELP}</span></p>
		</td>
	</tr>
</table>
</DIV>