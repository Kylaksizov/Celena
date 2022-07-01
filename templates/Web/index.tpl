<!DOCTYPE html>
<html lang="ru">
<head>
    {META}
    <meta name="viewport" content="user-scalable=0, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <link rel="icon" href="/app/core/system/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{THEME}/css/style.css?v={version}">
    {STYLES}
    {SCRIPTS}
</head>
<body>

{include file="inc/header.tpl"}

{crumbs}

<section class="cont"[show="post"] id="article"[/show]>

    <main id="content">
        [category="1,2"]{include file="test.tpl"}[/category]
        [category="1,3"]<b>В категории 1 и 3</b>[/category]
        [category="2"]<b>В категории 2222222222222</b>[/category]

        [show="index"]
            <h1>This is simple template by Celena</h1>
            <div class="custom_header">
                {custom template="inc/customHeader" limit="5" sort="desc"}
            </div>
            <h2 class="title_box">All news</h2>
        [/show]

        [show="search"]{title}[/show]
        [show="category"]<h1>{category-name}</h1>[/show]
        <div class="news">
            {CONTENT}
        </div>

        [show="index"]
        <h2 class="title_box">News in Ukraine</h2>
        <div class="custom_footer">
            <div class="custom_fl">
                {custom template="inc/customFooterLeft" limit="5" sort="desc"}
            </div>
            <div class="custom_fr">
                {custom template="inc/customFooterRight" limit="3" sort="desc"}
            </div>
        </div>
        [/show]

    </main>

    [show="post"]
    {include file="inc/sitebar.tpl"}
    [/show]

</section>

{include file="inc/footer.tpl"}
{login}

{SYSTEMS}

</body>
</html>