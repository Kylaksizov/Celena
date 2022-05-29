<!DOCTYPE html>
<html lang="ru">
<head>
    {META}
    <meta name="viewport" content="user-scalable=0, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <link rel="icon" href="/app/core/system/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{THEME}/css/shop.css">
    <link rel="stylesheet" href="{THEME}/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic" rel="stylesheet">
    {STYLES}
    {SCRIPTS}
</head>
<body>

{include file="inc/header.tpl"}
{include file="inc/menu.tpl"}
{crumbs}

<section class="flex content">

    {include file="inc/sitebar.tpl"}

    <main id="camby_content">

        [show="plugins/Celena/Shop/Index"]
        <div id="slider" class="owl-carousel">
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
            <a href="#"><img src="{THEME}/img/slide.jpg" alt=""></a>
        </div>
        [/show]
        [show="plugins/Celena/Shop/Category"]
        <div class="flex cat_info">
            <h1 class="category_name">{category-name}</h1>
            {sort}
        </div>
        <div class="clr"></div>
        [/show]

        [show="plugins/Celena/Shop/Index,plugins/Celena/Shop/Category,plugins/Celena/Shop/Search"]<div class="all_goods">[/show]
            {CONTENT}
        [show="plugins/Celena/Shop/Index,plugins/Celena/Shop/Category,plugins/Celena/Shop/Search"]</div>[/show]

        [show="index"]<br>
        <div class="description_page">
            <h1>Интернет-магазин</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi consectetur dicta facere fugit impedit inventore, magni molestias necessitatibus nisi omnis saepe vero voluptates voluptatibus? Architecto atque beatae dignissimos distinctio dolor error exercitationem impedit ipsa ipsam iste iusto maiores molestiae non obcaecati odio officia quae quaerat quas quidem, rerum veritatis, vitae voluptatum. Dignissimos dolore expedita ipsum magnam maxime perferendis possimus ut. Ab dicta dolores iure necessitatibus quis unde. Ad alias aliquid aut beatae corporis deserunt, ea eaque enim, error ipsa laboriosam libero magnam necessitatibus nesciunt officiis quam sint sit veritatis voluptas voluptates. Dolor doloribus iusto perspiciatis. Blanditiis est iusto minus quidem.</p>
        </div>[/show]

    </main>

</section>

{login}

{include file="inc/footer.tpl"}

<div id="cart_modal" class="modal"></div>

{SYSTEMS}

</body>
</html>