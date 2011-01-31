<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;"><br>
<span class=text><b>{_PHOTO_GALLERY} № {ID}</b></span><br><br>
<form action="{URLSITE}/exepanel/photo_gallery.php?method=add&good={ID}&" enctype="multipart/form-data" method="POST">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=300><input class="inputtext" type="file" size=32 name="imggood"></td>
		<td align=center class="conttop" width=100><input class="inputbutton" type="submit" value="{_PHOTO_GALLERY_ADD}"></td>
	</tr>
<table>
</form>
<span class=text>* {_LABEL_IMG_EXT}</span>
<br><br>
<form action="{URLSITE}/exepanel/photo_gallery.php?method=change&good={ID}&" enctype="multipart/form-data" method="POST">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width=180>{_PHOTO_GALLERY}</td>
		<td align=center class="conttop" width=120>{_PHOTO_GALLERY_GOOD} *</td>
		<td align=center class="conttop" width=80>{_CP_DEL}</td>
	</tr>
{PHOTO_LIST}
</table>
{BUTTON_CHANGE}
</form><br>
<p><a href="javascript:window.close();" class=inmenu>{_CP_CLOSE}</a></p>
<span class="text">* {_LABEL_IMG_REQ}</span>

</div>