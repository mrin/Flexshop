<script language="JavaScript">
// submit form
function submitform(msg) {
	if(confirm(msg)) {
		document.specoffer.submit();
	}
}
</script>
<div style="padding-left:5px;padding-right:5px;padding-bottom:10px;">
<b>
<a href="{URLSITE}/exepanel/goods.php?type=add&" class="inmenu">{_GOODS_ADD}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=view" class="inmenu">{_GOODS_LIST}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=search" class="inmenu">{_GOODS_SEARCH}</a> ::
<a href="{URLSITE}/exepanel/goods.php?type=specoffer" class="inmenu">{_GOODS_SPEC_OFFER}</a>
</b><br>
<div id="infolabel">{_SPEC_OFFER_INFO} <img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/special_offer.gif"></div>
{SPEC_OFFER_LIST}
</div>