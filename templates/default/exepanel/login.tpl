<html>
<head>
<title>{_CP_TITLECP}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="SHORTCUT ICON" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="{URLSITE}/templates/{TEMPLDEF}/exepanel/css/style.css" type="text/css">
</head>
<body style="margin-top:40px;text-align:center">
<script language="JavaScript">
	function update_pic() {
		document.getElementById('genid').src='{URLSITE}/exepanel/genid.php?rnd='+Math.random();
	}
</script>

<table width="603" cellspacing="2" cellpadding="0" style="background-color:#FFFFFF; border-collapse:collapse; border:1px solid #000000;" align="center">
  <tr valign="top"> 
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
        <tr> 
          <td height="2" colspan="3">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><img src="{URLSITE}/templates/{TEMPLDEF}/exepanel/img/login_bg.gif" width="100%" height="50"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr valign="top"> 
          <td colspan="3"> 
            <table width="100%" border="0" cellspacing="10" cellpadding="0">
              <tr valign="top"> 
                <td>
				{ERROR_SUCCESS}
					<p align="center"><b>{_INST_ADMIN}</b></p>
					<form action="{URLSITE}/exepanel/index.php?getaccess" method="POST">
					<table align='center' class='dash' cellpadding='5' cellspacing='0'>
							<tr>
								<td align=right><span class=text>{_CP_LOGIN}:</span></td>
								<td><input type="text" name="login" size=16 class="loginform"></td>
							</tr>
							<tr>
								<td align=right><span class=text>{_CP_PWD}:</span></td>
								<td><input type="password" name="pwd" size=16 class="inputtext"></td>
							</tr>
							<tr>
								<td align=right><img id="genid" src="{URLSITE}/exepanel/genid.php" title="{_CP_SECURIMG}" border=0><br>
								<a href="#" onclick="update_pic();" class="none">({_CP_REFRESH})</a>
								</td>
								<td><input type="text" name="img" size=6 class="inputtext"></td>
							</tr>
					</table>
					<p align=center><input type="submit" value="{_CP_ENTER}" class="inputbutton"></p>
					</form>
</td></tr></table>
<br /><br /></center><br></td></tr></table></td></tr>
    </td>
  </tr>
</table>
<br>
<span style="color:#ffffff; font-size:12px;">&copy; FlexStudio.biz 2007</span>
</body>
</html>