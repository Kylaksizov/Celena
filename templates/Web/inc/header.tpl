<header id="header" class="fx">
    <nav id="menu">
        <a href="/" id="logo" title="Celena"></a>
        <a href="#" id="media_menu"></a>
        <ul class="nav_top">
            <li><a href="/">Главная</a></li>
            <li><a href="/news-america/">Новости в Америке</a></li>
            <li><a href="/news-ukraine/">Новости в Украине</a></li>
            <li><a href="/o-nas.html">О нас</a></li>
            <li><a href="/contacts.html">Контакты</a></li>
            [role="0"]<li><a href="#authorization" class="open_modal">Вход</a></li>
            <li><a href="#registration" class="open_modal">Регистрация</a></li>[/role]
            [not-role="0"]<li><a href="?logout=1">Выход</a></li>[/not-role]
        </ul>
    </nav>
    <form action="/search/" method="GET" id="search" class="fx">
        <input type="search" name="str" placeholder="Поиск..." autocomplete="off">
        <input type="submit" id="search_go" value="">
    </form>
</header>