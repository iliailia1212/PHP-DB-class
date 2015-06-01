<?php
$cfg = array(
	'bd' => array(
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'name' => 'test_bd',
	)
);
include_once('bd.class.php');
$bd = new bd();
$bd->connect($cfg);//Подключаемся

$bd->create('test',array(//Создаём запись в таблце ```test```
	'name' => 'test2',//С данными name: test
	'num' => 3//И num: 3
));

$bd->update('test',//Обновляем таблцу ```test```
  array(//Фильтр
    array('num', 3)// num = 3
  ),
  array('name'=>'test3')//Обновляем имя на ```test3```
);

$bd->delete('test',//Удаляем данны из таблце ```test```
  array(//Фильтр
    array('id',1)//id = 1
  )
);


print_r(
  $bd->read('test',//Получаем данны из таблци ```test```
    array('*'),//Все колонки
    array(//Фильтр
     array('name','test3') //name = test3
    )
  )
);

var_dump($bd->err());//Смотрим ошибки

#Пример более сложного фильтра

print_r(
  $bd->read('test',//Получаем данны из таблци ```test```
    array('*'),//Все колонки
    array(//Фильтр
     array('id',3,'<','OR'), //id < 3
     array('name','test3'), //name = test3
    )//Результат: `id` < 3 OR `name` = 'test3'
  )
);
?>
