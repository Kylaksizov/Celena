<?php

namespace app\traits;

trait Meta{



    public function setMeta(){

        return [
            [
                'property' => 'og:title',
                'content' => 'NEX CRM',
            ],
            [
                'property' => 'og:description',
                'content' => 'CRM система для автоматизации бизнес процессов',
            ]
        ];
    }
}