<?
session_start();
$locdir=$HTTP_SERVER_VARS["DOCUMENT_ROOT"];
$pth="../";
###################БЛОК ВКЛЮЧЕНИЙ В global_include.php##############################
@include "$pth/lib/global/global_include.php";
if($_SESSION['lang']=="en")$var="_en"; else $var="";
$err = '';
$step_post = (int)getfrompost("step");
$step_get = (int)getfromget("step") <= 0 ? "1" : (int)getfromget("step");

// Log ERRORS
function logger($msg) {
	GLOBAL $err;
	$err .="$msg<br>\n";
}

//Switch STEP name
function step() {
	GLOBAL $m;
	switch($GLOBALS['step_get']) {
		case "1": return $m['_INST_STEP1'];
		case "2": return $m['_INST_STEP2'];
		case "3": return $m['_INST_STEP3'];
		case "4": return $m['_INST_STEP4'];
		case "5": return $m['_INST_STEP5'];
		case "6": return $m['_INST_SUCCESS'];
		default : return "THIS STEP NOT FOUND :)";
	}
}

// Connect to DB
function connect() {
	GLOBAL $mysql, $dbhost, $dbname, $dblogin, $dbpwd;
	if(strlen(getfrompost('dbhost'))>4) 
		$mysql = new db(getfrompost('dbhost'), getfrompost('dbname'), getfrompost('dblogin'), getfrompost('dbpwd')); 
			else $mysql = new db($dbhost, $dbname, $dblogin, decryptdata($dbpwd)); 	
	if(!$mysql->connect()) return false; else return true;
}

// Replace '     on   "
function rpl($str) {
	return str_replace(array("'", "\""), array('"', '"'),$str);
}

function action ($select) {
	GLOBAL  $m,$pth;
	switch($select) {
		//SAVE to config.php Mysql Settings
		case '2': {
				if(!is_writeable($pth."/lib/global/config.php")) @chmod($pth."/lib/global/config.php", 0777);
				$f_mas = @file($pth."/lib/global/config.php");
				$f=@fopen($pth."/lib/global/config.php", "w+");
				for($i=0;$i<=9; $i++) fwrite($f, $f_mas[$i]);
				fwrite($f, "$"."dbhost='".getfrompost('dbhost')."'".";"."\n");
				fwrite($f, "$"."dbname='".getfrompost('dbname')."'".";"."\n");
				fwrite($f, "$"."dblogin='".getfrompost('dblogin')."'".";"."\n");
				fwrite($f, "$"."dbpwd='".encryptdata(getfrompost('dbpwd'))."'".";"."\n");
				fwrite($f, "?>"."\n");
				fclose($f);
		break;
		}
		case '3': {
				if(!is_writeable($pth."/lib/global/config.php")) @chmod($pth."/lib/global/config.php", 0777);
				$f_mas = @file($pth."/lib/global/config.php");
				$f=@fopen($pth."/lib/global/config.php", "w+");
				fwrite($f, "<?"."\n");
				fwrite($f, "$"."deftpl='".getfrompost('deftpl')."'".";"."\n");
				fwrite($f, "$"."url='".getfrompost('url')."'".";"."\n");
				fwrite($f, "$"."homedir='".getfrompost('homedir')."'".";"."\n");
				fwrite($f, "$"."mail='".getfrompost('mail')."'".";"."\n");
				fwrite($f, "$"."deflang='".getfrompost('deflang')."'".";"."\n");
				fwrite($f, "$"."title='".rpl(getfrompost('title'))."'".";"."\n");
				fwrite($f, "$"."mini_title='".rpl(getfrompost('mini_title'))."'".";"."\n");
				fwrite($f, "$"."meta_description='".rpl(getfrompost('meta_description'))."'".";"."\n");
				fwrite($f, "$"."meta_keywords='".rpl(getfrompost('meta_keywords'))."'".";"."\n");
				for($i=10;$i<=14; $i++) fwrite($f, $f_mas[$i]);
		}
		
	}
}
// INSERT/UPDATE ADMIN RECORD
function insert_user($fio, $login, $pwd) {
	GLOBAL $mysql;
	if(is_resource($mysql->connect)) {
		$r = $mysql->query("SELECT id FROM user_admin WHERE id=1");
		if($mysql->num_rows($r) == 0) $mysql->query("INSERT INTO user_admin VALUES(1,'$fio','$login','$pwd')");
		if($mysql->num_rows($r) == 1) $mysql->query("UPDATE user_admin SET name='$fio', login='$login', pwd='$pwd' WHERE id=1");
	} else $step_get = 2;
}

// Install TABLES FROM BASE.SQL
function install() {
	GLOBAL $mysql,$m,$pth;
	if(is_resource($mysql->connect)) {
		if(!is_writeable($pth."/lib")) @chmod($pth."/lib", 0777);
		if(!is_writeable($pth."/lib/global")) @chmod($pth."/lib/global", 0777);
		if(!is_writeable($pth."/lib/global/config.php")) @chmod($pth."/lib/global/config.php", 0777);
		if(!is_writeable($pth."/photo_goods")) @chmod($pth."/photo_goods", 0777);
		if(!is_writeable($pth."/secretfiles")) @chmod($pth."/secretfilesp", 0777);
		
		//$mysql->query("ALTER DATABASE '".$mysql->dbname."' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$sql_mas = explode("<><>", @file_get_contents("base.sql"));
		for($i=0;$i<count($sql_mas);$i++){
			preg_match('#\{>([a-z0-9\-_]*?)\<}#is', $sql_mas[$i], $tblname);
			$sub_sql = explode("||", $sql_mas[$i]);
			$r = $mysql->query($sub_sql[0]);
			if($r) {
				echo "CREATE TABLE $tblname[1] <font class='textgreen'>SUCCESSFULLY</font><br>";
				for($sb=1; $sb<count($sub_sql); $sb++)
					$r2 = $mysql->query($sub_sql[$sb]);
			}	else echo "CREATE TABLE $tblname[1] <font class='textred'>ERROR</font><br>";
			
		}
		//Insert root category
		//$tree= new DBTree($mysql, "category", "id");
		//$datacat=array("sort" => 0, "name" => "Главная", "name_en" => "Root");
		//$tree->clear($datacat);
		
	} else $step_get = 2;
}

//Save settings
if($step_post >=1)
	switch($step_post) {
		//Agreement
		case "1": {
			if($HTTP_POST_VARS['license'] == 'yes') {
				 $step_get = 2; 
				 $HTTP_SESSION_VARS['license'] = 'yes';
			} else { $step_get = 1; logger($m["_INST_ERROR"]); $HTTP_SESSION_VARS['license']= 'no';}
		break;
		}
		//SAVE config.php - Database
		case "2": {
			if(empty($HTTP_POST_VARS['dbname']) || empty($HTTP_POST_VARS['dbhost']) || empty($HTTP_POST_VARS['dblogin']) || $HTTP_SESSION_VARS['license'] <> 'yes') { 
				$step_get = 2; 
				logger($m["_ERR_EMPTY_REQUIRE"]); 
			}
				else {
					if(!connect()) { logger($m["_INST_ERROR1"]); $step_get = 2; } 
						else {
						//SAVE confing
						action(2);
						// For NEXT STEP
						$step_get = 3;
					}
				}
		break;
		}
		//SAVE config.php - Database
		case "3": {
			if(empty($HTTP_POST_VARS['url']) || empty($HTTP_POST_VARS['mail']) || empty($HTTP_POST_VARS['homedir']) || $HTTP_SESSION_VARS['license'] <> 'yes') { 
				$step_get = 2; 
				logger($m["_ERR_EMPTY_REQUIRE"]); 
			}
				else {
					if(!connect()) { logger($m["_INST_ERROR1"]); $step_get = 2; } 
						else {
						//SAVE confing
						action(3);
						// For NEXT STEP
						$step_get = 4;
						$step_post = 4;
					}
				}
		break;
		}
		// Insert ADMIN RECORD
		case "5": {
			if(!empty($HTTP_POST_VARS['fio']) && !empty($HTTP_POST_VARS['login'])) {
				if(connect()) {   
					if(strlen(trim($HTTP_POST_VARS['pwd'])) >= 8) {
						$fio = addslashes(trim($HTTP_POST_VARS['fio']));
						$login = addslashes(trim($HTTP_POST_VARS['login']));
						$pwd = md5crypt(trim($HTTP_POST_VARS['pwd']));
						echo insert_user($fio, $login, $pwd);
						$step_post = 6;
						$step_get = 6;
					} else { $step_get = 5; logger($m["_ERR_FEWPWD"]);}
				} else { logger($m["_INST_ERROR1"]); $step_get = 2; }
			}	else { $step_get = 5; logger($m["_ERR_EMPTY_REQUIRE"]);}
		break;
		}
}
?>
<html>
<head>
<title><?=$m[_INST_TITLE];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="StyleSheet" href="img/style.css" type="text/css">
</head>
<body>
<div id="main">
	
	<div id="container">
		<div id="header">
			<div id="logo"><a href="http://flexstudio.biz"><img src="img/logo.gif" title="Flex Studio: Web-design, HTML, DHTML, CSS, AJAX, JavaScript, PHP, XML, MySQL"></a></div>
			<div id="lng"><a href="index.php?change_lang=en&step_id=<?=$step_get;?>">ENG</a> || <a href="index.php?change_lang=ru&step_id=<?=$step_get;?>">RUS</a></div>	
		</div>
		<p align='center' style="padding:12px; background:url('img/rect.gif') top no-repeat;"><span class="textwhite"><?=$m[_INST_TITLE]."</span><br><br><br><span class='textblack'>".(step()). "</span>";?></p>
		<p align='center'><?="<span class='textred'>$err</span>";?>
<?		
	//Agreement
	if($step_get == 1) {
		echo @file_get_contents("license$var.txt");
		echo"
		
		<form action='index.php?step=2' method='POST'>
			<input type=hidden name='step' value='1'>
			<input type='checkbox' name='license' value='yes'>
			$m[_INST_STEP1_YES]<br>
			<p align='center'><input type='submit' value='$m[_INST_NEXT]'></p>
		</form>
		";
	}
	if($step_get == 2) {
		echo "
		<form action='index.php?step=3' method='POST'>
		<input type='hidden' name='step' value='2'>
		<table align=center style='border-collapse:collapse;'>
			<tr>
				<td align=right>".$m[_INST_DBHOST]." *: </td>
				<td><input type='text' name='dbhost' value='$dbhost'></td>
			</tr>
			<tr>
				<td align=right>".$m[_INST_DBNAME]." *: </td>
				<td><input type='text' name='dbname' value='$dbname'></td>
			</tr>
			<tr>
				<td align=right>".$m[_INST_DBLOGIN]." *: </td>
				<td><input type='text' name='dblogin' value='$dblogin'></td>
			</tr>
			<tr>
				<td align=right>".$m[_INST_DBPWD]." : </td>
				<td><input type='password' name='dbpwd' value='".decryptdata($dbpwd)."'></td>
			</tr>
			</table><br>
			<center><input type=button value='$m[_INST_BACK]' onClick='javascript:history.back();'><input type=submit value='".$m[_INST_NEXT]."'></center>
			</form>
			<br><center>* ".$m[_LABEL_OBLIG]."</center><br>
		";
	}
	//Config.php 
	if($step_get == 3) {
		$f=@dir($pth."/templates");
			while($name=$f->read()) {
				if($name<>"." && $name<>".." && is_dir($pth."/templates/".$name)) {
					if($name == $deftpl) $slkd = " selected"; else $slkd = "";
					$option.="<option value='$name'$slkd>$name</option>";
				}
			}
				
			echo "

				<form action='index.php?step=4' method='POST'>
					<input type='hidden' name='step' value='3'>
					<table align=center style='border-collapse:collapse;'>
						<tr>
							<td align=right>".$m[_INST_ADDRSURL]." *: </td>
							<td><input type='text' name='url' value='$url' size='25'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_EMAIL]." *: </td>
							<td><input type='text' name='mail' value='$mail'><br><span class='textred'>ROOT: $locdir</span></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_HOMEDIR]." *: </td>
							<td><input type='text' name='homedir' value='$homedir' size='35'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_SITE_TITLE].": </td>
							<td><input type='text' name='title' value='$title' size='35'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_MINI_TITLE].": </td>
							<td><input type='text' name='mini_title' value='$mini_title'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_META_DESCR].": </td>
							<td><input type='text' name='meta_description' value='$meta_description' size='35'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_META_KEY].": </td>
							<td><input type='text' name='meta_keywords' value='$meta_keywords' size='35'></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_DEFLANG]." : </td>
							<td><select name='deflang'><option value='ru'>Russian<option value='en'>English</select></td>
						</tr>
						<tr>
							<td align=right>".$m[_INST_DEFTPL]." : </td>
							<td><select name='deftpl'>$option</select></td>
						</tr>
						</table><br><br>
						<center><input type=button value='$m[_INST_BACK]' onClick='javascript:history.back();'><input type=submit value='".$m[_INST_NEXT]."'></center>
						</form>
						<br><center>* ".$m[_LABEL_OBLIG]."</center><br>
					";
	}
	//Install Tables
	if($step_post == 4 && $step_get == 4) {
		install();
		echo "<br><center><input type=button value='$m[_INST_BACK]' onClick='javascript:history.back();'><input type=button value='".$m[_INST_NEXT]."' onClick=\"window.location='index.php?step=5'\"></center>";
	}
	//Authorization DATA
	if($step_get == 5) {
		echo "
		<form action='index.php?step=5' method='POST'>
			<input type=hidden name='step' value='5'>
			<table align=center style='border-collapse:collapse;'>
				<tr>
					<td align=right>".$m[_INST_FIO]." *: </td>
					<td><input type='text' name='fio'></td>
				</tr>
				<tr>
					<td align=right>".$m[_INST_LOGIN]." *: </td>
					<td><input type='text' name='login'></td>
				</tr>
				<tr>
					<td align=right>".$m[_INST_PWD]." *: </td>
					<td><input type='password' name='pwd'></td>
				</tr>
			</table><br>
				<center><input type=submit value='".$m[_CP_ADD]."'></center>	
				<br><center>* ".$m[_LABEL_OBLIG]."</center><br>
		</form>			
		";
	}
	if($step_post == 6 && $step_get == 6) {
		echo "
		<center><br><a class='textgreen' href='$url/exepanel'>".$m[_CP_TITLECP]."</a></center>
		";
	}

?>
		</p>
	</div>
</div>
<center> <font size=1>Copyright © 2007 <a href="http://flexstudio.biz">flexstudio.biz</a> (BY). All rights reserved.</font></center>
</body>
</html>