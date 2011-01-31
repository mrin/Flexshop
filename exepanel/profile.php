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
// Функция изменения пароля
function changepwd($pwd1,$pwd2,$name)
	{	GLOBAL $mysql,$template, $HTTP_SESSION_VARS;
		if(!empty($pwd1) && !empty($pwd2)){
				// Сравнивание полей паролей
				if(strcasecmp($pwd1,$pwd2)==0){
					if(strlen($pwd1)>=6) {
						$pwd_md5=md5crypt($pwd1);
						// Обновление имени и пароля
						if($mysql->sql_update("UPDATE user_admin SET pwd='$pwd_md5', name='".esc_db($name)."' WHERE login='".$HTTP_SESSION_VARS[login_db]."' LIMIT 1"))
							{
							$HTTP_SESSION_VARS["pwd_my"]=$pwd_md5;
							$HTTP_SESSION_VARS["pwd_db"]=$pwd_md5;
							$HTTP_SESSION_VARS["name_db"]=$name;
							$template->logtxt("_SUCC_CHANGE","1");
							} else $template->logtxt("_ERR_REQUEST","0");
					} else $template->logtxt("_ERR_FEWPWD","0");
				} else $template->logtxt("_ERR_CMPPWD","0");
		} else 
				if(!empty($name))
					{	// Обновление только имени
						if($mysql->sql_update("UPDATE user_admin SET name='".esc_db($name)."' WHERE login='".$HTTP_SESSION_VARS[login_db]."' LIMIT 1"))
							{
							$HTTP_SESSION_VARS["name_db"]=$name;
							$template->logtxt("_SUCC_CHANGE","1");
							} else $template->logtxt("_ERR_REQUEST","0");
					} else $template->logtxt("_ERR_EMPTYLP","0");	
	}
//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
// Реакция на изменение пароля
if(isset($HTTP_GET_VARS["change"]))
	changepwd(trim($HTTP_POST_VARS["pwd1"]),trim($HTTP_POST_VARS["pwd2"]), trim(esc_db($HTTP_POST_VARS["name"])));
	
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_PROFILE"];
		$m["NAME"]=$HTTP_SESSION_VARS["name_db"];
		$m["CENTERCONTENT"]=$template->show_content("/exepanel/profile.tpl");
		
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>