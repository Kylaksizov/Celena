<article class="flex product" itemscope itemtype="http://schema.org/Product">

    <div id="main_goods" class="flex">

        {*Картинки товара*}
        <div class="product_pictures">
            <div id="goods_carousel" class="owl-carousel">
                <img src="{poster}" alt="">
            </div>
            <div id="goods_carousel2" class="owl-carousel">
                {images}
            </div>
            <span class="g_id">{id}</span>
            [sale]<span class="g_label sale">-{sale}</span>[/sale]
        </div>

        {*Информация о товаре*}
        <div class="product_info one_goods">

            <div class="flex jc_fs">
                <div>{rating}</div>
                <span class="rating_count">( {rating-count} отзывов )</span>
                <a href="#" class="favorite fav_added"></a>
            </div>
            <h1 itemprop="name">{title}{edit}</h1>
            <div class="prices">
                <span class="price" data-price="{price}">{price} {currency}</span>
                [sale]<span class="old_price">{old-price} {currency}</span>[/sale]
            </div>
            <div class="btn-cart">
                {buy}
                {buy-click}
                {add-cart}
            </div>

            <div class="main_goods_box">
                <h3>Характеристики</h3>
                {properties}
            </div>

        </div>

        <div class="tabs">

            <ul class="tabs_caption">
                <li class="active">Описание</li>
                <li>Отзывы <b>({rating-count})</b></li>
            </ul>

            <div class="tabs_content active">
                {content}
            </div>

            <div class="tabs_content" id="reviews">

                {reviews}

                <a href="#add_review" class="add_review open_modal">Добавить отзыв</a>

            </div>

        </div><!-- .tabs-->

    </div>

    <div id="right_bar">

        <div class="site_bar_box">
            <div class="box_title">Похожие товары</div>
            <div class="box_content goods_see">
                {products category="7" template="custom" limit="12"}
            </div>
        </div>

    </div>

</article>

{*[goods-see]
<h3 class="title_center">Вы смотрели:</h3>
<div id="goods_see" class="owl-carousel">
    {goods-see}
</div>
[/goods-see]*}

<div class="modal" id="add_review">
    <h4 class="modal_title">Добавление отзыва</h4>
    <p>Что бы добавить отзыв, <a href="/registration.html">зарегистрируйтесь</a> или <a href="#authorization" class="ico_login open_modal">войдите</a> на сайте.</p>
    <a href="#" class="close"></a>
</div>

<div class="modal" id="add_review">
    <h4 class="modal_title">Добавление отзыва</h4>
    <form action="#" method="POST">
        <label for="">Ваша оценка:</label> <div class="rating"></div>
        <input type="hidden" name="rate">
        <textarea name="review" rows="10"></textarea>
        <input type="submit" class="btn" data-a="AddReview" value="Добавить">
    </form>
    <a href="#" class="close"></a>
</div>