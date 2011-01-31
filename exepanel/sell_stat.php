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
// Создания переменных для JAVA календаря
function for_calendar($array) {
	$str="Array(";
	for($i=0;$i<count($array)-1;$i++) {
		$str .= "'$array[$i]',";
	}
	return $str."'".$array[count($array)-1]."');";
}
// Месяцы
$m['AMONTHS'] = for_calendar($m["_MONTHS"]);
//Дни
$m['ADATOFWEEK'] = for_calendar($m["_DAYS"]);

//************************************************************************ 
// Возвращает по типу статистики, ее описание
function return_label_text($type) {
	GLOBAL $m;
	switch($type) {
		case "profit": $m['INFO'] = $m['_SELL_STAT_INFO1'];break;
		case "report": $m['INFO'] = $m['_SELL_STAT_INFO2'];break;
		default :$m['INFO'] = $m['_SELL_STAT_INFO1'];break;
	}
}

//************************************************************************ 
// Для графика дохода
function profit_stat() {
	GLOBAL $mysql,$template,$m;
	$period = (int) getfrompost("period");
	$from = getfrompost("from");
	$to = getfrompost("to");
	
	$m['FROM'] = addslashes($from);
	$m['TO'] = addslashes($to);
	
	if(!EMPTY($from) && !EMPTY($to)) {
		$from = date("Y.m.d", strtotime($from));
		$to = date("Y.m.d", strtotime($to));
	} else { $from = ''; $to = ''; }
	
	
	if($period > 0) 
		$m['CREATE_GRAPH'] = "<img src=\"$m[URLSITE]/exepanel/graph_stats.php?type=profit&period=$period&from=$from&to=$to&\">";
	
	return $template->show_content("/exepanel/profit_stat.tpl");

}

//************************************************************************ 
// Делает CHECKED для RADIO BUTTON
function checked($type) {
	GLOBAL $m;
	switch($type) {
		case "1": $m['CHECK1'] = 'checked'; break;
		case "2": $m['CHECK2'] = 'checked'; break;
		case "3": $m['CHECK3'] = 'checked'; break;
		case "4": $m['CHECK4'] = 'checked'; break;
		default : $m['CHECK1'] = 'checked';
	}
}

//************************************************************************ 
// Формирование графика отчета о продажах И ССЫЛКИ на формирование отчета
function report_stat() {
	GLOBAL $mysql,$template,$m;

	$from = getfrompost("from");
	$to = getfrompost("to");
	$type_report = (int) getfrompost("type_report") <=0 ? "1": (int) getfrompost("type_report");
	
	$m['FROM'] = $from;
	$m['TO'] = $to;
	
	// Запоминание checked для типа отчета
	checked($type_report);

	if(!EMPTY($from) && !EMPTY($to)) {
		$from = date("Y.m.d", strtotime($from));
		$to = date("Y.m.d", strtotime($to));
		$m['CREATE_GRAPH'] = "<img src=\"$m[URLSITE]/exepanel/graph_stats.php?type=report&period=$period&from=$from&to=$to&\">";
		$m['OPEN_REPORT'] = "<a class=\"inmenu\" href=\"javascript:open_window('$m[URLSITE]/exepanel/sell_stat.php?type=report_txt&type_report=$type_report&period=$period&from=$from&to=$to&',770,400)\">$m[_SELL_STAT_REPORT_SHOW]</a>";
	} 
	
	return $template->show_content("/exepanel/report_stat.tpl");
	
}


//************************************************************************ 
// Построение текстового отчета "Меню Отчет о продажах"
function txt_stat() {
	GLOBAL $mysql,$template,$m;
	
		$from = getfromget("from");
		$to = getfromget("to");
		
		
		// При указание промежутка ДАТ
		if(!EMPTY($from) && !EMPTY($to)) {
			$m[FROM] = addslashes($from); $m[TO] = addslashes($to);
			if($from <= $to) {
				// Составление группировки для SELECT
				switch((int)getfromget("type_report")) {
					case "1": $group = ",DATE_FORMAT(hp.datepay, '%Y-%m-%d %H:%i:%s' ) AS dt "; break;
					case "2": $group = ",DATE_FORMAT(hp.datepay, '%Y-%m-%d' ) AS dt ";  break;
					case "3": $group = ",DATE_FORMAT(hp.datepay, '%Y-%m' ) AS dt "; break;
					case "4": $group = ",DATE_FORMAT(hp.datepay, '%Y' ) AS dt "; break;
					default : $group = ",DATE_FORMAT(hp.datepay, '%Y-%m-%d %H:%i:%s' ) AS dt "; 
				}
				$r = $mysql->query("
					SELECT SUM(hp.amount) AS amount, count(hp.invoice) as ctrans, 
						hp.invoice, hp.buyer_ID, hp.datepay, hp.from_acc_pay, hp.ip,
						hp.good, hp.agent_ID, mp.login as buyer,
						mypurchase.login $group
					FROM history_pay as hp
						LEFT JOIN mypurchase as mp ON mp.id = hp.buyer_ID
						LEFT JOIN mypurchase ON mypurchase.id = hp.agent_ID
					WHERE ( DATE_FORMAT(hp.datepay, '%Y.%m.%d') BETWEEN '".addslashes($from)."' AND '".addslashes($to)."') AND hp.STATUS =1
					GROUP BY dt
					ORDER BY hp.invoice");	
					if($mysql->num_rows($r) > 0) {
					
						// Составление шапки таблицы
						switch((int)getfromget("type_report")) {
							// Подробный отчет
							case "1": {
								$m[LIST_STAT]=<<<EOF
								<tr class="conttop">
									<td align="center">$m[_PAYMENT_INVOICE]</td>
									<td align="center">$m[_PAYMENT_DATEPAY]</td>
									<td align="center">$m[_GOODS_STEP4_IDGOOD]</td>
									<td align="center">$m[_PAYMENT_PRICE] (y.e.)</td>
									<td align="center">$m[_PAYMENT_ACCFROMPAY]</td>
									<td align="center">$m[_MYGOODS_BUYER_LOGIN]</td>
									<td align="center">$m[_PAYMENT_IP]</td>
									<td align="center">$m[_MYGOODS_AGENT_LOGIN]</td>
								</tr>
EOF;
							break;
							}
							// По дням
							case "2": {
								$m[LIST_STAT]=<<<EOF
								<tr class="conttop">
									<td align="center">$m[_SELL_STAT_DATE]</td>
									<td align="center">$m[_PAYMENT_PRICE] (y.e.)</td>
									<td align="center">$m[_PAYMENT_COUNT_TRANSACTION]</td>
								</tr>
EOF;
							break;
							}
							case "3": {
								$m[LIST_STAT]=<<<EOF
								<tr class="conttop">
									<td align="center">$m[_SELL_STAT_DATE]</td>
									<td align="center">$m[_PAYMENT_PRICE] (y.e.)</td>
									<td align="center">$m[_PAYMENT_COUNT_TRANSACTION]</td>
								</tr>
EOF;
							break;
							}
							case "4":{
								$m[LIST_STAT]=<<<EOF
								<tr class="conttop">
									<td align="center">$m[_SELL_STAT_DATE]</td>
									<td align="center">$m[_PAYMENT_PRICE] (y.e.)</td>
									<td align="center">$m[_PAYMENT_COUNT_TRANSACTION]</td>
								</tr>
EOF;
							break;
							}
						}
						
					// Перебор всех строк
					while($row = $mysql->fetch_array($r)) {

						switch((int)getfromget("type_report")) {
							// Подробный отчет
							case "1": {
								$summa += $row[amount];
								$count_trans += 1;
								$m[LIST_STAT] .=<<<EOF
									<tr class="contlight">
										<td align="center">$row[invoice]</td>
										<td align="center">$row[datepay]</td>
										<td align="center">$row[good]</td>
										<td align="center">$row[amount]</td>
										<td align="center">$row[from_acc_pay]</td>
										<td align="center">$row[buyer]</td>
										<td align="center">$row[ip]</td>
										<td align="center">$row[login]</td>
									</tr>
EOF;
							break;
							}
							// По дням
							case "2": {
								$summa += $row[amount];
								$count_trans += $row[ctrans];
								$m[LIST_STAT] .=<<<EOF
									<tr class="contlight">
										<td align="center">$row[dt]</td>
										<td align="center">$row[amount]</td>
										<td align="center">$row[ctrans]</td>
									</tr>
EOF;
							break;
							}
							// По месяцам
							case "3": {
								$summa += $row[amount];
								$count_trans += $row[ctrans];
								$m[LIST_STAT] .=<<<EOF
									<tr class="contlight">
										<td align="center">$row[dt]</td>
										<td align="center">$row[amount]</td>
										<td align="center">$row[ctrans]</td>
									</tr>
EOF;
							break;
							}
							// По годам
							case "4": {
								$summa += $row[amount];
								$count_trans += $row[ctrans];
								$m[LIST_STAT] .=<<<EOF
									<tr class="contlight">
										<td align="center">$row[dt]</td>
										<td align="center">$row[amount]</td>
										<td align="center">$row[ctrans]</td>
									</tr>
EOF;
							break;
							}
						}
					}
					//Вывод общей сумм и кол-ва транзакций
					if($mysql->num_rows($r) > 0) {
						$m[TOTAL_SUMM] = $m["_SELL_STAT_TOTAL_SUMM"].": $summa(y.e.)";
						$m[TOTAL_TRANS] = $m["_SELL_STAT_TOTAL_TRANS"].": $count_trans";
					}
				} else $m[ERROR] = $template->logmsg2($m[_SELL_STAT_LABEL2_NOTFOUND], 0);
			} else $m[ERROR] = $template->logmsg2($m[_SELL_STAT_LABEL2_ERROR], 0);
		} else $m[ERROR] = $template->logmsg2($m[_SELL_STAT_LABEL2_ERROR], 0);
		
		// Вывод шаблона статистики
		echo $template->show_content("/exepanel/report_txt_stat.tpl");
	exit;
}

//************************************************************************ 
// Управление статистикой - вызов функций по типу запроса
function manage_sell_stats() {
	GLOBAL $template;
	return_label_text(getfromget("type"));
	switch(getfromget("type")) {
		case "profit":  return profit_stat(); break;
		case "report": 	return report_stat(); break;
		case "report_txt": return txt_stat(); break;
		default: return $template->show_content("/exepanel/profit_stat.tpl");
	}
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_STATSELLING"];
		$m["CENTERCONTENT"]=manage_sell_stats();
	} else 
		header("LOCATION: $url/exepanel/");
		
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>