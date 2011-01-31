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
// Инициализация класса Nested Tree (дерево каталога)
$tree = new DBTree($mysql,"category","id");

//************************************************************************ 
// Вывод дерева каталога
// QUERY: Сформированный запрос
// CAT: ID категории
function show_result($query,$cat)
{ GLOBAL $mysql,$template,$HTTP_SESSION_VARS;
	$result = $mysql->query($query);
	// Проверка на леквидность созданного запроса функцией в классе DBTree -> get_tree_from_id()
	if ($mysql->num_rows($result) == 0) {$template->logtxt("_ERR_NOREC",3); return false; }
	if($HTTP_SESSION_VARS['lang']=="en")$var="name_en"; else $var="name";
	$col=2;
	$mas=array();
	// Заполнение массива из результата запроса
	while ($row = $mysql->fetch_array($result)) $mas[]=strip($row);
	for($id=0; $id<=count($mas)-1; $id++) {
		// Изменение классов цветовых таблиц
		if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
		// Если элемент содержит потомков
		if(isset($mas[$id][nflag]) && $mas[$id][nflag]) {
			// Проверка на открытость или закрытость папки-картинки
			if(!empty($mas[$id+1][cat_level]) && ($mas[$id+1][cat_level]-$mas[$id][cat_level])==1) 
				{	
					$folder="open"; $exp="shrink=".$mas[$id][id]."&"; $node="minus"; 
				} else {
					$folder="closed"; $exp="cat=".$mas[$id][id].""; $node="plus"; 
					}
			// Формирование категорий в шаблон
			$tt=$template->show_contxt(
			"
			<a href='{URLSITE}/exepanel/category.php?type=show&$exp'>
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/node_$node.gif' class=text></a>
			<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$folder.gif'> 
			<a href='{URLSITE}/exepanel/category.php?type=show&cat=".$mas[$id][id]."&' class=text><b>".$mas[$id][$var]."</b></a>
			");
			}
			else{	
					if($cat==$mas[$id][id])$doc="doc_sel"; else $doc="doc";
					$tt=$template->show_contxt("
					<a href='{URLSITE}/exepanel/category.php?type=show&cat=".$mas[$id][id]."&' class=text>
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$doc.gif'></a>
					<a href='{URLSITE}/exepanel/category.php?type=show&cat=".$mas[$id][id]."&' class=text>".$mas[$id][$var]."</a>"
					);
				}
		//Спец опции для ROOT категории
		if($mas[$id][id]==1) 
					//Операции
					$operation=$template->show_contxt("
					<a href='{URLSITE}/exepanel/category.php?type=add&cat=".$mas[$id][id]."&'>
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/insrow.gif' title='{_CAT_ADD}'></a>");
			else
				{
				//Операции
				$operation=$template->show_contxt("
					<a href='{URLSITE}/exepanel/category.php?type=add&cat=".$mas[$id][id]."&'>
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/insrow.gif' title='{_CAT_ADD_IN}'></a>&nbsp;&nbsp;&nbsp;
					<a href='{URLSITE}/exepanel/category.php?type=edit&cat=".$mas[$id][id]."&'>
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/edit.gif' title='{_CAT_CHANGE} \"".$mas[$id][$var]."\"'></a>&nbsp;&nbsp;&nbsp;
					<a href=\"javascript:submiturl('{URLSITE}/exepanel/category.php?type=delete&id=".$mas[$id][id]."&', '{_CP_DEL}?')\">
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/delete.gif' title='{_CAT_DELETE} \"".$mas[$id][$var]."\"'></a>
					");
				$srt=$template->show_contxt("<a href='{URLSITE}/exepanel/category.php?type=move&cat=".$mas[$id][id]."&'>
					<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/sort_arrow.gif' title='{_CAT_SORT}'></a>");
				}
		//Формирование строк в шаблон
		$kct = str_repeat("&nbsp; &nbsp; &nbsp;",$mas[$id]['cat_level']).$tt;
		$categ.=<<<EOF
			<tr class="$clas" onmouseover="colorstyle(this,'onover')" onmouseout="colorstyle(this,'$clas')">
				<td align="left">$kct</td>
				<td align="center">$srt</td>
				<td align="center">$operation</td>
			</tr>
EOF;
		$col++;
		}
		return $categ;
}

//************************************************************************ 
// Создание списка родительских категорий в OPTION тег (ВСЕХ КАТЕГОРИЙ)
// Задание по $CAT ID текущей родительской категории
// $CAT: ID категории
// Возвращает сформированный <OPTION>
function create_parent($cat)
{ 	GLOBAL $mysql,$template,$tree,$m;
	// Выбор всех категорий
	$result = $mysql->query("SELECT * FROM ".$tree->table." ORDER BY ".$tree->left);
	if ($mysql->num_rows($result) == 0) return false;
	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
		while ($row = $mysql->fetch_array($result)) {	
			$row=strip($row);
			$mas[]=$row;
		}
		for($i=0;$i<count($mas);$i++) {
				if($cat==$mas[$i][id])$select="selected"; else $select="";
				if(($mas[$i+1]['cat_level']-$mas[$i]['cat_level'])==1) $clas="class='bgparentcat'"; else $clas="";
				$opt.= "<option value='".$mas[$i][id]."' $clas $select>".str_repeat("&nbsp; &nbsp;",	$mas[$i]['cat_level']).$mas[$i][$var];
			}
			
	return $opt;
}

//************************************************************************ 
// Список <OPTION> для выбранной $CAT
// Текущим выбирается родитель $CAT категории
// $CAT: ID категории
// Возвращает массив ARRAY("0" => СПИСОК, "1" => ID родителя);
function get_parent($cat)
{ 	GLOBAL $mysql,$template,$tree;
	// Выбор всех категорий
	$result = $mysql->query("SELECT * FROM ".$tree->table." ORDER BY ".$tree->left);
	// Получение ID родительской категории для $CAT
	$id=$tree->getparent($cat);
	if ($mysql->num_rows($result) == 0) return false;
	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
		while ($row = $mysql->fetch_array($result))
			{	$row=strip($row);
				if($id[id]==$row[id]) $select="selected"; 
					else $select="";
				if($cat <> $row[id])
					$opt.= "<option value='".$row[id]."' $select>".str_repeat("&nbsp; &nbsp;",	$row['cat_level']).$row[$var];
			}
	return array($opt,$id[id]);
}

//************************************************************************ 
// Получение списка <OPTION> категорий на одном уровне
function get_cat_on_level($cat)
{ 	GLOBAL $mysql,$template,$tree;
	$result=$tree->cat_on_level($cat);
	if ($mysql->num_rows($result) == 0) return false;
	//Язык на котором следует вывести названия
	if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
		while ($row = $mysql->fetch_array($result))
			{	$row=strip($row);
				if($cat==$row[id]) { $select="selected"; $name=$row[$var];}
					else $select="";
				$opt.= "<option value='".$row[id]."' $select>".$row[$var];
			}
	return array($opt,$id[id],$name);
}

//************************************************************************ 
// Просмотр категорий и занесение/удаление в/из сессии текущих выбранных категорий
// $CAT: ID категории
// Возвращает таблицу по шаблону КАТЕГОРИЙ
function show_cat($cat=1){
	GLOBAL $template,$mysql,$tree,$m;
		// Получение данных на категорию которую следует закрыть
		$shrink= (int) getfromget("shrink");
		if(empty($cat)) $cat=1;
		// Проверка на существование категории
		if(!$mysql->sql_select("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->id."=$cat") || $mysql->row <> 1) $cat=1;
			else 
				// Если выбрано действие "Свернуть", удалить из сессии выбранную категорию
				if($shrink >=1) $tree->catShrinkCategory($shrink, "category_expand"); 
					// Иначе занести в сессию выбранную категорию
					else $tree->catExpandCategory($cat, "category_expand");
		// Заполнение шаблонной переменной, вывоз функции с передачей созданного запроса и ID категории
		$m[CATEGORY_LIST]=show_result($tree -> get_tree_from_id($cat),$cat);
		return $template->show_content("/exepanel/category_list.tpl");
}

//************************************************************************ 
// Добавление категорий
// $CAT: ID категории куда после которой необходимо сделать вставку
function add_cat($cat) {
	GLOBAL $template,$mysql,$tree,$m;
	// ID категории куда следует вставить новую запись
	$parentid = (int) getfrompost("parentid");
	// Название категорий на Русском и Английском
	$nameru = htmlspecialchars(getfrompost("nameru"));
	$nameen = htmlspecialchars(getfrompost("nameen"));
	// Создание списка всех возможных категорий куда можно включить новую запись
	$m[PARENTNAME]=create_parent($cat);
		// Реакция на добавление записи
		if(getfromget("method")=="add")
			{	//Проверка на существование родительской категории
				if($mysql->sql_select("SELECT * FROM ".$tree->table." WHERE ".$tree->id."=$parentid") && $mysql->row==1) {
					if((!empty($nameru) || !empty($nameen)) && $parentid >= 1)
						{	// Добавление записи
							if(!$tree->insert($parentid, array("sort" => 0, "name" => addslashes($nameru), "name_en" => addslashes($nameen)))) $template->logtxt("_ERR_FIELD",0);
								else $template -> logtxt("_SUCC_ADD",1);
						} else $template->logtxt("_ERR_FIELD",0);
				} else $template->logtxt("_ERR_FIELD",0);
			}
	// Обновление списка категорий куда можно включить новую запись
	$m[PARENTNAME]=create_parent($cat);
	$m[CATEGORYID]=$parentid == 0 ? $cat : $parentid;
	return $template->show_content("/exepanel/category_add.tpl");
}

//************************************************************************ 
// Изменение категории
// $CAT: ID катеории
function edit_cat($cat) {
	GLOBAL $template,$mysql,$tree,$m;
	// Проверка на леквидность ID категории
	$r=$mysql->sql_select("SELECT * FROM ".$tree->table." WHERE ".$tree->id."=$cat");
	//Получение списка OPTION и Родительской категории $CAT
	list($spisok,$pid)=get_parent($cat);
	if($cat > 1 && $r && $mysql->row == 1)
		{	// Отображение значений полей категории которой нужно изменить
			if(getfromget("method")<>"change") {
				$row=$mysql->fetch_array($r);
				$row=strip($row);
				$m[PARENTNAME]=$spisok;
				$m[NAMERU]=$row[name];
				$m[NAMEEN]=$row[name_en];
				$m[ID]=$cat;
			}
			else {
				// Событие на изменение
				$parentid = (int) getfrompost("parentid");
				$nameru = htmlspecialchars(getfrompost("nameru"));
				$nameen = htmlspecialchars(getfrompost("nameen"));
				
				// Обновление категории по ее $CAT ID
				if($tree->update($cat, array("sort" => 0, "name" => addslashes($nameru), "name_en" => addslashes($nameen)))) 
					{
						$template->logtxt("_SUCC_CHANGE",1);
						// Если изменена родительская категория, то переместить категорию
						if($parentid <> $pid && $parentid >= 1) {
							if($mysql->sql_select("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->id."=$parentid") && $mysql->row == 1)
								{	// Перемещение категории
									if($tree->moveAll($cat, $parentid)) $template->logtxt("_CAT_MOVE_SUCCESS",1);
										else $template->logtxt("_ERR_REQUEST",0);
								} else $template->logtxt("_CAT_PARENT_ERROR",0);
						} 
					} else $template->logtxt("_ERR_FIELD",0);
				// Обновление данныз на шаблоне редактировании
				$m[ID]=$cat;
				$m[NAMERU]=stripslashes($nameru);
				$m[NAMEEN]=stripslashes($nameen);
				list($spisok,$pid)=get_parent($cat);
				$m[PARENTNAME]=$spisok;
			}
		} else $template->logtxt("_ERR_FIELD",0);
	
	return $template->show_content("/exepanel/category_edit.tpl");
}

//************************************************************************ 
// Перемещение вверх/вниз категорий товаров в пределе родителя категории
function move_cat($cat)
	{ GLOBAL $template,$mysql,$tree,$m;
			list($spisok,$pid,$name)=get_cat_on_level($cat);
			$m[POSITION_CAT]=$spisok;
			$m["NAME"]=$name;
			$m[ID]=$cat;
		if(getfromget("method") == "change" && $cat > 0)
			{	
				if($tree->ChangePositionAll($cat, (int) getfrompost("to"), $position = getfrompost("after_before")))
					return show_cat($cat);
						else $template->logtxt("_CAT_MOVE_ERROR",0);
			}
		return $template->show_content("/exepanel/category_move.tpl");
	}

//************************************************************************ 
// Удаление категории
// $CAT: ID катеории
function delete_cat($cat) {
	GLOBAL $template,$tree,$mysql;
	// Проверка на леквидность удаляемой категории
	if($cat>1 && $mysql->sql_select("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->id."=$cat") && $mysql->row == 1)
		{	// Получение cat_left и cat_right для категории
			$c=$tree->getElementInfo($cat);
			// Выборка всех потомков для категории
			$r=$mysql->query("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->left.">=$c[0] AND ".$tree->right."<=$c[1] ORDER BY ".$tree->left);
			if($r && $mysql->num_rows($r)>0)
				{	// Сост. запроса  и его выполнение на проверку существования в категориях выбранных товаров 
					while($row=$mysql->fetch_array($r)) $sql.=" OR cat_ID=".$row[id]."";
					$r=$mysql->query("SELECT count(good_ID) AS num FROM goods WHERE cat_ID=$cat ".$sql."");
					$c=$mysql->fetch_array($r);
					// Если в категориях пусто то можно удалять
					if(isset($c[num]) && (int)$c[num]== 0)
						{	// Удаление категории и ее потомков
							if($tree->deleteAll($cat)) $template->logtxt("_SUCC_DELETE",1);
								else $template->logtxt("_ERR_REQUEST",0);
						} else $template->logtxt("_CAT_DELETE_ERROR",0);
				} else $template->logtxt("_ERR_REQUEST",0);
		} else $template->logtxt("_ERR_FIELD",0);
		
		return show_cat();
}

//************************************************************************ 
// Реакция на события, удаление, редактирование и т.д. Вызов функций
function category()
	{ GLOBAL $template,$mysql,$tree,$m;
		$type=getfromget("type");
		$cat=(int) getfromget("cat");
		$id=(int) getfromget("id");
		// Удаление таблицы категорий и создание нововой с ГЛАВНОЙ записью
		//$datacat=array("sort" => 0, "name" => "Главная", "name_en" => "Root");
		//$tree->clear($datacat);
		switch($type){
			case "add": return add_cat($cat);
			case "edit": return edit_cat($cat);
			case "move": return move_cat($cat);
			case "show": return show_cat($cat);
			case "delete": return delete_cat($id);
			default:  return show_cat($cat);
		}
	}
	
//************************************************************************ 
// Проверка на авторизацию
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_CATGOODS"];
		$m["CENTERCONTENT"]=category();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>