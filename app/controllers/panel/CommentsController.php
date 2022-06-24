<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\CommentsModel;


class CommentsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/comments.css'];
        $this->view->scripts = ['js/comments.js'];

        $content = '<h1>Пользователи</h1>';

        $CommentsModel = new CommentsModel();
        $Comments = $CommentsModel->getAll(null, null, null, 'DESC');

        if($Comments["comments"]){

            $contentComments = '';

            foreach ($Comments["comments"] as $row) {

                $status = $row["status"] ? '<a href="#" data-a="Comments:moder='.$row["id"].'" class="comment_active"></a>' : '<a href="#" data-a="Comments:active='.$row["id"].'" class="comment_moderation"></a>';

                $contentComments .= '<tr class="role_'.$row["role"].'">
                    <td>'.$row["id"].'</td>
                    <td><a href="#" onclick="alert(\'В разработке\');" class="ico_see"></a></td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/users/'.$row["id"].'/">'.$row["name"].'</a> <span class="fs12">('.$row["role_name"].')</span>'.CONFIG_SYSTEM["separator"].'
                        <div class="clr"></div>
                        <div class="comment_box">'.$row["comment"].'</div>
                    </td>
                    <td class="fs12">'.date("d.m.Y H:i", $row["created"]).'</td>
                    <td class="tc">'.$status.'</td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Comments:delete='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $contentComments = '<tr class="tc"><td colspan="6">Комментариев нет</td></tr>';

        $content .= '<table class="no_odd">
            <tr>
                <th width="10">#</th>
                <th width="10"><span class="ico_see"></span></th>
                <th>Автор (группа)'.CONFIG_SYSTEM["separator"].'Комментарий</th>
                <th width="120">Дата / время</th>
                <th width="70">Статус</th>
                <th width="50"></th>
            </tr>
            '.$contentComments.'
        </table>'.$Comments["pagination"];

        $this->view->render('Комментарии', $content);
    }

}