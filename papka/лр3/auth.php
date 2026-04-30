<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['username'])) {
        $u = $_POST['username'];

        echo "Привет, " . htmlentities($u);
    }
}
?>