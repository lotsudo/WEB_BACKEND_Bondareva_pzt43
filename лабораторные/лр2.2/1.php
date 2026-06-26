<?php
    $integerVar = 42;
    $floatVar = 3.14159;
    $stringVar = "100 рублей";
    $boolVar = true;
    $nullVar = null;
    $name = "Иван Петров";
    $age = 22;
    $city = "Гродно";

    // Константы
    define("TAX_RATE", 0.2);
    const COMPANY = "ООО «Ромашка»";

    // Дата
    $dateString = "2025-04-10";
    echo "<h3>Переменные, Константы и типы данных </h3>";
        echo "<b>Переменные </b><br>";
            echo 'Выведите на экран значения переменных $integerVar, $floatVar, $stringVar, $boolVar, $nullVar.', "<p></p>";
            echo "<b>$integerVar, $floatVar, $stringVar, $boolVar, $nullVar. </b>";
    echo "<hr>";
        echo "<b>Константы </b>";
            echo "Выведите значения констант TAX_RATE и COMPANY.<br> ";
            echo TAX_RATE ," ",  COMPANY;
    echo "<hr>";
        echo "<b> Типы данных и приведение </b><br>";
            echo "Преобразуйте $stringVar в целое число двумя способами: явно (int) и неявно (сложив с 0). Выведите результаты и их типы.";
                $explicitInt = (int)$stringVar;
                $implicitInt = $stringVar + 0;
                echo "Явное: " . $explicitInt . " (тип: " . gettype($explicitInt) . ")\n";
                echo "Неявное: " . $implicitInt . " (тип: " . gettype($implicitInt) . ")\n";
    echo "<hr>";
    echo "<h3>Операции и приоритет</h3><br>";
        echo "<b>Арифметические операции и инкремент/декремент</b><br>";   
            echo 'Создайте выражение, демонстрирующее разницу между ++$i и $i++.<br> ';
                $i = 5;
            echo "<b>Исходное число:</b> $i <br>";
            echo "<b>Префиксный (++\$i):</b> " . ++$i . " (значение изменилось сразу) <br>";
                $i = 5;
            echo "<b>Постфиксный (\$i++):</b> " . $i++ . " (сначала вывел старое, прибавил потом) <br>";
            echo "<b>Значение \$i после вывода:</b> $i <br>";
    echo "<hr>";
        echo "<b> Строковые операции </b></br>";
            echo 'Составьте строку: "Имя: Иван Петров, возраст: 22, город: Гродно", используя переменные и оператор ..<br>';
            $newstring = "Имя: $name, возраст: $age, город: $city";
            echo $newstring;
    echo "<hr>";
        echo "<b>Операции сравнения и логические операции</b>";
            echo 'Используя оператор <=>, сравните $age и 25. Выведите результат.<br>';
            echo $age <=> 25;
    echo "<hr>";
        echo "<b>Приоритет операций</b>";
            echo 'Вычислите без скобок:2 + 3 * 4 - 1. Затем добавьте скобки, чтобы изменить порядок, и сравните. <br>';
            echo 2 + 3 * 4 - 1, " - Результат без скобок<br>";
            echo (2 + 3) * 4 - 1, " - Результат со скобками ((2 + 3) * 4 - 1)";
    echo "<hr>";
    echo "<h3>Операторы управления</h3><br>";
        echo "<b>Условные операторы</b><br>";
            echo 'Используя match, выведите категорию возраста:
                    до 18 – "ребёнок",
                    18–35 – "молодой",
                    36–60 – "взрослый",
                    старше 60 – "пенсионер".<br>';
                $output = match (true){
                    $age < 18 => "Ребёнок",
                    $age < 35 => "Молодой",
                    $age < 60 => "Взрослый",
                    $age > 60 => "Пенсионер", 
                };
            echo "Результат проверки:  $output <br>";
    echo "<hr>";
        echo "<b>Циклы</b><br>";
            echo 'Выведите числа от 1 до 10 с помощью while.<br>';
                $i = 1;
                while ($i<= 10) {
                    echo $i ," ";
                    $i++;
                };
    echo "<hr>";
        echo "<b>Операторы передачи управления</b><br>";
            echo 'Остановите выполнение цикла при достижении 8 с помощью break.<br>';
                $i = 1;
                while ($i<= 10) {
                    echo $i ," ";
                    $i++;
                    if ( $i == 8 ) break;
                };
    echo "<hr>";
        echo "<b>Операторы включения файлов</b><br>";
            echo 'Создайте отдельный файл config.php, в котором определите константы DB_HOST, DB_USER, DB_PASS. Подключите этот файл в основном скрипте с помощью require_once. Выведите значения констант.<br>';
                require_once 'config.php'   ;
                echo DB_HOST," ", DB_PASS," ", DB_USER;  
    echo "<hr>";
        echo "<b>Стрелочные функции</b><br>";
            echo 'Создайте массив чисел [1,2,3,4,5]. Используя array_map и стрелочную функцию, верните массив квадратов этих чисел.<br>';
                $numbers = [1, 2, 3, 4, 5];
                $squares = array_map(fn($n) => $n ** 2, $numbers);
                print_r($squares);
    echo "<hr>";
        echo "<b>Оператор return</b><br>";
            echo 'Напишите функцию divide($a, $b), которая возвращает результат деления, а если $b == 0, возвращает null (досрочный выход). Продемонстрируйте, что функция без return возвращает null.<br>';
                function divide($a, $b) {
                    if ($b == 0) return null;
                    return $a / $b;
                }
                var_dump(divide(10, 2));
                var_dump(divide(10, 0));
                echo 'Без return: <br>';
                function noReturn() {
                    $x = 10 + 5;
                };
                $result = noReturn();
                var_dump($result);
    echo "<hr>";
    echo "<h3>Математические функции и работа с датой/временем</h3><br>";
        echo "<b>Матеметические функции</b><br>";
            echo 'Сгенерируйте случайное целое число от 1 до 100.<br>';
                echo rand(1,100);
    echo "<hr>";
        echo "<b>Функции даты и времени</b><br>";
            echo 'Создайте объект DateTime для даты $dateString и добавьте к нему 2 недели. Выведите новую дату.<br>';  
                $dateString = '2026-04-15';
                $date = new DateTime($dateString);
                $date->modify('+2 weeks');
                echo $date->format('Y-m-d'); 
                echo "<hr>";
    echo "<b>Расчет значения функции по формуле</b><br>";
    echo 'Вычислите значение функции $y = \sqrt{|x|} + e^x + \sin(x^2)$ при заданном $x$.<br>';
    
    $x = 2.5; // Исходное значение переменной
    
    // Расчет по формуле
    $y = sqrt(abs($x)) + exp($x) + sin(pow($x, 2));
    
    echo "<b>Исходные данные:</b> x = $x<br>";
    echo "<b>Результат вычисления y:</b> " . round($y, 4) . "<br>";
?> 
            