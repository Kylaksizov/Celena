<header id="header" class="flex">
    <div class="flex">
        <a href="/" id="logo" title="Camby Shop"></a>
        <a href="#" id="media_menu"></a>
        <ul class="nav_top">
            <li><a href="/">Главная</a></li>
            <li><a href="#">Доставка</a></li>
            <li><a href="#">О нас</a></li>
            <li><a href="#">Контакты</a></li>
            [role="0"]<li><a href="#authorization" class="ico_login open_modal">Вход</a></li>
            <li><a href="/registration.html" class="ico_reg">Регистрация</a></li>[/role]
            [not-role="0"]<li><a href="?logout=1" class="ico_logout">Выход</a></li>[/not-role]
        </ul>
    </div>
    <div class="flex">
        {*<a href="#order_click" class="btn-callback open_modal">Заказать звонок</a>*}
        <ul class="contacts">
            <li class="ico_tel"><a href="tel:123456789011">+1 (234) 567 890 11</a></li>
            <li class="ico_mail"><a href="mailto:info@camby.top">info@camby.top</a></li>
        </ul>
        <form action="/search/" method="GET" id="search" class="flex">
            <input type="search" name="str" placeholder="Введите запрос..." autocomplete="off">
            <input type="submit" id="search_go" value="">
        </form>
        <a href="#cart_modal" id="cart" class="open_modal">
            <p>В корзине: пусто</p>
            <p>&nbsp;</p>
        </a>
    </div>
</header>