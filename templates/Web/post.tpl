<article class="article" itemscope itemtype="http://schema.org/Article">
    <h1>{title}</h1>
    [category="2"]<b>В категории 2</b>[/category]
    {categories}
    <img src="{poster}" alt="">
    <div class="post_content">
        {content}
    </div>
    <p>Просмотров: <b>{see}</b></p>

    <h3>Тут дополнительные поля</h3>
    {field:tekstovoe-pole}
    <br>
    {field:bolyshoe-pole}
    <br>
    {field:list}

    <div class="comments">

        {comments}

        <h3>Добавить комментарий</h3>
        {add-comment}
    </div>

</article>