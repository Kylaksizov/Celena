$(function(){

    function addField(el){
        el.closest(".p_val").after(`<div class="p_val">
            <input type="text" name="val[]" value="">
            <a href="#" class="add_val">+</a>
            <a href="#" class="remove_val">-</a>
        </div>`);
        $(".p_val:last input").focus();
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
                $(this).closest(".p_val").prev().find('[name="val[]"]').focus();
                $(this).closest(".p_val").remove();
                return false
            }
        }
    });

    // удаление значения
    $(document).on("click", ".remove_val", function(){
        if($(".p_val").length > 1) $(this).closest(".p_val").remove();
        return false
    })
})