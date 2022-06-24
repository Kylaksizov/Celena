<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\PostModel;
use app\models\UsersModel;
use app\traits\SiteMap;
use Exception;

class Roles{

    public function index(){

        if(!empty($_POST["name"])) self::saveEditRole();
        if(!empty($_POST["delete"])) self::deleteRole();

        die("info::error::Неизвестный запрос!");
    }


    /**
     * @name сохранение / редактирование роли
     * ======================================
     * @return void
     * @throws Exception
     */
    private static function saveEditRole(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $role);
        $roleId = !empty($role[1]) ? intval($role[1]) : null;

        $name = trim(strip_tags($_POST["name"]));
        unset($_POST["name"]);

        $rules = ($roleId == '1') ? null : json_encode($_POST, JSON_UNESCAPED_UNICODE);

        $UsersModel = new UsersModel();

        if(empty($roleId)) $UsersModel->addRole($name, $rules);
        else $UsersModel->editRole($roleId, $name, $rules);

        $script = '<script>
            $.server_say({say: "Изменения сохранены!", status: "success"});
            setTimeout(function(){
                window.location.href = "/'.CONFIG_SYSTEM["panel"].'/roles/";
            }, 1000)
        </script>';

        System::script($script);
    }


    /**
     * @name удаление группы
     * =====================
     * @return void
     * @throws Exception
     */
    #TODO доделать перемещение всех пользователей в другую группу
    private function deleteRole(){

        $id = intval($_POST["delete"]);
        
        if($id == '1' || $id == '2') die("info::error::Данная группа не может быть удалена!");

        $UsersModel = new UsersModel();
        $UsersModel->deleteRole($id);

        $script = '<script>
            $.server_say({say: "Группа удалена!", status: "success"});
            $(".cel_tmp").closest("tr").remove();
        </script>';

        System::script($script);
    }

}