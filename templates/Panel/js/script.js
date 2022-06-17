$(function(){

    let url = document.location.href;
    let urlExplode = url.split('/');
    let pathLink = document.location.pathname;

    $.each($('#menu a'), function() {
        let hrefLink = $(this).attr('href');
        if(hrefLink == '/' && pathLink == '/' /* || pathLink.match(new RegExp("/", "g") || []).length == 2*/) $("#home_link").addClass("active");
        else{
            if(pathLink == hrefLink){
                $(this).parent().addClass('active');
                if($(this).next('ul').length){
                    $(this).next('ul').show();
                }
            }
            if('/'+urlExplode[3]+'/'+urlExplode[4]+'/' == hrefLink){
                $(this).addClass("active");
                $(this).parent().parent().parent().addClass("active");
                $(this).parent().parent().show();
            }
            if(url.indexOf(hrefLink) + 1){
                $(this).addClass("active");
                $(this).parent().parent().parent().addClass("active");
                $(this).parent().parent().show();
            }
        }
    });

    // открытие подменю
    $(document).on("click", "#menu nav > ul > li > a", function(){
        let hrefLink = $(this).attr('href');
        if(hrefLink == '#'){
            $('#menu nav > ul > li > ul').stop(true, false).slideUp(300);
            $(this).next('ul').stop(false, true).slideDown(300);
            return false;
        }
    })

    // открытие модального окна
    $(document).on("click", ".open_modal", function(){
        let modal_id = $(this).attr("href");
        if(modal_id != undefined){
            $('body').append($(modal_id));
            $(modal_id).stop(true, true).fadeOut(300)
            $(".bg_0, " + modal_id).stop(true, true).fadeIn(300).addClass('opened_modal');
        }
        return false;
    })
    $(document).on("click", ".bg_0, .close", function(){
        $(".modal, .modal_big").removeClass('open_modal').addClass('closes_modal');
        $(".bg_0, .modal, .modal_big, .alert_msg, .alert_overlay").fadeOut(300, function(){
            $(".modal, .modal_big").removeClass('closes_modal');
        });
        return false;
    })

    $(document).on("click", ".ico_notify, .open_profile_setting", function(){
        $(this).next().stop(true, true).slideDown(300);
        return false;
    })

    // Escape
    $(document).on("keydown", function(e){
        if(e.which === 27){
            $(".esc").fadeOut(300);
        }
    })
    $(document).mouseup( function(e){
        let div = $(".esc");
        if (!div.is(e.target) && div.has(e.target).length === 0)
            div.fadeOut(300);
    });


    // TMP EDITOR
    // https://quilljs.com/docs/api/#getcontents
    let toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],

        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
        [{ 'direction': 'rtl' }],                         // text direction

        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [ 'link', 'image', 'video', 'formula' ],          // add's image support
        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
        [{ 'font': [] }],
        [{ 'align': [] }],

        ['clean']                                         // remove formatting button
    ];
    $('[data-editor]').each(function(){
        let editorId = $(this).attr('data-editor');
        new Quill('[data-editor="'+editorId+'"]', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: "snow"
        });
    })
    $(document).on("mouseover", '[data-a]', function(){
        $('[data-editor]').each(function(){
            let editorName = $(this).attr('data-editor');
            let editorContent = $(this).find(".ql-editor").html();
            $("#editor_"+editorName).remove();
            $(this).append(`<textarea name="`+editorName+`" id="editor_`+editorName+`" style="display:none">`+editorContent+`</textarea>`);
        })
    })
    // TMP EDITOR END



})