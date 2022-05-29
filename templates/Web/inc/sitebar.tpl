<aside id="site_bar">

    <a href="#" class="my_ad">
        <img src="{THEME}/img/ad.jpg" alt="">
    </a>

    <div class="sticky">

        [show="plugins/Celena/Shop/Category"]
        <div id="filter_goods" class="site_bar_box">
            <div class="box_title">Фильтр товаров <a href="#" class="close_filter"></a></div>
            <div class="box_content">

                {filter}
                <br>

            </div>
        </div>
        <a href="#" id="open_filter"></a>
        [/show]
        [show="plugins/Celena/Shop/Index,plugins/Celena/Shop/Category,plugins/Celena/Shop/Product"]
        <div class="site_bar_box">
            <div class="box_title">Новые товары</div>
            <div class="box_content">
                {products category="0" template="customProducts" limit="3" order="price" sort="asc"}
                <br><br><br><br>
                {products category="0" template="customProducts" limit="1"}
            </div>
        </div>
        [/show]

    </div>

</aside>