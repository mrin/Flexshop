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
//Формирование полей для заполнения
function step_0() {
	GLOBAL $template,$m;
	if(getfromget("method")=="add" && (int)getfromget("step")==0) {
	
	$inputtext=(int)getfrompost("inputtext");
	$inputarea=(int)getfrompost("inputarea");
	
	$nameru=getfrompost("nameru");
	$nameen=getfrompost("nameen");
	
	$_SESSION["add_type"]["nameru"]=$nameru;
	$_SESSION["add_type"]["nameen"]=$nameen;
	$_SESSION["add_type"]["inputtext"]=$inputtext;
	$_SESSION["add_type"]["inputarea"]=$inputarea;
	$m[NAMERU]=$_SESSION["add_type"]["nameru"];
	$m[NAMEEN]=$_SESSION["add_type"]["nameen"];
	$m[TEXT]=$_SESSION["add_type"]["inputtext"];
	$m[AREA]=$_SESSION["add_type"]["inputarea"];
	
	getfrompost("file")==1 ? $m[CHECK]="checked":$m[CHECK]="";
		if(( $inputarea>0 || $inputtext >0 || getfrompost("file")==1) && (!empty($nameru) || !empty($nameen))) {
/*
1 - <input class=''{class_field}'' type=''text'' name=''{name_field}_{ID}''  size=''{size_field}'' value=''{value_field}''>
2 - <input class=''{class_field}'' type=''file'' name=''{name_field}_{ID}''  size=''{size_field}'' value=''{value_field}''>
3 - <textarea class=''{class_field}'' name=''{name_field}_{ID}''  cols=''{cols_field}'' rows=''{rows_field}''>{value_field}</textarea>
*/			
			// Поля input type=text
			for($i=0;$i<$inputtext;$i++) {
				$tr.=<<<EOF
				
					<tr>
						<td align=center class="contdark">
						<span class=text><b>Text</span>
						</td>
						<td align=center class="contdark">
						<span class=text>RUS:</span> <input type="text" class="inputtext" name="add_type_typ1_nameru_$i"><br>
						<span class=text>ENG:</span> <input type="text" class="inputtext" name="add_type_typ1_nameen_$i">
						</td>
						<td align=left class="contdark">
						<span class=text>
						CSS : <input type="text" class="inputtext" name="add_type_typ1_class_$i" size=8 value="inputtext">
						SIZE: <input type="text" class="inputcenter" name="add_type_typ1_size_$i" size=3 value="16">
						</span>
						</td>
						<td align=center class="contdark">
						<input class="inputcenter" type="text" name="add_type_typ1_sort_$i" size=2 value=0>
						</td>
					</tr>
				
EOF;
			}
			// Поля textarea
			for($i=0;$i<$inputarea;$i++) {
				$tr.=<<<EOF
					<tr>
						<td align=center class="contdark">
						<span class=text><b>Textarea</span>
						</td>
						<td align=center class="contdark">
						<span class=text>RUS:</span> <input type="text" class="inputtext" name="add_type_typ3_nameru_$i"><br>
						<span class=text>ENG:</span> <input type="text" class="inputtext" name="add_type_typ3_nameen_$i">
						</td>
						<td align=left class="contdark">
						<span class=text>
						CSS : <input type="text" class="inputtext" name="add_type_typ3_class_$i" size=8>
						ROWS: <input type="text" class="inputcenter" name="add_type_typ3_rows_$i" size=3 value="5">
						COLS: <input type="text" class="inputcenter" name="add_type_typ3_cols_$i" size=3 value="20">
						</span>
						</td>
						<td align=center class="contdark">
						<input class="inputcenter" type="text" name="add_type_typ3_sort_$i" size=2 value=0>
						</td>
					</tr>
EOF;
			}
		// Поле input type=file
		if((int)getfrompost("file")==1) {
						$tr.="
					<tr>
						<td align=center class=\"contdark\">
						<span class=text><b>File</span>
						</td>
						<td align=center class=\"contdark\">
						<span class=text>RUS:</span> <input type=\"text\" class=\"inputtext\" name=\"add_type_typ2_nameru_0\"><br>
						<span class=text>ENG:</span> <input type=\"text\" class=\"inputtext\" name=\"add_type_typ2_nameen_0\">
						</td>
						<td align=left class=\"contdark\">
						<span class=text>
						CSS : <input type=\"text\" class=\"inputtext\" name=\"add_type_typ2_class_0\" size=8 value=\"inputtext\">
						SIZE: <input type=\"text\" class=\"inputcenter\" name=\"add_type_typ2_size_0\" size=3 value=\"16\">
						</span>
						</td>
						<td align=center class=\"contdark\">
						<input class=\"inputcenter\" type=\"text\" name=\"add_type_typ2_sort_0\" size=2 value=0>
						</td>
					</tr>";
	}
			
			$m[TYPE_LIST]=$tr;
			return $template->show_content("/exepanel/type_goods_add2.tpl");
		} else { $template->logtxt("_ERR_FIELD",0); return $template->show_content("/exepanel/type_goods_add.tpl"); }
	} else
		{
		if(getfromget("step")<>"0") {
			unset($_SESSION["add_type"]);
			$m[NAMERU]="";
			$m[NAMEEN]="";
			$m[TEXT]=0;
			$m[AREA]=0;
		} else {
			$m[NAMERU]=$_SESSION["add_type"]["nameru"];
			$m[NAMEEN]=$_SESSION["add_type"]["nameen"];
			$m[TEXT]=$_SESSION["add_type"]["inputtext"];
			$m[AREA]=$_SESSION["add_type"]["inputarea"];
		}
		return $template->show_content("/exepanel/type_goods_add.tpl");
		}
	
}
/*
<input class=''{class_field}'' type=''text'' name=''{name_field}_{ID}''  size=''{size_field}'' value=''{value_field}''>
<input class=''{class_field}'' type=''file'' name=''{name_field}_{ID}''  size=''{size_field}'' value=''{value_field}''>
<textarea class=''{class_field}'' name=''{name_field}_{ID}''  cols=''{cols_field}'' rows=''{rows_field}''>{value_field}</textarea>
secret_field_ID int(11) unsigned NOT NULL auto_increment,	
	type_ID int(11) unsigned NOT NULL,							
	field_ID int(11) unsigned NOT NULL,							
	name varchar(255) NOT NULL default '',					
	name_en varchar(255) NOT NULL default '',				
	setting_array text NOT NULL default '',		
	sort int NOT NULL default 0,	
*/
//************************************************************************ 
// Добавление в базу ID типа товара + сформированные поля
function step_1() {
	GLOBAL $mysql,$template,$m;
	if(getfromget("method")=="add") {
		if(!empty($_SESSION["add_type"]["nameru"]) || !empty($_SESSION["add_type"]["nameen"])) {
		
		// Получение массивов по каждому типу поля
		$typ1=getarray("add_type_typ1", "POST");
		$typ2=getarray("add_type_typ2", "POST");
		$typ3=getarray("add_type_typ3", "POST");
		
		$succ=0;
				
		if(count($typ2)==1 || count($typ3)>0 || count($typ1)>0) {
			// Добавление записи в тип товаров
			$mysql->query("INSERT INTO type_goods VALUES('','".addslashes($_SESSION["add_type"]["nameru"])."','".addslashes($_SESSION["add_type"]["nameen"])."')");
			$typeid = $mysql->insert_id();
			
			// Однострочное поле
			if(count($typ1)>0) 
			for($i=0;$i<=count($typ1)-1;$i++) { 
				$setting_array=serialize(array("class_field" => $typ1["$i"]["class"], "size_field" => $typ1["$i"]["size"]));
				if((int)$typ1[$i][sort] <=0) $sorting=0; else  $sorting=(int)$typ1[$i][sort];
				if($typeid >0) 
					if($mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','1','".$typ1[$i][nameru]."','".$typ1[$i][nameen]."','".$setting_array."','".$sorting."')"))
						$succ++;
			}
			
			//Добавление полей типа TEXTAREA
			if(count($typ3)>0)
			for($i=0;$i<=count($typ3)-1;$i++) {
				$setting_array=serialize(array("class_field" => $typ3["$i"]["class"], "rows_field" => $typ3["$i"]["rows"], "cols_field" => $typ3["$i"]["cols"]));
				if((int)$typ3[$i][sort] <=0) $sorting=0; else  $sorting=(int)$typ3[$i][sort];
				if($typeid >0) 
					if($mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','3','".$typ3[$i][nameru]."','".$typ3[$i][nameen]."','".$setting_array."','".$sorting."')"))
						$succ++;
			}
			
			// Добавление поля FILE
			if(count($typ2)==1) {
			$setting_array=serialize(array("class_field" => $typ2["0"]["class"], "size_field" => $typ2["0"]["size"]));
				if((int)$typ2[0][sort] <=0) $sorting=0; else  $sorting=(int)$typ2[0][sort];
				if($typeid >0)
					if($mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','2','".$typ2[0][nameru]."','".$typ2[0][nameen]."','".$setting_array."','".$sorting."')"))
						$succ++;
			}
			
		$template->logmsg($template->show_contxt("<b>{_TYP_FIELD_ADDED}:</b> $succ"),3);
		return show_type();
		} else 	{$template->logtxt("_ERR_FIELD",0); return step_0();}
		
		
		} else {$template->logtxt("_ERR_FIELD",0); return step_0();}
	}
}
//************************************************************************ 
function add_type() {
	switch ((int)getfromget("step")) {
		case "1": return step_1();break;
		default : return step_0();break;
	}
}
//************************************************************************ 
// Вывод для редактирования
function show_edit($typeid) {
	GLOBAL $mysql,$template,$m;
	$r=$mysql->query("SELECT tp.name as tp_name,tp.name_en as tp_name_en,gsf.* 
		FROM type_goods as tp,goods_secret_fields as gsf WHERE tp.type_ID=gsf.type_ID AND tp.type_ID=$typeid ORDER BY gsf.sort");
	if($mysql->num_rows($r)<=0)  {$template->logtxt("_TYP_ERRFOUND",0); return show_type();}
	$row=$mysql->fetch_array($r);
	$row=strip($row);
	$m[ID]=$typeid;
	$tr="";
	$i=0;
	$j=0;
	$k=0;
	$mysql->sql_select("SELECT * FROM goods_secret_fields WHERE field_ID=2 AND type_ID=$typeid");
	if($mysql->row > 0) $m[CHECK]="disabled";
	$m[TYPNAME]="RUS: $row[tp_name], ENG: $row[tp_name_en]";
	do {
		if($row[field_ID]==1) {
			$setting_array=unserialize($row[setting_array]);

			$tr.=<<<EOF
			<tr><input type=hidden name="change_type_typ1_id_$i" value="$row[secret_field_ID]">
				<td align=center class="contdark">
					<span class=text><b>Text</span>
				</td>
				<td align=center class="contdark">
					<span class=text>RUS:</span> <input type="text" class="inputtext" name="change_type_typ1_nameru_$i" value="$row[name]"><br>
					<span class=text>ENG:</span> <input type="text" class="inputtext" name="change_type_typ1_nameen_$i" value="$row[name_en]">
				</td>
				<td align=left class="contdark">
					<span class=text>
						CSS : <input type="text" class="inputtext" name="change_type_typ1_class_$i" size=8 value="$setting_array[class_field]">
						SIZE: <input type="text" class="inputcenter" name="change_type_typ1_size_$i" size=3 value="$setting_array[size_field]">
					</span>
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="text" name="change_type_typ1_sort_$i" size=2 value="$row[sort]">
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="checkbox" name="change_type_typ1_del_$i" size=2 value="1">
				</td>
			</tr>			
EOF;
		$i++;
		}
		if($row[field_ID]==2) {
			$setting_array=unserialize($row[setting_array]);
			$tr.=<<<EOF
			<tr><input type=hidden name="change_type_typ2_id_$j" value="$row[secret_field_ID]">
				<td align=center class="contdark">
					<span class=text><b>File</span>
				</td>
				<td align=center class="contdark">
					<span class=text>RUS:</span> <input type="text" class="inputtext" name="change_type_typ2_nameru_$j" value="$row[name]"><br>
					<span class=text>ENG:</span> <input type="text" class="inputtext" name="change_type_typ2_nameen_$j" value="$row[name_en]">
				</td>
				<td align=left class="contdark">
					<span class=text>
						CSS : <input type="text" class="inputtext" name="change_type_typ2_class_$j" size=8 value="$setting_array[class_field]">
						SIZE: <input type="text" class="inputcenter" name="change_type_typ2_size_$j" size=3 value="$setting_array[size_field]">
					</span>
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="text" name="change_type_typ2_sort_$j" size=2 value="$row[sort]">
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="checkbox" name="change_type_typ2_del_$j" size=2 value="1">
				</td>
			</tr>			
EOF;
		}
		if($row[field_ID]==3) {
			$setting_array=unserialize($row[setting_array]);
			$tr.=<<<EOF
			<tr><input type=hidden name="change_type_typ3_id_$k" value="$row[secret_field_ID]">
				<td align=center class="contdark">
					<span class=text><b>Textarea</span>
				</td>
				<td align=center class="contdark">
					<span class=text>RUS:</span> <input type="text" class="inputtext" name="change_type_typ3_nameru_$k" value="$row[name]"><br>
					<span class=text>ENG:</span> <input type="text" class="inputtext" name="change_type_typ3_nameen_$k" value="$row[name_en]">
				</td>
				<td align=left class="contdark">
					<span class=text>
						CSS : <input type="text" class="inputtext" name="change_type_typ3_class_$k" size=8 value="$setting_array[class_field]">
						ROWS: <input type="text" class="inputcenter" name="change_type_typ3_rows_$k" size=3 value="$setting_array[rows_field]">
						COLS: <input type="text" class="inputcenter" name="change_type_typ3_cols_$k" size=3 value="$setting_array[cols_field]">
					</span>
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="text" name="change_type_typ3_sort_$k" size=2 value="$row[sort]">
				</td>
				<td align=center class="contdark">
					<input class="inputcenter" type="checkbox" name="change_type_typ3_del_$k" size=2 value="1">
				</td>
			</tr>			
EOF;
		$k++;
		}
	} while($row = $mysql->fetch_array($r)); 
	
	$m[TYPE_LIST]=$tr;
	return $template->show_content("/exepanel/type_goods_edit.tpl");
}
//************************************************************************ 
// Добавление дополнительно полей
function add_edit($typeid) {
	GLOBAL $template, $mysql,$m;
	if($mysql->sql_select("SELECT * FROM type_goods WHERE type_ID=$typeid") && $mysql->row ==1)
		{	$inputtext=(int)getfrompost("inputtext");
			$file=(int)getfrompost("file");
			$inputarea=(int)getfrompost("inputarea");
			$m[ID]=$typeid;
			$succ=0;
			// Добавление поля inputtext
			if($inputtext>0) 
				for($i=0;$i<=$inputtext-1;$i++) { 
				$setting_array=serialize(array("class_field" => "inputtext", "size_field" => 16));
					$mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','1','','','".$setting_array."','0')");
						$succ++;
			}
			//Добавление полей типа TEXTAREA
			if($inputarea>0)
				for($i=0;$i<=$inputarea-1;$i++) {
				$setting_array=serialize(array("class_field" => "inputarea", "rows_field" => 5, "cols_field" => 20));
					$mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','3','','','".$setting_array."','0')");
					$succ++;
			}
			// Добавление поля FILE
			if($file==1) {
				if($mysql->sql_select("SELECT * FROM goods_secret_fields WHERE field_ID=2 AND type_ID=$typeid") && $mysql->row == 0) {
				$setting_array=serialize(array("class_field" => "inputtext", "size_field" => 16));
					$mysql->query("INSERT INTO goods_secret_fields VALUES('','".$typeid."','2','','','".$setting_array."','0')");
					$succ++;
				}
			}
			if($succ > 0)$template->logtxt("_SUCC_ADD",1);
			return show_edit($typeid);
		} else {$template->logtxt("_ERR_REQUEST",0); return show_type();}
}
//************************************************************************ 
// Редактирование записей в типе товаров
function change_edit($typeid) {
	GLOBAL $template, $mysql,$m;
	if($mysql->sql_select("SELECT * FROM type_goods WHERE type_ID=$typeid") && $mysql->row ==1) {
		$typ1=getarray("change_type_typ1", "POST");
		$typ2=getarray("change_type_typ2", "POST");
		$typ3=getarray("change_type_typ3", "POST");
		
		for($i=0;$i<=count($typ1)-1;$i++) {
			if($typ1[$i][del] == 1) {
				if($mysql->sql_select("SELECT * FROM goods_secret_fields_value WHERE secret_field_ID=".$typ1[$i][id]."") && $mysql->row==0)
					$mysql->query("DELETE FROM goods_secret_fields WHERE secret_field_ID=".$typ1[$i][id]."");
				}
				else {
					$setting_array=serialize(array("class_field" => $typ1["$i"]["class"], "size_field" => $typ1["$i"]["size"]));
					$r=$mysql->query("UPDATE goods_secret_fields SET name='".$typ1[$i][nameru]."', name_en='".$typ1[$i][nameen]."', setting_array='$setting_array', sort='".$typ1[$i][sort]."' WHERE secret_field_ID=".$typ1[$i][id]."");
				}
		}
		if(count($typ2)==1) {
			if($typ2[0][del] == 1) {
				if($mysql->sql_select("SELECT * FROM goods_secret_fields_value WHERE secret_field_ID=".$typ2[0][id]."") && $mysql->row==0)
					$mysql->query("DELETE FROM goods_secret_fields WHERE secret_field_ID=".$typ2[0][id]."");
				}
				else {
					$setting_array=serialize(array("class_field" => $typ2["0"]["class"], "size_field" => $typ2["0"]["size"]));
					$mysql->query("UPDATE goods_secret_fields SET name='".$typ2[0][nameru]."', name_en='".$typ2[0][nameen]."', setting_array='$setting_array', sort='".$typ2[0][sort]."' WHERE secret_field_ID=".$typ2[0][id]."");
				}
		}
		for($i=0;$i<=count($typ3)-1;$i++)  {
			if($typ3[$i][del] == 1) {
				if($mysql->sql_select("SELECT * FROM goods_secret_fields_value WHERE secret_field_ID=".$typ3[$i][id]."") && $mysql->row==0)
					$mysql->query("DELETE FROM goods_secret_fields WHERE secret_field_ID=".$typ3[$i][id]."");
					}
				else {
					$setting_array=serialize(array("class_field" => $typ3["$i"]["class"], "rows_field" => $typ3["$i"]["rows"], "cols_field" => $typ3[$i][cols]));
					$mysql->query("UPDATE goods_secret_fields SET name='".$typ3[$i][nameru]."', name_en='".$typ3[$i][nameen]."', setting_array='$setting_array', sort='".$typ3[$i][sort]."' WHERE secret_field_ID=".$typ3[$i][id]."");
				}
		}
		$template->logtxt("_SUCC_CHANGE",1); return show_edit($typeid);
	} else {$template->logtxt("_ERR_REQUEST",0); return show_type();}
}
function edit_type() {
	GLOBAL $mysql,$template;
	$typeid=(int)getfromget("typeid");
	switch(getfromget("method")) {
		case "add":return add_edit($typeid);break;
		case "change": return change_edit($typeid); break;
		default: return show_edit($typeid); break;
	}
}
//************************************************************************ 
// Изменение названия ТИПА ТОВАРА
function name_change() {
	GLOBAL $template, $mysql,$m;
	// Получение массива из формы
	$mas = getarray("type_goods","POST");
	if(count($mas) <=0)  return FALSE;
	$err=0;
	for($i=0; $i<=count($mas)-1; $i++) {
		$mas[$i][id]=(int)$mas[$i][id];
		if($mas[$i][id] > 0 && $mysql->sql_select("SELECT * FROM type_goods WHERE type_ID=".$mas[$i][id]) && $mysql->row == 1) {
			if(!empty($mas[$i][nameru]) || !empty($mas[$i][nameen])) {
				// Обновление
				$r=$mysql->query("UPDATE type_goods SET name='".$mas[$i][nameru]."', name_en='".$mas[$i][nameen]."' WHERE type_id=".$mas[$i][id]);
				if(!$r) $err++;
			} else $err++;
		} else $err++;
	}
	if($err==0) $template->logtxt("_SUCC_CHANGE",1);
}
//************************************************************************ 
// Полное удаление типа товара
function full_delete($typeid) {
	GLOBAL $template, $mysql,$m;
	if($typeid > 0) {
		$q=$mysql->query("SELECT count(*) as kolvo FROM goods 
				WHERE goods.type_ID=$typeid");
		$row=$mysql->fetch_array($q);
		$kolvo1=$row[kolvo];
		$q=$mysql->query("SELECT count(*) as kolvo FROM goods_secret
				WHERE goods_secret.id_type=$typeid");
		$row=$mysql->fetch_array($q);
		$kolvo2=$row[kolvo];
		$q=$mysql->query("SELECT count(*) as kolvo FROM goods_secret_fields_value 
				WHERE goods_secret_fields_value.id_type=$typeid");
		$row=$mysql->fetch_array($q);
		$kolvo3=$row[kolvo];
		$kolvo=$kolvo1+$kolvo2+$kolvo3;
		if($kolvo==0) {
			$q=$mysql->query("DELETE FROM type_goods WHERE type_ID=$typeid");
			$q=$mysql->query("DELETE FROM goods_secret_fields WHERE type_ID=$typeid");
			$template->logtxt("_SUCC_DELETE",1);
		} else $template->logtxt("_TYP_NODEL",0);
	}
}
//************************************************************************ 
// Вывод  типов товаров
function show_type() {
	GLOBAL $template, $mysql, $m;
	switch(getfromget("method")) {
		case "change" : { name_change(); break; }
		case "del" : {$typeid = (int) getfromget("typeid"); full_delete($typeid); break;}
	} 
		$r=$mysql->query("SELECT * FROM type_goods");
			if($r and $mysql->num_rows($r) > 0) {
				$col=2; $i=0;
				while ($row = $mysql->fetch_array($r)) {
				$row=strip($row);
				if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
					$img=$template->show_contxt("
						<a href='{URLSITE}/exepanel/type_good.php?type=edit&typeid=".$row[type_ID]."&'>
						<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/edit.gif' title='{_TYP_CHANGE}'></a>&nbsp;&nbsp;&nbsp;
						
						<a href=\"javascript:submiturl('{URLSITE}/exepanel/type_good.php?type=show&method=del&typeid=".$row[type_ID]."&', '{_CP_DEL}?')\">
						<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/delete.gif' title='{_TYP_DELETE}'></a>
					");
					$m[TYP_GOODS_LIST] .=<<<EOF
						<input type="hidden" name="type_goods_id_$i" value="$row[type_ID]">
						<tr>
							<td align=center class="$clas"><input class="inputtext" type="text" name="type_goods_nameru_$i" value="$row[name]"></td>
							<td align=center class="$clas"><input class="inputtext" type="text" name="type_goods_nameen_$i" value="$row[name_en]"></td>
							<td align=center class="$clas">$img</td>
						</tr>
EOF;
				$i++;
				$col++;
				}
				$m[SUBMIT_BUTTON]=$template->show_contxt("<p><input class=inputbutton type=submit value='{_INST_SAVE}'></p>");
			} else $template->logtxt("_ERR_NOREC",3);
	return $template->show_content("/exepanel/type_good_list.tpl"); 
}
//************************************************************************ 
function manage_type() {
	$type = getfromget("type");
	switch($type) {
		case "show" : return show_type();
		case "add" : return add_type();
		case "edit": return edit_type();
		default : return show_type();
	}
}
//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){	
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_TYP_INFO"];
		$m["CENTERCONTENT"]=manage_type();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>