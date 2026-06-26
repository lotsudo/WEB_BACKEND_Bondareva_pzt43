<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'] ?? '';
    $mail = $_POST['email'] ?? '';
    $p1 = $_POST['password'] ?? '';
    $p2 = $_POST['confirm_password'] ?? '';

    if ($name == '' || $mail == '' || $p1 == '' || $p2 == '') {
        $errors[] = "не все поля";
    }

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email не ок";
    }

    if (strlen($p1) < 6) {
        $errors[] = "пароль короткий";
    }

    if ($p1 != $p2) {
        $errors[] = "пароли разные";
    }

    if (!$errors) {
        echo "ok: " . htmlspecialchars($name) . " " . htmlspecialchars($mail);
    }
}
?>

<?php
// вывод ошибок
foreach ($errors as $e) {
    echo "<p style='color:red'>$e</p>";
}
?>

<form method="post">
<input name="name" placeholder="имя"><br>
<input name="email" placeholder="email"><br>
<input type="password" name="password" placeholder="Пароль"><br>
<input type="password" name="confirm_password" placeholder="Повторите пароль"><br>
<button>ok</button>
</form>