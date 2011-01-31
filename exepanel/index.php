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
######################################################################

//************************************************************************ 
// Авторизация из формы
function login($login, $pwd, $img) {
GLOBAL $HTTP_SESSION_VARS, $mysql, $template;
	if(isset($_SESSION["secimg"]) && strcasecmp($img,$HTTP_SESSION_VARS["secimg"])== 0)
		{ 
			if(!empty($login) && !empty($pwd)) {
				$mysql->sql_select("SELECT * FROM user_admin WHERE login='".esc_db($login)."' and pwd='".md5crypt($pwd)."' and id=1");
				if($mysql->row ==1) {
					$res=($mysql->fetcharray());
					$HTTP_SESSION_VARS["login_my"]=$login;
					$HTTP_SESSION_VARS["login_db"]=$res[login];
					$HTTP_SESSION_VARS["pwd_my"]=md5crypt($pwd);
					$HTTP_SESSION_VARS["pwd_db"]=$res[pwd];
					$HTTP_SESSION_VARS["name_db"]=$res[name];
				} else $template->logtxt("_ERR_AUTH", "0");
			} else $template->logtxt("_ERR_AUTH", "0");
		}else $template->logtxt("_ERR_IMG", "0");
}

//************************************************************************ 
// Авторизация
if(isset($HTTP_GET_VARS['getaccess']))
	{
	if(!isset($_SESSION['login_my'])) $_SESSION['login_my'] = '';
	if(!isset($_SESSION['login_db'])) $_SESSION['login_db'] = '';
	if(!isset($_SESSION['pwd_my'])) $_SESSION['pwd_my'] = '';
	if(!isset($_SESSION['pwd_db'])) $_SESSION['pwd_db'] = '';
	if(!isset($_SESSION['name_db'])) $_SESSION['name_db'] = '';
	login($HTTP_POST_VARS["login"],$HTTP_POST_VARS["pwd"],$HTTP_POST_VARS["img"]);
	}
	
//************************************************************************ 
// Выход
if(isset($HTTP_GET_VARS['logout']))
	{
	session_unregister("login_db"); session_unregister("login_my"); session_unregister("pwd_db"); session_unregister("pwd_my"); session_unregister("name_db");
	unset($_SESSION['key_edit_good']);
	}
	
//************************************************************************ 
// Проверка на авторизацию
if(loginvalid()) header("LOCATION: $url/exepanel/profile.php");
	 else 
	 {
		$m["CURCAT"]=$m["_CP_AUTH"];
		$m["ERROR_SUCCESS"]=$template->msglog;
		echo $m["CENTERCONTENT"]=$template->show_content("/exepanel/login.tpl");
	 }

//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	
	//echo $template->show_content("/exepanel/index.tpl");

?>