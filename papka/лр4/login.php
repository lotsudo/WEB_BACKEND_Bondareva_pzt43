<?php
session_start();

// чтобы не было undefined index
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = $_POST['login'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($login === 'admin' && $pass === '123') {
        $_SESSION['user'] = 'admin';
        header("Location: dashboard.php");
        exit; // ОБЯЗАТЕЛЬНО
    } else {
        $err = "неверный логин или пароль";
    }
}
?>

<?php if ($err): ?>
<p style="color:red"><?= $err ?></p>
<?php endif; ?>

<form method="post">
<input name="login" placeholder="логин"><br>
<input type="password" name="password" placeholder="пароль"><br>
<button>login</button>
</form>