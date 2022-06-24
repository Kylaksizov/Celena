<?php

namespace app\controllers\ajax;

use app\core\System;
use app\models\CommentsModel;
use app\models\PostModel;

class Comments{



    public function index(){

        if(!empty($_POST["comment"])) self::addComment();
    }


    public function addComment(){

        if(!USER) die("info::error::Вы не авторизованы на сайте!");
        
        if(!ADMIN && empty(USER["rules"]["addComment"])) die("info::error::Вам запрещено добавлять комментарии!");

        $urls = explode("/", $_GET["url"]);

        $url = str_replace(CONFIG_SYSTEM["seo_type_end"], "", end($urls));

        if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4'){

            preg_match('/^([0-9]+)\-(.+?)$/is', $url, $urlParams);
            if(!empty($urlParams[1]) && is_numeric($urlParams[1])){

                unset($this->urls[count($urls)-1]);
                $url = [
                    'id' => intval($urlParams[1]),
                    'url' => trim(htmlspecialchars(strip_tags($urlParams[2]))),
                    'categories' => $urls
                ];
            }
        }

        $PostModel = new PostModel();
        $PostID = $PostModel->getPostId($url);

        $comment = trim(strip_tags($_POST["comment"]));

        // отправляем на модерацию
        if(!empty(USER["rules"]["addCommentModer"])){

            $infoTitle = 'Комментарий отправлен на модерацию!';
            $status = 0;

        } else{

            $infoTitle = 'Комментарий добавлен!';
            $status = 1;
        }
        
        $CommentsModel = new CommentsModel();
        $added = $CommentsModel->add($PostID, null, USER["id"], $comment, null, $status);

        if($added){

            $commentTpl = file_get_contents(ROOT . '/templates/' . CONFIG_SYSTEM["template"] . '/comments.tpl');

            $commentTpl = str_replace('{id}', $added, $commentTpl);
            $commentTpl = str_replace('{author}', USER["name"], $commentTpl);
            $commentTpl = str_replace('{date}', date("d.m.Y H:i", time()), $commentTpl);
            $commentTpl = str_replace('{comment}', $comment, $commentTpl);

            $script = '<script>
                $.server_say({say: "'.$infoTitle.'", status: "success"});
                if($("[data-comment-id]").length){
                    $("[data-comment-id]:last").after(`'.$commentTpl.'`);
                }
                $(\'[name="comment"]\').val("");
            </script>';

            System::script($script);

        } else die("info::error::Не удалось добавить комментарий!");

    }

}