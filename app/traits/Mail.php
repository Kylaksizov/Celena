<?php

namespace app\traits;

use PHPMailer\PHPMailer\PHPMailer;

trait Mail{


    public static function send($config, $reply = null, $to, $theme, $body, $files = []){

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        try {
            //Server settings
            //$mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = $config->SMTPHost;
            $mail->SMTPAuth = true;
            $mail->Username = $config->SMTPLogin;                 // SMTP username
            $mail->Password = $config->SMTPPassword;                           // SMTP password
            $mail->SMTPSecure = $config->SMTPSecure;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $config->SMTPPort;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($config->admin_email, $config->SMTPFrom);

            if(is_array($to)){
                foreach ($to as $email) {
                    $mail->addAddress($email);
                }
            } else $mail->addAddress($to);

            if(!$reply) $reply = $config->admin_email;
            $mail->addReplyTo($reply);
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            if(!empty($files)){
                foreach ($files as $file) {
                    $mail->addAttachment($file);         // Add attachments
                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                }
            }

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $theme;
            $mail->Body    = $body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;

        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

}