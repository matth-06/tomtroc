<?php
require_once 'Controllers/HomeController.php';

$action = $_GET['action'] ?? 'default';

$controller = new HomeController();
try {
    switch ($action) {
        case 'default':
            $controller->index();
            break;
        case 'accueil':
            $controller->index('home');
            break;
        case 'livreEx':
            $controller->render('livreEx');
            break;
        case 'showBook':
            $controller->render('detailLivre');
            break;
        case 'login':
            $controller->render('login');
            break;
        case 'monCompte':
            $controller->render('monCompte');
            break;
        case 'messagerie':
            $controller->render('messagerie');
            break;
    }
} catch (Exception $e) {
    // Gérer les exceptions ici, par exemple en affichant une page d'erreur.
    echo "Une erreur est survenue : " . $e->getMessage();
}
