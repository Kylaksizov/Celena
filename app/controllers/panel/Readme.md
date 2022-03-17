# Development Tools
___

## Константы

`ROOT` - корень  
`APP` - /app  
`CORE` - /app/core


## <a name="ajax">AJAX</a>
Атрибут `data-a` в любом элементе. Или `data-a="User:id=10&name=Питер Пен"` - отправит пост в AJAX файл User.  
Ajax должен содержать класс index.  
Если ajax не тормозит процесс, запрос идет дальше в контроллер по адресу урла.  
В AJAX контроллере если метод index пропускает дальше, т.е. возвращает что-то, будет доступно свойство $this->ajax, которое вернется в файл контроллера.


### Other help
[md info](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax), [md info 2](https://github.com/GnuriaN/format-README#%D0%A1%D1%81%D1%8B%D0%BB%D0%BA%D0%B8)  - работа с md файлами   
[Chart](https://www.amcharts.com/demos/) - графики