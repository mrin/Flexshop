<?php
//************************************************************************ 
// ПРИЕМ ТИКЕТОВ ЧЕРЕЗ POP3

session_start();
set_time_limit(0);
$pth="../../../";
@require($pth."/lib/modules/mail/pop3_class.php");
@require($pth."/lib/modules/mail/mimedecode.inc.php");
@include($pth."/lib/global/global_include.php");
@require_once($pth."/lib/modules/mail/smtp_class.php");
// Для конструктора
$apop_detect = TRUE;    
$log = FALSE;           
$log_file = "pop3.class.log"; 
$qmailer = FALSE;

//************************************************************************ 
// Дополнительные поля
$conn_timeout = "25";  // Connection Timeout
$sock_timeout = "10,500"; // Socket Timeout
//$apop = "0";
$savetofile = FALSE;

//************************************************************************ 
// Инициализация класса и подключение к базе данных
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);
// Инициализация класса работы с логгированием и шаблонизацией
$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);
// Инициализация класса POP3
$pop3 = new POP3($log,$log_file,$apop_detect);
$setting=array();


//************************************************************************ 
// Управление тикетами по  POP3
if($r=$mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=1")) 
	if($mysql->num_rows($r)==1) {
	
		// Получение  массива настроек
		$setting=strip($mysql->fetch_array($r));
		
		// Получение массива бан мыл
		if($setting[status_ban] == 1) {
			$r = $mysql->query("SELECT * FROM ticketsystem_ban");
			while($row = $mysql->fetch_array($r)) $banned[]=trim($row[email]);
		}
		
		// Закрытие тикета
		ticket_close();
		
		// Удаление тикета
		ticket_delete();
		
		// Подключение и скачивание писем
		if($setting['status'] == 1) {
			if(connect_pop3()) {
				download_email();
				close_pop3();
			}
		}
	
	}	
// Проверка на наличие мыла в блэк листе
function in_blacklist($email) {
	GLOBAL $setting, $banned;
	if($setting[status_ban] == 1 && count($banned) > 0) {
		if(in_array(trim($email),$banned) || $setting[email] == trim($email)) return 1; else return 0;
	}
}	
//************************************************************************ 
// Получение ID тикета
function GetTicketID($string) {
    return (int)substr(strstr($string, "[#"), 2, 7);
}

function esc2($string,$where) {
	preg_match('/'.$string.'([^*?]\S+)/', $where, $var);
	return $var[1];
}
//************************************************************************ 
// Удаление из строки ID тикета
function DelTicketID($string) {
	$c1=strstr($string, "[#");
	$c2=strstr($c1, "]");
	$exp=substr($c1, 0, strlen($c1)-strlen($c2)+1);
	$string=str_replace($exp, "",$string);
	if(strlen($c1)<2) return $string;
	return DelTicketID($string);
}
//************************************************************************ 
// Преобразование mime сообщения
function mimeDecode($email)
{
    $p['include_bodies'] = true;
    $p['include_headers'] = true;
    $p['decode_headers'] = true;
    $p['crlf'] = "\r\n";
    $p['input'] = $email;
	$m=new MIMEDECODE($email);
    $msg = $m->decode($p);
    return $msg;
}

//************************************************************************ 
//Удаляет тэги
function striptags($string) {
    $search = array("'<script[^>]*?>.*?</script>'si",
					"'<title[^>]*?>.*?</title>'si",
					"'<head[^>]*?>.*?</head>'si"
					);
    $replace = array("","","");
    $string = preg_replace($search, $replace, $string);
    return $string;
}

//************************************************************************ 
// Возвращение Mail адреса отправителя
function getFrom($head)
{
    $regex = '\<*[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,6})\>*';
    if (array_key_exists('reply-to', $head) and ereg($regex, $head['reply-to'])) {
        $from = $head['reply-to'];
    } elseif (array_key_exists('return-path', $head) and ereg($regex, $head['return-path'])) {
        $from = $head['return-path'];
    } elseif (array_key_exists('from', $head) and ereg($regex, $head['from'])) {
        $from = $head['from'];
    }
    $from = ereg_replace("^(.*)<", "", $from);
    $from = str_replace(array ("<", ">", " "), "", $from);
    return $from;
}

//************************************************************************ 
// Очитска текста
function textCleaner($text)
{
    $text = str_replace("'", "", $text);
    $text = str_replace("=20", "\n", $text);
    return $text;
}

//************************************************************************ 
// Преобразование из кодировки в другую кодировку
function conv($string, $from, $second) {
	return @convert_cyr_string(nl2br($string), $from, "w");
}
//************************************************************************ 
//  Формирование случайных чисел
function randomkey($length = 1) {                      
	$all = explode( " ", "0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z");                                    
	for($i=0;$i<$length;$i++) {                                  
		srand((double)microtime()*1000000);                          
		$randy = mt_rand(0, 34);                                        
		$passw .= toUpper($all[$randy]);                                       
	}                                                            
	return $passw;                                                
}

//************************************************************************ 
//Создаие объекта POP3 и подсоединение к серверу
function connect_pop3() {
	GLOBAL $pop3,$msg_list,$setting,$conn_timeout,$sock_timeout;
			if($pop3->connect($setting[pop3_server], $setting[pop3_port], $conn_timeout, $sock_timeout)){
			    if($pop3->login($setting[login],decryptdata($setting[pwd]))){
			        if(!$msg_list = $pop3->get_office_status()){
			            return 0;
			        } else return 1; 
			    }else  return 0;
			}else return 0; 
			$noob = TRUE;;	
}

//************************************************************************ 
// Закрытие подключения
function close_pop3() {
	GLOBAL $pop3;
	$pop3->close();
}

// ************************************************************************ 
// Удаление письма на сервере
function del_email($i) {
	GLOBAL $pop3,$setting;
	if(!$setting['status_save'])
		if($pop3->delete_mail($i)) return 1; else return 0;
}

//************************************************************************ 
// Разбор каждого сообщения в ящике
function download_email() {
	GLOBAL $msg_list,$qmailer,$pop3,$mysql,$template,$setting;
	for($i=1;$i<=$msg_list["count_mails"];$i++){
    if(!$message = $pop3->get_mail($i, $qmailer)){
         return 0;
     } else {
	 
	// Библиотека ICONV
	$header=iconv_mime_decode_headers($message, ICONV_MIME_DECODE_CONTINUE_ON_ERROR , "cp1251");
	$msg=mimeDecode($message);
	$head = $msg->headers;
    $base['from'] = getFrom($head);
	
	// Проверка email в черном списке
	if(in_blacklist($base['from'])) { del_email($i); continue; }
	
    $base['subject'] = striptags($header["Subject"]);
	
	// Поиск ID из темы сообщения
	$ticket_id = GetTicketID($base['subject']);
	$message_id = md5crypt($header["Message-ID"]);
	$base['date'] = $header["Date"];
	
	// Отправщик письма
	$base['rec_client'] = $header["Received"];
	
	// ************************************************************************ 
	// Выборка самого текста письма
		if($msg->ctype_primary == 'text' AND ($msg->ctype_secondary == 'plain' OR $msg->ctype_secondary == 'html')) {
			$base['body'] = conv($msg->body, $msg->ctype_parameters["charset"], $msg->ctype_secondary);
			$base['charset'] = $msg->ctype_parameters["charset"];
		} else
		    if (($msg->parts[0]->ctype_primary == "text") and ($msg->parts[0]->ctype_secondary == 'plain' OR $msg->parts[0]->ctype_secondary == 'html')){
		        $base['body'] = conv($msg->parts[0]->body, $msg->parts[0]->ctype_parameters["charset"],$msg->parts[0]->ctype_secondary );
				$base['charset'] = $msg->parts[0]->ctype_parameters["charset"];
		    } else
					 if (($msg->parts[0]->parts[0]->ctype_primary == 'text') and ($msg->parts[0]->parts[0]->ctype_secondary == 'plain' OR $msg->parts[0]->parts[0]->ctype_secondary == 'html')) {
						$base['body'] = conv($msg->parts[0]->parts[0]->body, $msg->parts[0]->parts[0]->ctype_parameters["charset"], $msg->parts[0]->parts[0]->ctype_secondary); 
						$base['charset'] = $msg->parts[0]->parts[0]->ctype_parameters["charset"];
					}
		$base['body']=strip_tags($base['body'], "<br><b>");
		
	// ************************************************************************ 
	// Поиск ID в сообщении
	if(empty($ticket_id))  $ticket_id = GetTicketID($base['body']);
	if(empty($ticket_id))  $ticketid=0;

		
	if($ticket_id == 0) {
		if($ticket_id = CreateTicket($base, $message_id)) SendMessage($base,$ticket_id);
	} else {
		$r=$mysql->query("SELECT * FROM ticketsystem WHERE ticket_ID=$ticket_id AND status <> 0");
		if($mysql->num_rows($r)==1) 
			add_message_to_ticket($ticket_id, $base, $message_id);
		else 
			if($ticket_id = CreateTicket($base, $message_id)) 
			SendMessage($base,$ticket_id);			
		}
		
		// Удаление письма с сервера
		del_email($i);
	}

}
}

// ************************************************************************ 
// Отправка письма об регистрации тикета в базе данных (постановка в очередь на отправку)
function SendMessage($base,$ticket_id) {
	GLOBAL $setting,$mysql,$template,$m,$keyID;
	
	//Тема сообщения отправителя
	$m[SUBJECT]=trim(DelTicketID($base[subject]));
	
	// Номер тикета
	$m[TICKETID]="[#$ticket_id]";
	
	// Ссылка на тикет
	$m[LINK]=$template->show_contxt("<a href='{URLSITE}/ticket.php?type=view&ticketID=$ticket_id&keyID=$keyID&'>{URLSITE}/ticket.php?type=view&ticketID=$ticket_id&keyID=$keyID</a>");
	
	//Замена по шаблону
	$subject = base64_encode($template->show_contxt($setting['subject'])); 
	$body = base64_encode($template->show_contxt($setting['msg']));
	
	// Отправка В ОЧЕРЕДЬ
	$mysql->query("INSERT INTO email_queue VALUES('', 1, '".$base[from]."', '".$base[charset]."', '".$subject."', '".$body ."')");
}

// ************************************************************************ 
// Добавление сообщения к существующему тикету
function add_message_to_ticket($ticket_id, $base, $message_id) {
	GLOBAL $mysql;
	$datesend=date("Y-m-d H:i:s");
	
	$base[subject] = base64_encode($base[subject]);
	$base[body] = base64_encode($base[body]);
	$msg=addslashes(serialize($base));
	
	$mysql->sql_select("SELECT * FROM ticketsystem_msgs WHERE message_ID='$message_id'");
	if($mysql->row==0) 
		$r=$mysql->query("INSERT INTO ticketsystem_msgs VALUES('', $ticket_id, '$message_id', '$datesend', '$msg', '1')");
}

// ************************************************************************ 
// Создание нового тикета
function CreateTicket($base, $message_id) {
	GLOBAL $mysql,$keyID;
	$r=$mysql->query("SELECT * FROM ticketsystem_msgs WHERE message_ID='$message_id'");
	if($mysql->num_rows($r)==0) {
	
		// Генерация ключа для просмотра тикета
		$keyID=randomkey(mt_rand(3,5))."-".randomkey(mt_rand(3,5))."-".randomkey(mt_rand(3,5));
		$datecreate=date("Y-m-d H:i:s");
		$sbj=DelTicketID($base[subject]);
		$subject=addslashes(trim($sbj));
		$base[subject] = base64_encode($sbj);
		$base[body]=DelTicketID($base[body]);
		$base[body] = base64_encode($base[body]);
		$r=$mysql->query("INSERT INTO ticketsystem VALUES('','$keyID','$base[from]','$datecreate','','$subject', 2)");
		$ticket_id=$mysql->insert_id();
		if($ticket_id > 0) {
			$base[subject] = base64_encode("[#$ticket_id] ".base64_decode($base[subject]));
			$msg=addslashes(serialize($base));
			$r=$mysql->query("INSERT INTO ticketsystem_msgs VALUES('', '$ticket_id', '$message_id', '$datecreate', '$msg', '1')");
		}
		return $ticket_id;
	} else return 0;
}

//************************************************************************ 
// Авто закрытие тикетов исходя из даты последнего сообщения
function ticket_close() {
	GLOBAL $setting,$mysql;
	if($setting[close_status] == 1) {
		$r = $mysql->query("SELECT system.ticket_ID as ticket_ID, MAX(msgs.datesend) as last FROM ticketsystem as system, ticketsystem_msgs as msgs 
			WHERE (system.status = 2 OR system.status = 1) AND system.ticket_ID = msgs.ticket_ID   GROUP BY msgs.ticket_ID");
		if($mysql->num_rows($r)>0)
			while($row = $mysql->fetch_array($r)) {
				$days = number_format(((time() - strtotime($row[last]))/60/60/24),'2',',','');
				if($days >= $setting[close_day]){
					$closedate = date("Y-m-d H:i:s");
					$mysql->query("UPDATE ticketsystem SET dateclose='$closedate', status='0' WHERE ticket_ID='".$row[ticket_ID]."'");	
				}
			}
	}
}

//************************************************************************ 
// Авто удаление тикетов исходя из даты закрытия
function ticket_delete() {
	GLOBAl $setting,$mysql;
		if($setting[delete_status] == 1) {
			$dt = $setting[delete_day]*24*60*60;
			$r=$mysql->query("DELETE ticketsystem, ticketsystem_msgs FROM  ticketsystem, ticketsystem_msgs 
				WHERE ticketsystem.status='0' AND ticketsystem.ticket_ID = ticketsystem_msgs.ticket_ID 
				AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(ticketsystem.dateclose) >= $dt) ");	
	}
}

?>
