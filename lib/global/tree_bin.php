<? 
#Получение пути где находиться категория. Возвращает массив родительский категорий до самого верхнего уровня
function PathToCategory($id)
	{	GLOBAL $mysql;
		$id = (int) $id;
			if($id <= 0) return NULL;
		$path = array();
			if(!$mysql->sql_select("SELECT id FROM category WHERE id=$id") || $mysql->row <= 0) return NULL;
		$curr=$id;
		while(1){
			if($curr==0)break;
			$q=$mysql->sql_select("SELECT id, parentID, name, name_en, typ FROM category WHERE id=$curr");
			$row=$mysql->fetch_var_array($q);
			$path[]=$row;
			$curr=$row[parentID];			
		}
		return array_reverse($path);
	}
	
#Получение полного списка где находиться категория в виде массива
function _recursiveGetCategoryList( $path, $level )
{	GLOBAL $mysql;
	$q = $mysql->sql_select( "SELECT id, parentID, name, name_en, typ FROM category WHERE parentID=".$path[$level-1]["id"]." ORDER BY sort ASC" );
	$res = array();
	$id = 0; 
	while( $row=$mysql->fetch_var_array($q) )
	{
		$row["level"] = $level;
		$res[] = $row; 
		if ( $level <= count($path)-1 )
		{   
			if ( (int)$row["id"] == (int)$path[$level]["id"] )
			{  
				$id = $row["id"];
				$array = _recursiveGetCategoryList( $path, $level+1 );
				foreach( $array as $val )
					$res[] = $val;
			}
		}
	}
 	return $res;
}

#Построение дерева категорий
function GetCategoryList($id)
{	GLOBAL $mysql;
	$path = PathToCategory($id);
	$res = array();
	$q = $mysql->sql_select( "SELECT id, parentID, name, name_en, typ FROM category WHERE parentID=0 ORDER BY sort ASC");
	$qq=$mysql->fetcharray();
	$res[] = array( "id" => 1, "parentID" => 0, 
					"name" => $qq[name], "name_en" => $qq[name_en], "typ" => $qq[typ], "level" => 0 );
					
	$q = $mysql->sql_select( "SELECT id, parentID, name, name_en, typ FROM category WHERE parentID=1 ORDER BY sort ASC");							
	while( $row = $mysql->fetch_var_array($q) )
	{
		$row["level"] = 1;
		$res[] = $row;
		if ( count($path) > 1 )
		{ 
			if ( $row["id"] == $path[1]["id"] )
			{  
				$array = _recursiveGetCategoryList( $path, 2 );
				foreach( $array as $val )
					$res[] = $val;
			}
		}
	}
	return $res;
}


?>