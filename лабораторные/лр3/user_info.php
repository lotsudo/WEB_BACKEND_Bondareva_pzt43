<?php
// просто проверка
if (isset($_GET['name']) && isset($_GET['city'])) {
    $n = $_GET['name'];
    $c = $_GET['city'];

    echo "Пользователь " . htmlspecialchars($n) . " живет в городе " . htmlspecialchars($c);
} else {
    echo "нет данных";
}
?>