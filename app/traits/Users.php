<?php

namespace app\traits;

use app\_classes\Functions;
use app\models\UsersModel;
use Exception;

trait Users{



    public function addUser($name, $email, $password = null, $role = 2, $status = 1){

        if(empty($password)) $password = Functions::generationPassword();

        $UserModel = new UsersModel();
        $add = $UserModel->add($name, $email, sha1(md5($password).':NEX'), $role, $status);

        return [
            'id' => $add,
            'password' => $password,
            'role' => $role,
            'status' => $status
        ];
    }




    /**
     * @name получение информации о пользователе
     * =========================================
     * @param $id
     * @return mixed|null
     */
    public function getUser($id){
        
        $UserModel = new UsersModel();
        return $UserModel->getUser($id);
    }




    /**
     * @name получение авторизированого пользователя
     * =============================================
     * @param $id
     * @param $hash
     * @return mixed|null
     */
    public function getAuth($id, $hash){

        $UserModel = new UsersModel();
        return $UserModel->getAuth(intval($id), htmlspecialchars($hash));
    }




    /**
     * @name получение пользователей
     * =============================
     * @return array
     * @throws Exception
     */
    public function getUsers(){

        $UserModel = new UsersModel();
        return $UserModel->getUsers();
    }
}