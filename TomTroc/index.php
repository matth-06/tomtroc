<?php
require_once 'Controllers/HomeController.php';
require_once 'Controllers/UserController.php';
require_once 'Controllers/BookController.php';
require_once 'Controllers/MessageController.php';

session_start();
$action = $_GET['action'] ?? 'default';

try {
    switch ($action) {
        case 'default':
        case 'accueil':
            $controller = new HomeController();
            $controller->index();
            break;
        case 'livreEx':
            $controller = new BookController();
            $controller->index($_GET['search'] ?? '');
            break;
        case 'showBook':
            $controller = new BookController();
            $controller->show((int)($_GET['id'] ?? 0));
            break;
        case 'login':
            $controller = new UserController();
            $controller->showLogin();
            break;
        case 'signup':
            $controller = new UserController();
            $controller->showSignup();
            break;
        case 'monCompte':
            $controller = new UserController();
            $controller->account();
            break;
        case 'messagerie':
            $controller = new MessageController();
            $controller->messagerie((int)($_GET['user_id'] ?? null));
            break;
        case 'envoyer_message':
            $controller = new MessageController();
            $controller->messagerie((int)($_GET['user_id'] ?? null));
            break;
        case 'sendMessage':
            $controller = new MessageController();
            $controller->sendMessage(
                (int)($_GET['receiver_id'] ?? 0),
                $_POST['content'] ?? ''
            );
            break;
        case 'registerUser':
            $controller = new UserController();
            $controller->register(
                $_POST['pseudo'] ?? '',
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
            break;
        case 'connectUser':
            $controller = new UserController();
            $controller->connect(
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
            break;
        case 'updateProfile':
            $controller = new UserController();
            $controller->updateProfile($_POST);
            break;
        case 'ajoutLivre':
            $controller = new BookController();
            $controller->createForm();
            break;
        case 'addBook':
            $controller = new BookController();
            $controller->store($_POST, $_FILES, $_SESSION['user_id'] ?? 0);
            break;
        case 'edit':
            $controller = new BookController();
            $controller->editForm((int)($_GET['id'] ?? 0));
            break;
        case 'updateBook':
            $controller = new BookController();
            $controller->update((int)($_GET['id'] ?? 0), $_POST, $_FILES);
            break;
        case 'delete':
            $controller = new BookController();
            $controller->delete((int)($_GET['id'] ?? 0));
            break;
        default:
            $controller = new HomeController();
            $controller->index();
            break;
    }
} catch (Exception $e) {
    echo 'Une erreur est survenue : ' . $e->getMessage();
}
