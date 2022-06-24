<?php

namespace app\traits;


use app\core\View;
use app\models\CommentsModel;
use Exception;

trait Comments {


    /**
     * @name получение комментариев по pid или plugin_id
     * =================================================
     * @param $e
     * @param string $template
     * @param $post_id
     * @param $plugin_id
     * @param int $status
     * @param string $sort
     * @return string
     * @throws Exception
     */
    public static function get($e, string $template = 'comments', $post_id = null, $plugin_id = null, int $status = 1, string $sort = 'ASC'){

        $result = '';

        $tpl = new View($e->route);
        $tpl->load(true, true);
        $tpl->include($template);

        $CommentsModel = new CommentsModel();
        $Comments = $CommentsModel->getAll($post_id, $plugin_id, $status, $sort);

        if(!empty($Comments["comments"])){

            foreach ($Comments["comments"] as $row) {

                $tpl->set("{id}", $row["id"]);
                $tpl->set("{author}", $row["name"]);
                $tpl->set("{date}", date("d.m.Y H:i", $row["created"]));
                $tpl->set("{comment}", $row["comment"]);

                $tpl->push();
            }

            $tpl->clearPush();
            $result = $tpl->get();
        }

        return $result;
    }


    /**
     * @name форма для комментария
     * ===========================
     * @return string
     */
    public static function form(){

        return '<form action method="POST">
            <textarea name="comment" rows="4" placeholder="Ваш комментарий"></textarea>
            <input type="submit" data-a="Comments" class="btn" value="Отправить">
        </form>';
    }
}