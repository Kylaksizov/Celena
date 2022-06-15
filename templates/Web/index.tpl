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

<section class="flex content">

    {include file="inc/sitebar.tpl"}

    <main id="content">

        {CONTENT}

    </main>

</section>

{login}

{include file="inc/footer.tpl"}

{SYSTEMS}

</body>
</html>