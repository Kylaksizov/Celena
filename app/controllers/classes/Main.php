<?php

namespace app\controllers\classes;


class Main{


    # TODO возможно scanTags нужно переделать (оптимизировать)

    public static function scanTags($e){

        preg_match_all('/\{products\s+(category=\"(.+?)\")?\s+(template=\"(.+?)\")?\s+(limit=\"(.+?)\")?\}/is', $e->view->include[$e->route["controller"]], $customProducts);

        preg_match_all('/\{products\s+(category=\"(.+?)\")?\s+(template=\"(.+?)\")?\s+(limit=\"(.+?)\")?\}/is', $e->view->tplIndex, $customProductsIndex);

        if(!empty($customProducts[0]) || !empty($customProductsIndex[0])){

            $Products = new CustomProducts();

            if(!empty($customProducts[0])){

                foreach ($customProducts[0] as $tplKey => $tag) {

                    $res = $Products->get($e, $customProducts[4][$tplKey]);
                    $e->view->clear();
                    //$e->view->set($tag, $res);
                    $e->view->include[$e->route["controller"]] = str_replace($tag, $res, $e->view->include[$e->route["controller"]]);
                }
            }

            if(!empty($customProductsIndex[0])){

                foreach ($customProductsIndex[0] as $tplKey => $tag) {

                    $res = $Products->get($e, $customProductsIndex[4][$tplKey]);
                    $e->view->clear();
                    $e->view->tplIndex = str_replace($tag, $res, $e->view->tplIndex);
                }
            }
        }
    }

}