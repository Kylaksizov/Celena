[md info](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax)  
[md info 2](https://github.com/GnuriaN/format-README#%D0%A1%D1%81%D1%8B%D0%BB%D0%BA%D0%B8)  
[Chart](https://www.amcharts.com/demos/)

## Константы

`ROOT` - корень  
`APP` - /app  
`CORE` - /app/core


## AJAX
Атрибут `data-a` в любом элементе. Или `data-a="User:id=10&name=Питер Пен"` - отправит пост в AJAX файл User.  
Ajax должен содержать класс index.  
Если ajax не тормозит процесс, запрос идет дальше в контроллер по адресу урла.  
В контроллере если файл ajax пропускает дальше, будет доступно свойство $this->ajax, которое вернет файл ajax.


## TPL

`[show="index"]...[/show]` - выведет содержимое если контроллер *index*  
`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  
`[not-show="index"]...[/not-show]` - выведет если мы не на контроллере *index*  
`{include file="includes/inc.tpl"}` - подключаем файл *includes/inc.tpl*  
`{{MenuModule}}` - выведет содержимое модуля Menu  
`{SYSTEM}` - обязательный тег в конце index.tpl

## info

### --- пометки ---

### --- важное ---

1. Пользователи
   - получение инфы о пользователе находится в конструкторе [Router.php](app/core/Router.php)