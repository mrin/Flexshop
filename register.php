<?
session_start();
$pth="./";
###################БЛОК ВКЛЮЧЕНИЙ##############################

@include "$pth/lib/global/global_include.php";
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
$tpl = $templator->readTemplateFromFile("/shop/main_template.tpl", "/shop/register.tpl");
	if(!$tpl) die("Read template failed");	
		
// Заполнение шаблона переводом, массива $m
$templator->create_all_vars($m);

//Авторизация
$control->loginvalid(); 
#############################################################


#############################################################
// Создание страницы
$control->create_head_html();

//Отображение блока категорий товаров
$control->show_cat((int)getfromget("n_ct"));

// Отображение блока новостей
$control->show_block_news();

// Отображение случайных товаров
$control->show_random_goods();

// Регистрация нового пользователя
$control->register_user();

// Вывод шаблона
$templator->generateOutput();

?>