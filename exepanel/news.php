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
// Оторбажание списка новостей
// Удаление новости
// Возвращает страницу со списком новостей
function show_news()
	{	GLOBAL $m, $template,$mysql;
		$page=(int) getfromget("page");
		// Ивыборка всех новостей
		$mysql->sql_select("SELECT * FROM news");
		$rows=$mysql->row;
		// Удаление новости
		if(getfromget("type")== "delete"){
			$id=(int)getfromget("id");
			// Проверка на существование новости
			if($mysql->sql_select("SELECT * FROM news WHERE id=$id") && $mysql->row==1) {
			// Удаление нововсти
			if($mysql->sql_delete("DELETE FROM news WHERE id=$id LIMIT 1")) $template->logtxt("_SUCC_DELETE",1); else $template->logtxt("_ERR_REQUEST",0);
			} else $template->logtxt("_ERR_REQUEST",0);
		}
		// Выборка исходя из выбранной страничности
		if($page<=1) $limit="0"; else $limit=ceil($page*10)-10;
		$mysql->sql_select("SELECT * FROM news ORDER BY date DESC LIMIT $limit,10");
		if($mysql->row > 0)
			{	$col=2;
				while($res=$mysql->fetcharray()) 
					{
					$res=strip($res);
					if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
					//Статусы рассылки
					switch($res[subscribe_send]){
						case "0": {$stat="no"; $stat_msg="_NEWS_STATUS0"; break;}
						case "1": {$stat="ok"; $stat_msg="_NEWS_STATUS1"; break;}
						default: {$stat="no"; $stat_msg="_NEWS_STATUS0"; break;}
					}
					$m["LIST_NEWS"].="
					<tr>
						<td align=center class=$clas><span class=text>".date("Y-m-d", strtotime($res[date]))."</span></td>
						<td align=left class=$clas><span class=text><b>RUS: </b> ".htmlspecialchars(substr($res[title], 0,60))."...<br><b>ENG:</b> ".htmlspecialchars(substr($res[title_en], 0,60))."...</span></td>
						<td align=center class=$clas>".$template->show_contxt("<img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/$stat.gif' title='{".$stat_msg."}'>")."</td>
						<td align=center class=$clas>
						".$template->show_contxt("<a href='{URLSITE}/exepanel/news.php?type=edit&id=".$res[id]."&'><img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/edit.gif' title='{_CP_CHANGE}'></a>&nbsp;&nbsp;&nbsp;")."
						".$template->show_contxt("<a href=\"javascript:submiturl('{URLSITE}/exepanel/news.php?type=delete&id=".$res[id]."&', '{_CP_DEL}?')\"><img src='{URLSITE}/templates/{TEMPLDEF}/exepanel/img/delete.gif' title='{_CP_DEL}'>")."
						</td>
					</tr>
					";
					$col++;
					}
					
			} else $template->logtxt("_ERR_NONEWS",3);
	// Вывод страничности
  for ($c = 1; $c <= ceil($rows / 10); $c++) {
  If ($page == $c) {
   $m[PAGES_NEWS].=$template->show_contxt("<a href='{URLSITE}/exepanel/news.php?type=show&page=$c' class='numpagecur'>$c</a>");
  }
  Else {
  if(empty($page)) { $cl="numpagecur"; $page="fdg"; } else $cl="numpage";
   $m[PAGES_NEWS].=$template->show_contxt("<a href='{URLSITE}/exepanel/news.php?type=show&page=$c' class='$cl'><b>$c</a>");
  }
  If ($c <> ceil($rows / 10)) {
   $m[PAGES_NEWS].=" | ";
  }
} 
 If ($rows <= 0) {
  $m[PAGES_NEWS].=$template->show_contxt( "<span class=numpagecur>1</span></b>");
 }
		return $template->show_content("/exepanel/news_list.tpl");
	}
	

//************************************************************************ 
// Добавление в очередь рассылку
function add_queue_msg($row, $titleru, $titleen, $msgru, $msgen, $setting) {
GLOBAL $template, $mysql, $m;
	$sbj_en = (strlen($titleen)>1 ? " (News) ":"");
	$sbj_ru = (strlen($titleru)>1 ? "Новость ":"");
	$mru = (strlen($msgru)>2 ? $msgru:"");
	$men = (strlen($msgen)>2 ? $msgen:"");
	$line = ((strlen($mru)>2 && strlen($men)>2) ? "<br>==================================<br>":"");
	$subject = base64_encode($sbj_ru.$sbj_en);
	$body = "$mru $line $men ";
	// Подпись
	$m[BODYMSG]=str_replace(array('\"', "\'"), array('"',"'"), trim($body));
	$body = base64_encode(nl2br($template->show_contxt(str_replace(array('\"', "\'"), array('"',"'"), trim($setting[sign_msg])))));
	$mysql->query("INSERT INTO email_queue VALUES('', 2, '".$row[mail]."', 'windows-1251', '".$subject."', '".$body."')");
}	
	
//************************************************************************ 
// Редактирование новости
function edit_news()
	{	GLOBAL $m, $mysql,$template;
		$id=(int) getfromget("id");
		$dat=getfrompost("dat");
		$titleru=addslashes(getfrompost("titleru"));
		$titleen=addslashes(getfrompost("titleen"));
		$msgru=addslashes(getfrompost("msgru"));
		$msgen=addslashes(getfrompost("msgen"));
		$subscribe_send=(int)getfrompost("subscribe_send");
		// Провека на существование новости
		if($mysql->sql_select("SELECT * FROM news WHERE id=$id") && $mysql->row==1) {
				$res=$mysql->fetcharray();
				$res=strip($res);
				$m[ID]=$res[id];
				$m[DATE]=date("Y-m-d", strtotime($res[date]));
				$m[TITLERU]=$res[title];
				$m[TITLEEN]=$res[title_en];
				$m[MSGRU]=$res[msg];
				$m[MSGEN]=$res[msg_en];
			if(getfromget("method")!="change") return $template->show_content("/exepanel/news_edit.tpl");
				 else
				 // Редактирование
				{	// Проверка на заполненность полей
					if(!empty($dat) && (!empty($msgru) || !empty($msgen)) && (!empty($titleru) || !empty($titleen)))
						{	
						$da=explode("-",$dat);
						if(!$subscribe_send=="1")$subscribe_send=0;
						
						// Проверка на правильность введенной даты
						if(checkdate($da[1],$da[2],$da[0])){
							$fulldate=$dat." ".date("H:i:s");
							
							//Обновление выбранной новости
							if($mysql->sql_update("UPDATE news SET date='$fulldate', title='$titleru', title_en='$titleen', msg='$msgru', msg_en='$msgen', subscribe_send='$subscribe_send' WHERE id=$id LIMIT 1"))
								{   
									if($subscribe_send=="1") {
											// Выборка подписи
											
											$setting = $mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=2");
											$setting = strip($mysql->fetch_array($setting));
											
											// Добавление в очередь писем для рассылки
											$r=$mysql->query("SELECT name, mail FROM mypurchase ");
											while($row = $mysql->fetch_array($r)) {
												$row=strip($row);
												
												// Добавление в рассылку писем
												add_queue_msg($row, stripslashes($titleru), stripslashes($titleen), stripslashes($msgru), stripslashes($msgen), $setting);
											}
										}
									$template->logtxt("_SUCC_CHANGE",1); 
									return show_news(); 
								} 
								else {
									$template->logtxt("_ERR_REQUEST",0); 
									return $template->show_content("/exepanel/news_edit.tpl"); 
									}
							} else {
							$template->logtxt("_ERR_DATE",0); 
							return $template->show_content("/exepanel/news_edit.tpl");
							}
						} else {
							$template->logtxt("_ERR_FIELD",0); 
							return $template->show_content("/exepanel/news_edit.tpl");
						}
				}
		} else { $template->logtxt("_ERR_REQUEST",0); return show_news(); }	
	}


//************************************************************************ 
#Добавление новости
function add_news()
	{	GLOBAL $m,$mysql,$template;
		$m[DATE]=date("Y-m-d");
		if(getfromget("method")=="add"){
			$dat=getfrompost("dat");
			$titleru=addslashes(getfrompost("titleru"));
			$titleen=addslashes(getfrompost("titleen"));
			$msgru=addslashes(getfrompost("msgru"));
			$msgen=addslashes(getfrompost("msgen"));
			$subscribe_send=(int)getfrompost("subscribe_send");
			$m[DATE]=getfrompost("dat");
			$m[TITLERU]=stripslashes(getfrompost("titleru"));
			$m[TITLEEN]=stripslashes(getfrompost("titleen"));
			$m[MSGRU]=stripslashes(getfrompost("msgru"));
			$m[MSGEN]=stripslashes(getfrompost("msgen"));
			
				// Проверка на заполненность полей
				if(!empty($dat) && (!empty($msgru) || !empty($msgen)) && (!empty($titleru) || !empty($titleen)))
					{	if(!$subscribe_send=="1")$subscribe_send=0;
						$da=explode("-",$dat);
						
						// Проверка правильности введенной даты
						if(checkdate($da[1],$da[2],$da[0]))
							{
								$fulldate=$dat." ".date("H:i:s");
								
								// Добавление новой новости
								if($mysql->sql_insert("INSERT INTO news VALUES('','$fulldate', '$titleru','$titleen','$msgru','$msgen','$subscribe_send')")) 
									{
										if($subscribe_send=="1") {
											// Выборка подписи сообщения
											$setting = $mysql->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=2");
											$setting = strip($mysql->fetch_array($setting));
											
											// Добавление в очередь писем для рассылки
											$r=$mysql->query("SELECT name, mail FROM mypurchase ");
											while($row = $mysql->fetch_array($r)) {
												$row=strip($row);
												
												// Добавление в рассылку писем
												add_queue_msg($row, stripslashes($titleru), stripslashes($titleen), stripslashes($msgru), stripslashes($msgen), $setting);
											}
										}
										$template->logtxt("_SUCC_ADD",1); 
										return show_news();
										
									} else {$template->logtxt("_ERR_REQUEST",0); return $template->show_content("/exepanel/news_add.tpl");}
							} else {$template->logtxt("_ERR_DATE",0); return $template->show_content("/exepanel/news_add.tpl");}
					} else {$template->logtxt("_ERR_FIELD",0);  return $template->show_content("/exepanel/news_add.tpl"); }
		} else return $template->show_content("/exepanel/news_add.tpl");

	}
//************************************************************************ 
//Реакция на события (удаление, показ, редактирование)
function news()
	{	GLOBAL $m;
		
		switch (strtolower(getfromget("type"))){
			case "add":  return add_news(); break;
			case "show": return show_news();break;
			case "edit": return edit_news();break;
			default: return show_news();
			}
	}
//************************************************************************ 
//Проверка на авторизацию
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_NEWS"];
		$m["CENTERCONTENT"]=news();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
//Вывод шаблона
//Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>