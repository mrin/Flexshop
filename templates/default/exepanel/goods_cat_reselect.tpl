<html>
<head>
<title>{_CP_TITLECP}</title>
<link rel="SHORTCUT ICON" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/favicon.ico">
<style type="text/css">
.text{
	font-family:Verdana; 
	font-size: 8pt; 
	color:#020e3b; 
	text-decoration:none; 
} 
.text:link{
	font-family:Verdana; 
	font-size: 8pt;  
	color:#020e3b; 
	text-decoration:none; 
} 
img {border:none;}
a.none {font-family:Verdana; color:#261bff; text-decoration:none; font-size:7pt;} 
.inputbutton {
	color:#FFFFFF; 
	height:20px; 
	font-size : 8pt; 
	background:url('{URLSITE}/templates/{TEMPLDEF}/exepanel/img/bg_button.gif') top repeat-x;
	background-color:#1d539e; 
	border:1px solid #02124c; 	
}
.text:hover{
	font-family:Verdana; 
	font-size: 8pt; 
	color:#000000; 
	text-decoration:none; 
} 
</style>
</head>
<body>
<script language="JavaScript">
function select_cat(id_r, razdel){
	window.top.opener.parent.document.all.cat_good.value = id_r;
	window.top.opener.parent.document.all.category_good.value = razdel;
	window.parent.close();
}
</script>
<p align="center">
<br>
<span class=text><b>{_GOODS_STEP3_CATEGORY}</span>
<br><br>
<table align="center" width="50%" border="0">
{CATEGORY_LIST}
</table>
<input class="inputbutton" type="button" value="{_CP_CLOSE}" onclick="window.parent.close();"></p>
</p>
</body>
</html>
