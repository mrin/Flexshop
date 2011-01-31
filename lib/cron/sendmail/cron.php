<?
//************************************************************************ 
// Рассылка писем из очереди

session_start();
set_time_limit(0);
$pth="../../../";
@include($pth."/lib/global/global_include.php");
@require_once($pth."/lib/modules/mail/smtp_class.php");
//************************************************************************ 
// Подкл. к базе
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);

// Получение конфигов
$r = $mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=1 OR tpl_ID=2 ORDER BY tpl_ID");
$setting[1]=strip($mysql->fetch_array($r));
$setting[2]=strip($mysql->fetch_array($r));

// Первый конфиг - ТИКЕТ СИСТЕМА
$smtp1 = new smtp($setting[1][smtp_server],$setting[1][smtp_port],$setting[1][email], $setting[1][name], $setting[1][login],decryptdata($setting[1][pwd]));
// Второй конфиг - Общий email
$smtp2 = new smtp($setting[2][smtp_server],$setting[2][smtp_port],$setting[2][email], $setting[2][name], $setting[2][login],decryptdata($setting[2][pwd]));

// Рассылка тикетов
$r=$mysql->query("SELECT * FROM email_queue WHERE tpl_ID=1");
$rows1 = $mysql->num_rows($r);

// Рассылка основных писем
$r=$mysql->query("SELECT * FROM email_queue WHERE tpl_ID=2");
$rows2 = $mysql->num_rows($r);


if($rows1 > 0) {
	// Подключение к первому
	if($smtp1->connect()) {
		for($i=0;$i<$rows1; $i++) {
			$r=$mysql->query("SELECT * FROM email_queue WHERE tpl_ID=1 LIMIT 1");
			$row = $mysql->fetch_array($r);
			// Отправка и удаление
			$data = $smtp1->create_header($row[mail_to], base64_decode($row[subject]), base64_decode($row[letter]), $row[charset]);
			if($smtp1->send($row[mail_to], $data)) $mysql->query("DELETE FROM email_queue WHERE id=".$row[id]);
		}
		// Закрыть подключение
		$smtp1->smtp_close();
	} else echo "<br>error connect 1";
}

if($rows2 > 0) {
	// Подключение ко второму
	if($smtp2->connect()) {
		// Проверка на лимит, в close_day засунут лимит
		if($rows2 > $setting[2][close_day]) $rows2=$setting[2][close_day];
		for($i=0;$i<$rows2; $i++) {
			$r=$mysql->query("SELECT * FROM email_queue WHERE tpl_ID=2 LIMIT 1");
			$row = $mysql->fetch_array($r);
			// Отправка и удаление
			$data = $smtp2->create_header($row[mail_to], base64_decode($row[subject]), base64_decode($row[letter]), $row[charset]);
			if($smtp2->send($row[mail_to], $data)) $mysql->query("DELETE FROM email_queue WHERE id=".$row[id]);
		}	
		// Закрыть подключение
		$smtp2->smtp_close();
	} else echo "<br>error connect 1";
}




?>