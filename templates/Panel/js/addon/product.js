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

        console.log(propertySelected);
        console.log(propertySelectedTitle);

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

        console.log(selectElement);

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
        $(this).closest(".prop").remove();
        return false
    })

    // удаление элемента свойства
    $(document).on("click", ".remove_sub_property", function(){
        $(this).closest(".prop_sub").remove();
        return false
    })

    // изменение свойств
    /*$(document).on("click", "#properties_product", function(){
        $("#editProp").remove();
        $(this).append('<input type="hidden" name="editProp" id="editProp" value="1">');
    })*/

})