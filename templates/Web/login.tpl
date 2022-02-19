<article class="simple_article">

    [not-role="гость"]
    <div class="info">Вы уже зарегистрированны</div>
    [/not-role]
    [role="гость"]
    <h1>Регистрация на сайте</h1>

    <div class="reg_info">
        При регистрации на сайте, Вы получите скидку 1% с любой покупки товара
    </div>

    <form action="#" method="POST" id="registration">
        <label for="">Ваше имя <span class="req">*</span></label>
        <input type="text" name="name">
        <label for="">Ваш E-mail <span class="req">*</span></label>
        <input type="email" name="email">
        <label for="">Пароль <span class="req">*</span></label>
        <input type="password" name="password" placeholder="Придумайте пароль">
        <label for="">Повторите пароль <span class="req">*</span></label>
        <input type="password" name="password_repeat" placeholder="Повторите пароль">
        <input type="checkbox" name="consent" id="consent"><label for="consent">Согласен с <a href="#">правилами сайта</a></label>
        <input type="hidden" name="registration" value="1">
        <input type="submit" class="btn" data-a id="reg_submit" value="Зарегистрироваться">
    </form>
    [/role]

</article>