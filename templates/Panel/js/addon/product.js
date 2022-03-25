$(function(){

    // выбор свойства
    $(document).on("click", "#addPropertiy", function(){
        let propertySelected = $("#propertiesAll option:selected").attr("data-property");

        if(!propertySelected){
            $.server_say({say: "Выберите свойство!", status: "error"});
            return false;
        }

        let propertySelectedTitle = $("#propertiesAll option:selected").text();
        propertySelected = JSON.parse(propertySelected);

        console.log(propertySelected);
        console.log(propertySelectedTitle);

        if($('[data-prop-id="'+propertySelected[0].id+'"]').length) $.server_say({say: "Свойство уже добавлено!", status: "error"});
        else{

            let optionsSelect = '';
            for (let key in propertySelected) {
                optionsSelect += `<option value="`+propertySelected[key].id+`">`+propertySelected[key].val+`</option>`;
            }

            $("#properties_product").append(`<div class="prop" data-prop-id="`+propertySelected[0].id+`">
                <div class="prop_main" data-prop-id="`+propertySelected[0].id+`">
                    <div class="pr">
                        <label for="">`+propertySelectedTitle+`: <a href="#" class="del_property"></a></label>
                        <select class="property_name" name="">
                            <option value="">-- не выбрано --</option>
                            `+optionsSelect+`
                        </select>
                    </div>
                    <div>
                        <label for="">Артикул</label>
                        <input type="text" name="prop[vendor]" value="" placeholder="Артикул">
                    </div>
                    <div>
                        <label for="">Цена</label>
                        <input type="number" name="prop[price]" min="0" step=".1" value="" placeholder="Цена">
                    </div>
                    <div>
                        <label for="">Кол-во</label>
                        <input type="number" name="prop[stock]" min="0" step="1" value="" placeholder="Кол-во">
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

        let selectElement = $(this).closest(".prop").find(".property_name").html();

        $(this).closest(".prop").find(".prop_subs").append(`<div class="prop_sub">
            <div class="pr">
                <select class="property_name" name="">
                    `+selectElement+`
                </select>
            </div>
            <input type="text" name="prop[vendor]" value="" placeholder="Артикул">
            <input type="number" name="prop[price]" min="0" step=".1" value="" placeholder="Цена">
            <input type="number" name="prop[stock]" min="0" step="1" value="" placeholder="Кол-во">
            <a href="#" class="remove_sub_property">-</a>
        </div>`);

        return false
    })

    // удаление свойства
    $(document).on("click", ".del_property", function(){
        $(this).closest(".prop").remove();
        return false
    })

    // удаление элемента свойства
    $(document).on("click", ".remove_sub_property", function(){
        $(this).closest(".prop_sub").remove();
        return false
    })

})