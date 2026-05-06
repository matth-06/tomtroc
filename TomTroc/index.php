<?php
require_once 'Controllers/HomeController.php';
require_once 'Controllers/UserController.php';

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
        case 'signup':
            $controller->render('signup');
            break;
        case 'registerUser':
            $pseudo = $_POST['pseudo'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userController = new UserController();
            $result = $userController->registerUser($pseudo, $email, $password);

            if ($result === true) {
                header('Location: index.php?action=login');
            } else {
                // Gérer l'erreur d'enregistrement
                echo $result;
            }
            break;
        
        case 'connectUser':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userController = new UserController();
            $result = $userController->connectUser($email, $password);

            if ($result === true) {
                header('Location: index.php?action=monCompte');
            } else {
                // Gérer l'erreur de connexion
                echo $result;
            }
            break;
    }
} catch (Exception $e) {
    // Gérer les exceptions ici, par exemple en affichant une page d'erreur.
    echo "Une erreur est survenue : " . $e->getMessage();
}
