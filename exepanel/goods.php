<?
session_start();
set_time_limit(0);
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
// Инициализация класса Nested Tree (дерево каталога)
$tree = new DBTree($mysql,"category","id");

//************************************************************************ 
// Поля типов товара
// $id_num - Номер товара из secret_goods, передается при изменении для вывода
// дополнительных полей товара
function show_type_goods_form($typeid, $id_num=FALSE) {
	GLOBAL $mysql,$template,$m;
	
	$r=$mysql->query("SELECT * FROM goods_secret_fields WHERE type_ID=$typeid ORDER BY sort");
	if($mysql->num_rows($r)<=0) return false;
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
	$i=0;
	
	// При выборе из secret_goods вывести за одно и значения полей (для редактирования)
	if($id_num) {
		$edit =$mysql->query("SELECT * FROM goods_secret_fields_value WHERE id_num=$id_num");
			while($foredit = $mysql->fetch_array($edit)) {
				$m_edit[$foredit['secret_field_ID']] = decryptdata($foredit['sercet_field_value']);
				$m_edit_crypt[$foredit['secret_field_ID']] = $foredit['sercet_field_value'];
			}
	}
	
	// Максимальный размер файла который загрузится
	$upload_max = (int) ini_get('upload_max_filesize');
    $post_max = (int) ini_get('post_max_size');
    $max_size = ($upload_max < $post_max) ? $upload_max : $post_max;

	while($row = $mysql->fetch_array($r)) {
		// Текстовое поле
		if($row[field_ID]==1) {
			if(empty($m_edit[$row[secret_field_ID]]) && $id_num ) {}
			else {
			$setting_array=unserialize($row[setting_array]);
			$row2 = strip($row);
			
			$values = $m_edit[$row[secret_field_ID]];
					
			if(strlen($setting_array[class_field])>=2) $cl="class=\"{$setting_array[class_field]}\""; else $cl='';
			$tr.=<<<EOF
			<tr>
				<input type="hidden" name="type_good_fieldid_$i" value="$row[field_ID]">
				<input type="hidden" name="type_good_sfid_$i" value="$row[secret_field_ID]">
				<td align="right" class="contdark" width="180"><b>{$row2[$var]}</b>:</td>
				<td align="left" class="contlight"><input type="text" $cl name="type_good_value_$i" size="{$setting_array[size_field]}" value="$values"></td>
			</tr>			
EOF;
		$i++;
		}
		
		}
		// Файл поле
		if($row[field_ID]==2) {
			$setting_array=unserialize($row[setting_array]);
			$row2 = strip($row);
			
			if(empty($m_edit[$row[secret_field_ID]]) && $id_num ) {}
			else {
			//При редактировании вывести значение поля
			if($id_num) {
			
				
				$checkexists = 
				!@file_exists($template->homedir."/secretfiles/".$m_edit_crypt[$row[secret_field_ID]]) 
				? " (<b>".$m[_GOODS_UPDATING_LOADED_FILE_NOTEXISTS]."</b>)"
				: "<br> (size: ".(round(filesize($template->homedir."/secretfiles/".$m_edit_crypt[$row[secret_field_ID]])/1024))." KByte)";				
				$values = $m[_GOODS_UPDATING_LOADED_FILE].": ".$m_edit[$row[secret_field_ID]].$checkexists."<br>";
				strlen($empt) > 5 ? $values="" : "";
			}
			
			if(strlen($setting_array[class_field])>=2) $cl="class=\"{$setting_array[class_field]}\""; else $cl='';
			$tr.=<<<EOF
			<tr>
				<input type="hidden" name="type_good_fieldid_$i" value="$row[field_ID]">
				<input type="hidden" name="type_good_sfid_$i" value="$row[secret_field_ID]">
				<td align="right" class="contdark" width="180"><b>{$row2[$var]} (MAX: $max_size Mbyte)</b>
				<img src="{$m[URLSITE]}/templates/{$m[TEMPLDEF]}/exepanel/img/pic_info.gif" onMouseOut="hide_help('hifile')" onMouseOver="show_help('hifile')">:
				</td>
				<td align="left" class="contlight">$values<input type="file" $cl name="type_good_value_$i" size="{$setting_array[size_field]}"></td>
			</tr>			
EOF;
				$i++;
			}
		
		}
		// Многострочное поле
		if($row[field_ID]==3) {
			$setting_array=unserialize($row[setting_array]);
			$row2 = strip($row);
			if(empty($m_edit[$row[secret_field_ID]]) && $id_num ) {}
			else {
				
			$values = $m_edit[$row[secret_field_ID]];
			if(strlen($setting_array[class_field])>=2) $cl="class=\"{$setting_array[class_field]}\""; else $cl='';
			$tr.=<<<EOF
			<tr>
				<input type="hidden" name="type_good_fieldid_$i" value="$row[field_ID]">
				<input type="hidden" name="type_good_sfid_$i" value="$row[secret_field_ID]">
				<td align="right" class="contdark" width="180"><b>{$row2[$var]}</b>:</td>
				<td align="left" class="contlight"><textarea $cl name="type_good_value_$i" rows="{$setting_array[rows_field]}" cols="{$setting_array[cols_field]}">$values</textarea></td>
			</tr>			
EOF;
		$i++;
			}
		}
	}
	
	return $tr;
}



//************************************************************************ 
// Вывод категорий
// $type_link  0 - при добавлении  1 - при редактировании
// $query - сформированный запрос с категориями 
function show_category($query,$cat,$type_link=0) {
 GLOBAL $mysql,$template,$HTTP_SESSION_VARS;
	$result = $mysql->query($query);
	
	// Проверка на леквидность созданного запроса функцией в классе DBTree -> get_tree_from_id()
	if ($mysql->num_rows($result) == 0) {$template->logtxt("_ERR_NOREC",3); return false; }
	if($HTTP_SESSION_VARS['lang']=="en")$var="name_en"; else $var="name";
	$mas=array();
	
	// Заполнение массива из результата запроса
	while ($row = $mysql->fetch_array($result)) $mas[]=strip($row);
	for($id=0; $id<=count($mas)-1; $id++) {
	
	// Если элемент содержит потомков
		if(isset($mas[$id][nflag]) && $mas[$id][nflag]) {

		// Проверка на открытость или закрытость папки-картинки
			if(!empty($mas[$id+1][cat_level]) && ($mas[$id+1][cat_level]-$mas[$id][cat_level])==1) 
				{	
					$folder="open"; $exp="shrink=".$mas[$id][id]."&"; $node="minus"; 
				} else {
					$folder="closed"; $exp="cat=".$mas[$id][id].""; $node="plus"; 
					}
					
			$lnk = ($type_link == 0 ? "type=add&step=2" : "type=category");
			
			// Формирование категорий в шаблон
			$tt=$template->show_contxt(
			"
			<a href='{URLSITE}/exepanel/goods.php?$lnk&$exp'>
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/node_$node.gif' class='text'></a>
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$folder.gif'> 
			<span class='text'><b>".$mas[$id][$var]."</b></span>
			");
			}
			else{	
					$nav = navigator($mas[$id][id]);
					$slcat = ($type_link == 0 ? "" : ",'".$nav."'");
					if($cat==$mas[$id][id])$doc="doc_sel"; else $doc="doc";
					$tt=$template->show_contxt("
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$doc.gif'>
					<span class='text'>".$mas[$id][$var]." 
					[<a class='none' href=\"javascript:select_cat({$mas[$id][id]}{$slcat})\"><i><b>{_GOODS_CATEGORY_SELECT}</b></i></a>]</span>"
					);
				}

		//Формирование строк в шаблон
		$categ.= "
			<tr>
				<td align='left' class='text'>".str_repeat("&nbsp; &nbsp; &nbsp;",$mas[$id]['cat_level']).$tt."</td>
			</tr>
		";
		}
		return $categ;
}

//************************************************************************ 
// Вывод категорий (запоминание открытых)
// $type_link  0 - при добавлении  1 - при редактировании 
function category($cat=1,$type_link){
	GLOBAL $template,$mysql,$tree,$m;
		// Получение данных на категорию которую следует закрыть
		$shrink= (int) getfromget("shrink");
		$cat=(int)$cat;
		if($cat<=0) $cat=1;
		// Проверка на существование категории
		if(!$mysql->sql_select("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->id."=$cat") || $mysql->row <> 1) $cat=1;
			else 
				// Если выбрано действие "Свернуть", удалить из сессии выбранную категорию
				if($shrink >=1) $tree->catShrinkCategory($shrink, "category_expand"); 
					// Иначе занести в сессию выбранную категорию
					else $tree->catExpandCategory($cat, "category_expand");
		
		return show_category($tree -> get_tree_from_id($cat),$cat,$type_link);
}

//************************************************************************ 
// Создание списка родительских категорий в OPTION тег (ВСЕХ КАТЕГОРИЙ)
// Возвращает сформированный <SELECT>
function create_select_cat_list($selected=FALSE)
{ 	GLOBAL $mysql,$template,$tree;
	
	// Выбор всех категорий
	$result = $mysql->query(
		"SELECT cat.*,
			IF (cat.".$tree->left."+1 < cat.".$tree->right.", 1, 0) as nflag,
			count(gs.good_ID) as kolvo
			FROM ".$tree->table." as cat
			LEFT JOIN goods as gs ON gs.cat_ID = cat.id 
			GROUP BY cat.id
			ORDER BY cat.".$tree->left
		);
	if ($mysql->num_rows($result) == 0) return false;
	
	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
	
	$opt = "<select name='cat'>\n";
	$sc=0;
		while ($row = $mysql->fetch_array($result))
			{	$row=strip($row);
				if($row[nflag]==1 && $sc > 0) $opt .= "</optgroup>\n";
				if($row[nflag]==1) {$opt .= "<optgroup label='".str_repeat("&nbsp; &nbsp;",	$row['cat_level']).$row[$var]."'>\n"; $sc++;}
					else {
						if($selected && $selected==$row[id]) $select="selected"; else $select="";
						$opt .= "<option value='".$row[id]."' $select>".str_repeat("&nbsp;", $row['cat_level']).$row[$var]." ($row[kolvo])\n";
					}
			}
	$opt .= "</optgroup></select>\n";
	return $opt;
}

//************************************************************************ 
// Обновление категорий
function refresh_cat_list() {
	// Подключение AJAX
	$cat = (int)getfromget("cat");
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	$GLOBALS["_RESULT"] = array("view" => create_select_cat_list($cat));
	exit;
}

//************************************************************************ 
// Перевыбор категории при ДОБАВЛЕНИИ ТОВАРА
function category_reselect() {
	GLOBAL $template,$m;
	$cat = getfromget("cat");
	$m[CATEGORY_LIST] = category($cat,1);
	echo $template->show_content("/exepanel/goods_cat_reselect.tpl");
	exit;
}

//************************************************************************ 
// Выводит родительские категории в виде строки
function navigator($cat=1) {
	GLOBAL $mysql,$tree;
	$r=$tree->enumPath2($cat);
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
	while ($row = $mysql->fetch_array($r)) {
        if ($cat <> $row['id']) {
            $navigator .= $row[$var] . " > ";
        } else {
            $navigator .=$row[$var];
        }
    }
	return $navigator;
}

//************************************************************************ 
// Включение/выключение товара
function on_off_good($goodid, $status) {
	GLOBAL $mysql;
	$goodid = (int)$goodid;
	if($status>1) $status=1;
	if($status < 0) $status=0;
	$r=$mysql->query("SELECT * FROM goods WHERE good_ID=".$goodid);
	if($mysql->num_rows($r)==1) {
		$mysql->query("UPDATE goods SET disabled=$status WHERE good_ID=".$goodid);
		return true;
	} else  return false; 
}

//***********************************************************************
// Добавление/удаление из спец.предложений
function special_offer($goodid, $status) {
	GLOBAL $mysql;
	$goodid = (int)$goodid;
	$status = (int)$status;
	if($status>1) $status=1;
	if($status < 0) $status=0;
	$r = $mysql->query("UPDATE goods SET spec_offer=$status WHERE good_ID=$goodid");
}
// На странице со списком товаров
function change_spec_offer() {
	GLOBAL $mysql,$m,$template;
	$goodid = (int) getfromget("good");
	$r = $mysql->query("SELECT spec_offer FROM goods WHERE good_ID=$goodid");
	if($mysql->num_rows($r) == 1) {
		$row = $mysql->fetch_array($r);
		$status = $row["spec_offer"];
		switch($status) {
			case "0": {
				$m_ajax["msg"] = $template->logmsg2($m["_GOODS_SPEC_OFFER_SUCC"], 1);
				special_offer($goodid,1);
			break;
			}
			case "1": {
				$m_ajax["msg"] = $template->logmsg2($m["_GOODS_SPEC_OFFER_DELETE"], 1);
				special_offer($goodid,0);
			break;
			}
		}		
		$m_ajax["succ"] = "yes";
	} else {
		$m_ajax["succ"] = "no";
		$m_ajax["msg"] = $template->logmsg2($m["_ERR_REQUEST"],0);
	}
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
}

//********************************************************
// Удаление из спец. предложений, отдельная страница
function all_spec_offer() {
	GLOBAL $mysql,$m,$template;
	// Язык по умолчанию
	if($_SESSION['lang']=="en")$var="_en"; else $var="";
// Шапка таблицы
$head=<<<EOF
<form name="specoffer" method="POST" onsubmit="return false">
<input type="hidden" name="method" value="del">
<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>	
	<tr>
		<td align="left" class="conttop" width="180">$m[_GOODS_STEP4_NAMEGOOD]</td>
		<td align="center" class="conttop" width="60">$m[_CP_DEL]</td>
	</tr>
EOF;
// Низ таблицы
$foot=<<<EOF
</table>
<p>
<input type="button" value="$m[_CP_DEL]" id="btn" onclick="submitform('$m[_CP_DEL]?')"; class="inputbutton">
</p>
</form>
EOF;
		// Если сабмит формы
		if(getfrompost("method")=="del") {
			$spec = getarray("spec_offer","POST");
			for($i=0;$i<count($spec);$i++) { 
				$spec[$i][id] = (int)$spec[$i][id];
				if((int)$spec[$i][del] == 1)
					$mysql->query("UPDATE goods SET spec_offer=0 WHERE good_ID=".$spec[$i][id]); 
			}
		}
		// Вывод текущих товаров в спец-предложении
		$r = $mysql->query("SELECT good_ID,title,title_en FROM goods WHERE spec_offer=1");
		if($mysql->num_rows($r)>0) {
				$i=0;
				while($row = $mysql->fetch_array($r)) {
					$row = strip($row);
					$ttl = $row['title'.$var];
					$vl  = $row[good_ID];
	$body.="<tr><input type=\"hidden\" name=\"spec_offer_id_$i\" value=\"$vl\">
		<td align=\"left\" class=\"contlight\" width=\"180\">$ttl</td>
		<td align=\"center\" class=\"contlight\" width=\"60\"><input type=\"checkbox\" name=\"spec_offer_del_$i\" value=\"1\"></td>
	</tr>";		

				$i++;
				}
				$m[SPEC_OFFER_LIST] = $head.$body.$foot;

		} else $m[SPEC_OFFER_LIST] = $template->logmsg2($m[_SPEC_OFFER_EMPTY],0);


	return $template->show_content("/exepanel/goods_specoffer.tpl");
}

//************************************************************************ 
// Листинг возможных типов товаров, включен кроме OFFLINE типа 
function type_good_list($typeid=0,$enable=TRUE)
{ 	GLOBAL $mysql,$template,$tree;
	
	if($enable) {
		// Выбор всех категорий
		$result = $mysql->query("SELECT * FROM type_goods");
		if ($mysql->num_rows($result) == 0) return false;
	}
		//Язык на котором следует вывести названия
		if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
		
		if(!$enable)$e = "disabled";
			$opt = "<select name='type_good' $e>";
			while ($row = $mysql->fetch_array($result))
				{	$row=strip($row);
					if($typeid==$row[type_ID])$select="selected"; else $select="";
					$opt.= "<option value='".$row[type_ID]."' $select>".$row[$var];
				}
			$opt .= "</select>";
		if($enable) return $opt; else return "<select name='type_good' $e></select>";
	
}

//************************************************************************ 
// Добавление текстовый СКРЫТЫХ полей - работает при закачке товара
// $typeid - тип товара
// $id - номер в goods_secret 
// $sfid - номер поля в goods_secret_field
// $value - секретный текст :)
// $update_flag - флаг для определения запроса на добавление либо на обновление
function insert_text($typeid, $id, $sfid, $value, $update_flag = FALSE) {
	GLOBAL $mysql,$template;
	$r = $mysql->query("
		SELECT * 
		FROM goods_secret_fields 
		WHERE type_ID=$typeid AND secret_field_ID=$sfid");
		
	if($mysql->num_rows($r)==1) {
		$row = $mysql->fetch_array($r);
		$crypt_value = encryptdata($value);
		if(!$update_flag)
			$r = $mysql->query("
				INSERT INTO goods_secret_fields_value 
				VALUES('$sfid','$id','$typeid','$crypt_value')
				");
			 else {
				// Удаление файла перед обновлением (если тип переданного поля  - ФАЙЛ)
				if($row[field_ID] == 2) {
					if($value <> "empty") {
					
						$r = $mysql->query("
							SELECT * 
							FROM goods_secret_fields_value 
							WHERE id_type=$typeid AND id_num=$id AND secret_field_ID=$sfid
							");
						$todel = $mysql->fetch_array($r);
						@unlink($template->homedir."/secretfiles/".$todel['sercet_field_value']);
						
						$r = $mysql->query("
							UPDATE goods_secret_fields_value
							SET	sercet_field_value = '$crypt_value'
							WHERE id_type=$typeid AND id_num=$id AND secret_field_ID=$sfid
							LIMIT 1
						");
					}
				} else
				$r = $mysql->query("
					UPDATE goods_secret_fields_value
					SET	sercet_field_value = '$crypt_value'
					WHERE id_type=$typeid AND id_num=$id AND secret_field_ID=$sfid
					LIMIT 1
				");
			}
		return TRUE;
	} else return FALSE;
}

//************************************************************************ 
// Проверка на допустимое расширение файла
// TRUE - есть в допустимых, FALSE нет
// $filename - имя файла
function check_ext($filename) {
	// Допустимые добавляемые расширения
	$ext = "jpg|jpeg|gif|png|psd|exe|rar|zip|uha|mp3|avi|mpg|wmv|dat|chm|txt|csv";
	$ext = explode("|", $ext);
	$d = explode(".", tolower($filename));
	if(in_array($d[sizeof($d)-1],$ext)) return true; else return false;
}

//************************************************************************ 
// Добавление файлов - при закачке товара
// $typeid - тип товара
// $sfid - номер поля в goods_secret_field (для сверки поля на тип ФАЙЛ)
// $file - переменная ФАЙЛ
// $id - номер товара ПЕРЕДАЕТСЯ ПРИ РЕДАКТИРОВАНИИ
// $update_flag - флаг для определения запроса на добавление либо на обновление
// Возвращает имя файла, которое в последуещем нужно зашифровать и в базу
function insert_file($typeid, $sfid, $file, $id=FALSE, $update_flag = FALSE) {
	GLOBAL $mysql,$template;
	
	// Максимальный размер файла который загрузится
	$upload_max = (int) ini_get('upload_max_filesize');
    $post_max = (int) ini_get('post_max_size');
    $max_size = ($upload_max < $post_max) ? $upload_max : $post_max;
	
	if(@filesize($file["tmp_name"]) > $max_size*1024*1024) return false;
	
	$file['name'] = str_replace(array(" ", "-", ":"), array('_','_','_'), $file[name]);
	// Формирование имени и пути
	$flname=mt_rand(1,99999).toLower($file['name']);
	$filename = encryptdata($flname);
	$lnk = $template->homedir."/secretfiles/$filename";
	
	
	
	//***При обновление - проверка на длину имя файла, если пусто, то обновлять нет необходимости
	if($update_flag == true) {
		if(strlen($file['name']) <4 ) return "empty";
		// Проверка на существования значения поля в goods_secret_fields_value по номеру товара
		$r = $mysql->query("
			SELECT * FROM goods_secret_fields_value 
			WHERE id_num=$id AND id_type=$typeid AND secret_field_ID=$sfid
			");
		if($mysql->num_rows($r)==0) return "empty";
	}
	
	
	// Проверка на существование ТИПА поля
	$r = $mysql->query("
			SELECT * FROM goods_secret_fields 
			WHERE field_ID=2 AND type_ID=$typeid AND secret_field_ID=$sfid
			");	
	
	if(check_ext($file['name']) && strlen($file['name']) >4) {
		if($mysql->num_rows($r)==1) {
			if(move_uploaded_file($file['tmp_name'], $lnk)){		
				$f = @fopen($lnk, "r");
				$content = fread($f, filesize($lnk));
				@fclose($f);
				$f = @fopen($lnk, "w");
				$content = encryptdata($content, false);
				@fwrite($f, $content);
				@fclose($f);
				return $flname;
			} else return FALSE;
		} else return FALSE;
	} else return FALSE;
	
}

//************************************************************************ 
// Изменение "Включение в продажу",  "Цена"
function change_onoff_price() {
	GLOBAL $mysql,$template,$m;
	$what = getarray("goods_change","POST");
	
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	
	$mem_err = '';
	
	for($i=0;$i<count($what); $i++) {
		$err="";
		$what[$i]["id"] = (int) $what[$i]["id"];
		
		if($what[$i]["id"] > 0) {
	
			$r = $mysql->query("SELECT * FROM goods WHERE good_ID=".$what[$i]["id"]);
			$row = strip($mysql->fetch_array($r));

			//Включатель, если передано 1 - то включить продажу, если 0 то выключить продажу
			$what[$i]["onoff"] = (int)$what[$i]["onoff"];
			if($what[$i]["onoff"] > 1 || $what[$i]["onoff"] == 1) $onoff=0;
			if($what[$i]["onoff"] <> 1) $onoff=1;
			
			// Цена
			$what[$i]["price"] = (double)str_replace(",",".",$what[$i]['price']);
			if($what[$i]["price"] <= 0) $err = $m["_GOODS_STEP3_PRICE_ERROR"]." #".$what[$i]["id"]."<br>";
			
			// Скидка и агентские
			$skidka_good = (int)$row["rate_skidka"];
			$agent_good = (int)$row["rate_agent"];
			$skidka=$what[$i]["price"]*$skidka_good/100;
			$agent=($what[$i]["price"]-$skidka)*$agent_good/100;
			if(($what[$i]["price"]-$skidka-$agent) < 0.01) 
				$err .=$m["_GOODS_STEP3_SUMM_ERROR"]." #".$what[$i]["id"]."<br>";
				
			$mem_err .=$err;
			
			if(strlen($err) < 4) {
				$mysql->query("
				UPDATE goods 
				SET price='".$what[$i]['price']."', disabled='$onoff' 
				WHERE good_ID=".$what[$i]["id"]);
			}
		}
	}
	
	// Вывод ошибок
	if(strlen($mem_err) > 4) $GLOBALS["_RESULT"] = array("msg" => $template->logmsg2($mem_err,0), "succ" => "no");
		else $GLOBALS["_RESULT"] = array("msg" => $template->logmsg2($m["_SUCC_CHANGE"],1), "succ" => "yes");
		
	// Если передана пустая форма
	if(count($what) == 0) $GLOBALS["_RESULT"] = array("msg" => $template->logmsg2($m["_ERR_REQUEST"],0), "succ" => "no");
	
	
	exit;
}

//************************************************************************
// Возвращает по ID название свойства товара
function return_prop_good($id) {
	GLOBAL $m;
	switch($id) { 
		case "0": return $m["_GOODS_STEP1_PROP0"];
		case "1": return $m["_GOODS_STEP1_PROP1"];
		case "2": return $m["_GOODS_STEP1_PROP2"];
	}
}

//************************************************************************ 
// Возвращает статус ТОВАРА
/*  0 - не продан, 1 - продан, - 2 - в процессе покупки */
function return_secret_goods_status($status) {
	GLOBAL $m;
	switch($status) {
		case "0": return $m["_GOODS_STATUS0"];
		case "1": return $m["_GOODS_STATUS1"];
		case "2": return $m["_GOODS_STATUS2"];
	}
}

//************************************************************************ 
// Добавление товара
function add_goods() {
	GLOBAL $mysql,$template,$m;
	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="_en"; else $var="";
	
	$step_post = (int)getfrompost("step");
	$step_get = (int)getfromget("step");
	
	// Сохранение данных
	switch($step_post) {
		
		// Свойство товара
		case "1": {
			$prop_good = (int)getfrompost("prop_good");
			if($prop_good == 0 || $prop_good == 1 || $prop_good == 2)
				$_SESSION['good']['prop_good']=$prop_good;
				else { 
					$template->logtxt("_GOODS_SPET1_PROP_EMPTY", 0); 
					$template->show_content("/exepanel/goods_add_step1.tpl");
					return false;
				}
		break;
		}
		
		// Категория товара
		case "2": {
			$cat_good = (int)getfrompost("cat_good");
			if($cat_good > 0) $_SESSION['good']['cat_good'] = $cat_good;
				else {
					$template->logtxt("_GOODS_STEP2_EMPTY", 0); 
					$step_get = 2;
				}
		break;
		}
		
		// Добавление товара и присвоение ID товару
		case "3": {
			
			// Тип товара
			if($_SESSION['good']['prop_good'] <> 2) {
				$_SESSION['good']['type_good'] = (int) getfrompost("type_good");
				if((int) getfrompost("type_good") <= 0 || 
					!$mysql->sql_select("SELECT * FROM type_goods WHERE type_ID=".$_SESSION['good']['type_good']) || 
						$mysql->row == 0) {
					$err = $template->show_contxt("{_GOODS_STEP3_TYPE_GOOD_ERROR}<br>");	
				}
			} else $_SESSION['good']['type_good'] = 0;
			
			// Категория товара
			$_SESSION['good']['cat_good'] = (int) getfrompost("cat_good");
			if((int) getfrompost("cat_good") <=0 ||
				!$mysql->sql_select("SELECT * FROM category WHERE id=".$_SESSION['good']['cat_good']) ||
					$mysql->row == 0) {
				$err .= $template->show_contxt("{_GOODS_STEP3_CATEGORY_ERROR}<br>");	
			}
			
			// Артикул
			$_SESSION['good']['articul_good'] = getfrompost("articul_good");
			
			// Цена
			$_SESSION['good']['price_good'] = (double)str_replace(",",".",getfrompost("price_good"));
			if($_SESSION['good']['price_good'] <= 0) $err .= $template->show_contxt("{_GOODS_STEP3_PRICE_ERROR}<br>");
			
			// Скидка и агентские
			$_SESSION['good']['skidka_good'] = abs((int) getfrompost("skidka_good"));
			$_SESSION['good']['agent_good'] = abs((int) getfrompost("agent_good"));
			$skidka=$_SESSION['good']['price_good']*$_SESSION['good']['skidka_good']/100;
			$agent=($_SESSION['good']['price_good']-$skidka)*$_SESSION['good']['agent_good']/100;
			if(($_SESSION['good']['price_good']-$skidka-$agent) < 0.01) 
				$err .=$template->show_contxt("{_GOODS_STEP3_SUMM_ERROR}<br>");
				
			// Склад 
			if($_SESSION['good']['prop_good'] == 2) {
				$_SESSION['good']['sklad_good'] = (int) getfrompost("sklad_good");
				if((int) getfrompost("sklad_good") <0) $_SESSION['good']['sklad_good']=0;
			} else $_SESSION['good']['sklad_good'] =0;
			
			// Названия товара
			$_SESSION['good']['titleru_good'] = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("titleru_good")));
			$_SESSION['good']['titleen_good'] = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("titleen_good")));
			if(strlen(getfrompost("titleru_good")) < 1 && strlen(getfrompost("titleen_good")) < 1) 
				$err .=$template->show_contxt("{_GOODS_STEP3_TITLE_ERROR}<br>");
				
			// Описание товара
			$_SESSION['good']['descrru_good'] = str_replace(array('\"', "\'"), array("'","'"), nl2br(strip_tags(getfrompost("descrru_good"))));
			$_SESSION['good']['descren_good'] = str_replace(array('\"', "\'"), array("'","'"), nl2br(strip_tags(getfrompost("descren_good"))));
			if(strlen(getfrompost("descrru_good")) < 1 && strlen(getfrompost("descren_good")) < 1) 
				$err .=$template->show_contxt("{_GOODS_STEP3_DESCR_ERROR}<br>");
				
			// Дополнительное описание товара
			$_SESSION['good']['addtitionalru_good'] = str_replace(array('\"', "\'"), array("'","'"), nl2br(strip_tags(getfrompost("addtitionalru_good"))));
			$_SESSION['good']['addtitionalen_good'] = str_replace(array('\"', "\'"), array("'","'"), nl2br(strip_tags(getfrompost("addtitionalen_good"))));
			
			// Meta
			$_SESSION['good']['meta_desc_good'] = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("meta_desc_good")));
			$_SESSION['good']['meta_key_good'] = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("meta_key_good")));
			
			if(strlen($err) < 3) {
				$dateupload = date("Y-m-d H:i:s");
				$r = $mysql->query("INSERT INTO goods VALUES('', 
				'".$_SESSION['good']['cat_good']."',
				'".$_SESSION['good']['type_good']."',
				'".addslashes($_SESSION['good']['articul_good'])."',
				'".addslashes($_SESSION['good']['titleru_good'])."',
				'".addslashes($_SESSION['good']['titleen_good'])."',
				'".addslashes($_SESSION['good']['descrru_good'])."',
				'".addslashes($_SESSION['good']['descren_good'])."',
				'".addslashes($_SESSION['good']['addtitionalru_good'])."',
				'".addslashes($_SESSION['good']['addtitionalen_good'])."',
				'".addslashes($_SESSION['good']['meta_desc_good'])."',
				'".addslashes($_SESSION['good']['meta_key_good'])."',
				'".$_SESSION['good']['price_good']."',
				'".$dateupload."',
				'0',
				'".$_SESSION['good']['skidka_good']."',
				'".$_SESSION['good']['agent_good']."',
				'".$_SESSION['good']['prop_good']."',
				'".$_SESSION['good']['sklad_good']."',
				'0',
				'1'
				)");
				if($good_id = $mysql->insert_id()) {
					unset($_SESSION['good']);
					$_SESSION['good_id'] = $good_id;
				} else $step_get = 3;
			} else {
				$template->logmsg($err, 0);
				$step_get = 3;
			}
		break;
		}
		
		//Завершающий этап
		case "4": {
			// Если нажата кнопка завершить
			if(getfrompost("finish")=="yes") {
				
				// вкл.выкл. продажи товара
				$status=(int) getfrompost("check_onoff_good");
				if($status >= 1) on_off_good($_SESSION['good_id'], 0);
					else on_off_good($_SESSION['good_id'], 1);
					
				unset($_SESSION['good_id']);
				return view_goods();
			}
			// Загрузка/до загрузка товара
			if(getfrompost("nextadd")=="yes") {
				
				// Подключение класса AJAX
				$JsHttpRequest =& new JsHttpRequest("windows-1251");
				
				$r = $mysql->query("SELECT * FROM goods WHERE good_ID=".$_SESSION['good_id']);
					if($mysql->num_rows($r) == 1) {
						$row = strip($mysql->fetch_array($r));
						
						//  Подсчет кол-ва товаров добавленных
						$r2 = $mysql->query("SELECT * FROM goods_secret WHERE id_good=".$_SESSION['good_id']);
						$kolvo = $mysql->num_rows($r2);
						
						// Добавление
						if(($row[prop_good]==1 && $kolvo==0) || $row[prop_good]==0) {
						
							// Выборка переданных данных
							$tovar = getarray("type_good" ,"POST");
							$errsum = 0;
							 
								
								// Проверка на входные данные
								for($i=0;$i<count($tovar); $i++) {
								$tovar[$i][value] = strip_tags($tovar[$i][value]);
								switch($tovar[$i][fieldid]) {
										// text
										case "1": {
											if(empty($tovar[$i][value])) {
												$errsum ++;
												$err_ajax .='Text field ERROR<br>';
											}
										break;
										}
										// file
										case "2": {
											if(!$flname = insert_file($row[type_ID], (int)$tovar[$i][sfid], $_FILES['type_good_value_'.$i])) {
												$errsum ++;
												$err_ajax .='File '.$_FILES['type_good_value_'.$i][name].' not upload<br>';
											}
										break;
										}
										// textarea
										case "3": {
											if(empty($tovar[$i][value])) {
												$errsum ++;
												$err_ajax .='Textarea field ERROR<br>';
											}
										break;
										}
									}
								}
								
								// Добавление после проверки
								if($errsum == 0 && count($tovar)>0) {
									if($req = $mysql->query("INSERT INTO goods_secret VALUES('', '".$_SESSION['good_id']."', '".$row[type_ID]."', '', '0')") && $id = $mysql->insert_id()) {
										for($i=0;$i<count($tovar); $i++) 
										switch($tovar[$i][fieldid]) {
											case "1": {
												if(!insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value])))
													$err_ajax .='Text field ERROR (insert)<br>';
											break;
											}
											case "2": {
												if(!$flname || !insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], $flname)) 
													$err_ajax .='File '.$_FILES['type_good_value_'.$i][name].' not upload (insert)<br>';
											break;
											}
											case "3": {
												if(!insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value])))
													$err_ajax .='Textarea field ERROR (insert)<br>';
											break;
											}
										}
									} else $err_ajax .=$m[_ERR_REQUEST].'<br>';
								} else $err_ajax =$m[_ERR_NOFULLCHANGE].'<br>'.$err_ajax;
								
							} else $err_ajax .= $m[_GOODS_STEP4_ERR_TYPE].'<br>';
								
						} else $err_ajax .=$m[_ERR_REQUEST].'<br>';
										
				
				// Вывод ошибки
				if(strlen(trim($err_ajax))>1) {
					$m_ajax["msg"]=$template->logmsg2($err_ajax, 0); 
					$m_ajax["kolvo"] = $kolvo;
					
					// Удаление при неуспешной закачке
					if(strlen($flname)>1) @unlink($template->homedir."/secretfiles/".encryptdata($flname));
				}
					else { 
						// Вывод в случае успеха
						$m_ajax["msg"] = $template->logmsg2($m[_SUCC_ADD], 1);
						$m_ajax["kolvo"] = $kolvo+1; 
					}
					
				// Видимость формы загрузки
				if($row[prop_good]==1 && $m_ajax["kolvo"] > 0) $m_ajax["visibility"] = "hidden"; else $m_ajax["visibility"] = "visible";
				
				// Для AJAX
				$GLOBALS['_RESULT'] = $m_ajax;
				exit;
			}
		break;
		}
	}
	/************************************************************************/
	// Вывод из сессий
	switch($step_get) {
	
		// Свойство товара
		case "1": {
			$m[CHECK_PROP_GOOD0] = $_SESSION['good']['prop_good'] == 0 ? "checked":"";
			$m[CHECK_PROP_GOOD1] = $_SESSION['good']['prop_good'] == 1 ? "checked":"";
			$m[CHECK_PROP_GOOD2] = $_SESSION['good']['prop_good'] == 2 ? "checked":"";
			return $template->show_content("/exepanel/goods_add_step1.tpl");
			break;
		}
		
		// Категория товара
		case "2": {
			$cat = (int)($_SESSION['good']['cat_good'] > 0 && (int)getfromget("cat")<=0 ? $cat=$_SESSION['good']['cat_good']: getfromget("cat"));
			$m[CATEGORY_LIST] = category($cat,0);
			return $template->show_content("/exepanel/goods_add_step2.tpl");
		break;
		}
		
		// Характеристика товара
		case "3": {
		
			// Типы товаров
			$m[TYPE_GOOD] = $_SESSION['good']['prop_good'] <> 2 ? type_good_list($_SESSION['data']['type_good']):type_good_list($_SESSION['data']['type_good'],false);
			if(!$m[TYPE_GOOD]) {$template->logtxt("_GOODS_STEP3_TYPE_GOOD_EMPTY", 0); return $template->show_content("/exepanel/goods_add_step1.tpl");}
				
			// Категории
			$m[CATEGORY_GOOD] = navigator($_SESSION['good']['cat_good']);
			$m[CAT_GOOD] = $_SESSION['good']['cat_good'];
			
			$m[ARCTICUL_GOOD] = $_SESSION['good']['articul_good'];
			$m[PRICE_GOOD] = (double)str_replace(",",".",$_SESSION['good']['price_good']);
			$m[SKIDKA_GOOD] = (int)$_SESSION['good']['skidka_good'];
			$m[AGENT_GOOD] = (int)$_SESSION['good']['agent_good'];
			
			// Включение/выкл. склада
			$m[SKLAD_GOOD] = (int) $_SESSION['good']['sklad_good'];
			$m[SKLAD_GOOD_CHECK] = $_SESSION['good']['prop_good'] <> 2 ? "disabled":"";
			$m[SKLAD_GOOD_CLASS] = $_SESSION['good']['prop_good'] <> 2 ? "inputdisabled": "inputcenter";
			
			// Название товара
			$m[TITLERU_GOOD] = strip_tags($_SESSION['good']['titleru_good']);
			$m[TITLEEN_GOOD] = strip_tags($_SESSION['good']['titleen_good']);
			
			// Описание товара
			$m[DESCRRU_GOOD] = strip_tags($_SESSION['good']['descrru_good']);
			$m[DESCREN_GOOD] = strip_tags($_SESSION['good']['descren_good']);
			
			// Дополнительное описание
			$m[ADDITIONALRU_GOOD] = strip_tags($_SESSION['good']['addtitionalru_good']);
			$m[ADDITIONALEN_GOOD] = strip_tags($_SESSION['good']['addtitionalen_good']);
			
			// Мета тэги
			$m[META_DESC_GOOD] = strip_tags($_SESSION['good']['meta_desc_good']);
			$m[META_KEY_GOOD] = strip_tags($_SESSION['good']['meta_key_good']);
			
			return $template->show_content("/exepanel/goods_add_step3.tpl");
		}
		
		// Пополнение, завершающий этап
		case "4": {
			$r = $mysql->query("SELECT * FROM goods WHERE good_ID=".$_SESSION['good_id']);
			$r2 = $mysql->query("SELECT * FROM goods_secret WHERE id_good=".$_SESSION['good_id']);
				
			if($mysql->num_rows($r) == 1) {
				$row = strip($mysql->fetch_array($r));
				
				$m[PROP_GOOD] = return_prop_good($row[prop_good]);
				
				if($row[prop_good]==0 || $row[prop_good]==1) $m[COUNT_GOOD] = $mysql->num_rows($r2); else $m[COUNT_GOOD]="-";
				
				$m[ID_GOOD] = $row[good_ID];
				$m[NAME_GOOD] = $row['title'.$var];
				$m[PRICE_GOOD] = $row[price];
				
				if(($row[prop_good]==1 && $m[COUNT_GOOD]==0) || $row[prop_good]==0) {
				$m[TABLE_TYPE_GOOD] = show_type_goods_form($row[type_ID]);
				$m[ADD_UPDATE] = $template->show_content("/exepanel/goods_add_update.tpl");
				}
				return $template->show_content("/exepanel/goods_add_step4.tpl");
			}
			else {
				$template->logtxt("_GOODS_STEP4_EMPTY", 0); 
				return $template->show_content("/exepanel/goods_add_step1.tpl");}
		}
		
		default: {
			unset($_SESSION['good']); unset($_SESSION['category_expand']);
			return $template->show_content("/exepanel/goods_add_step1.tpl");
		}
	}
}

//************************************************************************ 
// Подсчет кол-ва товаров в СЕКРЕТ таблице
function return_count_secret_good($id) {
	GLOBAL $mysql;
	$r = $mysql->query("SELECT id_num FROM goods_secret WHERE id_good=$id");
	return $mysql->num_rows($r);
}

//************************************************************************ 
// Вывод списк товаров из таблицы SECRET_GOODS (МИНИ СПИСОК)
function return_listing_goods() {
	GLOBAL $mysql,$template,$m;
	
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	
	$goodid = (int) getfromget("goodid");
	$lnkk=$m[URLSITE].'/templates/'.$m[TEMPLDEF];
	// Шапка таблицы
	$head =<<<EOF
	<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
		<tr>
			<td align="center" class="conttop" width="129">$m[_GOODS_UPDATING_ID]</td>
			<td align="left" class="conttop" width="150">$m[_GOODS_UPDATING_PAYMENT_STATUS]</td>
			<td align="center" class="conttop" width="80">$m[_NEWS_ACTION]</td>
		</tr>
EOF;
	$foot =<<<EOF
	</table>
EOF;
	$prop_good = $mysql->query("SELECT prop_good FROM goods WHERE good_ID=$goodid LIMIT 1");
	$prop_good = $mysql->fetch_array($prop_good);
	
	$good = $mysql->query("
		SELECT id_num, status
		FROM goods_secret
		WHERE id_good=$goodid 
		ORDER BY id_num DESC
	");
	
	if($mysql->num_rows($good) > 0) {
		$col=2;
		while($row = $mysql->fetch_array($good)) {
			// Чередование стиля
			if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
			// Статус товара (продан, не продан, в процессе покупки)
			$status = $prop_good[prop_good] <> 1 ? return_secret_goods_status($row[status]) : $m[_GOODS_STATUS_NOT_FIX];
			$body .=<<<EOF
			<tr>
				<td align=center class="$clas" width="120">$row[id_num]</td>
				<td align="left" class="$clas" width="150">$status</td>
				<td align="center" class="$clas" width="80">
				<img onClick="updating(5, $row[id_num], '', $goodid, false);" style="cursor:pointer;" src="$lnkk/exepanel/img/edit.gif" title="{$m['_CP_EDIT']}">
				&nbsp;&nbsp;&nbsp;
				<img onClick="del_box($row[id_num], 'idtovid', 'delblock')" style="cursor:pointer;" src="$lnkk/exepanel/img/delete.gif" title="{$m['_CP_DEL']}">
				</td>
			</tr>
EOF;
		$col++;
		}
		
		$m_ajax["stat"] = "loading";
		$m_ajax["listing"] = $head.$body.$foot;
		
	} else {
		// В случае если загруженных товаров нет
		$m_ajax["stat"] = "notfound";
		$m_ajax["msg"]  = $template->logmsg2($m[_GOODS_UPDATING_EMPTY], 0);
	}
	
	$m_ajax["goodid"] = $goodid;
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
}

//************************************************************************ 
// Ключ для редактирования Запоминание в сессию
// $showform - если TRUE то вывод формы либо сообщения успешного сохранения
function mem_key($showform = FALSE) {
GLOBAL $m;
if(!$showform) {
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	$_SESSION['key_edit_good'] = getfromget("key");
	if(validate_key(getfromget("key")))
		$GLOBALS['_RESULT'] = array("key_flag" =>true);
			else $GLOBALS['_RESULT'] = array("key_flag" =>false);
	exit;
} else {
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
}

//************************************************************************
// Возвращает кнопку добавленя/пополнения
function return_btn_updating_good() {
	GLOBAL $m;
	return $BTN=<<<EOF
<input class="inputbutton" type="button" onclick="update_box('addblock')" value="$m[_CP_ADD]">
EOF;
}
//************************************************************************ 
// Пополнение товара из пункта меню товара "Пополнене"
function updating_good() {
	GLOBAL $template,$mysql,$m;
	
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	//Язык по умолчанию
	if($_SESSION['lang']=="en")$var="_en"; else $var="";
	
	$goodid = (int)getfromget("goodid");
	$method = getfromget("method");
	$nextadd = getfrompost("nextadd");
	
	$good = $mysql->query("SELECT gs.*, tg.name as tpname, tg.name_en as tpname_en 
						FROM goods as gs, type_goods as tg 
						WHERE gs.good_ID=$goodid AND tg.type_ID=gs.type_ID LIMIT 1");
						
	if($mysql->num_rows($good) == 1) {
		$good=strip($mysql->fetch_array($good));
		if($good["prop_good"]<>2) {
			// Отображение инфо о товаре
			if($method == "select") {
				$m["ID_GOOD"] = $good["good_ID"];
				$m["PROP_GOOD"] = return_prop_good($good["prop_good"]);
				$m["TYPE_GOOD"] = $good["tpname".$var];
				$m["NAME_GOOD"] = $good["title".$var];
				$m["PRICE_GOOD"]= $good["price"];
				$m["CAT_GOOD"]  = $good["cat_ID"];
				$m["COUNT_GOOD"]= $good["prop_good"] <> 2 ? return_count_secret_good($goodid) : $good["sklad"];
				$m["BTN_UPDATE_GOOD"] = $good["prop_good"] == 0 ? return_btn_updating_good() : $m["COUNT_GOOD"] == 0 ? return_btn_updating_good():"";
				
				$m["TABLE_TYPE_GOOD"] = show_type_goods_form($good['type_ID']);
				
				// Отображение формы для ввода ключа,  если ключ валидный, выводиться сообщение
				$m["MEM_KEY"]   = mem_key(TRUE);
				
				$m_ajax['stat'] = "updateform";
				// Инфо-форма
				$m_ajax['form'] = $template->show_content("/exepanel/goods_updating_form.tpl");					
			}
			
			/**************** Добавление - пополнение**************************/
			if($method == "update" && $nextadd == "yes") {
			
				$r = $mysql->query("SELECT * FROM goods WHERE good_ID=".$goodid);
				$row = strip($mysql->fetch_array($r));
						
				//  Подсчет кол-ва товаров добавленных
				$r2 = $mysql->query("SELECT * FROM goods_secret WHERE id_good=".$goodid);
				$kolvo = $mysql->num_rows($r2);
						
				// Добавление
				if(($row[prop_good]==1 && $kolvo==0) || $row[prop_good]==0) {
						
					// Выборка переданных данных
					$tovar = getarray("type_good" ,"POST");
					$errsum = 0;
							 
								
					// Проверка на входные данные
					for($i=0;$i<count($tovar); $i++) {
						$tovar[$i][value] = strip_tags($tovar[$i][value]);
						switch($tovar[$i][fieldid]) {
							// text
							case "1": {
								if(empty($tovar[$i][value])) {
									$errsum ++;
									$err_ajax .='Text field ERROR<br>';
								}
							break;
							}
							// file
							case "2": {
								if(!$flname = insert_file($row[type_ID], (int)$tovar[$i][sfid], $_FILES['type_good_value_'.$i])) {
									$errsum ++;
									$err_ajax .='File '.$_FILES['type_good_value_'.$i][name].' not upload<br>';
								}
							break;
							}
							// textarea
							case "3": {
								if(empty($tovar[$i][value])) {
									$errsum ++;
									$err_ajax .='Textarea field ERROR<br>';
								}
							break;
							}
							
						}
					}
								
						// Добавление после проверки
						if($errsum == 0 && count($tovar)>0) {
							if($req = $mysql->query("INSERT INTO goods_secret VALUES('', '".$goodid."', '".$row[type_ID]."', '', '0')") && $id = $mysql->insert_id()) {
								for($i=0;$i<count($tovar); $i++) 
									switch($tovar[$i][fieldid]) {
										case "1": {
											if(!insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value])))
												$err_ajax .='Text field ERROR (insert)<br>';
										break;
										}
										case "2": {
											if(!$flname || !insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], $flname)) 
												$err_ajax .='File '.$_FILES['type_good_value_'.$i][name].' not upload (insert)<br>';
										break;
										}
										case "3": {
											if(!insert_text($row[type_ID], $id, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value])))
												$err_ajax .='Textarea field ERROR (insert)<br>';
										break;
										}
									}
								} else $err_ajax .=$m[_ERR_REQUEST].'<br>';
						} else $err_ajax =$m[_ERR_NOFULLCHANGE].'<br>'.$err_ajax;		
					} else $err_ajax .= $m[_GOODS_STEP4_ERR_TYPE].'<br>';
								
										
				
				// Вывод ошибки
				if(strlen(trim($err_ajax))>1) {
					$m_ajax["msg"]=$template->logmsg2($err_ajax, 0); 
					$m_ajax["kolvo"] = $kolvo;
					$m_ajax["stat"] = 'errorupload';
					
					// Удаление при неуспешной закачке
					if(strlen($flname)>1) @unlink($template->homedir."/secretfiles/".encryptdata($flname));
				}
					else { 
						// Вывод в случае успеха
						$m_ajax["msg"] = $template->logmsg2($m[_SUCC_ADD], 1);
						$m_ajax["kolvo"] = $kolvo+1; 
						$m_ajax["stat"] = "success";
					}
					
				// Видимость кнопки загрузки
				if($row[prop_good]==1 && $m_ajax["kolvo"] > 0) $m_ajax["btnupdating"] = ""; else $m_ajax["btnupdating"] = return_btn_updating_good();
				
			
			} /*************************** КОНЕЦ ПОПОЛНЕНИЯ**********************/
			
			//Редактирование подтовара (отображение формы)
			if($method == "edit") {
				if(validate_key($_SESSION['key_edit_good'])) {
					$m_ajax["key_flag"] = true;
					
					// Выполняется если не нажата кнопка изменить
					if(getfromget("submethod") <> "update") {
						$subid = (int) getfromget("subid");
						$r = $mysql->query("SELECT * FROM goods_secret WHERE id_good=$goodid AND id_num=$subid LIMIT 1");
						if($mysql->num_rows($r) == 1) {
							$row = $mysql->fetch_array($r);
							$tbl = show_type_goods_form($row['id_type'], $subid);
							$m_ajax['form'] =<<<EOF
		<div id="formupdate" style="border:0px;">
			<span class="text"><b>$m[_GOODS_UPDATING_EDIT] ID $subid</span>
			<br><br>
			<form id="change_edit"  method="POST" enctype="multipart/form-data" onsubmit="return false" >
			<input type="hidden" name="update" value="yes">
			<table align='center' border="1" width="80%" class='dash' cellpadding='5' cellspacing='0'>
				$tbl
			</table>
			<p>
			<input type="button" value="$m[_INST_BACK]" onClick="updating(2, $goodid, '', '', false);" class="inputbutton">
			<input type="button" name="btn" value="$m[_CP_CHANGE]" onClick="updating(6, $subid, document.getElementById('change_edit'), $goodid, true)" class="inputbutton">
			</p>
			</form>
		</div>
EOF;
							$m_ajax["stat"] = "success";	
					
						} else {
							$m_ajax['error'] = $template->logmsg2($m["_GOOD_NOTFOUND"],0);
							$m_ajax["stat"] = "notfound";
						}
					}
					
			//*************************ОБНОВЛЕНИЕ-ИЗМЕНЕНИЕ*****************************//
			// Непосредственно само обновление
			if(getfromget('submethod') == "update" && getfrompost("update") == "yes") {
				
				$subid = (int) getfromget("subid");
				$valid_subid = $mysql->query("SELECT id_num FROM goods_secret WHERE id_num=$subid");
				if($mysql->num_rows($valid_subid)==1) {
				$r = $mysql->query("SELECT * FROM goods WHERE good_ID=".$goodid);
				$row = strip($mysql->fetch_array($r));
										
						
				// Выборка переданных данных
				$tovar = getarray("type_good" ,"POST");
				$errsum = 0;
								 
									
				// Проверка на входные данные
				for($i=0;$i<count($tovar); $i++) {
					$tovar[$i][value] = strip_tags($tovar[$i][value]);
					switch($tovar[$i][fieldid]) {
						// text
						case "1": {
							if(empty($tovar[$i][value])) {
								$errsum ++;
								$err_ajax .='Text field ERROR<br>';
							}
						break;
						}
						// file
						case "2": {
							if(!$flname = insert_file($row[type_ID], (int)$tovar[$i][sfid], $_FILES['type_good_value_'.$i], $subid, true)) {
								$errsum ++;
								$err_ajax .='File '.$_FILES['type_good_value_'.$i][name].' not upload<br>';
							}
						break;
						}
						// textarea
						case "3": {
							if(empty($tovar[$i][value])) {
								$errsum ++;
								$err_ajax .='Textarea field ERROR<br>';
							}
						break;
						}			
					}
				}
									
				//Обновление после проверки
				if($errsum == 0 && count($tovar)>0) {
					for($i=0;$i<count($tovar); $i++) 
						switch($tovar[$i][fieldid]) {
							case "1": {
								if(!insert_text($row[type_ID], $subid, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value]), true))
								$err_ajax .='Text field ERROR (insert)<br>';
							break;
							}
							case "2": {
								if(!insert_text($row[type_ID], $subid, (int)$tovar[$i][sfid], $flname, true)) 
								$err_ajax .="File ".$_FILES['type_good_value_'.$i][name]." not upload (insert)<br>";
							break;
							}
							case "3": {
								if(!insert_text($row[type_ID], $subid, (int)$tovar[$i][sfid], strip_tags($tovar[$i][value]), true))
								$err_ajax .="Textarea field ERROR (insert)<br>";
							break;
							}
						}
				} else $err_ajax =$m[_ERR_NOFULLCHANGE].'<br>'.$err_ajax;		
											
				// Вывод ошибки
				if(strlen(trim($err_ajax))>1) {
					$m_ajax["msg"]=$template->logmsg2($err_ajax, 0); 
					$m_ajax["stat"] = 'errorupload';
							
					// Удаление при неуспешной закачке
					if(strlen($flname)>1 && $flname <> "empty") @unlink($template->homedir."/secretfiles/".encryptdata($flname));
				
				}
					else { 
						// Вывод в случае успеха
						$m_ajax["msg"] = $template->logmsg2($m[_SUCC_CHANGE], 1);
						$m_ajax["stat"] = "success";
					}
				// В случае ошибки ID
				} else {
					$m_ajax["msg"]=$template->logmsg2($m[_ERR_NOTFOUND_GOOD], 0); 
					$m_ajax["stat"] = 'errorupload';
				}
			}
			//*************************КОНЕЦ ОБНОВЛЕНИЯ***********************//
					
				} else $m_ajax["key_flag"] = false;
			}
			
		} else {
			$m_ajax['error'] = $template->logmsg2($m["_GOODS_UPDATING_NOT"],0);
			$m_ajax["stat"] = "notfound";
		}
	} else {
		$m_ajax['error'] = $template->logmsg2($m["_GOOD_NOTFOUND"],0);
		$m_ajax["stat"] = "notfound";
	}
	
	$m_ajax['goodid'] = $good["good_ID"];
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
} 

//************************************************************************ 
// Редактирование
function edit_goods() {
GLOBAL $mysql,$template,$m;
	$good_ID = (int) getfromget("good");
	if($_SESSION['lang']=="en")$var="_en"; else $var="";
	if($good_ID > 0) {
		$r=$mysql->query("
		SELECT gs.*, tp.* 
		FROM goods as gs, type_goods as tp 
		WHERE gs.good_ID=$good_ID AND (gs.type_ID=0 OR gs.type_ID=tp.type_ID) LIMIT 1");
		if($mysql->num_rows($r) == 1) {
		$row = strip($mysql->fetch_array($r));
	
			$m["ID_GOOD"] = $good_ID;
			
			if($row["prop_good"]<>2)
				$m["TYPE_GOOD"] = $row["name".$var];
					else $m["TYPE_GOOD"]="-";
					
			// Категории
			$m["CATEGORY_GOOD"] = navigator($row['cat_ID']);
			$m["CAT_GOOD"] = $row['cat_ID'];
			
			$m["ARCTICUL_GOOD"] = $row['idarticul'];
			$m["PRICE_GOOD"] = $row['price'];
			$m["SKIDKA_GOOD"] = (int)$row['rate_skidka'];
			$m["AGENT_GOOD"] = (int)$row['rate_agent'];
			$m["DATE_GOOD"] = $row['dateupload'];
			
			// Включение/выкл. склада
			$m["SKLAD_GOOD"] = $row['sklad'];
			$m["SKLAD_GOOD_CHECK"] = $row['prop_good'] <> 2 ? "disabled":"";
			$m["SKLAD_GOOD_CLASS"] = $row['prop_good'] <> 2 ? "inputdisabled": "inputcenter";
			
			// Название товара
			$m["TITLERU_GOOD"] = $row['title'];
			$m["TITLEEN_GOOD"] = $row['title_en'];
			
			// Описание товара
			$m["DESCRRU_GOOD"] = strip_tags($row['descr']);
			$m["DESCREN_GOOD"] = strip_tags($row['descr_en']);
			
			// Дополнительное описание
			$m["ADDITIONALRU_GOOD"] = strip_tags($row['additional']);
			$m["ADDITIONALEN_GOOD"] = strip_tags($row['additional_en']);
			
			// Мета тэги
			$m["META_DESC_GOOD"] = $row['meta_key'];
			$m["META_KEY_GOOD"] = $row['meta_desc'];
		}
	$m_ajax["msg"] = "<span class=\"text\"><b>".$m[_GOODS_EDIT]." #".$m["ID_GOOD"]."</b></span>";
	$m_ajax["view"]= $template->show_content("/exepanel/goods_edit.tpl");
	}
	
	$good_ID = getfrompost("good");
	if($good_ID > 0) {
		$r=$mysql->query("SELECT * FROM goods WHERE good_ID=$good_ID");
		if($mysql->num_rows($r) == 1) {
			$row = strip($mysql->fetch_array($r));
			// Категория товара
			$cat_good = (int) getfrompost("cat_good");
			if((int) getfrompost("cat_good") <=0 ||
				!$mysql->sql_select("SELECT * FROM category WHERE id=".$cat_good) ||
					$mysql->row == 0) {
				$err .= $template->show_contxt("{_GOODS_STEP3_CATEGORY_ERROR}<br>");	
			}
			
			// Артикул
			$articul_good = getfrompost("articul_good");
			
			// Цена
			$price_good = (double) str_replace(",",".",getfrompost("price_good"));
			if($price_good <= 0) $err .= $template->show_contxt("{_GOODS_STEP3_PRICE_ERROR}<br>");
			
			// Скидка и агентские
			$skidka_good = abs((int) getfrompost("skidka_good"));
			$agent_good = abs((int) getfrompost("agent_good"));
			$price_good*$skidka_good/100;
			$agent=($price_good-$skidka)*$agent_good/100;
			if(($price_good-$skidka-$agent) < 0.01) 
				$err .=$template->show_contxt("{_GOODS_STEP3_SUMM_ERROR}<br>");
				
			// Склад 
			if($row["prop_good"] == 2) {
				$sklad_good = (int) getfrompost("sklad_good");
				if((int) getfrompost("sklad_good") <0) $sklad_good=0;
			} else $sklad_good=0;
			
			// Названия товара
			$titleru_good = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("titleru_good")));
			$titleen_good = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("titleen_good")));
			if(strlen(getfrompost("titleru_good")) < 1 && strlen(getfrompost("titleen_good")) < 1) 
				$err .=$template->show_contxt("{_GOODS_STEP3_TITLE_ERROR}<br>");
				
			// Описание товара
			$descrru_good = str_replace(array('\"', "\'"), array("'","'"),  nl2br(strip_tags(getfrompost("descrru_good"))));
			$descren_good = str_replace(array('\"', "\'"), array("'","'"),  nl2br(strip_tags(getfrompost("descren_good"))));
			if(strlen(getfrompost("descrru_good")) < 1 && strlen(getfrompost("descren_good")) < 1) 
				$err .=$template->show_contxt("{_GOODS_STEP3_DESCR_ERROR}<br>");
				
			// Дополнительное описание товара
			$addtitionalru_good = str_replace(array('\"', "\'"), array("'","'"), nl2br(strip_tags(getfrompost("addtitionalru_good"))));
			$addtitionalen_good = str_replace(array('\"', "\'"), array("'","'"),  nl2br(strip_tags(getfrompost("addtitionalen_good"))));
			
			// Meta
			$meta_desc_good = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("meta_desc_good")));
			$meta_key_good = str_replace(array('\"', "\'"), array("'","'"), strip_tags(getfrompost("meta_key_good")));
			
			if(strlen($err) < 3) {
				$r = $mysql->query("
					UPDATE goods SET
					cat_ID='".$cat_good."',
					idarticul='".addslashes($articul_good)."',
					title='".addslashes($titleru_good)."',
					title_en='".addslashes($titleen_good)."',
					descr='".addslashes($descrru_good)."',
					descr_en='".addslashes($descren_good)."',
					additional='".addslashes($addtitionalru_good)."',
					additional_en='".addslashes($addtitionalen_good)."',
					meta_key='".addslashes($meta_key_good)."',
					meta_desc='".addslashes($meta_desc_good)."',
					price=$price_good,
					rate_skidka=$skidka_good,
					rate_agent=$agent_good,
					sklad=$sklad_good
					WHERE good_ID=$good_ID LIMIT 1
				");
				$m_ajax["msg"]=$template->logmsg2($m["_SUCC_CHANGE"],1);
				
				// Для запроса на обновления SELECT категрий при изменении категории
				if($row["cat_ID"]<>$cat_good) {
					$m_ajax["succ"]="yes";
					$m_ajax["cat"] = $cat_good;
				} else $m_ajax["succ"]="redirect";
				
			} else {
				$m_ajax["msg"]=$template->logmsg2($err,0);
				$m_ajax["succ"]="no";
			}
		
		}
	}
	
	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;

}

//************************************************************************ 
// Просмотр и Поиск товаров

//Сортировка
function create_img($sort,$what) {
	GLOBAL $m;
		$v = $sort == "ASC" ? "down.gif":"up.gif";
		return "<img onClick=\"doLoad('1','$what$sort ',document.getElementById('frm'),false)\" style=\"cursor:pointer;\" src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/exepanel/img/$v' title='".$m[_CP_SORT]."'>";
}

// $action - либо просто просмотр в категории товаров то VIEW, если поиск то SEARCH
function view_goods($action = "view") {
	GLOBAL $mysql,$template,$m;
	

	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="title_en"; else $var="title";
	
	$cat = (int)getfromget("cat");
	$name = addslashes(getfromget("name"));
	$articul = addslashes(getfromget("articul"));
	$price =  (double) str_replace(",",".",getfromget("price"));
	$goodid = (int)getfromget("goodid");
	$tolistgood = (int)getfromget("tolistgood");
	
	// Если выбран список товаров, отобразить категории
	if($cat <= 0 && $action=="view") {
		$memcat = !create_select_cat_list($tolistgood) ? $err_cat=1:create_select_cat_list($tolistgood);
		$m[ACTION]=<<<EOF
		<br>
		<span class="text"><b>$m[_GOODS_LIST]</b></span>
		<br>
		<form name="frm" onsubmit="return false">
		<div id="catlist">$memcat</div>
		<br>
		<input class="inputbutton" name="btn" id="btn" onClick="doLoad('1','',document.getElementById('frm'),true);" type="button" value="$m[_CP_SHOW]">
		</form>
EOF;
		$m["CURTYPE"]="view";
		if($tolistgood >0) 
			$m["SCRIPT_FOR_RETURN_FROM_UPDATING"]=<<<EOF
			<script language="JavaScript">
				//update catlist
				doLoad('5','',document.getElementById('frm'),false);
			</script>
EOF;
	} else {
		// Если выбран ПОИСК - отобразить форму поиска
		$m[ACTION]=<<<EOF
		<br>
		<span class="text"><b>$m[_GOODS_SEARCH]</b></span>
		<br><br>
		<form name="search" method="GET" onsubmit="return false">
		<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
			<tr>
				<td align="right" class="contdark" width="180"><b>$m[_GOODS_STEP4_NAMEGOOD]</b>:</td>
				<td align="left" class="contlight"><input class="inputtext" type="text" name="name" size="50"></td>
			</tr>
			<tr>
				<td align="right" class="contdark" width="180"><b>$m[_GOODS_IDGOOD]</b>:</td>
				<td align="left" class="contlight"><input class="inputtext" type="text" name="goodid"></td>
			</tr>
			<tr>
				<td align="right" class="contdark" width="180"><b>$m[_GOODS_STEP3_ARTICUL]</b>:</td>
				<td align="left" class="contlight"><input class="inputtext" type="text" name="articul"></td>
			</tr>
			<tr>
				<td align="right" class="contdark" width="180"><b>$m[_GOODS_STEP3_PRICE]</b>:</td>
				<td align="left" class="contlight"><input class="inputcenter" type="text" name="price" size="6"> y.e.</td>
			</tr>
		</table>
		<br>
		<input class="inputbutton" name="btn" id="btn" onClick="doLoad('8','',document.getElementById('search'),true);" type="button" value="$m[_CP_SEARCH]">
		</form>
EOF;
		$m[CURTYPE]="search";
}
	// Вывод шаблона
	if(($action=="view" && $cat <= 0) || ($action=="search" && strlen($name) <= 0 && strlen($articul) <= 0 && $price <= 0 && $goodid <= 0)) {
			if($err_cat) {
				// Подключение класса AJAX
				$JsHttpRequest =& new JsHttpRequest("windows-1251");
				$GLOBALS["_RESULT"] = array("view" => '', "msg" =>$template->logmsg2($m[_CAT_NOTFOUND],0));
			exit;
			}
			return $template->show_content("/exepanel/goods_view.tpl");
		} else {
		
		//Вывод из категорий
		if($action == "view") {
			// Сортировка
			$sort_flag 	= 	getfromget("flag")=="DESC" ? "ASC":"DESC";
			$sort_id 	= 	getfromget("id")=="DESC" ? "ASC":"DESC";
			$sort_title = 	getfromget("title")=="DESC" ? "ASC":"DESC";
			$sort_price = 	getfromget("price")=="DESC" ? "ASC":"DESC";
			$sort_prop 	= 	getfromget("prop")=="DESC" ? "ASC":"DESC";
			$sort_sell 	= 	getfromget("sell")=="DESC" ? "ASC":"DESC";
			if(strlen(getfromget("flag"))) $main_sort = " ORDER BY disabled $sort_flag";
			if(strlen(getfromget("id"))) $main_sort = " ORDER BY good_ID $sort_id";
			if(strlen(getfromget("title"))) $main_sort = " ORDER BY $var $sort_title";
			if(strlen(getfromget("price"))) $main_sort = " ORDER BY price $sort_price";
			if(strlen(getfromget("prop"))) $main_sort = " ORDER BY prop_good $sort_prop";
			if(strlen(getfromget("sell"))) $main_sort = " ORDER BY count_sell $sort_sell";
			$sort_flag_img = create_img($sort_flag, '&flag=');
			$sort_id_img = create_img($sort_id, '&id=');
			$sort_title_img = create_img($sort_title, '&title=');
			$sort_price_img = create_img($sort_price, '&price=');
			$sort_prop_img = create_img($sort_prop, '&prop=');
			$sort_sell_img = create_img($sort_sell, '&sell=');
			// К запросу
			$query = "cat_ID=$cat";
		} 
		
		//Поиск товаров		
		if($action == "search") {
			if(strlen($articul) > 0) $articul = "AND idarticul='".encode_form($articul)."'"; else $articul="";
			if($price >= 0.01) $price = "AND price='$price'"; else $price="";
			if($goodid > 0 ) $goodid = "AND good_ID='$goodid'"; else $goodid="";
			$name = encode_form($name);
			$query1 = $name;
			$mas_except=array("-","+",",",":","(",")","_","=","*","#","@","|","/",'"',"'");
			foreach($mas_except as $expr) $query1=str_replace($expr, " ", $query1);
			$query1=explode(" ", $query1);
			$i=0;
			
			//Формирование запроса
			if(count($query1)>=2)
			for($i=0; $i<=count($query1)-2; $i++) 
				$query.="((title LIKE '%{$query1[$i]}%') OR (title_en LIKE '%{$query1[$i]}%')) OR ";
		
			if(count($query)>=2)$i=$i+1;
			$query.="((title LIKE '%{$query1[$i]}%') OR (title_en LIKE '%{$query1[$i]}%')) 
					OR (title LIKE '%$name%' OR title_en LIKE '%$name%')";
			$query="(".$query.") $articul $price $goodid";
			
			//for debug
			//$JsHttpRequest =& new JsHttpRequest("windows-1251");
			//$GLOBALS["_RESULT"]=array("view" => $query); exit;
		}
			if($r = $mysql->sql_select("SELECT * FROM goods WHERE $query $main_sort")) {
			
				// Шапка таблицы		
				$head=<<<EOF
				<form name="g_s_c" method="POST" enctype="multipart/form-data">
				<div id="checkall">
				<span onclick="check_all('g_s_c', 1)">$m[_CP_CHECKALL]</span><br>
				<span onclick="check_all('g_s_c', 0)">$m[_CP_UNCHECKALL]</span>
				</div>
				<table align='center' border="1"  class='dash' cellpadding='5' cellspacing='0'>
				<tr>
					<td align=center class="conttop" width="65">$m[_GOODS_ON]&nbsp;&nbsp;$sort_flag_img</td>
					<td align=left class="conttop" width="50">$m[_GOODS_IDGOOD]&nbsp;&nbsp;$sort_id_img</td>
					<td align=left class="conttop" width="170">$m[_GOODS_STEP4_NAMEGOOD]&nbsp;&nbsp;$sort_title_img</td>
					<td align=left class="conttop" width="60">$m[_GOODS_STEP3_PRICE]&nbsp;&nbsp;$sort_price_img</td>
					<td align=left class="conttop" width="90">$m[_GOODS_PROP]&nbsp;&nbsp;$sort_prop_img</td>
					<td align=left class="conttop" width="80">$m[_GOODS_SELL]&nbsp;&nbsp;$sort_sell_img</td>
					<td align=center class="conttop" width="60">$m[_GOODS_COUNT]</td>
					<td align=center class="conttop" width="120">$m[_NEWS_ACTION]</td>
				</tr>
EOF;
				$foot=<<<EOF
				</table><br>
				<input class="inputbutton" type="button" name="btn" id="btn" onclick="doLoad('7', '?type=change_onoff_price&', document.getElementById('g_s_c'), true);" value="$m[_INST_SAVE]">
				</form>
EOF;

				if($mysql->num_rows($r) > 0) {
					// Вывод BODY таблицы
					$col=2;
					$i=0;
					$lnkk=$m[URLSITE].'/templates/'.$m[TEMPLDEF];
					while($row = $mysql->fetch_array($r)) {
						$row = strip($row);
						
						// Чередование стиля
						if(($col % 2)==0) $clas="contlight"; else $clas="contdark";

						// Остаток товара (товар который не куплен)
						if($row["prop_good"] <> 2) {
							$ost = $mysql->query("SELECT * FROM goods_secret WHERE id_good=$row[good_ID] AND status=0");
							$ostatok = $mysql->num_rows($ost);
						} else $ostatok = $row["sklad"];
						if($ostatok == 0) $clas="contdisabled";
						
						// Св-ва товара
						if($row["prop_good"] == 2) $prop = return_prop_good($row["prop_good"]);
							else
								$prop = substr(return_prop_good($row["prop_good"]),0,4).".";

						
						//Спец. предложение
						if($row["spec_offer"]==0) {
							$spc_img = "special_offer.gif"; 
							$spc_msg = $m["_GOODS_SPEC_OFFER_ON"];
						}else {
							$spc_img = "special_offer_off.gif";
							$spc_msg = $m["_GOODS_SPEC_OFFER_OFF"];
						}
						
						//Checked для ВКЛ.ВЫКЛ. товара магазина
						if($row["disabled"] == 0) $check_onoff = "checked"; else $check_onoff = "";
						
						//Картинка пополнения, если не ОФФЛАЙН товар
						if($row["prop_good"] <> 2) $pic_good_add = "<img onClick=\"updating(1, $row[good_ID], '', '', false)\" style=\"cursor:pointer;\" title=\"$m[_GOODS_UPDATING_LABEL]\" src=\"$lnkk/exepanel/img/pic_goodsadd.gif\">\n"; else $pic_good_add="";
						
						$body .=<<<EOF
			<tr class="$clas" onmouseover="colorstyle(this,'onover')" onmouseout="colorstyle(this,'$clas')">
				<input type="hidden" name="goods_change_id_$i" value="$row[good_ID]">
				<td align="center" width="65"><input type="checkbox" name="goods_change_onoff_$i" value="1" $check_onoff></td>
				<td align="center" width="50">$row[good_ID]</td>
				<td align="left"   width="170"><font style="font-size:7pt">$row[$var]</font></td>
				<td align="center" width="60"><input class="inputcenter" type="text" name="goods_change_price_$i" value="$row[price]" size="5"></td>
				<td align="center" width="90">$prop</td>
				<td align="center" width="80">$row[count_sell]</td>
				<td align="center" width="60">$ostatok</td>
				<td align="center" width="120">
				<img onClick="doLoad('2','&good=$row[good_ID]','',false)" style="cursor:pointer;" src="$lnkk/exepanel/img/edit.gif" title="{$m['_CP_EDIT']}">
				<img onClick="doLoad('6','?type=spec_offer&good=$row[good_ID]','',false)" style="cursor:pointer;" src="$lnkk/exepanel/img/$spc_img" title="{$spc_msg}">
				<a target="_blank" href="$m[URLSITE]/exepanel/photo_gallery.php?good=$row[good_ID]&"><img src="$lnkk/exepanel/img/photo.gif" title="$m[_PHOTO_GALLERY]"></a>
				$pic_good_add 
				<img onClick="del_box($row[good_ID],'numgood', 'boxblock')" style="cursor:pointer;" src="$lnkk/exepanel/img/delete.gif" title="{$m['_CP_DEL']}">
				</td>
			</tr>	
EOF;
						$col++;
						$i++;
						}
				// Результат для AJAX
				$GLOBALS["_RESULT"] = array("view" => $head.$body.$foot, "msg"=>'');
				
				} else $GLOBALS["_RESULT"] = array("view" => '', "msg" =>$template->logmsg2($m[_GOODS_NOTFOUND],0));	

					
		} else $GLOBALS["_RESULT"] = array("view" => '', "msg"=> $template->logmsg2($m[_ERR_REQUEST],0));
		
		// Подключение класса AJAX
		$JsHttpRequest =& new JsHttpRequest("windows-1251");
		
		exit;
	}
		
}

//************************************************************************ 
// ПОЛНОЕ удаление товаров
function delete_good() {
GLOBAL $mysql,$m,$template;

	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");

	$goodid = (int) getfromget('goodid');
	
	if($goodid > 0 ) {
		$r = $mysql->query("SELECT good_ID,cat_ID FROM goods WHERE good_ID=".$goodid);
		if($mysql->num_rows($r) == 1) {
			$row = $mysql->fetch_array($r);
		
		
			$mysql->query("DELETE FROM goods 
							WHERE good_ID=".$goodid." LIMIT 1
						");
						
			// Удаление файлов
			$r=$mysql->query(
						"SELECT gs.*,gsv.*
						FROM goods_secret as gs, goods_secret_fields_value as gsv ,goods_secret_fields as gsf
						WHERE gs.id_good=$goodid AND gsv.id_num = gs.id_num AND gsf.secret_field_ID=gsv.secret_field_ID AND gsf.field_ID=2");
			while($todel = $mysql->fetch_array($r))
				@unlink($template->homedir."/secretfiles/".$todel['sercet_field_value']);
			
			// Удаление записей из двух таблиц о загруженном товаре
			$mysql->query("DELETE goods_secret, goods_secret_fields_value 
						FROM goods_secret, goods_secret_fields_value
						WHERE goods_secret.id_good=$goodid AND goods_secret_fields_value.id_num = goods_secret.id_num
						");
						
			// Удаление фоток
			$r = $mysql->query("SELECT * FROM photo_goods WHERE good_ID=$goodid");
			while($todel = $mysql->fetch_array($r)) {
				@unlink($template->homedir."/photo_goods/".$todel['path_to_photo']);
				@unlink($template->homedir."/photo_goods/mini_".$todel['path_to_photo']);
			}
			
			$m_ajax["msg"] = $template->logmsg2($m[_SUCC_DELETE], 1);
			$m_ajax["succ"] = "yes";
			$m_ajax["cat"] = $row[cat_ID];
			
			
		} else $m_ajax["msg"] = $template->logmsg2($m[_ERR_REQUEST], 0);
	} else $m_ajax["msg"] =$template->logmsg2($m[_ERR_REQUEST], 0);
	
	$m_ajax["goodid"] = $goodid;
	$GLOBALS["_RESULT"] = $m_ajax;
	exit;
}

//***********************************************************************************
// Удаление загруженного товара по одиночно
function delete_sub_good() {
GLOBAL $mysql,$m,$template;

	// Подключение класса AJAX
	$JsHttpRequest =& new JsHttpRequest("windows-1251");
	
	$subid = (int) getfromget('subid');
	//Проверка ключа на правильность
	if(validate_key($_SESSION['key_edit_good'])) {
		$m_ajax["key_flag"] = true;
		$r = $mysql->query("
			SELECT gs.id_good, gs.id_num, g.prop_good 
			FROM goods_secret as gs, goods as g 
			WHERE gs.id_num=$subid AND g.good_ID=gs.id_good LIMIT 1");
			
		if($mysql->num_rows($r) == 1) {
			$row = $mysql->fetch_array($r);
			
			//Подсчет кол-ва товаров в категории
			$kolvo = return_count_secret_good($row[id_good]);
			
			// Для удаления файлов
			$r = $mysql->query("
				SELECT gs.*,gsv.*
				FROM goods_secret as gs, goods_secret_fields_value as gsv ,goods_secret_fields as gsf
				WHERE gs.id_num=$subid  AND gsv.id_num = gs.id_num AND gsf.secret_field_ID=gsv.secret_field_ID AND gsf.field_ID=2
			");
			while($todel = $mysql->fetch_array($r))
				@unlink($template->homedir."/secretfiles/".$todel['sercet_field_value']);
			
			// Удаление записей из двух таблиц о загруженном товаре
			$mysql->query("
					DELETE goods_secret, goods_secret_fields_value 
					FROM goods_secret, goods_secret_fields_value
					WHERE goods_secret.id_num=$subid AND goods_secret_fields_value.id_num = goods_secret.id_num
					");
					
			$m_ajax["stat"] = "updateform";
			$m_ajax["msg"] = $template->logmsg2($m[_SUCC_DELETE],1);
			$m_ajax["kolvo"] = $kolvo-1;
		} else {
			$m_ajax["msg"] = $template->logmsg2($m["_GOODS_UPDATING_NOTFOUND"],0);
			$m_ajax["stat"] = "notfound";
		}
	} else $m_ajax["key_flag"] = false;
	
	// Отображение кнопки загрузки товара
	if(($kolvo-1) == 0 || $row['prop_good']==0 || ($row['prop_good']==1 && ($kolvo-1)==0))
		$m_ajax["btnupdating"] = return_btn_updating_good();
		
	$m_ajax["goodid"]=$row['id_good'];
	$GLOBALS["_RESULT"] = $m_ajax;
	
	exit;
}

//************************************************************************ 
// Управление товарами
function manage_goods() {
	GLOBAL $template, $mysql;
	$type = getfromget("type");
	
	switch($type) {
		case "add": return add_goods(); break;
		case "category": return category_reselect();break;
		case "view": return view_goods("view");break;
		case "search": return view_goods("search");break;
		case "edit": return edit_goods();break;
		case "refresh_cat_list": return refresh_cat_list();break;
		case "spec_offer": return change_spec_offer(); break;
		case "specoffer": return all_spec_offer(); break;
		case "change_onoff_price":return change_onoff_price(); break;
		case "delete":return delete_good();break;
		case "delete_sub":return delete_sub_good();break;
		case "updating":return updating_good();break;
		case "mem_key":return mem_key();break;
		case "return_listing_goods": return return_listing_goods();
		default: return view_goods();
	}
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_LISTGOODS"];
		$m["CENTERCONTENT"]=manage_goods();
		
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>