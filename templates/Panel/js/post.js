$(function(){

    // при выборе категории, выводим нужные свойства
    $(document).on("change", "#categoryOptions", function(){
        //parseCategory();
    })

    // редактирование фото
    $(document).on("click", '.edit_image', function(){
        let photoId = $(this).attr('data-img-id');
        let photoSrc = $(this).parent(".img_item").find('img').attr('src');
        let photoAlt = $(this).prev().prev().attr('data-caption');
        if(photoAlt == undefined) photoAlt = '';
        $("#photoEditor").html(`<img src="`+photoSrc+`" alt="">
            <div id="editorOptions">
                <input type="hidden" name="photo[id]" value="`+photoId+`">
                <label for="">Описание изображения</label>
                <input type="text" name="photo[alt]" value="`+photoAlt+`" autocomplete="off">
            </div>`);
        return false
    })

    // активация дезактивация товара
    $(document).on("change", ".status_post", function(){
        let postId = $(this).attr("data-id");
        let statusPost = $(this).prop("checked");
        $.ajaxSend($(this), {"ajax": "Post", "postId": postId, "statusPost": statusPost});
    })

    // сортировка изображений товаров
    if($("#post_images").length){
        $("#post_images").sortable({
            placeholder: "ui-state-highlight",
            stop: function() {
                let newSortImages = [];
                $("#post_images .img_item").each(function(){
                    newSortImages.push($(this).attr('data-img-id'));
                })
                $.ajaxSend($(this), {"ajax": "Post", "newSortImages": newSortImages});
            }
        });
        $("#post_images").disableSelection();
    }

})