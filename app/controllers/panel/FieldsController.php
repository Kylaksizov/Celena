<?php


namespace app\controllers\panel;

use app\core\PanelController;
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

        $Fields = [];

        $fieldsContent = '';

        if(!empty($Fields)){



        } else $fieldsContent = '<tr class="tc"><td colspan="6">Полей нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th width="20">ID</th>
                    <th width="200">Тег</th>
                    <th>Название поля</th>
                    <th width="130">Тип поля</th>
                    <th width="30">Статус</th>
                    <th width="50"></th>
                </tr>
                '.$fieldsContent.'
            </table>
        </div>';

        $this->view->render('Посты', $content);
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

            $h1 = 'Редактирование поля: <b>---</b>';

        }

        // категории
        $categoriesIsset = !empty($field["category"]) ? explode(",", $field["category"]) : [];
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
                        <input type="text" name="name" id="fieldName" value="'.(!empty($field["name"])?$field["name"]:'').'" autocomplete="off">
                    </div>
                    <div>
                        <label for="fieldTag" class="rq">Тег:</label>
                        <input type="text" name="tag" id="fieldTag" value="'.(!empty($field["tag"])?$field["tag"]:'').'" autocomplete="off">
                    </div>
                    <div>
                        <label for="fieldHint">Подсказка:</label>
                        <input type="text" name="hint" id="fieldHint" value="'.(!empty($field["hint"])?$field["hint"]:'').'" autocomplete="off">
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
                            <option value="textarea">Несколько строк</option>
                            <option value="select">Список</option>
                            <option value="code">Html/CSS/JS</option>
                            <option value="image">Изображение</option>
                            <option value="file">Файл</option>
                            <option value="checkbox">Переключатель \'Да\' или \'Нет\'</option>
                            <option value="dateTime">Дата и время</option>
                        </select>
                    </div>
                    <div class="fieldsSetts" data-type="input">
                        <label for="defaultInput">Значение по умолчанию:</label>
                        <input type="text" name="defaultInput" id="defaultInput" value="">
                    </div>
                    <div class="fieldsSetts" data-type="textarea,code">
                        <label for="defaultTextarea">Значение по умолчанию:</label>
                        <input type="text" name="defaultTextarea" id="defaultTextarea" value="">
                    </div>
                    <div class="fieldsSetts" data-type="select">
                        <label for="list">Список:</label>
                        <div>
                            <textarea name="list" id="list"></textarea>
                            <p class="descr">Одно значение - одна строка. Укажите разделитель прямой черты |, если хотите в выбранном варианте получить другое значение. Например, укажите: Да|Yes. Таким образом, в списке пользователь увидит Да, а при выборе в результате придет ответ Yes.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <label for="resizeOriginal">Уменьшить размер оригинала до:</label>
                        <div>
                            <input type="number" step="1" name="resizeOriginal" id="resizeOriginal" value="" placeholder="px" style="width:150px">
                            <p class="descr">Загружаемое изображение будет уменьшено до указаного размера по наибольшей стороне. Если оставить пустым, размер останется исходным.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <label for="qualityOriginal">Качество оригинала:</label>
                        <div>
                            <input type="number" step="1" name="qualityOriginal" id="qualityOriginal" value="" placeholder="%" style="width:150px">
                            <p class="descr">Укажите от 1 до 100, где 100 будет означать 100% качества, а 1 значит самое низкое качество. Если оставить пустым, качество останется исходным.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="image">
                        <input type="checkbox" name="thumb" id="thumb" class="ch_min" value="1"><label for="thumb">Создавать уменьшенную копию</label>
                    </div>
                    <div class="fieldsSetts imageThumb">
                        <label for="resizeThumb">Уменьшить размер уменьшеной копии до:</label>
                        <div>
                            <input type="number" step="1" name="resizeThumb" id="resizeThumb" value="" placeholder="px" style="width:150px">
                            <p class="descr">Укажите до каких размеров уменьшать изображение. Если оставить пустым, то размеры будут браться из общих настроек.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts imageThumb">
                        <label for="qualityThumb">Качество уменьшеной копии:</label>
                        <div>
                            <input type="number" step="1" name="qualityThumb" id="qualityThumb" value="" placeholder="%" style="width:150px">
                            <p class="descr">Сжать зображение до указанного. Если оставить пустым, то размеры будут браться из общих настроек.</p>
                        </div>
                    </div>
                    <div class="fieldsSetts" data-type="checkbox">
                        <label for="chDefault">Значение по умолчанию:</label>
                        <div>
                            <select name="chDefault" id="chDefault">
                                <option value="0">Выключено</option>
                                <option value="1">Включено</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr>
                <input type="checkbox" name="required" id="fieldRequired" value="1"><label for="fieldRequired">Обязательно для заполнения</label>
                <div class="clr"></div><br>
                
                <input type="submit" class="btn" data-a="Post" value="Сохранить">
                
            </form>';

        $this->view->render($title, $content);
    }

}