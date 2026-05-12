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
$phone = htmlspecialchars(trim($_POST["phone"] ?? ""));
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
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

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
    $mail->isHTML(true);
    $mail->Subject = 'Nuova richiesta dal sito';

    $body = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Nuova richiesta dal sito</title>
        </head>
        <body style="
            margin:0;
            padding:30px;
            background-color:#f9f6f0;
            font-family:Arial, sans-serif;
            color:#2e3a2b;
        ">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:650px;margin:auto;background:#fdfbf7;border-radius:12px;border:1px solid #c8d4c2;">
                
                <tr>
                    <td style="
                        background:#6b7d63;
                        color:white;
                        padding:24px 30px;
                        font-size:24px;
                        font-weight:bold;
                        border-radius:12px 12px 0 0;
                    ">
                        Nuova richiesta dal sito
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px;">

                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:15px; line-height:1.6;">

                            <tr>
                                <td style="color:#8a9685; padding:8px 0; width:140px;">
                                    Nome
                                </td>
                                <td style="padding:8px 0; font-weight:600;">
                                    ' . $first_name . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:#8a9685; padding:8px 0;">
                                    Cognome
                                </td>
                                <td style="padding:8px 0; font-weight:600;">
                                    ' . $last_name . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:#8a9685; padding:8px 0;">
                                    Telefono
                                </td>
                                <td style="padding:8px 0; font-weight:600;">
                                    ' . $phone . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:#8a9685; padding:8px 0;">
                                    Email
                                </td>
                                <td style="padding:8px 0;">
                                    <a href="mailto:' . $email . '" style="color:#c17c5a;text-decoration:none;">
                                        ' . $email . '
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td style="color:#8a9685; padding:8px 0;">
                                    Interesse
                                </td>
                                <td style="padding:8px 0; font-weight:600;">
                                    ' . $class_interest . '
                                </td>
                            </tr>

                        </table>

                        <div style="
                            margin-top:30px;
                            padding:20px;
                            background:#f9f6f0;
                            border-left:4px solid #c17c5a;
                            border-radius:8px;
                        ">
                            <div style="
                                color:#3d4f38;
                                font-weight:bold;
                                margin-bottom:10px;
                            ">
                                Messaggio
                            </div>

                            <div style="
                                color:#2e3a2b;
                                white-space:pre-line;
                                line-height:1.7;
                            ">
                                ' . $message . '
                            </div>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td style="
                        padding:20px 30px;
                        border-top:1px solid #c8d4c2;
                        color:#8a9685;
                        font-size:12px;
                        text-align:center;
                    ">
                        Messaggio inviato dal modulo contatti del sito
                    </td>
                </tr>

            </table>

        </body>
        </html>
        ';

    $mail->Body = $body;

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "Messaggio inviato con successo"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Errore invio email"
    ]);
}
