<?php

namespace App\Controllers;

class MainController extends BaseController {

    protected $title = "Tickera";

    public function mainAction(){
        return $this->renderHTML("index.twig");
    }
    
}
