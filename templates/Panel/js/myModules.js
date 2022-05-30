$(function(){

    let editor = CodeMirror.fromTextArea(document.getElementById("code1"), {
        //lineWrapping: true
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-php",
        indentUnit: 4,
        theme: "celena"
    });

    $(document).on("click", ".add", function(){
        $(this).after(`<textarea name="" id="code3" cols="30" rows="10"></textarea>`);
        let editor2 = CodeMirror.fromTextArea(document.getElementById("code3"), {
            //lineWrapping: true
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            theme: "celena"
        });
        return false;
    })
})