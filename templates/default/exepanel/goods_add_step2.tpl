<script language="JavaScript">
function select_cat(cat){
	if(confirm("{_GOODS_CATEGORY_SELECT}?")) {
		document.all("cat_good").value=cat;
		document.all("category").submit();
	}
}
</script>
<form name="category" action="{URLSITE}/exepanel/goods.php?type=add&step=3&" method="POST">
<input type="hidden" name="cat_good" value="1">
<input type="hidden" name="step" value="2">
</form>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>
<br>
<span class=text><b>{_GOODS_STEP2}</span>
<br><br>

<table align="center" width="50%" border="0">
{CATEGORY_LIST}
</table>
<p><input type="button" value="{_INST_BACK}" onclick="window.location='{URLSITE}/exepanel/goods.php?type=add&step=1'" class="inputbutton"></p>
</div>
