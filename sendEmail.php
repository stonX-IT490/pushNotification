<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

$creds = (array)json_decode(file_get_contents(__DIR__."/creds.json"));
print_r($creds);

require_once __DIR__ . "/rabbitmq-common/rabbitMQLib.php";
$client = new rabbitMQProducer('amq.direct', 'push');
$response = $client->send_request( [ ] );
if(!$response) {
  die("Error comm. with RMQ!\n");
} else if (isset($response['error']) && $response['error']) {
  die($response['msg']."\n");
}

print_r($response);

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->isSMTP();
$mail->Host = $creds['host'];
$mail->SMTPAuth = true;
$mail->Username = $creds['email'];
$mail->Password = $creds['password'];
$mail->SMTPSecure = 'tls';
$mail->Port = $creds['port'];
$mail->setFrom($creds['email'], 'stonX');

foreach($response as $watch) {
  try {
    $mail->clearAllRecipients();
    $mail->addAddress($watch['email']);
    
    //Content
    $mail->isHTML(true);
    $mail->Subject = 'stonX Watchlist Alert';
    $mail->Body = 'Your stock ' . $watch['symbol'] . ' has hit your given threshold: ' . $watch['watchValue'];

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
