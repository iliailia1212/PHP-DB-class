#MySqli Class
### connect($cfg)
Подключение к БД
Возращает true при удачном подключении и false при ошибки.
```
    $cfg = array(
        'db'=>array(
    		'host' => 'localhost',
    		'user' => 'root',
    		'password' => '',
    		'name' => 'test_bd',
        )
    )
```
### read(string $table, [array  $columns], [array  $filter], [array  $order])
Читает данные с таблице  
```$table``` - Название таблицы  
```$columns``` - Массив колонок  
```$filter``` -  [Фильтр](https://github.com/iliailia1212/db/blob/master/README.md#filter)  
```$order``` - массива вида: "название колонки по которой сортировать", ["Порядок соритровки ASC|DESC"]  

### create(string $table, array  $data)
Добавляет данные в таблицу  
```$table``` - Название таблицы  
```$data``` - Ассоциативный массив ввида:  
``` 'Ключ' => 'Данные' ```

### update(string $table, array  $filter, array  $data)
Обновляет данные в таблице  
```$table``` - Название таблицы  
```$filter``` - [Фильтр](https://github.com/iliailia1212/db/blob/master/README.md#filter)  
```$data``` - Ассоциативный массив ввида:  
``` 'Ключ' => 'Данные' ```

### delete(string $table, [array  $filter])
Удаляет данные из таблици  
```$table``` - Название таблицы 
```$filter``` - [Фильтр](https://github.com/iliailia1212/db/blob/master/README.md#filter)  

### err()
Возращяет ошибку или false в случие отсутсвии ошибки

### filter  
На вход принимает двумерный массив:
```
array(
    array(
        'Название колонки',
        'Значение',
        ['Оператор сравнения'],
        ['Логический опретаор (после данного фильтра)']
    )
)

```
####[Примеры](https://github.com/iliailia1212/db/blob/master/demo.php) 
