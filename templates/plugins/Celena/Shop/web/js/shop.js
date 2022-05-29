/**
 * @name функция запоминания настроек
 * @description если toggle задан, то переданное значение во втором параметре будет добавляться или удалять из массива
 * @param settingName
 * @param val
 * @param toggle
 */
function setConfig(settingName, val, toggle = false){
    let settings = getConfig()
    if(settings === null){
        settings = {}
        settings[settingName] = val
    } else{
        // если это массив, то делаем проверку (есть ли уже такое значение)
        if(toggle == false && Array.isArray(val) && val.length == 1){
            if(settings !== undefined && settings[settingName].indexOf( val[0] ) + 1){ // если значение в массиве найдено, удаляем его
                let key = settings[settingName].indexOf(val[0])
                settings[settingName].splice(key, 1)
            } else{
                if(settings[settingName].length >= 1) settings[settingName].push(val[0])
                else settings[settingName] = val
            }
            // если не требуется только добавлять значение в массив
        } else if(toggle == true && Array.isArray(val) && val.length == 1) settings[settingName].push(val[0])
        // если val не массив
        else settings[settingName] = val
    }
    localStorage.setItem("settings", JSON.stringify(settings))
}

// функция получения настроек
function getConfig(settingName){
    let settings = localStorage.getItem("settings")
    if(settings !== null){
        if(settingName == undefined) return JSON.parse(settings)
        else return JSON.parse(settings)[settingName]
    } else return null
}

$(function(){

    function unique(array){
        return array.filter(function(el, index, arr) {
            return index == arr.indexOf(el)
        })
    }

    function reintCart(){

        let cart = getCart()

        // если корзина не пустая
        if(cart !== null){

            let count_goods = 0 // кол-во товаров в корзине
            let total = 0 // сумма всех товаров
            let content_cart = '' // содержимое корзины
            let title_goods = '' // для input при оформлении заказа

            for (let key in cart) { // один товар

                count_goods = count_goods + parseInt(cart[key].count)
                let price = (parseFloat(cart[key].price) * parseInt(cart[key].count)).toFixed(2)
                total = total + parseFloat(price)


                // перебираем все характеристики
                let properties_count_result = []
                let propNumber = 1
                let tmp_price = 0
                let properties = '<ul class="properties_changed">'

                if(cart[key].properties != undefined){
                    cart[key].properties.forEach(function(item){ // перебираем свойства с каждой добавленной позиции

                        if(cart[key].properties.length > 1) properties += '<li class="prop_position">Позиция: '+propNumber+'</li>'

                        for(let j in item){

                            if(item[j].length){ // если более одного значения в свойстве

                                let tmpRepeatName, val_ = ''
                                item[j].forEach(function(itemI, i){
                                    if(tmpRepeatName == itemI.name){
                                        properties += ', ' + itemI.value
                                    } else{
                                        val_ = itemI.value
                                        properties += '<li><b>'+itemI.name+':</b> '+itemI.value
                                    }
                                    if(item[j] >= i && tmpRepeatName == itemI.name) properties += '</li>'
                                    tmpRepeatName = itemI.name
                                })

                            } else{

                                for(let k in item[j]){
                                    let propVal = []
                                    if(item[j][k].length == 1)
                                        propVal.push(item[j][k][0]['value'])
                                    else{
                                        for(let s in item[j][k]){
                                            propVal.push(item[j][k][s]['value'])
                                        }
                                    }
                                    properties += '<li><b>'+item[j][k][0]['name']+':</b> '+propVal.join(', ')+'</li>'
                                }
                            }
                        }
                        propNumber++;
                    })
                }
                properties += '</ul>'

                total += tmp_price // прибавляем к сумме сумму за свойства

                title_goods += cart[key].link+"|"+cart[key].title+"||"

                if(config.penny == ""){
                    price = Math.round(price)
                    cart[key].price = Math.round(cart[key].price)
                }

                content_cart += '<tr>\n' +
                    '    <td class="cart_img">\n' +
                    '        <a href="'+cart[key].link+'"><img src="'+cart[key].image+'" alt=""></a>\n' +
                    '    </td>\n' +
                    '    <td class="cart_title">\n' +
                    '        <a href="'+cart[key].link+'">'+cart[key].title+'</a><br>\n' +
                    '        <span class="cart_source_price">'+cart[key].price+' ₴</span>\n' +
                    '        <span class="open_properties" title="выбранные характеристики"></span>\n' +
                    properties +
                    '    </td>\n' +
                    '    <td>\n' +
                    '        <div class="counter_goods">\n' +
                    '            <a href="#" class="min">-</a>\n' +
                    '            <input type="text" data-product-id="'+cart[key].id+'" value="'+parseInt(cart[key].count)+'">\n' +
                    '            <a href="#" class="max">+</a>\n' +
                    '        </div>\n' +
                    '    </td>\n' +
                    '    <td class="cart_price">'+price+' ₴</td>\n' +
                    '    <td class="cart_actions">\n' +
                    '        <a href="#" data-product-id="'+cart[key].id+'" class="remove_goods">Удалить</a>\n' +
                    '    </td>\n' +
                    '</tr>'
            }

            if(config.penny == "") total = Math.round(total)
            else total = total.toFixed(2)

            let cart_body = `<h2 class="modal_header">Моя корзина</h2>
                <table>
                `+content_cart+`
                </table>
                <div class="fx ai_c">
                    <div class="fx ai_c">
                        <a href="/cart/" class="btn_order">Оформить заказ</a>
                        <a href="#" class="btn_clear">Очистить</a>
                    </div>
                    <p class="cart_total">Всего: <b>`+total+` `+config.currency+`</b></p>
                </div>
                <a href="#" class="close"></a>`

            $("#cart_modal, #cart_ordering").html(cart_body)
            $(".cart_order_form .cart_total").html(`Всего к оплате: <b>`+total+` `+config.currency+`</b>`)
            $("#total").val(total)
            //$("#productsJson").val(title_goods.slice(0, -2))
            $("#productsJson").val(JSON.stringify(cart))

            $("#cart").html('<p>В корзине: '+count_goods+' шт.</p>\n<p>на сумму: <span class="total">'+total+'</span> '+config.currency+'</p>')

        } else{

            $("#cart_modal, #cart_ordering").html('').fadeOut()
            $(".bg_0").fadeOut()
            $("#cart").html('<p>В корзине: пусто</p><p>&nbsp</p>')
        }
    }

    reintCart()

    /**
     * @name открытие характеристик в корзине
     */
    $(document).on("click", ".open_properties", function(){
        $(this).next().stop(true, true).slideToggle(300)
        return true
    })

    /**
     * @name добавление товара в корзину
     * @param goods
     */
    function addCart(goods){
        // goods - объект с новыми данными
        let cart = getCart()
        let ResultGoods = {}

        // если корзина не пуста
        if(cart != null){

            // если такой товар уже есть в корзине
            if(cart[goods.id] != undefined && cart[goods.id].id == goods.id){

                cart[goods.id].count++ // добавляем кол-во
                if(cart[goods.id].properties != undefined) cart[goods.id].properties.push(goods.properties)
                ResultGoods = cart

            // если такого товара еще нет в корзине
            } else{

                goods.count = 1
                cart[goods.id] = goods
                ResultGoods = cart
            }

            // добавление первого товара в корзину
        } else{

            goods.count = 1
            ResultGoods[goods.id] = goods
        }

        localStorage.setItem("cart", JSON.stringify(ResultGoods))
    }

    /**
     * @name получение товаров из корзины
     * @returns {*}
     */
    function getCart(){

        let cart = localStorage.getItem("cart")
        if(cart) return JSON.parse(cart)
        else return null
    }


    function recalculationGoods(id, action = "+", counter = null){

        let cart = getCart()
        let ResultGoods
        let counter_result

        if(counter != null){
            if(counter == "" || counter == "0" || isNaN(parseInt(counter))) counter_result = 1
            else counter_result = counter
            cart[id].count = counter_result
        } else{
            if(action == "+") cart[id].count++ // добавляем кол-во
            else if(cart[id].count > 1) cart[id].count-- // отнимаем кол-во
        }

        ResultGoods = cart
        localStorage.setItem("cart", JSON.stringify(ResultGoods))
        reintCart()

        // tmp
        if(counter != null || counter == "0" || isNaN(parseInt(counter))){
            let input = $('.counter_goods [data-product-id="'+id+'"]').focus()
            let strLength = input.val().length * 2
            input.focus()
            input[0].setSelectionRange(strLength, strLength)
            if(counter == "") input.val("")
        }
    }


    /**
     * @name удаление товара из корзины
     * @param id
     */
    function removeGoods(id){

        let cart = getCart()
        delete cart[id]
        localStorage.setItem("cart", JSON.stringify(cart))
        reintCart()
    }


    // добавление товара в корзину или покупка
    $(document).on("click", ".ks_buy, .ks_add_cart", function(e){

        let products = JSON.parse($(this).attr("data-product"))

        // проверяем какие характеристики выбраны
        let properties = {}
        $('[data-type-select="1"]').each(function(){
            let fName = $(this).prev().text().slice(0, -1)
            let fValue = $(this).find('option:selected').val()
            let fPid = $(this).find('option:selected').attr('data-p-id')
            let fStock = $(this).find('option:selected').attr('data-p-stock')
            let fPrice = $(this).find('option:selected').attr('data-p-sum')
            let fCalc = $(this).find('option:selected').attr('data-p-calc')
            if(fValue != ''){
                if(properties[fName] == undefined) properties[fName] = []
                properties[fName].push({
                    "pid": fPid,
                    "name": fName,
                    "value": fValue,
                    "price": fPrice,
                    "calc": fCalc,
                    "stock": (fStock != undefined && fStock != '') ? parseInt(fStock) : null
                })
            }
        })
        $('[data-type-select="2"] .active, [data-type-select="3"] .active').each(function(){
            let fName = $(this).parents(".nex_properties").find("label").text().slice(0, -1)
            let fPid = $(this).attr('data-p-id')
            let fValue = $(this).attr('data-p-val')
            let fStock = $(this).attr('data-p-stock')
            let fPrice = $(this).attr('data-p-sum')
            let fCalc = $(this).attr('data-p-calc')
            if(fValue != ''){
                if(properties[fName] == undefined) properties[fName] = []
                properties[fName].push({
                    "pid": fPid,
                    "name": fName,
                    "value": fValue,
                    "price": fPrice,
                    "calc": fCalc,
                    "stock": (fStock != undefined && fStock != '') ? parseInt(fStock) : null
                })
            }
        })

        if(JSON.stringify(properties) != '{}'){
            products["properties"] = [properties]
        }

        addCart(products) // добавляем товар в корзину
        reintCart()

        // полет в корзину
        $(this).append('<span class="added_goods">+1</span>').children().animate({
            top: "-70px",
            opacity: 0
        }, 800)

        // если это нажатие на кнопку купить
        if(e.target.className == "ks_add_cart") return false
    })

    // прибавляем кол-во
    $(document).on("click", ".counter_goods .max", function(){
        let goods_id = $(this).prev().attr("data-product-id")
        recalculationGoods(goods_id)
        return false
    })
    // отнимаем кол-во
    $(document).on("click", ".counter_goods .min", function(){
        let goods_id = $(this).next().attr("data-product-id")
        recalculationGoods(goods_id, "-")
        return false
    })
    // пересчет кол-во
    $(document).on("keyup", ".counter_goods input", function(){
        let goods_id = $(this).attr("data-product-id")
        let counter = $(this).val()
        recalculationGoods(goods_id, "-", counter)
    })
    // удалеие товара из корзины
    $(document).on("click", ".remove_goods", function(){
        let goods_id = $(this).attr("data-product-id")
        removeGoods(goods_id)
        return false
    })


    // оригинальная сумма
    let original_price = 0
    if($(".price").length > 0) original_price = parseFloat($(".price").text()).toFixed(2)
    if(config.penny == "") original_price = Math.round(original_price)


    /**
     * @name калькулятор характеристик
     * @param e
     */
    function recalculation(){

        let tmp_sum = 0
        let tmp_calc = 0
        let resultSum = original_price
        let original_price_ = 0
        let static_price_ = 0

        $(".nex_properties .ft_select").each(function(i, element){

            let el_name = $(element).context.localName

            // ============================
            // если это select
            // ============================
            if(el_name == "select"){

                tmp_sum = $(this).find('option:checked').attr("data-p-sum")
                tmp_calc = $(this).find('option:checked').attr("data-p-calc")

                if(tmp_sum != '' && tmp_calc != ''){

                    if(tmp_calc == 'new'){ // новая цена

                        resultSum = parseFloat(tmp_sum)

                    } else if(tmp_calc == '0'){ // минус цена

                        resultSum -= parseFloat(tmp_sum)

                    } else if(tmp_calc == '1'){ // плюс цена

                        resultSum += parseFloat(tmp_sum)

                    } else if(tmp_calc == '2'){ // уменьшение %

                        resultSum = original_price * parseInt(tmp_sum.replace('-', '').replace('%', '')) / 100
                        original_price_ = parseFloat(original_price) - parseFloat(resultSum)

                    } else{ // увеличение %

                        resultSum = original_price * parseInt(tmp_sum.replace('+', '').replace('%', '')) / 100
                        original_price_ = parseFloat(original_price) + parseFloat(resultSum)
                    }
                }


                // ============================
                // если это checkbox
                // ============================
            } else{

                $(this).find('.active').each(function(){

                    tmp_sum = $(this).attr("data-p-sum")
                    tmp_calc = $(this).attr("data-p-calc")

                    if(tmp_sum != '' && tmp_calc != ''){

                        if(tmp_calc == 'new'){ // новая цена

                            resultSum = parseFloat(tmp_sum)

                        } else if(tmp_calc == '0'){ // минус цена

                            resultSum -= parseFloat(tmp_sum)

                        } else if(tmp_calc == '1'){ // плюс цена

                            resultSum += parseFloat(tmp_sum)

                        } else if(tmp_calc == '2'){ // уменьшение %

                            resultSum = original_price * parseInt(tmp_sum.replace('-', '').replace('%', '')) / 100
                            original_price_ = parseFloat(original_price) - parseFloat(resultSum)

                        } else{ // увеличение %

                            resultSum = original_price * parseInt(tmp_sum.replace('+', '').replace('%', '')) / 100
                            original_price_ = parseFloat(original_price) + parseFloat(resultSum)
                        }
                    }
                })
            }

            if(static_price_ != 0) return false

        })

        /*if(original_price_ != 0) fSum = original_price_
        else fSum = parseFloat(original_price) + fSum

        if(static_price_ != 0) fSum = static_price_*/

        // меняем цену в кнопках
        let data_goods = JSON.parse($('[data-product]:first').attr('data-product'))
        data_goods.price = parseFloat(resultSum).toFixed(2)
        if(config.penny == "") data_goods.price = Math.round(data_goods.price)

        original_price_ = 0
        static_price_ = 0

        //console.log(data_goods)

        $('[data-product]').attr('data-product', JSON.stringify(data_goods))

        // прибавляем сумму характеристик к цене
        $('[data-price]').text(parseFloat(resultSum).toFixed(2) + " " + config.currency)
    }

    // подсчитываем доп сумму выбранных характеристик
    if($(".ft_select").length > 0){

        recalculation() // по уже выбранным

        // выбор характеристики SELECT
        $(document).on("change", ".ft_select", function(e){
            recalculation()
            return false
        })

        // выбор характеристики
        $(document).on("click", ".ft_select li", function(e){

            let type = $(this).parent().attr('data-type-select') // 2 - один выбор, 3 - множественный выбор

            // делаем активным или неактивным выбранную характеристику
            if($(this).hasClass("active")) $(this).removeClass("active")
            else{
                // если тип (2), отключаем остальные выбранные пункты
                if(type == 2) $(this).parents(".ft_select").find(".active").removeClass("active")
                $(this).addClass("active")
            }

            recalculation()

            return false
        })
    }



    //clearCart()
    function clearCart(){
        localStorage.setItem("cart", "")
        reintCart()
    }


    // любая отправка формы методом AJAX
    $(document).on("click", ".btn_clear", function(){
        clearCart()
        return false
    })

})