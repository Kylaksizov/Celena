/**
 * @name функция запоминания настроек
 * @description если toggle задан, то переданное значение во втором парметре будет добавляться или удалять из массима
 * @param settingName
 * @param val
 * @param toggle
 */
function setConfig(settingName, val, toggle = false){
    let settings = getConfig();
    if(settings === null){
        settings = {};
        settings[settingName] = val;
    } else{
        // если это массив, то делаем проверку (есть ли уже такое значение)
        if(toggle == false && Array.isArray(val) && val.length == 1){
            if(settings !== undefined && settings[settingName].indexOf( val[0] ) + 1){ // если значение в массиве найдено, удаляем его
                let key = settings[settingName].indexOf(val[0]);
                settings[settingName].splice(key, 1);
            } else{
                if(settings[settingName].length >= 1) settings[settingName].push(val[0]);
                else settings[settingName] = val;
            }
            // если не требуется только добавлять значение в массив
        } else if(toggle == true && Array.isArray(val) && val.length == 1) settings[settingName].push(val[0]);
        // если val не массив
        else settings[settingName] = val;
    }
    localStorage.setItem("settings", JSON.stringify(settings));
}

// функция получения настроек
function getConfig(settingName){
    let settings = localStorage.getItem("settings");
    if(settings !== null){
        if(settingName == undefined) return JSON.parse(settings);
        else return JSON.parse(settings)[settingName];
    } else return null;
}

/*=== Alert ===*/
// https://www.jqueryscript.net/demo/Mobile-friendly-Dialog-Toast-Plugin-With-jQuery-alert-js/
!function ($) {
    $._isalert=0,
        $.alert=function(){
            if(arguments.length){
                $._isalert=1;
                return $.confirm.apply($,arguments);
            }
        },
        $.confirm=function(){
            var args=arguments;
            if(args.length){
                var d =$('<div class="alert_overlay esc"></div><div class="alert_msg esc"><div class="alert_content">'+args[0]+'</div><div class="alert_buttons"><button class="alert_btn alert_btn_ok">Ok</button><button class="alert_btn alert_btn_cancel">Отмена</button></div></div>'),
                    fn=args[1],
                    flag=1,
                    _click = function(e){
                        typeof fn=='function'?(fn.call(d,e.data.r)!=!1&&d.remove()):d.remove();
                    };
                $._isalert&&d.find('.alert_btn_cancel').hide();
                d.on('contextmenu',!1)
                    .on('click','.alert_btn_ok',{r:!0},_click)
                    .on('click','.alert_btn_cancel',{r:!1},_click)
                    .appendTo('body');
            }
            $._isalert=0;
        },
        $.prompt=function(){
            var args=arguments;
            if(args.length){
                var d =$('<div class="alert_overlay esc"></div><div class="alert_msg esc"><div class="alert_content">'+args[0]+'<br><input type="text" id="prompt_js"></div><div class="alert_buttons"><button class="alert_btn alert_btn_ok">Ок</button><button class="alert_btn alert_btn_cancel">Отмена</button></div></div>'),
                    fn=args[1],
                    flag=1,
                    _click = function(e){
                        var prompt_js = false;
                        if(e.data.r !== false && $("#prompt_js").val().length > 0) prompt_js = $("#prompt_js").val();
                        typeof fn=='function'?(fn.call(d,e.data.r = prompt_js)!=!1&&d.remove()):d.remove();
                    };
                $._isalert&&d.find('.alert_btn_cancel').hide();
                d.on('contextmenu',!1)
                    .on('click','.alert_btn_ok',{r:!0},_click)
                    .on('click','.alert_btn_cancel',{r:!1},_click)
                    .appendTo('body');
                $("#prompt_js").focus();
            }
            $._isalert=0;
        },
        $.tips=function(m){
            $('.alert_tips').remove();
            $('<div class="alert_tips"><div>'+m+'</div></div>').appendTo('body').one('webkitAnimationEnd animationEnd',function(){$(this).remove()})
        }
}($);

$(function(){

    let config = {};

    function unique(array){
        return array.filter(function(el, index, arr) {
            return index == arr.indexOf(el);
        });
    }

    function reintCart(){

        let cart = getCart();

        // если корзина не пустая
        if(cart !== null){

            let count_goods = 0; // кол-во товаров в корзине
            let total = 0; // сумма всех товаров
            let content_cart = ''; // содержимое корзины
            let title_goods = ''; // для input при оформлении заказа

            for (let key in cart) { // один товар

                count_goods = count_goods + parseInt(cart[key].count);
                let price = (parseFloat(cart[key].price) * parseInt(cart[key].count)).toFixed(2);
                total = total + parseFloat(price);


                // перебираем все характеристики
                let features_result = [];
                let features_count_result = [];
                let tmp_price = 0;
                for(let j in cart[key].features){

                    cart[key].features[j].forEach(function(item, i){

                        if(features_result[j] != undefined){
                            features_result[j].push(item[0]);
                            //console.log(item[0] +" "+ parseInt(features_count_result[item[0]]));
                            //if(item[0].indexOf(features_result[j]) + 1)
                            if(isNaN(features_count_result[item[0]])) features_count_result[item[0]] = 1;
                            else features_count_result[item[0]] = parseInt(features_count_result[item[0]]) + 1;
                        }
                        else{
                            features_result[j] = [item[0]];
                            features_count_result[item[0]] = 1;
                        }
                        tmp_price += parseFloat(item[1]);
                    })
                }
                //total += tmp_price; // прибовляем к сумме сумму за характеристики

//console.log(features_result);

                let features = '<ul class="features_changed">';
                for(let key in features_result){
                    //console.log(key);
                    features += '<li><b>'+key+':</b> '+features_result[key]+'</li>';
                }
                features += '</ul>';

                title_goods += cart[key].link+"|"+cart[key].title+"||";

                if(config.penny == ""){
                    price = Math.round(price);
                    cart[key].price = Math.round(cart[key].price);
                }

                content_cart += '<tr>\n' +
                    '    <td class="cart_img">\n' +
                    '        <a href="'+cart[key].link+'"><img src="'+cart[key].image+'" alt=""></a>\n' +
                    '    </td>\n' +
                    '    <td class="cart_title">\n' +
                    '        <a href="'+cart[key].link+'">'+cart[key].title+'</a><br>\n' +
                    '        <span class="cart_source_price">'+cart[key].price+' ₴</span>\n' +
                    '        <span class="open_features" title="выбранные характеристики"></span>\n' +
                    features +
                    '    </td>\n' +
                    '    <td>\n' +
                    '        <div class="counter_goods">\n' +
                    '            <a href="#" class="min">-</a>\n' +
                    '            <input type="text" data-goods-id="'+cart[key].id+'" value="'+parseInt(cart[key].count)+'">\n' +
                    '            <a href="#" class="max">+</a>\n' +
                    '        </div>\n' +
                    '    </td>\n' +
                    '    <td class="cart_price">'+price+' ₴</td>\n' +
                    '    <td class="cart_actions">\n' +
                    '        <a href="#" data-goods-id="'+cart[key].id+'" class="remove_goods">Удалить</a>\n' +
                    '    </td>\n' +
                    '</tr>';
            }

            if(config.penny == "") total = Math.round(total);
            else total = total.toFixed(2);

            let cart_body = '<h2 class="modal_header">Моя корзина</h2>\n' +
                '<table>\n' +
                content_cart +
                '</table>\n' +
                '<div class="flex ai_c">\n' +
                '    <a href="/cart.html" class="btn_order">Оформить заказ</a>\n' +
                '    <p class="cart_total">Всего: <b>'+total+' ₴</b></p>\n' +
                '</div>\n' +
                '<a href="#" class="close"></a>';

            $("#cart_modal, #cart_ordering").html(cart_body);
            $(".cart_order_form .cart_total").html('Всего к оплате: <b>'+total+' ₴</b>');
            $("#total").val(total);
            $("#title_goods").val(title_goods);

            $("#cart").html('<p>В корзине: '+count_goods+' шт.</p>\n<p>на сумму: <span class="total">'+total+'</span> ₴</p>');

        }
    }

    reintCart();

    /**
     * @name открытие характеристик в корзине
     */
    $(document).on("click", ".open_features", function(){
        $(this).next().stop(true, true).slideToggle(300);
        return true;
    })

    /**
     * @name добавление товара в корзину
     * @param goods
     */
    function addCart(goods){
        // goods - объект с новыми данными
        let cart = getCart();
        let ResultGoods = {};

        // если корзина не пуста
        if(cart != null){

            // если такой товар уже есть в корзине
            if(cart["id"+goods.id] != undefined && cart["id"+goods.id].id == goods.id){

                cart["id"+goods.id].count++; // добавляем кол-во



                for(let key in goods.features){ // перебираем пришедшие данные
                    goods.features[key].forEach(function(item, i, arr) { // перебираем каждый массив
                        // item[0]                   - значение (существующий)
                        // item[1]                   - цена (существующий)
                        // item[2]                   - кол-во (существующий)
                        // goods.features[key][0][0] - значение (новое)
                        // goods.features[key][0][1] - цена (новое)
                        // goods.features[key][0][2] - кол-во (новое)
                        //console.log(item[i]);
                        //console.log(item.indexOf(goods.features[key][0][0]) + 1);


                        // TODO тут возникает глюк, если в товар добавили характеристику
                        if(cart["id"+goods.id].features[key][i].indexOf(item[i]) + 1){
                            cart["id"+goods.id].features[key][i][2] = cart["id"+goods.id].features[key][i][2] + 1;
                            //console.log(cart["id"+goods.id].features[key][i][2]);
                        } else{
                            cart["id"+goods.id].features[key].push(goods.features[key][i]);
                        }
                    });
                }

                //console.log(cart);
                //console.log(goods);
                //return false;

                ResultGoods = cart;

                // если такого товара еще нет в корзине
            } else{

                goods.count = 1;
                cart["id"+goods.id] = goods;
                ResultGoods = cart;
            }

            // добавление первого товара в корзину
        } else{

            goods.count = 1;
            ResultGoods["id"+goods.id] = goods;
        }

        localStorage.setItem("cart", JSON.stringify(ResultGoods));
    }

    /**
     * @name получение товаров из корзины
     * @returns {*}
     */
    function getCart(){

        let cart = localStorage.getItem("cart");
        if(cart) return JSON.parse(cart);
        else return null;
    }


    function recalculationGoods(id, action = "+", counter = null){

        let cart = getCart();
        let ResultGoods;
        let counter_result;

        if(counter != null){
            if(counter == "" || counter == "0" || isNaN(parseInt(counter))) counter_result = 1;
            else counter_result = counter;
            cart["id"+id].count = counter_result;
        } else{
            if(action == "+") cart["id"+id].count++; // добавляем кол-во
            else if(cart["id"+id].count > 1) cart["id"+id].count--; // отнимаем кол-во
        }

        ResultGoods = cart;
        localStorage.setItem("cart", JSON.stringify(ResultGoods));
        reintCart();

        // tmp
        if(counter != null || counter == "0" || isNaN(parseInt(counter))){
            let input = $('.counter_goods [data-goods-id="'+id+'"]').focus();
            let strLength = input.val().length * 2;
            input.focus();
            input[0].setSelectionRange(strLength, strLength);
            if(counter == "") input.val("");
        }
    }


    /**
     * @name удаление товара из корзины
     * @param id
     */
    function removeGoods(id){

        let cart = getCart();
        delete cart["id"+id];
        localStorage.setItem("cart", JSON.stringify(cart));
        reintCart();
    }


    // добавление товара в корзину или покупка
    $(document).on("click", ".ks_buy, .ks_add_cart", function(e){

        let goods = JSON.parse($(this).attr("data-goods"));

        // проверяем какие характеристики выбраны
        let features = {};
        $('[data-type-select="1"]').each(function(){
            let fName = $(this).prev().text().slice(0, -1);
            let fValue = $(this).find('option:selected').text();
            //let fSum = $(this).find('option:selected').attr("data-sum");
            features[fName] = [[fValue]];
        })
        $('[data-type-select="2"] .active, [data-type-select="3"] .active').each(function(){
            let fName = $(this).parents(".features").find("label").text().slice(0, -1);
            let fValue = $(this).text();
            //let fSum = $(this).attr("data-sum");
            features[fName] = [[fValue]];
        })
        if(JSON.stringify(features) != '{}'){
            goods["features"] = features;
        }

        addCart(goods); // добавляем товар в корзину
        reintCart();

        // полет в корзину
        $(this).append('<span class="added_goods">+1</span>').children().animate({
            top: "-70px",
            opacity: 0
        }, 800);

        // если это нажатие на кнопку купить
        if(e.target.className == "ks_add_cart") return false;
    })

    // прибавляем кол-во
    $(document).on("click", ".counter_goods .max", function(){
        let goods_id = $(this).prev().attr("data-goods-id");
        recalculationGoods(goods_id);
        return false;
    })
    // отнимаем кол-во
    $(document).on("click", ".counter_goods .min", function(){
        let goods_id = $(this).next().attr("data-goods-id");
        recalculationGoods(goods_id, "-");
        return false;
    })
    // пересчет кол-во
    $(document).on("keyup", ".counter_goods input", function(){
        let goods_id = $(this).attr("data-goods-id");
        let counter = $(this).val();
        recalculationGoods(goods_id, "-", counter);
    })
    // удалеие товара из корзины
    $(document).on("click", ".remove_goods", function(){
        let goods_id = $(this).attr("data-goods-id");
        removeGoods(goods_id);
        return false;
    })


    // оригинальная сумма
    let original_price = 0;
    if($(".price").length > 0) original_price = parseFloat($(".price").text()).toFixed(2);
    if(config.penny == "") original_price = Math.round(original_price);


    /**
     * @name калькулятор характеристик
     * @param e
     */
    function recalculation(e, el = false){


        let tmp_sum = 0;
        let fSum = 0;
        let original_price_ = 0;
        let static_price_ = 0;
        let fSumPercent = 0;
        let fSumArray = [];

        $(".features .ft_select").each(function(i, element){

            let el_name = $(element).context.localName;

            // ============================
            // если это select
            // ============================
            if(el_name == "select"){

                tmp_sum = $(this).find('option:checked').attr("data-sum");

                if(tmp_sum != undefined){
                    if(tmp_sum.indexOf("%") + 1 && tmp_sum.indexOf("-") + 1){
                        // уменьшение
                        fSum = original_price * parseInt(tmp_sum.replace('-', '').replace('%', '')) / 100;
                        original_price_ = parseFloat(original_price) - parseFloat(fSum);

                    } else if(tmp_sum.indexOf("%") + 1 && tmp_sum.indexOf("+") + 1){
                        // увеличение
                        fSum = original_price * parseInt(tmp_sum.replace('+', '').replace('%', '')) / 100;
                        original_price_ = parseFloat(original_price) + parseFloat(fSum);

                    } else if(tmp_sum.indexOf("+") + 1){

                        fSum += parseFloat(tmp_sum.slice(1));

                    } else if(tmp_sum.indexOf("-") + 1){

                        fSum -= parseFloat(tmp_sum.slice(1));

                    } else{

                        original_price_ = parseFloat(tmp_sum);
                        static_price_ = parseFloat(tmp_sum);
                    }
                }


                // ============================
                // если это checkbox
                // ============================
            } else{

                $(this).find('.active').each(function(){

                    tmp_sum = $(this).attr("data-sum");

                    if(tmp_sum != undefined){
                        if(tmp_sum.indexOf("%") + 1 && tmp_sum.indexOf("-") + 1){
                            // уменьшение
                            fSum = original_price * parseInt(tmp_sum.replace('-', '').replace('%', '')) / 100;
                            original_price_ = parseFloat(original_price) - parseFloat(fSum);

                        } else if(tmp_sum.indexOf("%") + 1 && tmp_sum.indexOf("+") + 1){
                            // увеличение
                            fSum = original_price * parseInt(tmp_sum.replace('+', '').replace('%', '')) / 100;
                            original_price_ = parseFloat(original_price) + parseFloat(fSum);

                        } else if(tmp_sum.indexOf("+") + 1){

                            fSum += parseFloat(tmp_sum.slice(1));

                        } else if(tmp_sum.indexOf("-") + 1){

                            fSum -= parseFloat(tmp_sum.slice(1));

                        } else{

                            original_price_ = parseFloat(tmp_sum);
                            static_price_ = parseFloat(tmp_sum);
                        }
                    }
                })
            }

            if(static_price_ != 0) return false;

        })

        if(original_price_ != 0) fSum = original_price_;
        else fSum = parseFloat(original_price) + fSum;

        if(static_price_ != 0) fSum = static_price_;

        console.log(fSum);

        // меняем цену в кнопках
        let data_goods = JSON.parse($('[data-goods]:first').attr('data-goods'));
        data_goods.price = parseFloat(fSum).toFixed(2);
        if(config.penny == "") data_goods.price = Math.round(data_goods.price);

        original_price_ = 0;
        static_price_ = 0;

        //console.log(data_goods);

        $('[data-goods]').attr('data-goods', JSON.stringify(data_goods));

        // прибавляем сумму характеристик к цене
        if(el != false){
            el.parents(".one_goods").find(".price").text(parseFloat(fSum).toFixed(2) + " " + config.currency);
        }
    }

    // подсчитываем доп сумму выбранных характеристик
    if($(".ft_select").length > 0){

        //recalculation(1); // по уже выбранным

        // выбор характеристики SELECT
        $(document).on("change", ".ft_select", function(e){
            recalculation(e, $(this));
            return false;
        })

        // выбор характеристики
        $(document).on("click", ".ft_select li", function(e){

            let type = $(this).parent().attr('data-type-select'); // 2 - один выбор, 3 - множественный выбор

            // делаем активным или неактивным выбранную характеристику
            if($(this).hasClass("active")) $(this).removeClass("active");
            else{
                // если тип (2), отключаем остальные выбранные пункты
                if(type == 2) $(this).parents(".ft_select").find(".active").removeClass("active");
                $(this).addClass("active");
            }

            recalculation(e, $(this));

            return false;
        })
    }



    //clearCart();
    function clearCart(){
        localStorage.setItem("cart", "");
    }


    // любая отправка формы методом AJAX
    $(document).on("click", ".ajax", function(){

        let this_ = $(this);
        let data = $(this).parents("form").serialize();

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: data,
            dataType: 'text',
            success: function(data){
                if(data == "reload") window.location.reload();
                else{
                    let response = data.split("::");

                    let class_name = ' success';
                    let answer = data;
                    if(response[1] != undefined){
                        class_name = ' '+response[0];
                        answer = response[1];
                    }
                    if(this_.parent().find('.answer').length > 0) this_.parent().find('.answer').remove();
                    this_.before('<span class="answer'+class_name+'">'+answer+'</span>');
                }
            },
            error: function(data){
                console.log(data);
            }
        });
        return false;
    })

})