<?
//************************************************************************
// Класс базы данных со своими функциями
class db {
	var $dbhost="";
	var $dbname="";
	var $dblogin="";
	var $dbpwd="";
	var $row;   // Кол-во строк возвращенных функцией SQL_SELECT
	var $fetch; // Результат выполнения запроса
	var $connect; // Ссылка подключения
	var $res;
//************************************************************************
// Конструктор класса
// Входные данные:
// $dbh - имя хоста
// $dbn - имя базы данных
// $dbl - имя пользователя
// $dbp - пароль
	public function __construct($dbh,$dbn, $dbl, $dbp)
		{
			$this->dbhost=$dbh;
			$this->dbname=$dbn;
			$this->dblogin=$dbl;
			$this->dbpwd=$dbp;
			return true;
		}
//************************************************************************
// Деструктор класса
	public function __destruct()
		{
			if($this->connect) @mysql_close($this->connect);
			$this->dbhost="";
			$this->dbname="";
			$this->dblogin="";
			$this->dbpwd="";
			$this->row="";
			$this->fetch="";
			$this->result="";
			$this->connect="";
		}
//************************************************************************
//Соединение с mySQL и выбор текущей базы данных
// Задание кодировки по умолчанию для подключения
	public function connect()
		{
			$this->connect=@mysql_connect($this->dbhost, $this->dblogin, $this->dbpwd);
			if($this->connect){
				if(@mysql_select_db($this->dbname)) {
					//@mysql_query("SET NAMES 'cp1251'");
					//@mysql_query("SET character set 'cp1251'");
                    @mysql_set_charset('utf8', $this->conect);
				} else return false;
			} else return false;	
			return true;
		}
//************************************************************************
//Выборка данных ассоц. массивом
	public function fetcharray()
		{
			return @mysql_fetch_array($this->fetch);
		}
//************************************************************************
//Выборка по переданной переменной
	public function fetch_var_array($q)
		{
			return @mysql_fetch_array($q);
		}
//************************************************************************
//Запрос на SELECT  (возврат кол-ва строк и ассоц. массив)
	public function sql_select($sql)
		{	
			$r=@mysql_query($sql);
			if($r){
				$this->row=@mysql_num_rows($r);
				if($this->row > 0) {$this->fetch=$r;}
				} else return false;
			return $r;
		}
//************************************************************************
//Запрос на INSERT
	public function sql_insert($sql)
		{
			$r=@mysql_query($sql);
			if($r) return true; else return false;
		}
//************************************************************************
//Запрос на UPDATE
	public function sql_update($sql)
		{
			$r=@mysql_query($sql);
			if($r)return true; else return false;
		}
//************************************************************************
//Запрос на DELETE
	public function sql_delete($sql)
		{
			$r=@mysql_query($sql);
			if($r)return true; else return false;
		}
//************************************************************************
//Выполнение запроса
	public function query($sql) {	
			if(!$this->connect) return 0;
			return @mysql_query($sql, $this->connect);
	}
//************************************************************************
//Получение эфект.строк
	public function affected_rows() {
			return @mysql_affected_rows($this->connect);
	}
//************************************************************************
//Подсчет строк в результате выполнения запроса
	public function num_rows($q) {
			return @mysql_num_rows($q);
	}
//************************************************************************
//Возврат. ассоц. массива после выполнения запроса
	public function fetch_array($q, $result_type=MYSQL_ASSOC) {
			return @mysql_fetch_array($q, $result_type);
	}
//************************************************************************
//Получение объектов из  выполненного запроса
	public function fetch_object($q) {
		return @mysql_fetch_object($q);
	}
//************************************************************************
//Установка указателя
	public function data_seek($q, $n) {
		return @mysql_data_seek($q, $n);
	}
//************************************************************************
//Очистка результата
	public function free_result($q) {
		return @mysql_free_result($q);
	}
//************************************************************************
//Возвращает ID последнего инсерта
	public function insert_id() {
		return @mysql_insert_id($this->connect);
	}
//***********************************************************************
// Возвращает информация о последнем действии
	public function query_info() {
		return @mysql_info($this->connect);
	}
//************************************************************************
//Вывод ошибок
	public function error() {
		return @mysql_error($this->connect);
	}
//************************************************************************
//Выврл ошибки с прекращение дальнейшей работы скрипта
	public function error_die($msg='') {
		die(((empty($msg))?'':$msg.': ').$this->error());
	}
//************************************************************************
//Преобразования
	public function sql2var($sql) {
		if((empty($sql)) || (!($query = $this->query($sql)))) return false;
		if($this->num_rows($query) < 1) return false;
		return $this->result2var($query);
	}
//************************************************************************
//Преобразования
	public function result2var($q) {
		if(!($Data = $this->fetch_array($q))) return false;
		$this->free_result($q);
		foreach($Data as $k=>$v) $GLOBALS[$k] = $v;
		return true;
	}
//************************************************************************
//Преобразования
	public function sql2array($sql, $keyField='') {
		if((empty($sql)) || (!($query = $this->query($sql)))) return false;
		if($this->num_rows($query) < 1) return false;
		return $this->result2array($query, $keyField);
	}
//************************************************************************
//Преобразования
	public function result2array($q, $keyField='') {
		$Result = array();
		while($Data = $this->fetch_array($q))
			if(empty($keyField)) $Result[] = $Data;
			else $Result[$Data[$keyField]] = $Data;
		$this->free_result($q);
		return $Result;
	}
//************************************************************************
//Просмотр списка таблиц
	public function list_tables() {
		return @mysql_list_tables($this->dbname, $this->connect);
	}
//************************************************************************
//Просмотр полей в выбранной таблице
	public function list_fields($table_name) {
		return @mysql_list_fields($this->dbname, $table_name, $this->connect);
	}
}	
?>