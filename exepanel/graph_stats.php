<?
session_start();
$pth="../";
###################БЛОК ВКЛЮЧЕНИЙ В global_include.php##############################
//Подключение конфигурационного файла
require_once($pth."/lib/global/global_include.php");


######################################################################

//************************************************************************ 
// Проверка на авторизацию 
if(loginvalid()){
	
//************************************************************************ 
// Инициализация класса и подключение к базе данных
	$mysql=new db($dbhost,$dbname, $dblogin, $HTTP_SESSION_VARS["xxdbxx"]);
	if(!$mysql->connect()) die($m[_INST_ERROR1]);
	// Инициализация класса работы с логгированием и шаблонизацией
	$template = new tpl_logg($locdir,$homedir,$url,$deftpl,$mail,$deflang,$dbhost,$dbname,$dblogin,$HTTP_SESSION_VARS["xxdbxx"]);


//************************************************************************ 
// Создание графика отчета
	function sell_stat($type) {
		GLOBAL $mysql,$template,$m;
		
		$period = (int) getfromget("period");
		$from = getfromget("from");
		$to = getfromget("to");
		
		// При указание промежутка ДАТ
		if(!EMPTY($from) && !EMPTY($to)) {
			$days = 
			(int)(mktime(0, 0, 0, date("m",strtotime(str_replace('.','-',$to))), date("d",strtotime(str_replace('.','-',$to))), date("Y",strtotime(str_replace('.','-',$to)))) -
			mktime(0, 0, 0, date("m",strtotime(str_replace('.','-',$from))), date("d",strtotime(str_replace('.','-',$from))), date("Y",strtotime(str_replace('.','-',$from)))))
			/60/60/24; ++$days;
			// Определение группировки в зависимости от разницы в датах
			switch($days) {
				case $days <=1: {
					$group = "DATE_FORMAT( datepay, '%m.%d' ) AS dt";
					$xaxis = $m["_SELL_STAT_TIME"];
				break;
				}
				case ($days >1 && $days <28): {
					$group = "DATE_FORMAT( datepay, '%m.%d' ) AS dt";
					$xaxis = $m["_SELL_STAT_MONTHS"]; 
				break;
				}
				case ($days >= 28 && $days < 365): {
					$group = "DATE_FORMAT( datepay, '%y.%m' ) AS dt";
					$xaxis = $m["_SELL_STAT_YEAR"];
				break;
				}
				case $days >= 365: {
					$group = "DATE_FORMAT( datepay, '%Y' ) AS dt";
					$xaxis = $m["_SELL_STAT_YEARONLY"];
				break;
				}	
			}
			$r = $mysql->query("
				SELECT SUM(amount) AS amount, $group
				FROM history_pay
				WHERE ( DATE_FORMAT(datepay, '%Y.%m.%d') BETWEEN '$from' AND '$to') AND STATUS =1
				GROUP BY dt");
				
			// Определение делений на ось X
			switch($days) {
				case $days <=1: {
					// Массив в который заносяться часы соответственно
					for($i=0;$i<=23;$i++)$data[]=0;
					// Описание подписей по X, 24 часа
					$dt=array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
					while($row = $mysql->fetch_array($r)) {
						$data[(int)$row['dt']]=$row['amount']; 
					}
				break;
				}
				case ($days >1 && $days < 28): {
					for($i=0;$i<$days;$i++) {
						$weekday = date("m.d", mktime(0, 0, 0, date("m",strtotime(str_replace('.','-',$from))), date("d",strtotime(str_replace('.','-',$from)))+$i, date("Y",strtotime(str_replace('.','-',$from)))));
						$dt[]=$weekday; $data[]=0;
					}
					while($row = $mysql->fetch_array($r)) 
						foreach($dt as $key=>$value) if($value == $row['dt']) $data[$key] = $row['amount'];
				break;
				}
				default : {
					while($row = $mysql->fetch_array($r)) {
						$data[]=$row['amount']; 
						$dt[]=$row['dt'];
					}
				}
			}
			
			$period_title = "$from - $to";
		}
		//Иначе
		else 
			// Просмотр периоды за который оторажать
			switch($period) {
				// Сегодня
				case "1": {
					$r = $mysql->query("
						SELECT SUM(amount) as amount, DATE_FORMAT(datepay, '%H') as dt
						FROM history_pay
						WHERE DATE_FORMAT(datepay, '%Y-%m-%d') = CURDATE()
						GROUP BY dt");
						
					// Массив в который заносяться часы соответственно
					for($i=0;$i<=23;$i++)$data[]=0;
					// Описание подписей по X, 24 часа
					$dt=array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
					
					while($row = $mysql->fetch_array($r)) {
						$data[(int)$row['dt']]=$row['amount']; 
						
					}
					$period_title = $m["_SELL_STAT_TODAY"];
					$xaxis = $m["_SELL_STAT_TIME"];
				break;
				}
				// Вчера
				case "2": {
					$r = $mysql->query("
						SELECT SUM(amount) as amount, DATE_FORMAT(datepay, '%H') as dt
						FROM history_pay
						WHERE TO_DAYS(NOW()) - TO_DAYS(datepay) = 1
						GROUP BY dt");
					// Массив в который заносяться часы соответственно
					for($i=0;$i<=23;$i++)$data[]=0;
					// Описание подписей по X, 24 часа
					$dt=array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
					while($row = $mysql->fetch_array($r)) {
						$data[(int)$row['dt']]=$row['amount']; 
					}
					$period_title = $m["_SELL_STAT_YESTERDAY"];
					$xaxis = $m["_SELL_STAT_TIME"];
				break;
				}
				// За неделю
				case "3": {
					$r = $mysql->query("
						SELECT SUM(amount) as amount, DATE_FORMAT(datepay, '%m-%d') as dt
						FROM history_pay
						WHERE DAYOFYEAR(DATE_FORMAT(datepay, '%Y-%m-%d')) BETWEEN DAYOFYEAR(CURDATE())-7 AND DAYOFYEAR(CURDATE())
						GROUP BY dt");
					for($i=6;$i<>-1;$i--) {
						$weekday = date("m-d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
						$dt[]=$weekday; $data[]=0;
					}
					while($row = $mysql->fetch_array($r)) 
						foreach($dt as $key=>$value) if($value == $row['dt']) $data[$key] = $row['amount'];
					
					$period_title = $m["_SELL_STAT_WEEK"];
					$xaxis = $m["_SELL_STAT_MONTHS"];
				break;
				}
				// За месяц
				case "4": {
					$r = $mysql->query("
						SELECT SUM(amount) as amount, DATE_FORMAT(datepay, '%m-%d') as dt
						FROM history_pay
						WHERE DAYOFYEAR(DATE_FORMAT(datepay, '%Y-%m-%d')) BETWEEN DAYOFYEAR(CURDATE())-28 AND DAYOFYEAR(CURDATE())
						GROUP BY dt");
					for($i=28;$i<>-1;$i--) {
						$weekday = date("m-d", mktime(0, 0, 0, date("m"), date("d")-$i, date("Y")));
						$dt[]=$weekday; $data[]=0;
					}
					while($row = $mysql->fetch_array($r)) 
						foreach($dt as $key=>$value) if($value == $row['dt']) $data[$key] = $row['amount'];
					
					$period_title = $m["_SELL_STAT_MONTH"];
					$xaxis = $m["_SELL_STAT_MONTHS"];
				break;
				}
				// За все время
				case "5": {
					$r = $mysql->query("
						SELECT SUM(amount) as amount, DATE_FORMAT(datepay, '%Y-%m') as dt
						FROM history_pay
						GROUP BY dt");
					while($row = $mysql->fetch_array($r)) {
						$data[]=$row['amount']; 
						$dt[]=$row['dt'];
					}
					$period_title = $m["_SELL_STAT_TOTAL"];
					$xaxis = $m["_SELL_STAT_YEAR"];
				break;
				}
			}
			
			
		if($mysql->num_rows($r)==0) $data=array(0,0,0);
		if($mysql->num_rows($r)==1 && $type == 'profit') $data[]=0;
		
		if($type == 'profit') {
			//Вывод линейного графика
			show_graph_line($data,$dt,$m["_SELL_STAT_LABEL1"]." ($period_title)",$xaxis);
		} else {
			//Вывод стобчатого графика
			show_graph_bar($data,$dt,$m["_SELL_STAT_LABEL2"]." ($period_title)",$xaxis);
		}
	}
//************************************************************************ 
// Линейный график
	function show_graph_line($data,$dt,$title,$xaxis) {
		GLOBAL $pth;
		DEFINE("TTF_DIR","$pth/lib/font/");
		require_once("$pth/lib/graph/jpgraph.php");
		require_once("$pth/lib/graph/jpgraph_line.php");
		require_once("$pth/lib/graph/jpgraph_bar.php");
		// Create the graph. These two calls are always required
		$graph = new Graph(640,300,"auto");    
		$graph->SetScale("textint");
		
		// Рисуем линию
		$line=new LinePlot($data);
		$graph->Add($line);
		
		// добавим тень
		$graph->SetShadow(true, 3, array(222,222,222));

		// определим отступ для области вывода
		$graph->img->SetMargin(80,40,30,60);
		// определим цвет отступа
		$graph->SetMarginColor('white');
		// создадим рамку
		$graph->SetFrame(true,'gray',1);
			
		//Пишем по оси X
		$graph->xaxis->SetFont(FF_ARIAL);
		$graph->xaxis->SetWeight(1);
		$graph->xaxis->SetTickLabels($dt);
		$graph->xaxis->SetLabelAngle(45);

		//Пишем по оси Y
		$graph->yaxis->SetFont(FF_ARIAL);
		$graph->yaxis->SetWeight(1);
		$graph->yaxis->SetLabelFormatString('%dy.e');
			
		//Title
		$graph->title->SetFont(FF_ARIAL);
		$graph->title->set($title);
		$graph->xaxis->title->SetFont(FF_ARIAL);	
		
		// установим цвет осей и подписей
		$graph->xaxis->SetColor('darkgray', 'darkgray');
		$graph->yaxis->SetColor('darkgray', 'darkgray');
		
		// определим отступ сверху
		$graph->yaxis->scale->SetGrace(30);
		
		//Тень
		$graph->SetShadow();		
		
		// Настраиваем лин-график
		$line->SetColor("red");
		$line->SetWeight(2);
		$graph->legend->SetFont(FF_ARIAL);
		$line->SetLegend($xaxis);
		// Display the graph
		$graph->Stroke();
	}
	
//************************************************************************ 
// Столбчатый график
	function show_graph_bar($data,$dt,$title,$xaxis) {
		GLOBAL $pth;
		DEFINE("TTF_DIR","$pth/lib/font/");
		require_once("$pth/lib/graph/jpgraph.php");
		require_once("$pth/lib/graph/jpgraph_line.php");
		require_once("$pth/lib/graph/jpgraph_bar.php");
		// создадим область для вывода диаграммы
		$graph = new Graph(640,300,"auto");

		// определим масштабирование по осям
		$graph->SetScale("textint");

		// добавим тень
		$graph->SetShadow(true, 3, array(222,222,222));

		// определим отступ для области вывода
		$graph->img->SetMargin(80,40,50,60);

		// определим цвет отступа
		$graph->SetMarginColor('white');

		// создадим рамку
		$graph->SetFrame(true,'gray',1);

		//Пишем по оси X
		$graph->xaxis->SetFont(FF_ARIAL);
		$graph->xaxis->SetTickLabels($dt);
		$graph->xaxis->SetLabelAngle(45);
		
		//Пишем по оси Y
		$graph->yaxis->SetFont(FF_ARIAL);
		$graph->yaxis->SetLabelFormatString('%dy.e');

		$graph->title->SetFont(FF_ARIAL);
		$graph->title->set($title);
		// граница вокруг диаграммы
		$graph->SetBox(true, 'gray');
		
		// спрячем метки на осях
		$graph->xaxis->HideTicks();
		$graph->yaxis->HideTicks();

		// установим цвет осей и подписей
		$graph->xaxis->SetColor('darkgray', 'darkgray');
		$graph->yaxis->SetColor('darkgray', 'darkgray');

		// определим шрифт для вывода подписей на осях
		$graph->xaxis->title->SetFont(FF_ARIAL,FS_NORMAL);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL);

		// определим отступ сверху
		$graph->yaxis->scale->SetGrace(20);
		
		// создадим диаграмму
		$bplot = new BarPlot($data);

		// определим цвет заполнения столбцов
		$bplot->SetFillColor('#ff9900');

		// покажем значения над каждым столбцом
		$bplot->value->SetFont(FF_ARIAL);
		$bplot->value->Show();
		$bplot->value->SetAngle(90);
		$bplot->value->SetFormat('%d');

		// тень столбцов
		$bplot->SetShadow();

		// установим цвет для значений
		$bplot->value->SetColor('#e40000');
		
		//Установка легенду
		$graph->legend->SetPos(0.05,0.05,'right','center');
		$graph->legend->SetFont(FF_ARIAL);
		$bplot->SetLegend($xaxis);
		
		// установим ширину столбцов
		$bplot->SetWidth(0.2);

		// добавим диаграмму в область вывода
		$graph->Add($bplot);
		
		// отобразим результат
		$graph->Stroke();
	}

//************************************************************************ 
// Управление статистикой
		switch(getfromget("type")) {
			case "profit":  return sell_stat("profit"); break;
			case "report": 	return sell_stat("report"); break;
			default:exit;
		}	
} else header("LOCATION: $url/exepanel/");
		

?>