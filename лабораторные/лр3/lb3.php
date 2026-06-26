<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЛР №3</title>
</head>
<body>

<h1>Лабораторная работа №3</h1>
<h2>GET и POST запросы в PHP</h2>

<hr>

<!-- Передача данных через GET -->

<h3>Задание 1 — GET запрос</h3>

<!-- Форма отправляет данные через метод GET -->
<form method="get">
    <label>Имя:</label>
    <input type="text" name="get_name" required><br>

    <label>Возраст:</label>
    <input type="number" name="get_age" required><br>

    <button type="submit">Отправить</button>
</form>

<?php
// Проверяем, были ли переданы параметры через GET
if (isset($_GET['get_name'], $_GET['get_age'])) {

    // htmlentities() — защита от XSS
    echo "<p><b>Результат:</b><br>";
    echo "Имя: " . htmlentities($_GET['get_name']) . "<br>";
    echo "Возраст: " . htmlentities($_GET['get_age']) . "</p>";
}
?>

<hr>

<!--Авторизация (POST) -->

<h3>Задание 2 — Форма авторизации (POST)</h3>

<!-- Форма отправляет логин и пароль методом POST -->
<form method="post">
    <label>Логин:</label>
    <input type="text" name="login" required><br>

    <label>Пароль:</label>
    <input type="password" name="password" required><br>

    <button type="submit" name="auth_btn">Войти</button>
</form>

<?php
// Проверяем, нажата ли кнопка авторизации
if (isset($_POST['auth_btn'])) {

    echo "<p><b>Авторизация:</b><br>";
    echo "Логин: " . htmlentities($_POST['login']) . "<br>";
    echo "Пароль: " . htmlentities($_POST['password']) . "</p>";
}
?>

<hr>

<!--Регистрация -->

<h3>Задание 3 — Простая регистрация (POST)</h3>

<!-- Форма регистрации -->
<form method="post">
    <label>Имя:</label>
    <input type="text" name="reg_name" required><br>

    <label>Email:</label>
    <input type="email" name="reg_email" required><br>

    <label>Пароль:</label>
    <input type="password" name="reg_pass" required><br>

    <button type="submit" name="reg_btn">Зарегистрироваться</button>
</form>

<?php
// Проверяем отправку формы регистрации
if (isset($_POST['reg_btn'])) {

    echo "<p><b>Регистрация успешна:</b><br>";
    echo "Имя: " . htmlentities($_POST['reg_name']) . "<br>";
    echo "Email: " . htmlentities($_POST['reg_email']) . "<br>";
    echo "Пароль: " . htmlentities($_POST['reg_pass']) . "</p>";
}
?>

<hr>

<!--Заказ пиццы -->

<h3>Задание 4 — Форма заказа пиццы</h3>

<!-- Форма заказа пиццы: radio, checkbox, select -->
<form method="post">

    <label>Ваше имя:</label>
    <input type="text" name="pizza_name" required>

    <p>Размер пиццы:</p>
    <label><input type="radio" name="size" value="Маленькая" required> Маленькая</label>
    <label><input type="radio" name="size" value="Средняя"> Средняя</label>
    <label><input type="radio" name="size" value="Большая"> Большая</label>

    <p>Добавки:</p>
    <label><input type="checkbox" name="toppings[]" value="Сыр"> Сыр</label>
    <label><input type="checkbox" name="toppings[]" value="Грибы"> Грибы</label>
    <label><input type="checkbox" name="toppings[]" value="Пепперони"> Пепперони</label>
    <label><input type="checkbox" name="toppings[]" value="Оливки"> Оливки</label>

    <p>Тип теста:</p>
    <select name="dough">
        <option value="Тонкое">Тонкое</option>
        <option value="Толстое">Толстое</option>
    </select>

    <br><br>
    <button type="submit" name="pizza_btn">Оформить заказ</button>
</form>

<?php
// Проверяем отправку формы заказа пиццы
if (isset($_POST['pizza_btn'])) {

    echo "<p><b>Ваш заказ:</b><br>";
    echo "Имя: " . htmlentities($_POST['pizza_name']) . "<br>";
    echo "Размер: " . htmlentities($_POST['size']) . "<br>";
    echo "Тесто: " . htmlentities($_POST['dough']) . "<br>";

    // Проверяем, выбраны ли добавки
    if (!empty($_POST['toppings'])) {
        echo "Добавки:<br>";
        foreach ($_POST['toppings'] as $t) {
            echo "- " . htmlentities($t) . "<br>";
        }
    } else {
        echo "Без добавок<br>";
    }

    echo "</p>";
}
?>

<hr>

<!--Фильтрация товаров через GET -->

<h3>Задание 5 — Фильтрация товаров (GET)</h3>

<!-- Форма фильтрации товаров -->
<form method="get">
    <label>Категория:</label>
    <select name="category">
        <option value="phones">Телефоны</option>
        <option value="laptops">Ноутбуки</option>
        <option value="tv">Телевизоры</option>
    </select>

    <button type="submit">Показать</button>
</form>

<?php
// Массив товаров по категориям
$products = [
    "phones" => ["iPhone 15", "Samsung Galaxy S23", "Xiaomi 13 Pro"],
    "laptops" => ["MacBook Air M2", "Lenovo ThinkPad X1", "ASUS ROG Strix"],
    "tv" => ["Samsung QLED 55", "LG OLED C2", "Sony Bravia XR"]
];

// Проверяем, выбрана ли категория
if (isset($_GET['category'])) {

    $cat = $_GET['category'];

    echo "<p><b>Результат фильтрации:</b><br>";

    // Проверяем, существует ли категория в массиве
    if (array_key_exists($cat, $products)) {

        echo "Категория: " . htmlentities($cat) . "<br><br>";
        echo "Товары:<br>";

        // Выводим товары выбранной категории
        foreach ($products[$cat] as $item) {
            echo "- " . htmlentities($item) . "<br>";
        }

    } else {
        echo "Категория не найдена.";
    }

    echo "</p>";
}
?>

</body>
</html>
