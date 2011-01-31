/* ------------------------------------------------------------------------------------------------
	Администраторская запись
	{>user_admin<}
*/ 
CREATE TABLE user_admin (
	id int(11) unsigned NOT NULL auto_increment,
	name varchar(30) NOT NULL default '',
	login varchar(16) NOT NULL,
	pwd varchar(32) NOT NULL,
	PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=1;

<><>

/* ------------------------------------------------------------------------------------------------
	Типы полей для динамического создания
	{>type_fields<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE type_fields(
	id int(11) unsigned NOT NULL,			/*номер типа полей*/
	type_field text NOT NULL default ''		/*сам тип <input class='{CLASS_FIELD}' type='text' name='{name_field}_{ID}'  size='{size_field}' value='{value_field}'> */
)TYPE=MyISAM;

||INSERT INTO type_fields VALUES("1", "<input class='{class_field}' type='text' name='{name_field}_{ID}'  size='{size_field}' value='{value_field}'>");
||INSERT INTO type_fields VALUES("2", "<input class='{class_field}' type='file' name='{name_field}_{ID}'  size='{size_field}' value='{value_field}'>");
||INSERT INTO type_fields VALUES("3", "<textarea class='{class_field}' name='{name_field}_{ID}'  cols='{cols_field}' rows='{rows_field}'>{value_field}</textarea>");
<><>
/* ------------------------------------------------------------------------------------------------
	Ведение товаров
	{>goods<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE goods (
	good_ID int(11) unsigned NOT NULL auto_increment,			/* - идентификатор товара*/
	cat_ID int(11) unsigned NOT NULL default '0',				/* - ID категории где находится товар*/
	type_ID int(11) unsigned NOT NULL default '0',				/* - ID типа товара */
	idarticul varchar(255) NOT NULL default '',					/* - Внутренний артикул товара */
	title varchar(255) NOT NULL default '',						/* - Название на русском */
	title_en varchar(255) NOT NULL default '',					/* - Название на английском */
	descr text NOT NULL default '',								/* - описание на русском*/
	descr_en text NOT NULL default '',							/* - описание на английском*/
	additional text NOT NULL default '',						/* - дополнительное описание на русском*/
	additional_en text NOT NULL default '',						/* - дополнительное описание на английском*/
	meta_key text NOT NULL default '',							/* - мета ключевые слова товара*/
	meta_desc text NOT NULL default '',							/* - мета описание товара*/
	price double(6,2) unsigned NOT NULL default '0.00',			/* - цена товара*/
	dateupload datetime NOT NULL default '0000-00-00 00:00:00',	/* - дата загрузки товара*/
	spec_offer tinyint(1) unsigned NOT NULL default '0',		/* - специальное предложение*/
	rate_skidka tinyint(3) unsigned NOT NULL default '0',		/* - скидка товара*/
	rate_agent tinyint(3) unsigned NOT NULL default '0',		/* - процент агентку за продажу товара*/
	prop_good tinyint(1) unsigned NOT NULL default '0',			/* - Уникальный - 0, Универсальный -1, Оффлайн доставка - 2*/
	sklad int(11) unsigned NOT NULL default '1',				/* - Количество товара на складе, если Оффлайн доставка */
	count_sell int(11) unsigned NOT NULL default '0',			/* - Количество продаж*/
	disabled tinyint(1) unsigned NOT NULL default '0',			/* - Включить или выключить продажу товара 0 - включена продажа, 1 - выкл.*/
	PRIMARY KEY  (good_ID)
) TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Фотографии для товара
	{>photo_goods<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE photo_goods (
	photo_ID int(11) unsigned NOT NULL auto_increment,	/* ID фотографии */
	good_ID int(11) unsigned NOT NULL,					/* ID товара */
	flag tinyint(1) unsigned NOT NULL default '0',		/* flag - 1 - главная фото, 0 - нет */
	path_to_photo varchar(255) NOT NULL default '',		/* path_to_photo - путь до фотографии */
	PRIMARY KEY(photo_ID)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Товар, который доставляется сразу после оплаты
	{>goods_secret<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE goods_secret (
	id_num int(11) unsigned NOT NULL auto_increment,   /* порядковый номер  */
	id_good int(11) unsigned NOT NULL default '0',     /*  номер товара */
	id_type int(11) unsigned NOT NULL default '0',     /*  тип товара */
	info_good text NOT NULL default '',			   	   /*  массив содержащий описание товара, добавляется после покупки */
	status tinyint(1) unsigned NOT NULL default '0',   /*  0 - не продан, 1 - продан, - 2 - в процессе покупки */
	PRIMARY KEY (id_num)
) TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Типы товаров - (шаблон для создания товара, цифрового)
	{>type_goods<}
------------------------------------------------------------------------------------------------ */

CREATE TABLE type_goods (
	type_ID int(11) unsigned NOT NULL auto_increment,
	name varchar(255) NOT NULL default '',			/*название типа на русском*/
	name_en varchar(255) NOT NULL default '',		/*название типа на английском*/
	PRIMARY KEY (type_ID)
) TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Описание полей "ТИПА ТОВАРА", который доставляется сразу после оплаты
	{>goods_secret_fields<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE goods_secret_fields (
	secret_field_ID int(11) unsigned NOT NULL auto_increment,	/*Порядковый тип поля*/
	type_ID int(11) unsigned NOT NULL,							/*ID типа товара*/
	field_ID int(11) unsigned NOT NULL,							/*ID типа поля ( 1 - однострочное, 2 - файл, 3 - многострочное ) */
	name varchar(255) NOT NULL default '',						/*название типа на русском*/
	name_en varchar(255) NOT NULL default '',					/*название типа на английском*/
	setting_array text NOT NULL default '',						/*массив дополнительной настройки поля*/
	sort int NOT NULL default 0,								/*Порядок расположения полей*/
	PRIMARY KEY (secret_field_ID)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Значения полей "ТИПА ТОВАРА", который доставляется сразу после оплаты
	{>goods_secret_fields_value<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE goods_secret_fields_value (
	secret_field_ID int(11) unsigned NOT NULL,		/*ID поля*/
	id_num int(11) unsigned NOT NULL,				/*ID номер секретного товара, которому принадлежит значение*/
	id_type int(11) unsigned NOT NULL default '0',	/*Тип товара*/
	sercet_field_value text NOT NULL				/*Значение*/
)TYPE=MyISAM;
<><>
/* ------------------------------------------------------------------------------------------------
	История платежей (товаров)
	{>history_pay<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE history_pay (
	invoice int(11) unsigned NOT NULL auto_increment,			/* номер созданного счета */	
	buyer_ID int(11) unsigned NOT NULL default '0',				/* номер в Мои Покупки */
	agent_ID int(11) unsigned NOT NULL default '0',				/* номер агента из акка. Мои покупки */
	amount double(14,2) unsigned NOT NULL default '0.00',		/* amount - сумма платежа  */
	datecreate datetime NOT NULL default '0000-00-00 00:00:00', /* дата создания счета */
	datepay datetime NOT NULL default '0000-00-00 00:00:00',	/* дата платежа */
	from_acc_pay varchar(255) NOT NULL default '',				/* номер счета с которого платили */
	good int(11) unsigned NOT NULL,								/* goods - номер купленного товара */
	good_secret_num int(11) unsigned NOT NULL default '0',		/* номер купленного товара, если товар УНИКАЛЬНЫЙ */
	status tinyint(1) unsigned NOT NULL default '0',			/* 0 - неоплачен, 1 - оплачен, 2 -удален */
	ip varchar(16) NOT NULL default '',							/* IP адрес плательщика  */
	descr text NOT NULL default '',								/* описание платежа */
	PRIMARY KEY (invoice)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Описание дополнительных регистрационных полей раздела МОИ ПОКУПКИ
	{>mypurchase_reg_fields<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE mypurchase_reg_fields (						
	reg_field_ID int(11) unsigned NOT NULL auto_increment,	/* порядковый ID */
	type_fields_id int(11) unsigned NOT NULL,				/* тип поля с таблицы type_fields */
	name varchar(255) NOT NULL default '',					/* описание поля на русском */
	name_en varchar(255) NOT NULL default '',				/* описание поля на английском */
	setting_array text NOT NULL default '',					/* массив дополнительных параметров для типа поля */
	sort int NOT NULL default '0',							/* порядок расположения */
	PRIMARY KEY (reg_field_ID)
) TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Значения дополнительных полей раздела МОИ ПОКУПКИ
	{>mypurchase_reg_fields_value<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE mypurchase_reg_fields_value (
	reg_field_ID int(11) unsigned NOT NULL ,			/* ID из таблицы  mypurchase_reg_fields */
	mypurchase_ID int(11) unsigned NOT NULL,			/* ID аккаунта МОИ ПОКУПКИ */
	reg_fields_value varchar(255) NOT NULL default ''	/* значение поля */
) TYPE=MyISAM;
<><>
/* ------------------------------------------------------------------------------------------------
	Раздел Мои Покупки и партнерская программа
	{>mypurchase<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE mypurchase (
	id int(11) unsigned NOT NULL auto_increment,					/* порядковый номер*/
	login text NOT NULL,
	pwd text NOT NULL,
	name varchar(255) NOT NULL default '',							/* ФИО*/
	mail varchar(255) NOT NULL default '',
	agent_amount double(6,4) unsigned NOT NULL default '0.0000',	/* сумма агенских в у.е.*/
	pincode varchar(12) NOT NULL default '',						/* пин код для входа при запросе пароля*/
	request_pwd tinyint(1) NOT NULL default '0',					/* статус запроса нового пароля*/
	status tinyint(1) NOT NULL default '0',							/* статуст аккаунта: 0 - блокирован, 1 включен, 2 - не подтвержден*/
	PRIMARY KEY (id)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	История выплат агентских из партнерской программы
	{>mypurchase_history_pay<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE mypurchase_history_pay (
	id int(11) unsigned NOT NULL auto_increment,				
	mypurchase_ID int(11) unsigned NOT NULL,					/* номер аккаунта*/
	datecreate datetime NOT NULL default '0000-00-00 00:00:00',	/* дата создания счета*/
	datepay datetime NOT NULL default '0000-00-00 00:00:00',	/* дата выплаты*/
	amount double(6,4) unsigned NOT NULL default '0.0000',		/* сумма выплаты*/
	descr text NOT NULL default '',								/* описание платежа*/
	status tinyint(1) NOT NULL default '0',						/* статус платежа: 0 отказано, 1 выполнен, 2 в обработке*/
	PRIMARY KEY (id)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Вид доставки не цифровых товаров (к примеру UPS, курьер)
	{>type_shipping<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE type_shipping(
	id int(11) unsigned NOT NULL auto_increment,
	name varchar(255) NOT NULL default '',					/* название доставки*/
	descr text NOT NULL default '',							/* описание*/
	typ tinyint(1) unsigned NOT NULL default '1',			/* тип применения 0 - за ед.товара, 1 - за весь заказ*/
	amount double(6,2) unsigned NOT NULL default '0.00',	/* цена доставки*/
	PRIMARY KEY (id)
	)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	Категории товаров
	{>category<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE category (
	id int(11) unsigned NOT NULL auto_increment,	/* -номер категории */
	cat_left int NOT null,
    cat_right int NOT null,							/* -cat_left, cat_right, cat_level - ключи категории */
    cat_level int NOT null,
	sort int NOT null,								/* -не используется */
	name varchar(255) NOT NULL default '',			/* -название на RU */
	name_en varchar(255) NOT NULL default '',		/* -название на EN */
	PRIMARY KEY (id),
	KEY(cat_left, cat_right, cat_level)
) TYPE=MyISAM AUTO_INCREMENT=138;
||INSERT INTO `category` VALUES (1, 1, 266, 0, 0, 'Главная', 'Root');
||INSERT INTO `category` VALUES (2, 2, 115, 1, 0, 'Программное обеспечение', 'SoftWare');
||INSERT INTO `category` VALUES (3, 3, 32, 2, 0, 'Интернет', 'Internet');
||INSERT INTO `category` VALUES (4, 33, 40, 2, 0, 'Безопасность', 'Security');
||INSERT INTO `category` VALUES (5, 41, 50, 2, 0, 'Бухгалтерия, делопроизводство', 'Accounts');
||INSERT INTO `category` VALUES (63, 117, 128, 2, 0, 'Мобильная связь', 'Mobile network');
||INSERT INTO `category` VALUES (8, 51, 56, 2, 0, 'Мобильная связь', 'Mobile');
||INSERT INTO `category` VALUES (9, 57, 64, 2, 0, 'Мультимедиа и графика', 'Media and Graphics');
||INSERT INTO `category` VALUES (62, 116, 175, 1, 0, 'PIN-коды', 'PIN-codes');
||INSERT INTO `category` VALUES (11, 65, 86, 2, 0, 'Программирование', 'Programming');
||INSERT INTO `category` VALUES (12, 87, 90, 2, 0, 'Продвижение сайтов', 'Promotion sites');
||INSERT INTO `category` VALUES (13, 91, 94, 2, 0, 'Рабочий стол', 'Desktop');
||INSERT INTO `category` VALUES (14, 95, 100, 2, 0, 'Редакторы', 'Editors');
||INSERT INTO `category` VALUES (15, 101, 106, 2, 0, 'Софт для карманных ПК', 'Soft for Hand PC');
||INSERT INTO `category` VALUES (16, 107, 114, 2, 0, 'Утилиты', 'Utilities');
||INSERT INTO `category` VALUES (17, 4, 19, 3, 0, 'Скрипты', 'Scripts');
||INSERT INTO `category` VALUES (18, 5, 6, 4, 0, 'Магазины', 'Shops');
||INSERT INTO `category` VALUES (19, 7, 8, 4, 0, 'CMS', 'CMS');
||INSERT INTO `category` VALUES (20, 9, 10, 4, 0, 'Поисковые системы', 'Search system');
||INSERT INTO `category` VALUES (21, 11, 12, 4, 0, 'Оформление сайта', 'Web-Design');
||INSERT INTO `category` VALUES (22, 13, 14, 4, 0, 'Казино, лотереи, игры', 'Casino, lottery, games');
||INSERT INTO `category` VALUES (23, 15, 16, 4, 0, 'Аукционы', 'Auctions');
||INSERT INTO `category` VALUES (24, 17, 18, 4, 0, 'WAP скрипты', 'WAP scripts');
||INSERT INTO `category` VALUES (25, 20, 21, 3, 0, 'FTP', 'FTP');
||INSERT INTO `category` VALUES (26, 22, 23, 3, 0, 'Forex', 'Forex');
||INSERT INTO `category` VALUES (27, 24, 25, 3, 0, 'SMTP', 'SMTP');
||INSERT INTO `category` VALUES (28, 26, 27, 3, 0, 'WEB дизайн', 'WEB дизайн');
||INSERT INTO `category` VALUES (29, 28, 29, 3, 0, 'Администрирование', 'Administration');
||INSERT INTO `category` VALUES (30, 30, 31, 3, 0, 'Чаты', 'Chats');
||INSERT INTO `category` VALUES (31, 34, 35, 3, 0, 'Антивирусы', 'Antiviruses');
||INSERT INTO `category` VALUES (32, 36, 37, 3, 0, 'Восстановление данных', 'Recover data');
||INSERT INTO `category` VALUES (33, 38, 39, 3, 0, 'Шифрование', 'Encryption');
||INSERT INTO `category` VALUES (34, 42, 43, 3, 0, 'Бизнес', 'Buisnes');
||INSERT INTO `category` VALUES (35, 44, 45, 3, 0, 'Домашняя бухгалтерия', 'Home accounting');
||INSERT INTO `category` VALUES (36, 46, 47, 3, 0, 'Заработная плата', 'Wages');
||INSERT INTO `category` VALUES (37, 48, 49, 3, 0, 'Торговля', 'Trade');
||INSERT INTO `category` VALUES (38, 52, 53, 3, 0, 'Программы для сотовых телефонов', 'Mobile Software');
||INSERT INTO `category` VALUES (39, 54, 55, 3, 0, 'Разблокировка телефонов', 'Unlock phone');
||INSERT INTO `category` VALUES (40, 66, 67, 3, 0, '1С', '1С');
||INSERT INTO `category` VALUES (41, 58, 59, 3, 0, '3D графика', '3D Graphic');
||INSERT INTO `category` VALUES (42, 60, 61, 3, 0, 'Анимация, видео', 'Animation, video');
||INSERT INTO `category` VALUES (43, 62, 63, 3, 0, 'Шрифты', 'Fonts');
||INSERT INTO `category` VALUES (44, 68, 69, 3, 0, 'ActiveX', 'ActiveX');
||INSERT INTO `category` VALUES (45, 70, 71, 3, 0, 'Assembler', 'Assembler');
||INSERT INTO `category` VALUES (46, 72, 73, 3, 0, 'C/С++', 'C/С++');
||INSERT INTO `category` VALUES (47, 74, 75, 3, 0, 'Delphi/Pascal', 'Delphi/Pascal');
||INSERT INTO `category` VALUES (48, 76, 77, 3, 0, 'Visual Basic', 'Visual Basic');
||INSERT INTO `category` VALUES (49, 78, 79, 3, 0, 'Базы данных , SQL, ODBC', 'Базы данных , SQL, ODBC');
||INSERT INTO `category` VALUES (50, 80, 81, 3, 0, 'PHP', 'PHP');
||INSERT INTO `category` VALUES (51, 82, 83, 3, 0, 'Другие языки программирования', 'Other language');
||INSERT INTO `category` VALUES (52, 84, 85, 3, 0, 'Отладчики и дизассемблеры', 'Debuggers');
||INSERT INTO `category` VALUES (53, 88, 89, 3, 0, 'Регистрация в каталогах', 'Registration in catalogs');
||INSERT INTO `category` VALUES (54, 92, 93, 3, 0, 'Темы,обои и.т.п.', 'Themes, wallpapers');
||INSERT INTO `category` VALUES (55, 96, 97, 3, 0, 'Текстовые редакторы', 'Text editors');
||INSERT INTO `category` VALUES (56, 98, 99, 3, 0, 'Другое', 'Other');
||INSERT INTO `category` VALUES (57, 102, 103, 3, 0, 'Palm OC и Palm Pilot', 'Palm OC и Palm Pilot');
||INSERT INTO `category` VALUES (58, 104, 105, 3, 0, 'Pocket PC', 'Pocket PC');
||INSERT INTO `category` VALUES (59, 108, 109, 3, 0, 'Дисковые и файловые утилиты', 'Disk and files utilites');
||INSERT INTO `category` VALUES (60, 110, 111, 3, 0, 'Компрессия и декомпрессия', 'Compression, decompression');
||INSERT INTO `category` VALUES (61, 112, 113, 3, 0, 'Контроль и администрирование ОС', 'Control and administration OS');
||INSERT INTO `category` VALUES (64, 129, 134, 2, 0, 'IP-телефония', 'VoIP');
||INSERT INTO `category` VALUES (65, 135, 146, 2, 0, 'Интернет провайдеры', 'IPS');
||INSERT INTO `category` VALUES (86, 136, 137, 3, 0, 'SOLO', 'SOLO');
||INSERT INTO `category` VALUES (67, 118, 119, 3, 0, 'Velcom (Беларусь)', 'Velcom (Belarus)');
||INSERT INTO `category` VALUES (68, 120, 121, 3, 0, 'MTS (Беларусь)', 'MTS (Belarus)');
||INSERT INTO `category` VALUES (69, 122, 123, 3, 0, 'BEST (Беларусь)', 'BEST(Belarus)');
||INSERT INTO `category` VALUES (70, 124, 125, 3, 0, 'Dialog (Беларусь)', 'Dialog (Belarus)');
||INSERT INTO `category` VALUES (71, 126, 127, 3, 0, 'Билайн (Россия)', 'Beeline (Russia)');
||INSERT INTO `category` VALUES (72, 147, 162, 2, 0, 'Онлайн-игры', 'Online-games');
||INSERT INTO `category` VALUES (73, 163, 170, 2, 0, 'Платежные системы', 'Payment Systems');
||INSERT INTO `category` VALUES (74, 148, 155, 3, 0, 'World of Warcraft', 'World of Warcraft');
||INSERT INTO `category` VALUES (75, 149, 150, 4, 0, 'Американская версия (US)', 'US version');
||INSERT INTO `category` VALUES (76, 151, 152, 4, 0, 'Европейская версия (EURO)', 'EURO version');
||INSERT INTO `category` VALUES (77, 153, 154, 4, 0, 'Игровая валюта (GOLD)', 'Game money  (GOLD)');
||INSERT INTO `category` VALUES (78, 156, 157, 3, 0, 'Lineage II', 'Lineage II');
||INSERT INTO `category` VALUES (79, 158, 159, 3, 0, 'Ultima Online', 'Ultima Online');
||INSERT INTO `category` VALUES (80, 160, 161, 3, 0, 'Eve-Online', 'Eve-Online');
||INSERT INTO `category` VALUES (81, 171, 174, 2, 0, 'Кредитные карты', 'Credit Cards');
||INSERT INTO `category` VALUES (82, 164, 165, 3, 0, 'Яндекс.Деньги', 'Yandex.Money');
||INSERT INTO `category` VALUES (83, 166, 167, 3, 0, 'RUpay', 'RUpay');
||INSERT INTO `category` VALUES (84, 168, 169, 3, 0, 'E-Gold', 'E-Gold');
||INSERT INTO `category` VALUES (85, 172, 173, 3, 0, 'Prepaid Credit Card', 'Prepaid Credit Card');
||INSERT INTO `category` VALUES (87, 138, 139, 3, 0, 'Atlant Telecom', 'Atlant Telecom');
||INSERT INTO `category` VALUES (88, 140, 141, 3, 0, 'Anitex', 'Anitex');
||INSERT INTO `category` VALUES (89, 142, 143, 3, 0, 'Деловая сеть', 'Buisnes Network');
||INSERT INTO `category` VALUES (90, 144, 145, 3, 0, 'Айчына', 'Aplus.by');
||INSERT INTO `category` VALUES (91, 130, 131, 3, 0, 'Оверлайн', 'Оверлайн');
||INSERT INTO `category` VALUES (92, 132, 133, 3, 0, 'WestCall', 'WestCall');
||INSERT INTO `category` VALUES (93, 176, 211, 1, 0, 'Цифровые товары', 'Digital goods');
||INSERT INTO `category` VALUES (94, 177, 190, 2, 0, 'ICQ номера', 'ICQ numbers');
||INSERT INTO `category` VALUES (95, 191, 200, 2, 0, 'Мобильные телефоны', 'Mobile phone');
||INSERT INTO `category` VALUES (96, 201, 202, 2, 0, 'Раскрутка сайтов', 'Promoution sites');
||INSERT INTO `category` VALUES (97, 203, 204, 2, 0, 'Базы данных', 'Databases');
||INSERT INTO `category` VALUES (98, 205, 206, 2, 0, 'Дизайн', 'Design');
||INSERT INTO `category` VALUES (99, 207, 208, 2, 0, 'Хостинг', 'Hosting');
||INSERT INTO `category` VALUES (100, 209, 210, 2, 0, 'Шаблоны для сайтов', 'Web-templates');
||INSERT INTO `category` VALUES (101, 192, 193, 3, 0, 'Видео для мобильных', 'Video');
||INSERT INTO `category` VALUES (102, 194, 195, 3, 0, 'Заставки для мобильных', 'Screensavers');
||INSERT INTO `category` VALUES (103, 196, 197, 3, 0, 'Игры для мобильных', 'Games');
||INSERT INTO `category` VALUES (104, 198, 199, 3, 0, 'Мелодии для мобильных', 'Melody');
||INSERT INTO `category` VALUES (105, 178, 179, 3, 0, '5-ти значные', '5 digitals');
||INSERT INTO `category` VALUES (106, 180, 181, 3, 0, '6-ти значные', '6 digitals');
||INSERT INTO `category` VALUES (107, 182, 183, 3, 0, '7-ми значные', '7 digitals');
||INSERT INTO `category` VALUES (108, 184, 185, 3, 0, '8-ми значные', '8 digitals');
||INSERT INTO `category` VALUES (109, 186, 187, 3, 0, '9-ти значные', '9 digitals');
||INSERT INTO `category` VALUES (110, 188, 189, 3, 0, 'Другие', 'Others');
||INSERT INTO `category` VALUES (111, 212, 221, 1, 0, 'Электронные книги', 'E-books');
||INSERT INTO `category` VALUES (112, 213, 214, 2, 0, 'Безопасность', 'Security');
||INSERT INTO `category` VALUES (113, 215, 216, 2, 0, 'Наука и образование', 'Science, education');
||INSERT INTO `category` VALUES (114, 217, 218, 2, 0, 'Продвижение сайтов', 'Pronotion sites');
||INSERT INTO `category` VALUES (115, 219, 220, 2, 0, 'Бизнес и экономика', 'Buisnes, Economy');
||INSERT INTO `category` VALUES (116, 222, 245, 1, 0, 'Телефоны и КПК', 'Cell Phones &amp; PDAs');
||INSERT INTO `category` VALUES (117, 246, 257, 1, 0, 'Фото и камеры', 'Cameras &amp; Photo');
||INSERT INTO `category` VALUES (118, 258, 265, 1, 0, 'Книги', 'Books');
||INSERT INTO `category` VALUES (119, 223, 236, 2, 0, 'Мобильные телефоны', 'Mobile phone');
||INSERT INTO `category` VALUES (120, 237, 244, 2, 0, 'DECT телефоны', 'DECT phone');
||INSERT INTO `category` VALUES (121, 224, 225, 3, 0, 'Nokia', 'Nokia');
||INSERT INTO `category` VALUES (122, 226, 227, 3, 0, 'Siemens', 'Siemens');
||INSERT INTO `category` VALUES (123, 228, 229, 3, 0, 'Sony-Erricson', 'Sony-Erricson');
||INSERT INTO `category` VALUES (124, 230, 231, 3, 0, 'Panasonic', 'Panasonic');
||INSERT INTO `category` VALUES (125, 232, 233, 3, 0, 'Motorolla', 'Motorolla');
||INSERT INTO `category` VALUES (126, 234, 235, 3, 0, 'LG', 'LG');
||INSERT INTO `category` VALUES (127, 238, 239, 3, 0, 'Siemens', 'Siemens');
||INSERT INTO `category` VALUES (128, 240, 241, 3, 0, 'Panasonic', 'Panasonic');
||INSERT INTO `category` VALUES (129, 242, 243, 3, 0, 'LG', 'LG');
||INSERT INTO `category` VALUES (130, 247, 248, 2, 0, 'Olympus', 'Olympus');
||INSERT INTO `category` VALUES (131, 249, 250, 2, 0, 'Canon', 'Canon');
||INSERT INTO `category` VALUES (132, 251, 252, 2, 0, 'Nikon', 'Nikon');
||INSERT INTO `category` VALUES (133, 253, 254, 2, 0, 'Sony', 'Sony');
||INSERT INTO `category` VALUES (134, 255, 256, 2, 0, 'Samsung', 'Samsung');
||INSERT INTO `category` VALUES (135, 259, 260, 2, 0, 'Наука, образование', 'Education, science');
||INSERT INTO `category` VALUES (136, 261, 262, 2, 0, 'Медицина', 'Medicine');
||INSERT INTO `category` VALUES (137, 263, 264, 2, 0, 'Цветы', 'Flowers');
<><>
/* ------------------------------------------------------------------------------------------------
	Система новостей
	{>news<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE news (
	id int(11) unsigned NOT NULL auto_increment,
	date datetime NOT NULL default '0000-00-00 00:00:00',	/* дата и время добавления/обновления новости*/
	title text NOT NULL default '',							/* заголовок на RU*/
	title_en text NOT NULL default '',						/* заголовок на EN*/
	msg text NOT NULL default '',							/* новость на RU*/
	msg_en text NOT NULL default '',						/* новость на EN*/
	subscribe_send tinyint(1) NOT NULL default '0',			/* статус рассылки: 0 - не рассылать, 1 - ожидания рассылки*/
	PRIMARY KEY(id)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	 База писем EMAIL для очередной отправки
	 {>email_queue<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE email_queue (				
	id int(11) unsigned NOT NULL auto_increment,
	tpl_ID int(11) unsigned NOT NULL,			/* ID из ticketssystem_setting*/
	mail_to varchar(255) NOT NULL default '', 	/* Email куда доставить */
	charset varchar(100) NOT NULL default '',	/* Кодировка письма*/
	subject varchar(255) NOT NULL default '',	/* Тема письма*/
	letter longtext NOT NULL default '',		/* Текст письма*/
	PRIMARY KEY(id)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	 Тикет система - НАСТРОЙКИ
	 {>ticketsystem_setting<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE ticketsystem_setting (
	tpl_ID int(11) unsigned NOT NULL,	
	email varchar(255) NOT NULL default '',			/* Email адрес с которого все будет отправляться*/
	name varchar(255) NOT NULL default '',			/* Название фирмы, сайта, и т.д.*/
	smtp_server varchar(255) NOT NULL default '',	/* SMTP server */
	smtp_port int(11) NOT NULL,						/* SMTP port*/
	pop3_server varchar(255) NOT NULL default '',	/* POP3 server */
	pop3_port int(11) NOT NULL,						/* POP3 port*/
	login varchar(255) NOT NULL default '',			/* POP3 username*/
	pwd varchar(255) NOT NULL default '',			/* POP3 password*/
	status tinyint(1) NOT NULL,						/* Статус mail приема тикетов ВКЛ - 1.ВЫКЛ -0*/
	status_save tinyint(1) NOT NULL,				/* Статус сохранения сообщений на сервере ВКЛ 1 - ВЫКЛ - 0*/
	close_status tinyint(1) NOT NULL,				/* Вкл.Выкл. авто закрытие тикета*/
	close_day int(11) unsigned NOT NULL,			/* Кол-во дней до закрытия тикета*/
	delete_status tinyint(1) NOT NULL,				/* Вкл.Выкл. авто удаление закрытых тикетов*/
	delete_day int(11) unsigned NOT NULL,			/* Кол-во дней до удаление закрытого тикета*/
	status_ban tinyint(1) NOT NULL,					/* ВКЛ. - ВЫКЛ. черного списка*/
	sign_msg text NOT NULL default '',				/* Подпись в ответах*/
	subject text NOT NULL default '',				/* Тема сообщения оповещении о регистрации тикета*/				
	msg text NOT NULL default ''					/* Сообщение о регистрациии*/					
)TYPE=MyISAM;
||
INSERT INTO ticketsystem_setting VALUES('1', 'mail@tut.by', 'Support FlexShop', 'mail.tut.by', '25', 'mail.tut.by', '110', 'username', 'pwd123', '0', '1', '0', '30', '0', '180', '0', 'Здравствуйте!<br>\n{BODYMSG}', '{TICKETID} {SUBJECT}', 'Сообщение о регистрации\n {TICKETID}, {SUBJECT}, {LINK}')
||INSERT INTO ticketsystem_setting VALUES('2', 'admin@tut.by', 'Flex-Shop', 'mail.tut.by', '25', 'mail.tut.by', '110', 'username', 'pwd123', '0', '1', '0', '30', '0', '180', '0', '{BODYMSG}', '', '')
<><>
/* ------------------------------------------------------------------------------------------------
	 Тикет система - БЛОКИРОВКА EMAIL от спама
	 {>ticketsystem_ban<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE ticketsystem_ban (
	ban_ID int(11) unsigned NOT NULL auto_increment,											
	email varchar(255) NOT NULL default '',						/*блокированные адреса*/
	descr text NOT NULL,										/*причина блокировки*/
	PRIMARY KEY (ban_ID)
)TYPE=MyISAM AUTO_INCREMENT=1;
<><>
/* ------------------------------------------------------------------------------------------------
	 Тикет система 
	 {>ticketsystem<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE ticketsystem (
	ticket_ID int(11) unsigned NOT NULL auto_increment,
	keyID varchar(32) NOT NULL,												
	email varchar(255) NOT NULL default '',						/*mail оповещение о новых сообщениях*/
	datecreate datetime NOT NULL default '0000-00-00 00:00:00',	/*дата и время создания*/
	dateclose datetime NOT NULL default '0000-00-00 00:00:00',	/*дата и время закрытия*/
	subject text NOT NULL default '',					/*тема тикета */
	status tinyint(1) NOT NULL,									/* 0 - закрыт тикет, 1 - тикет открыт, 2 - новый тикет */
	PRIMARY KEY (ticket_ID)
)TYPE=MyISAM AUTO_INCREMENT=1000000;
<><>
/* ------------------------------------------------------------------------------------------------
	Тикет система - СООБЩЕНИЯ
	{>ticketsystem_msgs<}
------------------------------------------------------------------------------------------------ */
CREATE TABLE ticketsystem_msgs (
	msg_ID int(11) unsigned NOT NULL auto_increment,
	ticket_ID int(11) unsigned NOT NULL,						/*ID тикета*/
	message_ID varchar(32) NOT NULL,							/*MD5 сообщения*/
	datesend datetime NOT NULL default '0000-00-00 00:00:00',	/*дата и время отправки*/
	msg longtext NOT NULL default '',	
	status tinyint(1) NOT NULL,									/*Статус - 0 прочитанное, 1 новое, 2 - отправлен ответ*/
	PRIMARY KEY (msg_ID)
)TYPE=MyISAM AUTO_INCREMENT=1;