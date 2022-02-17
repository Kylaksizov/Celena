<?php

namespace app\core\interface;

interface InitPlugin{

    public function install();
    public function update();
    public function delete();

}