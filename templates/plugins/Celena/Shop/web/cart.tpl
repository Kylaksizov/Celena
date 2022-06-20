<h1 class="title_page">Оформление заказа</h1>
<div{* class="flex jc_fs"*}>
    <div class="cart_static">
        {cart}
    </div>
    <br>
    <div class="cart_order_form">
        <p>Пожалуйста заполните все поля, что бы оформить заказ.</p>
        <form action method="POST">
            <input type="text" name="name" value="{name}" placeholder="Ваше имя" required>
            <input type="text" name="tel" placeholder="Ваш телефон" required>
            <input type="text" name="email" value="{email}" placeholder="Ваш E-mail">
            <textarea name="comment" id="" rows="5" placeholder="Комментарий..."></textarea>
            <input type="hidden" name="total" id="total">
            <input type="hidden" name="products" id="productsJson">
            <p class="cart_total">Всего к оплате: <b>0 ₴</b></p>
            <input type="submit" class="btn_order" data-a="Cart" value="Оформить заказ">
        </form>
    </div>
</div>