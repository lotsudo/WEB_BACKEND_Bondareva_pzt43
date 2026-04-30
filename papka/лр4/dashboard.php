<?php
session_start();

// если не вошёл — отправляем назад
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Панель</h2>
<p>Пользователь: <?= htmlspecialchars($_SESSION['user']) ?></p>

<a href="logout.php">Выйти</a>