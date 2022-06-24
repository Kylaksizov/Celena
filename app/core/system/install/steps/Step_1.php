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

        $files = [
            ROOT . '/app/cache',
            ROOT . '/app/core/data',
            ROOT . '/uploads',
            ROOT . '/templates',
        ];

        $filesContent = '';
        foreach ($files as $file) {
            @chmod($file, 0777);
            $chmod = substr(sprintf('%o', fileperms($file)), -4);

            $fileStatus = 'is_ok';

            if($chmod != '0777'){
                $fileStatus = 'is_no';
                $allowed = ' disabled';
            }

            $file = str_replace(ROOT.'/', ROOT . '/<b>', $file) . '</b>';

            $filesContent .= '<tr>
                <td class="file_path">'.$file.'</td>
                <td><b>'.$chmod.'</b></td>
                <td><span class="'.$fileStatus.'"></span></td>
            </tr>';
        }

        $phpversion = phpversion();
        $php_uname = php_uname();

        $dsFree = System::getNormSize(disk_free_space(ROOT));
        $ds = System::getNormSize(disk_total_space(ROOT));


        $errorText = !empty($allowed) ? '<p class="errorText">Исправьте проблемы и обновите страницу</p>' : '';

        return '<form action method="POST">
            <h1><a href="//celena.io/" id="celena_logo" target="_blank" title="Celena logo"></a> Проверка системы</h1>
            <p class="step_description">Проверьте права на файлы и папки</p>
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
                    '.$filesContent.'
                    <tr>
                        <td colspan="3"><b style="color:#e77b7b"><i>Для указанных файлови папок, поставьте права 0777</i></b></td>
                    </tr>
                </table>
                
            </div>
            '.$errorText.'
            <input type="submit" data-a="Step" class="btn"'.$allowed.' value="Далее">
        </form>';
    }
}