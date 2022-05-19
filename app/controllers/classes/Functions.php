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
        }
    }


    # TODO возможно scanTags нужно переделать (оптимизировать)

    public static function scanTags($e){

        if(!empty($e->view->include[$e->route["controller"]])) preg_match_all('/\{products(\s+category=\"(.+?)\")?(\s+template=\"(.+?)\")?(\s+limit=\"(.+?)\")?(\s+order=\"(.+?)\")?(\s+sort=\"(.+?)\")?\}/is', $e->view->include[$e->route["controller"]], $customProducts);

        preg_match_all('/\{products(\s+category=\"(.+?)\")?(\s+template=\"(.+?)\")?(\s+limit=\"(.+?)\")?(\s+order=\"(.+?)\")?(\s+sort=\"(.+?)\")?\}/is', $e->view->tplIndex, $customProductsIndex);

        if(!empty($customProducts[0]) || !empty($customProductsIndex[0])){

            $CustomProducts = new CustomProducts();

            if(!empty($customProducts[0])){

                foreach ($customProducts[0] as $tplKey => $tag) {

                    $categories = !empty($customProducts[2][$tplKey]) ? explode(",", $customProducts[2][$tplKey]) : [];

                    $res = $CustomProducts->get($e, false, $categories, $customProducts[4][$tplKey], intval($customProducts[6][$tplKey]), $customProducts[8][$tplKey], $customProducts[10][$tplKey]);
                    $e->view->include[$e->route["controller"]] = str_replace($tag, $res, $e->view->include[$e->route["controller"]]);
                }
            }

            if(!empty($customProductsIndex[0])){

                foreach ($customProductsIndex[0] as $tplKey => $tag) {

                    $categories = !empty($customProductsIndex[2][$tplKey]) ? explode(",", $customProductsIndex[2][$tplKey]) : [];

                    $res = $CustomProducts->get($e, false, $categories, $customProductsIndex[4][$tplKey], intval($customProductsIndex[6][$tplKey]), $customProductsIndex[8][$tplKey], $customProductsIndex[10][$tplKey]);
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