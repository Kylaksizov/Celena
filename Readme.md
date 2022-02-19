# Работа с шаблоном
___

## Структура файлов
Все шаблоны сайта находятся в папке **templates**.  
> *Panel* - шаблон для админ-панели, не рекомендуется ничего в нем изменять.  
Папки начинающиеся с нижнего подчеркивания так же не рекомендуется трогать.  

<font size="2">В папке вашего шаблона, например (Web), должна присутствовать такая структура файлов:</font>
- _index.tpl_ - главный файл, который может включать в себя другие подключаемые файлы и плагины.
- _products.tpl[^1]_ - шаблон для одного товара в списке товаров. [теги](#products.tpl)
- _product.tpl_ - шаблон товара с полным описанием.
- _products_custom.tpl_ - шаблон одного товара, подключаемого через тег custom.
- _cart.tpl_ - страницы оформления заказа.
- _login.tpl_ - форма и регистрации.

## Теги
___

### Глобальные теги
`[show="index"]...[/show]` - выведет содержимое если контроллер *index* [^1]  
`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  
`[not-show="index"]...[/not-show]` - выведет если мы не на контроллере *index*  
`{include file="includes/inc.tpl"}` - подключаем файл *includes/inc.tpl*  
`{{MenuModule}}` - выведет содержимое модуля Menu  
`{SYSTEM}` - обязательный тег в конце index.tpl

### Глобальные теги
`[show="index"]...[/show]` - выведет содержимое если контроллер *index* [^1]  
`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  
`[not-show="index"]...[/not-show]` - выведет если мы не на контроллере *index*  
`{include file="includes/inc.tpl"}` - подключаем файл *includes/inc.tpl*  
`{{MenuModule}}` - выведет содержимое модуля Menu  
`{SYSTEM}` - обязательный тег в конце index.tpl

### Глобальные теги
`[show="index"]...[/show]` - выведет содержимое если контроллер *index* [^1]  
`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  
`[not-show="index"]...[/not-show]` - выведет если мы не на контроллере *index*  
`{include file="includes/inc.tpl"}` - подключаем файл *includes/inc.tpl*  
`{{MenuModule}}` - выведет содержимое модуля Menu  
`{SYSTEM}` - обязательный тег в конце index.tpl

### Глобальные теги
`[show="index"]...[/show]` - выведет содержимое если контроллер *index* [^1]  
`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  
`[not-show="index"]...[/not-show]` - выведет если мы не на контроллере *index*  
`{include file="includes/inc.tpl"}` - подключаем файл *includes/inc.tpl*  
`{{MenuModule}}` - выведет содержимое модуля Menu  
`{SYSTEM}` - обязательный тег в конце index.tpl

### products.tpl
`[show="index"]...[/show]` - выведет содержимое если контроллер *index* 

## info

### --- пометки ---

### --- важное ---

1. Пользователи
   - получение инфы о пользователе находится в конструкторе [Router.php](app/core/Router.php)

### Notes
[^1]: Если роутер имеет контроллер