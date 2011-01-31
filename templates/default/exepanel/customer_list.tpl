<div style="padding-left:5px;padding-right:5px;">
<p>
<b>
<a href="{URLSITE}/exepanel/mygoods.php?method=customers" class=inmenu>{_CUTSOMER}</a> ::
<a href="{URLSITE}/exepanel/mygoods.php?method=request_money" class=inmenu>{_CUSTOMER_REQUEST_PAYMENTS}</a> ::
<a href="{URLSITE}/exepanel/mygoods.php?method=option_fields" class=inmenu>{_CUTSOMER_OPTION_MENU}</a>
</b>
</p>
<span class="text">{_CUSTOMER_INFO}</span>
<p>
<form action="{URLSITE}/exepanel/mygoods.php?method=customers" method="POST">
<input type="hidden" name="type" value="search">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="contdark">{_CUSTOMER_REQUEST_PAYMENTS_LOGIN}: </td>
		<td align=center class="contlight"><input class="inputtext" type="text" name="login" size="15"></td>
		<td align=center class="contlight"><input class="inputbutton" type="submit" value="{_PAYMENT_SEARCH}"></td>
	</tr>
</table>
</form>
</p>

<p>
{INFO_MESSAGE}
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
	<tr>
		<td align=center class="conttop" width="80">{_CUSTOMER_REQUEST_PAYMENTS_LOGIN}</td>
		<td align=center class="conttop" width="80">{_CUSTOMER_FULLNAME}</td>
		<td align=center class="conttop" width="80">{_CUSTOMER_AGENT_AMOUNT} (y.e.)</td>
		<td align=center class="conttop" width="80">{_CUSTOMER_EMAIL}</td>
		<td align=center class="conttop" width="80">{_CUSTOMER_REQUEST_PAYMENTS_STATUS}</td>
		<td align=center class="conttop" width="80">{_CUSTOMER_PAYED}</td>
		<td align=center class="conttop">{_NEWS_ACTION}</td>
	</tr>
	{CUSTOMER_LIST}
</table>
</p>
<p>{PAGES_NEWS}</p>
</div>