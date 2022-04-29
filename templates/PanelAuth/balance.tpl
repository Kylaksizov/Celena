<h1>Баланс</h1>

<div class="dg ai_i">
    <div class="hr hr_t">
        <div class="tabs">

            <ul class="tabs_caption">
                <li class="active">Оплата</li>
                <li>Движения по счету</li>
            </ul>

            <div class="tabs_content active">

                <div class="fx">
                    <div class="w50">
                        {services}
                    </div>
                    <div class="w50">
                        <form action="/payment/" method="POST" id="payForm" class="inp100 tl">
                            <label for="amount">Сумма:</label>
                            <input type="number" min="100" max="10000" step="0.01" name="amount" id="amount" data-tariff="base" placeholder="100 $" autocomplete="off" value="100">
                            <input type="checkbox" name="subscribe" id="subscribe_payment">
                            <label for="subscribe_payment">Привязать карту для автоматического продления сервиса</label><br>
                            <a href="#more_info_subscribe" class="open_modal fr">Подробнее...</a>
                            <br><br>

                            <div id="card">
                                <label for="tel">Номер телефона в международном формате:</label>
                                <input type="tel" name="tel" id="tel" placeholder="+380950123456">
                                <label for="card[number]">Номер карты:</label>
                                <input type="tel" name="card[number]" id="card[number]" placeholder="•••• •••• •••• ••••" autocomplete="cc-number">
                                <div class="fx">
                                    <div class="w50">
                                        <label for="card[period]">Срок действия:</label>
                                        <input type="tel" name="card[period]" id="card[period]" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                                    </div>
                                    <div class="w10"></div>
                                    <div class="w40">
                                        <label for="card[cvv2]">CVV2:</label>
                                        <input type="tel" name="card[cvv2]" id="card[cvv2]" class="pass_mask" placeholder="•••" maxlength="3" autocomplete="cc-csc">
                                    </div>
                                </div>
                                <br>
                                <div class="description">Карта будет подписана на ежемесячное списание средств в размеретарифа. И будет снята первая сумма <b>1000 $</b></div>
                            </div>
                            <br>
                            <div class="fx">
                                <div class="w30">
                                    <input type="submit" class="btn" value="Оплатить">
                                </div>
                                <div class="w70">
                                    <p class="description">Нажимая на кнопку «Оплатить»,<br>Вы принимаете <a href="https://nex.company/offer.pdf" target="_blank">Пользовательское соглашение</a></p>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

            <div class="tabs_content">
                {history}
            </div>

        </div><!-- .tabs-->
    </div>
</div>

<div id="more_info_subscribe" class="modal">
    <div class="modal_title">Автоматическая оплата</div>
    <ul class="tl">
        <li>• Автоматическое списание средств за 3 дня до окончания оплаченного периода;</li>
        <li>• Учитывается сумма всех подключенных услуг;</li>
        <li>• Сумма списания зависит от курса гривны к рублю на момент платежа;</li>
    </ul>
    <a href="#" class="close"></a>
</div>

<link rel="stylesheet" href="{THEME}/css/balance.css">