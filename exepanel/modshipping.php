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
// Конфигурация видов доставки товаров
function shipping()
	{	GLOBAL $mysql,$template,$m;
		# Добавление 
		if(getfromget("type")=="add")
			{
				$name=addslashes(getfrompost("name"));
				$amount=addslashes(getfrompost("amount"));
				$typ=(int)getfrompost("typ");
				$descr=addslashes(getfrompost("descr"));
				if(!empty($name) && (!empty($amount) || $amount=="0") && ($typ==1 || $typ==0))
					{	$amount=(double)str_replace(",",".",$amount); 
						if($amount>=0) {
							if($mysql->sql_insert("INSERT INTO type_shipping VALUES('','$name','$descr','$typ','$amount')")) $template->logtxt("_SUCC_ADD",1);
								else $template->logtxt("_ERR_REQUEST",0);
						}else $template->logtxt("_SHIPPING_ERR_AMOUNT",0);
					} else $template->logtxt("_ERR_FIELD",0);
			}
		if(getfromget("type")=="edit")
			{	$err=0;
				# Получение уже разобранного двумерного массива с формы
				$result=getarray("shipping", "POST");
				for($i=0;$i<=count($result)-1; $i++)
					{	
					$id=(int)($result[$i][id]);
						if($id >= 1) {
							# Удаление
							if($result[$i][del] > 0) $mysql->sql_delete("DELETE FROM type_shipping WHERE id='$id' LIMIT 1");
								else
								{	# Изменение
									if(!empty($result[$i][name]) && (!empty($result[$i][amount]) || $result[$i][amount]=="0") && ($result[$i][typ]==1 || $result[$i][typ]==0))
										{   $result[$i][amount]=(double) str_replace(",",".",$result[$i][amount]); 
											if(!$mysql->sql_update("UPDATE type_shipping SET name='".$result[$i][name]."', descr='".$result[$i][descr]."', typ='".$result[$i][typ]."', amount='".$result[$i][amount]."' WHERE id='$id' LIMIT 1")) $err++;
										} else $err++;
								}
							 } else $err++;
					}
				if($err==0 && count($result) > 0)$template->logtxt("_SUCC_CHANGE",1); else $template->logtxt("_ERR_NOFULLCHANGE",0);
			}
		# Вывод всех созданных видов доставок
		if($mysql->sql_select("SELECT * FROM type_shipping ORDER BY id DESC") && $mysql->row > 0)
			{	$col=2;$i=0;
				while($res=$mysql->fetcharray())
					{
					$res=strip($res);
					# Определение типа применения платежа
					if($res[typ]=="0") $rd="checked"; else $rd="";
					if($res[typ]=="1") $rc="checked"; else $rc="";
					# Чередование классов цвета
					if(($col % 2)==0) $clas="contlight"; else $clas="contdark";
					$m[SHIPPING_LIST].="
						<tr><input type=hidden name=\"shipping_id_$i\" value=\"$res[id]\">
							<td align=center class=\"$clas\"><input type=text maxlength=50 name=\"shipping_name_$i\" value=\"$res[name]\" size=20 class=\"inputtext\"></td>
							<td align=center class=\"$clas\"><input type=text name=\"shipping_amount_$i\" value=\"$res[amount]\" size=6 class=\"inputtext\"></td>
							<td align=center class=\"$clas\"><input type=radio name=\"shipping_typ_$i\" value=\"0\" $rd class=\"inputtext\"> / <input type=radio name=\"shipping_typ_$i\" value=\"1\" $rc class=\"inputtext\"></td>
							<td align=center class=\"$clas\"><textarea name=\"shipping_descr_$i\" rows=3 cols=30 class=\"inputarea\">$res[descr]</textarea></td>
							<td align=center class=\"$clas\"><input type=checkbox name=\"shipping_del_$i\" value=\"$res[id]\" class=\"inputtext\"></td>
						</tr>
					";
					$col++;
					$i++;
					}
			} else $template->logtxt("_ERR_NOREC",3);
		return $template->show_content("/exepanel/modshipping.tpl");
	}
//************************************************************************ 
// Проверка на авторизацию
if(loginvalid()){
		$m["TOPMENU"]=$template->show_content("/exepanel/topmenu.tpl");
		$m["CURCAT"]=$m["_CP_MODULESHIPPING"];
		$m["CENTERCONTENT"]=shipping();
	} else 
		header("LOCATION: $url/exepanel/");
//************************************************************************ 
// Вывод шаблона
// Ключи для замены в шаблон;
	$m["ERROR_SUCCESS"]=$template->msglog;
	echo $template->show_content("/exepanel/index.tpl");

?>