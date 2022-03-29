$(function(){

    // перебор выбранных значений ранее
    $('[data-prop-sel]').each(function(){
        $(this).find('option[value="'+$(this).attr('data-prop-sel')+'"]').prop('selected', true);
    })

    // выбор свойства
    $(document).on("click", "#addPropertiy", function(){
        let propertySelected = $("#propertiesAll option:selected").attr("data-property");

        if(!propertySelected){
            $.server_say({say: "Выберите свойство!", status: "error"});
            return false;
        }

        let propertySelectedTitle = $("#propertiesAll option:selected").text();
        propertySelected = JSON.parse(propertySelected);

        if($('[data-prop-id="'+propertySelected[0].id+'"]').length) $.server_say({say: "Свойство уже добавлено!", status: "error"});
        else{

            let optionsSelect = '';
            for (let key in propertySelected) {
                optionsSelect += `<option value="`+propertySelected[key]["vid"]+`">`+propertySelected[key].val+`</option>`;
            }

            $("#properties_product").append(`<div class="prop" data-prop-id="`+propertySelected[0].id+`">
                <div class="prop_main" data-prop-id="`+propertySelected[0].id+`">
                    <div class="pr">
                        <label for="">`+propertySelectedTitle+`: <a href="#" class="del_property"></a></label>
                        <select class="property_name" name="prop[`+propertySelected[0].id+`][id][]">
                            <option value="">-- не выбрано --</option>
                            `+optionsSelect+`
                        </select>
                    </div>
                    <div>
                        <label for="">Артикул</label>
                        <input type="text" name="prop[`+propertySelected[0].id+`][vendor][]" value="" placeholder="Артикул">
                    </div>
                    <div>
                        <label for="">Цена</label>
                        <input type="number" name="prop[`+propertySelected[0].id+`][price][]" min="0" step=".1" value="" placeholder="Цена">
                    </div>
                    <div>
                        <label for="">Кол-во</label>
                        <input type="number" name="prop[`+propertySelected[0].id+`][stock][]" min="0" step="1" value="" placeholder="Кол-во">
                    </div>
                    <a href="#" class="add_sub_property">+</a>
                </div>
                <div class="prop_subs"></div>
            </div>`);
        }

        return false
    })

    // добавление свойства
    $(document).on("click", ".add_sub_property", function(){

        let propId = $(this).closest(".prop_main").attr("data-prop-id");
        let selectElement = $(this).closest(".prop").find(".property_name")[0].outerHTML;

        $(this).closest(".prop").find(".prop_subs").append(`<div class="prop_sub">
            <div class="pr">
                `+selectElement+`
            </div>
            <input type="text" name="prop[`+propId+`][vendor][]" value="" placeholder="Артикул">
            <input type="number" name="prop[`+propId+`][price][]" min="0" step=".1" value="" placeholder="Цена">
            <input type="number" name="prop[`+propId+`][stock][]" min="0" step="1" value="" placeholder="Кол-во">
            <a href="#" class="remove_sub_property">-</a>
        </div>`);

        return false
    })

    // удаление свойства
    $(document).on("click", ".del_property", function(){
        
        let pp_id = [];
        $(this).closest(".prop").find(".pp_id").each(function(){
            pp_id.push($(this).val());
        })
        if(pp_id.length) $.ajaxSend($(this), {"ajax": "ProductShop", "pp_ids": pp_id});

        $(this).closest(".prop").remove();
        return false
    })

    // удаление элемента свойства
    $(document).on("click", '.remove_sub_property:not([data-a])', function(){
        $(this).closest(".prop_sub").remove();
        return false
    })

    // редактирование фото
    $(document).on("click", '.edit_image', function(){
        let photoId = $(this).attr('data-img-id');
        let photoSrc = $(this).parent(".img_item").find('img').attr('src');
        $("#photoEditor").html(`<img src="`+photoSrc+`" alt="">
            <div id="editorOptions">
                <input type="hidden" name="photo[id]" value="`+photoId+`">
                <label for="">Описание изображения</label>
                <input type="text" name="photo[alt]" value="" autocomplete="off">
            </div>`);
        return false
    })

    // активация дезактивация товара
    $(document).on("change", ".status_product", function(){
        let productId = $(this).attr("data-id");
        let statusProduct = $(this).prop("checked");
        $.ajaxSend($(this), {"ajax": "ProductShop", "productId": productId, "statusProduct": statusProduct});
    })

    // активация дезактивация категории
    $(document).on("change", ".status_category", function(){
        let categoryId = $(this).attr("data-id");
        let statusCategory = $(this).prop("checked");
        $.ajaxSend($(this), {"ajax": "CategoryShop", "categoryId": categoryId, "statusCategory": statusCategory});
    })

})