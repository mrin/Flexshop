<?
session_start();
$pth="../";
###################БЛОК ВКЛЮЧЕНИЙ В global_include.php##############################
@include "$pth/lib/global/global_include.php";
######################################################################

//************************************************************************ 
// Инициализация класса и подключение к базе данных
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);
// Инициализация класса работы с логгированием и шаблонизацией
$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);

//************************************************************************ 
// Настройка тикет-системы
function mail_setting() {
	GLOBAL $template, $mysql, $m;
	if(getfrompost("method")=="change") {
	
		$mail = toLower(getfrompost("mail"));
		$mail = (is_email($mail) ? $mail: "");
		
		// Имя сервиса
		$name = addslashes(htmlspecialchars(getfrompost("name")));
		
		$smtp_server = addslashes(toLower(getfrompost("smtp_server")));
		$smtp_port = (int)getfrompost("smtp_port");
		
		if($smtp_port == 0) $smtp_port = 25;
		
		// Лимит на отправку
		$lim = (int)getfrompost("lim");
		if($lim <= 0) $lim = 5;
		
		
		// Логин и пасс
		$user = addslashes(getfrompost("user"));
		$pwd = addslashes(encryptdata(getfrompost("pwd")));
		
		// Шаблон подписи
		$sign=addslashes(getfrompost("sign"));
		
		
		// Обновление настроек
		$mysql->query("UPDATE ticketsystem_setting SET email='$mail', name='$name', smtp_server='$smtp_server', smtp_port='$smtp_port', 
			login='$user',pwd='$pwd', close_day='$lim', sign_msg='$sign' WHERE tpl_ID=2");
		$template->logtxt("_SUCC_CHANGE",1);
	}
	
		$r=$mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=2");
		$row=strip($mysql->fetch_array($r));
		$m[MAIL]=$row[email];
		$m["NAME"]=$row[name];
		$m[SMTP_SERVER]=$row[smtp_server];
		$m[SMTP_PORT]=$row[smtp_port];
		$m[USER]=$row[login];
		$m[PASSWORD]=decryptdata($row[pwd]);
		$m[SIGN]=$row[sign_msg];
		// Лимит, засунул в close_day
		$m[LIMIT]=$row[close_day];

	
	return $template->show_content("/exepanel/mail_setting.tpl");
}


//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_MAIL_SETTING"];
		$m["CENTERCONTENT"]=mail_setting();
	} else 
		header("LOCATION: $url/exepanel/");
		
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>