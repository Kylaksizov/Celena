<?php

namespace app\core\system\install\steps;

use app\core\System;

class Step_1{


    public function __construct() {}
    public function __clone() {}




    public function postAction(){

        return 'next';
    }




    public function indexAction(){

        $allowed = '';

        //chmod(ROOT, 0600);
        $files = substr(sprintf('%o', fileperms(ROOT)), -4);

        $phpversion = phpversion();
        $php_uname = php_uname();
        $dsFree = System::getNormSize(disk_free_space("/"));
        $ds = System::getNormSize(disk_total_space("/"));

        //$allowed = ' disabled';
        $errorText = !empty($allowed) ? '<p class="errorText">Исправьте проблемы и обновите страницу</p>' : '';

        return '<form action method="POST">
            <h1><a href="//celena.io/" id="celena_logo" target="_blank" title="Celena logo"></a> Проверка системы</h1>
            <p class="step_description">Данный шаг ещё в процессе доработки</p>
            <div class="system_test">
            
                <table>
                    <tr>
                        <td>Система</td>
                        <td><b>'.$php_uname.'</b></td>
                        <td width="20"></td>
                    </tr>
                    <tr>
                        <td>PHP version</td>
                        <td><b>'.$phpversion.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                    <tr>
                        <td>Дисковое пространство</td>
                        <td>свободно <b>'.$dsFree.'</b> из <b>'.$ds.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                </table>
                <br>
                <br>
            
                <table>
                    <tr>
                        <td>Файл</td>
                        <td width="20">Права</td>
                        <td width="20"></td>
                    </tr>
                    <tr>
                        <td>'.ROOT.'</td>
                        <td><b>'.$files.'</b></td>
                        <td><span class="is_no"></span></td>
                    </tr>
                    <tr>
                        <td>'.ROOT.'</td>
                        <td><b>'.$files.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                    <tr>
                        <td>'.ROOT.'</td>
                        <td><b>'.$files.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                    <tr>
                        <td>'.ROOT.'</td>
                        <td><b>'.$files.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                    <tr>
                        <td>'.ROOT.'</td>
                        <td><b>'.$files.'</b></td>
                        <td><span class="is_ok"></span></td>
                    </tr>
                </table>
                
            </div>
            '.$errorText.'
            <input type="submit" data-a="Step" class="btn"'.$allowed.' value="Далее">
        </form>';
    }
}