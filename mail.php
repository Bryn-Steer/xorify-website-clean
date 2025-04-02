<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';


// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = strip_tags(trim($_POST["fullname"]));
    $phone = strip_tags(trim($_POST["phone"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);
    $topic = strip_tags(trim($_POST["topic"]));

    if (empty($fullname) || empty($phone) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please complete the form and try again.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // e.g., smtp.gmail.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'answers@xorify.com';  // your email
        $mail->Password   = 'poqivlxrtqxftwjx';     // your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($email, $fullname);
        $mail->addAddress('answers@xorify.com', 'Xorify');

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission from $fullname";
        $mail->Body    = "
            <strong>Name:</strong> $fullname<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>Topic:</strong> $topic<br>
            <strong>Message:</strong><br>$message
        ";

        $mail->send();
        http_response_code(200);
        echo "Thank You! Your message has been sent.";

    } catch (Exception $e) {
        http_response_code(500);
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
