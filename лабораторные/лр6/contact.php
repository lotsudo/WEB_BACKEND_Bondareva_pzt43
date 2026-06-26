<?php
require __DIR__ . '/код/PHPMailer-master/src/Exception.php';
require __DIR__ . '/код/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/код/PHPMailer-master/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);
    try {
        // Настройки SMTP 
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'feratyler04@gmail.com';
        $mail->Password   = 'uyaalwozbhgtgakn'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('feratyler04@gmail.com', 'Сервис сайта');
        $mail->addAddress('feratyler04@gmail.com');

        // Определение, какая форма была отправлена
        if (isset($_POST['type'])) {
            if ($_POST['type'] == 'subscribe') {
                $mail->Subject = 'Новая подписка';
                $mail->Body    = "Email для подписки: " . $_POST['email'];
            } elseif ($_POST['type'] == 'feedback') {
                $mail->Subject = 'Обратная связь';
                $mail->Body    = "Имя: {$_POST['name']}\nСообщение: {$_POST['message']}";
            } elseif ($_POST['type'] == 'anketa') {
                $mail->Subject = 'Заполненная анкета';
                $mail->Body    = "ФИО: {$_POST['fio']}\nВозраст: {$_POST['age']}\nОтзыв: {$_POST['review']}";
            }
            $mail->send();
            $status = "Данные успешно отправлены!";
        }
    } catch (Exception $e) {
        $status = "Ошибка: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<body>
    <h2><?php echo $status; ?></h2>

    <h3>Подписка</h3>
    <form method="POST"><input type="hidden" name="type" value="subscribe">
        <input type="email" name="email" placeholder="Email" required><button>Подписаться</button>
    </form>

    <h3>Обратная связь</h3>
    <form method="POST"><input type="hidden" name="type" value="feedback">
        <input type="text" name="name" placeholder="Имя" required><br>
        <textarea name="message" placeholder="Сообщение" required></textarea><br>
        <button>Отправить</button>
    </form>

    <h3>Анкета</h3>
    <form method="POST"><input type="hidden" name="type" value="anketa">
        <input type="text" name="fio" placeholder="ФИО" required><br>
        <input type="number" name="age" placeholder="Возраст"><br>
        <textarea name="review" placeholder="Ваш отзыв"></textarea><br>
        <button>Отправить анкету</button>
    </form>
</body>
</html>