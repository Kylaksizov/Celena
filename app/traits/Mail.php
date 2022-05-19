<?php

namespace app\traits;

use PHPMailer\PHPMailer\PHPMailer;

trait Mail{


    public static function send($to, $theme, $body, $files = []){

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        try {

            if(CONFIG_SYSTEM["mail_method"] == 'smtp'){

                //Server settings
                //$mail->SMTPDebug = 2;
                $mail->isSMTP();
                $mail->Host = CONFIG_SYSTEM["SMTPHost"];
                $mail->SMTPAuth = true;
                $mail->Username = CONFIG_SYSTEM["SMTPLogin"];                 // SMTP username
                $mail->Password = CONFIG_SYSTEM["SMTPPassword"];              // SMTP password
                $mail->SMTPSecure = CONFIG_SYSTEM["SMTPSecure"];              // Enable TLS encryption, `ssl` also accepted
                $mail->Port = CONFIG_SYSTEM["SMTPPort"];

            } else{

                $mail->isMail();
            }

            //Recipients
            $mail->setFrom(CONFIG_SYSTEM["admin_email"], CONFIG_SYSTEM["SMTPFrom"]);

            if(is_array($to)){
                foreach ($to as $email) {
                    $mail->addAddress($email);
                }
            } else $mail->addAddress($to);

            $mail->addReplyTo(CONFIG_SYSTEM["admin_email"]);
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