<?php
session_start();
@include("./lib/global/config.php");
//************************************************************************ 
//  Формирование случайных чисел
function randomPassword($length = 1) {                      
	$all = explode( " ", "0 1 2 3 4 5 6 7 8 9");                                    
	for($i=0;$i<$length;$i++) {                                  
		srand((double)microtime()*1000000);                          
		$randy = rand(0, 9);
		$passw .= $all[$randy];                                       
	}                                                            
	return $passw;                                                
}
//************************************************************************ 
// Запсь каждого сформированного числа в строку
$r1 = randomPassword($length = 1);
$r2 = randomPassword($length = 1);
$r3 = randomPassword($length = 1);
$r4 = randomPassword($length = 1);
$r5 = randomPassword($length = 1);
$r6 = randomPassword($length = 1);

$nm="$r1$r2$r3$r4$r5$r6";

//************************************************************************ 
// Сохранение числа в сессии для верификации
$type = $_GET["type"];
switch($type) {
	case 'feedback': {
		if(!isset($_SESSION["feedback_img"])) $_SESSION["feedback_img"] = $nm;
		else $_SESSION["feedback_img"]=$nm;
	}
	case 'register': {
		if(!isset($_SESSION["register_img"])) $_SESSION["register_img"] = $nm;
		else $_SESSION["register_img"]=$nm;
	}
}


$rn=array($r1, $r2, $r3, $r4, $r5, $r6);

//************************************************************************ 
// Ширина/высота рамки, середина
$w=100;
$h=20;
$ctr=ceil($h/2);

//************************************************************************ 
//  Создание холста
$img  = ImageCreateTrueColor($w,$h);

//************************************************************************ 
// Рисование фонов
function bg_fillarc($img,$x,$y) {
	$color = ImageColorAllocate($img, rand(170,255),rand(170,255),rand(170,255));
	imagefilledarc ($img, $x, $y, rand(5,20), rand(5,20), 0, 360, $color, IMG_ARC_EDGED );
}
function exec_fillarc($img,$c) {
	$cx=0;
	$cy=5;
	for($i=0;$i<$c;$i++){
		bg_fillarc($img,rand(1,$GLOBALS['w']),rand(1,$GLOBALS['h']));
		//bg_fillarc($img,$cx,$cy+rand(7,10));
		//bg_fillarc($img,$cx,$cy+rand(13,15));
		//bg_fillarc($img,$cx,$cy+rand(18,20));
		//$cx+=rand(4,10);
	}
}
function bg_pixel($img,$x,$y) {
	$color = ImageColorAllocate($img, rand(100,255),rand(100,255),rand(100,255));
	imagesetpixel ($img, $x, $y, $color);
}
function exec_pixel($img,$c,$back=FALSE) {
	if($back)imagefilledrectangle($img, 0,0,$GLOBALS['w'],$GLOBALS['h'], ImageColorAllocate($img, 255,255,255));
	for($i=0;$i<$c;$i++)
		bg_pixel($img, rand(1,$GLOBALS['w']), rand(1,$GLOBALS['h']));
}

function bg_liner($img, $x1,$y1, $x2,$y2) {
	$color = ImageColorAllocate($img, rand(120,200),rand(120,200),rand(120,00));
	imageline($img, $x1,$y1, $x2,$y2, $color);
}
function exec_liner($img,$c,$back=FALSE) {
	if($back)imagefilledrectangle($img, 0,0,$GLOBALS['w'],$GLOBALS['h'], ImageColorAllocate($img, 255,255,255));
	for($i=0;$i<$c; $i++)
		bg_liner($img, rand(3, round($GLOBALS['w']/2-3)), rand(3, $GLOBALS['h']), rand(round($GLOBALS['w']/2), $GLOBALS['w']), rand(3, $GLOBALS['h']));
}


//************************************************************************ 
// Случайный выбор фона
switch(rand(0,3)) {
	case 0: exec_fillarc($img, 100); break;
	case 1: exec_pixel($img, 1000,true); break;
	case 2: exec_liner($img, rand(4,10),true); break;
	case 3: {
		exec_pixel($img, 500, true);
		exec_liner($img, rand(4,10));
		exec_fillarc($img, rand(10,25));
		break;
	}
}

//************************************************************************ 
// рисование рамки
ImageSetThickness($img,1);
imagerectangle($img, 1,1,$w-1,$h-1,ImageColorAllocate($img, 0,0,0));

//************************************************************************ 
// Угол отображение каждого символа
$angle=rand(-15,10);

//************************************************************************ 
// Выбор цвета текста
function rnd_color($img) {
	$color[0]=array(255,0,0);
	$color[1]=array(127,0,0);
	$color[2]=array(195,0,237);
	$color[3]=array(0,165,0);
	$color[4]=array(60,130,148);
	$clr = $color[rand(0,4)];
	return ImageColorAllocate($img,$clr[0],$clr[1],$clr[2]);
}
//************************************************************************ 
// Выбор шрифта
function rnd_font() {
		return "crystal.ttf"; break;
}
//************************************************************************ 
// Вывод на созданном холстве текста
$r=$rn[0];
$x=8;
for ($i=1; $i<=6; $i++)
 {
	imageTtfText($img, 15, 0, $x, $ctr+9, rnd_color($img), $homedir."/lib/font/".rnd_font(), "$r");
	$r=$rn[$i];
	$x=$x+15;
	$angle=rand(-30,40);
 }


// Фильтр
imagefilter($img, IMG_FILTER_SMOOTH, rand(10,20));

header('Content-type: image/png'); 
ImagePNG($img);

ImageDestroy($img);
?> 
