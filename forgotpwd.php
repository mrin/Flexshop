<?
session_start();
$pth="./";
###################���� ���������##############################

@include "$pth/lib/global/global_include.php";
@require_once "functions.php";

// ������������� ������ � ����������� � ���� ������
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);

// ������������� ������ ������ � ������������� � �������������
$mytpl = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);
$templator = new templator($homedir, $deftpl);

// ������� ���������� �������� ���������
$control = new shop_function($mysql, $templator);

// ������������� ������ Nested Tree (������ ��������)
$tree = new DBTree($mysql,"category","id");

// �������� �������
$tpl = $templator->readTemplateFromFile("/shop/main_template.tpl", "/shop/news.tpl");
	if(!$tpl) die("Read template failed");	
		
// ���������� ������� ���������, ������� $m
$templator->create_all_vars($m);

//�����������
$control->loginvalid(); 
#############################################################


#############################################################
// �������� ��������
$control->create_head_html();

//����������� ����� ��������� �������
$control->show_cat((int)getfromget("n_ct"));

//����������� �������
if((int)getfromget("id") > 0) 
	$control->show_detail_news((int)getfromget("id"));
		else $control->show_all_news((int)getfromget("page"));


// ����� �������
$templator->generateOutput();

?>