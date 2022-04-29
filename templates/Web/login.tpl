[role="0"]
<div id="authorization" class="authorization modal">
    <h4 class="modal_title">Авторизация в системе</h4>
    <form action="#" method="POST" class="inp100">
        <input type="email" name="email" placeholder="E-mail">
        <input type="password" name="password" placeholder="Пароль">
        <input type="hidden" name="action" value="auth"><br>
        <div class="hr_line">или</div>
        {reg}
        <div class="fx ai_c">
            <a href="#member_pass" class="member_password open_modal">Забыли пароль?</a>
            <input type="submit" class="btn" data-a="Auth" value="Вход">
        </div>
    </form>
    <a href="#" class="close"></a>
</div>

<div id="member_pass" class="modal">
    <h4 class="modal_title">Восстановить пароль</h4>
    <form action="#" method="POST" class="inp100">
        <input type="email" name="email" placeholder="E-mail">
        <input type="hidden" name="action" value="member_pass_finish"><br>
        <input type="submit" class="btn" data-a="Auth" value="Восстановить">
    </form>
    <a href="#" class="close"></a>
</div>
{new-password}

<div id="registration" class="modal">
    <h4 class="modal_title">Регистрация в системе</h4>
    <form action="#" method="POST" class="inp100">
        {*<select name="type" id="type_account" class="inp100_">
            <option value="1" selected disabled>-- выберите тип услуги --</option>
            <option value="1">Управление отелем / гостиницей</option>
            <option value="1">Запись клиентов почасово</option>
            <option value="1">Грузоперевозки</option>
            <option value="1">Сбор данных с форм</option>
        </select>
        <br><br>*}
        <input type="text" name="name" placeholder="Фамилия Имя">
        <input type="email" name="email" placeholder="E-mail">
        <input type="password" name="password" placeholder="Пароль">
        <input type="password" name="password_repeat" placeholder="Повторите пароль">
        <input type="hidden" name="action" value="registration">
        <br>
        <div class="hr_line">или</div>
        {reg}
        <div>
            {*<input type="checkbox" name="agree" id="agree" class="ch_min" value="ok">*}
            <label for="agree">Регистрируясь, вы соглашаетесь с нашими<br><a href="/offer.pdf">Договором</a> и
                <a href="/police.pdf">Политикой конфиденциальности</a></label>
        </div>
        <br>
        <input type="submit" class="btn" data-a="Auth" value="Зарегистрироваться">
    </form>
    <a href="#" class="close"></a>
</div>
[/role]