<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\CommentsModel;
use Exception;

class Comments{

    public function index(){

        if(!empty($_POST["active"])) self::editStatus(intval($_POST["active"]), 1);
        if(!empty($_POST["moder"])) self::editStatus(intval($_POST["moder"]), 0);
        if(!empty($_POST["delete"])) self::delete();

        die("info::error::Неизвестный запрос!");
    }


    /**
     * @name изменение статуса комментария
     * ===================================
     * @return void
     * @throws Exception
     */
    private function editStatus($id, $status){

        $CommentsModel = new CommentsModel();
        $edit = $CommentsModel->editStatus($id, $status);

        if($edit){

            if($status){

                $script = '<script>
                    $.server_say({say: "Статус изменен!", status: "success"});
                    $(\'[data-a="Comments:active='.$id.'"]\').removeClass("comment_moderation").addClass("comment_active").attr("data-a", "Comments:moder='.$id.'");
                </script>';

            } else{

                $script = '<script>
                    $.server_say({say: "Статус изменен!", status: "success"});
                    $(\'[data-a="Comments:moder='.$id.'"]\').removeClass("comment_active").addClass("comment_moderation").attr("data-a", "Comments:active='.$id.'");
                </script>';
            }

            System::script($script);
        }

        die("info::error::Не удалось изменить статус!");
    }




    /**
     * @name удаление комментария
     * ==========================
     * @return void
     * @throws Exception
     */
    private function delete(){

        $id = intval($_POST["delete"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Comments", "delete": "'.$id.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));

        } else {

            $CommentsModel = new CommentsModel();
            $result = $CommentsModel->delete($id);

            if($result){

                $script = '<script>
                    $(\'[data-a="Comments:delete='.$id.'"]\').closest("tr").remove();
                    $.server_say({say: "Комментарий удален!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить комментарий!");
            }
        }
    }

}