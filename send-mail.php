<?php

// CONFIG (credenziali in mail-config.php, escluso da git)
$mail_config = @include __DIR__ . '/mail-config.php';

if (!is_array($mail_config)) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "color" => '#b42e2e',
        "message" => "Configurazione email mancante"
    ]);
    exit;
}

$gmail_username = $mail_config['gmail_username'];
$gmail_app_password = $mail_config['gmail_app_password'];
$send_to = $mail_config['send_to'];

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
    $mail->isHTML(true);
    $mail->Subject = 'Nuova richiesta dal sito';

    if ($_POST['category'] === 'fashion') {
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Nuova richiesta dal sito - MODA</title>
        </head>
        <body style="
            margin:0;
            padding:30px;
            background-color:#080808;
            font-family:Arial, sans-serif;
            color:#f7f4ef;
        ">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:650px;margin:auto;background:#111;border-radius:4px;border:1px solid #2a2a2a;">
                
                <tr>
                    <td style="
                        background:#111;
                        color:#c9a96e;
                        padding:30px 30px 20px 30px;
                        font-size:22px;
                        font-weight:bold;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        border-bottom: 2px solid #c9a96e;
                    ">
                        Nuova richiesta dal sito - MODA
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px;">

                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:15px; line-height:1.6;">

                            <tr>
                                <td style="color:rgba(247, 244, 239, 0.45); padding:10px 0; width:140px; border-bottom:1px solid #2a2a2a;">
                                    Nome
                                </td>
                                <td style="padding:10px 0; font-weight:600; color:#f7f4ef; border-bottom:1px solid #2a2a2a;">
                                    ' . $first_name . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:rgba(247, 244, 239, 0.45); padding:10px 0; border-bottom:1px solid #2a2a2a;">
                                    Cognome
                                </td>
                                <td style="padding:10px 0; font-weight:600; color:#f7f4ef; border-bottom:1px solid #2a2a2a;">
                                    ' . $last_name . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:rgba(247, 244, 239, 0.45); padding:10px 0; border-bottom:1px solid #2a2a2a;">
                                    Telefono
                                </td>
                                <td style="padding:10px 0; font-weight:600; color:#f7f4ef; border-bottom:1px solid #2a2a2a;">
                                    ' . $phone . '
                                </td>
                            </tr>

                            <tr>
                                <td style="color:rgba(247, 244, 239, 0.45); padding:10px 0; border-bottom:1px solid #2a2a2a;">
                                    Email
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #2a2a2a;">
                                    <a href="mailto:' . $email . '" style="color:#c9a96e;text-decoration:none;font-weight:600;">
                                        ' . $email . '
                                    </a>
                                </td>
                            </tr>

                        </table>

                        <div style="
                            margin-top:40px;
                            padding:20px;
                            background:#080808;
                            border:1px solid rgba(201, 169, 110, 0.3);
                            border-radius:4px;
                        ">
                            <div style="
                                color:#c9a96e;
                                font-size: 12px;
                                text-transform: uppercase;
                                letter-spacing: 1px;
                                font-weight:bold;
                                margin-bottom:12px;
                            ">
                                Messaggio
                            </div>

                            <div style="
                                color:#f7f4ef;
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
                        padding:25px 30px;
                        border-top:1px solid #2a2a2a;
                        color:rgba(247, 244, 239, 0.45);
                        font-size:11px;
                        text-align:center;
                        letter-spacing: 0.5px;
                    ">
                        Questo messaggio è stato inviato automaticamente dal modulo contatti.
                    </td>
                </tr>

            </table>

        </body>
        </html>
        ';
    } else {
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Nuova richiesta dal sito - WELLNESS</title>
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
                        Nuova richiesta dal sito  - WELLNESS
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
    }

    $mail->Body = $body;

    $mail->send();

    echo json_encode([
        "success" => true,
        "color" => '#5fbb75',
        "message" => "Messaggio inviato con successo"
    ]);
} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "color" => '#b42e2e',
        "message" => "Errore invio email"
    ]);
}
