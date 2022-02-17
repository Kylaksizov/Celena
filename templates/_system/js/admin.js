$(function(){

    let nexDev = localStorage.getItem('nexDev')
    let nexDevErrors = localStorage.getItem('nexDevErrors')

    if(nexDev !== null && nexDev == '1') $(".nex_dev_info").addClass('devShow')
    if(nexDevErrors !== null && nexDevErrors == '1') $(".error .db_hidden").addClass('devShow')

    $(document).on("click", ".nex_debug", function(){
        nexDev = localStorage.getItem('nexDev')
        if(nexDev === null || nexDev == '0'){
            $(".nex_dev_info").stop(true, true).slideDown('slow')
            localStorage.setItem('nexDev', '1')
        } else{
            $(".nex_dev_info").stop(true, true).slideUp('slow')
            localStorage.setItem('nexDev', '0')
            $(".nex_dev_info").removeClass('devShow')
        }
        return false
    })

    $(document).on("click", ".dev_show_log", function(){
        $(this).next().stop(true, true).slideToggle('slow')
        return false
    })

    $(document).on("click", ".log_e", function(){
        nexDevErrors = localStorage.getItem('nexDevErrors')
        if(nexDev === null || nexDevErrors == '0'){
            $(".error .db_hidden").stop(true, true).slideDown('slow')
            localStorage.setItem('nexDevErrors', '1')
        } else{
            $(".error .db_hidden").stop(true, true).slideUp('slow')
            localStorage.setItem('nexDevErrors', '0')
            $(".error .db_hidden").removeClass('devShow')
        }

        return false
    })

})