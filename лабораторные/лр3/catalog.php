<?php
$products = [
['name'=>'ноутбук','category'=>'tech','price'=>50000],
['name'=>'книга','category'=>'books','price'=>1000],
['name'=>'мышка','category'=>'tech','price'=>1500],
['name'=>'сумка','category'=>'other','price'=>2000],
];

$min = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$max = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : 999999;
$cat = $_GET['category'] ?? '';

?>

<form>
<input name="min_price" placeholder="min">
<input name="max_price" placeholder="max">

<select name="category">
<option value="">all</option>
<option value="tech">tech</option>
<option value="books">books</option>
<option value="other">other</option>
</select>

<button>ok</button>
</form>

<hr>

<?php
foreach ($products as $p) {

    if ($p['price'] < $min || $p['price'] > $max) continue;
    if ($cat && $p['category'] != $cat) continue;

    echo $p['name'] . " " . $p['price'] . "<br>";
}
?>