<?
//************************************************************************
// Подстановка из конфигурационного файла
$m["URLSITE"]=$url;
$m["TEMPLDEF"]=$deftpl;

//setlocale(LC_ALL, 'ru_RU.cp1251');

###############ФУНКЦИИ ФУНКЦИИ ФУНКЦИИ ФУНКЦИИ ФУНКЦИИ ФУНКЦИИ ФУНКЦИИ##################

//************************************************************************
// Функция шифрования TRIPLEDES по ключу
// $data - данные для шифрования
// $cryptobase - шифрование кодированной строки в base64
function encryptdata($data,$cryptobase=TRUE){
	//Ключ шифрования
	$key = "m3jDC30982jdj2S19dj2";
	$td = mcrypt_module_open(MCRYPT_TripleDES, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$encrypted_data = mcrypt_generic($td, trim($data));
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	if($cryptobase) $encrypted_data=base64_encode($encrypted_data);
	return $encrypted_data;
}  
//************************************************************************
// Функция ДЕшифрования TRIPLEDES  по ключу
// $data - данные для дешифрования
// $cryptobase - расшифровка первоначально из base64
function decryptdata($data,$cryptobase=TRUE){
	//Ключ ДЕшифрования
	$key = "m3jDC30982jdj2S19dj2";
	if($cryptobase) $data=base64_decode($data);
	$td = mcrypt_module_open(MCRYPT_TripleDES, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$decrypted_data = mdecrypt_generic($td, $data);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return trim($decrypted_data);
}

//************************************************************************
// Сравнение ключа для редактирование товаров
function validate_key($sendkey) {
	$key = "123";
	if(strcmp($sendkey,$key) == 0) return true; else return false;
}

//************************************************************************
// Преобразование данных переданный из формы, ЮНИКОДА в виндозный
function encode_form($var) {
	//if($res = iconv("UTF-8","windows-1251", $var)) $var = $res;
	return $var;
}

//************************************************************************
// Функция возвращает заданное кол-во пробелов
// $var - кол-во повторов созданной $sp переменной
// $nb - кол-во пробелов в строке $sp
function space($var, $nb) {
	if($var > 2) {for($i=0;$i<$nb;$i++) $sp.="&nbsp; "; $cp=$sp; for($i=0;$i<$var;$i++) $sp.=$cp; } else $sp="";
	return $sp;
}
//************************************************************************
// Функция взятие элемента из POST массива
// $var - имя переменной
// Возвращает значение этой переменной
function getfrompost($var)
	{ 
		GLOBAL $HTTP_POST_VARS;
		return trim($HTTP_POST_VARS[trim($var)]);
	}
//************************************************************************
// Функция взятие элемента из GET массива
// $var - имя переменной
// Возвращает значение этой переменной
function getfromget($var)
	{ 
		GLOBAL $HTTP_GET_VARS;
		return trim($HTTP_GET_VARS[trim($var)]);
	}
//************************************************************************
// Функция взятие элемента из SESSION массива
// $var - имя переменной
// Возвращает значение этой переменной
function getfromsess($var)
	{ 
		GLOBAL $HTTP_SESSION_VARS;
		return trim($HTTP_SESSION_VARS[trim($var)]);
	}
//************************************************************************
// Сбор двумерного массива из POST и GET переданных переменных, для удобной обработки данных формы
// $VAR - часть названия переменной ПРИМЕР: переменная shipping_name_4,
// то $VAR="shipping";
// $METHOD - метод которым были переданные данные (POST,GET)
// Возвращает массив вида $MAS[name][4]
function getarray($var,$method,$array=array())
	{
		GLOBAL $HTTP_POST_VARS,$HTTP_GET_VARS;
		if(strtolower($method)=="post")
			{
				$key=@array_keys($HTTP_POST_VARS);
				foreach($key as $ar=>$value)
					if(strstr($value, $var))
						{	$value=strtolower($value);
							$rez=explode("_",$value);
							$mas[$rez[count($rez)-1]][$rez[count($rez)-2]]=addslashes(trim($HTTP_POST_VARS[$value]));
						}
			return $mas;
			}
		if(strtolower($method)=="get")
			{
				$key=@array_keys($HTTP_GET_VARS);
				foreach($key as $ar=>$value)
					if(strstr($value, $var))
						{
							$rez=explode("_",$value);
							$mas[$rez[count($rez)-1]][$rez[count($rez)-2]]=addslashes(trim($HTTP_GET_VARS[$value]));
						}
			return $mas;
			}
		if(strtolower($method)=="array" && count($array) > 0) {
		$key=@array_keys($array);
				foreach($key as $ar=>$value)
					if(strstr($value, $var))
						{
							$rez=explode("_",$value);
							$mas[$rez[count($rez)-1]][$rez[count($rez)-2]]=addslashes(trim($array[$value]));
						}
			return $mas;
		}
	}
//************************************************************************
// Функция шифрования HASH MD5 
// $data - данные для шифрования
function md5crypt($data){
	$key="viKd93.d930d2:3d0ck3sDDE";
	return strtolower(md5($key.trim($data)));
	}
//************************************************************************
// Перевод русских и англ. символов в верхний регистр	
function toUpper($content) {
	$content = strtr($content, "abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнорпстуфхцчшщъьыэюя",
	"ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМHОРПСТУФХЦЧШЩЪЬЫЭЮЯ");
  return $content;
	}
//************************************************************************
// Перевод русских и англ.  символов в нижний регистр
function toLower($content) {
	$content = strtr($content, "ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМHОРПСТУФХЦЧШЩЪЬЫЭЮЯ",
	"abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнорпстуфхцчшщъьыэюя");
	return $content;
	}
//************************************************************************
// Блок проверки на различные символы в строке
// Возвращает FALSE - если обнаружен символ
function check_text($nabor,$text){                                //Проверка на использование запрещеннх сиволов, $nabor - набор символов,$text-текст для проверки. Если функция нашла недопустимый символ, то она его возвращает, если нет, то ничего не возвращает
                $nab_char[0] = "abcdefghijklmnopqrstuvwxyz1234567890_-@^";        //набор 0 - англ. буквы и цифры + черточки
                $nab_char[1] = "abcdefghijklmnopqrstuvwxyz1234567890_";                //набор 1 - англ. буквы и цифры + черточки
                $nab_char[2] = "abcdefghijklmnopqrstuvwxyz1234567890";                //набор 2 - англ. буквы и цифры
                $nab_char[3] = "abcdefghijklmnopqrstuvwxyz";                                                //набор 3 - англ. буквы
                $nab_char[4] = "1234567890™";                                                       //набор 4 - только цифры
                $nab_char[5] = "rzeu1234567890™";                                      //набор 5 - для кошельков WebMoney
                $nab_char[6] = "ёйцукенНгшщзхъфывапролджэячсмитьбю1234567890_-@^";                //набор 6 - рус. буквы и цифры
                $nab_char[7] = "ёйцукенНгшщзхъфывапролджэячсмитьбю1234567890_";                        //набор 7 - рус. буквы и цифры
                $nab_char[8] = "ёйцукенНгшщзхъфывапролджэячсмитьбю1234567890";                        //набор 8 - рус. буквы и цифры
                $nab_char[9] = "ёйцукенНгшщзхъфывапролджэячсмитьбю";                                                //набор 9 - рус. буквы
                $nab_char[10] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапролджэячсмитьбю1234567890/:~!@#$%^&*-|\+=?[]{}.,_";        //набор 10 - кроме запрещающие MSQL-опасные символы - кавычки и скобки
                $nab_char[11] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапролджэячсмитьбю1234567890!-._[](){}";        //набор 11 - для файлов
                $nab_char[12] = "1234567890.™"; 
                $nab_char[13] = "10™";    //Тип товара
                $nab_char[14] = "z1234567890™"; 
                $nab_char[15] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапролджэячсмитьбю1234567890™";
                $nab_char[16] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапр олджэячсмитьбю1234567890_-™";
                $nab_char[17] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапр олджэячсмитьбю™";
                $nab_char[18] = "abcdefghijklmnopqrstuvwxyz1234567890_-@.™";
                $nab_char[19] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъфывапролджэячсмитьбю1234567890._-™";
                $nab_char[20] = "abcdefghijklmnopqrstuvwxyzёйцукенНгшщзхъф ывапролджэячсмитьбю1234567890.,_-()[]™";
                $nab_char[21] = "1234567890.-()[] +™"; 
               for ($m = 0; $m < strlen($text); $m++) {
			$str_char = strstr($nab_char[$nabor],toLower($text[$m]));
				if($str_char == FALSE){
					return $text[$m];                        //Возврат недопустимого символа.
					break;
				}
                }
}
//************************************************************************
// Функция экранированя данных для сокр. записи
// $var - строка для обработки
function esc_db($var)
	{
		return mysql_escape_string($var);
	}
//************************************************************************
//Раскавычивает массив из БД
function strip($mass) {
	if(count($mass) > 0)
	foreach($mass as $key=>$value) {
		if(@array_key_exists($key,$mass)) $mass[$key]=@stripslashes($mass[$key]);
		if(@array_key_exists($value,$mass)) $mass[$value]=@stripslashes($mass[$value]);
		
	}
return $mass;
}
//************************************************************************
//Закавычивание массив из БД
function slash($mass) {
	if(count($mass) > 0)
	foreach($mass as $key=>$value) {
		if(@array_key_exists($key,$mass)) $mass[$key]=@addslashes($mass[$key]);
		if(@array_key_exists($value,$mass)) $mass[$value]=@addslashes($mass[$value]);
	}
	return $mass;
}

//************************************************************************
//Удаление элемента массива
function del_element_array($array, $index) {
	return array_splice ($array, $index-1, 1);
}

//************************************************************************
//Проверяет на правильность MAIL адреса
function is_email($email) {
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email);
}
//************************************************************************
// Проверка авторизации для панели администратора
function loginvalid() {
		GLOBAl $HTTP_SESSION_VARS;
		if(isset($_SESSION["login_db"]) && 
           isset($_SESSION["login_my"]) &&
           isset($_SESSION["pwd_my"]) &&
           isset($_SESSION["pwd_db"]))
            if(strlen($_SESSION["pwd_my"]) == 32 &&
               strlen($_SESSION["pwd_db"]) == 32 &&
               strcasecmp($_SESSION["login_my"], $_SESSION["login_db"]) == 0 &&
               strcasecmp($_SESSION["pwd_my"], $_SESSION["pwd_db"]) == 0)
                return true;
        else return false;
	}
?>