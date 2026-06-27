<?php
require_once 'Controllers/HomeController.php';
require_once 'Controllers/UserController.php';
require_once 'Controllers/BookController.php';
require_once 'Controllers/MessageController.php';

session_start();

function sanitizeString($value): string
{
    if ($value === null || is_array($value) || is_object($value)) {
        return '';
    }

    $sanitized = trim((string) $value);
    return strip_tags($sanitized);
}

function sanitizeArray($value): array
{
    if (!is_array($value)) {
        return [];
    }

    $sanitized = [];
    foreach ($value as $key => $item) {
        if (is_array($item)) {
            $sanitized[$key] = sanitizeArray($item);
            continue;
        }

        if (is_string($item)) {
            $lowerKey = strtolower($key);
            if (strpos($lowerKey, 'password') !== false) {
                $sanitized[$key] = sanitizePassword($item);
            } else {
                $sanitized[$key] = sanitizeString($item);
            }
            continue;
        }

        $sanitized[$key] = $item;
    }

    return $sanitized;
}

function sanitizeInt($value, int $default = 0, int $min = 0): int
{
    if ($value === null || is_array($value) || is_object($value)) {
        return $default;
    }

    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    if ($filtered === false) {
        return $default;
    }

    return $filtered < $min ? $default : $filtered;
}

function sanitizePassword($value): string
{
    if ($value === null || is_array($value) || is_object($value)) {
        return '';
    }

    return trim((string) $value);
}

function sanitizeEmail($value): string
{
    return filter_var(sanitizeString($value), FILTER_SANITIZE_EMAIL);
}

$getData = sanitizeArray($_GET);
$postData = sanitizeArray($_POST);
$fileData = sanitizeArray($_FILES);

$action = sanitizeString($getData['action'] ?? 'default');
$allowedActions = [
    'default',
    'accueil',
    'livreEx',
    'showBook',
    'login',
    'signup',
    'monCompte',
    'messagerie',
    'envoyer_message',
    'sendMessage',
    'registerUser',
    'connectUser',
    'logout',
    'updateProfile',
    'ajoutLivre',
    'addBook',
    'edit',
    'updateBook',
    'delete',
];

if (!in_array($action, $allowedActions, true)) {
    $action = 'default';
}

$search = sanitizeString($getData['search'] ?? '');
$bookId = sanitizeInt($getData['id'] ?? 0, 0, 0);
$userId = sanitizeInt($getData['user_id'] ?? 0, 0, 0);
$receiverId = sanitizeInt($getData['receiver_id'] ?? 0, 0, 0);
$sessionUserId = sanitizeInt($_SESSION['user_id'] ?? 0, 0, 0);

$messageContent = sanitizeString($postData['content'] ?? '');
$registerPseudo = sanitizeString($postData['pseudo'] ?? '');
$registerEmail = sanitizeEmail($postData['email'] ?? '');
$registerPassword = sanitizeString($postData['password'] ?? '');
$connectEmail = sanitizeEmail($postData['email'] ?? '');
$connectPassword = sanitizeString($postData['password'] ?? '');

try {
    switch ($action) {
        case 'default':
        case 'accueil':
            $controller = new HomeController();
            $controller->index();
            break;
        case 'livreEx':
            $controller = new BookController();
            $controller->index($search);
            break;
        case 'showBook':
            $controller = new BookController();
            $controller->show($bookId);
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
            $controller->messagerie($userId);
            break;
        case 'envoyer_message':
            $controller = new MessageController();
            $controller->messagerie($userId);
            break;
        case 'sendMessage':
            $controller = new MessageController();
            $controller->sendMessage($receiverId, $messageContent);
            break;
        case 'registerUser':
            $controller = new UserController();
            $controller->register($registerPseudo, $registerEmail, $registerPassword);
            break;
        case 'connectUser':
            $controller = new UserController();
            $controller->connect($connectEmail, $connectPassword);
            break;
        case 'logout':
            $controller = new UserController();
            $controller->logout();
            break;
        case 'updateProfile':
            $controller = new UserController();
            $controller->updateProfile($postData);
            break;
        case 'ajoutLivre':
            $controller = new BookController();
            $controller->createForm();
            break;
        case 'addBook':
            $controller = new BookController();
            $controller->store($postData, $fileData, $sessionUserId);
            break;
        case 'edit':
            $controller = new BookController();
            $controller->editForm($bookId);
            break;
        case 'updateBook':
            $controller = new BookController();
            $controller->update($bookId, $postData, $fileData);
            break;
        case 'delete':
            $controller = new BookController();
            $controller->delete($bookId);
            break;
        default:
            $controller = new HomeController();
            $controller->index();
            break;
    }
} catch (Exception $e) {
    echo 'Une erreur est survenue : ' . $e->getMessage();
}
