<?php
$price = [
    "small" => 250,
    "medium" => 350,
    "large" => 450
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $size = $_POST['size'];
    echo "размер: $size (" . $price[$size] . ")<br>";

    if (!empty($_POST['toppings'])) {
        echo "добавки: ";
        foreach ($_POST['toppings'] as $t) {
            echo $t . " ";
        }
    } else {
        echo "без добавок";
    }

    echo "<br>коммент: " . htmlspecialchars($_POST['comment']);
    echo "<br>доставка: " . $_POST['delivery'];
}
?>

<form method="post">

<input type="radio" name="size" value="small" checked> small<br>
<input type="radio" name="size" value="medium"> medium<br>
<input type="radio" name="size" value="large"> large<br><br>

<input type="checkbox" name="toppings[]" value="сыр"> сыр<br>
<input type="checkbox" name="toppings[]" value="грибы"> грибы<br>

<textarea name="comment"></textarea><br>

<select name="delivery">
<option>самовывоз</option>
<option>курьер</option>
</select><br>

<button>go</button>
</form>