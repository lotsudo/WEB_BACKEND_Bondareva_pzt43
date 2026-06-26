<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 2 — Гостевая книга</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">← На главную</a>
    <h2>Гостевая книга</h2>

    <form method="POST" action="">
        <label>Имя</label>
        <input type="text" name="author" maxlength="50" required>
        <label>Сообщение</label>
        <textarea name="message" maxlength="500" required></textarea>
        <button type="submit" name="submit">Отправить</button>
    </form>

    <?php
    define('DATA_FILE', __DIR__ . '/data/guestbook.txt');

    if (isset($_POST['submit'])) {
        $author = htmlspecialchars(trim($_POST['author']));
        $message = htmlspecialchars(trim($_POST['message']));

        if ($author && $message) {
            $record = date('d.m.Y H:i:s') . '|' . $author . '|' . $message . "\n";
            $fh = fopen(DATA_FILE, 'a') or die("Ошибка открытия файла");

            if (flock($fh, LOCK_EX)) {
                fwrite($fh, $record);
                flock($fh, LOCK_UN);
                echo '<p class="success">Сообщение добавлено</p>';
            } else {
                echo '<p class="error">Файл занят</p>';
            }
            fclose($fh);
        } else {
            echo '<p class="error">Заполните все поля</p>';
        }
    }

    echo '<h3>Сообщения</h3>';

    if (file_exists(DATA_FILE) && filesize(DATA_FILE) > 0) {
        $lines = file(DATA_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);

        foreach ($lines as $line) {
            $parts = explode('|', $line, 3);
            if (count($parts) === 3) {
                list($date, $author, $text) = $parts;
                echo '<div class="message">';
                echo '<b>' . $author . '</b> <span style="color:#888">' . $date . '</span><br>';
                echo nl2br($text);
                echo '</div>';
            }
        }
    } else {
        echo '<p>Пока нет сообщений.</p>';
    }
    ?>
</body>
</html>