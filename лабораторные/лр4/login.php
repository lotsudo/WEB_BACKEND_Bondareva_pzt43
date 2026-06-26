<?php
session_start();

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($login === 'admin' && $pass === '123') {
        $_SESSION['user'] = 'admin';
        session_write_close();
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "неверный логин или пароль";
    }
}
?> <!DOCTYPE html>
<html lang="ru">
<body>

<?php if ($err): ?>
    <p style="color:red"><?= $err ?></p>
<?php endif; ?>

<form method="post">
    <input name="login" placeholder="логин"><br>
    <input type="password" name="password" placeholder="пароль"><br>
    <button type="submit">login</button>
</form>

</body>
</html>