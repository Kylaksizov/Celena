<article class="goods_box">
    <a href="{link}" class="g_preview">
        <img src="{image-thumb-1}" alt="">
        <span class="g_id">{sid}</span>
        [sale]<span class="g_label sale">-{sale}</span>[/sale]
    </a>
    <div class="flex goods_info">
        <div class="gdn">
            {rating}
            <h2><a href="{link}">{title}</a></h2>
            <div class="price">
                [sale]<span class="price_sale">{price} {currency}</span>
                <span class="old_price">{old-price} {currency}</span>[/sale]
                [no-sale]{price} {currency}[/no-sale]
            </div>
            <div class="description">{description}</div>
            <div class="btn-cart">
                {buy}
                <a href="#order_click" class="buy_on_click open_modal" title="Заказать по телефону"></a>
                {add-cart}
            </div>
        </div>
    </div>
</article>