<?php
require_once 'Models/HomeModel.php';

class HomeController {
    public function index() {
        
        $title = 'Accueil';
        ob_start();
        include 'Views/home.php';
        $content = ob_get_clean();
        
        require_once 'Views/layout.php';
    }

    public function render(string $viewName) : void 
    { 
        ob_start();
        require('Views/template/' . $viewName . '.php');
        $content = ob_get_clean();

        require 'Views/layout.php';
    }
}
?>