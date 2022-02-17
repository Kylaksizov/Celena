<?php

namespace app\traits;


// https://api.telegram.org/bot5045603422:AAF3be0tXSZXdPz_3kt0xOhfluSN0jcv4eU/setWebhook?url=https://dle-x.com/telegram_bot.php
// https://core.telegram.org/bots/api#formatting-options

use CURLFile;

trait Telegram {


    public $chatId = ""; // 407136082
    //public $chatId = "-1001772241758"; // Канал


    public function TelegramSend($text, $link = false, $image = false){

        if(!empty($image) && !empty($link)){

            $url = "https://api.telegram.org/bot" . CONFIG_SYSTEM["telegramToken"] . "/sendPhoto?chat_id=" . $this->chatId . "&text=" . urlencode($text)."&parse_mode=HTML&disable_web_page_preview=false";

            $post_fields = array('chat_id'   => $this->chatId,
                'photo'     => new CURLFile(realpath($image)),
                'caption' => '<a href="'.$link.'"><b>'.$text.'</b></a>'
            );

            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type:multipart/form-data"
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            $bot = curl_exec($ch);
            curl_close($ch);

        } else{

            $url = "https://api.telegram.org/bot" . CONFIG_SYSTEM["telegramToken"] . "/sendMessage?chat_id=" . $this->chatId . "&text=" . urlencode('<a href="'.$link.'"><b>'.$text.'</b></a>')."&parse_mode=HTML";

            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $bot = curl_exec($ch);
            curl_close($ch);
        }

        return $bot;
    }
}