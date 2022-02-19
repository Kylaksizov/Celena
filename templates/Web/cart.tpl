<h1 class="title_page">Оформление заказа</h1>
<div{* class="flex jc_fs"*}>
    <div class="cart_static">
        {cart}
    </div>
    <div class="cart_order_form">
        <p>Пожалуйста заполните все поля, что бы оформить заказ.</p>
        <form action="?action=order" method="POST">
            <input type="text" name="name" placeholder="Ваше имя" required>
            <input type="text" name="phone" placeholder="Ваш телефон" required>
            <input type="text" name="email" placeholder="Ваш E-mail">
            <textarea name="" id="" rows="5" placeholder="Комментарий..."></textarea>
            <input type="hidden" name="total" id="total">
            <input type="hidden" name="title_goods" id="title_goods">
            <p class="cart_total">Всего к оплате: <b>0 ₴</b></p>
            <input type="submit" class="btn_order" value="Оформить заказ">
        </form>
    </div>
</div>