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
// Создает маленькое изображение
function img_to_default($name) {
	GLOBAL $template;
	// Картинка
	$source="../photo_goods/$name";
if(@file_exists($source)) {
	$sz_source = GetImageSize($source);
	switch($sz_source[mime]){
		case "image/png": $templ=@imagecreatefrompng($source); break;
		case "image/jpeg": $templ=@imagecreatefromjpeg($source); break;
		case "image/gif": $templ=@imagecreatefromgif($source);break;
	}

		$h = 100; 
		if($h > $sz_source[1]) $h = $sz_source[1];
		
		$w = $h*$sz_source[0]/$sz_source[1];
		$img_new = imagecreatetruecolor($w,$h);
		@imagecopyresampled($img_new,$templ,0,0,0,0,$w,$h,$sz_source[0],$sz_source[1]);
		
	switch($sz_source[mime]){
		case "image/png": @imagePNG($img_new,"../photo_goods/mini_$name",80);break;
		case "image/jpeg": @imageJPEG($img_new,"../photo_goods/mini_$name",80);break;
		case "image/gif": @imageGIF($img_new,"../photo_goods/mini_$name",80);break;
	}
	@ImageDestroy($templ);
	@ImageDestroy($img_new);
	}
}
//************************************************************************ 
// Функция удаление, добавление, вывод картинок для товара
function photo_manager($goodid) {
	GLOBAL $template, $m, $mysql;
	$m[ID]=$goodid;
	;
	$imggood = $_FILES['imggood'];
	// Добавление картинки
	if(getfromget("method")=="add" && strlen($imggood['name']) > 4)
		{
			$imggood['name']=tolower($imggood['name']);
			$ext=explode(".", $imggood['name']);
			$ext=$ext[sizeof($ext)-1];
			if($ext=="jpg" || $ext=="gif" || $ext=="png")
				{	$filename="img_".$goodid."_".(mt_rand(888,3987)).".$ext";
					if(move_uploaded_file($imggood['tmp_name'], $template->homedir."/photo_goods/$filename")){
							if($mysql->sql_insert("INSERT INTO photo_goods VALUES('', '$goodid', '0', '$filename')"))
								{
									$template->logtxt("_SUCC_ADD",1);
								} else
									{
										@unlink($template->homedir."/photo_goods/$filename");
										$template->logtxt("_ERR_REQUEST",0);
									}
						} else $template->logtxt("_ERR_REQUEST",0); 

				} else $template->logtxt("_LABEL_IMG_EXT",0);
		}
	// Изменение
	if(getfromget("method")=="change"){
		$mas=getarray("gallery", "POST");
		$err=0;
		$countt =  count($mas);
		for($i = 0; $i < $countt; $i++){	
				$mas[$i][id]=(int)$mas[$i][id];
				$r=$mysql->sql_select("SELECT * FROM photo_goods WHERE photo_ID=".$mas[$i][id]." LIMIT 1");
				if($mysql->row ==1) {
					$row=strip($mysql->fetch_array($r));
					// Удаление изображеня реально
					if((int)$mas[$i][del]==1) {
						$query_del.=" OR photo_ID=".$mas[$i][id];
						@unlink("../photo_goods/".$row[path_to_photo]);
						@unlink("../photo_goods/mini_".$row[path_to_photo]);
						}
						else {
							if( (int)$mas[id][flag]==$mas[$i][id]) {
								$mysql->query("UPDATE photo_goods SET flag='0' WHERE flag = 1 AND good_ID=$goodid");							
								img_to_default($row[path_to_photo]);
								$mysql->query("UPDATE photo_goods SET flag='1' WHERE photo_ID=".$mas[$i][id]." AND good_ID=$goodid LIMIT 1");
								$template->logtxt("_SUCC_CHANGE", 1);
							}
						}
				}
			}
	
			// Удаление из базы
			if(strlen($query_del)>10) $mysql->query("DELETE FROM photo_goods WHERE photo_ID='none' $query_del AND good_ID=$goodid");
	}
	
	// Вывод картинок для товара
	$r=$mysql->query("SELECT * FROM photo_goods WHERE good_ID=$goodid");
	if ($mysql->num_rows($r)>0) {
		$col=2; $i=0;
		while($row=$mysql->fetch_array($r)) {
		$row=strip($row);
		if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
			if($row[flag]==1) $checked="checked"; else $checked="";

			$tr.=<<<EOF
				<tr><input type="hidden" name="gallery_id_$i" value="$row[photo_ID]">
					<td align=center class="$clas" width="180"><a target="_blank" href="{URLSITE}/exepanel/img_gallery.php?img=$row[path_to_photo]&zoom=0" title="{_PHOTO_GALLERY_ZOOM}"><img src="{URLSITE}/exepanel/img_gallery.php?img=$row[path_to_photo]&zoom=1"></td>
					<td align=center class="$clas" width="120"><input type="radio" name="gallery_flag_id" value="$row[photo_ID]" $checked></td>
					<td align=center class="$clas" width="80"><input type="checkbox" name="gallery_del_$i" value="1"</td>
				</tr>	
EOF;
		$col++;
		$i++;
		}
		$m[PHOTO_LIST]=$template->show_contxt($tr);
		$m[BUTTON_CHANGE]=$template->show_contxt("<br><input type='submit' value='{_CP_CHANGE}' class='inputbutton'>");
	}
	return $template->show_content("/exepanel/photo_gallery.tpl");
}

function photo_gallery() {
	GLOBAL $template,$m,$mysql;
	$goodid=(int)getfromget("good");
	if(is_numeric($goodid) && $mysql->sql_select("SELECT good_ID FROM goods WHERE good_ID='$goodid'") && $mysql->row == 1)
		return	photo_manager($goodid);
			else 
				$template->logmsg($template->show_contxt("{_ERR_NOTFOUND_GOOD}<br><a class=err_err_a href='javascript:history.back();'>{_INST_BACK}</a>"), 0);
		
				
}

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
	
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_PHOTO_GALLERY"];
		$m["CENTERCONTENT"]=photo_gallery();
		
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>