<?
//************************************************************************
// Класс для работы с категориями магазина. Использует алгоритм NESTED SETS
class DBTree {
	var $db;	// Подсоединение к базе данных
	var $table;	// Таблица категорий
	var $id;	// Имя поля с id идентицикацией категории

	var $left = 'cat_left';
	var $right = 'cat_right';
	var $level = 'cat_level';

	var $qryParams = '';
	var $qryFields = '';
	var $qryTables = '';
	var $qryWhere = '';
	var $qryGroupBy = '';
	var $qryHaving = '';
	var $qryOrderBy = '';
	var $qryLimit = '';
	var $sqlNeedReset = true;
	var $sql;	

//************************************************************************
// Конструктор
// $DB : Класс подключения к базе данных
// $tableName : Название таблицы с категорией
// $itemId : Имя поля идентификации каждой записи
// $fieldNames : Дополнительное поле. Имена полей девых,правых уровней и самого уровня вложенности
//				 array(
//					'left' => 'cat_left', 
//					'right' => 'cat_right', 
//					'level' => 'cat_level'
//				 )
	function __construct(&$DB, $tableName, $itemId, $fieldNames=array()) {
		if(empty($tableName)) trigger_error("phpDbTree: Unknown table", E_USER_ERROR);
		if(empty($itemId)) trigger_error("phpDbTree: Unknown ID column", E_USER_ERROR);
		$this->db = $DB;
		$this->table = $tableName;
		$this->id = $itemId;
		if(is_array($fieldNames) && sizeof($fieldNames)) 
			foreach($fieldNames as $k => $v)
				$this->$k = $v;
	}
//************************************************************************
// Составление SQL запроса "приоткрытого" дерева по ID категории
// ID: номер категории.
// sessionArrayName: Имя массива в сессии
// $showroot - отображать или не отображать самую главную запись
// $countgoods - TRUE - включить выборку с подсчетом товаров, FALSE - без подсчета
// Возвращает SQL запрос
	function get_tree_from_id($id, $showroot=TRUE, $sessionArrayName="category_expand"){
		$ncat = (int) $id; // id нужного элемента
		if(count($_SESSION[$sessionArrayName])>0)
			foreach($_SESSION[$sessionArrayName] as $key=>$value)
				if($key > 1) $sql_add.="OR (B.".$this->id." = '".$value."' AND B.".$this->left." BETWEEN A.".$this->left." AND A.".$this->right.") ";
		
		$query = "SELECT A.* FROM ".$this->table." A, ".$this->table." B 
				WHERE (B.".$this->id." = '".$id."' AND B.".$this->left." BETWEEN A.".$this->left." AND A.".$this->right.") ".$sql_add."
				ORDER BY  A.".$this->left."";
				
		$result = $this->db->query($query);
		if (($alen = $this->db->num_rows($result)) == 0) return false;
		$i = 0;
		$sql = "";
		while ($row = $this->db->fetch_array($result))
		{
			if ((++$i == $alen) && ($row[$this->left]+1 == $row[$this->right])) break;
			
			if(!$countgoods)
			$sql .= " OR (".$this->level."=".($row[$this->level]+1)." AND ".$this->left.">".$row[$this->left]." AND ".$this->right."<".$row[$this->right]. ")";
		}

		$sql = "
			SELECT *, IF (".$this->left."+1 < ".$this->right.", 1, 0) AS nflag
			FROM ".$this->table." 
			WHERE ".$this->level."=".(($showroot) ? 0 : 1)."	".$sql." 
			ORDER BY ".$this->left."";
		
		return $sql;
	}
//************************************************************************
//  Добавление в сессионный массив ID категорий
// ID: Номер категории
// sessionArrayName: имя массива в сессии
function catExpandCategory( $ID, $sessionArrayName )
{		
		if(!isset($_SESSION[$sessionArrayName])) $_SESSION[$sessionArrayName]=array();
		$c=$this->enumpath($ID);
		$mas = array();
		while($ro=$this->db->fetch_array($c))$mas[]=$ro;
		$mas=array_reverse($mas);
		for($i=0;$i<=count($mas)-1;$i++) 
			if($mas[$i][nflag]==1 && $mas[$i][level]>0) {
				$k=$i; $mem=$mas[$i][id]; $_SESSION[$sessionArrayName][$mas[$k][id]] = $mem; break; 
			}
		
}
//************************************************************************
//  Удаление из массива ID категорий
// ID: Номер категории
// sessionArrayName: имя массива в сессии
function catShrinkCategory( $ID, $sessionArrayName ) {	
		if(!isset($_SESSION[$sessionArrayName])) $_SESSION[$sessionArrayName]=array();
		if(count($_SESSION[$sessionArrayName])>0){
				$nod=$this->GetElementInfo($ID);
				$query = "SELECT ".$this->id." FROM ".$this->table."
							WHERE ".$this->left." >= ".$nod[0]." AND ".$this->right." <= ".$nod[1]." ORDER BY ".$this->left."";
						$r=$this->db->query($query);
						if($r)
						while($row=$this->db->fetch_array($r)) { unset( $_SESSION[$sessionArrayName][$row[id]] );}		
		}
}
//************************************************************************
//  Возвращает категории на одном уровне  с $id в пределе родителя
// $id - ID категории 
function cat_on_level($id) {
		$cat=$this->GetParent($id);
		$query = "SELECT * FROM ".$this->table." 
				WHERE ".$this->left." >= ".$cat[$this->left]." AND ".$this->right." <= ".$cat[$this->right]." 
				AND ".$this->level." = ".($cat[$this->level]+1)." ORDER BY ".$this->left;
		return $this->db->query($query);
}
//************************************************************************
//  Смещает позицию категории 
// $id1 - категория которую необходимо сместить
// $id2 - категория куда необходимо переместить
// $position - положение элемента до или после $id2
function ChangePositionAll($id1, $id2, $position = 'after', $condition = '') {
        $node_info = $this->GetNodeInfo($id1);
		if (FALSE === $node_info) return FALSE;
        list($leftId1, $rightId1, $level1) = $node_info;
        $node_info = $this->GetNodeInfo($id2);
        if (FALSE === $node_info) return FALSE;
        list($leftId2, $rightId2, $level2) = $node_info;
        if ($level1 <> $level2) return FALSE;
        if ('before' == $position) {
            if ($leftId1 > $leftId2) {
                $sql = 'UPDATE ' . $this->table . ' SET '
                . $this->right . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->right . ' - ' . ($leftId1 - $leftId2) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->right . ' +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->right . ' END, '
                . $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->left . ' - ' . ($leftId1 - $leftId2) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->left . ' + ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->left . ' END '
                . 'WHERE ' . $this->left . ' BETWEEN ' . $leftId2 . ' AND ' . $rightId1;
            } else {
                $sql = 'UPDATE ' . $this->table . ' SET '
                . $this->right . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->right . ' + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN ' . $this->right . ' - ' . (($rightId1 - $leftId1 + 1)) . ' ELSE ' . $this->right . ' END, '
                . $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->left . ' + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN ' . $this->left . ' - ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->left . ' END '
                . 'WHERE ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . ($leftId2 - 1);
            }
        }
        if ('after' == $position) {
            if ($leftId1 > $leftId2) {
                $sql = 'UPDATE ' . $this->table . ' SET '
                . $this->right . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->right . ' - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->right . ' +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->right . ' END, '
                . $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->left . ' - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->left . ' + ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->left . ' END '
                . 'WHERE ' . $this->left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . $rightId1;
            } else {
                $sql = 'UPDATE ' . $this->table . ' SET '
                . $this->right . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->right . ' + ' . ($rightId2 - $rightId1) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN ' . $this->right . ' - ' . (($rightId1 - $leftId1 + 1)) . ' ELSE ' . $this->right . ' END, '
                . $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->left . ' + ' . ($rightId2 - $rightId1) . ' '
                . 'WHEN ' . $this->left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN ' . $this->left . ' - ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->left . ' END '
                . 'WHERE ' . $this->left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId2;
            }
        }
        $res = $this->db->query($sql);
        if (FALSE === $res) return FALSE;

        return TRUE;
    }

//************************************************************************
// Возращает cat_left, cat_right, cat_level  в виде массива по ID, иначе FALSE в случае ошибки
// $ID : номер категории
	function getElementInfo($ID) { return $this->getNodeInfo($ID); }
	function getNodeInfo($ID) {
		$this->sql = 'SELECT '.$this->left.','.$this->right.','.$this->level.' FROM '.$this->table.' WHERE '.$this->id.'=\''.$ID.'\'';
		if(($query=$this->db->query($this->sql)) && ($this->db->num_rows($query) == 1) && ($Data = $this->db->fetch_array($query)))
			return array((int)$Data[$this->left], (int)$Data[$this->right], (int)$Data[$this->level]); 
		else { return false; trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR); }
	}

//************************************************************************
// Очищает таблицу категорий и создает новую с главной записью
// $data : Доп. параметр массив. Присвоение главное записи названия и т.д.
// Пример $data = array ("name" =>"Главная")
	function clear($data=array()) {
		// Очистка таблицы
		if((!$this->db->query('TRUNCATE '.$this->table)) && (!$this->db->query('DELETE FROM '.$this->table))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Преобразование массива перед добавлением
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			if(sizeof($data)) $fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= '1,2,0';

		//Вставка записи
		$this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		return $this->db->insert_id();
	}

//************************************************************************
// Обновляет запись категории
// $ID : Идентификатор категории
// $data : Массив данных для апдэйта: array(<field_name> => <fields_value>)
	function update($ID, $data) {
		$sql_set = '';
		foreach($data as $k=>$v) $sql_set .= ','.$k.'=\''.$v.'\'';
		return $this->db->query('UPDATE '.$this->table.' SET '.substr($sql_set,1).' WHERE '.$this->id.'=\''.$ID.'\'');
	}

//************************************************************************
// Добавление новой записи таблицу категории
// $ID : an ID of the parent element
// $data : array with data to be inserted: array(<field_name> => <field_value>)
// Возвращает TRUE - если ОК, FALSE - если ошибочка
	function insert($ID, $data) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		//Преобразование данных перед вставкой
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			$fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= ($rightId).','.($rightId+1).','.($level+1);

		// Создание запроса, места для вставки
		if($ID) {
			$this->sql = 'UPDATE '.$this->table.' SET '
				. $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
				. $this->right.'=IF('.$this->right.'>='.$rightId.','.$this->right.'+2,'.$this->right.')'
				. 'WHERE '.$this->right.'>='.$rightId;
			if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);
		}

		// Вставка новой записи
		$this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->db->query($this->sql))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		return $this->db->insert_id();
	}

//************************************************************************
// Доп.функция добавление  новой записи в таблицу категорий
// $ID : Номер элемента ПОСЛЕ которого нужно вставить запись
// $data : массив данных: array(<field_name> => <field_value>)
// Возвращает TRUE если ОК, и FALSE если ошибка
	function insertNear($ID, $data) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID)))
			trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Преобразование DATA ARRAY
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			$fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= ($rightId+1).','.($rightId+2).','.($level);

		// Создание места для добавление новой записи
		if($ID) {
			$this->sql = 'UPDATE '.$this->table.' SET '
			.$this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
			.$this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'+2,'.$this->right.')'
                               . 'WHERE '.$this->right.'>'.$rightId;
			if(!($this->db->query($this->sql))) trigger_error("phpDbTree error:".$this->db->error(), E_USER_ERROR);
		}

		// Добавление новой записи
		$this->sql = 'INSERT INTO '.$this->table.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->db->query($this->sql))) trigger_error("phpDbTree error:".$this->db->error(), E_USER_ERROR);

		return $this->db->insert_id();
	}


//************************************************************************ 
//Перемещение родителя и всех его потомков
// $ID : Номер эелемента которого перемещаем 
// $newParentID : ID элемента куда перемещаем 
// Возвращает FALSE в случае ошибки 
   function moveAll($ID, $newParentId) { 
      if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR); 
      if(!(list($leftIdP, $rightIdP, $levelP) = $this->getNodeInfo($newParentId))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR); 
      if($ID == $newParentId || $leftId == $leftIdP || ($leftIdP >= $leftId && $leftIdP <= $rightId)) return false; 

      
      if ($leftIdP < $leftId && $rightIdP > $rightId && $levelP < $level - 1 ) { 
         $this->sql = 'UPDATE '.$this->table.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', ' 
                           .'IF('.$this->left.' BETWEEN '.($leftId).' AND '.($rightId).', '.$this->right.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->right.')),  ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->left.'-'.($rightId-$leftId+1).', ' 
                           .'IF('.$this->left.' BETWEEN '.$leftId.' AND '.($rightId).', '.$this->left.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->left. ')) ' 
            . 'WHERE '.$this->left.' BETWEEN '.($leftIdP+1).' AND '.($rightIdP-1) 
         ; 
      } elseif($leftIdP < $leftId) { 
         $this->sql = 'UPDATE '.$this->table.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightIdP.' AND '.($leftId-1).', '.$this->left.'+'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'-'.($leftId-$rightIdP).', '.$this->left.') ' 
            . '), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.$rightIdP.' AND '.$leftId.', '.$this->right.'+'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'-'.($leftId-$rightIdP).', '.$this->right.') ' 
            . ') ' 
            . 'WHERE '.$this->left.' BETWEEN '.$leftIdP.' AND '.$rightId 
            .' OR '.$this->right.' BETWEEN '.$leftIdP.' AND '.$rightId 
         ; 
      } else { 
         $this->sql = 'UPDATE '.$this->table.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightId.' AND '.$rightIdP.', '.$this->left.'-'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'+'.($rightIdP-1-$rightId).', '.$this->left.')' 
            . '), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'+'.($rightIdP-1-$rightId).', '.$this->right.') ' 
            . ') ' 
            . 'WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightIdP 
            . ' OR '.$this->right.' BETWEEN '.$leftId.' AND '.$rightIdP 
         ; 
      } 
      return $this->db->query($this->sql) or trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR); 
   } 

//************************************************************************
// Удалеят выбранный элемент, КРОМЕ его потомков
// $ID : ID удаляемого элемента
// Возвращает TRUE если ОК, и FALSE если ошибка
	function delete($ID) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Запрос на удаление записи
		$this->sql = 'DELETE FROM '.$this->table.' WHERE '.$this->id.'=\''.$ID.'\'';
		if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Очистка свободного места в дереве
		$this->sql = 'UPDATE '.$this->table.' SET '
			. $this->left.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->left.'-1,'.$this->left.'),'
			. $this->right.'=IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->right.'-1,'.$this->right.'),'
			. $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->level.'-1,'.$this->level.'),'
			. $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'-2,'.$this->left.'),'
			. $this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'-2,'.$this->right.') '
			. 'WHERE '.$this->right.'>'.$leftId
		;
		if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		return true;
	}

//************************************************************************
// Удаляет все принадлежащие категории потомков
// $ID : ID главной удаляемой категории
// Возвращает TRUE, FALSE
	function deleteAll($ID) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Удаляемые записи
		$this->sql = 'DELETE FROM '.$this->table.' WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightId;
		if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		// Очистка места в дереве
		$deltaId = ($rightId - $leftId)+1;
		$this->sql = 'UPDATE '.$this->table.' SET '
			. $this->left.'=IF('.$this->left.'>'.$leftId.','.$this->left.'-'.$deltaId.','.$this->left.'),'
			. $this->right.'=IF('.$this->right.'>'.$leftId.','.$this->right.'-'.$deltaId.','.$this->right.') '
			. 'WHERE '.$this->right.'>'.$rightId
		;
		if(!$this->db->query($this->sql)) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		return true;
	}

//************************************************************************
// Возвращает потомков выбранного элемента
// $ID : номер элемента для которого будут возвращены потомки
// $start_level : относительный уровень, от которого начало перечислять потомков
// $end_level : последний относительный уровень, на котором заканчивается перечисление потомка
//   1. Если $end_level не выдается только для потомков  
//      $start_level levels будут перечислены
//   2. Уровень должен быть всегда больше чем 0
// Уровень 1  направляет потомков элемента родителя
// Для использования нужен класс DB
	function enumChildrenAll($ID) { return $this->enumChildren($ID, 1, 0); }
	function enumChildren($ID, $start_level=1, $end_level=1) {
		if($start_level < 0) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		$whereSql1 = ' AND '.$this->table.'.'.$this->level;
		$whereSql2 = '_'.$this->table.'.'.$this->level.'+';

		if(!$end_level) $whereSql = $whereSql1.'>='.$whereSql2.(int)$start_level;
		else {
			$whereSql = ($end_level <= $start_level) 
				? $whereSql1.'='.$whereSql2.(int)$start_level
				: ' AND '.$this->table.'.'.$this->level.' BETWEEN _'.$this->table.'.'.$this->level.'+'.(int)$start_level
					.' AND _'.$this->table.'.'.$this->level.'+'.(int)$end_level;
		}

		$this->sql = $this->sqlComposeSelect(array(
			'', // Params
			'', // Fields
			$this->table.' _'.$this->table.', '.$this->table, // Tables
			'_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
				.' AND '.$this->table.'.'.$this->left.' BETWEEN _'.$this->table.'.'.$this->left.' AND _'.$this->table.'.'.$this->right
				.$whereSql
		));

		return $this->db->query($this->sql);
	}

//************************************************************************
// Возвращает РОДИТЕЛЬСКИЙ путь до самой верхней записи
// $ID : Номер записи для которой вернуть путь
// $showRoot : Опционально, вернет путь включая самую главную запись
// Для использования нужен класс DB
	function enumPath($ID, $showRoot=false) {
		$this->sql = $this->sqlComposeSelect(array(
			'IF ('.$this->table.'.'.$this->left.'+1 < '.$this->table.'.'.$this->right.', 1, 0) AS nflag, '.$this->table.'.'.$this->level.' as level,', // Params
			'', // Fields
			$this->table.' _'.$this->table.', '.$this->table, // Tables
			'_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
				.' AND _'.$this->table.'.'.$this->left.' BETWEEN '.$this->table.'.'.$this->left.' AND '.$this->table.'.'.$this->right
				.(($showRoot) ? '' : ' AND '.$this->table.'.'.$this->level.'>0'), // Where
			'', // GroupBy
			'', // Having
			$this->table.'.'.$this->left // OrderBy
		));
		return $this->db->query($this->sql);
	}
	function enumPath2($ID, $showRoot=false) {
		$this->sql = $this->sqlComposeSelect(array(
			$this->table.'.name as name,'.$this->table.'.name_en as name_en,', // Params
			'', // Fields
			$this->table.' _'.$this->table.', '.$this->table, // Tables
			'_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
				.' AND _'.$this->table.'.'.$this->left.' BETWEEN '.$this->table.'.'.$this->left.' AND '.$this->table.'.'.$this->right
				.(($showRoot) ? '' : ' AND '.$this->table.'.'.$this->level.'>0'), // Where
			'', // GroupBy
			'', // Having
			$this->table.'.'.$this->left // OrderBy
		));
		return $this->db->query($this->sql);
	}
//************************************************************************
//Возвращает SQL запрос для полученя родительской категории (С DATA array)
// $ID : Номер категории для которой возвратить ее родителя
// $level : Уровеь вложенности
// Для использования нужен класс DB
	function getParent($ID, $level=1) {
		if($level < 1) trigger_error("phpDbTree error: ".$this->db->error(), E_USER_ERROR);

		$this->sql = $this->sqlComposeSelect(array(
			$this->table.'.name AS name, '.$this->table.'.name_en AS name_en, 
			'.$this->table.'.'.$this->left.', '.$this->table.'.'.$this->right.', '.$this->table.'.'.$this->level.',', // Params
			'', // Fields
			$this->table.' _'.$this->table.', '.$this->table, // Tables
			'_'.$this->table.'.'.$this->id.'=\''.$ID.'\''
				.' AND _'.$this->table.'.'.$this->left.' BETWEEN '.$this->table.'.'.$this->left.' AND '.$this->table.'.'.$this->right
				.' AND '.$this->table.'.'.$this->level.'=_'.$this->table.'.'.$this->level.'-'.(int)$level // Where
		));
		return $this->db->fetch_array($this->db->query($this->sql));
	}

//************************************************************************
// Очистка SQL параметров
	function sqlReset() {
		$this->qryParams = ''; $this->qryFields = ''; $this->qryTables = ''; 
		$this->qryWhere = ''; $this->qryGroupBy = ''; $this->qryHaving = ''; 
		$this->qryOrderBy = ''; $this->qryLimit = '';
		return true;
	}

//************************************************************************
	function sqlSetReset($resetMode) { $this->sqlNeedReset = ($resetMode) ? true : false; }

//************************************************************************
//Назначение параметров SQL запросам
	function sqlParams($param='') { return (empty($param)) ? $this->qryParams : $this->qryParams = $param; }
	function sqlFields($param='') { return (empty($param)) ? $this->qryFields : $this->qryFields = $param; }
	function sqlSelect($param='') { return $this->sqlFields($param); }
	function sqlTables($param='') { return (empty($param)) ? $this->qryTables : $this->qryTables = $param; }
	function sqlFrom($param='') { return $this->sqlTables($param); }
	function sqlWhere($param='') { return (empty($param)) ? $this->qryWhere : $this->qryWhere = $param; }
	function sqlGroupBy($param='') { return (empty($param)) ? $this->qryGroupBy : $this->qryGroupBy = $param; }
	function sqlHaving($param='') { return (empty($param)) ? $this->qryHaving : $this->qryHaving = $param; }
	function sqlOrderBy($param='') { return (empty($param)) ? $this->qryOrderBy : $this->qryOrderBy = $param; }
	function sqlLimit($param='') { return (empty($param)) ? $this->qryLimit : $this->qryLimit = $param; }

//************************************************************************
// Составление запроса SELECT
	function sqlComposeSelect($arSql) {
		$joinTypes = array('join'=>1, 'cross'=>1, 'inner'=>1, 'straight'=>1, 'left'=>1, 'natural'=>1, 'right'=>1);

		$this->sql = 'SELECT '.$arSql[0].' ';
		if(!empty($this->qryParams)) $this->sql .= $this->sqlParams.' ';

		if(empty($arSql[1]) && empty($this->qryFields)) $this->sql .= $this->table.'.'.$this->id;
		else {
			if(!empty($arSql[1])) $this->sql .= $arSql[1];
			if(!empty($this->qryFields)) $this->sql .= ((empty($arSql[1])) ? '' : ',') . $this->qryFields;
		}
		$this->sql .= ' FROM ';
		$isJoin = ($tblAr=explode(' ',trim($this->qryTables))) && ($joinTypes[strtolower($tblAr[0])]);
		if(empty($arSql[2]) && empty($this->qryTables)) $this->sql .= $this->table;
		else {
			if(!empty($arSql[2])) $this->sql .= $arSql[2];
			if(!empty($this->qryTables)) {
				if(!empty($arSql[2])) $this->sql .= (($isJoin)?' ':',');
				elseif($isJoin) $this->sql .= $this->table.' ';
				$this->sql .= $this->qryTables;
			}
		}
		if((!empty($arSql[3])) || (!empty($this->qryWhere))) {
			$this->sql .= ' WHERE ' . $arSql[3] . ' ';
			if(!empty($this->qryWhere)) $this->sql .= (empty($arSql[3])) ? $this->qryWhere : 'AND('.$this->qryWhere.')';
		}
		if((!empty($arSql[4])) || (!empty($this->qryGroupBy))) {
			$this->sql .= ' GROUP BY ' . $arSql[4] . ' ';
			if(!empty($this->qryGroupBy)) $this->sql .= (empty($arSql[4])) ? $this->qryGroupBy : ','.$this->qryGroupBy;
		}
		if((!empty($arSql[5])) || (!empty($this->qryHaving))) {
			$this->sql .= ' HAVING ' . $arSql[5] . ' ';
			if(!empty($this->qryHaving)) $this->sql .= (empty($arSql[5])) ? $this->qryHaving : 'AND('.$this->qryHaving.')';
		}
		if((!empty($arSql[6])) || (!empty($this->qryOrderBy))) {
			$this->sql .= ' ORDER BY ' . $arSql[6] . ' ';
			if(!empty($this->qryOrderBy)) $this->sql .= (empty($arSql[6])) ? $this->qryOrderBy : ','.$this->qryOrderBy;
		}
		if(!empty($arSql[7])) $this->sql .= ' LIMIT '.$arSql[7];
		elseif(!empty($this->qryLimit)) $this->sql .= ' LIMIT '.$this->qryLimit;

		if($this->sqlNeedReset) $this->sqlReset();

		return $this->sql;
	}
//************************************************************************

}
?>