<aside id="site_bar">

    <a href="#" class="my_ad">
        <img src="{THEME}/img/ad.jpg" alt="">
    </a>

    <div class="sticky">

        [show="category"]
        <div id="filter_goods" class="site_bar_box">
            <div class="box_title">Фильтр товаров <a href="#" class="close_filter"></a></div>
            <div class="box_content">

                {filter}
                <br>

            </div>
        </div>
        <a href="#" id="open_filter"></a>
        [/show]
        [show="index, category, product"]
        <div class="site_bar_box">
            <div class="box_title">Новые товары</div>
            <div class="box_content">
                {goods category="0" template="custom" limit="4"}
            </div>
        </div>
        [/show]

    </div>

</aside>