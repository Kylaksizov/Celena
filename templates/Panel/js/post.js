$(function(){


    // поиск совпадений в двух массивах
    let intersect = function(arr1, arr2) {
        return arr1.filter(function(n) {
            return arr2.indexOf(n) !== -1;
        });
    };
    
    function buildFields(){

        let categoryOptions = [];
        $("#categoryOptions option:selected").each(function(){
            categoryOptions.push($(this).val());
        })

        $('#fields [data-category]').slideUp(200);
        
        $('#fields [data-category]').each(function(){
            
            let fieldCategories = $(this).attr("data-category").split(",");
            if(intersect(categoryOptions, fieldCategories).length){
                $(this).delay(200).slideDown({
                    start: function(){
                        $(this).css('display', 'grid');
                    }
                });
            }
        })
    }
    buildFields();


    // при выборе категории, выводим нужные доп поля
    /*$(document).on("select2:select", "#categoryOptions", function(){
        buildFields();
    })*/
    $('#categoryOptions').on('change', function (e) {
        buildFields();
    });

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

    // активация дезактивация поста
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