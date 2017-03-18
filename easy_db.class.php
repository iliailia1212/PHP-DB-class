<?php
include_once 'Config.class.php'; //Подключение файла настроек
class DB{
    public $mysqli;
    public $cfg;
    public $status = false; //Статус подключения
    public function __construct() {
        $this->cfg = Config::$cfg;//Подключаем настройки
        $this->mysqli = @new mysqli($this->cfg['mysql_host'], $this->cfg['mysql_user'], $this->cfg['mysql_pass'], $this->cfg['mysql_db']); //Подключаемся к БД
        if($this->mysqli->connect_errno) $this->status = "Невозможно подключиться к базе данных. Код ошибки: ".$this->mysqli->connect_error; //Если происзошла ошибка, возращаем её
        else return $this->status = true; //Если ошибок нет возращаем true
    }
    public function querys($qs){ //Выполнение запросов разделённых через ;
        if($this->mysqli->connect_error) return false;
        $status = true;
        foreach(explode(';',$qs) as $q){
            if(!$this->mysqli->query($q)) $status = false;
        }
        return $status; //Если хоть один не будет выполнен вернём false, иначе true
    }
    public function query($q){//Выполнение запроса
        if($this->mysqli->connect_error) return false;
        return $this->mysqli->query($q); //Возращаем результат запроса
    }
    public function get($q){//Получение данных с БД
        if($this->mysqli->connect_error) return false;
        $res = $this->mysqli->query($q);//Выполнение запроса
        if($res) return $res->fetch_all(MYSQLI_ASSOC); //Парсинг ответа
        else return false;//В случаи ошибки возращаем false
    }
    public function escape($str){//Экранирование строки
        if($this->mysqli->connect_error) return false;
        return $this->mysqli->real_escape_string($str);
    }
    public function last_id(){//Возврат последнего id
        return $this->mysqli->insert_id;
    }
}

?>
