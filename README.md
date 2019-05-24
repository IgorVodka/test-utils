# Raptor Test Utils v0.1.0

(c) Михаил Каморин aka raptor_MVK

## Описание

Состоит из следующих компонентов:
 - трейт `ExtraUtils`, содержащий набор вспомогательных методов для тестирования;
 - трейт `ExtraAssertions`, содержащий набор дополнительных утверждений для упрощения и улучшения визуализации
    тестирования (также подключает трейт `ExtraUtils`);
 - директория `DataLoader` содержит классы, которые могут использоваться для загрузки тестовых данных из файлов в
    формате JSON.

## Установка

Для установки необходимо:

- Открыть `composer.json`, добавить название пакета в блок `require` и ссылку на данный репозиторий в блок
`repositories`:

```
    "require": {
+        "raptor/test-utils": "1.0.*"
    },
    ...
    "repositories": [
+        {
+          "type": "git",
+          "url": "git@github.com:raptor-mvk/test-utils.git"
+        },
        ...
    ],
```

- Выполнить команду `composer update`


## Использование

### Вспомогательные методы

В класс, содержащий тесты, или в базовый для всех тестов класс необходимо подключить трейт `ExtraUtils` или
`ExtraAssertions`. После этого становятся доступны следующие статические методы:

 - `invokeMethod(object $object, string $methodName, ?array $parameters = null)` вызывает защищённый или приватный метод
    объекта с указанными параметрами

### Дополнительные утверждения

В класс, содержащий тесты, или в базовый для всех тестов класс необходимо подключить трейт `ExtraAssertions`. После
этого становятся доступны следующие дополнительные утверждения:

 - `assertArraysAreSame(array $expected, array $actual, ?string $message = null)` проверяет утверждение, что массивы
    полностью идентичны
 - `assertArraysAreSameIgnoringOrder(array $expected, array $actual, ?string $message = null)` проверяет утверждение,
    что массивы содержат одинаковые элементы, для ассоциативных массивов при этом порядок элементов не важен

### Загрузчик тестовых данных

Загрузчик позволяет выделить тестовые данные из провайдера в коде в отдельный JSON-файл. В результате загрузки из файла
формируется набор именованных тестовых данных, где данные для каждого теста обёрнуты в объект-контейнер. Значения
конкретных полей из файла возвращаются геттерами. Данный механизм позволяет решить следующие задачи:
 - выделение тестовых данных из кода
 - возможность передачи в тестирующий метод большого количества параметров без раздувания сигнатуры метода
 - возможность иерархической организации тестовых данных, когда различия данных между тестами незначительны

Требования к JSON-файлу:
 - файл должен содержать **массив** JSON-объектов, массив может содержать единственный объект
 - имена всех полей объектов не должны начинаться с подчёркивания, кроме специально оговоренных ниже случаев
 - каждый объект в массиве должен быть одного из двух типов:
     - объект 1-го типа содержит набор тестовых данных. В этом случае в объекте **не должно быть** служебного поля
     `_children` и **должно быть** служебное поле `_name`
     2. объект 2-го типа содержит массив наборов тестовых данных с заданными значениями по умолчанию для некоторых
     полей. В этом случае в объекте **должно быть** служебное поле `_children` и **не должно быть** служебного поля
     `_name`
 - служебное поле `_name` должно быть строковым, оно содержит название тестового набора данных
 - значения служебного поля `_name` должны быть уникальны и непусты
 - служебное поле `_children` должно содержать массив, к которому предъявляются те же требования, что и к корневому
    массиву файла

При обработке файла для объектов 1-го типа выполняется следующий алгоритм:
 - если данный объект имеет объект-родителя 2-го типа, то перебираются все неслужебные поля объекта-родителя
 - для каждого поля проверяется, задано ли значение этого поля для рассматриваемого объекта 1-го типа; если это не так,
    то для этого поля задаётся значение из объекта-родителя
 - если объект-родитель также имеет объект-родителя 2-го типа, то процедура повторяется для него
 
Промежуточным результатом работы загрузчика является массив, содержащий все полученные объекты 1-го типа с ключами,
являющимися значениями служеного поля `_name` в этих объектах. Само поле `_name` из объектов исключается.

Затем для каждого элемента массива значение оборачивается в объект-контейнер.

## История версий

v0.1.0

- реализованы дополнительные утверждения `assertArraysAraSame` и `assertArraysAreSameIgnoringOrder`
- реализована иерархия классов для загрузки тестовых данных в формате JSON (`DataLoader` и `DataProcessor`)

## Авторы

- Михаил Каморин aka raptor_MVK
