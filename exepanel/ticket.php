<?
session_start();
$pth="../";
###################БЛОК ВКЛЮЧЕНИЙ В global_include.php##############################
@include "$pth/lib/global/global_include.php";
######################################################################

//************************************************************************ 
// Инициализация класса и подключение к базе данных
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);
// Инициализация класса работы с логгированием и шаблонизацией
$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);

//************************************************************************ 
// Настройка тикет-системы
function ticket_setting() {
	GLOBAL $template, $mysql, $m;
	if(getfrompost("method")=="change") {
	
		$mail = toLower(getfrompost("mail"));
		$mail = (is_email($mail) ? $mail: "");
		
		// Имя сервиса
		$name = addslashes(htmlspecialchars(getfrompost("name")));
		
		$smtp_server = addslashes(toLower(getfrompost("smtp_server")));
		$smtp_port = (int)getfrompost("smtp_port");
		
		$pop3_server = addslashes(toLower(getfrompost("pop3_server")));
		$pop3_port = (int)getfrompost("pop3_port");
		
		if($pop3_port == 0) $pop3_port = 110;
		if($smtp_port == 0) $smtp_port = 25;
		
		// Логин и пасс
		$user = addslashes(getfrompost("user"));
		$pwd = encryptdata(getfrompost("pwd"));
		
		// Включение регистрации тикетов через POP3
		$on_off = (int)getfrompost("on_off");
		if($on_off<>1) $on_off = 0;
		
		// Сохранение писем на ящике
		$save = (int)getfrompost("save");
		if($save <> 1) $save=0;
		
		// Авто закрытие тикета
		$autoclose_status = (int)getfrompost("autoclose_status");
		if($autoclose_status <> 1) $autoclose_status=0;
		$autoclose_days = (int) getfrompost("autoclose_days");
		if($autoclose_days <= 0) $autoclose_days=1;
		
		// Авто удаление тикета
		$autodelete_status = (int)getfrompost("autodelete_status");
		if($autodelete_status <> 1) $autodelete_status=0;
		$autodelete_days = (int) getfrompost("autodelete_days");
		if($autodelete_days <= 0) $autodelete_days=1;
		
		$autofilter_status = (int)getfrompost("autofilter_status");
		if($autofilter_status  <> 1) $autofilter_status =0;
		
		// Шаблон подписи
		$sign=addslashes(getfrompost("sign"));
		
		// Шаблон сообщения при регистрации тикета
		$subject=addslashes(htmlspecialchars(getfrompost("subject")));
		$msg=addslashes(getfrompost("msg"));
		
		// Обновление настроек
		$mysql->query("UPDATE ticketsystem_setting SET email='$mail', name='$name', smtp_server='$smtp_server', smtp_port='$smtp_port', 
			pop3_server='$pop3_server', pop3_port='$pop3_port', login='$user',pwd='$pwd', status='$on_off', status_save='$save', 
			close_status='$autoclose_status', close_day='$autoclose_days', delete_status='$autodelete_status', delete_day='$autodelete_days',
			status_ban='$autofilter_status', sign_msg='$sign', subject='$subject', msg='$msg' WHERE tpl_ID=1");
		$template->logtxt("_SUCC_CHANGE",1);
	}
	
		$r=$mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=1");
		$row=strip($mysql->fetch_array($r));
		$m[MAIL]=$row[email];
		$m["NAME"]=$row[name];
		$m[SMTP_SERVER]=$row[smtp_server];
		$m[SMTP_PORT]=$row[smtp_port];
		$m[POP3_SERVER]=$row[pop3_server];
		$m[POP3_PORT]=$row[pop3_port];
		$m[USER]=$row[login];
		$m[PASSWORD]=decryptdata($row[pwd]);
		$m[CHECK_ON_OFF]=($row[status]==1 ? "checked": "");
		$m[CHECK_SAVE]=($row[status_save]==1 ? "checked": "");
		$m[CHECK_AUTOCLOSE]=($row[close_status]==1 ? "checked": "");
		$m[CHECK_AUTODELETE]=($row[delete_status]==1 ? "checked": "");
		$m[CHECK_AUTOFILTER] = ($row[status_ban]==1 ? "checked": "");
		$m[AUTOCLOSE_DAYS]=$row[close_day];
		$m[AUTODELETE_DAYS]=$row[delete_day];
		$m[SIGN]=$row[sign_msg];
		$m[SUBJECTT]=$row[subject];
		$m[MSG]=$row[msg];
	
	return $template->show_content("/exepanel/ticket_setting.tpl");
}

//************************************************************************ 
// Отображение списка тикетов
function ticket_show($status) {
	GLOBAL $mysql,$template,$m;
	
	switch ($status) {
		// Новый тикет
		case "new": {
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_NOREAD}"); 
			$st=2;
			$stt = "new";
			break;
		}
		// Открытый тикет
		case "opened": {
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_OPENED}"); 
			$st=1;
			$stt = "opened";
			break;
		}
		// Закрытый тикет
		case "closed": {
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_CLOSED}"); 
			$st=0;
			$stt = "closed";
			break;
		}	
		// Поиск тикета
		case "search": {
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_SEARCH}");
			$m[ID]= getfromget("id");
			$m[EMAIL] = getfromget("email");
			$m[SUBJECT] = getfromget("subject");
			$st=3;
			$stt = "search";
			$url_page="&method=search&id=$m[ID]&email=$m[EMAIL]&subject=$m[SUBJECT]&search_in=$st";
			$m[SEARCH_FORM]=$template->show_content("/exepanel/ticket_search.tpl");
			break;
		}
		default : {
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_NOREAD}"); 
			$st=2;
			$stt = "new";
			break;
		}
	}
	
	// Выборка исходя из выбранной страничности
	$page=(int) getfromget("page");
	if($page<=1) $limit="0"; else $limit=ceil($page*15)-15;
	
	// Формирование запроса, кроме поискового запроса
	if(($st==0||$st==1||$st==2) && getfromget("method")<>"search") {
		$zap1="SELECT COUNT(msgs.ticket_ID)-1 as reply FROM ticketsystem as system, ticketsystem_msgs as msgs 
			WHERE system.status=$st AND system.ticket_ID=msgs.ticket_ID GROUP BY msgs.ticket_ID";
		$zap2="SELECT system.*, COUNT(msgs.ticket_ID)-1 as reply, SUM(msgs.status) as new, MAX(msgs.datesend) as maxdate FROM ticketsystem as system, ticketsystem_msgs as msgs 
			WHERE system.status=$st AND system.ticket_ID=msgs.ticket_ID GROUP BY msgs.ticket_ID ORDER BY  maxdate, reply LIMIT $limit,15";
		$msgnotfound=$template->logmsg2($template->show_contxt($m[CURRENT_SELECT]." {_TICKET_NOTFOUND} "),3);
	} else {
	
			// Формирование запроса для ПОИСКА
			$search_in = (int) getfromget("search_in");
			$id = (int) getfromget("id");
			$email = addslashes(trim(getfromget("email")));
			$subject = addslashes(trim(encode_form(getfromget("subject"))));
			
			if(!empty($email) || !empty($subject))
				$sql2 = "AND (system.ticket_ID = $id OR (system.email LIKE '%$email%' AND system.subject LIKE '%$subject%'))";
					else $sql2 = "AND system.ticket_ID = $id";
			if(!empty($email) || !empty($subject) || !empty($id))
				$msgnotfound=$template->logmsg2($template->show_contxt("{_TICKET_NOTFOUND2}"),3);	
				
			// Если выбран поиск по всем категориям
			if($search_in >=0 && $search_in<=2) $sql1 = "system.status=$search_in AND"; else $sql1="";
			
			$zap1="SELECT COUNT(msgs.ticket_ID)-1 as reply FROM ticketsystem as system, ticketsystem_msgs as msgs 
				WHERE $sql1 system.ticket_ID=msgs.ticket_ID $sql2 GROUP BY msgs.ticket_ID";
			$zap2="SELECT system.*, COUNT(msgs.ticket_ID)-1 as reply, SUM(msgs.status) as new, FROM ticketsystem as system, ticketsystem_msgs as msgs 
				WHERE $sql1 system.ticket_ID=msgs.ticket_ID $sql2 GROUP BY msgs.ticket_ID ORDER BY system.datecreate DESC, reply DESC LIMIT $limit,15";

		} 
	
	// Подсчет кол-ва записей всего
	$r=$mysql->query($zap1);
	$rows = $mysql->num_rows($r);
	
	// Подсчет кол-ва новых сообщений
	//while($k = $mysql->fetch_array($r)) if( (int)$k['new'] == 1) $new ++;

	
	// Выборка согласно текущей страницы
	$r=$mysql->query($zap2);
	if($mysql->num_rows($r) > 0) {
		$col=2;
		while($row = $mysql->fetch_array($r)) {
			if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
			$col++;
			$row=strip($row);
			if(strcasecmp($row[dateclose], "0000-00-00 00:00:00")==0)$row[dateclose]="-";
			if($row[status]==1 || $row[status]==2) {
				$metd="close"; $lng="{_TICKET_CLOSE}"; $img="mail_close";
			} else { $metd="open"; $lng="{_TICKET_OPEN}"; $img="mail_open"; }
			$ico=
			$template->show_contxt("
			<a href='{URLSITE}/exepanel/ticket.php?type=view&retr=$stts&ticketid=".$row[ticket_ID]."&'>
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/mail_view.gif' title='{_TICKET_VIEW} ".$row[ticket_ID]."'></a>&nbsp;&nbsp;&nbsp;
			<a href=\"javascript:submiturl('{URLSITE}/exepanel/ticket.php?type=$metd&retr=$stt&ticketid=".$row[ticket_ID]."&', '$lng ".$row[ticket_ID]."?')\">
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$img.gif' title='$lng ".$row[ticket_ID]."'></a>&nbsp;&nbsp;&nbsp;
			<a href=\"javascript:submiturl('{URLSITE}/exepanel/ticket.php?type=del&retr=$stt&ticketid=".$row[ticket_ID]."&', '{_TICKET_DELETE} ".$row[ticket_ID]."?')\">
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/mail_delete.gif' title='{_TICKET_DELETE} ".$row[ticket_ID]."'></a>
			");
			$subj = substr($row[subject], 0,60);
			$m[TICKET_LIST].=<<<EOF
			<tr>
				<td align=center class="$clas" width="70">$row[ticket_ID]</td>
				<td align=left valign=top class="$clas" width="170"><font style="font-size:7pt">$subj...</font></td>
				<td align=center class="$clas" width="100">$row[datecreate]</td>
				<td align=center class="$clas" width="100">$row[dateclose]</td>
				<td align=center class="$clas" width="60">$row[reply]</td>
				<td align=center class="$clas" width="80">$row[new]</td>
				<td align=center class="$clas" width="100">$ico</td>
			</tr>
EOF;
		}
		
			// Вывод страничности
			for ($c = 1; $c <= ceil($rows / 15); $c++) {
				If ($page == $c) {
					$m[PAGES_NUMBER].=$template->show_contxt("<a href='{URLSITE}/exepanel/ticket.php?type=$status&page=$c$url_page' class='numpagecur'>$c</a>");
				 }
				Else {
					if(empty($page)) { $cl="numpagecur"; $page="fdg"; } else $cl="numpage";
					$m[PAGES_NUMBER].=$template->show_contxt("<a href='{URLSITE}/exepanel/ticket.php?type=$status&page=$c$url_page' class='$cl'><b>$c</a>");
				}
				If ($c <> ceil($rows / 15)) {
					$m[PAGES_NUMBER].=" | ";
				}
			} 
				If ($rows <= 0) {
					$m[PAGES_NUMBER].=$template->show_contxt( "<span class=numpagecur>1</span></b>");
				}
		
	} else {
		$m[NOTFOUND]=$msgnotfound;
		}
	
	return $template->show_content("/exepanel/ticket_view.tpl");
}

//************************************************************************ 
// Управление статусами тикета и удаление тикета
function ticket_status($status) {
	GLOBAL $mysql,$template;
	$ticketid = (int) getfromget("ticketid");
	switch($status) {
		case "open": 	{ $st = 1; $dtopen = date("Y-m-d H:i:s"); $part = "datecreate='$dtopen',"; $dtclose = "0000-00-00 00:00:00"; $upd = "update"; break;}
		case "close": 	{ $st = 0; $dtclose = date("Y-m-d H:i:s"); $upd = "update"; break;}
		case "del":		{ $upd ="delete"; break;}
		default : { $ticketid = 0;}
	}
	
	// Изменение статуса
	if($ticketid > 0 && $upd == "update") {
		$r=$mysql->query("SELECT * FROM ticketsystem WHERE ticket_ID='$ticketid'");
		if($mysql->num_rows($r) == 1) {
			$row=$mysql->fetch_array($r);
			if($row[status] <> $st) {
				$mysql->query("UPDATE ticketsystem SET status='$st', $part dateclose='$dtclose' WHERE ticket_ID='$ticketid'");
				$template->logtxt("_SUCC_CHANGE",1);
			}
		}
	}
	
	// Удаление тикета
	if($ticketid > 0 && $upd == "delete") {
		$mysql->query("DELETE ticketsystem, ticketsystem_msgs FROM  ticketsystem, ticketsystem_msgs 
				WHERE ticketsystem.ticket_ID='$ticketid' AND ticketsystem.ticket_ID = ticketsystem_msgs.ticket_ID");
		$template->logtxt("_SUCC_DELETE",1);
	}
	
	return ticket_show(getfromget("retr"));
}

// Вывод массива
function print_array($mass) {
	for($i=0;$i<=count($mass)-1; $i++) $str.=$mass[$i]."<br>";
	return $str;
}

// Добавление > в каждую строчку
function rpl($str) {
	for($i=0;$i<count($str);$i++)
		$string.=">".$str[$i];
	return $string;
}

//************************************************************************ 
// Отображение цепочки сообщений и ДОБАВЛЕНИЕ ОТВЕТОВ
function ticket_view() {
	GLOBAL $template, $mysql, $m;
	$ticketid = (int) getfromget("ticketid");
	$msgid = (int) getfromget("msgid");
	$retr=getfromget("retr");
	switch($retr) {
		case "new": $stt = "new";break;
		case "opened": $stt = "opened";break;
		case "closed": $stt = "closed";break;
		case "search": $stt = "search";break;
		default: $stt = "new";
	}
	$m[RETR] = $stt;
	$method = getfromget("method");
	
	if($ticketid > 0) {
		$r=$mysql->query("SELECT * FROM ticketsystem WHERE ticket_ID='$ticketid'");
		if($mysql->num_rows($r)==1) {
			$vsego=$mysql->query("SELECT * FROM  ticketsystem_msgs
				WHERE ticket_ID='$ticketid' ORDER BY datesend DESC");
				
			$m[CURRENT_SELECT]=$template->show_contxt("{_TICKET_VIEW2}"); 
			$row=strip($mysql->fetch_array($r));
			
			// Перевод в статус открытый
			if($row[status] == 2) $mysql->query("UPDATE ticketsystem SET status=1 WHERE ticket_ID='$ticketid'");
			$m[TICKETID] = $ticketid;
			$m[MAIL] = $row[email];
			$m[DATECREATE] = $row[datecreate];
			if("0000-00-00 00:00:00" == $row[dateclose]) $row[dateclose]="-";
			$m[DATECLOSE] = $row[dateclose];
			$m[SUBJECT_TICKET] = $row[subject];
			
			// Ответ на сообщение в тикете
			if($method == "reply") {
				$r2=$mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=1");
				$setting = strip($mysql->fetch_array($r2));
				
				// Отправка письма
				if(getfrompost("action") == "yes") {
					$base['from'] = $setting[email];
					$base['subject'] = base64_encode(str_replace(array('\"', "\'"), array("'","'"),trim(getfrompost("subject"))));
					$datesend = date("Y-m-d H:i:s");
					$to = trim(getfrompost("to"));
					$base['charset'] = getfrompost("charset");
					$base['date'] = $datesend;
					$base['rec_client'] = "reply";
					$base['body'] = base64_encode(nl2br(str_replace(array('\"', "\'"), array('"',"'"), trim(getfrompost("msg")))));
				
					if(is_email($to)) {
						
						$message_id = md5crypt(microtime(1));
						$msg=addslashes(serialize($base));
						$mysql->query("INSERT INTO ticketsystem_msgs VALUES('', $ticketid, '$message_id', '$datesend', '$msg', '0')");
						$mysql->query("UPDATE ticketsystem_msgs SET status=0 WHERE msg_ID=$msgid LIMIT 1");

						// Добавление в очередь письма
						$mysql->query("INSERT INTO email_queue VALUES('', 1, '$to', '".$base[charset]."', '".$base['subject']."', '".$base[body]."')");
						
						header("LOCATION: ". $template->show_contxt("{URLSITE}/exepanel/ticket.php?type=view&ticketid=$ticketid"));
						
					} else {
					
						// Если еmail не правилен
						$m[CHARSET] = $base['charset'];
						$m[TO] = $to;
						$m[SUBJECT] = getfrompost("subject");
						$m[MESSAGE] = getfrompost("msg");
						$m[MSGID] = $msgid;
						$m[TICKET_FORM] = $template->show_content("/exepanel/ticket_reply.tpl");
						$template->logtxt("_TICKET_EMAIL_ERR", 0);	
						}
				}
				
				// Отображение заполненой формы из базы
				if(getfrompost("action") <> "yes") {
					$r=$mysql->query("SELECT * FROM  ticketsystem_msgs
					WHERE ticket_ID='$ticketid' AND msg_ID='$msgid' ORDER BY datesend DESC");
					
					if($mysql->num_rows($r) == 1) {
						$row = $mysql->fetch_array($r);
						$row= unserialize(stripslashes($row[msg]));
						$m[CHARSET] = $row[charset];
						$m[TO] = $row[from];
						$m[SUBJECT] = str_replace('"', "'", base64_decode($row[subject]));
						$m[BODYMSG] = rpl(explode("\n", strip_tags(trim(base64_decode($row[body])))));
						$m[MESSAGE] = $template->show_contxt($setting[sign_msg]);
						$m[MSGID] = $msgid;
						$m[TICKET_FORM] = $template->show_content("/exepanel/ticket_reply.tpl");
					} else 
						$m[NOTFOUND] = $template->logtxt("_TICKET_MSGNOTFOUND",0);
				}	
			} else {
				
				// Выборка исходя из выбранной страничности
				$page=(int) getfromget("page");
				if($page<=1) $limit="0"; else $limit=ceil($page*4)-4;
				
				
				$rows=$mysql->num_rows($vsego);
				
				// Вывод страничности
				for ($c = 1; $c <= ceil($rows / 4); $c++) {
					If ($page == $c) {
						$m[PAGES_NUMBER].=$template->show_contxt("<a href='{URLSITE}/exepanel/ticket.php?type=view&ticketid=$ticketid&page=$c' class='numpagecur'>$c</a>");
					 }
					Else {
						if(empty($page)) { $cl="numpagecur"; $page="fdg"; } else $cl="numpage";
						$m[PAGES_NUMBER].=$template->show_contxt("<a href='{URLSITE}/exepanel/ticket.php?type=view&ticketid=$ticketid&page=$c' class='$cl'><b>$c</a>");
					}
					If ($c <> ceil($rows / 4)) {
						$m[PAGES_NUMBER].=" | ";
					}
				} 
					If ($rows <= 0) {
						$m[PAGES_NUMBER].=$template->show_contxt( "<span class=numpagecur>1</span></b>");
					}
				
				// Цепочка сообщений
				$r=$mysql->query("SELECT * FROM  ticketsystem_msgs
				WHERE ticket_ID='$ticketid' ORDER BY datesend DESC LIMIT $limit,5");
				
				while($row = $mysql->fetch_array($r)) {				
					$row_msg = unserialize(stripslashes($row[msg]));
					$m[HEADER_IP] = (is_array($row_msg[rec_client]) ? print_array($row_msg[rec_client]) : $row_msg[rec_client]);
					$m[DATESEND] = $row[datesend];
					$m[MAILSENDER] = $row_msg[from];
					$m[SUBJECT] = base64_decode($row_msg[subject]);
					$m[MESSAGE] = base64_decode($row_msg[body]);
					$m[MSGID] = $row[msg_ID];
					$m[CHARSET] = $row_msg[charset];
					switch($row[status]) {
						case "0": $m[STATUS] = $template->show_contxt("{_TICKET_STATUS0}"); break;
						case "1": $m[STATUS] = $template->show_contxt("{_TICKET_STATUS1}"); break;
						case "2": $m[STATUS] = $template->show_contxt("{_TICKET_STATUS2}"); break;
					}
					
					
					$m[TICKET_FORM].=$template->show_content("/exepanel/ticket_form.tpl");
				}
			}	
		return $template->show_content("/exepanel/ticket_show.tpl");
		
		} else return ticket_show($stt);
	} else return ticket_show($stt);
}

//************************************************************************ 
// Ведение черного списка EMAIL
function ticket_filter() {
	GLOBAL $mysql,$template,$m;
	
	$mail = toLower(trim(getfrompost("mail")));
	$descr = addslashes(getfrompost("descr"));
	
	// Добавление 
	if(getfrompost("method") == "add" && !EMPTY($mail)) {
		if(is_email($mail)) {
			$r = $mysql->query("SELECT * FROM ticketsystem_ban WHERE email='$mail'");
			if($mysql->num_rows($r) == 0) {
				$mysql->query("INSERT INTO ticketsystem_ban VALUES('','$mail','$descr')");
				$template->logtxt("_SUCC_ADD", 1);
			} else $template->logtxt("_TICKET_EMAIL_ISSET",0);
		} else $template->logtxt("_TICKET_EMAIL_ERR", 0);
	}
	
	// Удаление
	if(getfrompost("method") == "del" && !EMPTY($mail)) {
		if(is_email($mail)) {
			$r = $mysql->query("SELECT * FROM ticketsystem_ban WHERE email='$mail'");
			if($mysql->num_rows($r) > 0) {
				$mysql->query("DELETE FROM ticketsystem_ban WHERE email='$mail' LIMIT 1");
				$template->logtxt("_SUCC_DELETE", 1);
			} else $template->logtxt("_TICKET_EMAIL_EMPTY", 0);
		} else $template->logtxt("_TICKET_EMAIL_ERR", 0);
	}
	
	// Отображение списка баненых мыл
	if(getfromget("showall") == "yes") {
		$r=$mysql->query("SELECT * FROM ticketsystem_ban");
		while($row = $mysql->fetch_array($r)) {
			$row=strip($row);
			echo "$row[email]  -  $row[descr]<br>";
		}
		exit;
	}
	
	$rr=$mysql->query("SELECT * FROM ticketsystem_ban");
	$m[COUNTALL] = $mysql->num_rows($rr);
	return $template->show_content("/exepanel/ticket_filter.tpl");
}

//************************************************************************ 
// Управление тикетами
function manage_ticket() {
	switch(getfromget("type")) {
		case "new":    	return ticket_show("new"); break;
		case "opened": 	return ticket_show("opened"); break;
		case "closed": 	return ticket_show("closed"); break;
		case "setting":	return ticket_setting(); break;
		case "view": 	return ticket_view();break;
		case "filter":	return ticket_filter();break;
		case "search": 	return ticket_show("search"); break;
		case "close":	return ticket_status("close"); break;
		case "open": 	return ticket_status("open"); break;
		case "del": 	return ticket_status("del"); break;
		default : 		return ticket_show("new"); break;
	}
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_TICKET_SYS"];
		$m["CENTERCONTENT"]=manage_ticket();
	} else 
		header("LOCATION: $url/exepanel/");
		
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>