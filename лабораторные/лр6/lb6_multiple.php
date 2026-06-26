<?php
require __DIR__ . '/код/PHPMailer-master/src/Exception.php';
require __DIR__ . '/код/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/код/PHPMailer-master/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recipients'])) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'feratyler04@gmail.com'; 
        $mail->Password   = 'uyaalwozbhgtgakn'; // Не забудьте вставить код!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('feratyler04@gmail.com', 'Массовая рассылка');

        // Получаем строку из формы
        $emailString = $_POST['recipients'];
        $emails = explode(',', $emailString);

        foreach ($emails as $email) {
            $email = trim($email); // Убираем лишние пробелы вокруг email
            if (!empty($email)) {
                $mail->addAddress($email);
            }
        }

        $mail->Subject = 'Тестовая массовая рассылка';
        $mail->Body    = "Привет! Это сообщение отправлено пользователям с сайта.";

        $mail->send();
        $status = "Письмо успешно отправлено всем указанным адресатам!";
    } catch (Exception $e) {
        $status = "Ошибка: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Массовая рассылка</title>
</head>
<body>
    <h1>Отправка группе получателей</h1>
    <p><?php echo $status; ?></p>
    <form action="" method="POST">
        <label>Введите Email адреса через запятую:</label><br>
        <input type="text" name="recipients" placeholder="mail1@ex.com, mail2@ex.com" required style="width: 300px;"><br><br>
        <button type="submit">Отправить письмо</button>
    </form>
</body>
</html>