<html>
<head>
<title>{_CP_TITLECP}</title>
<link rel="SHORTCUT ICON" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/favicon.ico">
<style type="text/css">
.conttop {
	font-family : Tahoma;
	font-size:8pt;
	background: url('{URLSITE}/templates/{TEMPLDEF}/exepanel/img/cont_top.gif') repeat-x top;
	color:#ffffff;
	font-weight:bold;
	border-color: #a6a6a7;
	background-color:#c5d6ed;
} 
.contlight {
	border-color: #c3c3c3;
	background-color:#fcf8f1;
	font-family:Verdana; 
	font-size: 8pt; 
	color:#020e3b; 
}
.contdark { 
	border-color: #c3c3c3;
	background-color:#e0e5ed;
	font-family:Verdana; 
	font-size: 8pt; 
	color:#020e3b; 
}
.onover {
	border-color: #c3c3c3;
	background-color:#e9d988;
	font-family:Verdana; 
	font-size: 8pt; 
	color:#020e3b; 
}
.text{
	font-family:Verdana; 
	font-size: 8pt; 
	color:#020e3b; 
	text-decoration:none; 
} 
.err_err {
	font-family:Verdana; 
	font-size:8pt;
	font-weight:bold;
	color:#FFFFFF; 
	text-decoration:none;
	background-color:#eb0000; 
	border:1px solid #916614; 
}
.err_succ {
	font-family:Verdana; 
	font-weight:bold;
	font-size:8pt; 
	color:#000000; 
	text-decoration:none;
	background-color:#7cff94; 
	border:1px solid #916614; 
}
} 
</style>
</head>
<body>
<p align="center">
{ERROR}
<br>
<span class="text"><b>{_SELL_STAT_LABEL2_PERIOD} {_SELL_STAT_WITH}: {FROM} {_SELL_STAT_TO}: {TO}</span>
<br><br>
<table cellpadding="5" cellpadding="5" width="750" align="center">
	{LIST_STAT}
</table>
<br><span class="text"><b>{TOTAL_SUMM} , {TOTAL_TRANS}</b></span>
</p>
</body>
</html>