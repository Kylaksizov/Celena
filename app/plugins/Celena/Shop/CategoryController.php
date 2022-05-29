<?php

namespace app\plugins\Celena\Shop;

use app\core\Controller;
use app\plugins\Celena\Shop\classes\CustomProducts;
use app\plugins\Celena\Shop\classes\Functions;
use Exception;


class CategoryController extends Controller {


    /**
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/shop.css'];
        $this->view->scripts = ['js/shop.js'];

        Functions::preTreatment($this);

        // если тег ля вывода продуктов присутствует
        if($this->view->findTag('{CONTENT}', 1)){
            $Products = new CustomProducts();
            $products = $Products->get($this, true, end($this->urls), 'products');
            $this->view->setMain('{CONTENT}', $products);
            $this->view->clear();
        }

        $this->view->render(false);

        Functions::scanTags($this);
        $this->view->display();
    }

}