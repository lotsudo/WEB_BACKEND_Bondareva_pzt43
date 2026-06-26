<?php
session_start();
// Проверяем, авторизован ли кто-то (для солидности панели)
$userName = $_SESSION['user_name'] ?? 'Гость';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления ЛР №6 — Сервисы Почты</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background-color: var(--gray-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .panel-container {
            background: var(--white);
            max-width: 800px;
            width: 100%;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            font-family: var(--font);
        }
        .panel-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid var(--gray-mid);
            padding-bottom: 20px;
        }
        .panel-header h1 {
            color: var(--text);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .panel-header p {
            color: var(--gray-text);
        }
        .grid-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 600px) {
            .grid-links {
                grid-template-columns: 1fr;
            }
        }
        .card-link {
            display: flex;
            flex-direction: column;
            padding: 25px;
            background: var(--white);
            border: 2px solid var(--gray-mid);
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card-link:hover {
            border-color: var(--blue);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(125, 176, 217, 0.2);
        }
        .card-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .card-desc {
            font-size: 14px;
            color: var(--gray-text);
            line-height: 1.4;
            flex-grow: 1;
        }
        .card-btn {
            margin-top: 15px;
            display: inline-block;
            color: var(--blue);
            font-weight: 600;
            font-size: 14px;
        }
        .footer-btn {
            text-align: center;
            margin-top: 40px;
        }
        .btn-back {
            background: var(--blue);
            color: var(--white);
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: 700;
            transition: background 0.2s;
            display: inline-block;
        }
        .btn-back:hover {
            background: var(--blue-dark);
        }
    </style>
</head>
<body>

<div class="panel-container">
    <div class="panel-header">
        <h1>📨 Модуль почтовых рассылок (ЛР №6)</h1>
        <p>Текущий оператор системы: <strong><?= htmlspecialchars($userName) ?></strong></p>
    </div>

    <div class="grid-links">
        <a href="lb6.php" class="card-link">
            <div class="card-icon">✉️</div>
            <div class="card-title">Стандартная форма</div>
            <div class="card-desc">Отправка базового текстового сообщения (Имя и Текст) на ваш административный email через SMTP Gmail.</div>
            <div class="card-btn">Открыть сервис &rarr;</div>
        </a>

        <a href="lb6_attach.php" class="card-link">
            <div class="card-icon">📎</div>
            <div class="card-title">Форма с вложением</div>
            <div class="card-desc">Сервис отправки писем с возможностью прикрепить любой файл (картинку, документ, архив) прямо из формы.</div>
            <div class="card-btn">Открыть сервис &rarr;</div>
        </a>

        <a href="lb6_multiple.php" class="card-link">
            <div class="card-icon">👥</div>
            <div class="card-title">Групповая рассылка</div>
            <div class="card-desc">Инструмент массовой отправки писем. Принимает список email-адресов через запятую и шлет сообщения всем сразу.</div>
            <div class="card-btn">Открыть сервис &rarr;</div>
        </a>

        <a href="contact.php" class="card-link">
            <div class="card-icon">📊</div>
            <div class="card-title">Многоцелевые формы</div>
            <div class="card-desc">Сборная система: Подписка на новости, Обратная связь и Анкетирование (ФИО, Возраст, Отзыв) в одном файле.</div>
            <div class="card-btn">Открыть сервис &rarr;</div>
        </a>
    </div>

    <div class="footer-btn">
        <a href="index.php?url=/" class="btn-back">&larr; Вернуться в магазин IZUMI</a>
    </div>
</div>

</body>
</html>