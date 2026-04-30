<?php
// Примеры echo и print
echo "<h2>Примеры echo и print</h2>";
echo "Это вывод через echo<br>";
print "Это вывод через print<br><br>";


//Переменные разных типов 
echo "<h2>Переменные разных типов</h2>";

$intVar = 42;               // целое число
$floatVar = 3.14;           // число с плавающей точкой
$stringVar = "Строка";      // строка
$boolVar = true;            // логический тип

echo "Целое: $intVar<br>";
echo "Дробное: $floatVar<br>";
echo "Строка: $stringVar<br>";
echo "Булево: " . ($boolVar ? "true" : "false") . "<br><br>";


// Предопределённые переменные
echo "<h2>Предопределённые переменные</h2>";

echo "Ваш браузер: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
echo "Имя файла: " . $_SERVER['PHP_SELF'] . "<br><br>";


//Константы
echo "<h2>Константы</h2>";

define("SITE_NAME", "Учебный сайт по PHP");
const VERSION = "1.0";

echo "Название сайта: " . SITE_NAME . "<br>";
echo "Версия: " . VERSION . "<br><br>";


//  Предопределённые константы 
echo "<h2>Предопределённые константы</h2>";

echo "Текущая строка: " . __LINE__ . "<br>";
echo "Путь к файлу: " . __FILE__ . "<br>";
echo "Папка файла: " . __DIR__ . "<br>";
?>
