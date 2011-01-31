<?
//************************************************************************
// Вывод картинки с увеличением в 20 раз макс.
$img=str_replace("/", "", $_GET['img']);
$zoom = $_GET['zoom'];
$file="../photo_goods/$img";
if(@file_exists($file)) {
	$zoom=(int)$zoom;
	if($zoom >20) $zoom=20;
	if($zoom <=0) $zoom=0;
	$sz = GetImageSize($file);
	// Определение MIME типа для загрузки в переменную
	switch($sz[mime]){
		case "image/png": $img=imagecreatefrompng($file); break;
		case "image/jpeg": $img=imagecreatefromjpeg($file); break;
		case "image/gif": $img=imagecreatefromgif($file);break;
	}
	// Увеличение
	if($zoom<>0) {
		$h = $zoom*60; 
		$w = $h*$sz[0]/$sz[1];
		$img_new = imagecreatetruecolor($w,$h);
		imagecopyresampled($img_new,$img,0,0,0,0,$w,$h,$sz[0],$sz[1]);
		$img=$img_new;
	}
	// Определение MIME типа изображения
	header("Content-type: ".$sz['mime']);
	switch($sz[mime]){
		case "image/png": imagePNG($img,"",70);break;
		case "image/jpeg": imageJPEG($img,"",70);break;
		case "image/gif": imageGIF($img,"",70);break;
	}
	ImageDestroy($img);
	ImageDestroy($img_new);
	
} else echo "file not found";
?>