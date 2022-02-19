# Работа с шаблоном

## Структура файлов
Все шаблоны сайта находятся в папке **templates**.  
> *Panel* - шаблон для админ-панели, не рекомендуется ничего в нем изменять.  
Папки начинающиеся с нижнего подчеркивания так же не рекомендуется трогать.  

<font size="2">В папке вашего шаблона, например (Web), должна присутствовать такая структура файлов:</font>
- _[index.tpl](#index_tpl)_ - главный файл, который может включать в себя другие подключаемые файлы и плагины.
- _[products.tpl](#products_tpl)_ - превью товара.
- _[product.tpl](#product_tpl)_ - карточка товара.
- _[products_custom.tpl](#products_custom_tpl)_ - превью товара, подключаемого через тег custom.
- _[cart.tpl](cart_tpl)_ - страницы оформления заказа.
- _[login.tpl](login_tpl)_ - форма входа и регистрации.

## Теги


### <a name="index_tpl">Глобальные теги</a>
___
Обязательные теги
___
`{SYSTEM}` - системный тег для правильной работы сайта, ставится в конец тела документа, перед `</body>`  
`{META}` - выводит мета-теги сайта, ставится после `<head>`.
`{STYLES}` - выводит стили, ставится перед `</head>`.
`{SCRIPTS}` - выводит скрипты сайта, ставится перед `</head>`.
___
Второстепенные теги
___
`[show="index"]...[/show]` - выведет содержимое если контроллер[^*] *index* 

`[show="plugins/Kylaksizov/Menu/Index"]...[/show]` - выведет содержимое если мы в плагине **plugins/Kylaksizov/Menu/Index**  

`[not-show="index"]...[/not-show]` - выведет если мы не находимся в контроллере *index*  

`{include file="includes/inc.tpl"}` - подключит файл *includes/inc.tpl*  

`{{Kylaksizov/MenuModule}}` - выведет содержимое модуля Menu


### <a name="products_tpl">products.tpl</a>
`{tag}` - описание тега. 

### <a name="product_tpl">product.tpl</a>
`{tag}` - описание тега.

### <a name="products_custom_tpl">products_custom.tpl</a>
`{tag}` - описание тега.

### <a name="cart_tpl">cart.tpl</a>
`{tag}` - описание тега.

### <a name="login_tpl">login.tpl</a>
`{tag}` - описание тега.


### Notes
[^1]: Если роутер имеет контроллер

### For developers
[Panel](/app/controllers/panel/Readme.md#ajax)