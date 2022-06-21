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

        if(!is_array($categories) || $categories == 'index') $limit = CONFIG_SYSTEM["count_in_cat"];

        if(!empty($_GET["search"])) $News = $PostModel->search($_GET["search"], $categories, $limit, $order, $sort);
        else $News = $PostModel->getPosts($categories, $paginationPow, $limit, $order, $sort);

        if(!empty($News["posts"])){

            if($e->route["controller"] == 'category'){ // если категория

                if(!empty($News["posts"][0]["tpl_min"])) $template = $News["posts"][0]["tpl_min"];

                $tpl->include($template);

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
                    if(count($catLink) > 1){
                        array_pop($catLink);
                        $catLink = implode("/", $catLink);
                    } else $catLink = $catLink[0];

                    if(!empty($CategoryStep[$catLink]["title"]))
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
                
            } else $tpl->include($template);

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


                $poster = !empty($row["poster"]) ? '/uploads/posts/'.$row["poster"] : '/templates/system/img/no-image.svg';


                preg_match('/\{description(?:\slimit=\"(\d+)\")?\}/', $tpl->include[$template], $tplDescription);
                $descriptionTag = !empty($tplDescription[0]) ? $tplDescription[0] : '{description}';

                if(empty($tplDescription[1])) $short = $row["short"];
                else $short = mb_strimwidth(strip_tags($row["short"]), 0, intval($tplDescription[1]), '...');
                $tpl->set($descriptionTag, $short);

                
                $tpl->set('{link}', $link);
                $tpl->set('{title}', $row["title"]);
                $tpl->set('{see}', $row["see"]);

                $tpl->set('{date}', date("d.m.Y", $row["created"]));

                $tpl->set('{poster}', '//'.CONFIG_SYSTEM["home"].$poster);

                $tpl->set('{images}', '');
                $tpl->set('{rating}', '');

                $tpl->push();
            }

            $tpl->clearPush();

            return $tpl->get().(!empty($News["pagination"]) ? $News["pagination"] : '');
        }
    }

}