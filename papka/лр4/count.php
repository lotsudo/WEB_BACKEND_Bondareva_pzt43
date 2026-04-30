<?php
session_start();

if (!isset($_SESSION['v'])) {
    $_SESSION['v'] = 0;
}

$_SESSION['v']++;

setcookie("last", date("H:i:s"), time()+3600);

echo "просмотров: " . $_SESSION['v'] . "<br>";
echo "последний: " . ($_COOKIE['last'] ?? "нет");
?>