$(function(){

    setInterval(function() {
        var home = $("#home");
        home.html(home.html());
    }, 12000);

    // открытие модального окна
    $(".open_modal").click(function(){
        let modal_id = $(this).attr("href");
        $(".modal").stop(true, true).fadeOut(300);
        $(".bg_0, " + modal_id).stop(true, true).fadeIn(300);
        return false;
    })
    $(".bg_0, .close").click(function(){
        $(".bg_0, .modal").stop(true, true).fadeOut(300);
        return false;
    })

    window.addEventListener( "scroll", function() {
        siteAnimate();
        curMenu();
    });

    function siteAnimate(){
        $(".site-img-wrap").each(function () {
            var elem = $(this);
            var winBot = $(window).scrollTop() + window.innerHeight;
            var blockBot = elem.offset().top + elem.height();
            if (winBot >= blockBot) {
                elem.addClass("site-img-wrap-active");
            } else {
                elem.removeClass("site-img-wrap-active");
            }
        });
    }

    function curMenu(){
        $(".block").each(function () {
            var elem = $(this);
            var winBot = $(window).scrollTop() + window.innerHeight;
            var blockTop = elem.offset().top;
            if (winBot > blockTop) {
                $(".menu a").removeClass("active").each(function () {
                    if($(this).attr("href") === "#"+elem.attr("id")){
                        $(this).addClass("active");
                    }
                });
            }
        });
    }

    $('a[href^="#"]').click(function () {
        elementClick = $(this).attr("href");
        destination = $(elementClick).offset().top;
        $('html, body').animate( { scrollTop: destination }, 600 );
        return false;
    });

    $(".partners").slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        dots: false,
        prevArrow: '<img src="/public/templates/Client/img/back.svg" alt="" class="left-arr slide-arr">',
        nextArrow: '<img src="/public/templates/Client/img/back.svg" alt="" class="right-arr slide-arr">',
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });

    $(".tray-block-slider").slick({
        autoplay: true,
        infinite: true,
        fade: true,
        dots: false,
        arrows: false
    });

    $(".plus-item").click(function () {
        if(!$(this).hasClass("plus-item-active")){
            var items = $(".plus-item");
            var index = items.index($(this));
            index++;
            items.removeClass("plus-item-active");
            $(this).addClass("plus-item-active");
            $(".plus-notebook-item").removeClass("plus-notebook-item-active");
            $(".plus-notebook-item:nth-of-type("+index+")").addClass("plus-notebook-item-active");
        }
    });

    $(".mob-but").click(function () {
        if($(this).hasClass("mob-but-active")){
            $(this).removeClass("mob-but-active");
            $(".mob-menu-wrap").removeClass("mob-menu-wrap-active");
            $("#shadow").fadeOut(300);
        } else {
            $(this).addClass("mob-but-active");
            $(".mob-menu-wrap").addClass("mob-menu-wrap-active");
            $("#shadow").fadeIn(300);
        }
    });

    $("#shadow, .mob-menu a").click(function () {
        $(".mob-but").removeClass("mob-but-active");
        $(".mob-menu-wrap").removeClass("mob-menu-wrap-active");
        $("#shadow").fadeOut(300);
    });

    $(".mask_tel_UA").mask("+38 (999) 999-99-99");
    $(".mask_tel_RU").mask("+99 (999) 99-99-99");

});