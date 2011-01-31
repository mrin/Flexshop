<?
##Конфигурация текущего выбранного языка##
if(!isset($_SESSION["lang"]))
	{
	session_register("lang");
	if(!empty($deflang)) $_SESSION['lang']=$deflang;  else $_SESSION['lang']="ru";
	}

if(isset($_GET[change_lang]))$_SESSION['lang']=strtolower($_GET['change_lang']);
if(isset($_SESSION["lang"])){
	switch($HTTP_SESSION_VARS['lang']) {
		case "ru": include($pth."/lib/lang/ru.php");break;
		case "en": include($pth."/lib/lang/en.php");break;
		default :  include($pth."/lib/lang/$deflang.php");break;
	}
} 

?>