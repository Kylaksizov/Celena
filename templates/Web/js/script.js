$(function(){

    // TODO исправить правильный показ цены, если есть скидка (при старте странице)

    let timer = null;

    // получение конкретного GET параметра
    function $_GET(key) {
        let s = window.location.search;
        s = s.match(new RegExp(key + '=([^&=]+)'));
        return s ? s[1] : false;
    }
    // получение всех GET параметров
    function $GET() {
        let result = {};
        let s = decodeURI(window.location.search);
        if(s.indexOf("?") + 1){
            s = s.replace("?", "").split("&");
            s.forEach(function(item){
                item = item.split("=");
                result[item[0]] = item[1];
            })
        }
        return result;
    }

    $("#slider").owlCarousel({
        items: 1,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        loop: true,
        margin: 20,
        nav: true,
    })

    let owl1 = $('#goods_carousel');
    owl1.owlCarousel({
        items: 1,
        loop: true,
        margin: 10,
        nav: true,
        navText:['', '']
    })

    let owl2 = $('#goods_carousel2');
    owl2.owlCarousel({
        items: 5,
        loop: true,
        margin: 10,
        nav: true,
        responsive:{
            0:{items:0},
            590:{items:6},
            769:{items:4},
            870:{items:5},
            978:{items:4},
            1170:{items:5}
        }
    })

    $(document).on("click", "#goods_carousel .owl-prev", function() {
        owl2.trigger('prev.owl.carousel')
    })
    $(document).on("click", "#goods_carousel .owl-next", function() {
        owl2.trigger('next.owl.carousel')
    })

    $("#goods_see").owlCarousel({
        items: 4,
        loop: false,
        margin: 10,
        nav: true,
        responsive:{
            0:{items:1},
            550:{items:2},
            1350:{items:2},
            1400:{items:3},
            1820:{items:4}
        }
    })

    // рейтинг http://auxiliary.github.io/rater/
    $(".rating").rate({
        max_value: 5,
        step_size: 1,
        // readonly: false,
        // change_once: true, // выставление рейтинга один раз
        // ajax_method: 'POST',
        // url: window.location.href,
        // additional_data: {}, // дополнительные данные для передачи на сервер
    });
    $("#add_review .rating").on("change", function(ev, data){
        $('[name="rate"]').val(data.to);
    });

    // клик по картинке в слайдере
    $(document).on("click", "#goods_carousel a", function(){
        let src = $(this).find("img").attr("src");
        $('[data-fancybox="gallery"]').attr("href", src).find("img").attr("src", src);
        return false;
    })

    $(".order_phone").mask("+38 (099) 999-99-99");

    // открытие модального окна
    $(document).on("click", ".open_modal", function(){
        let modal_id = $(this).attr("href");
        $(".modal").stop(true, true).fadeOut(300);
        $(".bg_0, " + modal_id).stop(true, true).fadeIn(300);
        return false;
    })
    $(document).on("click", ".bg_0, .close", function(){
        $(".bg_0, .modal").stop(true, true).fadeOut(300);
        return false;
    })
    $(document).on("click", "#open_filter, .close_filter", function(){
        $("#filter_goods").stop(true, true).fadeToggle(300);
        return false;
    })
    $(document).on("click", "#media_menu", function(){
        $(".nav_top").stop(true, true).slideToggle(300);
        $(".contacts").stop(true, true).slideDown(300);
        return false;
    })

    if($(".product h1").length > 0 && $(window).width() <= 768){
        $(".product_pictures").before($(".product h1")[0].outerHTML);
        $(".product_info h1").remove();
    }

    let is_search = false;
    $(document).on("click", "#search_go", function(){
        if(is_search === false || $('#search [name="str"]').val() == ""){
            $('#search [name="str"]').show().focus();
            is_search = true;
            return false;
        }
    })

    // перемещение меню
    function moving_menu(){
        let w_size = $(window).width();
        let header_top = $("#header_top").html();

        if(w_size <= 610){
            $("#menu > p:first").after(header_top + '<li><hr></li>');
        }
    }

    moving_menu();

    $(document).on("click", "#menu_media a", function(){
        $(".bg_0, .site_bar").stop(true, true).fadeIn(300);
        return false;
    })



    /**
     * @name FILTER
     */
    if($("#filter_goods").length > 0){

        // перебираем GET и чекаем чекбоксы
        let GET = $GET();
        for (let key in GET){
            if(key != "min_price" && key != "max_price"){
                let param = GET[key].split(",");
                param.forEach(function(item){
                    $('#filter_goods [data-extraname="'+key+'"] .extra_val[value="'+item.replace("+", " ")+'"]').prop("checked", true);
                })
            }
        }

        function loadingShow(){
            $(".bg_00, .la-fire, .load-bar").remove(); // удаляем предыдущий loader
            $("#filter_goods .box_content").append('<div class="bg_00"></div><div class="la-fire"><div></div><div></div><div></div></div>');
            $(".all_goods").append('<div class="bg_00"><div class="load-bar"><div class="bar"></div><div class="bar"></div><div class="bar"></div></div>');
            $(".bg_00, .la-fire, .load-bar").stop(true, true).fadeIn(1000);
        }
        function loadingHide(){
            $(".bg_00").stop(false, true).fadeOut(700);
            $(".la-fire, .load-bar").stop(false, true).fadeOut(700);
        }

        // считываем все выбранные фильтры
        function extraEach(){

            let result = "";
            let obj = {};
            let url = window.location.href.replace(/\/page-[0-9]+\//g, "/").split("?")[0];

            $('.extra_val:checked').each(function(){
                let extra_name = $(this).parent().attr("data-extraname");
                if(!obj[extra_name]) obj[extra_name] = [];
                obj[extra_name].push($(this).val());
            })

            // создаем строку GET
            if(JSON.stringify(obj) != "{}"){

                for (let key in obj) {
                    let tmp_result = '';
                    obj[key].forEach(function(item) {
                        tmp_result += item.replace(" ", "+") + ",";
                    });
                    result += key + '=' + tmp_result.slice(0, -1) + "&";
                }
            }

            let filter_price = $("#filter_price").slider("option", "values");
            if(filter_price[0] == 0) filter_price[0] = 1;
            let prices = "min_price=" + filter_price[0] + "&max_price=" + filter_price[1];

            if(result) result = "&" + result.slice(0, -1);
            let result_url = url + "?" + prices + result;

            history.pushState(null, null, result_url);
            return prices + result;
        }

        function getGoodsByFilter(){

            loadingShow();

            let extraEachs = extraEach();
            let url = window.location.href.replace(/\/page-[0-9]+\//g, "/").split("?")[0] + "?" + extraEachs;

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "method": "get_goods_by_filter",
                    "params": extraEachs
                },
                dataType: 'text',
                success: function(data){
                    $(".all_goods").html(data);
                    loadingHide();
                    $(".rating").rate({
                        max_value: 5,
                        step_size: 1,
                    });
                }, error: function(data){
                    console.log(data);
                }
            });
        }

        // при выборе чекбоксов
        $(document).on("change", ".extra_val", function(){
            getGoodsByFilter();
        })

        let min_price = $("#min_price").val();
        let max_price = $("#max_price").val();
        if($_GET("min_price")){
            min_price = $_GET("min_price");
            $("#min_price").val(min_price);
        }
        if($_GET("max_price")) {
            max_price = $_GET("max_price");
            $("#max_price").val(max_price);
        }

        // при слайде ползунков
        if($("#filter_price").length){
            $("#filter_price").slider({
                range: true,
                min: config.filter_min_price,
                max: config.filter_max_price,
                step: config.filter_step,
                values: [min_price, max_price],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                },
                stop: function(event, ui){

                    getGoodsByFilter();
                }
            });
        }


        $(document).on("keyup", "#min_price, #max_price", function(){
            if(timer) clearTimeout(timer);
            timer = setTimeout(function(){
                timer = null;
                let min_price = $("#min_price").val();
                let max_price = $("#max_price").val();
                $("#filter_price").slider("values", [min_price, max_price]);
                getGoodsByFilter();
            }, 1000);
        });


        $(document).on("click", "#clear_filter", function(){

            loadingShow();

            let url = window.location.href.replace(/\/page-[0-9]+\//g, "/").split("?")[0];
            history.pushState(null, null, url);

            $("#min_price, #max_price").val("");
            $("#filter_price").slider("values", [config.filter_min_price, config.filter_max_price]);
            $('.extra_val').prop("checked", false);

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "method": "get_goods_by_filter",
                    "params": "min_price=1"
                },
                dataType: 'text',
                success: function(data){
                    $(".all_goods").html(data);
                    loadingHide();
                }, error: function(data){
                    console.log(data);
                }
            });

            return false;
        })

    }


    // search
    /*$(document).on("keyup", '#search [name="str"]', function(){

        let this_ = $(this);
        let search_string = $(this).val();
        $("#search_result").remove();

        if(timer) clearTimeout(timer);
        timer = setTimeout(function(){

            if(search_string.length >= 3){

                $.loading();

                timer = null;
                $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: {
                        "ajax": "Search",
                        "search": search_string
                    },
                    dataType: 'text',
                    success: function(data){
                        this_.parent().append('<ul id="search_result">' + data + '</ul>');
                        $.loading_close();
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            }

        }, 500);
    });*/

})