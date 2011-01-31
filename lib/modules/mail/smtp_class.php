<?php
// ************************************************************************ 
//  Класс для отправки писем по SMTP
class smtp {
	var $socket;
	var $host;
	var $port;
	var $email;
	var $name;
	var $login;
	var $pwd;
	var $errno;
	var $errsts;
	
	// ************************************************************************ 
	// Конструктор
	function __construct($host,$port,$email, $name, $login, $pwd){
		$this->host = $host;
		$this->port = $port;
		$this->email = $email;
		$this->name = $name;
		$this->login = $login;
		$this->pwd = $pwd;
	}
	
	// ************************************************************************ 
	// Подключение через сокет к SMTP
	function connect() {
		if( !$this->socket =@fsockopen($this->host, $this->port, $this->errno, $this->errstr, 30) ) {
		echo "SOCKET CONNECT ERROR (host: ".$this->host."; port: ".$this->port.")<br><Br>ERRORS:<br>".$this->errstr."<br><br>";
			return false;
		}		
				socket_set_blocking($this->socket, true);
	            if (!$this->server_parse("220", __LINE__)) return false;

	            @fputs($this->socket, "EHLO " . $this->host . "\r\n");
	            if (!$this->server_parse("250", __LINE__)) {
	               fclose($this->socket);
	               return false;
	            }
	            @fputs($this->socket, "AUTH LOGIN\r\n");
	            if (!$this->server_parse("334", __LINE__)) {
	               fclose($this->socket);
	               return false;
	            }

	             @fputs($this->socket, base64_encode($this->login) . "\r\n");
				if (!$this->server_parse("334", __LINE__)) {
	               //echo '<p>Логин авторизации не был принят сервером!</p>';
	               fclose($this->socket);
				return false;
				}
				@fputs($this->socket, base64_encode($this->pwd) . "\r\n");
				if (!$this->server_parse("235", __LINE__)) {
	              // echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
	               fclose($this->socket);
               return false;
            }	
				
		return true;
	}
	
	// ************************************************************************ 
	// Создание заголовка письма
	function create_header($mail_to, $subject, $message, $charset) {
			$SEND =   "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
			if(!empty($subject))
				$SEND .=   'Subject: =?'.$charset.'?B?'.base64_encode($subject)."=?=\r\n";
				else $SEND .= '';
			$SEND .= "Reply-To: ".$smtp_conf['smtp_email']."\r\n";
	                $SEND .= "MIME-Version: 1.0\r\n";
	                $SEND .= "Content-Type: text/html; charset=\"".$charset."\"\r\n";
	                $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
	                $SEND .= "From: \"".$this->name."\" <".$this->email.">\r\n";
	                $SEND .= "To: $mail_to <$mail_to>\r\n";
	                $SEND .= "X-Priority: 3\r\n\r\n";
	        $SEND .=  $message."\r\n";
			return $SEND;
	}
	
	// ************************************************************************ 
	// Отправка писем на одном подключении
	function send($mail_to, $data) {
	GLOBAL $code;
			@fputs($this->socket, "MAIL FROM: <".$this->email.">\r\n");
            if (!$this->server_parse("250", __LINE__)) {
               @fclose($this->socket);
               return false;
            }
            @fputs($this->socket, "RCPT TO: <" . $mail_to . ">\r\n");
	
            if (!$this->server_parse("250", __LINE__, true)) {
				if($code == 550) { fclose($this->socket); return true;}
               fclose($this->socket);
               return false;
            }
            @fputs($this->socket, "DATA\r\n");

            if (!$this->server_parse("354", __LINE__)) {
               fclose($this->socket);
               return false;
            }
            @fputs($this->socket, $data."\r\n.\r\n");

            if (!$this->server_parse("250", __LINE__)) {
               fclose($this->socket);
               return false;
            }
			return true;
	}
	
	// ************************************************************************ 
	// Закрытие подключения
	function smtp_close() {
		@fputs($this->socket, "QUIT\r\n");
		@fclose($this->socket);
	}
	// ************************************************************************ 
	// Выборка кодов сообщений для анализа
	function server_parse($response, $line = __LINE__, $rcto=FALSE) {
		GLOBAL $code;
	    while (substr($server_response, 3, 1) != ' '){
	        if (!($server_response = @fgets($this->socket, 256)))  return false;
			}
		if($rcto)$code = (int) substr($server_response, 0, 3);
	    if (!(substr($server_response, 0, 3) == $response)) return false;
		
	    return true;
	}
}



?>
