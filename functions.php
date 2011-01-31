<?
//************************************************************************
// Класс управление магазином

class shop_function {
	public $db;		// База данных
	public $tpl;	// Шаблонизатор
	private $cfg;	// Настройки с конфига ini
	private $lng;	// Язык перевода

	//************************************************************************
	// Конструктор

	
	public function __construct(&$db, &$tpl) {
		GLOBAL $HTTP_SESSION_VARS;
		$this->db = $db;
		$this->tpl = $tpl;
		if($HTTP_SESSION_VARS['lang']=="en")$this->lng="_en"; else $this->lng="";
		$this->cfg = parse_ini_file("./lib/global/config.ini",true);
	}

	//************************************************************************
	// Создание TITLE,META тэгов для страницы
	// $flag - (0 - данные из config.php, 1 - данные из config.php и товара
	// $goodid - номер товара
	// $infogood - массив информации о товаре, из DB
	public function create_head_html($flag=0,$goodid=0, $infogood=array()) {
		GLOBAL $title,$meta_keywords,$meta_description;
		if($flag == 0) {
			$this->tpl->setVariable("TITLE", $title);
			$this->tpl->setVariable("KEYWORDS", $meta_keywords);
			$this->tpl->setVariable("DESCRIPTION", $meta_description);
			$this->mini_cart();
		}
	}

	//************************************************************************
	// Авторизаци в разделе Мои Покупки
	private function authorization($login, $pwd){
		GLOBAL $HTTP_SESSION_VARS;
		$login = addslashes(htmlspecialchars("$login"));
		$pwd = encryptdata($pwd);

		$HTTP_SESSION_VARS['mypurchase_my']['login']=$login;
		$HTTP_SESSION_VARS['mypurchase_my']['pwd']=$pwd;
		$r = $this->db->query("SELECT * FROM mypurchase WHERE login='$login' AND pwd='$pwd'");
		if($this->db->num_rows($r) == 1) {
			$row = strip($this->db->fetch_array($r));
			if(strcmp($row['login'],$login) == 0 && strcmp($row['pwd'],$pwd)==0) {
				$HTTP_SESSION_VARS['mypurchase']=$row;
			} else return false;
		} else return false;
		return true;
	}

	//************************************************************************
	// Проверка на валидность логина и пароля
	// $block - показывать форму авторизации TRUE, Если False - не отображать блок
	public function loginvalid($block = TRUE) {
		GLOBAL $HTTP_POST_VARS,$HTTP_SESSION_VARS;

		if($HTTP_POST_VARS['getaccess']=="yes" && !empty($HTTP_POST_VARS['username']) && !empty($HTTP_POST_VARS['pwd']))
		$this->authorization($HTTP_POST_VARS['username'], $HTTP_POST_VARS['pwd']);


		if(strlen($HTTP_SESSION_VARS['mypurchase']['pwd'])>1 &&
		strlen($HTTP_SESSION_VARS['mypurchase']['login'])>2) {
			if(strcmp($HTTP_SESSION_VARS['mypurchase']['login'],$HTTP_SESSION_VARS['mypurchase_my']['login'])==0
			&& strcmp($HTTP_SESSION_VARS['mypurchase']['pwd'],$HTTP_SESSION_VARS['mypurchase_my']['pwd'])==0) {
				if($block) {
					$this->tpl->setVariable("LOGIN_NAME", $HTTP_SESSION_VARS['mypurchase']['login'],true);
					$this->tpl->addBlock("auth_success");
				}
				return true;
			} else {
				if($block)$this->tpl->addBlock("auth_fail");
				return false;
			}
		} else {
			if($block)$this->tpl->addBlock("auth_fail");
			return false;
		}
	}

	//************************************************************************
	// Выход
	public function logoff(){
		GLOBAL $HTTP_SESSION_VARS;
		unset($HTTP_SESSION_VARS['mypurchase']); unset($HTTP_SESSION_VARS['mypurchase_my']);
		header("LOCATION: ".$GLOBALS['m']['URLSITE']);
	}

	//************************************************************************
	// Вывод категорий
	// $query - сформированный запрос с категориями
	private function echo_category($query,$cat) {
		GLOBAL $HTTP_SESSION_VARS,$m;
		$result = $this->db->query($query);

		// Проверка на леквидность созданного запроса функцией в классе DBTree -> get_tree_from_id()
		if ($this->db->num_rows($result) == 0) return false;
		if($this->lng == "_en") $var="name_en"; else $var="name";
		$mas=array();

		// Заполнение массива из результата запроса
		while ($row = $this->db->fetch_array($result)) $mas[]=strip($row);

		for($id=0; $id<=count($mas)-1; $id++) {

			// Если элемент содержит потомков
			if(isset($mas[$id][nflag]) && $mas[$id][nflag]) {

				// Проверка на открытость или закрытость папки-картинки
				if(!empty($mas[$id+1][cat_level]) && ($mas[$id+1][cat_level]-$mas[$id][cat_level])==1)
				{
					$folder="open"; $exp="shrink=".$mas[$id][id]."&"; $node="minus";
				} else {
					$folder="closed"; $exp="n_ct=".$mas[$id][id].""; $node="plus";
				}

				// Формирование категорий в шаблон
				$tt=
				"
			<a href='$m[URLSITE]/view_cat.php?$exp'>
			<img src='$m[URLSITE]/templates/$m[TEMPLDEF]/shop/img/node_$node.gif' class='text'></a>
			<img src='$m[URLSITE]/templates/$m[TEMPLDEF]/shop/img/$folder.gif'> 
			<a href='$m[URLSITE]/view_cat.php?$exp' class=text>".$mas[$id][$var]."</a>
			";
			}
			else {
				if($cat==$mas[$id][id])$doc="doc_sel"; else $doc="doc";
				$tt="
					<a href='$m[URLSITE]/view_cat.php?n_ct=".$mas[$id][id]."&' class=text>
					<img src='$m[URLSITE]/templates/$m[TEMPLDEF]/shop/img/$doc.gif'>".$mas[$id][$var]."</a>";
			}

			//Формирование строк в шаблон
			$this->tpl->addBlock("rowcat");
			$this->tpl->setVariable("COL_CAT_NAME", str_repeat("&nbsp; &nbsp; &nbsp;",$mas[$id]['cat_level']-1).$tt,true);

		}
	}

	//************************************************************************
	// Вывод категорий (запоминание открытых)
	// $memory - запоминание открытых категорий (TRUE FALSE)
	public function show_cat($cat=1,$memory=TRUE){
		GLOBAL $template,$tree,$m;
		// Получение данных на категорию которую следует закрыть
		$shrink= (int) getfromget("shrink");
		$cat=(int)$cat;
		if($cat<=0) $cat=1;
		// Проверка на существование категории
		if(!$this->db->sql_select("SELECT ".$tree->id." FROM ".$tree->table." WHERE ".$tree->id."=$cat") || $this->db->row <> 1) $cat=1;
		else

		if($memory) {
			// Если выбрано действие "Свернуть", удалить из сессии выбранную категорию
			if($shrink >=1) $tree->catShrinkCategory($shrink, "category_expand");
			// Иначе занести в сессию выбранную категорию
			else $tree->catExpandCategory($cat, "category_expand");
		}
		else {
			unset($_SESSION['category_expand']);
			$tree->catExpandCategory($cat, "category_expand");
		}
		$this->echo_category($tree -> get_tree_from_id($cat, false, "category_expand"),$cat,$type_link);
	}

	//************************************************************************
	// Составление блока новостей
	public function show_block_news() {
		if($this->cfg['block_news']['enabled']) {
			$r=$this->db->query("
				SELECT * 
				FROM news 
				WHERE TO_DAYS(date) <= TO_DAYS(CURDATE()) 
				ORDER BY date DESC 
				LIMIT ".$this->cfg['block_news']['rows_block']);
			if($this->db->num_rows($r)>0) {
				while($row = $this->db->fetch_array($r)) {
					$row = strip($row);
					$this->tpl->setVariable('ID_NEWS', $row['id'],true);
					$this->tpl->setVariable('DATE_NEWS', date("d.m.Y", strtotime($row['date'])),true);
					$this->tpl->setVariable('TITLE_NEWS', $row['title'.$this->lng],true);
					$this->tpl->addBlock('rownews');
				}
				$this->tpl->addBlock('news');
			}
		}
	}

	// Подробный просмотр новости
	public function show_detail_news($id) {
		$r = $this->db->query("SELECT * FROM news WHERE id = $id AND TO_DAYS(date) <= TO_DAYS(CURDATE())");
		if($this->db->num_rows($r) == 1) {
			$row = strip($this->db->fetch_array($r));
			$this->tpl->setVariable('_DATE_NEWS', date("d.m.Y", strtotime($row['date'.$this->lng])),true);
			$this->tpl->setVariable('_TITLE_NEWS',$row['title'.$this->lng],true);
			$this->tpl->setVariable('_TEXT_NEWS',$row['msg'.$this->lng],true);
			$this->tpl->addBlock('detail_news');
		} else header("LOCATION: ".$GLOBALS['m']['URLSITE']);
	}

	// Вывод всех новостей
	public function show_all_news($page) {
		$page = $page <=0 ? 1: $page;
		if($page<=1) $limit="0"; else $limit=ceil($page*$this->cfg['block_news']['rows_on_page'])-$this->cfg['block_news']['rows_on_page'];
		// Подсчет всех новостей
		$r = $this->db->query("
			SELECT id 
			FROM news 
			WHERE TO_DAYS(date) <= TO_DAYS(CURDATE()) ");
		$rows = $this->db->num_rows($r);
		// Выборка согласно страничности
		$r = $this->db->query("
			SELECT * 
			FROM news 
			WHERE TO_DAYS(date) <= TO_DAYS(CURDATE()) 
			ORDER BY date DESC 
			LIMIT $limit, ".$this->cfg['block_news']['rows_on_page']);

		if($this->db->num_rows($r) > 0) {
			while($row = $this->db->fetch_array($r)) {
				$row = strip($row);
				$this->tpl->setVariable('_DATE_NEWS', date("d.m.Y", strtotime($row['date'])));
				$this->tpl->setVariable('_TITLE_NEWS', $row['title'.$this->lng]);
				$this->tpl->setVariable('_TEXT_NEWS', $row['msg'.$this->lng]);
				$this->tpl->addBlock('all_news');
			}
			$this->tpl->setVariable('NUM_PAGES',$this->pages($page, $rows, $this->cfg['block_news']['rows_on_page'], 5,"news.php?"), true);
			$this->tpl->addBlock('num_pages');
		} else  {
			$this->tpl->setVariable('NUM_PAGES', $GLOBALS['m']['_SHOP_NEWS_NOTFOUND']);
			$this->tpl->addBlock('num_pages');
		}

	}

	//**************************************************************************************
	// Создание нумерации страниц
	// $curpage - текущая страница
	// $total - всего записей
	// $limit - записей на странице
	// $left_right - слева и справа от текущей
	// $url - куда будут добавляться страничность page=3
	public function pages($curpage,$total,$limit,$left_right=5,$url) {
		$n = ceil($total/$limit);
		$start = ($curpage - $left_right) <=0 ? 1 : $curpage - $left_right;
		$end = ($curpage + $left_right) > $n ? $n : $curpage + $left_right;
		$mem = "<b>".$GLOBALS['m']['_SHOP_PAGES']."</b>: ";
		if($start > 1)
		$mem .= "<a href=\"$url"."page=".($start-1)."\"><img src='".$GLOBALS['m']['URLSITE']."/templates/".$GLOBALS['m']['TEMPLDEF']."/shop/img/slash_left.gif'> ...</a>";

		for ($i=$start; $i<=$end; $i++) {
			if($curpage==$i)
			$mem .="&nbsp;<span class='current'>&nbsp;$i&nbsp;</span>";
			else $mem .="&nbsp;&nbsp;<a class='control' href=\"$url"."page=$i\">$i</a>";

		}

		if($end < $n)
		$mem .= "&nbsp;&nbsp;<a href=\"$url"."page=".($end+1)."\">... <img src='".$GLOBALS['m']['URLSITE']."/templates/".$GLOBALS['m']['TEMPLDEF']."/shop/img/slash_right.gif'></a> &nbsp;&nbsp;";

		$mem .= " ".$GLOBALS['m']['_SHOP_PAGES_FROM']." $n";
		return $mem;
	}

	//**************************************************************************************
	// Отображение товаров из спец-предложений
	public function show_special_offer($page) {
		GLOBAL $m;
		if($this->cfg['special_offer']['enabled']) {

			$page = $page <=0 ? 1: $page;
			if($page<=1) $limit="0"; else $limit=ceil($page*$this->cfg['special_offer']['count'])-$this->cfg['special_offer']['count'];
			$r = $this->db->query("
				SELECT *
				FROM goods as g
				WHERE g.spec_offer=1 AND g.disabled=0
				");
			$rows=$this->db->num_rows($r);

			$r = $this->db->query("
				SELECT g.*, pg.path_to_photo, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				LEFT JOIN photo_goods as pg ON (pg.good_ID=g.good_ID AND pg.flag=1)
				WHERE g.spec_offer=1 AND g.disabled=0
				GROUP BY g.good_ID
				LIMIT $limit,".$this->cfg['special_offer']['count']
				);

				$i=1;
				while($row = $this->db->fetch_array($r)) {
					if($row['rate_skidka'] >= 1) $row['price_new'] = (double) ($row['price']-$row['price']/100*$row['rate_skidka']);
					$goods[$i] = strip($row);$i++;
				}

				$ch=$this->db->num_rows($r)/$this->cfg['special_offer']['cols'];
				$ch=explode(".", $ch);
				$g=false;
				$j=1;
				//Вывод полностью заполненной строки
				if($ch[0]>0) {
					while($g<>true) {
						for($i=1;$i<=$this->cfg['special_offer']['cols'];$i++) {

							//Вывод цены со скидкой или без.
							$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
							if(!EMPTY($goods[$j+$i-1]['price_new'])) {
								$this->tpl->setVariable('NEWPRICE_GOOD'.$i, $goods[$j+$i-1]['price_new']);
								$this->tpl->addBlock('price_skidka'.$i);
							} else $this->tpl->addBlock('price_noskidka'.$i);

							$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

							//Вывод кнопки Добавить в корзину
							if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
							|| $goods[$j+$i-1]['kolvo'] > 0)
							$this->tpl->addBlock('buybtn'.$i);
							else {
								$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
								$this->tpl->addBlock('buybtn'.$i.'_not');
							}

							if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
							$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['special_offer']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
							else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

							$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);
							$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);

							$this->tpl->addBlock('photo'.$i);
							$this->tpl->addBlock('title'.$i);
							$this->tpl->addBlock('action'.$i);





						}

						$j=$j+$this->cfg['special_offer']['cols'];
						$this->tpl->addBlock('special_offer_row');
						if(($j-1)==($ch[0]*$this->cfg['special_offer']['cols']))$g=true;
					}

				}

				//Вывод той строки, которая не доконца заполнена
				if($ch[1]>0){
					for($i=1;$i<=$this->cfg['special_offer']['cols'];$i++) {
						if($goods[$j+$i-1]['price'] > 0){

							//Вывод цены со скидкой или без.
							$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
							if(!EMPTY($goods[$j+$i-1]['price_new'])) {
								$this->tpl->setVariable('NEWPRICE_GOOD'.$i, $goods[$j+$i-1]['price_new']);
								$this->tpl->addBlock('price_skidka'.$i);
							} else $this->tpl->addBlock('price_noskidka'.$i);

							$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

							//Вывод кнопки Добавить в корзину
							if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
							|| $goods[$j+$i-1]['kolvo'] > 0)
							$this->tpl->addBlock('buybtn'.$i);
							else {
								$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
								$this->tpl->addBlock('buybtn'.$i.'_not');
							}


							if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
							$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['special_offer']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
							else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

							$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);

							$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);
							$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);
							$this->tpl->addBlock('photo'.$i);
							$this->tpl->addBlock('title'.$i);
							$this->tpl->addBlock('action'.$i);
						}
					}
					$this->tpl->addBlock('special_offer_row');
				}
				$this->tpl->setVariable('NUM_PAGES',$this->pages($page, $rows, $this->cfg['special_offer']['count'], 5,"index.php?"), true);
				if($rows > 0)
				$this->tpl->addBlock('special_offer');
		}
	}

	//**************************************************************************************
	// Отображение случайных товаров
	public function show_random_goods() {
		GLOBAL $m;
		if($this->cfg['random_goods']['enabled']){
			$goods = array();

			$r = $this->db->query("
				SELECT g.*, pg.path_to_photo, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID  AND gs.status = 0)
				LEFT JOIN photo_goods as pg ON (pg.good_ID=g.good_ID AND pg.flag=1)
				WHERE g.spec_offer=1 AND g.disabled=0
				GROUP BY g.good_ID
				ORDER BY RAND()
				LIMIT ".$this->cfg['random_goods']['count']
				);
				$rows=$this->db->num_rows($r);

				$i=1;
				while($row = $this->db->fetch_array($r)) {
					if($row['rate_skidka'] >= 1) $row['price_new'] = (double) ($row['price']-$row['price']/100*$row['rate_skidka']);
					$goods[$i] = strip($row);$i++;
				}

				$ch=$this->db->num_rows($r)/$this->cfg['random_goods']['cols'];
				$ch=explode(".", $ch);
				$g=false;
				$j=1;
				//Вывод полностью заполненной строки
				if($ch[0]>0) {
					while($g<>true) {
						for($i=1;$i<=$this->cfg['random_goods']['cols'];$i++) {

							//Вывод цены со скидкой или без.
							$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
							if(!EMPTY($goods[$j+$i-1]['price_new'])) {
								$this->tpl->setVariable('NEWPRICE_GOOD'.$i, $goods[$j+$i-1]['price_new']);
								$this->tpl->addBlock('rprice_skidka'.$i);
							} else $this->tpl->addBlock('rprice_noskidka'.$i);

							$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

							if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
							|| $goods[$j+$i-1]['kolvo'] > 0)
							$this->tpl->addBlock('rbuybtn'.$i);
							else {
								$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
								$this->tpl->addBlock('rbuybtn'.$i.'_not');
							}

							if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
							$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['random_goods']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
							else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

							$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);
							$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);

							$this->tpl->addBlock('rphoto'.$i);
							$this->tpl->addBlock('rtitle'.$i);
							$this->tpl->addBlock('raction'.$i);

						}

						$j=$j+$this->cfg['random_goods']['cols'];
						$this->tpl->addBlock('random_goods_row');
						if(($j-1)==($ch[0]*$this->cfg['random_goods']['cols']))$g=true;
					}

				}

				//Вывод той строки, которая не доконца заполнена
				if($ch[1]>0){
					for($i=1;$i<=$this->cfg['random_goods']['cols'];$i++) {
						if($goods[$j+$i-1]['price'] > 0){
							if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
							$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['random_goods']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
							else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

							$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);

							$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);
							$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
							$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

							//Вывод кнопки Добавить в корзину
							if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
							|| $goods[$j+$i-1]['kolvo'] > 0)
							$this->tpl->addBlock('rbuybtn'.$i);
							else {
								$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
								$this->tpl->addBlock('rbuybtn'.$i.'_not');
							}

							$this->tpl->addBlock('rphoto'.$i);
							$this->tpl->addBlock('rtitle'.$i);
							$this->tpl->addBlock('raction'.$i);
						}
					}
					$this->tpl->addBlock('random_goods_row');
				}

				if($rows > 0)
				$this->tpl->addBlock('random_goods');
		}
	}

	//**************************************************************************************
	// Отправка сообщения из раздела ОБРАТНАЯ СВЯЗЬ
	public  function send_feedback() {
		GLOBAL $m,$HTTP_SESSION_VARS;

		//Настройки тикет системы
		$setting_t = $this->db->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=1");
		$setting_t = strip($this->db->fetch_array($setting_t));

		// Выборка настроек подключения SMTP
		$setting = $this->db->query("SELECT * FROM ticketsystem_setting WHERE tpl_ID=2");
		$setting = strip($this->db->fetch_array($setting));

		if(getfrompost("send") == "yes") {

			$email = getfrompost("email");
			$subject = getfrompost("subject");
			$msg = getfrompost("msg");
			$capcha = (int) getfrompost("capcha");

			$this->tpl->setVariable('VALUE_EMAIL', getfrompost("email"));
			$this->tpl->setVariable('VALUE_SUBJECT', getfrompost("subject"));
			$this->tpl->setVariable('VALUE_MSG', getfrompost("msg"));

			if(is_email($email) && !EMPTY($msg) && !EMPTY($capcha)) {
				if(strlen($capcha) == 6 && strcmp(getfromsess('feedback_img'),$capcha)==0) {

					$subject = "FEEDBACK:".strip_tags($subject);
					$ip = $_SERVER['REMOTE_ADDR'];
					$body = nl2br("Sender: $email<br>IP: $ip<br><br>".strip_tags($msg));

					$smtp = new smtp($setting[smtp_server],$setting[smtp_port], $setting[email], $setting[name], $setting[login], decryptdata($setting[pwd]));
					$data = $smtp->create_header($setting[email], $subject, $body, "windows-1251");
					if($smtp->connect()) {
						if($smtp->send($setting['email'], $data))
						$this->tpl->setVariable('INFOMSG', $m['_SHOP_FEEDBACK_SENDSUCCESS']);
						else
						$this->tpl->setVariable('INFOMSG', $m['_SHOP_MAIL_SERVER_ERROR']);
					} else $this->tpl->setVariable('INFOMSG', $m['_SHOP_MAIL_SERVER_ERROR']);
					$smtp->smtp_close();

					$this->tpl->addBlock('infomsg');
					$HTTP_SESSION_VARS['feedback_img']='';
				} else {
					$this->tpl->setVariable('INFOMSG', $m['_ERR_IMG']);
					$this->tpl->addBlock('infomsg');
					$this->tpl->addBlock('formsend');
				}
			} else {
				$this->tpl->setVariable('INFOMSG', $m['_SHOP_REQUIRED_FIELD_ERROR']);
				$this->tpl->addBlock('infomsg');
				$this->tpl->addBlock('formsend');
			}
		} else $this->tpl->addBlock('formsend');

		$arr = explode('@', $setting_t[email]);
		$this->tpl->setVariable('mail_ticket_part_1', $arr[0]);
		$this->tpl->setVariable('mail_ticket_part_2', $arr[1]);
	}

	//**************************************************************************************
	// Вывод положения товара в категориях
	// Возвращает массив
	private function navigator($cat=1) {
		GLOBAL $tree;
		$r=$tree->enumPath2($cat);
		if($_SESSION['lang']=="en")$var="name_en"; else $var="name";
		$i=0;
		while ($row = $this->db->fetch_array($r)) {
			if ($cat <> $row['id']) {
				$navigator[$i]['name']=$row[$var];
				$navigator[$i]['id']=$row['id'];
			} else {
				$navigator[$i]['name']=$row[$var];
				$navigator[$i]['id']=$row['id'];
			}
			$i++;
		}
		return $navigator;
	}

	//**************************************************************************************
	// Подробный просмотр товара
	public function view_good($goodid) {
		GLOBAL $m, $mini_title;
		$r = $this->db->query("
				SELECT g.*,  count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				WHERE g.good_ID=$goodid AND g.disabled=0
				GROUP BY g.good_ID
				LIMIT 1
				");
		if($this->db->num_rows($r) == 1) {
			$row = strip($this->db->fetch_array($r));

			$this->tpl->setVariable('IDGOOD', $goodid);

			//Вывод категорий
			$array_cat = $this->navigator($row['cat_ID']);
			for($i=0;$i<count($array_cat);$i++)
			if($i != count($array_cat)-1) {
				$this->tpl->setVariable('CAT_NAME', $array_cat[$i]['name']);
				$this->tpl->setVariable('CAT_ID', $array_cat[$i]['id']);
				$this->tpl->addBlock('catfolder');
			} else {
				$this->tpl->setVariable('CAT_NAME', $array_cat[$i]['name']);
				$this->tpl->setVariable('CAT_ID', $array_cat[$i]['id']);
				$this->tpl->addBlock('catdoc');
			}

			$this->tpl->setVariable('NAME', $row['title'.$this->lng]);

			$this->tpl->setVariable('PRICE', $row['price']);
			if($row['rate_skidka']>=1){
				$this->tpl->setVariable('SKIDKA', $row['rate_skidka']);
				$this->tpl->addBlock('skidka');
			}
			if($row['rate_agent']>=1){
				$this->tpl->setVariable('AGENT', $row['rate_agent']);
				$this->tpl->addBlock('agent');
			}
			if($row['prop_good'] <> 2){
				$this->tpl->setVariable('DATE', date("d.m.Y",strtotime($row['dateupload'])));
				$this->tpl->addBlock('date');
			}

			// Замена TITLE, META страницы
			$this->tpl->setVariable('TITLE', $mini_title." ".$row['title'.$this->lng]);
			$this->tpl->setVariable('KEYWORDS', $row['meta_key']);
			$this->tpl->setVariable('DESCRIPTION', $row['meta_desc']);

			// Выборка фоток
			$ph = $this->db->query("SELECT path_to_photo FROM photo_goods WHERE good_ID=".$goodid);
			if($this->db->num_rows($ph) > 0) {
				$this->tpl->setVariable('height', $this->cfg['detail_good']['height']);
				while($rws = $this->db->fetch_array($ph)) {
					$this->tpl->setVariable('PHOTO_IMAGE', $rws['path_to_photo']);
					$this->tpl->addBlock('photo_img');
				}
				$this->tpl->addBlock('photo');
			}

			$this->tpl->setVariable('DESCR', $row['descr'.$this->lng]);
			$this->tpl->setVariable('ADDITIONAL', $row['additional'.$this->lng]);

			// Отображение кнопки ДОБАВИТЬ В КОРЗИНУ
			if(($row['prop_good'] == 2 && $row['sklad'] > 0)|| $row['kolvo'] > 0)
			$this->tpl->addBlock('buybtn');
			else {
				$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
				$this->tpl->addBlock('buybtn_not');
			}

			$this->show_cat($row['cat_ID'],false);
		} else header("LOCATION: ".$m['URLSITE']);
	}

	//**************************************************************************************
	// Возвращает 1 - в категории есть потомки, 0 - нет потомков
	private function check_sub_cat($cat) {
		$r = $this->db->query("SELECT *, IF (cat_left+1 < cat_right, 1, 0) AS nflag
						FROM category 
						WHERE id = $cat");
		$row =  $this->db->fetch_array($r);
		if($this->db->num_rows($r) == 0) return -1;
		return $row['nflag'];
	}

	//**************************************************************************************
	// Вывод товаров в категории
	public function view_cat($cat,$page) {
		GLOBAL $m;
		$default = "spec_offer";
		$page = $page <=0 ? 1: $page;
		if($page<=1) $limit="0"; else $limit=ceil($page*$this->cfg['view_goods_cat']['count'])-$this->cfg['view_goods_cat']['count'];

		if($this->check_sub_cat($cat) == 0 ) $_SESSION['mem_cat'] = $cat;
		if($this->check_sub_cat($cat) < -1) header("LOCATION: ".$m['URLSITE']);

		if($_SESSION['mem_cat'] > 0) {
			$r = $this->db->query("
				SELECT *
				FROM goods as g
				WHERE g.disabled=0 AND g.cat_ID =".$_SESSION['mem_cat']);
			$rows=$this->db->num_rows($r);
			$r = $this->db->query("
				SELECT g.*, pg.path_to_photo, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				LEFT JOIN photo_goods as pg ON (pg.good_ID=g.good_ID AND pg.flag=1)
				WHERE g.disabled=0 AND g.cat_ID = ".$_SESSION['mem_cat']."
				GROUP BY g.good_ID
				LIMIT $limit,".$this->cfg['view_goods_cat']['count']
				);
		} else {
			$r = $this->db->query("
				SELECT *
				FROM goods as g
				WHERE g.spec_offer=1 AND g.disabled=0
				");
			$rows=$this->db->num_rows($r);
			$r = $this->db->query("
				SELECT g.*, pg.path_to_photo, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				LEFT JOIN photo_goods as pg ON (pg.good_ID=g.good_ID AND pg.flag=1)
				WHERE g.spec_offer=1 AND g.disabled=0
				GROUP BY g.good_ID
				LIMIT $limit,".$this->cfg['special_offer']['count']
				);
		}



		$i=1;
		while($row = $this->db->fetch_array($r)) {
			if($row['rate_skidka'] >= 1) $row['price_new'] = (double) ($row['price']-$row['price']/100*$row['rate_skidka']);
			$goods[$i] = strip($row);$i++;
		}


		$ch=$this->db->num_rows($r)/$this->cfg['view_goods_cat']['cols'];
		$ch=explode(".", $ch);
		$g=false;
		$j=1;
		//Вывод полностью заполненной строки
		if($ch[0]>0) {
			while($g<>true) {
				for($i=1;$i<=$this->cfg['view_goods_cat']['cols'];$i++) {

					//Вывод цены со скидкой или без.
					$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
					if(!EMPTY($goods[$j+$i-1]['price_new'])) {
						$this->tpl->setVariable('NEWPRICE_GOOD'.$i, $goods[$j+$i-1]['price_new']);
						$this->tpl->addBlock('price_skidka'.$i);
					} else $this->tpl->addBlock('price_noskidka'.$i);

					$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

					//Вывод кнопки Добавить в корзину
					if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
					|| $goods[$j+$i-1]['kolvo'] > 0)
					$this->tpl->addBlock('buybtn'.$i);
					else {
						$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
						$this->tpl->addBlock('buybtn'.$i.'_not');
					}

					if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
					$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['view_goods_cat']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
					else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

					$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);
					$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);

					$this->tpl->addBlock('photo'.$i);
					$this->tpl->addBlock('title'.$i);
					$this->tpl->addBlock('action'.$i);

				}

				$j=$j+$this->cfg['view_goods_cat']['cols'];
				$this->tpl->addBlock('goods_row');
				if(($j-1)==($ch[0]*$this->cfg['view_goods_cat']['cols']))$g=true;
			}

		}

		//Вывод той строки, которая не доконца заполнена
		if($ch[1]>0){
			for($i=1;$i<=$this->cfg['view_goods_cat']['cols'];$i++) {
				if($goods[$j+$i-1]['price'] > 0){

					//Вывод цены со скидкой или без.
					$this->tpl->setVariable('PRICE_GOOD'.$i, $goods[$j+$i-1]['price'],true);
					if(!EMPTY($goods[$j+$i-1]['price_new'])) {
						$this->tpl->setVariable('NEWPRICE_GOOD'.$i, $goods[$j+$i-1]['price_new']);
						$this->tpl->addBlock('price_skidka'.$i);
					} else $this->tpl->addBlock('price_noskidka'.$i);

					$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);

					//Вывод кнопки Добавить в корзину
					if(($goods[$j+$i-1]['prop_good'] == 2 && $goods[$j+$i-1]['sklad'] > 0)
					|| $goods[$j+$i-1]['kolvo'] > 0)
					$this->tpl->addBlock('buybtn'.$i);
					else {
						$this->tpl->setVariable('SOLD_OUT', $m['_SHOP_SOLD_OUT'], true);
						$this->tpl->addBlock('buybtn'.$i.'_not');
					}


					if(file_exists('photo_goods/mini_'.$goods[$j+$i-1]['path_to_photo']))
					$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$goods[$j+$i-1]['path_to_photo']."&h=".$this->cfg['view_goods_cat']['height']."' title='".$goods[$j+$i-1]['title'.$this->lng]."'>";
					else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";

					$this->tpl->setVariable('PHOTO_GOOD'.$i, $img, true);

					$this->tpl->setVariable('TITLE_GOOD'.$i, $goods[$j+$i-1]['title'.$this->lng],true);
					$this->tpl->setVariable('ID_GOOD'.$i, $goods[$j+$i-1]['good_ID'],true);
					$this->tpl->addBlock('photo'.$i);
					$this->tpl->addBlock('title'.$i);
					$this->tpl->addBlock('action'.$i);
				}
			}
			$this->tpl->addBlock('goods_row');
		}

		if($rows > 0) {
			//Вывод категорий
			$array_cat = $this->navigator($_SESSION['mem_cat']);
			for($i=0;$i<count($array_cat);$i++)
			if($i != count($array_cat)-1) {
				$this->tpl->setVariable('CAT_NAME', $array_cat[$i]['name']);
				$this->tpl->setVariable('CAT_ID', $array_cat[$i]['id']);
				$this->tpl->addBlock('catfolder');
			} else {
				$this->tpl->setVariable('CAT_NAME', $array_cat[$i]['name']);
				$this->tpl->setVariable('CAT_ID', $array_cat[$i]['id']);
				$this->tpl->addBlock('catdoc');
			}
			$this->tpl->addBlock('cat_path');

			$this->tpl->setVariable('NUM_PAGES',$this->pages($page, $rows, $this->cfg['view_goods_cat']['count'], 5,"view_cat.php?n_ct=$cat&"), true);
			$this->tpl->addBlock('num_pages');
		} else
		$this->tpl->addBlock('goods_not_found');


	}

	//**************************************************************************************
	// ПОИСК ТОВАРОВ
	public function search_goods($page) {
		global  $m;
		// Опция дополнительного поиска
		switch((int)getfromget('opt_search')) {
			case 1: $type=1;break;
			default: $type=0;
		}

		$q = encode_form(strip_tags(getfromget('q')));
		$this->tpl->setVariable('QUERY_VALUE', $q);
		if(strlen($q) > 0) $this->tpl->setVariable('QUERY', $q);
		else {
			$this->tpl->setVariable('QUERY', $m['_SHOP_SEARCH_QUERY_NOTFOUND']);
			return false;
		}

		$search_in = (int)getfromget('search_in');
		$search_with = (double)getfromget('search_with'); $this->tpl->setVariable('search_with', $search_with);
		$search_to = (double)getfromget('search_to'); $this->tpl->setVariable('search_to', $search_to);

		$page = $page <=0 ? 1: $page;
		if($page<=1) $limit="0"; else $limit=ceil($page*$this->cfg['search_goods']['count'])-$this->cfg['search_goods']['count'];

		//Исключения из запроса
		$mas_except=array("-","+",",","=","*","#","@","/","$",'"',"'");
		foreach($mas_except as $expr) $query=str_replace($expr, " ", $q);

		$query = str_ireplace('  ', ' ', $query);
		$mas_query = explode(' ', $query);

		if($type == 1) {
			if($search_with > 0) $pricezap = " AND g.price >= $search_with";
			if($search_to > 0) $pricezap =" AND g.price <= $search_to";
			if($search_to > 0 && $search_with > 0) $pricezap = " AND (g.price >= $search_with AND g.price <= $search_to)";

		}

		//Формирование запроса
		if(count($mas_query)>=2)
		for($i=0; $i<=count($mas_query)-2; $i++)
		if($search_in == 2 && $type==1)
		$zap.="((g.title LIKE '%{$mas_query[$i]}%') OR (g.title_en LIKE '%{$mas_query[$i]}%')) OR
						((g.descr LIKE '%{$mas_query[$i]}%') OR (g.descr_en LIKE '%{$mas_query[$i]}%')) OR";
		else
		$zap.="((g.title LIKE '%{$mas_query[$i]}%') OR (g.title_en LIKE '%{$mas_query[$i]}%')) OR ";

		$last = count($mas_query)-1;
		if($search_in == 2 && $type==1)
		$zap.="((g.title LIKE '%{$mas_query[$last]}%') OR (g.title_en LIKE '%{$mas_query[$last]}%')) OR
				((g.descr LIKE '%{$mas_query[$last]}%') OR (g.descr_en LIKE '%{$mas_query[$last]}%'))
				OR (g.title LIKE '%$query%' OR g.title_en LIKE '%$query%')";
		else
		$zap.="((g.title LIKE '%{$mas_query[$last]}%') OR (g.title_en LIKE '%{$mas_query[$last]}%'))
					OR (g.title LIKE '%$query%' OR g.title_en LIKE '%$query%')";

		$zap="(".$zap.") AND disabled=0 $pricezap";


		$r = $this->db->query("
				SELECT count(*) as kolvo 
				FROM goods as g
				WHERE $zap AND g.disabled=0
				");

		$row = $this->db->fetch_array($r);
		$rows = $row['kolvo'];
		$this->tpl->setVariable('FOUND', $rows);

		$r = $this->db->query("
				SELECT g.*, pg.path_to_photo, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				LEFT JOIN photo_goods as pg ON (pg.good_ID=g.good_ID AND pg.flag=1)
				WHERE $zap AND g.disabled=0
				GROUP BY g.good_ID
				LIMIT $limit,".$this->cfg['search_goods']['count']
				);


				while($row = $this->db->fetch_array($r)) {
					$row = strip($row);

					//Скидка
					if($row['rate_skidka'] >= 1) $row['price'] = $row['price']-$row['price']/100*$row['rate_skidka'];

					$this->tpl->setVariable('TITLE_GOOD', $row['title'.$this->lng]);
					$this->tpl->setVariable('PRICE_GOOD', $row['price']);
					$this->tpl->setVariable('IDGOOD', $row['good_ID']);

					// Фото
					if(file_exists('photo_goods/mini_'.$row['path_to_photo']))
					$img = "<img src='".$m[URLSITE]."/img.php?img=mini_".$row['path_to_photo']."&h=".$this->cfg['search_goods']['height']."' title='".$row['title'.$this->lng]."'>";
					else $img = "<img src='".$m[URLSITE]."/templates/".$m[TEMPLDEF]."/shop/img/no_image_search.gif' title='".$m['_SHOP_IMG_NOTFOUND']."'>";
					$this->tpl->setVariable('PHOTO_GOOD', $img);

					$this->tpl->addBlock('search_row');
				}

				if($this->db->num_rows($r) == 0) {

				} else {
					$this->tpl->setVariable('NUM_PAGES',$this->pages($page, $rows, $this->cfg['search_goods']['count'], 5,"search.php?opt_search=$type&q=".getfromget('q')."&search_in=".$search_in."&search_with=".$search_with."&search_to=".$search_to), true);
					$this->tpl->addBlock('num_pages');
					$this->tpl->addBlock('search_head');
				}

	}


	//**************************************************************************************
	// Регистрация нового пользователя

	private function register_form_show($mas = array()) {
		$r = $this->db->query("SELECT * FROM mypurchase_reg_fields ORDER BY sort");
		if($this->db->num_rows($r) > 0) {
			while($row = $this->db->fetch_array($r)) {
				$row = strip($row);
				if($row['type_fields_id'] == 1) {
					$this->tpl->setVariable('ADDITIONAL_NAME', $row['name'.$this->lng]);
					$this->tpl->setVariable('VALUE_FIELD', $mas[$row['reg_field_ID']]['field']);
					$this->tpl->setVariable('NAME_FIELD', "additional_field_$row[reg_field_ID]");
					$this->tpl->addBlock('type1');
				}
				if($row['type_fields_id'] == 3) {
					$this->tpl->setVariable('ADDITIONAL_NAME',$row['name'.$this->lng]);
					$this->tpl->setVariable('VALUE_FIELD', $mas[$row['reg_field_ID']]['field']);
					$this->tpl->setVariable('NAME_FIELD', "additional_field_$row[reg_field_ID]");
					$this->tpl->addBlock('type3');
				}
			}
			$this->tpl->setVariable('STEP_NUM', '4');
			$this->tpl->addBlock('additional_fields');
		} else $this->tpl->setVariable('STEP_NUM', '3');
		$this->tpl->addBlock('register_form');
	}

	public function register_user() {
		GLOBAL $m;
		$reg_status = getfrompost("reg");
		if($reg_status == "yes") {

			$login = addslashes(strip_tags(getfrompost("login")));
			$pwd1 = addslashes(strip_tags(getfrompost("pwd1")));
			$pwd2 = addslashes(strip_tags(getfrompost("pwd2")));
			$name = addslashes(strip_tags(getfrompost("name")));
			$email = addslashes(strip_tags(getfrompost("email")));
			$agreement = getfrompost("agreement");
			$capcha = getfrompost("capcha");

			if(strlen($capcha) <> 6 || $capcha <> getfromsess("register_img")) $err .= "$m[_ERR_IMG]<br>";
			if(strlen($login) < 4) $err .= "$m[_SHOP_REGISTER_LOGIN_EMPTY]<br>";
			if(strlen($pwd1) < 6) $err .= "$m[_SHOP_REGISTER_PWD_EMPTY]<br>";
			if($pwd1 <> $pwd2) $err .= "$m[_SHOP_REGISTER_PWD_ERROR]<br>";
			if(strlen($name) < 4) $err .= "$m[_SHOP_REGISTER_NAME_EMPTY]<br>";
			if(!is_email($email)) $err .= "$m[_SHOP_REGISTER_EMAIL_ERROR]<br>";
			if($agreement <> "yes")  $err .= "$m[_SHOP_REGISTER_AGREEMENT_DECLINE]<br>";

			$_SESSION['register_img'] = '0';

			$mas = getarray("additional", "POST");

			$check = $this->db->query("SELECT login FROM mypurchase WHERE login='$login'");
			if($this->db->num_rows($check) > 0) $err .="$m[_SHOP_REGISTER_LOGIN] \"$login\" $m[_SHOP_REGISTER_LOGIN_EXISTS]<br>";

			// Если нет ошибок
			if(strlen($err) == 0) {

				//Добавление покупателя в главную таблицу
				$this->db->query("INSERT INTO mypurchase
							VALUES('', '$login', '".encryptdata($pwd1)."', '$name', '$email', '0.00', '', '0', '1')");
				$id = $this->db->insert_id();

				//Добавление дополнительной информации о покупателе
				if(count($mas) > 0 && $id > 0) {
					foreach($mas as $key=>$value)
					foreach ($value as $keykey => $val){
						$key = (int) $key;
						$val = addslashes($val);
						$r = $this->db->query("SELECT reg_field_ID FROM mypurchase_reg_fields WHERE reg_field_ID = ".$key);
						if($r && $this->db->num_rows($r) == 1)
						$this->db->query("INSERT INTO mypurchase_reg_fields_value VALUES('$key', '$id', '$val')");
					}
				}

				$this->tpl->setVariable('info_message2', $m['_SHOP_REGISTER_SUCCESS']);
				$this->tpl->addBlock('info_message2');

			} else {

				$this->tpl->setVariable('LOGIN', $login);
				$this->tpl->setVariable('PWD1', $pwd1);
				$this->tpl->setVariable('PWD2', $pwd2);
				$this->tpl->setVariable('NAME', $name);
				$this->tpl->setVariable('EMAIL', $email);

				$this->tpl->setVariable('info_message', $err);
				$this->tpl->addBlock('info_message');
				$this->register_form_show($mas);
			}

		} else $this->register_form_show();

	}


	//**************************************************************************************
	// Управление корзиной

	// Проверка существование товара
	private function exist_good($goodid) {
		if($goodid > 0) {
			$r = $this->db->query("
				SELECT g.*, count(gs.id_good) as kolvo
				FROM goods as g
				LEFT JOIN goods_secret AS gs ON (gs.id_good = g.good_ID AND gs.status = 0)
				WHERE g.disabled=0 AND g.good_ID= $goodid
				GROUP BY g.good_ID"
				);
				if($this->db->num_rows($r) == 1) {
					$row = strip($this->db->fetch_array($r));
					if($row['prop_good'] == 2 && $row['sklad'] > 0) return array($goodid, $row['sklad'], $row['prop_good'], $row['price'], $row['rate_skidka'], $row['title'], $row['title_en']);
					if($row['kolvo'] >= 1) return array($goodid, $row['kolvo'], $row['prop_good'], $row['price'], $row['rate_skidka'], $row['title'], $row['title_en']);
					return false;
				} else return false;
		} else return false;
	}

	//Удаление из карзины
	private function del_from_cart($goodid) {
		foreach($_SESSION['cart_goods_base'] as $key => $val)
		if($_SESSION['cart_goods_base'][$key]['goodid'] == $goodid) {
			unset($_SESSION['cart_goods_base'][$key]);
		}
	}

	//Мини корзина (кол-во товаров, сумма)
	public  function mini_cart() {
		$cart = $_SESSION['cart_goods_base'];
		$total_count=0;
		$total_price=0;
		if(count($cart) > 0) {
			foreach($cart as $key => $val) {
				$amount = (double) ($cart[$key]['price']-$cart[$key]['price']/100*$cart[$key]['rate_skidka'])*$cart[$key]['kolvo'];
				$total_count += $cart[$key]['kolvo'];
				$total_price += $amount;
			}
		}
		$this->tpl->setVariable('CART_TOTAL_COUNT', $total_count);
		$this->tpl->setVariable('CART_TOTAL_PRICE', $total_price);
	}

	// Корзина
	public function cart() {
		GLOBAL $m;
		$action = getfrompost("action");
		$goodid = (int) getfrompost("goodid");

		switch ($action) {

			//Добавление товара в корзину
			case "add": {
				$status = $this->exist_good($goodid);
				if($status == true || is_array($status)) {
					list($a['goodid'], $a['maximum'], $a['typegood'], $a['price'], $a['rate_skidka'], $a['title'], $a['title_en']) = $status;
					if(count($_SESSION['cart_goods_base']) > 0) {
						foreach($_SESSION['cart_goods_base'] as $key => $val) {
							if($_SESSION['cart_goods_base'][$key]['goodid'] == $goodid) {
								if($_SESSION['cart_goods_base'][$key]['maximum'] >= $_SESSION['cart_goods_base'][$key]['kolvo']+1)
								$_SESSION['cart_goods_base'][$key]['kolvo'] = $_SESSION['cart_goods_base'][$key]['kolvo']+1;
								else $_SESSION['cart_goods_base'][$key]['kolvo'] = $_SESSION['cart_goods_base'][$key]['maximum'];

								$flag = 1;
							}
						}

						if($flag <> 1) {
							$a['kolvo'] = 1;
							$_SESSION['cart_goods_base'][] = $a;
						}

					} else {
						$a['kolvo'] = 1;
						$_SESSION['cart_goods_base'][] = $a;
					}
				}
				//Вывод корзины
				$this->show_cart();
				break;
			}

			case "recalculate": {
				$arr = getarray("cart_goods", "POST");

				if(count($arr) > 0) {
					foreach ($arr as $val) {
						if($val['del'] == 1) {
							$this->del_from_cart((int)$val['id']);
						} else {
							$val['count'] = (int)$val['count'];
							foreach($_SESSION['cart_goods_base'] as $key=>$value) {
								if($_SESSION['cart_goods_base'][$key]['goodid'] == $val['id']) {
									if($_SESSION['cart_goods_base'][$key]['maximum'] >= $val['count'] && $val['count'] >=1)
									$_SESSION['cart_goods_base'][$key]['kolvo'] = $val['count'];
									else $_SESSION['cart_goods_base'][$key]['kolvo'] = $_SESSION['cart_goods_base'][$key]['maximum'];
								}
							}
						}
					}
				}

				//Вывод корзины
				$this->show_cart();
				break;
			}

			default: $this->show_cart();
		}
		//Обновление мини карзины
		$this->mini_cart();
	}

	// Вывод корзины
	public function show_cart() {
		global $m;

		$cart = $_SESSION['cart_goods_base'];
		if(count($cart) > 0) {
			$flag_online_good = 0;
			$flag_offline_good = 0;
			$total_offline=0;
			$total_price=0;
			foreach($cart as $key => $val) {
				if($cart[$key]['typegood'] ==  2) {

					$this->tpl->setVariable('GOOD_ID', $cart[$key]['goodid']);
					$this->tpl->setVariable('GOOD_NAME', $cart[$key]['title'.$this->lng]);
					$this->tpl->setVariable('GOOD_PRICE', $cart[$key]['price']);
					$this->tpl->setVariable('GOOD_DISCOUNT', $cart[$key]['rate_skidka']);
					$this->tpl->setVariable('GOOD_COUNT', $cart[$key]['kolvo']);
					$this->tpl->setVariable('GOOD_COUNT_MAX', $cart[$key]['maximum']);
					$amount = (double) ($cart[$key]['price']-$cart[$key]['price']/100*$cart[$key]['rate_skidka'])*$cart[$key]['kolvo'];
					$this->tpl->setVariable('GOOD_TOTAL', $amount);
					$this->tpl->addBlock('good_offline');
					$flag_offline_good = 1;
					$total_offline +=$cart[$key]['kolvo'];
					$total_price += $amount;
				}  else {

					$this->tpl->setVariable('GOOD_ID', $cart[$key]['goodid']);
					$this->tpl->setVariable('GOOD_NAME', $cart[$key]['title'.$this->lng]);
					$this->tpl->setVariable('GOOD_PRICE', $cart[$key]['price']);
					$this->tpl->setVariable('GOOD_DISCOUNT', $cart[$key]['rate_skidka']);
					$this->tpl->setVariable('GOOD_COUNT', $cart[$key]['kolvo']);
					$this->tpl->setVariable('GOOD_COUNT_MAX', $cart[$key]['maximum']);
					$amount = (double) ($cart[$key]['price']-$cart[$key]['price']/100*$cart[$key]['rate_skidka'])*$cart[$key]['kolvo'];
					$this->tpl->setVariable('GOOD_TOTAL', $amount);
					$this->tpl->addBlock('good_digital');
					$flag_online_good = 1;
					$total_price += $amount;

				}

			}

			//Добавление таблицы с OFFLINE товарами
			if($flag_offline_good == 1) {

				//Получение списка возможных доставок
				$ship = $this->db->query("SELECT * FROM type_shipping");

				//Добавление возможных видов доставок
				if($this->db->num_rows($ship) > 0) {
					$this->tpl->addBlock('offline_good');
					while($row = $this->db->fetch_array($ship)) {
						$row = strip($row);
						$this->tpl->setVariable('SHIP_ID', $row['id']);
						$this->tpl->setVariable('SHIP_NAME', $row['name']);
						$this->tpl->setVariable('SHIP_PRICE', $row['amount']);
						$this->tpl->setVariable('SHIP_DESCR', $row['descr']);
						if($row['typ'] == 0) $this->tpl->setVariable('SHIP_TYPE', $m['_SHOP_CART_SHIP_TYPE0']);
						else $this->tpl->setVariable('SHIP_TYPE', $m['_SHOP_CART_SHIP_TYPE1']);
						$this->tpl->addBlock('type_shipping');
					}
					$this->tpl->addBlock('shipping');
				}

				//Получение списка возможных доставок для JAVA скрипта
				$ship = $this->db->query("SELECT * FROM type_shipping");
				if($this->db->num_rows($ship) > 0)
				while($row = $this->db->fetch_array($ship)) {
					$row = strip($row);
					$this->tpl->setVariable('SHIP_ID', $row['id']);
					$this->tpl->setVariable('SHIP_PRICE', $row['amount']);
					if($row['typ'] == 0) {
						$this->tpl->setVariable('SHIP_COUNT', $total_offline);
						$this->tpl->addBlock('java_ship_type0');
					}
					else $this->tpl->addBlock('java_ship_type1');
				}

				//Включение блока проверки выбора способа доставки (JAVA)
				$this->tpl->addBlock('checkshipping');
			}
			//Добавление таблицы с ONLINE товарами
			if($flag_online_good == 1) $this->tpl->addBlock('digital_good');


			$this->tpl->setVariable('TOTAL_PRICE', $total_price);

			//Вкл. если в корзине больше 0 товаров
			$this->tpl->addBlock('enable_cart');

		} else {
			$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_CART_EMPTY']);
			$this->tpl->addBlock('info_message');
		}
	}


	//**************************************************************************************
	// Управление МОИ ПОКУПКИ

	public function mygoods() {
		if($this->loginvalid(false)) {

			$type=getfromget("type");
			switch($type) {
				case "goods": $this->mygoods_goods();break;
				case "history": $this->mygoods_history();break;
				case "profile": $this->mygoods_profile();break;
				default : $this->mygoods_profile();break;
			}
			//Отображение если авторизован
			$this->tpl->addBlock('logged_mygoods');

		} else {
			if(getfrompost("getaccess") == "yes") $this->tpl->addBlock('notlogin');
			$this->tpl->addBlock('auth_mygoods');
		}
	}

	//Управление профайлом ПОКА ТОЛЬКО ПРОСМОТР СДЕЛАН
	private function mygoods_profile() {
		$r = $this->db->query("
					SELECT * 
					FROM mypurchase 
					WHERE login='".$_SESSION['mypurchase_my']['login']."'");

		$user = $this->db->fetch_array($r);

		$this->tpl->setVariable('login', $_SESSION['mypurchase_my']['login']);
		$this->tpl->setVariable('name', $user['name']);
		$this->tpl->setVariable('email', $user['mail']);

		//Заполнение дополнительными полями формы
		$r = $this->db->query("
					SELECT mrf.*, mrfv.reg_fields_value 
					FROM mypurchase_reg_fields as mrf
					LEFT JOIN mypurchase_reg_fields_value as mrfv ON mrf.reg_field_ID=mrfv.reg_field_ID AND mrfv.mypurchase_ID = ".$user['id']."
					ORDER BY mrf.sort");

		if($this->db->num_rows($r) > 0)
		while($row = $this->db->fetch_array($r)) {
			$row = strip($row);
			if($row['type_fields_id'] == 1) {
				$this->tpl->setVariable('ADDITIONAL_NAME', $row['name'.$this->lng]);
				$this->tpl->setVariable('VALUE_FIELD', $row['reg_fields_value']);
				$this->tpl->setVariable('NAME_FIELD', "additional_field_$row[reg_field_ID]");
				$this->tpl->addBlock('type1');
			}
			if($row['type_fields_id'] == 3) {
				$this->tpl->setVariable('ADDITIONAL_NAME',$row['name'.$this->lng]);
				$this->tpl->setVariable('VALUE_FIELD', $row['reg_fields_value']);
				$this->tpl->setVariable('NAME_FIELD', "additional_field_$row[reg_field_ID]");
				$this->tpl->addBlock('type3');
			}
		}

		$this->tpl->setVariable('STEP_NUM', '4');
		$this->tpl->addBlock('additional_fields');
		$this->tpl->addBlock('profile_mygoods');
	}

	//История платежей
	private function mygoods_history() {
		GLOBAL $m;
		$r = $this->db->query("
					SELECT * 
					FROM history_pay
					WHERE buyer_ID = ".$_SESSION['mypurchase']['id']."
					ORDER BY datecreate DESC
					");
		if($this->db->num_rows($r) > 0) {
			while($row = $this->db->fetch_array($r)) {
				$row = strip($row);
				$this->tpl->setVariable('INVOICE', $row['invoice']);
				$this->tpl->setVariable('DATECREATE', $row['datecreate']);
				if($row['datepay'] == "0000-00-00 00:00:00") $this->tpl->setVariable('DATEPAY', '-');
				else $this->tpl->setVariable('DATEPAY', $row['datepay']);
				$this->tpl->setVariable('AMOUNT', $row['amount']);
				$this->tpl->setVariable('GOODID', $row['good']);
				switch ($row['status']) {
					case 0:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS0']); break;
					case 1:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS1']); break;
					case 2:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS2']); break;
				}
				$this->tpl->addBlock('list_payments');

			}
			$this->tpl->addBlock('list_history');
		} else {
			$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_HISTORY_EMPTY']);
			$this->tpl->addBlock('info_message');
		}
		$this->tpl->addBlock('history_mygoods');
	}

	//Вывод списка заказанных товаров
	private function mygoods_goods() {
		GLOBAL $m;
		$r = $this->db->query("
					SELECT hp.*, hp.status as pay_stat, gs.*
					FROM history_pay as hp
					LEFT JOIN goods as gs ON gs.good_ID = hp.good
					WHERE hp.buyer_ID = ".$_SESSION['mypurchase']['id']
					);
					if($this->db->num_rows($r) > 0) {
						while($row = $this->db->fetch_array($r)) {
							$row = strip($row);
							$this->tpl->setVariable('INVOICE', $row['invoice']);
							switch ($row['pay_stat']) {
								case 0:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS0']); break;
								case 1:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS1']); break;
								case 2:$this->tpl->setVariable('STATUS', $m['_PAYMENT_STATUS2']); break;
							}
							$this->tpl->setVariable('GOODID', $row['good_ID']);
							if(strlen($row['title'].$this->lng) < 1)
							$this->tpl->setVariable('GOOD_TITLE', '-');
							else
							$this->tpl->setVariable('GOOD_TITLE', $row['title'].$this->lng);
							switch ($row[prop_good]) {
								case 0: {
									$this->tpl->setVariable('PROP_GOOD', $m['_GOODS_STEP1_PROP0']);

									if($row['pay_stat'] == 1) {
										$this->tpl->setVariable('RECEIVE', $m['_SHOP_MYGOODS_BTN_RECEIVE']);
										$this->tpl->addBlock('access_to_good');
									} else {
										$this->tpl->addBlock('noaccess_to_good');
									}

									break;
								}
								case 1: {
									$this->tpl->setVariable('PROP_GOOD', $m['_GOODS_STEP1_PROP1']);
									if($row['pay_stat'] == 1) {
										$this->tpl->setVariable('RECEIVE', $m['_SHOP_MYGOODS_BTN_RECEIVE']);
										$this->tpl->addBlock('access_to_good');
									} else {
										$this->tpl->addBlock('noaccess_to_good');
									}
									break;
								}
								case 2: {
									$this->tpl->setVariable('PROP_GOOD', $m['_GOODS_STEP1_PROP2']);
									$this->tpl->setVariable('RECEIVE', '-');
									break;
								}
							}

							$this->tpl->addBlock('list_goods');
						}
						$this->tpl->addBlock('exist_goods');
					} else {
						$this->tpl->setVariable('info_message', $m['_SHOP_MYGOODS_GOODS_NOTFOUND']);
						$this->tpl->addBlock('info_message');
					}
					$this->tpl->addBlock('goods_mygoods');
	}

	//Получение товара, который оплачен
	public function mygood_download() {
		GLOBAL $m;
		$goodid = (int) getfromget('goodid');
		$invoice = (int) getfromget('invoice');
		$crc = getfromget('crc');
		if($this->loginvalid(false)) {
			$r = $this->db->query("
				SELECT hp.*
				FROM history_pay as hp
				WHERE hp.invoice = '$invoice' AND hp.good = '$goodid' AND buyer_ID = ".$_SESSION['mypurchase']['id']." AND hp.status=1
			");
			if($this->db->num_rows($r) == 1) {
				$hp = $this->db->fetch_array($r);
				$r = $this->db->query("
					SELECT g.*, gs.*
					FROM goods as g
					LEFT JOIN goods_secret as gs ON gs.id_good = '$goodid' AND id_num = ".$hp[good_secret_num]."
					WHERE g.good_ID='$goodid'
				");
				if($this->db->num_rows($r) == 1) {
					$good = strip($this->db->fetch_array($r));

					if(strlen($crc) > 4) {
						$this->download($good, $hp);
						return true;
					}

					if($good['prop_good'] <> 2) {
						$r = $this->db->query("
							SELECT gsfv.sercet_field_value as value, gsf.*
							FROM goods_secret_fields_value as gsfv
							LEFT JOIN goods_secret_fields as gsf ON gsf.secret_field_ID = gsfv.secret_field_ID
							WHERE gsfv.id_num = ".$hp[good_secret_num]."
							ORDER BY gsf.sort
						");

						while($lst = $this->db->fetch_array($r)) {
							$lst = strip($lst);

							if($lst['field_ID'] == 1 || $lst['field_ID'] == 3) {
								$this->tpl->setVariable('TITLE_FIELD', $lst['name'.$this->lng]);
								$this->tpl->setVariable('VALUE_FIELD', decryptdata($lst['value']));
								$this->tpl->addBlock('listing_text');
							} else {
								$this->tpl->setVariable('TITLE_FIELD', $lst['name'.$this->lng]);

								if(file_exists('secretfiles/'.$lst['value'])) {
									$this->tpl->setVariable('VALUE_FIELD', $lst['value']);
									$this->tpl->setVariable('GOODID', $goodid);
									$this->tpl->setVariable('INVOICE', $invoice);
									$this->tpl->addBlock('file_exist');
								} else $this->tpl->addBlock('nofile_exist');


								$this->tpl->addBlock('listing_file');
							}
						}
						$this->tpl->setVariable('TITLE_GOOD', $good['title'.$this->lng]);
					} else {
						$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_RECEIVE_NODIGIT']);
						$this->tpl->addBlock('info_message');
					}
				} else {
					$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_RECEIVE_EMPTYGOOD']);
					$this->tpl->addBlock('info_message');
				}
				$this->tpl->addBlock('ship_good');
			} else {
				$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_RECEIVE_FORBIDDEN']);
				$this->tpl->addBlock('info_message');
			}
		} else {
			$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_RECEIVE_LOGIN']);
			$this->tpl->addBlock('info_message');
		}
	}

	//Скачивание
	private function download($good, $hp) {
		GLOBAL $homedir;
		$r = $this->db->query("
					SELECT *
					FROM goods_secret_fields_value 
					WHERE id_num = ".$hp[good_secret_num]);
		$rows = $this->db->num_rows($r);
		while($row = $this->db->fetch_array($r)) {
			$g = $this->db->query("
					SELECT *
					FROM goods_secret_fields
					WHERE secret_field_ID = ".$row[secret_field_ID]." AND field_ID=2
					");
			if($this->db->num_rows($g)==1) {
				$rrr = $this->db->query("
					SELECT *
					FROM goods_secret_fields_value 
					WHERE secret_field_ID = ".$row[secret_field_ID]);
				$rr = $this->db->fetch_array($rrr);
				$crcc = $rr['sercet_field_value'];
			}
		}


		$filename = decryptdata($crcc);
		if($rows > 0) {

			if(file_exists("secretfiles/$crcc")) {
				$f=@fopen("secretfiles/$crcc","r");
				$content = @fread($f, filesize("secretfiles/$crcc"));
				$content = decryptdata($content, false);

				header("Cache-Control: ");
				header("Pragma: ");
				header("Content-Type: application/octet-stream");
				header("Content-Length: " .(string)(filesize("secretfiles/$crcc")) );
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header("Content-Transfer-Encoding: binary\n");
				print $content;
				flush();

				fclose($f);
			} else echo "File not found";
		} else echo "File not found (db)";
	}
	//**************************************************************************************
	// Управление ПАРТНЕРСКАЯ ПРОГРАММА

	public function partner() {
		if($this->loginvalid(false)) {
			switch (getfromget("type")) {
				case "stat": {
					$this->partner_stat();
					break;
				}
				case "codes": {
					$this->partner_codes();
					break;
				}
				case "history": {
					$this->partner_history();
					break;
				}
				default : $this->partner_stat();
			}
			$this->tpl->addBlock('partner_form');
		} else {
			if(getfrompost("getaccess") == "yes") $this->tpl->addBlock('notlogin');
			$this->tpl->addBlock('auth_pp');
		}
	}

	// Вывод статистики текущей
	private function partner_stat() {
		GLOBAL $m;
		if(getfrompost('payout') == "yes") {
			$r = $this->db->query("SELECT agent_amount FROM  mypurchase WHERE id=".$_SESSION['mypurchase']['id']);
			$pay  = $this->db->fetch_array($r);
			if($pay['agent_amount'] >= 1) {
				$desc = getfrompost('desc');
				if(strlen($desc) > 3) {
					$this->db->query("UPDATE mypurchase SET agent_amount=agent_amount-".$pay['agent_amount']." WHERE id=".$_SESSION['mypurchase']['id']);
					$this->db->query("
									INSERT 
									INTO 
									mypurchase_history_pay 
									VALUES(
									'',
									".$_SESSION['mypurchase']['id'].",
									'".date("Y-m-d H:i:s")."',
									'0000-00-00 00:00:00',
									'$pay[agent_amount]',
									'".addslashes($desc)."',
									2
									)
									");		
					$this->tpl->setVariable('info_message', $m['_SHOP_PARTNER_PAYOUT_SUCCESS']);
					$this->tpl->addBlock('info_message');
				} else {
					$this->tpl->setVariable('info_message', $m['_SHOP_PARTNER_PAYOUT_ERROR2']);
					$this->tpl->addBlock('info_message');
				}
			} else {
				$this->tpl->setVariable('info_message', $m['_SHOP_PARTNER_PAYOUT_ERROR1']);
				$this->tpl->addBlock('info_message');
			}
		}

		$r = $this->db->query("
						SELECT mp.*, SUM(mhp.amount) as sm, SUM(mhpp.amount) as fullsum, count(hp.agent_ID) as kolvo
						FROM mypurchase as mp
						LEFT JOIN mypurchase_history_pay as mhp ON mhp.mypurchase_ID = mp.id AND mhp.status=1
						LEFT JOIN mypurchase_history_pay as mhpp ON mhpp.mypurchase_ID = mp.id
						LEFT JOIN history_pay as hp ON hp.agent_ID = mp.ID AND hp.status = 1
						WHERE mp.id = ".$_SESSION['mypurchase']['id']."
						GROUP BY mhp.mypurchase_ID, mhpp.mypurchase_ID, hp.agent_ID
						");

		$agent = $this->db->fetch_array($r);
		$this->tpl->setVariable('CURRENT_EARN', $agent['agent_amount']);
		$this->tpl->setVariable('TOTAL_BUY', $agent['kolvo']);
		$this->tpl->setVariable('TOTAL_EARN', $agent['fullsum']+$agent['agent_amount']);

		if($agent['agent_amount'] >= 1) $this->tpl->addBlock('pay_button');
		$this->tpl->addBlock('partner_stat');
	}

	// Вывод партнерских ссылок
	private function partner_codes() {
		$this->tpl->setVariable('ID_AGENT', $_SESSION['mypurchase']['id']);
		$this->tpl->addBlock('partner_code');
	}

	//Вывод истории выплат
	private function partner_history() {
		GLOBAL $m;
		$r = $this->db->query("
						SELECT *
						FROM mypurchase_history_pay
						WHERE mypurchase_ID = ".$_SESSION['mypurchase']['id']."
						ORDER BY id DESC
						"
						);
						if($this->db->num_rows($r) > 0) {
							while($row = $this->db->fetch_array($r)) {
								$row = strip($row);
								$this->tpl->setVariable('INVOICE', $row['id']);
								$this->tpl->setVariable('DATECREATE', $row['datecreate']);
								if($row['datepay'] == "0000-00-00 00:00:00") $this->tpl->setVariable('DATEPAY', '-');
								else $this->tpl->setVariable('DATEPAY', $row['datepay']);
								$this->tpl->setVariable('AMOUNT', $row['amount']);
								$this->tpl->setVariable('DESCR', $row['descr']);
								switch ($row['status']) {
									case 0:$this->tpl->setVariable('STATUS', $m['_SHOP_PARTNER_PAYMENT_STATUS0']); break;
									case 1:$this->tpl->setVariable('STATUS', $m['_SHOP_PARTNER_PAYMENT_STATUS1']); break;
									case 2:$this->tpl->setVariable('STATUS', $m['_SHOP_PARTNER_PAYMENT_STATUS2']); break;
								}
								$this->tpl->addBlock('list_payments');
							}
							$this->tpl->addBlock('list_history');
						} else {
							$this->tpl->setVariable('INFO_MESSAGE', $m['_SHOP_MYGOODS_HISTORY_EMPTY']);
							$this->tpl->addBlock('info_message');
						}
						$this->tpl->addBlock('history_partner');
	}
}
?>
