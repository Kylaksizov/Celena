<?php

namespace app\controllers;

use app\core\Controller;
use app\core\System;
use app\core\View;
use app\models\PostModel;
use app\controllers\classes\Functions;
use app\traits\Fields;


class PostController extends Controller {



    public function indexAction(){

        //$this->view->styles = ['css/post.css'];
        //$this->view->scripts = ['js/post.js'];

        Functions::preTreatment($this);

        $PostModel = new PostModel();

        $this->view->include('post');

        $fieldsQuery = [
            'p.id, p.uid AS author_id, p.title, p.url, p.content, p.m_title, p.m_description, p.category',
            '{date}'       => 'p.created',
            '{poster}'     => 'p.poster',
            '{images}'     => '1',
        ];

        $findTags = $this->view->findTags($fieldsQuery);

        $url = str_replace(CONFIG_SYSTEM["seo_type_end"], "", end($this->urls));

        if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4'){

            preg_match('/^([0-9]+)\-(.+?)$/is', $url, $urlParams);
            if(!empty($urlParams[1]) && is_numeric($urlParams[1])){

                unset($this->urls[count($this->urls)-1]);
                $url = [
                    'id' => intval($urlParams[1]),
                    'url' => trim(htmlspecialchars(strip_tags($urlParams[2]))),
                    'categories' => $this->urls
                ];
            }
        }

        $Post = $PostModel->get($url, $findTags);

        // если товар есть
        if(!empty($Post["post"])){

            // CRUMBS
            $CategoryStep = System::setKeys($Post["categories"], "url");

            // CATEGORY NAME
            $categoryName = !empty($CategoryStep[end($this->urls)]) ? $CategoryStep[end($this->urls)]["title"] : '';
            $this->view->setMain('{category-name}', $categoryName);

            $addCategoryLink = '//'.CONFIG_SYSTEM["home"].'/';

            $catLink = $this->urls;
            array_pop($catLink);
            $catLink = implode("/", $catLink);

            $crumbs = '<div id="crumbs">';
            if(count($CategoryStep) > 1){

                $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>';

                foreach ($this->urls as $url) {

                    if(!empty($CategoryStep[$url]["title"])){
                        $addCategoryLink .= $url . '/';
                        $crumbs .= CONFIG_SYSTEM["separator"] . '<a href="' . $addCategoryLink . '">' . $CategoryStep[$url]["title"] . '</a>';
                    }
                }

            } else{

                $crumbs .= '<a href="//' . CONFIG_SYSTEM["home"] . '/">' . CONFIG_SYSTEM["site_title"] . '</a>' . CONFIG_SYSTEM["separator"] . '<a href="//' . CONFIG_SYSTEM["home"] . '/'. $catLink . '/">' . $CategoryStep[$catLink]["title"] . '</a>';
            }

            $crumbs .= '</div>';

            $this->view->setMain('{crumbs}', $crumbs);
            // CRUMBS END



            $poster = '//'.CONFIG_SYSTEM["home"].'/templates/'.CONFIG_SYSTEM["template"].'/img/no-image.svg';
            if(!empty($Post["post"]["poster_src"])){
                $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$Post["post"]["poster_src"];
            } else if(!empty($Post["images"])){
                $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.end($Post["images"])["src"];
            }


            $this->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($Post["post"]["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $Post["post"]["id"]);

            //$this->view->set('{link}', $link);
            $this->view->set('{title}', $Post["post"]["title"]);
            $this->view->set('{see}', $Post["post"]["see"]);



            // --- категории поста
            if($this->view->findTag('{categories}')){
                $categories = '<ul class="cel_categories">';
                $addLink = '//'.CONFIG_SYSTEM["home"].'/';
                foreach ($CategoryStep as $row) {

                    $addLink .= $row["url"].'/';
                    $categories .= '<li><a href="' . $addLink . '">' . $row["title"] . '</a></li>';
                }
                $categories .= '</ul>';
                $this->view->set('{categories}', $categories);
            }

            // конечная категория
            if($this->view->findTag('{category}')){
                $categoryLast = end($Post["categories"]);
                $this->view->set('{category}', '<a href="'.$addCategoryLink.'">'.$categoryLast["title"].'</a>');
            }

            // конечная категория
            if($this->view->findTag('{category-title}')){
                $categoryLast = end($Post["categories"]);
                $this->view->set('{category-title}', $categoryLast["title"]);
            }
            // ---

            $categoryLink = implode("/", $this->urls);

            // FULL LINK
            $link = $Post["post"]["url"].CONFIG_SYSTEM["seo_type_end"];
            if(CONFIG_SYSTEM["seo_type"] == '2' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $Post["post"]["id"] . '-' . $link;
            if(CONFIG_SYSTEM["seo_type"] == '3' || CONFIG_SYSTEM["seo_type"] == '4')
                $link = $categoryLink . '/' . $link;
            $link = '//'.CONFIG_SYSTEM["home"].'/'.$link;


            $this->view->set('{poster}', $poster);

            // IMAGES
            $images = '';
            if(!empty($findTags["{images}"]) && !empty($Post["images"])){
                foreach ($Post["images"] as $image) {
                    $images .= '<figure><a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"].'" data-fancybox="group"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"].'" alt=""></a></figure>';
                }
            }
            $this->view->set('{images}', $images);


            $this->view->set('{rating}', '');
            $this->view->set('{content}', $Post["post"]["content"]);


            $this->view->set('{rating-count}', '1');

            $this->view->set('{categories}', $Post["post"]["category"]);

            $this->view->include["post"] = Fields::setTags($this->view->include["post"], $Post["post"]["id"]);

            // если есть галерея, то добавляем плагин
            if(strripos($this->view->include["post"], 'data-fancybox') !== false)
                $this->view->plugins = ['fancybox'];



            $edit = '';
            if(ADMIN) $edit = '<a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/post/edit/'.$Post["post"]["id"].'/" target="_blank" class="edit_goods" title="Редактировать"></a>';

            $this->view->set('{edit}', $edit);
            $this->view->setMain('{CONTENT}', $this->view->get());



        } else{

            header("Location: ".CONFIG_SYSTEM["home"]."/404/");
            View::errorCode(404);
        }


        $metaTitle = !empty($Post["post"]["m_title"]) ? $Post["post"]["m_title"] : CONFIG_SYSTEM["site_title"];
        $metaDescription = !empty($Post["post"]["m_description"]) ? $Post["post"]["m_description"] : CONFIG_SYSTEM["site_description"];

        $this->view->setMeta($metaTitle, $metaDescription, [
            [
                'property' => 'og:title',
                'content' => $metaTitle,
            ],
            [
                'property' => 'og:description',
                'content' => $metaDescription,
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}