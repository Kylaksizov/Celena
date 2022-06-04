<?php

namespace app\controllers\classes;

use app\core\System;
use app\core\View;
use app\models\PostModel;

class Custom{



    public function get($e, $paginationPow = false, $categories = [], $template = 'custom', $limit = 10, $order = 'id', $sort = 'desc'){

        $tpl = new View($e->route);

        if(empty($template)) $template = 'custom';

        $PostModel = new PostModel();

        $tpl->load();
        $tpl->include($template);

        if(!is_array($categories) || $categories == 'index') $limit = CONFIG_SYSTEM["count_in_cat"];

        if(!empty($_GET["search"])) $News = $PostModel->search($_GET["search"], $categories, $limit, $order, $sort);
        else $News = $PostModel->getPosts($categories, $paginationPow, $limit, $order, $sort);

        if(!empty($News["posts"])){

            if($e->route["controller"] == 'category'){ // если категория

                // CRUMBS
                $CategoryStep = System::setKeys($News["categories"], "url");

                // CATEGORY NAME
                $categoryName = !empty($CategoryStep[end($e->urls)]) ? $CategoryStep[end($e->urls)]["title"] : '';
                $e->view->setMain('{category-name}', $categoryName);

                $addCategoryLink = '//'.CONFIG_SYSTEM["home"].'/';

                $crumbs = '<div id="crumbs">';
                if(count($CategoryStep) > 1){

                    $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>';

                    foreach ($e->urls as $url) {

                        if(!empty($CategoryStep[$url]["title"])){
                            $addCategoryLink .= $url . '/';
                            $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addCategoryLink . '">' . $CategoryStep[$url]["title"] . '</a>';
                        }
                    }

                } else{

                    $catLink = $e->urls;
                    array_pop($catLink);
                    $catLink = implode("/", $catLink);

                    $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<a href="//' . CONFIG_SYSTEM["home"] . '/'. $catLink . '/">' . $CategoryStep[$catLink]["title"] . '</a>';
                }

                $crumbs .= '</div>';

                $e->view->setMain('{crumbs}', $crumbs);
                // CRUMBS END

                $e->view->setMeta($News["categories"][$News["posts"][0]["category_id"]]["m_title"], $News["categories"][$News["posts"][0]["category_id"]]["m_description"], [
                    [
                        'property' => 'og:title',
                        'content' => $News["categories"][$News["posts"][0]["category_id"]]["m_title"],
                    ],
                    [
                        'property' => 'og:description',
                        'content' => $News["categories"][$News["posts"][0]["category_id"]]["m_description"],
                    ]
                ]);
            }

            $buildCatLinks = (CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4') ? Functions::buildCatLinks($News["categories"]) : '';

            foreach ($News["posts"] as $row) {

                // FULL LINK
                $link = $row["url"].CONFIG_SYSTEM["seo_type_end"];
                if(CONFIG_SYSTEM["seo_type"] == '2')
                    $link = $row["id"] . '-' . $link;
                if(CONFIG_SYSTEM["seo_type"] == '3')
                    $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $link;
                if(CONFIG_SYSTEM["seo_type"] == '4')
                    $link = $buildCatLinks[$row["category_id"]]["urls"].'/' . $row["id"] . '-' . $link;
                $link = '//'.CONFIG_SYSTEM["home"].'/'.$link;


                $poster = !empty($row["poster"]) ? $row["poster"] : 'no-image.png';


                $tpl->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($row["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $row["id"]);

                if($tpl->findTag('{vendor}'))
                    $tpl->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($row["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $row["vendor"]);

                $tpl->set('{link}', $link);
                $tpl->set('{title}', $row["title"]);

                $tpl->set('{poster}', '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$poster);

                $tpl->set('{images}', '');
                $tpl->set('{rating}', '');
                $tpl->set('{description}', '');

                $tpl->push();
            }

            $tpl->clearPush();

            return $tpl->get().(!empty($News["pagination"]) ? $News["pagination"] : '');
        }
    }

}