<?php
Class db{
	private $mysqli;
	public function connect($cfg){//Функция подключения ,при вызове передаём настройки с данными от бд
		$this->mysqli = new mysqli($cfg['bd']['host'], $cfg['bd']['user'], $cfg['bd']['password'], $cfg['bd']['name']);//подключение к бд
		if($this->mysqli->connect_errno !== 0) throw new Exception('Ошибка подключения к бд #'.$this->mysqli->connect_errno);
		$this->mysqli->query("SET NAMES 'utf8'");//установка кодировки
		$this->mysqli->set_charset("utf8");//установка кодировки
	}
	public function read($table,$columns=array('*'),$filter=false,$order=false){//функция для получения данных из бд ,при вызове передаём: название таблица, [название столбцов], [дополнительный sql фильтр]
		return $this->changeToArray($this->mysqli->query('SELECT '.$this->columns($columns)." FROM `$table`".$this->filter($filter).$this->order($order)));//преобразовываем их в двух мерный массив и возращяем
	}
	public function create($table,$data){//добавления данных в бд, при вызове передаём: названия таблицы, массив вида 'название столбца'=>'данные'
		$keys = array();//тут будут хранится название столбцов
		$values = array();//тут будут хранится вносимые данные
		foreach($data as $key=>$val) {//перебор входящего массива
			$keys[] = sprintf('`%s`',$key);//добавляем новое название столбца
			$values[] = $this->safety($val);//добавляем новые данные
		}
		$this->mysqli->query("INSERT INTO `$table` (".implode(', ', $keys).') VALUES ('.implode(', ', $values).')');//добалвяем данные в БД
	}
	public function update($table, $filter, $data){//функция редактирования, при вызове передаём: названия таблицы,фильтр, массив вида 'название столбца'=>'данные'
		$chunks = array();//тут будет хранится sql запросы
		foreach($data as $key=>$val) {//переберам данные
			$chunks[] = sprintf('`%s` = %s', $key, $this->safety($val));//добавляем новые данный в sql запрос
		}
		$this->mysqli->query("UPDATE `$table` SET ".implode(', ', $chunks).$this->filter($filter));//обновляем данные в бд
	}
	public function delete($table, $filter = false){//функция удаления ,при вызове передаём: название таблицы, [фильтр]
		$this->mysqli->query("DELETE FROM `$table`".$this->filter($filter));//удаляем данные из бд
	}
	public function err(){//функция возращает ошибки
		if($this->mysqli->errno == 0) return false;
		else return $this->mysqli->error;
	}
	private function filter($data) {//Функция для генерации sql фильтра данных
		if(empty($data)) return '';//Если данные пустны, возращяем пустую строку
		$counts = count($data);//Подсчитываем количество фильтров
		$text = ' WHERE';//Создаём переменую для фильтров
		foreach($data as $name=>$el){
			$data_n = $this->configs($el,array(array(true,'name'),array(true,'data'),array(false,'='),array(false,'AND')));//Генрируем массив с данными
			$text .= " `$data_n[0]` $data_n[2] ".$this->safety($data_n[1],array('array'));//Добавляем новые данные в нужном виде
			if(--$counts) $text .= ' '.$data_n[3];//Если этот элемент не последний, то добавляем логический оператор
		}
		return $text;
	}
	private function order($data) {//Функция для генерации sql соритровки
		if(empty($data)) return '';//Если данные пустны, возращяем пустую строку
		$data_n = $this->configs($data,array(array(true,'name'),array(false,'ASC')));//Генрируем массив с данными
		return 'ORDER BY `$data_n[0]` $data_n[1]';//Возращаем данные в нужном виде
	}
	private function columns($data) {//Функция для генерации списка колонок
		$list = array();//Создаём переменую для хранения колонок
		foreach($data as $el){//Перебераем список
			$list[] = (($el == '*')?$el:sprintf('`%s`', $el));//Добавляем колонку в нужном ввиде
		}
		return implode(', ', $list);//Возращаем данные добавив запятые
	}
	private function configs($arr, $def) {//Функция для герации массива данных исходя из данных значений и стандартных
		$i = 0;
		foreach($def as $val){//Перебераем стандартные значения
			if(!isset($arr[$i])) {//Если данных нету в массиве ,добавляем их из стандартных
				if($def[$i][0]) throw new Exception('Ошибка при генерации массива настроек, обезательные данные не были переданы!');//Если эти данные обезатенльные ,то выводим ошибку
				else $arr[$i] = $def[$i][1];//Если эти данные не обезательные берём их их стандартных данных
			}
			$i++;
		}
		return $arr;
	}
	private function safety($str, $not = array()) {//Функция для форматирования данных, устранения иньекций
		if(in_array(gettype($str),$not)) return '""';//Если тип переданных данные равен запрещёным типам, то возрёщаем пустую строку
		else switch(gettype($str)) {//Определяем тип данных
			case 'boolean':
				$str = $str?1:0;//Если данные типа boolean , то возращаем 1 или 0
			break;
			case 'integer':
				$str = $str;//Если это цифра ,то так её и оставляем
			break;
			case 'string':
				$str = '"'.$this->mysqli->real_escape_string($str).'"';//Если это строка, то добавляем кавычки с двух сторон и убираем все имеющиеся кавычки
			break;
			case 'array':
				$str = $this->safety(json_encode($str));//Если это массив ,то преобразовываем его в json данные
			break;
			default:
				$str = '""';//Если данные другого типа, то возращяем пустую строку
			break;
		}
		return $str;
	}
	private function changeToArray($result) {//внутреняя функция для преобразования sql данных в двухмерный массив
		if($result===false) return array();
		$results = array();
		while ($row = $result->fetch_assoc()){ $results[] = $row; }
		return $results;
	}
	public function __destruct() {//функция закрытия соединения.
		if(!empty($this->mysqli)) $this->mysqli->close();
	}
}
?>
