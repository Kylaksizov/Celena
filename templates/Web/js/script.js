$(function(){

    // открытие модального окна
    $(document).on("click", ".open_modal", function(){
        let modal_id = $(this).attr("href");
        if(modal_id != undefined){
            $('body').append($(modal_id));
            $(modal_id).stop(true, true).fadeOut(300)
            $(".bg_0, " + modal_id).stop(true, true).fadeIn(300).addClass('opened_modal');
        }
        return false;
    })
    $(document).on("click", ".bg_0, .close", function(){
        $(".modal, .modal_big").removeClass('open_modal').addClass('closes_modal');
        $(".bg_0, .modal, .modal_big, .alert_msg, .alert_overlay").fadeOut(300, function(){
            $(".modal, .modal_big").removeClass('closes_modal');
        });
        return false;
    })

    // Escape
    $(document).on("keydown", function(e){
        if(e.which === 27){
            $(".esc").fadeOut(300);
        }
    })
    $(document).mouseup( function(e){
        let div = $(".esc");
        if (!div.is(e.target) && div.has(e.target).length === 0)
            div.fadeOut(300);
    });

})