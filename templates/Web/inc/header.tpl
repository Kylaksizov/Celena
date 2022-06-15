<header id="header">
    <nav class="menu">
        <a href="/" id="logo" title="Celena system"></a>
        <a href="#" id="media_menu"></a>
        <ul class="nav_top">
            <li><a href="/">Главная</a></li>
            <li><a href="#">Новости</a></li>
            <li><a href="#">О нас</a></li>
            <li><a href="#">Контакты</a></li>
            [role="0"]<li><a href="#authorization" class="open_modal">Вход</a></li>
            <li><a href="#registration" class="open_modal">Регистрация</a></li>[/role]
            [not-role="0"]<li><a href="?logout=1">Выход</a></li>[/not-role]
        </ul>
    </nav>
    <form action="/search/" method="GET" id="search" class="flex">
        <input type="search" name="str" placeholder="Поиск..." autocomplete="off">
        <input type="submit" id="search_go" value="">
    </form>
</header>