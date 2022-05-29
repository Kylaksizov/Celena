$(function(){

    $(document).on("click", ".propsOpen", function (){
        $(this).parent().find(".props").slideToggle(100);
        return false;
    })

})