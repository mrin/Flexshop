<div>
<a class="inmenu" href="{URLSITE}/exepanel/news.php?type=add">{_NEWS_ADD}</a>::<a class="inmenu" href="{URLSITE}/exepanel/news.php?type=show">{_NEWS_LIST}</a>
<br><br>
<p align=center><span class="text">{PAGES_NEWS}</span></p>
<table align='center' width="95%" border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width="100">{_NEWS_DATE}</td>
		<td align=center class="conttop">{_NEWS_MSGNEWS}</td>
		<td align=center class="conttop" width="70">{_NEWS_STATUS}</td>
		<td align=center class="conttop" width="70">{_NEWS_ACTION}</td>
	</tr>
	<!-- TR TD INSERT FROM PHPCODE -->
	{LIST_NEWS}
</table>

<p align=center>{PAGES_NEWS}</p>
<div style="padding-left:20px;">
<table align='left' width="30%" class=text cellpadding=0 cellspacing=0>
	<tr>
		<td width="15"><img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/ok.gif"></td>
		<td> - <b>{_NEWS_STATUS1}</td>
	</tr>
	<tr>
		<td><img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/no.gif"></td>
		<td> - <b>{_NEWS_STATUS0}</td>
	</tr>	
</table>
</div>
</div>