<?php

namespace app\controllers\ajax\panel;


use app\core\System;

class CelenaPlugin{

    public function index(){

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'showDetail': self::detailPlugin(); break;
            }

        }

        die("info::error::Неизвестный запрос");
    }


    private function detailPlugin(){

        $script = '<script>
            $("#result_response_plugin").html(`<h2 class="modal_title">Название плагина</h2>
                
                <div class="plugin_header" style="background: url(https://ps.w.org/elementor/assets/banner-772x250.png?rev=2597493) no-repeat center center"></div>
                
                <div class="modal_body">
                
                    <div class="plugin_more_info">
                        
                        <div class="tabs">
                            <ul class="tabs_caption">
                                <li class="active">Описание</li>
                                <li>Скриншоты</li>
                                <li>Инструкция</li>
                                <li>Отзывы</li>
                            </ul>
                            <div class="tabs_content active">
                                <h2>Заголовок 1</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad atque dolorem, eaque eum fugiat hic, illum ipsa ipsum itaque quam qui reiciendis, repellat reprehenderit tempore. Aspernatur aut earum eum magni molestiae necessitatibus repellat tempore. Accusamus aliquam beatae debitis deserunt doloribus, earum eum ex, exercitationem hic impedit iste magnam, molestias necessitatibus nisi numquam pariatur provident recusandae soluta suscipit temporibus. Blanditiis consectetur delectus expedita fugiat, hic id, nobis perspiciatis quasi <br>ratione sapiente, unde voluptas. Ab atque beatae blanditiis consectetur</p>
                                <h3>Заголовок 2</h3>
                                <p>culpa, deserunt dolorem ducimus, eveniet excepturi explicabo facere fuga hic iste itaque laboriosam molestiae necessitatibus nesciunt,<br>nobis possimus quae rem repudiandae sapiente sint su<br>nt tempora veniam voluptates! Ab delectus dolore dolores hic laborum qui voluptatem voluptatum. Consequatur ducimus, enim face<br>re inventore iure laborum libero, obcaecati perspiciatis pra<br>esentium quam quia quis reiciendis reprehenderit sint unde, vel voluptates. Aspernatur, quisquam ullam! Aperiam ea eaque eligendi facere impedit labore, magnam qui, quia quis saepe sint, velit vitae voluptas! Accusantium animi a<br>periam architecto aspernatur atque dicta, doloremque dolores ea eaque earum eos facilis harum id illo illum incidunt inventore iste iure labore modi molestias nesciunt non pariatur praesentium quam quas quis quo quos, sed sint sunt totam velit vitae. Accusamus consequatur dolor facere fugit illum inventore<br> libero molestiae officiis ut voluptatem. Aut consectetur dolorum molestiae placeat quas quis, ut. Alias aliquid autem dolorem expedita facilis hic iusto libero modi nemo nisi, nulla quasi<br> quos ratione? A accusantium, aliquam asperiores consectetur dolorem doloremque eos facere inventore itaque laboriosam laborum neque, nesciunt, optio perferendis quod. Aliquam aliquid aperiam asperiores aspernatur, blanditiis consectetur consequuntur cum debitis deleniti dolore dolorem dolores error eveniet ex expedita explicabo ipsa ipsum magni minus molestiae mollitia nam natus neque nostrum odio optio pariatur placeat provident quas recusandae reprehenderit saepe sapiente sunt temporibus totam vero vitae? Assumenda dolorum facere ipsa iure optio. Exercitationem quae, ullam.
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad atque dolorem, eaque eum fugiat hic, illum ipsa ipsum itaque quam qui reiciendis, repellat reprehenderit tempore. Aspernatur aut earum eum magni molestiae necessitatibus repellat tempore. Accusamus aliquam beatae debitis deserunt doloribus, earum eum ex, exercitationem hic impedit iste magnam, molestias necessitatibus nisi numquam pariatur provident recusandae soluta suscipit temporibus. Blanditiis consectetur delectus expedita fugiat, hic id, nobis perspiciatis quasi <br>ratione sapiente, unde voluptas. Ab atque beatae blanditiis consectetur<br> culpa, deserunt dolorem ducimus, eveniet excepturi explicabo facere fuga hic iste itaque laboriosam molestiae necessitatibus nesciunt,<br>nobis possimus quae rem repudiandae sapiente sint su<br>nt tempora veniam voluptates! Ab delectus dolore dolores hic laborum qui voluptatem voluptatum. Consequatur ducimus, enim face<br>re inventore iure laborum libero, obcaecati perspiciatis pra<br>esentium quam quia quis reiciendis reprehenderit sint unde, vel voluptates. Aspernatur, quisquam ullam! Aperiam ea eaque eligendi facere impedit labore, magnam qui, quia quis saepe sint, velit vitae voluptas! Accusantium animi a<br>periam architecto aspernatur atque dicta, doloremque dolores ea eaque earum eos facilis harum id illo illum incidunt inventore iste iure labore modi molestias nesciunt non pariatur praesentium quam quas quis quo quos, sed sint sunt totam velit vitae. Accusamus consequatur dolor facere fugit illum inventore<br> libero molestiae officiis ut voluptatem. Aut consectetur dolorum molestiae placeat quas quis, ut. Alias aliquid autem dolorem expedita facilis hic iusto libero modi nemo nisi, nulla quasi<br> quos ratione? A accusantium, aliquam asperiores consectetur dolorem doloremque eos facere inventore itaque laboriosam laborum neque, nesciunt, optio perferendis quod. Aliquam aliquid aperiam asperiores aspernatur, blanditiis consectetur consequuntur cum debitis deleniti dolore dolorem dolores error eveniet ex expedita explicabo ipsa ipsum magni minus molestiae mollitia nam natus neque nostrum odio optio pariatur placeat provident quas recusandae reprehenderit saepe sapiente sunt temporibus totam vero vitae? Assumenda dolorum facere ipsa iure optio. Exercitationem quae, ullam.
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus ad atque dolorem, eaque eum fugiat hic, illum ipsa ipsum itaque quam qui reiciendis, repellat reprehenderit tempore. Aspernatur aut earum eum magni molestiae necessitatibus repellat tempore. Accusamus aliquam beatae debitis deserunt doloribus, earum eum ex, exercitationem hic impedit iste magnam, molestias necessitatibus nisi numquam pariatur provident recusandae soluta suscipit temporibus. Blanditiis consectetur delectus expedita fugiat, hic id, nobis perspiciatis quasi <br>ratione sapiente, unde voluptas. Ab atque beatae blanditiis consectetur<br> culpa, deserunt dolorem ducimus, eveniet excepturi explicabo facere fuga hic iste itaque laboriosam molestiae necessitatibus nesciunt,<br>nobis possimus quae rem repudiandae sapiente sint su<br>nt tempora veniam voluptates! Ab delectus dolore dolores hic laborum qui voluptatem voluptatum. Consequatur ducimus, enim face<br>re inventore iure laborum libero, obcaecati perspiciatis pra<br>esentium quam quia quis reiciendis reprehenderit sint unde, vel voluptates. Aspernatur, quisquam ullam! Aperiam ea eaque eligendi facere impedit labore, magnam qui, quia quis saepe sint, velit vitae voluptas! Accusantium animi a<br>periam architecto aspernatur atque dicta, doloremque dolores ea eaque earum eos facilis harum id illo illum incidunt inventore iste iure labore modi molestias nesciunt non pariatur praesentium quam quas quis quo quos, sed sint sunt totam velit vitae. Accusamus consequatur dolor facere fugit illum inventore<br> libero molestiae officiis ut voluptatem. Aut consectetur dolorum molestiae placeat quas quis, ut. Alias aliquid autem dolorem expedita facilis hic iusto libero modi nemo nisi, nulla quasi<br> quos ratione? A accusantium, aliquam asperiores consectetur dolorem doloremque eos facere inventore itaque laboriosam laborum neque, nesciunt, optio perferendis quod. Aliquam aliquid aperiam asperiores aspernatur, blanditiis consectetur consequuntur cum debitis deleniti dolore dolorem dolores error eveniet ex expedita explicabo ipsa ipsum magni minus molestiae mollitia nam natus neque nostrum odio optio pariatur placeat provident quas recusandae reprehenderit saepe sapiente sunt temporibus totam vero vitae? Assumenda dolorum facere ipsa iure optio. Exercitationem quae, ullam.</p>
                            </div>
                            <div class="tabs_content">
                                <a href="https://ps.w.org/elementor/assets/screenshot-1.gif?rev=1608747" data-fancybox="plugin"><img src="https://ps.w.org/elementor/assets/screenshot-1.gif?rev=1608747" alt=""></a>
                            </div>
                            <div class="tabs_content">
                                
                            </div>
                            <div class="tabs_content">
                                
                            </div>
                        </div>
                        
                        <div class="plugin_details">
                            <a href="#" class="btn btn_install" data-a="CelenaPlugin:action=install&id=1">Установить</a>
                            <ul>
                                <li><b>Версия:</b> <span>3.1.5</span></li>
                                <li><b>Автор:</b> <span>Kylaksizov</span></li>
                                <li><b>Последнее обновление:</b> <span>23.01.2022</span></li>
                                <li><b>PHP:</b> <span>5.6 - 8.0</span></li>
                                <li><b>Версия Celena:</b> <span>1.0 - 3.0</span></li>
                                <li><b>Кол-во установок:</b> <span>5132</span></li>
                            </ul>
                            <hr>
                            <div class="rating_plugin" data-rateyo-rating="80%"></div>
                        </div>
                        
                    </div>
                    
                </div>`);
            $(".rating_plugin").rateYo({
                starWidth: "17",
                fullStar: true,
                readOnly: true,
                normalFill: "#ccc",
                ratedFill: "#ffc500"
            });
        </script>';

        System::script($script);
    }

}