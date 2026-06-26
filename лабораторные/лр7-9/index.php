<?php
// =========================================================================
// 1. ИНИЦИАЛИЗАЦИЯ СЕССИЙ, КУКИ И КОНСТАНТ
// =========================================================================
ob_start();
session_start();

// Скрипт 13: Счетчик просмотров
if (!isset($_SESSION['v'])) {
    $_SESSION['v'] = 0;
}
$_SESSION['v']++;
setcookie("last", date("H:i:s"), time() + 3600, "/");

define("SITE_NAME", "Dwello — Учебный сайт по PHP");
const VERSION = "1.3";
$reg_errors = [];
$auth_err = "";
$mail_status = "";

// =========================================================================
// ИНИЦИАЛИЗАЦИЯ ПОДКЛЮЧЕНИЯ К БАЗЕ ДАННЫХ SQLITE
// =========================================================================
try {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }
    $db = new PDO("sqlite:" . __DIR__ . "/data/reviews.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Таблица 1: Отзывы
    $db->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        author TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Таблица 2: Заявки на покупку/просмотр домов
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        client_name TEXT NOT NULL,
        client_phone TEXT NOT NULL,
        property_name TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    die("Ошибка инициализации базы данных SQLite: " . $e->getMessage());
}

// =========================================================================
// 2. ОБРАБОТКА ДЕЙСТВИЙ (POST И GET)
// =========================================================================

// Выход из системы
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ЗАПРОС ТИПА 1: DELETE (Удаление отзыва админом)
if (isset($_GET['delete_id']) && isset($_SESSION['user']) && $_SESSION['user'] === 'admin') {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=deleted#guestbook");
    exit;
}

// ЗАПРОС ТИПА 1 (дубль): DELETE (Удаление заявки админом)
if (isset($_GET['delete_order_id']) && isset($_SESSION['user']) && $_SESSION['user'] === 'admin') {
    $delete_order_id = (int)$_GET['delete_order_id'];
    $stmt = $db->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([':id' => $delete_order_id]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=order_deleted#dashboard");
    exit;
}

// Подключение модулей PHPMailer
require_once __DIR__ . '/код/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/код/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/код/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Авторизация admin
    if (isset($_POST['auth_login_btn'])) {
        $login = $_POST['login'] ?? '';
        $pass = $_POST['password'] ?? '';

        if ($login === 'admin' && $pass === '123') {
            $_SESSION['user'] = 'admin';
            session_write_close();
            header("Location: " . $_SERVER['PHP_SELF'] . "#dashboard");
            exit;
        } else {
            $auth_err = "неверный логин или пароль";
        }
    }

    // Валидация формы регистрации нового агента
    if (isset($_POST['reg_btn'])) {
        $reg_name = $_POST['reg_name'] ?? '';
        $reg_mail = $_POST['reg_email'] ?? '';
        $p1 = $_POST['password'] ?? '';
        $p2 = $_POST['confirm_password'] ?? '';

        if ($reg_name == '' || $reg_mail == '' || $p1 == '' || $p2 == '') {
            $reg_errors[] = "не все поля";
        }
        if (!filter_var($reg_mail, FILTER_VALIDATE_EMAIL)) {
            $reg_errors[] = "email не ок";
        }
        if (strlen($p1) < 6) {
            $reg_errors[] = "пароль короткий";
        }
        if ($p1 != $p2) {
            $reg_errors[] = "пароли разные";
        }
    }

    // ЗАПРОС ТИПА 2: INSERT INTO (Добавление отзыва)
    if (isset($_POST['gb_submit'])) {
        $author = htmlspecialchars(trim($_POST['author']));
        $message = htmlspecialchars(trim($_POST['message']));

        if ($author && $message) {
            $stmt = $db->prepare("INSERT INTO reviews (author, message) VALUES (:author, :message)");
            $stmt->execute([':author' => $author, ':message' => $message]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=success#guestbook");
            exit;
        } else {
            $reg_errors[] = "Пожалуйста, заполните все поля!";
        }
    }

    // ЗАПРОС ТИПА 2 (дубль): INSERT INTO (Добавление заявки на просмотр недвижимости)
    if (isset($_POST['book_property_btn'])) {
        $c_name = htmlspecialchars(trim($_POST['client_name']));
        $c_phone = htmlspecialchars(trim($_POST['client_phone']));
        $p_name = htmlspecialchars(trim($_POST['property_name']));

        if ($c_name && $c_phone && $p_name) {
            $stmt = $db->prepare("INSERT INTO orders (client_name, client_phone, property_name) VALUES (:c_name, :c_phone, :p_name)");
            $stmt->execute([
                ':c_name' => $c_name,
                ':c_phone' => $c_phone,
                ':p_name' => $p_name
            ]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?msg=booked#properties");
            exit;
        }
    }

    // Интегрированный блок PHPMailer
    if (isset($_POST['mail_type']) || isset($_POST['recipients']) || isset($_FILES['attachment'])) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'feratyler04@gmail.com';
            $mail->Password   = 'uyaalwozbhgtgakn'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('feratyler04@gmail.com', 'Dwello Service');

            if (isset($_POST['recipients'])) {
                $emails = explode(',', $_POST['recipients']);
                foreach ($emails as $email) {
                    $email = trim($email);
                    if (!empty($email)) $mail->addAddress($email);
                }
                $mail->Subject = 'Тестовая массовая рассылка Dwello';
                $mail->Body    = "Привет! Это сообщение отправлено пользователям с сайта.";
                $mail->isHTML(false);
            }
            elseif (isset($_FILES['attachment'])) {
                $mail->addAddress('feratyler04@gmail.com');
                if ($_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                    $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
                }
                $mail->isHTML(false);
                $mail->Subject = 'Сообщение с вложением от ' . ($_POST['name'] ?? 'Клиент');
                $mail->Body    = "Сообщение: " . ($_POST['message'] ?? '');
            }
            elseif (isset($_POST['mail_type'])) {
                $mail->addAddress('feratyler04@gmail.com');
                if ($_POST['mail_type'] == 'subscribe') {
                    $mail->Subject = 'Новая подписка на Dwello';
                    $mail->Body    = "Email для подписки: " . $_POST['email'];
                } elseif ($_POST['mail_type'] == 'feedback') {
                    $mail->Subject = 'Обратная связь';
                    $mail->Body    = "Имя: {$_POST['name']}\nСообщение: {$_POST['message']}";
                }
            }

            $mail->send();
            $mail_status = "Успешно отправлено!";
        } catch (Exception $e) {
            $mail_status = "Ошибка почты: " . $mail->ErrorInfo;
        }
    }
}

// Данные каталога недвижимости
$products = [
    ['name'=>'San Francisco Luxury Villa','category'=>'phones','price'=>2500000, 'img'=>'Mask group (2).png', 'rooms'=>4, 'size'=>'3,500 sq ft', 'loc'=>'San Francisco, California'],
    ['name'=>'Beverly Hills Cozy Modern','category'=>'laptops','price'=>850000, 'img'=>'Mask group (3).png', 'rooms'=>3, 'size'=>'1,500 sq ft', 'loc'=>'Beverly Hills, California'],
    ['name'=>'Palo Alto Elite Estate','category'=>'tv','price'=>3700000, 'img'=>'Mask group (4).png', 'rooms'=>6, 'size'=>'4,000 sq ft', 'loc'=>'Palo Alto, California']
];

$min = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$max = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : 99999999;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= SITE_NAME ?> v<?= VERSION ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="st.css">
<style>
    .php-info-bar { background: #eef9ff; padding: 10px 0; text-align: center; font-size: 14px; border-bottom: 1px solid #d0e7f5; }
    .php-section { padding: 60px 0; background: #fdfdfd; border-top: 1px solid #eee; }
    .php-section h2 { font-size: 32px; font-weight: 700; margin-bottom: 20px; text-align: center; }
    .input-custom { padding: 12px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px; width: 100%; max-width: 300px; font-family: inherit; }
    .alert-danger { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    .alert-success { color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    .string-box { background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-top: 15px; }
    .modal-booking { background: #f0f4f8; padding: 12px; border-radius: 6px; margin-top: 10px; border-left: 4px solid #00afec; }
</style>
</head>
<body>

<div class="php-info-bar">
    <div class="container">
        Просмотров страницы: <strong><?= $_SESSION['v'] ?></strong> | 
        Предыдущий визит: <strong><?= $_COOKIE['last'] ?? 'Первый раз' ?></strong> | 
        Текущий файл: <code><?= basename($_SERVER['PHP_SELF']) ?></code>
    </div>
</div>

<header class="header">
<div class="container">
<nav class="navbar">
<a href="#" class="logo">
<img src="images/logo.png" alt="Logo">
</a>
<ul class="nav-menu">
<li><a href="#">Home</a></li>
<li><a href="#properties">Residences</a></li>
<li><a href="#strings-demo">String Tasks (ЛР №2)</a></li>
<li><a href="#guestbook">Guestbook</a></li>
<li><a href="#mailer-section">PHPMailer forms</a></li>
<?php if (isset($_SESSION['user'])): ?>
    <li><a href="#dashboard" style="color: #00afec;"><b>Dashboard (<?= htmlspecialchars($_SESSION['user']) ?>)</b></a></li>
<?php else: ?>
    <li><a href="#dashboard">Sign In</a></li>
<?php endif; ?>
</ul>
<button class="burger-menu" id="burger-btn" aria-label="Menu">
    <span></span>
    <span></span>
    <span></span>
</button>
</nav>
</div>
</header>

<section class="hero">
<div class="container">
<div class="hero-wrapper">
<div class="hero-content">
<h1>Find Your Dream Home</h1>
<p>Explore our curated selection of exquisite properties individually tailored to your unique dream home vision.</p>
<p style="font-size: 14px; color: #666;"><i>Скрипт разработан: <strong>Дарья</strong>, 2026 г.</i></p>
<a href="#properties" class="btn-dark" style="text-decoration:none; display:inline-block; text-align:center; width:150px; line-height:50px; margin-top:15px;">Explore</a>
</div>
<div class="hero-image">
<img src="images/hero image 1.png" alt="House">
</div>
</div>
</div>
</section>

<section class="search-section">
<div class="container">
<form method="GET" action="#properties" class="search-box">
    <div class="search-field">
        <label style="display:block; font-size:12px; color:#888;">Min Price</label>
        <input type="number" name="min_price" placeholder="e.g. 500000" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>" style="border:none; outline:none; font-size:16px; width:100px;">
    </div>
    <div class="search-field">
        <label style="display:block; font-size:12px; color:#888;">Max Price</label>
        <input type="number" name="max_price" placeholder="e.g. 3000000" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>" style="border:none; outline:none; font-size:16px; width:100px;">
    </div>
    <div class="search-field">
        <label style="display:block; font-size:12px; color:#888;">Category</label>
        <select name="category" style="border:none; outline:none; background:transparent; font-size:16px;">
            <option value="">All Types</option>
            <option value="phones">Premium</option>
            <option value="laptops">Modern</option>
            <option value="tv">Townhouse</option>
        </select>
    </div>
    <button type="submit" class="search-button" style="border:none; cursor:pointer;">Search</button>
</form>
</div>
</section>  

<section class="about">
<div class="container">
<div class="about-wrapper">
<div class="about-image">
<img src="images/Mask group (1).png" alt="House">
</div>
<div class="about-content">
<h2>We Help You To Find Your Dream Home</h2>
<p>From cozy cottages to luxurious estates, our dedicated team guides you through every step of the process, ensuring you obtain your ideal home with ease.</p>
<div class="stats">
<div class="stat-item">
<h3>8K+</h3>
<span>Houses Available</span>
</div>
<div class="stat-item">
<h3>6K+</h3>
<span>Houses Sold</span>
</div>
<div class="stat-item">
<h3>2K+</h3>
<span>Trusted Agents</span>
</div>
</div>
</div>
</div>
</div>
</section>

<section class="properties" id="properties">
<div class="container">
<div class="section-header">
<h2>Our Popular Residences</h2>
<p>Providing a diverse range of exquisite residential properties to fulfill your every need.</p>
<?php if(isset($_GET['msg']) && $_GET['msg'] === 'booked'): ?>
    <div class="alert-success" style="text-align: center; max-width: 600px; margin: 15px auto;">🎉 Заявка на просмотр успешно отправлена и сохранена в БД!</div>
<?php endif; ?>
</div>

<div class="properties-grid">
<?php
$has_items = false;
foreach ($products as $p) {
    if ($p['price'] < $min || $p['price'] > $max) continue;
    $has_items = true;
    ?>
    <div class="property-card">
        <img src="images/<?= $p['img'] ?>" alt="Property" class="property-image">
        <div class="property-content">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <div class="property-meta">
                <div class="property-location">
                    <img src="images/location.png" alt="">
                    <span><?= htmlspecialchars($p['loc']) ?></span>
                </div>
                <div class="property-details">
                    <span><img src="images/bed.png" alt=""> <?= $p['rooms'] ?> Rooms</span>
                    <span><img src="images/size.png" alt=""> <?= $p['size'] ?></span>
                </div>
            </div>
            <div class="property-footer" style="margin-bottom: 15px;">
                <div class="property-price">$<?= number_format($p['price']) ?></div>
            </div>
            
            <div class="modal-booking" style="text-align: left;">
                <h4 style="margin: 0 0 8px 0; font-size:13px; color:#333; font-weight:600;">Записаться на просмотр:</h4>
                <form method="POST" action="">
                    <input type="hidden" name="property_name" value="<?= htmlspecialchars($p['name']) ?>">
                    <input type="text" name="client_name" placeholder="Ваше имя" class="input-custom" style="padding:6px; font-size:12px; margin-bottom:5px; max-width:100%; box-sizing:border-box;" required><br>
                    <input type="text" name="client_phone" placeholder="Ваш телефон" class="input-custom" style="padding:6px; font-size:12px; margin-bottom:8px; max-width:100%; box-sizing:border-box;" required><br>
                    <button type="submit" name="book_property_btn" class="btn-dark" style="padding:8px 10px; font-size:12px; cursor:pointer; border:none; width:100%; background:#00afec;">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
<?php } 
if (!$has_items) {
    echo "<p style='grid-column: 1/-1; text-align:center; color:#888;'>Нет объектов, соответствующих выбранному диапазону цен.</p>";
}
?>
</div>
</div>
</section>

<section class="php-section" id="strings-demo">
<div class="container">
    <h2>Задания со строками (Лабораторная работа №2)</h2>
    <div class="string-box">
        <?php
        $text1 = " PHP (Hypertext Preprocessor) — это скриптовый язык программирования общего назначения. ";
        $text2 = "Я люблю PHP. PHP — это мощный язык. Я учу PHP.";
        $userComment = "<b>Отличный сайт!</b> <script>alert('XSS');</script>";
        $slugSource = "привет, как дела?";
        $name = "Дарья";

        echo "<b>Способы записи строк:</b><br>";
        echo 'Привет, $name! (в одинарных кавычках)<br>';
        echo "Привет, $name! (в двойных кавычках)<br>";
        
        $slugSource = mb_convert_case($slugSource, MB_CASE_TITLE, "UTF-8");
        echo "<b>Изменение регистра (Заглавные буквы):</b> " . htmlspecialchars($slugSource) . "<br>";
        echo "<b>Проверка наличия 'JavaScript':</b> " . (str_contains($text2, "JavaScript") ? "Да" : "Нет") . "<br>";
        echo "<b>Безопасный вывод (htmlspecialchars):</b> " . htmlspecialchars($userComment) . "<br>";
        echo "<b>Форматирование стоимости услуг:</b> " . number_format(12345.6789, 2, ',', ' ') . " руб.";
        ?>
    </div>
</div>
</section>

<section class="php-section" id="guestbook">
<div class="container" style="max-width: 600px;">
    <h2>Гостевая книга отзывов о Dwello (SQL СУБД SQLite)</h2>
    
    <div style="margin-bottom: 20px; padding: 10px; background: #e9ecef; border-radius: 5px; font-size: 14px;">
        <?php if (isset($_SESSION['user']) && $_SESSION['user'] === 'admin'): ?>
            <span style="background: #ffc107; color: #212529; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">⚡ ПАНЕЛЬ УПРАВЛЕНИЯ АКТИВИРОВАНА</span>
        <?php else: ?>
            <span>Вы просматриваете страницу как обычный Гость. Чтобы удалять отзывы, войдите под admin на панели внизу.</span>
        <?php endif; ?>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert-success">Ваш отзыв успешно сохранен в базу данных SQL!</div>
    <?php endif; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert-success">🗑️ Отзыв успешно удален из базы данных!</div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="author" placeholder="Ваше имя" class="input-custom" required><br>
        <textarea name="message" placeholder="Ваш отзыв о работе компании" class="input-custom" style="height:100px;" required></textarea><br>
        <button type="submit" name="gb_submit" class="btn-dark" style="border:none; cursor:pointer; padding:12px 30px;">Отправить в БД</button>
    </form>

    <div style="margin-top: 30px;">
        <h3>Отзывы из базы данных (SQL):</h3>
        <?php
        // ЗАПРОС ТИПА 3: SELECT * FROM (Выборка отзывов)
        $query = $db->query("SELECT * FROM reviews ORDER BY id DESC");
        $reviews = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($reviews) > 0) {
            foreach ($reviews as $row) {
                $date = date('d.m.Y H:i', strtotime($row['created_at']));
                echo '<div class="string-box" style="margin-bottom:15px; padding:15px; position: relative;">';
                if (isset($_SESSION['user']) && $_SESSION['user'] === 'admin') {
                    echo '<a href="?delete_id=' . $row['id'] . '" onclick="return confirm(\'Удалить отзыв?\')" style="position: absolute; top: 15px; right: 15px; color: #c53929; background: #fce8e6; padding: 4px 10px; border-radius: 4px; font-size: 12px; text-decoration:none;">Удалить</a>';
                }
                echo '<b>' . htmlspecialchars($row['author']) . '</b> <small style="color:#aaa; margin-left:10px;">' . $date . '</small><br>';
                echo nl2br(htmlspecialchars($row['message']));
                echo '</div>';
            }
        } else {
            echo '<p style="color:#999;">В базе данных пока нет отзывов.</p>';
        }
        ?>
    </div>
</div>
</section>

<section class="php-section" id="mailer-section">
<div class="container" style="max-width: 600px;">
    <h2>Управление почтовой службой PHPMailer</h2>
    <?php if ($mail_status) echo "<div class='alert-success'>$mail_status</div>"; ?>
    
    <div class="string-box">
        <h3>Форма 1: Обратная связь (Вложение файла)</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Ваше имя" class="input-custom" required><br>
            <textarea name="message" placeholder="Сообщение" class="input-custom" required></textarea><br>
            <input type="file" name="attachment" class="input-custom" required><br>
            <button type="submit" class="btn-dark" style="border:none; cursor:pointer;">Отправить отзыв с файлом</button>
        </form>
    </div>

    <div class="string-box">
        <h3>Форма 2: Массовая рассылка клиентам</h3>
        <form method="POST">
            <input type="text" name="recipients" placeholder="Укажите email через запятую" class="input-custom" style="max-width:100%;" required><br>
            <button type="submit" class="btn-dark" style="border:none; cursor:pointer;">Запустить рассылку</button>
        </form>
    </div>
</div>
</section>

<section class="php-section" id="dashboard">
<div class="container" style="max-width: 600px;">
    <?php if (!isset($_SESSION['user'])): ?>
        <h2>Личный кабинет сотрудника Dwello</h2>
        <?php if ($auth_err) echo "<div class='alert-danger'>$auth_err</div>"; ?>
        <form method="POST" action="#dashboard">
            <input type="text" name="login" placeholder="Логин (admin)" class="input-custom" required><br>
            <input type="password" name="password" placeholder="Пароль (123)" class="input-custom" required><br>
            <button type="submit" name="auth_login_btn" class="btn-dark" style="border:none; cursor:pointer; width:100px; height:45px;">Войти</button>
        </form>
    <?php else: ?>
        <h2>Панель управления сессией агента</h2>
        <div class="alert-success">Вы вошли как: <b><?= htmlspecialchars($_SESSION['user']) ?></b></div>
        
        <div class="string-box" style="background: #fdfefe; border: 1px dashed #00afec;">
            <h4>📊 Сводная SQL статистика по базе данных:</h4>
            <?php
            // ЗАПРОС ТИПА 4: COUNT (Агрегатный подсчет записей)
            $count_reviews = $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
            $count_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            
            // ЗАПРОС ТИПА 5: WHERE с приведением даты (SQLite-совместимый)
            $today_date = date('Y-m-d');
            $stmt_today = $db->prepare("SELECT COUNT(*) FROM orders WHERE date(created_at) = :today");
            $stmt_today->execute([':today' => $today_date]);
            $count_today_orders = $stmt_today->fetchColumn();
            ?>
            <ul>
                <li>Всего отзывов оставлено: <strong><?= $count_reviews ?></strong></li>
                <li>Всего заявок на дома: <strong><?= $count_orders ?></strong></li>
                <li>Из них поступило за сегодня: <strong style="color: green;"><?= $count_today_orders ?></strong></li>
            </ul>
        </div>

        <div class="string-box" style="background: #fff; margin-top:20px;">
            <h3>Новые заявки на просмотр объектов (Таблица `orders`):</h3>
            <?php
            if(isset($_GET['msg']) && $_GET['msg'] === 'order_deleted') {
                echo "<div class='alert-success' style='padding:5px; font-size:13px;'>Заявка удалена.</div>";
            }
            
            $o_query = $db->query("SELECT * FROM orders ORDER BY id DESC");
            $orders = $o_query->fetchAll(PDO::FETCH_ASSOC);

            if (count($orders) > 0) {
                echo '<table style="width:100%; border-collapse: collapse; font-size:14px; margin-top:10px;">';
                echo '<tr style="background:#eee; text-align:left;"><th style="padding:8px;">Клиент</th><th style="padding:8px;">Телефон</th><th style="padding:8px;">Объект</th><th style="padding:8px;">Действие</th></tr>';
                foreach ($orders as $ord) {
                    echo '<tr style="border-bottom:1px solid #ddd;">';
                    echo '<td style="padding:8px;">' . htmlspecialchars($ord['client_name']) . '</td>';
                    echo '<td style="padding:8px;">' . htmlspecialchars($ord['client_phone']) . '</td>';
                    echo '<td style="padding:8px;">' . htmlspecialchars($ord['property_name']) . '</td>';
                    echo '<td style="padding:8px;"><a href="?delete_order_id=' . $ord['id'] . '" onclick="return confirm(\'Удалить заявку?\')" style="color:red; text-decoration:none;">❌ Удалить</a></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p style="color:#999; margin-top:10px;">Новых заявок пока нет.</p>';
            }
            ?>
        </div>

        <br>
        <a href="?action=logout" class="btn-dark" style="text-decoration:none; display:inline-block; text-align:center; padding:0 25px; line-height:45px; background:#c0392b;">Выйти</a>
    <?php endif; ?>
</div>
</section>

<footer class="footer">
<div class="container">
<div class="footer-grid">
<div class="footer-brand">
<div class="footer-logo"><img src="images/logo.png" alt="Logo"></div>
<p>Bringing you closer to your dream home with trusted guidance and personalized service.</p>
</div>
<div class="footer-column">
<h4>About</h4>
<ul>
<li><a href="#">Home</a></li>
<li><a href="#">Services</a></li>
<li><a href="#">Agents</a></li>
</ul>
</div>
<div class="footer-column">
<h4>Support</h4>
<ul>
<li><a href="#">Help Center</a></li>
<li><a href="#">FAQ</a></li>
</ul>
</div>
<div class="footer-column">
<h4>Find Us</h4>
<ul>
<li><a href="#">Location</a></li>
</ul>
</div>
<div class="footer-column">
<h4>Our Social</h4>
<ul>
<li><a href="#"><img src="images/instagram.png" alt="">Instagram</a></li>
<li><a href="#"><img src="images/facebook.png" alt="">Facebook</a></li>
</ul>
</div>
</div>
</div>
</footer>

<script>
const cards = document.querySelectorAll('.feature-card, .property-card, .testimonial-card');
const observer = new IntersectionObserver(entries => {
entries.forEach(entry => {
if(entry.isIntersecting){
entry.target.classList.add('show');
}
});
},{ threshold:0.15 });
cards.forEach(card => { observer.observe(card); });

const burgerBtn = document.getElementById('burger-btn');
const navMenu = document.querySelector('.nav-menu');
const navLinks = document.querySelectorAll('.nav-menu a');

burgerBtn.addEventListener('click', () => {
    burgerBtn.classList.toggle('active');
    navMenu.classList.toggle('active');
    document.body.style.overflowY = navMenu.classList.contains('active') ? 'hidden' : 'initial';
});

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        burgerBtn.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflowY = 'initial';
    });
});
</script>
</body>
</html>