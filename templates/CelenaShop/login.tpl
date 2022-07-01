[role="0"]
<div id="authorization" class="authorization modal">
    <h4 class="modal_title">Авторизация в системе</h4>
    <form action="#" method="POST" class="inp100">
        <input type="email" name="email" placeholder="E-mail">
        <input type="password" name="password" placeholder="Пароль">
        <input type="hidden" name="action" value="auth"><br>
        {*<div class="hr_line">или</div>
        {reg}*}
        <div class="fx ai_c">
            <a href="#member_pass" class="member_password open_modal">Забыли пароль?</a>
            <input type="submit" class="btn" data-s="Auth" value="Вход">
        </div>
    </form>
    <a href="#" class="close"></a>
</div>

<div id="member_pass" class="modal">
    <h4 class="modal_title">Восстановить пароль</h4>
    <form action="#" method="POST" class="inp100">
        <input type="email" name="email" placeholder="E-mail">
        <input type="hidden" name="action" value="member_pass_finish"><br>
        <input type="submit" class="btn" data-s="Auth" value="Восстановить">
    </form>
    <a href="#" class="close"></a>
</div>
{new-password}

<div id="registration" class="modal">
    <h4 class="modal_title">Регистрация в системе</h4>
    <form action="#" method="POST" class="inp100">
        <input type="text" name="name" placeholder="Имя">
        <input type="email" name="email" placeholder="E-mail">
        <input type="password" name="password" placeholder="Пароль">
        <input type="password" name="password_repeat" placeholder="Повторите пароль">
        <input type="hidden" name="action" value="registration">
        <label for="agree">Регистрируясь, вы соглашаетесь с условиями сайта</label>
        <br>
        <input type="submit" class="btn" data-s="Auth" value="Зарегистрироваться">
    </form>
    <a href="#" class="close"></a>
</div>
[/role]