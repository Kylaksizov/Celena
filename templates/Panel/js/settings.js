$(function(){
    
    $("#selectMain").on("change", function(){
        let selectMain = $(this).find("option:selected").val()
        if(selectMain == 2 || selectMain == 3) $("#main_content").show()
        else $("#main_content").hide().val("")
    })
    
})