<?
session_start();
$pth="../";
###################БЛОК ВКЛЮЧЕНИЙ В global_include.php##############################
@include "$pth/lib/global/global_include.php";
require_once "$pth/lib/global/JsHttpRequest.php";
######################################################################

//************************************************************************ 
// Инициализация класса и подключение к базе данных
$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
if(!$mysql->connect()) die($m[_INST_ERROR1]);
// Инициализация класса работы с логгированием и шаблонизацией
$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);


//************************************************************************ 
// Ключ для редактирования Запоминание в сессию
// $showform - если TRUE то вывод формы либо сообщения успешного сохранения
function mem_key() {
GLOBAL $m;
	if(!validate_key($_SESSION['key_edit_good'])) {
		$form=<<<EOF
		<div id="mem_key">
			<input class="inputcenter" id="memkey" type="text" size="45">
			<input class="inputbutton" type="button" onClick="check_mem_key(document.getElementById('memkey').value)" value="$m[_GOODS_UPDATING_KEY_SAVE]"></td>
		</div>
EOF;
	} else return $m["_GOODS_UPDATING_KEY_IN_SESSION"]; 
	return $form;

}

//************************************************************************ 
// Возвращает SELECT статуса платежа
// $status - Статус платежа
// $name - Имя переменной формы
// $enabled - блокирует или нет элемент формы
function return_status($status,$name, $enabled) {
	GLOBAL $m;
	switch($status) {
		case "0": $check_0 = "selected";break;
		case "1": $check_1 = "selected";break;
		case "2": $check_2 = "selected";break;
	}
	$out=<<<EOF
	<select name="$name" $enabled>
		<option value="0" $check_0>$m[_PAYMENT_STATUS0]
		<option value="1" $check_1>$m[_PAYMENT_STATUS1]
		<option value="2" $check_2>$m[_PAYMENT_STATUS2]
	</select>
EOF;
	return $out;
}

//************************************************************************ 
// Функция формирования дополнительного поля для платежа (вывод доп. полей из таблицы histor_pay)
function return_additional_info($array,$i) {
GLOBAL $m;
	return <<<EOF
<DIV class="hi" id="hi$i">
<table cellSpacing="4" cellPadding="4" width="345" border="0">
	<tr>
		<td>
		<p align="left" class="text">
		<b>$m[_PAYMENT_INVOICE]</b>: #$array[invoice] <br>
		<b>$m[_PAYMENT_DATECREATE]</b>: $array[datecreate]<br>
		<b>$m[_PAYMENT_AGENT]</b>: $array[agent]<br>
		<b>$m[_PAYMENT_IP]</b>: $array[ip]<br>
		<b>$m[_PAYMENT_ACCFROMPAY]</b>: $array[from_acc_pay]<br>
		<b>$m[_PAYMENT_DESCR]</b>: <br>$array[descr]<br>
		</p>
		</td>
	</tr>
</table>
</DIV>
EOF;
}

//************************************************************************ 
// Функция формирования таблицы истории платежей
// $array - массив данных, береться строчка из базы
// $clas - стиль строки
// $i - для формирования имен полей форм
// $enabled - disabled элементов формы
// $head - если TRUE выведет шапу таблицы
// $end - выводит конец таблицы
function return_filled_row($array, $clas, $i, $enabled, $head = FALSE, $end=FALSE) {
	GLOBAL $m,$add_info;
	// Вывод шапки таблицы
	if($head)
	$content =<<<EOF
	<form name="p_c_h" method="POST" enctype="multipart/form-data">
	<table align="center" border="1" class="dash" cellpadding='5' cellspacing='0'>
	<tr>
		<td align="center" class="conttop" width="65">$m[_PAYMENT_INVOICE]</td>
		<td align="center" class="conttop" width="80">$m[_PAYMENT_DATEPAY]</td>
		<td align="center" class="conttop" width="60">$m[_PAYMENT_IDGOOD]</td>
		<td align="center" class="conttop" width="65">$m[_PAYMENT_SUBIDGOOD]</td>
		<td align="center" class="conttop" width="70">$m[_PAYMENT_PRICE] y.e.</td>
		<td align="center" class="conttop" width="120">$m[_PAYMENT_STATUS]</td>
		<td align="center" class="conttop" width="100">$m[_PAYMENT_BUYER]</td>
		<td align="center" class="conttop" width="60">$m[_PAYMENT_ADDITIONAL]</td>
		<td align="center" class="conttop" width="60">$m[_NEWS_ACTION]</td>
		</td>
	</tr>	
EOF;
	// Вывод строк таблицы
	if(!$head) {
	
		// Формирование SELECT'а статуса платежа
		$status = return_status($array[status], "change_payment_status_$i", $enabled);
		// Формирование дополнительной информации в DIV
		$add_info .= return_additional_info($array,$i);
		
		if($array[good_secret_num] > 0)
			$snum =<<<EOF
			<input class="inputcenter" type="text" name="change_payment_snum_$i" size="6" value="$array[good_secret_num]" $enabled>
EOF;
		$content =<<<EOF
		<input type="hidden" name="change_payment_invoice_$i" value="$array[invoice]">
		<tr class="$clas" onmouseover="colorstyle(this,'onover')" onmouseout="colorstyle(this,'$clas')">
			<td align="center" width="65">$array[invoice]</td>
			<td align="center" width="80">$array[datepay]</td>
			<td align="center" width="60"><a class="inmenu" href="$m[URLSITE]/exepanel/goods.php?type=view&tolistgood=$array[cat_ID]">$array[good]</a></td>
			<td align="center" width="65">$snum</td>
			<td align="center" width="70">$array[amount]</td>
			<td align="center" width="120">$status</td>
			<td align="center" width="100">$array[login]</td>
			<td align="center" width="60"><img style="cursor:help" src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/help.gif" onMouseOut="hide_help('hi$i')" onMouseOver="show_help_other('hi$i')"></td>
			<td align="center" width="60"><img onClick="updating(2, '', '', '$array[invoice]', false);" style="cursor:pointer;" src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/delete.gif" title="{$m['_CP_DEL']}"></td>
			</td>
		</tr>	
EOF;
	}
	if($end) $content =<<<EOF
		</table>
		<p><input class="inputbutton" type="button" name="btn" id="btn" onClick="updating(3, '', document.getElementById('p_c_h'), '', true)" value="$m[_INST_SAVE]" $enabled></p>
		</form>
EOF;
	return $content;
}

//************************************************************************ 
// Вывод таблицы ИСТОРИИ ПЛАТЕЖЕЙ и листание таблицы
function payment_show() {
	GLOBAL $mysql,$template,$m,$add_info;
	
	$method = getfromget("method");
	
	//Для листания страниц через AJAX (в случае если "YES")
	$js = getfromget("js");
	
	//Invoice при поиске
	$inv = (int) getfromget("inv");
	if($inv > 0) $add_sql = "WHERE invoice=$inv";
	
	// Выборка исходя из выбранной страничности
	$page=(int) getfromget("page");
	if($page<=1) $limit="0"; else $limit=ceil($page*10)-10;
	
	// Подсчет кол-ва строк
	$rows = $mysql->num_rows($mysql->query("SELECT invoice FROM history_pay"));
	
	$r = $mysql->query("
		SELECT hp.*, gs.cat_ID, mp.login, mp_agent.login as agent
			FROM history_pay AS hp
				LEFT JOIN goods AS gs ON gs.good_ID=hp.good
				LEFT JOIN mypurchase AS mp ON mp.id=hp.buyer_ID
				LEFT JOIN mypurchase AS mp_agent ON mp_agent.id=hp.agent_ID
		$add_sql
		ORDER BY hp.invoice DESC
		LIMIT $limit, 10
		");
	if($mysql->num_rows($r) > 0 || $inv > 0) {
		
		$col=2;
		$i=0;
		
		// Проверка ключа, если не валидный, блокирует элементы
		if(!validate_key($_SESSION['key_edit_good'])) $enabled = "disabled";
		
		// Заполнение строк
		while($row = $mysql->fetch_array($r)) {
			if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
			$out.= return_filled_row(strip($row),$clas, $i, $enabled, false);$col++;
			$i++;
		}
		
		//Сборка таблицы и доп.поля DIV
		if($mysql->num_rows($r) >0) 
			$out = return_filled_row('','','','', true).$out.return_filled_row('','','',$enabled,false,true).$add_info;
			
		// Вывод страничности
			for ($c = 1; $c <= ceil($rows / 10); $c++) {
				If ($page == $c) {
					$m[PAGES_NUMBER].="<span onClick='pagelist($c)' class='numpagecur'>$c</span>";
				 }
				Else {
					if(empty($page)) { $cl="numpagecur"; $page="fdg"; } else $cl="numpage";
					$m[PAGES_NUMBER].="<span onClick='pagelist($c)' class='$cl'><b>$c</b></span>";
				}
				If ($c <> ceil($rows / 10)) {
					$m[PAGES_NUMBER].=" | ";
				}
			} 
				If ($rows <= 0) {
					$m[PAGES_NUMBER].=$template->show_contxt( "<span class=numpagecur>1</span></b>");
				}
				
		// Если не редактирования (либо просто вывод, либо листание)
		if($method <> 'edit') {
			if($js <> 'yes') {
				$m['LIST_PAYMENTS'] = $out;
				// Вывод формы для ввода кода
				$m[MEM_KEY] = mem_key();
			}
			else {
				// Подключение класса AJAX
				$JsHttpRequest = new JsHttpRequest("windows-1251");
				
				$m_ajax["listing"] = $out;
				$m_ajax["pages"] = $m[PAGES_NUMBER];
				
				// При поиске
				if($inv > 0) {
					
					// Проверка на кол-во найденных счетов
					if($mysql->num_rows($r) == 0){
						$m_ajax["msg"]=$template->logmsg2($m['_PAYMENT_INVOICE_NOTFOUND'],0);
						$m_ajax["listing"]='';
						$m_ajax["pages"]='';
					} else {$m_ajax["msg"]=''; $m_ajax["pages"]='';}
					
					// Кнопка возврата к списку
					$m_ajax["back"]= "<input class=\"inputbutton\" type=\"button\" onClick=\"pagelist(1)\" value=\"$m[_PAYMENT_SEARCH_BACK]\">";
			
				}
				
				$GLOBALS["_RESULT"] = $m_ajax;
				exit;
				}
		}
		
	} else {
		// При обычной загрузке страницы, вывод ошибки
		$m['MSGS'] = $template->logmsg2($m['_PAYMENT_NOTFOUND'],0);
		// При выводе через AJAX, вывод ошибки
		if($js == 'yes') {
			// Подключение класса AJAX
			$JsHttpRequest = new JsHttpRequest("windows-1251");
			$m_ajax["msg"]=$template->logmsg2($m['_PAYMENT_INVOICE_NOTFOUND'],0);
			$m_ajax["listing"]='';
			$m_ajax["pages"]='';
			$GLOBALS["_RESULT"] = $m_ajax;
			exit;
		}
	}
	
	return $template->show_content("/exepanel/payment_history.tpl");
}

//************************************************************************ 
// Редактирование счета
function payment_edit() {
	GLOBAL $mysql,$template,$m;
	// Подключение класса AJAX
	$JsHttpRequest = new JsHttpRequest("windows-1251");
	if(validate_key($_SESSION['key_edit_good'])) {
	$m_ajax["key_flag"] = true;
		// Преобразование переданных параметров из формы в массив
		$arr = getarray("change_payment","POST");
		
		for($i=0;$i<count($arr); $i++) {
			$invoice = (int) $arr[$i]['invoice'];
			$r = $mysql->query("SELECT invoice,good FROM history_pay WHERE invoice=$invoice");
			if($mysql->num_rows($r) == 1) {
			
				// Определение номера товара из счета
				$row = $mysql->fetch_array($r); $goodid = $row[good];
				
				$snum = (int)$arr[$i]['snum'];
				$status = (int)$arr[$i]['status'];
				$status =  ($status<0 || $status>2) ? 0:$status;
				
				// Изменение SUBID счета
				if($snum > 0) {
					// Проверка на существование и принадлежность к товару ID загруженного товара
					if($mysql->num_rows($mysql->query("SELECT id_num FROM goods_secret WHERE id_num=$snum AND id_good = $goodid"))==1)
						$r = $mysql->query("UPDATE history_pay SET good_secret_num='$snum', status='$status' WHERE invoice=$invoice LIMIT 1");
					else $err .= "INVALID SUBID #$invoice<br>";
				}
				
				if($snum <= 0) $r = $mysql->query("UPDATE history_pay SET status='$status' WHERE invoice=$invoice LIMIT 1");
				
			}
		}
	
		$m_ajax["msg"] = EMPTY($err) ? $template->logmsg2($m['_SUCC_CHANGE'],1) : $template->logmsg2($err,0);
			
	} else $m_ajax["key_flag"] = false;
	
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
}

//************************************************************************ 
// Удаление счета
function delete_invoice() {
	GLOBAL $mysql,$template,$m;
	// Подключение класса AJAX
	$JsHttpRequest = new JsHttpRequest("windows-1251");
	
	if(validate_key($_SESSION['key_edit_good'])) {
		$invoice = (int)getfromget('invoice');
		$m_ajax["key_flag"] = true;
		$r = $mysql->query("DELETE FROM history_pay WHERE invoice=$invoice LIMIT 1");
		$m_ajax["msg"] = ($r==1 ? $template->logmsg2($m['_SUCC_DELETE'], 1): $template->logmsg2($m['_PAYMENT_INVOICE_NOTFOUND'],0));
	} else $m_ajax["key_flag"] = false;
	
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
}

//************************************************************************ 
// Управление историями платежей
function manage_payment_history() {
	GLOBAL $template;

	switch(getfromget("type")) {
		case "delete": return delete_invoice();break;
		case "edit":  return payment_edit(); break;
		default:return payment_show();
	}
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_PAYHISTORY"];
		$m["CENTERCONTENT"]=manage_payment_history();
	} else 
		header("LOCATION: $url/exepanel/");
		
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>