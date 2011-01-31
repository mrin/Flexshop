<?
date_default_timezone_set('Europe/Helsinki');
error_reporting(0);

#Определение ROOT директрории
$locdir=$HTTP_SERVER_VARS["DOCUMENT_ROOT"];
//Подключение конфигурационного файла
include($pth."/lib/global/config.php");
//Подключение мултиязычного модуля #ТОЛЬКО ПОСЛЕ СЕССИИ и КОНФИГУРАЦИОННОГО ФАЙЛА#
include($pth."/lib/lang/conf.php");
//Класс работы с базой данных
require_once($pth."/lib/global/database_class.php");
//Класс шаблонизатора и логирования записей - свой
require_once($pth."/lib/global/log_tpl_parse_class.php");
//Подключение дополнительных функций
include($pth."/lib/global/func.php");
//Класс обработки дерева каталога
include($pth."/lib/global/dbtree.php");
// Класс шаблонизатора - MiniTemplator
require_once($pth."/lib/global/templator_class.php");
#Декрипт пароля к базе данных
if(!isset($_SESSION["xxdbxx"])) $_SESSION["xxdbxx"]=decryptdata($dbpwd);

?>