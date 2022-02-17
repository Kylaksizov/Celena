$(window).scroll(function() { // приклеивающийся хедер // 
    if ($(this).scrollTop() > 300){
        $('nav').addClass("stickys");
      }
      else{
        $('nav').removeClass("stickys");
      }
});

(function() {  // смена бекграунда // 
    var curImgId = 1;
    var numberOfImages = 3; // Change this to the number of background images
    window.setInterval(function() {
        $('#header').css({
            'background': 'url("/public/templates/Client/img/' + curImgId + '.jpg") no-repeat',
            'background-size': 'cover'
        });
        curImgId++;
        if(curImgId == numberOfImages) curImgId = 1;
    }, 15000);

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
    $(document).on("click", ".nav-wrapper a", function(){
        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top
        }, 400);
        return false;
    })

})();
