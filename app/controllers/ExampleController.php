<?php

namespace app\controllers;

use app\_classes\Auth;
use app\core\Controller;
use app\models\UsersModel;
use app\traits\Users;
use app\libs\binotel\BinotelApi;


class ExampleController extends Controller{


    use Users;


    public function indexAction(){



        echo Auth::google_init();



        $key = '0104fb-9aac681';
        $secret = '91342b-502ac4-531e9e-da770b-caf135af';

        $api = new BinotelApi($key, $secret);

        //$result = $api->sendRequest('settings/list-of-voice-files', array());

        // двухсторонний звонок - работает
        /*$result = $api->sendRequest('calls/internal-number-to-external-number', array(
            'internalNumber' => '901',
            'externalNumber' => '0952059675'
        ));*/
        //return $result["generalCallID"];

        // Принудительное завершение звонка
        /*$result = $api->sendRequest('calls/hangup-call', array(
            'generalCallID' => '3248527349'
        ));*/

        // Входящие звонки за период времени
        $result = $api->sendRequest('stats/incoming-calls-for-period', array(
            'startTime' => 1370034000, // Sat, 01 Jun 2013 00:00:00 +0300
            'stopTime' => time() // Sat, 01 Jun 2013 23:59:59 +0300
        ));

        // Потерянные звонки за сегодня
        //$result = $api->sendRequest('stats/list-of-lost-calls-for-today', array());

        // Данные о звонке по идентификатору звонка
        /*$result = $api->sendRequest('stats/call-details', array(
            'generalCallID' => array('3248285072', '2256039', '2252553')
        ));*/

        // Ссылка на запись разговора
        /*$result = $api->sendRequest('stats/call-record', array(
            'generalCallID' => '3248285072'
        ));*/

        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;

        if ($result['status'] === 'success') {
            var_dump($result['listOfVoiceFiles']);
        } else {
            printf('REST API ошибка %s: %s %s', $result['code'], $result['message'], PHP_EOL);
        }


        $UsersModel = new UsersModel();
        $UsersModel->add();


        $this->view->load();

        // $includeMenu = $this->view->include('includes/menu');



        $this->view->include('includes/menu');
        $this->view->set('{menu-title}', 'Главная');
        $this->view->setMain('{menu}', $this->view->get());
        $this->view->clear();

        //$this->view->set('{test}', 'work');
        //$this->view->get('page');

        $this->view->include('test');
        $this->view->set('{res}', '777');
        $test = $this->view->get();
        $this->view->clear();

        $this->view->include('page');
        $this->view->set('{test}', '333');

        foreach (['1', '2'] as $item) {

            $this->view->set('{test}', $test . '-' . $item);
            $this->view->push();
        }
        $this->view->clearPush();

        //$this->view->setMain('{menu}', $this->view->get());

        /*$this->view->styles = [
            'css/test.css',
            'css/test2.css'
        ];
        $this->view->scripts = [
            'js/test.js',
            'js/test2.js'
        ];*/

        $this->view->setMeta('Главная страница', 'Описание страницы', [
            [
                'property' => 'og:title',
                'content' => 'Описание страницы',
            ],
            [
                'property' => 'og:description',
                'content' => 'Для гугла',
            ]
        ]);

        $this->view->render();
    }

}