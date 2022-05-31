$(function(){

    let php = {
        //lineWrapping: true
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-php",
        indentUnit: 4,
        theme: "celena"
    }

    let base = {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-mysql",
        indentUnit: 4,
        theme: "celena"
    }

    /*CodeMirror.fromTextArea(document.getElementById("code"), php);
    CodeMirror.fromTextArea(document.getElementById("code2"), php);*/

    CodeMirror.fromTextArea(document.getElementById("baseInstall"), base);
    CodeMirror.fromTextArea(document.getElementById("baseUpdate"), base);
    CodeMirror.fromTextArea(document.getElementById("baseOn"), base);
    CodeMirror.fromTextArea(document.getElementById("baseOff"), base);
    CodeMirror.fromTextArea(document.getElementById("baseDel"), base);

    $(document).on("click", ".add_file", function(){

        let milliseconds = new Date();
        let fileId = milliseconds.getTime();

        $("#filesMod").append(`<div class="fileMod" data-fileId="`+fileId+`">
                <div class="fx ai_c">
                    <div class="fpb">
                        <label for="">Путь к файлу:</label>
                        <input type="text" name="filePath[`+fileId+`]" class="filePath" placeholder="controllers/...">
                    </div>
                    <a href="#" class="remove remove_file"></a>
                </div>
                <div class="actionsFile">
                    <div class="fx ai_c">
                        <select name="actionsFile[`+fileId+`][]" class="actionsFileSelect">
                            <option value="">Выбрать действие</option>
                            <option value="1">Найти и заменить</option>
                            <option value="2">Найти и добавить выше</option>
                            <option value="3">Найти и добавить ниже</option>
                            <option value="4">Заменить файл</option>
                            <option value="5">Создать новый файл</option>
                        </select>
                        <a href="#" class="remove remove_action"></a>
                    </div>
                    <div class="actionsBox"></div>
                </div>
                <a href="#" class="add_action">Добавить действие</a>
            </div>`);
        return false;
    })

    $(document).on("click", ".add_action", function(){

        let fileId = $(this).parent().attr("data-fileId");

        $(this).before(`<div class="actionsFile">
                <div class="fx ai_c">
                    <select name="actionsFile[`+fileId+`][]" class="actionsFileSelect">
                        <option value="">Выбрать действие</option>
                        <option value="1">Найти и заменить</option>
                        <option value="2">Найти и добавить выше</option>
                        <option value="3">Найти и добавить ниже</option>
                        <option value="4">Заменить файл</option>
                        <option value="5">Создать новый файл</option>
                    </select>
                    <a href="#" class="remove remove_action"></a>
                </div>
                <div class="actionsBox"></div>
            </div>`);
        return false;
    })

    $(document).on("change", '.actionsFileSelect', function(){

        let milliseconds = new Date();
        let uniqueId = milliseconds.getTime();

        let fileId = $(this).closest(".fileMod").attr("data-fileId");
        let actionSelected = $(this).find("option:selected").val();

        if(actionSelected == '1'){

            $(this).parent().next().html(`<div class="actionBox_">
                <label for="">Найти:</label>
                <textarea name="`+fileId+`[search][]" id="code`+uniqueId+`" rows="1"></textarea>
            </div>
            <div class="actionBox_">
                <label for="">Заменить на:</label>
                <textarea name="`+fileId+`[act][]" id="code`+uniqueId+1+`" rows="1"></textarea>
            </div>`);

        } else if(actionSelected == '2'){

            $(this).parent().next().html(`<div class="actionBox_">
                <label for="">Найти:</label>
                <textarea name="`+fileId+`[search][]" id="code`+uniqueId+`" rows="1"></textarea>
            </div>
            <div class="actionBox_">
                <label for="">Добавить выше:</label>
                <textarea name="`+fileId+`[act][]" id="code`+uniqueId+1+`" rows="1"></textarea>
            </div>`);

        } else if(actionSelected == '3'){

            $(this).parent().next().html(`<div class="actionBox_">
                <label for="">Найти:</label>
                <textarea name="`+fileId+`[search][]" id="code`+uniqueId+`" rows="1"></textarea>
            </div>
            <div class="actionBox_">
                <label for="">Добавить ниже:</label>
                <textarea name="`+fileId+`[act][]" id="code`+uniqueId+1+`" rows="1"></textarea>
            </div>`);

        } else if(actionSelected == '4'){

            $(this).parent().next().html(`<div class="actionBox_">
                <label for="">Заменить на:</label>
                <textarea name="`+fileId+`[act][]" id="code`+uniqueId+`" rows="1"></textarea>
            </div>`);

        } else if(actionSelected == '5'){

            $(this).parent().next().html(`<div class="actionBox_">
                <label for="">Содержимое файла:</label>
                <textarea name="`+fileId+`[act][]" id="code`+uniqueId+`" rows="1"></textarea>
            </div>`);

        }

        CodeMirror.fromTextArea(document.getElementById("code"+uniqueId), php);

        if(actionSelected == '1' || actionSelected == '2' || actionSelected == '3')
            CodeMirror.fromTextArea(document.getElementById("code"+uniqueId+1), php);

        return false;
    })

    $(document).on("click", ".remove_file", function(){
        $(this).closest(".fileMod").remove();
        return false;
    })

    $(document).on("click", ".remove_action", function(){
        $(this).closest(".actionsFile").remove();
        return false;
    })
})