<?php

namespace app\controllers\classes;


class Functions{


    public static function preTreatment($e){

        //if(!USER) Auth::google_callback();

        //$Auth = Auth::google_init();

        if(!USER){

            $e->view->include('login');
            //$this->view->set('{reg}', $Auth);

            $new_password = '';
            if(!empty($_GET["member_pass"])){

                $AuthModel = new AuthModel(true);
                $member_test = $AuthModel->member_test($_GET["member_pass"]);

                if(is_numeric($member_test)){

                    $new_password = '<div id="new_password" class="modal" style="display:block;">
                        <h4 class="modal_title">Восстановление пароля</h4>
                        <form action="#" method="POST" class="inp100">
                            <input type="password" name="password" placeholder="Введите новый пароль">
                            <input type="password" name="password_repeat" placeholder="Повторите пароль">
                            <input type="hidden" name="action" value="member_pass_start">
                            <input type="hidden" name="new_password" value="'.$member_test.'"><br>
                            <input type="submit" class="btn" data-a="Registration" value="Восстановить">
                        </form>
                    </div>';

                } else{

                    header("Location: /");
                    exit;
                }
            }

            $e->view->set('{new-password}', $new_password);

            $login = $e->view->get();
            $e->view->setMain('{login}', $login);

        } else $e->view->setMain('{login}', '');

        $e->view->setMain('{sort}', '');
    }


    # TODO возможно scanTags нужно переделать (оптимизировать)

    public static function scanTags($e){

        if(!empty($e->view->include[$e->route["controller"]])) preg_match_all('/\{custom(\s+category=\"(.+?)\")?(\s+template=\"(.+?)\")?(\s+limit=\"(.+?)\")?(\s+order=\"(.+?)\")?(\s+sort=\"(.+?)\")?\}/is', $e->view->include[$e->route["controller"]], $custom);

        preg_match_all('/\{custom(\s+category=\"(.+?)\")?(\s+template=\"(.+?)\")?(\s+limit=\"(.+?)\")?(\s+order=\"(.+?)\")?(\s+sort=\"(.+?)\")?\}/is', $e->view->tplIndex, $customIndex);

        if(!empty($custom[0]) || !empty($customIndex[0])){

            $Custom = new Custom();

            if(!empty($custom[0])){

                foreach ($custom[0] as $tplKey => $tag) {

                    $categories = !empty($custom[2][$tplKey]) ? explode(",", $custom[2][$tplKey]) : [];

                    $res = $Custom->get($e, false, $categories, $custom[4][$tplKey], intval($custom[6][$tplKey]), $custom[8][$tplKey], $custom[10][$tplKey]);
                    $e->view->include[$e->route["controller"]] = str_replace($tag, $res, $e->view->include[$e->route["controller"]]);
                }
            }

            if(!empty($customIndex[0])){

                foreach ($customIndex[0] as $tplKey => $tag) {

                    $categories = !empty($customIndex[2][$tplKey]) ? explode(",", $customIndex[2][$tplKey]) : [];

                    $res = $Custom->get($e, false, $categories, $customIndex[4][$tplKey], intval($customIndex[6][$tplKey]), $customIndex[8][$tplKey], $customIndex[10][$tplKey]);
                    $e->view->tplIndex = str_replace($tag, $res, $e->view->tplIndex);
                }
            }
        }
    }


    /**
     * @name построение ссылок категорий
     * =================================
     * @param $categories
     * @return array
     */
    public static function buildCatLinks($categories){
        $result = [];
        foreach ($categories as $id => $category) {
            $result[$id] = $category;
            if(empty($category["pid"])) $result[$id]["urls"] = $category["url"];
            else{
                $result[$id]["urls"] = $categories[$category["pid"]]["url"].'/'.$category["url"];
                if(!empty($categories[$category["pid"]]["pid"])){
                    $result[$id]["urls"] = self::rebuildCategory($categories, $categories[$category["pid"]]["pid"]).$result[$id]["urls"];
                }
            }
        }
        return $result;
    }

    private static function rebuildCategory($categories, $pid){
        $result = '';
        if(!empty($categories[$pid]["url"])) $result = $categories[$pid]["url"].'/';
        if(!empty($categories[$pid]["pid"])){
            $result = self::rebuildCategory($categories, $categories[$pid]["pid"]).$result;
        }
        return $result;
    }

}