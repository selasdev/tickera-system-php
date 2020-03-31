<?php

namespace App\Controllers;

class MainController extends BaseController {

    protected $title = "Tickera";

    public function mainAction(){
        return $this->renderHTML("index.twig");
    }

    public function createDummyUser($request){
        $body = $request->getParsedBody();
        $isAdmin = count($body) == 1;
        echo "Is admin: $isAdmin";
        return $this->renderHTML("index.twig");
    }
    
}
