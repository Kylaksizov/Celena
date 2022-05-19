<?php

namespace app\core\system\install\steps;

class Start{


    public function __construct() {}
    public function __clone() {}




    public function postAction(){
        
        if(!empty($_POST["agreement"])){
            SetCookie("licence", "1", time() + 3600, "/");
            return 'next';
        }
    }




    public function indexAction(){

        return '<form action method="POST">
            <h1><a href="//celena.io/" id="celena_logo" target="_blank">celena</a> Начало установки</h1>
            <div class="licence_text">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus accusantium ipsum iste itaque iure natus, nisi nobis quibusdam rerum sunt! Ad assumenda autem eaque esse fugiat id iste iure modi neque omnis, optio quas, quisquam sed tempore ut vero, voluptatibus voluptatum! Accusamus eveniet facilis, fugiat in non praesentium quibusdam veniam veritatis? Ad consectetur cupiditate deserunt dolorem fuga id natus quae quibusdam. Architecto iste molestiae natus nulla quisquam! Alias asperiores consectetur culpa magnam modi molestiae, voluptatum. Ad, aliquam assumenda dignissimos doloremque doloribus eaque, est expedita in necessitatibus nemo non nostrum quaerat quos ratione reiciendis sit, tempora tempore veritatis vitae voluptatem? Aliquid amet atque, consequuntur, deleniti eos error expedita facere facilis harum illum minus natus nesciunt nisi, nostrum odit optio perspiciatis quaerat quas quibusdam quo rerum saepe soluta tenetur ut voluptates. Delectus facere facilis, incidunt minus necessitatibus omnis quaerat quis tempora? Accusamus ad aut, consectetur consequuntur cupiditate est excepturi facilis in natus nesciunt non obcaecati officiis quasi quidem soluta veritatis voluptates voluptatum! Accusantium adipisci alias dignissimos dolorem dolores eius eveniet, expedita fuga hic impedit ipsum maxime, minima omnis quisquam quod, ratione sed temporibus voluptas! Accusantium aliquam architecto aspernatur assumenda at earum eligendi eveniet excepturi facere fugit, harum hic illo laborum magnam mollitia nam neque, pariatur perferendis quasi quis quos sed sit totam veniam vitae? Ad commodi consequuntur cumque delectus expedita explicabo facere id illum ipsum laborum numquam, quam quia sed sint tempore tenetur voluptate! Ab ad alias, aliquam amet architecto aut consequatur cum debitis dicta dolore dolorem doloremque ducimus, eligendi exercitationem illum in minus molestiae mollitia necessitatibus nemo omnis perferendis praesentium quaerat quod rerum totam ullam vitae. Animi architecto asperiores assumenda consequatur cum, debitis delectus doloremque esse est illo in incidunt iste itaque labore natus nostrum obcaecati quibusdam reiciendis sapiente soluta sunt suscipit vel vero voluptatibus voluptatum. Animi dolorum error eum tempore.
            </div>
            <div class="fx ai_c">
                <div>
                    <input type="checkbox" name="agreement" id="agreement" value="1"><label for="agreement">Согласен с условиями</label>
                </div>
                <input type="submit" data-a="Start" class="btn" value="Начать установку">
            </div>
        </form>';
    }
}