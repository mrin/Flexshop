<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>{_CP_TITLECP}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="SHORTCUT ICON" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/css/style.css" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="{URLSITE}/templates/{TEMPLDEF}/exepanel/user_menu.js"></script>
</head>
<body id='main'>
<table align='center' width='800' height='100%' class='dott' cellpadding='0' cellspacing='0'>
	<tr valign='top' height='100%' bgcolor='#FFFFFF'>
		<td align='center'>
			<!--TOP BEGIN-->
			<table width="800" height="150" border="0" cellpadding="0" cellspacing="0">
				<tr height="25" bgcolor="#d8e0ff">
					<td colspan="2" style="border-bottom:1px solid #c2902e;">
						<div>
							<table width="100%" cellpadding="0">
								<tr>
									<td align=left width="25"><img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/help.gif"></td>
									<td align=left>&nbsp;<a target="_blank" href="{URLSITE}/exepanel/help.php" class=text>{_CP_HELP}</a> </td>
									<td align=right><span class="text">{_CP_LANG} -> <a href="{URLSITE}/exepanel/?change_lang=ru" class="text">{_CP_LANGRU}</a>::<a href="{URLSITE}/exepanel/?change_lang=en" class="text">{_CP_LANGEN}</a>&nbsp;</span></td>
								</tr>	
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td rowspan="2" width="166" height="150"style="background-image:url('{URLSITE}/templates/{TEMPLDEF}/exepanel/img/top_01.gif');">&nbsp;</td>
					<td width="634" height="121" style="background-image:url('{URLSITE}/templates/{TEMPLDEF}/exepanel/img/top_02.gif');">&nbsp;</td>
				</tr>
				<tr height="29" valign=top>
					<!--MENU BEGIN -->
					<td width="634" style="background:url('{URLSITE}/templates/{TEMPLDEF}/exepanel/img/top_03.gif')">{TOPMENU}</td>
					<!--MENU END -->
				</tr>	
			</table>
			<!--TOP END -->
			
			<!-- CURDIR BEGIN -->
			<table class="bgcurdir" width='100%' cellpadding="0" cellspacing="0">
				<tr><td>
				<div>
					<table align='center' width='100%' height='25' cellpadding='0' cellspacing='0'>
						<tr height="25" bgcolor="#d8e0ff">
							<td align="center" style="border-bottom:1px solid #c2902e; border-top:1px solid #c2902e;"><span class="text"><b>{CURCAT}</span></td>
						</tr>
					</table>
				</div>
				</td></tr>
			</table>
			<!-- CURDIR END -->
			
			<!-- ERR_SUCCESS BEGIN-->
			{ERROR_SUCCESS}
			<!-- ERR_SUCCESS END-->
			
			<!-- CONTENT BEGIN-->
			{CENTERCONTENT}
			<!-- CONTENT END -->
		</td>
	</tr>
</table>
<center> <font size=1>Copyright © 2007 flexstudio.biz (BY). All rights reserved.</font></center>
</body>
</html>