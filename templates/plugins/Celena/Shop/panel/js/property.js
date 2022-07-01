$(function(){

    function addField(el){
        let timestamp = new Date().getTime();
        el.closest(".p_val").after(`<div class="p_val">
            <input type="text" name="val[]" value="">
            <a href="#" class="add_val">+</a>
            <a href="#" class="remove_val">-</a>
            <div class="is_def" title="Сделать по умолчанию">
                <input type="radio" name="def" id="is_def_`+timestamp+`">
                <label for="is_def_`+timestamp+`"></label>
            </div>
        </div>`);
        $(".p_val:last input:first").focus();
    }

    // добавление значения
    $(document).on("click", ".add_val", function(){
        addField($(this));
        return false
    })

    // добавление и удаление поля по нажатию Enter
    $(document).on("keydown", '[name="val[]"]', function(event){
        if(event.keyCode == 13){ // добавление
            event.preventDefault();
            event.stopPropagation();
            addField($(this));
            return false
        }
        if(event.keyCode == 8){ // удаление
            console.log($(this).val().length);
            if($(".p_val").length > 1 && $(this).val().length == 0){
                event.preventDefault();
                event.stopPropagation();
                $(this).closest(".p_val").remove();
                return false
            }
        }
    });

    // изменение поля
    $(document).on("keyup", '.p_val [name="val[]"]', function(event){
        let valText = $(this).val();
        $(this).closest(".p_val").find('[name="def"]').val(valText);
    });

    // удаление значения
    $(document).on("click", ".remove_val", function(){
        if($(".p_val").length > 1) $(this).closest(".p_val").remove();
        else $(this).closest(".p_val").find("input").val("");
        return false
    })
})