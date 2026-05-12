<?php

// CONFIG
$gmail_username = "website.afattori@gmail.com";
$gmail_app_password = "vait mlfm eiqr omcb";
$send_to = "pietromambelli8@gmail.com";

// ONLY HANDLE POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit;
}

// SANITIZE INPUTS
$first_name = htmlspecialchars(trim($_POST["first_name"] ?? ""));
$last_name = htmlspecialchars(trim($_POST["last_name"] ?? ""));
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$class_interest = htmlspecialchars(trim($_POST["class_interest"] ?? ""));
$message = htmlspecialchars(trim($_POST["message"] ?? ""));

// VALIDATE
if (!$first_name || !$email) {
    die("Compila i campi obbligatori.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email non valida.");
}

// LOAD PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {

    // SMTP CONFIG
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $gmail_username;
    $mail->Password = $gmail_app_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // EMAIL SETTINGS
    $mail->setFrom($gmail_username, 'Website Contact Form');
    $mail->addAddress($send_to);

    // REPLY TO USER
    $mail->addReplyTo($email, $first_name . ' ' . $last_name);

    // CONTENT
    $mail->isHTML(false);
    $mail->Subject = 'Nuova richiesta dal sito';

    $body = "
        Nuova richiesta dal sito

        Nome: $first_name
        Cognome: $last_name
        Email: $email
        Interesse: $class_interest

        Messaggio:
        $message
        ";

    $mail->Body = $body;

    $mail->send();

    echo "Messaggio inviato con successo.";
} catch (Exception $e) {
    echo "Errore invio email: {$mail->ErrorInfo}";
}
