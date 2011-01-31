<?
session_start();
$pth="./";
###################БЛОК ВКЛЮЧЕНИЙ##############################

@include "$pth/lib/global/global_include.php";
@require_once($pth."/lib/modules/mail/smtp_class.php");
@require_once "functions.php";

// Инициализация класса и подключение к базе данных
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);

// Инициализация класса работы с логгированием и шаблонизацией
$mytpl = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);
$templator = new templator($homedir, $deftpl);

// Функции управления интернет магазином
$control = new shop_function($mysql, $templator);

// Инициализация класса Nested Tree (дерево каталога)
$tree = new DBTree($mysql,"category","id");

// Загрузка шаблона
$tpl = $templator->readTemplateFromFile("/shop/main_template.tpl", "/shop/contacts.tpl");
	if(!$tpl) die("Read template failed");	
		
// Заполнение шаблона переводом, массива $m
$templator->create_all_vars($m);

//Авторизация
$control->loginvalid(); 
#############################################################

switch(getfrompost("action")) {
	case "logoff":$control->logoff();break;
}


#############################################################
// Создание страницы
$control->create_head_html();

// Отображение блока категорий товаров
$control->show_cat((int)getfromget("n_ct"));

// Форма обратной связи
$control->send_feedback();

//E-mail адреса
$arr = explode('@', $mail);
$templator->setVariable('mail_main_part_1', $arr[0]);
$templator->setVariable('mail_main_part_2', $arr[1]);

// Вывод шаблона
$templator->generateOutput();

?>