<?
session_start();
$pth="../";
// Определение ROOT директрории
$locdir=$HTTP_SERVER_VARS["DOCUMENT_ROOT"];
// Класс шаблонизатора
@include($pth."/lib/global/log_tpl_parse_class.php");
// Фукция проверки входных данных
@include($pth."/lib/global/check.php");
// Подключение дополнительных функций
@include($pth."/lib/global/func.php");
// Класс работы с базой
@include($pth."/lib/global/database_class.php");
###################################################################################

//************************************************************************ 
// Функция изменения общих данных (Обязательно ДО БОКА ВКЛЮЧЕНИЯ)
function setting_other()
	{	GLOBAL $pth,$HTTP_SESSION_VARS;
		$url=htmlspecialchars(getfrompost("url"));
		$mail=htmlspecialchars(getfrompost("mail"));
		$homedir=htmlspecialchars(getfrompost("homedir"));
		$deflang=htmlspecialchars(getfrompost("deflang"));
		$deftpl=htmlspecialchars(getfrompost("deftpl"));
		$title=htmlspecialchars(getfrompost("title"));
		$mini_title=htmlspecialchars(getfrompost("mini_title"));
		$meta_description=htmlspecialchars(getfrompost("meta_description"));
		$meta_keywords=htmlspecialchars(getfrompost("meta_keywords"));
		if(empty($url) || empty($mail) ||empty($homedir)) return false;
			else
			{ 	$fmas=@file($pth."/lib/global/config.php");
				$f=@fopen($pth."/lib/global/config.php", "w+");
				fwrite($f, "<?"."\n");
				fwrite($f, "$"."deftpl='".$deftpl."'".";"."\n");
				fwrite($f, "$"."url='".$url."'".";"."\n");
				fwrite($f, "$"."homedir='".$homedir."'".";"."\n");
				fwrite($f, "$"."mail='".$mail."'".";"."\n");
				fwrite($f, "$"."deflang='".$deflang."'".";"."\n");
				fwrite($f, "$"."title='".$title."'".";"."\n");
				fwrite($f, "$"."mini_title='".$mini_title."'".";"."\n");
				fwrite($f, "$"."meta_description='".$meta_description."'".";"."\n");
				fwrite($f, "$"."meta_keywords='".$meta_keywords."'".";"."\n");
			   for($i=10; $i<=count($fmas);$i++)
					fwrite($f, $fmas[$i]);
			   fclose($f);
			   #Назначение выбранного языка по умолчанию
			   $HTTP_SESSION_VARS["lang"]=$deflang;
			   return true;
			}
	}
//************************************************************************ 
// Функция изменения настроек mySQL  (Обязательно ДО БОКА ВКЛЮЧЕНИЯ)
function setting_db()
	{	GLOBAL $pth,$HTTP_SESSION_VARS;
		$dbhost=htmlspecialchars(getfrompost("dbhost"));
		$dbname=htmlspecialchars(getfrompost("dbname"));
		$dblogin=htmlspecialchars(getfrompost("dblogin"));
		$dbpwd=htmlspecialchars(getfrompost("dbpwd"));
			if(empty($dbhost) || empty($dbname) || empty($dblogin)) return false;
				$test=new db($dbhost,$dbname,$dblogin,$dbpwd);
			if(!$test->connect()) return false; 
				else
					{
						$HTTP_SESSION_VARS["xxdbxx"]=$dbpwd;
						$fmas=@file($pth."/lib/global/config.php");
						$f=@fopen($pth."/lib/global/config.php", "w+");
							for($i=0; $i<=9;$i++)
								fwrite($f, $fmas[$i]);
						fwrite($f, "$"."dbhost='".$dbhost."'".";"."\n");
					    fwrite($f, "$"."dbname='".$dbname."'".";"."\n");
					    fwrite($f, "$"."dblogin='".$dblogin."'".";"."\n");
					    fwrite($f, "$"."dbpwd='".encryptdata($dbpwd)."'".";"."\n");
						fwrite($f, "?>"."\n");
						fclose($f);
						return true;
					}
	}
//************************************************************************ 
// Проверка на авторизацию
if(loginvalid()){
	// Реакция на изменение общих настроек
	if(isset($HTTP_GET_VARS["change_other"])){if(setting_other())$err="no"; else $err="yes";}
	// Реакция на изменение mySQL настроек
	if(isset($HTTP_GET_VARS["change_db"])){if(setting_db())$err="no"; else $err="yes";}
	#########################БЛОК ВКЛЮЧЕНИЙ#################################
	// Подключение конфигурационного файла
	@include($pth."/lib/global/config.php");
	// Подключение мултиязычного модуля #ТОЛЬКО ПОСЛЕ СЕССИИ и КОНФИГУРАЦИОННОГО ФАЙЛА#
	@include($pth."/lib/lang/conf.php");
	$m["URLSITE"]=$url;
	$m["TEMPLDEF"]=$deftpl;
	if(!isset($_SESSION["xxdbxx"])) $_SESSION["xxdbxx"] = decryptdata($dbpwd);
	######################################################################

	// Инициализация класса работы с MYSQL
	$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
	if(!$mysql->connect()) die($m[_INST_ERROR1]);
	// Инициализация класса работы с логгированием и шаблонизацией
	$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);
	######################################################################

	// Отображение конфига текущего
	function show_config()
		{	GLOBAL $m,$template,$HTTP_SESSION_VARS,$title,$mini_title,$meta_description,$meta_keywords;
			$m[INS_URL]=$template->url;
			$m[INS_MAIL]=$template->mail;
			$m[INS_DBHOST]=$template->dbhost;
			$m[INS_DBNAME]=$template->dbname;
			$m[INS_DBLOGIN]=$template->dblogin;
			$m[INS_DBPWD]=$template->dbpwd;
			$m[INS_HOMEDIR]=$template->homedir;
			$m[INS_TITLE]=$title;
			$m[INS_MINI_TITLE]=$mini_title;
			$m[INS_META_DESCR]=$meta_description;
			$m[INS_META_KEY]=$meta_keywords;
			// Язык по умолчанию
			if($template->deflang=="ru") $selectru="selected";
			if($template->deflang=="en") $selecten="selected";
			$m[INS_OPTLANG]="<option value='en' $selecten>English<option value='ru' $selectru>Russian";
			// Шаблон
			$f=@dir($template->homedir."/templates");
			if($f)
				 while($name=$f->read())
					if($name<>"." && $name<>".." && $name<>"exepanel" && is_dir($template->homedir."/templates/".$name))
						{
							if($template->deftpl==$name) $select="selected"; else $select="";
							$m[INS_OPTTPL].="<option value='$name' $select>$name";
						}
			// Подстановка контента
			return $template->show_content("/exepanel/s_shop.tpl");
		}
	if($err=="no")$template->logtxt("_SUCC_CHANGE", "1");
	if($err=="yes")$template->logtxt("_ERR_FIELD", "0");
	######################################################################

			$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
			$m["CURCAT"]=$m["_CP_CONFIGURATION"];
			$m["CENTERCONTENT"]=show_config();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>