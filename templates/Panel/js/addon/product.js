$(function(){

    // перебор выбранных значений ранее
    $('[data-prop-sel]').each(function(){
        $(this).find('option[value="'+$(this).attr('data-prop-sel')+'"]').prop('selected', true);
    })


    // добавление поля
    function addField(element = null){

        let thisElement;
        if(element === null) thisElement = $("#propertiesAll option:selected");
        else thisElement = element;

        let propertySelected = thisElement.attr("data-property");

        if(element === null && !propertySelected){
            $.server_say({say: "Выберите свойство!", status: "error"});
            return false;
        }

        let propertySelectedTitle = thisElement.text();
        propertySelected = JSON.parse(propertySelected);

        if(element === null && $('[data-prop-id="'+propertySelected[0].id+'"]').length) $.server_say({say: "Свойство уже добавлено!", status: "error"});
        else{

            if(element != null && $('[data-prop-id="'+propertySelected[0].id+'"]').length) return;

            let optionsSelect = (propertySelected[0]["sep"] == '1') ? '<option value="sep" class="sep_field">ПРОИЗВОЛЬНОЕ ПОЛЕ</option>' : '';
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
                    <a href="#" class="remove_sub_property">-</a>
                </div>
                <div class="prop_subs"></div>
            </div>`);
        }
    }

    // выводим нужные свойства исходя из выбранных категорий
    function parseCategory(){

        let categorySelected = [];
        $("#categoryOptions option:selected").each(function(){
            categorySelected.push($(this).val());
        })

        // чистим то что не заполнено
        $(".prop").each(function(){

            let deleteElement = true;
            $(this).find("select option:selected, input").each(function(){
                if($(this).val() != ''){
                    deleteElement = false;
                    return false;
                }
            })

            if(deleteElement) $(this).remove();
        })

        // перебираем options in properties
        $("#propertiesAll option:not(:first)").each(function(){

            let thisElement = $(this);

            thisElement.addClass("dn");
            let categories = thisElement.attr("data-category");
            let display = thisElement.attr("data-display");

            if(categories == "" && display == '1'){ // если не заданы категории и включен вывод
                thisElement.removeClass("dn");
                addField(thisElement);
            }

            if(categories != ""){ // если категории заданы в option
                categories = categories.split(",");
                categorySelected.forEach(function(item) {
                    if(categories.includes(item)){
                        thisElement.removeClass("dn");
                        if(display == '1') addField(thisElement);
                        else{
                            // ???
                        }
                    }
                });
            }
        })
    }

    // показываем бренды исходя из выбранных категорий
    function parseBrands(){

        let categorySelected = [];
        $("#categoryOptions option:selected").each(function(){
            categorySelected.push($(this).val());
        })

        // перебираем options brands
        $("#productBrand option").each(function(){

            let thisElement = $(this);
            let categories = thisElement.attr("data-brand-categories");

            if(thisElement.val() != '' && categories != ""){ // если категории заданы в option
                thisElement.addClass("dn");
                categories = categories.split(",");
                categorySelected.forEach(function(item) {
                    if(categories.includes(item)){
                        thisElement.removeClass("dn");
                    }
                });
            }
        })
    }
    parseCategory();
    parseBrands();


    // выбор свойства
    $(document).on("click", "#addPropertiy", function(){
        addField();
        return false
    })
    
    // при выборе категории, выводим нужные свойства
    $(document).on("change", "#categoryOptions", function(){
        parseCategory();
        parseBrands();
    })

    // добавление свойства
    $(document).on("click", ".add_sub_property", function(){

        let propId = $(this).closest(".prop").attr("data-prop-id");
        let selectElement = $(this).parent().find(".property_name")[0].outerHTML;

        if(
            $(this).parent().find(".property_name")[0].nodeName != 'SELECT' &&
            $(this).parent().find(".callback_select").length
        ) selectElement += $(this).closest(".prop").find(".callback_select")[0].outerHTML;

        $(this).parent().after(`<div class="prop_sub">
            <div class="pr inFocus">
                `+selectElement+`
            </div>
            <input type="text" name="prop[`+propId+`][vendor][]" value="" placeholder="Артикул">
            <input type="number" name="prop[`+propId+`][price][]" min="0" step=".1" value="" placeholder="Цена">
            <input type="number" name="prop[`+propId+`][stock][]" min="0" step="1" value="" placeholder="Кол-во">
            <a href="#" class="add_sub_property">+</a>
            <a href="#" class="remove_sub_property">-</a>
        </div>`);
        $(".inFocus input").val("").attr("placeholder", "Введите значение").focus();
        $(".inFocus").removeClass("inFocus");

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
        let photoAlt = $(this).prev().attr('data-caption');
        $("#photoEditor").html(`<img src="`+photoSrc+`" alt="">
            <div id="editorOptions">
                <input type="hidden" name="photo[id]" value="`+photoId+`">
                <label for="">Описание изображения</label>
                <input type="text" name="photo[alt]" value="`+photoAlt+`" autocomplete="off">
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

    // произвольное поле
    $(document).on("change", ".property_name", function(){
        if($(this).find("option:selected").val() == 'sep'){
            let elementAttrName = $(this).attr("name");
            $(this).replaceWith('<input name="'+elementAttrName+'" class="property_name inFocus" placeholder="Введите значение"><span class="callback_select"></span>');
            $(".inFocus").focus().removeClass("inFocus");
        }
    })

    // произвольное поле CALLBACK
    $(document).on("click", ".callback_select", function(){
        let propId = $(this).closest(".prop").attr("data-prop-id");

        let propertySelected = $('#propertiesAll option[value="'+propId+'"]').attr("data-property");
        propertySelected = JSON.parse(propertySelected);

        let optionsSelect = (propertySelected[0]["sep"] == '1') ? '<option value="sep" class="sep_field">ПРОИЗВОЛЬНОЕ ПОЛЕ</option>' : '';
        for (let key in propertySelected) {
            optionsSelect += `<option value="`+propertySelected[key]["vid"]+`">`+propertySelected[key].val+`</option>`;
        }

        $(this).prev().replaceWith(`<select class="property_name" name="prop[`+propId+`][id][]">
            <option value="">-- не выбрано --</option>
            `+optionsSelect+`
        </select>`);
        $(this).remove();
    })

    // сортировка изображений товаров
    if($("#product_images").length){
        $("#product_images").sortable({
            placeholder: "ui-state-highlight",
            stop: function() {
                let newSortImages = [];
                $("#product_images .img_item").each(function(){
                    newSortImages.push($(this).attr('data-img-id'));
                })
                $.ajaxSend($(this), {"ajax": "ProductShop", "newSortImages": newSortImages});
            }
        });
        $("#product_images").disableSelection();
    }

})