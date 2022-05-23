<?php

namespace app\controllers;

use app\controllers\classes\Functions;
use app\core\Controller;
use app\core\View;
use app\models\PageModel;


class PageController extends Controller {



    public function indexAction(){

        Functions::preTreatment($this);

        $PageModel = new PageModel();

        $this->view->include('page');

        $url = str_replace(".html", "", $this->url);

        $Page = $PageModel->get($url);

        // если страница есть
        if(!empty($Page["page"])){

            // CRUMBS
            $this->view->setMain('{crumbs}', "");
            // CRUMBS END



            $poster = '//'.CONFIG_SYSTEM["home"].'/templates/'.CONFIG_SYSTEM["template"].'/img/no-image.svg';
            if(!empty($Page["page"]["src"])){
                $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$Page["page"]["src"];
            } else if(!empty($Page["page"]["poster"]) && !empty($Page["images"][$Page["page"]["poster"]]["src"])){
                $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$Page["images"][$Page["page"]["poster"]]["src"];
            } else if(!empty($Product["images"])){
                $poster = '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.end($Product["images"])["src"];
            }


            $this->view->set('{id}', !empty(CONFIG_SYSTEM["str_pad_id"]) ? str_pad($Page["page"]["id"], CONFIG_SYSTEM["str_pad_id"], '0', STR_PAD_LEFT) : $Page["page"]["id"]);

            if($this->view->findTag('{vendor}'))
                $this->view->set('{vendor}', !empty(CONFIG_SYSTEM["str_pad_vendor"]) ? str_pad($Page["page"]["vendor"], CONFIG_SYSTEM["str_pad_vendor"], '0', STR_PAD_LEFT) : $Page["page"]["vendor"]);

            //$this->view->set('{link}', $link);
            $this->view->set('{title}', $Page["page"]["title"]);


            $this->view->set('{poster}', $poster);

            // IMAGES
            $images = '';
            if(!empty($findTags["{images}"]) && !empty($Product["images"])){
                foreach ($Product["images"] as $image) {
                    $images .= '<figure><a href="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image["src"].'" data-fancybox="group"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image["src"].'" alt=""></a></figure>';
                }
            }
            $this->view->set('{images}', $images);

            $this->view->set('{content}', $Page["page"]["content"]);


            $edit = '';
            if(ADMIN) $edit = '<a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/pages/edit/'.$Page["page"]["id"].'/" target="_blank" class="edit_goods" title="Редактировать"></a>';

            $this->view->set('{edit}', $edit);
            $this->view->setMain('{CONTENT}', $this->view->get());



        } else{

            header("Location: ".CONFIG_SYSTEM["home"]."/404/");
            View::errorCode(404);
        }


        $this->view->setMeta($Page["page"]["m_title"], $Page["page"]["m_description"], [
            [
                'property' => 'og:title',
                'content' => $Page["page"]["m_title"],
            ],
            [
                'property' => 'og:description',
                'content' => $Page["page"]["m_description"],
            ]
        ]);

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}