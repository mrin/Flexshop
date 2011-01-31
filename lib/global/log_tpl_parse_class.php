<?
//************************************************************************
// Класс для логгирования ошибок и шаблонизации*/
class tpl_logg {
	var $root;
	var $msglog;
	var $url;
	var $homedir;
	var $deftpl;
	var $mail;
    var $deflang;
	var $dbhost;
	var $dbname;
	var $dblogin;
	var $dbpwd;
//************************************************************************
// Конструктор
// $rt - Локальная директория ROOT
// $dbpwdq - Дешифрованный пароль
// Остальные поля из конфига.
	function __construct($rt,$homedirq,$urlq,$deftplq,$mailq,$deflangq,$dbhostq,$dbnameq,$dbloginq,$dbpwdq)
		{
		$this->root=$rt;
		$this->url=$urlq;
		$this->homedir=$homedirq;
		$this->deftpl=$deftplq;
		$this->mail=$mailq;
		$this->deflang=$deflangq;
		$this->dbhost=$dbhostq;
		$this->dbname=$dbnameq;
		$this->dblogin=$dbloginq;
		$this->dbpwd=$dbpwdq;
		}
//************************************************************************
// Функция логирования действий и ошибок для панели управления администратора
// $key - Ключ в массиве значений, к примеру $m["_INST_TITLE"], то ключ _INST_TITLE
// Статусы
// 0:  Ошибка
// 1:	Успешное выполнение
// 3:  Информационное сообщение
	function logtxt($key,$flag)
		{
			GLOBAL $m;
			$m["ERRSUCCMSG"]=$m[$key];
			switch ($flag){
				case "0":$this->msglog.=$this->show_content("/exepanel/error_error.tpl");break;
				case "1":$this->msglog.=$this->show_content("/exepanel/error_success.tpl");break;
				case "3":$this->msglog.=$this->show_content("/exepanel/info_message.tpl");break;
				default :$this->msglog.=$this->show_content("/exepanel/error_error.tpl");
				}
		}
	function logmsg($msg,$flag)
		{
			GLOBAL $m;
			$m["ERRSUCCMSG"]=$msg;
			switch ($flag){
				case "0":$this->msglog.=$this->show_content("/exepanel/error_error.tpl");break;
				case "1":$this->msglog.=$this->show_content("/exepanel/error_success.tpl");break;
				case "3":$this->msglog.=$this->show_content("/exepanel/info_message.tpl");break;
				default :$this->msglog.=$this->show_content("/exepanel/error_error.tpl");
				}
		}
	function logmsg2($msg,$flag)
		{
			GLOBAL $m;
			$m["ERRSUCCMSG"]=$msg;
			switch ($flag){
				case "0":$rr=$this->show_content("/exepanel/error_error.tpl");break;
				case "1":$rr=$this->show_content("/exepanel/error_success.tpl");break;
				case "3":$rr=$this->show_content("/exepanel/info_message.tpl");break;
				default :$rr=$this->show_content("/exepanel/error_error.tpl");
				}
				return $rr;
		}
//************************************************************************
// Функция для шаблонизации
// $templ - путь до файла в котором произвести замену
	function show_content($templ)
		{	
		    GLOBAL $m;
			$temp=@file_get_contents($this->homedir."/templates/".$this->deftpl.$templ);
			$key = array();
			// Выборка всех ключей из шаблона
			preg_match_all('#\{([a-z0-9\-_]*?)\}#is', $temp, $key);
			$key=array_unique($key[1]);
				foreach($key as $keys => $value) {$what[]="{".strtoupper($value)."}"; $to[]=$m[strtoupper($value)];}
					$temp=str_ireplace($what, $to, $temp);
						return $temp;
		}
//************************************************************************
// Функция для шаблонизации
// $templ - текст для замены
	function show_contxt($templ)
		{	
		    GLOBAL $m;
			$temp=$templ;
			$key = array();
			// Выборка всех ключей из шаблона
			preg_match_all('#\{([a-z0-9\-_]*?)\}#is', $temp, $key);
			$key=array_unique($key[1]);
				foreach($key as $keys => $value) {$what[]="{".strtoupper($value)."}"; $to[]=$m[strtoupper($value)];}
					$temp=str_ireplace($what, $to, $temp);
						return $temp;
		}
}
?>