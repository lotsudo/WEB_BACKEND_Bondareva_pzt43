<?php
// типы данных
$a = 10;
$b = 2.5;
$text = "hello";
$flag = true;

// константа
define("NUM", 100);

// операции
$sum = $a + $b;
$str = "php" . " works";

// if
if ($a > 5) {
    $res = "a > 5";
} else {
    $res = "a <= 5";
}

// switch
switch ($a) {
    case 10:
        $sw = "десятка";
        break;
    default:
        $sw = "другое";
}

// цикл
$line = "";
for ($i = 0; $i < 5; $i++) {
    if ($i == 3) continue; // пропускаем 3
    $line .= $i . " ";
}

// функция
function plus($x, $y) {
    return $x + $y;
}

// стрелочная
$kv = fn($x) => $x * $x;

// массив
$arr = [5, 2, 9];
sort($arr);

// ассоциативный
$user = ["name" => "Иван", "age" => 19];

// строки
$s = "привет мир";
$len = strlen($s);
$new = str_replace("мир", "php", $s);

// дата
$today = date("d.m.Y H:i");

?>

<html>
<body>

<h3>Проверка</h3>

<p>sum: <?= $sum ?></p>
<p><?= $str ?></p>

<p><?= $res ?></p>
<p><?= $sw ?></p>

<p>цикл: <?= $line ?></p>

<p>функция: <?= plus(2,3) ?></p>
<p>стрелка: <?= $kv(4) ?></p>

<p>массив:
<?php foreach($arr as $v) echo $v . " "; ?>
</p>

<p>строка: <?= $len ?> / <?= $new ?></p>

<p>дата: <?= $today ?></p>

</body>
</html>