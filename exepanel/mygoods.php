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
//Добавление дополнительных полей
function option_fields_add() {
	GLOBAL $template,$mysql,$m;
	$nameru = addslashes(getfrompost('nameru'));
	$nameen = addslashes(getfrompost('nameen'));
	switch((int)getfrompost('type_field')) {
		case 1: $type_field = 1; break;
		case 3: $type_field = 3; break;
		default: $type_field = 1; break;
	}
	
	if(!empty($nameen) || !empty($nameru)) {
		$mysql->query("INSERT INTO mypurchase_reg_fields VALUES('', '$type_field', '$nameru', '$nameen', '', '0')");
		$m['INFO_MESSAGE'] = $template->logmsg2($m['_SUCC_ADD'], 1);
	} else 
		$m['INFO_MESSAGE'] = $template->logmsg2($m['_ERR_EMPTY_REQUIRE'], 0);
		return option_fields_view();
}


//************************************************************************ 
//Редактирование дополнительных полей
function option_fields_edit() {
	GLOBAL $template,$mysql,$m;
	$mas = getarray("edit_fields", "POST");
	for($i=0; $i<count($mas); $i++) {
		$mas[$i]['id'] = (int) $mas[$i]['id'];
		if($mas[$i]['del'] <> 1) {
			switch((int)$mas[$i]['typefields']) {
				case 1: $type_field = 1; break;
				case 3: $type_field = 3; break;
				default: $type_field = 1; break;
			}
			
			$mysql->query("
			UPDATE mypurchase_reg_fields 
			SET name='".$mas[$i]['nameru']."', name_en='".$mas[$i]['nameen']."', type_fields_id='$type_field', 
				sort='".(int)$mas[$i]['sort']."'
			WHERE reg_field_ID = ".$mas[$i]['id']);
			
		} else {
			$mysql->query("DELETE FROM mypurchase_reg_fields WHERE reg_field_ID=".$mas[$i]['id']);
			$mysql->query("DELETE FROM mypurchase_reg_fields_value WHERE reg_field_ID=".$mas[$i]['id']);
		}
		
	}
	$m['INFO_MESSAGE2'] = $template->logmsg2($m['_SUCC_CHANGE'],1);
	return option_fields_view();
}


//************************************************************************ 
//Вывод списка дополнительных полей
function option_fields_view() {
	GLOBAL $template,$mysql,$m;
	$r = $mysql->query("SELECT * FROM mypurchase_reg_fields ORDER BY sort");
	if($mysql->num_rows($r) > 0) {
		$i=0;
		while($row = $mysql->fetch_array($r)) {
			$row = strip($row);
			if($row['type_fields_id'] == 1) $checked1 = "checked"; else $checked1="";
			if($row['type_fields_id'] == 3) $checked2 = "checked"; else $checked2="";
			$tr .=<<<EOF
	<tr>
		<input type="hidden" name="edit_fields_id_$i" value="$row[reg_field_ID]">
		<td align=center class="contlight" width=150><input class="inputtext" type="text" name="edit_fields_nameru_$i" value="$row[name]"></td>
		<td align=center class="contlight" width=150><input class="inputtext" type="text" name="edit_fields_nameen_$i" value="$row[name_en]"></td>
		<td align=center class="contlight" width=100>
			<table>
				<tr class="contlight">
					<td><input type="radio" name="edit_fields_typefields_$i" value="1" $checked1></td>
					<td>$m[_CUTSOMER_OPTION_TYPE1]</td>
				</tr>
				<tr class="contlight">
					<td><input type="radio" name="edit_fields_typefields_$i" value="3" $checked2></td>
					<td>$m[_CUTSOMER_OPTION_TYPE3]</td>
				</tr>
			</table>
		</td>
		<td align=center class="contlight" width=50><input class="inputcenter" type="text" name="edit_fields_sort_$i" size="4" value="$row[sort]"></td>
		<td align=center class="contlight" width=50><input type="checkbox" name="edit_fields_del_$i" size="4" value="1"></td>
	</tr>
EOF;
			$i++;
		}
		$tr.=<<<EOF
		<tr>
			<td colspan=5 align=center>
			<input class="inputbutton" type="submit" value="$m[_INST_SAVE]">
			</td>
		</tr>
EOF;
		$m['OPTION_LIST'] = $tr;
	}
	
	return $template->show_content("/exepanel/option_reg_fields.tpl");
}


//************************************************************************ 
//Изменение статуса платежа в выплатах
function request_money_change(){
	global $template, $m, $mysql;
	$invoice = (int) getfrompost("id");
	$status =(int) getfrompost("status");
	$descr = addslashes(trim(getfrompost("descr")));
	if($status >=0 && $status <=2 && $invoice > 0) {
		switch($status) {
			case 0: $r=$mysql->query("UPDATE mypurchase_history_pay SET status = '0', descr = '$descr', datepay='0000-00-00 00:00:00' WHERE id=".$invoice); break;
			case 1: $r=$mysql->query("UPDATE mypurchase_history_pay SET status = '1', descr = '$descr', datepay='".date("Y-m-d H:m:i")."' WHERE id=".$invoice); break;
			case 2: $r=$mysql->query("UPDATE mypurchase_history_pay SET status = '2', descr = '$descr', datepay='0000-00-00 00:00:00' WHERE id=".$invoice); break;
		}
		
		if($r) 
			$m['INFO_MESSAGE'] = $template->logmsg2($m['_SUCC_CHANGE'], 1); 
			else $m['INFO_MESSAGE'] = $template->logmsg2($m['_ERR_REQUEST'], 0);
	} else $m['INFO_MESSAGE'] = $template->logmsg2($m['_ERR_REQUEST'], 0);
	return request_money_view();
}


//************************************************************************ 
//Вывод списка дополнительных полей
// $search = FALSE - отключен поиск, TRUE - включен поиск
function request_money_view($search = FALSE){
	global $template, $m, $mysql;
	
	if($search) {
		$id = (int) getfrompost("id");
		$r = $mysql->query("
		SELECT mhp.*, my.login as login
		FROM mypurchase_history_pay as mhp
		LEFT JOIN mypurchase as my ON my.id = mhp.mypurchase_ID
		WHERE mhp.id = $id
		");
	} else 
	$r = $mysql->query("
		SELECT mhp.*, my.login as login
		FROM mypurchase_history_pay as mhp
		LEFT JOIN mypurchase as my ON my.id = mhp.mypurchase_ID
		WHERE mhp.status = 2
		ORDER BY mhp.datecreate
		");
	
	$col=2;
	if($mysql->num_rows($r) > 0) {
		while($row = $mysql->fetch_array($r)) {
			if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
			$col++;
			$row = strip($row);
			if($row['status'] == 0) $checked0 = "selected"; else $checked0="";
			if($row['status'] == 1)	$checked1 = "selected"; else $checked1="";
			if($row['status'] == 2)	$checked2 = "selected"; else $checked2="";
			if($row['datepay'] == "0000-00-00 00:00:00") $row['datepay']='-';
			$tr.=<<<EOF
	<form action="$m[URLSITE]/exepanel/mygoods.php?method=request_money" method="POST" onSubmit="check_request(this); return false;">
	<input type="hidden" name="type" value="change">
	<input type="hidden" name="id" value="$row[id]">
	<tr class="$clas" onmouseover="colorstyle(this,'onover')" onmouseout="colorstyle(this,'$clas')">
		<td align=center width="80">$row[id]</td>
		<td align=center width="80">$row[datecreate]</td>
		<td align=center width="80">$row[datepay]</td>
		<td align=center width="80">$row[login]</td>
		<td align=center width="80">$row[amount]</td>
		<td align=justify width="180">
		<textarea cols="25" rows="4" name="descr">$row[descr]</textarea>
		</td>
		<td align=center width="80">
		<select name="status">
		<option value="2" style="background-color:#f1f400" $checked2>$m[_CUSTOMER_REQUEST_PAYMENTS_STATUS2]
		<option value="1" style="background-color:#69ff27" $checked1>$m[_CUSTOMER_REQUEST_PAYMENTS_STATUS1]
		<option value="0" style="background-color:red; color:#FFFFFF" $checked0>$m[_CUSTOMER_REQUEST_PAYMENTS_STATUS0]
		</select>
		</td>
		<td align=center><input class="inputbutton" type="submit" value="$m[_CUSTOMER_REQUEST_PAYMENTS_CONFIRM]"></td>
	</tr>
	</form>
EOF;
		$m['REQUEST_LIST'] = $tr;
		}
	}
	return $template->show_content("/exepanel/request_money.tpl");
}

//************************************************************************ 
// Поиск и Вывод покупателей
// $search - если поиск TRUE
function customers_view($search = FALSE) {
	global $template, $m, $mysql;
	
	if($search) $add_query = "WHERE mp.login LIKE '%".addslashes(strip_tags(getfrompost("login")))."%'";
	
	$page=(int) getfromget("page");
	if($page<=1) $limit="0"; else $limit=ceil($page*15)-15;
	
	$r = $mysql->query("
				SELECT mp.*, count(hp.invoice) as accept
				FROM mypurchase as mp
				LEFT JOIN history_pay as hp ON hp.buyer_ID =  mp.id AND hp.status = 1
				$add_query
				GROUP BY mp.id
				");
	$rows = $mysql->num_rows($r);
	
	$r = $mysql->query("
				SELECT mp.*, count(hp.invoice) as accept
				FROM mypurchase as mp
				LEFT JOIN history_pay as hp ON hp.buyer_ID =  mp.id AND hp.status = '1'
				$add_query
				GROUP BY mp.id
				LIMIT $limit, 15
				");
	
	$col=2;
	while($row = $mysql->fetch_array($r)) {
		$row = strip($row);
		if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
		switch($row['status']) {
			case 0: $stat = $m['_CUSTOMER_STATUS0']; break;
			case 1: $stat = $m['_CUSTOMER_STATUS1'];break;
			case 2: $stat = $m['_CUSTOMER_STATUS2']; break;
		}
		
		$col++;
		$tr.=<<<EOF
	<tr class="$clas" onmouseover="colorstyle(this,'onover')" onmouseout="colorstyle(this,'$clas')">
		<td align=center width="80">$row[login]</td>
		<td align=left width="120">$row[name]</td>
		<td align=center width="80">$row[agent_amount]</td>
		<td align=left width="120">$row[mail]</td>
		<td align=center width="120"><b>$stat<b></td>
		<td align=center width="80">$row[accept]</td>
		<td align=center>
		<img src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/edit.gif">
		<img src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/histrory_payments.gif">
		<img src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/history_agent.gif">
		<img src="$m[URLSITE]/templates/$m[TEMPLDEF]/exepanel/img/delete.gif">
		</td>
	</tr>
EOF;
	}
	$m['CUSTOMER_LIST'] = $tr;
	
	// Вывод страничности
	  for ($c = 1; $c <= ceil($rows / 15); $c++) {
		  If ($page == $c) {
		   $m[PAGES_NEWS].=$template->show_contxt("<a href='{URLSITE}/exepanel/mygoods.php?method=customers&page=$c' class='numpagecur'>$c</a>");
		  }
	  Else {
		  if(empty($page)) { $cl="numpagecur"; $page="fdg"; } else $cl="numpage";
		   $m[PAGES_NEWS].=$template->show_contxt("<a href='{URLSITE}/exepanel/mygoods.php?method=customers&page=$c' class='$cl'><b>$c</a>");
		  }
	  If ($c <> ceil($rows / 15)) {
	   $m[PAGES_NEWS].=" | ";
	  }
	} 
	 If ($rows <= 0) {
	  $m[PAGES_NEWS].=$template->show_contxt( "<span class=numpagecur>1</span></b>");
	 }
	
	if($rows <=0) $m['INFO_MESSAGE'] = $template->logmsg2($m['_ERR_NOREC'], 0);
	
	return $template->show_content("/exepanel/customer_list.tpl");
}


//************************************************************************ 
//Вызов функций исходя из запроса
function mygoods() {
	global $template;
	$action = getfromget("method");
	
	switch($action) {
		case "option_fields": {
			$type = getfrompost("type");
			switch ($type) {
				case "add": return option_fields_add(); break;
				case "edit": return option_fields_edit(); break;
				default: return option_fields_view();
			}
		break;
		}
	
		case "request_money": {
			$type = getfrompost("type");
			switch($type) {
				case "change": return request_money_change(); break;
				case "search": return request_money_view(true);
				default: return request_money_view();
			}
		break;
		}
	
		case "customers": {
			$type = getfrompost("type");
			if($type == "search") return customers_view(true);
				else return customers_view(false);
		break;
		}
		
		default: return customers_view(false);
	}
	return $template->show_content("/exepanel/customer_list.tpl");
}

//************************************************************************ 
//Проверка на авторизацию
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_MYGOODS"]." / ".$m["_CP_PARTNERSHIP"];
		$m["CENTERCONTENT"]=mygoods();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
//Вывод шаблона
//Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>