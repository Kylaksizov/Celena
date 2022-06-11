<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\System;
use app\models\panel\CategoryModel;
use Exception;


class FieldsController extends PanelController {


    /**
     * @name посты
     * ============
     * @return void
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/fields.css'];
        $this->view->scripts = ['js/fields.js'];
        $this->view->plugins = ['jquery-ui'];

        $content = '<div class="fx">
            <h1>Дополнительные поля</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/fields/add/" class="btn">Добавить поле</a>
        </div>';

        $Fields = System::getFields();

        $fieldsContent = '';

        if(!empty($Fields)){

            foreach ($Fields as $field) {

                $type = '';

                switch ($field["type"]){
                    case 'input':    $type = 'Одна строка';     break;
                    case 'textarea': $type = 'Несколько строк'; break;
                    case 'select':   $type = 'Список';          break;
                    case 'code':     $type = 'Html/CSS/JS';     break;
                    case 'image':    $type = 'Изображение';     break;
                    case 'file':     $type = 'Файл';            break;
                    case 'checkbox': $type = 'Переключатель \'Да\' или \'Нет\''; break;
                    case 'date':     $type = 'Дата';            break;
                    case 'dateTime': $type = 'Дата и время';    break;
                }

                $status = !empty($field["status"]) ? ' checked' : '';
                $rq = !empty($field["rq"]) ? 'field_rq' : '';

                $fieldsContent .= '<tr class="'.$rq.'">
                    <td>
                        <a href="/'.CONFIG_SYSTEM["panel"].'/fields/edit/'.$field["tag"].'/">'.$field["name"].'</a>
                    </td>
                    <td>
                        <b>'.$field["tag"].'</b>
                    </td>
                    <td>'.$type.'</td>
                    <td><input type="checkbox" name="status['.$field["tag"].']" class="ch_min status_field" data-tag="'.$field["tag"].'"  id="status_'.$field["tag"].'"'.$status.'><label for="status_'.$field["tag"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Fields:delete='.$field["tag"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }


        } else $fieldsContent = '<tr class="tc"><td colspan="5">Полей нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th>Название поля</th>
                    <th width="200">Тег</th>
                    <th width="130">Тип поля</th>
                    <th width="30">Статус</th>
                    <th width="50"></th>
                </tr>
                '.$fieldsContent.'
            </table>
        </div>';

        $this->view->render('Дополнительные поля', $content);
    }




    /**
     * @name добавление и редактирование постов
     * ========================================
     * @return void
     * @throws Exception
     */
    public function addAction(){

        $this->view->styles = ['css/fields.css'];
        $this->view->scripts = ['js/fields.js'];
        $this->view->plugins = ['select2'];

        $title = $h1 = 'Добавление поля';



        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);



        if(!empty($this->urls[3])){
            
            $Field = System::getField($this->urls[3]);
            $h1 = 'Редактирование поля: <b>'.$Field["name"].'</b>';
        }

        // категории
        $categoriesIsset = !empty($Field["category"]) ? $Field["category"] : [];
        $categoryOptions = '';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        $content = '<h1>'.$h1.'</h1>
            <form action method="POST" class="box_">
                <div class="table_settings">
                    <div>
                        <label for="fieldName" class="rq">Название:</label>
                        <input type="text" name="name" id="fieldName" value="'.(!empty($Field["name"])?$Field["name"]:'').'" required autocomplete="off">
                    </div>
                    <div>
                        <label for="fieldTag">Тег:</label>
                        <input type="text" name="tag" id="fieldTag" value="'.(!empty($Field["tag"])?$Field["tag"]:'').'" autocomplete="off">
                    </div>
                    <div>
                        <label for="fieldHint">Подсказка:</label>
                        <input type="text" name="hint" id="fieldHint" value="'.(!empty($Field["hint"])?$Field["hint"]:'').'" autocomplete="off">
                    </div>
                    <div>
                        <label for="fieldCategory">Категория:</label>
                        <select name="category[]" id="fieldCategory" class="multipleSelect" multiple>
                            '.$categoryOptions.'
                        </select>
                    </div>
                    <div>
                        <label for="fieldType">Тип поля</label>
                        <select name="type" id="fieldType">
                            <option value="input">Одна строка</option>
                            <option value="textarea"'.(!empty($Field["type"])&&$Field["type"]=='textarea'?' selected':'').'>Несколько строк</option>
                            <option value="select"'.(!empty($Field["type"])&&$Field["type"]=='select'?' selected':'').'>Список</option>
                            <option value="code"'.(!empty($Field["type"])&&$Field["type"]=='code'?' selected':'').'>Html/CSS/JS</option>
                            <option value="image"'.(!empty($Field["type"])&&$Field["type"]=='image'?' selected':'').'>Изображение</option>
                            <option value="file"'.(!empty($Field["type"])&&$Field["type"]=='file'?' selected':'').'>Файл</option>
                            <option value="checkbox"'.(!empty($Field["type"])&&$Field["type"]=='checkbox'?' selected':'').'>Переключатель \'Да\' или \'Нет\'</option>
                            <option value="date"'.(!empty($Field["type"])&&$Field["type"]=='date'?' selected':'').'>Дата</option>
                            <option value="dateTime"'.(!empty($Field["type"])&&$Field["type"]=='dateTime'?' selected':'').'>Дата и время</option>
                        </select>
                    </div>
                    <div class="fieldsSetts" data-type="input">
                        <label for="defaultInput">Значение по умолчанию:</label>
                        <input type="text" name="defaultInput" id="defaultInput" value="'.(!empty($Field["default"])?$Field["default"]:'').'">
                    </div>
                    <div class="fieldsSetts" data-type="textarea,code">
                        <label for="defaultTextarea">Значение по умолчанию:</label>
                        <textarea name="defaultTextarea" rows="5" id="defaultTextarea">'.(!empty($Field["default"])?$Field["default"]:'').'</textarea>
                    </div>
                    <div class="fieldsSetts" data-type="select">
                        <label for="list">Список:</label>
                        <div>
                            <textarea name="list" rows="5" id="list">'.(!empty($Field["list"])?implode(PHP_EOL, $Field["list"]):'').'</textarea>
                            <p class="descr">Одно значение - одна строка. Укажите разделитель прямой черты |, если хотите в выбранном варианте получить другое значение. Например, укажите: Да|Yes. Таким образом, в списке пользователь увидит Да, а при выборе в результате придет ответ Yes.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="select">
                        <input type="checkbox" name="multiple" id="multiple" class="ch_min" value="1"'.(!empty($Field["multiple"])?' checked':'').'><label for="multiple">Мультиселект</label>
                    </div>
                    <div class="fieldsSetts" data-type="image,file">
                        <label for="maxCount">Максимальное кол-во:</label>
                        <div>
                            <input type="number" step="1" name="maxCount" id="maxCount" value="'.(!empty($Field["maxCount"])?$Field["maxCount"]:'').'" placeholder="" style="width:150px">
                            <p class="descr">Если не указано, можно загружать неограниченое кол-во шт.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <label for="resizeOriginal">Уменьшить размер оригинала до:</label>
                        <div>
                            <input type="number" step="1" name="resizeOriginal" id="resizeOriginal" value="'.(!empty($Field["resizeOriginal"])?$Field["resizeOriginal"]:'').'" placeholder="px" style="width:150px">
                            <p class="descr">Загружаемое изображение будет уменьшено до указаного размера по наибольшей стороне. Если оставить пустым, размер останется исходным.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <label for="qualityOriginal">Качество оригинала:</label>
                        <div>
                            <input type="number" step="1" name="qualityOriginal" id="qualityOriginal" value="'.(!empty($Field["qualityOriginal"])?$Field["qualityOriginal"]:'').'" placeholder="%" style="width:150px">
                            <p class="descr">Укажите от 1 до 100, где 100 будет означать 100% качества, а 1 значит самое низкое качество. Если оставить пустым, качество останется исходным.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <input type="checkbox" name="thumb" id="thumb" class="ch_min" value="1"'.(!empty($Field["thumb"])?' checked':'').'><label for="thumb">Создавать уменьшенную копию</label>
                    </div>
                    <div class="fieldsSetts imageThumb"'.(!empty($Field["thumb"])?' style="display:grid"':'').'>
                        <label for="resizeThumb">Размер уменьшенной копии:</label>
                        <div>
                            <input type="number" step="1" name="resizeThumb" id="resizeThumb" value="'.(!empty($Field["resizeThumb"])?$Field["resizeThumb"]:'').'" placeholder="px" style="width:150px">
                            <p class="descr">Укажите до каких размеров уменьшать изображение. Если оставить пустым, то размеры будут браться из общих настроек.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts imageThumb"'.(!empty($Field["thumb"])?' style="display:grid"':'').'>
                        <label for="qualityThumb">Качество уменьшеной копии:</label>
                        <div>
                            <input type="number" step="1" name="qualityThumb" id="qualityThumb" value="'.(!empty($Field["qualityThumb"])?$Field["qualityThumb"]:'').'" placeholder="%" style="width:150px">
                            <p class="descr">Сжать зображение до указанного. Если оставить пустым, то размеры будут браться из общих настроек.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="file">
                        <label for="format">Разрешенные форматы:</label>
                        <div>
                            <input type="text" name="format" id="format" value="'.(!empty($Field["format"])?$Field["format"]:'').'" placeholder="" style="width:150px">
                            <p class="descr">Укажите форматы файлов через запятую, допустимые к загрузке. Если не указано, будут загружатся форматы, разрешенные по умолчанию: zip, rar, docx, excel, txt</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="checkbox">
                        <label for="chDefault">Значение по умолчанию:</label>
                        <div>
                            <select name="chDefault" id="chDefault">
                                <option value="0">Выключено</option>
                                <option value="1"'.(!empty($Field["default"])&&$Field["default"]=='1'?' selected':'').'>Включено</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr>
                <input type="checkbox" name="required" id="fieldRequired" value="1"'.(!empty($Field["rq"])?' checked':'').'><label for="fieldRequired">Обязательно для заполнения</label>
                <br><br>
                <input type="checkbox" name="status" id="status" value="1"'.(!empty($Field["status"])?' checked':'').'><label for="status">Активный статус</label>
                <div class="clr"></div><br>
                
                <input type="submit" class="btn" data-a="Fields" value="Сохранить">
                
            </form>';

        $this->view->render($title, $content);
    }

}