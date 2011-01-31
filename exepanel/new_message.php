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
// Добавление в очередь рассылку	
function add_queue_msg($mail, $subject, $msg, $setting) {
GLOBAL $template, $mysql,$m;
	$subject = base64_encode(strip_tags(trim($subject)));
	$m[BODYMSG] = nl2br(str_replace(array('\"', "\'"), array('"',"'"), strip_tags(trim($msg))));
	// Подпись
	$body = base64_encode($template->show_contxt(nl2br(str_replace(array('\"', "\'"), array('"',"'"), strip_tags(trim($setting[sign_msg]))))));
	$mysql->query("INSERT INTO email_queue VALUES('', 2, '".$mail."', 'windows-1251', '".$subject."', '".$body."')");
}

//************************************************************************ 
// Создание рассылки
function manage_compose() {
GLOBAL $template,$m, $mysql;
	if(getfrompost("action") == "yes") {
		(int) getfrompost("all") == 1 ? $all=1 : $all=0;
		$m[SUBJECT] = getfrompost("subject");
		$m[MSG] = getfrompost("msg");
		$m[RECIPIENT] = getfrompost("rec");
		if(strlen(trim($m[MSG])) > 2) {
		
		// Выборка подписи
		$setting = $mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=2");
		$setting = strip($mysql->fetch_array($setting));
		
		// Если отправка всем
		if($all) {
			$r = $mysql->query("SELECT mail FROM mypurchase");
			$rows=$mysql->num_rows($r);
			if($rows>0)
				while($row = $mysql->fetch_array($r))
					{
						$row=strip($row);
						add_queue_msg($row[mail], $m[SUBJECT], $m[MSG], $setting);
					}
			$template->logmsg($template->show_contxt("{_SEND_MAIL_COMPOSED} - $rows"), 1);
		}
		
		// Если отправка выборочно
		if(!$all) {
			$rec = explode(",", trim($m[RECIPIENT]));
			if(count($rec)>0) {
				for($i=0;$i<count($rec);$i++) {
					if($i <> count($rec)-1) $or="OR"; else $or="";
					if(strlen(trim($rec[$i])) > 3)
						$sql.="login='".addslashes(trim($rec[$i]))."' $or ";
				}
				$r = $mysql->query("SELECT mail FROM mypurchase WHERE $sql ");
				$rows=$mysql->num_rows($r);
				if($rows>0)
					while($row = $mysql->fetch_array($r))
						{
							$row=strip($row);
							add_queue_msg($row[mail], $m[SUBJECT], $m[MSG], $setting);
						}
				$template->logmsg($template->show_contxt("{_SEND_MAIL_COMPOSED} - $rows"), 1);
			}	
		}
	} else $template->logtxt("_SEND_MAIL_ERROR",0);
	} else 
		$m[RECIPIENT] = $template->show_contxt("{_SEND_MAIL_FIELD_INFO}");
	return $template->show_content("/exepanel/new_message.tpl");
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_SEND_MAIL_INFO"];
		$m["CENTERCONTENT"]=manage_compose();
		
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>