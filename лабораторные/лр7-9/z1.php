<?php
// =========================================================================
// 1. ИНИЦИАЛИЗАЦИЯ СЕССИЙ И КРИТИЧЕСКИХ НАСТРОЕК (СТРОГО НА ПЕРВОЙ СТРОКЕ)
// =========================================================================
ob_start();
session_start();

// Мягкий режим ошибок MySQL (чтобы скрипт не падал при дебаге)
mysqli_report(MYSQLI_REPORT_OFF);

// Счетчик просмотров страницы
if (!isset($_SESSION['v'])) {
    $_SESSION['v'] = 0;
}
$_SESSION['v']++;
setcookie("last", date("H:i:s"), time() + 3600, "/");

// =========================================================================
// 2. ГЛОБАЛЬНЫЕ КОНСТАНТЫ И АВТО-СОЗДАНИЕ БАЗЫ ДАННЫХ
// =========================================================================
define('DB_HOST', 'localhost'); // Используем локальный хост (не сетевой 127.0.0.1)
define('DB_USER', 'root');      
define('DB_PASS', '123qweasD'); // Твой проверенный пароль
define('DB_NAME', 'dwello_db'); 

// Подключаемся к серверу, чтобы проверить/создать саму базу данных
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if ($link) {
    mysqli_query($link, "CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    mysqli_select_db($link, DB_NAME);
} else {
    die("Критическая ошибка: Не удалось подключиться к MySQL. Проверь Open Server Panel! " . mysqli_connect_error());
}

define("SITE_NAME", "Dwello — Учебный сайт по PHP");
define("VERSION", "1.0");
define('DATA_FILE', __DIR__ . '/data/guestbook.txt');

// =========================================================================
// 3. ПОДКЛЮЧЕНИЕ СТАТИЧЕСКИХ МОДУЛЕЙ (PHPMailer)
// =========================================================================
require_once __DIR__ . '/код/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/код/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/код/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =========================================================================
// 4. ОБРАБОТКА ФОРМЫ АВТОРИЗАЦИИ (ВХОД ПОД АДМИНОМ)
// =========================================================================
$reg_errors = [];
$success_auth = false;

if (isset($_POST['auth_submit'])) {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    
    if ($login === 'admin' && $password === 'admin123') { // Можешь поставить свой пароль админа
        $_SESSION['user'] = 'admin';
        $success_auth = true;
    } else {
        $reg_errors[] = "Неверный логин или пароль администратора!";
    }
}

// Выход из системы
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    header("Location: z1.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= SITE_NAME ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; color: #333; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #e2f5ea; color: #1e7e34; border: 1px solid #d1f0db; }
        .alert-danger { background: #fce8e6; color: #c53929; border: 1px solid #fad2cf; }
        .status-box { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 5px solid #007bff; margin-top: 20px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-radius; }
        input[type="submit"] { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        input[type="submit"]:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>Добро пожаловать в систему Dwello!</h1>
    <p>Просмотров этой страницы в текущей сессии: <strong><?= $_SESSION['v'] ?></strong></p>
    <p>Время последнего захода: <strong><?= isset($_COOKIE['last']) ? $_COOKIE['last'] : 'Первый раз' ?></strong></p>

    <?php if (!empty($reg_errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($reg_errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user']) && $_SESSION['user'] === 'admin'): ?>
        <div class="alert alert-success">
            <h3>Вы успешно авторизованы в системе под логином: <span style="text-decoration: underline;">admin</span></h3>
            <p><strong>👑 Статус: Администратор.</strong> Вам доступна Панель расширенного управления отзывами на странице Гостевой книги.</p>
            <p><a href="index.php" style="color: #007bff; font-weight: bold;">Перейти к управлению Гостевой книгой (index.php) →</a></p>
            <br>
            <a href="z1.php?logout=1" style="color: #c53929;">Выйти из панели управления</a>
        </div>
    <?php else: ?>
        <div style="background: #f1f3f5; padding: 20px; border-radius: 6px;">
            <h3>Вход в Панель Расширенного Управления</h3>
            <form action="z1.php" method="POST">
                <label>Логин администратора:</label>
                <input type="text" name="login" placeholder="Например: admin" required>
                <label>Пароль:</label>
                <input type="password" name="password" placeholder="Например: admin123" required>
                <input type="submit" name="auth_submit" value="Войти в систему">
            </form>
        </div>
    <?php endif; ?>

    <div class="status-box">
        <h3>🔍 Текущий статус связи с MySQL:</h3>
        <?php if ($link): ?>
            <p style="color: #1e7e34; font-weight: bold;">✅ Соединение успешно установлено!</p>
            <p>База данных <strong><?= DB_NAME ?></strong> активна и готова к работе.</p>
        <?php else: ?>
            <p style="color: #c53929; font-weight: bold;">❌ База данных недоступна!</p>
            <p>Описание: <?= mysqli_connect_error() ?></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>