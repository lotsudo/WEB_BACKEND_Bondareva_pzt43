<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">← На главную</a>
    <h2>Задание 1. Типовые действия с файлами</h2>

    <h3>1. Создание файла и запись</h3>
    <div class="code">
        $fh = fopen("testfile.txt", "w") or die("Не удалось");<br>
        $text = "Строка 1\nСтрока 2\nСтрока 3";<br>
        fwrite($fh, $text);<br>
        fclose($fh);
    </div>
    <?php
    $fh = fopen("testfile.txt", "w") or die("Не удалось создать файл");
    $text = "Строка 1\nСтрока 2\nСтрока 3";
    fwrite($fh, $text);
    fclose($fh);
    ?>
    <div class="result">Файл testfile.txt создан.</div>

    <h3>2. Проверка существования</h3>
    <div class="code">file_exists("testfile.txt")</div>
    <div class="result">
        <?php echo file_exists("testfile.txt") ? "Файл существует" : "Файл не существует"; ?>
    </div>

    <h3>3. Построчное чтение (fgets + feof)</h3>
    <div class="code">
        $fh = fopen("testfile.txt", "r");<br>
        while (!feof($fh)) { echo fgets($fh) . "&lt;br&gt;"; }<br>
        fclose($fh);
    </div>
    <div class="result">
        <?php
        $fh = fopen("testfile.txt", "r");
        while (!feof($fh)) {
            $line = fgets($fh);
            if ($line !== false) echo htmlspecialchars($line) . "<br>";
        }
        fclose($fh);
        ?>
    </div>

    <h3>4. Чтение целиком (file_get_contents)</h3>
    <div class="code">echo file_get_contents("testfile.txt");</div>
    <div class="result">
        <?php echo nl2br(htmlspecialchars(file_get_contents("testfile.txt"))); ?>
    </div>

    <h3>5. Копирование</h3>
    <div class="code">copy("testfile.txt", "testfile2.txt");</div>
    <?php copy("testfile.txt", "testfile2.txt") or die("Невозможно"); ?>
    <div class="result">Скопировано в testfile2.txt</div>

    <h3>6. Переименование</h3>
    <div class="code">rename("testfile2.txt", "testfile2.new");</div>
    <?php rename("testfile2.txt", "testfile2.new"); ?>
    <div class="result">Переименовано в testfile2.new</div>

    <h3>7. Удаление</h3>
    <div class="code">unlink("testfile2.new");</div>
    <?php unlink("testfile2.new"); ?>
    <div class="result">Файл testfile2.new удалён</div>

    <h3>8. Свойства файла</h3>
    <div class="code">
        filemtime("testfile.txt") — время изменения<br>
        fileatime("testfile.txt") — время доступа<br>
        filesize("testfile.txt") — размер
    </div>
    <div class="result">
        Изменён: <?php echo date("d.m.Y H:i:s", filemtime("testfile.txt")); ?><br>
        Доступ: <?php echo date("d.m.Y H:i:s", fileatime("testfile.txt")); ?><br>
        Размер: <?php echo filesize("testfile.txt"); ?> байт
    </div>

    <h3>9. Работа с каталогами</h3>
    <div class="code">
        getcwd() — текущий каталог<br>
        opendir("."), readdir($dh), closedir($dh)<br>
        mkdir("newdir"), rmdir("newdir")
    </div>
    <div class="result">
        Текущий каталог: <?php echo getcwd(); ?><br><br>
        Содержимое:<br>
        <?php
        $dh = opendir(".");
        while (($entry = readdir($dh)) !== false) {
            if ($entry == "." || $entry == "..") continue;
            echo ($entry) . (is_dir($entry) ? " [каталог]" : " [файл]") . "<br>";
        }
        closedir($dh);
        ?>
    </div>
</body>
</html>