$(function(){

    function fieldType(){
        
        let fieldType = $("#fieldType option:selected").val();

        $(".fieldsSetts:not(.imageThumb)").slideUp(200);
        $('[data-type]').each(function(){
            let dataType = $(this).attr("data-type").split(",");
            if(dataType.indexOf(fieldType) + 1){
                $(this).delay(200).slideDown({
                    start: function(){
                        $(this).css('display', 'grid');
                    }
                });
            }
        })
    }

    fieldType();

    $(document).on("change", "#fieldType", function(){
        fieldType();
    })

    $(document).on("change", "#thumb", function(){
        if($(this).prop("checked")) $(".imageThumb").slideDown({
            start: function(){
                $(this).css('display', 'grid');
            }
        });
        else $(".imageThumb").slideUp();
    })

    // активация дезактивация поста
    $(document).on("change", ".status_field", function(){
        let tag = $(this).attr("data-tag");
        let statusField = $(this).prop("checked");
        $.ajaxSend($(this), {"ajax": "Fields", "tag": tag, "statusField": statusField});
    })

})