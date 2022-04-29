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
    {STYLES}
    <link rel="stylesheet" href="{THEME}/css/style.css">
    <link rel="stylesheet" href="{THEME}/css/jquery-ui.min.css">
    {SCRIPTS}

</head>
<body>

<form action method="POST" id="authForm">
    <h3>Авторизация в панели</h3>
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Пароль">
    <input type="hidden" name="action" value="auth">
    <input type="submit" data-a="Auth" class="btn" value="Войти">
</form>

{SYSTEMS}

</body>
</html>