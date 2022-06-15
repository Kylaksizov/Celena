<!DOCTYPE html>
<html lang="ru">
<head>
    {META}
    <meta name="viewport" content="user-scalable=0, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <link rel="icon" href="/app/core/system/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{THEME}/css/style.css">
    {STYLES}
    {SCRIPTS}
</head>
<body>

{include file="inc/header.tpl"}

{crumbs}

<section class="cont">

    {include file="inc/sitebar.tpl"}

    <main id="content">

        [show="index"]
            <h1>This is simple template by Celena</h1>
            <div class="custom_header">
                {custom template="inc/customHeader" limit="5" sort="desc"}
            </div>
            <h2 class="title_box">All news</h2>
        [/show]

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

</section>

{include file="inc/footer.tpl"}
{login}

{SYSTEMS}

</body>
</html>