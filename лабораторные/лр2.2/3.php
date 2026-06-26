<?php
    $text1 = " PHP (Hypertext Preprocessor) — это скриптовый язык программирования общего назначения. ";
    $text2 = "Я люблю PHP. PHP — это мощный язык. Я учу PHP.";
    $userComment = "<b>Отличный сайт!</b> <script>alert('XSS');</script>";
    $price = " 1 234,56 руб. ";
    $slugSource = "Привет, как дела?";
    $csvLine = "Иванов;Иван;ivan@mail.com;29;Минск";
    $name = "Дарья";

    echo "<h3>Работа со строками </h3>";
        echo "<b>Способы записи строк </b><br>";
            echo 'a) Объявите переменную $name со своим именем. Выведите три строки:
                    используя одинарные кавычки: Привет, $name! — объясните результат.
                    используя двойные кавычки: "Привет, $name!" — объясните.
                    используя heredoc: многострочное приветствие с переменной $name.<br>';
                echo 'Привет, $name! (Одинарные кавычки не принимают переменных)<br>';
                echo "Привет, $name!(Двойные кавычки позволяют вставлять в echo переменные.)<br>";
                echo <<< EOT
                    Меня зовут "$name".
                    Heredoc Позволят ввводить текст аналогично двойным кавычкам, но подходит для больших объемов текста. <br>
                    EOT; 
    echo "<hr>";
        echo "<b>Доступ к символам</b><br>";
            echo 'Замените первый символ строки $slugSource на заглавную букву (используйте доступ по индексу и учтите UTF-8).<br>';
                $slugSource = mb_strtoupper(mb_substr($slugSource, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($slugSource, 1, mb_strlen($slugSource), 'UTF-8');
                echo $slugSource; 
    echo "<hr>";
        echo "<b>Операции со строками</b><br>";
            echo 'Соедините строки "Имя:" и ваше имя с помощью конкатенации и составного присваивания.<br>';
                $prefix = "Имя: ";
                echo $prefix . $name, "<br>";
    echo "<hr>";
        echo "<b>Длина строки</b><br>";
            echo 'Проверьте, есть ли в строке $text2 слово "JavaScript" с помощью str_contains.<br>';
                $needle = "JavaScript";
                $result = str_contains($text2, $needle) ? "Да, найдено" : "Нет, не найдено";
                echo $result;
    echo "<hr>";
        echo "<b>Извлечение части строки</b><br>";
            echo 'Получите последние 10 символов строки $text1 (используйте отрицательный старт).<br>';
                echo mb_substr($text1, -10);   
    echo "<hr>";
        echo "<b>Замена части строки</b><br>";
            echo 'Замените в $text2 все вхождения "PHP" на "РНР" (кириллица) с помощью str_replace.<Br>';
                str_replace('PHP', "РНР", "$text2");
                echo $text2;
    echo "<hr>";
        echo "<b>Удаление пробелов</b><br>";
            echo 'Очистите строку $price от лишних пробелов в начале и конце.<br>';
                $cleared_price = trim($price);
                echo $cleared_price;
    echo "<hr>";
        echo "<b>Изменение регистра</b><br>";
            echo 'Сделайте заглавными первые буквы каждого слова в строке $slugSource.<br>';
                $upperslugSource = mb_convert_case($slugSource, MB_CASE_TITLE, "UTF-8");
                echo $upperslugSource;
    echo "<hr>";
        echo "<b>Разбиение и объединение</b><br>";
            echo 'Разбейте строку $slugSource на отдельные символы с помощью mb_str_split и выведите их через запятую.<br>';
                $splitslugSource = mb_str_split($slugSource);
                foreach ($splitslugSource as $char) echo $char . ',';
    echo "<hr>";
        echo "<b>Безопасный вывод</b><br>";
            echo 'Преобразуйте строку $userComment в HTML-сущности с помощью htmlspecialchars и выведите результат.<br>';
                echo htmlspecialchars($userComment);
    echo "<hr>";
        echo "<b>Форматирование</b><br>";
            echo 'Отформатируйте число 12345.6789 с точностью 2 знака после запятой, с пробелом в качестве разделителя тысяч и запятой в качестве десятичного разделителя (используйте number_format).<br>';
                $number = "12345.6789";
                $formatted_number = number_format($number, 2, ',', ' ');
                echo $formatted_number;
?>