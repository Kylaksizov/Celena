<?php

namespace app\core\interface;

interface InitPlugin{

    public function install();
    public function powerOn();
    public function powerOff();
    public function delete();

}