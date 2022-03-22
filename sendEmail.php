<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

require_once __DIR__ . "/rabbitmq-dmzHost/rabbitMQLib.php";

$client = new rabbitMQProducer('amq.direct', 'dmz');

$response = $client->send_request([ 'type' => 'getWatchedStocks' ]);
if(!$response) {
  die("Error comm. with RMQ!\n");
} else if (isset($response['error']) && $response['error']) {
  die($response['msg']."\n");
}

$creds = json_decode(file_get_contents(__DIR__."creds.json"));

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
$mail->Host       = $creds->host;                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = $creds->email;                     //SMTP username
$mail->Password   = $creds->password;                               //SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
$mail->Port       = $creds->port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
$mail->setFrom($creds->email, 'stonX');

try {
    $mail->addAddress('joe@example.net');
    
    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
