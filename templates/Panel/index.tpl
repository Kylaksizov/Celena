<!doctype html>
<html lang="ru">
<head>
    {META}
    <meta name="HandheldFriendly" content="true">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <link rel="shortcut icon" href="{THEME}/img/favicon.ico">
    <link rel="apple-touch-icon" href="{THEME}/img/touch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{THEME}/img/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="{THEME}/img/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="{THEME}/img/touch-icon-ipad-retina.png">
    {STYLES}
    <link rel="stylesheet" href="{THEME}/css/style.css">
    <link rel="stylesheet" href="{THEME}/css/jquery-ui.min.css">

    {SCRIPTS}
</head>
<body>

<header id="header">

    <div id="search_box">
        <a href="#" class="menu_t"></a>
        <input type="search" name="search" placeholder="Поиск заявок, клиентов и т.д." autocomplete="off">
    </div>

    <a href="{panel}/balance/" id="balance">
        <span>Баланс</span>
        <b>116923.5 ₽</b>
    </a>

    <div id="notification">
        <a href="#" class="ico_notify" title="Уведомления">
            <span>3</span>
        </a>
        <ul class="esc">
            <li class="new_notify">
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
            <li class="new_notify">
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
            <li class="new_notify">
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <span class="new_lead">Новый лид №15</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                </a>
            </li>
        </ul>
    </div>

    <div class="profile_header">
        <a href="#" class="open_profile_setting">
            <img src="//minible-v-light.react.themesbrand.com/static/media/avatar-4.b23e41d9.jpg" alt="" class="avatar">
            <span>Владимир Кулаксизов</span>
            <span class="cur"></span>
        </a>
        <ul class="esc">
            <li><a href="{panel}/profile/" class="profile">Профиль</a></li>
            <li><a href="{panel}/settings/" class="settings">Настройки</a></li>
            <li><a href="?logout=1" class="logout">Выход</a></li>
        </ul>
    </div>

</header>

<main id="content">

    <aside id="menu">
        <div class="company_name"><a href="{panel}/">МОЙ МАГАЗИН</a> <a href="{HOME}" target="_blank" class="on_site"></a></div>
        <nav>
            {menu}
        </nav>
    </aside>

    <div id="space">
        {CONTENT}
    </div>

</main>


{SYSTEMS}

<input type="text" id="copyThis">

</body>
</html>