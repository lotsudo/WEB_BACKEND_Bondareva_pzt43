<?php
// Подключаем необходимые файлы PHPMailer (путь от lb6_attach.php к папке libs)
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
        $mail->Username   = 'feratyler04@gmail.com'; // Ваш email
        $mail->Password   = 'uyaalwozbhgtgakn'; // Ваш пароль приложения
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Получатель
        $mail->setFrom('feratyler04@gmail.com', 'Обратная связь');
        $mail->addAddress('feratyler04@gmail.com');

        // Обработка вложения
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        }

        // Тело письма
        $mail->isHTML(false);
        $mail->Subject = 'Сообщение с вложением';
        $mail->Body    = "Имя: " . $_POST['name'] . "\nСообщение: " . $_POST['message'];

        $mail->send();
        $status = "Сообщение с файлом успешно отправлено!";
    } catch (Exception $e) {
        $status = "Ошибка отправки: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отправка с вложением</title>
</head>
<body>
    <h1>Обратная связь с вложением</h1>
    <p><?php echo $status; ?></p>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Ваше имя" required><br><br>
        <textarea name="message" placeholder="Ваш вопрос" required></textarea><br><br>
        <label>Выберите файл:</label><br>
        <input type="file" name="attachment"><br><br>
        <button type="submit">Отправить</button>
    </form>
</body>
</html>