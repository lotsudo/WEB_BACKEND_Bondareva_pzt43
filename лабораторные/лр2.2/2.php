<?php
$students = [
    ['name' => 'Анна', 'age' => 20, 'grade' => 4.5, 'city' => 'Минск'],
    ['name' => 'Иван', 'age' => 22, 'grade' => 3.8, 'city' => 'Гродно'],
    ['name' => 'Мария', 'age' => 19, 'grade' => 4.9, 'city' => 'Брест'],
    ['name' => 'Петр', 'age' => 21, 'grade' => 4.1, 'city' => 'Гродно'],
    ['name' => 'Елена', 'age' => 20, 'grade' => 4.7, 'city' => 'Минск'],
    ['name' => 'Алексей', 'age' => 23, 'grade' => 3.5, 'city' => 'Витебск']
            ];

$colors = ['red', 'green', 'blue', 'yellow', 'black', 'white'];

$capitals = [
    'Россия' => 'Москва',
    'Беларусь' => 'Минск',
    'Польша' => 'Варшава',
    'Германия' => 'Берлин'
            ];
echo "<h3>Работа с массивами </h3>";
    echo "<b>Инициализация и доступ </b><br>";
        echo 'Добавьте в массив $colors новый элемент purple в конец, затем удалите первый элемент и выведите его значение.<br>';
            $colors[] = 'purple';
            unset($colors[0]);
            foreach ($colors as $color) echo $color, " ";
echo "<hr>";
    echo "<b> Перебор и foreach</b><br>";
        echo 'Используя foreach с ключами, выведите все пары страна-столица из $capitals в формате: "Столица [страны] — [город]".<br>';
            foreach ($capitals as $country => $city)  echo "Столица страны $country — город $city<br>";
echo "<hr>";
    echo "<b> Сортировка</b><br>";
        echo 'Отсортируйте массив $students по возрасту (по убыванию) с сохранением ключей используйте asort (предварительно получив массив возрастов).<br>';
            usort($students, fn($a, $b) => $a['age'] <=> $b['age']);
            foreach ($students as $student) {
                echo "Имя: " . $student['name'] . " | ";
                echo "Возраст: " . $student['age'] . " | ";
                echo "Город: " . $student['city'] . "<br>";
            }   
echo "<hr>";
    echo "<b> Функции поиска и проверки</b><br>";
        echo 'Проверьте, существует ли в массиве $students ключ grade у первого элемента (используйте array_key_exists).<br>';
        echo array_key_exists('grade', $students[0]);
echo "<hr>";
    echo "<b> Работа с частью массива</b><br>";
        echo 'Удалите из массива $students второй элемент с помощью array_splice. Выведите удаленный элемент.<br>';
        print_r (array_splice($students, 0, 1,));
echo "<hr>";
    echo "<b> Преобразование массивов</b><br>";
        echo 'Создайте новый массив, в котором ключами будут названия цветов, а значениями — их длина (количество символов). Используйте array_combine.<br>';
            for ($i = 0; $i < count($colors); $i++)
                $colors_length[$i] = mb_strlen($colors[$i]);
            $new_colors = array_combine($colors, $colors_length);
            foreach ($new_colors as $color => $length)
                echo "Цвет: $color, количество букв: $length <br>";
echo "<hr>";
    echo "<b> Функции высшего порядка</b><br>";
        echo 'Используя array_map, создайте массив из полных имен студентов в формате "Имя (возраст лет)".<br>';
            $new_students = array_map(function($student){
                return $student['name'] . ' (' . $student['age'] . ' лет)';
            },$students);
            print_r($new_students);
echo "<hr>";
    echo "<b> Случайные элементы</b><br>";
        echo 'Перемешайте массив $colors случайным образом (shuffle) и выведите его.<br>';
            shuffle($colors);
            foreach ($colors as $color)
                echo $color, " ";

?>