<?php

namespace app\core\interfaces;

interface InitPlugin{

    public function install();
    public function update();
    public function powerOn();
    public function powerOff();
    public function delete();

}