$(function(){

    // активация дезактивация категории
    $(document).on("change", ".status_category", function(){
        let categoryId = $(this).attr("data-id");
        let statusCategory = $(this).prop("checked");
        $.ajaxSend($(this), {"ajax": "Category", "categoryId": categoryId, "statusCategory": statusCategory});
    })

})