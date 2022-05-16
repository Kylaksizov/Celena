$(function(){

    //$.server_say({say: "Выберите свойство!", status: "error"});

    // генерация пароля
    function generatePassword() {
        let chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        let passwordLength = 12;
        let password = "";
        for (let i = 0; i <= passwordLength; i++) {
            let randomNumber = Math.floor(Math.random() * chars.length);
            password += chars.substring(randomNumber, randomNumber +1);
        }
        return password;
    }

    // установка пароля
    let generatedPass = false;
    $(document).on("click", ".generatePassword", function(){
        $("#passwordHelper").text(generatePassword());
        $("#password, #password2").val(generatePassword());
        generatedPass = true;
        return false;
    })

    // если нажал на пароль TMP
    /*$(document).on("click", "#passwordHelper", function(){
        generatedPass = false;
    })
    $(document).on("click", "#createAccess", function(){
        if(generatedPass){
            $.alert("Не забудьте скопировать пароль");
            return false;
        }
    })*/

})