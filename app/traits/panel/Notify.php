<?php

namespace app\traits\panel;

use app\models\panel\NotifyModel;

trait Notify{


    public function addNotify($title, $message, $link = '', $status = 1){

        if(mb_strlen($title) > 30) return 'Слишком длинный заголовок, должно быть не более 30 символов';
        
        $NotifyModel = new NotifyModel();
        return $NotifyModel->add($title, $message, $link, $status);
    }


    public function getNotify(){

        $NotifyModel = new NotifyModel();
        return $NotifyModel->getMessages();
    }


    public function seeNotify($id){

        $NotifyModel = new NotifyModel();
        return $NotifyModel->see($id);
    }

}