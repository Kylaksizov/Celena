<!doctype html>
<html lang="ru">
<head>
    {META}
    <meta name="viewport" content="user-scalable=0, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <link rel="shortcut icon" sizes="16x16" href="{THEME}/img/favicon.ico">
    <link rel="stylesheet" href="{THEME}/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic" rel="stylesheet">
    {STYLES}
    {SCRIPTS}
</head>
<body>

{include file="views/header.tpl"}
{include file="views/menu.tpl"}
{crumbs}

<section class="flex content">

    {include file="views/include/sitebar.tpl"}

    <main id="camby_content">

        [show="index"]
        <div id="slider" class="owl-carousel">
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
        </div>
        [/show]

        [show="category"]
        <div class="flex cat_info">
            <h1 class="category_name">{category-name}</h1>
            {sort}
        </div>
        [/show]

        <div class="clr"></div>

        [show="category"]<div class="flex all_goods">[/show]
            {CONTENT}
        [show="category"]</div>[/show]

        [show="index"]{include file="views/include/seo.tpl"}[/show]

    </main>

</section>

{include file="views/footer.tpl"}

<div id="cart_modal" class="modal"></div>

{SYSTEMS}

</body>
</html>