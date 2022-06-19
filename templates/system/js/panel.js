/*=== Tooltip ===*/
(function( $ ){
    $.fn.tooltip = function(options) {

        options = $.extend({
            class: 'tooltip'

        }, options);

        $(this).each(function (i) {
            $("body").stop(true, true).append("<div class='" + options.class + "' id='" + options.class + i + "'>" + $(this).attr('title') + "</div>");
            var tooltips = $("#" + options.class + i);
            if ($(this).attr("title") != "" && $(this).attr("title") != "undefined") {
                $(this).removeAttr("title").mouseover(function () {
                    tooltips.css({
                        opacity: 0.95,
                        display: "none"
                    }).stop(true, true).fadeIn(300);
                }).mousemove(function (kmouse) {
                    var bw = $("html,body").width();
                    if ((bw / 2) < kmouse.pageX) {
                        tooltips.css({
                            left: kmouse.pageX - 15 - tooltips.width(),
                            top: kmouse.pageY + 15
                        });
                    } else {
                        tooltips.css({
                            left: kmouse.pageX + 15,
                            top: kmouse.pageY + 15
                        });
                    }
                }).mouseout(function () {
                    tooltips.stop(true, true).fadeOut(100);
                });
            }
        });

    };
})(jQuery);
/*=== Server ansver ===*/
(function ($) {

    $.server_say = function (options) {

        options = $.extend({
            status: "default",
            say: 'Готово!',
            mr: Math.floor(Math.random() * (1000 - 1 + 1)) + 1,
            delay: 5000

        }, options);

        //console.log(options.mr);

        $("#main_answer_server").append('<div id="apps_modal_' + options.mr + '" class="server_say ' + options.status + '">' + options.say + '<a href="#" class="close"></a></div>');
        $("#apps_modal_" + options.mr).stop(true, true).delay(options.delay).fadeOut(2000);

        return true;

    };

})(jQuery);

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

            let ok = 'Ok';
            let cancel = 'Отмена';
            let args = arguments;
            let argsCount = Array.isArray(arguments[1]) ? arguments[1].length : 0;

            if(argsCount >= 3){

                let buttons = '';
                if(Array.isArray(arguments[1])){
                    arguments[1].forEach(function(arrayItem){
                        buttons += arrayItem;
                    });
                    args[1] = arguments[2];
                }

                if(args.length){

                    // если содержимое содержит много символов
                    let alertSize = '';
                    if(args[0].length > 250) alertSize = ' alert_big';

                    var d =$('<div class="alert_overlay esc"></div>' +
                            '<div class="alert_msg'+alertSize+' esc">' +
                            '<div class="alert_content">'+args[0]+'</div>' +
                            '<div class="alert_buttons many_buttons">' +
                            buttons +
                            '</div>' +
                            '</div>'),
                        fn=args[1],
                        flag=1,
                        _click = function(e){
                            typeof fn=='function'?(fn.call(d,e.target.innerHTML)!=!1&&d.remove()):d.remove();
                        };
                    $._isalert&&d.find('.alert_btn_cancel').hide();
                    d.on('contextmenu',!1)
                        .on('click','.alert_btn',{r:!0},_click)
                        .appendTo('body');
                }

            } else{

                if(Array.isArray(arguments[1])){
                    if(arguments[1][0] != undefined) ok = arguments[1][0];
                    if(arguments[1][1] != undefined) cancel = arguments[1][1];
                    args[1] = arguments[2];
                }
                if(args.length){

                    // если содержимое содержит много символов
                    let alertSize = '';
                    if(args[0].length > 250) alertSize = ' alert_big';

                    var d =$('<div class="alert_overlay esc"></div>' +
                            '<div class="alert_msg'+alertSize+' esc">' +
                            '<div class="alert_content">'+args[0]+'</div>' +
                            '<div class="alert_buttons">' +
                            '<button class="alert_btn alert_btn_ok">'+ok+'</button>' +
                            '<button class="alert_btn alert_btn_cancel">'+cancel+'</button>' +
                            '</div>' +
                            '</div>'),
                        fn=args[1],
                        flag=1,
                        _click = function(e){
                            //console.log(e.target.innerHTML);
                            typeof fn=='function'?(fn.call(d,e.data.r)!=!1&&d.remove()):d.remove();
                        };
                    $._isalert&&d.find('.alert_btn_cancel').hide();
                    d.on('contextmenu',!1)
                        .on('click','.alert_btn_ok',{r:!0},_click)
                        .on('click','.alert_btn_cancel',{r:!1},_click)
                        .appendTo('body');
                }
            }

            $._isalert=0;
        },
        $.prompt=function(){
            var args=arguments;
            if(args.length){
                var d =$('<div class="alert_overlay esc"></div><div class="alert_msg esc"><div class="alert_content">'+args[0]+'<br><input type="text" id="prompt_js" autocomplete="off"></div><div class="alert_buttons"><button class="alert_btn alert_btn_ok">Ок</button><button class="alert_btn alert_btn_cancel">Отмена</button></div></div>'),
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

/*=== Loading ===*/
!function ($) {
    $.loading = function(title){

        if(title == undefined) title = 'Подождите...';
        $("#loading-text").text(title)

        let timeClose = 5000;
        if(arguments[0] == undefined) arguments[0] = title;
        if(arguments[1] != undefined) timeClose = arguments[1];

        $('#loading').stop(true, true).delay(700).fadeIn(300);
        //$('#loading-text').html('<span class="errors">'+arguments[0]+'</span>');

        // если ответа нет более 10 секунд
        /*setTimeout(function(){
            $(".loading").html('<span class="unknown_error">Неизвестная ошибка!</span>').css("background", "#f00").stop(true, false).delay(2000).fadeOut(300, function () {
                $(this).remove();
            });
        }, timeClose)*/
    }
    $.loading_close = function(){
        $('#loading').stop(true, true).fadeOut(700);
    }
}($);
/*=== Loading end ===*/

/*=== ajaxSend ===*/
!function ($) {
    $.ajaxSend = function(){

        let params = {};

        $.loading();

        let this_ = arguments[0];

        // добавляем элементу временный класс
        $(".cel_tmp").removeClass("cel_tmp");
        this_.addClass("cel_tmp");

        let data = new FormData();
        let d1;
        let d2;

        // если объекта нет или передан пустой объект
        if ($.isEmptyObject(arguments[1])){

            function escapeRegExp(string){
                return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            }

            //if(/*this_.parents("form").length == 0*/this_.attr('data-a') != undefined && this_.attr('data-a').length > 0){

            // если в атрибуте data-a что-то задано
            if(this_.attr('data-a') != undefined && this_.attr('data-a').length > 0){

                let method = '';

                if(this_.attr('data-a').indexOf(':') + 1){
                    let tmp = this_.attr('data-a').split(":");
                    method = tmp[0];
                    params = tmp[1];
                } else{
                    method = this_.attr('data-a');
                    params = this_.parents("form").serialize();
                }

                // если есть CKEDITOR
                if(this_.parents("form").find('.cke_contents').length > 0){

                    this_.parents("form").find('.cke_contents').each(function(){
                        let textareaId = $(this).parent().parent().prev().attr("id");
                        let textareaName = $(this).parent().parent().prev().attr("name");

                        var desc = CKEDITOR.instances[textareaId].getData();
                        desc = desc.replace('&ndash;', '-');
                        desc = desc.replace('&laquo;', '"');
                        desc = desc.replace('&raquo;', '"');
                        console.log(desc);
                        params += `&`+textareaName+`=`+desc;
                    })
                }

                data.append("ajax", method);
                data.append("params", params);

                // если есть поля с выбором файлов
                // ===============================
                $('[type="file"]').each(function(){
                    let fileName = $(this).attr("name");
                    // перебираем файлы если есть
                    for (let i = 0; i < this.files.length; i++) {
                        data.append(fileName, this.files[i]);
                    }
                    //data.append(fileName+'[]', this.files);
                })
                d1 = false;
                d2 = false;

            } else data = this_.parents("form").serialize();

        } else data = arguments[1];

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: data,
            dataType: 'text',
            processData: d1,
            contentType: d2,
            success: function(data){

                console.log('ajaxSend -> ' + data);

                /**
                 * @param redirect - редирект на data[1]
                 * @param eval - выполнится функция из JavaScript или JQuery -> data[1]
                 * @param в других случаях будет выведен алерт
                 */
                if(data.indexOf('::') + 1){

                    data = data.split('::');

                    if(data[0] == 'redirect') window.location.href = data[1]; // редирект

                    else if(data[0] == 'eval'){ // выполнение функции

                        if(data[2] != undefined){

                            eval(data[1]);
                            $.alert(data[2]); // вывод текста в алерте со статусом

                        } else eval(data[1]);

                    } else if(data[0] == 'info'){ // информационное сообщение

                        if(data[2] != undefined){

                            $.server_say({say: data[2], status: data[1], delay: 5000});

                        } else $.server_say({say: data[1], delay: 5000});
                    }

                    else $.alert('<span class="'+data[0]+'">'+data[1]+'</span>'); // вывод текста в алерте со статусом

                }

                else if(data.indexOf('reload') + 1) window.location.reload();

                else $.alert(data);

                $.loading_close();

            },
            error: function(data){
                console.log(data);
                $.loading_close();
            }
        });

    }
}($);
/*=== ajaxSend end ===*/

$(function(){

    let url = window.location.href;

    $('[title]').tooltip();

    // tabs
    $(document).on('click', 'ul.tabs_caption li:not(.active)', function() {
        $(this)
            .addClass('active').siblings().removeClass('active')
            .closest('div.tabs').find('div.tabs_content').removeClass('active').eq($(this).index()).addClass('active');
        history.pushState(null, null, '#' + parseInt($(this).index()));
    });
    if(url.indexOf("#") + 1){
        let tabNumber = url.split("#");
        tabNumber = parseInt(tabNumber[1]);
        if(!isNaN(tabNumber)){
            $(".tabs_caption li, .tabs_content").removeClass("active");
            $(".tabs_caption li:eq("+tabNumber+")").addClass("active");
            $(".tabs_content:eq("+tabNumber+")").addClass("active");
        }
    }

    // отправка любой формы через AJAX
    $(document).on("click", '[data-a]:not(.data-stop)', function(){

        // проверяем есть ли поля с required
        let allowed_send = true;
        $(this).parents('form').find('[required]').each(function(){
            let tmp_field = $(this).val();
            if(tmp_field == null || tmp_field.length == 0) allowed_send = false;
        })
        // если все поля заполнены, отправляем данные с формы
        if(allowed_send){
            $.ajaxSend($(this));
            return false;
        }
    })

    // Нажатие клавишы Esc
    $(document).on("keydown", function(e){
        if(e.which === 27){
            $(".modal, .modal_big").removeClass('open_modal').addClass('closes_modal');
            $(".bg_0, .modal, .modal_big, .alert_msg, .alert_overlay").fadeOut(300, function(){
                $(".modal, .modal_big").removeClass('closes_modal');
            });
        }
    })

    if($(".multipleSelect").length) $('.multipleSelect').select2();

    // контекстное меню
    /*$(document).on("contextmenu", '.cxt', function(e){
        e.preventDefault()

        $('.contextM').removeClass('contextM')
        $(this).addClass('contextM')

        $('.contextmenu').stop(true, true).css({
            "left": e.pageX + 'px',
            "top": e.pageY + 'px'
        }).show();

        return false
    })*/

    $(document).on('change', 'input[type="file"]', function(){

        let all_files = "<b>выбранные:</b> ";
        for (let i = 0; i < this.files.length; i++) {
            all_files += '<span>' + this.files[i]["name"] + '</span>';
        }
        $(this).parent().next().next(".files_preload").remove();
        $(this).parent().after(`<div class="clr"></div><div class="files_preload">`+all_files+`</div>`);
        $(this).parent().next().next(".files_preload").fadeIn(300);
    })

    // https://air-datepicker.com/ru/docs
    if($('.date').length){
        $('.date').each(function(){
            let calendarPosition = ($(this).attr("data-position") != undefined) ? {position: $(this).attr("data-position")} : {}
            new AirDatepicker(this, calendarPosition);
        })
    }
    if($('.dateTime').length) {
        $('.dateTime').each(function(){
            let calendarData = ($(this).attr("data-position") != undefined) ? {position: $(this).attr("data-position"), timepicker: true} : {timepicker: true}
            new AirDatepicker(this, calendarData);
        })
    }


    // поиск совпадений в двух массивах
    let intersect = function(arr1, arr2) {
        return arr1.filter(function(n) {
            return arr2.indexOf(n) !== -1;
        });
    };

    if($('#categoryOptions').length){

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

        $('#categoryOptions').on('change', function (e) {
            buildFields();
        });
    }

})