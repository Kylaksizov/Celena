<aside id="site_bar">

    <a href="#" class="my_ad">
        <img src="{THEME}/img/ad.jpg" alt="">
    </a>

    <div class="sticky">

        [show="index,category,news"]
        <div class="site_bar_box">
            <div class="box_title">Новости</div>
            <div class="box_content">
                {custom category="0" template="custom" limit="3" sort="asc"}
                <br><br><br><br>
                {custom category="0" template="custom" limit="1"}
            </div>
        </div>
        [/show]

    </div>

</aside>